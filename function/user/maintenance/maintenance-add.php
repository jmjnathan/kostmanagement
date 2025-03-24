<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit();
}

try {
    $pdo = new PDO("mysql:host=localhost;dbname=kos_management", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $session_username = $_SESSION['username'];

    // Ambil id_penghuni dan id_kamar dari username pengguna
    $stmt = $pdo->prepare("SELECT id, room_id FROM penghuni WHERE username = :username");
    $stmt->execute(['username' => $session_username]);
    $penghuni = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$penghuni) {
        $_SESSION['notifikasi'] = "Data penghuni tidak ditemukan!";
        header('Location: pengajuan_maintenance.php');
        exit();
    }

    $id_penghuni = $penghuni['id'];
    $id_kamar = $penghuni['room_id'];

    // Ambil data dari form
    $kategori = $_POST['kategori'] ?? '';
    $deskripsi = $_POST['deskripsi'] ?? '';
    $status_user = $_POST['status'] ?? '';
    $deadline = $_POST['deadline'] ?? '';

    // Validasi input
    if (empty($kategori) || empty($deskripsi) || empty($status_user) || empty($deadline)) {
        $_SESSION['notifikasi'] = "Semua field wajib diisi!";
        header('Location: pengajuan_maintenance.php');
        exit();
    }

    // Handle file upload
    $bukti_path = null;
    if (!empty($_FILES['bukti']['name'])) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $file_name = time() . "_" . basename($_FILES['bukti']['name']);
        $target_file = $target_dir . $file_name;

        if (move_uploaded_file($_FILES['bukti']['tmp_name'], $target_file)) {
            $bukti_path = $target_file;
        } else {
            $_SESSION['notifikasi'] = "Gagal mengunggah bukti!";
            header('Location: pengajuan_maintenance.php');
            exit();
        }
    }

    // Insert data ke database
    $stmt = $pdo->prepare("INSERT INTO maintenance (id_penghuni, id_kamar, tanggal_pengajuan, deskripsi, kategori, bukti, status_user, deadline, status) 
                           VALUES (:id_penghuni, :id_kamar, NOW(), :deskripsi, :kategori, :bukti, :status_user, :deadline, 'Pending')");
    $stmt->execute([
        'id_penghuni' => $id_penghuni,
        'id_kamar' => $id_kamar,
        'deskripsi' => $deskripsi,
        'kategori' => $kategori,
        'bukti' => $bukti_path,
        'status_user' => $status_user,
        'deadline' => $deadline
    ]);

    $_SESSION['notifikasi'] = "Pengajuan berhasil dikirim!";
    header('Location: ../../../page/users/maintenance.php');
    exit();
} catch (PDOException $e) {
    $_SESSION['notifikasi'] = "Terjadi kesalahan: " . $e->getMessage();
    header('Location: ../../../page/users/maintenance.php');
    exit();
}
?>

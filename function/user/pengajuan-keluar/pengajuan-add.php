<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: ../../index.php');
    exit();
}

if ($_SESSION['role'] !== 'user') {
    header('Location: ../../dashboard-user.php');
    exit();
}

try {
    $pdo = new PDO("mysql:host=localhost;dbname=kos_management", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $session_username = $_SESSION['username'];

    // Ambil id_penghuni berdasarkan username
    $stmt = $pdo->prepare("SELECT id FROM penghuni WHERE username = :username");
    $stmt->execute(['username' => $session_username]);
    $penghuni = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$penghuni) {
        $_SESSION['notifikasi'] = "Gagal mengajukan keluar. Data penghuni tidak ditemukan.";
        header('Location: ../../dashboard-user.php');
        exit();
    }

    $id_penghuni = $penghuni['id'];
    $tanggal_pengajuan = date('Y-m-d');
    $tanggal_rencana_keluar = date('Y-m-d');
    $alasan = $_POST['alasan'] ?? '';
    
    if (empty($alasan)) {
        $_SESSION['notifikasi'] = "Gagal! Alasan harus diisi.";
        header('Location: ../../dashboard-user.php');
        exit();
    }

    // Insert pengajuan keluar kos
    $stmt = $pdo->prepare("INSERT INTO pengajuan_keluar (penghuni_id, tanggal_pengajuan, tanggal_rencana_keluar, alasan, status) VALUES (:id_penghuni, :tanggal_pengajuan, :tanggal_rencana_keluar, :alasan, 'Pending')");
    $stmt->execute([
        'id_penghuni' => $id_penghuni,
        'tanggal_pengajuan' => $tanggal_pengajuan,
        'tanggal_rencana_keluar' => $tanggal_rencana_keluar,
        'alasan' => $alasan
    ]);
    
    $_SESSION['notifikasi'] = "Pengajuan keluar kos berhasil dikirim.";
    header('Location: ../../../page/users/pengajuan-keluar.php');
    exit();
} catch (PDOException $e) {
    $_SESSION['notifikasi'] = "Terjadi kesalahan: " . $e->getMessage();
    header('Location: ../../../page/users/pengajuan-keluar.php');
    exit();
}

echo "File pengajuan-add.php berhasil diakses!";
exit();

?>
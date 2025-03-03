<?php
session_start();

// Cek apakah pengguna sudah login dan memiliki hak akses
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit();
}

if (isset($_GET['id'])) {
    $penghuni_id = $_GET['id']; // ID penghuni yang akan dihapus

    // Koneksi ke database
    $host = 'localhost';
    $dbname = 'kos_management';
    $username = 'root';
    $password = '';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Ambil room_id dari penghuni yang akan dihapus
        $stmt = $pdo->prepare("SELECT room_id FROM penghuni WHERE id = :id");
        $stmt->execute(['id' => $penghuni_id]);
        $penghuni = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($penghuni) {
            // Mulai transaksi
            $pdo->beginTransaction();

            // Hapus penghuni dari database
            $delete_stmt = $pdo->prepare("DELETE FROM penghuni WHERE id = :id");
            $delete_stmt->execute(['id' => $penghuni_id]);

            // Set status kamar menjadi '1' (kosong)
            $update_stmt = $pdo->prepare("UPDATE rooms SET status = '1' WHERE id = :room_id");
            $update_stmt->execute(['room_id' => $penghuni['room_id']]);

            // Commit transaksi jika semua berhasil
            $pdo->commit();

            $_SESSION['toast_message'] = "Data penghuni berhasil dihapus!";
        } else {
            $_SESSION['toast_message'] = "Data penghuni tidak ditemukan!";
        }

        // Redirect ke halaman penghuni setelah proses selesai
        header('Location: ../../../page/admin/penghuni.php');
        exit();

    } catch (Exception $e) {
        // Rollback jika terjadi error
        $pdo->rollback();
        echo "Error: " . $e->getMessage();
        exit();
    }
} else {
    // Jika tidak ada ID yang diberikan, redirect ke halaman penghuni
    header('Location: ../../../page/admin/penghuni.php');
    exit();
}
?>

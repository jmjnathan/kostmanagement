<?php
session_start();

// Cek apakah pengguna sudah login dan memiliki hak akses
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit();
}

if (isset($_GET['id'])) {
    $room_id = $_GET['id'];

    // Koneksi ke database
    $host = 'localhost';
    $dbname = 'kos_management';
    $username = 'root';
    $password = '';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Query untuk menghapus kamar
        $stmt = $pdo->prepare("DELETE FROM penghuni WHERE id = :id");
        $stmt->execute(['id' => $room_id]);

        $_SESSION['toast_message'] = "Data berhasil dihapus!";
        header('Location: ../../../page/admin/penghuni.php');
        exit();

        // Redirect ke halaman kamar setelah penghapusan
        header('Location: ../../../page/admin/penghuni.php');
        exit();
    } catch (PDOException $e) {
        echo 'Connection failed: ' . $e->getMessage();
        exit();
    }
} else {
    // Jika tidak ada ID yang diberikan, redirect ke halaman kamar
    header('Location: ../../../page/admin/penghuni.php');
    exit();
}
?>

<?php
session_start();

// Cek apakah pengguna sudah login dan memiliki hak akses admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit();
}

// Periksa apakah ID peraturan diberikan
if (!isset($_GET['id'])) {
    $_SESSION['toast_message'] = "ID peraturan tidak ditemukan!";
    header('Location: ../../../page/admin/peraturan.php');
    exit();
}

$rule_id = $_GET['id']; // ID peraturan yang akan dihapus

// Koneksi ke database
$host = 'localhost';
$dbname = 'kos_management';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Cek apakah peraturan ada
    $stmt = $pdo->prepare("SELECT id FROM peraturan_kos WHERE id = :id");
    $stmt->execute(['id' => $rule_id]);
    $rule = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($rule) {
        // Hapus peraturan dari database
        $delete_stmt = $pdo->prepare("DELETE FROM peraturan_kos WHERE id = :id");
        $delete_stmt->execute(['id' => $rule_id]);

        $_SESSION['toast_message'] = "Peraturan berhasil dihapus!";
    } else {
        $_SESSION['toast_message'] = "Peraturan tidak ditemukan!";
    }

    // Redirect ke halaman daftar peraturan
    header('Location: ../../../page/admin/peraturan.php');
    exit();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    exit();
}
?>

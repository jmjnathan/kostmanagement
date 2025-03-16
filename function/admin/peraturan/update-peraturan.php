<?php
session_start();

// Cek apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit();
}

// Cek role pengguna, hanya admin yang boleh mengakses
if ($_SESSION['role'] !== 'admin') {
    header('Location: dashboard-user.php');
    exit();
}

// Koneksi ke database
$host = 'localhost';
$dbname = 'kos_management';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Validasi input
        $id = !empty($_POST['id']) ? $_POST['id'] : null;
        $isi_peraturan = !empty($_POST['isi_peraturan']) ? trim($_POST['isi_peraturan']) : null;

        if ($id === null || $isi_peraturan === null) {
            echo "Error: ID atau isi peraturan tidak boleh kosong.";
            exit();
        }

        // Mulai transaksi database
        $pdo->beginTransaction();

        try {
            // Update data peraturan
            $sql = "UPDATE peraturan_kos SET isi_peraturan = :isi_peraturan WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'id' => $id,
                'isi_peraturan' => $isi_peraturan
            ]);

            // Commit transaksi jika berhasil
            $pdo->commit();

            $_SESSION['toast_message'] = "Peraturan berhasil diperbarui!";
            header('Location: ../../../page/admin/peraturan.php');
            exit();
        } catch (Exception $e) {
            // Rollback jika terjadi kesalahan
            $pdo->rollBack();
            echo "Error: " . $e->getMessage();
        }
    }
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
    exit();
}
?>
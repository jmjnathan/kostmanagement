<?php
session_start();

// Pastikan hanya admin yang bisa mengakses
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak']);
    exit();
}

$host = 'localhost';
$dbname = 'kos_management';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $_POST['id'] ?? null;
        $status = $_POST['status'] ?? null; // Pastikan sesuai dengan request dari frontend

        if (!$id || !$status) {
            echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap']);
            exit();
        }

        if ($status === 'approved') {
            $stmt = $pdo->prepare("UPDATE maintenance SET status = :status WHERE id = :id");
        } else {
            $stmt = $pdo->prepare("UPDATE maintenance SET status = :status WHERE id = :id");
        }

        $stmt->execute([
            'id' => $id,
            'status' => $status,
        ]);

        echo json_encode(['status' => 'success', 'message' => 'Status berhasil diperbarui']);
        header('Location: ../../../page/admin/maintenance.php');
        exit();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Metode tidak valid']);
        exit();
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Kesalahan: ' . $e->getMessage()]);
    exit();
}
?>

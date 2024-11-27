<?php
session_start();
$host = 'localhost';
$dbname = 'kos_management';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $_POST['id'];
        $feedback = $_POST['feedback'];
        $status = $_POST['status'];

        // Validasi data
        if (!empty($id) && !empty($feedback) && !empty($status)) {
            $sql = "UPDATE kritik_dan_saran SET isi_feedback = :feedback, status = :status, tanggal_feedback = NOW() WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'id' => $id,
                'feedback' => $feedback,
                'status' => $status,
            ]);

            // Set pesan ke session
            $_SESSION['toast_message'] = "Data berhasil diperbarui!";
            header('Location: ../../../page/admin/kritik-saran.php'); // Redirect ke halaman sebelumnya
            exit();
        } else {
            echo "Data tidak lengkap!";
        }
    }
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
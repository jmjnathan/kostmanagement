<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];           // ID dari kritik dan saran
    $feedback = $_POST['isi_feedback'];  // Feedback yang diberikan
    $status = $_POST['status'];    // Status yang dipilih (misalnya: "Tanggap", "Selesai", dll)

    $host = 'localhost';
    $dbname = 'kos_management';
    $username = 'root';
    $password = '';

    try {
        // Menghubungkan ke database
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Query untuk update kritik dan saran beserta tanggal feedback
        $stmt = $pdo->prepare("UPDATE kritik_dan_saran SET isi_feedback = :feedback, status = :status, tanggal_feedback = NOW() WHERE id = :id");
        $stmt->execute([
            ':feedback' => $feedback, // Menggunakan $feedback yang sudah didefinisikan
            ':status' => $status,
            ':id' => $id
        ]);

        // Redirect kembali ke halaman kritik-saran
        header('Location: ../../../page/admin/kritik-saran.php');
        exit();

    } catch (PDOException $e) {
        echo 'Error: ' . $e->getMessage();
    }
}
?>

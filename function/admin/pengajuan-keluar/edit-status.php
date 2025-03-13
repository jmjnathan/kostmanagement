<?php
$host = 'localhost';
$dbname = 'kos_management';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $_POST['id'] ?? null;
        $status = $_POST['action'] ?? null;
        $note = $_POST['note'] ?? '';

        if ($id && ($status === 'approved' || $status === 'rejected')) {
            if ($status === 'approved') {
                $stmt = $pdo->prepare("UPDATE pengajuan_keluar SET status = :status, note = :note, approved_at = CURDATE() WHERE id = :id");
            } else {
                $stmt = $pdo->prepare("UPDATE pengajuan_keluar SET status = :status, note = :note WHERE id = :id");
            }

            $stmt->execute([
                'id' => $id,
                'status' => $status,
                'note' => $note
            ]);
        }
    }
    header('Location: ../../../page/admin/pengajuan-keluar.php');
    exit();
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<?php
// Koneksi PDO ke database
$host = 'localhost';
$dbname = 'kos_management';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}

// Jika terdapat `GET` ID untuk menampilkan data maintenance tertentu
if (isset($_GET['id'])) {
    $request_id = $_GET['id'];

    // Ambil data maintenance berdasarkan ID
    $stmt = $pdo->prepare("SELECT * FROM maintenance WHERE request_id = :request_id");
    $stmt->execute(['request_id' => $request_id]);
    $maintenance = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$maintenance) {
        echo "Maintenance request not found.";
        exit();
    }
}
?>
<?php
// Proses jika form di-submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form POST
    $request_id = $_POST['request_id'];
    $room_id = $_POST['room_id'];
    $description = $_POST['description'];
    $status = $_POST['status'];

    // Validasi input
    if (empty($status)) {
        echo "Status cannot be empty.";
        exit();
    }

    // Update status pada tabel maintenance
    try {
        $stmt = $pdo->prepare("UPDATE maintenance SET 
            status = :status,
            updated_at = NOW()
            WHERE request_id = :request_id");

        $stmt->execute([
            'status' => $status,
            'request_id' => $request_id
        ]);

        // Redirect dengan pesan sukses
        $_SESSION['toast_message'] = "Maintenance response updated successfully!";
        header('Location: ../../../page/admin/maintenance.php');
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit();
    }
}
?>

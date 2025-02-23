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
$stmt = $pdo->query("SELECT * FROM maintenance");
$maintenances = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($maintenances as $maintenance) {
    echo "<tr>";
    echo "<td>{$maintenance['request_id']}</td>";
    echo "<td>{$maintenance['room_id']}</td>";
    echo "<td>{$maintenance['description']}</td>";
    echo "<td>{$maintenance['status']}</td>";
    echo "<td>{$maintenance['requested_by']}</td>";
    echo "<td>{$maintenance['created_at']}</td>";
    echo "<td>";
    include 'path_to_modal_code.php'; // Ganti dengan file modal di atas
    echo "</td>";
    echo "</tr>";
}
?>


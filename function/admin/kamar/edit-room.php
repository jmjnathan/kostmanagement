<?php
// Koneksi PDO ke database (pastikan sudah ada di awal file)
$host = 'localhost';
$dbname = 'kos_management';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_GET['id'])) {
        $room_id = $_GET['id'];

        // Query untuk mendapatkan data kamar berdasarkan ID
        $stmt = $pdo->prepare("SELECT * FROM rooms WHERE id = :id");
        $stmt->execute(['id' => $room_id]);
        $room = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$room) {
            // Jika data tidak ditemukan, tampilkan error atau redirect ke halaman lain
            echo "Room not found.";
            exit();
        }
    }
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}
?>

<?php
// Koneksi PDO ke database (pastikan sudah ada di awal file)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form POST
    $roomId = $_POST['id'];
    $roomName = $_POST['name'];
    $roomType = $_POST['room_type'];
    $roomAc = $_POST['ac'];
    $roomCapacity = $_POST['capacity'];
    $roomPrice = $_POST['price'];
    $roomStatus = $_POST['status'];
    $roomDescription = $_POST['description'];

    // Validasi input (optional, Anda bisa menambahkan lebih banyak validasi)
    if (empty($roomName) || empty($roomType) || empty($roomAc) || empty($roomCapacity) || empty($roomPrice) || empty($roomStatus)) {
        echo "Please fill in all fields.";
        exit();
    }

    // Update data kamar di database
    try {
        $stmt = $pdo->prepare("UPDATE rooms SET 
            name = :name, 
            type = :type, 
            ac = :ac, 
            capacity = :capacity, 
            price = :price, 
            status = :status, 
            description = :description 
            WHERE id = :id");

        $stmt->execute([
            'name' => $roomName,
            'type' => $roomType,
            'ac' => $roomAc,
            'capacity' => $roomCapacity,
            'price' => $roomPrice,
            'status' => $roomStatus,
            'description' => $roomDescription,
            'id' => $roomId
        ]);

         // Set pesan ke session
         $_SESSION['toast_message'] = "Data berhasil diubah!";
         header('Location: ../../../page/admin/kamar.php');
         exit();
         
        // Redirect setelah sukses
         header('Location: ../../../page/admin/kamar.php');
          exit();

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit();
    }
}
?>

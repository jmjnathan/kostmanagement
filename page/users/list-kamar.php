<?php
session_start();
require '../../db.php';

// Cek apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit();
}

// Cek role pengguna
if ($_SESSION['role'] !== 'user') {
    header('Location: dashboard-admin.php');
    exit();
}

$host = 'localhost';
$dbname = 'kos_management';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Ambil daftar kamar dari tabel rooms
    $stmt = $pdo->query("SELECT * FROM rooms WHERE status = 1");
    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List Kamar</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Font & Boxicons -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="../../assets/logo/Kozie.png">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">

    <!-- Navbar -->
    <nav class="bg-gradient-to-r from-indigo-500 to-blue-500 text-white py-4 px-6 flex justify-between items-center">
        <div class="text-xl font-medium">
            <a href="#">KosKozie</a>
        </div>
        <ul class="hidden md:flex space-x-6">
            <li><a href="../../logout.php" class="flex items-center space-x-2 px-4 py-2 text-red-500 hover:text-red-700"><i class="bx bx-log-out text-xl"></i><span>Logout</span></a></li>
        </ul>
    </nav>

    <!-- Container -->
    <div class="max-w-4xl mx-auto mt-6 bg-white p-6 shadow-lg rounded-lg">
        <div class="mb-4 flex flex-col sm:flex-row justify-between items-center">
            <h2 class="text-2xl font-semibold text-center sm:text-left">Daftar Kamar</h2>
            <a href="dashboard-users.php" class="inline-flex items-center px-4 py-2 mt-2 sm:mt-0 bg-gray-500 text-white rounded-lg shadow-md hover:bg-gray-600">
                <i class='bx bx-arrow-back text-xl mr-2'></i> Kembali
            </a>
        </div>

        <!-- List Kamar -->
        <div class="space-y-6">
            <?php if (count($rooms) > 0): ?>
                <?php foreach ($rooms as $room): ?>
                    <div class="flex flex-col md:flex-row items-center bg-white shadow-md rounded-lg p-4 gap-4">
                        
                        <!-- Gambar Kamar -->
                        <img src="data:image/jpeg;base64,<?php echo base64_encode($room['foto']); ?>" alt="Room Image" class="w-full md:w-40 h-40 object-cover rounded-lg">
                        
                        <!-- Detail Kamar -->
                        <div class="text-center md:text-left flex-1">
                            <h3 class="text-lg font-semibold">No Kamar: <?php echo $room['name']; ?></h3>
                            <p class="text-sm text-gray-600"><?php echo $room['ac']; ?></p>
                        </div>

                        <!-- Harga & Status -->
                        <div class="text-center md:text-right">
                            <p class="text-lg font-semibold text-blue-600">Rp <?php echo number_format($room['price'], 0, ',', '.'); ?></p>
                            <p class="text-sm font-medium <?php echo ($room['status'] == 1) ? 'text-green-600' : 'text-red-600'; ?>">
                                <?php echo ($room['status'] == 1) ? 'Tersedia' : 'Tidak Tersedia'; ?>
                            </p>
                        </div>

                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center text-gray-500">Tidak ada kamar yang tersedia saat ini.</p>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>


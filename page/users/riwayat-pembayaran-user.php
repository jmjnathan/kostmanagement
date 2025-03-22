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

if (!isset($_SESSION['id_penghuni'])) {
    die("Error: User ID tidak ditemukan dalam sesi.");
}

$host = 'localhost';
$dbname = 'kos_management';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $session_username = $_SESSION['username'];
    $user_id = $_SESSION['id_penghuni'];
    
   //  // Ambil data penghuni
   //  $stmt = $pdo->prepare("SELECT A.nama, A.id, B.no_kamar 
   //                         FROM penghuni A
   //                         INNER JOIN rooms B ON A.room_id = B.id
   //                         WHERE A.id = :user_id");
   //  $stmt->execute(['user_id' => (int)$user_id]);
   //  $penghuni = $stmt->fetch(PDO::FETCH_ASSOC);
    
   //  if (!$penghuni) {
   //      die("Data penghuni tidak ditemukan.");
   //  }

    // Ambil riwayat pembayaran user
    $stmt = $pdo->prepare("SELECT * FROM pembayaran WHERE penghuni_id = :user_id AND status = 'lunas' ORDER BY tanggal_bayar DESC"); 
    $stmt->execute(['user_id' => (int)$user_id]);
    $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
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
    <title>Riwayat Pembayaran</title>
    
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
    <div class="max-w-4xl mx-auto mt-10 bg-white p-6 shadow-lg rounded-lg">
        <div class="mb-4 flex justify-between items-center">
            <h2 class="text-2xl font-semibold">Riwayat Pembayaran</h2>
            <a href="dashboard-users.php" class="inline-flex items-center px-4 py-2 bg-gray-500 text-white rounded-lg shadow-md hover:bg-gray-600">
                <i class='bx bx-arrow-back text-xl mr-2'></i> Kembali
            </a>
        </div>

        <!-- Tabel Pembayaran -->
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-300 rounded-lg">
                <thead class="bg-gray-200">
                    <tr class="text-left">
                        <th class="px-4 py-3 border">Tgl Bayar</th>
                        <th class="px-4 py-3 border">Metode Bayar</th>
                        <th class="px-4 py-3 border">Jumlah</th>
                        <th class="px-4 py-3 border">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($payments) > 0): ?>
                        <?php foreach ($payments as $payment): ?>
                            <tr class="border-t">
                                <td class="px-4 py-3 border"><?php echo date('d/m/Y', strtotime($payment['tanggal_bayar'])); ?></td>
                                <td class="px-4 py-3 border"><?php echo $payment['metode']; ?></td>
                                <td class="px-4 py-3 border">Rp <?php echo number_format($payment['jumlah'], 0, ',', '.'); ?></td>
                                <td class="px-4 py-3 border text-green-600 font-semibold"><?php echo $payment['status']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="px-4 py-3 text-center text-gray-500">Belum ada riwayat pembayaran.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>

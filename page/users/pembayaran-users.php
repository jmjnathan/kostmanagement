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
    
    // Query untuk mengambil data penghuni berdasarkan user_id
    $stmt = $pdo->prepare("SELECT A.* , B. * 
                           FROM penghuni A
                           INNER JOIN rooms B ON A.room_id = B.id
                           WHERE A.id = :user_id");
    $stmt->execute(['user_id' => (int)$user_id]);
    $penghuni = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$penghuni) {
        die("Data penghuni tidak ditemukan.");
    }

    $user_name = $penghuni['nama'] ?? 'User';
    $user_room = $penghuni['no_kamar'] ?? '-';
    $user_phone = $penghuni['no_telp'] ?? '-';
    
    // Query Pembayaran dengan PDO
    $stmt = $pdo->prepare("SELECT * FROM pembayaran WHERE penghuni_id = :user_id"); 
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
    <title>Dashboard Penghuni</title>
    
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

    <!-- Card Rincian Penghuni -->
    <div class="max-w-lg mx-auto mt-10 bg-white p-6 shadow-lg rounded-lg">
      <div class="mb-4">
         <a href="dashboard-users.php" class="inline-flex items-center px-4 py-2 bg-gray-500 text-white rounded-lg shadow-md hover:bg-gray-600">
               <i class='bx bx-arrow-back text-xl mr-2'></i>
         </a>
      </div>
        <h2 class="text-lg font-semibold mb-4">Informasi Penghuni</h2>
        <div class="mb-4">
            <p class="font-medium">Nama: <?php echo $user_name; ?></p>
            <p class="font-medium">No Kamar: <?php echo $user_room; ?></p>
            <p class="font-medium">No Telp: <?php echo $user_phone; ?></p>
        </div>
    </div>

    <!-- Card Rincian Pembayaran -->
    <div class="max-w-lg mx-auto mt-6 bg-white p-6 shadow-lg rounded-lg">
        <h2 class="text-lg font-semibold mb-4">Rincian Pembayaran Bulanan</h2>
        <?php foreach ($payments as $payment): ?>
            <div class="flex justify-between items-center border-b pb-2 mb-2">
                <div>
                    <p class="font-medium"><?php echo $bulan[date('F', strtotime($payment['jatuh_tempo']))]; ?></p>
                    <p class="text-gray-500 text-sm">Jatuh tempo: <?php echo date('d/m/y', strtotime($payment['jatuh_tempo'])); ?></p>
                </div>
                <p class="<?php echo ($payment['status'] == 'Dibayar') ? 'text-green-600' : 'text-red-600'; ?>">
                    <?php echo ($payment['status'] == 'Dibayar') ? 'Dibayar' : 'Belum Bayar'; ?>
                </p>
            </div>
        <?php endforeach; ?>
        <button class="mt-4 w-full bg-blue-500 text-white py-2 rounded-lg shadow-md hover:bg-blue-600">Bayar</button>
    </div>

<script>
    console.log("Riwayat Pembayaran:", <?php echo json_encode($payments); ?>);
    console.log('penghuni Id', <?php echo json_encode($penghuni); ?>);
</script>

</body>
</html>

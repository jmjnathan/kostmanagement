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
    var_dump($_SESSION['id_penghuni']);
exit();
}

$host = 'localhost';
$dbname = 'kos_management';
$username = 'root';
$password = '';

try{
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $session_username = $_SESSION['username'];
    
    // Query untuk mengambil nama user
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute(['username' => $session_username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $user_name = $user['name'] ?? 'User';
    $user_id = $user['id_penghuni'] ?? 0;

    // Ambil ID pengguna dari sesi
$month_year = isset($_GET['month_year']) ? $_GET['month_year'] : date('Y-m');
$limit = 10;
$offset = 0;

// Query Pembayaran dengan PDO
$sql = "SELECT 
            A.* 
        FROM pembayaran A 
        WHERE A.penghuni_id = :user_id
        LIMIT :limit OFFSET :offset"; 

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_id', (int)$user_id, PDO::PARAM_INT);
$stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
$stmt->execute();
$payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt_peraturan = $pdo->query("SELECT isi_peraturan FROM peraturan_kos");
$peraturan_kos = $stmt_peraturan->fetchAll(PDO::FETCH_COLUMN);

}catch(PDOException $e){
    echo "Connection failed: " . $e->getMessage();
}

?>
<?php
date_default_timezone_set('Asia/Jakarta');

// Array bulan dalam bahasa Indonesia
$bulan = [
    'January' => 'Januari',
    'February' => 'Februari',
    'March' => 'Maret',
    'April' => 'April',
    'May' => 'Mei',
    'June' => 'Juni',
    'July' => 'Juli',
    'August' => 'Agustus',
    'September' => 'September',
    'October' => 'Oktober',
    'November' => 'November',
    'December' => 'Desember'
];

// Ambil bulan dan tahun saat ini
$bulan_inggris = date('F');
$tahun = date('Y');

// Konversi bulan ke bahasa Indonesia
$bulan_tahun = $bulan[$bulan_inggris] . ' ' . $tahun;
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
        .dashboard-container {
            max-width: 800px;
            margin: auto;
            padding: 10px;
            border-radius: 10px;
            background-color: white;
        }
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
            gap: 10px;
            justify-content: center;
            padding: 10px;
        }
        .dashboard-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 10px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            font-size: 12px;
            font-weight: bold;
            text-align: center;
            transition: all 0.3s ease-in-out;
        }

        .dashboard-card i {
            font-size: 24px;
            margin-bottom: 5px;
        }

        /* Efek Hover */
        .dashboard-card:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            opacity: 0.9;
        }

        .dashboard-card:nth-child(1) { background-color: #4CAF50; color: white; }
        .dashboard-card:nth-child(2) { background-color: #FF9800; color: white; }
        .dashboard-card:nth-child(3) { background-color: #3F51B5; color: white; }
        .dashboard-card:nth-child(4) { background-color: #009688; color: white; }
        .dashboard-card:nth-child(5) { background-color: #9C27B0; color: white; }
        .dashboard-card:nth-child(6) { background-color: #F44336; color: white; }
       
    </style>
</head>
<body class="bg-gray-100 min-h-screen">

    <!-- Navbar -->
    <nav class="bg-gradient-to-r from-indigo-500 to-blue-500 text-white py-4 px-6 flex justify-between items-center">
        <div class="text-xl font-medium">
            <a href="#">KosKozie</a>
        </div>

        <!-- Menu untuk Desktop -->
        <ul class="hidden md:flex space-x-6">
            <li><a href="../../logout.php" class="flex items-center space-x-2 px-4 py-2 text-red-500 hover:text-red-700"><i class="bx bx-log-out text-xl"></i><span>Logout</span></a></li>
        </ul>

        <!-- Menu Mobile -->
        <button id="menu-toggle" class="md:hidden text-2xl focus:outline-none">
            <i class="bx bx-menu"></i>
        </button>

        <ul id="mobile-menu" class="hidden absolute top-full left-0 w-full bg-indigo-600 text-white py-4 px-6 space-y-3 md:hidden">
            <li><a href="dashboard-users.php" class="block px-4 py-2 hover:bg-indigo-700">Dashboard</a></li>
            <li><a href="pembayaran-users.php" class="block px-4 py-2 hover:bg-indigo-700">Bayar Kos</a></li>
            <li><a href="maintenance.php" class="block px-4 py-2 hover:bg-indigo-700">Perbaikan</a></li>
            <li><a href="pengajuan-keluar.php" class="block px-4 py-2 hover:bg-indigo-700">Keluar Kos</a></li>
            <li><a href="../../logout.php" class="block px-4 py-2 text-red-500 hover:bg-red-700">Logout</a></li>
        </ul>
    </nav>

    <div class="flex flex-col items-center mt-6 mx-4">
        <h1 class="text-2xl font-bold mb-2 text-center">Selamat Datang, <?php echo htmlspecialchars($user_name); ?>!</h1>
        <p class="text-base text-gray-600 mb-4 text-center">Dashboard Penghuni Kos</p>
    </div>

    <!-- Konten Dashboard -->
    <div class="dashboard-container mt-5 mb-5">
        <div class="dashboard-grid">
            <a href="pembayaran-users.php" class="dashboard-card">
                <i class='bx bx-wallet'></i>
                <p>Pembayaran</p>
            </a>
            <a href="riwayat-pembayaran-user.php" class="dashboard-card">
                <i class='bx bx-history'></i>
                <p>Riwayat</p>
            </a>
            <a href="list-kamar.php" class="dashboard-card">
                <i class='bx bx-home'></i>
                <p>List Kamar</p>
            </a>
            <a href="maintenance.php" class="dashboard-card">
                <i class='bx bx-wrench'></i>
                <p>Maintenance</p>
            </a>
            <!-- Akun Dropdown -->
            <div class="dashboard-card relative group">
                <button class="flex flex-col items-center justify-center p-2 rounded-lg font-bold text-sm text-white transition-all duration-300 ease-in-out">
                    <i class='bx bx-user text-2xl'></i>
                    <span>Akun</span>
                </button>

                <!-- Dropdown Menu -->
                <div class="absolute hidden group-hover:flex flex-col items-center justify-center w-40 bg-white rounded-lg shadow-lg mt-2 p-2 transition-all duration-300 ease-in-out">
                    <a href="edit-profile.php" class="w-full px-4 py-2 text-gray-700 text-center rounded-lg hover:bg-gray-100">Edit Profile</a>
                    <a href="ubah-password.php" class="w-full px-4 py-2 text-gray-700 text-center rounded-lg hover:bg-gray-100">Ubah Password</a>
                </div>
            </div>



            <a href="pengajuan-keluar.php" class="dashboard-card">
                <i class='bx bx-door-open'></i>
                <p>Pengajuan Keluar</p>
            </a>
        </div>
    </div>

    <!-- Table Riwayat Pembayaran -->
<div class="dashboard-container mt-5">
    <h2 class="text-lg md:text-xl font-semibold mb-4 text-center">
    Riwayat Pembayaran Bulan <?php echo ucfirst($bulan_tahun); ?>
    </h2>
    <div class="overflow-x-auto max-h-64 overflow-y-auto border rounded-lg">
        <table class="min-w-full table-auto border rounded-lg">
            <thead>
                <tr class="bg-gray-100 text-gray-700">
                    <th class="px-4 py-2 text-left font-medium">Tanggal Pembayaran</th>
                    <th class="px-4 py-2 text-left font-medium">Jumlah</th>
                    <th class="px-4 py-2 text-left font-medium">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($payments)): ?>
                    <tr>
                        <td colspan="3" class="px-4 py-2 text-center text-gray-500">
                            Data tidak ditemukan
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($payments as $payment): ?>
                        <tr class="border-b border-gray-300">
                            <td class="px-4 py-2">
                                <?= date_format(date_create($payment['tanggal_bayar']), 'd M Y'); ?>
                            </td>                            
                            <td class="px-4 py-2">Rp <?= number_format($payment['jumlah'], 0, ',', '.') ?></td>
                            <td class="px-4 py-2 text-green-500 font-bold"><?= htmlspecialchars($payment['status']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

    <!-- Table Peraturan -->
<div class="dashboard-container mt-5">
    <h2 class="text-lg md:text-xl font-semibold mb-4 text-center">
        Peraturan Kos
    </h2>
    <div class="overflow-x-auto max-h-64 overflow-y-auto rounded-lg p-4">
        <ol class="list-decimal pl-5">
            <?php if (!empty($peraturan_kos)): ?>
                <?php foreach ($peraturan_kos as $isi_peraturan): ?>
                    <li><?= htmlspecialchars($isi_peraturan) ?></li>
                <?php endforeach; ?>
            <?php else: ?>
                <li class="text-gray-500">Belum ada peraturan yang tersedia.</li>
            <?php endif; ?>
        </ol>
    </div>
</div>

<script>
    console.log("Riwayat Pembayaran:", <?php echo json_encode($payments); ?>);
    console.log('User Id', <?php echo json_encode($user_id); ?>);
</script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const userMenu = document.getElementById("user-menu");
        const dropdownMenu = document.getElementById("dropdown-menu");

        userMenu.addEventListener("click", function (event) {
            event.stopPropagation(); // Menghindari penutupan langsung saat klik
            dropdownMenu.classList.toggle("hidden");
        });

        document.addEventListener("click", function (event) {
            if (!userMenu.contains(event.target) && !dropdownMenu.contains(event.target)) {
                dropdownMenu.classList.add("hidden");
            }
        });
    });
</script>



</body>
</html>

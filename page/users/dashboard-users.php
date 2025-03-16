<?php
session_start();
require '../../db.php';

// Cek apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit();
}

// Ambil nama pengguna
$session_username = $_SESSION['username'];
$result = mysqli_query($conn, "SELECT name FROM users WHERE username = '$session_username'");
$user = mysqli_fetch_assoc($result);
$user_name = $user['name'] ?? 'Penghuni';
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
            border: 2px solid #ccc;
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
        .dashboard-card:nth-child(4) { background-color: #F44336; color: white; }
        .dashboard-card:nth-child(5) { background-color: #9C27B0; color: white; }
        .dashboard-card:nth-child(6) { background-color: #009688; color: white; }
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
    <div class="dashboard-container mt-5">
        <div class="dashboard-grid">
            <a href="pembayaran-users.php" class="dashboard-card">
                <i class='bx bx-wallet'></i>
                <p>Pembayaran</p>
            </a>
            <a href="riwayat-pembayaran.php" class="dashboard-card">
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
            <a href="pengajuan-keluar.php" class="dashboard-card">
                <i class='bx bx-door-open'></i>
                <p>Pengajuan Keluar</p>
            </a>
        </div>
    </div>

</body>
</html>

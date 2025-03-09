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
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css">
</head>
<body class="bg-gray-100 flex justify-center items-center min-h-screen">
    <div class="text-center">
        <h1 class="text-3xl font-bold mb-2">Selamat Datang, <?php echo htmlspecialchars($user_name); ?>!</h1>
        <p class="text-lg text-gray-600 mb-6">Dashboard Penghuni Kos</p>
        <div class="grid grid-cols-3 gap-6">
    <a href="pembayaran-users.php" class="block bg-gradient-to-r from-indigo-500 to-blue-500 p-6 rounded-lg shadow-md hover:shadow-lg transition text-white text-center">
        <i class='bx bx-wallet text-5xl mb-2'></i>
        <p class="mt-2 text-lg font-semibold">Pembayaran</p>
    </a>
    <a href="riwayat-pembayaran.php" class="block bg-gradient-to-r from-green-500 to-teal-500 p-6 rounded-lg shadow-md hover:shadow-lg transition text-white text-center">
        <i class='bx bx-history text-5xl mb-2'></i>
        <p class="mt-2 text-lg font-semibold">Riwayat Pembayaran</p>
    </a>
    <a href="list-kamar.php" class="block bg-gradient-to-r from-purple-500 to-pink-500 p-6 rounded-lg shadow-md hover:shadow-lg transition text-white text-center">
        <i class='bx bx-home text-5xl mb-2'></i>
        <p class="mt-2 text-lg font-semibold">List Kamar</p>
    </a>
    <a href="maintenance.php" class="block bg-gradient-to-r from-red-500 to-orange-500 p-6 rounded-lg shadow-md hover:shadow-lg transition text-white text-center">
        <i class='bx bx-wrench text-5xl mb-2'></i>
        <p class="mt-2 text-lg font-semibold">Pengajuan Maintenance</p>
    </a>
    <a href="kritik-saran.php" class="block bg-gradient-to-r from-yellow-500 to-amber-500 p-6 rounded-lg shadow-md hover:shadow-lg transition text-white text-center">
        <i class='bx bx-message-square-dots text-5xl mb-2'></i>
        <p class="mt-2 text-lg font-semibold">Kritik & Saran</p>
    </a>
    <a href="pengajuan-keluar.php" class="block bg-gradient-to-r from-gray-500 to-black p-6 rounded-lg shadow-md hover:shadow-lg transition text-white text-center">
        <i class='bx bx-door-open text-5xl mb-2'></i>
        <p class="mt-2 text-lg font-semibold">Pengajuan Keluar Kos</p>
    </a>
</div>

    </div>
</body>
</html>

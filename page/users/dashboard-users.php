<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit();
}

$host = 'localhost';  
$dbname = 'kos_management';  
$username = 'root'; 
$password = '';  

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $session_username = $_SESSION['username'];    
    $stmt = $pdo->prepare("SELECT name FROM users WHERE username = :username");
    $stmt->execute(['username' => $session_username]);
    
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    $admin_name = $admin['name'] ?? 'Admin';
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Penghuni</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="flex">
        <!-- Sidebar -->
        <div class="w-64 bg-gradient-to-r from-blue-500 to-indigo-500 text-white p-5 min-h-screen fixed">
            <h2 class="text-lg font-bold text-center mb-6">Dashboard Penghuni</h2>
            <ul class="space-y-4">
                <li><a href="#" class="block py-2 px-4 bg-white text-blue-500 rounded-md">Pembayaran</a></li>
                <li><a href="#" class="block py-2 px-4 hover:bg-blue-400 rounded-md">Riwayat Pembayaran</a></li>
                <li><a href="#" class="block py-2 px-4 hover:bg-blue-400 rounded-md">List Kamar</a></li>
                <li><a href="#" class="block py-2 px-4 hover:bg-blue-400 rounded-md">Pengajuan Maintenance</a></li>
                <li><a href="#" class="block py-2 px-4 hover:bg-blue-400 rounded-md">Kritik & Saran</a></li>
                <li><a href="#" class="block py-2 px-4 hover:bg-blue-400 rounded-md">Pengajuan Keluar Kos</a></li>
            </ul>
        </div>
        
        <!-- Main Content -->
        <div class="ml-64 p-8 w-full">
            <h1 class="text-2xl font-semibold text-gray-700 mb-6">Selamat Datang di Dashboard</h1>
            <div class="grid grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded-lg shadow-md text-center">
                    <h2 class="text-lg font-semibold">Pembayaran</h2>
                    <p class="text-gray-600">Lihat dan lakukan pembayaran kos.</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md text-center">
                    <h2 class="text-lg font-semibold">Riwayat Pembayaran</h2>
                    <p class="text-gray-600">Cek histori pembayaran kos Anda.</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md text-center">
                    <h2 class="text-lg font-semibold">List Kamar</h2>
                    <p class="text-gray-600">Lihat daftar kamar yang tersedia.</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md text-center">
                    <h2 class="text-lg font-semibold">Pengajuan Maintenance</h2>
                    <p class="text-gray-600">Ajukan perbaikan fasilitas kos.</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md text-center">
                    <h2 class="text-lg font-semibold">Kritik & Saran</h2>
                    <p class="text-gray-600">Berikan masukan untuk pengelola kos.</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md text-center">
                    <h2 class="text-lg font-semibold">Pengajuan Keluar Kos</h2>
                    <p class="text-gray-600">Ajukan keluar dari kos dengan mudah.</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

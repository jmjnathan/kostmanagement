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
            <a href="dashboard-users.php" class="inline-flex items-center px-4 py-2 mt-2 sm:mt-0 bg-gray-500 text-white rounded-lg shadow-md hover:bg-gray-600">
                <i class='bx bx-arrow-back text-xl mr-2'></i> Kembali
            </a>
        </div>

    <div class="max-w-lg mx-auto bg-white p-6 rounded-lg shadow-lg">
        <h2 class="text-xl font-bold text-center mb-4">Pengajuan Keluar Kos</h2>
        <form action="proses_pengajuan_keluar.php" method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-semibold">Tgl Pengajuan</label>
                <input type="date" name="tgl_pengajuan" class="w-full p-2 border rounded">
            </div>
            <div>
                <label class="block text-sm font-semibold">Nama Penghuni</label>
                <input type="text" name="nama_penghuni" class="w-full p-2 border rounded" required>
            </div>
            <div>
                <label class="block text-sm font-semibold">No Kamar</label>
                <input type="text" name="no_kamar" class="w-full p-2 border rounded" required>
            </div>
            <div>
                <label class="block text-sm font-semibold">Pengajuan Tgl Keluar</label>
                <input type="date" name="tgl_keluar" class="w-full p-2 border rounded" required>
            </div>
            <div>
                <label class="block text-sm font-semibold">Alasan</label>
                <textarea name="alasan" class="w-full p-2 border rounded" required></textarea>
            </div>
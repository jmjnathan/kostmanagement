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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@latest/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <title>Dashboard</title>
</head>
<body class="bg-gray-100 min-h-screen">

<style>
    body {
        font-family: 'Poppins', sans-serif;
    }
</style>

<!-- Sidebar -->
<div id="sidebar" class="hidden md:block w-72 h-full bg-white text-gray-800 fixed top-0 left-0 p-5  flex-col shadow-lg z-50">
    <div class="mb-6 text-center">
        <img src="../../assets/logo/Kozie.png" alt="Logo" class="h-20 mx-auto"> 
        <h1 class="text-lg font-semibold text-blue-800 mt-4 uppercase">
            Dashboard for Users
        </h1>
    </div>
    <nav>
    <ul class="space-y-4">
        <li>
            <a href="#" class="block px-4 py-2 rounded-md text-blue-500 font-semibold hover:bg-gray-100 flex items-center space-x-3">
                <i class="bx bx-home text-xl"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li>
            <a href="#" class="block px-4 py-2 rounded-md font-semibold hover:bg-gray-100 flex items-center space-x-3">
                <i class="bx bx-bed text-xl"></i>
                <span>Kamar</span>
            </a>
        </li>
        <li>
            <a href="#" class="block px-4 py-2 rounded-md font-semibold hover:bg-gray-100 flex items-center space-x-3">
                <i class="bx bx-user text-xl"></i>
                <span>Penghuni</span>
            </a>
        </li>
        <li>
            <a href="#" class="block px-4 py-2 rounded-md font-semibold hover:bg-gray-100 flex items-center space-x-3">
                <i class="bx bx-wallet text-xl"></i>
                <span>Pembayaran</span>
            </a>
        </li>
        <li>
            <a href="#" class="block px-4 py-2 rounded-md font-semibold hover:bg-gray-100 flex items-center space-x-3">
                <i class="bx bx-cabinet text-xl"></i>
                <span>Perabotan</span>
            </a>
        </li>
        <li>
            <a href="#" class="block px-4 py-2 rounded-md font-semibold hover:bg-gray-100 flex items-center space-x-3">
                <i class="bx bx-chat text-xl"></i>
                <span>Komplain</span>
            </a>
        </li>
        <li>
            <a href="#" class="block px-4 py-2 rounded-md font-semibold hover:bg-gray-100 flex items-center space-x-3">
                <i class="bx bx-wrench text-xl"></i>
                <span>Maintenance</span>
            </a>
        </li>
        <li>
            <a href="../../logout.php" class="block px-4 py-2 rounded-md text-red-500 hover:bg-red-100 flex items-center space-x-3">
                <i class="bx bx-log-out text-xl"></i>
                <span>Logout</span>
            </a>
        </li>
    </ul>
</nav>
</div>

<!-- Mobile Sidebar Toggle -->
<div class="md:hidden fixed top-4 left-4 z-50">
    <button id="toggle-sidebar" class="bg-blue-500 text-white p-2 rounded-md shadow">
        <i class="bx bx-menu text-xl"></i>
    </button>
</div>

<!-- Main Content -->
<div class="md:ml-72 flex flex-col min-h-screen">
    <!-- Navbar -->
    <nav class="flex items-center justify-between bg-white p-4 fixed top-0 left-0 md:left-72 right-0 shadow-md z-10">
        <form action="#" class="flex items-center space-x-2">
            <div class="relative">
                <input type="search" placeholder="Search..." class="border rounded-md px-4 py-2 w-full md:w-64">
                <button type="submit" class="absolute right-0 top-0 p-2">
                    <i class='bx bx-search text-xl'></i>
                </button>
            </div>
        </form>
        <div class="flex items-center space-x-4">
            <!-- Profile -->
            <a href="#" class="profile">
                <img src="../../assets/logo/user.png" alt="Profile" class="h-10 w-10 rounded-full">
            </a>
            <span class="text-sm md:text-lg font-semibold text-blue-700">
                Welcome, <?php echo htmlspecialchars($admin_name); ?>!
            </span>
        </div>
    </nav>

    <!-- Content -->
    <div class="p-8 mt-16">
        <h1 class="text-xl md:text-2xl font-semibold mb-6">Dashboard Overview</h1>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
            <!-- Card Example -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg md:text-xl font-semibold">Total Users</h2>
                <p class="mt-2 text-gray-600">150 Users</p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg md:text-xl font-semibold">Active Rooms</h2>
                <p class="mt-2 text-gray-600">45 Rooms</p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg md:text-xl font-semibold">Pending Payments</h2>
                <p class="mt-2 text-gray-600">5 Payments</p>
            </div>
        </div>
    </div>
</div>

<script>
    // Sidebar Toggle Script
    const sidebar = document.getElementById('sidebar');
    const toggleSidebar = document.getElementById('toggle-sidebar');

    toggleSidebar.addEventListener('click', () => {
        sidebar.classList.toggle('hidden');
    });
</script>

</body>
</html>

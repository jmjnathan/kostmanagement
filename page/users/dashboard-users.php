<?php
   session_start();

   // Cek apakah pengguna sudah login
   if (!isset($_SESSION['username'])) {
      header('Location: index.php'); // Jika belum login, arahkan ke halaman login
      exit();
   }

   // Cek role pengguna, jika bukan user, alihkan ke halaman lain
   if ($_SESSION['role'] !== 'user') {
      header('Location: ../../logout.php'); // Jika bukan user, arahkan ke dashboard user atau halaman lain
      exit();
   }
$host = 'localhost';  
$dbname = 'kos_management';  
$username = 'root'; 
$password = '';  

try {
    // Membuat koneksi ke database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $session_username = $_SESSION['username'];    
    $stmt = $pdo->prepare("SELECT name FROM users WHERE username = :username");
    $stmt->execute(['username' => $session_username]);
    
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $user_name = $user['name'] ?? 'User';
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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../../assets/logo/Kozie.png">
</head>
<body class="bg-gray-100 min-h-screen">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>

    <!-- Navbar -->
    <nav class="bg-gradient-to-r from-indigo-500 to-blue-500 text-white py-4 px-6 flex justify-between items-center relative">
        <div class="text-xl font-medium">
            <a href="#">KosKozie</a>
        </div>

        <!-- Menu untuk Desktop -->
        <ul class="hidden md:flex space-x-6">
            <li><a href="dashboard-users.php" class="flex items-center space-x-2 px-4 py-2 rounded-md font-medium hover:text-blue-300"><i class="bx bx-home text-xl"></i><span>Dashboard</span></a></li>
            <li><a href="dashboard-users.php" class="flex items-center space-x-2 px-4 py-2 rounded-md font-medium hover:text-blue-300"><i class="bx bx-money text-xl"></i><span>Bayar Kos</span></a></li>
            <li><a href="dashboard-users.php" class="flex items-center space-x-2 px-4 py-2 rounded-md font-medium hover:text-blue-300"><i class="bx bx-wrench text-xl"></i><span>Pengajuan Perbaikan</span></a></li>
            <li><a href="dashboard-users.php" class="flex items-center space-x-2 px-4 py-2 rounded-md font-medium hover:text-blue-300"><i class="bx bx-door-open text-xl"></i><span>Pengajuan Keluar Kos</span></a></li>
            <li><a href="dashboard-users.php" class="flex items-center space-x-2 px-4 py-2 rounded-md font-medium hover:text-blue-300"><i class="bx bx-cog text-xl"></i><span>Settings</span></a></li>
            <li><a href="../../logout.php" class="flex items-center space-x-2 px-4 py-2 rounded-md text-red-500 hover:text-red-700 font-medium"><i class="bx bx-log-out text-xl"></i><span>Logout</span></a></li>
        </ul>

        <!-- Menu Mobile -->
        <button id="menu-toggle" class="md:hidden text-2xl focus:outline-none">
            <i class="bx bx-menu"></i>
        </button>

        <ul id="mobile-menu" class="hidden absolute top-full left-0 w-full bg-indigo-600 text-white py-4 px-6 space-y-3 md:hidden">
            <li><a href="dashboard-users.php" class="block px-4 py-2 rounded-md font-medium hover:bg-indigo-700"><i class="bx bx-home text-xl"></i> Dashboard</a></li>
            <li><a href="dashboard-users.php" class="block px-4 py-2 rounded-md font-medium hover:bg-indigo-700"><i class="bx bx-money text-xl"></i> Bayar Kos</a></li>
            <li><a href="dashboard-users.php" class="block px-4 py-2 rounded-md font-medium hover:bg-indigo-700"><i class="bx bx-wrench text-xl"></i> Pengajuan Perbaikan</a></li>
            <li><a href="dashboard-users.php" class="block px-4 py-2 rounded-md font-medium hover:bg-indigo-700"><i class="bx bx-door-open text-xl"></i> Pengajuan Keluar Kos</a></li>
            <li><a href="dashboard-users.php" class="block px-4 py-2 rounded-md font-medium hover:bg-indigo-700"><i class="bx bx-cog text-xl"></i> Settings</a></li>
            <li><a href="../../logout.php" class="block px-4 py-2 rounded-md text-red-500 hover:bg-red-600"><i class="bx bx-log-out text-xl"></i> Logout</a></li>
        </ul>

        <div class="hidden sm:block">
            <span class="text-sm md:text-base lg:text-lg font-medium truncate">
                Welcome, <?php echo htmlspecialchars($user_name); ?>!
            </span>
        </div>

    </nav>

    <!-- Script untuk Menu Toggle -->
    <script>
        document.getElementById("menu-toggle").addEventListener("click", function() {
            document.getElementById("mobile-menu").classList.toggle("hidden");
        });
    </script>
</body>
</html>

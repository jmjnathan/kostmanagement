<?php
   session_start();

   // Cek apakah pengguna sudah login
   if (!isset($_SESSION['username'])) {
      header('Location: index.php'); // Jika belum login, arahkan ke halaman login
      exit();
   }

   // Cek role pengguna, jika bukan admin, alihkan ke halaman lain
   if ($_SESSION['role'] !== 'superadmin') {
      header('Location: ../../logout.php'); // Jika bukan admin, arahkan ke dashboard user atau halaman lain
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
    
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    $admin_name = $admin['name'] ?? 'Super Admin';
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
    <script src="https://cdn.jsdelivr.net/npm/boxicons@2.1.2/dist/boxicons.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <!-- Boxicons for icons -->
    <title>Dashboard</title>
</head>
<body class="bg-gray-100 h-screen flex flex-col">

   <style>
      body {
         font-family: 'Poppins', sans-serif;
      }
   </style>

   <!-- SIDEBAR -->
   <div class="w-72 h-full bg-white text-slate-800 fixed top-0 left-0 p-5 flex flex-col">
      <div class="grid mb-6 place-items-center">
         <!-- Brand Section -->
         <img src="../../assets/logo/Kozie.png" alt="Logo" class="h-40 w-auto"> 
         <span class="text-lg font-semibold text-blue-500">
               Welcome, <?php echo htmlspecialchars($admin_name); ?>!
         </span>
      </div>
      <nav class="flex-1">
         <ul class="space-y-2">
            <li><a href="#" class="block px-4 py-2 rounded-md text-blue-500">Dashboard</a></li>
            <li><a href="#" class="block px-4 py-2 rounded-md">Users</a></li>
            <li><a href="#" class="block px-4 py-2 rounded-md">Settings</a></li>
            <li><a href="../../logout.php" class="block px-4 py-2 rounded-md">Logout</a></li>
         </ul>
      </nav>
   </div>
   <!-- SIDEBAR -->

   <!-- NAVBAR -->
   <div class="ml-72 w-screen fixed">
      <nav class="flex items-center justify-between bg-white shadow-md p-4 ">
         <i class='bx bx-menu text-2xl cursor-pointer'></i>
      <!-- Search Form -->
      <!-- <div class="justify-between"> -->
         <form action="#" class="flex items-center space-x-2">
            <div class="form-input relative">
               <input type="search" placeholder="Search..." class="border rounded-md px-4 py-2 w-64">
               <button type="submit" class="absolute right-0 top-0 p-2">
                  <i class='bx bx-search text-xl'></i>
               </button>
            </div>
         </form>
         <!-- Profile -->
         <a href="#" class="profile">
            <img src="user.png" alt="Profile" class="h-8 w-8 rounded-full">
         </a>
         </div>
   </nav>

   <!-- NAVBAR -->

   <!-- MAIN CONTENT -->
      Ini dashboard Super Admin
   <!-- MAIN CONTENT -->      
   </div>
</body>
</html>

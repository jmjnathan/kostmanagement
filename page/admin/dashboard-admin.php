<?php
      session_start();

      // Cek apakah pengguna sudah login
      if (!isset($_SESSION['username'])) {
         header('Location: index.php'); // Jika belum login, arahkan ke halaman login
         exit();
      }

      // Cek role pengguna, jika bukan admin, alihkan ke halaman lain
      if ($_SESSION['role'] !== 'admin') {
         header('Location: dashboard-user.php'); // Jika bukan admin, arahkan ke dashboard user atau halaman lain
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

         // Query untuk menghitung total kamar
         $totalRoomsQuery = $pdo->query("SELECT COUNT(*) as total_rooms FROM rooms");
         $totalRooms = $totalRoomsQuery->fetch(PDO::FETCH_ASSOC)['total_rooms'];

         //Query Untuk Menghitung Total Penghuni
         $totalPenghuniQuery = $pdo->query("SELECT COUNT(*) as total_penghuni FROM penghuni");
         $totalPenghuni = $totalPenghuniQuery->fetch(PDO::FETCH_ASSOC)['total_penghuni'];

         // Query untuk menghitung kamar kosong (status 'Tersedia')
         $emptyRoomsQuery = $pdo->query("SELECT COUNT(*) as empty_rooms FROM rooms WHERE status = '1'");
         $emptyRooms = $emptyRoomsQuery->fetch(PDO::FETCH_ASSOC)['empty_rooms'];

         // Query untuk menghitung kamar kosong (status 'Tersedia')
         $fixRoomsQuery = $pdo->query("SELECT COUNT(*) as fix_rooms FROM rooms WHERE status = '2'");
         $fixRooms = $fixRoomsQuery->fetch(PDO::FETCH_ASSOC)['fix_rooms'];

         // Query untuk menghitung total uang masuk bulan ini
         $currentMonth = date('Y-m');
         $totalIncomeQuery = $pdo->prepare("SELECT SUM(jumlah) as total_income FROM pembayaran WHERE DATE_FORMAT(created_at, '%Y-%m') = :currentMonth AND status = 'lunas'");
         $totalIncomeQuery->execute(['currentMonth' => $currentMonth]);
         $totalIncome = $totalIncomeQuery->fetch(PDO::FETCH_ASSOC)['total_income'] ?? 0;

         
      } catch (PDOException $e) {
         echo 'Connection failed: ' . $e->getMessage();
         exit();
      }

      ?>
<?php
setlocale(LC_TIME, 'id_ID.UTF-8'); // Mengatur locale ke bahasa Indonesia
date_default_timezone_set('Asia/Jakarta'); // Menetapkan timezone Jakarta

$bulan_tahun = strftime('%B %Y'); // Menampilkan bulan dan tahun
?>

<!DOCTYPE html>
      <html lang="en">
      <head>
         <meta charset="UTF-8">
         <meta name="viewport" content="width=device-width, initial-scale=1.0">
         <script src="https://cdn.tailwindcss.com"></script>        
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
      <div id="sidebar" class="hidden md:block w-72 h-full bg-gradient-to-r from-indigo-500 to-blue-500 text-gray-800 fixed top-0 left-0 p-5  flex-col shadow-lg z-50">
         <div class="mb-6 text-center">
            <img src="../../assets/logo/Kozie.png" alt="Logo" class="h-20 mx-auto rounded-full"> 
            <h1 class="text-lg font-semibold text-white mt-4 uppercase">
                  Dashboard for Admin
            </h1>
         </div>
         <nav>
         <ul class="space-y-4">
            <li><a href="dashboard-admin.php" class="block px-4 py-2 rounded-md text-white font-semibold bg-tr hover:text-blue-300 items-center space-x-3 shadow-lg "><i class="bx bx-home text-xl"></i><span>Dashboard Overview</span></a></li>
            <li><a href="kamar.php" class="block px-4 py-2 rounded-md font-semibold text-white hover:text-blue-300  items-center space-x-3"><i class="bx bx-bed text-xl"></i><span>Kamar</span></a></li>
            <li><a href="penghuni.php" class="block px-4 py-2 rounded-md font-semibold text-white hover:text-blue-300  items-center space-x-3"><i class="bx bx-user text-xl"></i><span>Penghuni</span></a></li>
            <li><a href="pembayaran.php" class="block px-4 py-2 rounded-md font-semibold text-white hover:text-blue-300  items-center space-x-3"><i class="bx bx-wallet text-xl"></i><span>Pembayaran</span></a></li>
            <li><a href="komplain.php" class="block px-4 py-2 rounded-md font-semibold text-white hover:text-blue-300  items-center space-x-3"><i class="bx bx-chat text-xl"></i><span>Komplain</span></a></li>
            <li><a href="maintenance.php" class="block px-4 py-2 rounded-md font-semibold text-white hover:text-blue-300  items-center space-x-3"><i class="bx bx-wrench text-xl"></i><span>Maintenance</span></a></li>
            <li><a href="broadcast.php" class="block px-4 py-2 rounded-md font-semibold  text-white hover:text-blue-300  items-center space-x-3"><i class="bx bx-bell text-xl"></i><span>Broadcast Notifikasi</span></a></li>
            <li><a href="kritik-saran.php" class="block px-4 py-2 rounded-md font-semibold text-white hover:text-blue-300  items-center space-x-3"><i class="bx bx-message-detail text-xl"></i><span>Kritik dan Saran</span></a></li>
            <li><a href="../../logout.php" class="block px-4 py-2 rounded-md text-red-500 hover:text-red-700  items-center space-x-3 font-semibold"><i class="bx bx-log-out text-xl"></i><span>Logout</span></a></li>
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
         <nav class="flex items-center justify-end bg-white p-4 fixed top-0 left-0 md:left-72 right-0 shadow-md z-10">
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
         <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6 mb-5">
            <!-- Total Penghuni -->
            <div class="bg-blue-500 rounded-lg shadow-md p-6">
                  <div class="flex items-center space-x-4">
                     <i class="bx bx-user text-2xl text-white"></i>
                     <div>
                        <h2 class="text-md text-white md:text-xl font-semibold">Total Penghuni</h2>
                        <p class="mt-2 text-white">
                           <?php echo htmlspecialchars($totalPenghuni) . ' Penghuni'; ?>
                        </p></p>
                     </div>
                  </div>
            </div>
            <!-- Pending Payments -->
            <div class="bg-red-500 rounded-lg shadow-md p-6">
                  <div class="flex items-center space-x-4">
                     <i class="bx bx-wallet text-3xl text-white"></i>
                     <div>
                        <h2 class="text-lg text-white md:text-xl font-semibold">Transaksi Pending</h2>
                        <p class="mt-2 text-white">5 Transaksi</p>
                     </div>
                  </div>
            </div>

            <div class="bg-orange-500 rounded-lg shadow-md p-6">
               <a href="kamar.php" class="flex items-center space-x-4">
                  <i class="bx bx-wrench text-3xl text-white"></i>
                  <div>
                     <h2 class="text-lg text-white md:text-xl font-semibold">Kamar Diperbaiki</h2>
                     <p class="mt-2 text-white">
                        <?php echo htmlspecialchars($fixRooms) . ' / ' . htmlspecialchars($totalRooms) . ' Kamar'; ?>                       
                     </p>
                  </div>
               </a>
            </div>

            <!-- Empty Rooms -->
            <div class="bg-green-500 rounded-lg shadow-md p-6">
               <a href="kamar.php" class="flex items-center space-x-4">
                  <i class="bx bx-home text-3xl text-white"></i>
                  <div>
                     <h2 class="text-lg text-white md:text-xl font-semibold">Kamar Kosong</h2>
                     <p class="mt-2 text-white">
                        <?php echo htmlspecialchars($emptyRooms) . ' / ' . htmlspecialchars($totalRooms) . ' Kamar'; ?>                       
                     </p>
                  </div>
               </a>
            </div>

            <!-- Total Uang Masuk Bulan Ini -->
            <div class="bg-purple-500 rounded-lg shadow-md p-6">
               <div class="flex items-center space-x-4">
                  <i class="bx bx-money text-3xl text-white"></i>
                  <div>
                     <h2 class="text-lg text-white md:text-xl font-semibold">Uang Masuk</h2>
                     <p class="mt-2 text-white">
                        Rp <?php echo number_format($totalIncome, 0, ',', '.'); ?>
                     </p>
                  </div>
               </div>
            </div>


            
         </div>

<!-- Table Riwayat Pembayaran -->
         <div class="bg-white p-4 rounded-lg shadow-md">
            <h2 class="text-lg md:text-xl font-semibold mb-4">
               Riwayat Pembayaran Bulan <?php echo ucfirst($bulan_tahun); ?>
            </h2>
            <div class="overflow-x-auto">
               <table class="min-w-full table-auto">
                     <thead>
                        <tr class="bg-gray-100">
                           <th class="px-4 py-2 text-left font-medium">Nama Penghuni</th>
                           <th class="px-4 py-2 text-left font-medium">No Kamar</th>
                           <th class="px-4 py-2 text-left font-medium">Tanggal Pembayaran</th>
                           <th class="px-4 py-2 text-left font-medium">Status</th>
                        </tr>
                     </thead>
                     <tbody>
                        <?php if (empty($payments)): ?>
                           <tr>
                                 <td colspan="5" class="px-4 py-2 text-center text-gray-500">
                                    Data tidak ditemukan
                                 </td>
                           </tr>
                        <?php else: ?>
                           <?php foreach ($payments as $payment): ?>
                                 <tr class="border-b">
                                    <td class="px-4 py-2"><?= htmlspecialchars($payment['user_id']) ?></td>
                                    <td class="px-4 py-2"><?= number_format($payment['amount'], 2) ?></td>
                                    <td class="px-4 py-2"><?= date('d-m-Y', strtotime($payment['payment_date'])) ?></td>
                                    <td class="px-4 py-2"><?= htmlspecialchars($payment['status']) ?></td>
                                 </tr>
                           <?php endforeach; ?>
                        <?php endif; ?>
                     </tbody>
               </table>
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

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

         // Query pending payment
         $pendingPaymentsQuery = $pdo->query("SELECT COUNT(*) as pending_payments FROM pembayaran WHERE status = 'pending'");
         $pendingPayments = $pendingPaymentsQuery->fetch(PDO::FETCH_ASSOC)['pending_payments'] ?? 0;
         
         // Query untuk yg belum bayar 

         $month_year = date('Y-m'); // Ambil bulan dan tahun saat ini dalam format YYYY-MM

         $belumBayar = $pdo->prepare("SELECT COUNT(*) AS total_belum_bayar
                                    FROM penghuni B
                                    INNER JOIN rooms C ON B.room_id = C.id
                                    LEFT JOIN pembayaran A ON B.id = A.penghuni_id 
                                    AND DATE_FORMAT(A.tanggal_bayar, '%Y-%m') = :month_year
                                    WHERE A.penghuni_id IS NULL");

         $belumBayar->bindValue(':month_year', $month_year);
         $belumBayar->execute();

         $totalBelumBayar = $belumBayar->fetch(PDO::FETCH_ASSOC)['total_belum_bayar'] ?? 0;


         // Tangkap filter bulan-tahun dari GET request
         $month_year = isset($_GET['month_year']) ? $_GET['month_year'] : '';

         // Pagination
         $limit = 10; // Jumlah data per halaman
         $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
         $offset = ($page - 1) * $limit;

         $sql = "SELECT 
                     A.*, 
                     B.nama AS nama_penghuni, 
                     B.nomor_telepon AS penghuni_nomor_telepon,
                     C.name AS nomor_kamar
               FROM pembayaran A 
               INNER JOIN penghuni B ON A.penghuni_id = B.id
               INNER JOIN rooms C ON B.room_id = C.id
               WHERE (:month_year = '' OR DATE_FORMAT(A.tanggal_bayar, '%Y-%m') = :month_year)
               ORDER BY A.tanggal_bayar DESC
               LIMIT :limit OFFSET :offset";

         $stmt = $pdo->prepare($sql);
         $stmt->bindValue(':month_year', $month_year, PDO::PARAM_STR);
         $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
         $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
         $stmt->execute();
         $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

         $month_year = isset($_GET['month_year']) ? $_GET['month_year'] : date('Y-m'); // Default bulan ini

   // Query untuk mendapatkan penghuni yang belum membayar
         $sql = "SELECT 
               B.nama AS nama_penghuni, 
               B.nomor_telepon AS penghuni_nomor_telepon,
               C.name AS nomor_kamar,
               B.tanggal_masuk,
               DATEDIFF(CURDATE(), B.tanggal_masuk) AS lama_hari,
               TIMESTAMPDIFF(MONTH, B.tanggal_masuk, CURDATE()) AS lama_bulan
            FROM penghuni B
            INNER JOIN rooms C ON B.room_id = C.id
            LEFT JOIN pembayaran A ON B.id = A.penghuni_id AND DATE_FORMAT(A.tanggal_bayar, '%Y-%m') = :month_year
            WHERE A.penghuni_id IS NULL"; // Hanya yang belum bayar di bulan tertentu

         $stmt_bayar = $pdo->prepare($sql);
         $stmt_bayar->bindValue(':month_year', $month_year);
         $stmt_bayar->execute();
         $belum_bayar = $stmt_bayar->fetchAll(PDO::FETCH_ASSOC);


         
      } catch (PDOException $e) {
         echo 'Connection failed: ' . $e->getMessage();
         exit();
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
      <html lang="en">
      <head>
         <meta charset="UTF-8">
         <meta name="viewport" content="width=device-width, initial-scale=1.0">
         <script src="https://cdn.tailwindcss.com"></script>        
         <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
         <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
         <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
         <title>KosKozie</title>
         <link rel="icon" type="image/png" class="rounded-full" href="../../assets/logo/Kozie.png">
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
            <h1 class="text-lg font-medium text-white mt-4 uppercase">
                  Dashboard for Admin
            </h1>
         </div>
         <nav>
         <ul class="space-y-4">
            <li><a href="dashboard-admin.php" class="block px-4 py-2 rounded-md text-white font-medium bg-tr hover:text-blue-300 items-center space-x-3 shadow-lg "><i class="bx bx-home text-xl"></i><span>Dashboard Overview</span></a></li>
            <li><a href="kamar.php" class="block px-4 py-2 rounded-md font-medium text-white hover:text-blue-300  items-center space-x-3"><i class="bx bx-bed text-xl"></i><span>Kamar</span></a></li>
            <li><a href="penghuni.php" class="block px-4 py-2 rounded-md font-medium text-white hover:text-blue-300  items-center space-x-3"><i class="bx bx-user text-xl"></i><span>Penghuni</span></a></li>
            <li><a href="pembayaran.php" class="block px-4 py-2 rounded-md font-medium text-white hover:text-blue-300  items-center space-x-3"><i class="bx bx-wallet text-xl"></i><span>Pembayaran</span></a></li>
            <li>
               <a href="belum-bayar.php" class="block px-4 py-2 rounded-md font-medium text-white hover:text-blue-300 items-center space-x-3">
                  <i class="bx bx-time-five text-xl"></i>
                  <span>Belum Bayar</span>
               </a>
            </li>
            <li><a href="maintenance.php" class="block px-4 py-2 rounded-md font-medium text-white hover:text-blue-300  items-center space-x-3"><i class="bx bx-wrench text-xl"></i><span>Maintenance</span></a></li>
            <li><a href="broadcast.php" class="block px-4 py-2 rounded-md font-medium  text-white hover:text-blue-300  items-center space-x-3"><i class="bx bx-bell text-xl"></i><span>Broadcast Notifikasi</span></a></li>
            <li><a href="pengajuan-keluar.php" class="block px-4 py-2 rounded-md font-medium text-white hover:text-blue-300 items-center space-x-3"><i class="fa-solid fa-person-walking-arrow-right text-md"></i><span>Pengajuan Keluar Kos</span></a></li>
            <li><a href="peraturan.php" class="block px-4 py-2 rounded-md font-medium text-white hover:text-blue-300 items-center space-x-3"><i class="bx bx-info-circle text-xl"></i><span>Peraturan</span></a></li>
            <!-- <li><a href="pengguna.php" class="block px-4 py-2 rounded-md font-medium text-white hover:text-blue-300  items-center space-x-3"><i class="bx bx-group text-xl"></i><span>Pengguna</span></a></li> -->

            <li><a href="../../logout.php" class="block px-4 py-2 rounded-md text-red-500 hover:text-red-700  items-center space-x-3 font-medium"><i class="bx bx-log-out text-xl"></i><span>Logout</span></a></li>
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
                  <span class="text-sm md:text-lg font-medium text-blue-700">
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
               <a href="penghuni.php" class="block">
                  <div class="flex items-center space-x-4">
                     <i class="bx bx-user text-2xl text-white"></i>
                     <div>
                        <h2 class="text-md text-white md:text-xl font-medium">Total Penghuni</h2>
                        <p class="mt-2 text-white">
                           <?php echo htmlspecialchars($totalPenghuni) . ' Penghuni'; ?>
                        </p>                     
                     </div>
                  </div>
                  </a>
               </div>
            <!-- Pending Payments -->
            <div class="bg-red-500 rounded-lg shadow-md p-6">
               <a href="belum-bayar.php" class="flex items-center space-x-4">
                  <div class="flex items-center space-x-4">
                     <i class="bx bx-wallet text-3xl text-white"></i>
                     <div>
                        <h2 class="text-lg text-white md:text-xl font-medium">Belum Bayar</h2>
                        <p class="mt-2 text-white"><?php echo htmlspecialchars($totalBelumBayar) . ' Orang'; ?></p>
                     </div>
                  </div>
               </a>
            </div>

            <div class="bg-orange-500 rounded-lg shadow-md p-6">
               <a href="maintenance.php" class="flex items-center space-x-4">
                  <i class="bx bx-wrench text-3xl text-white"></i>
                  <div>
                     <h2 class="text-lg text-white md:text-xl font-medium">Diperbaiki</h2>
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
                     <h2 class="text-lg text-white md:text-xl font-medium">Kosong</h2>
                     <p class="mt-2 text-white">
                        <?php echo htmlspecialchars($emptyRooms) . ' / ' . htmlspecialchars($totalRooms) . ' Kamar'; ?>                       
                     </p>
                  </div>
               </a>
            </div>         
         </div>

         <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 gap-6 mb-5">
            <!-- Total Uang Masuk Bulan Ini -->
            <div class="bg-purple-500 rounded-lg shadow-md p-6">
               <div class="flex items-center space-x-4">
                  <i class="bx bx-money text-3xl text-white"></i>
                  <div>
                     <h2 class="text-lg text-white md:text-xl font-medium">Uang Masuk Bulan <?php echo ucfirst($bulan_tahun); ?></h2>
                     <p class="mt-2 text-white">
                        Rp <?php echo number_format($totalIncome, 0, ',', '.'); ?>
                     </p>
                  </div>
               </div>
            </div>
         </div>

<!-- Table Riwayat Pembayaran -->
   <div class="bg-white p-4 rounded-lg shadow-md mb-5">
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
                    <th class="px-4 py-2 text-left font-medium">Jumlah</th>
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
                            <td class="px-4 py-2"><?= htmlspecialchars($payment['nama_penghuni']) ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($payment['nomor_kamar']) ?></td>
                            <td class="px-4 py-2">
                              <?php 
                                 echo date_format(date_create($payment['tanggal_bayar']), 'd M Y'); 
                              ?>                              
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

<!-- Tabel Belum Bayar -->
<div class="bg-white p-4 rounded-lg shadow-md">
      <h2 class="text-lg md:text-xl font-semibold mb-4">
         Daftar Tunggakan Pembayaran Bulan <?php echo ucfirst($bulan_tahun); ?>
      </h2>
    <div class="overflow-x-auto">
        <table class="min-w-full table-auto">
            <thead>
                <tr class="bg-gray-100">
                    <th class="px-4 py-2 text-left font-medium">Nama Penghuni</th>
                    <th class="px-4 py-2 text-left font-medium">Lama</th>
                    <th class="px-4 py-2 text-left font-medium">Hubungi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($belum_bayar)): ?>
                    <tr>
                        <td colspan="5" class="px-4 py-2 text-center text-gray-500">
                            Data tidak ditemukan
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($belum_bayar as $belum_bayar): ?>
                        <tr class="border-b">
                        <td class="px-4 py-2">
                           <div class="font-bold"><?= htmlspecialchars($belum_bayar['nama_penghuni']) ?></div>
                           <div class="text-sm text-gray-600"><?= htmlspecialchars($belum_bayar['nomor_kamar']) ?></div>
                        </td>   
                        <td class="px-4 py-2 text-red-500 font-semibold"><?= $belum_bayar['lama_hari'] . ' hari (' . $belum_bayar['lama_bulan'] . ' bulan)'; ?></td>          
                           
                        <td class="px-4 py-2">
                                          <?php 
                                             // Ubah format nomor telepon dari 08... menjadi +628...
                                             $nomor_wa = preg_replace('/^08/', '+628', htmlspecialchars($belum_bayar['penghuni_nomor_telepon'])); 
                                          ?>
                                          <a href="https://wa.me/<?= $nomor_wa ?>" target="_blank" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded-md shadow">
                                             <i class="bx bxl-whatsapp"></i> Hubungi
                                          </a>
                                       </td>                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
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

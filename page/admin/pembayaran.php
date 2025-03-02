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

   // Ambil nama admin
   $session_username = $_SESSION['username'];
   $stmt = $pdo->prepare("SELECT name FROM users WHERE username = :username");
   $stmt->execute(['username' => $session_username]);
   $admin = $stmt->fetch(PDO::FETCH_ASSOC);
   $admin_name = $admin['name'] ?? 'Admin';

   // Pagination default
   $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10; // Default 10
   $page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Default halaman 1
   $offset = ($page - 1) * $limit;

   // Ambil parameter filter dari query string
   $penghuni_id = isset($_GET['penghuni_id']) ? $_GET['penghuni_id'] : '';
   $month_year = isset($_GET['month_year']) ? $_GET['month_year'] : ''; // YYYY-MM

   // Query total data untuk pagination (dengan filter)
   $total_sql = "SELECT COUNT(*) FROM pembayaran 
                 WHERE penghuni_id LIKE :penghuni_id
                 AND (:month_year = '' OR DATE_FORMAT(tanggal_bayar, '%Y-%m') = :month_year)";

   $total_stmt = $pdo->prepare($total_sql);
   $total_stmt->bindValue(':penghuni_id', "%$penghuni_id%");
   $total_stmt->bindValue(':month_year', $month_year);
   $total_stmt->execute();
   $total_data = $total_stmt->fetchColumn();
   $total_pages = ceil($total_data / $limit);

   // Query data pembayaran dengan filter dan pagination
               $sql = "SELECT 
               A.*, 
               B.nama AS nama_penghuni, 
               B.nomor_telepon AS penghuni_nomor_telepon ,
               C.name AS nomor_kamar
            FROM pembayaran A 
            INNER JOIN penghuni B ON A.penghuni_id = B.id
            INNER JOIN rooms C on B.room_id = C.id
           WHERE penghuni_id LIKE :penghuni_id
           AND (:month_year = '' OR DATE_FORMAT(tanggal_bayar, '%Y-%m') = :month_year)
           ORDER BY tanggal_bayar DESC
           LIMIT :limit OFFSET :offset";

   $stmt_bayar = $pdo->prepare($sql);
   $stmt_bayar->bindValue(':penghuni_id', "%$penghuni_id%");
   $stmt_bayar->bindValue(':month_year', $month_year);
   $stmt_bayar->bindValue(':limit', $limit, PDO::PARAM_INT);
   $stmt_bayar->bindValue(':offset', $offset, PDO::PARAM_INT);
   $stmt_bayar->execute();
   $bayar = $stmt_bayar->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
   echo 'Connection failed: ' . $e->getMessage();
   exit();
}
?>


<?php
session_start();
if (isset($_SESSION['toast_message'])) {
    echo "<script>
        window.onload = function() {
            showToast('" . $_SESSION['toast_message'] . "');
        }
    </script>";
    unset($_SESSION['toast_message']); // Hapus pesan setelah ditampilkan
}
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
   /* Menambahkan z-index pada modal dan tombol */
   #bayar-modal {
      z-index: 1050; /* Atur modal agar berada di atas */
   }
   #bayar-modal-edit{
      z-index: 1050; /* Atur modal agar berada di atas */
   }
   #close-modal-cancel {
      z-index: 1060; /* Pastikan tombol Batal berada di atas modal */
   }

   #toast {
      visibility: hidden; /* Awalnya tersembunyi */
      min-width: 250px;
      background-color: #28a745; /* Warna hijau */
      color: white; /* Teks putih */
      text-align: center;
      border-radius: 5px; /* Sudut membulat */
      padding: 16px;
      position: fixed; /* Tetap di satu posisi */
      z-index: 1000; /* Agar tampil di atas elemen lain */
      bottom: 20px; /* Jarak dari bawah */
      right: 20px; /* Jarak dari kanan */
      font-size: 17px; /* Ukuran font */
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); 
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
            <li><a href="dashboard-admin.php" class="block px-4 py-2 rounded-md text-white font-semibold bg-tr hover:text-blue-300 items-center space-x-3  "><i class="bx bx-home text-xl"></i><span>Dashboard Overview</span></a></li>
            <li><a href="kamar.php" class="block px-4 py-2 rounded-md font-semibold text-white hover:text-blue-300  items-center space-x-3"><i class="bx bx-bed text-xl"></i><span>Kamar</span></a></li>
            <li><a href="penghuni.php" class="block px-4 py-2 rounded-md font-semibold text-white hover:text-blue-300  items-center space-x-3"><i class="bx bx-user text-xl"></i><span>Penghuni</span></a></li>
            <li><a href="pembayaran.php" class="block px-4 py-2 rounded-md font-semibold text-white hover:text-blue-300  shadow-lg items-center space-x-3"><i class="bx bx-wallet text-xl"></i><span>Pembayaran</span></a></li>
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
   <nav class="flex items-center justify-end bg-white p-4 fixed top-0  md:left-72 right-0 shadow-md z-10">
      <div class="flex items-center space-x-4 ">
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
   <div class="p-8">
      <div class="bg-white rounded-lg shadow-md">
         <div class="p-6 mt-16">
            <div class="justify-between flex mb-5">
               <h2 class="text-2xl font-semibold mb-4">Laporan Pembayaran</h2>   
            </div>

            <form action="kamar.php" method="GET">
            <div class="mb-5 grid grid-cols-4 gap-3">

            <!-- Filter -->
               <!-- Input Nama Pelanggan -->
               <div class="relative w-full">
                  <label for="name" class="block text-sm font-medium text-gray-700">Nama Pelanggan</label>
                  <input type="text" id="bayar_name" placeholder="Cari Nama Kamar" name="name"
                        class="py-3 px-4 mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
               </div>

               <div class="relative w-full">
                  <label for="month_year" class="block text-sm font-medium text-gray-700">Bulan & Tahun</label>
                  <input type="month" id="month_year" name="month_year" value="<?= date('Y-m'); ?>"
                        class="py-3 px-4 mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
               </div>
            </div>
            <!-- End Filter -->


               <div class="flex items-center justify-end mb-5">
                  <!-- Tombol Find -->
                  <button type="submit" class="flex items-center justify-center gap-2 w-32 bg-blue-500 hover:bg-blue-600 rounded-md px-4 py-2 text-white font-semibold shadow">
                     <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 4a7 7 0 011 13.938V21l5-5-5-5v3.062A5.975 5.975 0 0017 12a6 6 0 10-6 6c1.453 0 2.77-.48 3.939-1.281L12 17V7c0-1.343.672-2.602 1.745-3.485L14 3H11z" />
                     </svg>
                     Cari
                  </button>
                  <!-- Tombol Find -->
               </div>
            </form>
            

            <!-- Table to display bayar -->
            <div class="overflow-x-auto">
               <table class="min-w-full table-auto">
                  <thead>
                     <tr class="bg-gray-100">
                        <th class="px-4 py-2 text-left">Nama Penyewa</th>
                        <th class="px-4 py-2 text-left">Status Pembayaran</th>
                        <th class="px-4 py-2 text-right">Nominal Dibayar</th>
                        <th class="px-4 py-2 text-center">Metode Pembayaran</th>
                        <th class="px-4 py-2 text-center">Tanggal Bayar</th>
                     </tr>
                  </thead>
                  <tbody>
                     <?php if (empty($bayar)): ?>
                        <tr>
                           <td colspan="5" class="text-center py-3">No data available</td>
                        </tr>
                     <?php else: ?>
                        <?php foreach ($bayar as $bayar): ?>
                           <tr>
                              <td class="px-4 py-2"><?php echo htmlspecialchars($bayar['nama_penghuni']); ?>  (<?php echo htmlspecialchars($bayar['nomor_kamar']); ?>) <br> <?php echo htmlspecialchars($bayar['penghuni_nomor_telepon']); ?></td>
                              <td class="px-4 py-2 text-green-500"><?php echo htmlspecialchars($bayar['status']); ?></td>
                              <td class="px-4 py-2 w-60 text-right">
                                 <?php echo 'Rp ' . number_format($bayar['jumlah'], 0, ',', '.'); ?>
                              </td>
                              <td class="px-4 py-2 text-center"><?php echo htmlspecialchars($bayar['metode']); ?></td>
                              <td class="px-4 py-2 text-right"><?php echo htmlspecialchars($bayar['created_at']); ?></td>
                           </tr>

                        <?php endforeach; ?>
                     <?php endif; ?>
                  </tbody>
               </table>
            </div>

         </div>
      </div>
   </div>
</div>


<div id="toast"></div>


</body>
</html>


<!-- Modal EDIT-->



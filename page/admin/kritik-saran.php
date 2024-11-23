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


   // Query untuk data kamar dengan filter dan pagination
   $sql = "SELECT * FROM kritik_dan_saran";
   $stmt_rooms = $pdo->prepare($sql);
   $stmt_rooms->execute();
   $rooms = $stmt_rooms->fetchAll(PDO::FETCH_ASSOC);

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
   /* Menambahkan z-index pada modal dan tombol */
   #room-modal {
      z-index: 1050; /* Atur modal agar berada di atas */
   }
   #room-modal-edit{
      z-index: 1050; /* Atur modal agar berada di atas */
   }
   #close-modal-cancel {
      z-index: 1060; /* Pastikan tombol Batal berada di atas modal */
   }

</style>

<!-- Sidebar -->
<div id="sidebar" class="hidden md:block w-72 h-full bg-white text-gray-800 fixed top-0 left-0 p-5  flex-col shadow-lg z-50">
   <div class="mb-6 text-center">
      <img src="../../assets/logo/Kozie.png" alt="Logo" class="h-20 mx-auto">
      <h1 class="text-lg font-semibold text-blue-800 mt-4 uppercase">
         Dashboard for Admin
      </h1>
   </div>
   <nav>
      <ul class="space-y-4">
         <li><a href="dashboard-admin.php" class="block px-4 py-2 rounded-md font-semibold hover:bg-gray-100 items-center space-x-3"><i class="bx bx-home text-xl"></i><span>Dashboard Overview</span></a></li>
         <li><a href="kamar.php" class="block px-4 py-2 rounded-md font-semibold  hover:bg-gray-100 items-center space-x-3"><i class="bx bx-bed text-xl"></i><span>Kamar</span></a></li>
         <li><a href="penghuni.php" class="block px-4 py-2 rounded-md font-semibold hover:bg-gray-100  items-center space-x-3"><i class="bx bx-user text-xl"></i><span>Penghuni</span></a></li>
         <li><a href="pembayaran.php" class="block px-4 py-2 rounded-md font-semibold hover:bg-gray-100  items-center space-x-3"><i class="bx bx-wallet text-xl"></i><span>Pembayaran</span></a></li>
         <li><a href="perabotan.php" class="block px-4 py-2 rounded-md font-semibold hover:bg-gray-100  items-center space-x-3"><i class="bx bx-cabinet text-xl"></i><span>Perabotan</span></a></li>
         <li><a href="komplain.php" class="block px-4 py-2 rounded-md font-semibold hover:bg-gray-100  items-center space-x-3"><i class="bx bx-chat text-xl"></i><span>Komplain</span></a></li>
         <li><a href="maintenance.php" class="block px-4 py-2 rounded-md font-semibold hover:bg-gray-100  items-center space-x-3"><i class="bx bx-wrench text-xl"></i><span>Maintenance</span></a></li>
         <li><a href="broadcast.php" class="block px-4 py-2 rounded-md font-semibold hover:bg-gray-100  items-center space-x-3"><i class="bx bx-bell text-xl"></i><span>Broadcast Notifikasi</span></a></li>
         <li><a href="kritik-saran.php" class="block px-4 py-2 rounded-md text-blue-500 font-semibold hover:bg-gray-100  items-center space-x-3"><i class="bx bx-message-detail text-xl"></i><span>Kritik dan Saran</span></a></li>
         <li><a href="../../logout.php" class="block px-4 py-2 rounded-md text-red-500 hover:bg-red-100  items-center space-x-3"><i class="bx bx-log-out text-xl"></i><span>Logout</span></a></li>
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
   <div class="p-8">
      <div class="bg-white rounded-lg shadow-md">
         <div class="p-6 mt-16">
            <div class="justify-between flex mb-5">
               <h2 class="text-2xl font-semibold mb-4">Kritik dan Saran</h2>
            </div>
            
            <!-- Table to display rooms -->
            <div class="overflow-x-auto">
               <table class="min-w-full table-auto">
                  <thead>
                     <tr class="bg-gray-100">
                        <th class="px-4 py-2 text-left">Aksi</th>
                        <th class="px-4 py-2 text-left">Nama Pengirim</th>
                        <th class="px-4 py-2 text-left">Email Pengirim</th>
                        <th class="px-4 py-2 text-left">Tanggal Kirim</th>
                        <th class="px-4 py-2 text-left">Kritik</th>
                        <th class="px-4 py-2 text-left">Kategori</th>
                        <th class="px-4 py-2 text-center">Status</th>
                        <th class="px-4 py-2 text-left">Balasan Feedback</th>
                     </tr>
                  </thead>
                  <tbody>
                     <?php if (empty($rooms)): ?>
                        <tr>
                           <td colspan="9" class="text-center py-3">No data available</td>
                        </tr>
                     <?php else: ?>
                        <?php foreach ($rooms as $room): ?>
                           <tr>
                              <td class="px-4 py-2">
                                 <!-- Edit button in the table -->
                                 <a href="#" class="edit-room-btn text-blue-500 hover:text-blue-700">
                                    <i class="bx bx-edit"></i>
                                 </a>
                              </td>
                              <td class="px-4 py-2 w-64 h-16"><?php echo !empty($room['nama_pengirim']) ? htmlspecialchars($room['nama_pengirim']) : '-'; ?></td>
                              <td class="px-4 py-2 w-52"><?php echo !empty($room['email_pengirim']) ? htmlspecialchars($room['email_pengirim']) : '-'; ?></td>
                              <td class="px-4 py-2">
                                 <?php 
                                 if (!empty($room['tanggal_kirim'])) {
                                    $bulan = [
                                       1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                                       'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                                    ];
                                    $tanggal = date_create($room['tanggal_kirim']);
                                    $tanggal_indonesia = date_format($tanggal, "j") . " " . 
                                                         $bulan[(int)date_format($tanggal, "m")] . " " . 
                                                         date_format($tanggal, "Y");
                                    echo $tanggal_indonesia;
                                 } else {
                                    echo '-';
                                 }
                                 ?>
                              </td>
                              <td class="px-4 py-2 text-left"><?php echo !empty($room['judul']) ? htmlspecialchars($room['judul']) : '-'; ?></td>
                              <td class="px-4 py-2 text-left"><?php echo !empty($room['kategori']) ? number_format($room['kategori']) : '-'; ?></td>
                              <td class="px-4 py-2 w-60"><?php echo !empty($room['status']) ? htmlspecialchars($room['status']) : '-'; ?></td>
                              <td class="px-4 py-2 w-60"><?php echo !empty($room['isi_feedback']) ? htmlspecialchars($room['isi_feedback']) : '-'; ?></td>
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

<!-- Modal ADD-->
<div id="room-modal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex justify-center items-center z-50 hidden">
   <div class="bg-white rounded-lg p-6 shadow-md w-2/3">
      <div class="mb-4">
         <h2 class="text-xl font-semibold">Tambah Kamar</h2>
         <!-- Close Modal Icon -->
         <button id="close-modal-icon" class="text-red-500 absolute top-2 right-2">
            <i class="bx bx-x text-3xl"></i>
         </button>
      </div>
      <form action="../../function/admin/kamar/add-room.php" method="POST">
         <!-- Form Fields -->
         <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700">Nama Kamar</label>
            <input type="text" id="room_name" name="name" class="py-3 px-4 mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
         </div>
         <div class="flex space-x-4 mb-4">
            <div class="flex-1">
               <label for="type" class="block text-sm font-medium text-gray-700">Jenis Kamar</label>
               <select id="type" name="room_type" class="py-3 px-4 mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                  <option value="Kamar Mandi Luar">Kamar Mandi Luar</option>
                  <option value="Kamar Mandi Dalam">Kamar Mandi Dalam</option>
               </select>
            </div>
            <div class="flex-1">
               <label for="ac" class="block text-sm font-medium text-gray-700">AC</label>
               <select id="ac" name="ac" class="py-3 px-4 mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                  <option value="AC">AC</option>
                  <option value="Non-Ac">Non-Ac</option>
               </select>
            </div>
         </div>
         <div class="flex space-x-4 mb-4">
            <div class="flex-1">
               <label for="capacity" class="block text-sm font-medium text-gray-700">Kapasitas</label>
               <select id="capacity" name="capacity" class="py-3 px-4 mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                  <option value="1">1 orang</option>
                  <option value="2">2 orang</option>
               </select>
            </div>
            <div class="flex-1">
               <label for="price" class="block text-sm font-medium text-gray-700">Harga</label>
               <input type="number" id="price" name="price" class="py-3 px-4 mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>
         </div>
         
         <div class="mb-4">
            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
            <select id="status" name="status" class="mt-1 py-3 px-4 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
               <option value="Tersedia">Tersedia</option>
               <option value="Terisi">Terisi</option>
               <option value="Sedang Diperbaiki">Sedang Diperbaiki</option>
            </select>
         </div>
         
         <!-- Description Textarea -->
         <div class="mb-4">
            <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi Kamar</label>
            <textarea id="description" name="description" rows="4" class="py-3 px-4 mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
         </div>

         <div class="flex justify-end gap-5">
            <!-- Batal Button -->
            <button type="button" id="close-modal-cancel" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">Batal</button>
            <!-- Submit Button -->
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">Tambah</button>
         </div>
      </form>
   </div>
</div>

<!-- Modal ADD-->



</body>
</html>

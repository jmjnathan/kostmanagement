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
   
   // Query untuk mengambil nama admin
   $stmt = $pdo->prepare("SELECT name FROM users WHERE username = :username");
   $stmt->execute(['username' => $session_username]);
   $admin = $stmt->fetch(PDO::FETCH_ASSOC);
   $admin_name = $admin['name'] ?? 'Admin';

   // Query untuk mengambil data kritik dan saran
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
         <li><a href="kritik-saran.php" class="block px-4 py-2 text-blue-500 rounded-md font-semibold hover:bg-gray-100  items-center space-x-3"><i class="bx bx-message-detail text-xl"></i><span>Kritik dan Saran</span></a></li>
         <li><a href="../../logout.php" class="block px-4 py-2 rounded-md text-red-500 hover:bg-red-100  items-center space-x-3"><i class="bx bx-log-out text-xl"></i><span>Logout</span></a></li>
      </ul>
   </nav>
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
                        <th class="px-4 py-2 text-left">Tanggal Kirim</th>
                        <th class="px-4 py-2 text-left">Kritik</th>
                        <th class="px-4 py-2 text-left">Kategori</th>
                        <th class="px-4 py-2 text-center">Status</th>
                        <th class="px-4 py-2 text-left">Balasan Feedback</th>
                        <th class="px-4 py-2 text-left">Tanggal Feedback</th>
                     </tr>
                  </thead>
                  <tbody>
                     <?php if (empty($rooms)): ?>
                        <tr>
                           <td colspan="8" class="text-center py-3">No data available</td>
                        </tr>
                     <?php else: ?>
                        <?php foreach ($rooms as $room): ?>
                           <tr>
                              <td class="px-4 py-2 w-24">
                                 <!-- Edit button in the table -->
                                 <a href="#" class="edit-room-btn text-blue-500 hover:text-blue-700" data-id="<?= $room['id'] ?>" data-feedback="<?= htmlspecialchars($room['isi_feedback'] ?? '') ?>" data-status="<?= htmlspecialchars($room['status'] ?? '') ?>" onclick="openModal(this)">
                                    <i class="bx bx-edit"></i>
                                 </a>
                                 <a href="../../function/admin/kritik-saran/delete-kritik-saran.php?id=<?= $room['id'] ?>" class="ml-4 text-red-500 hover:text-red-700" onclick="return confirm('Apakah Anda yakin ingin menghapus kritik dan saran ini?');">
                                    <i class="bx bx-trash"></i>
                                 </a>
                              </td>
                              <td class="px-4 py-2 w-44"><?php echo htmlspecialchars($room['nama_pengirim'] ?? '-'); ?></td>
                              <td class="px-4 py-2 w-40">
                                 <?php 
                                 if (!empty($room['tanggal_kirim'])) {
                                    $bulan = [
                                       1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 
                                       'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'
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
                              <td class="px-4 py-2"><?php echo htmlspecialchars($room['judul'] ?? '-'); ?></td>
                              <td class="px-4 py-2"><?php echo htmlspecialchars($room['kategori'] ?? '-'); ?></td>
                              <td class="px-4 py-2 text-center 
                                 <?php 
                                       // Add the class based on status value
                                       if ($room['status'] == 'Selesai') {
                                          echo 'text-green-500 font-semibold';
                                       } elseif ($room['status'] == 'Belum di baca') {
                                          echo 'text-red-500 font-semibold';
                                       } elseif ($room['status'] == 'Diproses') {
                                          echo 'text-yellow-500 font-semibold';
                                       }
                                 ?>">
                                 <?php echo htmlspecialchars($room['status']); ?>
                              </td>                              
                              <td class="px-4 py-2"><?php echo htmlspecialchars($room['isi_feedback'] ?? '-'); ?></td>
                              <td class="px-4 py-2 w-40">
                                 <?php 
                                 if (!empty($room['tanggal_feedback'])) {
                                    $tanggal_feedback = date_create($room['tanggal_feedback']);
                                    $tanggal_feedback_indonesia = date_format($tanggal_feedback, "j") . " " . 
                                                                  $bulan[(int)date_format($tanggal_feedback, "m")] . " " . 
                                                                  date_format($tanggal_feedback, "Y");
                                    echo $tanggal_feedback_indonesia;
                                 } else {
                                    echo '-';
                                 }
                                 ?>
                              </td>

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


<!-- Modal untuk Balasan -->
<div id="feedbackModal" class="fixed inset-0 z-50 hidden bg-gray-900 bg-opacity-50 flex justify-center items-center">
   <div class="bg-white rounded-lg w-1/2 p-6">
      <h2 class="text-xl font-semibold mb-4">Balasan untuk Kritik dan Saran</h2>
      <form action="../../function/admin/kritik-saran/edit-kritik-saran.php" method="POST">
         <input type="hidden" name="id" id="kritik_id"> <!-- Untuk menyimpan ID kritik-saran yang akan dibalas -->
         <div class="mb-4">
            <label for="isi_feedback" class="block text-gray-700">Balasan:</label>
            <textarea id="isi_feedback" name="feedback" rows="4" class="w-full p-2 border rounded-md" required></textarea>         </div>
         <div class="mb-4">
            <label for="status" class="block text-gray-700">Status:</label>
            <select id="status" name="status" class="w-full p-2 border rounded-md">
               <option value="" disabled selected class="text-gray-300" >Pilih Status</option>
               <option value="Belum di baca">Belum di baca</option>
               <option value="Diproses">Diproses</option>
               <option value="Selesai">Selesai</option>
            </select>
         </div>
         <div class="flex justify-end">
            <button type="button" class="px-4 py-2 mr-4 bg-gray-300 text-gray-700 rounded-md" onclick="closeModal()">Batal</button>
            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md">Kirim</button>
         </div>
      </form>
   </div>
</div>


</body>
</html>


<script>
   // Fungsi untuk membuka modal dan mengisi data
   function openModal(button) {
      var id = button.getAttribute('data-id');
      var feedback = button.getAttribute('data-feedback');
      var status = button.getAttribute('data-status');
      
      // Isi data ke dalam modal
      document.getElementById('id').value = id;
      document.getElementById('isi_feedback').value = feedback;
      document.getElementById('status').value = status;

      // Tampilkan modal
      document.getElementById('feedbackModal').classList.remove('hidden');
   }

   // Fungsi untuk menutup modal
   function closeModal() {
      document.getElementById('feedbackModal').classList.add('hidden');
   }
</script>


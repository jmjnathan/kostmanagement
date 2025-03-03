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
<!-- Toast Fungsi -->
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
   <title>KosKozie</title>
   <link rel="icon" type="image/png" class="rounded-full" href="../../assets/logo/Kozie.png">
</head>
<body class="bg-gray-100 min-h-screen">

<style>
   body {
      font-family: 'Poppins', sans-serif;
   }
</style>

<style>
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
            <li><a href="dashboard-admin.php" class="block px-4 py-2 rounded-md text-white font-semibold bg-tr hover:text-blue-300 items-center space-x-3 "><i class="bx bx-home text-xl"></i><span>Dashboard Overview</span></a></li>
            <li><a href="kamar.php" class="block px-4 py-2 rounded-md font-semibold text-white hover:text-blue-300  items-center space-x-3"><i class="bx bx-bed text-xl"></i><span>Kamar</span></a></li>
            <li><a href="penghuni.php" class="block px-4 py-2 rounded-md font-semibold text-white hover:text-blue-300  items-center space-x-3"><i class="bx bx-user text-xl"></i><span>Penghuni</span></a></li>
            <li><a href="pembayaran.php" class="block px-4 py-2 rounded-md font-semibold text-white hover:text-blue-300  items-center space-x-3"><i class="bx bx-wallet text-xl"></i><span>Pembayaran</span></a></li>
            <li><a href="komplain.php" class="block px-4 py-2 rounded-md font-semibold text-white hover:text-blue-300  items-center space-x-3"><i class="bx bx-chat text-xl"></i><span>Komplain</span></a></li>
            <li><a href="maintenance.php" class="block px-4 py-2 rounded-md font-semibold text-white hover:text-blue-300  items-center space-x-3"><i class="bx bx-wrench text-xl"></i><span>Maintenance</span></a></li>
            <li><a href="broadcast.php" class="block px-4 py-2 rounded-md font-semibold  text-white hover:text-blue-300  items-center space-x-3"><i class="bx bx-bell text-xl"></i><span>Broadcast Notifikasi</span></a></li>
            <li><a href="kritik-saran.php" class="block px-4 py-2 rounded-md font-semibold text-white hover:text-blue-300  items-center space-x-3 shadow-lg "><i class="bx bx-message-detail text-xl"></i><span>Kritik dan Saran</span></a></li>
            <li><a href="../../logout.php" class="block px-4 py-2 rounded-md text-red-500 hover:text-red-700  items-center space-x-3 font-semibold"><i class="bx bx-log-out text-xl"></i><span>Logout</span></a></li>
      </ul>
   </nav>
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
                        <th class="px-4 py-2 text-left">Nama Pengirim</th>
                        <th class="px-4 py-2 text-left">Tanggal Kirim</th>
                        <th class="px-4 py-2 text-left">Kritik</th>
                        <th class="px-4 py-2 text-left">Kategori</th>
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
         <input type="hidden" name="id" id="id"> <!-- Untuk menyimpan ID kritik-saran yang akan dibalas -->
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

<div id="toast"></div>


</body>
</html>


<script>
   // Fungsi untuk membuka modal dan mengisi data
   function openModal(button) {
   var id = button.getAttribute('data-id');
   var feedback = button.getAttribute('data-feedback');
   var status = button.getAttribute('data-status');

   // Perbaikan akses ke input hidden
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

// Fungsi Toast Notification
   function showToast(message) {
        var toast = document.getElementById("toast");
        toast.innerHTML = message;
        toast.style.visibility = "visible";
        setTimeout(function() {
            toast.style.visibility = "hidden";
        }, 3000); // Toast akan menghilang setelah 3 detik
    }
</script>


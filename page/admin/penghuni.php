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
   $sql = 
   "SELECT 
      P.id, P.room_id, R.name as no_kamar, R.type, R.ac, P.nama, P.jenis_kelamin, P.nomor_telepon, P.nomor_telepon, P.alamat_asal,
      P.status, P.tanggal_masuk, P.tanggal_keluar, P.ktp, P.nomor_darurat, P.username, P.password, P.created_at, P.updated_at
   FROM 
      penghuni P
   INNER JOIN
      rooms R on P.room_id = R.id
   ";
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
   <title>Dashboard</title>
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
            <li><a href="penghuni.php" class="block px-4 py-2 rounded-md font-semibold text-white hover:text-blue-300  items-center space-x-3 shadow-lg"><i class="bx bx-user text-xl"></i><span>Penghuni</span></a></li>
            <li><a href="pembayaran.php" class="block px-4 py-2 rounded-md font-semibold text-white hover:text-blue-300  items-center space-x-3"><i class="bx bx-wallet text-xl"></i><span>Pembayaran</span></a></li>
            <li><a href="komplain.php" class="block px-4 py-2 rounded-md font-semibold text-white hover:text-blue-300  items-center space-x-3 "><i class="bx bx-chat text-xl"></i><span>Komplain</span></a></li>
            <li><a href="maintenance.php" class="block px-4 py-2 rounded-md font-semibold text-white hover:text-blue-300  items-center space-x-3"><i class="bx bx-wrench text-xl"></i><span>Maintenance</span></a></li>
            <li><a href="broadcast.php" class="block px-4 py-2 rounded-md font-semibold  text-white hover:text-blue-300  items-center space-x-3"><i class="bx bx-bell text-xl"></i><span>Broadcast Notifikasi</span></a></li>
            <li><a href="kritik-saran.php" class="block px-4 py-2 rounded-md font-semibold text-white hover:text-blue-300  items-center space-x-3  "><i class="bx bx-message-detail text-xl"></i><span>Kritik dan Saran</span></a></li>
            <li><a href="../../logout.php" class="block px-4 py-2 rounded-md text-red-500 hover:text-red-700  items-center space-x-3 font-semibold"><i class="bx bx-log-out text-xl"></i><span>Logout</span></a></li>
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
               <h2 class="text-2xl font-semibold mb-4">Daftar Penghuni</h2>
            </div>
            
            <!-- Table to display rooms -->
            <div class="overflow-x-auto">
               <table class="min-w-full table-auto">
                  <thead>
                     <tr class="bg-gray-100">
                        <th class="px-4 py-2 text-left">Aksi</th>
                        <th class="px-4 py-2 text-left">Nama</th>
                        <th class="px-4 py-2 text-left">Nomor Kamar</th>
                        <th class="px-4 py-2 text-left">Nomor Telepon</th>
                        <th class="px-4 py-2 text-left">Jenis Kelamin</th>
                        <th class="px-4 py-2 text-left">Alamat Asal</th>
                        <th class="px-4 py-2 text-left">Tanggal Masuk dan Keluar</th>
                        <th class="px-4 py-2 text-left">Durasi Sewa</th>
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
                              <td class="px-4 py-2 w-16">
                                 <!-- Edit button in the table -->
                                 <a href="#" class="edit-room-btn text-blue-500 hover:text-blue-700" data-id="<?= $room['id'] ?>" data-feedback="<?= htmlspecialchars($room['isi_feedback'] ?? '') ?>" data-status="<?= htmlspecialchars($room['status'] ?? '') ?>" onclick="openModal(this)">
                                    <i class="bx bx-edit"></i>
                                 </a>
                                 <a href="../../function/admin/penghuni/delete-penghuni.php?id=<?= $room['id'] ?>" class="ml-4 text-red-500 hover:text-red-700" onclick="return confirm('Apakah Anda yakin ingin menghapus penghuni?');">
                                    <i class="bx bx-trash"></i>
                                 </a>
                              </td>
                              <td class="px-4 py-2 w-56">
                                 <div class="font-bold text-md"><?php echo htmlspecialchars($room['nama'] ?? '-'); ?></div>
                                 <div class="text-sm"><?php echo htmlspecialchars($room['ktp'] ?? '-'); ?></div>
                              </td>
                              <td class="px-4 py-2">
                                 <div class="font-bold text-md">
                                    <?php echo htmlspecialchars($room['no_kamar']); ?>
                                 </div>
                                 <div class="text-sm">
                                     <?php 
                                 if ($room['type'] === 'km_luar') {
                                       echo 'Kamar Mandi Luar';
                                 } elseif ($room['type'] === 'km_dalam') {
                                       echo 'Kamar Mandi Dalam';
                                 } else {
                                       echo htmlspecialchars($room['type']); // Default jika ada tipe lain
                                 }
                              ?>,
                                    <?php echo htmlspecialchars($room['ac'] ?? '-'); ?>
                                 </div>
                              </td>
                              <td class="px-4 py-2 w-40">
                              <div class="font-bold text-md"><?php echo htmlspecialchars($room['nomor_telepon'] ?? '-'); ?></div>
                                 <div class="text-sm"><?php echo htmlspecialchars($room['nomor_darurat'] ?? '-'); ?></div>
                              </td>
                              <td class="px-4 py-2">
                                 <?php echo htmlspecialchars($room['jenis_kelamin']); ?>
                              </td>                              
                              <td class="px-4 py-2 w-40">
                                 <?php echo htmlspecialchars($room['alamat_asal']); ?>
                              </td>
                              <td class="px-4 py-2 w-44">
                                 <div class="font-bold text-md">
                                    <?php 
                                    // Mengubah format tanggal masuk
                                    if (!empty($room['tanggal_masuk'])) {
                                       $tanggal_masuk = date_create($room['tanggal_masuk']);
                                       echo date_format($tanggal_masuk, 'j F Y'); // Contoh format: 12 Januari 2024
                                    } else {
                                       echo '-';
                                    }
                                    ?>
                                 </div>
                                 <div class="text-sm">
                                    <?php 
                                    // Mengubah format tanggal keluar
                                    if (!empty($room['tanggal_keluar'])) {
                                       $tanggal_keluar = date_create($room['tanggal_keluar']);
                                       echo date_format($tanggal_keluar, 'j F Y'); // Contoh format: 12 Januari 2024
                                    } else {
                                       echo '-';
                                    }
                                    ?>
                                 </div>
                              </td>
                              <td class="px-4 py-2 w-40">
                                 <?php
                                    // Menghitung durasi sewa jika kedua tanggal ada
                                    if (!empty($room['tanggal_masuk']) && !empty($room['tanggal_keluar'])) {
                                       $tanggal_masuk = date_create($room['tanggal_masuk']);
                                       $tanggal_keluar = date_create($room['tanggal_keluar']);
                                       $diff = date_diff($tanggal_masuk, $tanggal_keluar); // Menghitung selisih tanggal
                                       echo "<span class='text-md'>Sewa: " . $diff->days . " Hari</span>"; // Menampilkan durasi dalam hari
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


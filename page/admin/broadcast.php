<?php
session_start();
require '../../db.php';

// Cek apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
   header('Location: index.php');
   exit();
}

// Cek role pengguna, jika bukan admin, alihkan ke halaman lain
if ($_SESSION['role'] !== 'admin') {
   header('Location: dashboard-user.php');
   exit();
}

// Ambil nama admin
$session_username = $_SESSION['username'];
$result = mysqli_query($conn, "SELECT name FROM users WHERE username = '$session_username'");
$admin = mysqli_fetch_assoc($result);
$admin_name = $admin['name'] ?? 'Admin';

// Ambil daftar penghuni
$result_penghuni = mysqli_query($conn, "SELECT id, nama, nomor_telepon FROM penghuni");
$penghuni = mysqli_fetch_all($result_penghuni, MYSQLI_ASSOC);

// Proses broadcast pesan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message']) && isset($_POST['recipients'])) {
   $message = trim($_POST['message']);
   $selected_numbers = $_POST['recipients'];
   
   if (!empty($message) && !empty($selected_numbers)) {
      foreach ($selected_numbers as $id) {
         $query = mysqli_query($conn, "SELECT nomor_telepon FROM penghuni WHERE id = '$id'");
         $p = mysqli_fetch_assoc($query);
         if ($p) {
            kirimPesanFonnte($p['nomor_telepon'], $message);
         }
      }
      echo "<script>alert('Pesan berhasil dikirim!');</script>";
   }
}

// Fungsi untuk mengirim pesan menggunakan Fonnte
function kirimPesanFonnte($nomor, $pesan) {
   $token = 'qeKHChfydDbZdsc1eAVZ'; // Ganti dengan token asli Anda
   $data = [
      'target' => $nomor,
      'message' => $pesan,
   ];
   
   $ch = curl_init('https://api.fonnte.com/send');
   curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: $token"]);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   curl_setopt($ch, CURLOPT_POST, true);
   curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
   curl_exec($ch);
   curl_close($ch);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <script src="https://cdn.tailwindcss.com"></script>
   <title>Dashboard Admin</title>
</head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <title>KosKozie</title>
    <link rel="icon" type="image/png" class="rounded-full" href="../../assets/logo/Kozie.png">

</head>

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
<body class="bg-gray-100 min-h-screen font-poppins">

      <!-- Sidebar -->
      <div id="sidebar" class="w-72 h-screen bg-gradient-to-r from-indigo-500 to-blue-500 text-white p-5 fixed top-0 left-0 shadow-lg">
         <div class="mb-6 text-center">
            <img src="../../assets/logo/Kozie.png" alt="Logo" class="h-20 mx-auto rounded-full">
            <h1 class="text-lg font-medium mt-4 uppercase">DASHBOARD FOR ADMIN</h1>
         </div>
         <nav>
            <ul class="space-y-4">
            <li><a href="dashboard-admin.php" class="block px-4 py-2 rounded-md text-white font-medium bg-tr hover:text-blue-300 items-center space-x-3 "><i class="bx bx-home text-xl"></i><span>Dashboard Overview</span></a></li>
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
            <li><a href="broadcast.php" class="block px-4 py-2 rounded-md font-medium  text-white hover:text-blue-300  items-center space-x-3 shadow-lg"><i class="bx bx-bell text-xl"></i><span>Broadcast Notifikasi</span></a></li>
            <li><a href="pengajuan-keluar.php" class="block px-4 py-2 rounded-md font-medium text-white hover:text-blue-300 items-center space-x-3"><i class="fa-solid fa-person-walking-arrow-right text-md"></i><span>Pengajuan Keluar Kos</span></a></li>
            <li><a href="kritik-saran.php" class="block px-4 py-2 rounded-md font-medium text-white hover:text-blue-300  items-center space-x-3"><i class="bx bx-message-detail text-xl"></i><span>Kritik dan Saran</span></a></li>
            <li><a href="peraturan.php" class="block px-4 py-2 rounded-md font-medium text-white hover:text-blue-300 items-center space-x-3"><i class="bx bx-info-circle text-xl"></i><span>Peraturan</span></a></li>
            <!-- <li><a href="pengguna.php" class="block px-4 py-2 rounded-md font-medium text-white hover:text-blue-300  items-center space-x-3"><i class="bx bx-group text-xl"></i><span>Pengguna</span></a></li> -->
            <li><a href="../../logout.php" class="block px-4 py-2 rounded-md text-red-500 hover:text-red-700  items-center space-x-3 font-medium"><i class="bx bx-log-out text-xl"></i><span>Logout</span></a></li>
      </ul>
         </nav>
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
            <span class="text-sm md:text-lg font-medium text-blue-700">
               Welcome, <?php echo htmlspecialchars($admin_name); ?>!
            </span>
         </div>
      </nav>

   <div class="p-8">
      <div class="bg-white rounded-lg shadow-md">
            <div class="p-6 mt-16">
            <h2 class="text-2xl font-semibold mb-4">Broadcast Pesan ke Penghuni</h2>
            <form action="" method="POST">
               <label class="block text-gray-700 font-medium">Tulis Pesan:</label>
               <textarea name="message" rows="4" class="w-full border p-2 rounded-md" required></textarea>
            
            <!-- <div class="grid grid-cols-2 gap-4"> -->
               <!-- <div>
                  <h3 class="text-gray-700 font-medium mb-2">Daftar Penghuni:</h3>
                  <ul class="border p-2 h-40 overflow-y-auto rounded-md">
                     <?php foreach ($penghuni as $p): ?>
                        <li class="mb-1 px-2 py-1 bg-gray-100 rounded-md"> <?php echo $p['nama'] . " (" . $p['nomor_telepon'] . ")"; ?> </li>
                     <?php endforeach; ?>
                  </ul>
               </div> -->
               <div>
                  <h3 class="text-gray-700 font-medium mb-2 mt-4">Kirim ke:</h3>

                  <!-- Filter Nama & Nomor Telepon (Berjejeran) -->
                  <div class="mb-2 flex space-x-2">
                     <input type="text" id="searchInput" placeholder="Cari Nama..." 
                        class="py-2 px-4 w-1/2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                     
                     <input type="text" id="searchPhone" placeholder="Cari Nomor Telepon..." 
                        class="py-2 px-4 w-1/2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                  </div>


                  <!-- Daftar Nama dengan Scroll -->
                  <div class="w-full border p-2 h-96 overflow-y-auto rounded-md bg-white shadow-md">
                     <!-- Opsi Pilih Semua -->
                     <label class="flex items-center space-x-3 p-2 cursor-pointer transition duration-200 border-b">
                        <input type="checkbox" id="selectAll" class="w-5 h-5 text-blue-500 border-gray-300 rounded focus:ring focus:ring-blue-200">
                        <span class="text-gray-700 font-medium">Pilih Semua</span>
                     </label>

                     <!-- Daftar Nama -->
                     <div id="namaList">
                        <?php foreach ($penghuni as $p): ?>
                              <label class="flex items-center space-x-3 p-2 hover:bg-gray-100 rounded-md cursor-pointer transition duration-200 nama-item">
                                 <input type="checkbox" name="recipients[]" value="<?php echo $p['id']; ?>" class="w-5 h-5 text-blue-500 border-gray-300 rounded focus:ring focus:ring-blue-200 checkbox-item">
                                 <span class="text-gray-700 font-medium"><?php echo $p['nama'] . " - " . $p['nomor_telepon']; ?></span>
                              </label>
                        <?php endforeach; ?>
                     </div>
                  </div>
               </div>


            <!-- </div> -->
            <div class="flex justify-end">
               <button type="submit" class="mt-4 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md w-full">Kirim</button>
            </div>
         </form>
      </div>
   </div>
</body>

<script>
   // fungsi untuk memilih semua checkbox
   document.getElementById('selectAll').addEventListener('change', function() {
      let checkboxes = document.querySelectorAll('.checkbox-item');
      checkboxes.forEach(checkbox => {
         checkbox.checked = this.checked;
      });
   });
   // Fungsi untuk filter nama & nomor telepon
   function filterNamaTelepon() {
      let filterName = document.getElementById("searchInput").value.toLowerCase();
      let filterPhone = document.getElementById("searchPhone").value.toLowerCase();
      let namaItems = document.querySelectorAll(".nama-item");

      namaItems.forEach(function (item) {
         let text = item.querySelector("span.text-gray-700").innerText.toLowerCase(); // Perbaikan seleksi elemen

         let matchName = filterName === "" || text.includes(filterName);
         let matchPhone = filterPhone === "" || text.includes(filterPhone);

         item.style.display = matchName && matchPhone ? "" : "none";
      });
   }

   // Tambahkan event listener ke input pencarian
   document.getElementById("searchInput").addEventListener("input", filterNamaTelepon);
   document.getElementById("searchPhone").addEventListener("input", filterNamaTelepon);

</script>
</html>

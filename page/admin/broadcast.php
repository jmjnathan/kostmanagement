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
    <title>Broadcast Pesan</title>
</head>
<body class="bg-gray-100 min-h-screen font-poppins">

      <!-- Sidebar -->
      <div id="sidebar" class="w-72 h-screen bg-gradient-to-r from-indigo-500 to-blue-500 text-white p-5 fixed top-0 left-0 shadow-lg">
         <div class="mb-6 text-center">
            <img src="../../assets/logo/Kozie.png" alt="Logo" class="h-20 mx-auto rounded-full">
            <h1 class="text-lg font-semibold mt-4 uppercase">DASHBOARD FOR ADMIN</h1>
         </div>
         <nav>
            <ul class="space-y-4">
            <li><a href="dashboard-admin.php" class="block px-4 py-2 rounded-md text-white font-semibold bg-tr hover:text-blue-300 items-center space-x-3 "><i class="bx bx-home text-xl"></i><span>Dashboard Overview</span></a></li>
            <li><a href="kamar.php" class="block px-4 py-2 rounded-md font-semibold text-white hover:text-blue-300  items-center space-x-3"><i class="bx bx-bed text-xl"></i><span>Kamar</span></a></li>
            <li><a href="penghuni.php" class="block px-4 py-2 rounded-md font-semibold text-white hover:text-blue-300  items-center space-x-3"><i class="bx bx-user text-xl"></i><span>Penghuni</span></a></li>
            <li><a href="pembayaran.php" class="block px-4 py-2 rounded-md font-semibold text-white hover:text-blue-300  items-center space-x-3"><i class="bx bx-wallet text-xl"></i><span>Pembayaran</span></a></li>
            <li><a href="komplain.php" class="block px-4 py-2 rounded-md font-semibold text-white hover:text-blue-300  items-center space-x-3"><i class="bx bx-chat text-xl"></i><span>Komplain</span></a></li>
            <li><a href="maintenance.php" class="block px-4 py-2 rounded-md font-semibold text-white hover:text-blue-300  items-center space-x-3"><i class="bx bx-wrench text-xl"></i><span>Maintenance</span></a></li>
            <li><a href="broadcast.php" class="block px-4 py-2 rounded-md font-semibold  text-white hover:text-blue-300  items-center space-x-3 shadow-lg"><i class="bx bx-bell text-xl"></i><span>Broadcast Notifikasi</span></a></li>
            <li><a href="kritik-saran.php" class="block px-4 py-2 rounded-md font-semibold text-white hover:text-blue-300  items-center space-x-3"><i class="bx bx-message-detail text-xl"></i><span>Kritik dan Saran</span></a></li>
            <li><a href="../../logout.php" class="block px-4 py-2 rounded-md text-red-500 hover:text-red-700  items-center space-x-3 font-semibold"><i class="bx bx-log-out text-xl"></i><span>Logout</span></a></li>
      </ul>
         </nav>
      </div>
      
      <!-- Main Content -->
      <div class="ml-72 p-8 w-full">
         <h2 class="text-2xl font-semibold mb-4">Broadcast Pesan ke Penghuni</h2>
         <form action="" method="POST" class="bg-white p-6 rounded-lg shadow-md">
            <label class="block text-gray-700 font-semibold">Pesan:</label>
            <textarea name="message" rows="4" class="w-full border p-2 rounded-md" required></textarea>
            
            <label class="block text-gray-700 font-semibold mt-4">Pilih Penerima:</label>
            <div class="grid grid-cols-2 gap-4">
               <div>
                  <h3 class="text-gray-700 font-semibold mb-2">Daftar Penghuni:</h3>
                  <ul class="border p-2 h-40 overflow-y-auto rounded-md">
                     <?php foreach ($penghuni as $p): ?>
                        <li class="mb-1 px-2 py-1 bg-gray-100 rounded-md"> <?php echo $p['nama'] . " (" . $p['nomor_telepon'] . ")"; ?> </li>
                     <?php endforeach; ?>
                  </ul>
               </div>
               <div>
                  <h3 class="text-gray-700 font-semibold mb-2">Pilih Tujuan:</h3>
                  <select name="recipients[]" multiple class="w-full border p-2 h-40 overflow-y-auto rounded-md bg-white" required>
                     <?php foreach ($penghuni as $p): ?>
                        <option value="<?php echo $p['id']; ?>" class="px-2 py-1"> <?php echo $p['nama'] . " (" . $p['nomor_telepon'] . ")"; ?> </option>
                     <?php endforeach; ?>
                  </select>
               </div>
            </div>
            
            <button type="submit" class="mt-4 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">Kirim</button>
         </form>
      </div>
   </div>
</body>
</html>

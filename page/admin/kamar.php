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
 
      // Default pagination parameters
   $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10; // Default 10
   $page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Default halaman 1
   $offset = ($page - 1) * $limit;
 
   // Total data untuk menghitung jumlah halaman dengan filter
   $total_sql = "SELECT COUNT(*) FROM rooms WHERE name LIKE :name AND type LIKE :type";
   $total_stmt = $pdo->prepare($total_sql);
   $total_stmt->bindValue(':name', "%$name%");
   $total_stmt->bindValue(':type', "%$type%");
   $total_stmt->execute();
   $total_data = $total_stmt->fetchColumn();
   $total_pages = ceil($total_data / $limit);
 
   // Ambil parameter filter dari query string
   $name = isset($_GET['name']) ? $_GET['name'] : '';
   $type = isset($_GET['room_type']) ? $_GET['room_type'] : '';
 
   // Query untuk data kamar dengan filter dan pagination
   $sql = "SELECT * FROM rooms WHERE name LIKE :name AND type LIKE :type ORDER BY name ASC LIMIT :limit OFFSET :offset";
   $stmt_rooms = $pdo->prepare($sql);
   $stmt_rooms->bindValue(':name', "%$name%");
   $stmt_rooms->bindValue(':type', "%$type%");
   $stmt_rooms->bindValue(':limit', $limit, PDO::PARAM_INT);
   $stmt_rooms->bindValue(':offset', $offset, PDO::PARAM_INT);
   $stmt_rooms->execute();
   $rooms = $stmt_rooms->fetchAll(PDO::FETCH_ASSOC);
 
} catch (PDOException $e) {
   echo 'Connection failed: ' . $e->getMessage();
   exit();
}
?>
 
<?php
try {
   // Koneksi ke database
   $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
   $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
 
   // Ambil parameter filter dari query string
   $name = isset($_GET['name']) ? $_GET['name'] : '';
   $type = isset($_GET['type']) ? $_GET['type'] : '';
 
   // Persiapkan query dengan filter dinamis
   $sql = "SELECT * FROM rooms WHERE name LIKE :name AND type LIKE :type";
   $stmt = $pdo->prepare($sql);
 
   // Bind parameter untuk filter
   $stmt->bindValue(':name', "%$name%");
   $stmt->bindValue(':type', "%$type%");
 
   // Eksekusi query
   $stmt->execute();
   $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
 
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
         <li><a href="kamar.php" class="block px-4 py-2 rounded-md font-semibold text-blue-500 hover:bg-gray-100 items-center space-x-3"><i class="bx bx-bed text-xl"></i><span>Kamar</span></a></li>
         <li><a href="penghuni.php" class="block px-4 py-2 rounded-md font-semibold hover:bg-gray-100  items-center space-x-3"><i class="bx bx-user text-xl"></i><span>Penghuni</span></a></li>
         <li><a href="pembayaran.php" class="block px-4 py-2 rounded-md font-semibold hover:bg-gray-100  items-center space-x-3"><i class="bx bx-wallet text-xl"></i><span>Pembayaran</span></a></li>
         <li><a href="perabotan.php" class="block px-4 py-2 rounded-md font-semibold hover:bg-gray-100  items-center space-x-3"><i class="bx bx-cabinet text-xl"></i><span>Perabotan</span></a></li>
         <li><a href="komplain.php" class="block px-4 py-2 rounded-md font-semibold hover:bg-gray-100  items-center space-x-3"><i class="bx bx-chat text-xl"></i><span>Komplain</span></a></li>
         <li><a href="maintenance.php" class="block px-4 py-2 rounded-md font-semibold hover:bg-gray-100  items-center space-x-3"><i class="bx bx-wrench text-xl"></i><span>Maintenance</span></a></li>
         <li><a href="broadcast.php" class="block px-4 py-2 rounded-md font-semibold hover:bg-gray-100  items-center space-x-3"><i class="bx bx-bell text-xl"></i><span>Broadcast Notifikasi</span></a></li>
         <li><a href="kritik-saran.php" class="block px-4 py-2 rounded-md font-semibold hover:bg-gray-100  items-center space-x-3"><i class="bx bx-message-detail text-xl"></i><span>Kritik dan Saran</span></a></li>
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
               <h2 class="text-2xl font-semibold mb-4">Kamar</h2>
               <!-- Button to open modal -->
               <button id="open-modal" class="flex items-center justify-center gap-2 w-auto bg-blue-500 hover:bg-blue-600 rounded-md px-4 py-2 text-white font-semibold shadow">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                     <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                  </svg>
                  Tambah Kamar
               </button>
            </div>
 
 
            <!-- Table to display rooms -->
            <div class="overflow-x-auto">
               <table class="min-w-full table-auto">
                  <thead>
                     <tr class="bg-gray-100">
                        <th class="px-4 py-2 text-left">Aksi</th>
                        <th class="px-4 py-2 text-left">No Kamar</th>
                        <th class="px-4 py-2 text-left">Jenis</th>
                        <th class="px-4 py-2 text-left">AC</th>
                        <th class="px-4 py-2 text-right">Kapasitas</th>
                        <th class="px-4 py-2 text-right">Harga</th>
                        <th class="px-4 py-2 text-left">Deskripsi</th>
                        <th class="px-4 py-2 text-center">Status</th>
                     </tr>
                  </thead>
                  <tbody>
                     <?php if (empty($rooms)): ?>
                        <tr>
                           <td colspan="5" class="text-center py-3">No data available</td>
                        </tr>
                     <?php else: ?>
                        <?php foreach ($rooms as $room): ?>
                           <tr>
                              <td class="px-4 py-2">
                                 <!-- Edit button in the table -->
                                 <a href="#" class="edit-room-btn text-blue-500 hover:text-blue-700" data-room-id="<?= $room['id'] ?>" data-name="<?= $room['name'] ?>" data-type="<?= $room['type'] ?>" data-ac="<?= $room['ac'] ?>" data-capacity="<?= $room['capacity'] ?>" data-price="<?= $room['price'] ?>" data-status="<?= $room['status'] ?>">
                                       <i class="bx bx-edit"></i>
                                 </a>
                                 <a href="../../function/admin/kamar/delete-room.php?id=<?= $room['id'] ?>" class="ml-4 text-red-500 hover:text-red-700" onclick="return confirm('Apakah Anda yakin ingin menghapus kamar ini?');">
                                       <i class="bx bx-trash"></i>
                                 </a>
                              </td>
                              <td class="px-4 py-2 w-36 h-16"><?php echo htmlspecialchars($room['name']); ?></td>
                              <td class="px-4 py-2 w-52"><?php echo htmlspecialchars($room['type']); ?></td>
                              <td class="px-4 py-2"><?php echo htmlspecialchars($room['ac']); ?></td>
                              <td class="px-4 py-2 text-right"><?php echo htmlspecialchars($room['capacity']); ?> orang</td>
                              <td class="px-4 py-2 text-right">Rp.<?php echo number_format($room['price'], 0, ',', '.'); ?></td>
                              <td class="px-4 py-2 w-60"><?php echo htmlspecialchars($room['description']); ?></td>
                              <td class="px-4 py-2 text-center 
                                 <?php 
                                       // Add the class based on status value
                                       if ($room['status'] == 'Tersedia') {
                                          echo 'text-green-500 font-semibold';
                                       } elseif ($room['status'] == 'Terisi') {
                                          echo 'text-red-500 font-semibold';
                                       } elseif ($room['status'] == 'Sedang Diperbaiki') {
                                          echo 'text-yellow-500 font-semibold';
                                       }
                                 ?>">
                                 <?php echo htmlspecialchars($room['status']); ?>
                              </td>
                           </tr>
 
                        <?php endforeach; ?>
                     <?php endif; ?>
                  </tbody>
               </table>
            </div>
 
            <!-- Pagination -->
            <div class="mt-4 flex justify-center items-center">
               <a href="?page=<?= $page - 1 ?>&limit=<?= $limit ?>" 
                  class="px-3 py-1 mx-1 border rounded <?= $page <= 1 ? 'text-gray-400 pointer-events-none' : 'text-blue-500' ?>">
                  Sebelumnya
               </a>
               <span class="mx-2">Halaman <?= $page ?> dari <?= $total_pages ?></span>
               <a href="?page=<?= $page + 1 ?>&limit=<?= $limit ?>" 
                  class="px-3 py-1 mx-1 border rounded <?= $page >= $total_pages ? 'text-gray-400 pointer-events-none' : 'text-blue-500' ?>">
                  Berikutnya
               </a>
            </div>
            <!-- End Pagination -->
 
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
 
<!-- Modal EDIT-->
<div id="room-modal-edit" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex justify-center items-center z-50 hidden">
   <div class="bg-white rounded-lg p-6 shadow-md w-2/3">
      <div class="mb-4">
         <h2 class="text-xl font-semibold">Edit Kamar</h2>
         <!-- Close Modal Icon -->
         <button id="close-modal-icon" class="text-red-500 absolute top-2 right-2">
            <i class="bx bx-x text-3xl"></i>
         </button>
      </div>
      <form action="../../function/admin/kamar/edit-room.php" method="POST">
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
         <div class="flex justify-end gap-5">
            <!-- Batal Button -->
            <button type="button" id="close-modal-cancel" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">Batal</button>
            <!-- Submit Button -->
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">Edit</button>
         </div>
      </form>
   </div>
</div>
 
<!-- Modal EDIT-->
 
 
<script>
 
   function changeLimit() {
      const limit = document.getElementById('limit').value;
      window.location.href = `?limit=${limit}&page=1`; // Reset ke halaman 1 saat jumlah data berubah
   }
 
   // Toggle Sidebar for mobile
   document.getElementById('toggle-sidebar').addEventListener('click', function() {
      const sidebar = document.getElementById('sidebar');
      sidebar.classList.toggle('hidden');
   });
 
   // Open and Close Modal
// Open and Close Modal
const openModalButton = document.getElementById('open-modal');
const closeModalButton = document.getElementById('close-modal-icon');
const closeModalCancelButton = document.getElementById('close-modal-cancel'); 
const modal = document.getElementById('room-modal-add');
const modalEdit = document.getElementById('room-modal-edit');
 
openModalButton.addEventListener('click', function() {
   modal.classList.remove('hidden');
   // Menyembunyikan sidebar dan navbar ketika modal dibuka
   // document.getElementById('sidebar').classList.add('hidden');
   // document.querySelector('nav').classList.add('hidden');
});
 
closeModalButton.addEventListener('click', function() {
   modal.classList.add('hidden');
   document.getElementById('sidebar').classList.remove('hidden');
   document.querySelector('nav').classList.remove('hidden');
});
 
// Tombol Batal untuk menutup modal
closeModalCancelButton.addEventListener('click', function() {
   modal.classList.add('hidden');
   document.getElementById('sidebar').classList.remove('hidden');
   document.querySelector('nav').classList.remove('hidden');
});
</script>
<script>
// Open and close Add Room Modal
document.getElementById('open-modal').addEventListener('click', function() {
   document.getElementById('room-modal').classList.remove('hidden');
});
 
// Open and close Edit Room Modal
const editRoomButtons = document.querySelectorAll('.edit-room-btn');
editRoomButtons.forEach(button => {
   button.addEventListener('click', function(e) {
      e.preventDefault();
 
      // Get data attributes from the clicked button
      const roomId = this.getAttribute('data-room-id');
      const roomName = this.getAttribute('data-name');
      const roomType = this.getAttribute('data-type');
      const roomAc = this.getAttribute('data-ac');
      const roomCapacity = this.getAttribute('data-capacity');
      const roomPrice = this.getAttribute('data-price');
      const roomStatus = this.getAttribute('data-status');
 
      // Set the values in the modal
      document.getElementById('room_name').value = roomName;
      document.getElementById('type').value = roomType;
      document.getElementById('ac').value = roomAc;
      document.getElementById('capacity').value = roomCapacity;
      document.getElementById('price').value = roomPrice;
      document.getElementById('status').value = roomStatus;
 
      // Show the edit modal
      document.getElementById('room-modal-edit').classList.remove('hidden');
   });
});
 
// Close modal functions
document.getElementById('close-modal-icon').addEventListener('click', function() {
   document.getElementById('room-modal').classList.add('hidden');
});
 
document.getElementById('close-modal-cancel').addEventListener('click', function() {
   document.getElementById('room-modal').classList.add('hidden');
});
</script>
 
 
</body>
</html>
 
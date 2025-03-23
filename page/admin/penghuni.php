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

      // Ambil parameter filter dari query string
      $name = isset($_GET['name']) ? $_GET['name'] : '';   

   // Total data untuk menghitung jumlah halaman dengan filter
   $total_sql = "SELECT COUNT(*) FROM penghuni WHERE nama LIKE :name";
   $total_stmt = $pdo->prepare($total_sql);
   $total_stmt->bindValue(':name', "%$name%");
   $total_stmt->execute();
   $total_data = $total_stmt->fetchColumn();
   $total_pages = ceil($total_data / $limit);


   // Query untuk data penghuni dengan filter dan pagination
   $sql = "SELECT 
         A.id, 
         A.nama, 
         A.jenis_kelamin, 
         A.nomor_telepon, 
         A.alamat_asal, 
         A.status, 
         B.name AS room_name, 
         A.ktp, 
         A.tanggal_masuk,  
         A.nomor_darurat
      FROM 
         penghuni A
      INNER JOIN
         rooms B ON B.id = A.room_id
      WHERE 
         A.nama LIKE :name
      ORDER BY A.tanggal_masuk DESC
      LIMIT :limit OFFSET :offset";
   $stmt_rooms = $pdo->prepare($sql);
   $stmt_rooms->bindValue(':name', "%$name%");
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
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
   <title>KosKozie</title>
   <link rel="icon" type="image/png" class="rounded-full" href="../../assets/logo/Kozie.png">
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
      <h1 class="text-lg font-medium text-white mt-4 uppercase">
         Dashboard for Admin
      </h1>
   </div>
   <nav>
      <ul class="space-y-4">
            <li><a href="dashboard-admin.php" class="block px-4 py-2 rounded-md text-white font-medium bg-tr hover:text-blue-300 items-center space-x-3  "><i class="bx bx-home text-xl"></i><span>Dashboard Overview</span></a></li>
            <li><a href="kamar.php" class="block px-4 py-2 rounded-md font-medium text-white hover:text-blue-300  items-center space-x-3 "><i class="bx bx-bed text-xl"></i><span>Kamar</span></a></li>
            <li><a href="penghuni.php" class="block px-4 py-2 rounded-md font-medium text-white hover:text-blue-300  items-center space-x-3 shadow-lg"><i class="bx bx-user text-xl"></i><span>Penghuni</span></a></li>
            <li><a href="pembayaran.php" class="block px-4 py-2 rounded-md font-medium text-white hover:text-blue-300  items-center space-x-3"><i class="bx bx-wallet text-xl"></i><span>Pembayaran</span></a></li>
            <li>
               <a href="belum-bayar.php" class="block px-4 py-2 rounded-md font-medium text-white hover:text-blue-300 items-center space-x-3">
                  <i class="bx bx-time-five text-xl"></i>
                  <span>Belum Bayar</span>
               </a>
            </li><li><a href="maintenance.php" class="block px-4 py-2 rounded-md font-medium text-white hover:text-blue-300  items-center space-x-3"><i class="bx bx-wrench text-xl"></i><span>Maintenance</span></a></li>
            <li><a href="broadcast.php" class="block px-4 py-2 rounded-md font-medium  text-white hover:text-blue-300  items-center space-x-3"><i class="bx bx-bell text-xl"></i><span>Broadcast Notifikasi</span></a></li>
            <li><a href="pengajuan-keluar.php" class="block px-4 py-2 rounded-md font-medium text-white hover:text-blue-300 items-center space-x-3"><i class="fa-solid fa-person-walking-arrow-right text-md"></i><span>Pengajuan Keluar Kos</span></a></li>
            <!-- <li><a href="pengguna.php" class="block px-4 py-2 rounded-md font-medium text-white hover:text-blue-300  items-center space-x-3"><i class="bx bx-group text-xl"></i><span>Pengguna</span></a></li> -->
            <li><a href="peraturan.php" class="block px-4 py-2 rounded-md font-medium text-white hover:text-blue-300 items-center space-x-3"><i class="bx bx-info-circle text-xl"></i><span>Peraturan</span></a></li>
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

   <!-- Content -->
   <div class="p-8">
      <div class="bg-white rounded-lg shadow-md">
         <div class="p-6 mt-16">
            <div class="justify-between flex mb-5">
               <h2 class="text-2xl font-semibold mb-4">Penghuni</h2>
               <!-- Button to open modal -->
               <button id="open-modal" class="flex items-center justify-center gap-2 w-auto bg-blue-500 hover:bg-blue-600 rounded-md px-4 py-2 text-white font-medium shadow">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                     <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                  </svg>
                  Tambah Penghuni
               </button>
            </div>

            <form action="penghuni.php" method="GET">
            <!-- Filter -->
               <div class="mb-5 grid grid-cols-2 gap-3">
                  <div class="relative w-full">
                     <label for="name" class="block text-sm font-medium text-gray-700">Nama Penghuni</label>
                     <input type="text" id="room_name" placeholder="Cari Nama penghuni" name="name" class="py-3 px-4 mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                  </div>
               </div>
               <!-- Filter -->

               <div class="flex items-center justify-end mb-5">
                  <!-- Tombol Find -->
                  <button type="submit" class="flex items-center justify-center gap-2 w-32 bg-blue-500 hover:bg-blue-600 rounded-md px-4 py-2 text-white font-medium shadow">
                     <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 4a7 7 0 011 13.938V21l5-5-5-5v3.062A5.975 5.975 0 0017 12a6 6 0 10-6 6c1.453 0 2.77-.48 3.939-1.281L12 17V7c0-1.343.672-2.602 1.745-3.485L14 3H11z" />
                     </svg>
                     Cari
                  </button>
                  <!-- Tombol Find -->
               </div>
            </form>
            
            <!-- Table to display rooms -->
            <div class="overflow-x-auto">
               <table class="min-w-full table-auto">
                  <thead>
                     <tr class="bg-gray-100">
                        <th class="px-4 py-2 text-left">Aksi</th>
                        <th class="px-4 py-2 text-left">Nama Penghuni</th>
                        <th class="px-4 py-2 text-left">No Kamar</th>
                        <th class="px-4 py-2 text-left">Jenis Kelamin</th>
                        <th class="px-4 py-2 text-left">Status</th>
                        <th class="px-4 py-2 text-left">Tanggal Masuk</th>
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
                                 <!-- Tombol Edit -->
                                 <a href="#" 
                                    class="edit-room-btn text-blue-500 hover:text-blue-700" 
                                    data-id="<?= $room['id'] ?>" 
                                    data-name="<?= htmlspecialchars($room['nama']) ?>" 
                                    data-room-id="<?= htmlspecialchars($room['room_id']) ?>" 
                                    data-nomor-telepon="<?= htmlspecialchars($room['nomor_telepon']) ?>" 
                                    data-jenis-kelamin="<?= htmlspecialchars($room['jenis_kelamin']) ?>" 
                                    data-alamat-asal="<?= htmlspecialchars($room['alamat_asal']) ?>" 
                                    data-status="<?= htmlspecialchars($room['status']) ?>" 
                                    onclick="openModal(this)">
                                    <i class="bx bx-edit"></i>
                                 </a>

                                 <!-- Tombol Hapus -->
                                 <a href="../../function/admin/penghuni/delete-penghuni.php?id=<?= $room['id'] ?>" 
                                    class="ml-4 text-red-500 hover:text-red-700" 
                                    onclick="return confirm('Apakah Anda yakin ingin menghapus penghuni ini?');">
                                    <i class="bx bx-trash text-xl"></i>
                                 </a>
                              </td>

                              <td class="px-4 py-2">
                                 <div class="font-bold"><?= htmlspecialchars($room['nama']) ?></div>
                                 <div class="text-sm text-gray-600"><?= htmlspecialchars($room['nomor_telepon']) ?></div>
                              </td>
                              <td class="px-4 py-2 ">
                                 <?= htmlspecialchars($room['room_name']) ?>
                              </td>
                              <td class="px-4 py-2">
                                 <?= htmlspecialchars($room['jenis_kelamin']) ?>
                              </td>
                              <td class="<?php 
                                          if ($room['status'] === 'active') {
                                             echo 'text-green-500 font-medium text-left'; 
                                          } elseif ($room['status'] === 'inactive') {
                                             echo 'text-red-500 font-medium text-left'; 
                                          } 
                                       ?>">
                                          <?php
                                          if ($room['status'] === 'active') {
                                             echo 'Aktif';
                                          } elseif ($room['status'] === 'inactive') {
                                             echo 'Non-aktif';
                                          } else {
                                             echo 'Status Tidak Diketahui'; // Tambahan jika status tidak sesuai
                                          }
                                          ?>
                                       </td>
                              <td class="px-4 py-2">
                                 <?php 
                                    echo date_format(date_create($room['tanggal_masuk']), 'd M Y'); 
                                 ?>
                              </td> 
                           </tr>
                        <?php endforeach; ?>
                     <?php endif; ?>
                  </tbody>
               </table>
                  <div class="flex justify-end mt-4">
                     <nav class="inline-flex space-x-1">
                           <?php if ($page > 1): ?>
                              <a href="?page=<?= $page - 1 ?>&limit=<?= $limit ?>&name=<?= urlencode($name) ?>" class="px-3 py-1 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                                 &laquo;
                              </a>
                           <?php endif; ?>

                           <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                              <a href="?page=<?= $i ?>&limit=<?= $limit ?>&name=<?= urlencode($name) ?>" class="px-3 py-1 <?= $i == $page ? 'bg-blue-500 text-white' : 'bg-gray-300 text-gray-700 hover:bg-gray-400' ?> rounded-md">
                                 <?= $i ?>
                              </a>
                           <?php endfor; ?>

                           <?php if ($page < $total_pages): ?>
                              <a href="?page=<?= $page + 1 ?>&limit=<?= $limit ?>&name=<?= urlencode($name) ?>" class="px-3 py-1 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                                 &raquo;
                              </a>
                           <?php endif; ?>
                     </nav>
                  </div>
            </div>
         </div>
      </div>
   </div>
</div>

<!-- Modal ADD-->
<div id="room-modal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex justify-center items-center z-50 hidden">
   <div class="bg-white rounded-lg p-6 shadow-md w-2/3">
      <div class="mb-4">
         <h2 class="text-xl font-medium mb-2">Tambah Penghuni</h2>
         <label class="text-red-500 text-sm ">Semua field wajib diisi</label>
         <!-- Close Modal Icon -->
         <button id="close-modal-icon" class="text-red-500 absolute top-2 right-2">
            <i class="bx bx-x text-3xl"></i>
         </button>
      </div>
      <form id="add-penghuni-form" action="../../function/admin/penghuni/add-penghuni.php" method="POST">
    <div class="flex space-x-4 mb-4">
        <div class="flex-1">
            <label for="nama" class="block text-sm font-medium text-gray-700">Nama Penghuni</label>
            <input type="text" id="nama" name="nama" placeholder="Masukkan Nama penghuni" class="py-3 px-4 mt-1 block w-full border border-gray-300 rounded-md">
        </div>
        <div class="flex-1">
            <label for="jenis_kelamin" class="block text-sm font-medium text-gray-700">Jenis Kelamin</label>
            <select id="jenis_kelamin" name="jenis_kelamin" class="py-3 px-4 mt-1 block w-full border border-gray-300 rounded-md">
                <option value="">-- Pilih Jenis Kelamin --</option>
                <option value="Laki-laki">Laki-Laki</option>
                <option value="Perempuan">Perempuan</option>
            </select>
        </div>
    </div>

    <div class="flex space-x-4 mb-4">
        <div class="flex-1">
            <label for="ktp" class="block text-sm font-medium text-gray-700">NIK</label>
            <input type="text" id="ktp" name="ktp" placeholder="Masukkan NIK" maxlength="16" class="py-3 px-4 mt-1 block w-full border border-gray-300 rounded-md" oninput="validateNIK(this)">
            <p id="error-msg" class="text-red-500 text-sm mt-1 hidden">NIK harus 16 angka!</p>
        </div>
        <div class="flex-1">
            <label for="alamat_asal" class="block text-sm font-medium text-gray-700">Alamat Asal</label>
            <input type="text" id="alamat_asal" name="alamat_asal" placeholder="Masukkan Alamat Asal" class="py-3 px-4 mt-1 block w-full border border-gray-300 rounded-md">
        </div>
    </div>

    <div class="flex space-x-4 mb-4">
        <div class="flex-1">
            <label for="nomor_telepon" class="block text-sm font-medium text-gray-700">Nomor Telepon</label>
            <input type="text" id="nomor_telepon" name="nomor_telepon" placeholder="Masukkan Nomor Telepon" class="py-3 px-4 mt-1 block w-full border border-gray-300 rounded-md">
        </div>
        <div class="flex-1">
            <label for="nomor_darurat" class="block text-sm font-medium text-gray-700">Nomor Telepon Darurat (Opsional)</label>
            <input type="text" id="nomor_darurat" name="nomor_darurat" placeholder="Masukkan Nomor Telepon Darurat" class="py-3 px-4 mt-1 block w-full border border-gray-300 rounded-md">
        </div>
    </div>

    <div class="flex space-x-4 mb-4">
        <div class="flex-1">
            <label for="room_id" class="block text-sm font-medium text-gray-700">Nama Kamar</label>
            <select name="room_id" id="room_id" class="mt-1 p-2 border rounded w-full">
                <option value="">-- Pilih Kamar --</option>
                <?php
                try {
                    $pdo = new PDO("mysql:host=localhost;dbname=kos_management", "root", "");
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $sql = "SELECT id, name FROM rooms WHERE status = '1' ORDER BY name ASC";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute();
                    while ($room = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo '<option value="' . htmlspecialchars($room['id']) . '">' . htmlspecialchars($room['name']) . '</option>';
                    }
                } catch (PDOException $e) {
                    echo '<option value="">Error: ' . htmlspecialchars($e->getMessage()) . '</option>';
                }
                ?>
            </select>
        </div>
        <div class="flex-1">
            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
            <select id="status" name="status" class="py-3 px-4 mt-1 block w-full border border-gray-300 rounded-md">
                <option value="">-- Pilih Status --</option>
                <option value="active">Active</option>
                <option value="non-active">Non Active</option>
            </select>
        </div>
    </div>

    <div class="flex space-x-4 mb-4">
        <div class="flex-1">
            <label for="tanggal_masuk" class="block text-sm font-medium text-gray-700">Tanggal Masuk</label>
            <input type="date" id="tanggal_masuk" name="tanggal_masuk" class="py-3 px-4 mt-1 block w-full border border-gray-300 rounded-md">
        </div>
    </div>

    <div class="flex space-x-4 mb-4">
            <div class="flex-1">
               <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
               <input type="text" id="username" name="username" placeholder="Masukkan Username" class="py-3 px-4 mt-1 block w-full border border-gray-300 rounded-md">
            </div>
            <div class="flex-1">
               <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
               <div class="relative">
                  <input type="text" id="password" name="password" value="123456789" class="py-3 px-4 mt-1 block w-full border border-gray-300 rounded-md">
                  <button type="button" onclick="generatePassword()" class="absolute right-2 top-2 text-sm text-blue-500 hover:text-blue-700">Generate</button>
               </div>
            </div>
         </div>

    <div class="flex justify-end gap-5">
        <button type="button" id="close-modal-cancel" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">Batal</button>
        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">Tambah</button>
    </div>
</form>   </div>
</div>
<!-- Modal ADD-->

<!-- Modal Edit -->
<div id="edit-room-modal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex justify-center items-center z-50 hidden">
   <div class="bg-white rounded-lg p-6 shadow-md w-2/3 relative">
      <h2 class="text-xl font-medium mb-2">Edit Penghuni</h2>
      <label class="text-red-500 text-sm ">Semua field wajib diisi</label>
      <!-- Close Modal Icon -->
      <button id="close-edit-modal" class="absolute top-2 right-2 text-red-500 text-3xl">&times;</button>

      <form id="edit-form" action="../../function/admin/penghuni/edit-penghuni.php" method="POST">
         <input type="hidden" id="edit_id" name="id">

         <div class="flex space-x-4 mb-4">
            <div class="flex-1">
               <label class="block text-sm font-medium text-gray-700">Nama Penghuni</label>
               <input type="text" id="edit_nama" name="nama" class="py-3 px-4 mt-1 block w-full border rounded-md">
            </div>

            <div class="flex-1">
               <label for="edit_room_id" class="block text-sm font-medium text-gray-700">Nama Kamar</label>
               <select name="edit_room_id" id="edit_room_id" class="mt-1 p-2 border rounded w-full">
                  <option value="">-- Pilih Kamar --</option>
                  <?php
                  try {
                     $pdo = new PDO("mysql:host=localhost;dbname=kos_management", "root", "");
                     $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                     $sql = "SELECT id, name FROM rooms WHERE status = '1' ORDER BY name ASC";
                     $stmt = $pdo->prepare($sql);
                     $stmt->execute();
                     while ($room = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo '<option value="' . htmlspecialchars($room['id']) . '">' . htmlspecialchars($room['name']) . '</option>';
                     }
                  } catch (PDOException $e) {
                     echo '<option value="">Error: ' . htmlspecialchars($e->getMessage()) . '</option>';
                  }
                  ?>
               </select>
            </div>
         </div>

         <div class="flex space-x-4 mb-4">
            <div class="flex-1">
               <label class="block text-sm font-medium text-gray-700">NIK</label>
               <input type="text" id="edit_ktp" name="ktp" class="py-3 px-4 mt-1 block w-full border rounded-md">
            </div>
            <div class="flex-1">
               <label class="block text-sm font-medium text-gray-700">Alamat Asal</label>
               <input type="text" id="edit_alamat_asal" name="alamat_asal" class="py-3 px-4 mt-1 block w-full border rounded-md">
            </div>
         </div>

         <div class="flex space-x-4 mb-4">
            <div class="flex-1">
               <label class="block text-sm font-medium text-gray-700">Nomor Telepon</label>
               <input type="text" id="edit_nomor_telepon" name="nomor_telepon" class="py-3 px-4 mt-1 block w-full border rounded-md">
            </div>
            <div class="flex-1">
               <label class="block text-sm font-medium text-gray-700">Status</label>
               <select id="edit_status" name="status" class="py-3 px-4 mt-1 block w-full border rounded-md">
                  <option value="active">Active</option>
                  <option value="non-active">Non Active</option>
               </select>
            </div>
         </div>

         <div class="flex space-x-4 mb-4">
            <div class="flex-1">
               <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
               <input type="text" id="username" name="username" placeholder="Masukkan Username" class="py-3 px-4 mt-1 block w-full border border-gray-300 rounded-md">
            </div>
            <div class="flex-1">
               <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
               <div class="relative">
                  <input type="text" id="password" name="password" value="123456789" class="py-3 px-4 mt-1 block w-full border border-gray-300 rounded-md">
                  <button type="button" onclick="generatePassword()" class="absolute right-2 top-2 text-sm text-blue-500 hover:text-blue-700">Generate</button>
               </div>
            </div>
         </div>

         <div class="flex justify-end gap-5">
            <button type="button" id="close-edit-modal-btn" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">Batal</button>
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">Simpan Perubahan</button>
         </div>
      </form>
   </div>
</div>

<!-- Modal Edit -->
<div id="toast"></div>
</body>
</html>


<!-- Modal EDIT-->
<script>

document.getElementById("add-penghuni-form").addEventListener("submit", function(event) {
        let isValid = true;
        const requiredFields = ["nama", "jenis_kelamin", "ktp", "alamat_asal", "nomor_telepon", "room_id", "status", "tanggal_masuk"];
        
        requiredFields.forEach(field => {
            const input = document.getElementById(field);
            if (!input.value.trim()) {
                isValid = false;
                input.classList.add("border-red-500");
            } else {
                input.classList.remove("border-red-500");
            }
        });

        // Validasi NIK harus 16 angka
        const nikInput = document.getElementById("ktp");
        if (nikInput.value.length !== 16) {
            isValid = false;
            document.getElementById("error-msg").classList.remove("hidden");
        } else {
            document.getElementById("error-msg").classList.add("hidden");
        }

        if (!isValid) {
            event.preventDefault(); // Hentikan form jika ada error
            alert("Harap isi semua field yang diperlukan dengan benar!");
        }
    });

    function validateNIK(input) {
        const errorMsg = document.getElementById("error-msg");
        const nikValue = input.value;

        input.value = nikValue.replace(/\D/g, ""); // Hanya angka

        if (nikValue.length !== 16) {
            errorMsg.classList.remove("hidden");
        } else {
            errorMsg.classList.add("hidden");
        }
    }

   function changeLimit() {
      const limit = document.getElementById('limit').value;
      window.location.href = `?limit=${limit}&page=1`; // Reset ke halaman 1 saat jumlah data berubah
   }

   // Toggle Sidebar for mobile
   document.getElementById('toggle-sidebar').addEventListener('click', function() {
      const sidebar = document.getElementById('sidebar');
      sidebar.classList.toggle('hidden');
   });
   function generatePassword() {
        document.getElementById('password').value = '123456789';
    }

const closeModalButton = document.getElementById('close-modal-icon');
const closeModalCancelButton = document.getElementById('close-modal-cancel'); 
const modalEdit = document.getElementById('room-modal-edit');  // Pastikan modal yang benar

// Tombol Batal untuk menutup modal
closeModalCancelButton.addEventListener('click', function() {
   modalEdit.classList.add('hidden');  // Pastikan menambahkan class 'hidden' pada modal yang tepat
   document.getElementById('sidebar').classList.remove('hidden');  // Menampilkan kembali sidebar jika diperlukan
   document.querySelector('nav').classList.remove('hidden');  // Menampilkan kembali navbar jika diperlukan
});

// Menutup modal menggunakan tombol close (X)
closeModalButton.addEventListener('click', function() {
   modalEdit.classList.add('hidden');
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
      const id = this.getAttribute('data-id');
      const name = this.getAttribute('data-name');
      const roomId = this.getAttribute('data-room-id');
      const nik = this.getAttribute('data-nik');
      const alamatAsal = this.getAttribute('data-alamat-asal');
      const nomorTelepon = this.getAttribute('data-nomor-telepon');
      const status = this.getAttribute('data-status');
      

      // ngeset data ke modal edit
      document.getElementById('nama').value = name;
      document.getElementById('room_id').value = roomId;
      document.getElementById('nik').value = nik;
      document.getElementById('alamat_asal').value = alamatAsal;
      document.getElementById('nomor_telepon').value = nomorTelepon;
      document.getElementById('status').value = status;

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

// Fungsi Toast Notification
function showToast(message) {
        var toast = document.getElementById("toast");
        toast.innerHTML = message;
        toast.style.visibility = "visible";
        setTimeout(function() {
            toast.style.visibility = "hidden";
        }, 3000); // Toast akan menghilang setelah 3 detik
    }


    // Tampilkan Modal Edit dengan Data Penghuni
    document.addEventListener("DOMContentLoaded", function () {
    // Event Listener untuk tombol Edit (Menggunakan Event Delegation)
    document.body.addEventListener("click", function (event) {
        if (event.target.closest(".edit-room-btn")) {
            event.preventDefault();

            let button = event.target.closest(".edit-room-btn");

            // Ambil data dari atribut tombol
            document.getElementById("edit_id").value = button.dataset.id;
            document.getElementById("edit_nama").value = button.dataset.name;
            document.getElementById("edit_room_id").value = button.dataset.room_id;
            document.getElementById("edit_ktp").value = button.dataset.ktp;
            document.getElementById("edit_alamat_asal").value = button.dataset.alamat_asal;
            document.getElementById("edit_nomor_telepon").value = button.dataset.nomor_telepon;
            document.getElementById("edit_status").value = button.dataset.status;

            // Tampilkan modal edit
            document.getElementById("edit-room-modal").classList.remove("hidden");
        }
    });

    // Tutup Modal Edit saat tombol "X" atau "Batal" diklik
    document.getElementById("close-edit-modal").addEventListener("click", function () {
        document.getElementById("edit-room-modal").classList.add("hidden");
    });
    document.getElementById("close-edit-modal-btn").addEventListener("click", function () {
        document.getElementById("edit-room-modal").classList.add("hidden");
    });
});



</script>



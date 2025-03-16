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

$limit = 10; // Jumlah peraturan per halaman
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $session_username = $_SESSION['username'];

    // Query untuk mengambil nama admin
    $stmt = $pdo->prepare("SELECT name FROM users WHERE username = :username");
    $stmt->execute(['username' => $session_username]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    $admin_name = $admin['name'] ?? 'Admin';
  
   // Ambil semua peraturan
   $stmt = $pdo->query("SELECT * FROM peraturan_kos ORDER BY created_at DESC");
   $rules = $stmt->fetchAll(PDO::FETCH_ASSOC);

   $stmt = $pdo->prepare("SELECT * FROM peraturan_kos ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $rules = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Hitung total data
    $stmt = $pdo->query("SELECT COUNT(*) FROM peraturan_kos");
    $total_rows = $stmt->fetchColumn();
    $total_pages = ceil($total_rows / $limit);

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
<div id="sidebar" class="hidden md:block w-72 h-full bg-gradient-to-r from-indigo-500 to-blue-500 text-gray-800 fixed top-0 left-0 p-5  flex-col shadow-lg z-50">
   <div class="mb-6 text-center">
      <img src="../../assets/logo/Kozie.png" alt="Logo" class="h-20 mx-auto rounded-full">
      <h1 class="text-lg font-medium text-white mt-4 uppercase">
         Dashboard for Admin
      </h1>
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
            <li><a href="maintenance.php" class="block px-4 py-2 rounded-md font-medium text-white hover:text-blue-300  items-center space-x-3 "><i class="bx bx-wrench text-xl"></i><span>Maintenance</span></a></li>
            <li><a href="broadcast.php" class="block px-4 py-2 rounded-md font-medium  text-white hover:text-blue-300  items-center space-x-3"><i class="bx bx-bell text-xl"></i><span>Broadcast Notifikasi</span></a></li>
            <li><a href="pengajuan-keluar.php" class="block px-4 py-2 rounded-md font-medium text-white hover:text-blue-300 items-center space-x-3"><i class="fa-solid fa-person-walking-arrow-right text-md"></i><span>Pengajuan Keluar Kos</span></a></li>
            <li><a href="kritik-saran.php" class="block px-4 py-2 rounded-md font-medium text-white hover:text-blue-300  items-center space-x-3"><i class="bx bx-message-detail text-xl"></i><span>Kritik dan Saran</span></a></li>
            <!-- <li><a href="pengguna.php" class="block px-4 py-2 rounded-md font-medium text-white hover:text-blue-300  items-center space-x-3"><i class="bx bx-group text-xl"></i><span>Pengguna</span></a></li> -->
            <li><a href="peraturan.php" class="block px-4 py-2 rounded-md font-medium text-white hover:text-blue-300 items-center space-x-3 shadow-lg"><i class="bx bx-info-circle text-xl"></i><span>Peraturan</span></a></li>
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


    <!-- Content -->
    <div class="p-8 mt-16">
        <div class="bg-white rounded-lg shadow-md">
            <div class="p-6">
                <div class="justify-between flex mb-5">
                    <h2 class="text-2xl font-semibold mb-4">Peraturan</h2>
                    <button class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition"
                    onclick="openAddModal()">Tambah Peraturan</button>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto">
                        <thead>
                            <tr class="bg-gray-100">
                            <th class="px-4 py-2 text-center">Aksi</th>
                                <th class="px-4 py-2 text-left">Deskripsi</th>                                
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($rules)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-3">Tidak ada pengajuan maintenance</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($rules as $request): ?>
                                    <tr>
                                        <td class="px-4 py-2 text-center">
                                             <a href="#" class="text-blue-500 hover:text-blue-700" 
                                                onclick="openEditModal(<?php echo htmlspecialchars(json_encode($request)); ?>)">
                                                <i class="bx bx-edit"></i>                                            
                                             </a>
                                            <a href="../../function/admin/peraturan/delete-peraturan.php?id=<?= $request['id'] ?>" class="ml-4 text-red-500 hover:text-red-700" onclick="return confirm('Apakah Anda yakin ingin menghapus peraturan ini?');">
                                                <i class="bx bx-trash"></i>
                                            </a>
                                        </td>
                                        <td class="px-4 py-2 text-left"><?php echo htmlspecialchars($request['isi_peraturan']); ?></td>                                        
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    <?php if ($total_pages > 1): ?>
                     <div class="flex justify-end mt-4">
                        <nav class="inline-flex space-x-1">
                              <?php if ($page > 1): ?>
                                 <a href="?page=<?= $page - 1 ?>" class="px-3 py-1 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                                    &laquo;
                                 </a>
                              <?php endif; ?>

                              <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                 <a href="?page=<?= $i ?>" class="px-3 py-1 <?= $i == $page ? 'bg-blue-500 text-white' : 'bg-gray-300 text-gray-700 hover:bg-gray-400' ?> rounded-md">
                                    <?= $i ?>
                                 </a>
                              <?php endfor; ?>

                              <?php if ($page < $total_pages): ?>
                                 <a href="?page=<?= $page + 1 ?>" class="px-3 py-1 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                                    &raquo;
                                 </a>
                              <?php endif; ?>
                        </nav>
                     </div>
                  <?php endif; ?>

                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Peraturan -->
<div id="addModal" class="fixed hidden inset-0 bg-gray-500 bg-opacity-75 flex justify-center items-center z-50 hiddens">
    <div class="bg-white rounded-lg shadow-lg p-6 w-96">
        <h3 class="text-lg font-semibold mb-4">Tambah Peraturan</h3>
        <form method="POST" action ="../../function/admin/peraturan/tambah-peraturan.php">
            <textarea name="isi_peraturan" class="w-full p-2 border rounded-md" placeholder="Tulis peraturan..." required></textarea>
            <div class="flex justify-end mt-4">
                <button type="button" class="bg-red-500 text-white px-4 py-2 rounded-md mr-2 hover:bg-gray-600" 
                    onclick="closeAddModal()">Batal</button>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Peraturan -->
<div id="editModal" class="fixed hidden inset-0 bg-gray-500 bg-opacity-75 flex justify-center items-center z-50">
    <div class="bg-white rounded-lg shadow-lg p-6 w-96">
        <h3 class="text-lg font-semibold mb-4">Edit Peraturan</h3>
        <form method="POST" action="../../function/admin/peraturan/update-peraturan.php">
            <input type="hidden" id="edit_rule_id" name="id">
            <textarea id="edit_isi_peraturan" name="isi_peraturan" class="w-full p-2 border rounded-md" required></textarea>
            <div class="flex justify-end mt-4">
                <button type="button" class="bg-red-500 text-white px-4 py-2 rounded-md mr-2 hover:bg-gray-600" 
                    onclick="closeEditModal()">Batal</button>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Simpan</button>
            </div>
        </form>
    </div>
</div>


</div>

</body>

<script>
   function openAddModal() {
    document.getElementById('addModal').classList.remove('hidden');
   }

   function closeAddModal() {
      document.getElementById('addModal').classList.add('hidden');
   }

   function openEditModal(rule) {
      document.getElementById('edit_rule_id').value = rule.id;
      document.getElementById('edit_isi_peraturan').value = rule.isi_peraturan;
      document.getElementById('editModal').classList.remove('hidden');
   }

   function closeEditModal() {
      document.getElementById('editModal').classList.add('hidden');
   }

</script>
</html>

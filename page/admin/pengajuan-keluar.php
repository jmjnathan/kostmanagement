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

    // Query untuk mengambil data pengajuan maintenance
    $sql = 
    "SELECT 
      A. *,
      B.nama nama_penghuni, B.room_id, C.name nomor_kamar
      FROM pengajuan_keluar A 
      INNER JOIN penghuni B ON B.id = A.penghuni_id
      INNER JOIN rooms C ON B.room_id = C.id;"
    ;
    $stmt_requests = $pdo->prepare($sql);
    $stmt_requests->execute();
    $requests = $stmt_requests->fetchAll(PDO::FETCH_ASSOC);

    // Hapus penghuni jika tanggal keluar sudah lewat
   $delete_sql = "DELETE FROM penghuni WHERE id IN (
      SELECT penghuni_id FROM pengajuan_keluar WHERE tanggal_rencana_keluar < CURDATE() AND status = 'approved'
   )";

   $update_room_sql = "UPDATE rooms SET status = 'tersedia' WHERE id IN (
      SELECT room_id FROM penghuni WHERE id IN (
         SELECT penghuni_id FROM pengajuan_keluar WHERE tanggal_rencana_keluar < CURDATE() AND status = 'approved'
      )
   )";

$pdo->exec($delete_sql);
$pdo->exec($update_room_sql);


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
            <li><a href="maintenance.php" class="block px-4 py-2 rounded-md font-medium text-white hover:text-blue-300  items-center space-x-3"><i class="bx bx-wrench text-xl"></i><span>Maintenance</span></a></li>
            <li><a href="broadcast.php" class="block px-4 py-2 rounded-md font-medium  text-white hover:text-blue-300  items-center space-x-3"><i class="bx bx-bell text-xl"></i><span>Broadcast Notifikasi</span></a></li>
            <li><a href="pengajuan-keluar.php" class="block px-4 py-2 rounded-md font-medium text-white hover:text-blue-300 items-center space-x-3 shadow-lg"><i class="fa-solid fa-person-walking-arrow-right text-md"></i><span>Pengajuan Keluar Kos</span></a></li>
            <li><a href="kritik-saran.php" class="block px-4 py-2 rounded-md font-medium text-white hover:text-blue-300  items-center space-x-3"><i class="bx bx-message-detail text-xl"></i><span>Kritik dan Saran</span></a></li>
            <!-- <li><a href="pengguna.php" class="block px-4 py-2 rounded-md font-medium text-white hover:text-blue-300  items-center space-x-3"><i class="bx bx-group text-xl"></i><span>Pengguna</span></a></li> -->
            <li><a href="peraturan.php" class="block px-4 py-2 rounded-md font-medium text-white hover:text-blue-300 items-center space-x-3"><i class="bx bx-info-circle text-xl"></i><span>Peraturan</span></a></li>
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
                    <h2 class="text-2xl font-semibold mb-4">Pengajuan Keluar Kos</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto">
                        <thead>
                            <tr class="bg-gray-100">
                            <th class="px-4 py-2 text-center">Aksi</th>
                                <th class="px-4 py-2 text-left">Nama Penghuni</th>
                                <th class="px-4 py-2 text-left">Pengajuan</th>
                                <th class="px-4 py-2 text-left">Keluar</th>
                                <th class="px-4 py-2 text-left">Alasan</th>
                                <th class="px-4 py-2 text-left">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($requests)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-3">Tidak ada pengajuan maintenance</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($requests as $request): ?>
                                    <tr>
                                       <td class="px-4 py-2 text-center">
                                          <a href="#" class="text-blue-500 hover:text-blue-700" onclick="openModal(<?php echo htmlspecialchars(json_encode($request)); ?>)">
                                             <i class="bx bx-edit"></i>
                                          </a>
                                       </td>
                                        <td class="px-4 py-2">
                                          <div class="font-bold"><?= htmlspecialchars($request['nama_penghuni']) ?></div>
                                          <div class="text-sm text-gray-600"><?= htmlspecialchars($request['nomor_kamar']) ?></div>
                                       </td>                                        
                                       <td class="px-4 py-2">
                                          <?php 
                                          echo date_format(date_create($request['tanggal_pengajuan']), 'd M Y'); 
                                          ?>
                                       </td>
                                       <td class="px-4 py-2">
                                          <?php 
                                          echo date_format(date_create($request['tanggal_rencana_keluar']), 'd M Y'); 
                                          ?>
                                       </td>
                                        <td class="px-4 py-2"><?php echo htmlspecialchars($request['alasan']); ?></td>
                                        <td class="<?php 
                                          if ($request['status'] === 'approved') {
                                             echo 'text-green-500 font-medium text-left'; 
                                          } elseif ($request['status'] === 'pending') {
                                             echo 'text-yellow-500 font-medium text-left'; 
                                          } 
                                       ?>">
                                          <?php
                                          if ($request['status'] === 'approved') {
                                             echo 'Diterima';
                                          } elseif ($request['status'] === 'pending') {
                                             echo 'Menunggu Persetujuan';
                                          } else {
                                             echo 'Status Tidak Diketahui'; // Tambahan jika status tidak sesuai
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

<!-- Modal Approval Keluar -->
<div id="approval-modal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex justify-center items-center z-50 hidden">
    <div class="bg-white rounded-lg p-6 w-3/6 shadow-lg">
        <h2 class="text-xl font-semibold mb-4">Setujui Pengajuan Keluar Kos</h2>
        <form id="approval-form" method="POST" action="../../function/admin/pengajuan-keluar/edit-status.php">
            <input type="hidden" id="request-id" name="id">
            
            <div class="flex-1">
                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                <select id="status" name="status" class="py-3 px-4 mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <option value="">-- Pilih Status --</option>
                    <option value="approved">Terima</option>
                </select>
            </div>
            <label class="block text-sm font-medium text-gray-700 mt-3">Catatan (Opsional)</label>
            <textarea id="note" name="note" rows="3" class="w-full border rounded-md p-2 mt-2"></textarea>

            <div class="flex justify-end space-x-3 mt-4">
                <!-- Tombol Batal (Tutup Modal) -->
                <button type="button" onclick="closeModal()" class="bg-red-500 text-white px-4 py-2 rounded-md">Batal</button>
                <!-- Tombol Simpan (Submit Form) -->
                <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-md">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
   function openModal(request) {
        document.getElementById('approval-modal').classList.remove('hidden');
        document.getElementById('request-id').value = request.id;
        document.getElementById('status').value = request.status;
        document.getElementById('note').value = request.note || '';    }
   function closeModal() {
        document.getElementById('approval-modal').classList.add('hidden');
    }

</script>

</body>


</html>

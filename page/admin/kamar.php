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
    
    // Ambil nama admin
    $stmt = $pdo->prepare("SELECT name FROM users WHERE username = :username");
    $stmt->execute(['username' => $session_username]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    $admin_name = $admin['name'] ?? 'Admin';

    // Default pagination parameters
    $limit = 10; // Default 10
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Default halaman 1
    $offset = ($page - 1) * $limit;

    // Ambil parameter filter dari query string
    $name = isset($_GET['name']) ? $_GET['name'] : '';

    // Total data untuk menghitung jumlah halaman dengan filter
    $total_sql = "SELECT COUNT(*) FROM rooms WHERE name LIKE :name";
    $total_stmt = $pdo->prepare($total_sql);
    $total_stmt->bindValue(':name', "%$name%");
    $total_stmt->execute();
    $total_data = $total_stmt->fetchColumn();
    $total_pages = ceil($total_data / $limit);

    // Query untuk data kamar dengan filter dan pagination
    $sql = "SELECT * FROM rooms WHERE name LIKE :name ORDER BY name ASC LIMIT :limit OFFSET :offset";
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

// Menampilkan pesan toast jika ada
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
    <style>
        body {
            font-family: 'Poppins', sans-serif;
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
</head>
<body class="bg-gray-100 min-h-screen">

<!-- Sidebar -->
<div id="sidebar" class="hidden md:block w-72 h-full bg-gradient-to-r from-indigo-500 to-blue-500 text-gray-800 fixed top-0 left-0 p-5 flex-col shadow-lg z-50">
    <div class="mb-6 text-center">
        <img src="../../assets/logo/Kozie.png" alt="Logo" class="h-20 mx-auto rounded-full">
        <h1 class="text-lg font-medium text-white mt-4 uppercase">Dashboard for Admin</h1>
    </div>
    <nav>
        <ul class="space-y-4">
            <li><a href="dashboard-admin.php" class="block px-4 py-2 rounded-md text-white font-medium bg-tr hover:text-blue-300 items-center space-x-3"><i class="bx bx-home text-xl"></i><span>Dashboard Overview</span></a></li>
            <li><a href="kamar.php" class="block px-4 py-2 rounded-md font-medium text-white hover:text-blue-300 items-center space-x-3 shadow-lg"><i class="bx bx-bed text-xl"></i><span>Kamar</span></a></li>
            <li><a href="penghuni.php" class="block px-4 py-2 rounded-md font-medium text-white hover:text-blue-300 items-center space-x-3"><i class="bx bx-user text-xl"></i><span>Penghuni</span></a></li>
            <li><a href="pembayaran.php" class="block px-4 py-2 rounded-md font-medium text-white hover:text-blue-300 items-center space-x-3"><i class="bx bx-wallet text-xl"></i><span>Pembayaran</span></a></li>
            <li><a href="belum-bayar.php" class="block px-4 py-2 rounded-md font-medium text-white hover:text-blue-300 items-center space-x-3"><i class="bx bx-time-five text-xl"></i><span>Belum Bayar</span></a></li>
            <li><a href="maintenance.php" class="block px-4 py-2 rounded-md font-medium text-white hover:text-blue-300 items-center space-x-3"><i class="bx bx-wrench text-xl"></i><span>Maintenance</span></a></li>
            <li><a href="broadcast.php" class="block px-4 py-2 rounded-md font-medium text-white hover:text-blue-300 items-center space-x-3"><i class="bx bx-bell text-xl"></i><span>Broadcast Notifikasi</span></a></li>
            <li><a href="pengajuan-keluar.php" class="block px-4 py-2 rounded-md font-medium text-white hover:text-blue-300 items-center space-x-3"><i class="fa-solid fa-person-walking-arrow-right text-md"></i><span>Pengajuan Keluar Kos</span></a></li>
            <li><a href="peraturan.php" class="block px-4 py-2 rounded-md font-medium text-white hover:text-blue-300 items-center space-x-3"><i class="bx bx-info-circle text-xl"></i><span>Peraturan</span></a></li>
            <li><a href="../../logout.php" class="block px-4 py-2 rounded-md text-red-500 hover:text-red-700 items-center space-x-3 font-medium"><i class="bx bx-log-out text-xl"></i><span>Logout</span></a></li>
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
    <nav class="flex items-center justify-end bg-white p-4 fixed top-0 md:left-72 right-0 shadow-md z-10">
        <div class="flex items-center space-x-4">
            <a href="#" class="profile">
                <img src="../../assets/logo/user.png" alt="Profile" class="h-10 w-10 rounded-full">
            </a>
            <span class="text-sm md:text-lg font-medium text-blue-700">Welcome, <?php echo htmlspecialchars($admin_name); ?>!</span>
        </div>
    </nav>

    <!-- Content -->
    <div class="p-8">
        <div class="bg-white rounded-lg shadow-md">
            <div class="p-6 mt-16">
                <div class="justify-between flex mb-5">
                    <h2 class="text-2xl font-semibold mb-4">Kamar</h2>
                    <button id="open-modal" class="flex items-center justify-center gap-2 w-auto bg-blue-500 hover:bg-blue-600 rounded-md px-4 py-2 text-white font-medium shadow">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Kamar
                    </button>
                </div>

                <form action="kamar.php" method="GET">
                    <div class="mb-5 grid grid-cols-2 gap-3">
                        <div class="relative w-full">
                            <label for="name" class="block text-sm font-medium text-gray-700">Nama Kamar</label>
                            <input type="text" id="room_name" placeholder="Cari Nama Kamar" name="name" class="py-3 px-4 mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                    </div>
                    <div class="flex items-center justify-end mb-5">
                        <button type="submit" class="flex items-center justify-center gap-2 w-32 bg-blue-500 hover:bg-blue-600 rounded-md px-4 py-2 text-white font-medium shadow">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 4a7 7 0 011 13.938V21l5-5-5-5v3.062A5.975 5.975 0 0017 12a6 6 0 10-6 6c1.453 0 2.77-.48 3.939-1.281L12 17V7c0-1.343.672-2.602 1.745-3.485L14 3H11z" />
                            </svg>
                            Cari
                        </button>
                    </div>
                </form>

                <!-- Table to display rooms -->
                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="px-4 py-2 text-left">Aksi</th>
                                <th class="px-4 py-2 text-left">No Kamar</th>
                                <th class="px-4 py-2 text-left">Jenis</th>
                                <th class="px-4 py-2 text-right">Kapasitas</th>
                                <th class="px-4 py-2 text-right">Harga</th>
                                <th class="px-4 py-2 text-left">Deskripsi</th>
                                <th class="px-4 py-2 text-center">Status</th>
                                <th class="px-4 py-2 text-center">Aktif</th>
                                <th class="px-4 py-2 text-center">Foto</th>
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
                                        <td class="px-4 py-2">
                                            <a href="#" 
                                              class="edit-room-btn text-blue-500 hover:text-blue-700" 
                                              data-id="<?= $room['id'] ?>" 
                                              data-name="<?= htmlspecialchars($room['name']) ?>" 
                                              data-room-type="<?= htmlspecialchars($room['type']) ?>" 
                                              data-ac="<?= htmlspecialchars($room['ac']) ?>" 
                                              data-capacity="<?= htmlspecialchars($room['capacity']) ?>" 
                                              data-price="<?= htmlspecialchars($room['price']) ?>" 
                                              data-status="<?= htmlspecialchars($room['status']) ?>" 
                                              data-active="<?= htmlspecialchars($room['active']) ?>" 
                                              onclick="openModal(this)">
                                              <i class="bx bx-edit"></i>
                                           </a>
                                            <a href="../../function/admin/kamar/delete-room.php?id=<?= $room['id'] ?>" class="ml-4 text-red-500 hover:text-red-700" onclick="return confirm('Apakah Anda yakin ingin menghapus kamar ini?');">
                                                <i class="bx bx-trash"></i>
                                            </a>
                                        </td>
                                        <td class="px-4 py-2 w-36 h-16"><?php echo htmlspecialchars($room['name']); ?></td>
                                        <td class="px-4 py-2 w-52">
                                            <?php 
                                                if ($room['type'] === 'km_luar') {
                                                    echo 'Kamar Mandi Luar';
                                                } elseif ($room['type'] === 'km_dalam') {
                                                    echo 'Kamar Mandi Dalam';
                                                } else {
                                                    echo htmlspecialchars($room['type']); // Default jika ada tipe lain
                                                }
                                            ?> - <?php echo htmlspecialchars($room['ac']); ?>
                                        </td>
                                        <td class="px-4 py-2 text-right"><?php echo htmlspecialchars($room['capacity']); ?> orang</td>
                                        <td class="px-4 py-2 text-right">Rp.<?php echo number_format($room['price'], 0, ',', '.'); ?></td>
                                        <td class="px-4 py-2 w-60"><?php echo htmlspecialchars($room['description']); ?></td>
                                        <td class="<?php 
                                            // Tentukan teks berdasarkan status
                                            if ($room['status'] === '1') {
                                                echo 'text-green-500 font-medium text-center'; // Class untuk 'Tersedia'
                                            } elseif ($room['status'] === '2') {
                                                echo 'text-yellow-500 font-medium text-center'; // Class untuk 'Di Perbaiki'
                                            } elseif ($room['status'] === '3') {
                                                echo 'text-red-500 font-medium text-center'; // Class untuk 'Terisi'
                                            }
                                        ?>">
                                            <?php
                                            // Tampilkan teks berdasarkan status
                                            if ($room['status'] === '1') {
                                                echo 'Tersedia';
                                            } elseif ($room['status'] === '2') {
                                                echo 'Di Perbaiki';
                                            } elseif ($room['status'] === '3') {
                                                echo 'Terisi';
                                            } else {
                                                echo htmlspecialchars($room['status']); // Default jika tidak cocok
                                            }
                                            ?>
                                        </td>
                                        <td class="<?php 
                                            // Tentukan teks berdasarkan status
                                            if ($room['active'] === 'active') {
                                                echo 'text-green-500 font-medium text-center'; // Class untuk 'Aktif'
                                            } elseif ($room['active'] === 'non-active') {
                                                echo 'text-red-500 font-medium text-center'; // Class untuk 'Tidak Aktif'
                                            } 
                                        ?>">
                                            <?php
                                            // Tampilkan teks berdasarkan status
                                            if ($room['active'] === 'active') {
                                                echo 'Aktif';
                                            } elseif ($room['active'] === 'non-active') {
                                                echo 'Tidak Aktif';
                                            } else {
                                                echo htmlspecialchars($room['active']); // Default jika tidak cocok
                                            }
                                            ?>
                                        </td>
                                        <td class="px-4 py-2 text-center">
                                            <img src="data:image/jpeg;base64,<?php echo base64_encode($room['foto']); ?>" alt="Room Image" class="w-16 h-16 object-cover rounded-lg">
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
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
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal ADD-->
<div id="room-modal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex justify-center items-center z-50 hidden">
    <div class="bg-white rounded-lg p-6 shadow-md w-2/3">
        <div class="mb-4">
            <h2 class="text-xl font-medium">Tambah Kamar</h2>
            <button id="close-modal-icon" class="text-red-500 absolute top-2 right-2">
                <i class="bx bx-x text-3xl"></i>
            </button>
        </div>
        <form action="../../function/admin/kamar/add-room.php" method="POST" enctype="multipart/form-data">
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700">Nama Kamar</label>
                <input type="text" id="room_name" name="name" placeholder="Masukkan Nama Kamar" class="py-3 px-4 mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
            </div>
            <div class="flex space-x-4 mb-4">
                <div class="flex-1">
                    <label for="type" class="block text-sm font-medium text-gray-700">Jenis Kamar</label>
                    <select id="type" name="room_type" class="py-3 px-4 mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                        <option value="">-- Pilih Jenis Kamar --</option>
                        <option value="km_luar">Kamar Mandi Luar</option>
                        <option value="km_dalam">Kamar Mandi Dalam</option>
                    </select>
                </div>
                <div class="flex-1">
                    <label for="ac" class="block text-sm font-medium text-gray-700">Fasilitas</label>
                    <select id="ac" name="ac" class="py-3 px-4 mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                        <option value="">-- Pilih Jenis Fasilitas --</option>
                        <option value="AC">AC</option>
                        <option value="Non-Ac">Non-Ac</option>
                    </select>
                </div>
            </div>
            <div class="flex space-x-4 mb-4">
                <div class="flex-1">
                    <label for="capacity" class="block text-sm font-medium text-gray-700">Kapasitas</label>
                    <select id="capacity" name="capacity" class="py-3 px-4 mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                        <option value="">-- Pilih Kapasitas --</option>
                        <option value="1">1 orang</option>
                        <option value="2">2 orang</option>
                    </select>
                </div>
                <div class="flex-1">
                    <label for="price" class="block text-sm font-medium text-gray-700">Harga</label>
                    <input type="number" id="price" placeholder='Masukkan Harga' name="price" class="py-3 px-4 mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                </div>
            </div>
            <div class="flex space-x-4 mb-4">
               <div class="flex-1">
                  <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                  <select id="status" name="status" class="py-3 px-4 mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                     <option value="1">Tersedia</option>
                     <option value="2">Sedang Diperbaiki</option>
                     <option value="3">Terisi</option>
                  </select>
               </div>
               <div class="flex-1">
                  <label for="active" class="block text-sm font-medium text-gray-700">Aktif/Non-aktif</label>
                  <select id="active" name="active" class="mt-1 py-3 px-4 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                     <option value="active">Aktif</option>
                     <option value="non-active">Non-aktif</option>
                  </select>
               </div>
            </div>
            <div class="mb-4">
                <label for="foto" class="block text-sm font-medium text-gray-700">Foto Kamar</label>
                <input type="file" id="foto" name="foto" accept="image/*" class="py-3 px-4 mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
            </div>
           
            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi Kamar</label>
                <textarea id="description" name="description" placeholder="Masukkan Deskripsi Kamar" rows="4" class="py-3 px-4 mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required></textarea>
            </div>
            <div class="flex justify-end gap-5">
                <button type="button" id="close-modal-cancel" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">Batal</button>
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">Tambah</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div id="room-modal-edit" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex justify-center items-center z-50 hidden">
    <div class="bg-white rounded-lg p-6 shadow-md w-2/3 relative">
        <h2 class="text-xl font-semibold mb-2">Edit Kamar</h2>
        <button id="close-modal-icon" class="absolute top-2 right-2 text-red-500 text-3xl" onclick="closeModal()">&times;</button>

        <form id="edit-form" action="../../function/admin/kamar/edit-room.php" method="POST">
            <input type="hidden" id="edit-room-id" name="id">

            <div class="mb-4">
                <label for="edit-room_name" class="block text-sm font-medium text-gray-700">Nama Kamar</label>
                <input type="text" id="edit-room_name" name="name" class="py-3 px-4 mt-1 block w-full border rounded-md" required>
            </div>

            <div class="flex space-x-4 mb-4">
                <div class="flex-1">
                    <label for="edit-type" class="block text-sm font-medium text-gray-700">Jenis Kamar</label>
                    <select id="edit-type" name="room_type" class="py-3 px-4 mt-1 block w-full border rounded-md" required>
                        <option value="km_luar">Kamar Mandi Luar</option>
                        <option value="km_dalam">Kamar Mandi Dalam</option>
                    </select>
                </div>
                <div class="flex-1">
                    <label for="edit-ac" class="block text-sm font-medium text-gray-700">AC</label>
                    <select id="edit-ac" name="ac" class="py-3 px-4 mt-1 block w-full border rounded-md" required>
                        <option value="AC">AC</option>
                        <option value="Non-Ac">Non-Ac</option>
                    </select>
                </div>
            </div>

            <div class="flex space-x-4 mb-4">
                <div class="flex-1">
                    <label for="edit-capacity" class="block text-sm font-medium text-gray-700">Kapasitas</label>
                    <select id="edit-capacity" name="capacity" class="py-3 px-4 mt-1 block w-full border rounded-md" required>
                        <option value="1">1 orang</option>
                        <option value="2">2 orang</option>
                    </select>
                </div>
                <div class="flex-1">
                    <label for="edit-price" class="block text-sm font-medium text-gray-700">Harga</label>
                    <input type="number" id="edit-price" name="price" class="py-3 px-4 mt-1 block w-full border rounded-md" required>
                </div>
            </div>

            <div class="flex space-x-4 mb-4">
                <div class="flex-1">
                    <label for="edit-status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select id="edit-status" name="status" class="py-3 px-4 mt-1 block w-full border rounded-md" required>
                        <option value="1">Tersedia</option>
                        <option value="2">Sedang Diperbaiki</option>
                        <option value="3">Terisi</option>
                    </select>
                </div>
                <div class="flex-1">
                    <label for="edit-active" class="block text-sm font-medium text-gray-700">Aktif/Non-aktif</label>
                    <select id="edit-active" name="active" class="py-3 px-4 mt-1 block w-full border rounded-md" required>
                        <option value="active">Aktif</option>
                        <option value="non-active">Non-aktif</option>
                    </select>
                </div>
            </div>

            <div class="mb-4">
                <label for="edit-description" class="block text-sm font-medium text-gray-700">Deskripsi Kamar</label>
                <textarea id="edit-description" name="description" rows="4" class="py-3 px-4 mt-1 block w-full border rounded-md"></textarea>
            </div>

            <div class="flex justify-end gap-5">
                <button type="button" id="close-modal-edit-cancel" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded" onclick="closeModal()">Batal</button>
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<div id="toast"></div>

<script>
    // Open Add Room Modal
    document.getElementById('open-modal').addEventListener('click', function() {
        document.getElementById('room-modal').classList.remove('hidden');
    });

    // Open Edit Room Modal
    const editRoomButtons = document.querySelectorAll('.edit-room-btn');
    editRoomButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();

            // Get data attributes from the clicked button
            const roomId = this.getAttribute('data-id');
            const roomName = this.getAttribute('data-name');
            const roomType = this.getAttribute('data-room-type');
            const roomAc = this.getAttribute('data-ac');
            const roomCapacity = this.getAttribute('data-capacity');
            const roomPrice = this.getAttribute('data-price');
            const roomStatus = this.getAttribute('data-status');
            const roomActive = this.getAttribute('data-active');

            // Populate modal fields with data
            document.getElementById('edit-room-id').value = roomId;
            document.getElementById('edit-room_name').value = roomName;
            document.getElementById('edit-type').value = roomType;
            document.getElementById('edit-ac').value = roomAc;
            document.getElementById('edit-capacity').value = roomCapacity;
            document.getElementById('edit-price').value = roomPrice;
            document.getElementById('edit-status').value = roomStatus;
            document.getElementById('edit-active').value = roomActive;

            // Show the edit modal
            document.getElementById('room-modal-edit').classList.remove('hidden');
        });
    });

    // Close modals
    const closeModalButtons = document.querySelectorAll('#close-modal-icon, #close-modal-cancel, #close-modal-edit-cancel');
    closeModalButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Hide both modals
            document.getElementById('room-modal').classList.add('hidden');
            document.getElementById('room-modal-edit').classList.add('hidden');
        });
    });

    // Toast Notification Function
    function showToast(message) {
        var toast = document.getElementById("toast");
        toast.innerHTML = message;
        toast.style.visibility = "visible";
        setTimeout(function() {
            toast.style.visibility = "hidden";
        }, 3000); // Toast will disappear after 3 seconds
    }
</script>

</body>
</html>
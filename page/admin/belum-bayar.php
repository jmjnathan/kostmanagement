<?php
session_start();

// Cek apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit();
}

// Cek role pengguna
if ($_SESSION['role'] !== 'admin') {
    header('Location: dashboard-user.php');
    exit();
}

$host = 'localhost';
$dbname = 'kos_management';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Ambil nama admin
    $session_username = $_SESSION['username'];
    $stmt = $pdo->prepare("SELECT name FROM users WHERE username = :username");
    $stmt->execute(['username' => $session_username]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    $admin_name = $admin['name'] ?? 'Admin';

    // Ambil parameter filter dari query string
    $month_year = isset($_GET['month_year']) ? $_GET['month_year'] : date('Y-m');
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = 10; // Jumlah data per halaman
    $offset = ($page - 1) * $limit;

    // Query untuk mendapatkan total data
    $count_sql = "SELECT COUNT(*) FROM penghuni B
                  LEFT JOIN pembayaran A ON B.id = A.penghuni_id AND DATE_FORMAT(A.tanggal_bayar, '%Y-%m') = :month_year
                  WHERE A.penghuni_id IS NULL";
    $stmt_count = $pdo->prepare($count_sql);
    $stmt_count->bindValue(':month_year', $month_year);
    $stmt_count->execute();
    $total_records = $stmt_count->fetchColumn();
    $total_pages = ceil($total_records / $limit);

    // Query untuk mendapatkan penghuni yang belum membayar dengan pagination
    $sql = "SELECT B.nama AS nama_penghuni, B.nomor_telepon AS penghuni_nomor_telepon,
                   C.name AS nomor_kamar, B.tanggal_masuk,
                   DATEDIFF(CURDATE(), B.tanggal_masuk) AS lama_hari,
                   TIMESTAMPDIFF(MONTH, B.tanggal_masuk, CURDATE()) AS lama_bulan
            FROM penghuni B
            INNER JOIN rooms C ON B.room_id = C.id
            LEFT JOIN pembayaran A ON B.id = A.penghuni_id AND DATE_FORMAT(A.tanggal_bayar, '%Y-%m') = :month_year
            WHERE A.penghuni_id IS NULL
            LIMIT :limit OFFSET :offset";
    
    $stmt_bayar = $pdo->prepare($sql);
    $stmt_bayar->bindValue(':month_year', $month_year);
    $stmt_bayar->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt_bayar->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt_bayar->execute();
    $belum_bayar = $stmt_bayar->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
    exit();
}
?>

<?php
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
            <li><a href="kamar.php" class="block px-4 py-2 rounded-md font-medium text-white hover:text-blue-300 items-center space-x-3"><i class="bx bx-bed text-xl"></i><span>Kamar</span></a></li>
            <li><a href="penghuni.php" class="block px-4 py-2 rounded-md font-medium text-white hover:text-blue-300 items-center space-x-3"><i class="bx bx-user text-xl"></i><span>Penghuni</span></a></li>
            <li><a href="pembayaran.php" class="block px-4 py-2 rounded-md font-medium text-white hover:text-blue-300 items-center space-x-3"><i class="bx bx-wallet text-xl"></i><span>Pembayaran</span></a></li>
            <li>
               <a href="belum-bayar.php" class="block px-4 py-2 rounded-md font-medium text-white hover:text-blue-300 items-center space-x-3 shadow-lg">
                  <i class="bx bx-time-five text-xl"></i>
                  <span>Belum Bayar</span>
               </a>
            </li>
            <li><a href="maintenance.php" class="block px-4 py-2 rounded-md font-medium text-white hover:text-blue-300 items-center space-x-3"><i class="bx bx-wrench text-xl"></i><span>Maintenance</span></a></li>
            <li><a href="broadcast.php" class="block px-4 py-2 rounded-md font-medium text-white hover:text-blue-300 items-center space-x-3"><i class="bx bx-bell text-xl"></i><span>Broadcast Notifikasi</span></a></li>
            <li><a href="pengajuan-keluar.php" class="block px-4 py-2 rounded-md font-medium text-white hover:text-blue-300 items-center space-x-3"><i class="fa-solid fa-person-walking-arrow-right text-md"></i><span>Pengajuan Keluar Kos</span></a></li>
            <li><a href="kritik-saran.php" class="block px-4 py-2 rounded-md font-medium text-white hover:text-blue-300 items-center space-x-3"><i class="bx bx-message-detail text-xl"></i><span>Kritik dan Saran</span></a></li>
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
                    <h2 class="text-2xl font-semibold mb-4">Laporan Tunggakan</h2>
                </div>

                <form action="belum-bayar.php" method="GET">
                  <div class="mb-5 grid grid-cols-2 gap-3">
                     <div class="relative w-full">
                           <label for="month_year" class="block text-sm font-medium text-gray-700">Bulan & Tahun</label>
                           <input type="month" id="month_year" name="month_year" value="<?= htmlspecialchars($month_year); ?>" class="py-3 px-4 mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                     </div>
                  </div>
                  <div class="flex items-center justify-end mb-5">
                     <button type="submit" class="flex items-center justify-center gap-2 w-32 bg-blue-500 hover:bg-blue-600 rounded-md px-4 py-2 text-white font-medium shadow">
                           <i class="bx bx-search"></i>
                           Cari
                     </button>
                  </div>
               </form>


                <!-- Table to display unpaid tenants -->
                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="px-4 py-2 text-left">Nama Penyewa</th>
                                <th class="px-4 py-2 text-left">Nomor Telepon</th>
                                <th class="px-4 py-2 text-left">Lama Tunggakan</th>
                                <th class="px-4 py-2 text-left">Hubungi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($belum_bayar)): ?>
                                <tr>
                                    <td colspan="3" class="text-center py-3">No data available</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($belum_bayar as $tenant): ?>
                                    <tr>
                                       <td class="px-4 py-2">
                                          <div class="font-bold"><?= htmlspecialchars($tenant['nama_penghuni']) ?></div>
                                          <div class="text-sm text-gray-600"><?= htmlspecialchars($tenant['nomor_kamar']) ?></div>
                                       </td>                                         
                                       <td class="px-4 py-2"><?php echo htmlspecialchars($tenant['penghuni_nomor_telepon']); ?></td>
                                       <td class="px-4 py-2 text-red-500 font-semibold"><?= $tenant['lama_bulan'] . ' bulan'; ?></td>          
                                       <td class="px-4 py-2">
                                          <?php 
                                             // Ubah format nomor telepon dari 08... menjadi +628...
                                             $nomor_wa = preg_replace('/^08/', '+628', htmlspecialchars($tenant['penghuni_nomor_telepon'])); 
                                          ?>
                                          <a href="https://wa.me/<?= $nomor_wa ?>" target="_blank" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded-md shadow">
                                             <i class="bx bxl-whatsapp"></i> Hubungi
                                          </a>
                                       </td>
                      
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>

                    <div class="flex justify-end mt-4">
                        <nav class="inline-flex space-x-1">
                            <?php if ($page > 1): ?>
                                <a href="?month_year=<?= $month_year ?>&page=<?= $page - 1 ?>" class="px-3 py-1 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">&laquo;</a>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <a href="?month_year=<?= $month_year ?>&page=<?= $i ?>" class="px-3 py-1 <?= $i == $page ? 'bg-blue-500 text-white' : 'bg-gray-300 text-gray-700 hover:bg-gray-400' ?> rounded-md"> <?= $i ?> </a>
                            <?php endfor; ?>

                            <?php if ($page < $total_pages): ?>
                                <a href="?month_year=<?= $month_year ?>&page=<?= $page + 1 ?>" class="px-3 py-1 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">&raquo;</a>
                            <?php endif; ?>
                        </nav>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<div id="toast"></div>

<script>
    // Fungsi Toast Notification
    function showToast(message) {
        var toast = document.getElementById("toast");
        toast.innerHTML = message;
        toast.style.visibility = "visible";
        setTimeout(function() {
            toast.style.visibility = "hidden";
        }, 3000); // Toast will disappear after 3 seconds
    }

    document.getElementById("month_year").addEventListener("change", function() {
        this.form.submit();
    });
</script>

</body>
</html>
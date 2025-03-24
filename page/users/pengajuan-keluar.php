<?php
session_start();
require '../../db.php';

// Cek apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit();
}

// Cek role pengguna
if ($_SESSION['role'] !== 'user') {
    header('Location: dashboard-admin.php');
    exit();
}

try {
    $pdo = new PDO("mysql:host=localhost;dbname=kos_management", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $session_username = $_SESSION['username'];

    // Query untuk mengambil data nomor kamar
    $stmt = $pdo->prepare("SELECT A.*, B.name 
                           FROM penghuni A 
                           INNER JOIN rooms B ON A.room_id = B.id 
                           WHERE A.username = :username");
    $stmt->execute(['username' => $session_username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Ambil id_penghuni berdasarkan username
    $stmt = $pdo->prepare("SELECT id FROM penghuni WHERE username = :username");
    $stmt->execute(['username' => $session_username]);
    $penghuni = $stmt->fetch(PDO::FETCH_ASSOC);

    $id_penghuni = $penghuni ? $penghuni['id'] : null;

    // Cek apakah user sudah memiliki pengajuan keluar
    $pengajuan = [];
    $sudahMengajukan = false;
    if ($id_penghuni) {
        $stmt = $pdo->prepare(
            "SELECT * FROM pengajuan_keluar WHERE penghuni_id = :id_penghuni ORDER BY tanggal_pengajuan DESC LIMIT 1"
        );
        $stmt->execute(['id_penghuni' => $id_penghuni]);
        $pengajuan = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($pengajuan) {
            $sudahMengajukan = true;
        }
    }
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengajuan Keluar Kos</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Font & Boxicons -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="../../assets/logo/Kozie.png">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">

    <!-- Navbar -->
    <nav class="bg-gradient-to-r from-indigo-500 to-blue-500 text-white py-4 px-6 flex justify-between items-center">
        <div class="text-xl font-medium">
            <a href="#">KosKozie</a>
        </div>
        <ul class="hidden md:flex space-x-6">
            <li><a href="../../logout.php" class="flex items-center space-x-2 px-4 py-2 text-red-500 hover:text-red-700"><i class="bx bx-log-out text-xl"></i><span>Logout</span></a></li>
        </ul>
    </nav>

    <!-- Container -->
    <div class="max-w-4xl mx-auto mt-6 bg-white p-6 shadow-lg rounded-lg">
        <div class="mb-4 flex flex-col sm:flex-row justify-between items-center">
            <a href="dashboard-users.php" class="inline-flex items-center px-4 py-2 mt-2 sm:mt-0 bg-gray-500 text-white rounded-lg shadow-md hover:bg-gray-600">
                <i class='bx bx-arrow-back text-xl mr-2'></i> Kembali
            </a>
        </div>

        <h2 class="text-xl font-bold text-center mb-4">Pengajuan Keluar Kos</h2>

        <?php if (!$sudahMengajukan) : ?>
            <!-- FORM PENGAJUAN KELUAR -->
            <form action="../../function/user/pengajuan-keluar/pengajuan-add.php" method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold">Nama Penghuni</label>
                    <input type="text" value="<?php echo $user['nama']; ?>" name="nama_penghuni" class="w-full p-2 border rounded" required readonly>
                </div>
                <div>
                    <label class="block text-sm font-semibold">No Kamar</label>
                    <input type="text" name="no_kamar" value="<?php echo $user['name']; ?>" class="w-full p-2 border rounded" required readonly>
                </div>
                <div>
                    <label class="block text-sm font-semibold">Tgl Pengajuan</label>
                    <input type="date" name="tgl_pengajuan" id="tgl_pengajuan" class="w-full p-2 border rounded" required readonly>
                </div>
                <div>
                    <label class="block text-sm font-semibold">Pengajuan Tgl Keluar</label>
                    <input type="date" name="tgl_keluar" class="w-full p-2 border rounded" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold">Alasan</label>
                    <textarea name="alasan" class="w-full p-2 border rounded" required></textarea>
                </div>
                <button type="submit" class="w-full bg-indigo-600 text-white py-2 rounded hover:bg-indigo-700">
                    Ajukan Keluar Kos
                </button>
            </form>
        <?php else : ?>
            <!-- SURAT PENGAJUAN KELUAR -->
            <div class="border p-6 rounded-lg shadow-md bg-gray-50">
                <h3 class="text-lg font-bold text-center mb-4">Surat Pengajuan Keluar</h3>
                <div class="grid grid-cols-2 gap-y-2 text-sm">
                    <p class="font-semibold">Nama</p>
                    <p>: <?php echo $user['nama']; ?></p>

                    <p class="font-semibold">No Kamar</p>
                    <p>: <?php echo $user['name']; ?></p>

                    <p class="font-semibold">Tgl Pengajuan</p>
                    <p>: <?php echo $pengajuan['tanggal_pengajuan']; ?></p>

                    <p class="font-semibold">Tgl Keluar</p>
                    <p>: <?php echo $pengajuan['tanggal_keluar']; ?></p>

                    <p class="font-semibold">Alasan</p>
                    <p>: <?php echo $pengajuan['alasan']; ?></p>

                    <p class="font-semibold">Status</p>
                    <p>: <span class="font-bold text-indigo-600"><?php echo $pengajuan['status']; ?></span></p>
                </div>
            </div>

        <?php endif; ?>
    </div>
</body>
</html>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        let today = new Date().toISOString().split('T')[0]; // Format YYYY-MM-DD
        let tglPengajuan = document.getElementById("tgl_pengajuan");
        if (tglPengajuan) {
            tglPengajuan.value = today;
        }
    });
</script>

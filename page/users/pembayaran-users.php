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

if (!isset($_SESSION['id_penghuni'])) {
    die("Error: User ID tidak ditemukan dalam sesi.");
}

$host = 'localhost';
$dbname = 'kos_management';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $session_username = $_SESSION['username'];
    $user_id = $_SESSION['id_penghuni'];

    // Ambil data penghuni
    $stmt = $pdo->prepare("SELECT A.*, B.name AS room_name, A.tanggal_masuk, B.price
                           FROM penghuni A
                           INNER JOIN rooms B ON A.room_id = B.id
                           WHERE A.id = :user_id");
    $stmt->execute(['user_id' => (int)$user_id]);
    $penghuni = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$penghuni) {
        die("Data penghuni tidak ditemukan.");
    }

    $user_name = $penghuni['nama'] ?? 'User';
    $user_room = $penghuni['room_name'] ?? '-';
    $user_phone = $penghuni['nomor_telepon'] ?? '-';
    $tanggal_masuk = $penghuni['tanggal_masuk'];
    $jumlah = $penghuni['price'] ?? 0;

    // Ambil pembayaran bulan ini berdasarkan tanggal masuk penghuni
    $stmt = $pdo->prepare(
        "SELECT A.*
        FROM pembayaran A
        WHERE A.penghuni_id = :user_id
        AND MONTH(A.tanggal_bayar) = MONTH(CURRENT_DATE())
        AND YEAR(A.tanggal_bayar) = YEAR(CURRENT_DATE())
        AND A.status = 'Belum Bayar'"
    ); 
    $stmt->execute(['user_id' => (int)$user_id]);
    $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Jika tidak ada tagihan bulan ini, buat tagihan baru
    if (empty($payments)) {
        // Ambil bulan dan tahun dari tanggal masuk penghuni
        $bulan_masuk = date('m', strtotime($tanggal_masuk));
        $tahun_masuk = date('Y', strtotime($tanggal_masuk));

        // Tentukan tanggal jatuh tempo berdasarkan bulan dan tahun masuk
        if ($bulan_masuk == date('m')) {
            // Jika bulan masuk sama dengan bulan ini, jatuh tempo adalah tanggal yang sama di bulan ini
            $tanggal_jatuh_tempo = date('Y-m-d', strtotime($tanggal_masuk));
        } else {
            // Jika bulan masuk bukan bulan ini, tentukan jatuh tempo sesuai tanggal bulan ini
            $tanggal_jatuh_tempo = date('Y-m-d', strtotime('first day of this month'));
        }
        
        $stmt = $pdo->prepare(
            "INSERT INTO pembayaran (penghuni_id, jumlah, status, tanggal_bayar, keterangan)
            VALUES (:user_id, :jumlah, :status, :jatuh_tempo, :keterangan)"
        );
        
        // Menambahkan status yang sesuai dengan kolom 'status' di tabel pembayaran
        $status = 'Belum Bayar'; // Pastikan nilai ini sesuai dengan yang ada di ENUM atau VARCHAR
        $keterangan = ""; // Jika diperlukan keterangan, bisa diisi sesuai kebutuhan
        
        $stmt->execute([
            'user_id' => (int)$user_id,
            'jumlah' => $jumlah, // Menggunakan harga sewa yang sesuai
            'status' => $status,
            'jatuh_tempo' => $tanggal_jatuh_tempo,
            'keterangan' => $keterangan
        ]);

        // Ambil kembali data setelah insert
        $stmt = $pdo->prepare(
            "SELECT A.*
            FROM pembayaran A
            WHERE A.penghuni_id = :user_id
            AND MONTH(A.tanggal_bayar) = MONTH(CURRENT_DATE())
            AND YEAR(A.tanggal_bayar) = YEAR(CURRENT_DATE())
            AND A.status = 'Belum Bayar'"
        ); 
        $stmt->execute(['user_id' => (int)$user_id]);
        $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <title>Dashboard Penghuni</title>
    
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

    <!-- Card Rincian Penghuni -->
    <div class="max-w-4xl mx-auto mt-6 bg-white p-6 shadow-lg rounded-lg">
        <h2 class="text-2xl font-semibold ml-4">Pembayaran Kos</h2>
        <div class=" p-4 rounded-lg">
            <div class="grid grid-cols-2 gap-2 text-gray-700 font-medium">
                <p>Nama:</p><p><?php echo $user_name; ?></p>
                <p>No Kamar:</p><p><?php echo $user_room; ?></p>
                <p>No Telp:</p><p><?php echo $user_phone; ?></p>
                <p>Harga Kamar:</p><p><?php echo "Rp " . number_format($jumlah, 0, ',', '.'); ?></p>
                <p>Tanggal Masuk:</p><p><?php echo date('d M Y', strtotime($tanggal_masuk)); ?></p>
            </div>
        </div>
    </div>

    <!-- Card Rincian Pembayaran -->
    <div class="max-w-4xl mx-auto mt-6 bg-white p-6 shadow-lg rounded-lg">
        <h2 class="text-lg font-semibold mb-4">Rincian Tagihan Bulanan</h2>
        <?php if (empty($payments)): ?>
            <p class="text-center text-gray-600">Semua tagihan sudah dibayar!</p>
        <?php else: ?>
            <?php foreach ($payments as $payment): ?>
                <div class="flex justify-between items-center pb-2 mb-2">
                    <div>
                        <p class="text-md font-semibold"><?php echo date('F Y', strtotime($payment['tanggal_bayar'])); ?></p>
                        <p class="text-sm mt-2"> Jatuh Tempo: <?php echo date('d F Y', strtotime($payment['jatuh_tempo'])); ?></p>
                    </div>
                    <p class="text-red-600">Belum Bayar</p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        
        <!-- Button Pembayaran -->
        <div class="flex justify-center items-center border-b pb-2 mb-2 w-full">
            <button class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md w-full">Bayar</button>
        </div>
    </div>
    
</body>
</html>

<script>
    console.log(<?php echo json_encode($payment); ?>);
</script>

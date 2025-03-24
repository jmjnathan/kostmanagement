<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit();
}

if ($_SESSION['role'] !== 'user') {
    header('Location: dashboard-user.php');
    exit();
}

try {
    $pdo = new PDO("mysql:host=localhost;dbname=kos_management", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $session_username = $_SESSION['username'];

    // Query untuk mengambil data nomor kamar
    $stmt = $pdo->prepare("SELECT B.name 
                           FROM penghuni A 
                           INNER JOIN rooms B ON A.room_id = B.id 
                           WHERE A.username = :username");
    $stmt->execute(['username' => $session_username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Ambil id_penghuni berdasarkan username
    $stmt = $pdo->prepare("SELECT id FROM penghuni WHERE username = :username");
    $stmt->execute(['username' => $session_username]);
    $penghuni = $stmt->fetch(PDO::FETCH_ASSOC);

    // Pastikan id_penghuni ditemukan
    $id_penghuni = $penghuni ? $penghuni['id'] : null;

    if ($id_penghuni) {
        $stmt = $pdo->prepare("SELECT id, tanggal_pengajuan, kategori, deskripsi, status, bukti, deadline 
                            FROM maintenance 
                            WHERE id_penghuni = :id_penghuni 
                            ORDER BY tanggal_pengajuan DESC");
        $stmt->execute(['id_penghuni' => $id_penghuni]);
        $laporan_maintenance = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $laporan_maintenance = [];
    }




} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}

// Notifikasi jika ada
$notifikasi = $_SESSION['notifikasi'] ?? null;
unset($_SESSION['notifikasi']); // Hapus notifikasi setelah ditampilkan
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengajuan Maintenance</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
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
            <li>
                <a href="../../logout.php" class="flex items-center space-x-2 px-4 py-2 text-red-500 hover:text-red-700">
                    <i class="bx bx-log-out text-xl"></i><span>Logout</span>
                </a>
            </li>
        </ul>
    </nav>

        <!-- Daftar Laporan Maintenance -->
        <div class="mt-10 max-w-4xl mx-auto bg-white p-6 shadow-lg rounded-lg">
            <div class="mb-4 flex flex-col sm:flex-row justify-between items-center">
                <h2 class="text-xl font-semibold mb-4">Daftar Pengajuan Maintenance</h2>
                <a href="dashboard-users.php" class="inline-flex items-center px-4 py-2 mt-2 sm:mt-0 bg-gray-500 text-white rounded-lg shadow-md hover:bg-gray-600">
                    <i class='bx bx-arrow-back text-xl mr-2'></i> Kembali
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full ">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class=" px-4 py-2">Tanggal</th>
                            <th class=" px-4 py-2">Kategori</th>
                            <th class=" px-4 py-2">Deskripsi</th>
                            <th class=" px-4 py-2">Status</th>
                            <th class=" px-4 py-2">Deadline</th>
                            <th class=" px-4 py-2">Bukti</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($laporan_maintenance as $laporan): ?>
                            <tr class="text-center">
                                <td class="px-4 py-2">
                                    <?php 
                                      echo date_format(date_create($laporan['tanggal_pengajuan']), 'd M Y'); 
                                  ?>
                                </td>                                 
                                <td class=" px-4 py-2"><?php echo htmlspecialchars($laporan['kategori']); ?></td>
                                <td class=" px-4 py-2"><?php echo htmlspecialchars($laporan['deskripsi']); ?></td>
                                <td class=" px-4 py-2"><?php echo htmlspecialchars($laporan['status']); ?></td>
                                <td class=" px-4 py-2"><?php echo htmlspecialchars($laporan['deadline']); ?></td>
                                <td class=" px-4 py-2">
                                    <?php if (!empty($laporan['bukti'])): ?>
                                        <a href="<?php echo htmlspecialchars($laporan['bukti']); ?>" target="_blank" class="text-blue-500 underline">Lihat</a>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>


    <!-- Container -->
    <div class="max-w-4xl mx-auto mt-6 bg-white p-6 shadow-lg rounded-lg">
        <div class="mb-4 flex flex-col sm:flex-row justify-between items-center">
            <h2 class="text-2xl font-semibold text-center sm:text-left">Pengajuan Maintenance</h2>
        </div>
        
        <?php if (!empty($notifikasi)): ?>
            <div class="mb-4 p-3 bg-green-500 text-white rounded-md">
                <?php echo htmlspecialchars($notifikasi); ?>
            </div>
        <?php endif; ?>
        
        <form action="../../function/user/maintenance/maintenance-add.php" method="post" enctype="multipart/form-data">
            <label class="block font-medium">No Kamar:</label>
            <input type="text" name="nomor_kamar" value="<?php echo htmlspecialchars($user['name']); ?>" class="w-full px-3 py-2 border rounded-md" readonly>
            
            <label class="block font-medium mt-4">Kategori Maintenance:</label>
            <select name="kategori" class="w-full px-3 py-2 border rounded-md">
                <option value="">Pilih Pengajuan</option>
                <option value="Listrik">Listrik</option>
                <option value="Air">Air</option>
                <option value="Kamar Mandi">Kamar Mandi</option>
                <option value="Kebocoran">Kebocoran</option>
                <option value="Alasan Lain">Alasan Lain</option>
            </select>
            
            <label class="block font-medium mt-4">Deskripsi Kerusakan:</label>
            <textarea name="deskripsi" class="w-full px-3 py-2 border rounded-md" required></textarea>
            
            <label class="block font-medium mt-4">Status:</label>
            <select name="status" class="w-full px-3 py-2 border rounded-md">
                <option value="">Pilih Status</option>    
                <option value="Urgent">Urgent</option>
                <option value="Normal">Normal</option>
            </select>
            
            <label class="block font-medium mt-4">Upload Bukti:</label>
            <input type="file" name="bukti" class="w-full px-3 py-2 border rounded-md">
            
            <label class="block font-medium mt-4">Deadline Pengerjaan:</label>
            <input type="date" name="deadline" class="w-full px-3 py-2 border rounded-md" required>
            
            <button type="submit" class="w-full mt-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Submit</button>
        </form>

        
    </div>


    
</body>
</html>

<script>
    console.log('Pengguna Data:', <?php echo json_encode($laporan_maintenance); ?>);
</script>

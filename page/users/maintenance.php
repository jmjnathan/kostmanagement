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

$notifikasi = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nomor_kamar = $_POST['nomor_kamar'];
    $kategori = $_POST['kategori'];
    $deskripsi = $_POST['deskripsi'];
    $status = 'pending'; // Status awal pengajuan
    $deadline = $_POST['deadline'];
    
    // Upload file
    $bukti = '';
    if (!empty($_FILES['bukti']['name'])) {
        $target_dir = "uploads/";
        $bukti = $target_dir . basename($_FILES['bukti']['name']);
        move_uploaded_file($_FILES['bukti']['tmp_name'], $bukti);
    }
    
    // Simpan ke database
    $sql = "INSERT INTO maintenance (nomor_kamar, kategori, deskripsi, status, bukti, deadline, id_penghuni) 
            VALUES ('$nomor_kamar', '$kategori', '$deskripsi', '$status', '$bukti', '$deadline', '{$_SESSION['id_penghuni']}')";
    
    if ($conn->query($sql) === TRUE) {
        $notifikasi = "Pengajuan anda telah dikirim";
    } else {
        $notifikasi = "Error: " . $conn->error;
    }
}
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
            <li><a href="../../logout.php" class="flex items-center space-x-2 px-4 py-2 text-red-500 hover:text-red-700"><i class="bx bx-log-out text-xl"></i><span>Logout</span></a></li>
        </ul>
    </nav>

    <!-- Container -->
    <div class="max-w-4xl mx-auto mt-6 bg-white p-6 shadow-lg rounded-lg">
        <div class="mb-4 flex flex-col sm:flex-row justify-between items-center">
            <h2 class="text-2xl font-semibold text-center sm:text-left">Pengajuan Maintenance</h2>
            <a href="dashboard-users.php" class="inline-flex items-center px-4 py-2 mt-2 sm:mt-0 bg-gray-500 text-white rounded-lg shadow-md hover:bg-gray-600">
                <i class='bx bx-arrow-back text-xl mr-2'></i> Kembali
            </a>
        </div>
        
        <?php if ($notifikasi): ?>
            <div class="mb-4 p-3 bg-green-500 text-white rounded-md">
                <?php echo $notifikasi; ?>
            </div>
        <?php endif; ?>
        
        <form action="" method="post" enctype="multipart/form-data">
            <label class="block font-medium">No Kamar:</label>
            <input type="text" name="nomor_kamar" class="w-full px-3 py-2 border rounded-md" required>
            
            <label class="block font-medium mt-4">Kategori Maintenance:</label>
            <select name="kategori" class="w-full px-3 py-2 border rounded-md">
                <option value="Listrik">Pilih Pengajuan</option>
                <option value="Listrik">Listrik</option>
                <option value="Air">Air</option>
                <option value="Fasilitas">Kamar Mandi</option>
                <option value="Fasilitas">Kebocoran</option>
                <option value="Fasilitas">Alasan Lain</option>
            </select>
            </select>
            
            <label class="block font-medium mt-4">Deskripsi Kerusakan:</label>
            <textarea name="deskripsi" class="w-full px-3 py-2 border rounded-md" required></textarea>
            
            <label class="block font-medium mt-4">Status:</label>
            <select name="status" class="w-full px-3 py-2 border rounded-md">
                <option value="Listrik">Pilih Status</option>    
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
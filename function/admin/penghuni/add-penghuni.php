<?php
session_start();

// Cek apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    header('Location: index.php'); // Jika belum login, arahkan ke halaman login
    exit();
}

// Cek role pengguna, jika bukan admin, alihkan ke halaman lain
if ($_SESSION['role'] !== 'admin') {
    header('Location: dashboard-penghuni.php'); // Jika bukan admin, arahkan ke dashboard user atau halaman lain
    exit();
}

// Konfigurasi database
$host = 'localhost';
$dbname = 'kos_management';
$username = 'root';
$password = '';

try {
    // Koneksi ke database menggunakan PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Ambil data dari form
        $nomor_kamar = $_POST['nomor_kamar'];
        $nama_penghuni = $_POST['nama_penghuni'];
        $nomor_hp = $_POST['nomor_hp'];
        $alamat_asal = $_POST['alamat_asal'];
        $nik = $_POST['nik'];
        $tanggal_masuk = $_POST['tanggal_masuk'];
        $jenis_kelamin = $_POST['jenis_kelamin'];
        $status = $_POST['status'];

        // Query untuk menambahkan penghuni baru ke dalam database
        $stmt = $pdo->prepare("INSERT INTO penghuni_kos (nomor_kamar, nama_penghuni, nomor_hp, alamat_asal, nik, tanggal_masuk, jenis_kelamin, status) 
                               VALUES (:nomor_kamar, :nama_penghuni, :nomor_hp, :alamat_asal, :nik, :tanggal_masuk, :jenis_kelamin, :status)");
        $stmt->execute([
            'nomor_kamar' => $nomor_kamar,
            'nama_penghuni' => $nama_penghuni,
            'nomor_hp' => $nomor_hp,
            'alamat_asal' => $alamat_asal,
            'nik' => $nik,
            'tanggal_masuk' => $tanggal_masuk,
            'jenis_kelamin' => $jenis_kelamin,
            'status' => $status,
        ]);

        // Redirect ke halaman daftar penghuni setelah berhasil menambahkan
        header('Location: ../../../page/admin/penghuni.php');
        exit();
    }

} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
    exit();
}
?>

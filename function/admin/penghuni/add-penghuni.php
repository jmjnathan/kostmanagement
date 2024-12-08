<?php
// Koneksi ke database
include($_SERVER['DOCUMENT_ROOT'] . '/kostmanagement/page/admin/penghuni.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nomor_kamar = $_POST['nomor_kamar'];
    $nama_penghuni = $_POST['nama_penghuni'];
    $nomor_hp = $_POST['nomor_hp'];
    $alamat_asal = $_POST['alamat_asal'];
    $nik = $_POST['nik'];
    $tanggal_masuk = $_POST['tanggal_masuk'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $status = $_POST['status'];

    // Query untuk menambahkan data penghuni
    $sql = "INSERT INTO penghuni_kos (nomor_kamar, nama_penghuni, nomor_hp, alamat_asal, nik, tanggal_masuk, jenis_kelamin, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($nomor_kamar, $nama_penghuni, $nomor_hp, $alamat_asal, $nik, $tanggal_masuk, $jenis_kelamin, $status);

    if ($stmt->execute()) {
        // Redirect kembali ke halaman penghuni setelah berhasil
        header('Location: /admin/penghuni.php?status=success');
    } else {
        // Tampilkan pesan error jika gagal
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
    $conn->close();
}
?>

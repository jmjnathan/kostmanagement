<?php
require_once('../../lib/tcpdf.php'); // Sesuaikan dengan lokasi TCPDF

$host = 'localhost';
$dbname = 'kos_management';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Ambil data pembayaran
    $sql = "SELECT A.*, 
                   B.nama AS nama_penghuni, 
                   B.nomor_telepon AS penghuni_nomor_telepon,
                   C.name AS nomor_kamar
            FROM pembayaran A 
            INNER JOIN penghuni B ON A.penghuni_id = B.id
            INNER JOIN rooms C on B.room_id = C.id
            ORDER BY tanggal_bayar DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $bayar = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Buat instance TCPDF
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('KosKozie');
$pdf->SetTitle('Laporan Pembayaran');

// Hapus header dan footer default
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Tambahkan halaman
$pdf->AddPage();

// Judul
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(190, 10, 'Laporan Pembayaran KosKozie', 0, 1, 'C');

// Garis bawah judul
$pdf->SetLineWidth(0.3);
$pdf->Line(10, 25, 200, 25);
$pdf->Ln(10);

// Header tabel
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(40, 7, 'Nama Penyewa', 1);
$pdf->Cell(30, 7, 'Nomor Kamar', 1);
$pdf->Cell(30, 7, 'Telepon', 1);
$pdf->Cell(30, 7, 'Nominal', 1);
$pdf->Cell(30, 7, 'Tanggal', 1);
$pdf->Cell(30, 7, 'Status', 1);
$pdf->Ln();

// Isi tabel
$pdf->SetFont('helvetica', '', 10);
foreach ($bayar as $row) {
    $pdf->Cell(40, 7, $row['nama_penghuni'], 1);
    $pdf->Cell(30, 7, $row['nomor_kamar'], 1);
    $pdf->Cell(30, 7, $row['penghuni_nomor_telepon'], 1);
    $pdf->Cell(30, 7, 'Rp ' . number_format($row['jumlah'], 0, ',', '.'), 1);
    $pdf->Cell(30, 7, date('d M Y', strtotime($row['tanggal_bayar'])), 1);
    $pdf->Cell(30, 7, $row['status'], 1);
    $pdf->Ln();
}

ob_end_clean();
$pdf->Output('laporan_pembayaran.pdf', 'I');


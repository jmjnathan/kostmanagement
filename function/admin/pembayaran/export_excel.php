<?php
require '../../../lib/phpexcel.php'; // Pastikan path benar

// Konfigurasi koneksi database
$host = 'localhost';
$dbname = 'kos_management';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Query data pembayaran
    $sql = "SELECT 
            A.*, 
            B.nama AS nama_penghuni, 
            B.nomor_telepon AS penghuni_nomor_telepon,
            C.name AS nomor_kamar
        FROM pembayaran A
        INNER JOIN penghuni B ON A.penghuni_id = B.id
        INNER JOIN rooms C ON B.room_id = C.id
        ORDER BY tanggal_bayar DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $bayar = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Buat objek PHPExcel
$excel = new PHPExcel();
$excel->setActiveSheetIndex(0);
$sheet = $excel->getActiveSheet();

// Set header kolom
$headers = ['No', 'Nama Penyewa', 'Nomor Kamar', 'Nomor Telepon', 'Status', 'Nominal', 'Metode', 'Tanggal', 'Keterangan'];
$col = 'A';
foreach ($headers as $header) {
    $sheet->setCellValue($col . '1', $header);
    $col++;
}

// Masukkan data ke dalam tabel
$row = 2;
$no = 1;
foreach ($bayar as $data) {
    $sheet->setCellValue('A' . $row, $no++);
    $sheet->setCellValue('B' . $row, $data['nama_penghuni']);
    $sheet->setCellValue('C' . $row, $data['nomor_kamar']);
    $sheet->setCellValue('D' . $row, $data['penghuni_nomor_telepon']);
    $sheet->setCellValue('E' . $row, $data['status']);
    $sheet->setCellValue('F' . $row, 'Rp ' . number_format($data['jumlah'], 0, ',', '.'));
    $sheet->setCellValue('G' . $row, $data['metode']);
    $sheet->setCellValue('H' . $row, date('d M Y', strtotime($data['created_at'])));
    $sheet->setCellValue('I' . $row, $data['keterangan']);
    $row++;
}

// Set header untuk download file Excel
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="Laporan_Pembayaran.xlsx"');
header('Cache-Control: max-age=0');

$writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
$writer->save('php://output');
exit();

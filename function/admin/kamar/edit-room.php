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

// Koneksi ke database
$host = 'localhost';
$dbname = 'kos_management';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Ambil & bersihkan data dari form
        $room_id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT); // Pastikan 'id' sesuai dengan input form
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
        $room_type = filter_input(INPUT_POST, 'room_type', FILTER_SANITIZE_STRING);
        $ac = filter_input(INPUT_POST, 'ac', FILTER_SANITIZE_STRING);
        $capacity = filter_input(INPUT_POST, 'capacity', FILTER_VALIDATE_INT);
        $price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);
        $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);
        $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);

        // Pastikan room_id valid
        if (!$room_id) {
            $_SESSION['error_message'] = "Error: ID kamar tidak valid!";
            header("Location: ../../../page/admin/kamar.php");
            exit();
        }

        // Cek apakah room_id ada di database
        $check_stmt = $pdo->prepare("SELECT id FROM rooms WHERE id = :room_id");
        $check_stmt->execute(['room_id' => $room_id]);
        if ($check_stmt->rowCount() === 0) {
            $_SESSION['error_message'] = "Error: Kamar tidak ditemukan!";
            header("Location: ../../../page/admin/kamar.php");
            exit();
        }

        // UPDATE DATA KAMAR
        $stmt = $pdo->prepare("UPDATE rooms SET 
            name = :name, 
            type = :type, 
            ac = :ac, 
            capacity = :capacity, 
            price = :price, 
            status = :status, 
            description = :description,
            updated_at = NOW() 
            WHERE id = :room_id");

        $stmt->execute([
            'name' => $name,
            'type' => $room_type,
            'ac' => $ac,
            'capacity' => $capacity,
            'price' => $price,
            'status' => $status,
            'description' => $description,
            'room_id' => $room_id
        ]);

        // Periksa apakah data berhasil diperbarui
        if ($stmt->rowCount() > 0) {
            $_SESSION['toast_message'] = "Data kamar berhasil diperbarui!";
        } else {
            $_SESSION['error_message'] = "Tidak ada perubahan data!";
        }

        // Redirect ke halaman kamar
        header("Location: ../../../page/admin/kamar.php");
        exit();
    }

} catch (PDOException $e) {
    $_SESSION['error_message'] = "Error: " . $e->getMessage();
    header("Location: ../../../page/admin/kamar.php");
    exit();
}
?>
<?php
session_start();

// Cek apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    header('Location: index.php'); // Redirect jika belum login
    exit();
}

// Cek role pengguna, hanya admin yang boleh mengakses
if ($_SESSION['role'] !== 'admin') {
    header('Location: dashboard-user.php'); // Redirect jika bukan admin
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
        // Validasi input agar tidak kosong
        $nama  = !empty($_POST['nama']) ? trim($_POST['nama']) : null;
        $jenis_kelamin  = !empty($_POST['jenis_kelamin']) ? $_POST['jenis_kelamin'] : null;
        $ktp            = !empty($_POST['ktp']) ? $_POST['ktp'] : null;
        $alamat_asal    = !empty($_POST['alamat_asal']) ? $_POST['alamat_asal'] : null;
        $nomor_telepon  = !empty($_POST['nomor_telepon']) ? $_POST['nomor_telepon'] : null;
        $nomor_darurat  = !empty($_POST['nomor_darurat']) ? $_POST['nomor_darurat'] : null;
        $room_id        = !empty($_POST['room_id']) ? $_POST['room_id'] : null;
        $status         = !empty($_POST['status']) ? $_POST['status'] : null;
        $tanggal_masuk  = !empty($_POST['tanggal_masuk']) ? $_POST['tanggal_masuk'] : null;
        $username       = !empty($_POST['username']) ? $_POST['username'] : null;
        $password       = !empty($_POST['password']) ? $_POST['password'] : null;

        // Jika ada data yang kosong, beri pesan error
        if (!$nama || !$jenis_kelamin || !$ktp || !$alamat_asal || !$nomor_telepon || !$room_id || !$status || !$tanggal_masuk || !$username || !$password) {
            $_SESSION['toast_message'] = "Semua field wajib diisi!";
            header('Location: ../../../page/admin/penghuni.php');
            exit();
        }

        // Hash password sebelum disimpan
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Mulai transaksi
        $pdo->beginTransaction();

        try {
            // Insert data penghuni
            $stmt = $pdo->prepare("INSERT INTO penghuni 
                (nama, jenis_kelamin, ktp, alamat_asal, nomor_telepon, nomor_darurat, room_id, status, tanggal_masuk, username, password) 
                VALUES (:nama, :jenis_kelamin, :ktp, :alamat_asal, :nomor_telepon, :nomor_darurat, :room_id, :status, :tanggal_masuk, :username, :password)");
            $stmt->execute([
                'nama' => $nama,
                'jenis_kelamin' => $jenis_kelamin,
                'ktp' => $ktp,
                'alamat_asal' => $alamat_asal,
                'nomor_telepon' => $nomor_telepon,
                'nomor_darurat' => $nomor_darurat,
                'room_id' => $room_id,
                'status' => $status,
                'tanggal_masuk' => $tanggal_masuk,
                'username' => $username,
                'password' => $hashed_password
            ]);

            $penghuni_id = $pdo->lastInsertId();

            $user_stmt = $pdo->prepare("INSERT INTO users (id_penghuni, name,  username, password_hash, role) 
                            VALUES (:id_penghuni, :name, :username, :password, 'user')");
            
            $user_stmt->execute([
                'id_penghuni' => $penghuni_id, 
                'name' => $nama, 
                'username' => $username,
                'password' => $hashed_password
            ]);

            // Update status kamar menjadi "terisi" (3)
            $update_stmt = $pdo->prepare("UPDATE rooms SET status = '3' WHERE id = :room_id");
            $update_stmt->execute(['room_id' => $room_id]);

            // Commit transaksi jika semua berhasil
            $pdo->commit();

            $_SESSION['toast_message'] = "Data penghuni berhasil ditambahkan!";
            header('Location: ../../../page/admin/penghuni.php');
            exit();
        } catch (Exception $e) {
            // Rollback jika terjadi error
            $pdo->rollback();
            echo "Error: " . $e->getMessage();
        }
    }
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
    exit();
}
?>

<?php
session_start();

// Cek apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit();
}

// Cek role pengguna, hanya admin yang boleh mengakses
if ($_SESSION['role'] !== 'admin') {
    header('Location: dashboard-user.php');
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
        // Debugging: Periksa data yang dikirim
        // var_dump($_POST); exit(); // Hapus ini setelah debugging selesai

        // Validasi input
        $id             = !empty($_POST['id']) ? $_POST['id'] : null;
        $nama           = !empty($_POST['nama']) ? trim($_POST['nama']) : null;
        $ktp            = !empty($_POST['ktp']) ? $_POST['ktp'] : null;
        $alamat_asal    = !empty($_POST['alamat_asal']) ? $_POST['alamat_asal'] : null;
        $nomor_telepon  = !empty($_POST['nomor_telepon']) ? $_POST['nomor_telepon'] : null;
        $nomor_darurat  = !empty($_POST['nomor_darurat']) ? $_POST['nomor_darurat'] : null;
        $room_id        = !empty($_POST['room_id']) ? $_POST['room_id'] : null;
        $status         = !empty($_POST['status']) ? $_POST['status'] : null;
        $tanggal_masuk  = !empty($_POST['tanggal_masuk']) ? $_POST['tanggal_masuk'] : null;
        $username       = !empty($_POST['username']) ? $_POST['username'] : null;
        $password       = !empty($_POST['password']) ? $_POST['password'] : null;

        // Jika ada data yang kosong, tampilkan error
      //   if (!$id || !$nama || !$jenis_kelamin || !$ktp || !$alamat_asal || !$nomor_telepon || !$room_id || !$status || !$tanggal_masuk || !$username) {
      //       $_SESSION['toast_message'] = "Semua field wajib diisi!";
      //       header('Location: ../../../page/admin/penghuni.php');
      //       exit();
      //   }

        // Mulai transaksi database
        $pdo->beginTransaction();

        try {
            // Ambil data penghuni lama
            $stmt_old = $pdo->prepare("SELECT room_id FROM penghuni WHERE id = :id");
            $stmt_old->execute(['id' => $id]);
            $old_data = $stmt_old->fetch(PDO::FETCH_ASSOC);
            $old_room_id = $old_data['room_id'];

            // Jika ada password baru, hash sebelum menyimpan
            $hashed_password = $password ? password_hash($password, PASSWORD_DEFAULT) : null;

            // Update data penghuni
            $sql = "UPDATE penghuni SET 
                        nama = :nama, 
                        ktp = :ktp, 
                        alamat_asal = :alamat_asal, 
                        nomor_telepon = :nomor_telepon, 
                        nomor_darurat = :nomor_darurat, 
                        room_id = :room_id, 
                        status = :status, 
                        tanggal_masuk = :tanggal_masuk, 
                        username = :username";
            
            // Tambahkan update password jika diisi
            if ($hashed_password) {
                $sql .= ", password = :password";
            }
            
            $sql .= " WHERE id = :id";

            $stmt = $pdo->prepare($sql);
            $params = [
                'id'            => $id,
                'nama'          => $nama,
                'ktp'           => $ktp,
                'alamat_asal'   => $alamat_asal,
                'nomor_telepon' => $nomor_telepon,
                'nomor_darurat' => $nomor_darurat,
                'room_id'       => $room_id,
                'status'        => $status,
                'tanggal_masuk' => $tanggal_masuk,
                'username'      => $username
            ];

            if ($hashed_password) {
                $params['password'] = $hashed_password;
            }

            $stmt->execute($params);

            // Jika kamar berubah, update status kamar lama dan baru
            if ($old_room_id !== $room_id) {
                // Set kamar lama menjadi "kosong"
                $update_old_room = $pdo->prepare("UPDATE rooms SET status = '0' WHERE id = :old_room_id");
                $update_old_room->execute(['old_room_id' => $old_room_id]);

                // Set kamar baru menjadi "terisi"
                $update_new_room = $pdo->prepare("UPDATE rooms SET status = '1' WHERE id = :room_id");
                $update_new_room->execute(['room_id' => $room_id]);
            }

            // Commit transaksi jika semua berhasil
            $pdo->commit();

            $_SESSION['toast_message'] = "Data penghuni berhasil diperbarui!";
            header('Location: ../../../page/admin/penghuni.php');
            exit();
        } catch (Exception $e) {
            // Rollback jika terjadi kesalahan
            $pdo->rollback();
            echo "Error: " . $e->getMessage();
        }
    }
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
    exit();
}
?>

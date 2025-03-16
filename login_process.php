<?php
session_start();
include 'db.php'; // Menghubungkan ke database

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Gunakan prepared statement untuk keamanan
    $sql = "SELECT id, username, id_penghuni, role, password_hash FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password_hash'])) {
            // Simpan ke session
            $_SESSION['id_penghuni'] = $user['id_penghuni'];  // Gunakan 'id' (bukan 'user_id')
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Redirect sesuai role
            if ($user['role'] == 'user') {
                header("Location: page/users/dashboard-users.php");
            } else {
                header("Location: index.php?error=" . urlencode("Anda tidak memiliki akses sebagai user!"));
            }
            exit();
        } else {
            header("Location: index.php?error=" . urlencode("Username atau password salah!"));
            exit();
        }
    } else {
        header("Location: index.php?error=" . urlencode("Username atau password salah!"));
        exit();
    }
}
?>

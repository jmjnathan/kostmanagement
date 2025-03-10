<?php
session_start();
include 'db.php'; // Menghubungkan ke database

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
   // Mengambil input dari form
   $username = mysqli_real_escape_string($conn, $_POST['username']);
   $password = $_POST['password'];

   // Query untuk cek user di database
   $sql = "SELECT * FROM users WHERE username = '$username'";
   $result = $conn->query($sql);

   if ($result === false) {
      die("Query error: " . $conn->error);
   }

   if ($result->num_rows > 0) {
      $user = $result->fetch_assoc();
  
      if (password_verify($password, $user['password_hash'])) {
          $_SESSION['user_id'] = $user['user_id'];
          $_SESSION['username'] = $user['username'];
          $_SESSION['role'] = $user['role'];
  
          if ($user['role'] == 'user') {
              header("Location: page/users/dashboard-users.php");
          } else {
              // Jika role bukan 'user', tampilkan alert error
              header("Location: index.php?error=" . urlencode("Anda tidak memiliki akses sebagai user!"));
          }
          exit();
      } else {
          // Jika password salah, redirect dengan error di URL
          header("Location: index.php?error=" . urlencode("Username atau password salah!"));
          exit();
      }
  } else {
      // Jika username tidak ditemukan
      header("Location: index.php?error=" . urlencode("Username atau password salah!"));
      exit();
  }
}
?>

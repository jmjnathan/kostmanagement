   <?php
   session_start();
   include 'db.php'; // Menghubungkan ke konfigurasi database

   if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      // Mengambil dan membersihkan input dari form
      $username = mysqli_real_escape_string($conn, $_POST['username']);
      $password = $_POST['password']; // Password tidak perlu di-escape

      // Query untuk memeriksa pengguna
      $sql = "SELECT * FROM users WHERE username = '$username'";
      $result = $conn->query($sql);

      // Debug: Cek apakah query berhasil dan ada hasil
      if ($result === false) {
         die("Query error: " . $conn->error);
      }

      if ($result->num_rows > 0) {
         $user = $result->fetch_assoc();

         // Debug: Tampilkan username yang dicari
         echo "Username: " . $username . "<br>";

         // Verifikasi password tanpa hashing
         if ($password === $user['password_hash']) { // Menggunakan password_hash sebagai password plaintext
               // Set session untuk pengguna
               $_SESSION['user_id'] = $user['user_id'];
               $_SESSION['username'] = $user['username'];
               $_SESSION['role'] = $user['role'];

               // Redirect berdasarkan peran pengguna
               if ($user['role'] == 'admin') {
                  header("Location: page/admin/dashboard-admin.php");
            } elseif ($user['role'] == 'superadmin') {
                  header("Location: page/super-admin/dashboard-super-admin.php");
            } else {
                  header("Location: page/users/dashboard-users.php");
            }
            exit();           
         } else {
               // Tampilkan pesan kesalahan
               $error = "Password salah untuk username: " . $username;
               header("Location: index.html?error=" . urlencode($error));
         }
      } else {
         $error = "Username tidak ditemukan!";
         header("Location: index.html?error=" . urlencode($error));
      }
   }
   ?>

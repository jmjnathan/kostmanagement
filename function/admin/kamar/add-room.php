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

   $host = 'localhost';
   $dbname = 'kos_management';
   $username = 'root';
   $password = '';

   try {
      $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      if ($_SERVER['REQUEST_METHOD'] === 'POST') {
         // Ambil data dari form
         $name = $_POST['name'];
         $room_type = $_POST['room_type'];
         $ac = $_POST['ac'];
         $capacity = $_POST['capacity'];
         $price = $_POST['price'];
         $status = $_POST['status'];
         $description = $_POST['description'];
         
         // Query untuk menambahkan kamar baru ke dalam database
         $stmt = $pdo->prepare("INSERT INTO rooms (name, type, ac, capacity, price, status, description) 
                                 VALUES (:name, :type, :ac, :capacity, :price, :status,:description)");
         $stmt->execute([
               'name' => $name,
               'type' => $room_type,
               'ac' => $ac,
               'capacity' => $capacity,
               'price' => $price,
               'status' => $status,
               'description' => $description
         ]);

         // Redirect ke halaman kamar setelah berhasil menambahkan
         header('Location: ../../../page/admin/kamar.php');
         exit();
      }

   } catch (PDOException $e) {
      echo 'Connection failed: ' . $e->getMessage();
      exit();
   }
   ?>

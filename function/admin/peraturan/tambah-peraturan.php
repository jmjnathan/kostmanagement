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
         $isi_peraturan = $_POST['isi_peraturan'];
         
         // Query untuk menambahkan kamar baru ke dalam database
         $stmt = $pdo->prepare("INSERT INTO peraturan_kos (isi_peraturan) 
                                 VALUES (:isi_peraturan)");
         $stmt->execute([
               'isi_peraturan' => $isi_peraturan,
         ]);

         $_SESSION['toast_message'] = "Data berhasil ditambahkan!";
         header('Location: ../../../page/admin/peraturan.php');
         exit();

         // Redirect ke halaman kamar setelah berhasil menambahkan
         header('Location: ../../../page/admin/peraturan.php');
         exit();
      }

   } catch (PDOException $e) {
      echo 'Connection failed: ' . $e->getMessage();
      exit();
   }
   ?>

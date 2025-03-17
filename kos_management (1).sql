-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 16, 2025 at 08:04 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kos_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `kritik_dan_saran`
--

CREATE TABLE `kritik_dan_saran` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `nama_pengirim` varchar(100) DEFAULT NULL,
  `judul` varchar(255) NOT NULL,
  `kategori` enum('Kritik','Saran') NOT NULL,
  `status` varchar(225) DEFAULT NULL,
  `tanggal_kirim` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kritik_dan_saran`
--

INSERT INTO `kritik_dan_saran` (`id`, `user_id`, `nama_pengirim`, `judul`, `kategori`, `status`, `tanggal_kirim`) VALUES
(1, NULL, 'Maria Dyan', 'kritik harga', 'Kritik', 'Selesai', '2024-11-23 11:17:43');

-- --------------------------------------------------------

--
-- Table structure for table `maintenance`
--

CREATE TABLE `maintenance` (
  `id` int(11) NOT NULL,
  `id_penghuni` int(11) NOT NULL,
  `id_kamar` int(11) NOT NULL,
  `tanggal_pengajuan` date NOT NULL,
  `deskripsi` text NOT NULL,
  `kategori` varchar(50) NOT NULL,
  `status` enum('pending','in_progress','completed','canceled') NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `maintenance`
--

INSERT INTO `maintenance` (`id`, `id_penghuni`, `id_kamar`, `tanggal_pengajuan`, `deskripsi`, `kategori`, `status`) VALUES
(1, 30, 32, '2025-03-07', 'Plafon kamar saya jebol', 'Perbaikan', 'completed');

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id` int(11) NOT NULL,
  `penghuni_id` int(11) NOT NULL,
  `jumlah` decimal(10,2) NOT NULL,
  `tanggal_bayar` date NOT NULL,
  `status` enum('pending','lunas','gagal') DEFAULT 'pending',
  `metode` enum('cash','transfer','qris') NOT NULL,
  `bukti_transfer` varchar(255) DEFAULT NULL,
  `keterangan` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pembayaran`
--

INSERT INTO `pembayaran` (`id`, `penghuni_id`, `jumlah`, `tanggal_bayar`, `status`, `metode`, `bukti_transfer`, `keterangan`, `created_at`, `updated_at`) VALUES
(3, 30, 950000.00, '2025-03-04', 'lunas', 'transfer', NULL, 'pembayaran uang kos bulan maret 2025', '2025-03-04 08:36:35', '2025-03-13 06:16:49'),
(4, 40, 1200000.00, '2025-03-13', 'lunas', 'transfer', NULL, 'Uang masuk kos bulan ini', '2025-03-13 06:20:09', '2025-03-13 06:20:09'),
(5, 27, 950000.00, '2025-03-16', 'lunas', 'cash', NULL, '', '2025-03-16 03:03:21', '2025-03-16 03:30:49');

-- --------------------------------------------------------

--
-- Table structure for table `pengajuan_keluar`
--

CREATE TABLE `pengajuan_keluar` (
  `id` int(11) NOT NULL,
  `penghuni_id` int(11) NOT NULL,
  `alasan` text DEFAULT NULL,
  `tanggal_pengajuan` date DEFAULT curdate(),
  `tanggal_rencana_keluar` date NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `note` text DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengajuan_keluar`
--

INSERT INTO `pengajuan_keluar` (`id`, `penghuni_id`, `alasan`, `tanggal_pengajuan`, `tanggal_rencana_keluar`, `status`, `note`, `approved_at`) VALUES
(2, 37, 'Pindah kos yang lebih murah', '2025-03-13', '2025-03-31', 'approved', 'ok', '2025-03-13 00:00:00'),
(3, 36, 'bosen', '2025-03-16', '2025-03-31', 'pending', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `penghuni`
--

CREATE TABLE `penghuni` (
  `id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `jenis_kelamin` varchar(10) NOT NULL,
  `nomor_telepon` varchar(50) DEFAULT NULL,
  `alamat_asal` text DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `tanggal_masuk` date DEFAULT NULL,
  `tanggal_keluar` date DEFAULT NULL,
  `ktp` varchar(50) DEFAULT NULL,
  `nomor_darurat` varchar(50) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `penghuni`
--

INSERT INTO `penghuni` (`id`, `room_id`, `nama`, `jenis_kelamin`, `nomor_telepon`, `alamat_asal`, `status`, `tanggal_masuk`, `tanggal_keluar`, `ktp`, `nomor_darurat`, `username`, `password`, `created_at`, `updated_at`) VALUES
(27, 33, 'Jonathan Christiawan', 'Laki-laki', '085799726802', 'Blora', 'active', '2025-03-04', NULL, '1234567890123456', '-', 'jonathan', '$2y$10$X8m.Q6US6ZWGWlzMZwM0EedoExJJ2vPWQJm.WRGo/RVenrAyXeEQ2', '2025-03-04 08:22:46', '2025-03-04 08:22:46'),
(30, 32, 'Maria Dyan', 'Perempuan', '081770253720', 'Riau', 'active', '2025-03-04', NULL, '1234567890123456', '-', 'mariadi', '$2y$10$etevnhBrbQVr.DnPsMSJvO8eZYl0e.avR3u61Z3bEdp3cuv3xEV4a', '2025-03-04 08:33:01', '2025-03-04 08:33:01'),
(31, 34, 'Rafael Yusia Adikusuma', 'Laki-laki', '085870258500', 'Ungaran', 'active', '2025-03-04', NULL, '1234567890123456', '-', 'rafaelyak', '$2y$10$maosEzwhC46lfSjhcNnpvOG3F91RzW4vyz4JppjdGjheDDcVSabjO', '2025-03-04 08:33:50', '2025-03-04 08:33:50'),
(32, 35, 'Aurellia Divosa', 'Perempuan', '082135818817', 'Semarang', 'active', '2025-03-04', NULL, '1234567890123456', '-', 'mposs', '$2y$10$jJ4Hsrsp2LvM7Zd0EXYaLepkWzQkBKZVfzSN1vrqYebQSmEveI.r.', '2025-03-04 08:35:10', '2025-03-04 08:35:10'),
(35, 36, 'Oktavius Theo', 'Laki-laki', '081383037979', 'Jakarta', 'active', '2025-03-07', NULL, '1234567890123456', '-', 'theyo', '$2y$10$oWZ2/6WXlphVWYGITldoI.vxZ9sLKFRjSH0a3eblAgHEc5dSRYqe.', '2025-03-07 09:11:17', '2025-03-07 09:11:17'),
(36, 37, 'Ferrey', 'Laki-laki', '08970086988', 'Semarang', 'active', '2025-03-07', NULL, '1234567890123456', '-', 'ferrey', '$2y$10$DaZDQ2NVU3OVBb4OPFSVhuGnjH/9Dc/46pb23sCAtC6HjpC3wI/hu', '2025-03-07 09:34:29', '2025-03-07 09:34:29'),
(37, 38, 'Yan', 'Perempuan', '081770253720', 'Jakarta', 'active', '2025-03-09', NULL, '1234567890', '-', 'yan', '$2y$10$DcBfLaHomRxhEPjm1NN.v.3FlnIJ4ogT7.hCvr2R2O0PSEAF9pizu', '2025-03-09 04:57:27', '2025-03-09 04:57:27'),
(39, 39, 'Vos', 'Perempuan', '081383037979', 'Semarang', 'active', '2025-02-09', NULL, '1234567890123456', '-', 'vos', '$2y$10$A6E0kIYrE5aXdBRLfMKGqes9nfKadi5ppiCT.abTzeuyFc6dBgAfe', '2025-03-09 07:32:35', '2025-03-13 05:42:27'),
(40, 40, 'Christiawan Jonathan', 'Laki-laki', '085799726902', 'Blora', 'active', '2025-03-13', NULL, '1234567890123456', '-', 'christ', '$2y$10$m1ngfWDqibOXG/DI1PWB7.nYnwdZ/Qx5mjNOOl5n22CJuF5YdtBQS', '2025-03-13 06:18:53', '2025-03-13 06:18:53'),
(41, 41, 'Raf', 'Laki-laki', '085870258500', 'Jakarta', 'active', '2025-03-16', NULL, 'undefined', '-', 'raf', '$2y$10$PAXh411xX1bagnskIgzWa.tcMr7FxYSP1aA5ziTPGr5JFvVmEk7bm', '2025-03-16 05:51:29', '2025-03-16 05:51:29'),
(42, 42, 'Rafael Yusia Adikusumo', 'Perempuan', '085870258500', 'Jakarta', 'active', '2025-03-16', NULL, '1234567890123456', '-', 'rapa', '$2y$10$wksQu5qgslT0ziER.mvk2ehA3I56hHxrEh6OjXOCgIi7yjm8nbLGS', '2025-03-16 06:53:24', '2025-03-16 06:53:24');

-- --------------------------------------------------------

--
-- Table structure for table `peraturan_kos`
--

CREATE TABLE `peraturan_kos` (
  `id` int(11) NOT NULL,
  `isi_peraturan` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `peraturan_kos`
--

INSERT INTO `peraturan_kos` (`id`, `isi_peraturan`, `created_at`) VALUES
(1, 'Dilarang membawa tamu ke dalam kamar kos tanpa izin.\r\n', '2025-03-16 03:56:11'),
(5, 'Dilarang membawa tamu ke dalam kamar kos tanpa izin.', '2025-03-16 05:42:56'),
(6, 'Wajib menjaga kebersihan kamar dan lingkungan kos.', '2025-03-16 05:42:56'),
(7, 'Dilarang merokok dan membawa minuman keras di dalam area kos.', '2025-03-16 05:42:56'),
(8, 'Pembayaran kos harus dilakukan setiap tanggal masuk setiap bulan.', '2025-03-16 05:42:56'),
(9, 'Jam malam berlaku pukul 22.00, harap menjaga ketenangan.', '2025-03-16 05:42:56'),
(10, 'Laporkan segera jika ada kerusakan fasilitas kepada pengelola.', '2025-03-16 05:42:56'),
(11, 'Waktu kunjungan tamu hanya diperbolehkan hingga pukul 22:00.', '2025-03-16 05:42:56'),
(12, 'Dilarang memodifikasi kamar tanpa izin pemilik kos.', '2025-03-16 05:42:56'),
(13, 'Hewan peliharaan tidak diperbolehkan kecuali mendapat izin khusus.', '2025-03-16 05:42:56'),
(14, 'Parkir kendaraan harus sesuai dengan tempat yang telah disediakan.', '2025-03-16 05:42:56'),
(15, 'Setiap penghuni bertanggung jawab atas keamanan barang pribadi masing-masing.', '2025-03-16 05:42:56'),
(16, 'Jika ingin keluar dari kos, wajib memberikan pemberitahuan minimal satu bulan sebelumnya.', '2025-03-16 05:42:56');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(50) NOT NULL,
  `ac` varchar(225) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `status` varchar(225) DEFAULT NULL,
  `active` varchar(20) NOT NULL,
  `capacity` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `name`, `type`, `ac`, `price`, `status`, `active`, `capacity`, `description`, `created_at`, `updated_at`) VALUES
(32, 'A-001', 'km_luar', 'AC', 950000.00, '3', 'active', 1, '', '2025-03-04 08:04:52', '2025-03-12 07:35:52'),
(33, 'A-002', 'km_luar', 'AC', 950000.00, '3', 'active', 1, '', '2025-03-04 08:05:09', '2025-03-12 07:35:55'),
(34, 'A-003', 'km_luar', 'AC', 950000.00, '3', 'active', 1, '', '2025-03-04 08:05:24', '2025-03-12 07:35:56'),
(35, 'A-004', 'km_luar', 'AC', 950000.00, '3', 'active', 1, '', '2025-03-04 08:05:36', '2025-03-12 07:35:58'),
(36, 'A-005', 'km_dalam', 'AC', 1200000.00, '3', 'active', 1, 'tidak termasuk listrik', '2025-03-07 05:22:27', '2025-03-12 07:36:00'),
(37, 'A-006', 'km_dalam', 'AC', 1200000.00, '3', 'active', 1, 'token sendiri', '2025-03-07 05:23:06', '2025-03-12 07:36:01'),
(38, 'A-007', 'km_dalam', 'AC', 1200000.00, '3', 'active', 1, '', '2025-03-09 04:54:55', '2025-03-12 07:36:02'),
(39, 'A-008', 'km_luar', 'Non-Ac', 750000.00, '3', 'active', 1, '', '2025-03-09 07:12:03', '2025-03-12 07:36:04'),
(40, 'A-009', 'km_dalam', 'AC', 1200000.00, '3', 'active', 1, '', '2025-03-12 08:05:48', '2025-03-13 06:18:53'),
(41, 'A-010', 'km_dalam', 'AC', 1200000.00, '3', 'active', 1, '', '2025-03-13 06:14:45', '2025-03-16 05:51:29'),
(42, 'B-001', 'km_dalam', 'AC', 1200000.00, '3', 'active', 1, '', '2025-03-16 06:19:09', '2025-03-16 06:53:24');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `id_penghuni` int(20) NOT NULL,
  `name` varchar(225) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('admin','superadmin','user') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `id_penghuni`, `name`, `password_hash`, `role`, `created_at`, `updated_at`) VALUES
(1, 'admin', -99, 'Mbak Kos', '$2y$10$vv3cvg8UQw99BMwBl.q/3OUjGBAdKz/IgW8iV7eHoy7Z4ZMkcsZE6', 'admin', '2024-10-27 05:04:39', '2025-03-07 08:03:44'),
(2, 'superadmin', -99, 'Haha', '$2y$10$32xKcSg7Zh9ERGfKH02yTO0RAZCx7k6v1HykapniydGRHpJTl8Kl6', 'superadmin', '2024-11-20 06:56:13', '2025-03-07 08:03:51'),
(4, 'jonathan', 27, 'Jonathan Christiawan', '$2y$10$X8m.Q6US6ZWGWlzMZwM0EedoExJJ2vPWQJm.WRGo/RVenrAyXeEQ2', 'user', '2025-03-04 08:22:46', '2025-03-07 08:03:34'),
(6, 'mariadi', 30, 'Maria Dyan', '$2y$10$etevnhBrbQVr.DnPsMSJvO8eZYl0e.avR3u61Z3bEdp3cuv3xEV4a', 'user', '2025-03-04 08:33:01', '2025-03-07 08:04:10'),
(7, 'rafaelyak', 31, 'Rafael Yusia Adikusuma', '$2y$10$maosEzwhC46lfSjhcNnpvOG3F91RzW4vyz4JppjdGjheDDcVSabjO', 'user', '2025-03-04 08:33:50', '2025-03-07 08:04:20'),
(8, 'mposs', 32, 'Aurellia Divosa', '$2y$10$jJ4Hsrsp2LvM7Zd0EXYaLepkWzQkBKZVfzSN1vrqYebQSmEveI.r.', 'user', '2025-03-04 08:35:10', '2025-03-07 08:04:30'),
(9, 'theyo', 35, 'Oktavius Theo', '$2y$10$oWZ2/6WXlphVWYGITldoI.vxZ9sLKFRjSH0a3eblAgHEc5dSRYqe.', 'user', '2025-03-07 09:11:17', '2025-03-07 09:11:17'),
(10, 'ferrey', 36, 'Ferrey', '$2y$10$DaZDQ2NVU3OVBb4OPFSVhuGnjH/9Dc/46pb23sCAtC6HjpC3wI/hu', 'user', '2025-03-07 09:34:29', '2025-03-07 09:34:29'),
(11, 'yan', 37, 'Yan', '$2y$10$DcBfLaHomRxhEPjm1NN.v.3FlnIJ4ogT7.hCvr2R2O0PSEAF9pizu', 'user', '2025-03-09 04:57:27', '2025-03-09 04:57:27'),
(12, 'diana', 38, 'Diana', '$2y$10$gkY91gEaerP1nPRrG83VKebdSvs.jr8py5aOzYnFwGlAuFvBRaYUy', 'user', '2025-03-09 07:12:36', '2025-03-09 07:12:36'),
(13, 'vos', 39, 'Vos', '$2y$10$A6E0kIYrE5aXdBRLfMKGqes9nfKadi5ppiCT.abTzeuyFc6dBgAfe', 'user', '2025-03-09 07:32:35', '2025-03-09 07:32:35'),
(14, 'christ', 40, 'Christiawan Jonathan', '$2y$10$m1ngfWDqibOXG/DI1PWB7.nYnwdZ/Qx5mjNOOl5n22CJuF5YdtBQS', 'user', '2025-03-13 06:18:53', '2025-03-13 06:18:53'),
(15, 'raf', 41, 'Raf', '$2y$10$PAXh411xX1bagnskIgzWa.tcMr7FxYSP1aA5ziTPGr5JFvVmEk7bm', 'user', '2025-03-16 05:51:29', '2025-03-16 05:51:29'),
(16, 'rapa', 42, 'Rafael Yusia Adikusumo', '$2y$10$wksQu5qgslT0ziER.mvk2ehA3I56hHxrEh6OjXOCgIi7yjm8nbLGS', 'user', '2025-03-16 06:53:24', '2025-03-16 06:53:24');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `kritik_dan_saran`
--
ALTER TABLE `kritik_dan_saran`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `maintenance`
--
ALTER TABLE `maintenance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_penghuni` (`id_penghuni`),
  ADD KEY `id_kamar` (`id_kamar`);

--
-- Indexes for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id`),
  ADD KEY `penghuni_id` (`penghuni_id`);

--
-- Indexes for table `pengajuan_keluar`
--
ALTER TABLE `pengajuan_keluar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `penghuni_id` (`penghuni_id`);

--
-- Indexes for table `penghuni`
--
ALTER TABLE `penghuni`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `peraturan_kos`
--
ALTER TABLE `peraturan_kos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `kritik_dan_saran`
--
ALTER TABLE `kritik_dan_saran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `maintenance`
--
ALTER TABLE `maintenance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `pengajuan_keluar`
--
ALTER TABLE `pengajuan_keluar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `penghuni`
--
ALTER TABLE `penghuni`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `peraturan_kos`
--
ALTER TABLE `peraturan_kos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `kritik_dan_saran`
--
ALTER TABLE `kritik_dan_saran`
  ADD CONSTRAINT `kritik_dan_saran_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `maintenance`
--
ALTER TABLE `maintenance`
  ADD CONSTRAINT `maintenance_ibfk_1` FOREIGN KEY (`id_penghuni`) REFERENCES `penghuni` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `maintenance_ibfk_2` FOREIGN KEY (`id_kamar`) REFERENCES `rooms` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`penghuni_id`) REFERENCES `penghuni` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pengajuan_keluar`
--
ALTER TABLE `pengajuan_keluar`
  ADD CONSTRAINT `pengajuan_keluar_ibfk_1` FOREIGN KEY (`penghuni_id`) REFERENCES `penghuni` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `penghuni`
--
ALTER TABLE `penghuni`
  ADD CONSTRAINT `penghuni_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

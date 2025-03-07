-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 07, 2025 at 07:25 AM
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
(1, 30, 32, '2025-03-07', 'Plafon kamar saya jebol', 'Perbaikan', 'pending');

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pembayaran`
--

INSERT INTO `pembayaran` (`id`, `penghuni_id`, `jumlah`, `tanggal_bayar`, `status`, `metode`, `bukti_transfer`, `created_at`, `updated_at`) VALUES
(3, 30, 950000.00, '2025-03-04', 'lunas', 'transfer', NULL, '2025-03-04 08:36:35', '2025-03-04 08:36:35');

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
(32, 35, 'Aurellia Divosa', 'Perempuan', '082135818817', 'Semarang', 'active', '2025-03-04', NULL, '1234567890123456', '-', 'mposs', '$2y$10$jJ4Hsrsp2LvM7Zd0EXYaLepkWzQkBKZVfzSN1vrqYebQSmEveI.r.', '2025-03-04 08:35:10', '2025-03-04 08:35:10');

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
  `capacity` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `name`, `type`, `ac`, `price`, `status`, `capacity`, `description`, `created_at`, `updated_at`) VALUES
(32, 'A-001', 'km_luar', 'AC', 950000.00, '3', 1, '', '2025-03-04 08:04:52', '2025-03-04 08:33:01'),
(33, 'A-002', 'km_luar', 'AC', 950000.00, '3', 1, '', '2025-03-04 08:05:09', '2025-03-04 08:22:46'),
(34, 'A-003', 'km_luar', 'AC', 950000.00, '3', 1, '', '2025-03-04 08:05:24', '2025-03-04 08:33:50'),
(35, 'A-004', 'km_luar', 'AC', 950000.00, '3', 1, '', '2025-03-04 08:05:36', '2025-03-04 08:35:10'),
(36, 'A-005', 'km_dalam', 'AC', 1200000.00, '1', 1, 'tidak termasuk listrik', '2025-03-07 05:22:27', '2025-03-07 05:22:27'),
(37, 'A-006', 'km_dalam', 'AC', 1200000.00, '1', 1, 'kamar tidak termasuk listrik', '2025-03-07 05:23:06', '2025-03-07 05:23:06');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `name` varchar(225) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('admin','superadmin','user') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `name`, `password_hash`, `role`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'Mbak Kos', '$2y$10$vv3cvg8UQw99BMwBl.q/3OUjGBAdKz/IgW8iV7eHoy7Z4ZMkcsZE6', 'admin', '2024-10-27 05:04:39', '2025-03-04 09:43:00'),
(2, 'superadmin', 'Haha', '$2y$10$32xKcSg7Zh9ERGfKH02yTO0RAZCx7k6v1HykapniydGRHpJTl8Kl6', 'superadmin', '2024-11-20 06:56:13', '2025-03-07 04:29:37'),
(4, 'jonathan', 'Jonathan Christiawan', '$2y$10$X8m.Q6US6ZWGWlzMZwM0EedoExJJ2vPWQJm.WRGo/RVenrAyXeEQ2', 'user', '2025-03-04 08:22:46', '2025-03-04 08:22:46'),
(6, 'mariadi', 'Maria Dyan', '$2y$10$etevnhBrbQVr.DnPsMSJvO8eZYl0e.avR3u61Z3bEdp3cuv3xEV4a', 'user', '2025-03-04 08:33:01', '2025-03-04 08:33:01'),
(7, 'rafaelyak', 'Rafael Yusia Adikusuma', '$2y$10$maosEzwhC46lfSjhcNnpvOG3F91RzW4vyz4JppjdGjheDDcVSabjO', 'user', '2025-03-04 08:33:50', '2025-03-04 08:33:50'),
(8, 'mposs', 'Aurellia Divosa', '$2y$10$jJ4Hsrsp2LvM7Zd0EXYaLepkWzQkBKZVfzSN1vrqYebQSmEveI.r.', 'user', '2025-03-04 08:35:10', '2025-03-04 08:35:10');

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
-- Indexes for table `penghuni`
--
ALTER TABLE `penghuni`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `room_id` (`room_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `penghuni`
--
ALTER TABLE `penghuni`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

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
-- Constraints for table `penghuni`
--
ALTER TABLE `penghuni`
  ADD CONSTRAINT `penghuni_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

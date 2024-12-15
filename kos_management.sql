-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 15, 2024 at 04:57 AM
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
-- Table structure for table `complaints`
--

CREATE TABLE `complaints` (
  `complaint_id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `description` text NOT NULL,
  `priority` enum('low','medium','high') DEFAULT 'low',
  `status` enum('open','in_progress','resolved','closed') DEFAULT 'open',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `complaint_updates`
--

CREATE TABLE `complaint_updates` (
  `update_id` int(11) NOT NULL,
  `complaint_id` int(11) NOT NULL,
  `update_text` text NOT NULL,
  `status` enum('open','in_progress','resolved','closed') NOT NULL,
  `updated_by` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `furniture`
--

CREATE TABLE `furniture` (
  `id_furniture` int(255) NOT NULL,
  `nama_furniture` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `furniture`
--

INSERT INTO `furniture` (`id_furniture`, `nama_furniture`, `created_at`, `updated_at`) VALUES
(1, 'Meja belajar', '2024-11-02 08:03:36', '2024-11-02 08:03:36');

-- --------------------------------------------------------

--
-- Table structure for table `kritik_dan_saran`
--

CREATE TABLE `kritik_dan_saran` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `nama_pengirim` varchar(100) DEFAULT NULL,
  `email_pengirim` varchar(150) DEFAULT NULL,
  `judul` varchar(255) NOT NULL,
  `isi_feedback` text DEFAULT NULL,
  `kategori` enum('Kritik','Saran') NOT NULL,
  `status` varchar(225) DEFAULT NULL,
  `tanggal_kirim` datetime DEFAULT current_timestamp(),
  `tanggal_feedback` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kritik_dan_saran`
--

INSERT INTO `kritik_dan_saran` (`id`, `user_id`, `nama_pengirim`, `email_pengirim`, `judul`, `isi_feedback`, `kategori`, `status`, `tanggal_kirim`, `tanggal_feedback`) VALUES
(1, 3, 'Maria Dyan', 'asd', 'kritik harga', 'setelah ini akan kami pertimbangkan lagi', 'Kritik', 'Selesai', '2024-11-23 11:17:43', '2024-12-08 14:08:58');

-- --------------------------------------------------------

--
-- Table structure for table `maintenance_requests`
--

CREATE TABLE `maintenance_requests` (
  `request_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `description` text NOT NULL,
  `status` enum('pending','in_progress','completed') DEFAULT 'pending',
  `requested_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `amount_due` decimal(10,2) NOT NULL,
  `amount_paid` decimal(10,2) DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `status` enum('paid','unpaid','late') DEFAULT 'unpaid',
  `due_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_history`
--

CREATE TABLE `payment_history` (
  `history_id` int(11) NOT NULL,
  `payment_id` int(11) NOT NULL,
  `transaction_date` date NOT NULL,
  `transaction_type` enum('manual','automatic') NOT NULL,
  `transaction_amount` decimal(10,2) NOT NULL,
  `status` enum('success','failed') NOT NULL,
  `method` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(1, 19, 'Maria Dyan', 'Perempuan', '08123456789', 'Unika', 'active', '2024-12-08', '2025-12-01', '1234567890987654', '085799726802', 'maria_dyan', 'mariadi', '2024-12-08 07:32:47', '2024-12-08 07:42:37'),
(2, 22, 'Rafael', 'Laki-Laki', '1234567890', 'Ungaran', 'active', '2024-10-01', '2024-12-31', '1234567890', '0987654321', 'rafael', 'rafael', '2024-12-08 09:13:50', '2024-12-08 09:32:23');

-- --------------------------------------------------------

--
-- Table structure for table `properties`
--

CREATE TABLE `properties` (
  `property_id` int(11) NOT NULL,
  `property_name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `properties`
--

INSERT INTO `properties` (`property_id`, `property_name`, `created_at`, `updated_at`) VALUES
(1, 'Meja', '2024-11-01 09:06:20', '2024-11-01 09:06:20');

-- --------------------------------------------------------

--
-- Table structure for table `room`
--

CREATE TABLE `room` (
  `room_id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL,
  `room_number` varchar(50) NOT NULL,
  `room_type` varchar(50) NOT NULL,
  `monthly_rent` decimal(10,2) NOT NULL,
  `status` enum('available','occupied','maintenance') NOT NULL DEFAULT 'available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room`
--

INSERT INTO `room` (`room_id`, `property_id`, `room_number`, `room_type`, `monthly_rent`, `status`, `created_at`) VALUES
(3, 1, 'A-01', 'AC', 1200000.00, 'available', '2024-11-01 09:07:25');

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
(19, 'A-001', 'km_dalam', 'Non-Ac', 750000.00, '1', 1, 'perabotan full set', '2024-11-27 07:19:44', '2024-11-27 07:46:25'),
(21, 'A-002', 'km_dalam', 'AC', 1200000.00, '3', 1, 'full set ', '2024-11-28 07:42:48', '2024-12-08 08:14:04'),
(22, 'A-003', 'km_dalam', 'AC', 1200000.00, '1', 1, 'Full perabotan tinggal nempatin', '2024-12-08 08:14:59', '2024-12-08 08:14:59');

-- --------------------------------------------------------

--
-- Table structure for table `tenants`
--

CREATE TABLE `tenants` (
  `tenant_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `lease_start` date NOT NULL,
  `lease_end` date DEFAULT NULL,
  `security_deposit` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `name` varchar(225) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('admin','superadmin','user') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `name`, `password_hash`, `email`, `role`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'Andi', 'admin', 'admin@gmail.com', 'admin', '2024-10-27 05:04:39', '2024-12-08 09:32:41'),
(2, 'superadmin', 'Haha', 'superadmin', 'email@email.com', 'superadmin', '2024-11-20 06:56:13', '2024-11-20 13:06:24'),
(3, 'mariadi', 'Maria Dyan', 'mariadi', 'mariadi@gmail.com', 'user', '2024-11-20 13:01:46', '2024-11-20 13:01:46');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `complaints`
--
ALTER TABLE `complaints`
  ADD PRIMARY KEY (`complaint_id`),
  ADD KEY `tenant_id` (`tenant_id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `complaint_updates`
--
ALTER TABLE `complaint_updates`
  ADD PRIMARY KEY (`update_id`),
  ADD KEY `complaint_id` (`complaint_id`),
  ADD KEY `updated_by` (`updated_by`);

--
-- Indexes for table `furniture`
--
ALTER TABLE `furniture`
  ADD PRIMARY KEY (`id_furniture`);

--
-- Indexes for table `kritik_dan_saran`
--
ALTER TABLE `kritik_dan_saran`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `maintenance_requests`
--
ALTER TABLE `maintenance_requests`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `room_id` (`room_id`),
  ADD KEY `requested_by` (`requested_by`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `tenant_id` (`tenant_id`);

--
-- Indexes for table `payment_history`
--
ALTER TABLE `payment_history`
  ADD PRIMARY KEY (`history_id`),
  ADD KEY `payment_id` (`payment_id`);

--
-- Indexes for table `penghuni`
--
ALTER TABLE `penghuni`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `properties`
--
ALTER TABLE `properties`
  ADD PRIMARY KEY (`property_id`);

--
-- Indexes for table `room`
--
ALTER TABLE `room`
  ADD PRIMARY KEY (`room_id`),
  ADD UNIQUE KEY `room_number` (`room_number`),
  ADD KEY `property_id` (`property_id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tenants`
--
ALTER TABLE `tenants`
  ADD PRIMARY KEY (`tenant_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `complaints`
--
ALTER TABLE `complaints`
  MODIFY `complaint_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `complaint_updates`
--
ALTER TABLE `complaint_updates`
  MODIFY `update_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `furniture`
--
ALTER TABLE `furniture`
  MODIFY `id_furniture` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `kritik_dan_saran`
--
ALTER TABLE `kritik_dan_saran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `maintenance_requests`
--
ALTER TABLE `maintenance_requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_history`
--
ALTER TABLE `payment_history`
  MODIFY `history_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `penghuni`
--
ALTER TABLE `penghuni`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `properties`
--
ALTER TABLE `properties`
  MODIFY `property_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `room`
--
ALTER TABLE `room`
  MODIFY `room_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `tenants`
--
ALTER TABLE `tenants`
  MODIFY `tenant_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `complaints`
--
ALTER TABLE `complaints`
  ADD CONSTRAINT `complaints_ibfk_1` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`tenant_id`),
  ADD CONSTRAINT `complaints_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `room` (`room_id`);

--
-- Constraints for table `complaint_updates`
--
ALTER TABLE `complaint_updates`
  ADD CONSTRAINT `complaint_updates_ibfk_1` FOREIGN KEY (`complaint_id`) REFERENCES `complaints` (`complaint_id`),
  ADD CONSTRAINT `complaint_updates_ibfk_2` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `kritik_dan_saran`
--
ALTER TABLE `kritik_dan_saran`
  ADD CONSTRAINT `kritik_dan_saran_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `maintenance_requests`
--
ALTER TABLE `maintenance_requests`
  ADD CONSTRAINT `maintenance_requests_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `room` (`room_id`),
  ADD CONSTRAINT `maintenance_requests_ibfk_2` FOREIGN KEY (`requested_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`tenant_id`);

--
-- Constraints for table `payment_history`
--
ALTER TABLE `payment_history`
  ADD CONSTRAINT `payment_history_ibfk_1` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`payment_id`);

--
-- Constraints for table `penghuni`
--
ALTER TABLE `penghuni`
  ADD CONSTRAINT `penghuni_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `room`
--
ALTER TABLE `room`
  ADD CONSTRAINT `room_ibfk_1` FOREIGN KEY (`property_id`) REFERENCES `properties` (`property_id`);

--
-- Constraints for table `tenants`
--
ALTER TABLE `tenants`
  ADD CONSTRAINT `tenants_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `tenants_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `room` (`room_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

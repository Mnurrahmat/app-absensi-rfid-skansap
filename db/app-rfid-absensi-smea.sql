-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 20, 2025 at 06:20 PM
-- Server version: 8.0.30
-- PHP Version: 8.3.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `app-rfid-absensi-smea`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int NOT NULL,
  `admin_username` varchar(30) NOT NULL,
  `admin_password` varchar(50) NOT NULL,
  `admin_nama` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `admin_username`, `admin_password`, `admin_nama`) VALUES
(1, 'admin', 'admin', 'Administrator');

-- --------------------------------------------------------

--
-- Table structure for table `jabatan`
--

CREATE TABLE `jabatan` (
  `jabatan_id` int NOT NULL,
  `jabatan_nama` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `jabatan`
--

INSERT INTO `jabatan` (`jabatan_id`, `jabatan_nama`) VALUES
(14, 'Kepala Perpustakaan'),
(17, 'Pegawai Honorer');

-- --------------------------------------------------------

--
-- Table structure for table `pegawai`
--

CREATE TABLE `pegawai` (
  `pegawai_id` int NOT NULL,
  `jabatan_id` int NOT NULL,
  `pegawai_rfid` varchar(30) NOT NULL,
  `pegawai_nama` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `pegawai_nip` varchar(18) NOT NULL,
  `pegawai_jeniskelamin` enum('M','F') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `pegawai_lahir` date NOT NULL,
  `pegawai_nomorhp` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `pegawai_alamat` varchar(500) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `pegawai_foto` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `pegawai_status` enum('1','0') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `pegawai`
--

INSERT INTO `pegawai` (`pegawai_id`, `jabatan_id`, `pegawai_rfid`, `pegawai_nama`, `pegawai_nip`, `pegawai_jeniskelamin`, `pegawai_lahir`, `pegawai_nomorhp`, `pegawai_alamat`, `pegawai_foto`, `pegawai_status`) VALUES
(21, 14, '9085FC20', 'Rahmat', '190019871012220001', 'M', '2000-01-09', '6289098890222', 'Jl. Kemerdeaan No.1', '20927237236883e0dfe6d66.jpeg', '1'),
(23, 17, '438FE6B6', 'Mat', '190019871012220002', 'M', '2000-02-02', '082196949911', 'Jl. Sungguminasa', '179613677568916b941f499.jpeg', '1');

-- --------------------------------------------------------

--
-- Table structure for table `pengaturan_jam`
--

CREATE TABLE `pengaturan_jam` (
  `id` int NOT NULL,
  `jam_masuk` time NOT NULL,
  `batas_terlambat` time NOT NULL,
  `jam_pulang` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pengaturan_jam`
--

INSERT INTO `pengaturan_jam` (`id`, `jam_masuk`, `batas_terlambat`, `jam_pulang`) VALUES
(1, '06:30:00', '06:45:00', '16:45:00');

-- --------------------------------------------------------

--
-- Table structure for table `rekap`
--

CREATE TABLE `rekap` (
  `rekap_id` bigint NOT NULL,
  `pegawai_id` int NOT NULL,
  `rekap_tanggal` date NOT NULL,
  `rekap_masuk` time DEFAULT NULL,
  `rekap_keluar` time DEFAULT NULL,
  `rekap_photomasuk` varchar(255) DEFAULT NULL,
  `status1` tinyint NOT NULL DEFAULT '0',
  `rekap_photokeluar` varchar(255) DEFAULT NULL,
  `status2` tinyint NOT NULL DEFAULT '0',
  `rekap_keterangan` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `rekap`
--

INSERT INTO `rekap` (`rekap_id`, `pegawai_id`, `rekap_tanggal`, `rekap_masuk`, `rekap_keluar`, `rekap_photomasuk`, `status1`, `rekap_photokeluar`, `status2`, `rekap_keterangan`) VALUES
(18, 21, '2025-07-26', '03:54:50', '03:57:41', '6883e10a9de6d_picture.jpg', 0, '6883e1b5370dd_picture.jpg', 0, 'Hadir Pulang'),
(19, 21, '2025-08-05', '10:16:20', '10:16:30', '689169740050d_picture.jpg', 0, '6891697e059a4_picture.jpg', 0, 'Hadir Pulang'),
(20, 23, '2025-08-05', '10:25:46', '10:26:03', '68916baa2ec09_picture.jpg', 0, '68916bbb83130_picture.jpg', 0, 'Hadir Pulang'),
(21, 23, '2025-08-05', '10:25:41', '10:26:01', '68916baa2ec09_picture.jpg', 0, '68916bbb83130_picture.jpg', 0, 'Hadir Pulang'),
(22, 23, '2025-08-19', '00:33:52', NULL, '68a355f0d031a_picture.jpg', 0, NULL, 0, 'Hadir Masuk'),
(23, 21, '2025-08-19', '00:41:15', NULL, '68a357ab5b65e_picture.jpg', 0, NULL, 0, 'Hadir Masuk');

-- --------------------------------------------------------

--
-- Table structure for table `rfid_code`
--

CREATE TABLE `rfid_code` (
  `id` double NOT NULL,
  `rfid_code` varchar(64) NOT NULL,
  `used` int NOT NULL DEFAULT '0',
  `time_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `rfid_code`
--

INSERT INTO `rfid_code` (`id`, `rfid_code`, `used`, `time_update`) VALUES
(32, '9085FC20', 1, '2025-07-25 19:51:50'),
(35, '438FE6B6', 1, '2025-08-05 02:24:44');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `admin_username` (`admin_username`);

--
-- Indexes for table `jabatan`
--
ALTER TABLE `jabatan`
  ADD PRIMARY KEY (`jabatan_id`),
  ADD UNIQUE KEY `jabatan_nama` (`jabatan_nama`);

--
-- Indexes for table `pegawai`
--
ALTER TABLE `pegawai`
  ADD PRIMARY KEY (`pegawai_id`),
  ADD UNIQUE KEY `pegawai_rfid` (`pegawai_rfid`),
  ADD UNIQUE KEY `pegawai_nip` (`pegawai_nip`),
  ADD KEY `pegawai jabatanid to jabatanid` (`jabatan_id`);

--
-- Indexes for table `pengaturan_jam`
--
ALTER TABLE `pengaturan_jam`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rekap`
--
ALTER TABLE `rekap`
  ADD PRIMARY KEY (`rekap_id`),
  ADD KEY `fk_rekap_pegawai` (`pegawai_id`);

--
-- Indexes for table `rfid_code`
--
ALTER TABLE `rfid_code`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `jabatan`
--
ALTER TABLE `jabatan`
  MODIFY `jabatan_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `pegawai`
--
ALTER TABLE `pegawai`
  MODIFY `pegawai_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `pengaturan_jam`
--
ALTER TABLE `pengaturan_jam`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `rekap`
--
ALTER TABLE `rekap`
  MODIFY `rekap_id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `rfid_code`
--
ALTER TABLE `rfid_code`
  MODIFY `id` double NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `pegawai`
--
ALTER TABLE `pegawai`
  ADD CONSTRAINT `pegawai jabatanid to jabatanid` FOREIGN KEY (`jabatan_id`) REFERENCES `jabatan` (`jabatan_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `rekap`
--
ALTER TABLE `rekap`
  ADD CONSTRAINT `fk_rekap_pegawai` FOREIGN KEY (`pegawai_id`) REFERENCES `pegawai` (`pegawai_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

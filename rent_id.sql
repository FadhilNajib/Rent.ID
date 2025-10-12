-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 11, 2025 at 07:57 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rent.id`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id_admin` int(10) NOT NULL,
  `nama` varchar(20) NOT NULL,
  `email` varchar(20) NOT NULL,
  `password` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `id_customer` int(10) NOT NULL,
  `nama` varchar(20) DEFAULT NULL,
  `email` varchar(20) NOT NULL,
  `no_telepon` int(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `tanggal_daftar` date NOT NULL,
  `alamat` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`id_customer`, `nama`, `email`, `no_telepon`, `password`, `tanggal_daftar`, `alamat`) VALUES
(1, 'najib', 'fadhilnaji@gmail.com', 2147483647, '0', '2025-10-07', 'Dk Plosowangi Rt03/Rw03 Ds Plosowangi Kec.Tersono'),
(2, 'pelanggan1', 'pelanggan@upn.com', 0, '$2y$10$IbfDyZDfBkMFnoOm4xtgO.9TwxC/pZLhP53F8XjHM8t2dq/HFpF6S', '2025-10-09', ''),
(3, 'Alex', 'Alexisnder@gmail.com', 0, '$2y$10$slhCl.ldH9/t3YcQnr3Gfex4J8M1ahVZcEXvPQ.YHRjrGPINrO5CO', '2025-10-12', '');

-- --------------------------------------------------------

--
-- Table structure for table `kendaraan`
--

CREATE TABLE `kendaraan` (
  `kendaraan_id` int(11) NOT NULL,
  `id_mitra` int(11) NOT NULL,
  `jenis_kendaraan` enum('motor','mobil','lainnya') NOT NULL,
  `merk` varchar(100) NOT NULL,
  `model` varchar(100) NOT NULL,
  `tahun` year(4) DEFAULT NULL,
  `plat_nomor` varchar(20) NOT NULL,
  `harga_sewa_per_hari` decimal(10,2) NOT NULL,
  `status_kendaraan` enum('tersedia','disewa','maintenance') DEFAULT 'tersedia',
  `foto_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mitra`
--

CREATE TABLE `mitra` (
  `id_mitra` int(10) NOT NULL,
  `nama_mitra` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(20) NOT NULL,
  `no_telepon` int(20) NOT NULL,
  `alamat` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mitra`
--

INSERT INTO `mitra` (`id_mitra`, `nama_mitra`, `password`, `email`, `no_telepon`, `alamat`) VALUES
(1, 'sewa motor', '$2y$10$uf57Xgm90c0am', 'sewa@gmail.com', 888887766, 'jogja'),
(2, 'bagus', '$2y$10$kX8NtTNyCkFBmIyBTDqoR.5WPrkMWRCXFJsK.Yo87pvDl694DFHFe', 'bagus@yahoo.com', 2134567, 'malang'),
(3, 'motor', '$2y$10$63hH3njIIzsEh1RoHGt8EuECoWAg3qse4HDCPN4lk015UcBmbxunC', 'motor@yahoo.com', 987654321, 'jogja'),
(4, 'mitra1', '$2y$10$DYhmLd2NWGSBdBturM.tyOsuajS4RpBMMGZWSUyYYIR.jJOpVwoHO', 'mitra@upn.com', 0, ''),
(5, 'lopaa', '$2y$10$gJpTyBatVM2SAmYYcL/nt.WOlcBx.hVV0tX/1XfOcCIpa.YiN.7ha', 'lopa@gmail.com', 0, ''),
(6, 'ku', '$2y$10$lfruJY0CSPHsae9p9jHIjeoMJ44Qa13KpgonhomNzF0iWhaw6QcKK', 'kuku@gmail.com', 0, '');

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran`
--

CREATE TABLE `pembayaran` (
  `pembayaran_id` int(11) NOT NULL,
  `rental_id` int(11) NOT NULL,
  `metode_pembayaran` enum('transfer_bank','e_wallet','COD') NOT NULL,
  `jumlah_bayar` decimal(12,2) NOT NULL,
  `tanggal_bayar` datetime DEFAULT current_timestamp(),
  `status_pembayaran` enum('pending','sukses','gagal') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `rental_id` int(11) NOT NULL,
  `id_customer` int(11) NOT NULL,
  `kendaraan_id` int(11) NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `total_harga` decimal(12,2) NOT NULL,
  `status_rental` enum('pending','aktif','selesai','dibatalkan') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ulasan`
--

CREATE TABLE `ulasan` (
  `ulasan_id` int(11) NOT NULL,
  `id_customer` int(11) NOT NULL,
  `kendaraan_id` int(11) NOT NULL,
  `rating` tinyint(4) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `komentar` text DEFAULT NULL,
  `tanggal_ulasan` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`id_customer`);

--
-- Indexes for table `kendaraan`
--
ALTER TABLE `kendaraan`
  ADD PRIMARY KEY (`kendaraan_id`),
  ADD UNIQUE KEY `plat_nomor` (`plat_nomor`),
  ADD KEY `id_mitra` (`id_mitra`);

--
-- Indexes for table `mitra`
--
ALTER TABLE `mitra`
  ADD PRIMARY KEY (`id_mitra`);

--
-- Indexes for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`pembayaran_id`),
  ADD KEY `rental_id` (`rental_id`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`rental_id`),
  ADD KEY `id_customer` (`id_customer`),
  ADD KEY `kendaraan_id` (`kendaraan_id`);

--
-- Indexes for table `ulasan`
--
ALTER TABLE `ulasan`
  ADD PRIMARY KEY (`ulasan_id`),
  ADD KEY `id_customer` (`id_customer`),
  ADD KEY `kendaraan_id` (`kendaraan_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `id_customer` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `kendaraan`
--
ALTER TABLE `kendaraan`
  MODIFY `kendaraan_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mitra`
--
ALTER TABLE `mitra`
  MODIFY `id_mitra` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `pembayaran_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `rental_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ulasan`
--
ALTER TABLE `ulasan`
  MODIFY `ulasan_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `kendaraan`
--
ALTER TABLE `kendaraan`
  ADD CONSTRAINT `kendaraan_ibfk_1` FOREIGN KEY (`id_mitra`) REFERENCES `mitra` (`id_mitra`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`rental_id`) REFERENCES `transaksi` (`rental_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `transaksi_ibfk_1` FOREIGN KEY (`id_customer`) REFERENCES `customer` (`id_customer`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `transaksi_ibfk_2` FOREIGN KEY (`kendaraan_id`) REFERENCES `kendaraan` (`kendaraan_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `ulasan`
--
ALTER TABLE `ulasan`
  ADD CONSTRAINT `ulasan_ibfk_1` FOREIGN KEY (`id_customer`) REFERENCES `customer` (`id_customer`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ulasan_ibfk_2` FOREIGN KEY (`kendaraan_id`) REFERENCES `kendaraan` (`kendaraan_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 05, 2025 at 09:00 AM
-- Server version: 11.4.8-MariaDB-cll-lve
-- PHP Version: 8.4.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `aplx2447_hjs`
--

-- --------------------------------------------------------

--
-- Table structure for table `m_order`
--

CREATE TABLE `m_order` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `ttd_id` int(11) NOT NULL DEFAULT 0,
  `cap_id` int(11) NOT NULL DEFAULT 0,
  `order_kat_id` int(11) NOT NULL DEFAULT 0,
  `order_katsub_id` int(11) NOT NULL DEFAULT 0,
  `shipper_id` int(11) NOT NULL DEFAULT 0,
  `consignee_id` int(11) NOT NULL DEFAULT 0,
  `nama` varchar(255) DEFAULT NULL,
  `no_bastb` varchar(255) DEFAULT NULL,
  `container_seal` varchar(255) DEFAULT NULL,
  `container_size` varchar(255) DEFAULT NULL,
  `loading_des` varchar(255) DEFAULT NULL,
  `commodity` varchar(255) DEFAULT NULL,
  `vessel` varchar(255) DEFAULT NULL,
  `voyage` varchar(255) DEFAULT NULL,
  `conditi` varchar(255) DEFAULT NULL,
  `discharging_date` varchar(255) DEFAULT NULL,
  `invoice_no` varchar(255) DEFAULT NULL,
  `invoice_date` varchar(255) DEFAULT NULL,
  `credit_terms` varchar(255) DEFAULT NULL,
  `customer` varchar(255) NOT NULL,
  `attn` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `total_amount` int(100) DEFAULT 0,
  `muncul_rek` int(11) NOT NULL DEFAULT 0,
  `rek_id` int(11) NOT NULL DEFAULT 0,
  `status` int(11) NOT NULL DEFAULT 0 COMMENT '0. open\r\n1. kirim (dalam perjalanan)\r\n2. transit (singgah)\r\n3. sampai (sampai di lokasi blum sortir)\r\n4. selesai (selesai sortir dan selesai pindah)\r\n',
  `notes` varchar(500) DEFAULT NULL,
  `notes_sortir` varchar(500) DEFAULT NULL,
  `harga_seal` int(11) NOT NULL DEFAULT 0,
  `harga_truck_muat` int(11) NOT NULL DEFAULT 0,
  `harga_thc_muat` int(11) NOT NULL DEFAULT 0,
  `harga_of` int(11) NOT NULL DEFAULT 0,
  `harga_lss` int(11) NOT NULL DEFAULT 0,
  `harga_thc_bongkar` int(11) NOT NULL DEFAULT 0,
  `harga_strip` int(11) NOT NULL DEFAULT 0,
  `harga_truck_bongkar` int(11) NOT NULL DEFAULT 0,
  `harga_buruh` int(11) NOT NULL DEFAULT 0,
  `harga_inv` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `codx` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `m_order`
--

INSERT INTO `m_order` (`id`, `user_id`, `ttd_id`, `cap_id`, `order_kat_id`, `order_katsub_id`, `shipper_id`, `consignee_id`, `nama`, `no_bastb`, `container_seal`, `container_size`, `loading_des`, `commodity`, `vessel`, `voyage`, `conditi`, `discharging_date`, `invoice_no`, `invoice_date`, `credit_terms`, `customer`, `attn`, `address`, `total_amount`, `muncul_rek`, `rek_id`, `status`, `notes`, `notes_sortir`, `harga_seal`, `harga_truck_muat`, `harga_thc_muat`, `harga_of`, `harga_lss`, `harga_thc_bongkar`, `harga_strip`, `harga_truck_bongkar`, `harga_buruh`, `harga_inv`, `created_at`, `updated_at`, `codx`) VALUES
(59, 0, 17, 18, 1, 2, 190, 191, 'abqgf', '', '', '', '', '', '', '', '', '14-12-2025', '01', '10-12-2025', '14 hari', 'cv. agung jaya', '', '', 20000, 1, 2, 0, NULL, NULL, 100000, 1200000, 1043844, 3500000, 1750000, 0, 0, 4400000, 500000, 13295000, '2025-12-02 06:57:36', '2025-12-04 09:06:26', 'VNplqxATFa');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `m_order`
--
ALTER TABLE `m_order`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `m_order`
--
ALTER TABLE `m_order`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

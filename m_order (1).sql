-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 24, 2025 at 02:28 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hjs`
--

-- --------------------------------------------------------

--
-- Table structure for table `m_order`
--

CREATE TABLE `m_order` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `codx` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `m_order`
--

INSERT INTO `m_order` (`id`, `user_id`, `order_kat_id`, `order_katsub_id`, `shipper_id`, `consignee_id`, `nama`, `no_bastb`, `container_seal`, `container_size`, `loading_des`, `commodity`, `vessel`, `voyage`, `conditi`, `discharging_date`, `invoice_no`, `invoice_date`, `credit_terms`, `customer`, `attn`, `address`, `total_amount`, `muncul_rek`, `rek_id`, `status`, `notes`, `notes_sortir`, `harga_seal`, `harga_truck_muat`, `harga_thc_muat`, `harga_of`, `harga_lss`, `harga_thc_bongkar`, `harga_strip`, `harga_truck_bongkar`, `harga_buruh`, `created_at`, `updated_at`, `codx`) VALUES
(49, 0, 1, 2, 190, 194, 'fvbFq', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, 0, 0, 0, 0, NULL, NULL, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2025-08-13 16:15:03', '2025-08-13 15:15:11', 'qIVJuptyTK'),
(50, 0, 1, 2, 164, 191, 'Qkjye', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'acb', 'ac', 'ac', 'gera', 'ac', 'ab', 4000000, 1, 2, 0, NULL, NULL, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2025-09-03 07:15:42', '2025-10-01 09:04:16', 'DgTzdjOCfv'),
(51, 0, 1, 1, 190, 193, 'ZitJz', 'fdsaf', 'SPNU 2544367', 'Y25.251003978867', 'BITUNG', 'SURABAYA', 'KM. ARMADA PERMATA', '15/2025', '1', '', 'INV-HJS-123.MT-I/2025', '27 September 2025', '14 Hari', 'tes', 'CV. AGUNG JAYA', 'Ternate', 13295000, 1, 2, 1, NULL, NULL, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2025-09-27 04:41:10', '2025-10-01 08:57:36', 'eEWljPqLXI'),
(56, 0, 1, 2, 190, 184, 'YMIdZ', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, 0, 0, 0, 0, NULL, NULL, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2025-10-04 21:04:23', '2025-10-04 21:36:23', 'snoCxAulDU'),
(57, 0, 1, 1, 190, 191, 'ynJec', '005/2025', 'SPNU 235678 - Y25.25100235678', '20 FT', 'BITUNG - SURABAYA', 'CENGKEH', 'KM. ARMADA PERMATA', '16 / 2025', '1', '', 'INV-HJS', '29 Oktober 2025', '12 November 2025', 'PT. Angin Ribut', 'Bpk. Chris', 'Singkil 1', 1000000, 1, 2, 3, NULL, NULL, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2025-10-04 23:40:42', '2025-10-20 06:27:41', 'wiHmxUTQXd');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

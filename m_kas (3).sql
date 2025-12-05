-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 04 Des 2025 pada 05.19
-- Versi server: 8.0.30
-- Versi PHP: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Basis data: `hjs`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `m_kas`
--

CREATE TABLE `m_kas` (
  `id` int NOT NULL,
  `user_id` int NOT NULL DEFAULT '0',
  `order_id` int NOT NULL DEFAULT '0',
  `order_kat_id` int NOT NULL DEFAULT '0' COMMENT 'ini di isi di master order saat menambah keuangan yg tujuannya hanya untuk menulaporan supaya bisa liat laporan berdasarkan keuntungan perkategori dan per sub kategori',
  `order_katsub_id` int NOT NULL DEFAULT '0' COMMENT 'ini di isi di master order saat menambah keuangan yg tujuannya hanya untuk menulaporan supaya bisa liat laporan berdasarkan keuntungan perkategori dan per sub kategori',
  `order_codx` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `shipper_id` int NOT NULL DEFAULT '0',
  `consignee_id` int NOT NULL DEFAULT '0',
  `kat_kas_id` int NOT NULL DEFAULT '0',
  `kat_kassub_id` int NOT NULL DEFAULT '0',
  `kat_kassub_list_id` int NOT NULL DEFAULT '0',
  `nama` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `des` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nilai` int NOT NULL DEFAULT '0',
  `nota` varchar(550) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `stat` int NOT NULL DEFAULT '0' COMMENT '1.masuk\r\n2.keluar\r\n3.deposit\r\n4.piutang\r\n5.piutang lunas',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `codx` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `m_kas`
--

INSERT INTO `m_kas` (`id`, `user_id`, `order_id`, `order_kat_id`, `order_katsub_id`, `order_codx`, `shipper_id`, `consignee_id`, `kat_kas_id`, `kat_kassub_id`, `kat_kassub_list_id`, `nama`, `des`, `nilai`, `nota`, `stat`, `created_at`, `updated_at`, `codx`) VALUES
(1, 0, 50, 0, 0, NULL, 164, 0, 0, 0, 0, 'pembayaran invoice', NULL, 4000000, NULL, 5, '2025-09-27 02:05:10', '2025-09-27 02:10:30', NULL),
(2, 90, 50, 0, 0, 'DgTzdjOCfv', 164, 191, 0, 0, 0, 'masuk pembayaran', '', 4000000, NULL, 1, '2025-09-27 03:06:15', '2025-09-27 02:41:22', 'eIxDcLkyBm'),
(3, 90, 49, 0, 0, 'qIVJuptyTK', 190, 194, 0, 0, 0, 'bayar invoice', NULL, 1000000, NULL, 1, '2025-09-27 03:10:47', '2025-09-27 02:10:58', 'SMcRuXJGUh'),
(5, 90, 50, 0, 0, 'DgTzdjOCfv', 164, 191, 0, 0, 0, 'bayar buruh', NULL, 10000, NULL, 1, '2025-09-27 03:37:34', '2025-09-27 02:37:49', 'gPREYineMN'),
(6, 90, 50, 0, 0, 'DgTzdjOCfv', 164, 191, 0, 0, 0, 'bayar tenaga kerja', NULL, 25000, NULL, 1, '2025-09-27 03:37:50', '2025-09-27 02:38:01', 'ZwomYqBHjy'),
(7, 90, 50, 0, 0, 'DgTzdjOCfv', 164, 191, 0, 0, 0, 'bayar garasi', NULL, 500000, NULL, 1, '2025-09-27 03:38:02', '2025-09-27 02:38:12', 'WcruqVQyis'),
(8, 90, 0, 0, 0, NULL, 0, 0, 0, 0, 0, 'bayar aqua', '-', 5000, NULL, 2, '2025-09-27 03:38:20', '2025-09-27 02:40:54', 'PakJGIvpiA'),
(9, 90, 0, 0, 0, NULL, 0, 0, 0, 0, 0, 'bayar talangan', '-', 50000, NULL, 2, '2025-09-27 03:38:39', '2025-09-27 02:38:59', 'kjCbMuzRtY'),
(10, 90, 0, 0, 0, NULL, 0, 0, 0, 0, 0, 'bayar karton', '-', 300000, NULL, 2, '2025-09-27 03:39:25', '2025-09-27 02:39:40', 'gtDxoXPZaO'),
(11, 90, 0, 0, 0, NULL, 0, 0, 0, 0, 0, 'biaya minyak', '-', 400000, NULL, 2, '2025-09-26 03:39:41', '2025-09-27 02:46:08', 'XvknoCJLgD'),
(12, 90, 0, 0, 0, NULL, 0, 0, 0, 0, 0, 'biaya gelon', '-', 500000, NULL, 2, '2025-09-26 03:39:55', '2025-09-27 02:46:02', 'LAvjUfRwDY'),
(13, 90, 51, 0, 0, 'eEWljPqLXI', 190, 193, 8, 0, 0, 'Trucking Tomohon', NULL, 1000000, NULL, 1, '2025-09-27 08:11:04', '2025-09-27 07:12:27', 'BWwNpMzoGy'),
(14, 0, 51, 0, 0, NULL, 190, 0, 0, 0, 0, 'pembayaran invoice', NULL, 13295000, NULL, 4, '2025-09-27 07:15:03', '2025-09-27 07:15:03', NULL),
(15, 90, 51, 0, 0, 'eEWljPqLXI', 190, 193, 9, 0, 0, 'Biaya Buruh', NULL, 400000, NULL, 4, '2025-09-27 08:22:01', '2025-09-27 07:24:43', 'BdINeXRHlg'),
(16, 0, 54, 0, 0, NULL, 0, 0, 0, 0, 0, 'pembayaran invoice', NULL, 0, NULL, 4, '2025-10-01 08:30:07', '2025-10-01 08:30:07', NULL),
(17, 90, 51, 1, 1, 'eEWljPqLXI', 190, 193, 0, 0, 0, 'tes', NULL, 10000, NULL, 1, '2025-10-04 20:43:11', '2025-10-04 19:43:24', 'bzdOktLjHv'),
(18, 90, 0, 0, 0, NULL, 0, 0, 0, 0, 0, 'gera', 'fdsafasdfs', 12000, NULL, 1, '2025-10-04 20:43:58', '2025-10-04 19:44:16', 'kBdeIwmDVu'),
(19, 90, 56, 1, 2, 'snoCxAulDU', 0, 0, 0, 0, 0, 'masuk pembayaran', NULL, 35000, NULL, 1, '2025-10-04 21:04:38', '2025-10-04 20:04:54', 'nxkXrRbUdA'),
(20, 90, 56, 1, 2, 'snoCxAulDU', 190, 184, 0, 0, 0, 'masuk pembayaran', NULL, 30000, NULL, 1, '2025-10-04 22:36:23', '2025-10-04 21:39:10', 'RDsrvBaQbY'),
(21, 90, 56, 1, 2, 'snoCxAulDU', 190, 184, 0, 0, 0, 'bayar buruh', NULL, 20000, NULL, 4, '2025-10-05 04:12:13', '2025-10-05 03:14:11', 'aNslgLkzDq'),
(22, 90, 56, 1, 2, 'snoCxAulDU', 190, 184, 0, 0, 0, 'Byar mobil', NULL, 35000, NULL, 4, '2025-10-05 06:16:56', '2025-10-05 05:17:10', 'qkruiJOdBs'),
(23, 0, 57, 0, 0, NULL, 190, 0, 0, 0, 0, 'pembayaran invoice', NULL, 1000000, NULL, 4, '2025-10-20 06:22:55', '2025-12-03 10:10:54', NULL),
(24, 90, 57, 1, 1, 'wiHmxUTQXd', 190, 191, 9, 0, 0, 'Biaya Buruh', NULL, 400000, NULL, 4, '2025-10-20 07:29:16', '2025-10-20 06:29:47', 'taqgdLfviH'),
(25, 90, 57, 1, 1, 'wiHmxUTQXd', 190, 191, 8, 0, 0, 'trucking', NULL, 1200000, NULL, 4, '2025-10-20 07:30:34', '2025-10-20 06:31:09', 'YESDhZnoMW'),
(26, 90, 0, 0, 0, NULL, 0, 0, 1, 0, 0, 'Beban Gaji', 'Bayar gaji direktur', 20000000, NULL, 2, '2025-11-23 10:05:20', '2025-11-23 10:05:42', 'RJYNaMOIpf'),
(27, 90, 0, 0, 0, NULL, 0, 0, 1, 0, 0, 'Beban Gaji', 'fdsgfdgsdg', 10000000, NULL, 2, '2025-11-23 10:09:58', '2025-11-23 10:10:16', 'CQoHFrhcfE'),
(28, 0, 0, 0, 0, NULL, 0, 0, 1, 1, 22, 'Beban Gaji', 'tes', 5000000, NULL, 2, '2025-11-23 10:32:45', '2025-11-23 10:32:55', 'ZoApDJaKzg'),
(29, 90, 0, 0, 0, NULL, 0, 0, 0, 0, 0, 'Bayar Listrik', 'Bayar Listrik', 10000, NULL, 2, '2025-11-23 10:47:47', '2025-11-23 10:48:09', 'UXyDrCTufi'),
(30, 0, 0, 0, 0, NULL, 0, 0, 1, 2, 35, 'Beban Bonus & THR', 'bonus ', 20000, NULL, 2, '2025-11-23 10:48:59', '2025-11-23 10:49:11', '5DFEERLGMy'),
(31, 0, 0, 0, 0, NULL, 0, 0, 1, 2, 36, 'Beban Bonus & THR', 'tes', 25000, NULL, 2, '2025-11-23 10:50:52', '2025-11-23 21:00:55', 'uIzkR9pJYz'),
(32, 0, 0, 0, 0, NULL, 0, 0, 1, 1, 22, 'Beban Gaji', 'tes', 27000, NULL, 2, '2025-11-23 10:51:44', '2025-11-23 21:12:44', 'VQw3RgkZit');

--
-- Indeks untuk tabel yang dibuang
--

--
-- Indeks untuk tabel `m_kas`
--
ALTER TABLE `m_kas`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `m_kas`
--
ALTER TABLE `m_kas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;


-- --------------------------------------------------------

-- Struktur dari tabel `m_order_deadline`

CREATE TABLE `m_order_deadline` (
  `id` int NOT NULL,
  `order_id` int NOT NULL,
  `deadline_date` date NOT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indeks untuk tabel `m_order_deadline`
--
ALTER TABLE `m_order_deadline`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- AUTO_INCREMENT untuk tabel `m_order_deadline`
--
ALTER TABLE `m_order_deadline`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

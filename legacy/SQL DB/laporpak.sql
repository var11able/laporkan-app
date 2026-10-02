-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 24, 2024 at 11:09 AM
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
-- Database: `laporpak`
--

-- --------------------------------------------------------

--
-- Table structure for table `log`
--

CREATE TABLE `log` (
  `id_log` int(11) NOT NULL,
  `isi_log` text NOT NULL,
  `tgl_log` datetime NOT NULL,
  `id_user` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `log`
--

INSERT INTO `log` (`id_log`, `isi_log`, `tgl_log`, `id_user`) VALUES
(16, 'Profil berhasil diubah!', '2023-05-13 09:52:13', 1),
(17, 'Tanggapan sudah dalam pengerjaan berhasil ditambahkan', '2023-05-13 09:54:10', 1),
(18, 'Tanggapan Terima kasih.Info anda valid,Akan kita proses. berhasil ditambahkan', '2023-05-13 20:06:18', 1),
(19, 'Tanggapan sudah dalam proses pengerjaan berhasil ditambahkan', '2023-05-13 20:09:10', 1),
(20, 'Tanggapan proses pengerjaan berhasil ditambahkan', '2023-05-13 20:09:34', 1),
(21, 'Tanggapan terima kasih,info anda valid.akan kami teruskan ke pihak terkait. berhasil ditambahkan', '2023-06-04 18:42:49', 1),
(22, 'Tanggapan proses pengerjaan oleh Dinas PUPR dan BNPB berhasil ditambahkan', '2023-06-04 18:43:29', 1),
(23, 'Tanggapan proses pengerjaan masih dilakukan berhasil ditambahkan', '2023-06-04 18:44:38', 1),
(24, 'Tanggapan jalan sudah bisa dilalui kendaraan.terima kasih atas laporan anda berhasil ditambahkan', '2023-06-04 18:45:16', 1),
(25, 'Kecamatan Cermee berhasil ditambahkan', '2023-06-04 18:47:08', 1),
(26, 'Kelurahan Cermee Barat berhasil ditambahkan', '2023-06-04 18:47:25', 1),
(27, 'Pengguna andri123 berhasil dihapus', '2024-05-23 14:35:53', 1),
(28, 'Tanggapan tst berhasil ditambahkan', '2024-05-23 18:11:38', 1),
(29, 'Tanggapan tst berhasil ditambahkan', '2024-05-23 18:54:53', 1),
(30, 'Pengaduan test berhasil dihapus', '2024-05-23 18:56:44', 1),
(31, 'Tanggapan test tanggapan berhasil ditambahkan', '2024-05-24 14:17:24', 1),
(32, 'Pengaduan Telepon Umum rusak di daerah Bakti Jaya berhasil dihapus', '2024-05-24 14:19:38', 1),
(33, 'Pengaduan Jembatan antar desa rusak, di daerah Rawa buntu berhasil dihapus', '2024-05-24 14:19:43', 1),
(34, 'Pengaduan Jalan di daerah pocis, bakti jaya, rusak parah. \r\nTerimakasih. berhasil dihapus', '2024-05-24 14:19:47', 1),
(35, 'Pengaduan Tanah longsor, daerah Serpong dekat Pesantren Hidayatullah Yayasan Al- Firdaus berhasil dihapus', '2024-05-24 14:19:52', 1),
(36, 'Pengaduan Jl.Sudirman longsor pak,belum ada petugas datang berhasil dihapus', '2024-05-24 14:19:59', 1),
(37, 'Pengaduan pak ada tanah longsor di jalan santawi no 88 (depan pengairan) berhasil dihapus', '2024-05-24 14:20:06', 1);

-- --------------------------------------------------------

--
-- Table structure for table `masyarakat`
--

CREATE TABLE `masyarakat` (
  `id_masyarakat` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `no_telepon` varchar(20) NOT NULL,
  `alamat` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `masyarakat`
--

INSERT INTO `masyarakat` (`id_masyarakat`, `nama`, `username`, `password`, `no_telepon`, `alamat`) VALUES
(1, 'Andre Farhan Saputra', 'andre123', '$2y$10$np0VReX.hpfpyfGZMB61keaDNoe14ZSzZ9mAP0Zql6mSQuCYfWOla', '087733932416', 'Jl. AMD Babakan Pocis No. 100 RT02/02'),
(2, 'Muhammad Irgi Al Ghithraf', 'irgi5', '$2y$10$51l3fVtvtoUTkuv8NajfjOaWxP7Y/T8Re2LMjmvciuQ0y2a5jgE3e', '085773094859', 'Perum Puri Serpong 1 Blok D5 No. 7 RT 08/02 Setu, Setu, Tangerang Selatan, Banten'),
(3, 'Ableza Melani Putri', 'ableza24', '$2y$10$xiVl2xqkEMAUvj3h2fnUeeuMnt.dxrHIO0m5jgEREujFQAYH1c1zG', '089677646147', 'JL. AMD Babakan Pocis No. 7 RT 02/01 Bakti Jaya Setu Kota Tangerang Selatan'),
(4, 'Annisa Ulazijah Faqih', 'annisa98', '$2y$10$K287REWLi5fn/pCmi90HTumNG2DiwAPjxsGWvx5oDTbhK3aolTBqC', '087808564812', 'Kp. Jaletreng No. 41 RT 03/03 Serpong, Serpong'),
(5, 'Azzaura', 'azzaura56', '$2y$10$5AZT/bsxiJQ/YcrCou.U2.Hun0o3s9aH6do73f01Tm8IZwH/udx/W', '085212984453', 'Perum Griya Cendekia No. 17 RT 05/06, Curug, Gunung Sindur'),
(6, 'Putri', 'putri77', '$2y$10$3ZPUwLjSErGxeyaKoRk9g.mzQJRy8IGnCU9pD9fb0YcUisNGGg8l6', '098789789789', 'BONDOWOSO'),
(7, 'Rizal Anwar Sanusi', 'rizal88', '$2y$10$v./WieT/FIMFzWH9lWEGQelp/ECd/QUlR1mX4I/Ro3pIMysZqOklS', '089566665656', 'tapen bondowoso'),
(8, 'Ishvara Pranidhana Lakshmana', 'Ishvara', '$2y$10$FLGgc5YXzcMVuvNUqGmJJuSTAJ3gNgknImD0v2vj8Ignz.t2tPNHG', '081382788104', 'tst'),
(9, 'Test Nama Lengkap', 'test', '$2y$10$2Nx1qrbpoMm07go58.1Ik.32Vfg.nUodi3aG2JhXtuEOTikKV4YG.', '081234467', 'testalamat'),
(10, 'Test Daftar Ss', 'test daff', '$2y$10$DnWhDOrV3ABRFKutzF1wjueBbm5t95nG7kp2SVvHSfyXJWq/kVhVa', '33333333', 'dadas');

-- --------------------------------------------------------

--
-- Table structure for table `pengaduan`
--

CREATE TABLE `pengaduan` (
  `id_pengaduan` int(11) NOT NULL,
  `isi_laporan` text NOT NULL,
  `tgl_pengaduan` datetime NOT NULL,
  `foto` varchar(500) DEFAULT 'default.png',
  `status_pengaduan` enum('belum_ditanggapi','proses','valid','pengerjaan','selesai','tidak_valid') NOT NULL DEFAULT 'belum_ditanggapi',
  `id_masyarakat` int(11) NOT NULL,
  `id_kelurahan` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengaduan`
--

INSERT INTO `pengaduan` (`id_pengaduan`, `isi_laporan`, `tgl_pengaduan`, `foto`, `status_pengaduan`, `id_masyarakat`, `id_kelurahan`) VALUES
(8, 'test laporan', '2024-05-24 11:57:32', 'default.png', 'proses', 8, 0);

-- --------------------------------------------------------

--
-- Table structure for table `saran`
--

CREATE TABLE `saran` (
  `id_saran` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `no_telepon` varchar(20) NOT NULL,
  `alamat` text NOT NULL,
  `saran` text NOT NULL,
  `tgl_saran` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `saran`
--

INSERT INTO `saran` (`id_saran`, `nama`, `no_telepon`, `alamat`, `saran`, `tgl_saran`) VALUES
(1, 'Rizal Anwar Sanusi', '0897497897', 'TAPEN', 'CONTOH SARAN', '2023-06-04 18:46:31');

-- --------------------------------------------------------

--
-- Table structure for table `tanggapan`
--

CREATE TABLE `tanggapan` (
  `id_tanggapan` int(11) NOT NULL,
  `isi_tanggapan` text NOT NULL,
  `tgl_tanggapan` datetime NOT NULL,
  `status_tanggapan` enum('proses','valid','pengerjaan','selesai','tidak_valid') NOT NULL,
  `foto_tanggapan` varchar(500) NOT NULL DEFAULT 'default.png',
  `id_pengaduan` int(11) NOT NULL,
  `id_user` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tanggapan`
--

INSERT INTO `tanggapan` (`id_tanggapan`, `isi_tanggapan`, `tgl_tanggapan`, `status_tanggapan`, `foto_tanggapan`, `id_pengaduan`, `id_user`) VALUES
(25, 'test tanggapan', '2024-05-24 14:17:24', 'proses', 'default.png', 8, 1);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id_user` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `no_telepon` varchar(20) NOT NULL,
  `jabatan` enum('administrator','operator') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id_user`, `nama`, `username`, `password`, `no_telepon`, `jabatan`) VALUES
(1, 'Administrator', 'admin', '$2y$10$L201Eud0B8zkRfT9wOctFeK1LSJWFxwDV8He41JDk4ggRLUb9aIC6', '08956434564', 'administrator');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `log`
--
ALTER TABLE `log`
  ADD PRIMARY KEY (`id_log`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `masyarakat`
--
ALTER TABLE `masyarakat`
  ADD PRIMARY KEY (`id_masyarakat`);

--
-- Indexes for table `pengaduan`
--
ALTER TABLE `pengaduan`
  ADD PRIMARY KEY (`id_pengaduan`),
  ADD KEY `id_masyarakat` (`id_masyarakat`),
  ADD KEY `id_kelurahan` (`id_kelurahan`);

--
-- Indexes for table `saran`
--
ALTER TABLE `saran`
  ADD PRIMARY KEY (`id_saran`);

--
-- Indexes for table `tanggapan`
--
ALTER TABLE `tanggapan`
  ADD PRIMARY KEY (`id_tanggapan`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_pengaduan` (`id_pengaduan`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `log`
--
ALTER TABLE `log`
  MODIFY `id_log` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `masyarakat`
--
ALTER TABLE `masyarakat`
  MODIFY `id_masyarakat` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `pengaduan`
--
ALTER TABLE `pengaduan`
  MODIFY `id_pengaduan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `saran`
--
ALTER TABLE `saran`
  MODIFY `id_saran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tanggapan`
--
ALTER TABLE `tanggapan`
  MODIFY `id_tanggapan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

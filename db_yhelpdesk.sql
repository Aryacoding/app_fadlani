-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 12, 2026 at 04:24 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_yhelpdesk`
--

-- --------------------------------------------------------

--
-- Table structure for table `area_cover`
--

CREATE TABLE `area_cover` (
  `id_area` int(11) NOT NULL,
  `nama_area` varchar(100) NOT NULL,
  `kode_pos` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `area_cover`
--

INSERT INTO `area_cover` (`id_area`, `nama_area`, `kode_pos`) VALUES
(1, 'Kec. Banjarmasin Tengah (Kalsel)', '70111'),
(2, 'Kec. Banjarmasin Utara (Kalsel)', '70122'),
(3, 'Kec. Banjarbaru Utara (Kalsel)', '70711'),
(4, 'Kec. Martapura (Kalsel)', '70611'),
(5, 'Kec. Pelaihari (Kalsel)', '70811'),
(6, 'Kec. Jekan Raya (Kalteng)', '73112'),
(7, 'Kec. Pahandut (Kalteng)', '73111'),
(8, 'Kec. Mentawa Baru Ketapang (Kalteng)', '74322'),
(9, 'Kec. Arut Selatan (Kalteng)', '74111'),
(10, 'Kec. Kumai (Kalteng)', '74181');

-- --------------------------------------------------------

--
-- Table structure for table `foto_tiket`
--

CREATE TABLE `foto_tiket` (
  `id_foto` int(11) NOT NULL,
  `id_tiket` int(11) NOT NULL,
  `nama_file_foto` varchar(255) NOT NULL,
  `sumber_foto` enum('Pelanggan','Teknisi') NOT NULL,
  `keterangan_foto` varchar(150) DEFAULT NULL COMMENT 'Misal: Foto Kabel Putus, Foto Redaman',
  `waktu_unggah` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `foto_tiket`
--

INSERT INTO `foto_tiket` (`id_foto`, `id_tiket`, `nama_file_foto`, `sumber_foto`, `keterangan_foto`, `waktu_unggah`) VALUES
(1, 1, 'BUKTI_1775974347_567.png', 'Teknisi', 'Bukti Perbaikan Teknisi', '2026-04-12 06:12:27'),
(2, 1, 'BUKTI_1775974347_307.jpg', 'Teknisi', 'Bukti Perbaikan Teknisi', '2026-04-12 06:12:27');

-- --------------------------------------------------------

--
-- Table structure for table `jabatan`
--

CREATE TABLE `jabatan` (
  `id_jabatan` int(11) NOT NULL,
  `nama_jabatan` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jabatan`
--

INSERT INTO `jabatan` (`id_jabatan`, `nama_jabatan`, `deskripsi`) VALUES
(1, 'Developer', 'Hak akses tertinggi pembuat sistem'),
(2, 'Admin Dispatcher', 'Bertugas memonitor tiket masuk dan membagikannya ke Teknisi'),
(3, 'Teknisi Lapangan', 'Bertugas menangani perbaikan fisik dan logic di lokasi pelanggan'),
(4, 'Manager', 'Pimpinan yang memonitor laporan dan kinerja tim'),
(5, 'Customer Service', 'Melayani keluhan dasar pelanggan via sistem');

-- --------------------------------------------------------

--
-- Table structure for table `karyawan`
--

CREATE TABLE `karyawan` (
  `id_karyawan` int(11) NOT NULL,
  `nip_karyawan` varchar(30) NOT NULL,
  `nama_karyawan` varchar(100) NOT NULL,
  `jenis_kelamin` enum('Laki-laki','Perempuan') NOT NULL,
  `alamat` text NOT NULL,
  `no_whatsapp` varchar(15) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `foto_profil` varchar(255) DEFAULT 'default_profil.png',
  `id_jabatan` int(11) NOT NULL,
  `status_ketersediaan` enum('Ready','Bertugas','Off') DEFAULT 'Ready' COMMENT 'Khusus untuk jabatan Teknisi',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `karyawan`
--

INSERT INTO `karyawan` (`id_karyawan`, `nip_karyawan`, `nama_karyawan`, `jenis_kelamin`, `alamat`, `no_whatsapp`, `email`, `foto_profil`, `id_jabatan`, `status_ketersediaan`, `created_at`) VALUES
(1, 'DEV-001', 'Fadlani', 'Laki-laki', 'Kantor Pusat', '0895782382737', 'dev@sistem.com', '1775934652_avatar-2.jpg', 1, 'Ready', '2026-04-11 18:46:11'),
(2, 'ADM-001', 'Siti Aminah', 'Perempuan', 'Jl. Darmo No. 12', '08123456701', 'siti@icon.co.id', '1775974257_avatar-6.jpg', 2, 'Ready', '2026-04-12 02:55:33'),
(3, 'ADM-002', 'Budi Santoso', 'Laki-laki', 'Jl. Kenjeran No. 8', '08123456702', 'budi@icon.co.id', 'default_profil.png', 2, 'Ready', '2026-04-12 02:55:33'),
(4, 'MGR-001', 'Ahmad Dahlan', 'Laki-laki', 'Jl. Diponegoro No. 1', '08123456703', 'ahmad@icon.co.id', 'default_profil.png', 4, 'Ready', '2026-04-12 02:55:33'),
(5, 'TEK-001', 'Wahyu Hidayat', 'Laki-laki', 'Jl. Rungkut No. 5', '08123456704', 'wahyu@icon.co.id', '1775974645_avatar-10.jpg', 3, 'Ready', '2026-04-12 02:55:33'),
(6, 'TEK-002', 'Dodi Pratama', 'Laki-laki', 'Jl. Wiyung No. 22', '08123456705', 'dodi@icon.co.id', 'default_profil.png', 3, 'Bertugas', '2026-04-12 02:55:33'),
(7, 'TEK-003', 'Andi Kusuma', 'Laki-laki', 'Jl. Gubeng No. 10', '08123456706', 'andi@icon.co.id', 'default_profil.png', 3, 'Ready', '2026-04-12 02:55:33'),
(8, 'TEK-004', 'Dimas Anggara', 'Laki-laki', 'Jl. Perak No. 3', '08123456707', 'dimas@icon.co.id', 'default_profil.png', 3, 'Off', '2026-04-12 02:55:33'),
(9, 'TEK-005', 'Reza Pahlevi', 'Laki-laki', 'Jl. Tunjungan No. 9', '08123456708', 'reza@icon.co.id', 'default_profil.png', 3, 'Ready', '2026-04-12 02:55:33'),
(10, 'TEK-006', 'Teguh Saputra', 'Laki-laki', 'Jl. Pahlawan No. 7', '08123456709', 'teguh@icon.co.id', 'default_profil.png', 3, 'Bertugas', '2026-04-12 02:55:33'),
(11, 'CS-001', 'Rina Marlina', 'Perempuan', 'Jl. Arjuna No. 4', '08123456710', 'rina@icon.co.id', 'default_profil.png', 5, 'Ready', '2026-04-12 02:55:33');

-- --------------------------------------------------------

--
-- Table structure for table `kategori_gangguan`
--

CREATE TABLE `kategori_gangguan` (
  `id_kategori` int(11) NOT NULL,
  `nama_kategori` varchar(100) NOT NULL,
  `SLA_jam` int(11) DEFAULT NULL COMMENT 'Service Level Agreement dalam hitungan Jam'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kategori_gangguan`
--

INSERT INTO `kategori_gangguan` (`id_kategori`, `nama_kategori`, `SLA_jam`) VALUES
(1, 'Internet Mati Total (Lampu LOS Merah)', 24),
(2, 'Koneksi Lambat / Lemot (Speed Drop)', 12),
(3, 'Koneksi Sering Putus / RTO', 12),
(4, 'Perangkat Modem / Router Mati (Malfungsi)', 24),
(5, 'Kabel Fiber Optik Terputus / Pemindahan Tiang', 48);

-- --------------------------------------------------------

--
-- Table structure for table `paket_layanan`
--

CREATE TABLE `paket_layanan` (
  `id_paket` int(11) NOT NULL,
  `nama_paket` varchar(100) NOT NULL,
  `bandwidth` varchar(50) NOT NULL COMMENT 'Misal: 20 Mbps, 50 Mbps',
  `harga` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `paket_layanan`
--

INSERT INTO `paket_layanan` (`id_paket`, `nama_paket`, `bandwidth`, `harga`) VALUES
(1, 'ICONNET Basic', '10 Mbps', 150000.00),
(2, 'ICONNET Standard', '20 Mbps', 200000.00),
(3, 'ICONNET Premium', '35 Mbps', 250000.00),
(4, 'ICONNET Pro', '50 Mbps', 350000.00),
(5, 'ICONNET Gamer', '100 Mbps', 500000.00);

-- --------------------------------------------------------

--
-- Table structure for table `pelanggan`
--

CREATE TABLE `pelanggan` (
  `id_pelanggan` int(11) NOT NULL,
  `no_langganan` varchar(30) NOT NULL,
  `nik_ktp` varchar(20) NOT NULL,
  `nama_pelanggan` varchar(100) NOT NULL,
  `jenis_kelamin` enum('Laki-laki','Perempuan') NOT NULL,
  `no_whatsapp` varchar(15) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `alamat_pemasangan` text NOT NULL,
  `titik_koordinat_maps` varchar(100) DEFAULT NULL COMMENT 'Format: Latitude, Longitude',
  `id_paket` int(11) NOT NULL,
  `id_area` int(11) NOT NULL,
  `status_pelanggan` enum('Aktif','Isolir','Cabut') DEFAULT 'Aktif',
  `tgl_pemasangan` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pelanggan`
--

INSERT INTO `pelanggan` (`id_pelanggan`, `no_langganan`, `nik_ktp`, `nama_pelanggan`, `jenis_kelamin`, `no_whatsapp`, `email`, `alamat_pemasangan`, `titik_koordinat_maps`, `id_paket`, `id_area`, `status_pelanggan`, `tgl_pemasangan`, `created_at`) VALUES
(1, 'ICN-11001', '3578010101900001', 'Anton Syahputra', 'Laki-laki', '085100112233', 'anton@gmail.com', 'Jl. Lambung Mangkurat No 10', '-3.3166, 114.5901', 1, 1, 'Aktif', '2023-01-15', '2026-04-12 02:58:15'),
(2, 'ICN-11002', '3578010101900002', 'Bella Safira', 'Perempuan', '085100112234', 'bella@gmail.com', 'Jl. Brigjen H. Hasan Basri No 15', '-3.2981, 114.5843', 2, 2, 'Aktif', '2023-02-20', '2026-04-12 02:58:15'),
(3, 'ICN-11003', '3578010101900003', 'Citra Kirana', 'Perempuan', '085100112235', 'citra@gmail.com', 'Jl. A. Yani Km 33 No 45', '-3.4391, 114.8251', 3, 3, 'Aktif', '2023-03-10', '2026-04-12 02:58:15'),
(4, 'ICN-11004', '3578010101900004', 'Deni Sumargo', 'Laki-laki', '085100112236', 'deni@gmail.com', 'Jl. Sekumpul No 90', '-3.4256, 114.8562', 4, 4, 'Aktif', '2023-04-05', '2026-04-12 02:58:15'),
(5, 'ICN-11005', '3578010101900005', 'Eka Putra', 'Laki-laki', '085100112237', 'eka@gmail.com', 'Jl. Pancasila No 100', '-3.8012, 114.7671', 5, 5, 'Isolir', '2023-05-12', '2026-04-12 02:58:15'),
(6, 'ICN-11006', '3578010101900006', 'Fara Diba', 'Perempuan', '085100112238', 'fara@gmail.com', 'Jl. Tjilik Riwut Km 2', '-2.2023, 113.9142', 1, 6, 'Aktif', '2023-06-18', '2026-04-12 02:58:15'),
(7, 'ICN-11007', '3578010101900007', 'Gilang Dirga', 'Laki-laki', '085100112239', 'gilang@gmail.com', 'Jl. Diponegoro No 33', '-2.2134, 113.9198', 2, 7, 'Aktif', '2023-07-25', '2026-04-12 02:58:15'),
(8, 'ICN-11008', '3578010101900008', 'Hana Saraswati', 'Perempuan', '085100112240', 'hana@gmail.com', 'Jl. HM Arsyad No 12', '-2.5401, 112.9511', 3, 8, 'Aktif', '2023-08-30', '2026-04-12 02:58:15'),
(9, 'ICN-11009', '3578010101900009', 'Iwan Fals', 'Laki-laki', '085100112241', 'iwan@gmail.com', 'Jl. Pangeran Antasari No 5', '-2.6841, 111.6213', 4, 9, 'Cabut', '2023-09-14', '2026-04-12 02:58:15'),
(10, 'ICN-11010', '3578010101900010', 'Joko Anwar', 'Laki-laki', '085100112242', 'joko@gmail.com', 'Jl. Panglima Utar No 88', '-2.7351, 111.7321', 5, 10, 'Aktif', '2023-10-10', '2026-04-12 02:58:15'),
(11, 'ICN-11011', '3578010101900011', 'Kartika Putri', 'Perempuan', '085100112243', 'kartika@gmail.com', 'Jl. Merdeka No 11', '-3.3188, 114.5931', 1, 1, 'Aktif', '2024-01-05', '2026-04-12 02:58:15'),
(12, 'ICN-11012', '3578010101900012', 'Lukman Sardi', 'Laki-laki', '085100112244', 'lukman@gmail.com', 'Jl. Cemara Raya No 4', '-3.2941, 114.5872', 2, 2, 'Aktif', '2024-02-12', '2026-04-12 02:58:15'),
(13, 'ICN-11013', '3578010101900013', 'Maudy Ayunda', 'Perempuan', '085100112245', 'maudy@gmail.com', 'Jl. Panglima Batur No 1', '-3.4412, 114.8315', 3, 3, 'Isolir', '2024-03-20', '2026-04-12 02:58:15'),
(14, 'ICN-11014', '3578010101900014', 'Nino Fernandez', 'Laki-laki', '085100112246', 'nino@gmail.com', 'Jl. Menteri Empat No 2', '-3.4188, 114.8451', 4, 4, 'Aktif', '2024-04-18', '2026-04-12 02:58:15'),
(15, 'ICN-11015', '3578010101900015', 'Olla Ramlan', 'Perempuan', '085100112247', 'olla@gmail.com', 'Jl. Datu Daim No 9', '-3.8051, 114.7712', 5, 5, 'Aktif', '2024-05-22', '2026-04-12 02:58:15'),
(16, 'ICN-11016', '3578010101900016', 'Pandu Dewanata', 'Laki-laki', '085100112248', 'pandu@gmail.com', 'Jl. Yos Sudarso No 50', '-2.2115, 113.9012', 1, 6, 'Aktif', '2024-06-15', '2026-04-12 02:58:15'),
(17, 'ICN-11017', '3578010101900017', 'Qory Sandioriva', 'Perempuan', '085100112249', 'qory@gmail.com', 'Jl. G. Obos No 11', '-2.2281, 113.9145', 2, 7, 'Aktif', '2024-07-30', '2026-04-12 02:58:15'),
(18, 'ICN-11018', '3578010101900018', 'Reza Rahadian', 'Laki-laki', '085100112250', 'rezar@gmail.com', 'Jl. MT Haryono No 7', '-2.5356, 112.9554', 3, 8, 'Aktif', '2024-08-11', '2026-04-12 02:58:15'),
(19, 'ICN-11019', '3578010101900019', 'Sule Sutisna', 'Laki-laki', '085100112251', 'sule@gmail.com', 'Jl. Sutan Syahrir No 44', '-2.6811, 111.6254', 4, 9, 'Cabut', '2024-09-09', '2026-04-12 02:58:15'),
(20, 'ICN-11020', '3578010101900020', 'Tara Basro', 'Perempuan', '085100112252', 'tara@gmail.com', 'Jl. Bahari No 2', '-2.7315, 111.7351', 5, 10, 'Aktif', '2024-10-01', '2026-04-12 02:58:15'),
(21, 'ICN-11021', '3578010101900021', 'Umar Lubis', 'Laki-laki', '085100112253', 'umar@gmail.com', 'Jl. Veteran No 66', '-3.3211, 114.5951', 1, 1, 'Aktif', '2025-01-10', '2026-04-12 02:58:15'),
(22, 'ICN-11022', '3578010101900022', 'Vino G Bastian', 'Laki-laki', '085100112254', 'vino@gmail.com', 'Jl. Kayu Tangi No 9', '-3.2921, 114.5861', 4, 2, 'Isolir', '2025-02-14', '2026-04-12 02:58:15'),
(23, 'ICN-11023', '3578010101900023', 'Wulan Guritno', 'Perempuan', '085100112255', 'wulan@gmail.com', 'Jl. Karang Anyar No 12', '-3.4451, 114.8211', 2, 3, 'Aktif', '2025-03-21', '2026-04-12 02:58:15'),
(24, 'ICN-11024', '3578010101900024', 'Xander Setiawan', 'Laki-laki', '085100112256', 'xander@gmail.com', 'Jl. Tanjung Rema No 10', '-3.4215, 114.8511', 3, 4, 'Aktif', '2025-04-05', '2026-04-12 02:58:15'),
(25, 'ICN-11025', '3578010101900025', 'Yura Yunita', 'Perempuan', '085100112257', 'yura@gmail.com', 'Jl. Parit No 33', '-3.8066, 114.7731', 5, 5, 'Aktif', '2025-05-10', '2026-04-12 02:58:15');

-- --------------------------------------------------------

--
-- Table structure for table `pengaturan`
--

CREATE TABLE `pengaturan` (
  `id_pengaturan` int(11) NOT NULL,
  `nama_aplikasi` varchar(100) NOT NULL DEFAULT 'yHelpdesk Ticketing',
  `nama_instansi` varchar(150) NOT NULL DEFAULT 'PT. ICON+',
  `telp_instansi` varchar(20) NOT NULL,
  `email_instansi` varchar(100) NOT NULL,
  `alamat_instansi` text NOT NULL,
  `nama_pimpinan` varchar(100) NOT NULL,
  `background_login` varchar(255) DEFAULT NULL,
  `nama_dev` varchar(100) NOT NULL,
  `npm_dev` varchar(30) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengaturan`
--

INSERT INTO `pengaturan` (`id_pengaturan`, `nama_aplikasi`, `nama_instansi`, `telp_instansi`, `email_instansi`, `alamat_instansi`, `nama_pimpinan`, `background_login`, `nama_dev`, `npm_dev`, `updated_at`) VALUES
(1, 'Helpdesk Ticketing', 'PT. Indonesia Comnet Plus Palangkaraya', '0811-2233-4455', 'helpdesk@iconpln.co.id', 'Jl. Diponegoro No.18, Langkai, Kecamatan. Pahandut, Kota Palangka Raya, Kalimantan Tengah 74874', 'Bapak Manajer', 'bg_1775935599.jpeg', 'Muhammad Rizky Fadlani', '12345678', '2026-04-12 11:34:10');

-- --------------------------------------------------------

--
-- Table structure for table `spk_gejala`
--

CREATE TABLE `spk_gejala` (
  `id_gejala` int(11) NOT NULL,
  `kode_gejala` varchar(10) NOT NULL,
  `pertanyaan` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `spk_gejala`
--

INSERT INTO `spk_gejala` (`id_gejala`, `kode_gejala`, `pertanyaan`) VALUES
(1, 'G01', 'Lampu indikator LOS pada modem menyala atau berkedip MERAH.'),
(2, 'G02', 'Lampu indikator PON mati atau terus berkedip (tidak mau stabil/hijau).'),
(3, 'G03', 'Lampu indikator Power pada modem mati total (tidak ada lampu yang menyala sama sekali).'),
(4, 'G04', 'Lampu indikator WLAN / WiFi pada modem dalam keadaan mati.'),
(5, 'G05', 'Lampu indikator Internet mati, tetapi lampu indikator lainnya menyala normal.'),
(6, 'G06', 'Koneksi internet terasa sangat lambat (tidak sesuai dengan paket yang dilanggan).'),
(7, 'G07', 'Sering mengalami Ping tinggi / Lag / Patah-patah saat bermain game online atau Zoom meeting.'),
(8, 'G08', 'Nama WiFi (SSID) tidak terdeteksi atau tidak muncul di pengaturan HP/Laptop.'),
(9, 'G09', 'Gagal terhubung ke WiFi dengan keterangan \"Password Salah\" atau \"Authentication Error\".'),
(10, 'G10', 'Perangkat berhasil terhubung ke WiFi, namun muncul notifikasi \"Tidak Ada Koneksi Internet\" / \"No Internet Access\".'),
(11, 'G11', 'Koneksi internet sering terputus sendiri secara tiba-tiba lalu tersambung kembali (Putus Nyambung / Request Time Out).'),
(12, 'G12', 'Hanya website atau aplikasi tertentu saja yang tidak bisa dibuka (misal: YouTube bisa, tapi WhatsApp tidak bisa).'),
(13, 'G13', 'Lupa password WiFi atau lupa nama WiFi (SSID) saat ini.'),
(14, 'G14', 'Melihat langsung kondisi kabel fiber optik di luar rumah terputus atau menjuntai ke jalan.'),
(15, 'G15', 'Kabel optik warna kuning/hitam (Patchcord) di dalam rumah tertekuk tajam, tertindih barang, atau tergigit tikus.'),
(16, 'G16', 'Bodi modem terasa sangat panas saat disentuh (Overheating).'),
(17, 'G17', 'Jangkauan sinyal WiFi sangat lemah dan tidak menjangkau ruangan lain (misal: di kamar lantai 2 sinyal hilang).'),
(18, 'G18', 'Saat membuka browser, otomatis dialihkan ke halaman Peringatan \"Isolir\" atau \"Tagihan Belum Dibayar\".'),
(19, 'G19', 'Internet terasa lambat karena terlalu banyak orang/perangkat yang terhubung ke WiFi secara bersamaan.'),
(20, 'G20', 'Modem sering me-restart sendiri secara otomatis di tengah-tengah pemakaian.');

-- --------------------------------------------------------

--
-- Table structure for table `spk_rule_base`
--

CREATE TABLE `spk_rule_base` (
  `id_rule` int(11) NOT NULL,
  `id_solusi` int(11) NOT NULL,
  `id_gejala` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `spk_rule_base`
--

INSERT INTO `spk_rule_base` (`id_rule`, `id_solusi`, `id_gejala`) VALUES
(1, 1, 1),
(2, 14, 1),
(3, 15, 1),
(4, 2, 2),
(5, 5, 2),
(6, 3, 3),
(7, 14, 3),
(8, 4, 4),
(9, 2, 4),
(10, 2, 5),
(11, 5, 5),
(12, 9, 5),
(13, 6, 6),
(14, 12, 6),
(15, 2, 6),
(16, 7, 7),
(17, 6, 7),
(18, 12, 7),
(19, 4, 8),
(20, 2, 8),
(21, 8, 9),
(22, 12, 9),
(23, 2, 10),
(24, 9, 10),
(25, 13, 10),
(26, 11, 11),
(27, 15, 11),
(28, 7, 11),
(29, 13, 12),
(30, 2, 12),
(31, 8, 13),
(32, 14, 14),
(33, 1, 15),
(34, 14, 15),
(35, 11, 16),
(36, 10, 17),
(37, 7, 17),
(38, 9, 18),
(39, 12, 19),
(40, 8, 19),
(41, 11, 20),
(42, 3, 20);

-- --------------------------------------------------------

--
-- Table structure for table `spk_solusi`
--

CREATE TABLE `spk_solusi` (
  `id_solusi` int(11) NOT NULL,
  `kode_solusi` varchar(10) NOT NULL,
  `nama_solusi` varchar(100) NOT NULL,
  `tindakan_perbaikan` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `spk_solusi`
--

INSERT INTO `spk_solusi` (`id_solusi`, `kode_solusi`, `nama_solusi`, `tindakan_perbaikan`) VALUES
(1, 'S01', 'Pengecekan Fisik Kabel Fiber', '1. Pastikan kabel optik warna kuning/hitam (patchcord) yang menancap di belakang modem sudah terpasang dengan kuat dan berbunyi \"klik\".\r\n2. Cek sepanjang kabel di dalam rumah, pastikan tidak ada yang tertekuk tajam (bending), tertindih meja/lemari, atau digigit hewan.\r\n3. Jika kabel terlihat patah di dalam, segera laporkan ke teknisi.'),
(2, 'S02', 'Restart / Reboot Modem', '1. Matikan modem dengan menekan tombol Power di bagian belakang modem, atau cabut kabel adaptor dari stopkontak.\r\n2. Biarkan modem dalam keadaan mati selama kurang lebih 5 hingga 10 menit.\r\n3. Nyalakan kembali dan tunggu 3 menit hingga semua lampu indikator stabil.'),
(3, 'S03', 'Pengecekan Aliran Listrik (Adaptor)', '1. Pastikan stopkontak listrik di rumah Anda berfungsi normal.\r\n2. Coba pindahkan colokan adaptor modem ke stopkontak lain.\r\n3. Pastikan kabel adaptor menancap kuat pada port Power di belakang modem.\r\n4. Jika adaptor rusak/hangus, laporkan untuk penggantian perangkat.'),
(4, 'S04', 'Aktifkan Ulang Pemancar WiFi (WLAN)', '1. Cek tombol kecil bertuliskan \"WLAN\" atau \"WiFi\" di sisi samping atau belakang modem.\r\n2. Tekan dan tahan tombol tersebut selama 3-5 detik.\r\n3. Lepaskan dan perhatikan bagian depan modem, pastikan lampu indikator WLAN menyala warna hijau.'),
(5, 'S05', 'Cek Info Gangguan Massal', '1. Lakukan restart modem terlebih dahulu.\r\n2. Jika lampu PON berkedip terus atau lampu Internet mati dalam waktu lama, ada kemungkinan terjadi gangguan massal (Maintenance Jaringan Pusat).\r\n3. Tunggu estimasi perbaikan pusat, atau hubungi CS untuk konfirmasi area.'),
(6, 'S06', 'Tes Kecepatan Akurat (Speedtest)', '1. Untuk hasil paling akurat, gunakan laptop dan sambungkan langsung ke modem menggunakan Kabel LAN.\r\n2. Pastikan tidak ada perangkat lain yang sedang men-download file besar atau menonton video resolusi tinggi.\r\n3. Buka situs speedtest.net dan klik \"GO\".\r\n4. Jika hasil di bawah 50% dari paket langganan, lampirkan bukti tersebut saat membuat tiket.'),
(7, 'S07', 'Optimasi Sinyal & Channel WiFi', '1. Hindari meletakkan modem di dekat perangkat elektronik pemancar gelombang lain (seperti microwave, TV, atau radio).\r\n2. Jangan masukkan modem ke dalam laci atau lemari TV.\r\n3. Jika Anda mengerti seting, login ke panel admin modem dan ubah \"Channel WiFi\" ke angka 1, 6, atau 11 untuk menghindari tabrakan sinyal dengan WiFi tetangga.'),
(8, 'S08', 'Ubah / Reset Password WiFi', '1. Buka browser (Google Chrome/Safari) di HP yang tersambung ke WiFi, ketik 192.168.1.1 atau 192.168.100.1 di kolom URL.\r\n2. Masukkan Username dan Password admin modem Anda (biasanya ada di stiker bawah modem).\r\n3. Masuk ke menu Network / WLAN -> Security.\r\n4. Ubah \"WPA Passphrase\" atau \"Pre-Shared Key\" menjadi password baru yang lebih sulit.\r\n5. Klik Apply/Save.'),
(9, 'S09', 'Pembayaran Tagihan (Buka Isolir)', '1. Silakan periksa mutasi email, SMS, atau aplikasi resmi dari ISP Anda terkait info tagihan.\r\n2. Lakukan pelunasan pembayaran melalui m-Banking, minimarket, atau channel resmi lainnya.\r\n3. Setelah berhasil membayar, cabut kabel adaptor modem selama 5 menit, lalu nyalakan kembali untuk me-refresh sistem dan membuka isolir.'),
(10, 'S10', 'Penambahan WiFi Extender / Mesh', '1. Kemampuan pancaran sinyal modem bawaan biasanya terbatas hanya sejauh 10-15 meter tanpa halangan dinding tebal.\r\n2. Untuk rumah bertingkat atau berukuran besar, disarankan menggunakan perangkat tambahan \"WiFi Extender\" atau \"Router Mesh\".\r\n3. Pasang Extender tersebut di area tengah antara letak modem utama dan ruangan yang \"Blank Spot\".'),
(11, 'S11', 'Pendinginan Modem (Cooling Down)', '1. Matikan modem dengan mencabut kabel daya selama minimal 30 menit.\r\n2. Pastikan letak modem berada di area terbuka dengan sirkulasi udara yang baik (jangan ditaruh di tempat tertutup, berdebu, atau ditumpuk dengan barang panas seperti STB TV).\r\n3. Nyalakan kembali setelah modem tidak terasa panas.'),
(12, 'S12', 'Membatasi Pengguna Jaringan', '1. Lambatnya koneksi sering terjadi karena bandwidth paket sudah habis terbagi oleh banyak perangkat yang terkoneksi.\r\n2. Silakan ubah password WiFi Anda untuk \"mengusir\" pengguna asing (pembobol WiFi).\r\n3. Batasi aktivitas download berukuran besar secara bersamaan.'),
(13, 'S13', 'Clear Cache / Flush DNS', '1. Pada HP/Browser: Buka pengaturan browser, pilih \"Clear Browsing Data\" (Cache & Cookies), lalu coba akses kembali website tersebut.\r\n2. Pada PC/Laptop Windows: Buka CMD (Command Prompt), ketik perintah \"ipconfig /flushdns\" dan tekan Enter. Kemudian restart koneksi internet Anda.'),
(14, 'S14', 'Segera Buat Tiket & Panggil Teknisi', '1. Kondisi jaringan fisik terputus di tiang luar atau kabel patchcord putus tidak dapat diperbaiki sendiri karena membutuhkan alat penyambung khusus (Splicer).\r\n2. JANGAN mencoba menyambung sendiri serat optik karena serpihan kaca di dalamnya berbahaya.\r\n3. Amankan kabel, buat tiket di sistem ini agar teknisi segera meluncur membawa kabel pengganti.'),
(15, 'S15', 'Cek Redaman via Panel Admin', '1. Login ke panel Admin Modem via browser.\r\n2. Masuk ke menu Status / Device Information -> Optical Info.\r\n3. Perhatikan nilai \"Rx Optical Power\" atau \"Rx Power\".\r\n4. Jika angkanya lebih buruk dari -27 dBm (misal -30 atau -35 dBm), maka kabel dalam keadaan terjepit parah atau kotor. Segera hubungi teknisi.');

-- --------------------------------------------------------

--
-- Table structure for table `tiket`
--

CREATE TABLE `tiket` (
  `id_tiket` int(11) NOT NULL,
  `no_tiket` varchar(30) NOT NULL,
  `id_pelanggan` int(11) NOT NULL,
  `id_kategori` int(11) NOT NULL,
  `id_karyawan_teknisi` int(11) DEFAULT NULL COMMENT 'Relasi ke karyawan dengan jabatan Teknisi',
  `tgl_lapor` datetime DEFAULT current_timestamp(),
  `keluhan_detail` text NOT NULL,
  `status_tiket` enum('Menunggu Diproses','Teknisi Menuju Lokasi','Sedang Diperbaiki','Selesai','Dibatalkan') DEFAULT 'Menunggu Diproses',
  `catatan_teknisi` text DEFAULT NULL,
  `tgl_selesai` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tiket`
--

INSERT INTO `tiket` (`id_tiket`, `no_tiket`, `id_pelanggan`, `id_kategori`, `id_karyawan_teknisi`, `tgl_lapor`, `keluhan_detail`, `status_tiket`, `catatan_teknisi`, `tgl_selesai`) VALUES
(1, 'TKT-20260412-A1B2', 2, 1, 5, '2026-04-12 08:30:00', 'Internet tiba-tiba mati sejak semalam, lampu LOS di modem berkedip merah terus.', 'Selesai', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry&amp;amp;amp;#039;s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.', '2026-04-12 08:14:21'),
(2, 'TKT-20260412-C3D4', 7, 3, 6, '2026-04-12 09:15:00', 'Koneksi sering putus nyambung (RTO) terutama saat dipakai Zoom meeting pagi ini.', 'Teknisi Menuju Lokasi', NULL, NULL),
(3, 'TKT-20260411-E5F6', 14, 2, 10, '2026-04-11 13:45:00', 'Speed internet sangat lambat. Langganan 50Mbps tapi kalau di speedtest cuma dapat 5Mbps.', 'Sedang Diperbaiki', 'Sedang mengecek redaman kabel pada FAT dan konfigurasi bandwidth di sisi OLT.', NULL),
(4, 'TKT-20260408-G7H8', 5, 4, 5, '2026-04-08 10:00:00', 'Modem mati total, sudah dicoba pindah colokan ke stopkontak lain tetap tidak menyala sama sekali.', 'Selesai', 'Adaptor modem terdeteksi rusak/konslet akibat lonjakan listrik. Telah dilakukan penggantian adaptor baru (12V 1A) dan internet kembali normal.', '2026-04-08 14:30:00'),
(5, 'TKT-20260409-I9J0', 21, 5, 9, '2026-04-09 07:20:00', 'Kabel fiber optic di depan rumah putus karena tersangkut truk pasir tadi subuh, mohon segera ditangani.', 'Selesai', 'Telah dilakukan penarikan kabel Drop Core baru sejauh 50 meter dari tiang ODP ke rumah pelanggan. Proses Splicing berhasil, nilai redaman sangat baik di angka -19 dBm. Koneksi OK.', '2026-04-09 11:45:00');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `id_karyawan` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role_akses` enum('Super Admin','Admin Dispatcher','Teknisi','Pimpinan') NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `id_karyawan`, `username`, `password`, `role_akses`, `last_login`, `is_active`) VALUES
(1, 1, 'admin', 'admin', 'Super Admin', NULL, 1),
(2, 2, 'admin_siti', 'admin_siti', 'Admin Dispatcher', NULL, 1),
(3, 4, 'manager1', '12345', 'Pimpinan', NULL, 1),
(4, 5, 'wahyu', 'wahyu', 'Teknisi', NULL, 1),
(5, 6, 'teknisi_dodi', '12345', 'Teknisi', NULL, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `area_cover`
--
ALTER TABLE `area_cover`
  ADD PRIMARY KEY (`id_area`);

--
-- Indexes for table `foto_tiket`
--
ALTER TABLE `foto_tiket`
  ADD PRIMARY KEY (`id_foto`),
  ADD KEY `id_tiket` (`id_tiket`);

--
-- Indexes for table `jabatan`
--
ALTER TABLE `jabatan`
  ADD PRIMARY KEY (`id_jabatan`);

--
-- Indexes for table `karyawan`
--
ALTER TABLE `karyawan`
  ADD PRIMARY KEY (`id_karyawan`),
  ADD UNIQUE KEY `nip_karyawan` (`nip_karyawan`),
  ADD KEY `id_jabatan` (`id_jabatan`);

--
-- Indexes for table `kategori_gangguan`
--
ALTER TABLE `kategori_gangguan`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indexes for table `paket_layanan`
--
ALTER TABLE `paket_layanan`
  ADD PRIMARY KEY (`id_paket`);

--
-- Indexes for table `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD PRIMARY KEY (`id_pelanggan`),
  ADD UNIQUE KEY `no_langganan` (`no_langganan`),
  ADD UNIQUE KEY `nik_ktp` (`nik_ktp`),
  ADD KEY `id_paket` (`id_paket`),
  ADD KEY `id_area` (`id_area`);

--
-- Indexes for table `pengaturan`
--
ALTER TABLE `pengaturan`
  ADD PRIMARY KEY (`id_pengaturan`);

--
-- Indexes for table `spk_gejala`
--
ALTER TABLE `spk_gejala`
  ADD PRIMARY KEY (`id_gejala`),
  ADD UNIQUE KEY `kode_gejala` (`kode_gejala`);

--
-- Indexes for table `spk_rule_base`
--
ALTER TABLE `spk_rule_base`
  ADD PRIMARY KEY (`id_rule`),
  ADD KEY `id_solusi` (`id_solusi`),
  ADD KEY `id_gejala` (`id_gejala`);

--
-- Indexes for table `spk_solusi`
--
ALTER TABLE `spk_solusi`
  ADD PRIMARY KEY (`id_solusi`),
  ADD UNIQUE KEY `kode_solusi` (`kode_solusi`);

--
-- Indexes for table `tiket`
--
ALTER TABLE `tiket`
  ADD PRIMARY KEY (`id_tiket`),
  ADD UNIQUE KEY `no_tiket` (`no_tiket`),
  ADD KEY `id_pelanggan` (`id_pelanggan`),
  ADD KEY `id_kategori` (`id_kategori`),
  ADD KEY `id_karyawan_teknisi` (`id_karyawan_teknisi`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `id_karyawan` (`id_karyawan`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `area_cover`
--
ALTER TABLE `area_cover`
  MODIFY `id_area` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `foto_tiket`
--
ALTER TABLE `foto_tiket`
  MODIFY `id_foto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `jabatan`
--
ALTER TABLE `jabatan`
  MODIFY `id_jabatan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `karyawan`
--
ALTER TABLE `karyawan`
  MODIFY `id_karyawan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `kategori_gangguan`
--
ALTER TABLE `kategori_gangguan`
  MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `paket_layanan`
--
ALTER TABLE `paket_layanan`
  MODIFY `id_paket` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `pelanggan`
--
ALTER TABLE `pelanggan`
  MODIFY `id_pelanggan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `pengaturan`
--
ALTER TABLE `pengaturan`
  MODIFY `id_pengaturan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `spk_gejala`
--
ALTER TABLE `spk_gejala`
  MODIFY `id_gejala` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `spk_rule_base`
--
ALTER TABLE `spk_rule_base`
  MODIFY `id_rule` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `spk_solusi`
--
ALTER TABLE `spk_solusi`
  MODIFY `id_solusi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `tiket`
--
ALTER TABLE `tiket`
  MODIFY `id_tiket` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `foto_tiket`
--
ALTER TABLE `foto_tiket`
  ADD CONSTRAINT `foto_tiket_ibfk_1` FOREIGN KEY (`id_tiket`) REFERENCES `tiket` (`id_tiket`) ON DELETE CASCADE;

--
-- Constraints for table `karyawan`
--
ALTER TABLE `karyawan`
  ADD CONSTRAINT `karyawan_ibfk_1` FOREIGN KEY (`id_jabatan`) REFERENCES `jabatan` (`id_jabatan`);

--
-- Constraints for table `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD CONSTRAINT `pelanggan_ibfk_1` FOREIGN KEY (`id_paket`) REFERENCES `paket_layanan` (`id_paket`),
  ADD CONSTRAINT `pelanggan_ibfk_2` FOREIGN KEY (`id_area`) REFERENCES `area_cover` (`id_area`);

--
-- Constraints for table `spk_rule_base`
--
ALTER TABLE `spk_rule_base`
  ADD CONSTRAINT `spk_rule_base_ibfk_1` FOREIGN KEY (`id_solusi`) REFERENCES `spk_solusi` (`id_solusi`) ON DELETE CASCADE,
  ADD CONSTRAINT `spk_rule_base_ibfk_2` FOREIGN KEY (`id_gejala`) REFERENCES `spk_gejala` (`id_gejala`) ON DELETE CASCADE;

--
-- Constraints for table `tiket`
--
ALTER TABLE `tiket`
  ADD CONSTRAINT `tiket_ibfk_1` FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan` (`id_pelanggan`) ON DELETE CASCADE,
  ADD CONSTRAINT `tiket_ibfk_2` FOREIGN KEY (`id_kategori`) REFERENCES `kategori_gangguan` (`id_kategori`),
  ADD CONSTRAINT `tiket_ibfk_3` FOREIGN KEY (`id_karyawan_teknisi`) REFERENCES `karyawan` (`id_karyawan`) ON DELETE SET NULL;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`id_karyawan`) REFERENCES `karyawan` (`id_karyawan`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 12, 2026 at 04:05 PM
-- Server version: 10.11.16-MariaDB-cll-lve
-- PHP Version: 8.4.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mitt8154_edulampung`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama` varchar(150) DEFAULT NULL,
  `role` enum('admin','guru','sekolah') NOT NULL DEFAULT 'admin',
  `sekolah_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- --------------------------------------------------------

--
-- Table structure for table `admin_sessions`
--

CREATE TABLE `admin_sessions` (
  `token` varchar(64) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `expires_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `classroom_kelas_stats`
--

CREATE TABLE `classroom_kelas_stats` (
  `id` int(11) NOT NULL,
  `sekolah_kelas_id` int(11) NOT NULL,
  `bulan` char(7) NOT NULL COMMENT 'format YYYY-MM',
  `jumlah_guru` int(11) NOT NULL DEFAULT 0,
  `jumlah_siswa` int(11) NOT NULL DEFAULT 0,
  `jumlah_task` int(11) NOT NULL DEFAULT 0 COMMENT 'courseWork yang dibuat guru bulan ini',
  `jumlah_materi` int(11) NOT NULL DEFAULT 0 COMMENT 'courseWorkMaterials (upload) bulan ini',
  `synced_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jurnal`
--

CREATE TABLE `jurnal` (
  `id` int(11) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `kategori` varchar(100) NOT NULL,
  `penulis` varchar(255) NOT NULL,
  `abstrak` text DEFAULT NULL,
  `jumlah_halaman` int(11) NOT NULL DEFAULT 0,
  `tahun_terbit` int(11) NOT NULL DEFAULT 2026,
  `volume` varchar(50) DEFAULT NULL,
  `nomor_edisi` varchar(50) DEFAULT NULL,
  `issn` varchar(50) DEFAULT NULL,
  `kata_kunci` varchar(255) DEFAULT NULL,
  `bahasa` varchar(30) NOT NULL DEFAULT 'Indonesia',
  `file_jurnal` varchar(500) NOT NULL,
  `file_bukti_plagiarisme` varchar(500) NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `catatan_admin` text DEFAULT NULL,
  `admin_id` int(11) NOT NULL COMMENT 'akun penulis yang mengajukan',
  `reviewed_by` int(11) DEFAULT NULL COMMENT 'admin yang approve/reject',
  `reviewed_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jurnal_kategori`
--

CREATE TABLE `jurnal_kategori` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `jurnal_kategori`
--

INSERT INTO `jurnal_kategori` (`id`, `nama`, `created_at`) VALUES
(1, 'Pendidikan', '2026-07-07 16:03:09'),
(2, 'Sains & Teknologi', '2026-07-07 16:03:09');

-- --------------------------------------------------------

--
-- Table structure for table `mata_pelajaran`
--

CREATE TABLE `mata_pelajaran` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `jenjang` enum('sma','smk','slb') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mata_pelajaran`
--

INSERT INTO `mata_pelajaran` (`id`, `nama`, `jenjang`) VALUES
(5, 'Bahasa Indonesia', 'sma'),
(17, 'Bahasa Indonesia', 'smk'),
(26, 'Bahasa Indonesia', 'slb'),
(6, 'Bahasa Inggris', 'sma'),
(18, 'Bahasa Inggris', 'smk'),
(31, 'Bina Diri', 'slb'),
(4, 'Biologi', 'sma'),
(9, 'Ekonomi', 'sma'),
(2, 'Fisika', 'sma'),
(8, 'Geografi', 'sma'),
(13, 'Informatika', 'sma'),
(20, 'Informatika', 'smk'),
(27, 'IPA', 'slb'),
(19, 'IPA Terapan', 'smk'),
(28, 'IPS', 'slb'),
(3, 'Kimia', 'sma'),
(1, 'Matematika', 'sma'),
(16, 'Matematika', 'smk'),
(25, 'Matematika', 'slb'),
(14, 'PAI', 'sma'),
(22, 'PAI', 'smk'),
(32, 'PAI', 'slb'),
(34, 'Pancasila', 'sma'),
(35, 'Pancasila', 'smk'),
(36, 'Pancasila', 'slb'),
(12, 'PJOK', 'sma'),
(23, 'PJOK', 'smk'),
(30, 'PJOK', 'slb'),
(15, 'PKN', 'sma'),
(21, 'PKN', 'smk'),
(24, 'Produktif Keahlian', 'smk'),
(7, 'Sejarah', 'sma'),
(11, 'Seni Budaya', 'sma'),
(29, 'Seni Budaya', 'slb'),
(10, 'Sosiologi', 'sma');

-- --------------------------------------------------------

--
-- Table structure for table `materi`
--

CREATE TABLE `materi` (
  `id` int(11) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `tipe` enum('video','ppt','pdf') NOT NULL,
  `jenjang` enum('sma','smk','slb') NOT NULL,
  `mapel_id` int(11) NOT NULL,
  `url` varchar(500) NOT NULL,
  `thumbnail` varchar(500) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `materi`
--

INSERT INTO `materi` (`id`, `judul`, `deskripsi`, `tipe`, `jenjang`, `mapel_id`, `url`, `thumbnail`, `created_at`, `updated_at`, `is_active`) VALUES
(6, 'Momentum dan Impuls', 'Materi Momentum dan Impuls untuk kelas 11 mempelajari besaran yang berkaitan dengan gerak dan gaya kontak singkat, seperti tabrakan. Momentum mengukur kesukaran menghentikan benda bergerak, sedangkan Impuls adalah perubahan momentum. Keduanya berkaitan erat dengan Hukum Kekekalan Momentum dan jenis-jenis tumbukan', 'video', 'sma', 2, 'https://youtu.be/B6tVwew-bjE', 'https://img.youtube.com/vi/B6tVwew-bjE/hqdefault.jpg', '2026-06-07 20:18:40', '2026-06-20 08:51:20', 1),
(7, 'Budgeting Basics', 'This educational video on \"Budgeting Basics\" aims to enhance students\' literacy and numeracy skills by exploring the essential elements of creating and managing a budget. Through engaging visuals, clear explanations, and practical examples, students will learn how to track income and expenses, set financial goals, and make informed decisions to achieve financial stability. This video is designed for middle to high school students and emphasizes real-life applications of budgeting principles', 'video', 'smk', 18, 'https://youtu.be/cJdhW9Cg33M', 'https://img.youtube.com/vi/cJdhW9Cg33M/hqdefault.jpg', '2026-06-07 20:35:48', '2026-06-20 08:50:05', 1),
(9, 'Kelompol Sosial', 'Kelompok sosial adalah kumpulan individu yang memiliki kesadaran bersama akan keanggotaan, saling berinteraksi, dan memiliki tujuan atau nilai yang sama. Kumpulan ini bukan sekadar kerumunan, melainkan memiliki ikatan timbal balik yang membentuk struktur dan norma tertentu.', 'video', 'sma', 10, 'https://youtu.be/h6eecQsxQYk', 'https://img.youtube.com/vi/h6eecQsxQYk/hqdefault.jpg', '2026-06-21 10:27:46', '2026-06-21 10:27:46', 1),
(10, 'Larutan Penyangga', 'Dalam kimia, buffer (atau larutan penyangga) adalah larutan yang berfungsi untuk mempertahankan nilai pH agar tetap stabil, bahkan jika ditambahkan sedikit asam, sedikit basa, atau diencerkan.', 'video', 'sma', 3, 'https://youtu.be/dhi0_xv2fIA', 'https://img.youtube.com/vi/dhi0_xv2fIA/hqdefault.jpg', '2026-06-21 10:30:26', '2026-06-21 10:30:26', 1),
(11, 'Pancasila Sikap Demokratis', 'Sikap demokratis Pancasila adalah tindakan yang berlandaskan nilai-nilai luhur ideologi negara, yang mengutamakan musyawarah mufakat, kekeluargaan, dan gotong royong. Sikap ini menyeimbangkan antara hak dan kewajiban dengan menghargai pendapat orang lain tanpa memaksakan kehendak demi tercapainya keadilan sosial.', 'video', 'sma', 34, 'https://www.youtube.com/watch?v=3g875koQZQc', 'https://img.youtube.com/vi/3g875koQZQc/hqdefault.jpg', '2026-06-26 09:25:29', '2026-06-27 13:37:52', 1),
(12, 'Pancasila Kreativitas Nilai Pancasila', 'Pancasila adalah dasar negara serta falsafah bangsa Indonesia. Sebagai ideologi Pancasila, Pancasila menjadi pedoman hidup yang dinamis. Kreativitas berakar pada Pancasila saat ide dan karya yang orisinal, bermanfaat, dan berdampak luas selaras dengan nilai-nilai luhur kemanusiaan, persatuan, dan keadilan', 'video', 'sma', 34, 'https://www.youtube.com/watch?v=3IVO3iiEyVo', 'https://img.youtube.com/vi/3IVO3iiEyVo/hqdefault.jpg', '2026-06-26 09:27:08', '2026-06-27 13:37:52', 1),
(13, 'MTK Permutasian Kombinasi', 'Dalam matematika, permutasi dan kombinasi adalah metode penyusunan dan pemilihan objek dari sebuah himpunan. Inti perbedaannya terletak pada urutan: permutasi memperhatikan urutan, sedangkan kombinasi tidak.', 'video', 'sma', 1, 'https://www.youtube.com/watch?v=CPAoA00EoY4', 'https://img.youtube.com/vi/CPAoA00EoY4/hqdefault.jpg', '2026-06-26 09:29:23', '2026-06-26 09:29:23', 1),
(14, 'MATRIK', 'Matriks dalam matematika adalah susunan bilangan, simbol, atau ekspresi yang diatur dalam baris dan kolom sehingga membentuk bangun persegi atau persegi panjang. Susunan ini biasanya dibatasi dengan tanda kurung siku \'\\([]\\)\' atau kurung biasa \'()', 'video', 'sma', 1, 'https://www.youtube.com/watch?v=h09av1Aidtw', 'https://img.youtube.com/vi/h09av1Aidtw/hqdefault.jpg', '2026-06-26 09:30:57', '2026-06-26 09:30:57', 1),
(15, 'House keeping', NULL, 'video', 'smk', 24, 'https://www.youtube.com/watch?v=8vMRE0nK0U4', 'https://img.youtube.com/vi/8vMRE0nK0U4/hqdefault.jpg', '2026-06-26 09:34:34', '2026-06-26 09:34:34', 1),
(16, 'Silent reading', 'Silent reading (atau membaca senyap) adalah kegiatan membaca dalam hati atau tanpa mengeluarkan suara, di mana pembaca hanya menggunakan mata untuk memproses teks. Metode ini bertujuan penuh untuk memahami makna bacaan dengan lebih cepat dan melatih fokus tanpa gangguan', 'video', 'smk', 18, 'https://youtu.be/DejahfjFccU', 'https://img.youtube.com/vi/DejahfjFccU/hqdefault.jpg', '2026-06-28 16:07:14', '2026-06-28 16:07:14', 1),
(17, 'Laju Reaksi', 'Laju reaksi adalah ukuran seberapa cepat atau lambat suatu reaksi kimia berlangsung. Materi ini mempelajari tentang perubahan konsentrasi pereaksi (reaktan) yang berkurang atau hasil reaksi (produk) yang bertambah dalam setiap satuan waktu', 'video', 'sma', 3, 'https://youtu.be/RBEFXY041cg', 'https://img.youtube.com/vi/RBEFXY041cg/hqdefault.jpg', '2026-06-28 16:22:10', '2026-06-28 16:22:10', 1),
(18, 'Table setup (tata meja)', 'Table setup (tata meja) adalah rangkaian kegiatan dalam industri perhotelan dan tata boga untuk mengatur dan melengkapi meja makan menggunakan peralatan standar seperti linen, piring, gelas, dan alat makan (cutlery). Penataan ini disesuaikan dengan jenis hidangan yang akan disajikan, dengan tujuan untuk menjamin kenyamanan tamu dan meningkatkan efisiensi pelayanan pramusaji.', 'video', 'smk', 24, 'https://youtu.be/OdKxnxxSSHc', 'https://img.youtube.com/vi/OdKxnxxSSHc/hqdefault.jpg', '2026-06-28 16:28:31', '2026-06-28 16:28:31', 1),
(19, 'Jenis Jenis Pakan Ikan', 'Materi Jenis-Jenis Pakan Ikan pada jurusan perikanan SMK mempelajari klasifikasi, nutrisi, dan teknik produksi pakan. Fokus utamanya adalah memahami perbedaan antara pakan alami yang kaya nutrisi untuk larva ikan, dan pakan buatan yang diformulasikan secara khusus untuk mendukung pertumbuhan optimal.', 'video', 'smk', 24, 'https://www.youtube.com/watch?v=r2RbpPbYxE8', 'https://img.youtube.com/vi/r2RbpPbYxE8/hqdefault.jpg', '2026-06-30 13:31:05', '2026-06-30 13:31:05', 1),
(20, 'Nail Art', 'Materi Nail Art pada mata pelajaran Tata Kecantikan SMK adalah kompetensi yang mempelajari keterampilan dasar hingga lanjutan dalam menghias, melukis, dan mempercantik kuku. Peserta didik dibekali kemampuan menganalisis desain, teknik perawatan, hingga pengaplikasian seni kuku untuk mendukung peluang usaha di industri kecantikan.', 'video', 'smk', 24, 'https://www.youtube.com/watch?v=A4LGyqZxIvM', 'https://img.youtube.com/vi/A4LGyqZxIvM/hqdefault.jpg', '2026-06-30 13:32:26', '2026-06-30 13:32:26', 1),
(21, 'Roti Gandum', 'Materi roti gandum dalam kurikulum Tata Boga SMK mempelajari teknik pengolahan roti sehat tinggi serat. Siswa praktik langsung memilih bahan (tepung gandum utuh, ragi, air, garam, lemak), teknik mixing, fermentasi, hingga pemanggangan dengan standar suhu oven 218 °C selama 25 menit.', 'video', 'smk', 24, 'https://www.youtube.com/watch?v=UafFVljVfgM', 'https://img.youtube.com/vi/UafFVljVfgM/hqdefault.jpg', '2026-06-30 13:33:39', '2026-06-30 13:33:39', 1),
(22, 'Membuat Bolu Tape Panggang', 'Materi Bolu Tape Panggang dalam kurikulum Jurusan SMK Tata Boga adalah praktik pengolahan pastry dan bakery untuk menguasai teknik pembuatan cake berbasis fermentasi lokal. Siswa belajar mengolah tape singkong menjadi produk bernilai jual tinggi dengan memperhatikan standar industri, tekstur yang lembut, dan keseimbangan rasa.', 'video', 'smk', 24, 'https://www.youtube.com/watch?v=UtvpgVo_eFE', 'https://img.youtube.com/vi/UtvpgVo_eFE/hqdefault.jpg', '2026-06-30 13:34:46', '2026-06-30 13:34:46', 1),
(23, 'Sistem Saraf Pusat Tepi', 'Materi Sistem Saraf Pusat dan Tepi adalah bagian dari sistem koordinasi manusia yang dipelajari di kelas XI SMA. Materi ini berfokus pada bagaimana otak, sumsum tulang belakang, dan jaringan saraf bekerja sama menerima rangsangan, mengolah informasi, dan menghasilkan respons atau gerakan.', 'video', 'sma', 4, 'https://www.youtube.com/watch?v=0YdUjN0hlqw', 'https://img.youtube.com/vi/0YdUjN0hlqw/hqdefault.jpg', '2026-06-30 13:36:08', '2026-06-30 13:36:08', 1),
(24, 'SISTEM GERAK PADA MANUSIA', 'Materi Sistem Gerak pada Manusia dalam pelajaran Biologi SMA adalah kajian mengenai bagaimana tubuh manusia bekerja sama untuk melakukan aktivitas fisik. Sistem ini menjadikan tulang sebagai alat gerak pasif dan otot sebagai alat gerak aktif, yang dihubungkan oleh sendi.', 'video', 'sma', 4, 'https://www.youtube.com/watch?v=QXWgMGyiaLE', 'https://img.youtube.com/vi/QXWgMGyiaLE/hqdefault.jpg', '2026-06-30 13:37:07', '2026-06-30 13:37:07', 1),
(25, 'Manfaat Sejarah', 'Mempelajari sejarah melatih cara berpikir kritis, analitis, dan kronologis. Hal ini membantu kita memahami identitas diri, mengambil pelajaran dari peristiwa masa lampau, dan membuat keputusan yang lebih bijak untuk masa depan.', 'video', 'sma', 7, 'https://www.youtube.com/watch?v=JRgdsltyNXs&list=PLtZRyzyp5mQK6c-reZghm0PxEjT2QMQir', 'https://img.youtube.com/vi/JRgdsltyNXs/hqdefault.jpg', '2026-06-30 13:39:19', '2026-06-30 13:39:19', 1);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sekolah`
--

CREATE TABLE `sekolah` (
  `id` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `jenjang` enum('sma','smk','slb') NOT NULL,
  `kota_kabupaten` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sekolah`
--

INSERT INTO `sekolah` (`id`, `nama`, `jenjang`, `kota_kabupaten`, `is_active`) VALUES
(5, 'SMAN 1 Bandar Lampung', 'sma', 'Bandar Lampung', 1);

-- --------------------------------------------------------

--
-- Table structure for table `sekolah_kelas`
--

CREATE TABLE `sekolah_kelas` (
  `id` int(11) NOT NULL,
  `sekolah_id` int(11) NOT NULL,
  `mapel_id` int(11) NOT NULL,
  `classroom_url` varchar(500) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `fk_admin_sekolah` (`sekolah_id`);

--
-- Indexes for table `admin_sessions`
--
ALTER TABLE `admin_sessions`
  ADD PRIMARY KEY (`token`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `classroom_kelas_stats`
--
ALTER TABLE `classroom_kelas_stats`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_kelas_bulan` (`sekolah_kelas_id`,`bulan`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jurnal`
--
ALTER TABLE `jurnal`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_jurnal_status` (`status`),
  ADD KEY `idx_jurnal_kategori` (`kategori`),
  ADD KEY `idx_jurnal_admin` (`admin_id`),
  ADD KEY `jurnal_reviewer_fk` (`reviewed_by`);

--
-- Indexes for table `jurnal_kategori`
--
ALTER TABLE `jurnal_kategori`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `jurnal_kategori_nama_unique` (`nama`);

--
-- Indexes for table `mata_pelajaran`
--
ALTER TABLE `mata_pelajaran`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_mapel` (`nama`,`jenjang`),
  ADD KEY `idx_mapel_jenjang` (`jenjang`);

--
-- Indexes for table `materi`
--
ALTER TABLE `materi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_materi_jenjang` (`jenjang`),
  ADD KEY `idx_materi_tipe` (`tipe`),
  ADD KEY `idx_materi_mapel` (`mapel_id`),
  ADD KEY `idx_materi_active` (`is_active`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `sekolah`
--
ALTER TABLE `sekolah`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_sekolah_jenjang` (`jenjang`);

--
-- Indexes for table `sekolah_kelas`
--
ALTER TABLE `sekolah_kelas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_sekolah_mapel` (`sekolah_id`,`mapel_id`),
  ADD KEY `idx_sekolah_kelas_sekolah` (`sekolah_id`),
  ADD KEY `idx_sekolah_kelas_mapel` (`mapel_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `classroom_kelas_stats`
--
ALTER TABLE `classroom_kelas_stats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jurnal`
--
ALTER TABLE `jurnal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `jurnal_kategori`
--
ALTER TABLE `jurnal_kategori`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `mata_pelajaran`
--
ALTER TABLE `mata_pelajaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `materi`
--
ALTER TABLE `materi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `sekolah`
--
ALTER TABLE `sekolah`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `sekolah_kelas`
--
ALTER TABLE `sekolah_kelas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin`
--
ALTER TABLE `admin`
  ADD CONSTRAINT `fk_admin_sekolah` FOREIGN KEY (`sekolah_id`) REFERENCES `sekolah` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `admin_sessions`
--
ALTER TABLE `admin_sessions`
  ADD CONSTRAINT `admin_sessions_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `classroom_kelas_stats`
--
ALTER TABLE `classroom_kelas_stats`
  ADD CONSTRAINT `classroom_kelas_stats_ibfk_1` FOREIGN KEY (`sekolah_kelas_id`) REFERENCES `sekolah_kelas` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `jurnal`
--
ALTER TABLE `jurnal`
  ADD CONSTRAINT `jurnal_admin_fk` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jurnal_reviewer_fk` FOREIGN KEY (`reviewed_by`) REFERENCES `admin` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `materi`
--
ALTER TABLE `materi`
  ADD CONSTRAINT `materi_ibfk_1` FOREIGN KEY (`mapel_id`) REFERENCES `mata_pelajaran` (`id`);

--
-- Constraints for table `sekolah_kelas`
--
ALTER TABLE `sekolah_kelas`
  ADD CONSTRAINT `fk_sekolah_kelas_mapel` FOREIGN KEY (`mapel_id`) REFERENCES `mata_pelajaran` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_sekolah_kelas_sekolah` FOREIGN KEY (`sekolah_id`) REFERENCES `sekolah` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

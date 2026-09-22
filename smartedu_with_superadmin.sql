-- phpMyAdmin SQL Dump - COMPLETE SETUP FOR SMAGAEDU WITH SUPERADMIN
-- Run this file to set up the database with centralized role system

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `smartedu`
--

-- --------------------------------------------------------
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(50) NOT NULL,
  `deskripsi` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `nama` (`nama`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert default roles
INSERT INTO `roles` (`nama`, `deskripsi`) VALUES
('superadmin', 'Akses penuh ke semua fitur sistem'),
('admin', 'Admin sekolah - mengelola data utama'),
('guru', 'Guru - mengelola materi dan ujian'),
('siswa', 'Siswa - mengakses materi dan mengumpulkan tugas');

-- --------------------------------------------------------
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
CREATE TABLE `permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `deskripsi` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert default permissions
INSERT INTO `permissions` (`nama`, `deskripsi`) VALUES
('manage_users', 'Kelola semua pengguna'),
('manage_mapel', 'Kelola mata pelajaran'),
('manage_materi', 'Kelola materi pembelajaran'),
('manage_ujian', 'Kelola ujian'),
('manage_proyek', 'Kelola proyek/tugas'),
('view_reports', 'Lihat laporan dan statistik'),
('manage_settings', 'Kelola pengaturan sistem');

-- --------------------------------------------------------
-- Table structure for table `role_permissions`
--

DROP TABLE IF EXISTS `role_permissions`;
CREATE TABLE `role_permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `role_permission` (`role_id`,`permission_id`),
  CONSTRAINT `role_permissions_role_id` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_permissions_permission_id` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Set permission untuk superadmin (semua permission)
INSERT INTO `role_permissions` (`role_id`, `permission_id`) 
SELECT r.id, p.id 
FROM roles r, permissions p 
WHERE r.nama = 'superadmin';

-- Set permission untuk admin
INSERT INTO `role_permissions` (`role_id`, `permission_id`) 
SELECT r.id, p.id 
FROM roles r, permissions p 
WHERE r.nama = 'admin' AND p.nama IN ('manage_users', 'manage_mapel', 'manage_materi', 'manage_ujian', 'manage_proyek');

-- Set permission untuk guru
INSERT INTO `role_permissions` (`role_id`, `permission_id`) 
SELECT r.id, p.id 
FROM roles r, permissions p 
WHERE r.nama = 'guru' AND p.nama IN ('manage_materi', 'manage_ujian', 'manage_proyek');

-- Set permission untuk siswa
INSERT INTO `role_permissions` (`role_id`, `permission_id`) 
SELECT r.id, p.id 
FROM roles r, permissions p 
WHERE r.nama = 'siswa' AND p.nama = 'view_reports';

-- --------------------------------------------------------
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(100) NOT NULL,
  `role_id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `status` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  `created_by` varchar(100) DEFAULT NULL,
  `modified_at` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `username` (`username`),
  KEY `role_id` (`role_id`),
  CONSTRAINT `users_role_id` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for table `user_profiles`
--

DROP TABLE IF EXISTS `user_profiles`;
CREATE TABLE `user_profiles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `nip` varchar(50) DEFAULT NULL,
  `nis` varchar(50) DEFAULT NULL,
  `tgl_lahir` date DEFAULT NULL,
  `jenis_kelamin` enum('L','P') DEFAULT NULL,
  `mapel_uuid` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  KEY `mapel_uuid` (`mapel_uuid`),
  CONSTRAINT `user_profiles_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- BUAT USER SUPERADMIN PERTAMA
-- Password: superadmin123
-- Silakan ganti password setelah login pertama!
-- --------------------------------------------------------

INSERT INTO `users` (`uuid`, `role_id`, `nama`, `username`, `password`, `email`, `status`) VALUES
(UUID(), 1, 'Super Admin', 'superadmin', '$2y$10$nntygBg3bburFDsTZiob3eXrXqBd1Q7mq1leIqxwDxT4pbziFKOJi', 'superadmin@sekolah.ac.id', 'aktif');

-- --------------------------------------------------------
-- VIEW untuk kompatibilitas lama (opsional)
-- --------------------------------------------------------

-- View admin & superadmin untuk kompatibilitas
DROP VIEW IF EXISTS `admin_view`;
CREATE VIEW `admin_view` AS 
SELECT u.id, u.uuid, u.nama, u.username, u.password, u.created_by, u.modified_at, u.deleted_at
FROM users u 
JOIN roles r ON u.role_id = r.id 
WHERE r.nama IN ('superadmin', 'admin');

-- View guru untuk kompatibilitas
DROP VIEW IF EXISTS `guru_view`;
CREATE VIEW `guru_view` AS 
SELECT u.id, u.uuid, u.nama, u.username, u.password, up.mapel_uuid, up.jenis_kelamin, u.created_by, u.modified_at, u.deleted_at
FROM users u 
JOIN roles r ON u.role_id = r.id 
LEFT JOIN user_profiles up ON u.id = up.user_id
WHERE r.nama = 'guru';

-- View siswa untuk kompatibilitas
DROP VIEW IF EXISTS `siswa_view`;
CREATE VIEW `siswa_view` AS 
SELECT u.id, u.uuid, up.nis, u.nama, u.username, u.password, up.tgl_lahir, up.jenis_kelamin, u.created_by, u.modified_at, u.deleted_at
FROM users u 
JOIN roles r ON u.role_id = r.id 
LEFT JOIN user_profiles up ON u.id = up.user_id
WHERE r.nama = 'siswa';

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
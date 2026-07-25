-- phpMyAdmin SQL Dump - UPDATED VERSION WITH CENTRALIZED USERS TABLE
-- For: Superadmin, Admin, Guru, Siswa Roles

-- Tabel roles untuk mengatur hak akses
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

-- Tabel users terpusat - menggantikan tabel admin, guru, siswa terpisah
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

-- Tabel data tambahan untuk user (bisa dihubungkan ke semua role)
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

-- Buat view untuk kompatibilitas dengan kode lama (jika diperlukan)
-- View admin untuk kompatibilitas
CREATE VIEW `admin_view` AS 
SELECT u.id, u.uuid, u.nama, u.username, u.password, u.created_by, u.modified_at, u.deleted_at
FROM users u 
JOIN roles r ON u.role_id = r.id 
WHERE r.nama IN ('superadmin', 'admin');

-- View guru untuk kompatibilitas
CREATE VIEW `guru_view` AS 
SELECT u.id, u.uuid, u.nama, u.username, u.password, up.mapel_uuid, up.jenis_kelamin, u.created_by, u.modified_at, u.deleted_at
FROM users u 
JOIN roles r ON u.role_id = r.id 
LEFT JOIN user_profiles up ON u.id = up.user_id
WHERE r.nama = 'guru';

-- View siswa untuk kompatibilitas
CREATE VIEW `siswa_view` AS 
SELECT u.id, u.uuid, up.nis, u.nama, u.username, u.password, up.tgl_lahir, up.jenis_kelamin, u.created_by, u.modified_at, u.deleted_at
FROM users u 
JOIN roles r ON u.role_id = r.id 
LEFT JOIN user_profiles up ON u.id = up.user_id
WHERE r.nama = 'siswa';

-- Tabel roles_permission - untuk mengatur hak akses detail (opsional)
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

-- Tabel role_permissions - hubungan antara role dan permission
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
WHERE r.nama = 'siswa' AND p.nama IN ('view_reports');

COMMIT;
-- =====================================================
-- MIGRASI: Role Kepala Sekolah & Jadwal Guru
-- =====================================================

-- 1. Tambah role kepala_sekolah
INSERT IGNORE INTO roles (nama, deskripsi) VALUES ('kepala_sekolah', 'Kepala Sekolah - melihat data guru, jadwal, dan laporan');

-- 2. Table jadwal_guru untuk upload gambar jadwal
CREATE TABLE IF NOT EXISTS `jadwal_guru` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(100) NOT NULL,
  `guru_uuid` varchar(100) NOT NULL,
  `file_gambar` varchar(255) NOT NULL,
  `deskripsi` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `guru_uuid` (`guru_uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- File yang dimodifikasi/ditambahkan:
-- application/core/MY_Controller.php (ditambahkan require_kepala_sekolah_or_superadmin)
-- application/controllers/Kepala_sekolah.php (CONTROLLER BARU)
-- application/controllers/Guru.php (ditambahkan method jadwal, upload_jadwal, hapus_jadwal)
-- application/views/kepala_sekolah/kepala_sekolah.php (VIEW BARU - daftar guru)
-- application/views/kepala_sekolah/kepala_sekolah-detail.php (VIEW BARU - detail guru)
-- application/views/guru/guru-jadwal.php (VIEW BARU - upload jadwal)
-- application/views/partials/navbar.php (ditambahkan link Jadwal & Data Guru)
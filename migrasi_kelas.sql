-- ============================================================
-- SQL Migration: Master Data Kelas
-- ============================================================
-- Jalankan script ini untuk menambahkan tabel kelas
-- dan kolom kelas_uuid pada tabel siswa
-- ============================================================

-- 1. Buat tabel kelas (jika belum ada)
CREATE TABLE IF NOT EXISTS `kelas` (
  `id` int(50) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(100) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `modified_at` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 2. Tambahkan kolom kelas_uuid pada tabel siswa (jika belum ada)
ALTER TABLE `siswa` ADD COLUMN IF NOT EXISTS `kelas_uuid` varchar(100) DEFAULT NULL AFTER `jenis_kelamin`;
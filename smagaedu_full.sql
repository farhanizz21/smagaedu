-- ============================================================================
-- SMAGAEDU - SETUP DATABASE LENGKAP (1 FILE UNTUK IMPORT KE MYSQL)
-- ----------------------------------------------------------------------------
-- File ini adalah konsolidasi dari seluruh file migrasi yang ada di project:
--   smartedu.sql, smartedu_updated.sql, smartedu_with_superadmin.sql,
--   smartedu_backup.sql, migrate_users.sql, migrasi_kelas.sql,
--   migrasi_kepala_sekolah.sql, migrasi_jenis_penilaian.sql,
--   migrasi_jenis_soal.sql, migrasi_jawaban_soal.sql, migrasi_jawaban_e.sql,
--   migrasi_ujian_soal_jodohkan.sql, migrasi_ujian_sub_bab.sql
--
-- Isi file (28 tabel + 3 view + data master wajib):
--   A. TABEL UTAMA (RBAC / user terpusat)
--      roles, permissions, role_permissions, users, user_profiles
--   B. TABEL LEGACY (masih dipakai view & data lama)
--      admin, guru, siswa
--   C. TABEL AKADEMIK
--      kelas, mapel, materi, bab, sub_materi, bab_komentar,
--      panduan, perangkat, proyek, proyek_jawaban, proyek_komentar,
--      kelompok, kelompok_siswa, ujian, ujian_soal, ujian_soal_jodohkan,
--      ujian_jawaban, ujian_siswa, jadwal_guru, password_resets
--   D. VIEW KOMPATIBILITAS
--      admin_view, guru_view, siswa_view
--   E. DATA MASTER WAJIB
--      5 role, 7 permission, 16 role_permission, 1 user superadmin
--
-- Semua perubahan hasil migrasi lama SUDAH menyatu di dalam definisi tabel:
--   ujian.bab_uuid, ujian.jenis_penilaian
--   ujian_soal.jenis_soal, jawaban_a, jawaban_b, jawaban_c, jawaban_d,
--   ujian_soal.jawaban_e, ujian_soal.jawaban_benar
--   siswa.kelas_uuid
--   user_profiles.mapel_uuid (JSON multi mapel + kelas_list)
--   ujian_soal_jodohkan (tabel pasangan soal menjodohkan)
--
-- CARA IMPORT:
--   phpMyAdmin : pilih database tujuan -> tab Import -> pilih file ini -> Go
--   Terminal   : mysql -u root -p smagaedu < smagaedu_full.sql
--                (buat dulu database-nya bila belum ada)
--
-- CATATAN: bila database tujuan belum ada, hilangkan komentar (tanda --) pada
--          blok CREATE DATABASE / USE `smagaedu` di bawah ini.
--
-- PERINGATAN: file ini melakukan DROP TABLE & DROP VIEW. Seluruh isi tabel
--             dengan nama yang sama akan dihapus lalu dibuat ulang.
--             Backup dulu: mysqldump -u root smagaedu > backup_smagaedu.sql
--
-- AKUN DEFAULT SETELAH IMPORT:
--   Username : superadmin
--   Password : superadmin123   <-- WAJIB diganti setelah login pertama!
-- ============================================================================

-- CREATE DATABASE IF NOT EXISTS `smagaedu`
--   DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
-- USE `smagaedu`;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
SET NAMES utf8mb4;

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;

-- Matikan pemeriksaan foreign key agar urutan drop/create tidak jadi masalah
SET FOREIGN_KEY_CHECKS = 0;

-- ============================================================================
-- 1. HAPUS OBJEK LAMA (view dulu, baru tabel)
-- ============================================================================

DROP VIEW IF EXISTS `admin_view`;
DROP VIEW IF EXISTS `guru_view`;
DROP VIEW IF EXISTS `siswa_view`;

DROP TABLE IF EXISTS `roles`;
DROP TABLE IF EXISTS `permissions`;
DROP TABLE IF EXISTS `role_permissions`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `user_profiles`;
DROP TABLE IF EXISTS `admin`;
DROP TABLE IF EXISTS `guru`;
DROP TABLE IF EXISTS `siswa`;
DROP TABLE IF EXISTS `kelas`;
DROP TABLE IF EXISTS `mapel`;
DROP TABLE IF EXISTS `materi`;
DROP TABLE IF EXISTS `bab`;
DROP TABLE IF EXISTS `sub_materi`;
DROP TABLE IF EXISTS `bab_komentar`;
DROP TABLE IF EXISTS `panduan`;
DROP TABLE IF EXISTS `perangkat`;
DROP TABLE IF EXISTS `proyek`;
DROP TABLE IF EXISTS `proyek_jawaban`;
DROP TABLE IF EXISTS `proyek_komentar`;
DROP TABLE IF EXISTS `kelompok`;
DROP TABLE IF EXISTS `kelompok_siswa`;
DROP TABLE IF EXISTS `ujian`;
DROP TABLE IF EXISTS `ujian_soal`;
DROP TABLE IF EXISTS `ujian_soal_jodohkan`;
DROP TABLE IF EXISTS `ujian_jawaban`;
DROP TABLE IF EXISTS `ujian_siswa`;
DROP TABLE IF EXISTS `jadwal_guru`;
DROP TABLE IF EXISTS `password_resets`;

-- ============================================================================
-- 2. STRUKTUR TABEL
-- ============================================================================

-- --------------------------------------------------------
-- Struktur tabel `roles`
-- --------------------------------------------------------

CREATE TABLE `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(50) NOT NULL,
  `deskripsi` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `nama` (`nama`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Struktur tabel `permissions`
-- --------------------------------------------------------

CREATE TABLE `permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `deskripsi` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Struktur tabel `role_permissions`
-- --------------------------------------------------------

CREATE TABLE `role_permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `role_permission` (`role_id`,`permission_id`),
  KEY `role_permissions_permission_id` (`permission_id`),
  CONSTRAINT `role_permissions_permission_id` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_permissions_role_id` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Struktur tabel `users`
-- --------------------------------------------------------

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
-- Struktur tabel `user_profiles`
-- --------------------------------------------------------

CREATE TABLE `user_profiles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `nip` varchar(50) DEFAULT NULL,
  `nis` varchar(50) DEFAULT NULL,
  `tgl_lahir` date DEFAULT NULL,
  `jenis_kelamin` enum('L','P') DEFAULT NULL,
  `mapel_uuid` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  KEY `mapel_uuid` (`mapel_uuid`(768)),
  CONSTRAINT `user_profiles_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Struktur tabel `admin`
-- --------------------------------------------------------

CREATE TABLE `admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(100) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `created_by` varchar(100) NOT NULL,
  `modified_at` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Struktur tabel `guru`
-- --------------------------------------------------------

CREATE TABLE `guru` (
  `id` int(100) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(100) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `mapel_uuid` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `jenis_kelamin` int(5) NOT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `modified_at` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Struktur tabel `siswa`
-- --------------------------------------------------------

CREATE TABLE `siswa` (
  `id` int(50) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(100) NOT NULL,
  `nis` int(100) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `tgl_lahir` datetime NOT NULL,
  `jenis_kelamin` int(5) NOT NULL,
  `kelas_uuid` varchar(100) DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `modified_at` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Struktur tabel `kelas`
-- --------------------------------------------------------

CREATE TABLE `kelas` (
  `id` int(50) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(100) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `modified_at` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Struktur tabel `mapel`
-- --------------------------------------------------------

CREATE TABLE `mapel` (
  `id` int(50) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(100) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `modified_at` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Struktur tabel `materi`
-- --------------------------------------------------------

CREATE TABLE `materi` (
  `id` int(50) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(100) NOT NULL,
  `mapel_uuid` varchar(100) DEFAULT NULL,
  `judul` varchar(100) NOT NULL,
  `thumbnail` varchar(100) DEFAULT NULL,
  `berkas` varchar(100) NOT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `modified_at` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Struktur tabel `bab`
-- --------------------------------------------------------

CREATE TABLE `bab` (
  `id` int(50) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(100) NOT NULL,
  `materi_uuid` varchar(100) DEFAULT NULL,
  `judul` varchar(200) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `dokumentasi` varchar(200) DEFAULT NULL,
  `dokumentasi_link` varchar(500) DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `modified_at` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Struktur tabel `sub_materi`
-- --------------------------------------------------------

CREATE TABLE `sub_materi` (
  `id` int(50) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(100) NOT NULL,
  `bab_uuid` varchar(100) DEFAULT NULL,
  `judul` varchar(200) NOT NULL,
  `berkas` varchar(200) DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `modified_at` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Struktur tabel `bab_komentar`
-- --------------------------------------------------------

CREATE TABLE `bab_komentar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(100) NOT NULL,
  `bab_uuid` varchar(100) NOT NULL,
  `komentar` text NOT NULL,
  `created_by` varchar(100) NOT NULL,
  `modified_at` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Struktur tabel `panduan`
-- --------------------------------------------------------

CREATE TABLE `panduan` (
  `id` int(50) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(100) NOT NULL,
  `judul` varchar(100) NOT NULL,
  `berkas` varchar(100) NOT NULL,
  `tujuan` varchar(100) NOT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `modified_at` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Struktur tabel `perangkat`
-- --------------------------------------------------------

CREATE TABLE `perangkat` (
  `id` int(50) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(100) NOT NULL,
  `mapel_uuid` varchar(100) NOT NULL,
  `guru_uuid` varchar(100) DEFAULT NULL,
  `jenis_file` enum('modul','atp') NOT NULL,
  `nama_file` varchar(255) NOT NULL,
  `file` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Struktur tabel `proyek`
-- --------------------------------------------------------

CREATE TABLE `proyek` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(100) NOT NULL,
  `judul` varchar(100) NOT NULL,
  `mapel_uuid` varchar(100) DEFAULT NULL,
  `file` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `tgl_mulai` datetime NOT NULL,
  `tgl_selesai` datetime NOT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `modified_at` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Struktur tabel `proyek_jawaban`
-- --------------------------------------------------------

CREATE TABLE `proyek_jawaban` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(100) NOT NULL,
  `proyek_uuid` varchar(100) NOT NULL,
  `kelompok_uuid` varchar(100) NOT NULL,
  `kelompok_nama` varchar(100) DEFAULT NULL,
  `jawaban_text` text DEFAULT NULL,
  `jawaban_file` varchar(100) DEFAULT NULL,
  `keterangan_file` text DEFAULT NULL,
  `nilai` float DEFAULT NULL,
  `created_by` varchar(100) NOT NULL,
  `modified_at` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Struktur tabel `proyek_komentar`
-- --------------------------------------------------------

CREATE TABLE `proyek_komentar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(100) NOT NULL,
  `proyek_uuid` varchar(100) NOT NULL,
  `komentar` text NOT NULL,
  `created_by` varchar(100) NOT NULL,
  `modified_at` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Struktur tabel `kelompok`
-- --------------------------------------------------------

CREATE TABLE `kelompok` (
  `id` int(50) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(100) NOT NULL,
  `proyek_uuid` varchar(100) NOT NULL,
  `kelompok` varchar(100) NOT NULL,
  `created_by` varchar(100) NOT NULL,
  `modified_at` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Struktur tabel `kelompok_siswa`
-- --------------------------------------------------------

CREATE TABLE `kelompok_siswa` (
  `id` int(50) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(100) NOT NULL,
  `kelompok_uuid` varchar(100) NOT NULL,
  `siswa_uuid` varchar(100) NOT NULL,
  `created_by` varchar(100) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Struktur tabel `ujian`
-- --------------------------------------------------------

CREATE TABLE `ujian` (
  `id` int(50) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(100) NOT NULL,
  `mapel_uuid` varchar(100) NOT NULL,
  `bab_uuid` varchar(100) DEFAULT NULL,
  `sub_materi_uuid` varchar(100) DEFAULT NULL,
  `nama` varchar(100) NOT NULL,
  `tgl_mulai` datetime NOT NULL,
  `tgl_selesai` datetime NOT NULL,
  `created_by` varchar(100) NOT NULL,
  `modified_at` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `jenis_penilaian` varchar(50) DEFAULT 'penilaian_harian' COMMENT 'jenis penilaian: penilaian_harian, penilaian_tengah_semester, penilaian_akhir_semester',
  PRIMARY KEY (`id`),
  KEY `idx_sub_materi_uuid` (`sub_materi_uuid`),
  KEY `idx_ujian_sub_materi_uuid` (`sub_materi_uuid`),
  KEY `idx_ujian_bab_uuid` (`bab_uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Struktur tabel `ujian_soal`
-- --------------------------------------------------------

CREATE TABLE `ujian_soal` (
  `id` int(50) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(100) NOT NULL,
  `ujian_uuid` varchar(100) NOT NULL,
  `soal` text NOT NULL,
  `jenis_soal` varchar(50) DEFAULT 'pilihan_ganda' COMMENT 'jenis soal: pilihan_ganda, pilihan_ganda_kompleks, menjodohkan, benar_salah, essay',
  `jawaban_a` text DEFAULT NULL COMMENT 'Jawaban A (untuk pilihan ganda) atau Soal/Kunci (untuk menjodohkan)',
  `jawaban_b` text DEFAULT NULL COMMENT 'Jawaban B (untuk pilihan ganda) atau Jawaban (untuk menjodohkan)',
  `jawaban_c` text DEFAULT NULL COMMENT 'Jawaban C (untuk pilihan ganda)',
  `jawaban_d` text DEFAULT NULL COMMENT 'Jawaban D (untuk pilihan ganda)',
  `jawaban_e` text DEFAULT NULL COMMENT 'Jawaban E (untuk pilihan ganda)',
  `jawaban_benar` text DEFAULT NULL COMMENT 'jawaban benar soal: A/B/C/D, benar/salah, atau JSON array untuk multiple answer',
  `created_by` varchar(100) NOT NULL,
  `modified_at` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Struktur tabel `ujian_soal_jodohkan`
-- --------------------------------------------------------

CREATE TABLE `ujian_soal_jodohkan` (
  `uuid` varchar(100) NOT NULL,
  `soal_uuid` varchar(100) NOT NULL,
  `kunci` text NOT NULL,
  `jawaban` text NOT NULL,
  `urutan` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`uuid`),
  KEY `soal_uuid` (`soal_uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Struktur tabel `ujian_jawaban`
-- --------------------------------------------------------

CREATE TABLE `ujian_jawaban` (
  `id` int(50) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(100) NOT NULL,
  `ujian_uuid` varchar(100) NOT NULL,
  `soal_uuid` varchar(100) NOT NULL,
  `jawaban_siswa` varchar(100) NOT NULL,
  `nilai` int(100) DEFAULT NULL,
  `created_by` varchar(100) NOT NULL,
  `modified_at` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Struktur tabel `ujian_siswa`
-- --------------------------------------------------------

CREATE TABLE `ujian_siswa` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(100) NOT NULL,
  `ujian_uuid` varchar(100) NOT NULL,
  `siswa_uuid` varchar(100) NOT NULL,
  `ujian_nilai` double DEFAULT NULL,
  `created_by` varchar(100) NOT NULL,
  `modified_at` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Struktur tabel `jadwal_guru`
-- --------------------------------------------------------

CREATE TABLE `jadwal_guru` (
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

-- --------------------------------------------------------
-- Struktur tabel `password_resets`
-- --------------------------------------------------------

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================================
-- 3. VIEW KOMPATIBILITAS (dipakai kode lama yang masih mengakses admin/guru/siswa)
-- ============================================================================

-- Samakan collation koneksi dengan database sumber agar metadata VIEW identik
SET collation_connection = utf8mb4_unicode_ci;

-- View admin & superadmin
CREATE VIEW `admin_view` AS
SELECT u.id, u.uuid, u.nama, u.username, u.password,
       u.created_by, u.modified_at, u.deleted_at
FROM users u
JOIN roles r ON u.role_id = r.id
WHERE r.nama IN ('superadmin', 'admin');

-- View guru
CREATE VIEW `guru_view` AS
SELECT u.id, u.uuid, u.nama, u.username, u.password,
       up.mapel_uuid, up.jenis_kelamin,
       u.created_by, u.modified_at, u.deleted_at
FROM users u
JOIN roles r ON u.role_id = r.id
LEFT JOIN user_profiles up ON u.id = up.user_id
WHERE r.nama = 'guru';

-- View siswa
CREATE VIEW `siswa_view` AS
SELECT u.id, u.uuid, up.nis, u.nama, u.username, u.password,
       up.tgl_lahir, up.jenis_kelamin,
       u.created_by, u.modified_at, u.deleted_at
FROM users u
JOIN roles r ON u.role_id = r.id
LEFT JOIN user_profiles up ON u.id = up.user_id
WHERE r.nama = 'siswa';


-- ============================================================================
-- 4. DATA MASTER WAJIB
-- ============================================================================

-- 4.1 Role
INSERT INTO `roles` (`id`, `nama`, `deskripsi`) VALUES
(1, 'superadmin', 'Akses penuh ke semua fitur sistem'),
(2, 'admin', 'Admin sekolah - mengelola data utama'),
(3, 'guru', 'Guru - mengelola materi dan ujian'),
(4, 'siswa', 'Siswa - mengakses materi dan mengumpulkan tugas'),
(5, 'kepala_sekolah', 'Kepala Sekolah - melihat data guru, jadwal, dan laporan');

-- 4.2 Permission
INSERT INTO `permissions` (`id`, `nama`, `deskripsi`) VALUES
(1, 'manage_users', 'Kelola semua pengguna'),
(2, 'manage_mapel', 'Kelola mata pelajaran'),
(3, 'manage_materi', 'Kelola materi pembelajaran'),
(4, 'manage_ujian', 'Kelola ujian'),
(5, 'manage_proyek', 'Kelola proyek/tugas'),
(6, 'view_reports', 'Lihat laporan dan statistik'),
(7, 'manage_settings', 'Kelola pengaturan sistem');

-- 4.3 Permission per role
--     superadmin : semua permission
--     admin      : manage_users, manage_mapel, manage_materi, manage_ujian, manage_proyek
--     guru       : manage_materi, manage_ujian, manage_proyek
--     siswa      : view_reports
--     kepala_sekolah : tanpa permission khusus (hanya cek role di controller)
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(1, 1), (1, 2), (1, 3), (1, 4), (1, 5), (1, 6), (1, 7),
(2, 1), (2, 2), (2, 3), (2, 4), (2, 5),
(3, 3), (3, 4), (3, 5),
(4, 6);

-- 4.4 Akun superadmin pertama (password: superadmin123)
INSERT INTO `users`
  (`id`, `uuid`, `role_id`, `nama`, `username`, `password`, `email`, `status`) VALUES
(1, '00000000-0000-4000-8000-000000000001', 1, 'Super Admin', 'superadmin',
 '$2y$10$nntygBg3bburFDsTZiob3eXrXqBd1Q7mq1leIqxwDxT4pbziFKOJi',
 'superadmin@sekolah.ac.id', 'aktif');

-- Catatan: profil (user_profiles) dibuat otomatis saat superadmin membuat user
-- melalui menu Master Data > User, jadi tidak perlu diisi manual di sini.


-- ============================================================================
-- 5. SELESAI
-- ============================================================================

SET FOREIGN_KEY_CHECKS = 1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- ----------------------------------------------------------------------------
-- VERIFIKASI (opsional, bisa dijalankan terpisah):
--   SHOW TABLES;                      -- harus ada 28 tabel + 3 view
--   SELECT * FROM roles;              -- 5 baris
--   SELECT * FROM permissions;        -- 7 baris
--   SELECT * FROM role_permissions;    -- 16 baris
--   SELECT id, username, role_id FROM users;  -- 1 baris (superadmin)
--
-- MEMBUAT USER BARU (bila ingin akun selain superadmin):
--   INSERT INTO users (uuid, role_id, nama, username, password, status)
--   VALUES (UUID(), 2, 'Nama Admin', 'admin1',
--           '$2y$10$hash_bcrypt_disini', 'aktif');
--   -- buat hash dengan: php -r "echo password_hash('password', PASSWORD_BCRYPT);"
-- ----------------------------------------------------------------------------

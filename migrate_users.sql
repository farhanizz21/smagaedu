-- phpMyAdmin SQL Dump - COMPLETE MIGRATION SCRIPT
-- Run this after smartedu_updated.sql to migrate existing data

START TRANSACTION;

-- Kumpulkan role IDs
-- superadmin = 1, admin = 2, guru = 3, siswa = 4

-- Migrasi admin lama ke users (role_id = 2 untuk admin, role_id = 1 untuk superadmin)
INSERT INTO users (id, uuid, role_id, nama, username, password, created_by, modified_at, deleted_at)
SELECT id, uuid, 2 as role_id, nama, username, password, created_by, modified_at, deleted_at 
FROM admin 
WHERE deleted_at IS NULL;

-- Migrasi guru lama ke users
INSERT INTO users (id, uuid, role_id, nama, username, password, created_by, modified_at, deleted_at)
SELECT id, uuid, 3 as role_id, nama, username, password, created_by, modified_at, deleted_at 
FROM guru 
WHERE deleted_at IS NULL;

-- Migrasi siswa lama ke users
INSERT INTO users (id, uuid, role_id, nama, username, password, created_by, modified_at, deleted_at)
SELECT id, uuid, 4 as role_id, nama, username, password, created_by, modified_at, deleted_at 
FROM siswa 
WHERE deleted_at IS NULL;

-- Migrasi profile guru (mapel_uuid, jenis_kelamin)
INSERT INTO user_profiles (user_id, mapel_uuid, jenis_kelamin)
SELECT u.id, g.mapel_uuid, 
       CASE WHEN g.jenis_kelamin = 1 THEN 'L' 
            WHEN g.jenis_kelamin = 2 THEN 'P' 
            ELSE NULL END as jenis_kelamin
FROM users u
JOIN guru g ON u.uuid = g.uuid
WHERE g.deleted_at IS NULL;

-- Migrasi profile siswa (nis, tgl_lahir, jenis_kelamin)
INSERT INTO user_profiles (user_id, nis, tgl_lahir, jenis_kelamin)
SELECT u.id, s.nis, s.tgl_lahir,
       CASE WHEN s.jenis_kelamin = 1 THEN 'L' 
            WHEN s.jenis_kelamin = 2 THEN 'P' 
            ELSE NULL END as jenis_kelamin
FROM users u
JOIN siswa s ON u.uuid = s.uuid
WHERE s.deleted_at IS NULL;

COMMIT;

-- ============================================================================
-- CARA MEMBUAT USER SUPERADMIN BARU
-- ============================================================================

-- 1. Buat user superadmin pertama (password: 'password' - silakan ganti!)
-- Gunakan password_hash() untuk membuat hash password yang aman

INSERT INTO users (uuid, role_id, nama, username, password, email, status) VALUES
(UUID(), 1, 'Super Admin', 'superadmin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.pOJriI7/odA/bnO.', 'superadmin@sekolah.ac.id', 'aktif');

-- Catatan: Password di atas adalah hash dari 'password'. Untuk membuat password baru:
-- PHP: echo password_hash('password_anda', PASSWORD_BCRYPT);
-- Atau pakai tool online bcrypt generator

-- ============================================================================
-- VERIFIKASI ROLE DAN PERMISSION
-- ============================================================================

-- Lihat semua role
SELECT * FROM roles;

-- Lihat semua permission
SELECT * FROM permissions;

-- Lihat permission berdasarkan role
SELECT r.nama as role, p.nama as permission 
FROM role_permissions rp
JOIN roles r ON rp.role_id = r.id
JOIN permissions p ON rp.permission_id = p.id
ORDER BY r.id, p.id;
-- ============================================================
-- Migrasi: Lepaskan username akun guru yang sudah dihapus
-- ============================================================
-- Latar belakang:
--   Hapus guru (tunggal/massal) hanya soft delete tabel `users`
--   sehingga username lamanya tetap terpakai (users.username
--   UNIQUE) dan tidak bisa dipakai ulang untuk guru baru.
--
-- Migrasi ini me-rename username akun guru yang sudah soft delete
-- (diberi suffix _terhapus_<id>) dan menonaktifkan akunnya, agar
-- username lama bebas dipakai ulang. Jejak username tetap ada.
--
-- Kode aplikasi (Guru_model::_lepas_akun_users) sudah melakukan
-- hal yang sama secara otomatis setiap kali guru dihapus.
--
-- Jalankan hanya bila perlu:
--   mysql -u root smagaedu_fix < migrasi_lepas_username_guru_terhapus.sql
-- ============================================================

UPDATE users
SET username = CONCAT(LEFT(username, 80), '_terhapus_', id),
    status   = 'nonaktif'
WHERE role_id = 3            -- guru
  AND deleted_at IS NOT NULL
  AND username NOT LIKE '%\_terhapus\_%';

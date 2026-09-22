-- ============================================================
-- Migrasi: Lepaskan NIS & username milik siswa yang sudah dihapus
-- ============================================================
-- Latar belakang:
--   Hapus siswa (tunggal/massal) hanya soft delete tabel `siswa`.
--   Akun login di tabel `users` tetap aktif dan masih memegang
--   username lama (users.username UNIQUE), serta user_profiles
--   masih memegang NIS. Akibatnya NIS/username lama tidak bisa
--   dipakai ulang untuk siswa baru.
--
-- Migrasi ini:
--   1. Me-rename username akun milik siswa yang sudah soft delete
--      (diberi suffix _terhapus_<id>) sehingga username lama bebas
--      dipakai ulang. Jejak username lama tetap tersimpan.
--   2. Menonaktifkan akun tersebut (status = nonaktif, deleted_at
--      mengikuti tanggal hapus siswa).
--
-- Kode aplikasi (Siswa_model::_lepas_akun_users) sudah melakukan
-- hal yang sama secara otomatis setiap kali siswa dihapus.
-- ============================================================

UPDATE users u
INNER JOIN siswa s ON s.uuid = u.uuid
SET u.username   = CONCAT(LEFT(u.username, 80), '_terhapus_', u.id),
    u.status     = 'nonaktif',
    u.deleted_at = COALESCE(u.deleted_at, s.deleted_at)
WHERE s.deleted_at IS NOT NULL
  AND u.deleted_at IS NULL;

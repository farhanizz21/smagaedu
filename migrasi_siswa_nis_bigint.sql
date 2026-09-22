-- ============================================================================
-- MIGRASI: Perbaikan tipe kolom `siswa`.`nis`
-- ----------------------------------------------------------------------------
-- Masalah : kolom `siswa`.`nis` bertipe int(100) (INT 4 byte, nilai maksimal
--           2.147.483.647) sehingga NIS 10 digit di atas nilai tersebut
--           tersimpan terpotong menjadi 2147483647.
--           Contoh: NIS 9990000001 tersimpan sebagai 2147483647.
-- Solusi  : ubah tipe kolom menjadi BIGINT(20) UNSIGNED (menampung sampai
--           18.446.744.073.709.551.615) agar seluruh NIS 10 digit aman.
-- Dampak  : hanya memperlebar tipe kolom, data yang sudah ada tidak berubah.
--           Kolom `user_profiles`.`nis` sudah varchar(50) sehingga tidak perlu
--           diubah.
-- File terkait: smagaedu_full.sql (definisi tabel `siswa`)
--
-- CARA PAKAI:
--   phpMyAdmin : pilih database -> tab SQL -> tempel isi file ini -> Go
--   Terminal   : mysql -u root -p <nama_database> < migrasi_siswa_nis_bigint.sql
-- ============================================================================

ALTER TABLE `siswa`
  MODIFY `nis` BIGINT(20) UNSIGNED NOT NULL;

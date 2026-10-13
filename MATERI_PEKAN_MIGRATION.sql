-- Jalankan sekali pada database yang sudah memiliki kolom semester.
-- Data materi lama akan diberi nomor pekan saat semester dibuka.
ALTER TABLE `materi`
  ADD COLUMN `pekan_ke` TINYINT UNSIGNED DEFAULT NULL AFTER `semester`;
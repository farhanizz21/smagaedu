-- Jalankan sekali pada database yang sudah berisi data.
-- Nilai default 1 menetapkan semua bab yang sudah ada ke Semester 1.
ALTER TABLE `materi`
  ADD COLUMN `semester` TINYINT UNSIGNED NOT NULL DEFAULT 1 AFTER `mapel_uuid`;
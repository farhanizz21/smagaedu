-- Menambahkan kolom jawaban_e ke tabel ujian_soal
-- Pilihan ganda kini memiliki 5 pilihan (A, B, C, D, E)
ALTER TABLE `ujian_soal`
ADD COLUMN `jawaban_e` text DEFAULT NULL
COMMENT 'Jawaban E (untuk pilihan ganda)'
AFTER `jawaban_d`;

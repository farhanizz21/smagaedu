-- Menambahkan kolom jawaban_a, jawaban_b, jawaban_c, jawaban_d ke tabel ujian_soal
ALTER TABLE `ujian_soal` 
ADD COLUMN `jawaban_a` text DEFAULT NULL 
COMMENT 'Jawaban A (untuk pilihan ganda) atau Soal/Kunci (untuk menjodohkan)' 
AFTER `jenis_soal`;

ALTER TABLE `ujian_soal` 
ADD COLUMN `jawaban_b` text DEFAULT NULL 
COMMENT 'Jawaban B (untuk pilihan ganda) atau Jawaban (untuk menjodohkan)' 
AFTER `jawaban_a`;

ALTER TABLE `ujian_soal` 
ADD COLUMN `jawaban_c` text DEFAULT NULL 
COMMENT 'Jawaban C (untuk pilihan ganda)' 
AFTER `jawaban_b`;

ALTER TABLE `ujian_soal` 
ADD COLUMN `jawaban_d` text DEFAULT NULL 
COMMENT 'Jawaban D (untuk pilihan ganda)' 
AFTER `jawaban_c`;

-- Menambahkan kolom jenis_soal ke tabel ujian_soal
ALTER TABLE `ujian_soal` 
ADD COLUMN `jenis_soal` varchar(50) DEFAULT 'pilihan_ganda' 
COMMENT 'jenis soal: pilihan_ganda, pilihan_ganda_kompleks, menjodohkan, benar_salah, essay' 
AFTER `soal`;
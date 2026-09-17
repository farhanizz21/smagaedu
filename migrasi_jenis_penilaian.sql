-- Menambahkan kolom jenis_penilaian ke tabel ujian
ALTER TABLE `ujian` 
ADD COLUMN `jenis_penilaian` varchar(50) DEFAULT 'penilaian_harian' 
COMMENT 'jenis penilaian: penilaian_harian, penilaian_tengah_semester, penilaian_akhir_semester' 
AFTER `deleted_at`;
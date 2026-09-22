-- Menambahkan kolom durasi (menit) ke tabel ujian
-- Kolom ini menyimpan lama waktu pengerjaan ujian dalam menit.
ALTER TABLE `ujian`
ADD COLUMN `durasi` INT NOT NULL DEFAULT 60
COMMENT 'durasi pengerjaan ujian dalam menit'
AFTER `tgl_selesai`;
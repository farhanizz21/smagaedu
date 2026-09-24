ALTER TABLE `ujian_soal`
  ADD COLUMN `jenis_jawaban_essay` varchar(10) DEFAULT 'teks'
  COMMENT 'jenis jawaban essay: teks atau file'
  AFTER `jenis_soal`;
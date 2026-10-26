CREATE TABLE `tugas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(100) NOT NULL,
  `mapel_uuid` varchar(100) NOT NULL,
  `bab_uuid` varchar(100) DEFAULT NULL,
  `judul` varchar(100) NOT NULL,
  `tgl_mulai` datetime NOT NULL,
  `tgl_selesai` datetime NOT NULL,
  `created_by` varchar(100) NOT NULL,
  `modified_at` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `idx_tugas_mapel_uuid` (`mapel_uuid`),
  KEY `idx_tugas_bab_uuid` (`bab_uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `ujian_soal`
  ADD COLUMN `tugas_uuid` varchar(100) DEFAULT NULL AFTER `ujian_uuid`,
  ADD KEY `idx_ujian_soal_tugas_uuid` (`tugas_uuid`);

CREATE TABLE `tugas_jawaban` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(100) NOT NULL,
  `tugas_uuid` varchar(100) NOT NULL,
  `soal_uuid` varchar(100) NOT NULL,
  `jawaban_siswa` text NOT NULL,
  `created_by` varchar(100) NOT NULL,
  `modified_at` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `idx_tugas_jawaban_lookup` (`tugas_uuid`, `soal_uuid`, `created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

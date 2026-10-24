CREATE TABLE IF NOT EXISTS `bab_selesai_baca` (
  `bab_uuid` varchar(100) NOT NULL,
  `siswa_uuid` varchar(100) NOT NULL,
  `completed_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`bab_uuid`, `siswa_uuid`),
  KEY `idx_bab_selesai_baca_siswa` (`siswa_uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

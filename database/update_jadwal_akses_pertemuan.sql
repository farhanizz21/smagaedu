-- Jalankan sekali pada database yang sudah ada.
ALTER TABLE `bab`
  ADD COLUMN `jadwal_akses_sama` tinyint(1) NOT NULL DEFAULT 1,
  ADD COLUMN `tanggal_akses` date DEFAULT NULL;

CREATE TABLE `bab_jadwal_kelas` (
  `bab_uuid` varchar(100) NOT NULL,
  `kelas_uuid` varchar(100) NOT NULL,
  `tanggal_akses` date NOT NULL,
  PRIMARY KEY (`bab_uuid`, `kelas_uuid`),
  KEY `idx_bab_jadwal_kelas_kelas` (`kelas_uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

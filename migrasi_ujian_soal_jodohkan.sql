-- Tabel untuk menyimpan pasangan soal/jawaban jenis menjodohkan
CREATE TABLE `ujian_soal_jodohkan` (
  `uuid` varchar(100) NOT NULL,
  `soal_uuid` varchar(100) NOT NULL,
  `kunci` text NOT NULL,
  `jawaban` text NOT NULL,
  `urutan` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Index untuk performa query
ALTER TABLE `ujian_soal_jodohkan`
  ADD PRIMARY KEY (`uuid`),
  ADD KEY `soal_uuid` (`soal_uuid`);

-- Auto generate UUID
ALTER TABLE `ujian_soal_jodohkan`
  MODIFY `uuid` varchar(100) NOT NULL;

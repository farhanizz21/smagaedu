-- Jalankan sekali untuk membuat penyimpanan periode akademik global.
CREATE TABLE IF NOT EXISTS `periode_akademik` (
  `id` TINYINT UNSIGNED NOT NULL,
  `pekan` TINYINT UNSIGNED NOT NULL,
  `semester` TINYINT UNSIGNED NOT NULL,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `periode_akademik_pekan_range` CHECK (`pekan` BETWEEN 1 AND 20),
  CONSTRAINT `periode_akademik_semester_range` CHECK (`semester` IN (1, 2))
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT IGNORE INTO `periode_akademik` (`id`, `pekan`, `semester`)
VALUES (1, 1, 1);

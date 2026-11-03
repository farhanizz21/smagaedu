ALTER TABLE `perangkat`
  MODIFY `jenis_file` ENUM('modul', 'atp', 'rme', 'prota', 'promes', 'kktp', 'tp') NOT NULL;

-- Migration: Add bab_uuid column to ujian table (if not exists)
-- This links exams (ujian) to bab (sub bab) so exams can be created from the Tambah Sub Bab form.

ALTER TABLE `ujian` ADD COLUMN IF NOT EXISTS `bab_uuid` VARCHAR(100) NULL AFTER `mapel_uuid`;

-- Add index for faster lookup
CREATE INDEX IF NOT EXISTS `idx_ujian_bab_uuid` ON `ujian`(`bab_uuid`);
ALTER TABLE `task` ADD COLUMN `is_complete` ENUM('Y','N') NULL DEFAULT 'N'  AFTER `due_date` ;

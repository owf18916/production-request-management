-- Add conveyor_id and shift to request_atk table
ALTER TABLE `request_atk` 
ADD COLUMN `conveyor_id` INT NULL AFTER `atk_id`,
ADD COLUMN `shift` ENUM('Shift A', 'Shift B') NULL AFTER `conveyor_id`,
ADD CONSTRAINT `fk_request_atk_conveyor_id` FOREIGN KEY (`conveyor_id`) REFERENCES `master_conveyor` (`id`) ON DELETE SET NULL,
ADD INDEX `idx_conveyor_id` (`conveyor_id`),
ADD INDEX `idx_shift` (`shift`);

-- Add conveyor_id and shift to request_checksheet table
ALTER TABLE `request_checksheet` 
ADD COLUMN `conveyor_id` INT NULL AFTER `checksheet_id`,
ADD COLUMN `shift` ENUM('Shift A', 'Shift B') NULL AFTER `conveyor_id`,
ADD CONSTRAINT `fk_request_checksheet_conveyor_id` FOREIGN KEY (`conveyor_id`) REFERENCES `master_conveyor` (`id`) ON DELETE SET NULL,
ADD INDEX `idx_conveyor_id_cs` (`conveyor_id`),
ADD INDEX `idx_shift_cs` (`shift`);

-- Add conveyor_id and shift to request_id table
ALTER TABLE `request_id` 
ADD COLUMN `conveyor_id` INT NULL AFTER `id_type`,
ADD COLUMN `shift` ENUM('Shift A', 'Shift B') NULL AFTER `conveyor_id`,
ADD CONSTRAINT `fk_request_id_conveyor_id` FOREIGN KEY (`conveyor_id`) REFERENCES `master_conveyor` (`id`) ON DELETE SET NULL,
ADD INDEX `idx_conveyor_id_rid` (`conveyor_id`),
ADD INDEX `idx_shift_rid` (`shift`);

-- Add conveyor_id and shift to request_memo table
ALTER TABLE `request_memo` 
ADD COLUMN `conveyor_id` INT NULL AFTER `request_number`,
ADD COLUMN `shift` ENUM('Shift A', 'Shift B') NULL AFTER `conveyor_id`,
ADD CONSTRAINT `fk_request_memo_conveyor_id` FOREIGN KEY (`conveyor_id`) REFERENCES `master_conveyor` (`id`) ON DELETE SET NULL,
ADD INDEX `idx_conveyor_id_rm` (`conveyor_id`),
ADD INDEX `idx_shift_rm` (`shift`);

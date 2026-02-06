-- Fix: Remove UNIQUE constraint from request_number to allow multiple items per request
-- request_number should identify a REQUEST GROUP, not be unique per row

ALTER TABLE `request_atk` DROP INDEX `request_number`;

-- Add back regular INDEX for query performance
ALTER TABLE `request_atk` ADD INDEX `idx_request_number_group` (`request_number`);

-- Do the same for other request types
ALTER TABLE `request_checksheet` DROP INDEX `request_number`;
ALTER TABLE `request_checksheet` ADD INDEX `idx_request_number_group` (`request_number`);

ALTER TABLE `request_id` DROP INDEX `request_number`;
ALTER TABLE `request_id` ADD INDEX `idx_request_number_group` (`request_number`);

ALTER TABLE `request_memo` DROP INDEX `request_number`;
ALTER TABLE `request_memo` ADD INDEX `idx_request_number_group` (`request_number`);

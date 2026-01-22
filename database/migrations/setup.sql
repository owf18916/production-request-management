-- Create database if not exists
CREATE DATABASE IF NOT EXISTS production_request_db;
USE production_request_db;

-- Drop existing tables in reverse order of dependencies
DROP TABLE IF EXISTS `user_conveyor`;
DROP TABLE IF EXISTS `master_conveyor`;
DROP TABLE IF EXISTS `password_reset_tokens`;
DROP TABLE IF EXISTS `activity_logs`;
DROP TABLE IF EXISTS `audit_logs`;
DROP TABLE IF EXISTS `request_attachments`;
DROP TABLE IF EXISTS `request_comments`;
DROP TABLE IF EXISTS `production_requests`;
DROP TABLE IF EXISTS `users`;

-- Create users table
CREATE TABLE `users` (
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `nik` VARCHAR(50) NOT NULL UNIQUE,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `full_name` VARCHAR(100) NOT NULL,
    `role` ENUM('admin', 'pic') NOT NULL DEFAULT 'pic',
    `last_login_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_nik` (`nik`),
    INDEX `idx_username` (`username`),
    INDEX `idx_role` (`role`),
    INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create production requests table
CREATE TABLE `production_requests` (
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `description` LONGTEXT NOT NULL,
    `status` ENUM('pending', 'in_progress', 'completed', 'rejected', 'on_hold') NOT NULL DEFAULT 'pending',
    `priority` ENUM('low', 'medium', 'high', 'urgent') NOT NULL DEFAULT 'medium',
    `assigned_to` INT,
    `start_date` DATE,
    `end_date` DATE,
    `completed_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_production_requests_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_production_requests_assigned_to` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
    INDEX `idx_user_id` (`user_id`),
    INDEX `idx_status` (`status`),
    INDEX `idx_priority` (`priority`),
    INDEX `idx_assigned_to` (`assigned_to`),
    INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create request comments table
CREATE TABLE `request_comments` (
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `request_id` INT NOT NULL,
    `user_id` INT NOT NULL,
    `comment` LONGTEXT NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_request_comments_request_id` FOREIGN KEY (`request_id`) REFERENCES `production_requests` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_request_comments_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    INDEX `idx_request_id` (`request_id`),
    INDEX `idx_user_id` (`user_id`),
    INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create request attachments table
CREATE TABLE `request_attachments` (
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `request_id` INT NOT NULL,
    `file_name` VARCHAR(255) NOT NULL,
    `file_path` VARCHAR(500) NOT NULL,
    `file_size` BIGINT NOT NULL,
    `mime_type` VARCHAR(100),
    `uploaded_by` INT NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_request_attachments_request_id` FOREIGN KEY (`request_id`) REFERENCES `production_requests` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_request_attachments_uploaded_by` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    INDEX `idx_request_id` (`request_id`),
    INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create audit logs table
CREATE TABLE `audit_logs` (
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT,
    `action` VARCHAR(100) NOT NULL,
    `model` VARCHAR(100) NOT NULL,
    `model_id` INT,
    `old_values` JSON,
    `new_values` JSON,
    `ip_address` VARCHAR(45),
    `user_agent` TEXT,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_audit_logs_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
    INDEX `idx_user_id` (`user_id`),
    INDEX `idx_model` (`model`),
    INDEX `idx_action` (`action`),
    INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create activity logs table
CREATE TABLE `activity_logs` (
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `activity` VARCHAR(255) NOT NULL,
    `description` LONGTEXT,
    `type` ENUM('login', 'logout', 'create', 'update', 'delete', 'view') NOT NULL,
    `ip_address` VARCHAR(45),
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_activity_logs_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    INDEX `idx_user_id` (`user_id`),
    INDEX `idx_type` (`type`),
    INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create password reset tokens table
CREATE TABLE `password_reset_tokens` (
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `token` VARCHAR(255) NOT NULL UNIQUE,
    `expires_at` TIMESTAMP NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_password_reset_tokens_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    INDEX `idx_token` (`token`),
    INDEX `idx_expires_at` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create master_conveyor table
CREATE TABLE `master_conveyor` (
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `conveyor_name` VARCHAR(100) NOT NULL UNIQUE,
    `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    `created_by` INT NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_master_conveyor_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
    INDEX `idx_status` (`status`),
    INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create user_conveyor table (Many-to-Many relationship)
CREATE TABLE `user_conveyor` (
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `conveyor_id` INT NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_user_conveyor_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_user_conveyor_conveyor_id` FOREIGN KEY (`conveyor_id`) REFERENCES `master_conveyor` (`id`) ON DELETE CASCADE,
    UNIQUE KEY `unique_user_conveyor` (`user_id`, `conveyor_id`),
    INDEX `idx_conveyor_id` (`conveyor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===== SEED DATA =====

-- Insert admin user (password: admin123)
INSERT INTO `users` (`nik`, `username`, `password`, `full_name`, `role`, `created_at`, `updated_at`)
VALUES (
    'ADM001',
    'admin',
    '$2y$12$RG5c.q8i7aLkVTLdlS9kV.VwlAKlFNcF8RRg4JLY0eF8vqpDLb7k2',
    'Administrator Produksi',
    'admin',
    NOW(),
    NOW()
);

-- Insert PIC user (password: pic123)
INSERT INTO `users` (`nik`, `username`, `password`, `full_name`, `role`, `created_at`, `updated_at`)
VALUES (
    'PIC001',
    'pic',
    '$2y$12$FhKqYDn0uL8xJvlMzWvDYOh1Q8j7kPLzKZvTLpMvKJIJxM6MU2X0y',
    'PIC Produksi',
    'pic',
    NOW(),
    NOW()
);

-- Insert sample conveyors
INSERT INTO `master_conveyor` (`conveyor_name`, `status`, `created_by`, `created_at`, `updated_at`)
VALUES 
    ('Conveyor A', 'active', 1, NOW(), NOW()),
    ('Conveyor B', 'active', 1, NOW(), NOW()),
    ('Conveyor C', 'inactive', 1, NOW(), NOW());

-- Assign conveyors to admin user
INSERT INTO `user_conveyor` (`user_id`, `conveyor_id`, `created_at`)
VALUES 
    (1, 1, NOW()),
    (1, 2, NOW()),
    (1, 3, NOW());

-- Assign first two conveyors to PIC user
INSERT INTO `user_conveyor` (`user_id`, `conveyor_id`, `created_at`)
VALUES 
    (2, 1, NOW()),
    (2, 2, NOW());

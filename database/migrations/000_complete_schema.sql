-- Production Request Management System - COMPLETE Database Schema
-- MySQL Database Migration File - ALL TABLES IN ONE FILE
-- Run this SQL to create the complete initial database structure

-- ===== DROP EXISTING TABLES (for reset) =====
DROP TABLE IF EXISTS `request_memo_history`;
DROP TABLE IF EXISTS `request_id_history`;
DROP TABLE IF EXISTS `request_id_details`;
DROP TABLE IF EXISTS `request_id`;
DROP TABLE IF EXISTS `request_checksheet_history`;
DROP TABLE IF EXISTS `request_checksheet`;
DROP TABLE IF EXISTS `master_checksheet`;
DROP TABLE IF EXISTS `request_atk_history`;
DROP TABLE IF EXISTS `request_atk`;
DROP TABLE IF EXISTS `master_atk`;
DROP TABLE IF EXISTS `user_conveyor`;
DROP TABLE IF EXISTS `master_conveyor`;
DROP TABLE IF EXISTS `password_reset_tokens`;
DROP TABLE IF EXISTS `activity_logs`;
DROP TABLE IF EXISTS `audit_logs`;
DROP TABLE IF EXISTS `request_attachments`;
DROP TABLE IF EXISTS `request_comments`;
DROP TABLE IF EXISTS `production_requests`;
DROP TABLE IF EXISTS `users`;

-- ===== CREATE TABLES =====

-- Create users table
CREATE TABLE IF NOT EXISTS `users` (
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
CREATE TABLE IF NOT EXISTS `production_requests` (
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
CREATE TABLE IF NOT EXISTS `request_comments` (
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
CREATE TABLE IF NOT EXISTS `request_attachments` (
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
CREATE TABLE IF NOT EXISTS `audit_logs` (
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
CREATE TABLE IF NOT EXISTS `activity_logs` (
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
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
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
CREATE TABLE IF NOT EXISTS `master_conveyor` (
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
CREATE TABLE IF NOT EXISTS `user_conveyor` (
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `conveyor_id` INT NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_user_conveyor_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_user_conveyor_conveyor_id` FOREIGN KEY (`conveyor_id`) REFERENCES `master_conveyor` (`id`) ON DELETE CASCADE,
    UNIQUE KEY `unique_user_conveyor` (`user_id`, `conveyor_id`),
    INDEX `idx_conveyor_id` (`conveyor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create master_atk table
CREATE TABLE IF NOT EXISTS `master_atk` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `kode_barang` VARCHAR(50) UNIQUE NOT NULL,
    `nama_barang` VARCHAR(150) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `created_by` INT NOT NULL,
    CONSTRAINT `fk_master_atk_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
    INDEX `idx_kode_barang` (`kode_barang`),
    INDEX `idx_nama_barang` (`nama_barang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create request_atk table
CREATE TABLE IF NOT EXISTS `request_atk` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `request_number` VARCHAR(50) UNIQUE NOT NULL,
    `atk_id` INT NOT NULL,
    `qty` INT NOT NULL,
    `status` ENUM('pending', 'approved', 'rejected', 'completed') DEFAULT 'pending',
    `requested_by` INT NOT NULL,
    `approved_by` INT NULL,
    `approved_at` TIMESTAMP NULL,
    `notes` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_request_atk_atk_id` FOREIGN KEY (`atk_id`) REFERENCES `master_atk` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_request_atk_requested_by` FOREIGN KEY (`requested_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_request_atk_approved_by` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
    INDEX `idx_request_number` (`request_number`),
    INDEX `idx_status` (`status`),
    INDEX `idx_requested_by` (`requested_by`),
    INDEX `idx_approved_by` (`approved_by`),
    INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create request_atk_history table
CREATE TABLE IF NOT EXISTS `request_atk_history` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `request_atk_id` INT NOT NULL,
    `action` VARCHAR(50) NOT NULL,
    `old_status` VARCHAR(50),
    `new_status` VARCHAR(50),
    `changed_by` INT NOT NULL,
    `notes` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_request_atk_history_request_atk_id` FOREIGN KEY (`request_atk_id`) REFERENCES `request_atk` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_request_atk_history_changed_by` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
    INDEX `idx_request_atk_id` (`request_atk_id`),
    INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create master_checksheet table
CREATE TABLE IF NOT EXISTS `master_checksheet` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `kode_checksheet` VARCHAR(50) UNIQUE NOT NULL,
    `nama_checksheet` VARCHAR(150) NOT NULL,
    `deskripsi` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `created_by` INT NOT NULL,
    CONSTRAINT `fk_master_checksheet_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
    INDEX `idx_kode_checksheet` (`kode_checksheet`),
    INDEX `idx_nama_checksheet` (`nama_checksheet`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create request_checksheet table
CREATE TABLE IF NOT EXISTS `request_checksheet` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `request_number` VARCHAR(50) UNIQUE NOT NULL,
    `checksheet_id` INT NOT NULL,
    `status` ENUM('pending', 'approved', 'rejected', 'completed') DEFAULT 'pending',
    `requested_by` INT NOT NULL,
    `approved_by` INT NULL,
    `approved_at` TIMESTAMP NULL,
    `notes` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_request_checksheet_checksheet_id` FOREIGN KEY (`checksheet_id`) REFERENCES `master_checksheet` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_request_checksheet_requested_by` FOREIGN KEY (`requested_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_request_checksheet_approved_by` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
    INDEX `idx_request_number` (`request_number`),
    INDEX `idx_status` (`status`),
    INDEX `idx_requested_by` (`requested_by`),
    INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create request_checksheet_history table
CREATE TABLE IF NOT EXISTS `request_checksheet_history` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `request_checksheet_id` INT NOT NULL,
    `action` VARCHAR(50) NOT NULL,
    `old_status` VARCHAR(50),
    `new_status` VARCHAR(50),
    `changed_by` INT NOT NULL,
    `notes` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_request_checksheet_history_request_checksheet_id` FOREIGN KEY (`request_checksheet_id`) REFERENCES `request_checksheet` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_request_checksheet_history_changed_by` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
    INDEX `idx_request_checksheet_id` (`request_checksheet_id`),
    INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create request_id table
CREATE TABLE IF NOT EXISTS `request_id` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `request_number` VARCHAR(50) UNIQUE NOT NULL,
    `id_type` ENUM('id_punggung', 'pin_4m', 'id_kaki', 'job_psd', 'id_other') NOT NULL,
    `status` ENUM('pending', 'approved', 'rejected', 'completed') DEFAULT 'pending' NOT NULL,
    `requested_by` INT NOT NULL,
    `approved_by` INT NULL,
    `approved_at` TIMESTAMP NULL,
    `notes` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_request_id_requested_by` FOREIGN KEY (`requested_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_request_id_approved_by` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
    INDEX `idx_request_number` (`request_number`),
    INDEX `idx_id_type` (`id_type`),
    INDEX `idx_status` (`status`),
    INDEX `idx_requested_by` (`requested_by`),
    INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create request_id_details table
CREATE TABLE IF NOT EXISTS `request_id_details` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `request_id_id` INT NOT NULL,
    `detail_key` VARCHAR(100) NOT NULL,
    `detail_value` TEXT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_request_id_details_request_id_id` FOREIGN KEY (`request_id_id`) REFERENCES `request_id` (`id`) ON DELETE CASCADE,
    INDEX `idx_request_id_id` (`request_id_id`),
    INDEX `idx_detail_key` (`detail_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create request_id_history table
CREATE TABLE IF NOT EXISTS `request_id_history` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `request_id_id` INT NOT NULL,
    `action` VARCHAR(50) NOT NULL,
    `old_status` VARCHAR(50),
    `new_status` VARCHAR(50),
    `changed_by` INT NOT NULL,
    `notes` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_request_id_history_request_id_id` FOREIGN KEY (`request_id_id`) REFERENCES `request_id` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_request_id_history_changed_by` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
    INDEX `idx_request_id_id` (`request_id_id`),
    INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create request_memo table
CREATE TABLE IF NOT EXISTS `request_memo` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `request_number` VARCHAR(50) UNIQUE NOT NULL,
    `memo_content` TEXT NOT NULL,
    `status` ENUM('pending', 'approved', 'rejected', 'completed') DEFAULT 'pending',
    `requested_by` INT NOT NULL,
    `approved_by` INT NULL,
    `approved_at` TIMESTAMP NULL,
    `notes` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_request_memo_requested_by` FOREIGN KEY (`requested_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_request_memo_approved_by` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
    INDEX `idx_request_number` (`request_number`),
    INDEX `idx_status` (`status`),
    INDEX `idx_requested_by` (`requested_by`),
    INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create request_memo_history table
CREATE TABLE IF NOT EXISTS `request_memo_history` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `request_memo_id` INT NOT NULL,
    `action` VARCHAR(50) NOT NULL,
    `old_status` VARCHAR(50),
    `new_status` VARCHAR(50),
    `changed_by` INT NOT NULL,
    `notes` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_request_memo_history_request_memo_id` FOREIGN KEY (`request_memo_id`) REFERENCES `request_memo` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_request_memo_history_changed_by` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
    INDEX `idx_request_memo_id` (`request_memo_id`),
    INDEX `idx_created_at` (`created_at`)
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

-- Insert sample master ATK
INSERT INTO `master_atk` (`kode_barang`, `nama_barang`, `created_by`, `created_at`, `updated_at`)
VALUES 
    ('ATK001', 'Kertas A4 (500 lembar)', 1, NOW(), NOW()),
    ('ATK002', 'Pulpen Biru', 1, NOW(), NOW()),
    ('ATK003', 'Pensil 2B', 1, NOW(), NOW());

-- Insert sample master checksheet
INSERT INTO `master_checksheet` (`kode_checksheet`, `nama_checksheet`, `deskripsi`, `created_by`, `created_at`, `updated_at`)
VALUES 
    ('CS001', 'Quality Check', 'Checksheet untuk quality control', 1, NOW(), NOW()),
    ('CS002', 'Safety Check', 'Checksheet untuk safety inspection', 1, NOW(), NOW());

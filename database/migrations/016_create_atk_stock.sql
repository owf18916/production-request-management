-- Create ATK Stock Master Table
CREATE TABLE IF NOT EXISTS atk_stock (
    id INT PRIMARY KEY AUTO_INCREMENT,
    atk_id INT UNIQUE NOT NULL,
    beginning_stock INT DEFAULT 0,
    in_qty INT DEFAULT 0,
    out_qty INT DEFAULT 0,
    adjustment INT DEFAULT 0,
    ending_stock INT DEFAULT 0,
    last_stocktake_date TIMESTAMP NULL,
    notes TEXT NULL,
    updated_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (atk_id) REFERENCES master_atk(id) ON DELETE CASCADE,
    FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE RESTRICT,
    INDEX idx_atk_id (atk_id),
    INDEX idx_ending_stock (ending_stock)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

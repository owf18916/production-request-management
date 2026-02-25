-- Create ATK Stock Transaction Log Table
CREATE TABLE IF NOT EXISTS atk_stock_transaction (
    id INT PRIMARY KEY AUTO_INCREMENT,
    atk_id INT NOT NULL,
    transaction_type ENUM('beginning', 'incoming', 'out', 'adjustment', 'restore') NOT NULL,
    qty INT NOT NULL,
    previous_balance INT,
    new_balance INT,
    reference_type VARCHAR(50),
    reference_id INT,
    notes TEXT,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (atk_id) REFERENCES master_atk(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT,
    INDEX idx_atk_id (atk_id),
    INDEX idx_transaction_type (transaction_type),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS request_id_history (
    id INT PRIMARY KEY AUTO_INCREMENT,
    request_id_id INT NOT NULL,
    status VARCHAR(50) NOT NULL,
    changed_by INT NOT NULL,
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (request_id_id) REFERENCES request_id(id) ON DELETE CASCADE,
    FOREIGN KEY (changed_by) REFERENCES users(id) ON DELETE RESTRICT,
    INDEX idx_request_id_id (request_id_id),
    INDEX idx_changed_by (changed_by),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

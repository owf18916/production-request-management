CREATE TABLE IF NOT EXISTS request_id_details (
    id INT PRIMARY KEY AUTO_INCREMENT,
    request_id_id INT NOT NULL,
    field_name VARCHAR(100) NOT NULL,
    field_value TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (request_id_id) REFERENCES request_id(id) ON DELETE CASCADE,
    INDEX idx_request_id_id (request_id_id),
    INDEX idx_field_name (field_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

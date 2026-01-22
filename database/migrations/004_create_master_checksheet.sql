-- Master Checksheet Table
CREATE TABLE IF NOT EXISTS master_checksheet (
    id INT PRIMARY KEY AUTO_INCREMENT,
    kode_checksheet VARCHAR(50) UNIQUE NOT NULL,
    nama_checksheet VARCHAR(150) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by INT NOT NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT,
    INDEX idx_kode_checksheet (kode_checksheet),
    INDEX idx_nama_checksheet (nama_checksheet)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

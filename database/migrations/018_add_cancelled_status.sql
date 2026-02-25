-- Add 'cancelled' status to request_atk and request_memo
ALTER TABLE request_atk 
MODIFY COLUMN status ENUM('pending', 'approved', 'rejected', 'completed', 'cancelled') DEFAULT 'pending';

ALTER TABLE request_memo 
MODIFY COLUMN status ENUM('pending', 'approved', 'rejected', 'completed', 'cancelled') DEFAULT 'pending';

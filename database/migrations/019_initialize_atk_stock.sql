-- Initialize stock records for existing ATK items that don't have stock record yet
-- This migration ensures all ATK items have a corresponding stock record
INSERT INTO atk_stock (atk_id, beginning_stock, updated_by, created_at, updated_at)
SELECT id, 0, 1, NOW(), NOW() FROM master_atk
WHERE id NOT IN (SELECT atk_id FROM atk_stock)
ON DUPLICATE KEY UPDATE updated_at = NOW();

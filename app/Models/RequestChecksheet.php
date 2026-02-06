<?php

namespace App\Models;

use App\Model;
use App\Database;

class RequestChecksheet extends Model
{
    protected string $table = 'request_checksheet';
    protected string $primaryKey = 'id';

    protected array $fillable = [
        'request_number',
        'checksheet_id',
        'conveyor_id',
        'shift',
        'qty',
        'status',
        'requested_by',
        'approved_by',
        'approved_at',
        'notes',
    ];

    /**
     * Get all records
     */
    public static function getAll(): array
    {
        $sql = "SELECT rc.*, mc.nama_checksheet, mco.conveyor_name, u.full_name 
                FROM request_checksheet rc
                LEFT JOIN master_checksheet mc ON rc.checksheet_id = mc.id
                LEFT JOIN master_conveyor mco ON rc.conveyor_id = mco.id
                LEFT JOIN users u ON rc.requested_by = u.id
                ORDER BY rc.created_at DESC";
        return Database::results($sql);
    }

    /**
     * Get requests by user ID
     */
    public static function getByUser(int $userId): array
    {
        $sql = "SELECT rc.*, mc.nama_checksheet, mco.conveyor_name, u.full_name 
                FROM request_checksheet rc
                LEFT JOIN master_checksheet mc ON rc.checksheet_id = mc.id
                LEFT JOIN master_conveyor mco ON rc.conveyor_id = mco.id
                LEFT JOIN users u ON rc.requested_by = u.id
                WHERE rc.requested_by = ?
                ORDER BY rc.created_at DESC";
        return Database::results($sql, [$userId]);
    }

    /**
     * Find by ID
     */
    public static function findById($id)
    {
        $sql = "SELECT rc.*, mc.nama_checksheet, mco.conveyor_name, u.full_name, ua.full_name as approved_by_name
                FROM request_checksheet rc
                LEFT JOIN master_checksheet mc ON rc.checksheet_id = mc.id
                LEFT JOIN master_conveyor mco ON rc.conveyor_id = mco.id
                LEFT JOIN users u ON rc.requested_by = u.id
                LEFT JOIN users ua ON rc.approved_by = ua.id
                WHERE rc.id = ?";
        return Database::row($sql, [$id]);
    }

    /**
     * Create new request checksheet
     */
    public static function create(array $data): bool
    {
        $fillable = (new static())->fillable;
        $columns = [];
        $values = [];
        $placeholders = [];

        foreach ($data as $key => $value) {
            if (in_array($key, $fillable)) {
                $columns[] = $key;
                $values[] = $value;
                $placeholders[] = '?';
            }
        }

        if (empty($columns)) {
            error_log('RequestChecksheet::create - No fillable columns matched');
            return false;
        }

        $sql = "INSERT INTO request_checksheet (" . implode(', ', $columns) . ") 
                VALUES (" . implode(', ', $placeholders) . ")";

        try {
            error_log('RequestChecksheet::create - SQL: ' . $sql . ' | Values: ' . json_encode($values));
            Database::query($sql, $values);
            error_log('RequestChecksheet::create - Success for request_number: ' . ($data['request_number'] ?? 'unknown'));
            return true;
        } catch (\Exception $e) {
            error_log('RequestChecksheet::create - Exception: ' . $e->getMessage() . ' | Data: ' . json_encode($data));
            return false;
        }
    }

    /**
     * Update request checksheet
     */
    public static function update($id, array $data): bool
    {
        $fillable = (new static())->fillable;
        $updates = [];
        $values = [];

        foreach ($data as $key => $value) {
            if (in_array($key, $fillable)) {
                $updates[] = "$key = ?";
                $values[] = $value;
            }
        }

        if (empty($updates)) {
            return false;
        }

        $updates[] = "updated_at = ?";
        $values[] = date('Y-m-d H:i:s');
        $values[] = $id;

        $sql = "UPDATE request_checksheet SET " . implode(', ', $updates) . " WHERE id = ?";

        try {
            Database::query($sql, $values);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Update status with history
     */
    public static function updateStatus($id, string $status, int $userId, $notes = null): bool
    {
        try {
            // Update status
            $sql = "UPDATE request_checksheet SET status = ?, updated_at = ? WHERE id = ?";
            Database::query($sql, [$status, date('Y-m-d H:i:s'), $id]);

            // If approved, update approved_by and approved_at
            if ($status === 'approved') {
                $sql = "UPDATE request_checksheet SET approved_by = ?, approved_at = ? WHERE id = ?";
                Database::query($sql, [$userId, date('Y-m-d H:i:s'), $id]);
            }

            // Record history
            $historySql = "INSERT INTO request_checksheet_history (request_checksheet_id, status, changed_by, notes) 
                          VALUES (?, ?, ?, ?)";
            Database::query($historySql, [$id, $status, $userId, $notes]);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Generate request number - Format: CHS-YYYYMMDD-XXX
     */
    public static function generateRequestNumber(): string
    {
        $date = date('Ymd');
        $sql = "SELECT COUNT(*) as count FROM request_checksheet WHERE request_number LIKE ?";
        $result = Database::row($sql, ["CHS-{$date}-%"]);
        
        $nextNumber = ($result->count ?? 0) + 1;
        $sequence = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        
        return "CHS-{$date}-{$sequence}";
    }

    /**
     * Get history for request checksheet
     */
    public static function getHistory($requestId): array
    {
        $sql = "SELECT rch.*, u.full_name 
                FROM request_checksheet_history rch
                LEFT JOIN users u ON rch.changed_by = u.id
                WHERE rch.request_checksheet_id = ?
                ORDER BY rch.created_at DESC";
        return Database::results($sql, [$requestId]);
    }

    /**
     * Check if request number exists
     */
    public static function requestNumberExists(string $requestNumber): bool
    {
        $sql = "SELECT id FROM request_checksheet WHERE request_number = ?";
        return Database::row($sql, [$requestNumber]) !== null;
    }

    /**
     * Count total requests
     */
    public static function count(): int
    {
        $sql = "SELECT COUNT(*) as count FROM request_checksheet";
        $result = Database::row($sql);
        return $result->count ?? 0;
    }

    /**
     * Count by status
     */
    public static function countByStatus(string $status): int
    {
        $sql = "SELECT COUNT(*) as count FROM request_checksheet WHERE status = ?";
        $result = Database::row($sql, [$status]);
        return $result->count ?? 0;
    }
}

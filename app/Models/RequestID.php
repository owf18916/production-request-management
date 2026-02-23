<?php

namespace App\Models;

use App\Model;
use App\Database;
use DateTime;

class RequestID extends Model
{
    protected string $table = 'request_id';
    protected string $primaryKey = 'id';

    protected array $fillable = [
        'request_number',
        'id_type',
        'conveyor_id',
        'shift',
        'status',
        'requested_by',
        'approved_by',
        'approved_at',
        'notes',
    ];

    /**
     * Valid ID types
     */
    const VALID_ID_TYPES = ['id_punggung', 'pin_4m', 'id_kaki', 'job_psd', 'id_other'];

    /**
     * Valid statuses
     */
    const VALID_STATUSES = ['pending', 'approved', 'rejected', 'completed', 'cancelled'];

    /**
     * Get all requests
     */
    public static function getAll(): array
    {
        $sql = "SELECT rid.*, mc.conveyor_name, u1.full_name as requester, u2.full_name as approver 
                FROM request_id rid
                LEFT JOIN master_conveyor mc ON rid.conveyor_id = mc.id
                LEFT JOIN users u1 ON rid.requested_by = u1.id
                LEFT JOIN users u2 ON rid.approved_by = u2.id
                ORDER BY rid.created_at DESC";
        return Database::results($sql);
    }

    /**
     * Get requests by user
     */
    public static function getByUser(int $userId): array
    {
        $sql = "SELECT rid.*, mc.conveyor_name, u1.full_name as requester, u2.full_name as approver 
                FROM request_id rid
                LEFT JOIN master_conveyor mc ON rid.conveyor_id = mc.id
                LEFT JOIN users u1 ON rid.requested_by = u1.id
                LEFT JOIN users u2 ON rid.approved_by = u2.id
                WHERE rid.requested_by = ?
                ORDER BY rid.created_at DESC";
        return Database::results($sql, [$userId]);
    }

    /**
     * Find by ID
     */
    public static function findById($id)
    {
        $sql = "SELECT rid.*, mc.conveyor_name, u1.full_name as requester, u2.full_name as approver 
                FROM request_id rid
                LEFT JOIN master_conveyor mc ON rid.conveyor_id = mc.id
                LEFT JOIN users u1 ON rid.requested_by = u1.id
                LEFT JOIN users u2 ON rid.approved_by = u2.id
                WHERE rid.id = ?";
        return Database::row($sql, [$id]);
    }

    /**
     * Create - STATIC method
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
            error_log('RequestID::create - No fillable columns matched');
            return false;
        }

        $sql = "INSERT INTO request_id (" . implode(', ', $columns) . ") 
                VALUES (" . implode(', ', $placeholders) . ")";

        try {
            error_log('RequestID::create - SQL: ' . $sql . ' | Values: ' . json_encode($values));
            Database::query($sql, $values);
            error_log('RequestID::create - Success for request_number: ' . ($data['request_number'] ?? 'unknown'));
            return true;
        } catch (\Exception $e) {
            error_log('RequestID::create - Exception: ' . $e->getMessage() . ' | Data: ' . json_encode($data));
            return false;
        }
    }

    /**
     * Update status - STATIC method with MIXED $id
     */
    public static function updateStatus($id, string $status, int $userId, $notes = null): bool
    {
        $approvedAt = null;
        if ($status === 'approved' || $status === 'completed') {
            $approvedAt = date('Y-m-d H:i:s');
        }

        $sql = "UPDATE request_id 
                SET status = ?, approved_by = ?, approved_at = ?, notes = ?, updated_at = ?
                WHERE id = ?";

        try {
            Database::query($sql, [$status, $userId, $approvedAt, $notes, date('Y-m-d H:i:s'), $id]);
            
            // Record history
            self::recordHistory($id, $status, $userId, $notes);
            
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Generate request number - Format: ID-YYYYMMDD-XXX
     */
    public static function generateRequestNumber(): string
    {
        $prefix = 'ID-' . date('Ymd') . '-';
        
        // Get count of requests created today
        $sql = "SELECT COUNT(*) as count FROM request_id WHERE DATE(created_at) = DATE(NOW())";
        $result = Database::row($sql);
        $count = ($result->count ?? 0) + 1;
        
        return $prefix . str_pad($count, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Get history
     */
    public static function getHistory(int $requestId): array
    {
        $sql = "SELECT rih.*, u.full_name as changed_by_name
                FROM request_id_history rih
                LEFT JOIN users u ON rih.changed_by = u.id
                WHERE rih.request_id_id = ?
                ORDER BY rih.created_at DESC";
        return Database::results($sql, [$requestId]);
    }

    /**
     * Get details
     */
    public static function getDetails(int $requestId): array
    {
        $sql = "SELECT detail_key, detail_value FROM request_id_details
                WHERE request_id_id = ?
                ORDER BY created_at ASC";
        return Database::results($sql, [$requestId]);
    }

    /**
     * Save details
     */
    public static function saveDetails(int $requestId, array $details): bool
    {
        try {
            // Delete existing details
            $sql = "DELETE FROM request_id_details WHERE request_id_id = ?";
            Database::query($sql, [$requestId]);
            
            // Insert new details
            foreach ($details as $fieldName => $fieldValue) {
                $sql = "INSERT INTO request_id_details (request_id_id, detail_key, detail_value)
                        VALUES (?, ?, ?)";
                Database::query($sql, [$requestId, $fieldName, $fieldValue]);
            }
            
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Record history
     */
    private static function recordHistory(int $requestId, string $status, int $userId, ?string $notes = null): void
    {
        $sql = "INSERT INTO request_id_history (request_id_id, status, changed_by, notes, created_at)
                VALUES (?, ?, ?, ?, NOW())";
        
        try {
            Database::query($sql, [$requestId, $status, $userId, $notes]);
        } catch (\Exception $e) {
            // History recording failure shouldn't break the main transaction
        }
    }

    /**
     * Get by status
     */
    public static function getByStatus(string $status): array
    {
        $sql = "SELECT rid.*, u1.full_name as requester, u2.full_name as approver 
                FROM request_id rid
                LEFT JOIN users u1 ON rid.requested_by = u1.id
                LEFT JOIN users u2 ON rid.approved_by = u2.id
                WHERE rid.status = ?
                ORDER BY rid.created_at DESC";
        return Database::results($sql, [$status]);
    }

    /**
     * Get by ID type
     */
    public static function getByIdType(string $idType): array
    {
        $sql = "SELECT rid.*, u1.full_name as requester, u2.full_name as approver 
                FROM request_id rid
                LEFT JOIN users u1 ON rid.requested_by = u1.id
                LEFT JOIN users u2 ON rid.approved_by = u2.id
                WHERE rid.id_type = ?
                ORDER BY rid.created_at DESC";
        return Database::results($sql, [$idType]);
    }

    /**
     * Count records
     */
    public static function count(): int
    {
        $sql = "SELECT COUNT(*) as count FROM request_id";
        $result = Database::row($sql);
        return $result->count ?? 0;
    }

    /**
     * Count by status
     */
    public static function countByStatus(string $status): int
    {
        if ($status === 'pending') {
            // Pending: no approval yet (approved_at is null)
            $sql = "SELECT COUNT(*) as count FROM request_id WHERE approved_at IS NULL AND status != 'rejected' AND status != 'completed'";
            $result = Database::row($sql);
        } elseif ($status === 'approved') {
            // Approved: has approved_at timestamp
            $sql = "SELECT COUNT(*) as count FROM request_id WHERE approved_at IS NOT NULL";
            $result = Database::row($sql);
        } else {
            // Rejected or Completed: based on status field
            $sql = "SELECT COUNT(*) as count FROM request_id WHERE status = ?";
            $result = Database::row($sql, [$status]);
        }
        return $result->count ?? 0;
    }
}

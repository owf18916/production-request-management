<?php

namespace App\Models;

use App\Model;
use App\Database;
use DateTime;

class RequestATK extends Model
{
    protected string $table = 'request_atk';
    protected string $primaryKey = 'id';

    protected array $fillable = [
        'request_number',
        'atk_id',
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
     * Get all requests
     */
    public static function getAll(): array
    {
        $sql = "SELECT ra.*, ma.nama_barang, mc.conveyor_name, u1.full_name as requester, u2.full_name as approver 
                FROM request_atk ra
                LEFT JOIN master_atk ma ON ra.atk_id = ma.id
                LEFT JOIN master_conveyor mc ON ra.conveyor_id = mc.id
                LEFT JOIN users u1 ON ra.requested_by = u1.id
                LEFT JOIN users u2 ON ra.approved_by = u2.id
                ORDER BY ra.created_at DESC";
        return Database::results($sql);
    }

    /**
     * Get requests by user
     */
    public static function getByUser(int $userId): array
    {
        $sql = "SELECT ra.*, ma.nama_barang, mc.conveyor_name, u1.full_name as requester, u2.full_name as approver 
                FROM request_atk ra
                LEFT JOIN master_atk ma ON ra.atk_id = ma.id
                LEFT JOIN master_conveyor mc ON ra.conveyor_id = mc.id
                LEFT JOIN users u1 ON ra.requested_by = u1.id
                LEFT JOIN users u2 ON ra.approved_by = u2.id
                WHERE ra.requested_by = ?
                ORDER BY ra.created_at DESC";
        return Database::results($sql, [$userId]);
    }

    /**
     * Find by ID
     */
    public static function findById($id)
    {
        $sql = "SELECT ra.*, ma.nama_barang, mc.conveyor_name, u1.full_name as requester, u2.full_name as approver 
                FROM request_atk ra
                LEFT JOIN master_atk ma ON ra.atk_id = ma.id
                LEFT JOIN master_conveyor mc ON ra.conveyor_id = mc.id
                LEFT JOIN users u1 ON ra.requested_by = u1.id
                LEFT JOIN users u2 ON ra.approved_by = u2.id
                WHERE ra.id = ?";
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
            error_log('RequestATK::create - No fillable columns matched');
            return false;
        }

        $sql = "INSERT INTO request_atk (" . implode(', ', $columns) . ") 
                VALUES (" . implode(', ', $placeholders) . ")";

        try {
            error_log('RequestATK::create - SQL: ' . $sql . ' | Values: ' . json_encode($values));
            Database::query($sql, $values);
            error_log('RequestATK::create - Success for request_number: ' . ($data['request_number'] ?? 'unknown'));
            return true;
        } catch (\Exception $e) {
            error_log('RequestATK::create - Exception: ' . $e->getMessage() . ' | Data: ' . json_encode($data));
            return false;
        }
    }

    /**
     * Update status - STATIC method with MIXED $id
     */
    public static function updateStatus($id, string $status, int $userId, $notes = null): bool
    {
        $approvedAt = null;
        if ($status === 'accepted' || $status === 'completed') {
            $approvedAt = date('Y-m-d H:i:s');
        }

        $sql = "UPDATE request_atk 
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
     * Generate request number - Format: ATK-YYYYMMDD-XXX
     */
    public static function generateRequestNumber(): string
    {
        $date = date('Ymd');
        $today = date('Y-m-d');
        
        // Get count of requests created today
        $sql = "SELECT COUNT(*) as count FROM request_atk 
                WHERE DATE(created_at) = ?";
        $result = Database::row($sql, [$today]);
        $count = ($result->count ?? 0) + 1;
        
        return sprintf('ATK-%s-%03d', $date, $count);
    }

    /**
     * Get request history
     */
    public static function getHistory(int $requestId): array
    {
        $sql = "SELECT rah.*, u.full_name as changed_by_name 
                FROM request_atk_history rah
                LEFT JOIN users u ON rah.changed_by = u.id
                WHERE rah.request_atk_id = ?
                ORDER BY rah.created_at ASC";
        return Database::results($sql, [$requestId]);
    }

    /**
     * Record history - Internal use
     */
    protected static function recordHistory(int $requestId, string $status, int $userId, ?string $notes = null): bool
    {
        $sql = "INSERT INTO request_atk_history (request_atk_id, status, changed_by, notes) 
                VALUES (?, ?, ?, ?)";
        try {
            Database::query($sql, [$requestId, $status, $userId, $notes]);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Count total requests
     */
    public static function count(): int
    {
        $sql = "SELECT COUNT(*) as count FROM request_atk";
        $result = Database::row($sql);
        return $result->count ?? 0;
    }

    /**
     * Count by status
     */
    public static function countByStatus(string $status): int
    {
        $sql = "SELECT COUNT(*) as count FROM request_atk WHERE status = ?";
        $result = Database::row($sql, [$status]);
        return $result->count ?? 0;
    }

    /**
     * Get requests by status and date range
     */
    public static function getByStatusAndDateRange(string $status, string $startDate, string $endDate): array
    {
        $sql = "SELECT ra.*, ma.nama_barang, mc.conveyor_name, u1.full_name as requester, u2.full_name as approver 
                FROM request_atk ra
                LEFT JOIN master_atk ma ON ra.atk_id = ma.id
                LEFT JOIN master_conveyor mc ON ra.conveyor_id = mc.id
                LEFT JOIN users u1 ON ra.requested_by = u1.id
                LEFT JOIN users u2 ON ra.approved_by = u2.id
                WHERE ra.status = ? AND DATE(ra.created_at) BETWEEN ? AND ?
                ORDER BY ra.created_at DESC";
        return Database::results($sql, [$status, $startDate, $endDate]);
    }

    /**
     * Search requests
     */
    public static function search(string $query): array
    {
        $query = "%{$query}%";
        $sql = "SELECT ra.*, ma.nama_barang, mc.conveyor_name, u1.full_name as requester, u2.full_name as approver 
                FROM request_atk ra
                LEFT JOIN master_atk ma ON ra.atk_id = ma.id
                LEFT JOIN master_conveyor mc ON ra.conveyor_id = mc.id
                LEFT JOIN users u1 ON ra.requested_by = u1.id
                LEFT JOIN users u2 ON ra.approved_by = u2.id
                WHERE ra.request_number LIKE ? OR ma.nama_barang LIKE ?
                ORDER BY ra.created_at DESC";
        return Database::results($sql, [$query, $query]);
    }
}

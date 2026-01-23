<?php

namespace App\Models;

use App\Model;
use App\Database;

class RequestMemo extends Model
{
    protected string $table = 'request_memo';
    protected string $primaryKey = 'id';

    protected array $fillable = [
        'request_number',
        'memo_content',
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
        $sql = "SELECT rm.*, u1.full_name as requester, u2.full_name as approver 
                FROM request_memo rm
                LEFT JOIN users u1 ON rm.requested_by = u1.id
                LEFT JOIN users u2 ON rm.approved_by = u2.id
                ORDER BY rm.created_at DESC";
        return Database::results($sql);
    }

    /**
     * Get requests by user
     */
    public static function getByUser(int $userId): array
    {
        $sql = "SELECT rm.*, u1.full_name as requester, u2.full_name as approver 
                FROM request_memo rm
                LEFT JOIN users u1 ON rm.requested_by = u1.id
                LEFT JOIN users u2 ON rm.approved_by = u2.id
                WHERE rm.requested_by = ?
                ORDER BY rm.created_at DESC";
        return Database::results($sql, [$userId]);
    }

    /**
     * Find by ID
     */
    public static function findById($id)
    {
        $sql = "SELECT rm.*, u1.full_name as requester, u2.full_name as approver 
                FROM request_memo rm
                LEFT JOIN users u1 ON rm.requested_by = u1.id
                LEFT JOIN users u2 ON rm.approved_by = u2.id
                WHERE rm.id = ?";
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

        $sql = "INSERT INTO request_memo (" . implode(', ', $columns) . ") 
                VALUES (" . implode(', ', $placeholders) . ")";

        try {
            Database::query($sql, $values);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Update status - STATIC method with MIXED $id
     */
    public static function updateStatus($id, string $status, int $userId, $notes = null): bool
    {
        $approved_at = ($status === 'approved') ? date('Y-m-d H:i:s') : null;
        $approved_by = ($status === 'approved') ? $userId : null;

        $sql = "UPDATE request_memo SET 
                status = ?,
                approved_by = ?,
                approved_at = ?,
                notes = ?,
                updated_at = ?
                WHERE id = ?";

        try {
            Database::query($sql, [
                $status,
                $approved_by,
                $approved_at,
                $notes,
                date('Y-m-d H:i:s'),
                $id
            ]);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Delete - STATIC method with MIXED $id
     */
    public static function delete($id): bool
    {
        $sql = "DELETE FROM request_memo WHERE id = ?";
        try {
            Database::query($sql, [$id]);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Generate unique request number
     * Format: MEMO-YYYYMMDD-XXX
     */
    public static function generateRequestNumber(): string
    {
        $date = date('Ymd');
        $sql = "SELECT COUNT(*) as count FROM request_memo WHERE request_number LIKE ?";
        $result = Database::row($sql, ["MEMO-{$date}-%"]);
        $count = ($result->count ?? 0) + 1;
        $sequence = str_pad($count, 3, '0', STR_PAD_LEFT);
        
        return "MEMO-{$date}-{$sequence}";
    }

    /**
     * Get history for a request
     */
    public static function getHistory(int $requestId): array
    {
        $sql = "SELECT rmh.*, u.full_name as changed_by_name 
                FROM request_memo_history rmh
                LEFT JOIN users u ON rmh.changed_by = u.id
                WHERE rmh.request_memo_id = ?
                ORDER BY rmh.created_at ASC";
        return Database::results($sql, [$requestId]);
    }

    /**
     * Count records
     */
    public static function count(): int
    {
        $sql = "SELECT COUNT(*) as count FROM request_memo";
        $result = Database::row($sql);
        return $result->count ?? 0;
    }

    /**
     * Add history record
     */
    public static function addHistory(int $requestId, string $status, int $userId, ?string $notes = null): bool
    {
        $sql = "INSERT INTO request_memo_history (request_memo_id, status, changed_by, notes, created_at)
                VALUES (?, ?, ?, ?, NOW())";

        try {
            Database::query($sql, [$requestId, $status, $userId, $notes]);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Search requests
     */
    public static function search(string $query): array
    {
        $query = "%{$query}%";
        $sql = "SELECT rm.*, u1.full_name as requester, u2.full_name as approver 
                FROM request_memo rm
                LEFT JOIN users u1 ON rm.requested_by = u1.id
                LEFT JOIN users u2 ON rm.approved_by = u2.id
                WHERE rm.request_number LIKE ? OR rm.memo_content LIKE ?
                ORDER BY rm.created_at DESC";
        return Database::results($sql, [$query, $query]);
    }

    /**
     * Count by status
     */
    public static function countByStatus(string $status): int
    {
        $sql = "SELECT COUNT(*) as count FROM request_memo WHERE status = ?";
        $result = Database::row($sql, [$status]);
        return $result->count ?? 0;
    }
}

<?php

namespace App\Models;

use App\Model;

/**
 * ProductionRequest Model
 */
class ProductionRequest extends Model
{
    protected string $table = 'production_requests';
    protected string $primaryKey = 'id';

    protected array $fillable = [
        'user_id',
        'title',
        'description',
        'status',
        'priority',
        'assigned_to',
        'start_date',
        'end_date',
    ];

    /**
     * Get requests by status
     */
    public static function getByStatus(string $status): array
    {
        $sql = "SELECT * FROM production_requests WHERE status = ? ORDER BY created_at DESC";
        return \App\Database::query($sql, [$status])->fetchAll(\PDO::FETCH_OBJ);
    }

    /**
     * Get requests by user
     */
    public static function getByUser(int $userId): array
    {
        $sql = "SELECT * FROM production_requests WHERE user_id = ? ORDER BY created_at DESC";
        return \App\Database::query($sql, [$userId])->fetchAll(\PDO::FETCH_OBJ);
    }

    /**
     * Get requests assigned to user
     */
    public static function getAssignedTo(int $userId): array
    {
        $sql = "SELECT * FROM production_requests WHERE assigned_to = ? ORDER BY created_at DESC";
        return \App\Database::query($sql, [$userId])->fetchAll(\PDO::FETCH_OBJ);
    }

    /**
     * Get high priority requests
     */
    public static function getHighPriority(): array
    {
        $sql = "SELECT * FROM production_requests WHERE priority IN ('high', 'urgent') AND status != 'completed' ORDER BY priority DESC";
        return \App\Database::results($sql);
    }
}

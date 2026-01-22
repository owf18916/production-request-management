<?php

namespace App\Models;

use App\Model;
use App\Database;
use PDO;

/**
 * Conveyor Model
 * Handles conveyor management and user assignments
 */
class Conveyor extends Model
{
    protected string $table = 'master_conveyor';
    protected string $primaryKey = 'id';

    protected array $fillable = [
        'conveyor_name',
        'status',
        'created_by',
    ];

    /**
     * Get all conveyors
     */
    public static function getAll(): array
    {
        $sql = "SELECT mc.id, mc.conveyor_name, mc.status, mc.created_by, u.full_name as created_by_name, 
                mc.created_at, mc.updated_at
                FROM master_conveyor mc
                JOIN users u ON mc.created_by = u.id
                ORDER BY mc.conveyor_name ASC";
        return Database::results($sql);
    }

    /**
     * Get only active conveyors
     */
    public static function getActive(): array
    {
        $sql = "SELECT mc.id, mc.conveyor_name, mc.status, mc.created_by, u.full_name as created_by_name,
                mc.created_at, mc.updated_at
                FROM master_conveyor mc
                JOIN users u ON mc.created_by = u.id
                WHERE mc.status = 'active'
                ORDER BY mc.conveyor_name ASC";
        return Database::results($sql);
    }

    /**
     * Find conveyor by ID
     */
    public static function findById(int $id): ?object
    {
        $sql = "SELECT mc.id, mc.conveyor_name, mc.status, mc.created_by, u.full_name as created_by_name,
                mc.created_at, mc.updated_at
                FROM master_conveyor mc
                JOIN users u ON mc.created_by = u.id
                WHERE mc.id = ?";
        return Database::row($sql, [$id]);
    }

    /**
     * Create a new conveyor
     */
    public static function createConveyor(array $data): bool
    {
        return self::create($data);
    }

    /**
     * Update conveyor
     */
    public static function updateConveyor(int $id, array $data): bool
    {
        // Remove created_by from update data
        unset($data['created_by']);
        return self::update($id, $data);
    }

    /**
     * Delete conveyor
     */
    public static function deleteConveyor(int $id): bool
    {
        return self::delete($id);
    }

    /**
     * Get all users assigned to a conveyor
     */
    public static function getConveyorUsers(int $conveyorId): array
    {
        $sql = "SELECT u.id, u.nik, u.username, u.full_name, u.role, u.created_at
                FROM users u
                JOIN user_conveyor uc ON u.id = uc.user_id
                WHERE uc.conveyor_id = ?
                ORDER BY u.full_name ASC";
        return Database::results($sql, [$conveyorId]);
    }

    /**
     * Get count of users in a conveyor
     */
    public static function getUserCount(int $conveyorId): int
    {
        $sql = "SELECT COUNT(*) as count FROM user_conveyor WHERE conveyor_id = ?";
        $result = Database::row($sql, [$conveyorId]);
        return (int) $result->count;
    }

    /**
     * Check if conveyor name is unique (excluding current ID)
     */
    public static function isUniqueConveyorName(string $name, ?int $excludeId = null): bool
    {
        if ($excludeId) {
            $sql = "SELECT COUNT(*) as count FROM master_conveyor WHERE conveyor_name = ? AND id != ?";
            $result = Database::row($sql, [$name, $excludeId]);
        } else {
            $sql = "SELECT COUNT(*) as count FROM master_conveyor WHERE conveyor_name = ?";
            $result = Database::row($sql, [$name]);
        }
        return $result->count == 0;
    }

    /**
     * Get conveyors with user count (for listing)
     */
    public static function getAllWithUserCount(): array
    {
        $sql = "SELECT mc.id, mc.conveyor_name, mc.status, mc.created_by, u.full_name as created_by_name,
                mc.created_at, mc.updated_at,
                COUNT(uc.id) as users_count
                FROM master_conveyor mc
                JOIN users u ON mc.created_by = u.id
                LEFT JOIN user_conveyor uc ON mc.id = uc.conveyor_id
                GROUP BY mc.id
                ORDER BY mc.conveyor_name ASC";
        return Database::results($sql);
    }

    /**
     * Get conveyors by status
     */
    public static function getByStatus(string $status): array
    {
        $sql = "SELECT mc.id, mc.conveyor_name, mc.status, mc.created_by, u.full_name as created_by_name,
                mc.created_at, mc.updated_at
                FROM master_conveyor mc
                JOIN users u ON mc.created_by = u.id
                WHERE mc.status = ?
                ORDER BY mc.conveyor_name ASC";
        return Database::results($sql, [$status]);
    }

    /**
     * Search conveyors by name
     */
    public static function search(string $keyword): array
    {
        $sql = "SELECT mc.id, mc.conveyor_name, mc.status, mc.created_by, u.full_name as created_by_name,
                mc.created_at, mc.updated_at,
                COUNT(uc.id) as users_count
                FROM master_conveyor mc
                JOIN users u ON mc.created_by = u.id
                LEFT JOIN user_conveyor uc ON mc.id = uc.conveyor_id
                WHERE mc.conveyor_name LIKE ?
                GROUP BY mc.id
                ORDER BY mc.conveyor_name ASC";
        return Database::results($sql, ["%$keyword%"]);
    }

    /**
     * Get users not yet assigned to a conveyor
     */
    public static function getUsersNotInConveyor(int $conveyorId): array
    {
        $sql = "SELECT u.id, u.nik, u.username, u.full_name, u.role
                FROM users u
                WHERE u.id NOT IN (
                    SELECT user_id FROM user_conveyor WHERE conveyor_id = ?
                )
                AND u.role = 'pic'
                ORDER BY u.full_name ASC";
        return Database::results($sql, [$conveyorId]);
    }

    /**
     * Check if conveyor has assigned users
     */
    public static function hasUsers(int $conveyorId): bool
    {
        $sql = "SELECT COUNT(*) as count FROM user_conveyor WHERE conveyor_id = ?";
        $result = Database::row($sql, [$conveyorId]);
        return (int) $result->count > 0;
    }

    /**
     * Toggle status between active and inactive
     */
    public static function toggleStatus(int $id): bool
    {
        $conveyor = self::findById($id);
        if (!$conveyor) {
            return false;
        }

        $newStatus = $conveyor->status === 'active' ? 'inactive' : 'active';
        return self::update($id, ['status' => $newStatus]);
    }
}

<?php

namespace App\Models;

use App\Model;
use App\Security;
use App\Database;
use PDO;

/**
 * User Model
 * Handles user authentication, authorization, and conveyor management
 */
class User extends Model
{
    protected string $table = 'users';
    protected string $primaryKey = 'id';

    protected array $fillable = [
        'nik',
        'username',
        'password',
        'full_name',
        'role',
        'last_login_at',
    ];

    protected array $hidden = [
        'password',
    ];

    /**
     * Authenticate user with username/nik and password
     */
    public static function authenticate(string $identifier, string $password): ?object
    {
        // Try to find by username first, then by NIK
        $user = self::findByUsername($identifier);
        if (!$user) {
            $user = self::findByNIK($identifier);
        }

        if (!$user) {
            return null;
        }

        // Verify password
        if (!Security::verifyPassword($password, $user->password)) {
            return null;
        }

        // Update last login
        self::update($user->id, ['last_login_at' => date('Y-m-d H:i:s')]);

        // Return user without password
        $user = self::getUserById($user->id);
        return $user;
    }

    /**
     * Get user by ID
     */
    public static function getUserById(int $id): ?object
    {
        $sql = "SELECT id, nik, username, full_name, role, last_login_at, created_at, updated_at FROM users WHERE id = ?";
        return Database::row($sql, [$id]);
    }

    /**
     * Get user by username
     */
    public static function findByUsername(string $username): ?object
    {
        $sql = "SELECT * FROM users WHERE username = ?";
        return Database::row($sql, [$username]);
    }

    /**
     * Get user by NIK
     */
    public static function findByNIK(string $nik): ?object
    {
        $sql = "SELECT * FROM users WHERE nik = ?";
        return Database::row($sql, [$nik]);
    }

    /**
     * Get all conveyors for a user
     */
    public static function getUserConveyors(int $userId): array
    {
        $sql = "SELECT mc.id, mc.conveyor_name, mc.status, mc.created_at, mc.updated_at
                FROM master_conveyor mc
                JOIN user_conveyor uc ON mc.id = uc.conveyor_id
                WHERE uc.user_id = ?
                ORDER BY mc.conveyor_name ASC";
        return Database::results($sql, [$userId]);
    }

    /**
     * Assign a conveyor to a user
     */
    public static function assignConveyor(int $userId, int $conveyorId): bool
    {
        $sql = "INSERT INTO user_conveyor (user_id, conveyor_id, created_at)
                VALUES (?, ?, NOW())
                ON DUPLICATE KEY UPDATE created_at = NOW()";
        try {
            Database::query($sql, [$userId, $conveyorId]);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Remove a conveyor from a user
     */
    public static function removeConveyor(int $userId, int $conveyorId): bool
    {
        $sql = "DELETE FROM user_conveyor WHERE user_id = ? AND conveyor_id = ?";
        return Database::query($sql, [$userId, $conveyorId])->rowCount() > 0;
    }

    /**
     * Check if user has access to a conveyor
     */
    public static function hasConveyorAccess(int $userId, int $conveyorId): bool
    {
        $sql = "SELECT COUNT(*) as count FROM user_conveyor WHERE user_id = ? AND conveyor_id = ?";
        $result = Database::row($sql, [$userId, $conveyorId]);
        return $result->count > 0;
    }

    /**
     * Create a new user
     */
    public static function createUser(array $data): bool
    {
        if (isset($data['password'])) {
            $data['password'] = Security::hashPassword($data['password']);
        }
        return self::create($data);
    }

    /**
     * Get all users with a specific role
     */
    public static function getByRole(string $role): array
    {
        $sql = "SELECT id, nik, username, full_name, role, last_login_at, created_at, updated_at 
                FROM users WHERE role = ? 
                ORDER BY full_name ASC";
        return Database::results($sql, [$role]);
    }

    /**
     * Get all active users
     */
    public static function getActive(): array
    {
        $sql = "SELECT id, nik, username, full_name, role, last_login_at, created_at, updated_at 
                FROM users 
                ORDER BY full_name ASC";
        return Database::results($sql);
    }

    /**
     * Get all users (for admin user management)
     */
    public static function getAll(): array
    {
        $sql = "SELECT id, nik, username, full_name, role, last_login_at, created_at, updated_at 
                FROM users 
                ORDER BY full_name ASC";
        return Database::results($sql);
    }

    /**
     * Get all users with their conveyor assignments
     */
    public static function getAllWithConveyors(): array
    {
        $sql = "SELECT u.id, u.nik, u.username, u.full_name, u.role, u.last_login_at, u.created_at, u.updated_at,
                       GROUP_CONCAT(mc.id) as conveyor_ids,
                       GROUP_CONCAT(mc.conveyor_name) as conveyor_names,
                       GROUP_CONCAT(mc.status) as conveyor_statuses
                FROM users u
                LEFT JOIN user_conveyor uc ON u.id = uc.user_id
                LEFT JOIN master_conveyor mc ON uc.conveyor_id = mc.id
                GROUP BY u.id
                ORDER BY u.full_name ASC";
        return Database::results($sql);
    }

    /**
     * Create a new user (base create method)
     */
    public static function create(array $data): bool
    {
        if (isset($data['password'])) {
            $data['password'] = Security::hashPassword($data['password']);
        }

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
            return false;
        }

        $sql = "INSERT INTO users (" . implode(', ', $columns) . ") 
                VALUES (" . implode(', ', $placeholders) . ")";

        try {
            Database::query($sql, $values);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Update a user by ID
     */
    public static function update($id, array $data): bool
    {
        if (isset($data['password'])) {
            $data['password'] = Security::hashPassword($data['password']);
        }

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

        $values[] = $id;
        $sql = "UPDATE users SET " . implode(', ', $updates) . " WHERE id = ?";

        try {
            Database::query($sql, $values);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Delete a user by ID
     */
    public static function delete($id): bool
    {
        $sql = "DELETE FROM users WHERE id = ?";
        try {
            $result = Database::query($sql, [$id]);
            return $result->rowCount() > 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Sync user conveyors (bulk assign/remove)
     * Replaces current conveyor assignments with new ones
     */
    public static function syncConveyors(int $userId, array $conveyorIds = []): bool
    {
        try {
            // Delete all current assignments
            $deleteSql = "DELETE FROM user_conveyor WHERE user_id = ?";
            Database::query($deleteSql, [$userId]);

            // Insert new assignments
            if (!empty($conveyorIds)) {
                $conveyorIds = array_filter($conveyorIds); // Remove empty values
                
                foreach ($conveyorIds as $conveyorId) {
                    $insertSql = "INSERT INTO user_conveyor (user_id, conveyor_id, created_at)
                                  VALUES (?, ?, NOW())";
                    Database::query($insertSql, [$userId, (int)$conveyorId]);
                }
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Update user password
     */
    public static function updatePassword(int $userId, string $newPassword): bool
    {
        $hashedPassword = Security::hashPassword($newPassword);
        $sql = "UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?";
        
        try {
            Database::query($sql, [$hashedPassword, $userId]);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Check if username exists (excluding current user)
     */
    public static function usernameExists(string $username, ?int $excludeId = null): bool
    {
        if ($excludeId !== null) {
            $sql = "SELECT COUNT(*) as count FROM users WHERE username = ? AND id != ?";
            $result = Database::row($sql, [$username, $excludeId]);
        } else {
            $sql = "SELECT COUNT(*) as count FROM users WHERE username = ?";
            $result = Database::row($sql, [$username]);
        }
        return $result->count > 0;
    }

    /**
     * Check if NIK exists (excluding current user)
     */
    public static function nikExists(string $nik, ?int $excludeId = null): bool
    {
        if ($excludeId !== null) {
            $sql = "SELECT COUNT(*) as count FROM users WHERE nik = ? AND id != ?";
            $result = Database::row($sql, [$nik, $excludeId]);
        } else {
            $sql = "SELECT COUNT(*) as count FROM users WHERE nik = ?";
            $result = Database::row($sql, [$nik]);
        }
        return $result->count > 0;
    }

    /**
     * Search users by NIK, username, or full name
     */
    public static function search(string $query): array
    {
        $searchTerm = "%$query%";
        $sql = "SELECT id, nik, username, full_name, role, last_login_at, created_at, updated_at 
                FROM users 
                WHERE nik LIKE ? OR username LIKE ? OR full_name LIKE ?
                ORDER BY full_name ASC";
        return Database::results($sql, [$searchTerm, $searchTerm, $searchTerm]);
    }

    /**
     * Count total users
     */
    public static function count(): int
    {
        $sql = "SELECT COUNT(*) as total FROM users";
        $result = Database::row($sql);
        return (int)$result->total;
    }
}


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
}


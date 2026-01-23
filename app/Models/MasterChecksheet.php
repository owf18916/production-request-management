<?php

namespace App\Models;

use App\Model;
use App\Database;

/**
 * MasterChecksheet Model
 * Handles Master Checksheet data operations
 */
class MasterChecksheet extends Model
{
    protected string $table = 'master_checksheet';
    protected string $primaryKey = 'id';

    protected array $fillable = [
        'kode_checksheet',
        'nama_checksheet',
        'created_by',
    ];

    protected array $hidden = [];

    /**
     * Get all Checksheets
     */
    public static function getAll(): array
    {
        $sql = "SELECT id, kode_checksheet, nama_checksheet, created_at, updated_at, created_by 
                FROM master_checksheet 
                ORDER BY nama_checksheet ASC";
        return Database::results($sql);
    }

    /**
     * Get Checksheet by ID
     */
    public static function findById(int $id): ?object
    {
        $sql = "SELECT id, kode_checksheet, nama_checksheet, created_at, updated_at, created_by 
                FROM master_checksheet 
                WHERE id = ?";
        return Database::row($sql, [$id]);
    }

    /**
     * Create new Checksheet
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
            return false;
        }

        // Add timestamps
        $columns[] = 'created_at';
        $columns[] = 'updated_at';
        $values[] = date('Y-m-d H:i:s');
        $values[] = date('Y-m-d H:i:s');
        $placeholders[] = '?';
        $placeholders[] = '?';

        $sql = "INSERT INTO master_checksheet (" . implode(', ', $columns) . ") 
                VALUES (" . implode(', ', $placeholders) . ")";

        try {
            Database::query($sql, $values);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Update Checksheet (static method with mixed $id - compatible with parent)
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

        // Add updated_at
        $updates[] = "updated_at = ?";
        $values[] = date('Y-m-d H:i:s');

        $values[] = $id;
        $sql = "UPDATE master_checksheet SET " . implode(', ', $updates) . " WHERE id = ?";

        try {
            Database::query($sql, $values);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Delete Checksheet (static method with mixed $id - compatible with parent)
     */
    public static function delete($id): bool
    {
        $sql = "DELETE FROM master_checksheet WHERE id = ?";
        try {
            $result = Database::query($sql, [$id]);
            return $result->rowCount() > 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Search Checksheet by kode_checksheet or nama_checksheet
     */
    public static function search(string $keyword): array
    {
        $searchTerm = "%$keyword%";
        $sql = "SELECT id, kode_checksheet, nama_checksheet, created_at, updated_at, created_by 
                FROM master_checksheet 
                WHERE kode_checksheet LIKE ? OR nama_checksheet LIKE ?
                ORDER BY nama_checksheet ASC";
        return Database::results($sql, [$searchTerm, $searchTerm]);
    }

    /**
     * Check if kode_checksheet exists (excluding current record)
     */
    public static function kodeChecksheetExists(string $kodeChecksheet, ?int $excludeId = null): bool
    {
        if ($excludeId !== null) {
            $sql = "SELECT COUNT(*) as count FROM master_checksheet WHERE kode_checksheet = ? AND id != ?";
            $result = Database::row($sql, [$kodeChecksheet, $excludeId]);
        } else {
            $sql = "SELECT COUNT(*) as count FROM master_checksheet WHERE kode_checksheet = ?";
            $result = Database::row($sql, [$kodeChecksheet]);
        }
        return $result->count > 0;
    }

    /**
     * Count total Checksheets
     */
    public static function count(): int
    {
        $sql = "SELECT COUNT(*) as total FROM master_checksheet";
        $result = Database::row($sql);
        return (int)$result->total;
    }
}

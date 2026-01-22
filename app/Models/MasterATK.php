<?php

namespace App\Models;

use App\Model;
use App\Database;

/**
 * MasterATK Model
 * Handles Master ATK (Alat Tulis Kantor) data operations
 */
class MasterATK extends Model
{
    protected string $table = 'master_atk';
    protected string $primaryKey = 'id';

    protected array $fillable = [
        'kode_barang',
        'nama_barang',
        'created_by',
    ];

    protected array $hidden = [];

    /**
     * Get all ATK
     */
    public static function getAll(): array
    {
        $sql = "SELECT id, kode_barang, nama_barang, created_at, updated_at, created_by 
                FROM master_atk 
                ORDER BY nama_barang ASC";
        return Database::results($sql);
    }

    /**
     * Get ATK by ID
     */
    public static function findById(int $id): ?object
    {
        $sql = "SELECT id, kode_barang, nama_barang, created_at, updated_at, created_by 
                FROM master_atk 
                WHERE id = ?";
        return Database::row($sql, [$id]);
    }

    /**
     * Create new ATK
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

        $sql = "INSERT INTO master_atk (" . implode(', ', $columns) . ") 
                VALUES (" . implode(', ', $placeholders) . ")";

        try {
            Database::query($sql, $values);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Update ATK
     */
    public static function update(mixed $id, array $data): bool
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
        $sql = "UPDATE master_atk SET " . implode(', ', $updates) . " WHERE id = ?";

        try {
            Database::query($sql, $values);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Delete ATK
     */
    public static function delete(mixed $id): bool
    {
        $sql = "DELETE FROM master_atk WHERE id = ?";
        try {
            $result = Database::query($sql, [$id]);
            return $result->rowCount() > 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Search ATK by kode_barang or nama_barang
     */
    public static function search(string $keyword): array
    {
        $searchTerm = "%$keyword%";
        $sql = "SELECT id, kode_barang, nama_barang, created_at, updated_at, created_by 
                FROM master_atk 
                WHERE kode_barang LIKE ? OR nama_barang LIKE ?
                ORDER BY nama_barang ASC";
        return Database::results($sql, [$searchTerm, $searchTerm]);
    }

    /**
     * Check if kode_barang exists (excluding current record)
     */
    public static function kodeBarangExists(string $kodeBarang, ?int $excludeId = null): bool
    {
        if ($excludeId !== null) {
            $sql = "SELECT COUNT(*) as count FROM master_atk WHERE kode_barang = ? AND id != ?";
            $result = Database::row($sql, [$kodeBarang, $excludeId]);
        } else {
            $sql = "SELECT COUNT(*) as count FROM master_atk WHERE kode_barang = ?";
            $result = Database::row($sql, [$kodeBarang]);
        }
        return $result->count > 0;
    }

    /**
     * Count total ATK
     */
    public static function count(): int
    {
        $sql = "SELECT COUNT(*) as total FROM master_atk";
        $result = Database::row($sql);
        return (int)$result->total;
    }
}

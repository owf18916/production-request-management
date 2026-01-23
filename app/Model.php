<?php

namespace App;

use PDO;

/**
 * Base Model Class
 * Provides common database operations for all models
 */
abstract class Model
{
    /**
     * Table name
     */
    protected string $table;

    /**
     * Primary key
     */
    protected string $primaryKey = 'id';

    /**
     * Fillable attributes
     */
    protected array $fillable = [];

    /**
     * Model attributes
     */
    protected array $attributes = [];

    /**
     * Hidden attributes
     */
    protected array $hidden = [];

    /**
     * Casts
     */
    protected array $casts = [];

    /**
     * Check if table is set
     */
    public function __construct()
    {
        if (!isset($this->table)) {
            throw new \Exception('Table name must be defined in model');
        }
    }

    /**
     * Find by primary key
     */
    public static function find($id)
    {
        $instance = new static();
        $sql = "SELECT * FROM {$instance->table} WHERE {$instance->primaryKey} = ?";
        return Database::row($sql, [$id]);
    }

    /**
     * Find by attribute
     */
    public static function findBy(string $attribute, $value)
    {
        $instance = new static();
        $sql = "SELECT * FROM {$instance->table} WHERE $attribute = ?";
        return Database::row($sql, [$value]);
    }

    /**
     * Get all records
     */
    public static function all(): array
    {
        $instance = new static();
        $sql = "SELECT * FROM {$instance->table}";
        return Database::results($sql);
    }

    /**
     * Get with pagination
     */
    public static function paginate(int $perPage = 15, int $page = 1): array
    {
        $instance = new static();
        $offset = ($page - 1) * $perPage;

        $total = (int) Database::row(
            "SELECT COUNT(*) as count FROM {$instance->table}"
        )->count;

        $sql = "SELECT * FROM {$instance->table} LIMIT ? OFFSET ?";
        $results = Database::query($sql, [$perPage, $offset])->fetchAll(PDO::FETCH_OBJ);

        return [
            'data' => $results,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'total_pages' => ceil($total / $perPage),
        ];
    }

    /**
     * Create a new record
     */
    public static function create(array $data): bool
    {
        $instance = new static();
        $fillable = $instance->fillable;

        $columns = [];
        $values = [];
        $params = [];

        foreach ($data as $key => $value) {
            if (empty($fillable) || in_array($key, $fillable)) {
                $columns[] = $key;
                $values[] = '?';
                $params[] = $value;
            }
        }

        $columnStr = implode(',', $columns);
        $valuesStr = implode(',', $values);
        $sql = "INSERT INTO {$instance->table} ($columnStr) VALUES ($valuesStr)";

        return Database::query($sql, $params)->rowCount() > 0;
    }

    /**
     * Update a record
     */
    public static function update($id, array $data): bool
    {
        $instance = new static();
        $fillable = $instance->fillable;

        $sets = [];
        $params = [];

        foreach ($data as $key => $value) {
            if (empty($fillable) || in_array($key, $fillable)) {
                $sets[] = "$key = ?";
                $params[] = $value;
            }
        }

        $params[] = $id;
        $setStr = implode(',', $sets);
        $sql = "UPDATE {$instance->table} SET $setStr WHERE {$instance->primaryKey} = ?";

        return Database::query($sql, $params)->rowCount() > 0;
    }

    /**
     * Delete a record
     */
    public static function delete($id): bool
    {
        $instance = new static();
        $sql = "DELETE FROM {$instance->table} WHERE {$instance->primaryKey} = ?";
        return Database::query($sql, [$id])->rowCount() > 0;
    }

    /**
     * Execute raw query
     */
    public static function query(string $sql, array $params = [])
    {
        return Database::query($sql, $params);
    }

    /**
     * Count total records
     */
    public static function count(): int
    {
        $instance = new static();
        return (int) Database::row(
            "SELECT COUNT(*) as count FROM {$instance->table}"
        )->count;
    }

    /**
     * Set attribute
     */
    public function __set(string $key, $value)
    {
        $this->attributes[$key] = $value;
    }

    /**
     * Get attribute
     */
    public function __get(string $key)
    {
        return $this->attributes[$key] ?? null;
    }

    /**
     * Convert model to array
     */
    public function toArray(): array
    {
        return array_diff_key($this->attributes, array_flip($this->hidden));
    }

    /**
     * Convert model to JSON
     */
    public function toJson(): string
    {
        return json_encode($this->toArray());
    }
}

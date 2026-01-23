<?php

namespace App;

use PDO;
use PDOException;

/**
 * Database Connection Manager
 * Handles PDO connections with prepared statements
 */
class Database
{
    private static ?PDO $instance = null;
    private PDO $pdo;

    private function __construct()
    {
        $config = require __DIR__ . '/../config/database.php';
        $db = $config['mysql'];
        $options = $config['options'];

        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $db['host'],
            $db['port'],
            $db['database'],
            $db['charset']
        );

        try {
            $this->pdo = new PDO($dsn, $db['username'], $db['password'], $options);
        } catch (PDOException $e) {
            die('Database connection failed: ' . $e->getMessage());
        }
    }

    /**
     * Get singleton instance
     */
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $db = new self();
            self::$instance = $db->pdo;
        }
        return self::$instance;
    }

    /**
     * Prevent cloning
     */
    private function __clone()
    {
    }

    /**
     * Prevent unserializing
     */
    public function __wakeup()
    {
        throw new \Exception('Cannot unserialize a singleton');
    }

    /**
     * Execute a prepared statement
     */
    public static function query(string $sql, array $params = []): \PDOStatement
    {
        $pdo = self::getInstance();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Fetch a single row
     */
    public static function row(string $sql, array $params = [])
    {
        $result = self::query($sql, $params)->fetchObject();
        return $result === false ? null : $result;
    }

    /**
     * Fetch all rows
     */
    public static function results(string $sql, array $params = []): array
    {
        return self::query($sql, $params)->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Get last inserted ID
     */
    public static function lastId(): string
    {
        return self::getInstance()->lastInsertId();
    }

    /**
     * Get affected rows count
     */
    public static function rowCount(string $sql, array $params = []): int
    {
        return self::query($sql, $params)->rowCount();
    }
}

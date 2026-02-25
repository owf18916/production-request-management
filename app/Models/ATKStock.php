<?php

namespace App\Models;

use App\Model;
use App\Database;

/**
 * ATKStock Model
 * Handles ATK stock management and transactions
 */
class ATKStock extends Model
{
    protected string $table = 'atk_stock';
    protected string $primaryKey = 'id';

    protected array $fillable = [
        'atk_id',
        'beginning_stock',
        'in_qty',
        'out_qty',
        'adjustment',
        'last_stocktake_date',
        'notes',
        'updated_by',
    ];

    /**
     * Get stock by ATK ID
     */
    public static function findByAtkId(int $atkId): ?object
    {
        $sql = "SELECT * FROM atk_stock WHERE atk_id = ?";
        return Database::row($sql, [$atkId]);
    }

    /**
     * Get ending stock for an ATK item
     */
    public static function getEndingStock(int $atkId): int
    {
        $stock = self::findByAtkId($atkId);
        if (!$stock) {
            return 0;
        }
        return $stock->ending_stock ?? 0;
    }

    /**
     * Initialize stock for new ATK item
     */
    public static function initializeStock(int $atkId, int $userId): bool
    {
        $sql = "INSERT INTO atk_stock (atk_id, beginning_stock, updated_by) 
                VALUES (?, 0, ?)";
        try {
            Database::query($sql, [$atkId, $userId]);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Add incoming stock
     */
    public static function addIncoming(int $atkId, int $qty, int $userId, ?string $notes = null): bool
    {
        // Get current stock
        $stock = self::findByAtkId($atkId);
        if (!$stock) {
            return false;
        }

        $previousBalance = $stock->ending_stock;
        $newBalance = $previousBalance + $qty;

        // Update stock
        $updateSql = "UPDATE atk_stock SET in_qty = in_qty + ?, updated_by = ? 
                      WHERE atk_id = ?";
        
        try {
            Database::query($updateSql, [$qty, $userId, $atkId]);
            
            // Recalculate ending stock
            self::updateEndingStock($atkId);

            // Create transaction record
            self::createTransaction(
                $atkId,
                'incoming',
                $qty,
                $previousBalance,
                $newBalance,
                null,
                null,
                $notes,
                $userId
            );

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Reduce stock when request approved
     */
    public static function reduceStock(int $atkId, int $qty, int $requestId, int $userId, ?string $notes = null): bool
    {
        // Check if stock available
        $stock = self::findByAtkId($atkId);
        if (!$stock || $stock->ending_stock < $qty) {
            return false;
        }

        $previousBalance = $stock->ending_stock;
        $newBalance = $previousBalance - $qty;

        $updateSql = "UPDATE atk_stock SET out_qty = out_qty + ?, updated_by = ? 
                      WHERE atk_id = ?";

        try {
            Database::query($updateSql, [$qty, $userId, $atkId]);
            
            // Recalculate ending stock
            self::updateEndingStock($atkId);

            // Create transaction record
            self::createTransaction(
                $atkId,
                'out',
                $qty,
                $previousBalance,
                $newBalance,
                'request_atk',
                $requestId,
                $notes,
                $userId
            );

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Restore stock when request cancelled (after approved)
     */
    public static function restoreStock(int $atkId, int $qty, int $requestId, int $userId, ?string $notes = null): bool
    {
        $stock = self::findByAtkId($atkId);
        if (!$stock) {
            return false;
        }

        $previousBalance = $stock->ending_stock;
        $newBalance = $previousBalance + $qty;

        $updateSql = "UPDATE atk_stock SET out_qty = out_qty - ?, updated_by = ? 
                      WHERE atk_id = ?";

        try {
            Database::query($updateSql, [$qty, $userId, $atkId]);
            
            // Recalculate ending stock
            self::updateEndingStock($atkId);

            // Create transaction record
            self::createTransaction(
                $atkId,
                'restore',
                $qty,
                $previousBalance,
                $newBalance,
                'request_atk',
                $requestId,
                $notes,
                $userId
            );

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Add manual adjustment
     */
    public static function addAdjustment(int $atkId, int $qty, int $userId, string $notes = ''): bool
    {
        $stock = self::findByAtkId($atkId);
        if (!$stock) {
            return false;
        }

        $previousBalance = $stock->ending_stock;
        $newBalance = $previousBalance + $qty;

        $updateSql = "UPDATE atk_stock SET adjustment = adjustment + ?, updated_by = ? 
                      WHERE atk_id = ?";

        try {
            Database::query($updateSql, [$qty, $userId, $atkId]);
            
            // Recalculate ending stock
            self::updateEndingStock($atkId);

            // Create transaction record
            self::createTransaction(
                $atkId,
                'adjustment',
                $qty,
                $previousBalance,
                $newBalance,
                null,
                null,
                $notes,
                $userId
            );

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Create transaction record
     */
    private static function createTransaction(
        int $atkId,
        string $type,
        int $qty,
        int $previousBalance,
        int $newBalance,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?string $notes = null,
        int $userId = 0
    ): bool {
        $sql = "INSERT INTO atk_stock_transaction 
                (atk_id, transaction_type, qty, previous_balance, new_balance, reference_type, reference_id, notes, created_by) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        try {
            Database::query($sql, [
                $atkId,
                $type,
                $qty,
                $previousBalance,
                $newBalance,
                $referenceType,
                $referenceId,
                $notes,
                $userId
            ]);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get transaction history for an ATK
     */
    public static function getTransactionHistory(int $atkId, int $limit = 50): array
    {
        $sql = "SELECT ast.*, u.full_name as created_by_name
                FROM atk_stock_transaction ast
                LEFT JOIN users u ON ast.created_by = u.id
                WHERE ast.atk_id = ?
                ORDER BY ast.created_at DESC
                LIMIT ?";
        return Database::results($sql, [$atkId, $limit]);
    }

    /**
     * Get all stock with ATK info
     */
    public static function getAllWithATKInfo(): array
    {
        $sql = "SELECT ast.*, ma.kode_barang, ma.nama_barang
                FROM atk_stock ast
                LEFT JOIN master_atk ma ON ast.atk_id = ma.id
                ORDER BY ma.nama_barang ASC";
        return Database::results($sql);
    }

    /**
     * Get stock status report
     */
    public static function getStockReport(): array
    {
        return self::getAllWithATKInfo();
    }

    /**
     * Private helper: Update ending balance based on formula
     */
    private static function updateEndingStock(int $atkId): bool
    {
        $sql = "UPDATE atk_stock 
                SET ending_stock = beginning_stock + in_qty - out_qty + adjustment,
                    updated_at = NOW()
                WHERE atk_id = ?";
        try {
            Database::query($sql, [$atkId]);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}

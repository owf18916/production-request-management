<?php

namespace App\Models;

use App\Model;
use App\Database;
use DateTime;

/**
 * Dashboard Model
 * Handles statistics and data retrieval for dashboard views
 */
class Dashboard extends Model
{
    /**
     * Get count of requests by status
     */
    public static function getRequestCountByStatus(string $status, string $table): int
    {
        $sql = "SELECT COUNT(*) as total FROM $table WHERE status = ?";
        $result = Database::row($sql, [$status]);
        return $result ? (int)$result->total : 0;
    }

    /**
     * Get count of requests by status for today
     */
    public static function getRequestCountByStatusToday(string $status, string $table): int
    {
        $sql = "SELECT COUNT(*) as total FROM $table 
                WHERE status = ? AND DATE(created_at) = CURDATE()";
        $result = Database::row($sql, [$status]);
        return $result ? (int)$result->total : 0;
    }

    /**
     * Get count of requests by status for this month
     */
    public static function getRequestCountByStatusThisMonth(string $status, string $table): int
    {
        $sql = "SELECT COUNT(*) as total FROM $table 
                WHERE status = ? AND MONTH(created_at) = MONTH(NOW()) 
                AND YEAR(created_at) = YEAR(NOW())";
        $result = Database::row($sql, [$status]);
        return $result ? (int)$result->total : 0;
    }

    /**
     * Get total pending requests for admin (all types)
     */
    public static function getTotalPendingRequestsAdmin(): int
    {
        $tables = ['request_atk', 'request_checksheet', 'request_id', 'request_memo'];
        $total = 0;
        
        foreach ($tables as $table) {
            $total += self::getRequestCountByStatus('pending', $table);
        }
        
        return $total;
    }

    /**
     * Get total approved requests today for admin (all types)
     */
    public static function getTotalApprovedTodayAdmin(): int
    {
        $tables = ['request_atk', 'request_checksheet', 'request_id', 'request_memo'];
        $total = 0;
        
        foreach ($tables as $table) {
            $total += self::getRequestCountByStatusToday('approved', $table);
        }
        
        return $total;
    }

    /**
     * Get total rejected requests today for admin (all types)
     */
    public static function getTotalRejectedTodayAdmin(): int
    {
        $tables = ['request_atk', 'request_checksheet', 'request_id', 'request_memo'];
        $total = 0;
        
        foreach ($tables as $table) {
            $total += self::getRequestCountByStatusToday('rejected', $table);
        }
        
        return $total;
    }

    /**
     * Get total approved requests this month for admin (all types)
     * NOTE: We count by approved_at field instead of status because status may not be consistently updated
     */
    public static function getTotalApprovedThisMonthAdmin(): int
    {
        $tables = ['request_atk', 'request_checksheet', 'request_id', 'request_memo'];
        $total = 0;
        
        // Get first and last day of current month
        $firstDay = date('Y-m-01 00:00:00');
        $lastDay = date('Y-m-t 23:59:59');
        
        foreach ($tables as $table) {
            // Count all records with approved_at in current month
            // We use approved_at as the indicator of approval, not status field
            $sql = "SELECT COUNT(*) as total FROM $table 
                    WHERE approved_at IS NOT NULL
                    AND approved_at >= ? AND approved_at <= ?";
            $result = Database::row($sql, [$firstDay, $lastDay]);
            $total += $result ? (int)$result->total : 0;
        }
        
        return $total;
    }

    /**
     * DEBUG: Get status values and counts for all approved records with approved_at in current month
     */
    public static function debugApprovedStatus(): array
    {
        $tables = ['request_atk', 'request_checksheet', 'request_id', 'request_memo'];
        $debugInfo = [];
        
        $firstDay = date('Y-m-01 00:00:00');
        $lastDay = date('Y-m-t 23:59:59');
        
        foreach ($tables as $table) {
            $debugInfo[$table] = [];
            
            // Check all records with approved_at in current month
            $sql = "SELECT status, COUNT(*) as count FROM $table 
                    WHERE approved_at >= ? AND approved_at <= ?
                    GROUP BY status";
            $results = Database::results($sql, [$firstDay, $lastDay]);
            $debugInfo[$table]['by_status'] = $results;
            
            // Check for empty/null status
            $sql = "SELECT COUNT(*) as total FROM $table 
                    WHERE approved_at >= ? AND approved_at <= ? 
                    AND (status = '' OR status IS NULL)";
            $result = Database::row($sql, [$firstDay, $lastDay]);
            $debugInfo[$table]['empty_status_count'] = $result ? (int)$result->total : 0;
            
            // Check for 'approved' status explicitly
            $sql = "SELECT COUNT(*) as total FROM $table 
                    WHERE approved_at >= ? AND approved_at <= ? 
                    AND status = 'approved'";
            $result = Database::row($sql, [$firstDay, $lastDay]);
            $debugInfo[$table]['approved_status_count'] = $result ? (int)$result->total : 0;
            
            // Get all unique status values in table
            $sql = "SELECT DISTINCT status FROM $table WHERE approved_at IS NOT NULL";
            $results = Database::results($sql);
            $debugInfo[$table]['all_unique_status'] = $results;
        }
        
        return $debugInfo;
    }

    /**
     */
    public static function getTotalRejectedThisMonthAdmin(): int
    {
        $tables = ['request_atk', 'request_checksheet', 'request_id', 'request_memo'];
        $total = 0;
        $currentMonth = date('Y-m');
        
        foreach ($tables as $table) {
            $sql = "SELECT COUNT(*) as total FROM $table 
                    WHERE status = 'rejected'
                    AND DATE_FORMAT(updated_at, '%Y-%m') = ?";
            $result = Database::row($sql, [$currentMonth]);
            $total += $result ? (int)$result->total : 0;
        }
        
        return $total;
    }

    /**
     * Get total completed requests this month for admin (all types)
     */
    public static function getTotalCompletedThisMonthAdmin(): int
    {
        $tables = ['request_atk', 'request_checksheet', 'request_id', 'request_memo'];
        $total = 0;
        
        foreach ($tables as $table) {
            $total += self::getRequestCountByStatusThisMonth('completed', $table);
        }
        
        return $total;
    }

    /**
     * Get total pending requests for PIC (specific user)
     */
    public static function getTotalPendingRequestsPIC(int $userId): int
    {
        $tables = ['request_atk', 'request_checksheet', 'request_id', 'request_memo'];
        $total = 0;
        
        foreach ($tables as $table) {
            $sql = "SELECT COUNT(*) as total FROM $table 
                    WHERE status = 'pending' AND requested_by = ?";
            $result = Database::row($sql, [$userId]);
            $total += $result ? (int)$result->total : 0;
        }
        
        return $total;
    }

    /**
     * Get total approved requests for PIC (specific user)
     */
    public static function getTotalApprovedRequestsPIC(int $userId): int
    {
        $tables = ['request_atk', 'request_checksheet', 'request_id', 'request_memo'];
        $total = 0;
        
        foreach ($tables as $table) {
            $sql = "SELECT COUNT(*) as total FROM $table 
                    WHERE status = 'approved' AND requested_by = ?";
            $result = Database::row($sql, [$userId]);
            $total += $result ? (int)$result->total : 0;
        }
        
        return $total;
    }

    /**
     * Get total rejected requests for PIC (specific user)
     */
    public static function getTotalRejectedRequestsPIC(int $userId): int
    {
        $tables = ['request_atk', 'request_checksheet', 'request_id', 'request_memo'];
        $total = 0;
        
        foreach ($tables as $table) {
            $sql = "SELECT COUNT(*) as total FROM $table 
                    WHERE status = 'rejected' AND requested_by = ?";
            $result = Database::row($sql, [$userId]);
            $total += $result ? (int)$result->total : 0;
        }
        
        return $total;
    }

    /**
     * Get total completed requests for PIC (specific user)
     */
    public static function getTotalCompletedRequestsPIC(int $userId): int
    {
        $tables = ['request_atk', 'request_checksheet', 'request_id', 'request_memo'];
        $total = 0;
        
        foreach ($tables as $table) {
            $sql = "SELECT COUNT(*) as total FROM $table 
                    WHERE status = 'completed' AND requested_by = ?";
            $result = Database::row($sql, [$userId]);
            $total += $result ? (int)$result->total : 0;
        }
        
        return $total;
    }

    /**
     * Get recent requests for admin (all types, limit)
     */
    public static function getRecentRequestsAdmin(int $limit = 10): array
    {
        // Get recent ATK requests
        $atk_sql = "SELECT 'ATK' as type, id, request_number, status, requested_by, created_at, 
                           requester, NULL as checksheet_name, NULL as id_type, NULL as memo_content
                    FROM (SELECT ra.id, ra.request_number, ra.status, ra.requested_by, ra.created_at,
                                 u.full_name as requester
                          FROM request_atk ra
                          LEFT JOIN users u ON ra.requested_by = u.id) t1";
        
        // Get recent Checksheet requests
        $checksheet_sql = "SELECT 'Checksheet' as type, id, request_number, status, requested_by, created_at,
                                  requester, checksheet_name, NULL as id_type, NULL as memo_content
                           FROM (SELECT rc.id, rc.request_number, rc.status, rc.requested_by, rc.created_at,
                                        u.full_name as requester, mc.nama_checksheet as checksheet_name
                                 FROM request_checksheet rc
                                 LEFT JOIN users u ON rc.requested_by = u.id
                                 LEFT JOIN master_checksheet mc ON rc.checksheet_id = mc.id) t2";
        
        // Get recent ID requests
        $id_sql = "SELECT 'ID' as type, id, request_number, status, requested_by, created_at,
                          requester, NULL as checksheet_name, id_type, NULL as memo_content
                   FROM (SELECT rid.id, rid.request_number, rid.status, rid.requested_by, rid.created_at,
                                u.full_name as requester, rid.id_type
                         FROM request_id rid
                         LEFT JOIN users u ON rid.requested_by = u.id) t3";
        
        // Get recent Memo requests
        $memo_sql = "SELECT 'Memo' as type, id, request_number, status, requested_by, created_at,
                           requester, NULL as checksheet_name, NULL as id_type, memo_content
                    FROM (SELECT rm.id, rm.request_number, rm.status, rm.requested_by, rm.created_at,
                                 u.full_name as requester, 
                                 SUBSTRING(rm.memo_content, 1, 50) as memo_content
                          FROM request_memo rm
                          LEFT JOIN users u ON rm.requested_by = u.id) t4";
        
        $union_sql = "($atk_sql) UNION ALL ($checksheet_sql) UNION ALL ($id_sql) UNION ALL ($memo_sql) 
                     ORDER BY created_at DESC LIMIT ?";
        
        return Database::results($union_sql, [$limit]);
    }

    /**
     * Get recent requests for PIC (user specific, all types, limit)
     */
    public static function getRecentRequestsPIC(int $userId, int $limit = 10): array
    {
        $requests = [];
        
        // Get recent ATK requests
        $atk_requests = RequestATK::getByUser($userId);
        foreach (array_slice($atk_requests, 0, $limit) as $req) {
            $req->type = 'ATK';
            $requests[] = $req;
        }
        
        // Get recent Checksheet requests
        $cs_requests = RequestChecksheet::getByUser($userId);
        foreach (array_slice($cs_requests, 0, $limit) as $req) {
            $req->type = 'Checksheet';
            $requests[] = $req;
        }
        
        // Get recent ID requests
        $id_requests = RequestID::getByUser($userId);
        foreach (array_slice($id_requests, 0, $limit) as $req) {
            $req->type = 'ID';
            $requests[] = $req;
        }
        
        // Get recent Memo requests
        $memo_requests = RequestMemo::getByUser($userId);
        foreach (array_slice($memo_requests, 0, $limit) as $req) {
            $req->type = 'Memo';
            $requests[] = $req;
        }
        
        // Sort by created_at DESC and limit
        usort($requests, function($a, $b) {
            return strtotime($b->created_at) - strtotime($a->created_at);
        });
        
        return array_slice($requests, 0, $limit);
    }

    /**
     * Get status distribution for all requests (admin)
     */
    public static function getStatusDistributionAdmin(): array
    {
        $distribution = [
            'pending' => 0,
            'approved' => 0,
            'rejected' => 0,
            'completed' => 0
        ];
        
        $tables = ['request_atk', 'request_checksheet', 'request_id', 'request_memo'];
        
        foreach ($tables as $table) {
            foreach (['pending', 'approved', 'rejected', 'completed'] as $status) {
                $distribution[$status] += self::getRequestCountByStatus($status, $table);
            }
        }
        
        return $distribution;
    }

    /**
     * Get status distribution for user requests (PIC)
     */
    public static function getStatusDistributionPIC(int $userId): array
    {
        $distribution = [
            'pending' => 0,
            'approved' => 0,
            'rejected' => 0,
            'completed' => 0
        ];
        
        $tables = ['request_atk', 'request_checksheet', 'request_id', 'request_memo'];
        
        foreach ($tables as $table) {
            foreach (['pending', 'approved', 'rejected', 'completed'] as $status) {
                $sql = "SELECT COUNT(*) as total FROM $table 
                        WHERE status = ? AND requested_by = ?";
                $result = Database::row($sql, [$status, $userId]);
                $distribution[$status] += $result ? (int)$result->total : 0;
            }
        }
        
        return $distribution;
    }

    /**
     * Get requests by type count (admin)
     */
    public static function getRequestsByTypeAdmin(): array
    {
        return [
            'atk' => self::getRequestCountByStatus('pending', 'request_atk'),
            'checksheet' => self::getRequestCountByStatus('pending', 'request_checksheet'),
            'id' => self::getRequestCountByStatus('pending', 'request_id'),
            'memo' => self::getRequestCountByStatus('pending', 'request_memo')
        ];
    }

    /**
     * Get requests by type count (PIC)
     */
    public static function getRequestsByTypePIC(int $userId): array
    {
        $tables = ['request_atk', 'request_checksheet', 'request_id', 'request_memo'];
        $types = ['atk' => 'request_atk', 'checksheet' => 'request_checksheet', 
                  'id' => 'request_id', 'memo' => 'request_memo'];
        $result = [];
        
        foreach ($types as $key => $table) {
            $sql = "SELECT COUNT(*) as total FROM $table WHERE requested_by = ?";
            $row = Database::row($sql, [$userId]);
            $result[$key] = $row ? (int)$row->total : 0;
        }
        
        return $result;
    }
}

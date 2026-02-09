<?php

namespace App;

/**
 * Session Manager
 * Handles session operations and data
 */
class Session
{
    private const CSRF_TOKEN_KEY = '_csrf_token';
    private const SESSION_TIMEOUT_KEY = '_session_timeout';
    private const SESSION_LIFETIME = 3600; // 1 hour

    /**
     * Start session
     */
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
            self::checkSessionTimeout();
        }
    }

    /**
     * Check session timeout
     */
    private static function checkSessionTimeout(): void
    {
        $timeout = self::SESSION_LIFETIME;

        if (isset($_SESSION[self::SESSION_TIMEOUT_KEY])) {
            if (time() - $_SESSION[self::SESSION_TIMEOUT_KEY] > $timeout) {
                self::destroy();
                return;
            }
        }

        $_SESSION[self::SESSION_TIMEOUT_KEY] = time();
    }

    /**
     * Put data into session
     */
    public static function put(string $key, $value)
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    /**
     * Get data from session
     */
    public static function get(string $key, $default = null)
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Check if key exists in session
     */
    public static function has(string $key): bool
    {
        self::start();
        return isset($_SESSION[$key]);
    }

    /**
     * Delete data from session
     */
    public static function forget(string $key): void
    {
        self::start();
        unset($_SESSION[$key]);
    }

    /**
     * Flash data - data that will be available only on next request
     */
    public static function flash(string $key, $value)
    {
        self::start();
        $_SESSION['_flash'][$key] = $value;
    }

    /**
     * Get flashed data
     */
    public static function getFlash(string $key, $default = null)
    {
        self::start();
        $value = $_SESSION['_flash'][$key] ?? $default;
        unset($_SESSION['_flash'][$key]);
        return $value;
    }

    /**
     * Peek flashed data (read without consuming)
     */
    public static function peekFlash(string $key, $default = null)
    {
        self::start();
        return $_SESSION['_flash'][$key] ?? $default;
    }

    /**
     * Destroy session
     */
    public static function destroy(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_unset();
            session_destroy();
        }
    }

    /**
     * Generate CSRF token
     */
    public static function generateToken(): string
    {
        self::start();

        if (!isset($_SESSION[self::CSRF_TOKEN_KEY])) {
            $_SESSION[self::CSRF_TOKEN_KEY] = bin2hex(random_bytes(32));
        }

        return $_SESSION[self::CSRF_TOKEN_KEY];
    }

    /**
     * Verify CSRF token
     */
    public static function verifyToken(string $token): bool
    {
        self::start();
        $storedToken = $_SESSION[self::CSRF_TOKEN_KEY] ?? '';
        return hash_equals($storedToken, $token);
    }

    /**
     * Get all session data
     */
    public static function all(): array
    {
        self::start();
        return $_SESSION;
    }

    /**
     * Set active conveyor and shift for production requests
     */
    public static function setActiveConveyor(int $conveyorId, string $conveyorName, string $shift): void
    {
        self::start();
        $_SESSION['active_conveyor_id'] = $conveyorId;
        $_SESSION['active_conveyor_name'] = $conveyorName;
        $_SESSION['active_shift'] = $shift;
    }

    /**
     * Get active conveyor ID
     */
    public static function getActiveConveyorId()
    {
        self::start();
        return $_SESSION['active_conveyor_id'] ?? null;
    }

    /**
     * Get active conveyor name
     */
    public static function getActiveConveyorName()
    {
        self::start();
        return $_SESSION['active_conveyor_name'] ?? null;
    }

    /**
     * Get active shift
     */
    public static function getActiveShift()
    {
        self::start();
        return $_SESSION['active_shift'] ?? null;
    }

    /**
     * Check if conveyor and shift are set
     */
    public static function hasActiveConveyorAndShift(): bool
    {
        self::start();
        return isset($_SESSION['active_conveyor_id']) && 
               isset($_SESSION['active_conveyor_name']) && 
               isset($_SESSION['active_shift']);
    }

    /**
     * Clear active conveyor and shift
     */
    public static function clearActiveConveyorAndShift(): void
    {
        self::start();
        unset($_SESSION['active_conveyor_id']);
        unset($_SESSION['active_conveyor_name']);
        unset($_SESSION['active_shift']);
    }
}

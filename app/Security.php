<?php

namespace App;

/**
 * Security Helper
 * Provides security utilities
 */
class Security
{
    /**
     * Hash a password
     */
    public static function hashPassword(string $password): string
    {
        $config = require __DIR__ . '/../config/app.php';
        $algo = $config['security']['password_hash_algo'];
        $options = $config['security']['password_hash_options'];

        return password_hash($password, $algo, $options);
    }

    /**
     * Verify a password
     */
    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Escape HTML to prevent XSS
     */
    public static function escape(mixed $value): string
    {
        if (is_array($value)) {
            return '';
        }

        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Sanitize input to prevent XSS
     */
    public static function sanitize(string $input, string $type = 'string'): mixed
    {
        return match ($type) {
            'email' => filter_var($input, FILTER_SANITIZE_EMAIL),
            'url' => filter_var($input, FILTER_SANITIZE_URL),
            'int' => filter_var($input, FILTER_SANITIZE_NUMBER_INT),
            'float' => filter_var($input, FILTER_SANITIZE_NUMBER_FLOAT),
            default => htmlspecialchars($input, ENT_QUOTES, 'UTF-8'),
        };
    }

    /**
     * Generate a random token
     */
    public static function generateToken(int $length = 64): string
    {
        return bin2hex(random_bytes($length / 2));
    }

    /**
     * Check if HTTPS is being used
     */
    public static function isSecure(): bool
    {
        return !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    }

    /**
     * Set secure cookie
     */
    public static function setCookie(
        string $name,
        string $value,
        int $expiryDays = 30,
        string $path = '/',
        string $domain = '',
        bool $secure = null,
        bool $httpOnly = true
    ): bool {
        if ($secure === null) {
            $secure = self::isSecure();
        }

        $expiry = time() + ($expiryDays * 24 * 60 * 60);

        return setcookie(
            $name,
            $value,
            [
                'expires' => $expiry,
                'path' => $path,
                'domain' => $domain,
                'secure' => $secure,
                'httponly' => $httpOnly,
                'samesite' => 'Strict',
            ]
        );
    }

    /**
     * Get cookie
     */
    public static function getCookie(string $name, string $default = ''): string
    {
        return $_COOKIE[$name] ?? $default;
    }

    /**
     * Delete cookie
     */
    public static function deleteCookie(string $name): bool
    {
        return setcookie($name, '', ['expires' => time() - 3600]);
    }
}

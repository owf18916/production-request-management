<?php

/**
 * Helper Functions
 * Global functions available throughout the application
 */

/**
 * Get environment variable
 */
if (!function_exists('env')) {
function env(string $key, $default = null)
{
    $envFile = __DIR__ . '/../.env';

    if (!file_exists($envFile)) {
        return $default;
    }

    static $env = null;

    if ($env === null) {
        $env = [];
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            // PHP 7.4 compatible: replace str_starts_with() with strpos()
            if (strpos($line, '#') === 0) {
                continue;
            }

            // PHP 7.4 compatible: replace str_contains() with strpos()
            if (strpos($line, '=') !== false) {
                [$envKey, $envValue] = explode('=', $line, 2);
                $envKey = trim($envKey);
                $envValue = trim($envValue);
                
                // Parse the value to proper type
                if ($envValue === '') {
                    $env[$envKey] = null;
                } elseif (strtolower($envValue) === 'true') {
                    $env[$envKey] = true;
                } elseif (strtolower($envValue) === 'false') {
                    $env[$envKey] = false;
                } elseif (strtolower($envValue) === 'null') {
                    $env[$envKey] = null;
                } elseif (is_numeric($envValue)) {
                    $env[$envKey] = (strpos($envValue, '.') === false) ? (int)$envValue : (float)$envValue;
                } elseif (preg_match('/^".*"$/', $envValue) || preg_match("/^'.*'$/", $envValue)) {
                    // Remove quotes from quoted strings
                    $env[$envKey] = substr($envValue, 1, -1);
                } else {
                    $env[$envKey] = $envValue;
                }
            }
        }
    }

    return $env[$key] ?? $default;
}
}

/**
 * Dump variable and die
 */
if (!function_exists('dd')) {
function dd(...$vars)
{
    foreach ($vars as $var) {
        echo '<pre>';
        var_dump($var);
        echo '</pre>';
    }
    die;
}
}

/**
 * Dump variable
 */
if (!function_exists('dump')) {
function dump(...$vars)
{
    foreach ($vars as $var) {
        echo '<pre>';
        var_dump($var);
        echo '</pre>';
    }
}
}

/**
 * Escape HTML output
 */
if (!function_exists('e')) {
function e($value)
{
    return \App\Security::escape($value);
}
}

/**
 * Get URL helper - Dynamic based on current host
 */
if (!function_exists('url')) {
function url(string $path = ''): string
{
    // Gunakan current protocol dan host untuk dynamic URL
    $protocol = empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off' ? 'http' : 'https';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    
    // Get app base path dari directory structure
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    $baseDir = dirname($scriptName);
    if ($baseDir === '\\' || $baseDir === '/') {
        $baseDir = '';
    }
    
    $baseUrl = rtrim("$protocol://$host$baseDir", '/');
    return $baseUrl . '/' . ltrim($path, '/');
}
}

/**
 * Get current URL
 */
if (!function_exists('currentUrl')) {
function currentUrl(): string
{
    $protocol = empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off' ? 'http' : 'https';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $uri = $_SERVER['REQUEST_URI'] ?? '/';
    return "$protocol://$host$uri";
}
}

/**
 * Check if request is AJAX
 */
if (!function_exists('isAjax')) {
function isAjax(): bool
{
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}
}

/**
 * Get CSRF token
 */
if (!function_exists('csrfToken')) {
function csrfToken(): string
{
    return \App\Session::generateToken();
}
}

/**
 * Verify CSRF token
 */
if (!function_exists('verifyCsrf')) {
function verifyCsrf(string $token): bool
{
    return \App\Session::verifyToken($token);
}
}

/**
 * Get flash message
 */
if (!function_exists('getFlash')) {
function getFlash(string $key, $default = null)
{
    return \App\Session::getFlash($key, $default);
}
}

/**
 * Check if flash message exists
 */
if (!function_exists('hasFlash')) {
function hasFlash(string $key): bool
{
    $flash = \App\Session::get('_flash', []);
    return isset($flash[$key]);
}
}

/**
 * Get session data
 */
if (!function_exists('session')) {
function session(string $key = null, $default = null)
{
    if ($key === null) {
        return \App\Session::all();
    }

    return \App\Session::get($key, $default);
}
}

/**
 * Hash a password
 */
if (!function_exists('hashPassword')) {
function hashPassword(string $password): string
{
    return \App\Security::hashPassword($password);
}
}

/**
 * Verify password
 */
if (!function_exists('verifyPassword')) {
function verifyPassword(string $password, string $hash): bool
{
    return \App\Security::verifyPassword($password, $hash);
}
}

/**
 * Get config value
 */
if (!function_exists('config')) {
function config(string $key, $default = null)
{
    $parts = explode('.', $key);
    $file = array_shift($parts);
    $configPath = __DIR__ . "/../config/$file.php";

    if (!file_exists($configPath)) {
        return $default;
    }

    $config = require $configPath;

    foreach ($parts as $part) {
        $config = $config[$part] ?? null;

        if ($config === null) {
            return $default;
        }
    }

    return $config;
}
}

/**
 * Check if value is empty
 */
if (!function_exists('isEmpty')) {
function isEmpty($value)
{
    if (is_array($value)) {
        return empty($value);
    }

    return empty(trim((string)$value));
}
}

/**
 * Generate a random string
 */
if (!function_exists('randomString')) {
function randomString(int $length = 32): string
{
    return substr(
        str_shuffle(str_repeat(
            'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789',
            $length
        )),
        0,
        $length
    );
}
}

/**
 * Array get with dot notation
 */
if (!function_exists('arrayGet')) {
function arrayGet(array $array, string $key, $default = null)
{
    $keys = explode('.', $key);

    foreach ($keys as $k) {
        if (!isset($array[$k])) {
            return $default;
        }
        $array = $array[$k];
    }

    return $array;
}
}

/**
 * Check if array has key with dot notation
 */
if (!function_exists('arrayHas')) {
function arrayHas(array $array, string $key): bool
{
    $keys = explode('.', $key);

    foreach ($keys as $k) {
        if (!isset($array[$k])) {
            return false;
        }
        $array = $array[$k];
    }

    return true;
}
}

/**
 * Format date
 */
if (!function_exists('formatDate')) {
function formatDate($date, string $format = 'Y-m-d H:i:s')
{
    if ($date instanceof DateTime) {
        return $date->format($format);
    }

    return date($format, strtotime((string)$date));
}
}

/**
 * Application logging function (appLog instead of log to avoid PHP built-in function conflict)
 */
if (!function_exists('appLog')) {
function appLog(string $message, string $level = 'INFO'): void
{
    $logFile = __DIR__ . '/../storage/logs/app.log';
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] [$level] $message\n";

    @file_put_contents($logFile, $logMessage, FILE_APPEND);
}
}

/**
 * Get asset URL - Helper specifically for static assets
 */
if (!function_exists('asset')) {
function asset(string $path = ''): string
{
    return url('assets/' . ltrim($path, '/'));
}
}


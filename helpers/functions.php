<?php

/**
 * Helper Functions
 * Global functions available throughout the application
 */

/**
 * Get environment variable
 */
if (!function_exists('env')) {
function env(string $key, mixed $default = null): mixed
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
            if (str_starts_with($line, '#')) {
                continue;
            }

            if (str_contains($line, '=')) {
                [$envKey, $envValue] = explode('=', $line, 2);
                $env[trim($envKey)] = trim($envValue);
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
function dd(mixed ...$vars): void
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
function dump(mixed ...$vars): void
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
function e(mixed $value): string
{
    return \App\Security::escape($value);
}
}

/**
 * Get URL helper
 */
if (!function_exists('url')) {
function url(string $path = ''): string
{
    $appUrl = env('APP_URL', 'http://localhost');
    return rtrim($appUrl, '/') . '/' . ltrim($path, '/');
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
function getFlash(string $key, mixed $default = null): mixed
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
function session(string $key = null, mixed $default = null): mixed
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
function config(string $key, mixed $default = null): mixed
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
function isEmpty(mixed $value): bool
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
function arrayGet(array $array, string $key, mixed $default = null): mixed
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
function formatDate(mixed $date, string $format = 'Y-m-d H:i:s'): string
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

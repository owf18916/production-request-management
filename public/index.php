<?php

/**
 * Public Index File
 * Entry point for all application requests
 */

// Define base path
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');
define('CONFIG_PATH', BASE_PATH . '/config');

// Load autoloader
require BASE_PATH . '/Autoloader.php';
Autoloader::register();

// Load helper functions
require BASE_PATH . '/helpers/functions.php';

// Load environment variables
if (file_exists(BASE_PATH . '/.env')) {
    $dotenv = file_get_contents(BASE_PATH . '/.env');
    // Parse .env file
}

// Enable error reporting in development
if (env('APP_DEBUG', false)) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

// Define base paths for URL handling
$basePaths = [
    '/production-request-management/public',
    '/production-request-management',
];

// Set charset (hanya untuk HTML, bukan untuk export/download)
$path_for_check = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$is_export = false;

// Extract path without base paths
foreach ($basePaths as $basePath) {
    $basePath_trimmed = rtrim($basePath, '/');
    if (strpos($path_for_check, $basePath_trimmed) === 0) {
        $path_for_check = substr($path_for_check, strlen($basePath_trimmed));
        break;
    }
}
$path_for_check = '/' . ltrim($path_for_check, '/');

// Check if this is an export route (before query string)
$path_before_query = explode('?', $path_for_check)[0];
$exportRoutes = ['/admin/requests/atk/export', '/admin/request-id/export', '/admin/request_checksheet/export', '/requests/atk/export', '/request-id/export', '/request_checksheet/export'];
foreach ($exportRoutes as $route) {
    if ($path_before_query === $route) {
        $is_export = true;
        break;
    }
}

if (!$is_export) {
    header('Content-Type: text/html; charset=' . env('APP_CHARSET', 'UTF-8'));
}

// Start session
\App\Session::start();

// Initialize router
$router = new \App\Router();

// Load routes
require BASE_PATH . '/routes/web.php';

// Get current request
$method = $_SERVER['REQUEST_METHOD'];

// Get the path - .htaccess will rewrite to public folder but REQUEST_URI might include it
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Remove the base path prefix to get the route
// Handle both /production-request-management/path and /production-request-management/public/path
foreach ($basePaths as $basePath) {
    if (strpos($path, $basePath) === 0) {
        $path = substr($path, strlen($basePath));
        break;
    }
}

// Remove trailing slash and default to /
$path = rtrim($path, '/') ?: '/';

// Dispatch request
$router->dispatch($method, $path);

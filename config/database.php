<?php

/**
 * Database Configuration
 * PDO Connection with MySQL
 */

return [
    'default' => env('DB_CONNECTION', 'mysql'),
    
    'mysql' => [
        'driver'    => 'mysql',
        'host'      => env('DB_HOST', 'localhost'),
        'port'      => env('DB_PORT', 3306),
        'database'  => env('DB_NAME', 'production_request_db'),
        'username'  => env('DB_USER', 'root'),
        'password'  => env('DB_PASSWORD', ''),
        'charset'   => env('DB_CHARSET', 'utf8mb4'),
        'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'),
        'prefix'    => env('DB_PREFIX', ''),
        'strict'    => env('DB_STRICT', true),
        'engine'    => env('DB_ENGINE', 'InnoDB'),
    ],
    
    'options' => [
        \PDO::ATTR_CASE              => \PDO::CASE_NATURAL,
        \PDO::ATTR_ERRMODE           => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_ORACLE_NULLS      => \PDO::NULL_NATURAL,
        \PDO::ATTR_STRINGIFY_FETCHES => false,
        \PDO::ATTR_EMULATE_PREPARES  => false,
    ],
];

<?php

/**
 * Application Configuration
 */

return [
    'name'  => env('APP_NAME', 'Production Request Management System'),
    'env'   => env('APP_ENV', 'development'),
    'debug' => env('APP_DEBUG', true),
    'url'   => env('APP_URL', 'http://localhost/production-request-management'),
    'timezone' => env('APP_TIMEZONE', 'UTC'),
    'charset'  => env('APP_CHARSET', 'UTF-8'),
    
    'security' => [
        'session_lifetime' => 3600, // 1 hour
        'remember_lifetime' => 2592000, // 30 days
        'password_hash_algo' => PASSWORD_BCRYPT,
        'password_hash_options' => [
            'cost' => 12,
        ],
    ],
];

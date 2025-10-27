<?php
/**
 * Database Configuration for GoRefill
 * 
 * This file contains all database connection settings
 * and application configuration.
 */

return [
    // Database Configuration
    'db' => [
        'host' => 'localhost',
        'port' => '3306',
        'dbname' => 'gorefill',
        'username' => 'root',
        'password' => 'root',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'options' => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
        ]
    ],
    
    // Application Configuration
    'app' => [
        'name' => 'GoRefill',
        'url' => 'http://localhost/gorefill/public',
        'env' => 'development', // development, production
        'debug' => true,
        'timezone' => 'Asia/Jakarta'
    ],
    
    // Session Configuration
    'session' => [
        'lifetime' => 7200, // 2 hours in seconds
        'cookie_name' => 'gorefill_session',
        'cookie_path' => '/',
        'cookie_secure' => false, // Set true in production with HTTPS
        'cookie_httponly' => true
    ],
    
    // Security
    'security' => [
        'password_min_length' => 8,
        'max_login_attempts' => 5,
        'lockout_time' => 900 // 15 minutes in seconds
    ],
    
    // Upload Settings
    'upload' => [
        'max_size' => 5242880, // 5MB in bytes
        'allowed_types' => ['jpg', 'jpeg', 'png', 'webp'],
        'products_path' => __DIR__ . '/../uploads/products/'
    ],
    
    // Pagination
    'pagination' => [
        'per_page' => 12,
        'admin_per_page' => 20
    ]
];

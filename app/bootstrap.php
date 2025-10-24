<?php
/**
 * Bootstrap File for GoRefill Application
 * 
 * This file initializes the application by:
 * - Loading configuration
 * - Starting session
 * - Creating PDO database connection
 * - Setting up error reporting
 */

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load configuration
$config = require_once __DIR__ . '/../config/config.php';

// Set timezone
date_default_timezone_set($config['app']['timezone']);

// Set error reporting based on environment
if ($config['app']['env'] === 'development' && $config['app']['debug']) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/../logs/php-errors.log');
}

// Create PDO database connection
try {
    $dsn = sprintf(
        "mysql:host=%s;port=%s;dbname=%s;charset=%s",
        $config['db']['host'],
        $config['db']['port'],
        $config['db']['dbname'],
        $config['db']['charset']
    );
    
    $pdo = new PDO(
        $dsn,
        $config['db']['username'],
        $config['db']['password'],
        $config['db']['options']
    );
    
    // Test connection (optional, remove in production for performance)
    if ($config['app']['debug']) {
        $pdo->query('SELECT 1');
    }
    
} catch (PDOException $e) {
    // Log error
    error_log('Database Connection Failed: ' . $e->getMessage());
    
    // Show user-friendly message
    if ($config['app']['debug']) {
        die('Database Connection Failed: ' . $e->getMessage());
    } else {
        die('Sorry, we are experiencing technical difficulties. Please try again later.');
    }
}

// Helper function to get base URL
function base_url($path = '') {
    global $config;
    return rtrim($config['app']['url'], '/') . '/' . ltrim($path, '/');
}

// Helper function to redirect
function redirect($path = '') {
    header('Location: ' . base_url($path));
    exit;
}

// Helper function to get asset URL
function asset($path) {
    return base_url('assets/' . ltrim($path, '/'));
}

// Helper function to sanitize output
function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

// Helper function to check if user is logged in
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

// Helper function to check user role
function has_role($role) {
    return isset($_SESSION['role']) && $_SESSION['role'] === $role;
}

// Helper function to require authentication
function require_auth($role = null) {
    if (!is_logged_in()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        redirect('index.php?route=auth.login');
    }
    
    if ($role !== null && !has_role($role)) {
        die('Access Denied: You do not have permission to access this page.');
    }
}

// Helper function to get current user
function current_user() {
    global $pdo;
    
    if (!is_logged_in()) {
        return null;
    }
    
    $stmt = $pdo->prepare("SELECT id, name, email, phone, role, created_at FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

// Helper function to generate CSRF token
function csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Helper function to verify CSRF token
function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Helper function to format currency
function format_currency($amount) {
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

// Helper function to format date
function format_date($date, $format = 'd M Y H:i') {
    return date($format, strtotime($date));
}

// Make config and pdo available globally
$GLOBALS['config'] = $config;
$GLOBALS['pdo'] = $pdo;

// Return for use in other files
return [
    'config' => $config,
    'pdo' => $pdo
];

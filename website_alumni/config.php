<?php
// Database configuration
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'ensaf_db');

// Site configuration
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$server = $_SERVER['HTTP_HOST'];
$subFolder = dirname($_SERVER['PHP_SELF']);
define('ROOT_URL', $protocol . $server . '/website_alumni');
define('SITE_NAME', 'ENSAF - École Nationale des Sciences Appliquées de Fès');
define('SITE_DESCRIPTION', 'Portail officiel de l\'École Nationale des Sciences Appliquées de Fès - Formation d\'ingénieurs d\'excellence');
define('ADMIN_EMAIL', 'admin@ensaf.ac.ma');

// File upload paths
define('UPLOAD_DIR', 'uploads/');
define('IMAGE_UPLOAD_DIR', UPLOAD_DIR . 'images/');
define('DOCUMENT_UPLOAD_DIR', UPLOAD_DIR . 'documents/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB

// Application settings
define('DEFAULT_LANGUAGE', 'fr');
define('ITEMS_PER_PAGE', 10);
define('DEBUG_MODE', true);
define('MAINTENANCE_MODE', false);

// Security settings
define('HASH_COST', 12); // Password hashing cost
define('SESSION_LIFETIME', 60 * 60 * 24); // 24 hours in seconds
define('CSRF_TOKEN_SECRET', 'ensaf_csrf_secret_123');
define('API_RATE_LIMIT', 100); // API rate limit per hour

// Start session
if (session_status() === PHP_SESSION_NONE) {
    // Set session parameters
    ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
    ini_set('session.cookie_lifetime', SESSION_LIFETIME);
    ini_set('session.use_strict_mode', 1);
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) ? 1 : 0);
    ini_set('session.cookie_samesite', 'Lax');
    
    session_start();
}

// Connect to database
$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);

// Check connection
if (!$conn) {
    die("ERROR: Could not connect to MySQL. " . mysqli_connect_error());
}

// Create database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
if (mysqli_query($conn, $sql)) {
    // Select the database
    mysqli_select_db($conn, DB_NAME);
} else {
    die("ERROR: Could not create database. " . mysqli_error($conn));
}

// Set charset
mysqli_set_charset($conn, "utf8mb4");

// Enable error reporting in debug mode
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Utility functions
function sanitize_input($data) {
    global $conn;
    return mysqli_real_escape_string($conn, htmlspecialchars(trim($data)));
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function get_user_role() {
    return isset($_SESSION['user_role']) ? $_SESSION['user_role'] : '';
}

function check_permission($required_role) {
    if (!is_logged_in()) {
        redirect(ROOT_URL . '/login.php');
    }
    
    if (get_user_role() != $required_role && get_user_role() != 'admin') {
        redirect(ROOT_URL . '/403.php');
    }
}

function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function set_flash_message($type, $message) {
    if ($type === 'success') {
        $_SESSION['success_message'] = $message;
    } else {
        $_SESSION['error_message'] = $message;
    }
}

// Create necessary directories if they don't exist
if (!file_exists(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0755, true);
}

if (!file_exists(IMAGE_UPLOAD_DIR)) {
    mkdir(IMAGE_UPLOAD_DIR, 0755, true);
}

if (!file_exists(DOCUMENT_UPLOAD_DIR)) {
    mkdir(DOCUMENT_UPLOAD_DIR, 0755, true);
}
?>
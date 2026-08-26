<?php
// ============================================
// Configuration & Database Connection
// Connected to 'cloths' database
// ============================================

define('APP_NAME', 'urban outfit — Fashion & Admin');
define('BASE_PATH', dirname(__DIR__));

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Dynamic Base URL Detection
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || ($_SERVER['SERVER_PORT'] ?? 80) == 443) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));

// Detect root or subfolder (e.g., /clothing)
$baseFolder = '';
if (strpos($scriptDir, '/admin') !== false) {
  $baseFolder = substr($scriptDir, 0, strpos($scriptDir, '/admin'));
} elseif (strpos($scriptDir, '/api') !== false) {
  $baseFolder = substr($scriptDir, 0, strpos($scriptDir, '/api'));
} elseif (strpos($scriptDir, '/customer') !== false) {
  $baseFolder = substr($scriptDir, 0, strpos($scriptDir, '/customer'));
} elseif (strpos($scriptDir, '/pages') !== false) {
  $baseFolder = substr($scriptDir, 0, strpos($scriptDir, '/pages'));
} else {
  $baseFolder = rtrim($scriptDir, '/');
}

define('BASE_URL', rtrim($protocol . $host . $baseFolder, '/'));
define('ADMIN_URL', BASE_URL . '/admin');
define('UPLOADS_PATH', BASE_PATH . '/uploads');
define('UPLOADS_URL', BASE_URL . '/uploads');

// Ensure uploads directory exists
if (!is_dir(UPLOADS_PATH . '/products')) {
  @mkdir(UPLOADS_PATH . '/products', 0777, true);
}
if (!is_dir(UPLOADS_PATH . '/categories')) {
  @mkdir(UPLOADS_PATH . '/categories', 0777, true);
}

// Database Credentials
$dbHost = '127.0.0.1';
$dbName = 'cloths';
$dbUser = 'root';
$dbPass = '';

// Connect to MySQL server
$conn = @new mysqli($dbHost, $dbUser, $dbPass);

if ($conn && !$conn->connect_error) {
  // Ensure database exists
  $conn->query("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
  $conn->close();
  
  // Connect to 'cloths' database
  $mysqli = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
  $mysqli->set_charset('utf8mb4');

  // Auto initialize schema if products or admins table is missing
  $checkTable = $mysqli->query("SHOW TABLES LIKE 'products'");
  if ($checkTable && $checkTable->num_rows === 0) {
    $schemaFile = BASE_PATH . '/database/schema.sql';
    if (file_exists($schemaFile)) {
      $sql = file_get_contents($schemaFile);
      $mysqli->multi_query($sql);
      while ($mysqli->more_results() && $mysqli->next_result()) {;} // flush multi queries
    }
  } else {
    // Check if image_label column exists in product_images
    $checkCol = $mysqli->query("SHOW COLUMNS FROM product_images LIKE 'image_label'");
    if ($checkCol && $checkCol->num_rows === 0) {
      $mysqli->query("ALTER TABLE product_images ADD COLUMN image_label VARCHAR(100) NULL AFTER alt_text");
    }
    // Check if image column exists in products
    $checkProdImg = $mysqli->query("SHOW COLUMNS FROM products LIKE 'image'");
    if ($checkProdImg && $checkProdImg->num_rows === 0) {
      $mysqli->query("ALTER TABLE products ADD COLUMN image VARCHAR(500) NULL AFTER subcategory_id");
    }
    // Check if gender column exists in products
    $checkGender = $mysqli->query("SHOW COLUMNS FROM products LIKE 'gender'");
    if ($checkGender && $checkGender->num_rows === 0) {
      $mysqli->query("ALTER TABLE products ADD COLUMN gender ENUM('women', 'men', 'kids', 'unisex') DEFAULT 'women' AFTER brand");
    }
  }
} else {
  $mysqli = null;
}

// ----------------------------------------------------
// Helper Functions
// ----------------------------------------------------

function adminUrl($path = '') {
  return ADMIN_URL . ($path ? '/' . ltrim($path, '/') : '');
}

function siteUrl($path = '') {
  return BASE_URL . ($path ? '/' . ltrim($path, '/') : '');
}

function isAdminLoggedIn() {
  return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

function requireAdminAuth() {
  if (!isAdminLoggedIn()) {
    header('Location: ' . adminUrl('login.php'));
    exit;
  }
}

function getAdmin() {
  global $mysqli;
  if (!$mysqli || !isAdminLoggedIn()) return null;
  $stmt = $mysqli->prepare('SELECT id, name, email, role, avatar, is_active FROM admins WHERE id = ?');
  $stmt->bind_param('i', $_SESSION['admin_id']);
  $stmt->execute();
  return $stmt->get_result()->fetch_assoc();
}

function redirect($url, $statusCode = 302) {
  header('Location: ' . $url, true, $statusCode);
  exit;
}

function sanitize($string) {
  return htmlspecialchars(trim((string)$string), ENT_QUOTES, 'UTF-8');
}

function formatPrice($amount) {
  return '₹' . number_format((float)$amount, 2);
}

function generateOrderNumber() {
  return 'ORD-' . strtoupper(substr(uniqid(), -8));
}

function getSetting($key, $default = '') {
  global $mysqli;
  if (!$mysqli) return $default;
  $stmt = $mysqli->prepare('SELECT value FROM settings WHERE `key` = ?');
  $stmt->bind_param('s', $key);
  $stmt->execute();
  $result = $stmt->get_result()->fetch_assoc();
  return $result ? $result['value'] : $default;
}

/**
 * Handle Single File Upload for Images
 */
function handleImageUpload($fileArray, $subfolder = 'products') {
  if (!isset($fileArray['error']) || is_array($fileArray['error'])) {
    return null;
  }

  if ($fileArray['error'] !== UPLOAD_ERR_OK) {
    return null;
  }

  if ($fileArray['size'] > 10 * 1024 * 1024) { // 10MB limit
    return null;
  }

  if (function_exists('finfo_open')) {
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $fileArray['tmp_name']);
    finfo_close($finfo);
  } else {
    $mimeType = mime_content_type($fileArray['tmp_name']);
  }

  $allowedMimes = [
    'image/jpeg' => 'jpg',
    'image/jpg' => 'jpg',
    'image/png' => 'png',
    'image/webp' => 'webp',
    'image/gif' => 'gif',
  ];

  if (!isset($allowedMimes[$mimeType])) {
    return null;
  }

  $ext = $allowedMimes[$mimeType];
  $filename = sprintf('%s_%s.%s', uniqid('img_', true), time(), $ext);
  $targetDir = UPLOADS_PATH . '/' . trim($subfolder, '/');
  
  if (!is_dir($targetDir)) {
    @mkdir($targetDir, 0777, true);
  }

  $targetPath = $targetDir . '/' . $filename;
  if (move_uploaded_file($fileArray['tmp_name'], $targetPath)) {
    return BASE_URL . '/uploads/' . trim($subfolder, '/') . '/' . $filename;
  }

  return null;
}

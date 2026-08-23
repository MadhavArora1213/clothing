<?php
define('APP_NAME', 'AURA & CO. — Fashion & Admin');
define('APP_URL', 'http://localhost/clothing');
define('BASE_PATH', dirname(__DIR__));

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

$dbHost = 'localhost';
$dbName = 'atelier_db';
$dbUser = 'root';
$dbPass = '';

// Check MySQL connection & auto-create database if not exists
$conn = @new mysqli($dbHost, $dbUser, $dbPass);

if ($conn->connect_error) {
  // If MySQL is not running or credentials differ, log or show helpful notice
  $mysqli = null;
} else {
  $conn->query("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
  $conn->close();
  
  $mysqli = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
  $mysqli->set_charset('utf8mb4');

  // Auto initialize schema if products table is missing
  $checkTable = $mysqli->query("SHOW TABLES LIKE 'products'");
  if ($checkTable && $checkTable->num_rows === 0) {
    $schemaFile = BASE_PATH . '/database/schema.sql';
    if (file_exists($schemaFile)) {
      $sql = file_get_contents($schemaFile);
      $mysqli->multi_query($sql);
      while ($mysqli->next_result()) {;} // flush multi queries
    }
  }
}

function isAdminLoggedIn() {
  return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

function requireAdminAuth() {
  if (!isAdminLoggedIn()) {
    header('Location: /admin/login.php');
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
  return 'ATL-' . strtoupper(uniqid());
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

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: text/plain');

echo "=== DEBUG START ===\n\n";

// 1. Load env manually
$envFile = dirname(__DIR__) . '/.env';
echo "1. .env path: {$envFile}\n";
echo "   .env exists: " . (file_exists($envFile) ? "YES" : "NO") . "\n";

$dbHost = '';
$dbName = '';
$dbUser = '';
$dbPass = '';

if (file_exists($envFile)) {
  $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
  foreach ($lines as $line) {
    if (trim($line)[0] === '#') continue;
    if (strpos($line, '=') === false) continue;
    list($key, $val) = explode('=', $line, 2);
    $key = trim($key);
    $val = trim($val, '" ');
    if ($key === 'DB_HOST') $dbHost = $val;
    if ($key === 'DB_NAME') $dbName = $val;
    if ($key === 'DB_USER') $dbUser = $val;
    if ($key === 'DB_PASS') $dbPass = $val;
  }
}

echo "   DB_HOST: {$dbHost}\n";
echo "   DB_NAME: {$dbName}\n";
echo "   DB_USER: {$dbUser}\n";
echo "   DB_PASS: " . substr($dbPass, 0, 3) . "***\n\n";

// 2. Connect
echo "2. Connecting...\n";
$mysqli = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
if ($mysqli->connect_error) {
  echo "   FAILED: " . $mysqli->connect_error . "\n";
  exit;
}
echo "   OK\n\n";

// 3. Check table
echo "3. password_reset_tokens table:\n";
$res = $mysqli->query("SHOW TABLES LIKE 'password_reset_tokens'");
if ($res && $res->num_rows > 0) {
  echo "   EXISTS\n";
} else {
  echo "   NOT EXISTS - creating...\n";
  $mysqli->query("CREATE TABLE IF NOT EXISTS password_reset_tokens (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_id INT UNSIGNED NOT NULL,
    token VARCHAR(64) NOT NULL UNIQUE,
    expires_at DATETIME NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_token (token),
    INDEX idx_customer (customer_id)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
  echo "   Created: " . ($mysqli->error ? $mysqli->error : "OK") . "\n";
}

// 4. Count tokens
$res = $mysqli->query("SELECT COUNT(*) as cnt FROM password_reset_tokens");
$row = $res->fetch_assoc();
echo "   Tokens count: " . $row['cnt'] . "\n\n";

// 5. Show last 3 tokens
echo "4. Recent tokens:\n";
$res = $mysqli->query("SELECT id, customer_id, LEFT(token, 20) as tok, expires_at FROM password_reset_tokens ORDER BY id DESC LIMIT 3");
while ($row = $res->fetch_assoc()) {
  echo "   ID={$row['id']} | Cust={$row['customer_id']} | Token={$row['tok']}... | Expires={$row['expires_at']}\n";
}

echo "\n=== DEBUG END ===";

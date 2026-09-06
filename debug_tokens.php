<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: text/plain');

echo "=== DEBUG ===\n\n";

// Correct path: same directory as this file
$envFile = __DIR__ . '/.env';
echo ".env path: {$envFile}\n";
echo ".env exists: " . (file_exists($envFile) ? "YES" : "NO") . "\n\n";

// Connect directly
$mysqli = new mysqli('localhost', 'u859024179_80w0e', 'SV&L&eWt0~', 'u859024179_TT9KY');
if ($mysqli->connect_error) {
  echo "DB FAIL: " . $mysqli->connect_error . "\n";
  exit;
}
echo "DB: OK\n\n";

// Check table
$res = $mysqli->query("SHOW TABLES LIKE 'password_reset_tokens'");
if ($res && $res->num_rows > 0) {
  echo "password_reset_tokens: EXISTS\n";
} else {
  echo "password_reset_tokens: NOT EXISTS - creating...\n";
  $mysqli->query("CREATE TABLE IF NOT EXISTS password_reset_tokens (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_id INT UNSIGNED NOT NULL,
    token VARCHAR(64) NOT NULL UNIQUE,
    expires_at DATETIME NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_token (token),
    INDEX idx_customer (customer_id)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
  echo "Created: " . ($mysqli->error ?: "OK") . "\n";
}

$count = $mysqli->query("SELECT COUNT(*) as c FROM password_reset_tokens")->fetch_assoc();
echo "Tokens: " . $count['c'] . "\n\n";

$res = $mysqli->query("SELECT id, customer_id, LEFT(token,20) as tok, expires_at FROM password_reset_tokens ORDER BY id DESC LIMIT 5");
while ($row = $res->fetch_assoc()) {
  echo "ID={$row['id']} Cust={$row['customer_id']} Token={$row['tok']}... Expires={$row['expires_at']}\n";
}

// Check specific token if provided
if (!empty($_GET['check_token'])) {
  $t = $_GET['check_token'];
  echo "\nToken check: " . substr($t,0,20) . "... (len=" . strlen($t) . ")\n";
  $stmt = $mysqli->prepare('SELECT customer_id, expires_at FROM password_reset_tokens WHERE token = ?');
  $stmt->bind_param('s', $t);
  $stmt->execute();
  $r = $stmt->get_result()->fetch_assoc();
  if ($r) {
    echo "FOUND! Cust={$r['customer_id']} Expires={$r['expires_at']} Expired=" . (strtotime($r['expires_at'])<time()?"YES":"NO") . "\n";
  } else {
    echo "NOT FOUND!\n";
  }
}

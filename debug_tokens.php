<?php
require_once dirname(__DIR__) . '/config/database.php';

header('Content-Type: text/plain');

echo "=== DEBUG: Password Reset Tokens ===\n\n";

// 1. Check DB connection
echo "1. DB Connection: " . ($mysqli ? "OK (" . $mysqli->host_info . ")" : "FAILED") . "\n";

// 2. Check if table exists
$check = $mysqli->query("SHOW TABLES LIKE 'password_reset_tokens'");
$exists = ($check && $check->num_rows > 0);
echo "2. password_reset_tokens table exists: " . ($exists ? "YES" : "NO") . "\n";

// 3. If not exists, create it
if (!$exists) {
  echo "   -> Creating table...\n";
  $create = $mysqli->query("CREATE TABLE IF NOT EXISTS password_reset_tokens (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_id INT UNSIGNED NOT NULL,
    token VARCHAR(64) NOT NULL UNIQUE,
    expires_at DATETIME NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_token (token),
    INDEX idx_customer (customer_id)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
  echo "   -> Create result: " . ($create ? "SUCCESS" : "FAILED: " . $mysqli->error) . "\n";
}

// 4. Count tokens
$count = $mysqli->query("SELECT COUNT(*) as cnt FROM password_reset_tokens")->fetch_assoc();
echo "3. Tokens in table: " . $count['cnt'] . "\n";

// 5. Show recent tokens (last 5)
$tokens = $mysqli->query("SELECT id, customer_id, LEFT(token, 20) as token_start, expires_at, created_at FROM password_reset_tokens ORDER BY id DESC LIMIT 5");
echo "4. Recent tokens:\n";
if ($tokens && $tokens->num_rows > 0) {
  while ($row = $tokens->fetch_assoc()) {
    echo "   ID: {$row['id']} | Customer: {$row['customer_id']} | Token: {$row['token_start']}... | Expires: {$row['expires_at']} | Created: {$row['created_at']}\n";
  }
} else {
  echo "   (no tokens found)\n";
}

// 6. Check if the URL token matches any stored token
if (isset($_GET['check_token'])) {
  $checkToken = $_GET['check_token'];
  echo "\n5. Checking token: " . substr($checkToken, 0, 20) . "...\n";
  echo "   Token length: " . strlen($checkToken) . "\n";
  
  $stmt = $mysqli->prepare('SELECT customer_id, expires_at FROM password_reset_tokens WHERE token = ?');
  $stmt->bind_param('s', $checkToken);
  $stmt->execute();
  $result = $stmt->get_result()->fetch_assoc();
  
  if ($result) {
    echo "   Found! Customer: {$result['customer_id']} | Expires: {$result['expires_at']}\n";
    echo "   Expired: " . (strtotime($result['expires_at']) < time() ? "YES" : "NO") . "\n";
  } else {
    echo "   NOT FOUND in database!\n";
  }
}

<?php
// ============================================
// Configuration & Database Connection
// Connected to 'cloths' database
// ============================================

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

define('APP_NAME', 'urban outfit — Fashion & Admin');
define('BASE_PATH', dirname(__DIR__));

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

// Cashfree Payment Gateway Configuration
$envFile = dirname(__DIR__) . '/.env';
if (file_exists($envFile)) {
  $envLines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
  foreach ($envLines as $line) {
    if (strpos(trim($line), '#') === 0) continue;
    if (strpos($line, '=') !== false) {
      list($key, $value) = explode('=', $line, 2);
      $_ENV[trim($key)] = trim($value);
    }
  }
}
define('CF_APP_ID', $_ENV['CF_APP_ID'] ?? '');
define('CF_SECRET_KEY', $_ENV['CF_SECRET_KEY'] ?? '');
define('CF_ENV', 'production');
define('CF_API_URL', 'https://api.cashfree.com/pg');

// Ensure uploads directory exists
if (!is_dir(UPLOADS_PATH . '/products')) {
  @mkdir(UPLOADS_PATH . '/products', 0777, true);
}
if (!is_dir(UPLOADS_PATH . '/categories')) {
  @mkdir(UPLOADS_PATH . '/categories', 0777, true);
}

// Database Credentials (supports .env for hosted)
$dbHost = $_ENV['DB_HOST'] ?? '127.0.0.1';
$dbName = $_ENV['DB_NAME'] ?? 'cloths';
$dbUser = $_ENV['DB_USER'] ?? 'root';
$dbPass = $_ENV['DB_PASS'] ?? '';

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
    // Check if last_login column exists in customers
    $checkLastLogin = $mysqli->query("SHOW COLUMNS FROM customers LIKE 'last_login'");
    if ($checkLastLogin && $checkLastLogin->num_rows === 0) {
      $mysqli->query("ALTER TABLE customers ADD COLUMN last_login DATETIME NULL AFTER is_active");
    }
    // Check if payment_session_id column exists in orders
    $checkPsid = $mysqli->query("SHOW COLUMNS FROM orders LIKE 'payment_session_id'");
    if ($checkPsid && $checkPsid->num_rows === 0) {
      $mysqli->query("ALTER TABLE orders ADD COLUMN payment_session_id VARCHAR(255) NULL AFTER payment_method");
    }
    // Check if carts table exists
    $checkCarts = $mysqli->query("SHOW TABLES LIKE 'carts'");
    if ($checkCarts && $checkCarts->num_rows === 0) {
      $mysqli->query("CREATE TABLE IF NOT EXISTS carts (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        customer_id INT UNSIGNED NULL,
        session_id VARCHAR(128) NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    }
    // Check if wishlists table exists, recreate without foreign keys if needed
    $checkWishlists = $mysqli->query("SHOW TABLES LIKE 'wishlists'");
    if ($checkWishlists && $checkWishlists->num_rows > 0) {
      $checkFk = $mysqli->query("SHOW CREATE TABLE wishlists");
      if ($checkFk) {
        $createSql = $checkFk->fetch_assoc()['Create Table'] ?? '';
        if (strpos($createSql, 'FOREIGN KEY') !== false) {
          $mysqli->query("DROP TABLE wishlists");
          $checkWishlists = false;
        }
      }
    }
    if ($checkWishlists && $checkWishlists->num_rows === 0) {
      $mysqli->query("CREATE TABLE IF NOT EXISTS wishlists (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        customer_id INT UNSIGNED NOT NULL,
        product_id INT UNSIGNED NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_wishlist_item (customer_id, product_id)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    }
    // Check if mega_menu_items table exists
    $checkMegaMenu = $mysqli->query("SHOW TABLES LIKE 'mega_menu_items'");
    if ($checkMegaMenu && $checkMegaMenu->num_rows === 0) {
      $mysqli->query("CREATE TABLE IF NOT EXISTS mega_menu_items (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        department ENUM('men', 'women', 'kids', 'explore') NOT NULL,
        name VARCHAR(150) NOT NULL,
        slug VARCHAR(150) NOT NULL,
        url VARCHAR(500) NULL,
        image VARCHAR(500) NULL,
        sort_order INT DEFAULT 0,
        is_active TINYINT(1) DEFAULT 1,
        is_sale TINYINT(1) DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
      // Seed default mega menu items
      $mysqli->query("INSERT INTO mega_menu_items (department, name, slug, url, sort_order, is_active, is_sale) VALUES
        ('men', 'Oversized Drop Tees', 'oversized-tees', NULL, 1, 1, 0),
        ('men', 'Streetwear', 'streetwear', NULL, 2, 1, 0),
        ('men', 'Ethnic Fusion Kurtas', 'kurtas', NULL, 3, 1, 0),
        ('men', 'Resort Co-Ords', 'co-ords', NULL, 4, 1, 0),
        ('men', 'Shirts', 'shirts', NULL, 5, 1, 0),
        ('men', 'Bottoms', 'bottoms', NULL, 6, 1, 0),
        ('women', 'Chikankari Edit', 'chikankari', NULL, 1, 1, 0),
        ('women', 'Dresses & Co-Ords', 'dresses', NULL, 2, 1, 0),
        ('women', 'Kurtis & Sets', 'kurtis', NULL, 3, 1, 0),
        ('women', 'Streetwear', 'streetwear', NULL, 4, 1, 0),
        ('women', 'Linen Collection', 'linen', NULL, 5, 1, 0),
        ('women', 'Bottoms', 'bottoms', NULL, 6, 1, 0),
        ('kids', 'Boys', 'boys', NULL, 1, 1, 0),
        ('kids', 'Girls', 'girls', NULL, 2, 1, 0),
        ('kids', 'Ethnic Wear', 'ethnic', NULL, 3, 1, 0),
        ('kids', 'Matching Co-Ords', 'co-ords', NULL, 4, 1, 0),
        ('explore', 'New Arrivals', 'new-arrivals', NULL, 1, 1, 0),
        ('explore', 'Bestsellers', 'bestsellers', NULL, 2, 1, 0),
        ('explore', 'Heritage Fusion', 'ethnic-fusion', NULL, 3, 1, 0),
        ('explore', 'Sale', 'sale', NULL, 4, 1, 1)
      ");
    }
    // Check if cart_items table exists
    $checkCartItems = $mysqli->query("SHOW TABLES LIKE 'cart_items'");
    if ($checkCartItems && $checkCartItems->num_rows === 0) {
      $mysqli->query("CREATE TABLE IF NOT EXISTS cart_items (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        cart_id INT UNSIGNED NOT NULL,
        product_id INT UNSIGNED NOT NULL,
        quantity INT UNSIGNED NOT NULL DEFAULT 1,
        size VARCHAR(20) NULL,
        unit_price DECIMAL(10,2) NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (cart_id) REFERENCES carts(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
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
  if (strpos($url, 'http') !== 0) {
    $url = BASE_URL . '/' . ltrim($url, '/');
  }
  header('Location: ' . $url, true, $statusCode);
  exit;
}

function sanitize($string) {
  return htmlspecialchars(trim((string)$string), ENT_QUOTES, 'UTF-8');
}

// ─── SECURITY FUNCTIONS ───

function generateCSRFToken() {
  if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
  }
  return $_SESSION['csrf_token'];
}

function validateCSRFToken($token) {
  return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function getCSRFInput() {
  return '<input type="hidden" name="csrf_token" value="' . generateCSRFToken() . '">';
}

function rateLimit($key, $maxAttempts = 5, $windowSeconds = 300) {
  global $mysqli;
  if (!$mysqli) return false;

  $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
  $rateKey = $key . ':' . $ip;

  if (!isset($_SESSION['rate_limit'])) {
    $_SESSION['rate_limit'] = [];
  }

  $now = time();

  if (!isset($_SESSION['rate_limit'][$rateKey])) {
    $_SESSION['rate_limit'][$rateKey] = ['count' => 0, 'first' => $now];
  }

  $rl = &$_SESSION['rate_limit'][$rateKey];

  if (($now - $rl['first']) > $windowSeconds) {
    $rl = ['count' => 0, 'first' => $now];
  }

  $rl['count']++;

  if ($rl['count'] > $maxAttempts) {
    return true; // rate limited
  }

  return false;
}

function getRemainingAttempts($key, $maxAttempts = 5, $windowSeconds = 300) {
  if (!isset($_SESSION['rate_limit'])) return $maxAttempts;
  $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
  $rateKey = $key . ':' . $ip;
  $rl = $_SESSION['rate_limit'][$rateKey] ?? null;
  if (!$rl) return $maxAttempts;
  $elapsed = time() - $rl['first'];
  if ($elapsed > $windowSeconds) return $maxAttempts;
  return max(0, $maxAttempts - $rl['count']);
}

function validateEmail($email) {
  $email = trim($email);
  if (strlen($email) > 254) return false;
  return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function validateName($name) {
  $name = trim($name);
  if (strlen($name) < 2 || strlen($name) > 100) return false;
  return preg_match('/^[a-zA-Z\s\.\-\']+$/', $name) === 1;
}

function validatePhone($phone) {
  $phone = trim($phone);
  if (empty($phone)) return true; // optional
  $phone = preg_replace('/[\s\-\(\)\+]/', '', $phone);
  return preg_match('/^[6-9]\d{9}$/', $phone) === 1;
}

function validateSubject($subject) {
  $subject = trim($subject);
  if (strlen($subject) < 2 || strlen($subject) > 200) return false;
  return preg_match('/^[a-zA-Z0-9\s\.\,\-\?\!\@\&\']+$/', $subject) === 1;
}

function validateMessage($message) {
  $message = trim($message);
  if (strlen($message) < 5 || strlen($message) > 2000) return false;
  return preg_match('/^[\s\S]{5,2000}$/', $message) === 1;
}

function isHoneypotFilled() {
  return !empty($_POST['website_url']);
}

function sanitizeInput($input, $maxLen = 500) {
  $input = trim((string)$input);
  $input = strip_tags($input);
  $input = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $input);
  if (strlen($input) > $maxLen) {
    $input = substr($input, 0, $maxLen);
  }
  return $input;
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
    $uploadedUrl = BASE_URL . '/uploads/' . trim($subfolder, '/') . '/' . $filename;

    // ── Auto git push to GitHub after every upload ──
    gitPushUpload($targetPath);

    return $uploadedUrl;
  }
  }

  return null;
}

/**
 * Auto push uploaded image to GitHub via REST API.
 * Works on ANY hosted server — no git, no shell_exec needed.
 * Requires in .env:
 *   GITHUB_TOKEN=ghp_xxxxxxxxxxxxxxxxxxxx
 *   GITHUB_REPO=MadhavArora1213/clothing
 *   GITHUB_BRANCH=main
 *
 * @param string $filePath  Absolute path to the newly uploaded file
 */
function gitPushUpload($filePath) {
  // ── Load GitHub credentials from .env ──
  $token  = $_ENV['GITHUB_TOKEN']  ?? '';
  $repo   = $_ENV['GITHUB_REPO']   ?? 'MadhavArora1213/clothing';
  $branch = $_ENV['GITHUB_BRANCH'] ?? 'main';

  if (empty($token)) {
    error_log('gitPushUpload: GITHUB_TOKEN not set in .env — skipping push');
    return;
  }

  if (!file_exists($filePath)) {
    error_log('gitPushUpload: file not found: ' . $filePath);
    return;
  }

  // ── Build relative path for GitHub API (use forward slashes) ──
  $repoRoot = str_replace('\\', '/', dirname(__DIR__));
  $absPath  = str_replace('\\', '/', $filePath);
  $relPath  = ltrim(str_replace($repoRoot, '', $absPath), '/');
  // e.g. "uploads/products/img_abc123.jpg"

  // ── Encode file content as base64 ──
  $content = base64_encode(file_get_contents($filePath));

  // ── GitHub API endpoint ──
  $apiUrl = "https://api.github.com/repos/{$repo}/contents/{$relPath}";

  // ── Check if file already exists (need its SHA for update) ──
  $sha = null;
  $ch = curl_init($apiUrl . '?ref=' . urlencode($branch));
  curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 15,
    CURLOPT_HTTPHEADER     => [
      'Authorization: token ' . $token,
      'Accept: application/vnd.github.v3+json',
      'User-Agent: UrbanOutfitCollection-AutoPush/1.0',
      'X-GitHub-Api-Version: 2022-11-28',
    ],
  ]);
  $existing = curl_exec($ch);
  curl_close($ch);
  if ($existing) {
    $existingData = json_decode($existing, true);
    $sha = $existingData['sha'] ?? null;
  }

  // ── Build request body ──
  $filename  = basename($filePath);
  $timestamp = date('Y-m-d H:i:s');
  $body = [
    'message' => "upload: {$filename} [{$timestamp}]",
    'content' => $content,
    'branch'  => $branch,
  ];
  if ($sha) {
    $body['sha'] = $sha; // required for updating existing file
  }

  // ── PUT request to GitHub API ──
  $ch = curl_init($apiUrl);
  curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 30,
    CURLOPT_CUSTOMREQUEST  => 'PUT',
    CURLOPT_POSTFIELDS     => json_encode($body),
    CURLOPT_HTTPHEADER     => [
      'Authorization: token ' . $token,
      'Accept: application/vnd.github.v3+json',
      'Content-Type: application/json',
      'User-Agent: UrbanOutfitCollection-AutoPush/1.0',
      'X-GitHub-Api-Version: 2022-11-28',
    ],
  ]);

  $response = curl_exec($ch);
  $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  $curlErr  = curl_error($ch);
  curl_close($ch);

  if ($curlErr) {
    error_log("gitPushUpload: curl error for {$filename}: {$curlErr}");
    return;
  }

  if (in_array($httpCode, [200, 201])) {
    error_log("gitPushUpload: SUCCESS pushed {$relPath} to GitHub (HTTP {$httpCode})");
  } else {
    $result = json_decode($response, true);
    $msg    = $result['message'] ?? $response;
    error_log("gitPushUpload: FAILED {$filename} — HTTP {$httpCode}: {$msg}");
  }
}

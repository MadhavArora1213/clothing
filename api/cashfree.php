<?php
// ─── STANDALONE - No database.php dependency ───
error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Clean output buffer
if (ob_get_level()) ob_end_clean();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: ' . ( $_SERVER['HTTP_HOST'] ?? '*'));
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  http_response_code(200);
  exit;
}

// Load .env directly
$envFile = dirname(__DIR__) . '/.env';
if (file_exists($envFile)) {
  $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
  foreach ($lines as $line) {
    if (strpos(trim($line), '#') === 0) continue;
    if (strpos($line, '=') !== false) {
      list($key, $value) = explode('=', $line, 2);
      $value = trim($value);
      if (($value[0] ?? '') === '"' && substr($value, -1) === '"') {
        $value = substr($value, 1, -1);
      }
      $_ENV[trim($key)] = $value;
    }
  }
}

$cfAppId = $_ENV['CF_APP_ID'] ?? '';
$cfSecret = $_ENV['CF_SECRET_KEY'] ?? '';
$cfApiUrl = 'https://api.cashfree.com/pg';

$action = $_POST['action'] ?? $_GET['action'] ?? '';

// ─── CREATE ORDER ───
if ($action === 'create_order') {
  // Start session
  if (session_status() === PHP_SESSION_NONE) session_start();

  if (empty($_SESSION['customer_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit;
  }

  $orderId = (int)($_POST['order_id'] ?? 0);
  if ($orderId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid order']);
    exit;
  }

  // Connect to DB directly
  $dbHost = $_ENV['DB_HOST'] ?? '127.0.0.1';
  $dbName = $_ENV['DB_NAME'] ?? 'cloths';
  $dbUser = $_ENV['DB_USER'] ?? 'root';
  $dbPass = $_ENV['DB_PASS'] ?? '';

  $mysqli = @new mysqli($dbHost, $dbUser, $dbPass, $dbName);
  if ($mysqli->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
    exit;
  }
  $mysqli->set_charset('utf8mb4');

  $customerId = $_SESSION['customer_id'];
  $stmt = $mysqli->prepare('SELECT id, order_number, grand_total, customer_name, customer_email, customer_phone FROM orders WHERE id = ? AND customer_id = ?');
  if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Query error']);
    $mysqli->close();
    exit;
  }
  $stmt->bind_param('ii', $orderId, $customerId);
  $stmt->execute();
  $order = $stmt->get_result()->fetch_assoc();

  if (!$order) {
    echo json_encode(['success' => false, 'message' => 'Order not found']);
    $mysqli->close();
    exit;
  }

  // Clean phone
  $phone = preg_replace('/[^0-9]/', '', $order['customer_phone'] ?? '9999999999');
  if (strlen($phone) > 10) $phone = substr($phone, -10);
  if (strlen($phone) !== 10) $phone = '9999999999';

  // Build CF order
  $cfOrderId = $order['order_number'] . '_' . time();
  $host = $_SERVER['HTTP_HOST'] ?? '';
  $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || ($_SERVER['SERVER_PORT'] ?? 80) == 443;
  $protocol = $isHttps ? 'https' : 'http';
  $baseUrl = $protocol . '://' . $host;

  // Calculate script directory for base URL
  $scriptDir = dirname($_SERVER['SCRIPT_NAME'] ?? '');
  $baseFolder = preg_replace('#/api$#', '', $scriptDir);
  $baseUrl .= $baseFolder;

  $payload = [
    'order_id' => $cfOrderId,
    'order_amount' => (float)$order['grand_total'],
    'order_currency' => 'INR',
    'customer_details' => [
      'customer_id' => 'CUST_' . preg_replace('/[^a-zA-Z0-9]/', '', (string)$customerId),
      'customer_name' => $order['customer_name'],
      'customer_email' => $order['customer_email'],
      'customer_phone' => $phone,
    ],
    'order_meta' => [
      'return_url' => $baseUrl . '/api/cashfree.php?action=callback&order_id=' . $order['id'],
      'notify_url' => $baseUrl . '/api/cashfree_webhook.php',
    ],
    'order_note' => "Order #{$order['order_number']}",
  ];

  // Call Cashfree API
  $ch = curl_init($cfApiUrl . '/orders');
  curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($payload),
    CURLOPT_HTTPHEADER => [
      'Content-Type: application/json',
      'x-client-id: ' . $cfAppId,
      'x-client-secret: ' . $cfSecret,
      'x-api-version: 2023-08-01',
    ],
  ]);

  $response = curl_exec($ch);
  $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  $curlErr = curl_error($ch);
  curl_close($ch);

  if ($curlErr) {
    echo json_encode(['success' => false, 'message' => 'Connection failed: ' . $curlErr]);
    $mysqli->close();
    exit;
  }

  $result = json_decode($response, true);

  if (isset($result['cf_order_id'])) {
    $upd = $mysqli->prepare('UPDATE orders SET payment_session_id = ? WHERE id = ?');
    if ($upd) {
      $upd->bind_param('si', $cfOrderId, $orderId);
      $upd->execute();
    }
    $mysqli->close();

    echo json_encode([
      'success' => true,
      'payment_session_id' => $result['payment_session_id'] ?? '',
      'order_id' => $orderId,
    ]);
  } else {
    $errMsg = $result['message'] ?? $result['error_description'] ?? 'Payment gateway error';
    echo json_encode(['success' => false, 'message' => $errMsg]);
    $mysqli->close();
  }
  exit;
}

// ─── CALLBACK ───
if ($action === 'callback') {
  if (session_status() === PHP_SESSION_NONE) session_start();
  $orderId = (int)($_GET['order_id'] ?? 0);

  $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
  $host = $_SERVER['HTTP_HOST'];
  $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
  $baseFolder = preg_replace('#/api$#', '', $scriptDir);
  $baseUrl = $protocol . '://' . $host . $baseFolder;

  if ($orderId > 0) {
    // Check payment status from DB
    $dbHost = $_ENV['DB_HOST'] ?? '127.0.0.1';
    $dbName = $_ENV['DB_NAME'] ?? 'cloths';
    $dbUser = $_ENV['DB_USER'] ?? 'root';
    $dbPass = $_ENV['DB_PASS'] ?? '';
    $db = @new mysqli($dbHost, $dbUser, $dbPass, $dbName);
    $paymentOk = false;

    if (!$db->connect_error) {
      $db->set_charset('utf8mb4');
      $stmt = $db->prepare('SELECT payment_status, payment_session_id, order_number FROM orders WHERE id = ?');
      if ($stmt) {
        $stmt->bind_param('i', $orderId);
        $stmt->execute();
        $ord = $stmt->get_result()->fetch_assoc();
        if ($ord) {
          // If already paid, proceed to success
          if (in_array($ord['payment_status'], ['paid', 'completed'])) {
            $paymentOk = true;
          } else {
            // Verify with Cashfree API
            $cfAppId = $_ENV['CF_APP_ID'] ?? '';
            $cfSecret = $_ENV['CF_SECRET_KEY'] ?? '';
            $cfOrderId = $ord['payment_session_id'] ?? '';
            if ($cfAppId && $cfSecret && $cfOrderId && preg_match('/^ORD-/i', $cfOrderId)) {
              $ch = curl_init('https://api.cashfree.com/pg/orders/' . urlencode($cfOrderId));
              curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 10,
                CURLOPT_HTTPHEADER => [
                  'x-client-id: ' . $cfAppId,
                  'x-client-secret: ' . $cfSecret,
                  'x-api-version: 2023-08-01',
                ],
              ]);
              $cfResp = curl_exec($ch);
              curl_close($ch);
              if ($cfResp) {
                $cfData = json_decode($cfResp, true);
                $cfStatus = $cfData['order_status'] ?? '';
                if ($cfStatus === 'PAID') {
                  $db->query("UPDATE orders SET payment_status = 'paid', order_status = 'confirmed' WHERE id = $orderId");
                  $paymentOk = true;
                }
              }
            }
          }
        }
      }
      $db->close();
    }

    if ($paymentOk) {
      // Payment confirmed — clear cart
      $_SESSION['last_order_id'] = $orderId;
      $db2 = @new mysqli($dbHost, $dbUser, $dbPass, $dbName);
      if (!$db2->connect_error) {
        $db2->set_charset('utf8mb4');
        $sid = session_id();
        $custId = $_SESSION['customer_id'] ?? null;
        if ($custId) {
          $del = $db2->prepare('DELETE ci FROM cart_items ci JOIN carts c ON ci.cart_id = c.id WHERE c.customer_id = ?');
          if ($del) { $del->bind_param('i', $custId); $del->execute(); }
        } else {
          $del = $db2->prepare('DELETE ci FROM cart_items ci JOIN carts c ON ci.cart_id = c.id WHERE c.session_id = ? AND c.customer_id IS NULL');
          if ($del) { $del->bind_param('s', $sid); $del->execute(); }
        }
        $db2->close();
      }
      header('Location: ' . $baseUrl . '/customer/order-success.php');
      exit;
    } else {
      // Payment not confirmed — redirect back to checkout
      header('Location: ' . $baseUrl . '/customer/checkout.php');
      exit;
    }
  }
  // Fallback: go to shop
  header('Location: ' . $baseUrl . '/shop.php');
  exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);

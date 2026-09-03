<?php
// Suppress all errors, log them instead
error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Start output buffer to catch any stray output
ob_start();

// Set JSON header first
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Load database and config (may output warnings)
require_once __DIR__ . '/../config/database.php';

// Flush all output buffers - discard any PHP warnings/errors
while (ob_get_level()) {
  ob_end_clean();
}

// Re-set headers after buffer flush
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

// Handle OPTIONS preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  http_response_code(200);
  exit;
}

// Create a new Cashfree Order
if ($action === 'create_order') {
  if (!isset($_SESSION['customer_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit;
  }

  $orderId = (int)($_POST['order_id'] ?? 0);
  if ($orderId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid order']);
    exit;
  }

  $customerId = $_SESSION['customer_id'];

  if (!$mysqli) {
    echo json_encode(['success' => false, 'message' => 'Database not connected']);
    exit;
  }

  $stmt = $mysqli->prepare('SELECT id, order_number, grand_total, customer_name, customer_email, customer_phone FROM orders WHERE id = ? AND customer_id = ?');
  if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Database query error']);
    exit;
  }

  $stmt->bind_param('ii', $orderId, $customerId);
  $stmt->execute();
  $order = $stmt->get_result()->fetch_assoc();

  if (!$order) {
    echo json_encode(['success' => false, 'message' => 'Order not found']);
    exit;
  }

  // Generate unique CF order ID
  $cfOrderId = $order['order_number'] . '_' . time();

  // Clean phone number - remove +91, spaces, dashes
  $phone = $order['customer_phone'] ?? '9999999999';
  $phone = preg_replace('/[^0-9]/', '', $phone);
  if (strlen($phone) > 10) {
    $phone = substr($phone, -10);
  }
  if (strlen($phone) !== 10) {
    $phone = '9999999999';
  }

  // Clean customer_id - alphanumeric only
  $custId = 'CUST_' . preg_replace('/[^a-zA-Z0-9]/', '', (string)$customerId);

  // Customer details
  $customerDetails = [
    'customer_id' => $custId,
    'customer_name' => $order['customer_name'],
    'customer_email' => $order['customer_email'],
    'customer_phone' => $phone,
  ];

  // Build return URL
  $isLocalhost = in_array($_SERVER['HTTP_HOST'] ?? '', ['localhost', '127.0.0.1', '::1']);
  $returnUrl = $isLocalhost
    ? BASE_URL . '/customer/order-success.php'
    : BASE_URL . '/api/cashfree.php?action=callback&order_id=' . $order['id'];

  $payload = [
    'order_id' => $cfOrderId,
    'order_amount' => (float)$order['grand_total'],
    'order_currency' => 'INR',
    'customer_details' => $customerDetails,
    'order_meta' => [
      'return_url' => $returnUrl,
      'notify_url' => $isLocalhost ? $returnUrl : BASE_URL . '/api/cashfree_webhook.php',
    ],
    'order_note' => "Order #{$order['order_number']}",
  ];

  $result = cashfreeApiCall('/orders', 'POST', $payload);

  if (isset($result['cf_order_id'])) {
    $upd = $mysqli->prepare('UPDATE orders SET payment_session_id = ? WHERE id = ?');
    if ($upd) {
      $upd->bind_param('si', $result['cf_order_id'], $orderId);
      $upd->execute();
    }

    echo json_encode([
      'success' => true,
      'cf_order_id' => $result['cf_order_id'],
      'payment_session_id' => $result['payment_session_id'] ?? '',
      'order_id' => $orderId,
      'order_amount' => $order['grand_total'],
    ]);
  } else {
    $errMsg = $result['message'] ?? $result['error_description'] ?? 'Failed to create payment order';
    echo json_encode(['success' => false, 'message' => $errMsg]);
  }
  exit;
}

// Handle callback return from Cashfree
if ($action === 'callback') {
  $orderId = (int)($_GET['order_id'] ?? 0);
  if ($orderId > 0) {
    $_SESSION['last_order_id'] = $orderId;
    header('Location: ' . BASE_URL . '/customer/order-success.php');
    exit;
  }
  header('Location: ' . BASE_URL . '/customer/cart.php');
  exit;
}

// Verify payment status
if ($action === 'verify') {
  $orderId = (int)($_GET['order_id'] ?? $_POST['order_id'] ?? 0);
  if ($orderId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid order']);
    exit;
  }

  $stmt = $mysqli->prepare('SELECT id, order_number, payment_session_id, payment_status FROM orders WHERE id = ?');
  $stmt->bind_param('i', $orderId);
  $stmt->execute();
  $order = $stmt->get_result()->fetch_assoc();

  if (!$order) {
    echo json_encode(['success' => false, 'message' => 'Order not found']);
    exit;
  }

  if (empty($order['payment_session_id'])) {
    echo json_encode(['success' => false, 'message' => 'No payment session found', 'status' => 'pending']);
    exit;
  }

  $result = cashfreeApiCall("/orders/{$order['order_number']}", 'GET');

  if (isset($result['order_status'])) {
    $cfStatus = $result['order_status'];
    $newPaymentStatus = 'pending';

    switch ($cfStatus) {
      case 'PAID':
        $newPaymentStatus = 'completed';
        break;
      case 'EXPIRED':
      case 'TERMINATED':
        $newPaymentStatus = 'failed';
        break;
    }

    $upd = $mysqli->prepare('UPDATE orders SET payment_status = ?, order_status = ? WHERE id = ? AND payment_status != "completed"');
    if ($upd) {
      $orderStatus = $newPaymentStatus === 'completed' ? 'confirmed' : 'pending';
      $upd->bind_param('ssi', $newPaymentStatus, $orderStatus, $orderId);
      $upd->execute();
    }

    echo json_encode(['success' => true, 'status' => $newPaymentStatus, 'cf_status' => $cfStatus]);
  } else {
    echo json_encode(['success' => false, 'message' => 'Could not verify payment', 'status' => $order['payment_status']]);
  }
  exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid action']);

// ─── Cashfree API Helper ───
function cashfreeApiCall($endpoint, $method = 'POST', $data = []) {
  if (!function_exists('curl_init')) {
    return ['message' => 'cURL not enabled on server'];
  }

  $appId = $_ENV['CF_APP_ID'] ?? '';
  $secret = $_ENV['CF_SECRET_KEY'] ?? '';
  $url = 'https://api.cashfree.com/pg' . $endpoint;

  $headers = [
    'Content-Type: application/json',
    'x-client-id: ' . $appId,
    'x-client-secret: ' . $secret,
    'x-api-version: 2023-08-01',
  ];

  $ch = curl_init();
  curl_setopt_array($ch, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_SSL_VERIFYPEER => true,
    CURLOPT_HTTPHEADER => $headers,
  ]);

  if ($method === 'POST') {
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
  }

  $response = curl_exec($ch);
  $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  $curlError = curl_error($ch);
  curl_close($ch);

  if ($curlError) {
    return ['message' => 'Network error: ' . $curlError];
  }

  $decoded = json_decode($response, true);
  return $decoded ?? ['message' => 'Invalid API response'];
}

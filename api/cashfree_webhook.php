<?php
require_once __DIR__ . '/../config/database.php';

// Cashfree Webhook Handler
// Cashfree sends payment status notifications here

$rawBody = file_get_contents('php://input');
$payload = json_decode($rawBody, true);

if (!$payload) {
  http_response_code(400);
  echo json_encode(['error' => 'Invalid payload']);
  exit;
}

// Log the webhook
error_log("Cashfree Webhook: " . $rawBody);

$eventType = $payload['type'] ?? '';
$orderData = $payload['payload']['order'] ?? [];
$orderEntity = $orderData['order'] ?? [];
$cfOrderId = $orderEntity['order_id'] ?? '';

if (empty($cfOrderId)) {
  http_response_code(400);
  echo json_encode(['error' => 'Missing order_id']);
  exit;
}

// Extract our order_number from cf order id (format: ORD-XXXXXXXX_timestamp)
$orderNumber = explode('_', $cfOrderId)[0] ?? $cfOrderId;

// Find the order
$stmt = $mysqli->prepare('SELECT id, payment_status FROM orders WHERE order_number = ?');
if (!$stmt) {
  http_response_code(500);
  echo json_encode(['error' => 'Database error']);
  exit;
}
$stmt->bind_param('s', $orderNumber);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
  http_response_code(404);
  echo json_encode(['error' => 'Order not found']);
  exit;
}

// Already completed, skip
if ($order['payment_status'] === 'completed' || $order['payment_status'] === 'paid') {
  echo json_encode(['status' => 'already_processed']);
  exit;
}

// Determine payment status from event
$orderStatus = $orderEntity['order_status'] ?? '';
$paymentStatus = 'pending';

switch ($orderStatus) {
  case 'PAID':
    $paymentStatus = 'completed';
    break;
  case 'EXPIRED':
  case 'TERMINATED':
    $paymentStatus = 'failed';
    break;
  case 'PENDING':
  default:
    $paymentStatus = 'pending';
    break;
}

// Update order
$newOrderStatus = $paymentStatus === 'completed' ? 'confirmed' : 'pending';
$upd = $mysqli->prepare('UPDATE orders SET payment_status = ?, order_status = ? WHERE id = ?');
if ($upd) {
  $upd->bind_param('ssi', $paymentStatus, $newOrderStatus, $order['id']);
  $upd->execute();
}

echo json_encode(['status' => 'ok', 'payment_status' => $paymentStatus]);

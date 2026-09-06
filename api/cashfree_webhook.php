<?php
require_once __DIR__ . '/../config/database.php';

$rawBody = file_get_contents('php://input');
if (!$rawBody) {
  http_response_code(400);
  echo json_encode(['error' => 'Empty body']);
  exit;
}

$payload = json_decode($rawBody, true);
if (!$payload) {
  http_response_code(400);
  echo json_encode(['error' => 'Invalid JSON']);
  exit;
}

// ── Verify Webhook Signature ──
$webhookSignature = $_SERVER['HTTP_X_WEBHOOK_SIGNATURE'] ?? '';
if (empty($webhookSignature)) {
  error_log("Cashfree Webhook: Missing x-webhook-signature header");
  http_response_code(401);
  echo json_encode(['error' => 'Missing signature']);
  exit;
}

// Verify signature using HMAC SHA256
$expectedSignature = hash_hmac('sha256', $rawBody, CF_SECRET_KEY);
if (!hash_equals($expectedSignature, $webhookSignature)) {
  error_log("Cashfree Webhook: Invalid signature. Expected: " . substr($expectedSignature, 0, 16) . "...");
  http_response_code(401);
  echo json_encode(['error' => 'Invalid signature']);
  exit;
}

error_log("Cashfree Webhook RAW: " . $rawBody);

// ── Cashfree 2023-08-01 payload structure ──
$eventType = $payload['type'] ?? '';
$data      = $payload['data'] ?? [];
$orderData = $data['order']   ?? [];
$cfOrderId = $orderData['order_id'] ?? '';

if (empty($cfOrderId)) {
  $cfOrderId = $payload['payload']['order']['order']['order_id']
            ?? $payload['payload']['order']['order_id']
            ?? $payload['data']['order']['order_id']
            ?? '';
}

if (empty($cfOrderId)) {
  error_log("Cashfree Webhook: no order_id found in payload: " . $rawBody);
  http_response_code(400);
  echo json_encode(['error' => 'Missing order_id']);
  exit;
}

$orderNumber = explode('_', $cfOrderId)[0];

$stmt = $mysqli->prepare('SELECT id, payment_status FROM orders WHERE order_number = ?');
if (!$stmt) {
  http_response_code(500);
  echo json_encode(['error' => 'DB prepare error']);
  exit;
}
$stmt->bind_param('s', $orderNumber);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
  $stmt2 = $mysqli->prepare('SELECT id, payment_status FROM orders WHERE order_number = ?');
  $stmt2->bind_param('s', $cfOrderId);
  $stmt2->execute();
  $order = $stmt2->get_result()->fetch_assoc();
}

if (!$order) {
  error_log("Cashfree Webhook: order not found for cf_order_id=$cfOrderId, order_number=$orderNumber");
  http_response_code(404);
  echo json_encode(['error' => 'Order not found']);
  exit;
}

if (in_array($order['payment_status'], ['paid', 'completed', 'refunded'])) {
  echo json_encode(['status' => 'already_processed']);
  exit;
}

$cfOrderStatus   = $orderData['order_status'] ?? '';
$cfPaymentStatus = $data['payment']['payment_status'] ?? '';

$newPaymentStatus = 'pending';
$newOrderStatus   = 'pending';

if ($eventType === 'PAYMENT_SUCCESS_WEBHOOK' || $cfOrderStatus === 'PAID' || $cfPaymentStatus === 'SUCCESS') {
  $newPaymentStatus = 'paid';
  $newOrderStatus   = 'confirmed';
} elseif (in_array($eventType, ['PAYMENT_FAILED_WEBHOOK', 'PAYMENT_USER_DROPPED_WEBHOOK'])
       || in_array($cfOrderStatus, ['EXPIRED', 'TERMINATED'])
       || $cfPaymentStatus === 'FAILED') {
  $newPaymentStatus = 'failed';
  $newOrderStatus   = 'pending';
}

$upd = $mysqli->prepare('UPDATE orders SET payment_status = ?, order_status = ? WHERE id = ?');
if ($upd) {
  $upd->bind_param('ssi', $newPaymentStatus, $newOrderStatus, $order['id']);
  $upd->execute();
}

error_log("Cashfree Webhook: order {$order['id']} → payment=$newPaymentStatus, order=$newOrderStatus (event=$eventType)");
echo json_encode(['status' => 'ok', 'payment_status' => $newPaymentStatus]);

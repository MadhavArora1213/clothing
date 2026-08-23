<?php
require_once dirname(__DIR__) . '/config/database.php';
header('Content-Type: application/json');

$customerId = $_SESSION['customer_id'] ?? null;
$sessionId = session_id();

if ($customerId) {
  $stmt = $mysqli->prepare('SELECT id FROM carts WHERE customer_id = ?');
  $stmt->bind_param('i', $customerId);
  $stmt->execute();
  $cart = $stmt->get_result()->fetch_assoc();
} else {
  $stmt = $mysqli->prepare('SELECT id FROM carts WHERE session_id = ?');
  $stmt->bind_param('s', $sessionId);
  $stmt->execute();
  $cart = $stmt->get_result()->fetch_assoc();
}

$count = 0;
if ($cart) {
  $result = $mysqli->query("SELECT SUM(quantity) as total FROM cart_items WHERE cart_id = {$cart['id']}");
  $count = $result->fetch_assoc()['total'] ?? 0;
}

echo json_encode(['count' => (int)$count]);

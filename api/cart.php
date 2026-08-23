<?php
require_once dirname(__DIR__) . '/config/database.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  echo json_encode(['success' => false, 'message' => 'Invalid method']);
  exit;
}

$customerId = $_SESSION['customer_id'] ?? null;
$sessionId = session_id();
$productId = (int)($_POST['product_id'] ?? 0);
$sizeId = !empty($_POST['size_id']) ? (int)$_POST['size_id'] : null;
$quantity = max(1, (int)($_POST['quantity'] ?? 1));

if ($productId <= 0) {
  echo json_encode(['success' => false, 'message' => 'Invalid product']);
  exit;
}

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

if (!$cart) {
  $stmt = $mysqli->prepare('INSERT INTO carts (customer_id, session_id) VALUES (?, ?)');
  $customerIdParam = $customerId;
  $stmt->bind_param('is', $customerIdParam, $sessionId);
  $stmt->execute();
  $cartId = $mysqli->insert_id;
} else {
  $cartId = $cart['id'];
}

$product = $mysqli->query("SELECT price FROM products WHERE id = $productId")->fetch_assoc();
if (!$product) {
  echo json_encode(['success' => false, 'message' => 'Product not found']);
  exit;
}

$stmt = $mysqli->prepare('SELECT id, quantity FROM cart_items WHERE cart_id = ? AND product_id = ? AND product_size_id ' . ($sizeId ? '= ?' : 'IS NULL'));
if ($sizeId) {
  $stmt->bind_param('iii', $cartId, $productId, $sizeId);
} else {
  $stmt->bind_param('ii', $cartId, $productId);
}
$stmt->execute();
$existing = $stmt->get_result()->fetch_assoc();

if ($existing) {
  $newQty = $existing['quantity'] + $quantity;
  $stmt = $mysqli->prepare('UPDATE cart_items SET quantity = ? WHERE id = ?');
  $stmt->bind_param('ii', $newQty, $existing['id']);
  $stmt->execute();
} else {
  $stmt = $mysqli->prepare('INSERT INTO cart_items (cart_id, product_id, product_size_id, quantity, unit_price) VALUES (?, ?, ?, ?, ?)');
  $stmt->bind_param('iiidd', $cartId, $productId, $sizeId, $quantity, $product['price']);
  $stmt->execute();
}

echo json_encode(['success' => true, 'message' => 'Added to cart']);

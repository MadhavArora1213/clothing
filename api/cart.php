<?php
require_once dirname(__DIR__) . '/config/database.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  echo json_encode(['success' => false, 'message' => 'Invalid method']);
  exit;
}

$customerId = $_SESSION['customer_id'] ?? null;
$sessionId = session_id();
$action = $_POST['action'] ?? 'add';
$productId = (int)($_POST['product_id'] ?? 0);
$size = !empty($_POST['size']) ? trim($_POST['size']) : null;
$quantity = max(1, (int)($_POST['quantity'] ?? 1));
$productName = $_POST['product_name'] ?? '';
$productPrice = (float)($_POST['product_price'] ?? 0);
$productImage = $_POST['product_image'] ?? '';
$productSlug = $_POST['product_slug'] ?? '';

if ($productId <= 0) {
  echo json_encode(['success' => false, 'message' => 'Invalid product']);
  exit;
}

$product = null;
$stmt = $mysqli->prepare('SELECT id, price FROM products WHERE id = ?');
if ($stmt) {
  $stmt->bind_param('i', $productId);
  $stmt->execute();
  $product = $stmt->get_result()->fetch_assoc();
}

if (!$product && $productName && $productPrice > 0) {
  $slug = $productSlug ?: preg_replace('/[^a-z0-9]+/', '-', strtolower($productName));
  $ins = $mysqli->prepare('INSERT INTO products (id, name, slug, price, image, is_active, category_id) VALUES (?, ?, ?, ?, ?, 1, 1) ON DUPLICATE KEY UPDATE id=id');
  if ($ins) {
    $ins->bind_param('issds', $productId, $productName, $slug, $productPrice, $productImage);
    $ins->execute();
  }
  $product = ['id' => $productId, 'price' => $productPrice];
}

if (!$product) {
  echo json_encode(['success' => false, 'message' => 'Product not found. Please provide product details.']);
  exit;
}

$unitPrice = $product['price'];

if ($customerId) {
  $stmt = $mysqli->prepare('SELECT id FROM carts WHERE customer_id = ?');
  if ($stmt) {
    $stmt->bind_param('i', $customerId);
    $stmt->execute();
    $cart = $stmt->get_result()->fetch_assoc();
  }
} else {
  $stmt = $mysqli->prepare('SELECT id FROM carts WHERE session_id = ?');
  if ($stmt) {
    $stmt->bind_param('s', $sessionId);
    $stmt->execute();
    $cart = $stmt->get_result()->fetch_assoc();
  }
}

if (!$cart) {
  $stmt = $mysqli->prepare('INSERT INTO carts (customer_id, session_id) VALUES (?, ?)');
  if ($stmt) {
    $customerIdParam = $customerId;
    $stmt->bind_param('is', $customerIdParam, $sessionId);
    $stmt->execute();
    $cartId = $mysqli->insert_id;
  } else {
    echo json_encode(['success' => false, 'message' => 'Failed to create cart']);
    exit;
  }
} else {
  $cartId = $cart['id'];
}

$existing = null;
if ($size) {
  $stmt = $mysqli->prepare('SELECT id, quantity FROM cart_items WHERE cart_id = ? AND product_id = ? AND size = ?');
  if ($stmt) {
    $stmt->bind_param('iis', $cartId, $productId, $size);
    $stmt->execute();
    $existing = $stmt->get_result()->fetch_assoc();
  }
} else {
  $stmt = $mysqli->prepare('SELECT id, quantity FROM cart_items WHERE cart_id = ? AND product_id = ? AND (size IS NULL OR size = \'\')');
  if ($stmt) {
    $stmt->bind_param('ii', $cartId, $productId);
    $stmt->execute();
    $existing = $stmt->get_result()->fetch_assoc();
  }
}

if ($existing) {
  $newQty = $existing['quantity'] + $quantity;
  $stmt = $mysqli->prepare('UPDATE cart_items SET quantity = ? WHERE id = ?');
  if ($stmt) {
    $stmt->bind_param('ii', $newQty, $existing['id']);
    $stmt->execute();
  }
} else {
  $stmt = $mysqli->prepare('INSERT INTO cart_items (cart_id, product_id, quantity, size, unit_price) VALUES (?, ?, ?, ?, ?)');
  if ($stmt) {
    $stmt->bind_param('iiisd', $cartId, $productId, $quantity, $size, $unitPrice);
    $stmt->execute();
  }
}

$countStmt = $mysqli->prepare('SELECT COALESCE(SUM(quantity), 0) as cnt FROM cart_items WHERE cart_id = ?');
$cartCount = 0;
if ($countStmt) {
  $countStmt->bind_param('i', $cartId);
  $countStmt->execute();
  $cartCount = $countStmt->get_result()->fetch_assoc()['cnt'] ?? 0;
}

echo json_encode(['success' => true, 'message' => 'Added to cart', 'cart_count' => $cartCount]);

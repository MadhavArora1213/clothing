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

// ─── Helper: find or create cart ───
function getOrCreateCart($mysqli, $customerId, $sessionId) {
  $cart = null;
  if ($customerId) {
    $stmt = $mysqli->prepare('SELECT id FROM carts WHERE customer_id = ?');
    if ($stmt) { $stmt->bind_param('i', $customerId); $stmt->execute(); $cart = $stmt->get_result()->fetch_assoc(); }
  }
  if (!$cart && !$customerId) {
    $stmt = $mysqli->prepare('SELECT id FROM carts WHERE session_id = ? AND customer_id IS NULL');
    if ($stmt) { $stmt->bind_param('s', $sessionId); $stmt->execute(); $cart = $stmt->get_result()->fetch_assoc(); }
  }
  if (!$cart) {
    $stmt = $mysqli->prepare('INSERT INTO carts (customer_id, session_id) VALUES (?, ?)');
    if ($stmt) { $cp = $customerId; $stmt->bind_param('is', $cp, $sessionId); $stmt->execute(); return $mysqli->insert_id; }
    return null;
  }
  return $cart['id'];
}

// ─── Helper: get cart count ───
function getCartCount($mysqli, $cartId) {
  $stmt = $mysqli->prepare('SELECT COALESCE(SUM(quantity), 0) as cnt FROM cart_items WHERE cart_id = ?');
  if ($stmt) { $stmt->bind_param('i', $cartId); $stmt->execute(); return $stmt->get_result()->fetch_assoc()['cnt'] ?? 0; }
  return 0;
}

// ─── Helper: recalculate cart totals and return them ───
function getCartTotals($mysqli, $cartId) {
  $stmt = $mysqli->prepare('SELECT ci.*, p.name, p.price, p.shipping_charge, p.free_shipping FROM cart_items ci JOIN products p ON ci.product_id = p.id WHERE ci.cart_id = ?');
  if (!$stmt) return null;
  $stmt->bind_param('i', $cartId);
  $stmt->execute();
  $items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
  $subtotal = 0; $shippingAmount = 0;
  foreach ($items as $item) {
    $subtotal += $item['unit_price'] * $item['quantity'];
    if (!$item['free_shipping']) $shippingAmount += $item['shipping_charge'] * $item['quantity'];
  }
  $grandTotal = $subtotal + $shippingAmount;
  return [
    'subtotal' => $subtotal,
    'shipping' => $shippingAmount,
    'grand_total' => $grandTotal,
    'item_count' => count($items),
    'items' => array_map(fn($i) => ['id' => $i['id'], 'name' => $i['name'], 'size' => $i['size'], 'unit_price' => $i['unit_price'], 'quantity' => $i['quantity'], 'image' => $i['image']], $items)
  ];
}

// ─── ACTION: ADD ───
if ($action === 'add') {
  $productId = (int)($_POST['product_id'] ?? 0);
  $size = !empty($_POST['size']) ? trim($_POST['size']) : null;
  $quantity = max(1, (int)($_POST['quantity'] ?? 1));

  if ($productId <= 0) { echo json_encode(['success' => false, 'message' => 'Invalid product']); exit; }

  $product = null;
  $stmt = $mysqli->prepare('SELECT id, name, slug, price, image FROM products WHERE id = ? AND is_active = 1');
  if ($stmt) { $stmt->bind_param('i', $productId); $stmt->execute(); $product = $stmt->get_result()->fetch_assoc(); }
  if (!$product) { echo json_encode(['success' => false, 'message' => 'Product not found or unavailable.']); exit; }

  $unitPrice = $product['price'];
  $cartId = getOrCreateCart($mysqli, $customerId, $sessionId);
  if (!$cartId) { echo json_encode(['success' => false, 'message' => 'Failed to create cart']); exit; }

  $existing = null;
  if ($size) {
    $stmt = $mysqli->prepare('SELECT id, quantity FROM cart_items WHERE cart_id = ? AND product_id = ? AND size = ?');
    if ($stmt) { $stmt->bind_param('iis', $cartId, $productId, $size); $stmt->execute(); $existing = $stmt->get_result()->fetch_assoc(); }
  } else {
    $stmt = $mysqli->prepare('SELECT id, quantity FROM cart_items WHERE cart_id = ? AND product_id = ? AND (size IS NULL OR size = \'\')');
    if ($stmt) { $stmt->bind_param('ii', $cartId, $productId); $stmt->execute(); $existing = $stmt->get_result()->fetch_assoc(); }
  }

  if ($existing) {
    $newQty = $existing['quantity'] + $quantity;
    $stmt = $mysqli->prepare('UPDATE cart_items SET quantity = ? WHERE id = ?');
    if ($stmt) { $stmt->bind_param('ii', $newQty, $existing['id']); $stmt->execute(); }
  } else {
    $stmt = $mysqli->prepare('INSERT INTO cart_items (cart_id, product_id, quantity, size, unit_price) VALUES (?, ?, ?, ?, ?)');
    if ($stmt) { $stmt->bind_param('iiisd', $cartId, $productId, $quantity, $size, $unitPrice); $stmt->execute(); }
  }

  echo json_encode(['success' => true, 'message' => 'Added to cart', 'cart_count' => getCartCount($mysqli, $cartId)]);
  exit;
}

// ─── ACTION: UPDATE QUANTITY ───
if ($action === 'update_quantity') {
  $cartItemId = (int)($_POST['cart_item_id'] ?? 0);
  $newQty = (int)($_POST['quantity'] ?? 1);

  if ($cartItemId <= 0) { echo json_encode(['success' => false, 'message' => 'Invalid cart item']); exit; }

  $cartId = getOrCreateCart($mysqli, $customerId, $sessionId);
  if (!$cartId) { echo json_encode(['success' => false, 'message' => 'Cart not found']); exit; }

  // Verify item belongs to this cart
  $stmt = $mysqli->prepare('SELECT id, cart_id FROM cart_items WHERE id = ? AND cart_id = ?');
  if (!$stmt) { echo json_encode(['success' => false, 'message' => 'Error']); exit; }
  $stmt->bind_param('ii', $cartItemId, $cartId);
  $stmt->execute();
  $item = $stmt->get_result()->fetch_assoc();
  if (!$item) { echo json_encode(['success' => false, 'message' => 'Item not found in cart']); exit; }

  if ($newQty < 1) {
    // Remove if quantity drops below 1
    $stmt = $mysqli->prepare('DELETE FROM cart_items WHERE id = ? AND cart_id = ?');
    if ($stmt) { $stmt->bind_param('ii', $cartItemId, $cartId); $stmt->execute(); }
  } else {
    $stmt = $mysqli->prepare('UPDATE cart_items SET quantity = ? WHERE id = ? AND cart_id = ?');
    if ($stmt) { $stmt->bind_param('iii', $newQty, $cartItemId, $cartId); $stmt->execute(); }
  }

  $totals = getCartTotals($mysqli, $cartId);
  echo json_encode(['success' => true, 'message' => 'Quantity updated', 'cart_count' => getCartCount($mysqli, $cartId), 'totals' => $totals]);
  exit;
}

// ─── ACTION: REMOVE ───
if ($action === 'remove') {
  $cartItemId = (int)($_POST['cart_item_id'] ?? 0);

  if ($cartItemId <= 0) { echo json_encode(['success' => false, 'message' => 'Invalid cart item']); exit; }

  $cartId = getOrCreateCart($mysqli, $customerId, $sessionId);
  if (!$cartId) { echo json_encode(['success' => false, 'message' => 'Cart not found']); exit; }

  // Verify item belongs to this cart
  $stmt = $mysqli->prepare('SELECT id FROM cart_items WHERE id = ? AND cart_id = ?');
  if (!$stmt) { echo json_encode(['success' => false, 'message' => 'Error']); exit; }
  $stmt->bind_param('ii', $cartItemId, $cartId);
  $stmt->execute();
  if ($stmt->get_result()->num_rows === 0) { echo json_encode(['success' => false, 'message' => 'Item not found in cart']); exit; }

  $stmt = $mysqli->prepare('DELETE FROM cart_items WHERE id = ? AND cart_id = ?');
  if ($stmt) { $stmt->bind_param('ii', $cartItemId, $cartId); $stmt->execute(); }

  $totals = getCartTotals($mysqli, $cartId);
  echo json_encode(['success' => true, 'message' => 'Item removed', 'cart_count' => getCartCount($mysqli, $cartId), 'totals' => $totals]);
  exit;
}

echo json_encode(['success' => false, 'message' => 'Unknown action']);

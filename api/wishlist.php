<?php
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['customer_id'])) {
  http_response_code(401);
  echo json_encode(['error' => 'Please login to use wishlist', 'action' => 'login_required']);
  exit;
}

$customerId = $_SESSION['customer_id'];
$action = $_POST['action'] ?? $_GET['action'] ?? '';
$productId = (int)($_POST['product_id'] ?? $_GET['product_id'] ?? 0);

if ($productId <= 0) {
  http_response_code(400);
  echo json_encode(['error' => 'Invalid product']);
  exit;
}

if ($action === 'add') {
  $stmt = $mysqli->prepare('INSERT IGNORE INTO wishlists (customer_id, product_id) VALUES (?, ?)');
  $stmt->bind_param('ii', $customerId, $productId);
  $stmt->execute();
  echo json_encode(['success' => true, 'message' => 'Added to wishlist', 'count' => $mysqli->query("SELECT COUNT(*) as c FROM wishlists WHERE customer_id = $customerId")->fetch_assoc()['c']]);
} elseif ($action === 'remove') {
  $stmt = $mysqli->prepare('DELETE FROM wishlists WHERE customer_id = ? AND product_id = ?');
  $stmt->bind_param('ii', $customerId, $productId);
  $stmt->execute();
  echo json_encode(['success' => true, 'message' => 'Removed from wishlist', 'count' => $mysqli->query("SELECT COUNT(*) as c FROM wishlists WHERE customer_id = $customerId")->fetch_assoc()['c']]);
} elseif ($action === 'toggle') {
  $check = $mysqli->prepare('SELECT id FROM wishlists WHERE customer_id = ? AND product_id = ?');
  $check->bind_param('ii', $customerId, $productId);
  $check->execute();
  if ($check->get_result()->num_rows > 0) {
    $del = $mysqli->prepare('DELETE FROM wishlists WHERE customer_id = ? AND product_id = ?');
    $del->bind_param('ii', $customerId, $productId);
    $del->execute();
    echo json_encode(['success' => true, 'status' => 'removed', 'message' => 'Removed from wishlist']);
  } else {
    $ins = $mysqli->prepare('INSERT INTO wishlists (customer_id, product_id) VALUES (?, ?)');
    $ins->bind_param('ii', $customerId, $productId);
    $ins->execute();
    echo json_encode(['success' => true, 'status' => 'added', 'message' => 'Added to wishlist']);
  }
} elseif ($action === 'check') {
  $check = $mysqli->prepare('SELECT id FROM wishlists WHERE customer_id = ? AND product_id = ?');
  $check->bind_param('ii', $customerId, $productId);
  $check->execute();
  echo json_encode(['wishlisted' => $check->get_result()->num_rows > 0]);
} else {
  http_response_code(400);
  echo json_encode(['error' => 'Invalid action']);
}

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
  echo json_encode(['success' => true, 'message' => 'Added to wishlist', 'count' => @$mysqli->query("SELECT COUNT(*) as c FROM wishlists WHERE customer_id = $customerId")->fetch_assoc()['c']]);
} elseif ($action === 'remove') {
  $stmt = $mysqli->prepare('DELETE FROM wishlists WHERE customer_id = ? AND product_id = ?');
  $stmt->bind_param('ii', $customerId, $productId);
  $stmt->execute();
  echo json_encode(['success' => true, 'message' => 'Removed from wishlist', 'count' => @$mysqli->query("SELECT COUNT(*) as c FROM wishlists WHERE customer_id = $customerId")->fetch_assoc()['c']]);
} elseif ($action === 'toggle') {
  // Ensure wishlists table exists (without foreign keys to support any product_id)
  @$mysqli->query("CREATE TABLE IF NOT EXISTS wishlists (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_id INT UNSIGNED NOT NULL,
    product_id INT UNSIGNED NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_wishlist_item (customer_id, product_id)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

  $check = $mysqli->prepare('SELECT id FROM wishlists WHERE customer_id = ? AND product_id = ?');
  $check->bind_param('ii', $customerId, $productId);
  $check->execute();
  if ($check->get_result()->num_rows > 0) {
    $del = $mysqli->prepare('DELETE FROM wishlists WHERE customer_id = ? AND product_id = ?');
    $del->bind_param('ii', $customerId, $productId);
    $del->execute();
    $cnt = $mysqli->query("SELECT COUNT(*) as c FROM wishlists WHERE customer_id = $customerId")->fetch_assoc()['c'];
    echo json_encode(['success' => true, 'status' => 'removed', 'message' => 'Removed from wishlist', 'wishlist_count' => (int)$cnt]);
  } else {
    $ins = $mysqli->prepare('INSERT IGNORE INTO wishlists (customer_id, product_id) VALUES (?, ?)');
    $ins->bind_param('ii', $customerId, $productId);
    $ins->execute();
    $cnt = $mysqli->query("SELECT COUNT(*) as c FROM wishlists WHERE customer_id = $customerId")->fetch_assoc()['c'];
    echo json_encode(['success' => true, 'status' => 'added', 'message' => 'Added to wishlist', 'wishlist_count' => (int)$cnt]);
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

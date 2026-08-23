<?php
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(['error' => 'Method not allowed']);
  exit;
}

if (!isset($_SESSION['customer_id'])) {
  http_response_code(401);
  echo json_encode(['error' => 'Please login to submit a review']);
  exit;
}

$customerId = $_SESSION['customer_id'];
$productId = (int)($_POST['product_id'] ?? 0);
$rating = (int)($_POST['rating'] ?? 0);
$title = trim($_POST['title'] ?? '');
$comment = trim($_POST['comment'] ?? '');

if ($productId <= 0 || $rating < 1 || $rating > 5) {
  http_response_code(400);
  echo json_encode(['error' => 'Invalid product or rating']);
  exit;
}

$check = $mysqli->prepare("SELECT id FROM reviews WHERE product_id = ? AND customer_id = ?");
$check->bind_param('ii', $productId, $customerId);
$check->execute();
if ($check->get_result()->num_rows > 0) {
  http_response_code(409);
  echo json_encode(['error' => 'You have already reviewed this product']);
  exit;
}

$stmt = $mysqli->prepare("INSERT INTO reviews (product_id, customer_id, customer_name, rating, title, comment, is_approved) VALUES (?, ?, (SELECT CONCAT(first_name, ' ', last_name) FROM customers WHERE id = ?), ?, ?, ?, 0)");
$stmt->bind_param('iiisss', $productId, $customerId, $customerId, $rating, $title, $comment);
$stmt->execute();

echo json_encode(['success' => true, 'message' => 'Review submitted successfully. It will be visible after admin approval.']);

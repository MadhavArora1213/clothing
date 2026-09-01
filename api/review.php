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

$stmt = $mysqli->prepare("INSERT INTO reviews (product_id, customer_id, customer_name, rating, title, comment, is_approved) VALUES (?, ?, (SELECT CONCAT(first_name, ' ', last_name) FROM customers WHERE id = ?), ?, ?, ?, 1)");
$stmt->bind_param('iiisss', $productId, $customerId, $customerId, $rating, $title, $comment);
$stmt->execute();
$reviewId = $mysqli->insert_id;

// Handle image uploads
$imageUrls = [];
if (!empty($_FILES['review_images']['name'][0])) {
  $files = $_FILES['review_images'];
  $count = min(count($files['name']), 5);
  for ($i = 0; $i < $count; $i++) {
    $fileArray = [
      'name' => $files['name'][$i],
      'type' => $files['type'][$i],
      'tmp_name' => $files['tmp_name'][$i],
      'error' => $files['error'][$i],
      'size' => $files['size'][$i],
    ];
    $uploaded = handleImageUpload($fileArray, 'reviews');
    if ($uploaded) {
      $imgStmt = $mysqli->prepare("INSERT INTO review_images (review_id, image_url, sort_order) VALUES (?, ?, ?)");
      $imgStmt->bind_param('isi', $reviewId, $uploaded, $i);
      $imgStmt->execute();
      $imageUrls[] = $uploaded;
    }
  }
}

echo json_encode(['success' => true, 'message' => 'Review submitted successfully!', 'images' => $imageUrls]);

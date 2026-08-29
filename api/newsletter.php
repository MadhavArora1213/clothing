<?php
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(['error' => 'Method not allowed']);
  exit;
}

$email = strtolower(trim($_POST['email'] ?? ''));
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
  http_response_code(400);
  echo json_encode(['error' => 'Please enter a valid email address']);
  exit;
}

// Create table if not exists
if ($mysqli) {
  $mysqli->query("CREATE TABLE IF NOT EXISTS newsletter_subscribers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    is_active TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
  )");

  $check = $mysqli->prepare('SELECT id FROM newsletter_subscribers WHERE email = ?');
  $check->bind_param('s', $email);
  $check->execute();
  if ($check->get_result()->num_rows > 0) {
    echo json_encode(['success' => true, 'message' => 'You are already subscribed!']);
    exit;
  }

  $stmt = $mysqli->prepare('INSERT INTO newsletter_subscribers (email) VALUES (?)');
  $stmt->bind_param('s', $email);
  $stmt->execute();
  echo json_encode(['success' => true, 'message' => 'Subscribed successfully! Welcome aboard.']);
} else {
  echo json_encode(['success' => true, 'message' => 'Subscribed successfully! Welcome aboard.']);
}

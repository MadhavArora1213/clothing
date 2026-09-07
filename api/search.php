<?php
require_once __DIR__ . '/../config/database.php';
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');

$q = strtolower(trim($_GET['q'] ?? ''));

if (strlen($q) < 2) {
  echo json_encode(['products' => [], 'categories' => []]);
  exit;
}

$results = ['products' => [], 'categories' => []];

if ($mysqli) {
  // Search products
  $searchTerm = "%{$q}%";
  $stmt = $mysqli->prepare("
    SELECT p.id, p.name, p.slug, p.price, p.original_price, p.discount_percent,
      (SELECT image_url FROM product_images WHERE product_id = p.id ORDER BY is_primary DESC, sort_order LIMIT 1) as image,
      c.name as category_name
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    WHERE p.is_active = 1 AND (p.name LIKE ? OR p.description LIKE ? OR c.name LIKE ?)
    ORDER BY p.name ASC
    LIMIT 8
  ");
  if ($stmt) {
    $stmt->bind_param('sss', $searchTerm, $searchTerm, $searchTerm);
    $stmt->execute();
    $results['products'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
  }

  // Search categories & subcategories
  $catStmt = $mysqli->prepare("
    SELECT id, name, slug, department, parent_id
    FROM categories
    WHERE is_active = 1 AND (name LIKE ? OR slug LIKE ?)
    ORDER BY parent_id ASC, name ASC
    LIMIT 6
  ");
  if ($catStmt) {
    $catStmt->bind_param('ss', $searchTerm, $searchTerm);
    $catStmt->execute();
    $results['categories'] = $catStmt->get_result()->fetch_all(MYSQLI_ASSOC);
  }
}

echo json_encode($results);

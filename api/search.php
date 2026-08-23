<?php
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');

$products = include __DIR__ . '/../data/products.php';
$q = strtolower(trim($_GET['q'] ?? ''));

if (strlen($q) < 2) {
  echo json_encode([]);
  exit;
}

$results = array_filter($products, function($p) use ($q) {
  return strpos(strtolower($p['title']), $q) !== false ||
         strpos(strtolower($p['brand']), $q) !== false ||
         strpos(strtolower($p['category']), $q) !== false;
});

echo json_encode(array_values($results));

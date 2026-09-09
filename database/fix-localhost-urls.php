<?php
/**
 * One-time fix: Replace all localhost image URLs with correct live domain.
 * Run via: php database/fix-localhost-urls.php
 */

require_once __DIR__ . '/../config/database.php';

if (!$mysqli) {
  die("Error: Database not connected.\n");
}

$oldBase = 'http://localhost/urban_outfit/clothing';
$newBase = 'https://urbanoutfitshop.com';

$totalFixed = 0;

// 1. Fix products.image
$stmt = $mysqli->prepare("UPDATE products SET image = REPLACE(image, ?, ?) WHERE image LIKE ?");
$stmt->bind_param('sss', $oldBase, $newBase, $oldBase . '%');
$stmt->execute();
$affected = $mysqli->affected_rows;
$totalFixed += $affected;
echo "✔ products.image: {$affected} rows updated\n";

// 2. Fix product_images.image_url
$stmt = $mysqli->prepare("UPDATE product_images SET image_url = REPLACE(image_url, ?, ?) WHERE image_url LIKE ?");
$stmt->bind_param('sss', $oldBase, $newBase, $oldBase . '%');
$stmt->execute();
$affected = $mysqli->affected_rows;
$totalFixed += $affected;
echo "✔ product_images.image_url: {$affected} rows updated\n";

// 3. Fix categories.image (if column exists)
$colCheck = $mysqli->query("SHOW COLUMNS FROM categories LIKE 'image'");
if ($colCheck && $colCheck->num_rows > 0) {
  $stmt = $mysqli->prepare("UPDATE categories SET image = REPLACE(image, ?, ?) WHERE image LIKE ?");
  $stmt->bind_param('sss', $oldBase, $newBase, $oldBase . '%');
  $stmt->execute();
  $affected = $mysqli->affected_rows;
  $totalFixed += $affected;
  echo "✔ categories.image: {$affected} rows updated\n";
}

// 4. Fix any other http://localhost references in products table
$cols = ['image', 'sku', 'brand'];
foreach ($cols as $col) {
  // Skip already handled columns
  if ($col === 'image') continue;
}

echo "\n🎉 Done! Total {$totalFixed} localhost URLs fixed.\n";
echo "New base URL: {$newBase}\n";

// Verify
$remaining = $mysqli->query("SELECT COUNT(*) as cnt FROM products WHERE image LIKE '%localhost%'")->fetch_assoc()['cnt'];
echo "Remaining localhost URLs in products: {$remaining}\n";

$remaining2 = $mysqli->query("SELECT COUNT(*) as cnt FROM product_images WHERE image_url LIKE '%localhost%'")->fetch_assoc()['cnt'];
echo "Remaining localhost URLs in product_images: {$remaining2}\n";

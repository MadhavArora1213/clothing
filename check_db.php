<?php
require_once __DIR__ . '/config/database.php';

echo "=== CATEGORIES ===\n";
$r = $mysqli->query("SELECT id, name, parent_id FROM categories WHERE is_active = 1 ORDER BY parent_id, id");
while ($row = $r->fetch_assoc()) {
    echo $row['id'] . ' | ' . $row['name'] . ' | parent: ' . $row['parent_id'] . "\n";
}

echo "\n=== PRODUCTS (first 20) ===\n";
$r2 = $mysqli->query("SELECT p.id, p.name, p.category_id, c.name AS cat_name, c.parent_id FROM products p JOIN categories c ON p.category_id = c.id WHERE p.is_active = 1 ORDER BY p.created_at DESC LIMIT 20");
while ($row = $r2->fetch_assoc()) {
    echo $row['id'] . ' | ' . $row['name'] . ' | cat: ' . $row['cat_name'] . ' | parent: ' . $row['parent_id'] . "\n";
}

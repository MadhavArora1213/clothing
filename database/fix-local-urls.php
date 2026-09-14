<?php
/**
 * Fix all broken image URLs in database for localhost development.
 * - Fixes products.image = '0' by copying primary image from product_images
 * - Replaces https://urbanoutfitshop.com with http://localhost/urban_outfit/clothing
 */

$mysqli = @new mysqli('127.0.0.1', 'root', '', 'cloths');
if ($mysqli->connect_error) {
    die("DB Error: " . $mysqli->connect_error . "\n");
}
$mysqli->set_charset('utf8mb4');

$localBase = 'http://localhost/urban_outfit/clothing';
$prodBase  = 'https://urbanoutfitshop.com';

$fixMode = (isset($_GET['fix']) && $_GET['fix'] == '1') || in_array('--fix', $argv ?? []);

echo "<h2>Fix Image URLs for Localhost</h2>\n";
echo "<p>Mode: " . ($fixMode ? "<b style='color:red'>FIXING...</b>" : "Dry run — add ?fix=1 to apply") . "</p>\n";

// ── STEP 1: Fix products.image where value is '0' or empty ──
echo "<h3>Step 1: Fix products.image = '0' or empty</h3>\n";
$broken = $mysqli->query("SELECT p.id, p.name, p.image, 
    (SELECT pi.image_url FROM product_images pi WHERE pi.product_id = p.id AND pi.is_primary = 1 LIMIT 1) as primary_img
    FROM products p WHERE p.image = '0' OR p.image IS NULL OR p.image = ''");

$fixCount1 = 0;
if ($broken && $broken->num_rows > 0) {
    echo "<table border='1' cellpadding='5'>\n<tr><th>ID</th><th>Name</th><th>Current</th><th>Primary from product_images</th><th>Action</th></tr>\n";
    while ($row = $broken->fetch_assoc()) {
        $fixUrl = $row['primary_img'] ?? '';
        $status = '';
        if (!empty($fixUrl)) {
            $status = "✅ Will fix";
            if ($fixMode) {
                $stmt = $mysqli->prepare("UPDATE products SET image = ? WHERE id = ?");
                $stmt->bind_param('si', $fixUrl, $row['id']);
                $stmt->execute();
                $fixCount1++;
            }
        } else {
            $status = "⚠️ No primary image in product_images either";
        }
        echo "<tr><td>{$row['id']}</td><td>" . htmlspecialchars($row['name']) . "</td><td><code>" . htmlspecialchars($row['image']) . "</code></td><td><code>" . htmlspecialchars($fixUrl) . "</code></td><td>{$status}</td></tr>\n";
    }
    echo "</table>\n";
    echo "<p>Fixed: {$fixCount1} products</p>\n";
} else {
    echo "<p>✅ No broken '0' products found</p>\n";
}

// ── STEP 2: Replace production URLs in products.image ──
echo "<h3>Step 2: Replace production URLs in products.image</h3>\n";
$prodUrls = $mysqli->query("SELECT id, name, image FROM products WHERE image LIKE '{$prodBase}%'");
$fixCount2 = 0;
if ($prodUrls && $prodUrls->num_rows > 0) {
    echo "<table border='1' cellpadding='5'>\n<tr><th>ID</th><th>Name</th><th>Before</th><th>After</th></tr>\n";
    while ($row = $prodUrls->fetch_assoc()) {
        $newUrl = str_replace($prodBase, $localBase, $row['image']);
        echo "<tr><td>{$row['id']}</td><td>" . htmlspecialchars($row['name']) . "</td><td><code>" . htmlspecialchars($row['image']) . "</code></td><td><code>" . htmlspecialchars($newUrl) . "</code></td></tr>\n";
        if ($fixMode) {
            $stmt = $mysqli->prepare("UPDATE products SET image = ? WHERE id = ?");
            $stmt->bind_param('si', $newUrl, $row['id']);
            $stmt->execute();
            $fixCount2++;
        }
    }
    echo "</table>\n";
    echo "<p>Fixed: {$fixCount2} products</p>\n";
} else {
    echo "<p>✅ No production URLs in products.image</p>\n";
}

// ── STEP 3: Replace production URLs in product_images.image_url ──
echo "<h3>Step 3: Replace production URLs in product_images.image_url</h3>\n";
$prodImgs = $mysqli->query("SELECT id, product_id, image_url, image_label FROM product_images WHERE image_url LIKE '{$prodBase}%'");
$fixCount3 = 0;
if ($prodImgs && $prodImgs->num_rows > 0) {
    echo "<p>{$prodImgs->num_rows} product_images with production URLs found.</p>\n";
    while ($row = $prodImgs->fetch_assoc()) {
        $newUrl = str_replace($prodBase, $localBase, $row['image_url']);
        if ($fixMode) {
            $stmt = $mysqli->prepare("UPDATE product_images SET image_url = ? WHERE id = ?");
            $stmt->bind_param('si', $newUrl, $row['id']);
            $stmt->execute();
            $fixCount3++;
        }
    }
    echo "<p>Fixed: {$fixCount3} product_images</p>\n";
} else {
    echo "<p>✅ No production URLs in product_images</p>\n";
}

// ── STEP 4: Check for localhost URLs in product_images that should work ──
echo "<h3>Step 4: Verify localhost product_images URLs</h3>\n";
$localImgs = $mysqli->query("SELECT id, product_id, image_url FROM product_images WHERE image_url LIKE '{$localBase}%'");
if ($localImgs && $localImgs->num_rows > 0) {
    echo "<p>{$localImgs->num_rows} localhost URLs in product_images (these should work on localhost)</p>\n";
}

// ── Summary ──
echo "<hr>\n<h3>Summary</h3>\n";
if ($fixMode) {
    echo "<p style='color:green; font-size:18px;'>✅ DONE! Fixed {$fixCount1} broken products + {$fixCount2} production product URLs + {$fixCount3} production image URLs</p>\n";
    echo "<p><a href='?'>Run again to verify</a></p>\n";
} else {
    echo "<p>Run with <a href='?fix=1'>?fix=1</a> to apply changes</p>\n";
}

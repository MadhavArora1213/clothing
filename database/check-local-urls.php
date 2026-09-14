<?php
/**
 * Check and fix image URLs in database for localhost.
 * Run via browser: http://localhost/urban_outfit/clothing/database/check-local-urls.php
 * Or add ?fix=1 to actually fix URLs.
 */

$mysqli = @new mysqli('127.0.0.1', 'root', '', 'cloths');
if ($mysqli->connect_error) {
    die("DB Connection Error: " . $mysqli->connect_error . "\n");
}
$mysqli->set_charset('utf8mb4');

$fixMode = isset($_GET['fix']) && $_GET['fix'] == '1';

// The correct base URL for localhost
$correctBase = 'http://localhost/urban_outfit/clothing';
$wrongBase1 = 'http://localhost/urban_outfit/clothing/uploads';
$wrongBase2 = 'http://localhost/uploads';
$wrongBase3 = 'http://127.0.0.1/urban_outfit/clothing';
$wrongBase4 = 'https://urbanoutfitshop.com';
$wrongBase5 = 'http://urbanoutfitshop.com';

echo "<h2>Image URL Diagnostic & Fix Tool</h2>";
echo "<p>Mode: " . ($fixMode ? "<b style='color:red'>FIX MODE</b>" : "Check only (add ?fix=1 to fix)") . "</p>";

// 1. Check products.image
echo "<h3>1. Products Table - image column</h3>";
$result = $mysqli->query("SELECT id, name, image FROM products ORDER BY id");
if ($result && $result->num_rows > 0) {
    echo "<table border='1' cellpadding='5' cellspacing='0'>";
    echo "<tr><th>ID</th><th>Name</th><th>Image URL</th><th>Status</th></tr>";
    while ($row = $result->fetch_assoc()) {
        $url = $row['image'];
        $status = "✅ OK";
        $fixedUrl = $url;

        if (empty($url)) {
            $status = "❌ Empty";
        } elseif (strpos($url, 'http://localhost/urban_outfit/clothing/uploads/products/') !== false) {
            // URL already has full path including uploads - this is WRONG
            // It should be http://localhost/urban_outfit/clothing/uploads/products/file.jpg
            // but it might be http://localhost/urban_outfit/clothing/http://localhost/urban_outfit/clothing/uploads/products/file.jpg
            $status = "⚠️ Double base detected";
        } elseif (strpos($url, 'http://localhost/urban_outfit/clothing/uploads/products/') === false && strpos($url, 'localhost') !== false) {
            $status = "⚠️ Needs fix";
        }

        // Check for common wrong patterns
        if (preg_match('#http://localhost/urban_outfit/clothing/http://#', $url)) {
            $status = "❌ Double URL";
            $fixedUrl = preg_replace('#http://localhost/urban_outfit/clothing/http://#', 'http://#', $url);
        } elseif (strpos($url, 'http://localhost/uploads/') !== false) {
            $status = "❌ Missing /urban_outfit/clothing prefix";
            $fixedUrl = str_replace('http://localhost/uploads/', 'http://localhost/urban_outfit/clothing/uploads/', $url);
        } elseif (strpos($url, $wrongBase4) !== false || strpos($url, $wrongBase5) !== false) {
            $status = "❌ Points to production domain";
            $fixedUrl = str_replace([$wrongBase4, $wrongBase5], $correctBase, $url);
        }

        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
        echo "<td><code>" . htmlspecialchars($url) . "</code>";
        if ($fixedUrl !== $url) {
            echo "<br><b>Should be:</b> <code style='color:green'>" . htmlspecialchars($fixedUrl) . "</code>";
        }
        echo "</td>";
        echo "<td>{$status}</td>";
        echo "</tr>";

        if ($fixMode && $fixedUrl !== $url) {
            $stmt = $mysqli->prepare("UPDATE products SET image = ? WHERE id = ?");
            $stmt->bind_param('si', $fixedUrl, $row['id']);
            $stmt->execute();
            echo "<!-- Fixed product {$row['id']} -->";
        }
    }
    echo "</table>";
} else {
    echo "<p>No products found.</p>";
}

// 2. Check product_images.image_url
echo "<h3>2. Product Images Table - image_url column</h3>";
$result2 = $mysqli->query("SELECT id, product_id, image_url, is_primary, image_label FROM product_images ORDER BY product_id, is_primary DESC, sort_order ASC");
if ($result2 && $result2->num_rows > 0) {
    echo "<table border='1' cellpadding='5' cellspacing='0'>";
    echo "<tr><th>ID</th><th>ProductID</th><th>Label</th><th>Primary</th><th>Image URL</th><th>Status</th></tr>";
    while ($row = $result2->fetch_assoc()) {
        $url = $row['image_url'];
        $status = "✅ OK";
        $fixedUrl = $url;

        if (empty($url)) {
            $status = "❌ Empty";
        } elseif (preg_match('#http://localhost/urban_outfit/clothing/http://#', $url)) {
            $status = "❌ Double URL";
            $fixedUrl = preg_replace('#http://localhost/urban_outfit/clothing/http://#', 'http://#', $url);
        } elseif (strpos($url, 'http://localhost/uploads/') !== false) {
            $status = "❌ Missing prefix";
            $fixedUrl = str_replace('http://localhost/uploads/', 'http://localhost/urban_outfit/clothing/uploads/', $url);
        } elseif ((strpos($url, 'https://urbanoutfitshop.com') !== false || strpos($url, 'http://urbanoutfitshop.com') !== false) && $fixMode === false) {
            // Only flag if running locally - these are correct for production
            $status = "✅ Production URL (correct for live)";
        } elseif (strpos($url, 'http://localhost') !== false && strpos($url, 'uploads/products/') === false) {
            $status = "⚠️ Unusual URL pattern";
        }

        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['product_id']}</td>";
        echo "<td>" . htmlspecialchars($row['image_label'] ?? '') . "</td>";
        echo "<td>" . ($row['is_primary'] ? 'Yes' : 'No') . "</td>";
        echo "<td><code>" . htmlspecialchars($url) . "</code>";
        if ($fixedUrl !== $url) {
            echo "<br><b>Should be:</b> <code style='color:green'>" . htmlspecialchars($fixedUrl) . "</code>";
        }
        echo "</td>";
        echo "<td>{$status}</td>";
        echo "</tr>";

        if ($fixMode && $fixedUrl !== $url) {
            $stmt = $mysqli->prepare("UPDATE product_images SET image_url = ? WHERE id = ?");
            $stmt->bind_param('si', $fixedUrl, $row['id']);
            $stmt->execute();
        }
    }
    echo "</table>";
} else {
    echo "<p>No product images found.</p>";
}

// 3. Check if the actual file exists on disk
echo "<h3>3. File Existence Check</h3>";
$uploadsDir = dirname(__DIR__) . '/uploads/products/';
if (is_dir($uploadsDir)) {
    $files = array_diff(scandir($uploadsDir), ['.', '..']);
    echo "<p>Files in uploads/products/: " . count($files) . "</p>";
    echo "<ul>";
    foreach (array_slice($files, 0, 20) as $f) {
        echo "<li><code>{$f}</code> (" . round(filesize($uploadsDir . $f) / 1024, 1) . " KB)</li>";
    }
    if (count($files) > 20) {
        echo "<li>... and " . (count($files) - 20) . " more</li>";
    }
    echo "</ul>";

    // Check the specific file
    $targetFile = $uploadsDir . 'img_6aa56f484d5341.01480624_1789226824.jpg';
    echo "<p><b>Specific file img_6aa56f484d5341.01480624_1789226824.jpg:</b> " . (file_exists($targetFile) ? "✅ EXISTS" : "❌ NOT FOUND") . "</p>";
} else {
    echo "<p>❌ uploads/products/ directory does NOT exist!</p>";
}

// 4. Summary of URL patterns found
echo "<h3>4. URL Pattern Summary</h3>";
$patterns = $mysqli->query("
    SELECT 
        CASE 
            WHEN image LIKE 'http://localhost/urban_outfit/clothing/uploads/products/%' THEN 'correct localhost'
            WHEN image LIKE 'http://localhost/uploads/%' THEN 'missing /urban_outfit/clothing prefix'
            WHEN image LIKE 'https://urbanoutfitshop.com/%' THEN 'production URL'
            WHEN image LIKE 'http://urbanoutfitshop.com/%' THEN 'production URL (http)'
            WHEN image LIKE 'https://images.unsplash.com/%' THEN 'Unsplash'
            WHEN image LIKE '' OR image IS NULL THEN 'empty'
            ELSE 'other: ' LEFT(image, 60)
        END as pattern_type,
        COUNT(*) as cnt
    FROM products
    GROUP BY pattern_type
");
if ($patterns) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>URL Pattern</th><th>Count</th></tr>";
    while ($p = $patterns->fetch_assoc()) {
        echo "<tr><td>" . htmlspecialchars($p['pattern_type']) . "</td><td>{$p['cnt']}</td></tr>";
    }
    echo "</table>";
}

if ($fixMode) {
    echo "<hr><p><b>✅ Fix complete!</b> <a href='?'>Run again to verify</a></p>";
}

<?php
require_once dirname(__DIR__) . '/config/database.php';
requireAdminAuth();

$product = null;
$editMode = false;

if (isset($_GET['id'])) {
  $stmt = $mysqli->prepare('SELECT * FROM products WHERE id = ?');
  $stmt->bind_param('i', $_GET['id']);
  $stmt->execute();
  $product = $stmt->get_result()->fetch_assoc();
  if ($product) {
    $editMode = true;
    $pageTitle = 'Edit Product — ATELIER Admin';
  }
}

if (!$editMode) {
  $pageTitle = 'Add Product — ATELIER Admin';
}

$categories = $mysqli->query('SELECT * FROM categories WHERE is_active = 1 ORDER BY sort_order, name')->fetch_all(MYSQLI_ASSOC);

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = sanitize($_POST['name'] ?? '');
  $slug = sanitize($_POST['slug'] ?? '');
  $sku = sanitize($_POST['sku'] ?? '');
  $brand = sanitize($_POST['brand'] ?? 'ATELIER');
  $description = sanitize($_POST['description'] ?? '');
  $features = $_POST['features'] ?? [];
  $material = sanitize($_POST['material'] ?? '');
  $care_instructions = sanitize($_POST['care_instructions'] ?? '');
  $price = (float)($_POST['price'] ?? 0);
  $original_price = !empty($_POST['original_price']) ? (float)$_POST['original_price'] : null;
  $discount_percent = (int)($_POST['discount_percent'] ?? 0);
  $category_id = (int)($_POST['category_id'] ?? 0);
  $subcategory_id = !empty($_POST['subcategory_id']) ? (int)$_POST['subcategory_id'] : null;
  $is_featured = isset($_POST['is_featured']) ? 1 : 0;
  $is_active = isset($_POST['is_active']) ? 1 : 0;
  $colors = $_POST['colors'] ?? [];
  $sizes = $_POST['sizes'] ?? [];
  $images = $_POST['images'] ?? [];

  if (empty($name) || empty($slug) || $price <= 0 || $category_id <= 0) {
    $error = 'Please fill in all required fields.';
  } else {
    $image = !empty($images) ? $images[0] : '';
    $imageJson = json_encode(array_values($images));

    if ($editMode) {
      $stmt = $mysqli->prepare('UPDATE products SET name=?, slug=?, sku=?, brand=?, description=?, features=?, material=?, care_instructions=?, price=?, original_price=?, discount_percent=?, category_id=?, subcategory_id=?, is_featured=?, is_active=?, image=? WHERE id=?');
      $featuresJson = json_encode(array_values($features));
      $stmt->bind_param('ssssssssddiiiisi', $name, $slug, $sku, $brand, $description, $featuresJson, $material, $care_instructions, $price, $original_price, $discount_percent, $category_id, $subcategory_id, $is_featured, $is_active, $image, $_GET['id']);
      $stmt->execute();
      $productId = (int)$_GET['id'];
    } else {
      $stmt = $mysqli->prepare('INSERT INTO products (name, slug, sku, brand, description, features, material, care_instructions, price, original_price, discount_percent, category_id, subcategory_id, is_featured, is_active, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
      $featuresJson = json_encode(array_values($features));
      $stmt->bind_param('ssssssssddiiiisi', $name, $slug, $sku, $brand, $description, $featuresJson, $material, $care_instructions, $price, $original_price, $discount_percent, $category_id, $subcategory_id, $is_featured, $is_active, $image);
      $stmt->execute();
      $productId = $mysqli->insert_id;
    }

    // Update images
    $mysqli->query("DELETE FROM product_images WHERE product_id = $productId");
    foreach ($images as $index => $img) {
      if (!empty($img)) {
        $mysqli->query("INSERT INTO product_images (product_id, image_url, sort_order, is_primary) VALUES ($productId, '" . $mysqli->real_escape_string($img) . "', $index, " . ($index === 0 ? '1' : '0') . ")");
      }
    }

    // Update colors
    $mysqli->query("DELETE FROM product_colors WHERE product_id = $productId");
    foreach ($colors as $color) {
      if (!empty($color['name']) && !empty($color['code'])) {
        $mysqli->query("INSERT INTO product_colors (product_id, color_code, color_name, sort_order) VALUES ($productId, '" . $mysqli->real_escape_string($color['code']) . "', '" . $mysqli->real_escape_string($color['name']) . "', " . (int)$color['sort'] . ")");
      }
    }

    // Update sizes
    $mysqli->query("DELETE FROM product_sizes WHERE product_id = $productId");
    foreach ($sizes as $size) {
      if (!empty($size['name'])) {
        $sizeName = $mysqli->real_escape_string($size['name']);
        $sizeStock = (int)($size['stock'] ?? 0);
        $sizeSku = $mysqli->real_escape_string($size['sku'] ?? '');
        $mysqli->query("INSERT INTO product_sizes (product_id, size, stock, sku) VALUES ($productId, '$sizeName', $sizeStock, '$sizeSku')");
      }
    }

    $success = $editMode ? 'Product updated successfully.' : 'Product added successfully.';
    if ($editMode) {
      $product = null;
      $editMode = false;
    }
  }
}

include dirname(__DIR__) . '/includes/header.php';
?>

<div class="admin-content">
  <div class="page-header">
    <h1><?= $editMode ? 'Edit Product' : 'Add New Product' ?></h1>
    <a href="/admin/products/" class="btn btn-secondary">&larr; Back to Products</a>
  </div>

  <?php if ($error): ?>
    <div class="alert alert-error" style="margin-bottom: var(--space-6);"><?= sanitize($error) ?></div>
  <?php endif; ?>
  <?php if ($success): ?>
    <div class="alert alert-success" style="margin-bottom: var(--space-6); background: #DCFCE7; color: #166534; border: 1px solid #BBF7D0; padding: var(--space-4); border-radius: var(--radius-md);"><?= sanitize($success) ?></div>
  <?php endif; ?>

  <form method="POST" action="">
    <div class="admin-form-page">
      <h2 style="font-family: var(--font-display); font-size: var(--text-h3); margin-bottom: var(--space-6);">Basic Information</h2>
      <div class="form-grid">
        <div class="form-group">
          <label>Product Name <span class="required">*</span></label>
          <input type="text" name="name" required value="<?= $product ? sanitize($product['name']) : '' ?>">
        </div>
        <div class="form-group">
          <label>URL Slug <span class="required">*</span></label>
          <input type="text" name="slug" required value="<?= $product ? sanitize($product['slug']) : '' ?>">
        </div>
        <div class="form-group">
          <label>SKU</label>
          <input type="text" name="sku" value="<?= $product ? sanitize($product['sku'] ?? '') : '' ?>">
        </div>
        <div class="form-group">
          <label>Brand</label>
          <input type="text" name="brand" value="<?= $product ? sanitize($product['brand'] ?? 'ATELIER') : 'ATELIER' ?>">
        </div>
        <div class="form-group full-width">
          <label>Description</label>
          <textarea name="description" rows="4"><?= $product ? sanitize($product['description'] ?? '') : '' ?></textarea>
        </div>
      </div>

      <h2 style="font-family: var(--font-display); font-size: var(--text-h3); margin: var(--space-8) 0 var(--space-6);">Features</h2>
      <div id="featuresList">
        <?php
          $features = $product ? json_decode($product['features'] ?? '[]', true) : [''];
          foreach ($features as $feature):
        ?>
          <div class="form-group">
            <input type="text" name="features[]" value="<?= sanitize($feature) ?>" placeholder="Enter feature">
          </div>
        <?php endforeach; ?>
      </div>
      <button type="button" class="btn btn-secondary" onclick="addFeature()" style="margin-bottom: var(--space-6);">+ Add Feature</button>

      <h2 style="font-family: var(--font-display); font-size: var(--text-h3); margin: var(--space-8) 0 var(--space-6);">Material & Care</h2>
      <div class="form-grid">
        <div class="form-group">
          <label>Material</label>
          <input type="text" name="material" value="<?= $product ? sanitize($product['material'] ?? '') : '' ?>">
        </div>
        <div class="form-group">
          <label>Care Instructions</label>
          <input type="text" name="care_instructions" value="<?= $product ? sanitize($product['care_instructions'] ?? '') : '' ?>">
        </div>
      </div>

      <h2 style="font-family: var(--font-display); font-size: var(--text-h3); margin: var(--space-8) 0 var(--space-6);">Pricing & Category</h2>
      <div class="form-grid">
        <div class="form-group">
          <label>Price (₹) <span class="required">*</span></label>
          <input type="number" step="0.01" name="price" required value="<?= $product ? $product['price'] : '' ?>">
        </div>
        <div class="form-group">
          <label>Original Price (₹)</label>
          <input type="number" step="0.01" name="original_price" value="<?= $product ? ($product['original_price'] ?? '') : '' ?>">
        </div>
        <div class="form-group">
          <label>Discount (%)</label>
          <input type="number" name="discount_percent" value="<?= $product ? $product['discount_percent'] : '0' ?>">
        </div>
        <div class="form-group">
          <label>Category <span class="required">*</span></label>
          <select name="category_id" required>
            <option value="">Select category</option>
            <?php foreach ($categories as $cat): ?>
              <option value="<?= $cat['id'] ?>" <?= $product && $product['category_id'] == $cat['id'] ? 'selected' : '' ?>><?= sanitize($cat['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <h2 style="font-family: var(--font-display); font-size: var(--text-h3); margin: var(--space-8) 0 var(--space-6);">Images</h2>
      <div class="form-group">
        <label>Product Images (one URL per line)</label>
        <textarea name="images_input" rows="3" placeholder="https://example.com/image1.jpg&#10;https://example.com/image2.jpg"><?php
          if ($product) {
            $imgs = $mysqli->query("SELECT image_url FROM product_images WHERE product_id = {$product['id']} ORDER BY sort_order")->fetch_all(MYSQLI_ASSOC);
            echo implode("\n", array_column($imgs, 'image_url'));
          }
        ?></textarea>
      </div>

      <h2 style="font-family: var(--font-display); font-size: var(--text-h3); margin: var(--space-8) 0 var(--space-6);">Colors</h2>
      <div id="colorsList">
        <?php
          $colors = $product ? $mysqli->query("SELECT * FROM product_colors WHERE product_id = {$product['id']} ORDER BY sort_order")->fetch_all(MYSQLI_ASSOC) : [['name' => '', 'code' => '#000000', 'sort' => 0]];
          foreach ($colors as $color):
        ?>
          <div class="form-grid" style="margin-bottom: var(--space-3);">
            <div class="form-group">
              <label>Color Name</label>
              <input type="text" name="colors[][name]" value="<?= sanitize($color['name']) ?>">
            </div>
            <div class="form-group">
              <label>Color Code</label>
              <input type="color" name="colors[][code]" value="<?= sanitize($color['code']) ?>" style="height: 48px; padding: 4px;">
            </div>
          </div>
        <?php endforeach; ?>
      </div>
      <button type="button" class="btn btn-secondary" onclick="addColor()" style="margin-bottom: var(--space-6);">+ Add Color</button>

      <h2 style="font-family: var(--font-display); font-size: var(--text-h3); margin: var(--space-8) 0 var(--space-6);">Sizes & Stock</h2>
      <div id="sizesList">
        <?php
          $sizes = $product ? $mysqli->query("SELECT * FROM product_sizes WHERE product_id = {$product['id']} ORDER BY id")->fetch_all(MYSQLI_ASSOC) : [['name' => 'M', 'stock' => 10, 'sku' => '']];
          foreach ($sizes as $size):
        ?>
          <div class="form-grid" style="margin-bottom: var(--space-3);">
            <div class="form-group">
              <label>Size</label>
              <input type="text" name="sizes[][name]" value="<?= sanitize($size['size']) ?>">
            </div>
            <div class="form-group">
              <label>Stock</label>
              <input type="number" name="sizes[][stock]" value="<?= (int)$size['stock'] ?>">
            </div>
          </div>
        <?php endforeach; ?>
      </div>
      <button type="button" class="btn btn-secondary" onclick="addSize()" style="margin-bottom: var(--space-6);">+ Add Size</button>

      <h2 style="font-family: var(--font-display); font-size: var(--text-h3); margin: var(--space-8) 0 var(--space-6);">Status</h2>
      <div class="form-grid">
        <div class="form-group">
          <label><input type="checkbox" name="is_featured" value="1" <?= $product && $product['is_featured'] ? 'checked' : '' ?>> Featured Product</label>
        </div>
        <div class="form-group">
          <label><input type="checkbox" name="is_active" value="1" <?= !$product || $product['is_active'] ? 'checked' : '' ?>> Active</label>
        </div>
      </div>

      <div class="form-actions">
        <a href="/admin/products/" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary"><?= $editMode ? 'Update Product' : 'Add Product' ?></button>
      </div>
    </div>
  </form>
</div>

<script>
function addFeature() {
  const list = document.getElementById('featuresList');
  const div = document.createElement('div');
  div.className = 'form-group';
  div.innerHTML = '<input type="text" name="features[]" value="" placeholder="Enter feature">';
  list.appendChild(div);
}
function addColor() {
  const list = document.getElementById('colorsList');
  const div = document.createElement('div');
  div.className = 'form-grid';
  div.style.marginBottom = 'var(--space-3)';
  div.innerHTML = '<div class="form-group"><label>Color Name</label><input type="text" name="colors[][name]" value=""></div><div class="form-group"><label>Color Code</label><input type="color" name="colors[][code]" value="#000000" style="height: 48px; padding: 4px;"></div>';
  list.appendChild(div);
}
function addSize() {
  const list = document.getElementById('sizesList');
  const div = document.createElement('div');
  div.className = 'form-grid';
  div.style.marginBottom = 'var(--space-3)';
  div.innerHTML = '<div class="form-group"><label>Size</label><input type="text" name="sizes[][name]" value=""></div><div class="form-group"><label>Stock</label><input type="number" name="sizes[][stock]" value="0"></div>';
  list.appendChild(div);
}
document.getElementById('searchInput')?.addEventListener('input', function() {
  window.location.href = '?search=' + encodeURIComponent(this.value);
});
document.getElementById('categoryFilter')?.addEventListener('change', function() {
  window.location.href = '?category=' + this.value;
});
</script>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>

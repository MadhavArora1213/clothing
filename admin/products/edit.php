<?php
require_once dirname(__DIR__, 2) . '/config/database.php';
requireAdminAuth();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
  redirect(adminUrl('products/'));
}

// Fetch Product
$stmt = $mysqli->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
  redirect(adminUrl('products/'));
}

// Fetch Existing Images
$imgsStmt = $mysqli->prepare("SELECT * FROM product_images WHERE product_id = ? ORDER BY is_primary DESC, sort_order ASC, id ASC");
$imgsStmt->bind_param('i', $id);
$imgsStmt->execute();
$existingImages = $imgsStmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Fetch Existing Colors
$colorsStmt = $mysqli->prepare("SELECT * FROM product_colors WHERE product_id = ? ORDER BY sort_order, id");
$colorsStmt->bind_param('i', $id);
$colorsStmt->execute();
$existingColors = $colorsStmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Fetch Existing Sizes
$sizesStmt = $mysqli->prepare("SELECT * FROM product_sizes WHERE product_id = ? ORDER BY id");
$sizesStmt->bind_param('i', $id);
$sizesStmt->execute();
$existingSizes = $sizesStmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Fetch All Categories
$categoriesQuery = $mysqli->query("SELECT * FROM categories WHERE is_active = 1 ORDER BY parent_id, sort_order, name");
$allCategories = $categoriesQuery ? $categoriesQuery->fetch_all(MYSQLI_ASSOC) : [];
$parentCategories = array_filter($allCategories, fn($c) => $c['parent_id'] == 0);
$subCategories = array_filter($allCategories, fn($c) => $c['parent_id'] > 0);

$error = '';
$success = '';

// Handle Delete Single Image Action via GET
if (isset($_GET['delete_image_id'])) {
  $delImgId = (int)$_GET['delete_image_id'];
  $delStmt = $mysqli->prepare("DELETE FROM product_images WHERE id = ? AND product_id = ?");
  $delStmt->bind_param('ii', $delImgId, $id);
  $delStmt->execute();
  redirect(adminUrl('products/edit.php?id=' . $id . '&msg=Image+deleted'));
}

// Handle Set Primary Image via GET
if (isset($_GET['set_primary_image_id'])) {
  $primaryImgId = (int)$_GET['set_primary_image_id'];
  $pStmt = $mysqli->prepare("SELECT image_url FROM product_images WHERE id = ? AND product_id = ?");
  $pStmt->bind_param('ii', $primaryImgId, $id);
  $pStmt->execute();
  $pImg = $pStmt->get_result()->fetch_assoc();
  if ($pImg) {
    $mysqli->query("UPDATE product_images SET is_primary = 0 WHERE product_id = $id");
    $mysqli->query("UPDATE product_images SET is_primary = 1 WHERE id = $primaryImgId");
    $upProd = $mysqli->prepare("UPDATE products SET image = ? WHERE id = ?");
    $upProd->bind_param('si', $pImg['image_url'], $id);
    $upProd->execute();
  }
  redirect(adminUrl('products/edit.php?id=' . $id . '&msg=Primary+image+updated'));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = sanitize($_POST['name'] ?? '');
  $slug = sanitize($_POST['slug'] ?? '');
  $sku = sanitize($_POST['sku'] ?? '');
  $brand = sanitize($_POST['brand'] ?? 'AURA & CO.');
  $gender = sanitize($_POST['gender'] ?? 'women');
  $category_id = (int)($_POST['category_id'] ?? 0);
  $subcategory_id = !empty($_POST['subcategory_id']) ? (int)$_POST['subcategory_id'] : null;
  $description = $_POST['description'] ?? '';
  $material = sanitize($_POST['material'] ?? '');
  $care_instructions = sanitize($_POST['care_instructions'] ?? '');
  $price = (float)($_POST['price'] ?? 0);
  $original_price = !empty($_POST['original_price']) ? (float)$_POST['original_price'] : null;
  $discount_percent = (int)($_POST['discount_percent'] ?? 0);
  $is_featured = isset($_POST['is_featured']) ? 1 : 0;
  $is_active = isset($_POST['is_active']) ? 1 : 0;

  $features = array_filter(array_map('trim', $_POST['features'] ?? []));
  $colors = $_POST['colors'] ?? [];
  $sizes = $_POST['sizes'] ?? [];
  $subimage_urls = $_POST['subimage_urls'] ?? [];
  $subimage_labels = $_POST['subimage_labels'] ?? [];

  if (empty($name)) {
    $error = 'Product Name is required.';
  } elseif ($price <= 0) {
    $error = 'Please enter a valid selling price.';
  } elseif ($category_id <= 0) {
    $error = 'Please select a main category.';
  } else {
    if (empty($slug)) {
      $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));
    }

    $slugCheck = $mysqli->prepare("SELECT id FROM products WHERE slug = ? AND id != ?");
    $slugCheck->bind_param('si', $slug, $id);
    $slugCheck->execute();
    if ($slugCheck->get_result()->num_rows > 0) {
      $slug = $slug . '-' . time();
    }

    $mainImageUrl = $product['image'];
    if (isset($_FILES['main_image_file']) && $_FILES['main_image_file']['error'] === UPLOAD_ERR_OK) {
      $uploaded = handleImageUpload($_FILES['main_image_file'], 'products');
      if ($uploaded) {
        $mainImageUrl = $uploaded;
        $mysqli->query("UPDATE product_images SET is_primary = 0 WHERE product_id = $id");
        $insP = $mysqli->prepare("INSERT INTO product_images (product_id, image_url, image_label, sort_order, is_primary) VALUES (?, ?, 'Main Front', 0, 1)");
        $insP->bind_param('is', $id, $mainImageUrl);
        $insP->execute();
      }
    } elseif (!empty($_POST['main_image_url']) && $_POST['main_image_url'] !== $product['image']) {
      $mainImageUrl = trim($_POST['main_image_url']);
      $mysqli->query("UPDATE product_images SET is_primary = 0 WHERE product_id = $id");
      $insP = $mysqli->prepare("INSERT INTO product_images (product_id, image_url, image_label, sort_order, is_primary) VALUES (?, ?, 'Main Front', 0, 1)");
      $insP->bind_param('is', $id, $mainImageUrl);
      $insP->execute();
    }

    $featuresJson = json_encode(array_values($features));

    $upStmt = $mysqli->prepare("UPDATE products SET 
      name=?, slug=?, sku=?, brand=?, gender=?, description=?, features=?, material=?, care_instructions=?, 
      price=?, original_price=?, discount_percent=?, category_id=?, subcategory_id=?, image=?, is_featured=?, is_active=? 
      WHERE id=?");

    $upStmt->bind_param('sssssssssddiiisiii', 
      $name, $slug, $sku, $brand, $gender, $description, $featuresJson, $material, $care_instructions, 
      $price, $original_price, $discount_percent, $category_id, $subcategory_id, $mainImageUrl, $is_featured, $is_active, $id);

    if ($upStmt->execute()) {
      if (isset($_FILES['subimage_files']['name']) && is_array($_FILES['subimage_files']['name'])) {
        $fileCount = count($_FILES['subimage_files']['name']);
        for ($i = 0; $i < $fileCount; $i++) {
          if ($_FILES['subimage_files']['error'][$i] === UPLOAD_ERR_OK) {
            $singleFile = [
              'name' => $_FILES['subimage_files']['name'][$i],
              'type' => $_FILES['subimage_files']['type'][$i],
              'tmp_name' => $_FILES['subimage_files']['tmp_name'][$i],
              'error' => $_FILES['subimage_files']['error'][$i],
              'size' => $_FILES['subimage_files']['size'][$i],
            ];
            $uploadedSub = handleImageUpload($singleFile, 'products');
            if ($uploadedSub) {
              $lbl = !empty($_POST['subimage_file_labels'][$i]) ? sanitize($_POST['subimage_file_labels'][$i]) : 'Sub Image';
              $subStmt = $mysqli->prepare("INSERT INTO product_images (product_id, image_url, image_label, sort_order, is_primary) VALUES (?, ?, ?, 5, 0)");
              $subStmt->bind_param('iss', $id, $uploadedSub, $lbl);
              $subStmt->execute();
            }
          }
        }
      }

      if (!empty($subimage_urls)) {
        foreach ($subimage_urls as $idx => $sUrl) {
          $sUrl = trim($sUrl);
          if (!empty($sUrl)) {
            $sLbl = !empty($subimage_labels[$idx]) ? sanitize($subimage_labels[$idx]) : 'Gallery View';
            $subStmt = $mysqli->prepare("INSERT INTO product_images (product_id, image_url, image_label, sort_order, is_primary) VALUES (?, ?, ?, ?, 0)");
            $sortOrder = $idx + 10;
            $subStmt->bind_param('issi', $id, $sUrl, $sLbl, $sortOrder);
            $subStmt->execute();
          }
        }
      }

      $mysqli->query("DELETE FROM product_colors WHERE product_id = $id");
      if (!empty($colors)) {
        $clrStmt = $mysqli->prepare("INSERT INTO product_colors (product_id, color_code, color_name, sort_order) VALUES (?, ?, ?, ?)");
        foreach ($colors as $idx => $clr) {
          $clrCode = trim($clr['code'] ?? '');
          $clrName = trim($clr['name'] ?? '');
          if (!empty($clrCode) && !empty($clrName)) {
            $clrStmt->bind_param('issi', $id, $clrCode, $clrName, $idx);
            $clrStmt->execute();
          }
        }
      }

      $mysqli->query("DELETE FROM product_sizes WHERE product_id = $id");
      if (!empty($sizes)) {
        $szStmt = $mysqli->prepare("INSERT INTO product_sizes (product_id, size, stock, sku) VALUES (?, ?, ?, ?)");
        foreach ($sizes as $sz) {
          $szName = trim($sz['name'] ?? '');
          $szStock = (int)($sz['stock'] ?? 0);
          $szSku = trim($sz['sku'] ?? '');
          if (!empty($szName)) {
            $szStmt->bind_param('isis', $id, $szName, $szStock, $szSku);
            $szStmt->execute();
          }
        }
      }

      $success = 'Product updated successfully!';
      
      $stmt->execute();
      $product = $stmt->get_result()->fetch_assoc();
      $imgsStmt->execute();
      $existingImages = $imgsStmt->get_result()->fetch_all(MYSQLI_ASSOC);
      $colorsStmt->execute();
      $existingColors = $colorsStmt->get_result()->fetch_all(MYSQLI_ASSOC);
      $sizesStmt->execute();
      $existingSizes = $sizesStmt->get_result()->fetch_all(MYSQLI_ASSOC);
    } else {
      $error = 'Failed to update product: ' . $mysqli->error;
    }
  }
}

$pageTitle = 'Edit Product: ' . $product['name'] . ' — AURA & CO. Admin';
include dirname(__DIR__) . '/includes/header.php';
?>

<div class="admin-content">
  <div class="page-header" style="margin-bottom: var(--space-6);">
    <div class="page-header-row" style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
      <div>
        <h1 style="margin: 0; font-size: 26px;">Edit Product: <?= sanitize($product['name']) ?></h1>
        <p style="margin: 4px 0 0 0; color: #64748b;">Manage apparel details, photo gallery, square color swatches, and size inventory.</p>
      </div>
      <div class="page-header-actions" style="display: flex; gap: 8px;">
        <a href="<?= adminUrl('products/') ?>" class="btn btn-secondary">&larr; Back to Products</a>
        <a href="<?= siteUrl('product.php?slug=' . $product['slug']) ?>" target="_blank" class="btn btn-secondary">View in Store &rarr;</a>
        <button type="submit" form="editProductForm" class="btn btn-primary" style="padding: 10px 22px;">Update Product</button>
      </div>
    </div>
  </div>

  <?php if ($error): ?>
    <div class="alert alert-error" style="margin-bottom: var(--space-6); background: #FEF2F2; color: #991B1B; border: 1px solid #F87171; padding: 12px 16px; border-radius: 8px; font-weight: 500;">
      <?= sanitize($error) ?>
    </div>
  <?php endif; ?>

  <?php if ($success || !empty($_GET['msg'])): ?>
    <div class="alert alert-success" style="margin-bottom: var(--space-6); background: #DCFCE7; color: #166534; border: 1px solid #BBF7D0; padding: 12px 16px; border-radius: 8px; font-weight: 500;">
      <?= sanitize($success ?: $_GET['msg']) ?>
    </div>
  <?php endif; ?>

  <form method="POST" action="" enctype="multipart/form-data" id="editProductForm">
    <div class="admin-form-two-col">
      
      <!-- ================= LEFT SIDE ================= -->
      <div class="form-col-main">
        
        <!-- 1. Basic Information Card -->
        <div class="admin-card-section">
          <div class="admin-card-section-header">
            <h3>
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
              1. Basic Product Details
            </h3>
          </div>
          
          <div style="display: flex; flex-direction: column; gap: 14px;">
            <div class="form-group" style="margin: 0;">
              <label>Product Title / Name <span class="required" style="color: #ef4444;">*</span></label>
              <input type="text" name="name" id="productName" required value="<?= sanitize($product['name']) ?>">
            </div>

            <div class="form-inline-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
              <div class="form-group" style="margin: 0;">
                <label>Brand</label>
                <input type="text" name="brand" value="<?= sanitize($product['brand'] ?? 'AURA & CO.') ?>">
              </div>
              <div class="form-group" style="margin: 0;">
                <label>SKU (Product Code)</label>
                <input type="text" name="sku" value="<?= sanitize($product['sku'] ?? '') ?>">
              </div>
            </div>

            <div class="form-group" style="margin: 0;">
              <label>URL Slug <span class="required" style="color: #ef4444;">*</span></label>
              <input type="text" name="slug" id="productSlug" required value="<?= sanitize($product['slug']) ?>">
            </div>

            <div class="form-group" style="margin: 0;">
              <label>Product Description</label>
              <textarea name="description" rows="4"><?= sanitize($product['description'] ?? '') ?></textarea>
            </div>

            <div class="form-group" style="margin: 0;">
              <label>Key Features / Highlights</label>
              <div id="featuresList">
                <?php
                  $features = json_decode($product['features'] ?? '[]', true) ?: [];
                  if (empty($features)) $features = [''];
                  foreach ($features as $f):
                ?>
                  <div style="display: flex; gap: 8px; margin-bottom: 8px;">
                    <input type="text" name="features[]" value="<?= sanitize($f) ?>" placeholder="e.g. Premium embroidery finish" style="flex: 1;">
                    <button type="button" class="btn btn-secondary btn-sm" onclick="this.parentElement.remove()">✕</button>
                  </div>
                <?php endforeach; ?>
              </div>
              <button type="button" class="btn btn-secondary btn-sm" onclick="addFeatureRow()">+ Add Feature Bullet</button>
            </div>
          </div>
        </div>

        <!-- 2. Color Variations (Square Swatches) -->
        <div class="admin-card-section">
          <div class="admin-card-section-header">
            <h3>
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 2a7 7 0 1 0 7 7"/></svg>
              2. Color Variations (Square Swatches)
            </h3>
            <span style="font-size: 11px; color: #64748b;">Live Swatches</span>
          </div>

          <!-- Preset Color Palette -->
          <div class="preset-colors-wrap">
            <strong style="font-size: 11px; text-transform: uppercase; color: #475569; display: block; width: 100%; margin-bottom: 2px;">Click to Add Popular Colors:</strong>
            <span class="preset-chip" onclick="addPresetColor('Pure White', '#FFFFFF')"><span class="color-swatch-square" style="background:#FFFFFF;"></span> White</span>
            <span class="preset-chip" onclick="addPresetColor('Baby Pink', '#FFB6C1')"><span class="color-swatch-square" style="background:#FFB6C1;"></span> Baby Pink</span>
            <span class="preset-chip" onclick="addPresetColor('Hot Pink', '#FF1493')"><span class="color-swatch-square" style="background:#FF1493;"></span> Hot Pink</span>
            <span class="preset-chip" onclick="addPresetColor('Deep Purple', '#800080')"><span class="color-swatch-square" style="background:#800080;"></span> Purple</span>
            <span class="preset-chip" onclick="addPresetColor('Lavender', '#E6E6FA')"><span class="color-swatch-square" style="background:#E6E6FA;"></span> Lavender</span>
            <span class="preset-chip" onclick="addPresetColor('Midnight Black', '#1A1A1A')"><span class="color-swatch-square" style="background:#1A1A1A;"></span> Black</span>
            <span class="preset-chip" onclick="addPresetColor('Royal Blue', '#4169E1')"><span class="color-swatch-square" style="background:#4169E1;"></span> Royal Blue</span>
            <span class="preset-chip" onclick="addPresetColor('Navy Blue', '#000080')"><span class="color-swatch-square" style="background:#000080;"></span> Navy</span>
            <span class="preset-chip" onclick="addPresetColor('Maroon', '#800000')"><span class="color-swatch-square" style="background:#800000;"></span> Maroon</span>
            <span class="preset-chip" onclick="addPresetColor('Emerald Green', '#047857')"><span class="color-swatch-square" style="background:#047857;"></span> Green</span>
            <span class="preset-chip" onclick="addPresetColor('Mustard Yellow', '#D97706')"><span class="color-swatch-square" style="background:#D97706;"></span> Mustard</span>
            <span class="preset-chip" onclick="addPresetColor('Beige / Cream', '#F5F5DC')"><span class="color-swatch-square" style="background:#F5F5DC;"></span> Beige</span>
          </div>

          <!-- Color Cards Grid -->
          <div id="colorsContainer" class="color-cards-grid">
            <?php if (!empty($existingColors)): ?>
              <?php foreach ($existingColors as $idx => $clr): ?>
                <div class="color-card-item">
                  <div class="color-preview-box" style="background: <?= htmlspecialchars($clr['color_code']) ?>; width: 30px; height: 30px;"></div>
                  <div style="flex: 1;">
                    <input type="text" name="colors[<?= $idx ?>][name]" value="<?= sanitize($clr['color_name']) ?>" required placeholder="Color Name">
                  </div>
                  <input type="color" name="colors[<?= $idx ?>][code]" value="<?= htmlspecialchars($clr['color_code']) ?>" title="Change color" onchange="updateColorPreview(this)">
                  <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.color-card-item').remove()" title="Remove" style="padding: 2px 6px;">✕</button>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="color-card-item">
                <div class="color-preview-box" style="background: #FFFFFF; width: 30px; height: 30px;"></div>
                <div style="flex: 1;">
                  <input type="text" name="colors[0][name]" value="Pure White" placeholder="Color Name" required>
                </div>
                <input type="color" name="colors[0][code]" value="#FFFFFF" title="Change color" onchange="updateColorPreview(this)">
                <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.color-card-item').remove()" title="Remove" style="padding: 2px 6px;">✕</button>
              </div>
            <?php endif; ?>
          </div>

          <button type="button" class="btn btn-secondary btn-sm" onclick="addColorRow()" style="margin-top: 10px;">+ Add Custom Color Box</button>
        </div>

        <!-- 3. Fabric & Care -->
        <div class="admin-card-section">
          <div class="admin-card-section-header">
            <h3>
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
              3. Fabric, Material &amp; Care
            </h3>
          </div>
          
          <div class="form-inline-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
            <div class="form-group" style="margin: 0;">
              <label>Fabric / Material</label>
              <input type="text" name="material" value="<?= sanitize($product['material'] ?? '') ?>">
            </div>

            <div class="form-group" style="margin: 0;">
              <label>Care Instructions</label>
              <input type="text" name="care_instructions" value="<?= sanitize($product['care_instructions'] ?? '') ?>">
            </div>
          </div>
        </div>

      </div>

      <!-- ================= RIGHT SIDE ================= -->
      <div class="form-col-side">
        
        <!-- 1. Product Photos & Gallery -->
        <div class="admin-card-section">
          <div class="admin-card-section-header">
            <h3>
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
              4. Product Photos &amp; Gallery (<?= count($existingImages) ?>)
            </h3>
          </div>

          <!-- Existing Images Grid -->
          <?php if (!empty($existingImages)): ?>
            <div class="gallery-grid" style="grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 10px; margin-bottom: 14px;">
              <?php foreach ($existingImages as $img): ?>
                <div class="gallery-card <?= $img['is_primary'] ? 'is-primary' : '' ?>" style="border-radius: 8px;">
                  <img src="<?= htmlspecialchars($img['image_url']) ?>" alt="" style="width: 100%; height: 110px; object-fit: cover; background: #f1f5f9;">
                  <div style="padding: 6px 8px; display: flex; flex-direction: column; gap: 4px;">
                    <span class="gallery-label-badge" style="font-size: 10px; padding: 2px 4px;"><?= sanitize($img['image_label'] ?? 'View') ?></span>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 4px; border-top: 1px solid #f1f5f9; padding-top: 4px;">
                      <?php if (!$img['is_primary']): ?>
                        <a href="<?= adminUrl('products/edit.php?id=' . $id . '&set_primary_image_id=' . $img['id']) ?>" style="font-size: 10px; color: #0284c7; text-decoration: underline;">Set Main</a>
                      <?php else: ?>
                        <span style="font-size: 10px; color: #0284c7; font-weight: 700;">★ Main</span>
                      <?php endif; ?>
                      <a href="<?= adminUrl('products/edit.php?id=' . $id . '&delete_image_id=' . $img['id']) ?>" style="font-size: 10px; color: #dc2626;" onclick="return confirm('Delete image?')">Delete</a>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>

          <!-- Upload More Box -->
          <div style="background: #f8fafc; border: 1.5px dashed #cbd5e1; padding: 12px; border-radius: 8px;">
            <strong style="font-size: 12px; color: #0f172a; display: block; margin-bottom: 6px;">+ Upload More Photo Files:</strong>
            <input type="file" name="subimage_files[]" multiple accept="image/*" style="font-size: 11px; margin-bottom: 8px;">
            
            <div id="subImagesContainer">
              <div class="subimage-input-row" style="padding: 6px 8px; gap: 6px;">
                <input type="url" name="subimage_urls[]" placeholder="Sub-image URL (https://...)" style="font-size: 11px;">
                <select name="subimage_labels[]" style="font-size: 11px;">
                  <option value="Salwar / Bottom View">Salwar / Bottom</option>
                  <option value="Dupatta / Scarf">Dupatta View</option>
                  <option value="Suit Front / Kurti">Suit Front</option>
                  <option value="Back View">Back View</option>
                  <option value="Fabric & Embroidery Detail">Fabric Detail</option>
                  <option value="Side / Angle View">Side View</option>
                  <option value="Styling / Model Shot">Model Shot</option>
                </select>
                <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.subimage-input-row').remove()" style="padding: 2px 4px;">✕</button>
              </div>
            </div>
            <button type="button" class="btn btn-secondary btn-sm" onclick="addSubImageRow()" style="font-size: 10px; padding: 2px 6px; margin-top: 6px;">+ Add URL Row</button>
          </div>
        </div>

        <!-- 2. Pricing & Discounts -->
        <div class="admin-card-section">
          <div class="admin-card-section-header">
            <h3>
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" x2="12" y1="2" y2="22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
              5. Pricing &amp; Discounts
            </h3>
          </div>

          <div class="form-inline-grid form-inline-grid-3" style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px;">
            <div class="form-group" style="margin: 0;">
              <label style="font-size: 11px;">Selling Price (₹) <span class="required" style="color: #ef4444;">*</span></label>
              <input type="number" step="0.01" name="price" id="sellingPrice" required value="<?= $product['price'] ?>" oninput="calcDiscount()">
            </div>

            <div class="form-group" style="margin: 0;">
              <label style="font-size: 11px;">Original / MRP (₹)</label>
              <input type="number" step="0.01" name="original_price" id="originalPrice" value="<?= $product['original_price'] ?? '' ?>" oninput="calcDiscount()">
            </div>

            <div class="form-group" style="margin: 0;">
              <label style="font-size: 11px;">Discount (%)</label>
              <input type="number" name="discount_percent" id="discountPercent" value="<?= $product['discount_percent'] ?? 0 ?>">
            </div>
          </div>
        </div>

        <!-- 3. Department & Category -->
        <div class="admin-card-section">
          <div class="admin-card-section-header">
            <h3>
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 20h16a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.93a2 2 0 0 1-1.66-.9l-.82-1.2A2 2 0 0 0 7.93 3H4a2 2 0 0 0-2 2v13c0 1.1.9 2 2 2Z"/></svg>
              6. Categories (Kids/Men/Women)
            </h3>
          </div>

          <div style="display: flex; flex-direction: column; gap: 12px;">
            <div class="form-group" style="margin: 0;">
              <label style="font-size: 11px;">Department Audience <span class="required" style="color: #ef4444;">*</span></label>
              <select name="gender" required>
                <option value="women" <?= ($product['gender'] ?? '') === 'women' ? 'selected' : '' ?>>Women Collection</option>
                <option value="men" <?= ($product['gender'] ?? '') === 'men' ? 'selected' : '' ?>>Men (Gents) Collection</option>
                <option value="kids" <?= ($product['gender'] ?? '') === 'kids' ? 'selected' : '' ?>>Kids Collection</option>
                <option value="unisex" <?= ($product['gender'] ?? '') === 'unisex' ? 'selected' : '' ?>>Unisex / Accessories</option>
              </select>
            </div>

            <div class="form-group" style="margin: 0;">
              <label style="font-size: 11px;">Main Category <span class="required" style="color: #ef4444;">*</span></label>
              <select name="category_id" required>
                <option value="">-- Select Main Category --</option>
                <?php foreach ($parentCategories as $pCat): ?>
                  <option value="<?= $pCat['id'] ?>" <?= $product['category_id'] == $pCat['id'] ? 'selected' : '' ?>>
                    <?= sanitize($pCat['name']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="form-group" style="margin: 0;">
              <label style="font-size: 11px;">Sub-Category (Suits, Sarees, etc.)</label>
              <select name="subcategory_id">
                <option value="">-- Select Sub-Category --</option>
                <?php foreach ($subCategories as $sCat): ?>
                  <option value="<?= $sCat['id'] ?>" <?= ($product['subcategory_id'] ?? 0) == $sCat['id'] ? 'selected' : '' ?>>
                    <?= sanitize($sCat['name']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
        </div>

        <!-- 4. Sizes & Stock Inventory -->
        <div class="admin-card-section">
          <div class="admin-card-section-header">
            <h3>
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/></svg>
              7. Sizes &amp; Stock Inventory
            </h3>
          </div>

          <div style="display: flex; gap: 4px; flex-wrap: wrap; margin-bottom: 8px;">
            <button type="button" class="btn btn-secondary btn-sm" style="font-size: 10px; padding: 2px 6px;" onclick="applySizePreset('standard')">Standard</button>
            <button type="button" class="btn btn-secondary btn-sm" style="font-size: 10px; padding: 2px 6px;" onclick="applySizePreset('ethnic')">Ethnic/Suits</button>
            <button type="button" class="btn btn-secondary btn-sm" style="font-size: 10px; padding: 2px 6px;" onclick="applySizePreset('kids')">Kids</button>
          </div>

          <div id="sizesContainer" class="sizes-inventory-list">
            <?php if (!empty($existingSizes)): ?>
              <?php foreach ($existingSizes as $sIdx => $sz): ?>
                <div class="size-item-row">
                  <input type="text" name="sizes[<?= $sIdx ?>][name]" value="<?= sanitize($sz['size']) ?>" placeholder="Size" required>
                  <input type="number" name="sizes[<?= $sIdx ?>][stock]" value="<?= (int)$sz['stock'] ?>" min="0" placeholder="Qty" required>
                  <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.size-item-row').remove()" style="padding: 4px 8px;">✕</button>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="size-item-row">
                <input type="text" name="sizes[0][name]" value="Free Size" placeholder="Size" required>
                <input type="number" name="sizes[0][stock]" value="10" min="0" placeholder="Qty" required>
                <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.size-item-row').remove()" style="padding: 4px 8px;">✕</button>
              </div>
            <?php endif; ?>
          </div>

          <button type="button" class="btn btn-secondary btn-sm" onclick="addSizeRow()" style="margin-top: 8px; width: 100%; justify-content: center;">+ Add Size</button>
        </div>

        <!-- 5. Status & Save Card -->
        <div class="admin-card-section" style="border: 2px solid #0f172a; background: #fafaf9;">
          <div style="display: flex; flex-direction: column; gap: 10px; margin-bottom: 16px;">
            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 13px; font-weight: 600;">
              <input type="checkbox" name="is_active" value="1" <?= $product['is_active'] ? 'checked' : '' ?> style="width: 18px; height: 18px; accent-color: #0f172a;">
              <span>Active in Store (Published)</span>
            </label>
            
            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 13px; font-weight: 600;">
              <input type="checkbox" name="is_featured" value="1" <?= $product['is_featured'] ? 'checked' : '' ?> style="width: 18px; height: 18px; accent-color: #0f172a;">
              <span>Featured on Homepage</span>
            </label>
          </div>

          <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; font-size: 15px; font-weight: 700; justify-content: center;">
            Update &amp; Save Changes &rarr;
          </button>
        </div>

      </div>

    </div>
  </form>
</div>

<script>
function calcDiscount() {
  const sp = parseFloat(document.getElementById('sellingPrice').value) || 0;
  const mrp = parseFloat(document.getElementById('originalPrice').value) || 0;
  if (mrp > sp && mrp > 0) {
    const disc = Math.round(((mrp - sp) / mrp) * 100);
    document.getElementById('discountPercent').value = disc;
  }
}

function addSubImageRow() {
  const container = document.getElementById('subImagesContainer');
  const div = document.createElement('div');
  div.className = 'subimage-input-row';
  div.style.padding = '6px 8px';
  div.style.gap = '6px';
  div.innerHTML = `
    <input type="url" name="subimage_urls[]" placeholder="Sub-image URL (https://...)" style="font-size: 11px;">
    <select name="subimage_labels[]" style="font-size: 11px;">
      <option value="Salwar / Bottom View">Salwar / Bottom</option>
      <option value="Dupatta / Scarf">Dupatta View</option>
      <option value="Suit Front / Kurti">Suit Front</option>
      <option value="Back View">Back View</option>
      <option value="Fabric & Embroidery Detail">Fabric Detail</option>
      <option value="Side / Angle View">Side View</option>
      <option value="Styling / Model Shot">Model Shot</option>
    </select>
    <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.subimage-input-row').remove()" style="padding: 2px 4px;">✕</button>
  `;
  container.appendChild(div);
}

let colorIndex = <?= count($existingColors) + 10 ?>;
function addPresetColor(name, hex) {
  const container = document.getElementById('colorsContainer');
  const card = document.createElement('div');
  card.className = 'color-card-item';
  card.innerHTML = `
    <div class="color-preview-box" style="background: ${hex}; width: 30px; height: 30px;"></div>
    <div style="flex: 1;">
      <input type="text" name="colors[${colorIndex}][name]" value="${name}" required placeholder="Color Name">
    </div>
    <input type="color" name="colors[${colorIndex}][code]" value="${hex}" title="Change color" onchange="updateColorPreview(this)">
    <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.color-card-item').remove()" title="Remove" style="padding: 2px 6px;">✕</button>
  `;
  container.appendChild(card);
  colorIndex++;
}

function addColorRow() {
  addPresetColor('Custom Color', '#800080');
}

function updateColorPreview(input) {
  const previewBox = input.closest('.color-card-item').querySelector('.color-preview-box');
  if (previewBox) {
    previewBox.style.background = input.value;
  }
}

let sizeIndex = <?= count($existingSizes) + 10 ?>;
function addSizeRow(name = '', stock = 10) {
  const container = document.getElementById('sizesContainer');
  const div = document.createElement('div');
  div.className = 'size-item-row';
  div.innerHTML = `
    <input type="text" name="sizes[${sizeIndex}][name]" value="${name}" placeholder="Size" required>
    <input type="number" name="sizes[${sizeIndex}][stock]" value="${stock}" min="0" placeholder="Qty" required>
    <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.size-item-row').remove()" style="padding: 4px 8px;">✕</button>
  `;
  container.appendChild(div);
  sizeIndex++;
}

function applySizePreset(type) {
  const container = document.getElementById('sizesContainer');
  container.innerHTML = '';
  if (type === 'standard') {
    ['XS', 'S', 'M', 'L', 'XL', 'XXL'].forEach(s => addSizeRow(s, 10));
  } else if (type === 'ethnic') {
    ['Unstitched / Free Size', 'S', 'M', 'L', 'XL', 'XXL'].forEach(s => addSizeRow(s, 10));
  } else if (type === 'kids') {
    ['2-3Y', '4-5Y', '6-7Y', '8-9Y', '10-11Y'].forEach(s => addSizeRow(s, 8));
  }
}

function addFeatureRow() {
  const list = document.getElementById('featuresList');
  const div = document.createElement('div');
  div.style.display = 'flex';
  div.style.gap = '8px';
  div.style.marginBottom = '8px';
  div.innerHTML = `
    <input type="text" name="features[]" placeholder="e.g. Premium embroidery finish" style="flex: 1;">
    <button type="button" class="btn btn-secondary btn-sm" onclick="this.parentElement.remove()">✕</button>
  `;
  list.appendChild(div);
}
</script>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>

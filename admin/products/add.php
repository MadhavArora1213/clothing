<?php
require_once dirname(__DIR__, 2) . '/config/database.php';
requireAdminAuth();

$error = '';
$success = '';

// Fetch all active categories & subcategories
$categoriesQuery = $mysqli->query("SELECT * FROM categories WHERE is_active = 1 ORDER BY parent_id, sort_order, name");
$allCategories = $categoriesQuery ? $categoriesQuery->fetch_all(MYSQLI_ASSOC) : [];

$parentCategories = array_filter($allCategories, fn($c) => $c['parent_id'] == 0);
$subCategories = array_filter($allCategories, fn($c) => $c['parent_id'] > 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = sanitize($_POST['name'] ?? '');
  $slug = sanitize($_POST['slug'] ?? '');
  $sku = sanitize($_POST['sku'] ?? '');
  $brand = sanitize($_POST['brand'] ?? 'urban outfit');
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
    $error = 'Please enter a valid price greater than 0.';
  } elseif ($category_id <= 0) {
    $error = 'Please select a main category.';
  } else {
    if (empty($slug)) {
      $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));
    }

    $slugCheck = $mysqli->prepare("SELECT id FROM products WHERE slug = ?");
    $slugCheck->bind_param('s', $slug);
    $slugCheck->execute();
    if ($slugCheck->get_result()->num_rows > 0) {
      $slug = $slug . '-' . time();
    }

    $mainImageUrl = '';
    if (isset($_FILES['main_image_file']) && $_FILES['main_image_file']['error'] === UPLOAD_ERR_OK) {
      $uploaded = handleImageUpload($_FILES['main_image_file'], 'products');
      if ($uploaded) {
        $mainImageUrl = $uploaded;
      }
    }
    if (empty($mainImageUrl) && !empty($_POST['main_image_url'])) {
      $mainImageUrl = trim($_POST['main_image_url']);
    }

    $featuresJson = json_encode(array_values($features));

    $stmt = $mysqli->prepare("INSERT INTO products 
      (name, slug, sku, brand, gender, description, features, material, care_instructions, price, original_price, discount_percent, category_id, subcategory_id, image, is_featured, is_active) 
      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->bind_param('sssssssssddiiisii', 
      $name, $slug, $sku, $brand, $gender, $description, $featuresJson, $material, $care_instructions, 
      $price, $original_price, $discount_percent, $category_id, $subcategory_id, $mainImageUrl, $is_featured, $is_active);

    if ($stmt->execute()) {
      $productId = $mysqli->insert_id;

      if (!empty($mainImageUrl)) {
        $imgStmt = $mysqli->prepare("INSERT INTO product_images (product_id, image_url, image_label, sort_order, is_primary) VALUES (?, ?, 'Main Front', 0, 1)");
        $imgStmt->bind_param('is', $productId, $mainImageUrl);
        $imgStmt->execute();
      }

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
              $subStmt = $mysqli->prepare("INSERT INTO product_images (product_id, image_url, image_label, sort_order, is_primary) VALUES (?, ?, ?, ?, 0)");
              $sortOrder = $i + 1;
              $subStmt->bind_param('issi', $productId, $uploadedSub, $lbl, $sortOrder);
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
            $subStmt->bind_param('issi', $productId, $sUrl, $sLbl, $sortOrder);
            $subStmt->execute();
          }
        }
      }

      if (!empty($colors)) {
        $clrStmt = $mysqli->prepare("INSERT INTO product_colors (product_id, color_code, color_name, sort_order) VALUES (?, ?, ?, ?)");
        foreach ($colors as $idx => $clr) {
          $clrCode = trim($clr['code'] ?? '');
          $clrName = trim($clr['name'] ?? '');
          if (!empty($clrCode) && !empty($clrName)) {
            $clrStmt->bind_param('issi', $productId, $clrCode, $clrName, $idx);
            $clrStmt->execute();
          }
        }
      }

      if (!empty($sizes)) {
        $szStmt = $mysqli->prepare("INSERT INTO product_sizes (product_id, size, stock, sku) VALUES (?, ?, ?, ?)");
        foreach ($sizes as $sz) {
          $szName = trim($sz['name'] ?? '');
          $szStock = (int)($sz['stock'] ?? 0);
          $szSku = trim($sz['sku'] ?? '');
          if (!empty($szName)) {
            $szStmt->bind_param('isis', $productId, $szName, $szStock, $szSku);
            $szStmt->execute();
          }
        }
      }

      header('Location: ' . adminUrl('products/?success=Product+added+successfully'));
      exit;
    } else {
      $error = 'Failed to create product: ' . $mysqli->error;
    }
  }
}

$pageTitle = 'Add New Product — urban outfit Admin';
include dirname(__DIR__) . '/includes/header.php';
?>

<div class="admin-content">
  <div class="page-header" style="margin-bottom: var(--space-6);">
    <div class="page-header-row" style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
      <div>
        <h1 style="margin: 0; font-size: 26px;">Add New Product</h1>
        <p style="margin: 4px 0 0 0; color: #64748b;">Add any product — shirts, dresses, kurtas, co-ords, kids wear, accessories, anything.</p>
      </div>
      <div class="page-header-actions" style="display: flex; gap: 8px;">
        <a href="<?= adminUrl('products/') ?>" class="btn btn-secondary">&larr; Back to Products</a>
        <button type="submit" form="productForm" class="btn btn-primary" style="padding: 10px 22px;">Save &amp; Publish Product</button>
      </div>
    </div>
  </div>

  <?php if ($error): ?>
    <div class="alert alert-error" style="margin-bottom: var(--space-6); background: #FEF2F2; color: #991B1B; border: 1px solid #F87171; padding: 12px 16px; border-radius: 8px; font-weight: 500;">
      <?= sanitize($error) ?>
    </div>
  <?php endif; ?>

  <form method="POST" action="" enctype="multipart/form-data" id="productForm">
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
              <input type="text" name="name" id="productName" required placeholder="e.g. Oversized Graphic Tee / Silk Kurta Set / Linen Co-Ord" value="<?= sanitize($_POST['name'] ?? '') ?>" oninput="autoGenerateSlug(this.value)">
            </div>

            <div class="form-inline-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
              <div class="form-group" style="margin: 0;">
                <label>Brand</label>
                <input type="text" name="brand" value="<?= sanitize($_POST['brand'] ?? 'urban outfit') ?>">
              </div>
              <div class="form-group" style="margin: 0;">
                <label>SKU (Product Code)</label>
                <input type="text" name="sku" placeholder="e.g. UO-MEN-TEE-001" value="<?= sanitize($_POST['sku'] ?? '') ?>">
              </div>
            </div>

            <div class="form-group" style="margin: 0;">
              <label>URL Slug <span class="required" style="color: #ef4444;">*</span></label>
              <input type="text" name="slug" id="productSlug" required placeholder="pure-georgette-embroidered-anarkali-suit-set" value="<?= sanitize($_POST['slug'] ?? '') ?>">
            </div>

            <div class="form-group" style="margin: 0;">
              <label>Product Description</label>
              <textarea name="description" rows="4" placeholder="Product description, fabric details, fit, styling tips..."><?= sanitize($_POST['description'] ?? '') ?></textarea>
            </div>

            <div class="form-group" style="margin: 0;">
              <label>Key Features / Highlights</label>
              <div id="featuresList">
                <div style="display: flex; gap: 8px; margin-bottom: 8px;">
                  <input type="text" name="features[]" placeholder="e.g. 260 GSM Premium Cotton / Breathable Linen" style="flex: 1;">
                  <button type="button" class="btn btn-secondary btn-sm" onclick="this.parentElement.remove()">✕</button>
                </div>
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
            <span style="font-size: 11px; color: #64748b;">Click to add common colors</span>
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
            <div class="color-card-item">
              <div class="color-preview-box" style="background: #FFFFFF; width: 30px; height: 30px;"></div>
              <div style="flex: 1;">
                <input type="text" name="colors[0][name]" value="Pure White" placeholder="Color Name" required>
              </div>
              <input type="color" name="colors[0][code]" value="#FFFFFF" title="Choose color" onchange="updateColorPreview(this)">
              <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.color-card-item').remove()" title="Remove" style="padding: 2px 6px;">✕</button>
            </div>
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
              <input type="text" name="material" placeholder="e.g. 100% Cotton / Linen Blend / Silk" value="<?= sanitize($_POST['material'] ?? '') ?>">
            </div>

            <div class="form-group" style="margin: 0;">
              <label>Care Instructions</label>
              <input type="text" name="care_instructions" placeholder="e.g. Dry Clean Only / Hand Wash" value="<?= sanitize($_POST['care_instructions'] ?? '') ?>">
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
              4. Product Photos
            </h3>
            <span style="font-size: 11px; color: #64748b;">Main + Sub-images</span>
          </div>

          <!-- Primary Image -->
          <div style="background: #f8fafc; border: 1.5px dashed #cbd5e1; padding: 14px; border-radius: 8px; margin-bottom: 14px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
              <strong style="color: #0f172a; font-size: 12px;">Main Product Photo</strong>
              <span style="font-size: 10px; font-weight: 700; background: #0284c7; color: #fff; padding: 2px 6px; border-radius: 4px;">Main</span>
            </div>
            
            <div class="form-group" style="margin: 0;">
              <input type="file" name="main_image_file" accept="image/*" onchange="previewMainFile(this)" style="font-size: 12px;">
            </div>

            <div id="mainImagePreviewContainer" style="margin-top: 10px; display: none;">
              <img id="mainImagePreview" src="" alt="Main Preview" style="width: 80px; height: 105px; object-fit: cover; border-radius: 4px; border: 2px solid #0284c7;">
            </div>
          </div>

          <!-- Sub-Images Gallery -->
          <div style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 14px; border-radius: 8px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
              <strong style="color: #0f172a; font-size: 12px;">Additional Product Images</strong>
              <button type="button" class="btn btn-secondary btn-sm" style="font-size: 11px; padding: 2px 6px;" onclick="addSubImageRow()">+ Add Image</button>
            </div>

            <div id="subImagesContainer">
              <div class="subimage-input-row" style="padding: 8px 10px; gap: 8px;">
                <input type="file" name="subimage_files[]" accept="image/*" style="font-size: 12px; flex: 1;">
                <select name="subimage_labels[]" style="font-size: 12px;">
                  <option value="Back View">Back View</option>
                  <option value="Front View">Front View</option>
                  <option value="Side View">Side / Angle View</option>
                  <option value="Detail Close-up">Detail / Close-up</option>
                  <option value="On Model">On Model</option>
                  <option value="Flat Lay">Flat Lay</option>
                  <option value="Packaging">Packaging</option>
                  <option value="Lifestyle Shot">Lifestyle Shot</option>
                </select>
                <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.subimage-input-row').remove()" style="padding: 2px 6px;">✕</button>
              </div>
            </div>
          </div>
        </div>

        <!-- 2. Pricing & Discounts -->
        <div class="admin-card-section">
          <div class="admin-card-section-header">
            <h3>
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" x2="12" y1="2" y2="22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
              5. Pricing
            </h3>
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px;">
            <div class="form-group" style="margin: 0;">
              <label style="font-size: 11px;">Selling Price (₹) <span class="required" style="color: #ef4444;">*</span></label>
              <input type="number" step="0.01" name="price" id="sellingPrice" required placeholder="2499" value="<?= sanitize($_POST['price'] ?? '') ?>" oninput="calcDiscount()">
            </div>

            <div class="form-group" style="margin: 0;">
              <label style="font-size: 11px;">Original / MRP (₹)</label>
              <input type="number" step="0.01" name="original_price" id="originalPrice" placeholder="3999" value="<?= sanitize($_POST['original_price'] ?? '') ?>" oninput="calcDiscount()">
            </div>

            <div class="form-group" style="margin: 0;">
              <label style="font-size: 11px;">Discount (%)</label>
              <input type="number" name="discount_percent" id="discountPercent" placeholder="0" value="<?= sanitize($_POST['discount_percent'] ?? '0') ?>">
            </div>
          </div>
        </div>

        <!-- 3. Department & Category -->
        <div class="admin-card-section">
          <div class="admin-card-section-header">
            <h3>
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 20h16a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.93a2 2 0 0 1-1.66-.9l-.82-1.2A2 2 0 0 0 7.93 3H4a2 2 0 0 0-2 2v13c0 1.1.9 2 2 2Z"/></svg>
              6. Department &amp; Category
            </h3>
          </div>

          <div style="display: flex; flex-direction: column; gap: 12px;">
            <div class="form-group" style="margin: 0;">
              <label style="font-size: 11px;">Department Audience <span class="required" style="color: #ef4444;">*</span></label>
              <select name="gender" id="genderSelect" required onchange="filterCategoriesByDepartment(this.value)">
                <option value="women" <?= ($_POST['gender'] ?? '') === 'women' ? 'selected' : '' ?>>Women Collection</option>
                <option value="men" <?= ($_POST['gender'] ?? '') === 'men' ? 'selected' : '' ?>>Men (Gents) Collection</option>
                <option value="kids" <?= ($_POST['gender'] ?? '') === 'kids' ? 'selected' : '' ?>>Kids Collection</option>
                <option value="unisex" <?= ($_POST['gender'] ?? '') === 'unisex' ? 'selected' : '' ?>>Unisex / Accessories</option>
              </select>
            </div>

            <div class="form-group" style="margin: 0;">
              <label style="font-size: 11px;">Main Category <span class="required" style="color: #ef4444;">*</span></label>
              <select name="category_id" id="mainCategorySelect" required onchange="updateSubcategories(this.value)">
                <option value="">-- Select Main Category --</option>
                <?php foreach ($parentCategories as $pCat): ?>
                  <option value="<?= $pCat['id'] ?>" data-dept="<?= $pCat['department'] ?>" <?= ($_POST['category_id'] ?? '') == $pCat['id'] ? 'selected' : '' ?>>
                    <?= sanitize($pCat['name']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="form-group" style="margin: 0;">
              <label style="font-size: 11px;">Sub-Category</label>
              <select name="subcategory_id" id="subCategorySelect">
                <option value="">-- Select Sub-Category --</option>
                <?php foreach ($subCategories as $sCat): ?>
                  <option value="<?= $sCat['id'] ?>" data-parent="<?= $sCat['parent_id'] ?>" <?= ($_POST['subcategory_id'] ?? '') == $sCat['id'] ? 'selected' : '' ?>>
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
              7. Sizes &amp; Stock
            </h3>
          </div>

          <div style="display: flex; gap: 4px; flex-wrap: wrap; margin-bottom: 8px;">
            <button type="button" class="btn btn-secondary btn-sm" style="font-size: 10px; padding: 2px 6px;" onclick="applySizePreset('standard')">Standard (XS-XXL)</button>
            <button type="button" class="btn btn-secondary btn-sm" style="font-size: 10px; padding: 2px 6px;" onclick="applySizePreset('free')">Free Size</button>
            <button type="button" class="btn btn-secondary btn-sm" style="font-size: 10px; padding: 2px 6px;" onclick="applySizePreset('kids')">Kids (Age)</button>
            <button type="button" class="btn btn-secondary btn-sm" style="font-size: 10px; padding: 2px 6px;" onclick="applySizePreset('numeric')">Numeric (38-46)</button>
          </div>

          <div id="sizesContainer" class="sizes-inventory-list">
            <div class="size-item-row">
              <input type="text" name="sizes[0][name]" value="M" placeholder="Size" required>
              <input type="number" name="sizes[0][stock]" value="10" min="0" placeholder="Qty" required>
              <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.size-item-row').remove()" style="padding: 4px 8px;">✕</button>
            </div>
          </div>

          <button type="button" class="btn btn-secondary btn-sm" onclick="addSizeRow()" style="margin-top: 8px; width: 100%; justify-content: center;">+ Add Size</button>
        </div>

        <!-- 5. Status & Save Card -->
        <div class="admin-card-section" style="border: 2px solid #0f172a; background: #fafaf9;">
          <div style="display: flex; flex-direction: column; gap: 10px; margin-bottom: 16px;">
            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 13px; font-weight: 600;">
              <input type="checkbox" name="is_active" value="1" checked style="width: 18px; height: 18px; accent-color: #0f172a;">
              <span>Active in Store (Published)</span>
            </label>
            
            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 13px; font-weight: 600;">
              <input type="checkbox" name="is_featured" value="1" style="width: 18px; height: 18px; accent-color: #0f172a;">
              <span>Featured on Homepage</span>
            </label>
          </div>

          <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; font-size: 15px; font-weight: 700; justify-content: center;">
            Save &amp; Publish Product &rarr;
          </button>
        </div>

      </div>

    </div>
  </form>
</div>

<script>
function autoGenerateSlug(text) {
  const slug = text.toLowerCase()
    .trim()
    .replace(/[^\w\s-]/g, '')
    .replace(/[\s_-]+/g, '-')
    .replace(/^-+|-+$/g, '');
  document.getElementById('productSlug').value = slug;
}

function calcDiscount() {
  const sp = parseFloat(document.getElementById('sellingPrice').value) || 0;
  const mrp = parseFloat(document.getElementById('originalPrice').value) || 0;
  if (mrp > sp && mrp > 0) {
    const disc = Math.round(((mrp - sp) / mrp) * 100);
    document.getElementById('discountPercent').value = disc;
  }
}

function filterCategoriesByDepartment(dept) {
  const mainSelect = document.getElementById('mainCategorySelect');
  for (let opt of mainSelect.options) {
    if (!opt.value) continue;
    const optDept = opt.getAttribute('data-dept');
    if (dept === 'unisex' || optDept === 'all' || optDept === dept) {
      opt.style.display = '';
    } else {
      opt.style.display = 'none';
    }
  }
}

function updateSubcategories(parentId) {
  const subSelect = document.getElementById('subCategorySelect');
  for (let opt of subSelect.options) {
    if (!opt.value) continue;
    const parent = opt.getAttribute('data-parent');
    if (!parentId || parent === parentId) {
      opt.style.display = '';
    } else {
      opt.style.display = 'none';
    }
  }
}

function previewMainFile(input) {
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = function(e) {
      document.getElementById('mainImagePreview').src = e.target.result;
      document.getElementById('mainImagePreviewContainer').style.display = 'block';
    }
    reader.readAsDataURL(input.files[0]);
  }
}

function addSubImageRow() {
  const container = document.getElementById('subImagesContainer');
  const div = document.createElement('div');
  div.className = 'subimage-input-row';
  div.style.padding = '8px 10px';
  div.style.gap = '8px';
  div.innerHTML = `
    <input type="file" name="subimage_files[]" accept="image/*" style="font-size: 12px; flex: 1;">
    <select name="subimage_labels[]" style="font-size: 12px;">
      <option value="Back View">Back View</option>
      <option value="Front View">Front View</option>
      <option value="Side View">Side / Angle View</option>
      <option value="Detail Close-up">Detail / Close-up</option>
      <option value="On Model">On Model</option>
      <option value="Flat Lay">Flat Lay</option>
      <option value="Packaging">Packaging</option>
      <option value="Lifestyle Shot">Lifestyle Shot</option>
    </select>
    <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.subimage-input-row').remove()" style="padding: 2px 6px;">✕</button>
  `;
  container.appendChild(div);
}

let colorIndex = 1;
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
  addPresetColor('Custom Color', '#FF69B4');
}

function updateColorPreview(input) {
  const previewBox = input.closest('.color-card-item').querySelector('.color-preview-box');
  if (previewBox) {
    previewBox.style.background = input.value;
  }
}

let sizeIndex = 1;
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
  } else if (type === 'free') {
    ['Free Size'].forEach(s => addSizeRow(s, 15));
  } else if (type === 'kids') {
    ['2-3Y', '4-5Y', '6-7Y', '8-9Y', '10-11Y', '12-13Y'].forEach(s => addSizeRow(s, 8));
  } else if (type === 'numeric') {
    ['38', '40', '42', '44', '46'].forEach(s => addSizeRow(s, 10));
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

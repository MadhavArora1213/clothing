<?php
require_once dirname(__DIR__, 2) . '/config/database.php';
requireAdminAuth();

$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 20;
$offset = ($page - 1) * $limit;
$search = sanitize($_GET['search'] ?? '');
$categoryFilter = (int)($_GET['category'] ?? 0);
$genderFilter = sanitize($_GET['gender'] ?? '');
$statusFilter = sanitize($_GET['status'] ?? '');

$where = ['1=1'];
$params = [];
$types = '';

if ($search) {
  $where[] = '(p.name LIKE ? OR p.sku LIKE ? OR p.brand LIKE ?)';
  $searchTerm = "%$search%";
  $params[] = $searchTerm;
  $params[] = $searchTerm;
  $params[] = $searchTerm;
  $types .= 'sss';
}

if ($categoryFilter > 0) {
  $where[] = '(p.category_id = ? OR p.subcategory_id = ?)';
  $params[] = $categoryFilter;
  $params[] = $categoryFilter;
  $types .= 'ii';
}

if ($genderFilter && in_array($genderFilter, ['women', 'men', 'kids', 'unisex'])) {
  $where[] = 'p.gender = ?';
  $params[] = $genderFilter;
  $types .= 's';
}

if ($statusFilter !== '') {
  $where[] = 'p.is_active = ?';
  $params[] = (int)$statusFilter;
  $types .= 'i';
}

$whereClause = implode(' AND ', $where);

// Count Total
$countSql = "SELECT COUNT(*) as total FROM products p WHERE $whereClause";
if (!empty($params)) {
  $countStmt = $mysqli->prepare($countSql);
  $countStmt->bind_param($types, ...$params);
  $countStmt->execute();
  $totalProducts = $countStmt->get_result()->fetch_assoc()['total'] ?? 0;
} else {
  $totalResult = $mysqli->query($countSql);
  $totalProducts = $totalResult ? $totalResult->fetch_assoc()['total'] : 0;
}

$totalPages = max(1, (int)ceil($totalProducts / $limit));

// Fetch Products with category & subcategory names
$query = "SELECT p.*, c.name as category_name, sub.name as subcategory_name 
          FROM products p 
          LEFT JOIN categories c ON p.category_id = c.id 
          LEFT JOIN categories sub ON p.subcategory_id = sub.id 
          WHERE $whereClause 
          ORDER BY p.created_at DESC 
          LIMIT ? OFFSET ?";

$stmt = $mysqli->prepare($query);
$bindTypes = $types . 'ii';
$bindParams = array_merge($params, [$limit, $offset]);
$stmt->bind_param($bindTypes, ...$bindParams);
$stmt->execute();
$products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Fetch categories for filter
$categories = $mysqli->query('SELECT * FROM categories ORDER BY parent_id, sort_order, name')->fetch_all(MYSQLI_ASSOC);

$pageTitle = 'Products Management — urban outfit Admin';
include dirname(__DIR__) . '/includes/header.php';
?>

<div class="admin-content">
  <div class="admin-page-header">
    <div>
      <h1>Products Management</h1>
      <p style="color: var(--color-text-secondary); margin-top: 4px;">
        Manage full apparel catalog for Women, Men & Kids with multi-images, color swatches & inventory.
      </p>
    </div>
    <div class="admin-actions">
      <a href="<?= adminUrl('products/add.php') ?>" class="btn btn-primary" style="display: flex; align-items: center; gap: 8px;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
        Add New Product
      </a>
    </div>
  </div>

  <!-- Filters Bar -->
  <div class="admin-card" style="margin-bottom: var(--space-6); padding: var(--space-4) var(--space-6);">
    <form method="GET" action="" style="display: flex; flex-wrap: wrap; gap: 12px; align-items: center; justify-content: space-between;">
      <div style="display: flex; flex-wrap: wrap; gap: 12px; align-items: center; flex: 1;">
        <div class="search-box" style="flex: 1; min-width: 220px; max-width: 320px;">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
          <input type="text" name="search" placeholder="Search title, SKU, brand..." value="<?= sanitize($search) ?>">
        </div>

        <select name="gender" class="filter-select" onchange="this.form.submit()">
          <option value="">All Genders / Departments</option>
          <option value="women" <?= $genderFilter === 'women' ? 'selected' : '' ?>>Women</option>
          <option value="men" <?= $genderFilter === 'men' ? 'selected' : '' ?>>Men</option>
          <option value="kids" <?= $genderFilter === 'kids' ? 'selected' : '' ?>>Kids</option>
          <option value="unisex" <?= $genderFilter === 'unisex' ? 'selected' : '' ?>>Unisex</option>
        </select>

        <select name="category" class="filter-select" onchange="this.form.submit()" style="min-width: 180px;">
          <option value="">All Categories</option>
          <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat['id'] ?>" <?= $categoryFilter == $cat['id'] ? 'selected' : '' ?>>
              <?= $cat['parent_id'] > 0 ? '— ' : '' ?><?= sanitize($cat['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>

        <select name="status" class="filter-select" onchange="this.form.submit()">
          <option value="">All Status</option>
          <option value="1" <?= $statusFilter === '1' ? 'selected' : '' ?>>Active Only</option>
          <option value="0" <?= $statusFilter === '0' ? 'selected' : '' ?>>Inactive Only</option>
        </select>
      </div>

      <div style="display: flex; gap: 8px;">
        <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
        <?php if ($search || $categoryFilter || $genderFilter || $statusFilter !== ''): ?>
          <a href="<?= adminUrl('products/') ?>" class="btn btn-secondary btn-sm" style="color: #ef4444;">Reset</a>
        <?php endif; ?>
      </div>
    </form>
  </div>

  <!-- Products List Table -->
  <div class="admin-card">
    <div class="table-wrap">
      <table class="admin-table">
        <thead>
          <tr>
            <th style="width: 320px;">Product &amp; Department</th>
            <th>SKU</th>
            <th>Category</th>
            <th>Colors (Swatches)</th>
            <th>Price</th>
            <th>Sizes &amp; Stock</th>
            <th>Status</th>
            <th style="text-align: right;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($products)): ?>
            <tr>
              <td colspan="8" style="text-align: center; padding: 60px; color: var(--color-text-tertiary);">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin: 0 auto 12px; display: block; opacity: 0.4;"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>
                No products found in the catalog.
                <div style="margin-top: 12px;">
                  <a href="<?= adminUrl('products/add.php') ?>" class="btn btn-primary btn-sm">+ Add Your First Product</a>
                </div>
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($products as $prod): ?>
              <?php
                // Fetch colors for this product
                $colorsStmt = $mysqli->prepare("SELECT * FROM product_colors WHERE product_id = ? ORDER BY sort_order, id");
                $colorsStmt->bind_param('i', $prod['id']);
                $colorsStmt->execute();
                $prodColors = $colorsStmt->get_result()->fetch_all(MYSQLI_ASSOC);

                // Fetch sizes & stock
                $sizesStmt = $mysqli->prepare("SELECT * FROM product_sizes WHERE product_id = ? ORDER BY id");
                $sizesStmt->bind_param('i', $prod['id']);
                $sizesStmt->execute();
                $prodSizes = $sizesStmt->get_result()->fetch_all(MYSQLI_ASSOC);

                $totalStock = array_sum(array_column($prodSizes, 'stock'));

                // Determine Main Image
                $imgUrl = !empty($prod['image']) ? $prod['image'] : siteUrl('assets/images/placeholder.jpg');

                // Gender Badge Color
                $genderBadgeClass = match($prod['gender'] ?? 'women') {
                  'women' => 'background: #FCE7F3; color: #9D174D; border: 1px solid #FBCFE8;',
                  'men' => 'background: #E0E7FF; color: #3730A3; border: 1px solid #C7D2FE;',
                  'kids' => 'background: #FEF3C7; color: #92400E; border: 1px solid #FDE68A;',
                  default => 'background: #F3F4F6; color: #374151; border: 1px solid #E5E7EB;'
                };
              ?>
              <tr>
                <td>
                  <div style="display: flex; align-items: center; gap: 14px;">
                    <img src="<?= htmlspecialchars($imgUrl) ?>" alt="" style="width: 52px; height: 68px; object-fit: cover; border-radius: var(--radius-sm); border: 1px solid #e2e8f0; background: var(--color-bg-elevated); flex-shrink: 0;">
                    <div>
                      <div style="font-weight: 600; color: var(--color-text-primary); font-size: 14px; line-height: 1.3;">
                        <?= sanitize($prod['name']) ?>
                      </div>
                      <div style="display: flex; gap: 6px; align-items: center; margin-top: 4px;">
                        <span style="font-size: 10px; font-weight: 700; text-transform: uppercase; padding: 2px 6px; border-radius: 4px; <?= $genderBadgeClass ?>">
                          <?= ucfirst($prod['gender'] ?? 'women') ?>
                        </span>
                        <?php if ($prod['is_featured']): ?>
                          <span style="font-size: 10px; font-weight: 700; background: #FEF08A; color: #854D0E; padding: 2px 6px; border-radius: 4px; border: 1px solid #FDE047;">★ Featured</span>
                        <?php endif; ?>
                      </div>
                      <small style="color: var(--color-text-tertiary); font-size: 11px;"><?= sanitize($prod['brand'] ?? 'AURA & CO.') ?></small>
                    </div>
                  </div>
                </td>
                <td style="font-family: monospace; font-size: 12px; color: #475569;">
                  <?= sanitize($prod['sku'] ?? '—') ?>
                </td>
                <td>
                  <div style="font-weight: 500;"><?= sanitize($prod['category_name'] ?? 'Uncategorized') ?></div>
                  <?php if (!empty($prod['subcategory_name'])): ?>
                    <small style="color: var(--color-text-tertiary); font-size: 11px;">&rsaquo; <?= sanitize($prod['subcategory_name']) ?></small>
                  <?php endif; ?>
                </td>
                <td>
                  <!-- Visual Square Color Swatches -->
                  <?php if (!empty($prodColors)): ?>
                    <div class="color-swatches-row">
                      <?php foreach ($prodColors as $clr): ?>
                        <span class="color-swatch-square" style="background-color: <?= htmlspecialchars($clr['color_code']) ?>;" title="<?= htmlspecialchars($clr['color_name']) ?> (<?= htmlspecialchars($clr['color_code']) ?>)"></span>
                      <?php endforeach; ?>
                    </div>
                    <small style="display: block; font-size: 11px; color: var(--color-text-tertiary); margin-top: 4px;">
                      <?= count($prodColors) ?> <?= count($prodColors) === 1 ? 'color' : 'colors' ?>
                    </small>
                  <?php else: ?>
                    <span style="color: var(--color-text-tertiary); font-size: 12px;">Standard</span>
                  <?php endif; ?>
                </td>
                <td>
                  <div style="font-weight: 600;"><?= formatPrice($prod['price']) ?></div>
                  <?php if (!empty($prod['original_price']) && $prod['original_price'] > $prod['price']): ?>
                    <div style="font-size: 11px; color: var(--color-text-tertiary); text-decoration: line-through;">
                      <?= formatPrice($prod['original_price']) ?>
                    </div>
                  <?php endif; ?>
                  <?php if ($prod['discount_percent'] > 0): ?>
                    <span style="font-size: 10px; font-weight: 700; color: #16A34A; background: #DCFCE7; padding: 1px 5px; border-radius: 4px;">
                      -<?= $prod['discount_percent'] ?>% OFF
                    </span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if (!empty($prodSizes)): ?>
                    <div style="display: flex; flex-wrap: wrap; gap: 4px; margin-bottom: 4px;">
                      <?php foreach ($prodSizes as $sz): ?>
                        <span style="font-size: 10px; background: #f1f5f9; padding: 1px 5px; border-radius: 3px; border: 1px solid #e2e8f0;">
                          <strong><?= sanitize($sz['size']) ?>:</strong> <?= (int)$sz['stock'] ?>
                        </span>
                      <?php endforeach; ?>
                    </div>
                  <?php endif; ?>
                  <span class="status-badge <?= $totalStock > 0 ? 'status-active' : 'status-inactive' ?>" style="font-size: 11px;">
                    <?= $totalStock > 0 ? number_format($totalStock) . ' in stock' : 'Out of stock' ?>
                  </span>
                </td>
                <td>
                  <span class="status-badge <?= $prod['is_active'] ? 'status-active' : 'status-inactive' ?>">
                    <?= $prod['is_active'] ? 'Active' : 'Inactive' ?>
                  </span>
                </td>
                <td style="text-align: right;">
                  <div style="display: flex; gap: 6px; justify-content: flex-end;">
                    <a href="<?= adminUrl('products/edit.php?id=' . $prod['id']) ?>" class="btn btn-secondary btn-sm" title="Edit Product">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"/></svg>
                      Edit
                    </a>
                    <a href="<?= adminUrl('products/delete.php?id=' . $prod['id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete \'<?= addslashes(sanitize($prod['name'])) ?>\'?')" title="Delete Product">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                      Delete
                    </a>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
      <div style="padding: var(--space-4) var(--space-6); display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--color-bg-elevated);">
        <span style="font-size: var(--text-body-sm); color: var(--color-text-tertiary);">
          Showing Page <?= $page ?> of <?= $totalPages ?> (<?= number_format($totalProducts) ?> total items)
        </span>
        <div style="display: flex; gap: 6px;">
          <?php if ($page > 1): ?>
            <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&category=<?= $categoryFilter ?>&gender=<?= urlencode($genderFilter) ?>&status=<?= urlencode($statusFilter) ?>" class="btn btn-secondary btn-sm">&larr; Previous</a>
          <?php endif; ?>
          <?php if ($page < $totalPages): ?>
            <a href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&category=<?= $categoryFilter ?>&gender=<?= urlencode($genderFilter) ?>&status=<?= urlencode($statusFilter) ?>" class="btn btn-secondary btn-sm">Next &rarr;</a>
          <?php endif; ?>
        </div>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>

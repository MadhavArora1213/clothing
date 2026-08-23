<?php
require_once dirname(__DIR__) . '/config/database.php';
requireAdminAuth();

$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 20;
$offset = ($page - 1) * $limit;
$search = sanitize($_GET['search'] ?? '');
$categoryFilter = (int)($_GET['category'] ?? 0);

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
  $where[] = 'p.category_id = ?';
  $params[] = $categoryFilter;
  $types .= 'i';
}

$whereClause = implode(' AND ', $where);

$totalResult = $mysqli->query("SELECT COUNT(*) as total FROM products p WHERE $whereClause");
$totalProducts = $totalResult->fetch_assoc()['total'];
$totalPages = max(1, (int)ceil($totalProducts / $limit));

$stmt = $mysqli->prepare("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE $whereClause ORDER BY p.created_at DESC LIMIT ? OFFSET ?");
$stmt->bind_param($types . 'ii', ...array_merge($params, [$limit, $offset]));
$stmt->execute();
$products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$categories = $mysqli->query('SELECT * FROM categories WHERE parent_id = 0 AND is_active = 1 ORDER BY sort_order, name')->fetch_all(MYSQLI_ASSOC);

$pageTitle = 'Products — ATELIER Admin';
include dirname(__DIR__) . '/includes/header.php';
?>

<div class="admin-content">
  <div class="admin-page-header">
    <h1>Products</h1>
    <div class="admin-actions">
      <div class="search-box">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
        <input type="text" placeholder="Search products..." id="searchInput" value="<?= sanitize($search) ?>">
      </div>
      <select class="filter-select" id="categoryFilter" style="min-width: 180px;">
        <option value="">All Categories</option>
        <?php foreach ($categories as $cat): ?>
          <option value="<?= $cat['id'] ?>" <?= $categoryFilter == $cat['id'] ? 'selected' : '' ?>><?= sanitize($cat['name']) ?></option>
        <?php endforeach; ?>
      </select>
      <a href="/admin/products/add.php" class="btn btn-primary">+ Add Product</a>
    </div>
  </div>

  <div class="admin-card">
    <div class="table-wrap">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Product</th>
            <th>SKU</th>
            <th>Category</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($products)): ?>
            <tr><td colspan="7" style="text-align: center; padding: 60px; color: var(--color-text-tertiary);">No products found.</td></tr>
          <?php else: ?>
            <?php foreach ($products as $product): ?>
              <?php
                $stockResult = $mysqli->query("SELECT SUM(stock) as total_stock FROM product_sizes WHERE product_id = {$product['id']}");
                $totalStock = $stockResult->fetch_assoc()['total_stock'] ?? 0;
              ?>
              <tr>
                <td>
                  <div style="display: flex; align-items: center; gap: 12px;">
                    <img src="<?= $product['image'] ?? '/assets/images/placeholder.jpg' ?>" alt="" style="width: 48px; height: 64px; object-fit: cover; border-radius: var(--radius-sm); background: var(--color-bg-elevated);">
                    <div>
                      <div style="font-weight: 600; color: var(--color-text-primary);"><?= sanitize($product['name']) ?></div>
                      <div style="font-size: var(--text-caption); color: var(--color-text-tertiary);"><?= sanitize($product['brand']) ?></div>
                    </div>
                  </div>
                </td>
                <td><?= sanitize($product['sku'] ?? 'N/A') ?></td>
                <td><?= sanitize($product['category_name'] ?? 'N/A') ?></td>
                <td>
                  <div><?= formatPrice($product['price']) ?></div>
                  <?php if ($product['discount_percent'] > 0): ?>
                    <div style="font-size: var(--text-caption); color: var(--color-accent-secondary);">-<?= $product['discount_percent'] ?>%</div>
                  <?php endif; ?>
                </td>
                <td>
                  <span class="status-badge <?= $totalStock > 0 ? 'status-active' : 'status-inactive' ?>">
                    <?= $totalStock > 0 ? number_format($totalStock) . ' in stock' : 'Out of stock' ?>
                  </span>
                </td>
                <td><span class="status-badge <?= $product['is_active'] ? 'status-active' : 'status-inactive' ?>"><?= $product['is_active'] ? 'Active' : 'Inactive' ?></span></td>
                <td>
                  <div style="display: flex; gap: 8px;">
                    <a href="/admin/products/edit.php?id=<?= $product['id'] ?>" class="btn btn-secondary btn-sm">Edit</a>
                    <a href="/admin/products/delete.php?id=<?= $product['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
    <?php if ($totalPages > 1): ?>
      <div style="padding: var(--space-4) var(--space-6); display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--color-bg-elevated);">
        <span style="font-size: var(--text-body-sm); color: var(--color-text-tertiary);">Page <?= $page ?> of <?= $totalPages ?></span>
        <div style="display: flex; gap: 8px;">
          <?php if ($page > 1): ?>
            <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&category=<?= $categoryFilter ?>" class="btn btn-secondary btn-sm">Previous</a>
          <?php endif; ?>
          <?php if ($page < $totalPages): ?>
            <a href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&category=<?= $categoryFilter ?>" class="btn btn-secondary btn-sm">Next</a>
          <?php endif; ?>
        </div>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>

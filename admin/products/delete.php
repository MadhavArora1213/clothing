<?php
require_once dirname(__DIR__, 2) . '/config/database.php';
requireAdminAuth();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
  redirect(adminUrl('products/'));
}

$stmt = $mysqli->prepare('SELECT * FROM products WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
  redirect(adminUrl('products/'));
}

// Function to delete product and associated records
function deleteProductCompletely($mysqli, $productId) {
  // Delete related records
  $mysqli->query("DELETE FROM product_images WHERE product_id = $productId");
  $mysqli->query("DELETE FROM product_colors WHERE product_id = $productId");
  $mysqli->query("DELETE FROM product_sizes WHERE product_id = $productId");
  $mysqli->query("DELETE FROM reviews WHERE product_id = $productId");
  
  // Delete main product
  $stmt = $mysqli->prepare("DELETE FROM products WHERE id = ?");
  $stmt->bind_param('i', $productId);
  return $stmt->execute();
}

// Support instant confirmation via URL or Form POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['confirm'])) {
  deleteProductCompletely($mysqli, $id);
  redirect(adminUrl('products/?msg=Product+deleted+successfully'));
}

$pageTitle = 'Delete Product — urban outfit Admin';
include dirname(__DIR__) . '/includes/header.php';
?>

<div class="admin-content">
  <div class="page-header">
    <h1>Delete Product Confirmation</h1>
  </div>
  <div class="admin-card" style="max-width: 600px; margin: 0 auto; padding: var(--space-8);">
    <div style="text-align: center; margin-bottom: var(--space-6);">
      <div style="width: 64px; height: 64px; border-radius: 50%; background: #FEE2E2; color: #DC2626; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
      </div>
      <h2 style="font-size: 20px; font-weight: 700; margin-bottom: 8px;">Delete "<?= sanitize($product['name']) ?>"?</h2>
      <p style="color: var(--color-text-secondary); font-size: 14px;">
        Are you sure you want to permanently delete this product along with its images, color options, and size inventory? This action cannot be undone.
      </p>
    </div>

    <form method="POST" style="display: flex; gap: 12px; justify-content: center;">
      <a href="<?= adminUrl('products/') ?>" class="btn btn-secondary">No, Cancel</a>
      <button type="submit" class="btn btn-danger" style="background: #DC2626; color: white;">Yes, Delete Product</button>
    </form>
  </div>
</div>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>

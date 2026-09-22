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

// Count existing order items for this product
$chkOrder = $mysqli->prepare('SELECT COUNT(*) AS cnt FROM order_items WHERE product_id = ?');
$chkOrder->bind_param('i', $id);
$chkOrder->execute();
$orderCount = (int)($chkOrder->get_result()->fetch_assoc()['cnt'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
    redirect(adminUrl('products/?msg=Invalid+request'));
  }

  $force = !empty($_POST['force']);

  // Product has existing orders and user hasn't confirmed force delete yet → show warning
  if ($orderCount > 0 && !$force) {
    redirect(adminUrl('products/delete.php?id=' . $id . '&warn=orders'));
  }

  // Delete related records first
  $delCartItems = $mysqli->prepare('DELETE FROM cart_items WHERE product_id = ?');
  $delCartItems->bind_param('i', $id);
  $delCartItems->execute();

  $delWish = $mysqli->prepare('DELETE FROM wishlists WHERE product_id = ?');
  $delWish->bind_param('i', $id);
  $delWish->execute();

  $delColors = $mysqli->prepare('DELETE FROM product_colors WHERE product_id = ?');
  $delColors->bind_param('i', $id);
  $delColors->execute();

  $delSizes = $mysqli->prepare('DELETE FROM product_sizes WHERE product_id = ?');
  $delSizes->bind_param('i', $id);
  $delSizes->execute();

  $delImages = $mysqli->prepare('DELETE FROM product_images WHERE product_id = ?');
  $delImages->bind_param('i', $id);
  $delImages->execute();

  // Force delete with existing orders → temporarily lift FK restriction.
  // order_items rows are kept (product_name/sku/price already snapshotted),
  // so order history stays intact; only the product link becomes dangling.
  if ($orderCount > 0) {
    $mysqli->query('SET FOREIGN_KEY_CHECKS=0');
  }

  $stmt = $mysqli->prepare('DELETE FROM products WHERE id = ?');
  $stmt->bind_param('i', $id);
  $deleted = $stmt->execute();

  if ($orderCount > 0) {
    $mysqli->query('SET FOREIGN_KEY_CHECKS=1');
  }

  if ($deleted) {
    $msg = $orderCount > 0
      ? 'Product+deleted+(forced)+—+order+history+is+preserved'
      : 'Product+deleted+successfully';
    redirect(adminUrl('products/?msg=' . $msg));
  }

  redirect(adminUrl('products/?msg=Cannot+delete+product'));
}

$showOrderWarning = ($orderCount > 0) && (($_GET['warn'] ?? '') === 'orders');

$pageTitle = 'Delete Product — Urban Outfit Admin';
include dirname(__DIR__) . '/includes/header.php';
?>

<div class="admin-content">
  <div class="page-header">
    <h1>Delete Product</h1>
  </div>
  <div class="admin-card" style="max-width: 600px; margin: 0 auto; padding: 40px;">
    <div style="text-align: center; margin-bottom: 24px;">
      <div style="width: 64px; height: 64px; border-radius: 50%; background: <?= $showOrderWarning ? '#FEF3C7' : '#FEE2E2' ?>; color: <?= $showOrderWarning ? '#D97706' : '#DC2626' ?>; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <?php if ($showOrderWarning): ?>
            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
          <?php else: ?>
            <polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/>
          <?php endif; ?>
        </svg>
      </div>
      <h2 style="font-size: 20px; font-weight: 700; margin-bottom: 8px;">Delete "<?= esc($product['name']) ?>"?</h2>

      <?php if ($showOrderWarning): ?>
        <div style="background: #FFFBEB; border: 1px solid #FDE68A; color: #92400E; padding: 14px 16px; border-radius: 8px; font-size: 13px; line-height: 1.6; text-align: left; margin-bottom: 16px;">
          <strong style="display: block; margin-bottom: 6px; font-size: 14px;">⚠ Warning: This product has <?= $orderCount ?> existing order<?= $orderCount !== 1 ? 's' : '' ?>.</strong>
          Force deleting will permanently remove this product from the catalog.
          <ul style="margin: 8px 0 0 18px; padding: 0;">
            <li>Order history will be <strong>preserved</strong> (item name, price &amp; qty are saved in each order).</li>
            <li>The product link in those orders will be detached — customers won't see it in re-order / product pages.</li>
            <li>This action <strong>cannot be undone</strong>.</li>
          </ul>
        </div>
        <p style="color: #666; font-size: 14px;">
          Are you sure you still want to force delete this product?
        </p>
      <?php else: ?>
        <p style="color: #666; font-size: 14px;">
          This product will be permanently deleted from the database. This action cannot be undone.
        </p>
      <?php endif; ?>
    </div>

    <?php if ($showOrderWarning): ?>
      <form method="POST" style="display: flex; gap: 12px; justify-content: center;">
        <?= getCSRFInput() ?>
        <input type="hidden" name="force" value="1">
        <a href="<?= adminUrl('products/') ?>" style="padding: 10px 24px; background: #f5f5f5; border: 1px solid #ddd; border-radius: 8px; font-size: 13px; font-weight: 600; text-decoration: none; color: #333;">No, Keep Product</a>
        <button type="submit" style="padding: 10px 24px; background: #DC2626; color: white; border: none; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer;" onclick="return confirm('Final confirmation: force delete this product despite <?= $orderCount ?> existing order(s)?');">
          Yes, Force Delete
        </button>
      </form>
    <?php else: ?>
      <form method="POST" style="display: flex; gap: 12px; justify-content: center;">
        <?= getCSRFInput() ?>
        <a href="<?= adminUrl('products/') ?>" style="padding: 10px 24px; background: #f5f5f5; border: 1px solid #ddd; border-radius: 8px; font-size: 13px; font-weight: 600; text-decoration: none; color: #333;">No, Cancel</a>
        <button type="submit" style="padding: 10px 24px; background: #DC2626; color: white; border: none; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer;">Yes, Delete</button>
      </form>
    <?php endif; ?>
  </div>
</div>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>

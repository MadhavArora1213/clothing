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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
    redirect(adminUrl('products/?msg=Invalid+request'));
  }

  // Delete related records first
  $delColors = $mysqli->prepare('DELETE FROM product_colors WHERE product_id = ?');
  $delColors->bind_param('i', $id);
  $delColors->execute();

  $delSizes = $mysqli->prepare('DELETE FROM product_sizes WHERE product_id = ?');
  $delSizes->bind_param('i', $id);
  $delSizes->execute();

  $delImages = $mysqli->prepare('DELETE FROM product_images WHERE product_id = ?');
  $delImages->bind_param('i', $id);
  $delImages->execute();

  // Hard delete — actually remove product from database
  $stmt = $mysqli->prepare('DELETE FROM products WHERE id = ?');
  $stmt->bind_param('i', $id);
  $stmt->execute();

  redirect(adminUrl('products/?msg=Product+deleted+successfully'));
}

$pageTitle = 'Delete Product — Urban Outfit Admin';
include dirname(__DIR__) . '/includes/header.php';
?>

<div class="admin-content">
  <div class="page-header">
    <h1>Delete Product</h1>
  </div>
  <div class="admin-card" style="max-width: 600px; margin: 0 auto; padding: 40px;">
    <div style="text-align: center; margin-bottom: 24px;">
      <div style="width: 64px; height: 64px; border-radius: 50%; background: #FEE2E2; color: #DC2626; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
      </div>
      <h2 style="font-size: 20px; font-weight: 700; margin-bottom: 8px;">Delete "<?= esc($product['name']) ?>"?</h2>
      <p style="color: #666; font-size: 14px;">
        This product will be permanently deleted from the database. This action cannot be undone.
      </p>
    </div>

    <form method="POST" style="display: flex; gap: 12px; justify-content: center;">
      <?= getCSRFInput() ?>
      <a href="<?= adminUrl('products/') ?>" style="padding: 10px 24px; background: #f5f5f5; border: 1px solid #ddd; border-radius: 8px; font-size: 13px; font-weight: 600; text-decoration: none; color: #333;">No, Cancel</a>
      <button type="submit" style="padding: 10px 24px; background: #DC2626; color: white; border: none; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer;">Yes, Delete</button>
    </form>
  </div>
</div>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>

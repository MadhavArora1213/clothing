<?php
require_once dirname(__DIR__) . '/config/database.php';
requireAdminAuth();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) redirect('/admin/products/');

$stmt = $mysqli->prepare('SELECT * FROM products WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
  redirect('/admin/products/');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $stmt = $mysqli->prepare('DELETE FROM products WHERE id = ?');
  $stmt->bind_param('i', $id);
  $stmt->execute();
  redirect('/admin/products/');
}

include dirname(__DIR__) . '/includes/header.php';
?>

<div class="admin-content">
  <div class="page-header">
    <h1>Delete Product</h1>
  </div>
  <div class="admin-form-page">
    <p style="margin-bottom: var(--space-6); color: var(--color-text-secondary);">Are you sure you want to delete <strong><?= sanitize($product['name']) ?></strong>? This action cannot be undone.</p>
    <form method="POST">
      <div class="form-actions">
        <a href="/admin/products/" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-danger">Delete Product</button>
      </div>
    </form>
  </div>
</div>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>

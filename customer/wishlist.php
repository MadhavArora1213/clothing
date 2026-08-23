<?php
require_once dirname(__DIR__) . '/config/database.php';

if (!isset($_SESSION['customer_id'])) {
  redirect('/customer/login.php?redirect=' . urlencode('/customer/wishlist.php'));
}

$customerId = $_SESSION['customer_id'];
$pageTitle = 'My Wishlist';
include dirname(__DIR__) . '/includes/header.php';

$stmt = $mysqli->prepare("SELECT p.*, (SELECT image_url FROM product_images WHERE product_id = p.id ORDER BY is_primary DESC, sort_order LIMIT 1) as image, w.created_at as added_at FROM wishlists w JOIN products p ON w.product_id = p.id WHERE w.customer_id = ? AND p.is_active = 1 ORDER BY w.created_at DESC");
$stmt->bind_param('i', $customerId);
$stmt->execute();
$items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_wishlist'])) {
  $removeId = (int)$_POST['remove_wishlist'];
  $del = $mysqli->prepare('DELETE FROM wishlists WHERE id = ? AND customer_id = ?');
  $del->bind_param('ii', $removeId, $customerId);
  $del->execute();
  redirect('/customer/wishlist.php');
}
?>

<main class="page-shell">
  <div class="container">
    <div class="page-hero reveal-up">
      <img src="https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=1800&h=900&fit=crop" alt="Wishlist banner" loading="eager">
      <div class="page-hero-content">
        <h1>My Wishlist</h1>
        <p><?= count($items) ?> item<?= count($items) !== 1 ? 's' : '' ?> saved.</p>
      </div>
    </div>

    <?php if (empty($items)): ?>
      <div style="text-align:center;padding:80px 0;color:var(--color-text-tertiary);">
        <p style="font-size:18px;margin-bottom:20px;">Your wishlist is empty.</p>
        <a href="<?= BASE_URL ?>/shop.php" class="btn btn-primary">Browse Products</a>
      </div>
    <?php else: ?>
      <div class="arya-product-grid">
        <?php foreach ($items as $item): ?>
          <div class="arya-product-card" style="position:relative;">
            <a href="<?= BASE_URL ?>/product.php?slug=<?= $item['slug'] ?>" class="arya-product-link">
              <div class="arya-product-media">
                <?php if ($item['discount_percent'] > 0): ?>
                  <span class="arya-sale-badge">-<?= $item['discount_percent'] ?>%</span>
                <?php endif; ?>
                <img src="<?= $item['image'] ?>" alt="<?= htmlspecialchars($item['name']) ?>" loading="lazy">
              </div>
            </a>
            <div class="arya-product-title"><a href="<?= BASE_URL ?>/product.php?slug=<?= $item['slug'] ?>"><?= htmlspecialchars($item['name']) ?></a></div>
            <div class="arya-product-price">
              <span class="arya-price-current">₹<?= number_format($item['price']) ?></span>
              <?php if ($item['original_price']): ?>
                <span class="arya-price-original">₹<?= number_format($item['original_price']) ?></span>
              <?php endif; ?>
            </div>
            <form method="POST" style="margin-top:8px;">
              <input type="hidden" name="remove_wishlist" value="<?= $item['id'] ?>">
              <button type="submit" class="btn btn-secondary btn-sm" style="width:100%;font-size:12px;" onclick="return confirm('Remove from wishlist?')">Remove</button>
            </form>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</main>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>

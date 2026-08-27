<?php
require_once dirname(__DIR__) . '/config/database.php';

$pageTitle = 'Shopping Cart — urban outfit';
$pageDescription = 'Review your cart items.';
include dirname(__DIR__) . '/includes/header.php';

$customerId = $_SESSION['customer_id'] ?? null;
$sessionId = session_id();

$cart = null;
if ($customerId) {
  $stmt = $mysqli->prepare('SELECT id FROM carts WHERE customer_id = ?');
  $stmt->bind_param('i', $customerId);
  $stmt->execute();
  $cart = $stmt->get_result()->fetch_assoc();
} else {
  $stmt = $mysqli->prepare('SELECT id FROM carts WHERE session_id = ?');
  $stmt->bind_param('s', $sessionId);
  $stmt->execute();
  $cart = $stmt->get_result()->fetch_assoc();
}

if (!$cart) {
  $stmt = $mysqli->prepare('INSERT INTO carts (customer_id, session_id) VALUES (?, ?)');
  $customerIdParam = $customerId;
  $stmt->bind_param('is', $customerIdParam, $sessionId);
  $stmt->execute();
  $cartId = $mysqli->insert_id;
} else {
  $cartId = $cart['id'];
}

$items = $mysqli->query("SELECT ci.*, p.name, p.price, p.image, p.slug FROM cart_items ci JOIN products p ON ci.product_id = p.id WHERE ci.cart_id = $cartId")->fetch_all(MYSQLI_ASSOC);

$subtotal = 0;
foreach ($items as $item) {
  $subtotal += $item['unit_price'] * $item['quantity'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';
  $itemId = (int)($_POST['item_id'] ?? 0);

  if ($action === 'remove' && $itemId > 0) {
    $stmt = $mysqli->prepare('DELETE FROM cart_items WHERE id = ? AND cart_id = ?');
    $stmt->bind_param('ii', $itemId, $cartId);
    $stmt->execute();
    redirect('/customer/cart.php');
  } elseif ($action === 'update' && $itemId > 0) {
    $qty = max(1, (int)($_POST['quantity'] ?? 1));
    $stmt = $mysqli->prepare('UPDATE cart_items SET quantity = ? WHERE id = ? AND cart_id = ?');
    $stmt->bind_param('iii', $qty, $itemId, $cartId);
    $stmt->execute();
    redirect('/customer/cart.php');
  }
}
?>

<main class="cart-page">
  <div class="aura-container">
    <div class="cart-header reveal">
      <div>
        <div class="lux-eyebrow">Your Selection</div>
        <h1 class="lux-section-title" style="margin-bottom: 4px;">Shopping Cart</h1>
        <p class="lux-section-sub" style="margin-top: 0;"><?= count($items) ?> item<?= count($items) !== 1 ? 's' : '' ?> ready for checkout</p>
      </div>
    </div>

    <?php if (empty($items)): ?>
      <div class="cart-empty reveal" style="text-align: center; padding: 100px 0;">
        <div class="cart-empty-icon">
          <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
        </div>
        <h3 style="font-family: var(--font-display); font-size: 28px; margin-bottom: 8px; color: var(--color-text-main);">Your cart is empty</h3>
        <p style="color: var(--color-text-muted); margin-bottom: 32px; font-size: 14px;">Looks like you haven't added anything to your cart yet.</p>
        <a href="/shop.php" class="lux-btn-primary" style="display: inline-flex; text-decoration: none;">
          <span>Continue Shopping</span>
        </a>
      </div>
    <?php else: ?>
      <div class="cart-layout">
        <div class="cart-items-col">
          <?php foreach ($items as $index => $item): ?>
            <?php
              $itemTotal = $item['unit_price'] * $item['quantity'];
              $img = !empty($item['image']) ? $item['image'] : 'https://via.placeholder.com/200x260?text=No+Image';
            ?>
            <div class="cart-item-card reveal" style="animation-delay: <?= 0.05 * $index ?>s;">
              <div class="cart-item-img">
                <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($item['name']) ?>" loading="lazy">
              </div>
              <div class="cart-item-body">
                <div class="cart-item-top">
                  <div>
                    <a href="/product.php?slug=<?= $item['slug'] ?>" class="cart-item-name"><?= sanitize($item['name']) ?></a>
                    <?php if ($item['size']): ?>
                      <div class="cart-item-meta">Size: <strong><?= sanitize($item['size']) ?></strong></div>
                    <?php endif; ?>
                  </div>
                  <div class="cart-item-price"><?= formatPrice($item['unit_price']) ?></div>
                </div>

                <div class="cart-item-bottom">
                  <form method="POST" class="qty-form">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                    <button type="button" class="qty-btn" data-action="dec" data-id="<?= $item['id'] ?>" data-current="<?= (int)$item['quantity'] ?>">&minus;</button>
                    <input type="number" name="quantity" value="<?= (int)$item['quantity'] ?>" min="1" class="qty-input" data-id="<?= $item['id'] ?>">
                    <button type="button" class="qty-btn" data-action="inc" data-id="<?= $item['id'] ?>" data-current="<?= (int)$item['quantity'] ?>">+</button>
                  </form>
                  <form method="POST" class="remove-form" onsubmit="return confirm('Remove this item?')">
                    <input type="hidden" name="action" value="remove">
                    <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                    <button type="submit" class="remove-btn" aria-label="Remove item">
                      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                    </button>
                  </form>
                </div>
              </div>
              <div class="cart-item-total">
                <span class="cart-total-label">Total</span>
                <span class="cart-total-value"><?= formatPrice($itemTotal) ?></span>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <div class="cart-summary-col">
          <div class="cart-summary-card reveal" style="animation-delay: 0.3s;">
            <h3 class="cart-summary-title">Order Summary</h3>
            <div class="cart-summary-rows">
              <div class="cart-summary-row">
                <span>Subtotal (<?= count($items) ?> item<?= count($items) !== 1 ? 's' : '' ?>)</span>
                <span><?= formatPrice($subtotal) ?></span>
              </div>
              <div class="cart-summary-row">
                <span>Shipping</span>
                <span class="text-muted">Free</span>
              </div>
            </div>

            <div style="margin: 16px 0; padding: 14px; background: var(--color-accent-light); border-radius: var(--radius-sm); display: flex; align-items: center; gap: 10px;">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--color-accent)" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
              <span style="font-size: 13px; font-weight: 600; color: var(--color-text-main);">Free shipping on all prepaid orders</span>
            </div>

            <div class="cart-summary-divider"></div>
            <div class="cart-summary-total">
              <span>Estimated Total</span>
              <span><?= formatPrice($subtotal) ?></span>
            </div>
            <a href="/customer/checkout.php" class="lux-btn-primary" style="display: flex; width: 100%; text-decoration: none; justify-content: center; margin-top: 20px;">
              <span>Proceed to Checkout</span>
            </a>
            <a href="/shop.php" class="lux-view-all" style="display: flex; width: 100%; justify-content: center; margin-top: 14px; text-decoration: none;">
              Continue Shopping
            </a>
          </div>

          <div class="cart-trust reveal" style="animation-delay: 0.4s;">
            <div class="trust-item">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
              <span>Secure Checkout</span>
            </div>
            <div class="trust-item">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 0 1-9 9.75 9.75 9.75 0 0 1-6.74-2.74L3 16"/><path d="M21 3v5h-5"/></svg>
              <span>7-Day Easy Returns</span>
            </div>
            <div class="trust-item">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
              <span>Free Express Shipping over ₹999</span>
            </div>
          </div>
        </div>
      </div>
    <?php endif; ?>
  </div>
</main>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>

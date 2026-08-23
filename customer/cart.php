<?php
require_once dirname(__DIR__) . '/config/database.php';

$pageTitle = 'Shopping Cart — ATELIER';
$pageDescription = 'Review your cart items.';
include dirname(__DIR__) . '/includes/header.php';

$customerId = $_SESSION['customer_id'] ?? null;
$sessionId = session_id();

// Get or create cart
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

<main style="padding-top: calc(var(--header-height) + var(--space-16)); padding-bottom: var(--space-16);">
  <div class="container">
    <div class="section-header">
      <h1 class="section-title">Shopping Cart</h1>
      <p class="section-subtitle"><?= count($items) ?> item<?= count($items) !== 1 ? 's' : '' ?> in your cart</p>
    </div>

    <?php if (empty($items)): ?>
      <div class="text-center" style="padding: 80px 0; color: var(--color-text-tertiary);">
        <p style="font-size: 18px; margin-bottom: var(--space-6);">Your cart is empty.</p>
        <a href="/shop.php" class="btn btn-primary">Continue Shopping</a>
      </div>
    <?php else: ?>
      <div class="admin-grid">
        <div class="admin-card">
          <div class="table-wrap">
            <table class="admin-table">
              <thead>
                <tr>
                  <th>Product</th>
                  <th>Price</th>
                  <th>Quantity</th>
                  <th>Total</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($items as $item): ?>
                  <tr>
                    <td>
                      <div style="display: flex; align-items: center; gap: 12px;">
                        <img src="<?= $item['image'] ?>" alt="" style="width: 64px; height: 80px; object-fit: cover; border-radius: var(--radius-sm); background: var(--color-bg-elevated);">
                        <div>
                          <a href="/product.php?slug=<?= $item['slug'] ?>" style="font-weight: 600; color: var(--color-text-primary);"><?= sanitize($item['name']) ?></a>
                          <?php if ($item['size']): ?>
                            <div style="font-size: var(--text-caption); color: var(--color-text-tertiary);">Size: <?= sanitize($item['size']) ?></div>
                          <?php endif; ?>
                        </div>
                      </div>
                    </td>
                    <td><?= formatPrice($item['unit_price']) ?></td>
                    <td>
                      <form method="POST" style="display: inline-flex; align-items: center; gap: 0;">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                        <button type="submit" name="quantity" value="<?= max(1, $item['quantity'] - 1) ?>" class="qty-btn" style="border: 1px solid var(--color-accent-tertiary); border-radius: var(--radius-sm);">&minus;</button>
                        <span style="width: 40px; text-align: center; font-weight: 600;"><?= (int)$item['quantity'] ?></span>
                        <button type="submit" name="quantity" value="<?= $item['quantity'] + 1 ?>" class="qty-btn" style="border: 1px solid var(--color-accent-tertiary); border-radius: var(--radius-sm);">+</button>
                      </form>
                    </td>
                    <td style="font-weight: 600;"><?= formatPrice($item['unit_price'] * $item['quantity']) ?></td>
                    <td>
                      <form method="POST">
                        <input type="hidden" name="action" value="remove">
                        <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Remove this item?')">&times;</button>
                      </form>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>

        <div>
          <div class="admin-card" style="margin-bottom: var(--space-6);">
            <div class="admin-card-header"><h2>Cart Summary</h2></div>
            <div style="padding: var(--space-6);">
              <div style="display: flex; justify-content: space-between; margin-bottom: var(--space-3);">
                <span>Subtotal</span>
                <span style="font-weight: 600;"><?= formatPrice($subtotal) ?></span>
              </div>
              <div style="display: flex; justify-content: space-between; margin-bottom: var(--space-3); color: var(--color-text-tertiary);">
                <span>Shipping</span>
                <span>Calculated at checkout</span>
              </div>
              <div style="border-top: 1px solid var(--color-accent-tertiary); padding-top: var(--space-4); display: flex; justify-content: space-between; font-size: var(--text-h4); font-weight: 700;">
                <span>Total</span>
                <span><?= formatPrice($subtotal) ?></span>
              </div>
              <a href="/customer/checkout.php" class="btn btn-primary" style="width: 100%; margin-top: var(--space-6); padding: var(--space-4);">Proceed to Checkout</a>
              <a href="/shop.php" class="btn btn-secondary" style="width: 100%; margin-top: var(--space-3);">Continue Shopping</a>
            </div>
          </div>
        </div>
      </div>
    <?php endif; ?>
  </div>
</main>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>

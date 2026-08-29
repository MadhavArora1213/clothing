<?php
require_once dirname(__DIR__) . '/config/database.php';

$pageTitle = 'Shopping Cart — ATELIER';
$pageDescription = 'Review your cart items.';

$customerId = $_SESSION['customer_id'] ?? null;
$sessionId = session_id();

$cart = null;
if ($customerId) {
  $stmt = $mysqli->prepare('SELECT id FROM carts WHERE customer_id = ?');
  if ($stmt) {
    $stmt->bind_param('i', $customerId);
    $stmt->execute();
    $cart = $stmt->get_result()->fetch_assoc();
  }
} else {
  $stmt = $mysqli->prepare('SELECT id FROM carts WHERE session_id = ?');
  if ($stmt) {
    $stmt->bind_param('s', $sessionId);
    $stmt->execute();
    $cart = $stmt->get_result()->fetch_assoc();
  }
}

if (!$cart) {
  $stmt = $mysqli->prepare('INSERT INTO carts (customer_id, session_id) VALUES (?, ?)');
  if ($stmt) {
    $customerIdParam = $customerId;
    $stmt->bind_param('is', $customerIdParam, $sessionId);
    $stmt->execute();
    $cartId = $mysqli->insert_id;
  } else {
    $cartId = 0;
  }
} else {
  $cartId = $cart['id'];
}

$items = [];
if ($cartId > 0) {
  $itemsResult = $mysqli->query("SELECT ci.*, p.name, p.price, p.image, p.slug FROM cart_items ci JOIN products p ON ci.product_id = p.id WHERE ci.cart_id = $cartId");
  $items = $itemsResult ? $itemsResult->fetch_all(MYSQLI_ASSOC) : [];
}

$subtotal = 0;
foreach ($items as $item) {
  $subtotal += $item['unit_price'] * $item['quantity'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';
  $itemId = (int)($_POST['item_id'] ?? 0);

  if ($action === 'remove' && $itemId > 0) {
    $stmt = $mysqli->prepare('DELETE FROM cart_items WHERE id = ? AND cart_id = ?');
    if ($stmt) {
      $stmt->bind_param('ii', $itemId, $cartId);
      $stmt->execute();
    }
    redirect('/customer/cart.php');
  } elseif ($action === 'update' && $itemId > 0) {
    $qty = max(1, (int)($_POST['quantity'] ?? 1));
    $stmt = $mysqli->prepare('UPDATE cart_items SET quantity = ? WHERE id = ? AND cart_id = ?');
    if ($stmt) {
      $stmt->bind_param('iii', $qty, $itemId, $cartId);
      $stmt->execute();
    }
    redirect('/customer/cart.php');
  }
}

$suggestedProducts = [];
$suggestResult = $mysqli->query("SELECT name, price, image, slug FROM products WHERE is_active = 1 ORDER BY RAND() LIMIT 2");
if ($suggestResult) {
  $suggestedProducts = $suggestResult->fetch_all(MYSQLI_ASSOC);
}

include dirname(__DIR__) . '/includes/header.php';
?>

<style>
  body { background: var(--color-bg, #FAF9F6); }

  /* ── Page Container ── */
  .cart-page {
    max-width: 1100px;
    margin: 0 auto;
    padding: calc(var(--header-height) + 32px) 24px 80px;
  }

  /* ── Breadcrumb ── */
  .cart-crumb {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 12px;
    font-weight: 500;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    color: var(--color-text-tertiary, #9A8E7E);
    margin-bottom: 12px;
  }
  .cart-crumb a {
    color: var(--color-text-tertiary, #9A8E7E);
    text-decoration: none;
    transition: color 0.2s;
  }
  .cart-crumb a:hover { color: var(--color-accent, #D4AF37); }
  .cart-crumb .sep { opacity: 0.4; }
  .cart-crumb .now { color: var(--color-accent, #D4AF37); font-weight: 600; }

  /* ── Page Header ── */
  .cart-head {
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    margin-bottom: 40px;
    padding-bottom: 20px;
    border-bottom: 1px solid var(--color-border, #E8E2D8);
  }
  .cart-head h1 {
    font-family: var(--font-display, 'Playfair Display');
    font-size: clamp(28px, 3.5vw, 38px);
    font-weight: 400;
    font-style: italic;
    color: var(--color-text-primary, #1a1a1a);
    letter-spacing: -0.01em;
  }
  .cart-head .count {
    font-size: 13px;
    font-weight: 500;
    color: var(--color-text-tertiary, #9A8E7E);
    background: var(--color-bg, #FAF9F6);
    border: 1px solid var(--color-border, #E8E2D8);
    padding: 6px 14px;
    border-radius: 999px;
  }

  /* ── Two-Column Layout ── */
  .cart-grid {
    display: grid;
    grid-template-columns: 1fr 380px;
    gap: 48px;
    align-items: start;
  }

  /* ── Items List ── */
  .cart-items { list-style: none; margin: 0; padding: 0; }

  .cart-item {
    display: grid;
    grid-template-columns: 90px 1fr auto;
    gap: 20px;
    align-items: center;
    padding: 24px 0;
    border-bottom: 1px solid var(--color-border, #E8E2D8);
    position: relative;
  }
  .cart-item:first-child { padding-top: 0; }
  .cart-item:last-child { border-bottom: none; }

  /* Thumbnail */
  .ci-img {
    width: 90px;
    height: 115px;
    border-radius: 10px;
    overflow: hidden;
    background: var(--color-surface-alt, #F0ECE4);
    flex-shrink: 0;
  }
  .ci-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
  }
  .ci-img:hover img { transform: scale(1.05); }

  /* Details */
  .ci-details { min-width: 0; }
  .ci-details h3 {
    font-family: var(--font-display, 'Playfair Display');
    font-size: 16px;
    font-weight: 600;
    color: var(--color-text-primary, #1a1a1a);
    margin-bottom: 4px;
    line-height: 1.35;
  }
  .ci-details h3 a {
    color: inherit;
    text-decoration: none;
    transition: color 0.2s;
  }
  .ci-details h3 a:hover { color: var(--color-accent, #D4AF37); }

  .ci-meta {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-top: 6px;
    font-size: 12px;
    color: var(--color-text-tertiary, #9A8E7E);
  }
  .ci-meta span {
    display: flex;
    align-items: center;
    gap: 4px;
  }
  .ci-meta .dot {
    width: 3px;
    height: 3px;
    border-radius: 50%;
    background: var(--color-border, #D6CEC4);
  }

  .ci-remove {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    margin-top: 10px;
    font-size: 12px;
    font-weight: 500;
    color: var(--color-text-tertiary, #9A8E7E);
    background: none;
    border: none;
    cursor: pointer;
    padding: 0;
    font-family: var(--font-body, 'Plus Jakarta Sans');
    transition: color 0.2s;
    text-decoration: underline;
    text-underline-offset: 3px;
  }
  .ci-remove:hover { color: #C0392B; }
  .ci-remove svg { width: 14px; height: 14px; }

  /* Right Column: Price / Qty / Total */
  .ci-right {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 14px;
    text-align: right;
  }
  .ci-price {
    font-size: 14px;
    font-weight: 500;
    color: var(--color-text-secondary, #5C5347);
  }

  /* Quantity Stepper */
  .ci-qty {
    display: flex;
    align-items: center;
    border: 1px solid var(--color-border, #E8E2D8);
    border-radius: 8px;
    overflow: hidden;
    background: #fff;
  }
  .ci-qty button {
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: none;
    border: none;
    font-size: 15px;
    color: var(--color-text-secondary, #5C5347);
    cursor: pointer;
    transition: background 0.15s;
  }
  .ci-qty button:hover { background: var(--color-surface-alt, #F5F0E8); }
  .ci-qty .qty-num {
    width: 40px;
    text-align: center;
    font-size: 13px;
    font-weight: 600;
    color: var(--color-text-primary, #1a1a1a);
    border: none;
    background: none;
    font-family: var(--font-body, 'Plus Jakarta Sans');
  }
  .ci-qty .qty-num::-webkit-inner-spin-button,
  .ci-qty .qty-num::-webkit-outer-spin-button { -webkit-appearance: none; }
  .ci-qty .qty-num { -moz-appearance: textfield; }

  .ci-total {
    font-family: var(--font-display, 'Playfair Display');
    font-size: 17px;
    font-weight: 700;
    color: var(--color-text-primary, #1a1a1a);
  }

  /* ── Summary Panel ── */
  .cart-summary {
    position: sticky;
    top: calc(var(--header-height) + 24px);
    border-radius: 16px;
    overflow: hidden;
    background: var(--color-surface, #FFFFFF);
    border: 1px solid var(--color-border, #E8E2D8);
    box-shadow: 0 2px 16px rgba(0,0,0,0.04);
  }

  /* Summary Header Banner */
  .summary-banner {
    background: var(--color-accent, #D4AF37);
    padding: 14px 24px;
    display: flex;
    align-items: center;
    gap: 10px;
  }
  .summary-banner svg { width: 18px; height: 18px; stroke: #fff; flex-shrink: 0; }
  .summary-banner span {
    font-size: 12.5px;
    font-weight: 600;
    color: #fff;
    letter-spacing: 0.01em;
  }

  .summary-body { padding: 28px 24px; }

  .summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0;
    font-size: 13px;
  }
  .summary-row .s-label {
    color: var(--color-text-tertiary, #9A8E7E);
    font-weight: 500;
  }
  .summary-row .s-value {
    font-weight: 600;
    color: var(--color-text-primary, #1a1a1a);
  }
  .summary-row .s-free {
    font-size: 12px;
    font-weight: 600;
    color: #27AE60;
    background: rgba(39,174,96,0.08);
    padding: 3px 10px;
    border-radius: 999px;
  }

  .summary-rule {
    height: 1px;
    background: var(--color-border, #E8E2D8);
    margin: 12px 0;
  }

  .summary-total-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 0 4px;
  }
  .summary-total-row .t-label {
    font-size: 14px;
    font-weight: 600;
    color: var(--color-text-primary, #1a1a1a);
  }
  .summary-total-row .t-value {
    font-family: var(--font-display, 'Playfair Display');
    font-size: 22px;
    font-weight: 700;
    color: var(--color-accent, #D4AF37);
  }

  /* Checkout Button */
  .summary-checkout {
    display: flex;
    width: 100%;
    padding: 15px;
    background: var(--color-text-primary, #1a1a1a);
    color: #fff;
    border: none;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    justify-content: center;
    align-items: center;
    gap: 8px;
    margin-top: 20px;
    font-family: var(--font-body, 'Plus Jakarta Sans');
    letter-spacing: 0.02em;
    transition: all 0.25s ease;
    position: relative;
    overflow: hidden;
  }
  .summary-checkout::after {
    content: '';
    position: absolute;
    top: 0; left: -100%; width: 100%; height: 100%;
    background: linear-gradient(90deg, transparent, rgba(212,175,55,0.2), transparent);
    transition: left 0.5s ease;
  }
  .summary-checkout:hover::after { left: 100%; }
  .summary-checkout:hover { background: var(--color-accent, #D4AF37); transform: translateY(-1px); box-shadow: 0 4px 14px rgba(212,175,55,0.3); }
  .summary-checkout svg { width: 16px; height: 16px; }

  .summary-secure {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    margin-top: 14px;
    font-size: 11px;
    color: var(--color-text-tertiary, #9A8E7E);
  }
  .summary-secure svg { width: 12px; height: 12px; }

  /* ── Complete the Look ── */
  .complete-look {
    margin-top: 24px;
    padding-top: 20px;
    border-top: 1px solid var(--color-border, #E8E2D8);
  }
  .complete-look-title {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    font-weight: 600;
    color: var(--color-text-primary, #1a1a1a);
    text-transform: uppercase;
    letter-spacing: 0.06em;
    margin-bottom: 16px;
  }
  .complete-look-title::after {
    content: '';
    flex: 1;
    height: 1px;
    background: var(--color-border, #E8E2D8);
  }

  .look-card {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 12px;
    border-radius: 10px;
    border: 1px solid var(--color-border, #E8E2D8);
    text-decoration: none;
    color: inherit;
    transition: all 0.2s ease;
    margin-bottom: 10px;
  }
  .look-card:last-child { margin-bottom: 0; }
  .look-card:hover {
    border-color: var(--color-accent, #D4AF37);
    box-shadow: 0 2px 8px rgba(212,175,55,0.1);
    transform: translateY(-1px);
  }
  .look-card-img {
    width: 56px;
    height: 70px;
    border-radius: 6px;
    overflow: hidden;
    flex-shrink: 0;
    background: var(--color-surface-alt, #F0ECE4);
  }
  .look-card-img img { width: 100%; height: 100%; object-fit: cover; }
  .look-card-info { min-width: 0; }
  .look-card-info h5 {
    font-size: 12px;
    font-weight: 600;
    color: var(--color-text-primary, #1a1a1a);
    margin-bottom: 4px;
    line-height: 1.35;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }
  .look-card-price {
    font-size: 13px;
    font-weight: 700;
    color: var(--color-accent, #D4AF37);
  }

  /* ── Empty State ── */
  .cart-empty {
    text-align: center;
    padding: 100px 0;
  }
  .cart-empty-icon {
    width: 88px;
    height: 88px;
    border-radius: 50%;
    background: linear-gradient(135deg, rgba(212,175,55,0.08), rgba(212,175,55,0.15));
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 24px;
  }
  .cart-empty-icon svg { width: 40px; height: 40px; stroke: var(--color-accent, #D4AF37); }
  .cart-empty h3 {
    font-family: var(--font-display, 'Playfair Display');
    font-size: 28px;
    font-weight: 400;
    font-style: italic;
    margin-bottom: 8px;
    color: var(--color-text-primary, #1a1a1a);
  }
  .cart-empty p {
    color: var(--color-text-tertiary, #9A8E7E);
    margin-bottom: 32px;
    font-size: 14px;
    max-width: 340px;
    margin-left: auto;
    margin-right: auto;
    line-height: 1.6;
  }
  .cart-empty .btn-shop {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 14px 36px;
    background: var(--color-text-primary, #1a1a1a);
    color: #fff;
    border: none;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.25s ease;
    font-family: var(--font-body, 'Plus Jakarta Sans');
  }
  .cart-empty .btn-shop:hover { background: var(--color-accent, #D4AF37); transform: translateY(-1px); }
  .cart-empty .btn-shop svg { width: 16px; height: 16px; }

  /* ── Mobile ── */
  @media (max-width: 900px) {
    .cart-grid { grid-template-columns: 1fr; gap: 32px; }
    .cart-summary { position: static; }
    .cart-head { flex-direction: column; gap: 8px; }
    .cart-item {
      grid-template-columns: 75px 1fr;
      gap: 14px;
    }
    .ci-right {
      grid-column: 1 / -1;
      flex-direction: row;
      align-items: center;
      justify-content: space-between;
      padding-top: 8px;
      border-top: 1px solid var(--color-border, #E8E2D8);
    }
    .ci-img { width: 75px; height: 95px; }
  }
  @media (max-width: 500px) {
    .cart-page { padding-left: 16px; padding-right: 16px; }
    .ci-img { width: 64px; height: 82px; }
    .ci-details h3 { font-size: 14px; }
  }
</style>

<main>
  <div class="cart-page">

    <!-- Breadcrumb -->
    <nav class="cart-crumb" aria-label="Breadcrumb">
      <a href="<?= BASE_URL ?>/">Home</a>
      <span class="sep">/</span>
      <span class="now">Your Shopping Cart</span>
    </nav>

    <!-- Header -->
    <div class="cart-head">
      <h1>Your cart</h1>
      <?php if (!empty($items)): ?>
        <span class="count"><?= count($items) ?> item<?= count($items) !== 1 ? 's' : '' ?></span>
      <?php endif; ?>
    </div>

    <?php if (empty($items)): ?>
      <!-- Empty State -->
      <div class="cart-empty">
        <div class="cart-empty-icon">
          <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z"/>
          </svg>
        </div>
        <h3>Your bag is empty</h3>
        <p>Looks like you haven't added anything yet. Explore our collection and find something you love.</p>
        <a href="<?= BASE_URL ?>/shop.php" class="btn-shop">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 12H5M12 19l-7-7 7-7"/></svg>
          Browse Collection
        </a>
      </div>

    <?php else: ?>
      <div class="cart-grid">

        <!-- Left: Cart Items -->
        <div>
          <ul class="cart-items">
            <?php foreach ($items as $item): ?>
              <?php
                $itemTotal = $item['unit_price'] * $item['quantity'];
                $img = !empty($item['image']) ? $item['image'] : 'https://via.placeholder.com/200x260?text=No+Image';
              ?>
              <li class="cart-item">
                <a href="<?= BASE_URL ?>/product.php?slug=<?= $item['slug'] ?>" class="ci-img">
                  <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($item['name']) ?>" loading="lazy">
                </a>

                <div class="ci-details">
                  <h3><a href="<?= BASE_URL ?>/product.php?slug=<?= $item['slug'] ?>"><?= sanitize($item['name']) ?></a></h3>
                  <div class="ci-meta">
                    <?php if ($item['size']): ?>
                      <span>Size: <?= sanitize($item['size']) ?></span>
                      <span class="dot"></span>
                    <?php endif; ?>
                    <span>SKU #<?= $item['product_id'] ?></span>
                  </div>
                  <button type="button" class="ci-remove" onclick="document.getElementById('remove-form-<?= $item['id'] ?>').submit();">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Remove
                  </button>
                  <form id="remove-form-<?= $item['id'] ?>" method="POST" style="display:none;">
                    <input type="hidden" name="action" value="remove">
                    <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                  </form>
                </div>

                <div class="ci-right">
                  <span class="ci-price"><?= formatPrice($item['unit_price']) ?></span>
                  <div class="ci-qty">
                    <button type="button" onclick="changeQty(<?= $item['id'] ?>, -1)" aria-label="Decrease quantity">&minus;</button>
                    <input type="text" class="qty-num" id="qty-<?= $item['id'] ?>" value="<?= (int)$item['quantity'] ?>" readonly>
                    <button type="button" onclick="changeQty(<?= $item['id'] ?>, 1)" aria-label="Increase quantity">+</button>
                  </div>
                  <span class="ci-total"><?= formatPrice($itemTotal) ?></span>
                </div>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>

        <!-- Right: Order Summary -->
        <aside class="cart-summary">

          <!-- Gold Banner -->
          <div class="summary-banner">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            <span>Free shipping on your first order!</span>
          </div>

          <div class="summary-body">
            <div class="summary-row">
              <span class="s-label">Subtotal (<?= count($items) ?> item<?= count($items) !== 1 ? 's' : '' ?>)</span>
              <span class="s-value"><?= formatPrice($subtotal) ?></span>
            </div>
            <div class="summary-row">
              <span class="s-label">Shipping</span>
              <span class="s-free">Free</span>
            </div>

            <div class="summary-rule"></div>

            <div class="summary-total-row">
              <span class="t-label">Total</span>
              <span class="t-value"><?= formatPrice($subtotal) ?></span>
            </div>

            <a href="<?= BASE_URL ?>/customer/checkout.php" class="summary-checkout">
              Proceed to Checkout
              <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>

            <div class="summary-secure">
              <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
              Secure checkout · SSL encrypted
            </div>

            <!-- Complete the Look -->
            <?php if (!empty($suggestedProducts)): ?>
              <div class="complete-look">
                <div class="complete-look-title">Complete the look</div>
                <?php foreach ($suggestedProducts as $sp): ?>
                  <a href="<?= BASE_URL ?>/product.php?slug=<?= $sp['slug'] ?>" class="look-card">
                    <div class="look-card-img">
                      <img src="<?= htmlspecialchars($sp['image'] ?: 'https://via.placeholder.com/120x144?text=No+Image') ?>" alt="<?= sanitize($sp['name']) ?>" loading="lazy">
                    </div>
                    <div class="look-card-info">
                      <h5><?= sanitize($sp['name']) ?></h5>
                      <div class="look-card-price"><?= formatPrice($sp['price']) ?></div>
                    </div>
                  </a>
                <?php endforeach; endif; ?>
            </div>
          </div>
        </aside>

      </div>
    <?php endif; ?>
  </div>
</main>

<script>
function changeQty(itemId, delta) {
  const input = document.getElementById('qty-' + itemId);
  let val = parseInt(input.value) + delta;
  if (val < 1) val = 1;

  const form = document.createElement('form');
  form.method = 'POST';
  form.innerHTML = `
    <input type="hidden" name="action" value="update">
    <input type="hidden" name="item_id" value="${itemId}">
    <input type="hidden" name="quantity" value="${val}">
  `;
  document.body.appendChild(form);
  form.submit();
}
</script>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>

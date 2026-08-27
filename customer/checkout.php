<?php
require_once dirname(__DIR__) . '/config/database.php';

if (!isset($_SESSION['customer_id'])) {
  redirect('/customer/login.php?redirect=' . urlencode('/customer/checkout.php'));
}

$customerId = $_SESSION['customer_id'];
$customer = $mysqli->query("SELECT * FROM customers WHERE id = $customerId")->fetch_assoc();

$stmt = $mysqli->prepare('SELECT id FROM carts WHERE customer_id = ?');
$stmt->bind_param('i', $customerId);
$stmt->execute();
$cart = $stmt->get_result()->fetch_assoc();

$items = [];
$subtotal = 0;
if ($cart) {
  $items = $mysqli->query("SELECT ci.*, p.name, p.price, p.image FROM cart_items ci JOIN products p ON ci.product_id = p.id WHERE ci.cart_id = {$cart['id']}")->fetch_all(MYSQLI_ASSOC);
  foreach ($items as $item) {
    $subtotal += $item['unit_price'] * $item['quantity'];
  }
}

if (empty($items)) {
  redirect('/customer/cart.php');
}

$shippingStandard = (float)getSetting('shipping_standard', 149);
$shippingFreeMin = (float)getSetting('shipping_free_min', 1999);
$shippingAmount = $subtotal >= $shippingFreeMin ? 0 : $shippingStandard;
$taxAmount = 0;
$discountAmount = 0;
$couponCode = '';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $shippingName = sanitize($_POST['shipping_name'] ?? '');
  $shippingPhone = sanitize($_POST['shipping_phone'] ?? '');
  $shippingAddress = sanitize($_POST['shipping_address'] ?? '');
  $shippingCity = sanitize($_POST['shipping_city'] ?? '');
  $shippingState = sanitize($_POST['shipping_state'] ?? '');
  $shippingPostal = sanitize($_POST['shipping_postal'] ?? '');
  $paymentMethod = sanitize($_POST['payment_method'] ?? 'cod');
  $couponCodeInput = strtoupper(sanitize($_POST['coupon_code'] ?? ''));

  if (empty($shippingName) || empty($shippingPhone) || empty($shippingAddress) || empty($shippingCity) || empty($shippingState) || empty($shippingPostal)) {
    $error = 'Please fill in all shipping details.';
  } else {
    $couponId = null;
    $couponDiscount = 0;

    if (!empty($couponCodeInput)) {
      $stmt = $mysqli->prepare('SELECT * FROM coupons WHERE code = ? AND is_active = 1 AND (starts_at IS NULL OR starts_at <= NOW()) AND (expires_at IS NULL OR expires_at >= NOW())');
      $stmt->bind_param('s', $couponCodeInput);
      $stmt->execute();
      $coupon = $stmt->get_result()->fetch_assoc();

      if ($coupon) {
        if ($coupon['usage_limit'] && $coupon['usage_count'] >= $coupon['usage_limit']) {
          $error = 'This coupon has reached its usage limit.';
        } elseif ($subtotal < $coupon['minimum_order_amount']) {
          $error = 'Minimum order amount for this coupon is ' . formatPrice($coupon['minimum_order_amount']) . '.';
        } else {
          $couponId = $coupon['id'];
          if ($coupon['type'] === 'percentage') {
            $couponDiscount = $subtotal * ($coupon['discount_value'] / 100);
            if ($coupon['maximum_discount_amount'] && $couponDiscount > $coupon['maximum_discount_amount']) {
              $couponDiscount = $coupon['maximum_discount_amount'];
            }
          } else {
            $couponDiscount = min($coupon['discount_value'], $subtotal);
          }
          $discountAmount = $couponDiscount;
          $couponCode = $couponCodeInput;
        }
      } else {
        $error = 'Invalid coupon code.';
      }
    }

    if (!$error) {
      $grandTotal = $subtotal - $discountAmount + $shippingAmount;
      $orderNumber = generateOrderNumber();

      $billingAddress = json_encode([
        'name' => $shippingName,
        'phone' => $shippingPhone,
        'address' => $shippingAddress,
        'city' => $shippingCity,
        'state' => $shippingState,
        'postal_code' => $shippingPostal
      ]);

      $stmt = $mysqli->prepare('INSERT INTO orders (order_number, customer_id, customer_name, customer_email, customer_phone, billing_address, shipping_address, subtotal, discount_amount, coupon_code, shipping_amount, tax_amount, grand_total, payment_method, payment_status, order_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
      $paymentStatus = 'pending';
      $orderStatus = 'pending';
      $stmt->bind_param('sissssdddsdddsss', $orderNumber, $customerId, $shippingName, $customer['email'], $shippingPhone, $billingAddress, $shippingAddress, $subtotal, $discountAmount, $couponCode, $shippingAmount, $taxAmount, $grandTotal, $paymentMethod, $paymentStatus, $orderStatus);
      $stmt->execute();
      $orderId = $mysqli->insert_id;

      foreach ($items as $item) {
        $totalPrice = $item['unit_price'] * $item['quantity'];
        $stmt = $mysqli->prepare('INSERT INTO order_items (order_id, product_id, product_name, product_sku, size, quantity, unit_price, total_price) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('iisssidd', $orderId, $item['product_id'], $item['name'], $item['sku'], $item['size'], $item['quantity'], $item['unit_price'], $totalPrice);
        $stmt->execute();
      }

      if ($couponId) {
        $stmt = $mysqli->prepare('INSERT INTO coupon_usage (coupon_id, order_id, customer_id, discount_amount) VALUES (?, ?, ?, ?)');
        $stmt->bind_param('iiid', $couponId, $orderId, $customerId, $discountAmount);
        $stmt->execute();
        $mysqli->query("UPDATE coupons SET usage_count = usage_count + 1 WHERE id = $couponId");
      }

      $mysqli->query("DELETE FROM cart_items WHERE cart_id = {$cart['id']}");

      $_SESSION['last_order_id'] = $orderId;
      redirect('/customer/order-success.php');
    }
  }
}

$pageTitle = 'Checkout — urban outfit';
include dirname(__DIR__) . '/includes/header.php';
?>

<style>
/* ─── CHECKOUT PAGE ─── */
.ck-page {
  min-height: calc(100vh - var(--header-height, 80px));
  display: grid;
  grid-template-columns: 1fr 480px;
  background: var(--color-bg);
}

/* ── LEFT: PRODUCTS ── */
.ck-left {
  padding: 60px 60px 60px 80px;
  display: flex;
  flex-direction: column;
}
.ck-left-header {
  margin-bottom: 40px;
}
.ck-breadcrumb {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.14em;
  color: var(--color-text-muted);
  margin-bottom: 24px;
}
.ck-breadcrumb a { color: var(--color-text-muted); text-decoration: none; }
.ck-breadcrumb a:hover { color: var(--color-accent); }
.ck-breadcrumb svg { color: var(--color-accent); }
.ck-left-icon {
  width: 56px; height: 56px;
  background: var(--color-accent-light);
  border-radius: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 20px;
  color: var(--color-accent);
}
.ck-left-title {
  font-family: var(--font-display);
  font-size: clamp(28px, 3vw, 38px);
  font-weight: 400;
  letter-spacing: -0.03em;
  color: var(--color-text-main);
  margin-bottom: 4px;
}
.ck-left-count {
  font-size: 14px;
  color: var(--color-text-muted);
}

/* Product Grid */
.ck-products {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 20px;
  flex: 1;
  align-content: start;
}
.ck-product {
  text-align: center;
  padding: 16px;
  background: var(--color-surface);
  border-radius: var(--radius-md);
  border: 1px solid var(--color-border);
  transition: all 0.3s;
}
.ck-product:hover {
  border-color: var(--color-accent);
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(0,0,0,0.06);
}
.ck-product-img {
  width: 100%;
  aspect-ratio: 3/4;
  object-fit: cover;
  border-radius: var(--radius-sm);
  margin-bottom: 12px;
  background: #F4F4F0;
}
.ck-product-name {
  font-size: 13px;
  font-weight: 600;
  color: var(--color-text-main);
  margin-bottom: 4px;
  line-height: 1.3;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
.ck-product-price {
  font-size: 14px;
  font-weight: 700;
  color: var(--color-text-main);
}
.ck-product-qty {
  font-size: 11px;
  color: var(--color-text-muted);
  margin-top: 2px;
}

/* ── RIGHT: FORM ── */
.ck-right {
  background: var(--color-surface);
  border-left: 1px solid var(--color-border);
  padding: 48px 48px 40px;
  display: flex;
  flex-direction: column;
  overflow-y: auto;
  max-height: calc(100vh - var(--header-height, 80px));
  position: sticky;
  top: var(--header-height, 80px);
}

/* Sections */
.ck-section {
  padding: 20px 0;
  border-bottom: 1px solid var(--color-border);
}
.ck-section:last-child { border-bottom: none; }
.ck-section-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  cursor: pointer;
  margin-bottom: 16px;
}
.ck-section-header h3 {
  font-family: var(--font-display);
  font-size: 17px;
  font-weight: 500;
  color: var(--color-text-main);
  display: flex;
  align-items: center;
  gap: 10px;
}
.ck-section-num {
  width: 28px; height: 28px;
  background: var(--color-accent);
  color: #fff;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
  font-weight: 700;
}
.ck-section-edit {
  font-size: 12px;
  font-weight: 600;
  color: var(--color-accent);
  cursor: pointer;
  text-transform: uppercase;
  letter-spacing: 0.06em;
}

/* Form fields */
.ck-field {
  margin-bottom: 14px;
}
.ck-field label {
  display: block;
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: var(--color-text-main);
  margin-bottom: 6px;
}
.ck-field label span { color: var(--color-accent); }
.ck-field input,
.ck-field textarea {
  width: 100%;
  padding: 12px 14px;
  border: 1.5px solid var(--color-border);
  border-radius: var(--radius-sm);
  font-size: 14px;
  font-family: var(--font-body);
  color: var(--color-text-main);
  background: var(--color-bg);
  transition: all 0.3s;
  outline: none;
}
.ck-field input:focus,
.ck-field textarea:focus {
  border-color: var(--color-text-main);
  background: #fff;
}
.ck-field textarea { resize: vertical; min-height: 70px; }
.ck-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}

/* Customer Info Card */
.ck-customer-card {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 16px;
  background: var(--color-bg);
  border-radius: var(--radius-md);
  margin-bottom: 12px;
}
.ck-customer-avatar {
  width: 44px; height: 44px;
  background: var(--color-accent-light);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--color-accent);
  flex-shrink: 0;
}
.ck-customer-info h4 {
  font-size: 14px;
  font-weight: 600;
  color: var(--color-text-main);
  margin-bottom: 2px;
}
.ck-customer-info p {
  font-size: 12px;
  color: var(--color-text-muted);
}

/* Payment */
.ck-payment-options {
  display: flex;
  flex-direction: column;
  gap: 10px;
}
.ck-payment-opt {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 14px 16px;
  border: 1.5px solid var(--color-border);
  border-radius: var(--radius-sm);
  cursor: pointer;
  transition: all 0.3s;
}
.ck-payment-opt:hover { border-color: var(--color-accent); }
.ck-payment-opt.active { border-color: var(--color-text-main); background: var(--color-bg); }
.ck-payment-opt input[type="radio"] { accent-color: var(--color-text-main); width: 16px; height: 16px; }
.ck-payment-opt span {
  font-size: 14px;
  font-weight: 500;
  color: var(--color-text-main);
}
.ck-payment-opt small {
  font-size: 11px;
  color: var(--color-text-muted);
  margin-left: auto;
}

/* Summary */
.ck-summary {
  padding-top: 16px;
}
.ck-summary-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 8px 0;
  font-size: 14px;
  color: var(--color-text-body);
}
.ck-summary-row.total {
  padding-top: 14px;
  margin-top: 8px;
  border-top: 1.5px solid var(--color-text-main);
  font-size: 18px;
  font-weight: 700;
  color: var(--color-text-main);
}
.ck-summary-row .free {
  color: #16A34A;
  font-weight: 600;
  font-size: 13px;
}

/* Coupon */
.ck-coupon {
  display: flex;
  gap: 8px;
  margin-top: 12px;
}
.ck-coupon input {
  flex: 1;
  padding: 12px 14px;
  border: 1.5px solid var(--color-border);
  border-radius: var(--radius-sm);
  font-size: 13px;
  font-family: var(--font-body);
  text-transform: uppercase;
  letter-spacing: 0.06em;
  outline: none;
  transition: border-color 0.3s;
}
.ck-coupon input:focus { border-color: var(--color-text-main); }
.ck-coupon-btn {
  padding: 12px 20px;
  background: var(--color-text-main);
  color: #fff;
  border: none;
  border-radius: var(--radius-sm);
  font-size: 12px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  cursor: pointer;
  transition: all 0.3s;
  white-space: nowrap;
}
.ck-coupon-btn:hover { background: var(--color-accent); }

/* Place Order Button */
.ck-place-btn {
  width: 100%;
  padding: 18px;
  background: var(--color-accent);
  color: #fff;
  border: none;
  border-radius: var(--radius-md);
  font-size: 14px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  cursor: pointer;
  transition: all 0.4s cubic-bezier(0.16,1,0.3,1);
  margin-top: 20px;
}
.ck-place-btn:hover {
  background: var(--color-text-main);
  transform: translateY(-2px);
  box-shadow: 0 8px 30px rgba(0,0,0,0.15);
}

/* Alert */
.ck-alert {
  padding: 12px 16px;
  border-radius: var(--radius-sm);
  font-size: 13px;
  font-weight: 500;
  margin-bottom: 16px;
  border-left: 3px solid;
}
.ck-alert-error { background: #FEF2F2; color: #991B1B; border-color: #DC2626; }

/* ── RESPONSIVE ── */
@media (max-width: 1024px) {
  .ck-page { grid-template-columns: 1fr; }
  .ck-left { padding: 40px 24px; }
  .ck-right { position: static; max-height: none; border-left: none; border-top: 1px solid var(--color-border); }
  .ck-products { grid-template-columns: repeat(3, 1fr); }
}
@media (max-width: 640px) {
  .ck-left { padding: 32px 16px; }
  .ck-right { padding: 32px 16px; }
  .ck-products { grid-template-columns: repeat(2, 1fr); gap: 12px; }
  .ck-row { grid-template-columns: 1fr; }
}
</style>

<!-- ═══ CHECKOUT ═══ -->
<section class="ck-page">

  <!-- LEFT: PRODUCTS -->
  <div class="ck-left">
    <div class="ck-left-header">
      <div class="ck-breadcrumb">
        <a href="<?= BASE_URL ?>/customer/cart.php">Cart</a>
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
        <span>Checkout</span>
      </div>
      <div class="ck-left-icon">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="1" y="3" width="15" height="13" rx="2"/><path d="M16 8h4l3 3v5a2 2 0 0 1-2 2h-1"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
      </div>
      <h1 class="ck-left-title">Confirm & Pay</h1>
      <p class="ck-left-count"><?= count($items) ?> item<?= count($items) !== 1 ? 's' : '' ?></p>
    </div>

    <div class="ck-products">
      <?php foreach ($items as $item): ?>
        <?php
          $img = !empty($item['image']) ? $item['image'] : 'https://via.placeholder.com/300x400?text=No+Image';
          $itemTotal = $item['unit_price'] * $item['quantity'];
        ?>
        <div class="ck-product">
          <img class="ck-product-img" src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($item['name']) ?>" loading="lazy">
          <div class="ck-product-name"><?= sanitize($item['name']) ?></div>
          <div class="ck-product-price"><?= formatPrice($item['unit_price']) ?></div>
          <?php if ($item['quantity'] > 1): ?>
            <div class="ck-product-qty">Qty: <?= (int)$item['quantity'] ?></div>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- RIGHT: FORM -->
  <div class="ck-right">
    <form method="POST" id="checkoutForm" style="display: flex; flex-direction: column; flex: 1;">

      <?php if ($error): ?>
        <div class="ck-alert ck-alert-error"><?= $error ?></div>
      <?php endif; ?>

      <!-- Guest Checkout -->
      <div class="ck-section">
        <div class="ck-section-header">
          <h3>
            <span class="ck-section-num">1</span>
            Guest Checkout
          </h3>
        </div>
        <div class="ck-customer-card">
          <div class="ck-customer-avatar">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          </div>
          <div class="ck-customer-info">
            <h4><?= sanitize($customer['first_name'] . ' ' . $customer['last_name']) ?></h4>
            <p><?= sanitize($customer['email'] ?? '') ?></p>
          </div>
        </div>
      </div>

      <!-- Shipping Information -->
      <div class="ck-section">
        <div class="ck-section-header">
          <h3>
            <span class="ck-section-num">2</span>
            Shipping Information
          </h3>
        </div>
        <div class="ck-field">
          <label>Full Name <span>*</span></label>
          <input type="text" name="shipping_name" required value="<?= sanitize($_POST['shipping_name'] ?? ($customer['first_name'] . ' ' . $customer['last_name'])) ?>">
        </div>
        <div class="ck-field">
          <label>Phone <span>*</span></label>
          <input type="tel" name="shipping_phone" required value="<?= sanitize($_POST['shipping_phone'] ?? $customer['phone'] ?? '') ?>">
        </div>
        <div class="ck-field">
          <label>Address <span>*</span></label>
          <textarea name="shipping_address" rows="2" required placeholder="Street address, apartment, suite..."><?= sanitize($_POST['shipping_address'] ?? '') ?></textarea>
        </div>
        <div class="ck-row">
          <div class="ck-field">
            <label>City <span>*</span></label>
            <input type="text" name="shipping_city" required value="<?= sanitize($_POST['shipping_city'] ?? '') ?>">
          </div>
          <div class="ck-field">
            <label>State <span>*</span></label>
            <input type="text" name="shipping_state" required value="<?= sanitize($_POST['shipping_state'] ?? '') ?>">
          </div>
        </div>
        <div class="ck-field">
          <label>Postal Code <span>*</span></label>
          <input type="text" name="shipping_postal" required value="<?= sanitize($_POST['shipping_postal'] ?? '') ?>">
        </div>
      </div>

      <!-- Payment -->
      <div class="ck-section">
        <div class="ck-section-header">
          <h3>
            <span class="ck-section-num">3</span>
            Payment
          </h3>
        </div>
        <div class="ck-payment-options">
          <label class="ck-payment-opt active">
            <input type="radio" name="payment_method" value="cod" checked>
            <span>Cash on Delivery</span>
            <small>Pay at your doorstep</small>
          </label>
          <label class="ck-payment-opt">
            <input type="radio" name="payment_method" value="online" disabled>
            <span>Online Payment</span>
            <small>Coming Soon</small>
          </label>
        </div>
      </div>

      <!-- Coupon -->
      <div class="ck-section">
        <div class="ck-section-header" style="margin-bottom:0;">
          <h3 style="font-size:14px;">Have a coupon code?</h3>
        </div>
        <div class="ck-coupon">
          <input type="text" name="coupon_code" placeholder="ENTER CODE" value="<?= sanitize($_POST['coupon_code'] ?? '') ?>">
          <button type="submit" class="ck-coupon-btn">Apply</button>
        </div>
      </div>

      <!-- Summary -->
      <div class="ck-section ck-summary">
        <div class="ck-summary-row">
          <span>Subtotal</span>
          <span><?= formatPrice($subtotal) ?></span>
        </div>
        <div class="ck-summary-row">
          <span>Shipping</span>
          <span class="free">Free</span>
        </div>
        <?php if ($discountAmount > 0): ?>
          <div class="ck-summary-row" style="color: #16A34A;">
            <span>Discount</span>
            <span>-<?= formatPrice($discountAmount) ?></span>
          </div>
        <?php endif; ?>
        <div class="ck-summary-row total">
          <span>Total</span>
          <span><?= formatPrice($subtotal - $discountAmount + $shippingAmount) ?></span>
        </div>
      </div>

      <button type="submit" class="ck-place-btn">Place Your Order</button>
    </form>
  </div>

</section>

<script>
document.querySelectorAll('.ck-payment-opt input[type="radio"]').forEach(radio => {
  radio.addEventListener('change', () => {
    document.querySelectorAll('.ck-payment-opt').forEach(opt => opt.classList.remove('active'));
    radio.closest('.ck-payment-opt').classList.add('active');
  });
});
</script>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>

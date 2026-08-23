<?php
require_once dirname(__DIR__) . '/config/database.php';

if (!isset($_SESSION['customer_id'])) {
  redirect('/customer/login.php?redirect=' . urlencode('/customer/checkout.php'));
}

$pageTitle = 'Checkout — ATELIER';
include dirname(__DIR__) . '/includes/header.php';

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

      $stmt = $mysqli->prepare('INSERT INTO orders (order_number, customer_id, customer_name, customer_email, customer_phone, billing_address, shipping_address, subtotal, discount_amount, coupon_id, coupon_code, shipping_amount, tax_amount, grand_total, payment_method, payment_status, order_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
      $paymentStatus = $paymentMethod === 'cod' ? 'pending' : 'pending';
      $stmt->bind_param('sisssssdddsddsss', $orderNumber, $customerId, $shippingName, $customer['email'], $shippingPhone, $billingAddress, $billingAddress, $subtotal, $discountAmount, $couponId, $couponCode, $shippingAmount, $taxAmount, $grandTotal, $paymentMethod, $paymentStatus, $orderStatus);
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

$addresses = $mysqli->query("SELECT * FROM addresses WHERE customer_id = $customerId ORDER BY is_default DESC, created_at DESC")->fetch_all(MYSQLI_ASSOC);
?>

<main style="padding-top: calc(var(--header-height) + var(--space-16)); padding-bottom: var(--space-16);">
  <div class="container">
    <div class="section-header">
      <h1 class="section-title">Checkout</h1>
    </div>

    <?php if ($error): ?>
      <div class="alert alert-error" style="margin-bottom: var(--space-6); max-width: 800px; margin-left: auto; margin-right: auto;"><?= sanitize($error) ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="admin-grid">
        <div>
          <div class="admin-card" style="margin-bottom: var(--space-6);">
            <div class="admin-card-header"><h2>Shipping Details</h2></div>
            <div style="padding: var(--space-6);">
              <div class="form-grid">
                <div class="form-group full-width">
                  <label>Full Name <span class="required">*</span></label>
                  <input type="text" name="shipping_name" required value="<?= sanitize($_POST['shipping_name'] ?? ($customer['first_name'] . ' ' . $customer['last_name'])) ?>">
                </div>
                <div class="form-group full-width">
                  <label>Phone <span class="required">*</span></label>
                  <input type="tel" name="shipping_phone" required value="<?= sanitize($_POST['shipping_phone'] ?? $customer['phone'] ?? '') ?>">
                </div>
                <div class="form-group full-width">
                  <label>Address <span class="required">*</span></label>
                  <textarea name="shipping_address" rows="2" required><?= sanitize($_POST['shipping_address'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                  <label>City <span class="required">*</span></label>
                  <input type="text" name="shipping_city" required value="<?= sanitize($_POST['shipping_city'] ?? '') ?>">
                </div>
                <div class="form-group">
                  <label>State <span class="required">*</span></label>
                  <input type="text" name="shipping_state" required value="<?= sanitize($_POST['shipping_state'] ?? '') ?>">
                </div>
                <div class="form-group">
                  <label>Postal Code <span class="required">*</span></label>
                  <input type="text" name="shipping_postal" required value="<?= sanitize($_POST['shipping_postal'] ?? '') ?>">
                </div>
              </div>
            </div>
          </div>

          <div class="admin-card">
            <div class="admin-card-header"><h2>Payment Method</h2></div>
            <div style="padding: var(--space-6);">
              <div class="form-group">
                <label><input type="radio" name="payment_method" value="cod" checked> Cash on Delivery (COD)</label>
              </div>
              <div class="form-group">
                <label><input type="radio" name="payment_method" value="online" disabled> Online Payment (Coming Soon)</label>
              </div>
            </div>
          </div>
        </div>

        <div>
          <div class="admin-card" style="margin-bottom: var(--space-6);">
            <div class="admin-card-header"><h2>Order Summary</h2></div>
            <div style="padding: var(--space-6);">
              <?php foreach ($items as $item): ?>
                <div style="display: flex; gap: 12px; margin-bottom: var(--space-4); padding-bottom: var(--space-4); border-bottom: 1px solid var(--color-bg-elevated);">
                  <img src="<?= $item['image'] ?>" alt="" style="width: 64px; height: 80px; object-fit: cover; border-radius: var(--radius-sm);">
                  <div style="flex: 1;">
                    <div style="font-weight: 600; font-size: var(--text-body-sm);"><?= sanitize($item['name']) ?></div>
                    <div style="font-size: var(--text-caption); color: var(--color-text-tertiary);">Qty: <?= (int)$item['quantity'] ?></div>
                    <div style="font-weight: 600; margin-top: 4px;"><?= formatPrice($item['unit_price'] * $item['quantity']) ?></div>
                  </div>
                </div>
              <?php endforeach; ?>

              <div style="margin-top: var(--space-4);">
                <div style="display: flex; justify-content: space-between; margin-bottom: var(--space-2);">
                  <span>Subtotal</span>
                  <span><?= formatPrice($subtotal) ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: var(--space-2);">
                  <span>Shipping</span>
                  <span><?= $shippingAmount > 0 ? formatPrice($shippingAmount) : 'Free' ?></span>
                </div>
                <?php if ($discountAmount > 0): ?>
                  <div style="display: flex; justify-content: space-between; margin-bottom: var(--space-2); color: var(--color-accent-secondary);">
                    <span>Discount</span>
                    <span>-<?= formatPrice($discountAmount) ?></span>
                  </div>
                <?php endif; ?>
                <div style="border-top: 1px solid var(--color-accent-tertiary); padding-top: var(--space-3); display: flex; justify-content: space-between; font-weight: 700; font-size: var(--text-h4);">
                  <span>Total</span>
                  <span><?= formatPrice($subtotal - $discountAmount + $shippingAmount) ?></span>
                </div>
              </div>
            </div>
          </div>

          <div class="admin-card">
            <div class="admin-card-header"><h2>Coupon Code</h2></div>
            <div style="padding: var(--space-6);">
              <div style="display: flex; gap: var(--space-3);">
                <input type="text" name="coupon_code" placeholder="Enter code" value="<?= sanitize($_POST['coupon_code'] ?? '') ?>" style="flex: 1; padding: var(--space-3) var(--space-4); border: 1.5px solid var(--color-accent-tertiary); border-radius: var(--radius-md);">
                <button type="submit" class="btn btn-secondary">Apply</button>
              </div>
            </div>
          </div>

          <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: var(--space-6); padding: var(--space-5); font-size: var(--text-btn);">Place Order</button>
        </div>
      </div>
    </form>
  </div>
</main>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>

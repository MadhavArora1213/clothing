<?php
require_once dirname(__DIR__) . '/config/database.php';

if (!isset($_SESSION['customer_id'])) {
  redirect('/customer/login.php?redirect=' . urlencode('/customer/checkout.php'));
}

$customerId = $_SESSION['customer_id'];
$customer = $mysqli->query("SELECT * FROM customers WHERE id = $customerId")->fetch_assoc();

$cart = $mysqli->query("SELECT * FROM carts WHERE customer_id = $customerId")->fetch_assoc();
if (!$cart) redirect('/customer/cart.php');

$items = [];
$subtotal = 0;
$shippingAmount = 0;
if ($cart) {
  $items = $mysqli->query("SELECT ci.*, p.name, p.price, p.image, p.shipping_charge, p.free_shipping, p.shipping_days FROM cart_items ci JOIN products p ON ci.product_id = p.id WHERE ci.cart_id = {$cart['id']}")->fetch_all(MYSQLI_ASSOC);
  foreach ($items as $item) {
    $subtotal += $item['unit_price'] * $item['quantity'];
    if (!$item['free_shipping']) {
      $shippingAmount += $item['shipping_charge'] * $item['quantity'];
    }
  }
}

if (empty($items)) {
  redirect('/customer/cart.php');
}

$taxAmount = 0;
$discountAmount = 0;
$couponCode = '';
$grandTotal = $subtotal - $discountAmount + $shippingAmount;

$error = '';

// Handle AJAX order creation + payment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax']) && $_POST['ajax'] == '1') {
  header('Content-Type: application/json');

  $shippingName = sanitize($_POST['shipping_name'] ?? '');
  $shippingPhone = sanitize($_POST['shipping_phone'] ?? '');
  $shippingAddress = sanitize($_POST['shipping_address'] ?? '');
  $shippingCity = sanitize($_POST['shipping_city'] ?? '');
  $shippingState = sanitize($_POST['shipping_state'] ?? '');
  $shippingPostal = sanitize($_POST['shipping_postal'] ?? '');

  if (empty($shippingName) || empty($shippingPhone) || empty($shippingAddress) || empty($shippingCity) || empty($shippingState) || empty($shippingPostal)) {
    echo json_encode(['success' => false, 'message' => 'Please fill in all shipping details.']);
    exit;
  }

  $orderNumber = generateOrderNumber();

  $billingAddress = json_encode([
    'name' => $shippingName, 'phone' => $shippingPhone,
    'address' => $shippingAddress, 'city' => $shippingCity,
    'state' => $shippingState, 'postal_code' => $shippingPostal
  ]);

  $stmt = $mysqli->prepare('INSERT INTO orders (order_number, customer_id, customer_name, customer_email, customer_phone, billing_address, shipping_address, subtotal, discount_amount, coupon_code, shipping_amount, tax_amount, grand_total, payment_method, payment_status, order_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
  $paymentStatus = 'pending';
  $orderStatus = 'pending';
  $stmt->bind_param('sissssdddsdddsss', $orderNumber, $customerId, $shippingName, $customer['email'], $shippingPhone, $billingAddress, $shippingAddress, $subtotal, $discountAmount, $couponCode, $shippingAmount, $taxAmount, $grandTotal, 'online', $paymentStatus, $orderStatus);
  $stmt->execute();
  $orderId = $mysqli->insert_id;

  foreach ($items as $item) {
    $totalPrice = $item['unit_price'] * $item['quantity'];
    $stmt = $mysqli->prepare('INSERT INTO order_items (order_id, product_id, product_name, product_sku, size, quantity, unit_price, total_price) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
    $stmt->bind_param('iisssidd', $orderId, $item['product_id'], $item['name'], $item['sku'], $item['size'], $item['quantity'], $item['unit_price'], $totalPrice);
    $stmt->execute();
  }

  $mysqli->query("DELETE FROM cart_items WHERE cart_id = {$cart['id']}");

  // Now call Cashfree API directly
  $envFile = dirname(__DIR__) . '/.env';
  if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
      if (strpos(trim($line), '#') === 0) continue;
      if (strpos($line, '=') !== false) {
        list($k, $v) = explode('=', $line, 2);
        $v = trim($v);
        if (($v[0] ?? '') === '"') $v = substr($v, 1, -1);
        $_ENV[trim($k)] = $v;
      }
    }
  }

  $cfOrderId = $orderNumber . '_' . time();
  $phone = preg_replace('/[^0-9]/', '', $shippingPhone);
  if (strlen($phone) > 10) $phone = substr($phone, -10);
  if (strlen($phone) !== 10) $phone = '9999999999';

  $payload = [
    'order_id' => $cfOrderId,
    'order_amount' => (float)$grandTotal,
    'order_currency' => 'INR',
    'customer_details' => [
      'customer_id' => 'CUST_' . $customerId,
      'customer_name' => $shippingName,
      'customer_email' => $customer['email'],
      'customer_phone' => $phone,
    ],
    'order_meta' => [
      'return_url' => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/clothing/api/cashfree.php?action=callback&order_id=' . $orderId,
      'notify_url' => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/clothing/api/cashfree_webhook.php',
    ],
  ];

  $ch = curl_init('https://api.cashfree.com/pg/orders');
  curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 30, CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($payload),
    CURLOPT_HTTPHEADER => [
      'Content-Type: application/json',
      'x-client-id: ' . ($_ENV['CF_APP_ID'] ?? ''),
      'x-client-secret: ' . ($_ENV['CF_SECRET_KEY'] ?? ''),
      'x-api-version: 2023-08-01',
    ],
  ]);

  $response = curl_exec($ch);
  $curlErr = curl_error($ch);
  curl_close($ch);

  if ($curlErr) {
    echo json_encode(['success' => false, 'message' => 'Payment gateway error: ' . $curlErr]);
    exit;
  }

  $result = json_decode($response, true);

  if (isset($result['cf_order_id'])) {
    $upd = $mysqli->prepare('UPDATE orders SET payment_session_id = ? WHERE id = ?');
    if ($upd) { $upd->bind_param('si', $result['cf_order_id'], $orderId); $upd->execute(); }

    echo json_encode([
      'success' => true,
      'payment_session_id' => $result['payment_session_id'] ?? '',
      'order_id' => $orderId,
    ]);
  } else {
    $errMsg = $result['message'] ?? $result['error_description'] ?? 'Payment gateway error';
    echo json_encode(['success' => false, 'message' => $errMsg]);
  }
  exit;
}

// Normal form submit (fallback)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $shippingName = sanitize($_POST['shipping_name'] ?? '');
  $shippingPhone = sanitize($_POST['shipping_phone'] ?? '');
  $shippingAddress = sanitize($_POST['shipping_address'] ?? '');
  $shippingCity = sanitize($_POST['shipping_city'] ?? '');
  $shippingState = sanitize($_POST['shipping_state'] ?? '');
  $shippingPostal = sanitize($_POST['shipping_postal'] ?? '');

  if (empty($shippingName) || empty($shippingPhone) || empty($shippingAddress) || empty($shippingCity) || empty($shippingState) || empty($shippingPostal)) {
    $error = 'Please fill in all shipping details.';
  }
}

$pageTitle = 'Checkout — urban outfit';
include dirname(__DIR__) . '/includes/header.php';
?>

<style>
.ck-page {
  min-height: calc(100vh - var(--header-height, 80px));
  display: grid; grid-template-columns: 1fr 420px; background: var(--color-bg);
}
.ck-left { padding: 40px 40px 40px 60px; display: flex; flex-direction: column; }
.ck-breadcrumb { display: flex; align-items: center; gap: 8px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.14em; color: var(--color-text-muted); margin-bottom: 20px; }
.ck-breadcrumb a { color: var(--color-text-muted); text-decoration: none; }
.ck-breadcrumb a:hover { color: var(--color-accent); }
.ck-left-title { font-family: var(--font-display); font-size: 32px; font-weight: 400; letter-spacing: -0.03em; color: var(--color-text-main); margin-bottom: 4px; }
.ck-left-count { font-size: 14px; color: var(--color-text-muted); margin-bottom: 24px; }
.ck-products { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 16px; }
.ck-product { text-align: center; padding: 14px; background: var(--color-surface); border-radius: var(--radius-md); border: 1px solid var(--color-border); }
.ck-product-img { width: 100%; aspect-ratio: 3/4; object-fit: cover; border-radius: var(--radius-sm); margin-bottom: 10px; background: #F4F4F0; }
.ck-product-name { font-size: 12px; font-weight: 600; color: var(--color-text-main); margin-bottom: 4px; line-height: 1.3; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.ck-product-price { font-size: 13px; font-weight: 700; color: var(--color-text-main); }
.ck-product-qty { font-size: 11px; color: var(--color-text-muted); margin-top: 2px; }
.ck-right { background: var(--color-surface); border-left: 1px solid var(--color-border); padding: 32px 36px; display: flex; flex-direction: column; max-height: calc(100vh - var(--header-height, 80px)); position: sticky; top: var(--header-height, 80px); overflow-y: auto; }
.ck-section { padding: 12px 0; border-bottom: 1px solid var(--color-border); }
.ck-section:last-child { border-bottom: none; }
.ck-section-header { display: flex; align-items: center; margin-bottom: 10px; }
.ck-section-header h3 { font-family: var(--font-display); font-size: 15px; font-weight: 500; color: var(--color-text-main); display: flex; align-items: center; gap: 8px; }
.ck-section-num { width: 24px; height: 24px; background: var(--color-accent); color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; }
.ck-field { margin-bottom: 10px; }
.ck-field label { display: block; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: var(--color-text-main); margin-bottom: 4px; }
.ck-field label span { color: var(--color-accent); }
.ck-field input, .ck-field textarea { width: 100%; padding: 10px 12px; border: 1.5px solid var(--color-border); border-radius: var(--radius-sm); font-size: 13px; font-family: var(--font-body); color: var(--color-text-main); background: var(--color-bg); outline: none; }
.ck-field input:focus, .ck-field textarea:focus { border-color: var(--color-text-main); background: #fff; }
.ck-field textarea { resize: none; min-height: 50px; }
.ck-row { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
.ck-customer-card { display: flex; align-items: center; gap: 12px; padding: 12px; background: var(--color-bg); border-radius: var(--radius-md); margin-bottom: 10px; }
.ck-customer-avatar { width: 36px; height: 36px; background: var(--color-accent-light); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--color-accent); flex-shrink: 0; }
.ck-customer-info h4 { font-size: 13px; font-weight: 600; color: var(--color-text-main); margin-bottom: 1px; }
.ck-customer-info p { font-size: 11px; color: var(--color-text-muted); }
.ck-summary-row { display: flex; justify-content: space-between; align-items: center; padding: 5px 0; font-size: 13px; color: var(--color-text-body); }
.ck-summary-row.total { padding-top: 10px; margin-top: 6px; border-top: 1.5px solid var(--color-text-main); font-size: 16px; font-weight: 700; color: var(--color-text-main); }
.ck-summary-row .free { color: #16A34A; font-weight: 600; font-size: 12px; }
.ck-place-btn { width: 100%; padding: 14px; border: none; border-radius: var(--radius-sm); background: var(--color-text-main); color: #fff; font-size: 14px; font-weight: 700; letter-spacing: 0.06em; text-transform: uppercase; cursor: pointer; transition: all 0.3s; margin-top: 12px; }
.ck-place-btn:hover { background: var(--color-accent); transform: translateY(-1px); }
.ck-place-btn:disabled { opacity: 0.6; cursor: not-allowed; }
.ck-alert-error { background: #FEF2F2; color: #991B1B; border: 1px solid #F87171; padding: 10px 14px; border-radius: 8px; font-size: 13px; margin-bottom: 12px; }
.ck-spinner { display: inline-block; width: 16px; height: 16px; border: 2px solid rgba(255,255,255,0.3); border-top-color: #fff; border-radius: 50%; animation: spin 0.6s linear infinite; margin-right: 8px; vertical-align: middle; }
@keyframes spin { to { transform: rotate(360deg); } }
@media (max-width: 900px) { .ck-page { grid-template-columns: 1fr; } .ck-left { padding: 24px; } .ck-right { max-height: none; position: static; border-left: none; border-top: 1px solid var(--color-border); } }
</style>

<section class="ck-page">
  <div class="ck-left">
    <div class="ck-breadcrumb">
      <a href="<?= BASE_URL ?>/shop.php">Shop</a>
      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
      Checkout
    </div>
    <div class="ck-left-title">Confirm & Pay</div>
    <div class="ck-left-count"><?= count($items) ?> item<?= count($items) > 1 ? 's' : '' ?></div>
    <div class="ck-products">
      <?php foreach ($items as $item):
        $img = !empty($item['image']) ? $item['image'] : 'https://via.placeholder.com/300x400?text=No+Image';
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

  <div class="ck-right">
    <form method="POST" id="checkoutForm" onsubmit="return handleCheckout(event)" style="display: flex; flex-direction: column; flex: 1;">

      <?php if ($error): ?>
        <div class="ck-alert-error"><?= $error ?></div>
      <?php endif; ?>

      <div class="ck-section">
        <div class="ck-section-header"><h3><span class="ck-section-num">1</span> Customer</h3></div>
        <div class="ck-customer-card">
          <div class="ck-customer-avatar">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          </div>
          <div class="ck-customer-info">
            <h4><?= sanitize($customer['first_name'] . ' ' . $customer['last_name']) ?></h4>
            <p><?= sanitize($customer['email'] ?? '') ?></p>
          </div>
        </div>
      </div>

      <div class="ck-section">
        <div class="ck-section-header"><h3><span class="ck-section-num">2</span> Shipping</h3></div>
        <div class="ck-field"><label>Full Name <span>*</span></label><input type="text" name="shipping_name" required value="<?= sanitize($_POST['shipping_name'] ?? ($customer['first_name'] . ' ' . $customer['last_name'])) ?>"></div>
        <div class="ck-field"><label>Phone <span>*</span></label><input type="tel" name="shipping_phone" required value="<?= sanitize($_POST['shipping_phone'] ?? $customer['phone'] ?? '') ?>"></div>
        <div class="ck-field"><label>Address <span>*</span></label><textarea name="shipping_address" rows="2" required placeholder="Street, landmark..."><?= sanitize($_POST['shipping_address'] ?? '') ?></textarea></div>
        <div class="ck-row">
          <div class="ck-field"><label>City <span>*</span></label><input type="text" name="shipping_city" required value="<?= sanitize($_POST['shipping_city'] ?? '') ?>"></div>
          <div class="ck-field"><label>State <span>*</span></label><input type="text" name="shipping_state" required value="<?= sanitize($_POST['shipping_state'] ?? '') ?>"></div>
        </div>
        <div class="ck-field"><label>Postal Code <span>*</span></label><input type="text" name="shipping_postal" required value="<?= sanitize($_POST['shipping_postal'] ?? '') ?>"></div>
      </div>

      <div class="ck-section">
        <div class="ck-section-header"><h3><span class="ck-section-num">3</span> Payment</h3></div>
        <div style="padding:12px 14px;border:1.5px solid var(--color-border);border-radius:var(--radius-sm);background:var(--color-bg);display:flex;align-items:center;gap:10px;">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
          <div><div style="font-size:13px;font-weight:600;">Online Payment</div><div style="font-size:11px;color:var(--color-text-muted);">UPI / Cards / Netbanking</div></div>
        </div>
      </div>

      <div class="ck-section ck-summary" style="flex:1;">
        <div class="ck-summary-row"><span>Subtotal</span><span><?= formatPrice($subtotal) ?></span></div>
        <div class="ck-summary-row"><span>Shipping</span><?php if ($shippingAmount > 0): ?><span><?= formatPrice($shippingAmount) ?></span><?php else: ?><span class="free">Free</span><?php endif; ?></div>
        <div class="ck-summary-row total"><span>Total</span><span><?= formatPrice($grandTotal) ?></span></div>
      </div>

      <button type="submit" id="payBtn" class="ck-place-btn">Pay <?= formatPrice($grandTotal) ?></button>
    </form>
  </div>
</section>

<script src="https://sdk.cashfree.com/js/v3/cashfree.js"></script>
<script>
const CF_ENV = '<?= CF_ENV ?>';

async function handleCheckout(e) {
  e.preventDefault();
  const btn = document.getElementById('payBtn');
  btn.disabled = true;
  btn.innerHTML = '<span class="ck-spinner"></span>Processing...';

  const form = document.getElementById('checkoutForm');
  const formData = new FormData(form);
  formData.append('ajax', '1');

  try {
    const res = await fetch(window.location.href, { method: 'POST', body: formData });
    const data = await res.json();

    if (!data.success) {
      alert(data.message || 'Error placing order');
      btn.disabled = false;
      btn.innerHTML = 'Pay <?= formatPrice($grandTotal) ?>';
      return;
    }

    btn.innerHTML = '<span class="ck-spinner"></span>Opening payment...';

    const cashfree = Cashfree({ mode: CF_ENV === 'sandbox' ? 'sandbox' : 'production' });
    cashfree.checkout({ paymentSessionId: data.payment_session_id, redirectTarget: '_self' });

  } catch (err) {
    alert('Network error: ' + err.message);
    btn.disabled = false;
    btn.innerHTML = 'Pay <?= formatPrice($grandTotal) ?>';
  }
}
</script>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>

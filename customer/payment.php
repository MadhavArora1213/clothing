<?php
require_once dirname(__DIR__) . '/config/database.php';

if (!isset($_SESSION['customer_id'])) {
  redirect('/customer/login.php');
}

$orderId = (int)($_GET['order_id'] ?? $_SESSION['last_order_id'] ?? 0);
$customerId = $_SESSION['customer_id'];

if (!$orderId) {
  redirect('/customer/cart.php');
}

// Fetch order
$stmt = $mysqli->prepare('SELECT id, order_number, grand_total, customer_name, customer_email, customer_phone, payment_status FROM orders WHERE id = ? AND customer_id = ?');
$stmt->bind_param('ii', $orderId, $customerId);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
  redirect('/customer/cart.php');
}

// If already paid, go to success
if ($order['payment_status'] === 'completed' || $order['payment_status'] === 'paid') {
  $_SESSION['last_order_id'] = $orderId;
  redirect('/customer/order-success.php');
}

$pageTitle = 'Payment — urban outfit';
include dirname(__DIR__) . '/includes/header.php';
?>

<style>
  .pay-page {
    min-height: calc(100vh - var(--header-height, 80px));
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 40px 20px;
    background: var(--color-bg, #FAF9F6);
  }
  .pay-card {
    width: 100%;
    max-width: 460px;
    background: #fff;
    border: 1px solid var(--color-border, #E8E2D8);
    border-radius: 16px;
    padding: 40px 36px;
    text-align: center;
  }
  .pay-logo {
    font-family: var(--font-display);
    font-size: 20px;
    font-weight: 700;
    color: var(--color-text-main, #1a1a1a);
    margin-bottom: 8px;
  }
  .pay-order-num {
    font-size: 12px;
    color: var(--color-text-muted, #9A8E7E);
    margin-bottom: 24px;
  }
  .pay-amount {
    font-family: var(--font-mono);
    font-size: 36px;
    font-weight: 800;
    color: var(--color-text-main, #1a1a1a);
    margin-bottom: 8px;
  }
  .pay-label {
    font-size: 13px;
    color: var(--color-text-muted, #9A8E7E);
    margin-bottom: 32px;
  }
  .pay-methods {
    display: flex;
    gap: 8px;
    justify-content: center;
    margin-bottom: 28px;
    flex-wrap: wrap;
  }
  .pay-method-badge {
    padding: 6px 14px;
    border: 1px solid var(--color-border, #E8E2D8);
    border-radius: 8px;
    font-size: 11px;
    font-weight: 600;
    color: var(--color-text-muted, #9A8E7E);
    background: var(--color-bg, #FAF9F6);
  }
  #cf-pay-btn {
    width: 100%;
    padding: 16px;
    background: var(--color-accent, #D4AF37);
    color: #fff;
    border: none;
    border-radius: 10px;
    font-size: 15px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s;
    letter-spacing: 0.04em;
    text-transform: uppercase;
  }
  #cf-pay-btn:hover {
    background: var(--color-text-main, #1a1a1a);
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.12);
  }
  #cf-pay-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    transform: none;
  }
  .pay-status {
    margin-top: 20px;
    font-size: 13px;
    color: var(--color-text-muted, #9A8E7E);
    display: none;
  }
  .pay-status.show { display: block; }
  .pay-spinner {
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 2.5px solid rgba(255,255,255,0.3);
    border-top-color: #fff;
    border-radius: 50%;
    animation: spin 0.7s linear infinite;
    vertical-align: middle;
    margin-right: 8px;
  }
  @keyframes spin { to { transform: rotate(360deg); } }
  .pay-cancel {
    display: block;
    margin-top: 16px;
    font-size: 13px;
    color: var(--color-text-muted, #9A8E7E);
    text-decoration: none;
    transition: color 0.2s;
  }
  .pay-cancel:hover { color: var(--color-accent, #D4AF37); }
</style>

<section class="pay-page">
  <div class="pay-card">
    <div class="pay-logo">urban outfit</div>
    <div class="pay-order-num">Order #<?= htmlspecialchars($order['order_number']) ?></div>

    <div class="pay-amount">₹<?= number_format($order['grand_total'], 2) ?></div>
    <div class="pay-label">Complete your payment securely</div>

    <div class="pay-methods">
      <span class="pay-method-badge">UPI</span>
      <span class="pay-method-badge">Credit Card</span>
      <span class="pay-method-badge">Debit Card</span>
      <span class="pay-method-badge">Net Banking</span>
      <span class="pay-method-badge">Wallets</span>
    </div>

    <button id="cf-pay-btn" onclick="startPayment()">
      Pay ₹<?= number_format($order['grand_total'], 2) ?>
    </button>

    <div class="pay-status" id="payStatus"></div>
    <a href="<?= BASE_URL ?>/customer/checkout.php" class="pay-cancel">Cancel and go back</a>
  </div>
</section>

<!-- Cashfree JS SDK v3 -->
<script src="https://sdk.cashfree.com/js/v3/cashfree.js"></script>

<script>
const CF_APP_ID = '<?= CF_APP_ID ?>';
const CF_ENV = '<?= CF_ENV ?>';
const ORDER_ID = <?= $order['id'] ?>;
const AMOUNT = <?= $order['grand_total'] ?>;

function showStatus(msg) {
  const el = document.getElementById('payStatus');
  el.textContent = msg;
  el.classList.add('show');
}

function startPayment() {
  const btn = document.getElementById('cf-pay-btn');
  btn.disabled = true;
  btn.innerHTML = '<span class="pay-spinner"></span>Creating payment...';
  showStatus('Connecting to Cashfree...');

  fetch('../api/cashfree.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'action=create_order&order_id=' + ORDER_ID
  })
  .then(r => {
    if (!r.ok) throw new Error('Server returned ' + r.status);
    return r.json();
  })
  .then(data => {
    if (!data.success) {
      showStatus('Error: ' + (data.message || 'Failed to create payment order'));
      btn.disabled = false;
      btn.innerHTML = 'Pay ₹' + AMOUNT.toLocaleString('en-IN', {minimumFractionDigits: 2});
      return;
    }

    showStatus('Opening payment gateway...');

    const cashfree = Cashfree({
      mode: CF_ENV === 'sandbox' ? 'sandbox' : 'production',
    });

    cashfree.checkout({
      paymentSessionId: data.payment_session_id,
      redirectTarget: '_self',
    });
  })
  .catch(err => {
    showStatus('Network error: ' + err.message);
    btn.disabled = false;
    btn.innerHTML = 'Pay ₹' + AMOUNT.toLocaleString('en-IN', {minimumFractionDigits: 2});
  });
}
</script>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>

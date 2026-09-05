<?php
require_once dirname(__DIR__) . '/config/database.php';

$pageTitle    = 'Order Confirmed — Urban Outfit Collection';
$pageRobots   = 'noindex, nofollow';
include dirname(__DIR__) . '/includes/header.php';

$orderId = $_SESSION['last_order_id'] ?? null;
$order = null;
if ($orderId) {
  $order = $mysqli->query("SELECT * FROM orders WHERE id = $orderId")->fetch_assoc();

  // For online orders - verify payment with Cashfree if still pending
  if ($order && $order['payment_method'] === 'online' && in_array($order['payment_status'], ['pending', 'failed'])) {
    // Try to verify payment via Cashfree API
    $envFile = dirname(__DIR__) . '/.env';
    if (file_exists($envFile)) {
      $envLines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
      foreach ($envLines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') !== false) {
          list($k, $v) = explode('=', $line, 2);
          $v = trim($v);
          if (($v[0] ?? '') === '"' && substr($v, -1) === '"') $v = substr($v, 1, -1);
          $_ENV[trim($k)] = $v;
        }
      }
    }

    // Find cf_order_id stored as payment_session_id or derive from order_number
    $cfOrderId = $order['payment_session_id'] ?? ($order['order_number'] . '_');
    // payment_session_id might be the cf_order_id — check payments endpoint
    // Use order_number pattern to find CF order
    $cfAppId = $_ENV['CF_APP_ID'] ?? '';
    $cfSecret = $_ENV['CF_SECRET_KEY'] ?? '';

    if ($cfAppId && $cfSecret) {
      // Search payments by looking up orders matching our order_number pattern
      // CF order ID was set as: $orderNumber . '_' . time() in checkout.php
      // We stored cf_order_id in payment_session_id column
      $storedCfId = $order['payment_session_id'] ?? '';
      // Only query if it looks like our order ID (ORD-... format), not CF internal ID
      if ($storedCfId && preg_match('/^ORD-/i', $storedCfId)) {
        $ch = curl_init('https://api.cashfree.com/pg/orders/' . urlencode($storedCfId));
        curl_setopt_array($ch, [
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_TIMEOUT => 10,
          CURLOPT_HTTPHEADER => [
            'x-client-id: ' . $cfAppId,
            'x-client-secret: ' . $cfSecret,
            'x-api-version: 2023-08-01',
          ],
        ]);
        $cfResponse = curl_exec($ch);
        curl_close($ch);

        if ($cfResponse) {
          $cfData = json_decode($cfResponse, true);
          $cfStatus = $cfData['order_status'] ?? '';
          if ($cfStatus === 'PAID') {
            $mysqli->query("UPDATE orders SET payment_status = 'paid', order_status = 'confirmed' WHERE id = $orderId");
            $order['payment_status'] = 'paid';
            $order['order_status'] = 'confirmed';
          } elseif (in_array($cfStatus, ['EXPIRED', 'TERMINATED'])) {
            unset($_SESSION['last_order_id']);
            header('Location: ' . BASE_URL . '/customer/payment.php?order_id=' . $orderId . '&status=failed');
            exit;
          }
        }
      }
    }

    // If still pending after verification, show order anyway (payment may still process via webhook)
  }

  unset($_SESSION['last_order_id']);
}
?>

<main style="padding-top: calc(var(--header-height) + var(--space-16)); padding-bottom: var(--space-16);">
  <div class="container">
    <div style="max-width: 600px; margin: 0 auto; text-align: center;">
      <?php if ($order): ?>
        <?php $isPaid = in_array($order['payment_status'], ['paid', 'completed']); ?>
        <div style="width: 80px; height: 80px; background: <?= $isPaid ? '#DCFCE7' : '#FEF9C3' ?>; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto var(--space-6); color: <?= $isPaid ? '#166534' : '#854D0E' ?>;">
          <?php if ($isPaid): ?>
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
          <?php else: ?>
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
          <?php endif; ?>
        </div>
        <h1 style="font-family: var(--font-display); font-size: var(--text-h2); margin-bottom: var(--space-4);"><?= $isPaid ? 'Order Confirmed!' : 'Order Placed!' ?></h1>
        <p style="color: var(--color-text-secondary); margin-bottom: var(--space-6);">
          <?php if ($isPaid): ?>
            Thank you for your purchase. Your order <strong><?= sanitize($order['order_number']) ?></strong> has been placed successfully.
          <?php else: ?>
            Your order <strong><?= sanitize($order['order_number']) ?></strong> has been placed. Payment is being processed — you'll receive a confirmation shortly.
          <?php endif; ?>
        </p>
        <div class="admin-card" style="text-align: left; margin-bottom: var(--space-8);">
          <div style="padding: var(--space-6);">
            <p><strong>Order Number:</strong> <?= sanitize($order['order_number']) ?></p>
            <p><strong>Amount:</strong> <?= formatPrice($order['grand_total']) ?></p>
            <p><strong>Payment:</strong> <?= ucfirst($order['payment_method']) ?></p>
            <p><strong>Status:</strong> <span class="status-badge status-<?= $order['order_status'] ?>"><?= ucfirst($order['order_status']) ?></span></p>
          </div>
        </div>
        <div style="display: flex; gap: var(--space-4); justify-content: center; flex-wrap: wrap;">
          <a href="<?= BASE_URL ?>/customer/orders.php" class="btn btn-primary">View Orders</a>
          <a href="<?= BASE_URL ?>/shop.php" class="btn btn-secondary">Continue Shopping</a>
        </div>
      <?php else: ?>
        <h1 style="font-family: var(--font-display); font-size: var(--text-h2); margin-bottom: var(--space-4);">No Order Found</h1>
        <p style="color: var(--color-text-secondary); margin-bottom: var(--space-8);">It looks like you haven't placed an order yet.</p>
        <a href="<?= BASE_URL ?>/shop.php" class="btn btn-primary">Start Shopping</a>
      <?php endif; ?>
    </div>
  </div>
</main>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>

<?php
require_once dirname(__DIR__) . '/config/database.php';
date_default_timezone_set('Asia/Kolkata');

if (!isset($_SESSION['customer_id'])) {
  redirect('/customer/login.php');
}

$customerId = $_SESSION['customer_id'];
$orders = [];

// ── Auto-verify pending payments via Cashfree ──
$pendingOrders = $mysqli->query("SELECT id, order_number, payment_session_id FROM orders WHERE customer_id = $customerId AND payment_method = 'online' AND payment_status = 'pending'");
if ($pendingOrders && $pendingOrders->num_rows > 0) {
  // Load env for CF keys
  $envFile = dirname(__DIR__) . '/.env';
  $cfAppId = ''; $cfSecret = '';
  if (file_exists($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
      if (strpos(trim($line), '#') === 0 || strpos($line, '=') === false) continue;
      list($k, $v) = explode('=', $line, 2);
      $v = trim($v);
      if (($v[0] ?? '') === '"' && substr($v, -1) === '"') $v = substr($v, 1, -1);
      $_ENV[trim($k)] = $v;
    }
    $cfAppId  = $_ENV['CF_APP_ID']     ?? '';
    $cfSecret = $_ENV['CF_SECRET_KEY'] ?? '';
  }
  if ($cfAppId && $cfSecret) {
    while ($po = $pendingOrders->fetch_assoc()) {
      $cfId = $po['payment_session_id'] ?? '';
      if (!$cfId) continue;
      $ch = curl_init('https://api.cashfree.com/pg/orders/' . urlencode($cfId));
      curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 8,
        CURLOPT_HTTPHEADER => [
          'x-client-id: ' . $cfAppId,
          'x-client-secret: ' . $cfSecret,
          'x-api-version: 2023-08-01',
        ],
      ]);
      $cfResp = curl_exec($ch);
      curl_close($ch);
      if ($cfResp) {
        $cfData = json_decode($cfResp, true);
        if (($cfData['order_status'] ?? '') === 'PAID') {
          $oid = (int)$po['id'];
          $mysqli->query("UPDATE orders SET payment_status='paid', order_status='confirmed' WHERE id=$oid");
        }
      }
    }
  }
}

$orderResult = $mysqli->query("SELECT * FROM orders WHERE customer_id = $customerId ORDER BY created_at DESC");
if ($orderResult) {
  while ($row = $orderResult->fetch_assoc()) {
    // Fetch items for each order
    $itemsResult = $mysqli->query("SELECT oi.product_name, oi.size, oi.quantity, oi.unit_price, oi.total_price, p.image FROM order_items oi LEFT JOIN products p ON oi.product_id = p.id WHERE oi.order_id = {$row['id']}");
    $row['items'] = $itemsResult ? $itemsResult->fetch_all(MYSQLI_ASSOC) : [];
    $orders[] = $row;
  }
}

$pageTitle = 'My Orders — urban outfit';
include dirname(__DIR__) . '/includes/header.php';
?>

<style>
/* ── Page base ── */
.ord-page {
  min-height: calc(100vh - var(--header-height, 80px));
  background: var(--color-bg);
  padding-bottom: 80px;
}

/* ── Hero banner ── */
.ord-hero {
  position: relative;
  height: 220px;
  display: flex;
  align-items: flex-end;
  overflow: hidden;
  margin-bottom: 40px;
}
.ord-hero-bg {
  position: absolute; inset: 0;
  background: url('https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=1400&h=300&fit=crop&crop=center') center/cover no-repeat;
}
.ord-hero-bg::after {
  content: '';
  position: absolute; inset: 0;
  background: linear-gradient(to right, rgba(10,10,10,0.78) 0%, rgba(10,10,10,0.3) 60%, transparent 100%);
}
.ord-hero-content {
  position: relative; z-index: 2;
  padding: 32px 60px;
  width: 100%;
}
.ord-hero-eyebrow {
  font-size: 11px; font-weight: 700; text-transform: uppercase;
  letter-spacing: 0.14em; color: var(--color-accent);
  display: flex; align-items: center; gap: 8px;
  margin-bottom: 8px;
}
.ord-hero-eyebrow::before {
  content: ''; width: 24px; height: 2px; background: var(--color-accent);
}
.ord-hero h1 {
  font-family: var(--font-display);
  font-size: clamp(28px, 3.5vw, 40px);
  font-weight: 700; color: #fff; line-height: 1.15;
  margin: 0;
}
.ord-hero h1 em { font-style: italic; color: var(--color-accent); }

/* ── Container ── */
.ord-container {
  max-width: 960px;
  margin: 0 auto;
  padding: 0 24px;
}

/* ── Breadcrumb ── */
.ord-breadcrumb {
  display: flex; align-items: center; gap: 6px;
  font-size: 12px; font-weight: 600; text-transform: uppercase;
  letter-spacing: 0.1em; color: var(--color-text-muted);
  margin-bottom: 28px;
}
.ord-breadcrumb a { color: var(--color-text-muted); text-decoration: none; transition: color 0.2s; }
.ord-breadcrumb a:hover { color: var(--color-accent); }
.ord-breadcrumb .sep { opacity: 0.4; }
.ord-breadcrumb .cur { color: var(--color-text-main); }

/* ── Stats strip ── */
.ord-stats {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 16px;
  margin-bottom: 36px;
}
.ord-stat {
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: 12px;
  padding: 20px;
  text-align: center;
  transition: all 0.25s;
  position: relative; overflow: hidden;
}
.ord-stat::before {
  content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
  background: var(--color-accent); transform: scaleX(0); transition: transform 0.25s;
}
.ord-stat:hover::before { transform: scaleX(1); }
.ord-stat:hover { box-shadow: 0 6px 20px rgba(0,0,0,0.07); transform: translateY(-2px); }
.ord-stat-num {
  font-family: var(--font-display);
  font-size: 26px; font-weight: 700; color: var(--color-text-main);
}
.ord-stat-label {
  font-size: 11px; font-weight: 600; text-transform: uppercase;
  letter-spacing: 0.06em; color: var(--color-text-muted); margin-top: 4px;
}

/* ── Section heading ── */
.ord-section-head {
  display: flex; align-items: center; gap: 12px;
  margin-bottom: 20px;
}
.ord-section-head h2 {
  font-family: var(--font-display);
  font-size: 22px; font-weight: 700; color: var(--color-text-main);
}
.ord-count-badge {
  font-size: 12px; font-weight: 700; padding: 4px 12px;
  background: var(--color-accent-light); color: var(--color-accent);
  border-radius: 999px;
}

/* ── Order card ── */
.ord-card {
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: 16px;
  margin-bottom: 20px;
  overflow: hidden;
  transition: all 0.25s;
}
.ord-card:hover { box-shadow: 0 8px 28px rgba(0,0,0,0.08); transform: translateY(-2px); }

/* Card header */
.ord-card-head {
  display: flex; align-items: center; justify-content: space-between;
  padding: 18px 24px;
  border-bottom: 1px solid var(--color-border);
  flex-wrap: wrap; gap: 12px;
  background: linear-gradient(to right, rgba(var(--color-accent-rgb, 212,175,55), 0.03), transparent);
}
.ord-card-head-left { display: flex; flex-direction: column; gap: 3px; }
.ord-num {
  font-family: var(--font-mono, monospace);
  font-size: 14px; font-weight: 700; color: var(--color-text-main);
  letter-spacing: 0.03em;
}
.ord-date { font-size: 12px; color: var(--color-text-muted); }
.ord-card-head-right { display: flex; align-items: center; gap: 10px; }

/* Status badges */
.s-badge {
  display: inline-flex; align-items: center; gap: 5px;
  font-size: 11px; font-weight: 700; text-transform: uppercase;
  letter-spacing: 0.07em; padding: 5px 14px; border-radius: 8px;
}
.s-badge::before {
  content: ''; width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0;
}
/* Order status — square-ish pill */
.s-pending    { background: #FEF3C7; color: #92400E; border: 1px solid #FCD34D; }  .s-pending::before    { background: #F59E0B; }
.s-confirmed  { background: #DBEAFE; color: #1E40AF; border: 1px solid #93C5FD; }  .s-confirmed::before  { background: #3B82F6; }
.s-processing { background: #EDE9FE; color: #5B21B6; border: 1px solid #C4B5FD; }  .s-processing::before { background: #7C3AED; }
.s-shipped    { background: #D1FAE5; color: #065F46; border: 1px solid #6EE7B7; }  .s-shipped::before    { background: #10B981; }
.s-delivered  { background: #DCFCE7; color: #166534; border: 1px solid #86EFAC; }  .s-delivered::before  { background: #16A34A; }
.s-cancelled  { background: #FEE2E2; color: #991B1B; border: 1px solid #FCA5A5; }  .s-cancelled::before  { background: #EF4444; }
.s-returned   { background: #FEF3C7; color: #92400E; border: 1px solid #FCD34D; }  .s-returned::before   { background: #F59E0B; }
/* Payment status — rounded pill, distinct shape */
.p-pending    { background: #fff7e6; color: #b45309; border: 1.5px dashed #F59E0B; border-radius: 999px; }  .p-pending::before    { background: #EAB308; }
.p-paid       { background: #DCFCE7; color: #166534; border: 1px solid #86EFAC;   border-radius: 999px; }  .p-paid::before       { background: #22C55E; box-shadow: 0 0 0 3px rgba(34,197,94,0.2); }
.p-completed  { background: #DCFCE7; color: #166534; border: 1px solid #86EFAC;   border-radius: 999px; }  .p-completed::before  { background: #22C55E; box-shadow: 0 0 0 3px rgba(34,197,94,0.2); }
.p-failed     { background: #FEE2E2; color: #991B1B; border: 1px solid #FCA5A5;   border-radius: 999px; }  .p-failed::before     { background: #EF4444; }
.p-refunded   { background: #E0E7FF; color: #3730A3; border: 1px solid #A5B4FC;   border-radius: 999px; }  .p-refunded::before   { background: #6366F1; }

/* Payment done highlight */
.payment-done-tag {
  display: inline-flex; align-items: center; gap: 6px;
  font-size: 11px; font-weight: 700; text-transform: uppercase;
  letter-spacing: 0.08em;
  padding: 5px 14px; border-radius: 999px;
  background: linear-gradient(135deg, #16A34A, #15803D);
  color: #fff;
  box-shadow: 0 2px 8px rgba(22,163,74,0.35);
}
.payment-done-tag svg { width: 12px; height: 12px; stroke: #fff; stroke-width: 2.5; }

/* Card body */
.ord-card-body { padding: 20px 24px; }

/* Product thumbnails */
.ord-thumbs { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
.ord-thumb {
  width: 58px; height: 72px;
  border-radius: 10px; overflow: hidden;
  border: 1px solid var(--color-border);
  background: #F4F0EC; flex-shrink: 0;
  position: relative;
}
.ord-thumb img { width: 100%; height: 100%; object-fit: cover; }
.ord-thumb-more {
  display: flex; align-items: center; justify-content: center;
  font-size: 12px; font-weight: 700; color: var(--color-text-muted);
  background: var(--color-bg); border: 1px dashed var(--color-border);
  width: 58px; height: 72px; border-radius: 10px; flex-shrink: 0;
}

/* Card footer */
.ord-card-foot {
  display: flex; align-items: center; justify-content: space-between;
  padding: 14px 24px;
  border-top: 1px solid var(--color-border);
  background: var(--color-bg);
  flex-wrap: wrap; gap: 10px;
}
.ord-total-label { font-size: 12px; color: var(--color-text-muted); margin-bottom: 2px; }
.ord-total-amount {
  font-family: var(--font-display);
  font-size: 22px; font-weight: 700; color: var(--color-text-main);
}
.ord-items-count {
  font-size: 12px; color: var(--color-text-muted); margin-top: 2px;
}
.ord-actions { display: flex; gap: 10px; align-items: center; }
.ord-btn {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 9px 20px; border-radius: 8px; font-size: 13px;
  font-weight: 600; text-decoration: none; transition: all 0.2s;
  font-family: var(--font-body); cursor: pointer; border: none;
}
.ord-btn-primary {
  background: var(--color-text-main); color: #fff;
}
.ord-btn-primary:hover { background: var(--color-accent); transform: translateY(-1px); }
.ord-btn-ghost {
  background: transparent; color: var(--color-text-main);
  border: 1.5px solid var(--color-border);
}
.ord-btn-ghost:hover { border-color: var(--color-text-main); background: var(--color-surface); }

/* Item details inside card */
.ord-items-detail {
  margin-top: 16px; padding-top: 16px;
  border-top: 1px solid var(--color-border);
}
.ord-item-row {
  display: flex; align-items: center; justify-content: space-between;
  padding: 10px 0; gap: 12px;
  border-bottom: 1px solid rgba(0,0,0,0.04);
  font-size: 13px;
}
.ord-item-row:last-child { border-bottom: none; }
.ord-item-info { display: flex; align-items: center; gap: 12px; flex: 1; min-width: 0; }
.ord-item-img { width: 44px; height: 54px; border-radius: 7px; overflow: hidden; flex-shrink: 0; background: #F4F0EC; }
.ord-item-img img { width: 100%; height: 100%; object-fit: cover; }
.ord-item-name {
  font-weight: 600; color: var(--color-text-main);
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.ord-item-meta { font-size: 11px; color: var(--color-text-muted); margin-top: 2px; }
.ord-item-price { font-weight: 700; color: var(--color-text-main); flex-shrink: 0; }

/* Expand toggle */
.ord-expand-btn {
  background: none; border: none; cursor: pointer;
  font-size: 12px; font-weight: 600; color: var(--color-accent);
  display: flex; align-items: center; gap: 5px;
  padding: 0; font-family: var(--font-body);
  transition: opacity 0.2s;
}
.ord-expand-btn:hover { opacity: 0.7; }
.ord-expand-btn svg { transition: transform 0.25s; }
.ord-expand-btn.expanded svg { transform: rotate(180deg); }

/* ── Empty state ── */
.ord-empty {
  text-align: center;
  padding: 80px 20px;
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: 20px;
}
.ord-empty-icon {
  width: 88px; height: 88px; border-radius: 50%;
  background: rgba(212,175,55,0.1);
  display: flex; align-items: center; justify-content: center;
  margin: 0 auto 20px;
}
.ord-empty-icon svg { width: 40px; height: 40px; stroke: var(--color-accent); }
.ord-empty h3 {
  font-family: var(--font-display); font-size: 28px; font-weight: 700;
  color: var(--color-text-main); margin-bottom: 10px;
}
.ord-empty p { color: var(--color-text-muted); font-size: 15px; margin-bottom: 28px; }

/* ── Responsive ── */
@media (max-width: 768px) {
  .ord-hero-content { padding: 24px; }
  .ord-stats { grid-template-columns: repeat(3, 1fr); gap: 10px; }
  .ord-card-head, .ord-card-foot { flex-direction: column; align-items: flex-start; }
  .ord-actions { width: 100%; justify-content: flex-end; }
}
@media (max-width: 480px) {
  .ord-stats { grid-template-columns: 1fr 1fr; }
  .ord-container { padding: 0 16px; }
}
</style>

<div class="ord-page">
  <!-- Hero -->
  <div class="ord-hero">
    <div class="ord-hero-bg"></div>
    <div class="ord-hero-content">
      <div class="ord-hero-eyebrow">Account</div>
      <h1>My <em>Orders</em></h1>
    </div>
  </div>

  <div class="ord-container">

    <!-- Breadcrumb -->
    <div class="ord-breadcrumb">
      <a href="<?= BASE_URL ?>/">Home</a>
      <span class="sep">/</span>
      <a href="<?= BASE_URL ?>/customer/account.php">Account</a>
      <span class="sep">/</span>
      <span class="cur">Orders</span>
    </div>

    <?php
    $totalSpent = 0;
    $paidCount = 0;
    foreach ($orders as $o) {
      if (!in_array($o['order_status'], ['cancelled'])) $totalSpent += $o['grand_total'];
      if (in_array($o['payment_status'], ['paid', 'completed'])) $paidCount++;
    }
    ?>

    <!-- Stats -->
    <div class="ord-stats">
      <div class="ord-stat">
        <div class="ord-stat-num"><?= count($orders) ?></div>
        <div class="ord-stat-label">Total Orders</div>
      </div>
      <div class="ord-stat">
        <div class="ord-stat-num"><?= formatPrice($totalSpent) ?></div>
        <div class="ord-stat-label">Amount Spent</div>
      </div>
      <div class="ord-stat">
        <div class="ord-stat-num"><?= $paidCount ?></div>
        <div class="ord-stat-label">Paid Orders</div>
      </div>
    </div>

    <!-- Orders list -->
    <?php if (empty($orders)): ?>
      <div class="ord-empty">
        <div class="ord-empty-icon">
          <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
        </div>
        <h3>No orders yet</h3>
        <p>You haven't placed any orders. Explore our collection and find something you love.</p>
        <a href="<?= BASE_URL ?>/shop.php" class="ord-btn ord-btn-primary" style="display:inline-flex;margin:0 auto;">
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
          Browse Collection
        </a>
      </div>

    <?php else: ?>

      <div class="ord-section-head">
        <h2>Order History</h2>
        <span class="ord-count-badge"><?= count($orders) ?> order<?= count($orders) !== 1 ? 's' : '' ?></span>
      </div>

      <?php foreach ($orders as $i => $order):
        $isPaid = in_array($order['payment_status'], ['paid', 'completed']);
        $items = $order['items'];
        $previewItems = array_slice($items, 0, 4);
        $extraCount = count($items) - 4;
        $cardId = 'order-detail-' . $order['id'];
      ?>
        <div class="ord-card">

          <!-- Head -->
          <div class="ord-card-head">
            <div class="ord-card-head-left">
              <div class="ord-num"><?= sanitize($order['order_number']) ?></div>
              <div class="ord-date">
                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:inline;vertical-align:middle;margin-right:3px;"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                <?= date('d M Y, g:i A', strtotime($order['created_at'])) ?>
              </div>
            </div>
            <div class="ord-card-head-right">
              <!-- Order status -->
              <?php
                $orderStatusLabels = [
                  'pending'    => 'Order Placed',
                  'confirmed'  => 'Confirmed',
                  'processing' => 'Processing',
                  'shipped'    => 'Shipped',
                  'delivered'  => 'Delivered',
                  'cancelled'  => 'Cancelled',
                  'returned'   => 'Returned',
                ];
                $orderLabel = $orderStatusLabels[$order['order_status']] ?? ucfirst($order['order_status']);
              ?>
              <div style="display:flex;flex-direction:column;align-items:flex-end;gap:3px;">
                <span style="font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:var(--color-text-muted);">Order</span>
                <span class="s-badge s-<?= $order['order_status'] ?>"><?= $orderLabel ?></span>
              </div>
              <!-- Payment status -->
              <div style="display:flex;flex-direction:column;align-items:flex-end;gap:3px;">
                <span style="font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:var(--color-text-muted);">Payment</span>
                <?php if ($isPaid): ?>
                  <span class="payment-done-tag">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="20 6 9 17 4 12"/></svg>
                    Done
                  </span>
                <?php elseif ($order['payment_status'] === 'failed'): ?>
                  <span class="s-badge p-failed">Failed</span>
                <?php elseif ($order['payment_status'] === 'refunded'): ?>
                  <span class="s-badge p-refunded">Refunded</span>
                <?php else: ?>
                  <span class="s-badge p-pending">Pending</span>
                <?php endif; ?>
              </div>
            </div>
          </div>

          <!-- Body — thumbnails + expand -->
          <div class="ord-card-body">
            <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
              <div class="ord-thumbs">
                <?php foreach ($previewItems as $item):
                  $img = !empty($item['image']) ? $item['image'] : 'https://via.placeholder.com/116x144?text=No+Image';
                ?>
                  <div class="ord-thumb">
                    <img src="<?= htmlspecialchars($img) ?>" alt="<?= sanitize($item['product_name']) ?>" loading="lazy">
                  </div>
                <?php endforeach; ?>
                <?php if ($extraCount > 0): ?>
                  <div class="ord-thumb-more">+<?= $extraCount ?></div>
                <?php endif; ?>
              </div>
              <?php if (!empty($items)): ?>
                <button class="ord-expand-btn" onclick="toggleOrderDetail('<?= $cardId ?>', this)" aria-label="Toggle order items">
                  View items
                  <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>
                </button>
              <?php endif; ?>
            </div>

            <!-- Collapsible item rows -->
            <div id="<?= $cardId ?>" class="ord-items-detail" style="display:none;">
              <?php foreach ($items as $item):
                $img = !empty($item['image']) ? $item['image'] : 'https://via.placeholder.com/88x108?text=No+Image';
              ?>
                <div class="ord-item-row">
                  <div class="ord-item-info">
                    <div class="ord-item-img">
                      <img src="<?= htmlspecialchars($img) ?>" alt="<?= sanitize($item['product_name']) ?>" loading="lazy">
                    </div>
                    <div style="min-width:0;">
                      <div class="ord-item-name"><?= sanitize($item['product_name']) ?></div>
                      <div class="ord-item-meta">
                        <?php if ($item['size']): ?>Size: <?= sanitize($item['size']) ?> · <?php endif; ?>
                        Qty: <?= (int)$item['quantity'] ?>
                      </div>
                    </div>
                  </div>
                  <div class="ord-item-price"><?= formatPrice($item['total_price']) ?></div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>

          <!-- Footer -->
          <div class="ord-card-foot">
            <div>
              <div class="ord-total-label">Order Total</div>
              <div class="ord-total-amount"><?= formatPrice($order['grand_total']) ?></div>
              <div class="ord-items-count"><?= count($items) ?> item<?= count($items) !== 1 ? 's' : '' ?></div>
            </div>
            <div class="ord-actions">
              <a href="<?= BASE_URL ?>/shop.php" class="ord-btn ord-btn-primary">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4zM3 6h18M16 10a4 4 0 01-8 0"/></svg>
                Shop Again
              </a>
            </div>
          </div>

        </div>
      <?php endforeach; ?>

      <!-- Back to account -->
      <div style="text-align:center;margin-top:32px;">
        <a href="<?= BASE_URL ?>/customer/account.php" style="display:inline-flex;align-items:center;gap:7px;font-size:13px;font-weight:600;color:var(--color-text-muted);text-decoration:none;padding:10px 24px;border:1px solid var(--color-border);border-radius:999px;transition:all 0.2s;" onmouseover="this.style.borderColor='var(--color-text-main)';this.style.color='var(--color-text-main)';" onmouseout="this.style.borderColor='var(--color-border)';this.style.color='var(--color-text-muted)';">
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
          Back to Account
        </a>
      </div>

    <?php endif; ?>

  </div><!-- /container -->
</div><!-- /page -->

<script>
function toggleOrderDetail(id, btn) {
  const el = document.getElementById(id);
  if (!el) return;
  const isOpen = el.style.display !== 'none';
  el.style.display = isOpen ? 'none' : 'block';
  btn.classList.toggle('expanded', !isOpen);
  btn.childNodes[0].textContent = isOpen ? 'View items' : 'Hide items';
}
</script>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>

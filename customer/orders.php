<?php
require_once dirname(__DIR__) . '/config/database.php';

if (!isset($_SESSION['customer_id'])) {
  redirect('/customer/login.php');
}

$customerId = $_SESSION['customer_id'];
$orderResult = $mysqli->query("SELECT * FROM orders WHERE customer_id = $customerId ORDER BY created_at DESC");
$orders = $orderResult ? $orderResult->fetch_all(MYSQLI_ASSOC) : [];

$pageTitle = 'My Orders — ATELIER';
include dirname(__DIR__) . '/includes/header.php';
?>

<style>
  body { background: #F5F0E8; }
  .orders-wrap {
    max-width: 900px;
    margin: 0 auto;
    padding: var(--space-8) var(--space-4) var(--space-16);
  }
  .orders-breadcrumb {
    font-size: 13px;
    color: #8B7355;
    margin-bottom: var(--space-5);
  }
  .orders-breadcrumb a { color: #8B7355; text-decoration: none; }
  .orders-breadcrumb a:hover { color: #D4AF37; }
  .orders-breadcrumb .sep { margin: 0 8px; opacity: 0.5; }
  .orders-breadcrumb .current { color: #D4AF37; font-weight: 600; }
  .orders-title {
    font-family: var(--font-display);
    font-size: clamp(28px, 3vw, 36px);
    font-weight: 400;
    font-style: italic;
    color: #1a1a1a;
    margin-bottom: var(--space-8);
  }
  .order-card {
    background: var(--color-surface);
    border: 1px solid #E8E2D8;
    border-radius: 14px;
    margin-bottom: var(--space-4);
    overflow: hidden;
    transition: var(--transition);
  }
  .order-card:hover { box-shadow: 0 4px 14px rgba(0,0,0,0.06); }
  .order-card-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: var(--space-5) var(--space-6);
    border-bottom: 1px solid #E8E2D8;
    flex-wrap: wrap;
    gap: var(--space-3);
  }
  .order-num {
    font-family: var(--font-mono);
    font-size: 14px;
    font-weight: 600;
    color: #1a1a1a;
  }
  .order-date {
    font-size: 13px;
    color: #8B7355;
  }
  .order-card-body {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: var(--space-5) var(--space-6);
    flex-wrap: wrap;
    gap: var(--space-3);
  }
  .order-status-badge {
    display: inline-block;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    padding: 5px 14px;
    border-radius: 999px;
  }
  .status-pending { background: #FEF3C7; color: #92400E; }
  .status-confirmed { background: #DBEAFE; color: #1E40AF; }
  .status-processing { background: #E0E7FF; color: #3730A3; }
  .status-shipped { background: #D1FAE5; color: #065F46; }
  .status-delivered { background: #DCFCE7; color: #166534; }
  .status-cancelled { background: #FEE2E2; color: #991B1B; }
  .order-amount {
    font-family: var(--font-display);
    font-size: 18px;
    font-weight: 700;
    color: #1a1a1a;
  }
  .order-items-preview {
    display: flex;
    gap: var(--space-3);
    flex-wrap: wrap;
  }
  .order-item-thumb {
    width: 56px;
    height: 70px;
    border-radius: 8px;
    overflow: hidden;
    background: #EDE8E0;
  }
  .order-item-thumb img { width: 100%; height: 100%; object-fit: cover; }

  /* Empty */
  .orders-empty {
    text-align: center;
    padding: 80px 0;
  }
  .orders-empty-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: rgba(212,175,55,0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto var(--space-5);
  }
  .orders-empty-icon svg { width: 36px; height: 36px; stroke: #D4AF37; }
  .orders-empty h3 {
    font-family: var(--font-display);
    font-size: 26px;
    font-weight: 700;
    margin-bottom: var(--space-2);
    color: #1a1a1a;
  }
  .orders-empty p { color: #8B7355; margin-bottom: var(--space-6); font-size: 14px; }
  .orders-empty .btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 14px 32px;
    background: #1a1a1a;
    color: #fff;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    transition: var(--transition);
    font-family: var(--font-body);
  }
  .orders-empty .btn:hover { background: #333; }

  @media (max-width: 600px) {
    .order-card-head { flex-direction: column; align-items: flex-start; }
    .order-card-body { flex-direction: column; align-items: flex-start; }
  }
</style>

<main style="padding-top: calc(var(--header-height) + var(--space-4));">
  <div class="orders-wrap">

    <div class="orders-breadcrumb">
      <a href="<?= BASE_URL ?>/">Home</a>
      <span class="sep">/</span>
      <a href="<?= BASE_URL ?>/customer/account.php">Account</a>
      <span class="sep">/</span>
      <span class="current">My Orders</span>
    </div>

    <h1 class="orders-title">My Orders</h1>

    <?php if (empty($orders)): ?>
      <div class="orders-empty">
        <div class="orders-empty-icon">
          <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
        </div>
        <h3>No orders yet</h3>
        <p>You haven't placed any orders. Start shopping to see your orders here.</p>
        <a href="<?= BASE_URL ?>/shop.php" class="btn">
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
          Browse Collection
        </a>
      </div>

    <?php else: ?>
      <?php foreach ($orders as $order): ?>
        <div class="order-card">
          <div class="order-card-head">
            <div>
              <div class="order-num"><?= sanitize($order['order_number']) ?></div>
              <div class="order-date"><?= date('M d, Y \a\t g:i A', strtotime($order['created_at'])) ?></div>
            </div>
            <span class="order-status-badge status-<?= $order['order_status'] ?>">
              <?= ucfirst(str_replace('_', ' ', $order['order_status'])) ?>
            </span>
          </div>
          <div class="order-card-body">
            <div class="order-items-preview">
              <?php
                $orderItems = $mysqli->query("SELECT oi.*, p.image FROM order_items oi LEFT JOIN products p ON oi.product_id = p.id WHERE oi.order_id = {$order['id']} LIMIT 4");
                if ($orderItems):
                  while ($oi = $orderItems->fetch_assoc()):
                    $img = $oi['image'] ?: 'https://via.placeholder.com/112x140?text=No+Image';
              ?>
                <div class="order-item-thumb">
                  <img src="<?= htmlspecialchars($img) ?>" alt="<?= sanitize($oi['product_name']) ?>" loading="lazy">
                </div>
              <?php
                  endwhile;
                endif;
              ?>
            </div>
            <div style="display: flex; align-items: center; gap: var(--space-4);">
              <div class="order-amount"><?= formatPrice($order['grand_total']) ?></div>
              <span class="order-status-badge status-<?= $order['payment_status'] ?>" style="font-size: 10px;">
                <?= ucfirst($order['payment_status']) ?>
              </span>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>

  </div>
</main>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>

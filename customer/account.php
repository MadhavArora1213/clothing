<?php
require_once dirname(__DIR__) . '/config/database.php';

if (!isset($_SESSION['customer_id'])) {
  redirect('/customer/login.php');
}

$customerId = $_SESSION['customer_id'];
$customer = $mysqli->query("SELECT * FROM customers WHERE id = $customerId")->fetch_assoc();

if (!$customer) {
  session_destroy();
  redirect('/customer/login.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $firstName = sanitize($_POST['first_name'] ?? '');
  $lastName = sanitize($_POST['last_name'] ?? '');
  $phone = sanitize($_POST['phone'] ?? '');

  if (empty($firstName) || empty($lastName)) {
    $error = 'First and last name are required.';
  } else {
    $stmt = $mysqli->prepare('UPDATE customers SET first_name = ?, last_name = ?, phone = ? WHERE id = ?');
    $stmt->bind_param('sssi', $firstName, $lastName, $phone, $customerId);
    $stmt->execute();
    $_SESSION['customer_name'] = $firstName . ' ' . $lastName;
    $success = 'Profile updated successfully.';
    $customer['first_name'] = $firstName;
    $customer['last_name'] = $lastName;
    $customer['phone'] = $phone;
  }
}

$addrResult = $mysqli->query("SELECT * FROM addresses WHERE customer_id = $customerId ORDER BY is_default DESC, created_at DESC");
$addresses = $addrResult ? $addrResult->fetch_all(MYSQLI_ASSOC) : [];
$ordResult = $mysqli->query("SELECT * FROM orders WHERE customer_id = $customerId ORDER BY created_at DESC LIMIT 5");
$orders = $ordResult ? $ordResult->fetch_all(MYSQLI_ASSOC) : [];
$ordCountResult = $mysqli->query("SELECT COUNT(*) as cnt FROM orders WHERE customer_id = $customerId");
$orderCount = $ordCountResult ? ($ordCountResult->fetch_assoc()['cnt'] ?? 0) : 0;
$spentResult = $mysqli->query("SELECT COALESCE(SUM(grand_total),0) as total FROM orders WHERE customer_id = $customerId AND order_status != 'cancelled'");
$totalSpent = $spentResult ? ($spentResult->fetch_assoc()['total'] ?? 0) : 0;
$addressCount = count($addresses);
$wishlistResult = @$mysqli->query("SELECT COUNT(*) as cnt FROM wishlists WHERE customer_id = $customerId");
$wishlistCount = $wishlistResult ? ($wishlistResult->fetch_assoc()['cnt'] ?? 0) : 0;

$pageTitle = 'My Account — ATELIER';
include dirname(__DIR__) . '/includes/header.php';
?>

<style>
  /* ── Hero ── */
  .acct-hero {
    position: relative;
    border-radius: var(--radius-xl);
    overflow: hidden;
    min-height: 380px;
    display: flex;
    align-items: flex-end;
    margin-bottom: var(--space-10);
  }
  .acct-hero-bg {
    position: absolute; inset: 0;
    background: url('https://images.unsplash.com/photo-1469334031218-e382a71b716b?w=1400&h=500&fit=crop&crop=center') center/cover no-repeat;
  }
  .acct-hero-bg::after {
    content: '';
    position: absolute; inset: 0;
    background: linear-gradient(135deg, rgba(15,15,15,0.82) 0%, rgba(15,15,15,0.35) 60%, transparent 100%);
  }
  .acct-hero-content {
    position: relative;
    z-index: 2;
    padding: var(--space-10) var(--space-8);
    width: 100%;
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: var(--space-6);
  }
  .acct-hero-left { max-width: 520px; }
  .acct-hero-eyebrow {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.14em;
    color: var(--color-accent);
    margin-bottom: var(--space-3);
    display: flex;
    align-items: center;
    gap: 8px;
  }
  .acct-hero-eyebrow::before {
    content: '';
    width: 28px;
    height: 2px;
    background: var(--color-accent);
  }
  .acct-hero h1 {
    font-family: var(--font-display);
    font-size: clamp(30px, 4vw, 44px);
    font-weight: 700;
    color: #fff;
    line-height: 1.15;
    margin-bottom: var(--space-3);
  }
  .acct-hero h1 em {
    font-style: italic;
    color: var(--color-accent);
  }
  .acct-hero p {
    color: rgba(255,255,255,0.7);
    font-size: 15px;
    line-height: 1.6;
  }
  .acct-hero-right {
    display: flex;
    gap: var(--space-3);
  }
  .acct-hero-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 28px;
    border-radius: var(--radius-full);
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    transition: var(--transition);
    border: none;
    cursor: pointer;
  }
  .acct-hero-btn.primary {
    background: var(--color-accent);
    color: #fff;
  }
  .acct-hero-btn.primary:hover { background: #C5A028; transform: translateY(-1px); }
  .acct-hero-btn.ghost {
    background: rgba(255,255,255,0.12);
    color: #fff;
    border: 1px solid rgba(255,255,255,0.25);
    backdrop-filter: blur(6px);
  }
  .acct-hero-btn.ghost:hover { background: rgba(255,255,255,0.2); }

  /* ── Stats Row ── */
  .acct-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: var(--space-4);
    margin-bottom: var(--space-10);
  }
  .acct-stat {
    background: var(--color-surface);
    border: 1px solid var(--color-border);
    border-radius: var(--radius-lg);
    padding: var(--space-5) var(--space-4);
    text-align: center;
    transition: var(--transition);
    position: relative;
    overflow: hidden;
  }
  .acct-stat::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    background: var(--color-accent);
    transform: scaleX(0);
    transition: var(--transition);
  }
  .acct-stat:hover::before { transform: scaleX(1); }
  .acct-stat:hover { box-shadow: var(--shadow-md); transform: translateY(-2px); }
  .acct-stat-num {
    font-family: var(--font-display);
    font-size: 28px;
    font-weight: 700;
    color: var(--color-text-main);
    line-height: 1.2;
  }
  .acct-stat-label {
    font-size: 12px;
    color: var(--color-text-muted);
    margin-top: 4px;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    font-weight: 500;
  }

  /* ── Categories Section ── */
  .acct-categories {
    margin-bottom: var(--space-10);
  }
  .acct-section-head {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    margin-bottom: var(--space-6);
  }
  .acct-section-head h2 {
    font-family: var(--font-display);
    font-size: 24px;
    font-weight: 700;
    color: var(--color-text-main);
  }
  .acct-section-head a {
    font-size: 13px;
    font-weight: 600;
    color: var(--color-accent);
    text-decoration: none;
    transition: var(--transition);
  }
  .acct-section-head a:hover { opacity: 0.7; }

  .cat-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: var(--space-4);
  }
  .cat-card {
    position: relative;
    border-radius: var(--radius-lg);
    overflow: hidden;
    aspect-ratio: 3/4;
    cursor: pointer;
    text-decoration: none;
  }
  .cat-card img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.6s cubic-bezier(0.16,1,0.3,1);
  }
  .cat-card:hover img { transform: scale(1.08); }
  .cat-card-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(to top, rgba(0,0,0,0.65) 0%, rgba(0,0,0,0.1) 50%, transparent 100%);
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
    padding: var(--space-5);
  }
  .cat-card-overlay h3 {
    font-family: var(--font-display);
    font-size: 18px;
    font-weight: 700;
    color: #fff;
    margin-bottom: 2px;
  }
  .cat-card-overlay span {
    font-size: 12px;
    color: rgba(255,255,255,0.7);
    font-weight: 500;
  }

  /* ── Featured Products ── */
  .acct-featured {
    margin-bottom: var(--space-10);
  }
  .feat-scroll {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: var(--space-4);
  }
  .feat-card {
    background: var(--color-surface);
    border: 1px solid var(--color-border);
    border-radius: var(--radius-lg);
    overflow: hidden;
    transition: var(--transition);
    text-decoration: none;
    color: inherit;
  }
  .feat-card:hover { box-shadow: var(--shadow-md); transform: translateY(-3px); }
  .feat-card-img {
    position: relative;
    aspect-ratio: 3/4;
    overflow: hidden;
  }
  .feat-card-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
  }
  .feat-card:hover .feat-card-img img { transform: scale(1.05); }
  .feat-card-badge {
    position: absolute;
    top: var(--space-3);
    left: var(--space-3);
    background: var(--color-accent);
    color: #fff;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    padding: 4px 10px;
    border-radius: var(--radius-full);
  }
  .feat-card-body {
    padding: var(--space-4);
  }
  .feat-card-body h4 {
    font-size: 14px;
    font-weight: 600;
    color: var(--color-text-main);
    margin-bottom: 4px;
  }
  .feat-card-body .price {
    font-family: var(--font-display);
    font-size: 16px;
    font-weight: 700;
    color: var(--color-text-main);
  }
  .feat-card-body .price-old {
    font-size: 13px;
    color: var(--color-text-muted);
    text-decoration: line-through;
    margin-left: 6px;
    font-weight: 400;
  }

  /* ── Profile & Orders Grid ── */
  .acct-bottom {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: var(--space-6);
    margin-bottom: var(--space-10);
  }
  .acct-panel {
    background: var(--color-surface);
    border: 1px solid var(--color-border);
    border-radius: var(--radius-lg);
    overflow: hidden;
  }
  .acct-panel-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: var(--space-5) var(--space-6);
    border-bottom: 1px solid var(--color-border);
  }
  .acct-panel-head h3 {
    font-family: var(--font-display);
    font-size: 18px;
    font-weight: 700;
  }
  .acct-panel-head a {
    font-size: 13px;
    font-weight: 600;
    color: var(--color-accent);
    text-decoration: none;
  }
  .acct-panel-body { padding: var(--space-6); }

  /* Profile form */
  .profile-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: var(--space-4);
    margin-bottom: var(--space-4);
  }
  .profile-row.full { grid-template-columns: 1fr; }
  .profile-field label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: var(--color-text-muted);
    text-transform: uppercase;
    letter-spacing: 0.04em;
    margin-bottom: 6px;
  }
  .profile-field input {
    width: 100%;
    padding: 10px 14px;
    border: 1px solid var(--color-border);
    border-radius: var(--radius-sm);
    font-size: 14px;
    color: var(--color-text-main);
    background: var(--color-bg);
    transition: var(--transition);
    font-family: var(--font-body);
  }
  .profile-field input:focus {
    outline: none;
    border-color: var(--color-accent);
    box-shadow: 0 0 0 3px rgba(212,175,55,0.12);
  }
  .profile-field input:disabled {
    opacity: 0.55;
    cursor: not-allowed;
    background: var(--color-border);
  }
  .profile-save-btn {
    width: 100%;
    padding: 12px;
    background: var(--color-text-main);
    color: #fff;
    border: none;
    border-radius: var(--radius-sm);
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition);
    margin-top: var(--space-2);
    font-family: var(--font-body);
  }
  .profile-save-btn:hover { background: #333; }

  /* Order items */
  .order-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: var(--space-4) 0;
    border-bottom: 1px solid var(--color-border);
    gap: var(--space-3);
  }
  .order-item:last-child { border-bottom: none; }
  .order-item-left h4 {
    font-family: var(--font-mono);
    font-size: 13px;
    font-weight: 600;
    color: var(--color-text-main);
    margin-bottom: 2px;
  }
  .order-item-left p {
    font-size: 12px;
    color: var(--color-text-muted);
  }
  .order-item-right {
    text-align: right;
    display: flex;
    align-items: center;
    gap: var(--space-3);
  }
  .order-item-amount {
    font-family: var(--font-display);
    font-size: 16px;
    font-weight: 700;
  }

  /* ── Brands Bar ── */
  .acct-brands {
    margin-bottom: var(--space-10);
    text-align: center;
  }
  .brands-row {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: var(--space-12);
    flex-wrap: wrap;
    opacity: 0.35;
    margin-top: var(--space-6);
  }
  .brands-row span {
    font-family: var(--font-display);
    font-size: 28px;
    font-weight: 700;
    color: var(--color-text-main);
    letter-spacing: 0.02em;
  }

  /* ── Empty State ── */
  .acct-empty {
    text-align: center;
    padding: var(--space-10) var(--space-4);
  }
  .acct-empty svg { width: 48px; height: 48px; stroke: var(--color-border); margin-bottom: var(--space-3); }
  .acct-empty p { font-size: 14px; color: var(--color-text-muted); margin-bottom: var(--space-4); }
  .acct-empty .btn { font-size: 13px; padding: 10px 24px; }

  /* ── Quick Actions ── */
  .acct-actions {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: var(--space-4);
    margin-bottom: var(--space-10);
  }
  .acct-action {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: var(--space-3);
    padding: var(--space-6) var(--space-4);
    background: var(--color-surface);
    border: 1px solid var(--color-border);
    border-radius: var(--radius-lg);
    text-decoration: none;
    color: var(--color-text-main);
    transition: var(--transition);
  }
  .acct-action:hover {
    border-color: var(--color-accent);
    box-shadow: var(--shadow-sm);
    transform: translateY(-2px);
  }
  .acct-action-icon {
    width: 52px;
    height: 52px;
    border-radius: var(--radius-md);
    background: var(--color-accent-light);
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .acct-action-icon svg { width: 24px; height: 24px; stroke: var(--color-accent); }
  .acct-action span {
    font-size: 13px;
    font-weight: 600;
  }

  /* ── Logout ── */
  .acct-logout {
    text-align: center;
    padding: var(--space-8) 0 var(--space-4);
  }
  .acct-logout a {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    font-weight: 500;
    color: var(--color-text-muted);
    text-decoration: none;
    padding: 10px 24px;
    border: 1px solid var(--color-border);
    border-radius: var(--radius-full);
    transition: var(--transition);
  }
  .acct-logout a:hover { border-color: #991B1B; color: #991B1B; background: #FEF2F2; }

  @media (max-width: 1024px) {
    .cat-grid { grid-template-columns: repeat(2, 1fr); }
    .feat-scroll { grid-template-columns: repeat(2, 1fr); }
    .acct-bottom { grid-template-columns: 1fr; }
  }
  @media (max-width: 768px) {
    .acct-stats { grid-template-columns: repeat(2, 1fr); }
    .acct-actions { grid-template-columns: repeat(2, 1fr); }
    .acct-hero-content { flex-direction: column; align-items: flex-start; }
    .brands-row { gap: var(--space-6); }
    .brands-row span { font-size: 20px; }
  }
  @media (max-width: 480px) {
    .cat-grid { grid-template-columns: 1fr 1fr; }
    .feat-scroll { grid-template-columns: 1fr; }
    .acct-stats { grid-template-columns: 1fr; }
  }
</style>

<main style="padding-top: calc(var(--header-height) + var(--space-6)); padding-bottom: var(--space-16);">
  <div class="container">

    <!-- ══════ HERO ══════ -->
    <div class="acct-hero">
      <div class="acct-hero-bg"></div>
      <div class="acct-hero-content">
        <div class="acct-hero-left">
          <div class="acct-hero-eyebrow">Welcome back</div>
          <h1>Hello, <em><?= sanitize($customer['first_name']) ?></em></h1>
          <p>Discover the latest arrivals, manage your orders, and explore our curated collections — all in one place.</p>
        </div>
        <div class="acct-hero-right">
          <a href="<?= BASE_URL ?>/shop.php" class="acct-hero-btn primary">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4zM3 6h18M16 10a4 4 0 01-8 0"/></svg>
            Shop Now
          </a>
          <a href="<?= BASE_URL ?>/customer/wishlist.php" class="acct-hero-btn ghost">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>
            Wishlist
          </a>
        </div>
      </div>
    </div>

    <!-- ══════ STATS ══════ -->
    <div class="acct-stats">
      <div class="acct-stat">
        <div class="acct-stat-num"><?= $orderCount ?></div>
        <div class="acct-stat-label">Orders</div>
      </div>
      <div class="acct-stat">
        <div class="acct-stat-num"><?= formatPrice($totalSpent) ?></div>
        <div class="acct-stat-label">Total Spent</div>
      </div>
      <div class="acct-stat">
        <div class="acct-stat-num"><?= $wishlistCount ?></div>
        <div class="acct-stat-label">Wishlist</div>
      </div>
      <div class="acct-stat">
        <div class="acct-stat-num"><?= $addressCount ?></div>
        <div class="acct-stat-label">Addresses</div>
      </div>
    </div>

    <!-- ══════ PROFILE + ORDERS ══════ -->
    <div class="acct-bottom">
      <!-- Profile -->
      <div class="acct-panel">
        <div class="acct-panel-head">
          <h3>Profile Details</h3>
        </div>
        <div class="acct-panel-body">
          <?php if ($error): ?>
            <div class="alert alert-error" style="margin-bottom: var(--space-4);"><?= sanitize($error) ?></div>
          <?php endif; ?>
          <?php if ($success): ?>
            <div class="alert alert-success" style="margin-bottom: var(--space-4);"><?= sanitize($success) ?></div>
          <?php endif; ?>
          <form method="POST">
            <div class="profile-row">
              <div class="profile-field">
                <label>First Name</label>
                <input type="text" name="first_name" value="<?= sanitize($customer['first_name']) ?>" required>
              </div>
              <div class="profile-field">
                <label>Last Name</label>
                <input type="text" name="last_name" value="<?= sanitize($customer['last_name']) ?>" required>
              </div>
            </div>
            <div class="profile-row full">
              <div class="profile-field">
                <label>Email Address</label>
                <input type="email" value="<?= sanitize($customer['email']) ?>" disabled>
              </div>
            </div>
            <div class="profile-row full">
              <div class="profile-field">
                <label>Phone Number</label>
                <input type="tel" name="phone" value="<?= sanitize($customer['phone'] ?? '') ?>" placeholder="+91 XXXXX XXXXX">
              </div>
            </div>
            <button type="submit" class="profile-save-btn">Save Changes</button>
          </form>
        </div>
      </div>

      <!-- Orders -->
      <div class="acct-panel">
        <div class="acct-panel-head">
          <h3>Recent Orders</h3>
          <?php if ($orderCount > 0): ?>
            <a href="<?= BASE_URL ?>/customer/orders.php">View All</a>
          <?php endif; ?>
        </div>
        <div class="acct-panel-body" style="padding-top: 0;">
          <?php if (empty($orders)): ?>
            <div class="acct-empty">
              <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z"/></svg>
              <p>No orders yet</p>
              <a href="<?= BASE_URL ?>/shop.php" class="btn btn-primary">Start Shopping</a>
            </div>
          <?php else: ?>
            <?php foreach ($orders as $order): ?>
              <div class="order-item">
                <div class="order-item-left">
                  <h4><?= sanitize($order['order_number']) ?></h4>
                  <p><?= date('M d, Y', strtotime($order['created_at'])) ?></p>
                </div>
                <div class="order-item-right">
                  <span class="order-item-amount"><?= formatPrice($order['grand_total']) ?></span>
                  <span class="status-badge status-<?= $order['order_status'] ?>"><?= ucfirst(str_replace('_', ' ', $order['order_status'])) ?></span>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- ══════ QUICK ACTIONS ══════ -->
    <div class="acct-actions">
      <a href="<?= BASE_URL ?>/customer/orders.php" class="acct-action">
        <div class="acct-action-icon">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
        </div>
        <span>My Orders</span>
      </a>
      <a href="<?= BASE_URL ?>/customer/wishlist.php" class="acct-action">
        <div class="acct-action-icon">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>
        </div>
        <span>Wishlist</span>
      </a>
      <a href="<?= BASE_URL ?>/customer/cart.php" class="acct-action">
        <div class="acct-action-icon">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4zM3 6h18M16 10a4 4 0 01-8 0"/></svg>
        </div>
        <span>Shopping Bag</span>
      </a>
      <a href="<?= BASE_URL ?>/shop.php" class="acct-action">
        <div class="acct-action-icon">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2zM12 12l6-3-6-3v6z"/></svg>
        </div>
        <span>Browse Collection</span>
      </a>
    </div>

    <!-- ══════ ADDRESSES ══════ -->
    <?php if (!empty($addresses)): ?>
      <div style="margin-bottom: var(--space-10);">
        <div class="acct-section-head">
          <h2>Saved Addresses</h2>
        </div>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: var(--space-4);">
          <?php foreach ($addresses as $address): ?>
            <div style="background: var(--color-surface); border: 1px solid var(--color-border); border-radius: var(--radius-lg); padding: var(--space-5); position: relative;">
              <?php if ($address['is_default']): ?>
                <span style="position: absolute; top: 12px; right: 12px; font-size: 10px; font-weight: 700; text-transform: uppercase; color: #166534; background: #F0FDF4; border: 1px solid #BBF7D0; padding: 2px 8px; border-radius: 999px;">Default</span>
              <?php endif; ?>
              <span style="display: inline-block; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: var(--color-accent); background: var(--color-accent-light); padding: 3px 10px; border-radius: 999px; margin-bottom: 12px;"><?= sanitize($address['label']) ?></span>
              <div style="font-weight: 600; margin-bottom: 8px;"><?= sanitize($address['full_name']) ?></div>
              <div style="font-size: 13px; color: var(--color-text-muted); line-height: 1.7;">
                <?= sanitize($address['address_line1']) ?><br>
                <?= sanitize($address['city']) ?>, <?= sanitize($address['state']) ?> - <?= sanitize($address['postal_code']) ?><br>
                <?= sanitize($address['phone']) ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>

    <!-- ══════ LOGOUT ══════ -->
    <div class="acct-logout">
      <a href="<?= BASE_URL ?>/customer/logout.php">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/></svg>
        Sign Out of Your Account
      </a>
    </div>

  </div>
</main>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>

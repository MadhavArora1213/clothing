<?php
if (!defined('BASE_URL')) {
  define('BASE_URL', '/clothing');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $pageTitle ?? 'urban outfit — Modern Luxury & Streetwear Fashion' ?></title>
  <meta name="description" content="<?= $pageDescription ?? 'Discover premium oversized drops, handcrafted ethnic fusion kurtas, resort co-ords, and modern streetwear.' ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Plus+Jakarta+Sans:ital,wght@0,300..800;1,300..800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css?v=<?= filemtime(__DIR__ . '/../css/style.css') ?>">
</head>
<body>

  <!-- Scrolling Marquee Ticker -->
  <div class="lux-ticker">
    <div class="lux-ticker-track">
      <?php
      $tickerItems = [
        'UP TO 50% OFF : THE ULTIMATE SALE IS LIVE',
        'NEW ARRIVALS EVERY DAY',
        'FREE EXPRESS SHIPPING ON ORDERS ABOVE ₹999',
        'HANDCRAFTED IN INDIA',
        '7-DAY EASY EXCHANGE',
      ];
      $all = array_merge($tickerItems, $tickerItems, $tickerItems);
      foreach ($all as $item):
      ?>
      <div class="lux-ticker-item">
        <span class="lux-ticker-text"><?= $item ?></span>
        <span class="lux-ticker-dot"></span>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Main Navbar -->
  <header class="lux-navbar" id="siteHeader">
    <div class="lux-navbar-inner">

      <!-- Mobile Menu Toggle -->
      <button class="lux-mobile-btn" id="mobileMenuBtn" aria-label="Toggle navigation menu">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><line x1="4" y1="8" x2="20" y2="8"/><line x1="4" y1="16" x2="20" y2="16"/></svg>
      </button>

      <!-- Brand Logo (Left) -->
      <a href="<?= BASE_URL ?>/" class="lux-brand">
        <div class="lux-logo-text">
          <span class="lux-logo-name">urban outfit</span>
          <span class="lux-logo-sub">fashion studio</span>
        </div>
      </a>

      <!-- Centered Navigation -->
      <nav class="lux-nav" aria-label="Main Navigation">
        <a href="<?= BASE_URL ?>/" class="<?= ($currentPage ?? '') === 'home' ? 'active' : '' ?>">Home</a>
        <a href="<?= BASE_URL ?>/shop.php?category=new">
          <span>Collection</span>
          <span class="nav-badge">New</span>
        </a>
        <a href="<?= BASE_URL ?>/pages/about.php">About Us</a>
        <a href="<?= BASE_URL ?>/pages/contact.php">Contact</a>
      </nav>

      <!-- Right Actions -->
      <div class="lux-actions">

        <!-- Search -->
        <button class="lux-icon-btn" id="searchBtn" title="Search">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        </button>

        <!-- Wishlist -->
        <a href="<?= BASE_URL ?>/customer/wishlist.php" class="lux-icon-btn" title="Wishlist">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3.33.93-4.17 2.36a.75.75 0 0 1-1.33 0C10.33 3.93 8.76 3 7 3A5.5 5.5 0 0 0 1.5 8.5c0 2.3 1.51 4.04 3 5.5l7.5 7.5L19 14z"/></svg>
        </a>

        <!-- Cart -->
        <a href="<?= BASE_URL ?>/customer/cart.php" class="lux-bag-btn" title="Cart">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
          <span class="lux-bag-counter cart-count">
            <?= isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0 ?>
          </span>
        </a>

        <!-- User -->
        <?php if (isset($_SESSION['customer_id'])): ?>
          <a href="<?= BASE_URL ?>/customer/account.php" class="lux-user-btn" title="My Account">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#D4AF37" stroke-width="1.5" stroke-linecap="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          </a>
        <?php else: ?>
          <a href="<?= BASE_URL ?>/customer/login.php" class="lux-user-btn" title="Sign In">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#D4AF37" stroke-width="1.5" stroke-linecap="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          </a>
        <?php endif; ?>

      </div>
    </div>
  </header>

  <!-- Mobile Drawer Menu -->
  <div class="lux-drawer" id="mobileDrawer">
    <div class="lux-drawer-backdrop" id="drawerBackdrop"></div>
    <div class="lux-drawer-body">
      <div class="lux-drawer-head">
        <div class="lux-brand">
          <div class="lux-logo-text">
            <span class="lux-logo-name" style="font-size:16px;">urban outfit</span>
          </div>
        </div>
        <button class="lux-close-btn" id="closeDrawerBtn">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
      </div>

      <nav class="lux-drawer-links">
        <a href="<?= BASE_URL ?>/">Home</a>
        <a href="<?= BASE_URL ?>/shop.php?category=new">Collection</a>
        <a href="<?= BASE_URL ?>/pages/about.php">About Us</a>
        <a href="<?= BASE_URL ?>/pages/contact.php">Contact</a>
      </nav>
    </div>
  </div>

  <!-- Search Modal -->
  <div class="lux-search-modal" id="searchModal">
    <div class="lux-search-backdrop" id="searchBackdrop"></div>
    <div class="lux-search-card">
      <div class="lux-search-input-wrap">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" id="searchInput" placeholder="Search for products..." autocomplete="off">
        <button class="lux-search-close" id="closeSearchBtn">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
      </div>
      <div class="lux-search-suggestions">
        <span>Popular:</span>
        <a href="<?= BASE_URL ?>/shop.php?search=oversized">Oversized Tees</a>
        <a href="<?= BASE_URL ?>/shop.php?search=linen">Linen Co-Ord</a>
        <a href="<?= BASE_URL ?>/shop.php?search=kurta">Chikankari Kurta</a>
      </div>
      <div id="searchResults" class="search-results-list"></div>
    </div>
  </div>

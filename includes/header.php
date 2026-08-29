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
        <img src="<?= BASE_URL ?>/src/Logo.png" alt="urban outfit" class="lux-logo-img">
      </a>

      <!-- Centered Navigation -->
      <nav class="lux-nav" aria-label="Main Navigation">
        <a href="<?= BASE_URL ?>/" class="<?= ($currentPage ?? '') === 'home' ? 'active' : '' ?>">Home</a>

        <!-- Collection Mega Dropdown -->
        <div class="lux-nav-item lux-mega-wrap">
          <a href="<?= BASE_URL ?>/shop.php?category=new" class="lux-nav-link">
            <span>Collection</span>
            <span class="nav-badge">New</span>
            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
          </a>
          <div class="lux-mega-dropdown">
            <div class="lux-mega-inner">
              <!-- Men -->
              <div class="lux-mega-col">
                <h4 class="lux-mega-heading">Men</h4>
                <a href="<?= BASE_URL ?>/shop.php?category=men&subcategory=oversized-tees" class="lux-mega-link">Oversized Drop Tees</a>
                <a href="<?= BASE_URL ?>/shop.php?category=men&subcategory=streetwear" class="lux-mega-link">Streetwear</a>
                <a href="<?= BASE_URL ?>/shop.php?category=men&subcategory=kurtas" class="lux-mega-link">Ethnic Fusion Kurtas</a>
                <a href="<?= BASE_URL ?>/shop.php?category=men&subcategory=co-ords" class="lux-mega-link">Resort Co-Ords</a>
                <a href="<?= BASE_URL ?>/shop.php?category=men&subcategory=shirts" class="lux-mega-link">Shirts</a>
                <a href="<?= BASE_URL ?>/shop.php?category=men&subcategory=bottoms" class="lux-mega-link">Bottoms</a>
              </div>
              <!-- Women -->
              <div class="lux-mega-col">
                <h4 class="lux-mega-heading">Women</h4>
                <a href="<?= BASE_URL ?>/shop.php?category=women&subcategory=chikankari" class="lux-mega-link">Chikankari Edit</a>
                <a href="<?= BASE_URL ?>/shop.php?category=women&subcategory=dresses" class="lux-mega-link">Dresses & Co-Ords</a>
                <a href="<?= BASE_URL ?>/shop.php?category=women&subcategory=kurtis" class="lux-mega-link">Kurtis & Sets</a>
                <a href="<?= BASE_URL ?>/shop.php?category=women&subcategory=streetwear" class="lux-mega-link">Streetwear</a>
                <a href="<?= BASE_URL ?>/shop.php?category=women&subcategory=linen" class="lux-mega-link">Linen Collection</a>
                <a href="<?= BASE_URL ?>/shop.php?category=women&subcategory=bottoms" class="lux-mega-link">Bottoms</a>
              </div>
              <!-- Kids -->
              <div class="lux-mega-col">
                <h4 class="lux-mega-heading">Kids</h4>
                <a href="<?= BASE_URL ?>/shop.php?category=kids&subcategory=boys" class="lux-mega-link">Boys</a>
                <a href="<?= BASE_URL ?>/shop.php?category=kids&subcategory=girls" class="lux-mega-link">Girls</a>
                <a href="<?= BASE_URL ?>/shop.php?category=kids&subcategory=ethnic" class="lux-mega-link">Ethnic Wear</a>
                <a href="<?= BASE_URL ?>/shop.php?category=kids&subcategory=co-ords" class="lux-mega-link">Matching Co-Ords</a>
              </div>
              <!-- Quick Links -->
              <div class="lux-mega-col lux-mega-highlight">
                <h4 class="lux-mega-heading">Explore</h4>
                <a href="<?= BASE_URL ?>/shop.php?category=new-arrivals" class="lux-mega-link">New Arrivals</a>
                <a href="<?= BASE_URL ?>/shop.php?category=bestsellers" class="lux-mega-link">Bestsellers</a>
                <a href="<?= BASE_URL ?>/shop.php?category=ethnic-fusion" class="lux-mega-link">Heritage Fusion</a>
                <a href="<?= BASE_URL ?>/shop.php?category=sale" class="lux-mega-link lux-mega-sale">Sale</a>
                <div class="lux-mega-img">
                  <img src="https://images.unsplash.com/photo-1596755094514-f87e34085b2c?w=400&h=250&fit=crop" alt="New Collection" loading="lazy">
                </div>
              </div>
            </div>
          </div>
        </div>

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
        <div class="lux-drawer-mega">
          <button class="lux-drawer-toggle" onclick="this.parentElement.classList.toggle('is-open')">
            <span>Collection</span>
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
          </button>
          <div class="lux-drawer-sub">
            <a href="<?= BASE_URL ?>/shop.php?category=new-arrivals">New Arrivals</a>
            <a href="<?= BASE_URL ?>/shop.php?category=bestsellers">Bestsellers</a>
            <a href="<?= BASE_URL ?>/shop.php?category=men">Men</a>
            <a href="<?= BASE_URL ?>/shop.php?category=women">Women</a>
            <a href="<?= BASE_URL ?>/shop.php?category=kids">Kids</a>
            <a href="<?= BASE_URL ?>/shop.php?category=ethnic-fusion">Heritage Fusion</a>
            <a href="<?= BASE_URL ?>/shop.php?category=sale">Sale</a>
          </div>
        </div>
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

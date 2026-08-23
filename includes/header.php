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
  <title><?= $pageTitle ?? 'AURA & CO. — Modern Luxury & Streetwear Fashion' ?></title>
  <meta name="description" content="<?= $pageDescription ?? 'Discover premium oversized drops, handcrafted ethnic fusion kurtas, resort co-ords, and modern streetwear.' ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Plus+Jakarta+Sans:ital,wght@0,300..800;1,300..800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
</head>
<body>

  <!-- Announcement Promo Bar -->
  <div class="aura-promo-bar">
    <div class="aura-container aura-promo-inner">
      <div class="aura-promo-tag">✨ NEW SEASON 2026 DROP</div>
      <div class="aura-promo-text">
        Use code <strong class="code-pill">WELCOME10</strong> for 10% OFF &bull; Free Express Air Shipping on orders above ₹999
      </div>
      <div class="aura-promo-links">
        <a href="<?= BASE_URL ?>/customer/order-tracking.php">Track Order</a>
        <span>|</span>
        <a href="<?= BASE_URL ?>/pages/contact.php">Help &amp; Concierge</a>
      </div>
    </div>
  </div>

  <!-- Main Site Header -->
  <header class="aura-header" id="siteHeader">
    <div class="aura-container aura-header-inner">
      
      <!-- Mobile Menu Toggle -->
      <button class="aura-mobile-btn" id="mobileMenuBtn" aria-label="Toggle navigation menu">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
      </button>

      <!-- Brand Logo -->
      <a href="<?= BASE_URL ?>/" class="aura-brand">
        <span class="aura-logo-name">AURA</span>
        <span class="aura-logo-sub">&amp; CO.</span>
      </a>

      <!-- Desktop Navigation Menu -->
      <nav class="aura-nav" aria-label="Main Navigation">
        <a href="<?= BASE_URL ?>/" class="<?= ($currentPage ?? '') === 'home' ? 'active' : '' ?>">Home</a>
        <a href="<?= BASE_URL ?>/shop.php?category=men">Men</a>
        <a href="<?= BASE_URL ?>/shop.php?category=women">Women</a>
        <a href="<?= BASE_URL ?>/shop.php?category=oversized">Oversized Drops</a>
        <a href="<?= BASE_URL ?>/shop.php?category=co-ords">Co-Ord Sets</a>
        <a href="<?= BASE_URL ?>/shop.php?category=ethnic-fusion" class="nav-highlight">
          <span>Ethnic Fusion</span>
          <span class="nav-badge">Arya Chic</span>
        </a>
        <a href="<?= BASE_URL ?>/shop.php?sale=1" class="nav-sale">50% OFF SALE</a>
      </nav>

      <!-- Header Action Icons -->
      <div class="aura-actions">
        
        <!-- Live Search Button -->
        <button class="aura-icon-btn" id="searchBtn" title="Search styles (Cmd+K)">
          <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        </button>

        <!-- Admin Portal Quick Switch Button -->
        <a href="<?= BASE_URL ?>/admin/login.php" class="aura-admin-btn" title="Open Dynamic Admin Management Panel">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg>
          <span>Admin</span>
        </a>

        <!-- Wishlist -->
        <a href="<?= BASE_URL ?>/customer/wishlist.php" class="aura-icon-btn" title="Saved Wishlist">
          <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3.33.93-4.17 2.36a.75.75 0 0 1-1.33 0C10.33 3.93 8.76 3 7 3A5.5 5.5 0 0 0 1.5 8.5c0 2.3 1.51 4.04 3 5.5l7.5 7.5L19 14z"/></svg>
        </a>

        <!-- Customer Account / Login -->
        <?php if (isset($_SESSION['customer_id'])): ?>
          <a href="<?= BASE_URL ?>/customer/account.php" class="aura-icon-btn" title="My Account">
            <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          </a>
        <?php else: ?>
          <a href="<?= BASE_URL ?>/customer/login.php" class="aura-icon-btn aura-user-btn" title="Sign In / Register">
            <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            <span class="user-label">Sign In</span>
          </a>
        <?php endif; ?>

        <!-- Cart Bag -->
        <a href="<?= BASE_URL ?>/customer/cart.php" class="aura-bag-btn" title="View Shopping Bag">
          <div class="bag-icon-wrap">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
            <span class="bag-counter cart-count">
              <?= isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0 ?>
            </span>
          </div>
          <span class="bag-label">Bag</span>
        </a>

      </div>
    </div>
  </header>

  <!-- Mobile Drawer Menu -->
  <div class="aura-drawer" id="mobileDrawer">
    <div class="aura-drawer-backdrop" id="drawerBackdrop"></div>
    <div class="aura-drawer-body">
      <div class="aura-drawer-head">
        <div class="aura-brand">
          <span class="aura-logo-name">AURA</span>
          <span class="aura-logo-sub">&amp; CO.</span>
        </div>
        <button class="aura-close-btn" id="closeDrawerBtn">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
      </div>

      <nav class="aura-drawer-links">
        <a href="<?= BASE_URL ?>/">Home</a>
        <a href="<?= BASE_URL ?>/shop.php?category=men">Men's Wardrobe</a>
        <a href="<?= BASE_URL ?>/shop.php?category=women">Women's Edit</a>
        <a href="<?= BASE_URL ?>/shop.php?category=oversized">Oversized Drop Tees</a>
        <a href="<?= BASE_URL ?>/shop.php?category=co-ords">Resort Co-Ord Sets</a>
        <a href="<?= BASE_URL ?>/shop.php?category=ethnic-fusion">Arya Ethnic Fusion</a>
        <a href="<?= BASE_URL ?>/shop.php?sale=1" class="text-sale">50% OFF Sale</a>
        
        <div class="drawer-divider"></div>
        
        <a href="<?= BASE_URL ?>/customer/order-tracking.php">Track Order Status</a>
        <a href="<?= BASE_URL ?>/pages/contact.php">Help Center / Contact Us</a>
        <a href="<?= BASE_URL ?>/admin/login.php" class="admin-link">⚙️ Admin Portal</a>
      </nav>
    </div>
  </div>

  <!-- Live Instant Search Modal -->
  <div class="aura-search-modal" id="searchModal">
    <div class="search-modal-backdrop" id="searchBackdrop"></div>
    <div class="search-modal-card">
      <div class="search-input-wrap">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" id="searchInput" placeholder="Search styles, fabrics, kurtas, oversized tees..." autocomplete="off">
        <button class="search-close-btn" id="closeSearchBtn">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
      </div>
      <div class="search-suggestions">
        <span>Popular:</span>
        <a href="<?= BASE_URL ?>/shop.php?search=oversized">Oversized Tees</a>
        <a href="<?= BASE_URL ?>/shop.php?search=linen">Linen Co-Ord</a>
        <a href="<?= BASE_URL ?>/shop.php?search=kurta">Chikankari Kurta</a>
        <a href="<?= BASE_URL ?>/shop.php?search=hoodie">Heavy Hoodie</a>
      </div>
      <div id="searchResults" class="search-results-list"></div>
    </div>
  </div>

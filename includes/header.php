<?php
if (!defined('BASE_URL')) {
  define('BASE_URL', '/clothing');
}
header("Permissions-Policy: accelerometer=(), camera=(), geolocation=(), gyroscope=(), magnetometer=(), microphone=(), payment=(), usb=()");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <?php
  $siteUrl     = 'https://urbanoutfitshop.com';
  $siteName    = 'Urban Outfit Collection';
  $siteHandle  = '@urbanoutfitshop';
  $defaultImg  = $siteUrl . '/src/og-default.jpg';

  $seoTitle    = $pageTitle       ?? 'Urban Outfit Collection — Modern Luxury & Streetwear Fashion India';
  $seoDesc     = $pageDescription ?? 'Discover premium oversized drop tees, handcrafted Chikankari ethnic fusion kurtas, resort co-ords & streetwear. Free shipping above ₹999. Made in India.';
  $seoImage    = $pageOgImage     ?? $defaultImg;
  $seoType     = $pageOgType      ?? 'website';
  $seoKeywords = $pageKeywords    ?? 'urban outfit, streetwear india, ethnic fusion kurta, oversized tee, resort co-ord, chikankari, indo western, fashion online india';

  $protocol   = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
  $host       = $_SERVER['HTTP_HOST'] ?? 'urbanoutfitshop.com';
  $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
  if ($host === 'urbanoutfitshop.com') $protocol = 'https';
  $canonicalUrl = $pageCanonical ?? ($protocol . '://' . $host . strtok($requestUri, '?'));
  if (isset($pageCanonical)) $canonicalUrl = $pageCanonical;

  $seoTitleClean = htmlspecialchars($seoTitle, ENT_QUOTES, 'UTF-8');
  $seoDescClean  = htmlspecialchars($seoDesc,  ENT_QUOTES, 'UTF-8');
  $seoImageClean = htmlspecialchars($seoImage, ENT_QUOTES, 'UTF-8');
  $canonicalClean= htmlspecialchars($canonicalUrl, ENT_QUOTES, 'UTF-8');
  ?>

  <title><?= $seoTitleClean ?></title>
  <meta name="description" content="<?= $seoDescClean ?>">
  <meta name="keywords"    content="<?= htmlspecialchars($seoKeywords, ENT_QUOTES, 'UTF-8') ?>">
  <meta name="author"      content="Urban Outfit Collection">
  <meta name="robots"      content="<?= $pageRobots ?? 'index, follow' ?>">
  <link rel="canonical"    href="<?= $canonicalClean ?>">

  <meta property="og:type"        content="<?= htmlspecialchars($seoType, ENT_QUOTES, 'UTF-8') ?>">
  <meta property="og:title"       content="<?= $seoTitleClean ?>">
  <meta property="og:description" content="<?= $seoDescClean ?>">
  <meta property="og:image"       content="<?= $seoImageClean ?>">
  <meta property="og:image:width"  content="1200">
  <meta property="og:image:height" content="630">
  <meta property="og:image:alt"   content="<?= $seoTitleClean ?>">
  <meta property="og:url"         content="<?= $canonicalClean ?>">
  <meta property="og:site_name"   content="<?= htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8') ?>">
  <meta property="og:locale"      content="en_IN">

  <meta name="twitter:card"        content="summary_large_image">
  <meta name="twitter:site"        content="<?= htmlspecialchars($siteHandle, ENT_QUOTES, 'UTF-8') ?>">
  <meta name="twitter:creator"     content="<?= htmlspecialchars($siteHandle, ENT_QUOTES, 'UTF-8') ?>">
  <meta name="twitter:title"       content="<?= $seoTitleClean ?>">
  <meta name="twitter:description" content="<?= $seoDescClean ?>">
  <meta name="twitter:image"       content="<?= $seoImageClean ?>">

  <meta name="theme-color" content="#000000">
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <meta name="apple-mobile-web-app-title" content="UOC">

  <link rel="icon"             type="image/png" href="<?= BASE_URL ?>/src/Logo.png">
  <link rel="apple-touch-icon" href="<?= BASE_URL ?>/src/Logo.png">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600&display=swap" rel="stylesheet">

  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@graph": [
      {
        "@type": "Organization",
        "@id": "<?= $siteUrl ?>/#organization",
        "name": "<?= $siteName ?>",
        "url": "<?= $siteUrl ?>",
        "logo": {
          "@type": "ImageObject",
          "url": "<?= $siteUrl ?>/src/Logo.png",
          "width": 200,
          "height": 60
        },
        "sameAs": [
          "https://instagram.com/urbanoutfitshop",
          "https://twitter.com/urbanoutfitshop"
        ],
        "contactPoint": {
          "@type": "ContactPoint",
          "contactType": "customer service",
          "availableLanguage": ["English", "Hindi"]
        },
        "address": {
          "@type": "PostalAddress",
          "addressCountry": "IN"
        }
      },
      {
        "@type": "WebSite",
        "@id": "<?= $siteUrl ?>/#website",
        "url": "<?= $siteUrl ?>",
        "name": "<?= $siteName ?>",
        "publisher": { "@id": "<?= $siteUrl ?>/#organization" },
        "potentialAction": {
          "@type": "SearchAction",
          "target": {
            "@type": "EntryPoint",
            "urlTemplate": "<?= $siteUrl ?>/shop.php?search={search_term_string}"
          },
          "query-input": "required name=search_term_string"
        }
      }
      <?php if (isset($pageSchema)): ?>,
      <?= $pageSchema ?>
      <?php endif; ?>
    ]
  }
  </script>

  <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css?v=<?= filemtime(__DIR__ . '/../css/style.css') ?>">
</head>
<body>

  <!-- Main Header -->
  <header class="uoc-header" id="siteHeader">
    <div class="uoc-header-inner">

      <!-- LEFT: Hamburger + Nav Links -->
      <div class="uoc-header-left">
        <button class="uoc-hamburger" id="mobileMenuBtn" aria-label="Toggle menu">
          <span></span>
          <span></span>
          <span></span>
        </button>
        <nav class="uoc-header-nav">
          <?php
          $currentPath = $_SERVER['REQUEST_URI'] ?? '';
          $catParam    = $_GET['category'] ?? '';
          ?>
          <a href="<?= BASE_URL ?>/men.php"
             class="uoc-nav-link <?= ($currentPage === 'men') ? 'active' : '' ?>">MEN</a>
          <a href="<?= BASE_URL ?>/women.php"
             class="uoc-nav-link <?= ($currentPage === 'women') ? 'active' : '' ?>">WOMEN</a>
          <a href="<?= BASE_URL ?>/kids.php"
             class="uoc-nav-link <?= ($currentPage === 'kids') ? 'active' : '' ?>">KIDS</a>
        </nav>
      </div>

      <!-- CENTER: Logo -->
      <a href="<?= BASE_URL ?>/" class="uoc-logo">
        <img src="<?= BASE_URL ?>/src/Logo.png" alt="Urban Outfit Collection" style="height:74px;">
      </a>

      <!-- RIGHT: Search + Icons -->
      <div class="uoc-header-right">

        <!-- Search bar -->
        <div class="uoc-search-bar" id="searchBarWrap" style="position:relative;">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
          <input type="text" id="navSearchInput" placeholder="What are you looking for?" autocomplete="off">
          <div id="navSearchDropdown" style="display:none;position:absolute;top:100%;left:0;right:0;margin-top:6px;background:#fff;border:1px solid #e5e5e5;border-radius:12px;box-shadow:0 8px 30px rgba(0,0,0,0.12);max-height:420px;overflow-y:auto;z-index:100;"></div>
        </div>

        <!-- Icon: Search (mobile only) -->
        <button class="uoc-header-icon uoc-icon-search-mobile" id="searchBtn" title="Search">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        </button>

        <!-- Icon: Account -->
        <?php if (isset($_SESSION['customer_id'])): ?>
          <a href="<?= BASE_URL ?>/customer/account.php" class="uoc-header-icon" title="Account">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          </a>
        <?php else: ?>
          <a href="<?= BASE_URL ?>/customer/login.php" class="uoc-header-icon" title="Sign In">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          </a>
        <?php endif; ?>

        <!-- Icon: Wishlist -->
        <a href="<?= BASE_URL ?>/customer/wishlist.php" class="uoc-header-icon" title="Wishlist">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3.33.93-4.17 2.36a.75.75 0 0 1-1.33 0C10.33 3.93 8.76 3 7 3A5.5 5.5 0 0 0 1.5 8.5c0 2.3 1.51 4.04 3 5.5l7.5 7.5L19 14z"/></svg>
          <?php
          $wlCount = 0;
          if (isset($_SESSION['customer_id']) && $mysqli) {
            $wlStmt = $mysqli->prepare('SELECT COUNT(*) as cnt FROM wishlists WHERE customer_id = ?');
            if ($wlStmt) { $wlStmt->bind_param('i', $_SESSION['customer_id']); $wlStmt->execute(); $wlCount = $wlStmt->get_result()->fetch_assoc()['cnt'] ?? 0; }
          }
          ?>
          <span class="uoc-badge" id="wishlistBadge" <?= $wlCount > 0 ? '' : 'style="display:none;"' ?>><?= $wlCount ?: 0 ?></span>
        </a>

        <!-- Icon: Cart -->
        <a href="<?= BASE_URL ?>/customer/cart.php" class="uoc-header-icon" title="Cart">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
          <span class="uoc-badge cart-count">
            <?php
            $cartCount = 0;
            if (isset($_SESSION['customer_id']) && $mysqli) {
              $ccStmt = $mysqli->prepare('SELECT COALESCE(SUM(ci.quantity),0) as cnt FROM carts c JOIN cart_items ci ON ci.cart_id=c.id WHERE c.customer_id=?');
              if ($ccStmt) { $ccStmt->bind_param('i', $_SESSION['customer_id']); $ccStmt->execute(); $cartCount = $ccStmt->get_result()->fetch_assoc()['cnt'] ?? 0; }
            }
            echo (int)$cartCount;
            ?>
          </span>
        </a>

      </div>
    </div>
  </header>

  <!-- Global Toast -->
  <style>
    .uoc-toast{position:fixed;top:24px;right:24px;z-index:99999;display:flex;align-items:center;gap:12px;padding:14px 20px;border-radius:12px;font-size:13px;font-weight:600;font-family:'Inter',sans-serif;box-shadow:0 8px 32px rgba(0,0,0,0.15);transform:translateX(calc(100% + 40px));opacity:0;transition:all 0.4s cubic-bezier(0.16,1,0.3,1);max-width:360px}
    .uoc-toast.show{transform:translateX(0);opacity:1}
    .uoc-toast.success{background:#fff;border-left:4px solid #22c55e;color:#166534}
    .uoc-toast.error{background:#fff;border-left:4px solid #ef4444;color:#991b1b}
    .uoc-toast.info{background:#fff;border-left:4px solid #000;color:#111}
    .uoc-toast-icon{flex-shrink:0;width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center}
    .uoc-toast.success .uoc-toast-icon{background:#dcfce7}
    .uoc-toast.error .uoc-toast-icon{background:#fee2e2}
    .uoc-toast.info .uoc-toast-icon{background:#f3f4f6}
    .uoc-toast-text{flex:1;line-height:1.4}
    .uoc-toast-close{flex-shrink:0;background:none;border:none;cursor:pointer;padding:4px;color:inherit;opacity:0.5;transition:opacity 0.2s}
    .uoc-toast-close:hover{opacity:1}
    @media(max-width:600px){.uoc-toast{top:12px;right:12px;left:12px;max-width:none}}
  </style>
  <script>
  function showToast(msg, type) {
    type = type || 'success';
    const old = document.querySelectorAll('.uoc-toast');
    old.forEach(el => el.remove());
    const icons = {
      success: '<svg width="14" height="14" fill="none" stroke="#22c55e" stroke-width="2.5" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>',
      error: '<svg width="14" height="14" fill="none" stroke="#ef4444" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>',
      info: '<svg width="14" height="14" fill="none" stroke="#111" stroke-width="2.5" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>'
    };
    const t = document.createElement('div');
    t.className = 'uoc-toast ' + type;
    t.innerHTML = '<span class="uoc-toast-icon">' + (icons[type] || icons.success) + '</span><span class="uoc-toast-text">' + msg + '</span><button class="uoc-toast-close" onclick="this.parentElement.remove()"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>';
    document.body.appendChild(t);
    requestAnimationFrame(() => requestAnimationFrame(() => t.classList.add('show')));
    setTimeout(() => { t.classList.remove('show'); setTimeout(() => t.remove(), 400); }, 3500);
  }
  </script>

  <!-- Sidebar Drawer — The Souled Store Style -->
  <div class="uoc-drawer" id="mobileDrawer">
    <div class="uoc-drawer-backdrop" id="drawerBackdrop"></div>
    <div class="uoc-drawer-body">

      <!-- Drawer Top: Logo + Login + Close -->
      <div class="drawer-top">
        <a href="<?= BASE_URL ?>/men.php" class="drawer-logo">
          <img src="<?= BASE_URL ?>/src/Logo.png" alt="Urban Outfit Collection" style="height:50px;">
        </a>
        <?php if (isset($_SESSION['customer_id'])): ?>
          <a href="<?= BASE_URL ?>/customer/account.php" class="drawer-login-btn">My Account</a>
        <?php else: ?>
          <a href="<?= BASE_URL ?>/customer/login.php" class="drawer-login-btn">Log In/Register</a>
        <?php endif; ?>
        <button class="drawer-close-btn" id="closeDrawerBtn" aria-label="Close menu">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
      </div>

      <!-- Department Tabs -->
      <div class="drawer-tabs">
        <button class="drawer-tab active" data-dept="men">MEN</button>
        <button class="drawer-tab" data-dept="women">WOMEN</button>
        <button class="drawer-tab" data-dept="kids">KIDS</button>
      </div>

      <!-- Scrollable Content -->
      <?php
      $drawerSubcats = ['men' => [], 'women' => [], 'kids' => []];
      if ($mysqli) {
        foreach (['men' => 2, 'women' => 1, 'kids' => 3] as $dept => $pid) {
          $dRes = $mysqli->query("SELECT c.id, c.name, c.slug, c.image FROM categories c WHERE c.parent_id = {$pid} AND c.is_active = 1 ORDER BY c.sort_order");
          if ($dRes) $drawerSubcats[$dept] = $dRes->fetch_all(MYSQLI_ASSOC);
        }
      }
      $placeholderImg = BASE_URL . '/src/placeholder-cat.png';
      ?>
      <div class="drawer-scroll">

        <?php foreach (['men', 'women', 'kids'] as $dept): ?>
        <div class="drawer-dept-content" id="drawerDept<?= ucfirst($dept) ?>" style="<?= $dept !== 'men' ? 'display:none' : '' ?>">

          <!-- Category Image Carousel -->
          <div class="drawer-carousel">
            <div class="drawer-carousel-track">
              <?php foreach ($drawerSubcats[$dept] as $i => $sub): ?>
              <a href="<?= BASE_URL ?>/shop.php?category=<?= htmlspecialchars($sub['slug']) ?>" class="drawer-carousel-card">
                <img src="<?= !empty($sub['image']) ? htmlspecialchars($sub['image']) : $placeholderImg ?>" alt="<?= htmlspecialchars($sub['name']) ?>" loading="lazy">
                <span><?= htmlspecialchars($sub['name']) ?></span>
              </a>
              <?php endforeach; ?>
            </div>
            <?php if (count($drawerSubcats[$dept]) > 4): ?>
            <div class="drawer-carousel-dots">
              <span class="dot active"></span><span class="dot"></span><span class="dot"></span>
            </div>
            <?php endif; ?>
          </div>

          <!-- Shop All Categories Grid -->
          <div class="drawer-section-label">Shop All</div>
          <div class="drawer-cat-grid">
            <?php foreach ($drawerSubcats[$dept] as $sub): ?>
            <a href="<?= BASE_URL ?>/shop.php?category=<?= htmlspecialchars($sub['slug']) ?>" class="drawer-cat-item">
              <img src="<?= !empty($sub['image']) ? htmlspecialchars($sub['image']) : $placeholderImg ?>" alt="<?= htmlspecialchars($sub['name']) ?>" loading="lazy">
              <span><?= htmlspecialchars($sub['name']) ?></span>
            </a>
            <?php endforeach; ?>
          </div>

        </div>
        <?php endforeach; ?>

        <!-- Accordion: More -->
        <div class="drawer-accordion">
          <button class="drawer-accordion-header" data-target="moreContent">
            <span>More</span>
            <svg class="chevron" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="6 9 12 15 18 9"/></svg>
          </button>
          <div class="drawer-accordion-body" id="moreContent">
            <div class="drawer-links-list">
              <?php if (isset($_SESSION['customer_id'])): ?>
                <a href="<?= BASE_URL ?>/customer/account.php">My Account</a>
                <a href="<?= BASE_URL ?>/customer/orders.php">My Orders</a>
                <a href="<?= BASE_URL ?>/customer/wishlist.php">My Wishlist</a>
              <?php endif; ?>
              <a href="<?= BASE_URL ?>/pages/about.php">About Us</a>
              <a href="<?= BASE_URL ?>/pages/contact.php">Contact Us</a>
              <a href="<?= BASE_URL ?>/pages/privacy.php">Privacy Policy</a>
              <a href="<?= BASE_URL ?>/pages/terms.php">Terms & Conditions</a>
              <a href="<?= BASE_URL ?>/pages/shipping.php">Shipping Info</a>
              <a href="<?= BASE_URL ?>/pages/returns.php">Returns & Exchanges</a>
            </div>
          </div>
        </div>

      </div><!-- .drawer-scroll -->
    </div><!-- .drawer-body -->
  </div>

  <!-- Search Modal -->
  <div class="uoc-search-modal" id="searchModal">
    <div class="uoc-search-backdrop" id="searchBackdrop"></div>
    <div class="uoc-search-card">
      <div class="uoc-search-input-wrap">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" id="modalSearchInput" placeholder="Search for products..." autocomplete="off">
        <button class="uoc-search-close" id="closeSearchBtn">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
      </div>
      <div id="searchResults" class="uoc-search-results"></div>
    </div>
  </div>
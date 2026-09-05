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

  <?php
  // ── SEO Variables (pages can override before including header) ──
  $siteUrl     = 'https://urbanoutfitshop.com';
  $siteName    = 'Urban Outfit Collection';
  $siteHandle  = '@urbanoutfitshop';
  $defaultImg  = $siteUrl . '/src/og-default.jpg';

  $seoTitle    = $pageTitle       ?? 'Urban Outfit Collection — Modern Luxury & Streetwear Fashion India';
  $seoDesc     = $pageDescription ?? 'Discover premium oversized drop tees, handcrafted Chikankari ethnic fusion kurtas, resort co-ords & streetwear. Free shipping above ₹999. Made in India.';
  $seoImage    = $pageOgImage     ?? $defaultImg;
  $seoType     = $pageOgType      ?? 'website';
  $seoKeywords = $pageKeywords    ?? 'urban outfit, streetwear india, ethnic fusion kurta, oversized tee, resort co-ord, chikankari, indo western, fashion online india';

  // Canonical URL: strip query params for non-shop pages, keep them for shop/product
  $protocol   = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
  $host       = $_SERVER['HTTP_HOST'] ?? 'urbanoutfitshop.com';
  $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
  // Use HTTPS on production
  if ($host === 'urbanoutfitshop.com') $protocol = 'https';
  $canonicalUrl = $pageCanonical ?? ($protocol . '://' . $host . strtok($requestUri, '?'));
  // For shop pages, include the query string in canonical
  if (isset($pageCanonical)) $canonicalUrl = $pageCanonical;

  // Clean values for HTML output
  $seoTitleClean = htmlspecialchars($seoTitle, ENT_QUOTES, 'UTF-8');
  $seoDescClean  = htmlspecialchars($seoDesc,  ENT_QUOTES, 'UTF-8');
  $seoImageClean = htmlspecialchars($seoImage, ENT_QUOTES, 'UTF-8');
  $canonicalClean= htmlspecialchars($canonicalUrl, ENT_QUOTES, 'UTF-8');
  ?>

  <!-- ═══ PRIMARY META ═══ -->
  <title><?= $seoTitleClean ?></title>
  <meta name="description" content="<?= $seoDescClean ?>">
  <meta name="keywords"    content="<?= htmlspecialchars($seoKeywords, ENT_QUOTES, 'UTF-8') ?>">
  <meta name="author"      content="Urban Outfit Collection">
  <meta name="robots"      content="<?= $pageRobots ?? 'index, follow' ?>">
  <link rel="canonical"    href="<?= $canonicalClean ?>">

  <!-- ═══ OPEN GRAPH (Facebook, WhatsApp, LinkedIn) ═══ -->
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

  <!-- ═══ TWITTER / X CARD ═══ -->
  <meta name="twitter:card"        content="summary_large_image">
  <meta name="twitter:site"        content="<?= htmlspecialchars($siteHandle, ENT_QUOTES, 'UTF-8') ?>">
  <meta name="twitter:creator"     content="<?= htmlspecialchars($siteHandle, ENT_QUOTES, 'UTF-8') ?>">
  <meta name="twitter:title"       content="<?= $seoTitleClean ?>">
  <meta name="twitter:description" content="<?= $seoDescClean ?>">
  <meta name="twitter:image"       content="<?= $seoImageClean ?>">

  <!-- ═══ MOBILE / PWA ═══ -->
  <meta name="theme-color" content="#1a1a1a">
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <meta name="apple-mobile-web-app-title" content="Urban Outfit">

  <!-- ═══ FAVICONS ═══ -->
  <link rel="icon"             type="image/png" href="<?= BASE_URL ?>/src/Logo.png">
  <link rel="apple-touch-icon" href="<?= BASE_URL ?>/src/Logo.png">

  <!-- ═══ PERFORMANCE ═══ -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="dns-prefetch" href="https://images.unsplash.com">

  <!-- ═══ SCHEMA: Organization + WebSite (every page) ═══ -->
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
              <?php
              $megaDepts = ['men' => 'Men', 'women' => 'Women', 'kids' => 'Kids'];
              foreach ($megaDepts as $deptSlug => $deptLabel):
                $megaSubs = [];
                if (isset($mysqli) && $mysqli) {
                  $megaStmt = $mysqli->prepare("SELECT name, slug FROM categories WHERE department = ? AND parent_id > 0 AND is_active = 1 ORDER BY sort_order ASC, name ASC");
                  if ($megaStmt) {
                    $megaStmt->bind_param('s', $deptSlug);
                    $megaStmt->execute();
                    $megaSubs = $megaStmt->get_result()->fetch_all(MYSQLI_ASSOC);
                  }
                }
              ?>
              <div class="lux-mega-col">
                <h4 class="lux-mega-heading"><?= $deptLabel ?></h4>
                <?php foreach ($megaSubs as $sub): ?>
                  <a href="<?= BASE_URL ?>/shop.php?category=<?= $deptSlug ?>&subcategory=<?= $sub['slug'] ?>" class="lux-mega-link"><?= htmlspecialchars($sub['name']) ?></a>
                <?php endforeach; ?>
              </div>
              <?php endforeach; ?>
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
        <a href="<?= BASE_URL ?>/customer/wishlist.php" class="lux-icon-btn" style="position:relative;" title="Wishlist">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3.33.93-4.17 2.36a.75.75 0 0 1-1.33 0C10.33 3.93 8.76 3 7 3A5.5 5.5 0 0 0 1.5 8.5c0 2.3 1.51 4.04 3 5.5l7.5 7.5L19 14z"/></svg>
          <?php if (isset($_SESSION['customer_id']) && $mysqli): ?>
            <?php
            $wlCount = 0;
            $wlStmt = $mysqli->prepare('SELECT COUNT(*) as cnt FROM wishlists WHERE customer_id = ?');
            if ($wlStmt) {
              $wlStmt->bind_param('i', $_SESSION['customer_id']);
              $wlStmt->execute();
              $wlCount = $wlStmt->get_result()->fetch_assoc()['cnt'] ?? 0;
            }
            ?>
            <?php if ($wlCount > 0): ?>
              <span id="wishlistBadge" class="lux-bag-counter" style="position:absolute;top:-6px;right:-8px;background:#dc2626;color:#fff;font-size:10px;min-width:16px;height:16px;line-height:16px;text-align:center;border-radius:8px;padding:0 4px;font-weight:700;"><?= $wlCount ?></span>
            <?php else: ?>
              <span id="wishlistBadge" class="lux-bag-counter" style="display:none;position:absolute;top:-6px;right:-8px;background:#dc2626;color:#fff;font-size:10px;min-width:16px;height:16px;line-height:16px;text-align:center;border-radius:8px;padding:0 4px;font-weight:700;">0</span>
            <?php endif; ?>
          <?php endif; ?>
        </a>

        <!-- Cart -->
        <a href="<?= BASE_URL ?>/customer/cart.php" class="lux-bag-btn" title="Cart">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
          <span class="lux-bag-counter cart-count">
            <?php
            $cartCount = 0;
            if (isset($_SESSION['customer_id']) && $mysqli) {
              $ccStmt = $mysqli->prepare('SELECT COALESCE(SUM(ci.quantity), 0) as cnt FROM carts c JOIN cart_items ci ON ci.cart_id = c.id WHERE c.customer_id = ?');
              if ($ccStmt) {
                $ccStmt->bind_param('i', $_SESSION['customer_id']);
                $ccStmt->execute();
                $cartCount = $ccStmt->get_result()->fetch_assoc()['cnt'] ?? 0;
              }
            } elseif (isset($mysqli) && $mysqli) {
              $ccStmt = $mysqli->prepare('SELECT COALESCE(SUM(ci.quantity), 0) as cnt FROM carts c JOIN cart_items ci ON ci.cart_id = c.id WHERE c.session_id = ?');
              if ($ccStmt) {
                $sid = session_id();
                $ccStmt->bind_param('s', $sid);
                $ccStmt->execute();
                $cartCount = $ccStmt->get_result()->fetch_assoc()['cnt'] ?? 0;
              }
            }
            echo $cartCount;
            ?>
          </span>
        </a>

        <!-- User -->
        <?php if (isset($_SESSION['customer_id'])): ?>
          <?php
            $custName = '';
            $custInitial = '';
            if (isset($mysqli) && $mysqli) {
              $cid = (int)$_SESSION['customer_id'];
              $custRow = $mysqli->query("SELECT first_name, last_name FROM customers WHERE id = $cid LIMIT 1");
              if ($custRow) {
                $row = $custRow->fetch_assoc();
                if ($row) {
                  $custName = trim($row['first_name']);
                  $custInitial = strtoupper(mb_substr($custName, 0, 1));
                }
              }
            }
          ?>
          <a href="<?= BASE_URL ?>/customer/account.php" class="lux-user-btn logged-in" title="My Account">
            <span class="user-avatar-circle"><?= $custInitial ?: '?' ?></span>
            <span class="user-name-label"><?= htmlspecialchars($custName) ?></span>
          </a>
        <?php else: ?>
          <a href="<?= BASE_URL ?>/customer/login.php" class="lux-user-btn" title="Sign In">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#D4AF37" stroke-width="1.5" stroke-linecap="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          </a>
        <?php endif; ?>

      </div>
    </div>
  </header>

  <!-- Global Toast Notification -->
  <style>
    .uoc-toast {
      position: fixed;
      top: 24px;
      right: 24px;
      z-index: 99999;
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 14px 20px;
      border-radius: 12px;
      font-size: 13px;
      font-weight: 600;
      font-family: var(--font-body, 'Plus Jakarta Sans');
      box-shadow: 0 8px 32px rgba(0,0,0,0.15);
      transform: translateX(calc(100% + 40px));
      opacity: 0;
      transition: all 0.4s cubic-bezier(0.16,1,0.3,1);
      max-width: 360px;
    }
    .uoc-toast.show {
      transform: translateX(0);
      opacity: 1;
    }
    .uoc-toast.success {
      background: #fff;
      border-left: 4px solid #22c55e;
      color: #166534;
    }
    .uoc-toast.error {
      background: #fff;
      border-left: 4px solid #ef4444;
      color: #991b1b;
    }
    .uoc-toast.info {
      background: #fff;
      border-left: 4px solid #D4AF37;
      color: #1a1a1a;
    }
    .uoc-toast-icon {
      flex-shrink: 0;
      width: 28px;
      height: 28px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .uoc-toast.success .uoc-toast-icon { background: #dcfce7; }
    .uoc-toast.error .uoc-toast-icon { background: #fee2e2; }
    .uoc-toast.info .uoc-toast-icon { background: #fef9c3; }
    .uoc-toast-text { flex: 1; line-height: 1.4; }
    .uoc-toast-close {
      flex-shrink: 0;
      background: none;
      border: none;
      cursor: pointer;
      padding: 4px;
      color: inherit;
      opacity: 0.5;
      transition: opacity 0.2s;
    }
    .uoc-toast-close:hover { opacity: 1; }
    @media (max-width: 600px) {
      .uoc-toast { top: 12px; right: 12px; left: 12px; max-width: none; }
    }
  </style>

  <script>
  function showToast(msg, type) {
    type = type || 'success';
    const old = document.querySelectorAll('.uoc-toast');
    old.forEach(el => el.remove());

    const icons = {
      success: '<svg width="14" height="14" fill="none" stroke="#22c55e" stroke-width="2.5" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>',
      error: '<svg width="14" height="14" fill="none" stroke="#ef4444" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>',
      info: '<svg width="14" height="14" fill="none" stroke="#D4AF37" stroke-width="2.5" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>'
    };

    const t = document.createElement('div');
    t.className = 'uoc-toast ' + type;
    t.innerHTML = '<span class="uoc-toast-icon">' + (icons[type] || icons.success) + '</span><span class="uoc-toast-text">' + msg + '</span><button class="uoc-toast-close" onclick="this.parentElement.remove()"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>';
    document.body.appendChild(t);
    requestAnimationFrame(() => requestAnimationFrame(() => t.classList.add('show')));
    setTimeout(() => { t.classList.remove('show'); setTimeout(() => t.remove(), 400); }, 3500);
  }
  </script>

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
            <?php
            $drawerDepts = ['men' => 'Men', 'women' => 'Women', 'kids' => 'Kids'];
            foreach ($drawerDepts as $dSlug => $dLabel):
              $drawerSubs = [];
              if (isset($mysqli) && $mysqli) {
                $drawerStmt = $mysqli->prepare("SELECT name, slug FROM categories WHERE department = ? AND parent_id > 0 AND is_active = 1 ORDER BY sort_order ASC, name ASC");
                if ($drawerStmt) {
                  $drawerStmt->bind_param('s', $dSlug);
                  $drawerStmt->execute();
                  $drawerSubs = $drawerStmt->get_result()->fetch_all(MYSQLI_ASSOC);
                }
              }
              if (!empty($drawerSubs)):
            ?>
            <a href="<?= BASE_URL ?>/shop.php?category=<?= $dSlug ?>" style="font-weight:700;margin-top:8px;"><?= $dLabel ?></a>
            <?php foreach ($drawerSubs as $ds): ?>
            <a href="<?= BASE_URL ?>/shop.php?category=<?= $dSlug ?>&subcategory=<?= $ds['slug'] ?>" style="padding-left:16px;"><?= htmlspecialchars($ds['name']) ?></a>
            <?php endforeach; ?>
            <?php endif; endforeach; ?>
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
  <style>
    .lux-user-btn.logged-in {
      display: flex;
      align-items: center;
      gap: 8px;
      text-decoration: none;
      transition: opacity 0.2s;
      width: auto;
      height: auto;
      overflow: visible;
      border: none;
      background: transparent;
    }
    .lux-user-btn.logged-in:hover { opacity: 0.8; }
    .user-avatar-circle {
      width: 34px;
      height: 34px;
      border-radius: 50%;
      border: 2px solid var(--color-accent, #D4AF37);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 13px;
      font-weight: 700;
      color: #fff;
      background: var(--color-accent, #D4AF37);
      font-family: var(--font-body, 'Plus Jakarta Sans');
      line-height: 1;
      flex-shrink: 0;
    }
    .user-name-label {
      font-size: 13px;
      font-weight: 600;
      color: var(--color-text-primary, #1a1a1a);
      max-width: 100px;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }
    @media (max-width: 768px) {
      .user-name-label { display: none; }
    }
  </style>
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

<?php
require_once __DIR__ . '/config/database.php';

$pageTitle = 'AURA & CO. — Modern Luxury Streetwear & Ethnic Fusion';
$pageDescription = 'Discover premium oversized drops, handcrafted ethnic fusion kurtas, resort co-ords, and modern streetwear.';
$currentPage = 'home';

// Fetch Featured & Trending products
$featured = [];
if ($mysqli) {
  $query = $mysqli->query("
    SELECT p.*, 
      (SELECT image_url FROM product_images WHERE product_id = p.id ORDER BY sort_order LIMIT 1) as image,
      (SELECT image_url FROM product_images WHERE product_id = p.id ORDER BY sort_order LIMIT 1 OFFSET 1) as hover_image
    FROM products p 
    WHERE p.is_active = 1 
    ORDER BY p.is_featured DESC, p.created_at DESC 
    LIMIT 8
  ");
  if ($query) {
    $featured = $query->fetch_all(MYSQLI_ASSOC);
  }
}

// Fallback curated products if database is fresh
if (empty($featured)) {
  $featured = [
    [
      'id' => 1,
      'name' => 'Vintage Nomad Acid-Wash Oversized Drop Tee',
      'slug' => 'vintage-nomad-acid-wash-tee',
      'price' => 1299,
      'original_price' => 2499,
      'discount_percent' => 48,
      'category_name' => 'Oversized Drops',
      'image' => 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=800&auto=format&fit=crop&q=80',
      'hover_image' => 'https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?w=800&auto=format&fit=crop&q=80',
      'is_bestseller' => 1
    ],
    [
      'id' => 2,
      'name' => 'Artisanal Hand-Block Indigo Linen Kurta Set',
      'slug' => 'artisanal-indigo-linen-kurta-set',
      'price' => 2899,
      'original_price' => 4999,
      'discount_percent' => 42,
      'category_name' => 'Ethnic Fusion',
      'image' => 'https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?w=800&auto=format&fit=crop&q=80',
      'hover_image' => 'https://images.unsplash.com/photo-1617137984095-74e4e5e3613f?w=800&auto=format&fit=crop&q=80',
      'is_bestseller' => 1
    ],
    [
      'id' => 3,
      'name' => 'Sorrento Breathable Resort Linen Co-Ord',
      'slug' => 'sorrento-resort-linen-co-ord',
      'price' => 2499,
      'original_price' => 4199,
      'discount_percent' => 40,
      'category_name' => 'Co-Ord Sets',
      'image' => 'https://images.unsplash.com/photo-1509631179647-0177331693ae?w=800&auto=format&fit=crop&q=80',
      'hover_image' => 'https://images.unsplash.com/photo-1489987707025-afc232f7ea0f?w=800&auto=format&fit=crop&q=80',
      'is_bestseller' => 0
    ],
    [
      'id' => 4,
      'name' => 'Kyoto Minimalist Drop-Shoulder Heavy Hoodie',
      'slug' => 'kyoto-minimalist-drop-shoulder-hoodie',
      'price' => 1899,
      'original_price' => 3499,
      'discount_percent' => 45,
      'category_name' => 'Streetwear',
      'image' => 'https://images.unsplash.com/photo-1556905055-8f358a7a47b2?w=800&auto=format&fit=crop&q=80',
      'hover_image' => 'https://images.unsplash.com/photo-1509967419530-da38b4704bc6?w=800&auto=format&fit=crop&q=80',
      'is_bestseller' => 1
    ],
    [
      'id' => 5,
      'name' => 'Elysian Draped Liquid Satin Maxi Evening Dress',
      'slug' => 'elysian-draped-satin-dress',
      'price' => 2199,
      'original_price' => 3999,
      'discount_percent' => 45,
      'category_name' => 'Women',
      'image' => 'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=800&auto=format&fit=crop&q=80',
      'hover_image' => 'https://images.unsplash.com/photo-1496747611176-843222e1e57c?w=800&auto=format&fit=crop&q=80',
      'is_bestseller' => 1
    ],
    [
      'id' => 6,
      'name' => 'Tactical Multi-Pocket Parachute Cargoes',
      'slug' => 'tactical-parachute-cargoes',
      'price' => 1999,
      'original_price' => 3599,
      'discount_percent' => 44,
      'category_name' => 'Streetwear',
      'image' => 'https://images.unsplash.com/photo-1517445312882-bc9910d016b7?w=800&auto=format&fit=crop&q=80',
      'hover_image' => 'https://images.unsplash.com/photo-1552374196-1ab2a1c593e8?w=800&auto=format&fit=crop&q=80',
      'is_bestseller' => 1
    ],
    [
      'id' => 7,
      'name' => 'AURA Monogram Heavyweight 16oz Canvas Tote',
      'slug' => 'aura-heavyweight-canvas-tote',
      'price' => 899,
      'original_price' => 1799,
      'discount_percent' => 50,
      'category_name' => 'Accessories',
      'image' => 'https://images.unsplash.com/photo-1544816155-12df9643f363?w=800&auto=format&fit=crop&q=80',
      'hover_image' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=800&auto=format&fit=crop&q=80',
      'is_bestseller' => 0
    ],
    [
      'id' => 8,
      'name' => 'Aura Modern Chikankari Embroidered Short Kurti',
      'slug' => 'aura-chikankari-short-kurti',
      'price' => 1799,
      'original_price' => 3299,
      'discount_percent' => 45,
      'category_name' => 'Ethnic Fusion',
      'image' => 'https://images.unsplash.com/photo-1610030469983-98e550d6193c?w=800&auto=format&fit=crop&q=80',
      'hover_image' => 'https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?w=800&auto=format&fit=crop&q=80',
      'is_bestseller' => 1
    ]
  ];
}

include __DIR__ . '/includes/header.php';
?>

<main class="aura-main">

  <!-- 1. Hero Showcase Section -->
  <section class="aura-hero">
    <div class="aura-hero-bg">
      <img src="https://images.unsplash.com/photo-1490481651871-ab68de25d43d?w=1600&auto=format&fit=crop&q=85" alt="Summer 2026 Drops" class="hero-image">
      <div class="hero-gradient"></div>
    </div>

    <div class="aura-container aura-hero-content">
      <div class="hero-badge">
        <span class="sparkle">✨</span>
        <span>Summer 2026 &bull; Arya Creation &amp; Streetwear Fusion</span>
      </div>

      <h1 class="hero-title">
        The Art of<br>Effortless Luxury
      </h1>

      <p class="hero-subtitle">
        Elevate your everyday aesthetic with breathable French flax linen, heavyweight 260+ GSM acid-wash tees, and modern handcrafted Chikankari fusion.
      </p>

      <div class="hero-buttons">
        <a href="<?= BASE_URL ?>/shop.php?new=1" class="btn btn-primary">
          <span>Shop New Arrivals</span>
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
        </a>
        <a href="<?= BASE_URL ?>/shop.php?category=ethnic-fusion" class="btn btn-glass">
          <span>Arya Ethnic Chic</span>
        </a>
        <a href="<?= BASE_URL ?>/shop.php?category=oversized" class="btn btn-glass">
          <span>Oversized Drops</span>
        </a>
      </div>

      <!-- Trust Badges Strip -->
      <div class="hero-trust">
        <div class="trust-pill">
          <span class="dot green"></span>
          <span>100% Organic Combed Cotton</span>
        </div>
        <div class="trust-pill">
          <span class="dot orange"></span>
          <span>Free Air Shipping > ₹999</span>
        </div>
        <div class="trust-pill">
          <span class="dot purple"></span>
          <span>7-Day Doorstep Exchange</span>
        </div>
      </div>
    </div>
  </section>

  <!-- 2. Explore by Category Bento -->
  <section class="aura-section bg-white">
    <div class="aura-container">
      <div class="section-head">
        <div>
          <span class="section-eyebrow">Curated Collections</span>
          <h2 class="section-title">Explore by Category</h2>
        </div>
        <a href="<?= BASE_URL ?>/shop.php" class="section-link">
          <span>View All Drops</span>
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
        </a>
      </div>

      <div class="category-bento">
        
        <!-- Card 1: Men's Streetwear -->
        <a href="<?= BASE_URL ?>/shop.php?category=men" class="bento-card bento-wide">
          <img src="https://images.unsplash.com/photo-1617137984095-74e4e5e3613f?w=800&auto=format&fit=crop&q=80" alt="Men's Collection">
          <div class="bento-overlay">
            <span class="bento-tag">URBAN LUXE</span>
            <h3 class="bento-title">Men's Wardrobe</h3>
            <span class="bento-cta">Shop Collection &rarr;</span>
          </div>
        </a>

        <!-- Card 2: Women's Edit -->
        <a href="<?= BASE_URL ?>/shop.php?category=women" class="bento-card">
          <img src="https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=800&auto=format&fit=crop&q=80" alt="Women's Collection">
          <div class="bento-overlay">
            <span class="bento-tag">CONTEMPORARY</span>
            <h3 class="bento-title">Women's Edit</h3>
            <span class="bento-cta">Shop Collection &rarr;</span>
          </div>
        </a>

        <!-- Card 3: Oversized Drops -->
        <a href="<?= BASE_URL ?>/shop.php?category=oversized" class="bento-card">
          <img src="https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?w=800&auto=format&fit=crop&q=80" alt="Oversized Drops">
          <div class="bento-overlay">
            <span class="bento-tag">260+ GSM FRENCH TERRY</span>
            <h3 class="bento-title">Oversized Drops</h3>
            <span class="bento-cta">Shop Collection &rarr;</span>
          </div>
        </a>

        <!-- Card 4: Arya Ethnic Fusion -->
        <a href="<?= BASE_URL ?>/shop.php?category=ethnic-fusion" class="bento-card bento-wide">
          <img src="https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?w=800&auto=format&fit=crop&q=80" alt="Arya Ethnic Fusion">
          <div class="bento-overlay">
            <span class="bento-tag">ARYA CREATION HERITAGE</span>
            <h3 class="bento-title">Artisanal Ethnic Fusion</h3>
            <span class="bento-cta">Shop Chikankari &amp; Kurtas &rarr;</span>
          </div>
        </a>

        <!-- Card 5: Resort Co-Ords -->
        <a href="<?= BASE_URL ?>/shop.php?category=co-ords" class="bento-card">
          <img src="https://images.unsplash.com/photo-1509631179647-0177331693ae?w=800&auto=format&fit=crop&q=80" alt="Resort Co-Ords">
          <div class="bento-overlay">
            <span class="bento-tag">BREEZY LINEN</span>
            <h3 class="bento-title">Resort Co-Ords</h3>
            <span class="bento-cta">Shop Collection &rarr;</span>
          </div>
        </a>

        <!-- Card 6: Accessories -->
        <a href="<?= BASE_URL ?>/shop.php?category=accessories" class="bento-card">
          <img src="https://images.unsplash.com/photo-1544816155-12df9643f363?w=800&auto=format&fit=crop&q=80" alt="Accessories">
          <div class="bento-overlay">
            <span class="bento-tag">HEAVY CANVAS</span>
            <h3 class="bento-title">Totes &amp; Caps</h3>
            <span class="bento-cta">Shop Collection &rarr;</span>
          </div>
        </a>

      </div>
    </div>
  </section>

  <!-- 3. Trending Now & Bestsellers -->
  <section class="aura-section">
    <div class="aura-container">
      <div class="section-head">
        <div>
          <span class="section-eyebrow">🔥 Most Wanted Right Now</span>
          <h2 class="section-title">Trending Garments</h2>
        </div>
        <div class="filter-tabs">
          <a href="<?= BASE_URL ?>/shop.php" class="tab-pill active">All Featured</a>
          <a href="<?= BASE_URL ?>/shop.php?category=oversized" class="tab-pill">Oversized</a>
          <a href="<?= BASE_URL ?>/shop.php?category=ethnic-fusion" class="tab-pill">Ethnic Fusion</a>
          <a href="<?= BASE_URL ?>/shop.php?category=co-ords" class="tab-pill">Co-Ords</a>
        </div>
      </div>

      <div class="product-grid">
        <?php foreach ($featured as $item): ?>
          <div class="aura-product-card">
            <div class="card-image-wrap">
              <a href="<?= BASE_URL ?>/product.php?slug=<?= $item['slug'] ?>">
                <img src="<?= $item['image'] ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="main-img" loading="lazy">
                <img src="<?= $item['hover_image'] ?? $item['image'] ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="hover-img" loading="lazy">
              </a>

              <!-- Badges -->
              <div class="card-badges">
                <?php if (!empty($item['discount_percent'])): ?>
                  <span class="badge-sale"><?= $item['discount_percent'] ?>% OFF</span>
                <?php endif; ?>
                <?php if (!empty($item['is_bestseller'])): ?>
                  <span class="badge-best">Bestseller</span>
                <?php endif; ?>
              </div>

              <!-- Quick Size Selector Overlay -->
              <div class="quick-size-bar">
                <span class="quick-label">QUICK ADD:</span>
                <div class="size-buttons">
                  <button onclick="quickAddToCart(<?= $item['id'] ?>, 'S')">S</button>
                  <button onclick="quickAddToCart(<?= $item['id'] ?>, 'M')">M</button>
                  <button onclick="quickAddToCart(<?= $item['id'] ?>, 'L')">L</button>
                  <button onclick="quickAddToCart(<?= $item['id'] ?>, 'XL')">XL</button>
                </div>
              </div>
            </div>

            <div class="card-details">
              <span class="card-category"><?= htmlspecialchars($item['category_name'] ?? 'Premium Essential') ?></span>
              <h4 class="card-name">
                <a href="<?= BASE_URL ?>/product.php?slug=<?= $item['slug'] ?>"><?= htmlspecialchars($item['name']) ?></a>
              </h4>
              
              <div class="card-price-row">
                <span class="price-current">₹<?= number_format($item['price']) ?></span>
                <?php if (!empty($item['original_price']) && $item['original_price'] > $item['price']): ?>
                  <span class="price-mrp">₹<?= number_format($item['original_price']) ?></span>
                  <span class="price-save">Save <?= $item['discount_percent'] ?>%</span>
                <?php endif; ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- 4. Limited Hour Flash Sale Banner -->
  <section class="aura-section bg-white py-0">
    <div class="aura-container">
      <div class="flash-banner">
        <div class="flash-content">
          <div class="flash-badge">⚡ LIMITED HOUR DROP</div>
          <h3 class="flash-title">Up to 50% OFF on Luxury Linen &amp; Heavyweight Drops</h3>
          <p class="flash-desc">Score top-rated Lucknowi Chikankari kurtas, 260 GSM mineral drop tees, and resort linen sets.</p>
          
          <div class="coupon-pill-wrap">
            <span>Use Coupon Code:</span>
            <span class="coupon-box" onclick="copyCouponCode('AURA20')">AURA20 📋</span>
          </div>
        </div>

        <div class="flash-countdown-wrap">
          <span class="countdown-label">Offer Expires In:</span>
          <div class="countdown-clock">
            <div class="clock-unit"><span class="num" id="cdHours">14</span><span class="lbl">Hours</span></div>
            <span class="colon">:</span>
            <div class="clock-unit"><span class="num" id="cdMins">35</span><span class="lbl">Mins</span></div>
            <span class="colon">:</span>
            <div class="clock-unit"><span class="num highlight" id="cdSecs">50</span><span class="lbl">Secs</span></div>
          </div>
          <a href="<?= BASE_URL ?>/shop.php?sale=1" class="btn btn-primary btn-flash">Shop Flash Sale &rarr;</a>
        </div>
      </div>
    </div>
  </section>

  <!-- 5. Artisanal Ethos & Fabric Excellence -->
  <section class="aura-section">
    <div class="aura-container">
      <div class="ethos-grid">
        <div class="ethos-visual">
          <div class="ethos-img-card">
            <img src="https://images.unsplash.com/photo-1596755094514-f87e34085b2c?w=800&auto=format&fit=crop&q=80" alt="Handcrafted Linen">
          </div>
          <div class="ethos-stat-card">
            <span class="stat-number">260+</span>
            <span class="stat-label">GSM French Terry Cotton</span>
            <p>Architectural drape that stays pristine through 50+ washes.</p>
          </div>
        </div>

        <div class="ethos-text">
          <span class="section-eyebrow">Ethos &amp; Craftsmanship</span>
          <h2 class="section-title">Where Arya Heritage Meets Urban Streetwear</h2>
          <p class="ethos-body">
            At <strong>AURA &amp; CO.</strong>, we fuse the intricate hand-block printing and tone-on-tone Chikankari of classic Indian couturiers with the relaxed, drop-shoulder silhouettes of contemporary urban street culture.
          </p>

          <div class="ethos-features">
            <div class="feature-item">
              <div class="feature-icon">🌿</div>
              <div>
                <h4>Pure Organic Linen &amp; Bio-Washed Cotton</h4>
                <p>Gentle on skin, pre-shrunk, and engineered for high-humidity breathability.</p>
              </div>
            </div>

            <div class="feature-item">
              <div class="feature-icon">📦</div>
              <div>
                <h4>Plastic-Free Matte Packaging</h4>
                <p>Dispatched in 100% recyclable bespoke paper mailers and boxes.</p>
              </div>
            </div>
          </div>

          <a href="<?= BASE_URL ?>/shop.php?category=ethnic-fusion" class="btn btn-dark">
            <span>Explore The Heritage Fusion Edit</span>
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
          </a>
        </div>
      </div>
    </div>
  </section>

</main>

<script>
// Live Countdown Timer Script
function startCountdown() {
  let h = 14, m = 35, s = 50;
  setInterval(() => {
    if (s > 0) { s--; }
    else {
      s = 59;
      if (m > 0) { m--; }
      else { m = 59; if (h > 0) { h--; } }
    }
    const hEl = document.getElementById('cdHours');
    const mEl = document.getElementById('cdMins');
    const sEl = document.getElementById('cdSecs');
    if (hEl) hEl.textContent = String(h).padStart(2, '0');
    if (mEl) mEl.textContent = String(m).padStart(2, '0');
    if (sEl) sEl.textContent = String(s).padStart(2, '0');
  }, 1000);
}
startCountdown();

function copyCouponCode(code) {
  navigator.clipboard.writeText(code);
  alert('🎉 Promo Code ' + code + ' copied to clipboard! Apply at checkout.');
}

function quickAddToCart(productId, size) {
  fetch('<?= BASE_URL ?>/api/cart.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'action=add&product_id=' + productId + '&size=' + encodeURIComponent(size) + '&quantity=1'
  }).then(r => r.json()).then(data => {
    if (data.success) {
      const badges = document.querySelectorAll('.cart-count');
      badges.forEach(b => b.textContent = data.cart_count || 1);
      alert('✓ Added size ' + size + ' to your bag!');
    } else {
      window.location.href = '<?= BASE_URL ?>/customer/cart.php';
    }
  }).catch(() => {
    window.location.href = '<?= BASE_URL ?>/customer/cart.php';
  });
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>

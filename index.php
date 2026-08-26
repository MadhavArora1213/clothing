<?php
require_once __DIR__ . '/config/database.php';

$pageTitle = 'urban outfit — Modern Luxury Streetwear & Ethnic Fusion';
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

  <!-- ===== HERO SECTION — Editorial Fashion ===== -->
  <section class="lux-hero">
    <div class="lux-hero-wrapper">

      <!-- Large title -->
      <h1 class="lux-hero-title">Your Personal Style</h1>

      <!-- Three arched images -->
      <div class="lux-hero-images">

        <!-- Left image -->
        <div class="lux-hero-img lux-hero-img-left">
          <div class="lux-arch-frame">
            <img src="https://images.unsplash.com/photo-1469334031218-e382a71b716b?w=400&auto=format&fit=crop&q=80" alt="Style 1" loading="eager">
          </div>
        </div>

        <!-- Center image (largest) -->
        <div class="lux-hero-img lux-hero-img-center">
          <div class="lux-arch-bg"></div>
          <img src="https://images.unsplash.com/photo-1483985988355-763728e1935b?w=500&auto=format&fit=crop&q=80" alt="Featured" loading="eager">
        </div>

        <!-- Right image -->
        <div class="lux-hero-img lux-hero-img-right">
          <div class="lux-arch-frame">
            <img src="https://images.unsplash.com/photo-1487222477894-8943e31ef7b2?w=400&auto=format&fit=crop&q=80" alt="Style 2" loading="eager">
          </div>
          <!-- Decorative squiggly -->
          <svg class="lux-deco-squig" width="50" height="40" viewBox="0 0 50 40" fill="none">
            <path d="M5 35C10 5 20 35 28 15C36 -5 45 20 45 20" stroke="#B8956A" stroke-width="1.5" fill="none" stroke-linecap="round"/>
          </svg>
        </div>
      </div>

      <!-- Bottom section -->
      <div class="lux-hero-bottom">
        <div class="lux-hero-desc">
          <p>Clothings, accessories, and shoes that are designed to help you express your unique personality and individuality</p>
        </div>

        <div class="lux-hero-dots">
          <span class="lux-dot"></span>
          <span class="lux-dot"></span>
          <span class="lux-dot active"></span>
          <span class="lux-dot"></span>
          <span class="lux-dot"></span>
        </div>

        <div class="lux-hero-right-area">
          <!-- Decorative star -->
          <svg class="lux-deco-star" width="28" height="28" viewBox="0 0 28 28" fill="none">
            <path d="M14 0L17 11L28 14L17 17L14 28L11 17L0 14L11 11L14 0Z" fill="#B8956A"/>
          </svg>
          <!-- Arrow button -->
          <a href="<?= BASE_URL ?>/shop.php" class="lux-arrow-btn">
            <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
              <path d="M3.5 9H14.5M14.5 9L9.5 4M14.5 9L9.5 14" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </a>
        </div>

        <div class="lux-hero-stat">
          <span class="lux-stat-num">4.2K</span>
          <span class="lux-stat-text">Customer</span>
        </div>
      </div>

    </div>
  </section>

  <!-- ===== COLLECTION CARDS ===== -->
  <section class="lux-collections">
    <div class="aura-container">
      <div class="lux-collections-grid">
        <a href="<?= BASE_URL ?>/shop.php?category=women" class="lux-col-card">
          <div class="lux-col-img">
            <img src="https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=600&auto=format&fit=crop&q=80" alt="Women" loading="lazy">
          </div>
          <div class="lux-col-info">
            <h3>Women</h3>
            <span>Shop Now &rarr;</span>
          </div>
        </a>
        <a href="<?= BASE_URL ?>/shop.php?category=men" class="lux-col-card">
          <div class="lux-col-img">
            <img src="https://images.unsplash.com/photo-1617137984095-74e4e5e3613f?w=600&auto=format&fit=crop&q=80" alt="Men" loading="lazy">
          </div>
          <div class="lux-col-info">
            <h3>Men</h3>
            <span>Shop Now &rarr;</span>
          </div>
        </a>
        <a href="<?= BASE_URL ?>/shop.php?category=kids" class="lux-col-card">
          <div class="lux-col-img">
            <img src="https://images.unsplash.com/photo-1503944583220-79d8926ad5e2?w=600&auto=format&fit=crop&q=80" alt="Kids" loading="lazy">
          </div>
          <div class="lux-col-info">
            <h3>Kids</h3>
            <span>Shop Now &rarr;</span>
          </div>
        </a>
      </div>
    </div>
  </section>

  <!-- ===== MARQUEE STRIP ===== -->
  <div class="lux-marquee">
    <div class="lux-marquee-track">
      <?php
      $items = ['Premium Streetwear','260+ GSM French Terry','Arya Heritage Chikankari','Organic Linen Fusion','Handcrafted in India','Plastic-Free Packaging','Free Express Shipping','7-Day Easy Exchange'];
      $all = array_merge($items, $items, $items);
      foreach ($all as $item):
      ?>
      <div class="lux-marquee-item">
        <span class="lux-marquee-dot">&#10022;</span>
        <span class="lux-marquee-text"><?= $item ?></span>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- ===== CATEGORIES SECTION ===== -->
  <section class="lux-section">
    <div class="aura-container">
      <div class="lux-section-head reveal">
        <div>
          <span class="lux-eyebrow">Curated Collections</span>
          <h2 class="lux-section-title">Shop by Category</h2>
        </div>
        <a href="<?= BASE_URL ?>/shop.php" class="lux-view-all">
          View All Drops
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
        </a>
      </div>

      <!-- Modern Asymmetric Grid -->
      <div class="lux-cat-grid">

        <!-- Hero Category Card -->
        <a href="<?= BASE_URL ?>/shop.php?category=ethnic-fusion" class="lux-cat-card lux-cat-hero reveal reveal-delay-1">
          <img src="https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?w=900&auto=format&fit=crop&q=80" alt="Arya Ethnic Fusion">
          <div class="lux-cat-glass">
            <span class="lux-cat-eyebrow">Heritage Collection</span>
            <h3 class="lux-cat-name">Artisanal Fusion</h3>
            <span class="lux-cat-btn">Discover</span>
          </div>
        </a>

        <!-- Right Stack -->
        <div class="lux-cat-stack">
          <a href="<?= BASE_URL ?>/shop.php?category=men" class="lux-cat-card lux-cat-sm reveal reveal-delay-2">
            <img src="https://images.unsplash.com/photo-1617137984095-74e4e5e3613f?w=600&auto=format&fit=crop&q=80" alt="Men's Collection">
            <div class="lux-cat-glass">
              <span class="lux-cat-eyebrow">The Wardrobe</span>
              <h3 class="lux-cat-name">Men's Edit</h3>
              <span class="lux-cat-btn">Discover</span>
            </div>
          </a>
          <a href="<?= BASE_URL ?>/shop.php?category=women" class="lux-cat-card lux-cat-sm reveal reveal-delay-3">
            <img src="https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=600&auto=format&fit=crop&q=80" alt="Women's Collection">
            <div class="lux-cat-glass">
              <span class="lux-cat-eyebrow">Contemporary</span>
              <h3 class="lux-cat-name">Women's Edit</h3>
              <span class="lux-cat-btn">Discover</span>
            </div>
          </a>
        </div>

        <!-- Bottom Row -->
        <a href="<?= BASE_URL ?>/shop.php?category=oversized" class="lux-cat-card lux-cat-wide reveal reveal-delay-1">
          <img src="https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?w=700&auto=format&fit=crop&q=80" alt="Oversized Drops">
          <div class="lux-cat-glass">
            <span class="lux-cat-eyebrow">Premium Weight</span>
            <h3 class="lux-cat-name">Oversized Drops</h3>
            <span class="lux-cat-btn">Discover</span>
          </div>
        </a>
        <a href="<?= BASE_URL ?>/shop.php?category=co-ords" class="lux-cat-card lux-cat-wide reveal reveal-delay-2">
          <img src="https://images.unsplash.com/photo-1509631179647-0177331693ae?w=700&auto=format&fit=crop&q=80" alt="Co-Ords">
          <div class="lux-cat-glass">
            <span class="lux-cat-eyebrow">Breezy Linen</span>
            <h3 class="lux-cat-name">Resort Co-Ords</h3>
            <span class="lux-cat-btn">Discover</span>
          </div>
        </a>
        <a href="<?= BASE_URL ?>/shop.php?category=accessories" class="lux-cat-card lux-cat-wide reveal reveal-delay-3">
          <img src="https://images.unsplash.com/photo-1544816155-12df9643f363?w=700&auto=format&fit=crop&q=80" alt="Accessories">
          <div class="lux-cat-glass">
            <span class="lux-cat-eyebrow">The Details</span>
            <h3 class="lux-cat-name">Accessories</h3>
            <span class="lux-cat-btn">Discover</span>
          </div>
        </a>

      </div>
    </div>
  </section>

  <!-- ===== TRENDING PRODUCTS ===== -->
  <section class="lux-section lux-section-cream">
    <div class="aura-container">
      <div class="lux-section-head reveal">
        <div>
          <span class="lux-eyebrow">Most Wanted Right Now</span>
          <h2 class="lux-section-title">Trending Garments</h2>
        </div>
        <a href="<?= BASE_URL ?>/shop.php" class="lux-view-all">
          View All
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
        </a>
      </div>

      <div class="lux-product-grid">
        <?php foreach ($featured as $i => $item): ?>
        <div class="lux-product-card reveal reveal-delay-<?= ($i % 4) + 1 ?>">
          <div class="lux-pcard-img">
            <a href="<?= BASE_URL ?>/product.php?slug=<?= $item['slug'] ?>">
              <img src="<?= $item['image'] ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="lux-img-main" loading="lazy">
              <img src="<?= $item['hover_image'] ?? $item['image'] ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="lux-img-hover" loading="lazy">
            </a>

            <!-- Badges -->
            <div class="lux-badges">
              <?php if (!empty($item['discount_percent'])): ?>
              <span class="lux-badge-sale"><?= $item['discount_percent'] ?>% OFF</span>
              <?php endif; ?>
              <?php if (!empty($item['is_bestseller'])): ?>
              <span class="lux-badge-hot">BESTSELLER</span>
              <?php endif; ?>
            </div>

            <!-- Wishlist -->
            <button class="lux-wishlist" onclick="event.preventDefault(); toggleWishlist(<?= $item['id'] ?>, this)" title="Add to Wishlist">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3.33.93-4.17 2.36a.75.75 0 0 1-1.33 0C10.33 3.93 8.76 3 7 3A5.5 5.5 0 0 0 1.5 8.5c0 2.3 1.51 4.04 3 5.5l7.5 7.5L19 14z"/></svg>
            </button>

            <!-- Quick Add Glassmorphic Drawer -->
            <div class="lux-quick-add">
              <span class="lux-qa-label">Quick Add</span>
              <div class="lux-size-row">
                <button onclick="quickAddToCart(<?= $item['id'] ?>, 'S')">S</button>
                <button onclick="quickAddToCart(<?= $item['id'] ?>, 'M')">M</button>
                <button onclick="quickAddToCart(<?= $item['id'] ?>, 'L')">L</button>
                <button onclick="quickAddToCart(<?= $item['id'] ?>, 'XL')">XL</button>
              </div>
            </div>
          </div>

          <div class="lux-pcard-body">
            <span class="lux-pcard-cat"><?= htmlspecialchars($item['category_name'] ?? 'Premium') ?></span>
            <h4 class="lux-pcard-name">
              <a href="<?= BASE_URL ?>/product.php?slug=<?= $item['slug'] ?>"><?= htmlspecialchars($item['name']) ?></a>
            </h4>
            <div class="lux-pcard-price">
              <span class="lux-price-now">&#8377;<?= number_format($item['price']) ?></span>
              <?php if (!empty($item['original_price']) && $item['original_price'] > $item['price']): ?>
              <span class="lux-price-was">&#8377;<?= number_format($item['original_price']) ?></span>
              <span class="lux-price-save">Save <?= $item['discount_percent'] ?>%</span>
              <?php endif; ?>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>

      <div class="lux-see-more-wrap reveal">
        <a href="<?= BASE_URL ?>/shop.php" class="lux-see-more-btn">
          <span>View All Products</span>
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
        </a>
      </div>
    </div>
  </section>

  <!-- ===== CURATED EDIT — Split-Screen Parallax ===== -->
  <section class="lux-curated">
    <div class="lux-curated-grid">
      <!-- Visual Side -->
      <div class="lux-curated-visual">
        <img src="https://images.unsplash.com/photo-1596755094514-f87e34085b2c?w=1000&auto=format&fit=crop&q=80" alt="The Signature Collection" loading="lazy">
        <div class="lux-curated-visual-overlay"></div>
      </div>

      <!-- Text Side -->
      <div class="lux-curated-text reveal-right">
        <div class="lux-curated-eyebrow">The Archives</div>
        <h3 class="lux-curated-title">The <span class="lux-curated-highlight">Signature</span> Collection</h3>
        <p class="lux-curated-desc">
          Discover our highest-rated Lucknowi Chikankari pieces, 260+ GSM oversized drops, and luxurious resort linen sets. Crafted for longevity and timeless appeal.
        </p>
        <div class="lux-coupon-row">
          <span>Complimentary Shipping on all orders.</span>
        </div>
        <a href="<?= BASE_URL ?>/shop.php?category=ethnic-fusion" class="lux-curated-cta">
          Explore The Collection
        </a>
      </div>
    </div>
  </section>

  <!-- ===== ETHOS SECTION ===== -->
  <section class="lux-section">
    <div class="aura-container">
      <div class="lux-ethos-grid">
        <!-- Visual Side -->
        <div class="lux-ethos-visual reveal-left">
          <div class="lux-ethos-img-main">
            <img src="https://images.unsplash.com/photo-1596755094514-f87e34085b2c?w=800&auto=format&fit=crop&q=80" alt="Premium Craftsmanship" loading="lazy">
          </div>
          <div class="lux-ethos-img-accent">
            <img src="https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?w=400&auto=format&fit=crop&q=80" alt="Ethnic Fusion Fabric" loading="lazy">
          </div>
        </div>

        <!-- Text Side -->
        <div class="lux-ethos-text reveal-right">
          <span class="lux-eyebrow">Ethos & Craftsmanship</span>
          <h2 class="lux-section-title">Where Arya Heritage Meets<br><em>Urban Streetwear</em></h2>
          <p class="lux-ethos-body">
            At <strong>urban outfit</strong>, we fuse the intricate hand-block printing and tone-on-tone Chikankari of classic Indian couturiers with relaxed, drop-shoulder silhouettes of contemporary urban street culture.
          </p>

          <div class="lux-ethos-features">
            <div class="lux-feature">
              <div class="lux-feature-icon">&#127807;</div>
              <div class="lux-feature-text">
                <h4>Pure Organic Linen & Bio-Washed Cotton</h4>
                <p>Gentle on skin, pre-shrunk, engineered for high-humidity breathability.</p>
              </div>
            </div>
            <div class="lux-feature">
              <div class="lux-feature-icon">&#128230;</div>
              <div class="lux-feature-text">
                <h4>Plastic-Free Matte Packaging</h4>
                <p>Dispatched in 100% recyclable bespoke paper mailers and boxes.</p>
              </div>
            </div>
            <div class="lux-feature">
              <div class="lux-feature-icon">&#129525;</div>
              <div class="lux-feature-text">
                <h4>Handcrafted in India</h4>
                <p>Every stitch by Lucknowi artisans keeping centuries-old craft alive.</p>
              </div>
            </div>
          </div>

          <a href="<?= BASE_URL ?>/shop.php?category=ethnic-fusion" class="lux-ethos-btn">
            <span>Explore Heritage Fusion Edit</span>
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
          </a>
        </div>
      </div>
    </div>
  </section>

  <!-- ===== TESTIMONIALS ===== -->
  <section class="lux-section lux-section-cream">
    <div class="aura-container">
      <div class="lux-section-head lux-section-head-center reveal">
        <span class="lux-eyebrow">What Our Community Says</span>
        <h2 class="lux-section-title">Trusted by Thousands</h2>
        <p class="lux-section-sub">Real customers, real stories</p>
      </div>

      <div class="lux-testimonials">
        <div class="lux-testimonial reveal reveal-delay-1">
          <div class="lux-testi-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
          <p class="lux-testi-text">"The acid-wash oversized tee is absolutely insane quality. The 260 GSM fabric feels premium and the fit is perfect. Already ordered 3 more colors!"</p>
          <div class="lux-testi-author">
            <div>
              <div class="lux-testi-name">Rohit Sharma</div>
              <div class="lux-testi-verified">Verified Buyer &middot; Mumbai</div>
            </div>
          </div>
        </div>

        <div class="lux-testimonial lux-testimonial-featured reveal reveal-delay-2">
          <div class="lux-testi-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
          <p class="lux-testi-text">"Finally found a brand that does ethnic fusion right. The Chikankari kurta set is gorgeous, breathable, and the hand-block print is so detailed. Love the Arya collection!"</p>
          <div class="lux-testi-author">
            <div>
              <div class="lux-testi-name">Ananya Patel</div>
              <div class="lux-testi-verified">Verified Buyer &middot; Delhi</div>
            </div>
          </div>
          <div class="lux-testi-featured-badge">Top Review</div>
        </div>

        <div class="lux-testimonial reveal reveal-delay-3">
          <div class="lux-testi-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
          <p class="lux-testi-text">"The resort linen co-ord set is my new favorite travel outfit. Super comfortable, wrinkle-resistant, and I get compliments everywhere. Free shipping was a bonus!"</p>
          <div class="lux-testi-author">
            <div>
              <div class="lux-testi-name">Vikram Kapoor</div>
              <div class="lux-testi-verified">Verified Buyer &middot; Bangalore</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ===== INSTAGRAM FEED ===== -->
  <section class="lux-insta-section">
    <div class="lux-insta-header reveal">
      <span class="lux-eyebrow" style="justify-content:center;">@auraandco.style</span>
      <h2 class="lux-section-title" style="text-align:center;">Join the Movement</h2>
      <p style="text-align:center; color: #737373; margin-top: 8px; font-size: 14px;">Tag us to be featured</p>
    </div>
    <div class="lux-insta-grid">
      <?php
      $igImgs = [
        'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=500&auto=format&fit=crop&q=80',
        'https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?w=500&auto=format&fit=crop&q=80',
        'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=500&auto=format&fit=crop&q=80',
        'https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?w=500&auto=format&fit=crop&q=80',
        'https://images.unsplash.com/photo-1509631179647-0177331693ae?w=500&auto=format&fit=crop&q=80',
        'https://images.unsplash.com/photo-1556905055-8f358a7a47b2?w=500&auto=format&fit=crop&q=80',
      ];
      foreach ($igImgs as $ig):
      ?>
      <div class="lux-insta-item">
        <img src="<?= $ig ?>" alt="urban outfit Instagram" loading="lazy">
        <div class="lux-insta-overlay">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="5"/><circle cx="17.5" cy="6.5" r="1.5" fill="white" stroke="none"/></svg>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- ===== NEWSLETTER / CTA SECTION ===== -->
  <section class="lux-section lux-newsletter">
    <div class="aura-container">
      <div class="lux-nl-inner reveal-scale">
        <span class="lux-eyebrow" style="justify-content:center; color: #D4AF37;">Stay in the Loop</span>
        <h2 class="lux-nl-title">Get First Access to<br>Exclusive Drops</h2>
        <p class="lux-nl-sub">Early access to new collections, exclusive subscriber-only discounts, and style guides from our team.</p>
        <div class="lux-nl-form">
          <input type="email" placeholder="Enter your email address..." class="lux-nl-input" id="nlEmail">
          <button class="lux-nl-btn" onclick="subscribeNewsletter()">
            <span>Subscribe</span>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
          </button>
        </div>
        <p class="lux-nl-note">No spam, ever. Unsubscribe anytime.</p>
      </div>
    </div>
  </section>

</main>

<script>
function quickAddToCart(productId, size) {
  fetch('<?= BASE_URL ?>/api/cart.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'action=add&product_id=' + productId + '&size=' + encodeURIComponent(size) + '&quantity=1'
  }).then(r => r.json()).then(data => {
    if (data.success) {
      document.querySelectorAll('.cart-count').forEach(b => b.textContent = data.cart_count || 1);
      showToast('Size ' + size + ' added to your bag!');
    } else {
      window.location.href = '<?= BASE_URL ?>/customer/cart.php';
    }
  }).catch(() => {
    window.location.href = '<?= BASE_URL ?>/customer/cart.php';
  });
}

function toggleWishlist(productId, btn) {
  btn.classList.toggle('active');
  btn.querySelector('svg').setAttribute('fill', btn.classList.contains('active') ? 'currentColor' : 'none');
}

function subscribeNewsletter() {
  const email = document.getElementById('nlEmail').value;
  if (!email || !email.includes('@')) {
    showToast('Please enter a valid email address');
    return;
  }
  showToast('Welcome! You are now subscribed for exclusive drops.');
  document.getElementById('nlEmail').value = '';
}

function showToast(msg) {
  const t = document.createElement('div');
  t.className = 'lux-toast';
  t.textContent = msg;
  document.body.appendChild(t);
  setTimeout(() => t.classList.add('lux-toast-show'), 10);
  setTimeout(() => {
    t.classList.remove('lux-toast-show');
    setTimeout(() => t.remove(), 400);
  }, 3000);
}

// Scroll Reveal
const revealObserver = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.classList.add('revealed');
    }
  });
}, { threshold: 0.08, rootMargin: '0px 0px -40px 0px' });

document.querySelectorAll('.reveal, .reveal-left, .reveal-right, .reveal-scale').forEach(el => revealObserver.observe(el));

// Header scroll
const header = document.getElementById('siteHeader');
if (header) {
  window.addEventListener('scroll', () => {
    header.classList.toggle('scrolled', window.scrollY > 20);
  });
}

// Parallax hero image
const heroBg = document.querySelector('.lux-hero-bg img');
if (heroBg) {
  window.addEventListener('scroll', () => {
    const scrolled = window.pageYOffset;
    if (scrolled < window.innerHeight) {
      heroBg.style.transform = 'scale(1.08) translateY(' + (scrolled * 0.12) + 'px)';
    }
  }, { passive: true });
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>

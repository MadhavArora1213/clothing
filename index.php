<?php
require_once __DIR__ . '/config/database.php';

$pageTitle       = 'Urban Outfit Collection — Streetwear, Ethnic Fusion & Modern Fashion India';
$pageDescription = 'Shop premium oversized drop tees, Chikankari ethnic fusion kurtas, resort co-ords & streetwear. Handcrafted in India. Free shipping above ₹999. New arrivals daily.';
$pageKeywords    = 'urban outfit collection, oversized tshirt india, ethnic fusion kurta, chikankari kurta online, resort co-ord set, streetwear india, indo western fashion, buy clothes online india, handcrafted fashion india';
$pageOgImage     = 'https://urbanoutfitshop.com/src/og-default.jpg';
$pageCanonical   = 'https://urbanoutfitshop.com/';
$pageSchema      = '{
  "@type": "WebPage",
  "@id": "https://urbanoutfitshop.com/#webpage",
  "url": "https://urbanoutfitshop.com/",
  "name": "Urban Outfit Collection — Streetwear, Ethnic Fusion & Modern Fashion India",
  "isPartOf": { "@id": "https://urbanoutfitshop.com/#website" },
  "about": { "@id": "https://urbanoutfitshop.com/#organization" },
  "description": "Shop premium oversized drop tees, Chikankari ethnic fusion kurtas, resort co-ords & streetwear. Handcrafted in India."
}';
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

  // Fetch available sizes per product (stock > 0)
  if (!empty($featured)) {
    $featuredIds = array_column($featured, 'id');
    $placeholders = implode(',', array_fill(0, count($featuredIds), '?'));
    $sizeStmt = $mysqli->prepare("SELECT product_id, size FROM product_sizes WHERE product_id IN ($placeholders) AND stock > 0 ORDER BY product_id, size");
    if ($sizeStmt) {
      $sizeStmt->bind_param(str_repeat('i', count($featuredIds)), ...$featuredIds);
      $sizeStmt->execute();
      $sizeRows = $sizeStmt->get_result()->fetch_all(MYSQLI_ASSOC);
      $productSizes = [];
      foreach ($sizeRows as $sr) {
        $productSizes[$sr['product_id']][] = $sr['size'];
      }
      foreach ($featured as &$p) {
        $p['sizes'] = $productSizes[$p['id']] ?? [];
      }
      unset($p);
    }
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
      'is_bestseller' => 1,
      'sizes' => ['S', 'M', 'L', 'XL']
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
      'is_bestseller' => 1,
      'sizes' => ['S', 'M', 'L', 'XL']
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
      'is_bestseller' => 0,
      'sizes' => ['S', 'M', 'L', 'XL']
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
      'is_bestseller' => 1,
      'sizes' => ['S', 'M', 'L', 'XL']
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
      'is_bestseller' => 1,
      'sizes' => ['XS', 'S', 'M', 'L', 'XL']
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
      'is_bestseller' => 1,
      'sizes' => ['S', 'M', 'L', 'XL']
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
      'is_bestseller' => 0,
      'sizes' => ['M', 'L', 'XL']
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
      'is_bestseller' => 1,
      'sizes' => ['S', 'M', 'L', 'XL']
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
  <section class="uoc-shop">
    <div class="aura-container">
      <div class="uoc-shop-head reveal">
        <span class="uoc-shop-eyebrow">Curated Collections</span>
        <h2 class="uoc-shop-title">Shop by<br><em>Category</em></h2>
      </div>

      <div class="uoc-shop-list">

        <a href="<?= BASE_URL ?>/shop.php?category=ethnic-fusion" class="uoc-shop-item reveal">
          <div class="uoc-shop-item-num">01</div>
          <div class="uoc-shop-item-name">
            <span class="uoc-shop-item-text">Artisanal Fusion</span>
            <span class="uoc-shop-item-tag">Heritage Collection</span>
          </div>
          <div class="uoc-shop-item-img">
            <img src="https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?w=600&h=400&fit=crop" alt="Artisanal Fusion" loading="lazy">
          </div>
          <div class="uoc-shop-item-arrow">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
          </div>
        </a>

        <a href="<?= BASE_URL ?>/shop.php?category=men" class="uoc-shop-item reveal reveal-delay-1">
          <div class="uoc-shop-item-num">02</div>
          <div class="uoc-shop-item-name">
            <span class="uoc-shop-item-text">Men's Edit</span>
            <span class="uoc-shop-item-tag">The Wardrobe</span>
          </div>
          <div class="uoc-shop-item-img">
            <img src="https://images.unsplash.com/photo-1617137984095-74e4e5e3613f?w=600&h=400&fit=crop" alt="Men's Edit" loading="lazy">
          </div>
          <div class="uoc-shop-item-arrow">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
          </div>
        </a>

        <a href="<?= BASE_URL ?>/shop.php?category=women" class="uoc-shop-item reveal reveal-delay-2">
          <div class="uoc-shop-item-num">03</div>
          <div class="uoc-shop-item-name">
            <span class="uoc-shop-item-text">Women's Edit</span>
            <span class="uoc-shop-item-tag">Contemporary</span>
          </div>
          <div class="uoc-shop-item-img">
            <img src="https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=600&h=400&fit=crop" alt="Women's Edit" loading="lazy">
          </div>
          <div class="uoc-shop-item-arrow">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
          </div>
        </a>

        <a href="<?= BASE_URL ?>/shop.php?category=oversized" class="uoc-shop-item reveal reveal-delay-3">
          <div class="uoc-shop-item-num">04</div>
          <div class="uoc-shop-item-name">
            <span class="uoc-shop-item-text">Oversized Drops</span>
            <span class="uoc-shop-item-tag">Premium Weight</span>
          </div>
          <div class="uoc-shop-item-img">
            <img src="https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?w=600&h=400&fit=crop" alt="Oversized Drops" loading="lazy">
          </div>
          <div class="uoc-shop-item-arrow">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
          </div>
        </a>

        <a href="<?= BASE_URL ?>/shop.php?category=co-ords" class="uoc-shop-item reveal reveal-delay-4">
          <div class="uoc-shop-item-num">05</div>
          <div class="uoc-shop-item-name">
            <span class="uoc-shop-item-text">Resort Co-Ords</span>
            <span class="uoc-shop-item-tag">Breezy Linen</span>
          </div>
          <div class="uoc-shop-item-img">
            <img src="https://images.unsplash.com/photo-1509631179647-0177331693ae?w=600&h=400&fit=crop" alt="Resort Co-Ords" loading="lazy">
          </div>
          <div class="uoc-shop-item-arrow">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
          </div>
        </a>

        <a href="<?= BASE_URL ?>/shop.php?category=accessories" class="uoc-shop-item reveal reveal-delay-4">
          <div class="uoc-shop-item-num">06</div>
          <div class="uoc-shop-item-name">
            <span class="uoc-shop-item-text">Accessories</span>
            <span class="uoc-shop-item-tag">The Details</span>
          </div>
          <div class="uoc-shop-item-img">
            <img src="https://images.unsplash.com/photo-1544816155-12df9643f363?w=600&h=400&fit=crop" alt="Accessories" loading="lazy">
          </div>
          <div class="uoc-shop-item-arrow">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
          </div>
        </a>

      </div>

      <div class="uoc-shop-bottom reveal">
        <a href="<?= BASE_URL ?>/shop.php" class="uoc-shop-explore">
          <span>Explore All Collections</span>
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
        </a>
      </div>
    </div>
  </section>

  <!-- ===== TRENDING PRODUCTS — NEW UI ===== -->
  <section class="uoc-trending">
    <div class="aura-container">
      <div class="uoc-trending-head reveal">
        <div class="uoc-trending-head-left">
          <span class="uoc-trending-eyebrow">Most Wanted Right Now</span>
          <h2 class="uoc-trending-title">Trending<br><em>Garments</em></h2>
        </div>
        <div class="uoc-trending-head-right">
          <p>Handpicked styles our community can't stop wearing. Updated weekly with fresh drops.</p>
          <a href="<?= BASE_URL ?>/shop.php" class="uoc-trending-viewall">
            View All
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
          </a>
        </div>
      </div>

      <div class="uoc-trending-grid">
        <?php foreach ($featured as $i => $item): ?>
        <div class="uoc-t-card reveal reveal-delay-<?= ($i % 4) + 1 ?>">
          <a href="<?= BASE_URL ?>/product.php?slug=<?= $item['slug'] ?>" class="uoc-t-card-link">

            <div class="uoc-t-card-img">
              <img src="<?= $item['image'] ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="uoc-t-img-main" loading="lazy">
              <img src="<?= $item['hover_image'] ?? $item['image'] ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="uoc-t-img-hover" loading="lazy">

              <!-- Badges -->
              <div class="uoc-t-badges">
                <?php if (!empty($item['discount_percent'])): ?>
                <span class="uoc-t-badge-sale"><?= $item['discount_percent'] ?>% OFF</span>
                <?php endif; ?>
                <?php if (!empty($item['is_bestseller'])): ?>
                <span class="uoc-t-badge-hot">BESTSELLER</span>
                <?php endif; ?>
              </div>

              <!-- Wishlist -->
              <button class="uoc-t-wish" onclick="event.preventDefault(); event.stopPropagation(); toggleWishlist(<?= $item['id'] ?>, this)" title="Add to Wishlist">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3.33.93-4.17 2.36a.75.75 0 0 1-1.33 0C10.33 3.93 8.76 3 7 3A5.5 5.5 0 0 0 1.5 8.5c0 2.3 1.51 4.04 3 5.5l7.5 7.5L19 14z"/></svg>
              </button>

              <!-- Quick Add -->
              <div class="uoc-t-quick">
                <span>Quick Add</span>
                <div class="uoc-t-sizes">
                  <?php foreach (($item['sizes'] ?? []) as $sz): ?>
                  <button onclick="event.preventDefault(); event.stopPropagation(); quickAddToCart(<?= $item['id'] ?>, '<?= htmlspecialchars($sz) ?>')"><?= htmlspecialchars($sz) ?></button>
                  <?php endforeach; ?>
                </div>
              </div>
            </div>

            <div class="uoc-t-card-info">
              <span class="uoc-t-card-cat"><?= htmlspecialchars($item['category_name'] ?? 'Premium') ?></span>
              <h4 class="uoc-t-card-name"><?= htmlspecialchars($item['name']) ?></h4>
              <div class="uoc-t-card-price">
                <span class="uoc-t-price-now">&#8377;<?= number_format($item['price']) ?></span>
                <?php if (!empty($item['original_price']) && $item['original_price'] > $item['price']): ?>
                <span class="uoc-t-price-was">&#8377;<?= number_format($item['original_price']) ?></span>
                <?php endif; ?>
              </div>
            </div>

          </a>
        </div>
        <?php endforeach; ?>
      </div>

      <div class="uoc-trending-bottom reveal">
        <a href="<?= BASE_URL ?>/shop.php" class="uoc-trending-explore">
          <span>Explore All Products</span>
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
        </a>
      </div>
    </div>
  </section>

  <!-- ===== SIGNATURE COLLECTION — EDITORIAL ===== -->
  <section class="uoc-sig-v3">
    <div class="uoc-sigv3-bg-text" aria-hidden="true">SIGNATURE</div>
    <div class="aura-container">
      <div class="uoc-sigv3-grid">

        <!-- Left: Content -->
        <div class="uoc-sigv3-content">
          <div class="uoc-sigv3-num">02</div>

          <div class="uoc-sigv3-tag">
            <span class="uoc-sigv3-tag-dot"></span>
            <span class="uoc-sigv2-tag-text">The Archives</span>
          </div>

          <h2 class="uoc-sigv3-title">
            <span class="uoc-sigv3-title-sm">The</span>
            <span class="uoc-sigv3-title-lg">Signature</span>
            <span class="uoc-sigv3-title-sm">Collection</span>
          </h2>

          <p class="uoc-sigv3-desc">Discover our highest-rated Lucknowi Chikankari pieces, 260+ GSM oversized drops, and luxurious resort linen sets. Crafted for longevity and timeless appeal.</p>

          <div class="uoc-sigv3-quote">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" opacity="0.3"><path d="M3 21c3 0 7-1 7-8V5c0-1.25-.756-2.017-2-2H4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2 1 0 1 0 1 1v1c0 1-1 2-2 2s-1 .008-1 1.031V21z"/><path d="M15 21c3 0 7-1 7-8V5c0-1.25-.757-2.017-2-2h-4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2h.75c0 2.25.25 4-2.75 4v3z"/></svg>
            <span>"Where tradition meets the streets"</span>
          </div>

          <div class="uoc-sigv3-divider"></div>

          <div class="uoc-sigv3-stats">
            <div class="uoc-sigv3-stat">
              <span class="uoc-sigv3-stat-num">500<span class="uoc-sigv3-stat-plus">+</span></span>
              <span class="uoc-sigv3-stat-label">Artisans</span>
            </div>
            <div class="uoc-sigv3-stat-divider"></div>
            <div class="uoc-sigv3-stat">
              <span class="uoc-sigv3-stat-num">12K<span class="uoc-sigv3-stat-plus">+</span></span>
              <span class="uoc-sigv3-stat-label">Pieces Crafted</span>
            </div>
            <div class="uoc-sigv3-stat-divider"></div>
            <div class="uoc-sigv3-stat">
              <span class="uoc-sigv3-stat-num">4.9</span>
              <span class="uoc-sigv3-stat-label">Avg Rating</span>
            </div>
          </div>

          <div class="uoc-sigv3-actions">
            <a href="<?= BASE_URL ?>/shop.php?category=ethnic-fusion" class="uoc-sigv3-btn">
              Explore The Collection
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </a>
            <a href="<?= BASE_URL ?>/shop.php?category=new-arrivals" class="uoc-sigv3-btn-ghost">New Arrivals <span class="uoc-sigv3-btn-arrow">&rarr;</span></a>
          </div>
        </div>

        <!-- Right: Image -->
        <div class="uoc-sigv3-visual">
          <div class="uoc-sigv3-img-main">
            <img src="https://images.unsplash.com/photo-1596755094514-f87e34085b2c?w=800&h=1100&fit=crop" alt="Signature Collection" loading="lazy">
            <div class="uoc-sigv3-img-overlay"></div>
          </div>

          <!-- Floating badge -->
          <div class="uoc-sigv3-badge">
            <span class="uoc-sigv3-badge-ring">
              <svg viewBox="0 0 100 100" class="uoc-sigv3-badge-svg">
                <defs>
                  <path id="circlePath" d="M 50,50 m -37,0 a 37,37 0 1,1 74,0 a 37,37 0 1,1 -74,0"/>
                </defs>
                <text>
                  <textPath href="#circlePath" class="uoc-sigv3-badge-text">PREMIUM &#8226; ETHICAL &#8226; HERITAGE &#8226;</textPath>
                </text>
              </svg>
            </span>
            <span class="uoc-sigv3-badge-center">EST.<br>2024</span>
          </div>

          <!-- Free shipping card -->
          <div class="uoc-sigv3-ship-card">
            <div class="uoc-sigv3-ship-icon">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="1" y="6" width="15" height="10" rx="1"/><path d="M16 10h4l3 3v3h-7V10z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
            </div>
            <div>
              <span class="uoc-sigv3-ship-title">Complimentary Shipping</span>
              <span class="uoc-sigv3-ship-sub">On all orders worldwide</span>
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- ===== ETHOS & CRAFTSMANSHIP — NEW UI ===== -->
  <section class="uoc-ethos">
    <div class="aura-container">
      <div class="uoc-ethos-grid">

        <!-- Left: Visual -->
        <div class="uoc-ethos-visual reveal">
          <div class="uoc-ethos-img-main">
            <img src="https://images.unsplash.com/photo-1596755094514-f87e34085b2c?w=800&h=900&fit=crop" alt="Premium Craftsmanship" loading="lazy">
          </div>
          <div class="uoc-ethos-img-accent">
            <img src="https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?w=400&h=400&fit=crop" alt="Ethnic Fusion Fabric" loading="lazy">
          </div>
          <div class="uoc-ethos-badge">
            <span class="uoc-ethos-badge-num">12+</span>
            <span class="uoc-ethos-badge-text">Years of Heritage</span>
          </div>
        </div>

        <!-- Right: Text -->
        <div class="uoc-ethos-text reveal">
          <span class="uoc-ethos-eyebrow">Ethos & Craftsmanship</span>
          <h2 class="uoc-ethos-title">Where Arya Heritage Meets<br><em>Urban Streetwear</em></h2>
          <p class="uoc-ethos-body">At <strong>urban outfit</strong>, we fuse the intricate hand-block printing and tone-on-tone Chikankari of classic Indian couturiers with relaxed, drop-shoulder silhouettes of contemporary urban street culture.</p>

          <div class="uoc-ethos-features">
            <div class="uoc-ethos-feat">
              <div class="uoc-ethos-feat-icon">&#127807;</div>
              <div class="uoc-ethos-feat-info">
                <h4>Pure Organic Linen & Bio-Washed Cotton</h4>
                <p>Gentle on skin, pre-shrunk, engineered for high-humidity breathability.</p>
              </div>
            </div>
            <div class="uoc-ethos-feat">
              <div class="uoc-ethos-feat-icon">&#128230;</div>
              <div class="uoc-ethos-feat-info">
                <h4>Plastic-Free Matte Packaging</h4>
                <p>Dispatched in 100% recyclable bespoke paper mailers and boxes.</p>
              </div>
            </div>
            <div class="uoc-ethos-feat">
              <div class="uoc-ethos-feat-icon">&#129525;</div>
              <div class="uoc-ethos-feat-info">
                <h4>Handcrafted in India</h4>
                <p>Every stitch by Lucknowi artisans keeping centuries-old craft alive.</p>
              </div>
            </div>
          </div>

          <a href="<?= BASE_URL ?>/shop.php?category=ethnic-fusion" class="uoc-ethos-btn">
            Explore Heritage Fusion Edit
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
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
  fetch('<?= BASE_URL ?>/api/wishlist.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'action=toggle&product_id=' + productId
  }).then(r => r.json()).then(data => {
    if (data.action === 'login_required') {
      window.location.href = '<?= BASE_URL ?>/customer/login.php';
      return;
    }
    if (data.success) {
      btn.classList.toggle('active');
      btn.querySelector('svg').setAttribute('fill', btn.classList.contains('active') ? 'currentColor' : 'none');
      showToast(data.message);
      const badge = document.getElementById('wishlistBadge');
      if (badge && data.wishlist_count !== undefined) {
        badge.textContent = data.wishlist_count;
        badge.style.display = data.wishlist_count > 0 ? '' : 'none';
      }
    }
  }).catch(() => {});
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

// showToast removed - using global uoc-toast

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

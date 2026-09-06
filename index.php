<?php
require_once __DIR__ . '/config/database.php';

$pageTitle       = 'UOC — Premium Streetwear & Ethnic Fusion Fashion India';
$pageDescription = 'Shop premium oversized drop tees, Chikankari ethnic fusion kurtas, resort co-ords & streetwear. Handcrafted in India. Free shipping above ₹999.';
$pageKeywords    = 'urban outfit collection, oversized tshirt india, ethnic fusion kurta, chikankari kurta online, resort co-ord set, streetwear india';
$pageOgImage     = 'https://urbanoutfitshop.com/src/og-default.jpg';
$pageCanonical   = 'https://urbanoutfitshop.com/';
$pageSchema      = '{
  "@type": "WebPage",
  "@id": "https://urbanoutfitshop.com/#webpage",
  "url": "https://urbanoutfitshop.com/",
  "name": "UOC — Premium Streetwear & Ethnic Fusion Fashion India",
  "isPartOf": { "@id": "https://urbanoutfitshop.com/#website" },
  "about": { "@id": "https://urbanoutfitshop.com/#organization" }
}';
$currentPage = 'home';

// Fetch Featured products
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

// Fallback
if (empty($featured)) {
  $featured = [
    ['id'=>1,'name'=>'Vintage Nomad Acid-Wash Oversized Drop Tee','slug'=>'vintage-nomad-acid-wash-tee','price'=>1299,'original_price'=>2499,'discount_percent'=>48,'category_name'=>'Oversized Drops','image'=>'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=800&auto=format&fit=crop&q=80','hover_image'=>'https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?w=800&auto=format&fit=crop&q=80','is_bestseller'=>1,'sizes'=>['S','M','L','XL']],
    ['id'=>2,'name'=>'Artisanal Hand-Block Indigo Linen Kurta Set','slug'=>'artisanal-indigo-linen-kurta-set','price'=>2899,'original_price'=>4999,'discount_percent'=>42,'category_name'=>'Ethnic Fusion','image'=>'https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?w=800&auto=format&fit=crop&q=80','hover_image'=>'https://images.unsplash.com/photo-1617137984095-74e4e5e3613f?w=800&auto=format&fit=crop&q=80','is_bestseller'=>1,'sizes'=>['S','M','L','XL']],
    ['id'=>3,'name'=>'Sorrento Breathable Resort Linen Co-Ord','slug'=>'sorrento-resort-linen-co-ord','price'=>2499,'original_price'=>4199,'discount_percent'=>40,'category_name'=>'Co-Ords','image'=>'https://images.unsplash.com/photo-1509631179647-0177331693ae?w=800&auto=format&fit=crop&q=80','hover_image'=>'https://images.unsplash.com/photo-1489987707025-afc232f7ea0f?w=800&auto=format&fit=crop&q=80','is_bestseller'=>0,'sizes'=>['S','M','L','XL']],
    ['id'=>4,'name'=>'Kyoto Minimalist Drop-Shoulder Heavy Hoodie','slug'=>'kyoto-minimalist-drop-shoulder-hoodie','price'=>1899,'original_price'=>3499,'discount_percent'=>45,'category_name'=>'Streetwear','image'=>'https://images.unsplash.com/photo-1556905055-8f358a7a47b2?w=800&auto=format&fit=crop&q=80','hover_image'=>'https://images.unsplash.com/photo-1509967419530-da38b4704bc6?w=800&auto=format&fit=crop&q=80','is_bestseller'=>1,'sizes'=>['S','M','L','XL']],
    ['id'=>5,'name'=>'Elysian Draped Liquid Satin Maxi Evening Dress','slug'=>'elysian-draped-satin-dress','price'=>2199,'original_price'=>3999,'discount_percent'=>45,'category_name'=>'Women','image'=>'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=800&auto=format&fit=crop&q=80','hover_image'=>'https://images.unsplash.com/photo-1496747611176-843222e1e57c?w=800&auto=format&fit=crop&q=80','is_bestseller'=>1,'sizes'=>['XS','S','M','L','XL']],
    ['id'=>6,'name'=>'Tactical Multi-Pocket Parachute Cargoes','slug'=>'tactical-parachute-cargoes','price'=>1999,'original_price'=>3599,'discount_percent'=>44,'category_name'=>'Streetwear','image'=>'https://images.unsplash.com/photo-1517445312882-bc9910d016b7?w=800&auto=format&fit=crop&q=80','hover_image'=>'https://images.unsplash.com/photo-1552374196-1ab2a1c593e8?w=800&auto=format&fit=crop&q=80','is_bestseller'=>1,'sizes'=>['S','M','L','XL']],
    ['id'=>7,'name'=>'AURA Monogram Heavyweight Canvas Tote','slug'=>'aura-heavyweight-canvas-tote','price'=>899,'original_price'=>1799,'discount_percent'=>50,'category_name'=>'Accessories','image'=>'https://images.unsplash.com/photo-1544816155-12df9643f363?w=800&auto=format&fit=crop&q=80','hover_image'=>'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=800&auto=format&fit=crop&q=80','is_bestseller'=>0,'sizes'=>['M','L','XL']],
    ['id'=>8,'name'=>'Aura Modern Chikankari Embroidered Short Kurti','slug'=>'aura-chikankari-short-kurti','price'=>1799,'original_price'=>3299,'discount_percent'=>45,'category_name'=>'Ethnic Fusion','image'=>'https://images.unsplash.com/photo-1610030469983-98e550d6193c?w=800&auto=format&fit=crop&q=80','hover_image'=>'https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?w=800&auto=format&fit=crop&q=80','is_bestseller'=>1,'sizes'=>['S','M','L','XL']]
  ];
}

include __DIR__ . '/includes/header.php';
?>

<main class="uoc-main">

  <!-- HERO -->
  <section class="uoc-hero">
    <div class="uoc-hero-slider">
      <div class="uoc-hero-slide active">
        <img src="https://images.unsplash.com/photo-1483985988355-763728e1935b?w=1600&auto=format&fit=crop&q=80" alt="New Collection" loading="eager">
      </div>
    </div>
    <div class="uoc-hero-content">
      <span class="uoc-hero-tag">New Season 2026</span>
      <h1>The Art of<br>Getting Dressed</h1>
      <p>Premium streetwear & ethnic fusion crafted for the modern wardrobe.</p>
      <div class="uoc-hero-btns">
        <a href="<?= BASE_URL ?>/shop.php?category=new-arrivals" class="uoc-btn uoc-btn-dark">Shop New Arrivals</a>
        <a href="<?= BASE_URL ?>/shop.php" class="uoc-btn uoc-btn-outline">Explore All</a>
      </div>
    </div>
  </section>

  <!-- CATEGORY GRID -->
  <section class="uoc-categories">
    <div class="uoc-container">
      <div class="uoc-cat-grid">
        <a href="<?= BASE_URL ?>/shop.php?category=men" class="uoc-cat-card">
          <img src="https://images.unsplash.com/photo-1617137984095-74e4e5e3613f?w=600&h=800&auto=format&fit=crop&q=80" alt="Men" loading="lazy">
          <div class="uoc-cat-overlay">
            <span>Men</span>
          </div>
        </a>
        <a href="<?= BASE_URL ?>/shop.php?category=women" class="uoc-cat-card">
          <img src="https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=600&h=800&auto=format&fit=crop&q=80" alt="Women" loading="lazy">
          <div class="uoc-cat-overlay">
            <span>Women</span>
          </div>
        </a>
        <a href="<?= BASE_URL ?>/shop.php?category=ethnic-fusion" class="uoc-cat-card">
          <img src="https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?w=600&h=800&auto=format&fit=crop&q=80" alt="Ethnic Fusion" loading="lazy">
          <div class="uoc-cat-overlay">
            <span>Ethnic Fusion</span>
          </div>
        </a>
      </div>
    </div>
  </section>

  <!-- MARQUEE -->
  <div class="uoc-marquee">
    <div class="uoc-marquee-track">
      <?php
      $items = ['Premium Streetwear','260+ GSM French Terry','Chikankari Heritage','Organic Linen Fusion','Handcrafted in India','Free Express Shipping','7-Day Easy Exchange'];
      $all = array_merge($items, $items, $items);
      foreach ($all as $item):
      ?>
      <div class="uoc-marquee-item">
        <span class="uoc-marquee-dot">&#10022;</span>
        <span><?= $item ?></span>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- TRENDING PRODUCTS -->
  <section class="uoc-products">
    <div class="uoc-container">
      <div class="uoc-section-header">
        <div>
          <span class="uoc-eyebrow">Most Wanted</span>
          <h2 class="uoc-section-title">Trending Now</h2>
        </div>
        <a href="<?= BASE_URL ?>/shop.php" class="uoc-view-all">View All &rarr;</a>
      </div>

      <div class="uoc-product-grid">
        <?php foreach ($featured as $i => $item): ?>
        <div class="uoc-product-card">
          <a href="<?= BASE_URL ?>/product.php?slug=<?= $item['slug'] ?>" class="uoc-product-link">

            <div class="uoc-product-img">
              <img src="<?= $item['image'] ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="uoc-img-main" loading="lazy">
              <img src="<?= $item['hover_image'] ?? $item['image'] ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="uoc-img-hover" loading="lazy">

              <div class="uoc-product-badges">
                <?php if (!empty($item['discount_percent'])): ?>
                <span class="uoc-badge-sale"><?= $item['discount_percent'] ?>% OFF</span>
                <?php endif; ?>
                <?php if (!empty($item['is_bestseller'])): ?>
                <span class="uoc-badge-hot">BESTSELLER</span>
                <?php endif; ?>
              </div>

              <button class="uoc-product-wish" onclick="event.preventDefault(); event.stopPropagation(); toggleWishlist(<?= $item['id'] ?>, this)" title="Wishlist">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3.33.93-4.17 2.36a.75.75 0 0 1-1.33 0C10.33 3.93 8.76 3 7 3A5.5 5.5 0 0 0 1.5 8.5c0 2.3 1.51 4.04 3 5.5l7.5 7.5L19 14z"/></svg>
              </button>

              <div class="uoc-product-quick">
                <span>Quick Add</span>
                <div class="uoc-product-sizes">
                  <?php foreach (($item['sizes'] ?? []) as $sz): ?>
                  <button onclick="event.preventDefault(); event.stopPropagation(); quickAddToCart(<?= $item['id'] ?>, '<?= htmlspecialchars($sz) ?>')"><?= htmlspecialchars($sz) ?></button>
                  <?php endforeach; ?>
                </div>
              </div>
            </div>

            <div class="uoc-product-info">
              <span class="uoc-product-cat"><?= htmlspecialchars($item['category_name'] ?? 'Premium') ?></span>
              <h4 class="uoc-product-name"><?= htmlspecialchars($item['name']) ?></h4>
              <div class="uoc-product-price">
                <span class="uoc-price-now">&#8377;<?= number_format($item['price']) ?></span>
                <?php if (!empty($item['original_price']) && $item['original_price'] > $item['price']): ?>
                <span class="uoc-price-was">&#8377;<?= number_format($item['original_price']) ?></span>
                <?php endif; ?>
              </div>
            </div>

          </a>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- FULL WIDTH PROMO -->
  <section class="uoc-promo">
    <img src="https://images.unsplash.com/photo-1596755094514-f87e34085b2c?w=1600&h=600&auto=format&fit=crop&q=80" alt="Heritage Collection" loading="lazy">
    <div class="uoc-promo-content">
      <span class="uoc-promo-tag">Heritage Collection</span>
      <h2>Where Tradition Meets<br>The Streets</h2>
      <p>Discover our highest-rated Chikankari pieces, handcrafted by 500+ artisans.</p>
      <a href="<?= BASE_URL ?>/shop.php?category=ethnic-fusion" class="uoc-btn uoc-btn-white">Shop Heritage</a>
    </div>
  </section>

  <!-- TESTIMONIALS -->
  <section class="uoc-testimonials">
    <div class="uoc-container">
      <div class="uoc-section-header uoc-section-header-center">
        <div>
          <span class="uoc-eyebrow">What Our Community Says</span>
          <h2 class="uoc-section-title">Trusted by Thousands</h2>
        </div>
      </div>

      <div class="uoc-testi-grid">
        <div class="uoc-testi-card">
          <div class="uoc-testi-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
          <p>"The acid-wash oversized tee is insane quality. 260 GSM fabric feels premium. Already ordered 3 more colors!"</p>
          <div class="uoc-testi-author">
            <div class="uoc-testi-avatar">R</div>
            <div>
              <strong>Rohit Sharma</strong>
              <span>Verified Buyer &middot; Mumbai</span>
            </div>
          </div>
        </div>

        <div class="uoc-testi-card uoc-testi-featured">
          <div class="uoc-testi-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
          <p>"Finally found a brand that does ethnic fusion right. The Chikankari kurta set is gorgeous and breathable."</p>
          <div class="uoc-testi-author">
            <div class="uoc-testi-avatar">A</div>
            <div>
              <strong>Ananya Patel</strong>
              <span>Verified Buyer &middot; Delhi</span>
            </div>
          </div>
        </div>

        <div class="uoc-testi-card">
          <div class="uoc-testi-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
          <p>"The resort linen co-ord is my new travel outfit. Super comfortable and I get compliments everywhere."</p>
          <div class="uoc-testi-author">
            <div class="uoc-testi-avatar">V</div>
            <div>
              <strong>Vikram Kapoor</strong>
              <span>Verified Buyer &middot; Bangalore</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- NEWSLETTER -->
  <section class="uoc-newsletter">
    <div class="uoc-container">
      <div class="uoc-newsletter-inner">
        <h3>Join the Movement</h3>
        <p>Subscribe for exclusive drops, early access & 10% off your first order.</p>
        <form class="uoc-newsletter-form" onsubmit="event.preventDefault(); subscribeNewsletter();">
          <input type="email" id="nlEmail" placeholder="Enter your email" required>
          <button type="submit">Subscribe</button>
        </form>
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

// Scroll Reveal
const revealObserver = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) entry.target.classList.add('revealed');
  });
}, { threshold: 0.08, rootMargin: '0px 0px -40px 0px' });

document.querySelectorAll('.reveal').forEach(el => revealObserver.observe(el));
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
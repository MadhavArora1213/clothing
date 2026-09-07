<?php
require_once __DIR__ . '/config/database.php';

$pageTitle       = 'Kids — UOC | Premium Ethnic & Casual Wear for Kids';
$pageDescription = 'Shop kids ethnic wear, boys & girls casuals, frocks & more. Handcrafted in India. Free shipping above ₹999.';
$pageKeywords    = 'kids ethnic wear india, boys kurta, girls frock, kids fashion online, UOC kids';
$pageOgImage     = 'https://urbanoutfitshop.com/src/og-default.jpg';
$pageCanonical   = 'https://urbanoutfitshop.com/kids.php';
$pageSchema      = '{
  "@type": "CollectionPage",
  "name": "Kids — Urban Outfit Collection",
  "url": "https://urbanoutfitshop.com/kids.php",
  "isPartOf": { "@id": "https://urbanoutfitshop.com/#website" }
}';
$currentPage = 'kids';

// Fetch Kids subcategories with image
$kidsSubcats = [];
if ($mysqli) {
  $sc = $mysqli->query("
    SELECT c.id, c.name, c.slug, c.image
    FROM categories c 
    WHERE c.parent_id = 3 AND c.is_active = 1 
    ORDER BY c.sort_order
  ");
  if ($sc) $kidsSubcats = $sc->fetch_all(MYSQLI_ASSOC);
}

// Fetch Kids products
$kidsProducts = [];
if ($mysqli) {
  $query = $mysqli->query("
    SELECT p.*, 
      (SELECT image_url FROM product_images WHERE product_id = p.id ORDER BY sort_order LIMIT 1) as image,
      (SELECT image_url FROM product_images WHERE product_id = p.id ORDER BY sort_order LIMIT 1 OFFSET 1) as hover_image
    FROM products p 
    JOIN categories c ON p.category_id = c.id
    WHERE p.is_active = 1 AND c.department = 'kids'
    ORDER BY p.is_featured DESC, p.created_at DESC 
    LIMIT 8
  ");
  if ($query) $kidsProducts = $query->fetch_all(MYSQLI_ASSOC);

  if (!empty($kidsProducts)) {
    $ids = array_column($kidsProducts, 'id');
    $ph = implode(',', array_fill(0, count($ids), '?'));
    $ss = $mysqli->prepare("SELECT product_id, size FROM product_sizes WHERE product_id IN ($ph) AND stock > 0 ORDER BY product_id, size");
    if ($ss) {
      $ss->bind_param(str_repeat('i', count($ids)), ...$ids);
      $ss->execute();
      $rows = $ss->get_result()->fetch_all(MYSQLI_ASSOC);
      $szMap = [];
      foreach ($rows as $r) $szMap[$r['product_id']][] = $r['size'];
      foreach ($kidsProducts as &$p) $p['sizes'] = $szMap[$p['id']] ?? [];
      unset($p);
    }
  }
}

// No fallback — only show real DB products

include __DIR__ . '/includes/header.php';
?>

<main class="uoc-main">

  <!-- DEPARTMENT HERO -->
  <section class="dept-hero" id="deptHero">
    <div class="dept-hero-slider">
      <div class="dept-hero-slide active">
        <img src="<?= BASE_URL ?>/images/kidshero1.png" alt="Kids Collection" loading="eager">
      </div>
      <div class="dept-hero-slide">
        <img src="<?= BASE_URL ?>/images/kidshero2.png" alt="Kids Collection" loading="lazy">
      </div>
    </div>
    <div class="dept-hero-content">
    </div>
    <div class="dept-hero-arrows">
      <button class="dept-hero-arrow" aria-label="Previous" onclick="slideHero(-1)">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
      </button>
      <button class="dept-hero-arrow" aria-label="Next" onclick="slideHero(1)">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
      </button>
    </div>
  </section>
  <div class="dept-hero-bottom">
    <div class="dept-hero-dots" id="heroDots">
      <span class="dept-hero-dot active" onclick="goToSlide(0)"></span>
      <span class="dept-hero-dot" onclick="goToSlide(1)"></span>
    </div>
  </div>

  <!-- SHOP BY CATEGORY -->
  <section class="shop-cat-section">
    <div class="uoc-container">
      <div class="shop-cat-header">
        <div>
          <div class="shop-cat-title">Shop By<br><strong>Category</strong></div>
          <div class="shop-cat-underline"></div>
        </div>
        <div class="shop-cat-arrows">
          <button class="shop-cat-arrow" onclick="scrollCat(-1)" aria-label="Previous">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
          </button>
          <button class="shop-cat-arrow" onclick="scrollCat(1)" aria-label="Next">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
          </button>
        </div>
      </div>
      <div class="shop-cat-scroll" id="catScroll">
        <?php foreach ($kidsSubcats as $sc): ?>
        <a href="<?= BASE_URL ?>/shop.php?category=kids&subcategory=<?= htmlspecialchars($sc['slug']) ?>" class="shop-cat-item">
          <div class="shop-cat-item-name"><?= htmlspecialchars($sc['name']) ?></div>
          <?php if (!empty($sc['image'])): ?>
          <img src="<?= htmlspecialchars($sc['image']) ?>" alt="<?= htmlspecialchars($sc['name']) ?>" class="shop-cat-item-img" loading="lazy">
          <?php else: ?>
          <img src="https://images.unsplash.com/photo-1503944583220-79d8926ad5e2?w=400&h=500&auto=format&fit=crop&q=80" alt="<?= htmlspecialchars($sc['name']) ?>" class="shop-cat-item-img" loading="lazy">
          <?php endif; ?>
        </a>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- MARQUEE -->
  <div class="uoc-marquee">
    <div class="uoc-marquee-track">
      <?php
      $items = ['Premium Kids Wear','Organic Cotton','Soft & Skin-Friendly','Handcrafted in India','Free Express Shipping','7-Day Easy Exchange','Easy Returns'];
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
  <?php if (!empty($kidsProducts)): ?>
  <section class="uoc-products">
    <div class="uoc-container">
      <div class="uoc-section-header">
        <div>
          <span class="uoc-eyebrow">Most Wanted</span>
          <h2 class="uoc-section-title">Trending For Kids</h2>
        </div>
        <a href="<?= BASE_URL ?>/shop.php?category=kids" class="uoc-view-all">View All &rarr;</a>
      </div>

      <div class="uoc-product-grid">
        <?php foreach ($kidsProducts as $i => $item): ?>
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
  <?php endif; ?>

  <!-- FULL WIDTH PROMO -->
  <section class="uoc-promo">
    <img src="https://images.unsplash.com/photo-1518831959646-742c3a14ebf7?w=1600&h=600&auto=format&fit=crop&q=80" alt="Kids Heritage" loading="lazy">
    <div class="uoc-promo-content">
      <span class="uoc-promo-tag">Kids Heritage</span>
      <h2>Little Ones,<br>Big Traditions</h2>
      <p>Ethnic wear for festivals, weddings & celebrations.</p>
      <a href="<?= BASE_URL ?>/shop.php?category=kids&subcategory=boys-ethnic-wear" class="uoc-btn uoc-btn-white">Shop Ethnic</a>
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

// Hero Slider
let currentSlide = 0;
const slides = document.querySelectorAll('#deptHero .dept-hero-slide');
const dots = document.querySelectorAll('#heroDots .dept-hero-dot');
let heroInterval;

function goToSlide(n) {
  slides[currentSlide].classList.remove('active');
  dots[currentSlide].classList.remove('active');
  currentSlide = n;
  slides[currentSlide].classList.add('active');
  dots[currentSlide].classList.add('active');
}

function slideHero(dir) {
  let next = currentSlide + dir;
  if (next >= slides.length) next = 0;
  if (next < 0) next = slides.length - 1;
  goToSlide(next);
  resetHeroInterval();
}

function resetHeroInterval() {
  clearInterval(heroInterval);
  heroInterval = setInterval(() => slideHero(1), 4000);
}

resetHeroInterval();

// Category Slider
function scrollCat(dir) {
  const el = document.getElementById('catScroll');
  if (!el) return;
  const itemWidth = 250;
  el.scrollBy({ left: dir * itemWidth, behavior: 'smooth' });
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>

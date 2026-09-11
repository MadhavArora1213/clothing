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
    LIMIT 40
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
          <div class="uoc-product-actions">
            <button class="uoc-btn-add-cart" onclick="event.preventDefault();event.stopPropagation();openSizePicker(this,'cart')" data-id="<?= $item['id'] ?>" data-sizes='<?= htmlspecialchars(json_encode($item['sizes'] ?? []), ENT_QUOTES) ?>' title="Add to Cart">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
              Add to Cart
            </button>
            <button class="uoc-btn-buy-now" onclick="event.preventDefault();event.stopPropagation();openSizePicker(this,'buynow')" data-id="<?= $item['id'] ?>" data-sizes='<?= htmlspecialchars(json_encode($item['sizes'] ?? []), ENT_QUOTES) ?>' title="Buy Now">
              Buy Now
            </button>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <!-- KIDS CTA BANNER -->
  <section class="kids-cta">
    <div class="uoc-container">
      <div class="kids-cta-box">
        <div class="kids-cta-content">
          <span class="kids-cta-badge">FESTIVE COLLECTION</span>
          <h2 class="kids-cta-title">Little Ones,<br>Big Traditions.</h2>
          <p class="kids-cta-text">Ethnic wear for festivals, weddings & celebrations — because tradition looks adorable on them.</p>
          <a href="<?= BASE_URL ?>/shop.php?category=kids&subcategory=boys-ethnic-wear" class="kids-cta-btn">
            Shop Ethnic
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
          </a>
        </div>
        <div class="kids-cta-deco">
          <span>KIDS</span>
        </div>
      </div>
    </div>
  </section>

  <style>
  .kids-cta {
    margin: 48px auto;
  }
  .kids-cta-box {
    background: linear-gradient(135deg, #0f1a0f 0%, #152515 50%, #0f1a0f 100%);
    border-radius: 24px;
    padding: 72px 64px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: relative;
    overflow: hidden;
  }
  .kids-cta-box::before {
    content: '';
    position: absolute;
    top: -100px;
    right: -100px;
    width: 400px;
    height: 400px;
    background: radial-gradient(circle, rgba(100,180,100,0.08) 0%, transparent 60%);
    pointer-events: none;
  }
  .kids-cta-box::after {
    content: '';
    position: absolute;
    bottom: -80px;
    left: -80px;
    width: 300px;
    height: 300px;
    background: radial-gradient(circle, rgba(100,180,100,0.04) 0%, transparent 60%);
    pointer-events: none;
  }
  .kids-cta-content {
    position: relative;
    z-index: 2;
  }
  .kids-cta-badge {
    display: inline-block;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.25em;
    color: #6a8a6a;
    background: rgba(100,180,100,0.08);
    border: 1px solid rgba(100,180,100,0.12);
    padding: 6px 16px;
    border-radius: 30px;
    margin-bottom: 24px;
  }
  .kids-cta-title {
    font-family: var(--font-display);
    font-size: 56px;
    font-weight: 800;
    color: #fff;
    line-height: 1.05;
    margin: 0 0 16px;
  }
  .kids-cta-text {
    font-size: 15px;
    color: #5a7a5a;
    line-height: 1.7;
    margin: 0 0 32px;
    max-width: 420px;
  }
  .kids-cta-btn {
    display: inline-flex;
    align-items: center;
    gap: 12px;
    background: #fff;
    color: #0f1a0f;
    padding: 16px 36px;
    font-size: 13px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    text-decoration: none;
    border-radius: 60px;
    transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
  }
  .kids-cta-btn:hover {
    background: #e6f5e6;
    gap: 18px;
    transform: translateY(-2px);
    box-shadow: 0 8px 30px rgba(100,180,100,0.15);
  }
  .kids-cta-deco {
    position: relative;
    z-index: 1;
  }
  .kids-cta-deco span {
    font-family: var(--font-display);
    font-size: 180px;
    font-weight: 900;
    color: rgba(100,180,100,0.04);
    line-height: 1;
    user-select: none;
    letter-spacing: -0.02em;
  }

  @media (max-width: 900px) {
    .kids-cta-box {
      padding: 48px 36px;
      flex-direction: column;
      text-align: center;
      gap: 32px;
    }
    .kids-cta-text {
      margin-inline: auto;
    }
    .kids-cta-deco span {
      font-size: 100px;
    }
  }
  @media (max-width: 600px) {
    .kids-cta-box {
      padding: 40px 24px;
      border-radius: 20px;
    }
    .kids-cta-title {
      font-size: 36px;
    }
    .kids-cta-deco span {
      font-size: 72px;
    }
  }
  </style>

</main>

<script>
/* ── SIZE PICKER MODAL ── */
var sizePickerOverlay = null;
var sizePickerModal = null;
var sizePickerData = { id: 0, sizes: [], mode: '' };

function ensureSizePicker() {
  if (sizePickerModal) return;
  sizePickerOverlay = document.createElement('div');
  sizePickerOverlay.className = 'uoc-sz-overlay';
  sizePickerOverlay.onclick = closeSizePicker;
  sizePickerModal = document.createElement('div');
  sizePickerModal.className = 'uoc-sz-modal';
  sizePickerModal.innerHTML = '<div class="uoc-sz-header"><span class="uoc-sz-title">Select Size</span><button class="uoc-sz-close" onclick="closeSizePicker()">&times;</button></div><div class="uoc-sz-grid" id="szGrid"></div><div class="uoc-sz-note">Please select a size to continue</div>';
  document.body.appendChild(sizePickerOverlay);
  document.body.appendChild(sizePickerModal);
}

function openSizePicker(btn, mode) {
  ensureSizePicker();
  var id = parseInt(btn.getAttribute('data-id'));
  var sizes = [];
  try { sizes = JSON.parse(btn.getAttribute('data-sizes')); } catch(e) { sizes = []; }
  sizePickerData = { id: id, sizes: sizes, mode: mode };
  var grid = document.getElementById('szGrid');
  grid.innerHTML = '';
  if (sizes.length === 0) {
    grid.innerHTML = '<div style="padding:20px;text-align:center;color:#999;font-size:13px;">No sizes available</div>';
  } else {
    sizes.forEach(function(sz) {
      var b = document.createElement('button');
      b.className = 'uoc-sz-btn';
      b.textContent = sz;
      b.onclick = function() { selectSizeAndAct(sz); };
      grid.appendChild(b);
    });
  }
  sizePickerOverlay.classList.add('show');
  sizePickerModal.classList.add('show');
}

function closeSizePicker() {
  if (sizePickerOverlay) sizePickerOverlay.classList.remove('show');
  if (sizePickerModal) sizePickerModal.classList.remove('show');
}

function selectSizeAndAct(size) {
  var id = sizePickerData.id;
  var mode = sizePickerData.mode;
  closeSizePicker();
  if (mode === 'buynow') {
    doBuyNow(id, size);
  } else {
    doAddToCart(id, size);
  }
}

function doAddToCart(productId, size) {
  fetch('<?= BASE_URL ?>/api/cart.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'action=add&product_id=' + productId + '&size=' + encodeURIComponent(size) + '&quantity=1'
  }).then(function(r) { return r.json(); }).then(function(data) {
    if (data.success) {
      document.querySelectorAll('.cart-count').forEach(function(b) { b.textContent = data.cart_count || 1; });
      showToast('Size ' + size + ' added to your bag!');
    } else {
      showToast(data.message || 'Failed to add', 'error');
    }
  }).catch(function() {
    window.location.href = '<?= BASE_URL ?>/customer/cart.php';
  });
}

function doBuyNow(productId, size) {
  fetch('<?= BASE_URL ?>/api/cart.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'action=add&product_id=' + productId + '&size=' + encodeURIComponent(size) + '&quantity=1'
  }).then(function(r) { return r.json(); }).then(function(data) {
    if (data.success) {
      document.querySelectorAll('.cart-count').forEach(function(b) { b.textContent = data.cart_count || 1; });
      window.location.href = '<?= BASE_URL ?>/customer/checkout.php';
    } else {
      showToast(data.message || 'Failed', 'error');
    }
  }).catch(function() {
    window.location.href = '<?= BASE_URL ?>/customer/checkout.php';
  });
}

function quickAddToCart(productId, size) {
  doAddToCart(productId, size);
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

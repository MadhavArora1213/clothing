<?php
require_once __DIR__ . '/config/database.php';

$slug = $_GET['slug'] ?? '';
$product = null;

if ($mysqli && !empty($slug)) {
  $stmt = $mysqli->prepare('SELECT p.*, c.name as category_name, c.slug as category_slug FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.slug = ? AND p.is_active = 1');
  if ($stmt) {
    $stmt->bind_param('s', $slug);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();
  }
}

// Fallback curated mock products if database is fresh
if (!$product) {
  $catalog = [
    'vintage-nomad-acid-wash-tee' => [
      'id' => 1,
      'name' => 'Vintage Nomad Acid-Wash Oversized Drop Tee',
      'brand' => 'AURA STREET',
      'price' => 1299,
      'original_price' => 2499,
      'discount_percent' => 48,
      'sku' => 'AUR-OVR-001',
      'category_name' => 'Oversized Drops',
      'description' => 'Engineered from heavyweight 260 GSM French Terry combed cotton with a bespoke mineral wash finish. Features a dropped shoulder boxy cut and reinforced ribbed collar.',
      'material' => '100% Organic Combed Cotton (260 GSM)',
      'care_instructions' => 'Machine wash cold inside out. Tumble dry low or line dry in shade. Do not iron on print.',
      'images' => [
        'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=1000&auto=format&fit=crop&q=85',
        'https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?w=1000&auto=format&fit=crop&q=85',
        'https://images.unsplash.com/photo-1583743814966-8936f5b7be1a?w=1000&auto=format&fit=crop&q=85'
      ]
    ],
    'artisanal-indigo-linen-kurta-set' => [
      'id' => 2,
      'name' => 'Artisanal Hand-Block Indigo Linen Kurta Set',
      'brand' => 'ARYA CREATION',
      'price' => 2899,
      'original_price' => 4999,
      'discount_percent' => 42,
      'sku' => 'AUR-ETH-002',
      'category_name' => 'Ethnic Fusion',
      'description' => 'Pure handspun breathable linen kurta set with intricate Lucknowi tone-on-tone embroidery and mother-of-pearl buttons. Pairs with tapered linen trousers.',
      'material' => '100% Pure French Flax Linen',
      'care_instructions' => 'Dry clean recommended for first wash. Gentle cold wash thereafter.',
      'images' => [
        'https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?w=1000&auto=format&fit=crop&q=85',
        'https://images.unsplash.com/photo-1617137984095-74e4e5e3613f?w=1000&auto=format&fit=crop&q=85'
      ]
    ],
    'sorrento-resort-linen-co-ord' => [
      'id' => 3,
      'name' => 'Sorrento Breathable Resort Linen Co-Ord',
      'brand' => 'AURA RESORT',
      'price' => 2499,
      'original_price' => 4199,
      'discount_percent' => 40,
      'sku' => 'AUR-CRD-003',
      'category_name' => 'Co-Ord Sets',
      'description' => 'Relaxed camp-collar short sleeve shirt with matching drawstring pleated shorts in breezy textured linen-cotton blend.',
      'material' => '65% Linen, 35% Cotton',
      'care_instructions' => 'Machine wash gentle. Hang dry.',
      'images' => [
        'https://images.unsplash.com/photo-1509631179647-0177331693ae?w=1000&auto=format&fit=crop&q=85',
        'https://images.unsplash.com/photo-1489987707025-afc232f7ea0f?w=1000&auto=format&fit=crop&q=85'
      ]
    ]
  ];

  $product = $catalog[$slug] ?? $catalog['vintage-nomad-acid-wash-tee'];
}

$imageUrls = $product['images'] ?? [
  'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=1000&auto=format&fit=crop&q=85',
  'https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?w=1000&auto=format&fit=crop&q=85'
];

// Fetch approved reviews for this product
$reviews = [];
$avgRating = 0;
$ratingCounts = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
$totalReviews = 0;
if (!empty($product['id']) && $mysqli) {
  $productId = (int)$product['id'];
  $revResult = $mysqli->query("SELECT * FROM reviews WHERE product_id = $productId AND is_approved = 1 ORDER BY created_at DESC");
  if ($revResult) {
    $reviews = $revResult->fetch_all(MYSQLI_ASSOC);
    $totalReviews = count($reviews);
    if ($totalReviews > 0) {
      $sum = 0;
      foreach ($reviews as $r) {
        $rating = (int)$r['rating'];
        $sum += $rating;
        if (isset($ratingCounts[$rating])) $ratingCounts[$rating]++;
      }
      $avgRating = round($sum / $totalReviews, 1);
    }
  }
}

$pageTitle = $product['name'] . ' — urban outfit';
$pageDescription = $product['description'] ?? '';
include __DIR__ . '/includes/header.php';

$isWishlisted = false;
if (!empty($_SESSION['customer_id']) && $mysqli && !empty($product['id'])) {
  $wlCheck = $mysqli->prepare('SELECT id FROM wishlists WHERE customer_id = ? AND product_id = ?');
  if ($wlCheck) {
    $wlCheck->bind_param('ii', $_SESSION['customer_id'], $product['id']);
    $wlCheck->execute();
    $isWishlisted = $wlCheck->get_result()->num_rows > 0;
  }
}
?>

<main class="aura-main" style="padding: 40px 0 80px;">
  <div class="aura-container">
    
    <div style="display: grid; grid-template-columns: 1.1fr 1fr; gap: 48px; align-items: start;">
      
      <!-- Product Image Gallery -->
      <div>
        <div style="border-radius: var(--radius-xl); overflow: hidden; background: #F1F5F9; aspect-ratio: 3/4; box-shadow: var(--shadow-md); margin-bottom: 16px;">
          <img src="<?= $imageUrls[0] ?>" alt="<?= htmlspecialchars($product['name']) ?>" id="mainProductImg" style="width: 100%; height: 100%; object-fit: cover;">
        </div>

        <div style="display: flex; gap: 12px;">
          <?php foreach ($imageUrls as $idx => $img): ?>
            <button onclick="document.getElementById('mainProductImg').src='<?= $img ?>'" style="width: 80px; height: 100px; border-radius: var(--radius-md); overflow: hidden; border: 2px solid <?= $idx === 0 ? 'var(--color-accent)' : 'var(--color-border)' ?>; cursor: pointer;">
              <img src="<?= $img ?>" alt="Thumb" style="width: 100%; height: 100%; object-fit: cover;">
            </button>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Product Details & Add to Cart -->
      <div>
        <span style="font-size: 11px; font-weight: 800; text-transform: uppercase; color: var(--color-accent); letter-spacing: 0.1em; display: block; margin-bottom: 6px;">
          <?= htmlspecialchars($product['category_name'] ?? 'AURA ESSENTIAL') ?>
        </span>

        <h1 style="font-family: var(--font-display); font-size: 32px; font-weight: 800; color: #0F172A; line-height: 1.2; margin-bottom: 12px;">
          <?= htmlspecialchars($product['name']) ?>
        </h1>

        <div style="display: flex; align-items: baseline; gap: 12px; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 1px solid var(--color-border);">
          <span style="font-family: var(--font-mono); font-size: 26px; font-weight: 800; color: #0F172A;">
            ₹<?= number_format($product['price']) ?>
          </span>
          <?php if (!empty($product['original_price'])): ?>
            <span style="font-family: var(--font-mono); font-size: 16px; color: #94A3B8; text-decoration: line-through;">
              ₹<?= number_format($product['original_price']) ?>
            </span>
            <span style="font-size: 12px; font-weight: 800; color: var(--color-emerald); background: #ECFDF5; padding: 2px 8px; border-radius: 4px;">
              <?= $product['discount_percent'] ?>% OFF
            </span>
          <?php endif; ?>
          <span style="font-size: 11px; color: #64748B;">(Inclusive of all taxes)</span>
        </div>

        <!-- Size Selector -->
        <div style="margin-bottom: 24px;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
            <label style="font-size: 12px; font-weight: 700; color: #0F172A;">Select Size:</label>
            <span style="font-size: 11px; font-weight: 600; color: var(--color-accent); cursor: pointer;">📏 Size Guide</span>
          </div>

          <div style="display: flex; gap: 8px;" id="sizeOptions">
            <?php foreach (['S', 'M', 'L', 'XL', 'XXL'] as $sz): ?>
              <button type="button" onclick="selectSize(this, '<?= $sz ?>')" class="size-select-btn" style="min-width: 48px; height: 44px; border: 1.5px solid var(--color-border); border-radius: var(--radius-md); font-weight: 700; font-size: 13px; cursor: pointer; transition: var(--transition);">
                <?= $sz ?>
              </button>
            <?php endforeach; ?>
          </div>
          <input type="hidden" id="selectedSize" value="M">
        </div>

        <!-- Stock Alert -->
        <div style="display: flex; align-items: center; gap: 8px; background: #FFFBEB; border: 1px solid #FDE68A; color: #B45309; padding: 10px 14px; border-radius: var(--radius-md); font-size: 12px; font-weight: 600; margin-bottom: 24px;">
          <span>⚡ Low Stock Alert: Only 4 pieces remaining in size M!</span>
        </div>

        <!-- Action Buttons -->
        <div style="display: flex; gap: 12px; margin-bottom: 32px;">
          <button type="button" onclick="handleAddToCart()" class="btn btn-primary" style="flex: 1; padding: 16px; font-size: 14px;">
            <span>Add to Bag</span>
          </button>
          <a href="<?= BASE_URL ?>/customer/checkout.php" class="btn btn-dark" style="flex: 1; padding: 16px; font-size: 14px; text-align: center;">
            <span>Buy Now</span>
          </a>
          <button type="button" id="productWishlistBtn" onclick="toggleProductWishlist()" style="width: 52px; height: 52px; border: 1.5px solid var(--color-border); border-radius: var(--radius-md); background: <?= $isWishlisted ? '#FEF2F2' : '#fff' ?>; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.3s; flex-shrink: 0;">
            <svg id="productWishlistIcon" width="22" height="22" viewBox="0 0 24 24" fill="<?= $isWishlisted ? '#dc2626' : 'none' ?>" stroke="<?= $isWishlisted ? '#dc2626' : '#64748B' ?>" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
            </svg>
          </button>
        </div>

        <!-- Fabric & Specifications Accordion -->
        <div style="border-top: 1px solid var(--color-border); padding-top: 20px; font-size: 13px; line-height: 1.7; color: #475569;">
          <h3 style="font-size: 14px; font-weight: 700; color: #0F172A; margin-bottom: 8px;">Product Description &amp; Fit:</h3>
          <p style="margin-bottom: 16px;"><?= nl2br(htmlspecialchars($product['description'] ?? '')) ?></p>
          
          <?php if (!empty($product['material'])): ?>
            <p><strong>Fabric:</strong> <?= htmlspecialchars($product['material']) ?></p>
          <?php endif; ?>
          <?php if (!empty($product['care_instructions'])): ?>
            <p><strong>Care:</strong> <?= htmlspecialchars($product['care_instructions']) ?></p>
          <?php endif; ?>
          <p><strong>SKU:</strong> <?= htmlspecialchars($product['sku'] ?? 'AUR-2026') ?></p>
        </div>

      </div>

    </div>

    <!-- ═══════════ Reviews & Ratings Section ═══════════ -->
    <style>
      .rev-section { margin-top: 60px; padding-top: 40px; border-top: 1px solid var(--color-border, #E8E2D8); }
      .rev-section-title {
        font-family: var(--font-display, 'Playfair Display');
        font-size: clamp(22px, 2.5vw, 28px);
        font-weight: 400;
        font-style: italic;
        color: #1a1a1a;
        margin-bottom: 32px;
      }

      /* Summary Card */
      .rev-summary {
        display: flex;
        gap: 48px;
        align-items: center;
        background: #fff;
        border: 1px solid #E8E2D8;
        border-radius: 14px;
        padding: 32px;
        margin-bottom: 36px;
      }
      .rev-summary-score { text-align: center; min-width: 120px; }
      .rev-summary-score .big {
        font-family: var(--font-display, 'Playfair Display');
        font-size: 52px;
        font-weight: 700;
        color: #D4AF37;
        line-height: 1;
      }
      .rev-summary-score .stars { color: #D4AF37; font-size: 18px; margin: 8px 0 4px; letter-spacing: 2px; }
      .rev-summary-score .count { font-size: 13px; color: #9A8E7E; }

      .rev-bars { flex: 1; }
      .rev-bar-row {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 6px;
        font-size: 13px;
      }
      .rev-bar-row .lbl { width: 16px; text-align: right; font-weight: 600; color: #5C5347; }
      .rev-bar-row .bar-track {
        flex: 1;
        height: 8px;
        background: #F0ECE4;
        border-radius: 4px;
        overflow: hidden;
      }
      .rev-bar-row .bar-fill {
        height: 100%;
        background: #D4AF37;
        border-radius: 4px;
        transition: width 0.4s ease;
      }
      .rev-bar-row .num { width: 28px; font-size: 12px; color: #9A8E7E; }

      /* Individual Review */
      .rev-list { margin-bottom: 48px; }
      .rev-card {
        padding: 24px 0;
        border-bottom: 1px solid #E8E2D8;
      }
      .rev-card:last-child { border-bottom: none; }
      .rev-card-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 10px;
      }
      .rev-author {
        display: flex;
        align-items: center;
        gap: 12px;
      }
      .rev-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #D4AF37, #B8960B);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: 700;
        color: #fff;
      }
      .rev-author-name { font-weight: 600; font-size: 14px; color: #1a1a1a; }
      .rev-author-date { font-size: 12px; color: #9A8E7E; }
      .rev-stars { color: #D4AF37; font-size: 14px; letter-spacing: 1px; }
      .rev-card-title {
        font-weight: 600;
        font-size: 15px;
        color: #1a1a1a;
        margin-bottom: 6px;
      }
      .rev-card-comment {
        font-size: 14px;
        color: #5C5347;
        line-height: 1.65;
      }

      /* Review Form */
      .rev-form-wrap {
        background: #fff;
        border: 1px solid #E8E2D8;
        border-radius: 14px;
        padding: 32px;
      }
      .rev-form-wrap h3 {
        font-family: var(--font-display, 'Playfair Display');
        font-size: 20px;
        font-weight: 600;
        font-style: italic;
        margin-bottom: 20px;
        color: #1a1a1a;
      }
      .rev-star-select {
        display: flex;
        gap: 6px;
        margin-bottom: 16px;
      }
      .rev-star-select .star-btn {
        font-size: 28px;
        background: none;
        border: none;
        cursor: pointer;
        color: #D6CEC4;
        transition: color 0.15s;
        padding: 0;
      }
      .rev-star-select .star-btn.active,
      .rev-star-select .star-btn:hover { color: #D4AF37; }
      .rev-form-field { margin-bottom: 14px; }
      .rev-form-field label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: #5C5347;
        margin-bottom: 6px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
      }
      .rev-form-field input,
      .rev-form-field textarea {
        width: 100%;
        padding: 12px 14px;
        border: 1px solid #E8E2D8;
        border-radius: 8px;
        font-size: 14px;
        font-family: var(--font-body, 'Plus Jakarta Sans');
        color: #1a1a1a;
        background: #FAF9F6;
        transition: border-color 0.2s;
      }
      .rev-form-field input:focus,
      .rev-form-field textarea:focus {
        outline: none;
        border-color: #D4AF37;
      }
      .rev-form-field textarea { resize: vertical; min-height: 100px; }
      .rev-submit-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 13px 32px;
        background: #1a1a1a;
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.25s;
        font-family: var(--font-body, 'Plus Jakarta Sans');
      }
      .rev-submit-btn:hover { background: #D4AF37; transform: translateY(-1px); }
      .rev-login-msg {
        text-align: center;
        padding: 40px;
        font-size: 14px;
        color: #9A8E7E;
        background: #FAF9F6;
        border: 1px dashed #E8E2D8;
        border-radius: 14px;
      }
      .rev-login-msg a {
        color: #D4AF37;
        font-weight: 600;
        text-decoration: underline;
        text-underline-offset: 2px;
      }
      .rev-login-msg a:hover { color: #B8960B; }

      @media (max-width: 700px) {
        .rev-summary { flex-direction: column; gap: 24px; text-align: center; }
        .rev-card-head { flex-direction: column; align-items: flex-start; gap: 6px; }
      }
    </style>

    <section class="rev-section">
      <h2 class="rev-section-title">Customer Reviews</h2>

      <?php if ($totalReviews > 0): ?>
        <!-- Rating Summary -->
        <div class="rev-summary">
          <div class="rev-summary-score">
            <div class="big"><?= $avgRating ?></div>
            <div class="stars"><?= str_repeat('★', (int)$avgRating) ?><?= str_repeat('☆', 5 - (int)$avgRating) ?></div>
            <div class="count"><?= $totalReviews ?> review<?= $totalReviews !== 1 ? 's' : '' ?></div>
          </div>
          <div class="rev-bars">
            <?php for ($i = 5; $i >= 1; $i--):
              $pct = $totalReviews > 0 ? ($ratingCounts[$i] / $totalReviews) * 100 : 0;
            ?>
              <div class="rev-bar-row">
                <span class="lbl"><?= $i ?></span>
                <div class="bar-track"><div class="bar-fill" style="width: <?= $pct ?>%"></div></div>
                <span class="num"><?= $ratingCounts[$i] ?></span>
              </div>
            <?php endfor; ?>
          </div>
        </div>

        <!-- Review List -->
        <div class="rev-list">
          <?php foreach ($reviews as $r):
            $initials = strtoupper(substr($r['customer_name'], 0, 1));
          ?>
            <div class="rev-card">
              <div class="rev-card-head">
                <div class="rev-author">
                  <div class="rev-avatar"><?= $initials ?></div>
                  <div>
                    <div class="rev-author-name"><?= sanitize($r['customer_name']) ?></div>
                    <div class="rev-author-date"><?= date('M d, Y', strtotime($r['created_at'])) ?></div>
                  </div>
                </div>
                <div class="rev-stars"><?= str_repeat('★', $r['rating']) ?><?= str_repeat('☆', 5 - $r['rating']) ?></div>
              </div>
              <?php if (!empty($r['title'])): ?>
                <div class="rev-card-title"><?= sanitize($r['title']) ?></div>
              <?php endif; ?>
              <?php if (!empty($r['comment'])): ?>
                <div class="rev-card-comment"><?= nl2br(sanitize($r['comment'])) ?></div>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div style="text-align: center; padding: 40px 0; color: #9A8E7E; font-size: 14px; margin-bottom: 32px;">
          No reviews yet. Be the first to review this product!
        </div>
      <?php endif; ?>

      <!-- Review Form -->
      <?php if (isset($_SESSION['customer_id'])): ?>
        <div class="rev-form-wrap">
          <h3>Write a Review</h3>
          <form id="reviewForm" onsubmit="submitReview(event)">
            <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
            <div class="rev-star-select" id="starSelect">
              <button type="button" class="star-btn" data-val="1" onclick="setRating(1)">&#9733;</button>
              <button type="button" class="star-btn" data-val="2" onclick="setRating(2)">&#9733;</button>
              <button type="button" class="star-btn" data-val="3" onclick="setRating(3)">&#9733;</button>
              <button type="button" class="star-btn" data-val="4" onclick="setRating(4)">&#9733;</button>
              <button type="button" class="star-btn" data-val="5" onclick="setRating(5)">&#9733;</button>
            </div>
            <input type="hidden" name="rating" id="ratingInput" value="0">
            <div class="rev-form-field">
              <label>Review Title</label>
              <input type="text" name="title" placeholder="e.g. Great quality, fits perfectly!" maxlength="255" required>
            </div>
            <div class="rev-form-field">
              <label>Your Review</label>
              <textarea name="comment" placeholder="Tell others what you think about this product..." maxlength="2000" required></textarea>
            </div>
            <button type="submit" class="rev-submit-btn" id="reviewSubmitBtn">
              Submit Review
            </button>
            <div id="reviewMsg" style="margin-top: 12px; font-size: 13px; display: none;"></div>
          </form>
        </div>
      <?php else: ?>
        <div class="rev-login-msg">
          <p>Please <a href="<?= BASE_URL ?>/customer/login.php">login</a> to write a review.</p>
        </div>
      <?php endif; ?>
    </section>

  </div>
</main>

<style>
/* Toast Notification */
.shop-toast {
  position: fixed;
  bottom: 32px;
  left: 50%;
  transform: translateX(-50%) translateY(20px);
  background: #1a1a1a;
  color: #fff;
  padding: 14px 24px;
  border-radius: 12px;
  font-size: 13px;
  font-weight: 600;
  z-index: 9999;
  display: flex;
  align-items: center;
  gap: 10px;
  box-shadow: 0 8px 32px rgba(0,0,0,0.25);
  opacity: 0;
  pointer-events: none;
  transition: all 0.4s cubic-bezier(0.16,1,0.3,1);
  white-space: nowrap;
}
.shop-toast.show {
  opacity: 1;
  transform: translateX(-50%) translateY(0);
  pointer-events: auto;
}
</style>

<script>
let activeSize = 'M';
function selectSize(btn, sz) {
  document.querySelectorAll('.size-select-btn').forEach(b => {
    b.style.borderColor = 'var(--color-border)';
    b.style.background = '#FFFFFF';
    b.style.color = '#0F172A';
  });
  btn.style.borderColor = 'var(--color-accent)';
  btn.style.background = 'var(--color-accent)';
  btn.style.color = '#FFFFFF';
  activeSize = sz;
  document.getElementById('selectedSize').value = sz;
}

// initialize default size
document.addEventListener('DOMContentLoaded', () => {
  const btns = document.querySelectorAll('.size-select-btn');
  if (btns[1]) selectSize(btns[1], 'M');
});

function handleAddToCart() {
  fetch('<?= BASE_URL ?>/api/cart.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'action=add&product_id=<?= $product['id'] ?>&size=' + encodeURIComponent(activeSize) + '&quantity=1&product_name=' + encodeURIComponent(<?= json_encode($product['name']) ?>) + '&product_price=<?= $product['price'] ?>&product_image=' + encodeURIComponent(<?= json_encode($imageUrls[0] ?? '') ?>) + '&product_slug=<?= $product['slug'] ?? '' ?>'
  }).then(r => r.json()).then(data => {
    if (data.success) {
      const badges = document.querySelectorAll('.cart-count');
      badges.forEach(b => b.textContent = data.cart_count || 1);
      showToast(<?= json_encode($product['name']) ?> + ' (Size ' + activeSize + ') added to your bag!');
    } else {
      showToast(data.message || 'Failed to add to cart', true);
    }
  }).catch(() => {
    window.location.href = '<?= BASE_URL ?>/customer/cart.php';
  });
}

function toggleProductWishlist() {
  fetch('<?= BASE_URL ?>/api/wishlist.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'action=toggle&product_id=<?= $product['id'] ?>'
  }).then(r => r.json()).then(data => {
    if (data.action === 'login_required') {
      window.location.href = '<?= BASE_URL ?>/customer/login.php';
      return;
    }
    if (data.success) {
      const btn = document.getElementById('productWishlistBtn');
      const icon = document.getElementById('productWishlistIcon');
      const isAdded = data.status === 'added';
      btn.style.background = isAdded ? '#FEF2F2' : '#fff';
      icon.setAttribute('fill', isAdded ? '#dc2626' : 'none');
      icon.setAttribute('stroke', isAdded ? '#dc2626' : '#64748B');
      showToast(data.message);
    }
  }).catch(() => {});
}

function showToast(msg, isError) {
  const existing = document.querySelector('.shop-toast');
  if (existing) existing.remove();
  const t = document.createElement('div');
  t.className = 'shop-toast';
  t.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="' + (isError ? '#ef4444' : '#22c55e') + '" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">' + (isError ? '<circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>' : '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>') + '</svg><span>' + msg + '</span>';
  document.body.appendChild(t);
  requestAnimationFrame(() => t.classList.add('show'));
  setTimeout(() => { t.classList.remove('show'); setTimeout(() => t.remove(), 400); }, 3000);
}

/* ── Reviews ── */
let selectedRating = 0;
function setRating(val) {
  selectedRating = val;
  document.getElementById('ratingInput').value = val;
  document.querySelectorAll('#starSelect .star-btn').forEach((btn, i) => {
    btn.classList.toggle('active', i < val);
  });
}

function submitReview(e) {
  e.preventDefault();
  if (selectedRating < 1) {
    const msg = document.getElementById('reviewMsg');
    msg.style.display = 'block';
    msg.style.color = '#C0392B';
    msg.textContent = 'Please select a star rating.';
    return;
  }
  const form = document.getElementById('reviewForm');
  const btn = document.getElementById('reviewSubmitBtn');
  btn.textContent = 'Submitting...';
  btn.disabled = true;

  fetch('<?= BASE_URL ?>/api/review.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'product_id=' + form.querySelector('[name=product_id]').value +
          '&rating=' + selectedRating +
          '&title=' + encodeURIComponent(form.querySelector('[name=title]').value) +
          '&comment=' + encodeURIComponent(form.querySelector('[name=comment]').value)
  }).then(r => r.json()).then(data => {
    const msg = document.getElementById('reviewMsg');
    msg.style.display = 'block';
    if (data.success) {
      msg.style.color = '#166534';
      msg.textContent = '✓ ' + data.message;
      showToast('Review submitted! Refreshing...');
      setTimeout(() => location.reload(), 1200);
    } else {
      msg.style.color = '#C0392B';
      msg.textContent = data.error || data.message || 'Something went wrong.';
      btn.textContent = 'Submit Review';
      btn.disabled = false;
    }
  }).catch(() => {
    const msg = document.getElementById('reviewMsg');
    msg.style.display = 'block';
    msg.style.color = '#C0392B';
    msg.textContent = 'Network error. Please try again.';
    btn.textContent = 'Submit Review';
    btn.disabled = false;
  });
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>

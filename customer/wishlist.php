<?php
require_once dirname(__DIR__) . '/config/database.php';

if (!isset($_SESSION['customer_id'])) {
  redirect('/customer/login.php?redirect=' . urlencode('/customer/wishlist.php'));
}

$customerId = $_SESSION['customer_id'];

// Remove item
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_wishlist'])) {
  $removeId = (int)$_POST['remove_wishlist'];
  $del = $mysqli->prepare('DELETE FROM wishlists WHERE id = ? AND customer_id = ?');
  if ($del) {
    $del->bind_param('ii', $removeId, $customerId);
    $del->execute();
  }
  redirect('/customer/wishlist.php');
}

// Fetch wishlist items with product details
$items = [];
$stmt = $mysqli->prepare("SELECT w.id as wishlist_id, w.created_at as added_at, p.id, p.name, p.slug, p.price, p.original_price, p.discount_percent, p.image, p.is_active FROM wishlists w JOIN products p ON w.product_id = p.id WHERE w.customer_id = ? ORDER BY w.created_at DESC");
if ($stmt) {
  $stmt->bind_param('i', $customerId);
  $stmt->execute();
  $items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

$pageTitle = 'My Wishlist — ATELIER';
include dirname(__DIR__) . '/includes/header.php';
?>

<style>
  body { background: var(--color-bg, #FAF9F6); }

  .wish-page {
    max-width: 1100px;
    margin: 0 auto;
    padding: calc(var(--header-height) + 32px) 24px 80px;
  }

  /* ── Header ── */
  .wish-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 36px;
    padding-bottom: 20px;
    border-bottom: 1px solid var(--color-border, #E8E2D8);
  }
  .wish-head-left { display: flex; align-items: baseline; gap: 12px; }
  .wish-head h1 {
    font-family: var(--font-display, 'Playfair Display');
    font-size: clamp(26px, 3vw, 34px);
    font-weight: 400;
    font-style: italic;
    color: var(--color-text-primary, #1a1a1a);
  }
  .wish-count {
    font-size: 13px;
    font-weight: 500;
    color: var(--color-text-tertiary, #9A8E7E);
    background: var(--color-surface, #fff);
    border: 1px solid var(--color-border, #E8E2D8);
    padding: 5px 14px;
    border-radius: 999px;
  }
  .wish-head-actions { display: flex; gap: 10px; }
  .wish-share-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 9px 18px;
    background: var(--color-surface, #fff);
    border: 1px solid var(--color-border, #E8E2D8);
    border-radius: 8px;
    font-size: 13px;
    font-weight: 500;
    color: var(--color-text-secondary, #5C5347);
    cursor: pointer;
    transition: all 0.2s;
    font-family: var(--font-body, 'Plus Jakarta Sans');
    text-decoration: none;
  }
  .wish-share-btn:hover { border-color: var(--color-accent, #D4AF37); color: var(--color-accent, #D4AF37); }
  .wish-share-btn svg { width: 15px; height: 15px; }

  /* ── Product Grid ── */
  .wish-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
    gap: 24px;
  }

  /* ── Product Card ── */
  .wish-card {
    background: var(--color-surface, #fff);
    border: 1px solid var(--color-border, #E8E2D8);
    border-radius: 14px;
    overflow: hidden;
    transition: all 0.3s ease;
    position: relative;
  }
  .wish-card:hover {
    box-shadow: 0 8px 24px rgba(0,0,0,0.06);
    transform: translateY(-2px);
  }

  /* Image */
  .wish-card-img {
    position: relative;
    aspect-ratio: 3/4;
    overflow: hidden;
    background: var(--color-surface-alt, #F0ECE4);
  }
  .wish-card-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.4s ease;
  }
  .wish-card:hover .wish-card-img img { transform: scale(1.05); }

  /* Sale Badge */
  .wish-badge {
    position: absolute;
    top: 12px;
    left: 12px;
    background: #C0392B;
    color: #fff;
    font-size: 11px;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 6px;
    letter-spacing: 0.02em;
  }

  /* Remove Button */
  .wish-remove {
    position: absolute;
    top: 12px;
    right: 12px;
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: rgba(255,255,255,0.9);
    backdrop-filter: blur(4px);
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
  }
  .wish-remove:hover { background: #C0392B; }
  .wish-remove:hover svg { stroke: #fff; }
  .wish-remove svg { width: 16px; height: 16px; stroke: #1a1a1a; transition: stroke 0.2s; }

  /* Quick Add Overlay */
  .wish-quick-add {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 12px;
    background: linear-gradient(transparent, rgba(0,0,0,0.6));
    display: flex;
    gap: 6px;
    opacity: 0;
    transform: translateY(10px);
    transition: all 0.3s ease;
  }
  .wish-card:hover .wish-quick-add {
    opacity: 1;
    transform: translateY(0);
  }
  .wish-size-btn {
    flex: 1;
    padding: 8px 0;
    background: rgba(255,255,255,0.95);
    border: none;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 700;
    color: #1a1a1a;
    cursor: pointer;
    transition: all 0.15s;
    font-family: var(--font-body, 'Plus Jakarta Sans');
  }
  .wish-size-btn:hover { background: var(--color-accent, #D4AF37); color: #fff; }

  /* Card Body */
  .wish-card-body { padding: 16px; }
  .wish-card-name {
    font-size: 14px;
    font-weight: 600;
    color: var(--color-text-primary, #1a1a1a);
    margin-bottom: 4px;
    line-height: 1.35;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }
  .wish-card-name a {
    color: inherit;
    text-decoration: none;
    transition: color 0.2s;
  }
  .wish-card-name a:hover { color: var(--color-accent, #D4AF37); }

  .wish-card-cat {
    font-size: 11px;
    color: var(--color-text-tertiary, #9A8E7E);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 8px;
  }

  .wish-card-price {
    display: flex;
    align-items: baseline;
    gap: 8px;
    margin-bottom: 12px;
  }
  .wish-price-now {
    font-family: var(--font-mono, 'JetBrains Mono');
    font-size: 16px;
    font-weight: 700;
    color: var(--color-text-primary, #1a1a1a);
  }
  .wish-price-was {
    font-family: var(--font-mono, 'JetBrains Mono');
    font-size: 13px;
    color: var(--color-text-tertiary, #9A8E7E);
    text-decoration: line-through;
  }
  .wish-price-off {
    font-size: 11px;
    font-weight: 700;
    color: #C0392B;
    background: rgba(192,57,43,0.08);
    padding: 2px 8px;
    border-radius: 4px;
  }

  .wish-add-btn {
    display: flex;
    width: 100%;
    padding: 11px;
    background: var(--color-text-primary, #1a1a1a);
    color: #fff;
    border: none;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.25s;
    font-family: var(--font-body, 'Plus Jakarta Sans');
    align-items: center;
    justify-content: center;
    gap: 6px;
  }
  .wish-add-btn:hover { background: var(--color-accent, #D4AF37); }
  .wish-add-btn svg { width: 15px; height: 15px; }

  /* ── Empty State ── */
  .wish-empty {
    text-align: center;
    padding: 100px 0;
  }
  .wish-empty-icon {
    width: 88px;
    height: 88px;
    border-radius: 50%;
    background: linear-gradient(135deg, rgba(212,175,55,0.08), rgba(212,175,55,0.15));
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 24px;
  }
  .wish-empty-icon svg { width: 40px; height: 40px; stroke: var(--color-accent, #D4AF37); }
  .wish-empty h3 {
    font-family: var(--font-display, 'Playfair Display');
    font-size: 28px;
    font-weight: 400;
    font-style: italic;
    margin-bottom: 8px;
    color: var(--color-text-primary, #1a1a1a);
  }
  .wish-empty p {
    color: var(--color-text-tertiary, #9A8E7E);
    margin-bottom: 32px;
    font-size: 14px;
    max-width: 340px;
    margin-left: auto;
    margin-right: auto;
    line-height: 1.6;
  }
  .wish-empty .btn-shop {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 14px 36px;
    background: var(--color-text-primary, #1a1a1a);
    color: #fff;
    border: none;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.25s ease;
    font-family: var(--font-body, 'Plus Jakarta Sans');
  }
  .wish-empty .btn-shop:hover { background: var(--color-accent, #D4AF37); transform: translateY(-1px); }
  .wish-empty .btn-shop svg { width: 16px; height: 16px; }

  /* ── Mobile ── */
  @media (max-width: 600px) {
    .wish-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; }
    .wish-card-body { padding: 12px; }
    .wish-card-name { font-size: 13px; }
    .wish-price-now { font-size: 14px; }
    .wish-head { flex-direction: column; gap: 12px; align-items: flex-start; }
    .wish-quick-add { opacity: 1; transform: none; }
  }
</style>

<main>
  <div class="wish-page">

    <!-- Header -->
    <div class="wish-head">
      <div class="wish-head-left">
        <h1>Wishlist</h1>
        <span class="wish-count"><?= count($items) ?> item<?= count($items) !== 1 ? 's' : '' ?></span>
      </div>
      <?php if (!empty($items)): ?>
        <div class="wish-head-actions">
          <button class="wish-share-btn" onclick="shareWishlist()">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
            Share all
          </button>
        </div>
      <?php endif; ?>
    </div>

    <?php if (empty($items)): ?>
      <!-- Empty State -->
      <div class="wish-empty">
        <div class="wish-empty-icon">
          <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3.33.93-4.17 2.36a.75.75 0 0 1-1.33 0C10.33 3.93 8.76 3 7 3A5.5 5.5 0 0 0 1.5 8.5c0 2.3 1.51 4.04 3 5.5l7.5 7.5L19 14z"/>
          </svg>
        </div>
        <h3>Your wishlist is empty</h3>
        <p>Save your favorite pieces here. Tap the heart icon on any product to add it to your wishlist.</p>
        <a href="<?= BASE_URL ?>/shop.php" class="btn-shop">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 12H5M12 19l-7-7 7-7"/></svg>
          Browse Collection
        </a>
      </div>

    <?php else: ?>
      <div class="wish-grid">
        <?php foreach ($items as $item):
          $img = !empty($item['image']) ? $item['image'] : 'https://via.placeholder.com/400x530?text=No+Image';
        ?>
          <div class="wish-card">
            <!-- Image -->
            <div class="wish-card-img">
              <a href="<?= BASE_URL ?>/product.php?slug=<?= $item['slug'] ?>">
                <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($item['name']) ?>" loading="lazy">
              </a>

              <?php if (!empty($item['discount_percent']) && $item['discount_percent'] > 0): ?>
                <span class="wish-badge">-<?= $item['discount_percent'] ?>%</span>
              <?php endif; ?>

              <!-- Remove -->
              <form method="POST" style="margin:0;">
                <input type="hidden" name="remove_wishlist" value="<?= $item['wishlist_id'] ?>">
                <button type="submit" class="wish-remove" title="Remove from wishlist" onclick="return confirm('Remove this item from your wishlist?')">
                  <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
              </form>

              <!-- Quick Add Sizes -->
              <div class="wish-quick-add">
                <button class="wish-size-btn" onclick="quickAdd(<?= $item['id'] ?>, 'S', '<?= addslashes($item['name']) ?>', <?= $item['price'] ?>, '<?= addslashes($img) ?>', '<?= $item['slug'] ?>')">S</button>
                <button class="wish-size-btn" onclick="quickAdd(<?= $item['id'] ?>, 'M', '<?= addslashes($item['name']) ?>', <?= $item['price'] ?>, '<?= addslashes($img) ?>', '<?= $item['slug'] ?>')">M</button>
                <button class="wish-size-btn" onclick="quickAdd(<?= $item['id'] ?>, 'L', '<?= addslashes($item['name']) ?>', <?= $item['price'] ?>, '<?= addslashes($img) ?>', '<?= $item['slug'] ?>')">L</button>
                <button class="wish-size-btn" onclick="quickAdd(<?= $item['id'] ?>, 'XL', '<?= addslashes($item['name']) ?>', <?= $item['price'] ?>, '<?= addslashes($img) ?>', '<?= $item['slug'] ?>')">XL</button>
              </div>
            </div>

            <!-- Body -->
            <div class="wish-card-body">
              <div class="wish-card-cat"><?= htmlspecialchars($item['category_name'] ?? 'ATELIER') ?></div>
              <div class="wish-card-name">
                <a href="<?= BASE_URL ?>/product.php?slug=<?= $item['slug'] ?>"><?= htmlspecialchars($item['name']) ?></a>
              </div>
              <div class="wish-card-price">
                <span class="wish-price-now">₹<?= number_format($item['price']) ?></span>
                <?php if (!empty($item['original_price']) && $item['original_price'] > $item['price']): ?>
                  <span class="wish-price-was">₹<?= number_format($item['original_price']) ?></span>
                  <span class="wish-price-off"><?= $item['discount_percent'] ?? 0 ?>% OFF</span>
                <?php endif; ?>
              </div>
              <button class="wish-add-btn" onclick="quickAdd(<?= $item['id'] ?>, 'M', '<?= addslashes($item['name']) ?>', <?= $item['price'] ?>, '<?= addslashes($img) ?>', '<?= $item['slug'] ?>')">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Add to Cart
              </button>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

  </div>
</main>

<script>
function quickAdd(productId, size, name, price, image, slug) {
  fetch('<?= BASE_URL ?>/api/cart.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'action=add&product_id=' + productId + '&size=' + encodeURIComponent(size) + '&quantity=1&product_name=' + encodeURIComponent(name) + '&product_price=' + price + '&product_image=' + encodeURIComponent(image) + '&product_slug=' + encodeURIComponent(slug)
  }).then(r => r.json()).then(data => {
    if (data.success) {
      const badges = document.querySelectorAll('.cart-count');
      badges.forEach(b => b.textContent = data.cart_count || 1);
      alert('Added ' + name + ' (Size ' + size + ') to your bag!');
    } else {
      alert(data.message || 'Failed to add to cart');
    }
  }).catch(() => {
    window.location.href = '<?= BASE_URL ?>/customer/cart.php';
  });
}

function shareWishlist() {
  if (navigator.share) {
    navigator.share({
      title: 'My ATELIER Wishlist',
      text: 'Check out my favorite pieces from ATELIER!',
      url: window.location.href
    });
  } else {
    navigator.clipboard.writeText(window.location.href).then(() => {
      alert('Wishlist link copied to clipboard!');
    });
  }
}
</script>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>

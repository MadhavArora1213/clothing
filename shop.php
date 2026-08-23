<?php
require_once __DIR__ . '/config/database.php';
$pageTitle = 'Shop Collection — AURA & CO.';
$pageDescription = 'Browse our complete collection of oversized drop tees, resort co-ords, and artisanal ethnic fusion kurtas.';
$currentPage = 'shop';

$category = $_GET['category'] ?? null;
$sale = isset($_GET['sale']);
$newArrivals = isset($_GET['new']);
$sort = $_GET['sort'] ?? 'newest';
$search = $_GET['search'] ?? null;

$products = [];
if ($mysqli) {
  $where = ['p.is_active = 1'];
  $params = [];
  $types = '';

  if ($category) {
    $catStmt = $mysqli->prepare("SELECT id FROM categories WHERE slug = ?");
    if ($catStmt) {
      $catStmt->bind_param('s', $category);
      $catStmt->execute();
      $cat = $catStmt->get_result()->fetch_assoc();
      if ($cat) {
        $where[] = 'p.category_id = ?';
        $params[] = $cat['id'];
        $types .= 'i';
      }
    }
  }

  if ($sale) {
    $where[] = 'p.discount_percent > 0';
  }
  if ($newArrivals) {
    $where[] = 'p.is_featured = 1';
  }
  if ($search) {
    $where[] = '(p.name LIKE ? OR p.description LIKE ?)';
    $searchTerm = "%{$search}%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $types .= 'ss';
  }

  $whereClause = implode(' AND ', $where);
  $orderBy = match($sort) {
    'price_asc' => 'p.price ASC',
    'price_desc' => 'p.price DESC',
    'name' => 'p.name ASC',
    default => 'p.created_at DESC'
  };

  $sql = "SELECT p.*, 
            (SELECT image_url FROM product_images WHERE product_id = p.id ORDER BY is_primary DESC, sort_order LIMIT 1) as image,
            (SELECT image_url FROM product_images WHERE product_id = p.id ORDER BY sort_order LIMIT 1 OFFSET 1) as hover_image
          FROM products p 
          WHERE $whereClause 
          ORDER BY $orderBy LIMIT 20";

  $stmt = $mysqli->prepare($sql);
  if ($stmt) {
    if (!empty($params)) {
      $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
  }
}

// Fallback curated products if database is fresh
if (empty($products)) {
  $products = [
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

<main class="aura-main" style="padding-top: 40px; padding-bottom: 80px;">
  <div class="aura-container">
    
    <!-- Shop Header Bar -->
    <div class="section-head" style="margin-bottom: 24px;">
      <div>
        <span class="section-eyebrow">AURA APPAREL STUDIO</span>
        <h1 class="section-title">
          <?= $category ? ucwords(str_replace('-', ' ', $category)) : ($sale ? '50% OFF Flash Sale' : ($newArrivals ? 'New Season Drops' : 'Complete Catalogue')) ?>
        </h1>
        <p style="font-size: 13px; color: #64748B; margin-top: 4px;">
          Showing <?= count($products) ?> curated garments &bull; 100% genuine fabrics
        </p>
      </div>

      <!-- Sorting Selector -->
      <form method="GET" action="" style="display: flex; gap: 8px; align-items: center;">
        <?php if ($category): ?><input type="hidden" name="category" value="<?= htmlspecialchars($category) ?>"><?php endif; ?>
        <?php if ($sale): ?><input type="hidden" name="sale" value="1"><?php endif; ?>
        <select name="sort" onchange="this.form.submit()" style="background: #FFFFFF; border: 1px solid var(--color-border); padding: 8px 14px; border-radius: var(--radius-full); font-size: 12px; font-weight: 700; color: #0F172A; cursor: pointer;">
          <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Sort: Newest Drops</option>
          <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Price: Low to High</option>
          <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Price: High to Low</option>
        </select>
      </form>
    </div>

    <!-- Category Filter Chips -->
    <div style="display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 32px;">
      <a href="<?= BASE_URL ?>/shop.php" class="tab-pill <?= empty($category) && !$sale && !$newArrivals ? 'active' : '' ?>">All Drops</a>
      <a href="<?= BASE_URL ?>/shop.php?category=men" class="tab-pill <?= $category === 'men' ? 'active' : '' ?>">Men</a>
      <a href="<?= BASE_URL ?>/shop.php?category=women" class="tab-pill <?= $category === 'women' ? 'active' : '' ?>">Women</a>
      <a href="<?= BASE_URL ?>/shop.php?category=oversized" class="tab-pill <?= $category === 'oversized' ? 'active' : '' ?>">Oversized Tees</a>
      <a href="<?= BASE_URL ?>/shop.php?category=ethnic-fusion" class="tab-pill <?= $category === 'ethnic-fusion' ? 'active' : '' ?>">Arya Ethnic Fusion</a>
      <a href="<?= BASE_URL ?>/shop.php?category=co-ords" class="tab-pill <?= $category === 'co-ords' ? 'active' : '' ?>">Resort Co-Ords</a>
      <a href="<?= BASE_URL ?>/shop.php?sale=1" class="tab-pill <?= $sale ? 'active' : '' ?>" style="color: var(--color-rose);">50% OFF Sale</a>
    </div>

    <!-- Product Grid -->
    <div class="product-grid">
      <?php foreach ($products as $item): ?>
        <div class="aura-product-card">
          <div class="card-image-wrap">
            <a href="<?= BASE_URL ?>/product.php?slug=<?= $item['slug'] ?>">
              <img src="<?= $item['image'] ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="main-img" loading="lazy">
              <img src="<?= $item['hover_image'] ?? $item['image'] ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="hover-img" loading="lazy">
            </a>

            <div class="card-badges">
              <?php if (!empty($item['discount_percent'])): ?>
                <span class="badge-sale"><?= $item['discount_percent'] ?>% OFF</span>
              <?php endif; ?>
              <?php if (!empty($item['is_bestseller'])): ?>
                <span class="badge-best">Bestseller</span>
              <?php endif; ?>
            </div>

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
</main>

<script>
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

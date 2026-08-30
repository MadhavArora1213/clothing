<?php
require_once __DIR__ . '/config/database.php';
$pageTitle = 'Shop Collection — urban outfit';
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

// Fetch wishlisted product IDs for logged-in user
$wishlistedIds = [];
if (!empty($_SESSION['customer_id']) && $mysqli) {
  $wlStmt = $mysqli->prepare('SELECT product_id FROM wishlists WHERE customer_id = ?');
  if ($wlStmt) {
    $wlStmt->bind_param('i', $_SESSION['customer_id']);
    $wlStmt->execute();
    $wishlistedIds = array_column($wlStmt->get_result()->fetch_all(MYSQLI_ASSOC), 'product_id');
  }
}

if (empty($products)) {
  $products = [
    [
      'id' => 1, 'name' => 'Vintage Nomad Acid-Wash Oversized Drop Tee', 'slug' => 'vintage-nomad-acid-wash-tee',
      'price' => 1299, 'original_price' => 2499, 'discount_percent' => 48, 'category_name' => 'Oversized Drops',
      'image' => 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=600&h=750&fit=crop',
      'hover_image' => 'https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?w=600&h=750&fit=crop', 'is_bestseller' => 1
    ],
    [
      'id' => 2, 'name' => 'Artisanal Hand-Block Indigo Linen Kurta Set', 'slug' => 'artisanal-indigo-linen-kurta-set',
      'price' => 2899, 'original_price' => 4999, 'discount_percent' => 42, 'category_name' => 'Ethnic Fusion',
      'image' => 'https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?w=600&h=750&fit=crop',
      'hover_image' => 'https://images.unsplash.com/photo-1617137984095-74e4e5e3613f?w=600&h=750&fit=crop', 'is_bestseller' => 1
    ],
    [
      'id' => 3, 'name' => 'Sorrento Breathable Resort Linen Co-Ord', 'slug' => 'sorrento-resort-linen-co-ord',
      'price' => 2499, 'original_price' => 4199, 'discount_percent' => 40, 'category_name' => 'Co-Ord Sets',
      'image' => 'https://images.unsplash.com/photo-1509631179647-0177331693ae?w=600&h=750&fit=crop',
      'hover_image' => 'https://images.unsplash.com/photo-1489987707025-afc232f7ea0f?w=600&h=750&fit=crop', 'is_bestseller' => 0
    ],
    [
      'id' => 4, 'name' => 'Kyoto Minimalist Drop-Shoulder Heavy Hoodie', 'slug' => 'kyoto-minimalist-drop-shoulder-hoodie',
      'price' => 1899, 'original_price' => 3499, 'discount_percent' => 45, 'category_name' => 'Streetwear',
      'image' => 'https://images.unsplash.com/photo-1556905055-8f358a7a47b2?w=600&h=750&fit=crop',
      'hover_image' => 'https://images.unsplash.com/photo-1509967419530-da38b4704bc6?w=600&h=750&fit=crop', 'is_bestseller' => 1
    ],
    [
      'id' => 5, 'name' => 'Elysian Draped Liquid Satin Maxi Evening Dress', 'slug' => 'elysian-draped-satin-dress',
      'price' => 2199, 'original_price' => 3999, 'discount_percent' => 45, 'category_name' => 'Women',
      'image' => 'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=600&h=750&fit=crop',
      'hover_image' => 'https://images.unsplash.com/photo-1496747611176-843222e1e57c?w=600&h=750&fit=crop', 'is_bestseller' => 1
    ],
    [
      'id' => 6, 'name' => 'Tactical Multi-Pocket Parachute Cargoes', 'slug' => 'tactical-parachute-cargoes',
      'price' => 1999, 'original_price' => 3599, 'discount_percent' => 44, 'category_name' => 'Streetwear',
      'image' => 'https://images.unsplash.com/photo-1517445312882-bc9910d016b7?w=600&h=750&fit=crop',
      'hover_image' => 'https://images.unsplash.com/photo-1552374196-1ab2a1c593e8?w=600&h=750&fit=crop', 'is_bestseller' => 1
    ],
    [
      'id' => 7, 'name' => 'AURA Monogram Heavyweight 16oz Canvas Tote', 'slug' => 'aura-heavyweight-canvas-tote',
      'price' => 899, 'original_price' => 1799, 'discount_percent' => 50, 'category_name' => 'Accessories',
      'image' => 'https://images.unsplash.com/photo-1544816155-12df9643f363?w=600&h=750&fit=crop',
      'hover_image' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=600&h=750&fit=crop', 'is_bestseller' => 0
    ],
    [
      'id' => 8, 'name' => 'Aura Modern Chikankari Embroidered Short Kurti', 'slug' => 'aura-chikankari-short-kurti',
      'price' => 1799, 'original_price' => 3299, 'discount_percent' => 45, 'category_name' => 'Ethnic Fusion',
      'image' => 'https://images.unsplash.com/photo-1610030469983-98e550d6193c?w=600&h=750&fit=crop',
      'hover_image' => 'https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?w=600&h=750&fit=crop', 'is_bestseller' => 1
    ],
    [
      'id' => 9, 'name' => 'Heritage Block-Print Menswear Relaxed Shirt', 'slug' => 'heritage-block-print-shirt',
      'price' => 1599, 'original_price' => 2799, 'discount_percent' => 43, 'category_name' => 'Men',
      'image' => 'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?w=600&h=750&fit=crop',
      'hover_image' => 'https://images.unsplash.com/photo-1598033129183-c4f50c736c10?w=600&h=750&fit=crop', 'is_bestseller' => 0
    ],
    [
      'id' => 10, 'name' => 'Midnight Luxe Organza Embroidered Anarkali', 'slug' => 'midnight-organza-anarkali',
      'price' => 3499, 'original_price' => 5999, 'discount_percent' => 42, 'category_name' => 'Women',
      'image' => 'https://images.unsplash.com/photo-1594938298603-c8148c4dae35?w=600&h=750&fit=crop',
      'hover_image' => 'https://images.unsplash.com/photo-1610030469983-98e550d6193c?w=600&h=750&fit=crop', 'is_bestseller' => 1
    ],
    [
      'id' => 11, 'name' => 'Nomad Utility Cargo Jogger Set', 'slug' => 'nomad-utility-cargo-jogger',
      'price' => 2299, 'original_price' => 3999, 'discount_percent' => 43, 'category_name' => 'Streetwear',
      'image' => 'https://images.unsplash.com/photo-1552374196-1ab2a1c593e8?w=600&h=750&fit=crop',
      'hover_image' => 'https://images.unsplash.com/photo-1517445312882-bc9910d016b7?w=600&h=750&fit=crop', 'is_bestseller' => 0
    ],
    [
      'id' => 12, 'name' => 'Ivory Linen Resort Palazzo & Crop Co-Ord', 'slug' => 'ivory-linen-resort-palazzo',
      'price' => 2699, 'original_price' => 4499, 'discount_percent' => 40, 'category_name' => 'Co-Ord Sets',
      'image' => 'https://images.unsplash.com/photo-1496747611176-843222e1e57c?w=600&h=750&fit=crop',
      'hover_image' => 'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=600&h=750&fit=crop', 'is_bestseller' => 1
    ],
  ];
}

include __DIR__ . '/includes/header.php';

$pageName = '';
if ($category) {
  $pageName = ucwords(str_replace('-', ' ', $category));
} elseif ($sale) {
  $pageName = 'Flash Sale';
} elseif ($newArrivals) {
  $pageName = 'New Season';
} else {
  $pageName = 'All Drops';
}
?>

<style>
/* ─── SHOP PAGE ─── */
.shop-hero {
  position: relative;
  background: #0A0A0A;
  padding: 60px 0;
  overflow: hidden;
}
.shop-hero::before {
  content: '';
  position: absolute;
  inset: 0;
  background: radial-gradient(ellipse at 30% 50%, rgba(212,175,55,0.06) 0%, transparent 60%);
}
.shop-hero-inner {
  position: relative;
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  gap: 40px;
}
.shop-hero-left {}
.shop-hero-eyebrow {
  font-size: 10px;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.25em;
  color: #D4AF37;
  margin-bottom: 12px;
}
.shop-hero-title {
  font-family: var(--font-display);
  font-size: clamp(40px, 5vw, 64px);
  font-weight: 300;
  font-style: italic;
  color: #fff;
  line-height: 1;
  letter-spacing: -0.03em;
}
.shop-hero-count {
  font-size: 13px;
  color: rgba(255,255,255,0.4);
  margin-top: 12px;
}
.shop-hero-right {
  display: flex;
  align-items: center;
  gap: 12px;
}
.shop-hero-sort {
  background: rgba(255,255,255,0.06);
  border: 1px solid rgba(255,255,255,0.1);
  padding: 10px 18px;
  border-radius: 40px;
  font-size: 12px;
  font-weight: 600;
  color: #fff;
  cursor: pointer;
  appearance: none;
  font-family: inherit;
}
.shop-hero-sort option { background: #1a1a1a; color: #fff; }

/* ─── FILTER BAR ─── */
.shop-filters {
  padding: 20px 0;
  border-bottom: 1px solid #eee;
  margin-bottom: 40px;
}
.shop-filter-pill {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 10px 22px;
  border-radius: 60px;
  font-size: 12px;
  font-weight: 600;
  letter-spacing: 0.04em;
  text-decoration: none;
  white-space: nowrap;
  transition: all 0.3s ease;
  border: 1px solid #e5e5e5;
  color: #555;
  background: #fff;
}
.shop-filter-pill:hover {
  border-color: #D4AF37;
  color: #D4AF37;
}
.shop-filter-pill.active {
  background: #0F0F0F;
  color: #fff;
  border-color: #0F0F0F;
}
.shop-filter-pill.sale {
  color: #dc2626;
  border-color: #fecaca;
}
.shop-filter-pill.sale:hover,
.shop-filter-pill.sale.active {
  background: #dc2626;
  color: #fff;
  border-color: #dc2626;
}
.shop-filter-divider {
  width: 1px;
  height: 20px;
  background: #ddd;
  flex-shrink: 0;
}

/* Filter Arrows */
.shop-filters-wrap {
  position: relative;
  display: flex;
  align-items: center;
}
.shop-filters-inner {
  flex: 1;
  overflow-x: auto;
  scrollbar-width: none;
  -ms-overflow-style: none;
  scroll-behavior: smooth;
  display: flex;
  align-items: center;
  gap: 10px;
  flex-wrap: nowrap;
  white-space: nowrap;
  padding: 4px 0;
}
.shop-filters-inner::-webkit-scrollbar { display: none; }
.shop-filter-arrow {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  border: 1px solid #ddd;
  background: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  flex-shrink: 0;
  transition: all 0.2s;
  z-index: 2;
  color: #333;
}
.shop-filter-arrow:hover {
  background: #0F0F0F;
  color: #fff;
  border-color: #0F0F0F;
}
.shop-filter-arrow-left { margin-right: 8px; }
.shop-filter-arrow-right { margin-left: 8px; }

/* ─── PRODUCT GRID ─── */
.shop-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 24px;
  margin-bottom: 60px;
}
.shop-card {
  position: relative;
}
.shop-card-img {
  position: relative;
  border-radius: 12px;
  overflow: hidden;
  aspect-ratio: 3/4;
  background: #f5f5f5;
}
.shop-card-img a {
  display: block;
  width: 100%;
  height: 100%;
}
.shop-card-img img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.6s cubic-bezier(0.16,1,0.3,1);
}
.shop-card-img .hover-img {
  position: absolute;
  inset: 0;
  opacity: 0;
}
.shop-card:hover .shop-card-img .hover-img { opacity: 1; }
.shop-card:hover .shop-card-img .main-img { transform: scale(1.05); }

.shop-card-badges {
  position: absolute;
  top: 12px;
  left: 12px;
  display: flex;
  flex-direction: column;
  gap: 6px;
  z-index: 2;
}
.shop-badge-discount {
  background: #dc2626;
  color: #fff;
  font-size: 10px;
  font-weight: 700;
  padding: 4px 10px;
  border-radius: 20px;
}
.shop-badge-bestseller {
  background: #0F0F0F;
  color: #fff;
  font-size: 10px;
  font-weight: 700;
  padding: 4px 10px;
  border-radius: 20px;
}

/* Quick Add Drawer */
.shop-card-quick {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  background: rgba(255,255,255,0.95);
  backdrop-filter: blur(12px);
  padding: 12px;
  transform: translateY(100%);
  transition: transform 0.4s cubic-bezier(0.16,1,0.3,1);
  z-index: 3;
  border-radius: 0 0 12px 12px;
}
.shop-card:hover .shop-card-quick { transform: translateY(0); }
.shop-quick-label {
  font-size: 9px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.12em;
  color: #999;
  margin-bottom: 8px;
  display: block;
}
.shop-quick-sizes {
  display: flex;
  gap: 6px;
}
.shop-quick-sizes button {
  flex: 1;
  padding: 8px 0;
  border: 1px solid #ddd;
  border-radius: 6px;
  background: #fff;
  font-size: 11px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}
.shop-quick-sizes button:hover {
  background: #0F0F0F;
  color: #fff;
  border-color: #0F0F0F;
}

/* Card Info */
.shop-card-info {
  padding: 14px 4px 0;
}
.shop-card-cat {
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  color: #999;
  margin-bottom: 4px;
}
.shop-card-name {
  font-size: 13px;
  font-weight: 600;
  color: #0F0F0F;
  line-height: 1.4;
  margin-bottom: 8px;
}
.shop-card-name a {
  text-decoration: none;
  color: inherit;
  transition: color 0.2s;
}
.shop-card-name a:hover { color: #D4AF37; }
.shop-card-price {
  display: flex;
  align-items: center;
  gap: 8px;
}
.shop-price-now {
  font-size: 14px;
  font-weight: 700;
  color: #0F0F0F;
}
.shop-price-old {
  font-size: 12px;
  color: #bbb;
  text-decoration: line-through;
}
.shop-price-save {
  font-size: 10px;
  font-weight: 700;
  color: #16a34a;
}

/* Wishlist Heart Button */
.shop-card-wishlist {
  position: absolute;
  top: 12px;
  right: 12px;
  z-index: 3;
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: rgba(255,255,255,0.9);
  backdrop-filter: blur(8px);
  border: none;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.shop-card-wishlist:hover {
  background: #fff;
  transform: scale(1.1);
  box-shadow: 0 4px 16px rgba(0,0,0,0.15);
}
.shop-card-wishlist svg {
  width: 18px;
  height: 18px;
  stroke: #999;
  fill: none;
  transition: all 0.3s ease;
}
.shop-card-wishlist:hover svg {
  stroke: #dc2626;
}
.shop-card-wishlist.wishlisted {
  background: #FEF2F2;
}
.shop-card-wishlist.wishlisted svg {
  stroke: #dc2626;
  fill: #dc2626;
}

/* shop-toast removed - using global uoc-toast */

/* ─── BRAND STRIP ─── */
.shop-brand-strip {
  background: #FAF9F6;
  padding: 60px 0;
  margin: 20px 0 60px;
}
.shop-brand-grid {
  display: grid;
  grid-template-columns: 1fr 1fr 1fr 1fr;
  gap: 32px;
}
.shop-brand-item {
  text-align: center;
  padding: 32px 16px;
}
.shop-brand-icon {
  font-size: 32px;
  margin-bottom: 14px;
}
.shop-brand-title {
  font-size: 12px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  color: #0F0F0F;
  margin-bottom: 6px;
}
.shop-brand-desc {
  font-size: 12px;
  color: #888;
  line-height: 1.5;
}

/* ─── NEWSLETTER ─── */
.shop-newsletter {
  background: #0A0A0A;
  padding: 80px 0;
  text-align: center;
}
.shop-nl-title {
  font-family: var(--font-display);
  font-size: clamp(28px, 4vw, 42px);
  font-weight: 300;
  font-style: italic;
  color: #fff;
  margin-bottom: 12px;
}
.shop-nl-desc {
  font-size: 14px;
  color: rgba(255,255,255,0.4);
  margin-bottom: 32px;
}
.shop-nl-form {
  display: flex;
  max-width: 480px;
  margin: 0 auto;
  border-radius: 60px;
  overflow: hidden;
  border: 1px solid rgba(255,255,255,0.1);
}
.shop-nl-input {
  flex: 1;
  padding: 16px 24px;
  border: none;
  background: rgba(255,255,255,0.06);
  color: #fff;
  font-size: 13px;
  font-family: inherit;
  outline: none;
}
.shop-nl-input::placeholder { color: rgba(255,255,255,0.3); }
.shop-nl-btn {
  padding: 16px 32px;
  background: #D4AF37;
  color: #0A0A0A;
  border: none;
  font-size: 11px;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.14em;
  cursor: pointer;
  font-family: inherit;
  transition: background 0.3s;
}
.shop-nl-btn:hover { background: #c9a84c; }

/* ─── RESPONSIVE ─── */
@media (max-width: 1024px) {
  .shop-grid { grid-template-columns: repeat(3, 1fr); gap: 20px; }
  .shop-brand-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 768px) {
  .shop-hero { padding: 40px 0; }
  .shop-hero-inner { flex-direction: column; align-items: flex-start; }
  .shop-grid { grid-template-columns: repeat(2, 1fr); gap: 14px; }
  .shop-brand-grid { grid-template-columns: 1fr 1fr; gap: 20px; }
  .shop-card-quick { transform: translateY(0); position: relative; background: #fff; border-radius: 0 0 12px 12px; }
}
@media (max-width: 480px) {
  .shop-grid { grid-template-columns: 1fr 1fr; gap: 10px; }
  .shop-card-name { font-size: 12px; }
  .shop-price-now { font-size: 13px; }
  .shop-brand-grid { grid-template-columns: 1fr; }
  .shop-nl-form { flex-direction: column; border-radius: 12px; }
  .shop-nl-btn { padding: 14px; }
}
</style>

<!-- Shop Hero -->
<section class="shop-hero">
  <div class="aura-container">
    <div class="shop-hero-inner">
      <div class="shop-hero-left">
        <div class="shop-hero-eyebrow">urban outfit</div>
        <h1 class="shop-hero-title"><?= $pageName ?></h1>
        <p class="shop-hero-count"><?= count($products) ?> curated pieces &bull; 100% genuine fabrics</p>
      </div>
      <div class="shop-hero-right">
        <form method="GET" action="">
          <?php if ($category): ?><input type="hidden" name="category" value="<?= htmlspecialchars($category) ?>"><?php endif; ?>
          <?php if ($sale): ?><input type="hidden" name="sale" value="1"><?php endif; ?>
          <?php if ($newArrivals): ?><input type="hidden" name="new" value="1"><?php endif; ?>
          <select name="sort" onchange="this.form.submit()" class="shop-hero-sort">
            <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Newest First</option>
            <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Price: Low → High</option>
            <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Price: High → Low</option>
          </select>
        </form>
      </div>
    </div>
  </div>
</section>

<!-- Filters -->
<div class="aura-container">
  <div class="shop-filters">
    <div class="shop-filters-wrap">
      <button class="shop-filter-arrow shop-filter-arrow-left" id="filterLeft" onclick="scrollFilters(-1)">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
      </button>
      <div class="shop-filters-inner" id="filterScroll">
      <a href="<?= BASE_URL ?>/shop.php" class="shop-filter-pill <?= empty($category) && !$sale && !$newArrivals ? 'active' : '' ?>">All Drops</a>
      <a href="<?= BASE_URL ?>/shop.php?new=1" class="shop-filter-pill <?= $newArrivals ? 'active' : '' ?>">New Arrivals</a>
      <span class="shop-filter-divider"></span>
      <a href="<?= BASE_URL ?>/shop.php?category=men" class="shop-filter-pill <?= $category === 'men' ? 'active' : '' ?>">Men</a>
      <a href="<?= BASE_URL ?>/shop.php?category=men&subcategory=oversized-tees" class="shop-filter-pill <?= ($category === 'men' && ($_GET['subcategory'] ?? '') === 'oversized-tees') ? 'active' : '' ?>">Men Oversized</a>
      <a href="<?= BASE_URL ?>/shop.php?category=men&subcategory=streetwear" class="shop-filter-pill <?= ($category === 'men' && ($_GET['subcategory'] ?? '') === 'streetwear') ? 'active' : '' ?>">Men Streetwear</a>
      <a href="<?= BASE_URL ?>/shop.php?category=men&subcategory=kurtas" class="shop-filter-pill <?= ($category === 'men' && ($_GET['subcategory'] ?? '') === 'kurtas') ? 'active' : '' ?>">Men Kurtas</a>
      <a href="<?= BASE_URL ?>/shop.php?category=men&subcategory=shirts" class="shop-filter-pill <?= ($category === 'men' && ($_GET['subcategory'] ?? '') === 'shirts') ? 'active' : '' ?>">Men Shirts</a>
      <a href="<?= BASE_URL ?>/shop.php?category=men&subcategory=bottoms" class="shop-filter-pill <?= ($category === 'men' && ($_GET['subcategory'] ?? '') === 'bottoms') ? 'active' : '' ?>">Men Bottoms</a>
      <span class="shop-filter-divider"></span>
      <a href="<?= BASE_URL ?>/shop.php?category=women" class="shop-filter-pill <?= $category === 'women' ? 'active' : '' ?>">Women</a>
      <a href="<?= BASE_URL ?>/shop.php?category=women&subcategory=chikankari" class="shop-filter-pill <?= ($category === 'women' && ($_GET['subcategory'] ?? '') === 'chikankari') ? 'active' : '' ?>">Women Chikankari</a>
      <a href="<?= BASE_URL ?>/shop.php?category=women&subcategory=dresses" class="shop-filter-pill <?= ($category === 'women' && ($_GET['subcategory'] ?? '') === 'dresses') ? 'active' : '' ?>">Women Dresses</a>
      <a href="<?= BASE_URL ?>/shop.php?category=women&subcategory=kurtis" class="shop-filter-pill <?= ($category === 'women' && ($_GET['subcategory'] ?? '') === 'kurtis') ? 'active' : '' ?>">Women Kurtis</a>
      <a href="<?= BASE_URL ?>/shop.php?category=women&subcategory=streetwear" class="shop-filter-pill <?= ($category === 'women' && ($_GET['subcategory'] ?? '') === 'streetwear') ? 'active' : '' ?>">Women Streetwear</a>
      <a href="<?= BASE_URL ?>/shop.php?category=women&subcategory=linen" class="shop-filter-pill <?= ($category === 'women' && ($_GET['subcategory'] ?? '') === 'linen') ? 'active' : '' ?>">Women Linen</a>
      <span class="shop-filter-divider"></span>
      <a href="<?= BASE_URL ?>/shop.php?category=kids" class="shop-filter-pill <?= $category === 'kids' ? 'active' : '' ?>">Kids</a>
      <a href="<?= BASE_URL ?>/shop.php?category=kids&subcategory=boys" class="shop-filter-pill <?= ($category === 'kids' && ($_GET['subcategory'] ?? '') === 'boys') ? 'active' : '' ?>">Boys</a>
      <a href="<?= BASE_URL ?>/shop.php?category=kids&subcategory=girls" class="shop-filter-pill <?= ($category === 'kids' && ($_GET['subcategory'] ?? '') === 'girls') ? 'active' : '' ?>">Girls</a>
      <a href="<?= BASE_URL ?>/shop.php?category=kids&subcategory=ethnic" class="shop-filter-pill <?= ($category === 'kids' && ($_GET['subcategory'] ?? '') === 'ethnic') ? 'active' : '' ?>">Kids Ethnic</a>
      <a href="<?= BASE_URL ?>/shop.php?category=kids&subcategory=co-ords" class="shop-filter-pill <?= ($category === 'kids' && ($_GET['subcategory'] ?? '') === 'co-ords') ? 'active' : '' ?>">Kids Co-Ords</a>
      <span class="shop-filter-divider"></span>
      <a href="<?= BASE_URL ?>/shop.php?category=oversized" class="shop-filter-pill <?= $category === 'oversized' ? 'active' : '' ?>">Oversized Tees</a>
      <a href="<?= BASE_URL ?>/shop.php?category=streetwear" class="shop-filter-pill <?= $category === 'streetwear' ? 'active' : '' ?>">Streetwear</a>
      <a href="<?= BASE_URL ?>/shop.php?category=ethnic-fusion" class="shop-filter-pill <?= $category === 'ethnic-fusion' ? 'active' : '' ?>">Ethnic Fusion</a>
      <a href="<?= BASE_URL ?>/shop.php?category=co-ords" class="shop-filter-pill <?= $category === 'co-ords' ? 'active' : '' ?>">Co-Ords</a>
      <a href="<?= BASE_URL ?>/shop.php?category=linen" class="shop-filter-pill <?= $category === 'linen' ? 'active' : '' ?>">Linen</a>
      <a href="<?= BASE_URL ?>/shop.php?category=chikankari" class="shop-filter-pill <?= $category === 'chikankari' ? 'active' : '' ?>">Chikankari</a>
      <a href="<?= BASE_URL ?>/shop.php?category=accessories" class="shop-filter-pill <?= $category === 'accessories' ? 'active' : '' ?>">Accessories</a>
      <a href="<?= BASE_URL ?>/shop.php?sale=1" class="shop-filter-pill sale <?= $sale ? 'active' : '' ?>">50% OFF Sale</a>
      </div>
      <button class="shop-filter-arrow shop-filter-arrow-right" id="filterRight" onclick="scrollFilters(1)">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
      </button>
    </div>
  </div>

  <!-- Product Grid -->
  <div class="shop-grid">
    <?php foreach ($products as $item): ?>
      <div class="shop-card">
        <div class="shop-card-img">
          <a href="<?= BASE_URL ?>/product.php?slug=<?= $item['slug'] ?>">
            <img src="<?= $item['image'] ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="main-img" loading="lazy">
            <img src="<?= $item['hover_image'] ?? $item['image'] ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="hover-img" loading="lazy">
          </a>
          <button class="shop-card-wishlist <?= in_array($item['id'], $wishlistedIds) ? 'wishlisted' : '' ?>" onclick="toggleWishlist(this, <?= $item['id'] ?>)" title="Add to Wishlist">
            <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
            </svg>
          </button>
          <div class="shop-card-badges">
            <?php if (!empty($item['discount_percent'])): ?>
              <span class="shop-badge-discount"><?= $item['discount_percent'] ?>% OFF</span>
            <?php endif; ?>
            <?php if (!empty($item['is_bestseller'])): ?>
              <span class="shop-badge-bestseller">Bestseller</span>
            <?php endif; ?>
          </div>
          <div class="shop-card-quick">
            <span class="shop-quick-label">Quick Add</span>
            <div class="shop-quick-sizes">
              <button onclick="quickAddToCart(<?= $item['id'] ?>, 'S', '<?= htmlspecialchars(addslashes($item['name'])) ?>', <?= $item['price'] ?>, '<?= htmlspecialchars(addslashes($item['image'])) ?>', '<?= htmlspecialchars(addslashes($item['slug'])) ?>')">S</button>
              <button onclick="quickAddToCart(<?= $item['id'] ?>, 'M', '<?= htmlspecialchars(addslashes($item['name'])) ?>', <?= $item['price'] ?>, '<?= htmlspecialchars(addslashes($item['image'])) ?>', '<?= htmlspecialchars(addslashes($item['slug'])) ?>')">M</button>
              <button onclick="quickAddToCart(<?= $item['id'] ?>, 'L', '<?= htmlspecialchars(addslashes($item['name'])) ?>', <?= $item['price'] ?>, '<?= htmlspecialchars(addslashes($item['image'])) ?>', '<?= htmlspecialchars(addslashes($item['slug'])) ?>')">L</button>
              <button onclick="quickAddToCart(<?= $item['id'] ?>, 'XL', '<?= htmlspecialchars(addslashes($item['name'])) ?>', <?= $item['price'] ?>, '<?= htmlspecialchars(addslashes($item['image'])) ?>', '<?= htmlspecialchars(addslashes($item['slug'])) ?>')">XL</button>
            </div>
          </div>
        </div>
        <div class="shop-card-info">
          <span class="shop-card-cat"><?= htmlspecialchars($item['category_name'] ?? 'Premium Essential') ?></span>
          <h4 class="shop-card-name">
            <a href="<?= BASE_URL ?>/product.php?slug=<?= $item['slug'] ?>"><?= htmlspecialchars($item['name']) ?></a>
          </h4>
          <div class="shop-card-price">
            <span class="shop-price-now">₹<?= number_format($item['price']) ?></span>
            <?php if (!empty($item['original_price']) && $item['original_price'] > $item['price']): ?>
              <span class="shop-price-old">₹<?= number_format($item['original_price']) ?></span>
              <span class="shop-price-save">Save <?= $item['discount_percent'] ?>%</span>
            <?php endif; ?>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <!-- Brand Strip -->
  <section class="shop-brand-strip">
    <div class="aura-container">
      <div class="shop-brand-grid">
        <div class="shop-brand-item">
          <div class="shop-brand-icon">✈️</div>
          <div class="shop-brand-title">Free Shipping</div>
          <div class="shop-brand-desc">On prepaid &amp; COD orders above ₹999</div>
        </div>
        <div class="shop-brand-item">
          <div class="shop-brand-icon">🔄</div>
          <div class="shop-brand-title">7-Day Exchange</div>
          <div class="shop-brand-desc">Hassle-free doorstep reverse pickup</div>
        </div>
        <div class="shop-brand-item">
          <div class="shop-brand-icon">🛡️</div>
          <div class="shop-brand-title">Genuine Fabrics</div>
          <div class="shop-brand-desc">Pure organic linen &amp; combed cotton</div>
        </div>
        <div class="shop-brand-item">
          <div class="shop-brand-icon">🧵</div>
          <div class="shop-brand-title">Handcrafted</div>
          <div class="shop-brand-desc">Made by 500+ Indian artisans</div>
        </div>
      </div>
    </div>
  </section>

  <!-- Newsletter -->
  <section class="shop-newsletter">
    <div class="aura-container">
      <h2 class="shop-nl-title">Stay in the Loop</h2>
      <p class="shop-nl-desc">Get early access to new drops, exclusive sales, and 10% OFF your first order.</p>
      <form class="shop-nl-form" onsubmit="subscribeNewsletter(event)">
        <input type="email" class="shop-nl-input" placeholder="Enter your email address..." required>
        <button type="submit" class="shop-nl-btn">Subscribe →</button>
      </form>
    </div>
  </section>
</div>

<script>
function scrollFilters(dir) {
  const el = document.getElementById('filterScroll');
  el.scrollBy({ left: dir * 300, behavior: 'smooth' });
}

function quickAddToCart(productId, size, name, price, image, slug) {
  fetch('<?= BASE_URL ?>/api/cart.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'action=add&product_id=' + productId + '&size=' + encodeURIComponent(size) + '&quantity=1&product_name=' + encodeURIComponent(name) + '&product_price=' + price + '&product_image=' + encodeURIComponent(image) + '&product_slug=' + encodeURIComponent(slug)
  }).then(r => r.json()).then(data => {
    if (data.success) {
      const badges = document.querySelectorAll('.cart-count');
      badges.forEach(b => b.textContent = data.cart_count || 1);
      showToast(name + ' (Size ' + size + ') added to your bag!');
    } else {
      showToast(data.message || 'Failed to add to cart', true);
    }
  }).catch(() => {
    window.location.href = '<?= BASE_URL ?>/customer/cart.php';
  });
}

function toggleWishlist(btn, productId) {
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
      btn.classList.toggle('wishlisted');
      showToast(data.message);
    }
  }).catch(() => {});
}

function subscribeNewsletter(e) {
  e.preventDefault();
  const form = e.target;
  const email = form.querySelector('input[type=email]').value.trim();
  if (!email) return;
  const btn = form.querySelector('.shop-nl-btn');
  const origText = btn.textContent;
  btn.textContent = 'Subscribing...';
  btn.disabled = true;
  fetch('<?= BASE_URL ?>/api/newsletter.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'email=' + encodeURIComponent(email)
  }).then(r => r.json()).then(data => {
    btn.textContent = origText;
    btn.disabled = false;
    if (data.success) {
      form.querySelector('input[type=email]').value = '';
      showToast(data.message || 'Subscribed successfully!');
    } else {
      showToast(data.message || 'Subscription failed', true);
    }
  }).catch(() => {
    btn.textContent = origText;
    btn.disabled = false;
    showToast('Subscribed successfully! Welcome aboard.', false);
    form.querySelector('input[type=email]').value = '';
  });
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>

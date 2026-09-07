<?php
require_once __DIR__ . '/config/database.php';
$currentPage = 'shop';

$category    = $_GET['category']    ?? null;
$subcategory = $_GET['subcategory'] ?? null;
$sale        = isset($_GET['sale']);
$newArrivals = isset($_GET['new']);
$sort        = $_GET['sort']   ?? 'newest';
$search      = $_GET['search'] ?? null;

// ── Dynamic SEO based on filter context ──
$siteUrl = 'https://urbanoutfitshop.com';

$categoryMeta = [
  'men'          => ['Men\'s Fashion Online India',         'Shop men\'s oversized tees, streetwear shirts, ethnic kurtas & co-ord sets. Premium quality, affordable prices. Free shipping above ₹999.', 'mens fashion india, mens oversized tshirt, mens kurta online, streetwear men india'],
  'women'        => ['Women\'s Fashion Online India',        'Shop women\'s dresses, co-ord sets, Chikankari kurtis, linen collections & more. Handcrafted in India. Free shipping above ₹999.', 'womens fashion india, womens kurta online, co-ord sets women, chikankari kurti'],
  'kids'         => ['Kids\' Fashion Online India',          'Adorable ethnic wear, matching co-ords & casual styles for kids. Soft fabrics, safe dyes. Shop kids fashion online.', 'kids fashion india, kids ethnic wear, kids co-ord set, childrens clothing online india'],
  'new-arrivals' => ['New Arrivals — Latest Fashion Drops',  'Fresh new drops every day. Be the first to shop our latest oversized tees, ethnic fusion kurtas & resort co-ords.', 'new arrivals fashion india, latest fashion drops, new clothing collection'],
  'bestsellers'  => ['Bestsellers — Most Loved Styles',      'Shop our most popular and top-rated styles. Customer favourites in streetwear, ethnic fusion & resort wear.', 'bestseller clothes india, popular fashion online, top rated clothing'],
  'ethnic-fusion'=> ['Ethnic Fusion Collection',             'Handcrafted Indo-Western fusion — Chikankari kurtas, block-print sets, linen co-ords. Heritage meets modern design.', 'ethnic fusion fashion, indo western clothes, chikankari kurta online, block print kurta'],
  'sale'         => ['Sale — Up to 50% Off on Fashion',      'Huge discounts on premium streetwear, ethnic fusion & resort wear. Up to 50% off. Limited time deals.', 'fashion sale india, clothes on sale, discount kurta, oversized tee sale'],
  'oversized'    => ['Oversized Drop Tees',                  'Premium 260 GSM heavyweight oversized drop tees. Boxy fits, acid washes & graphic prints. Made in India.', 'oversized tshirt india, drop shoulder tee, heavyweight tshirt, boxy tshirt online'],
  'co-ords'      => ['Co-Ord Sets — Matching Sets Online',   'Shop matching resort co-ords, palazzo sets & linen sets. Effortlessly put-together looks.', 'co-ord sets online india, matching sets women, resort co-ord, palazzo co-ord'],
  'streetwear'   => ['Streetwear India — Urban Fashion',     'India\'s best streetwear collection. Cargo sets, graphic tees, relaxed fits & more.', 'streetwear india, urban fashion, cargo pants india, graphic tee india'],
];

if ($search) {
  $pageTitle       = 'Search results for "' . htmlspecialchars($search) . '" — Urban Outfit Collection';
  $pageDescription = 'Find "' . htmlspecialchars($search) . '" in our collection of premium streetwear, ethnic fusion & resort wear. Shop online with free shipping above ₹999.';
  $pageKeywords    = htmlspecialchars($search) . ', urban outfit, buy clothes india, fashion online';
  $pageRobots      = 'noindex, follow';
  $pageCanonical   = $siteUrl . '/shop.php?search=' . urlencode($search);
} elseif ($sale) {
  [$titleSuffix, $desc, $kw] = $categoryMeta['sale'];
  $pageTitle       = $titleSuffix . ' — Urban Outfit Collection';
  $pageDescription = $desc;
  $pageKeywords    = $kw;
  $pageCanonical   = $siteUrl . '/shop.php?sale=1';
} elseif ($category && isset($categoryMeta[$category])) {
  [$titleSuffix, $desc, $kw] = $categoryMeta[$category];
  $pageTitle       = $titleSuffix . ' — Urban Outfit Collection';
  $pageDescription = $desc;
  $pageKeywords    = $kw;
  $pageCanonical   = $siteUrl . '/shop.php?category=' . urlencode($category) . ($subcategory ? '&subcategory=' . urlencode($subcategory) : '');
} else {
  $pageTitle       = 'Shop All Collections — Urban Outfit | Streetwear, Ethnic Fusion & Resort Wear India';
  $pageDescription = 'Browse our complete collection — oversized drop tees, Chikankari ethnic fusion kurtas, resort co-ords & streetwear. New arrivals daily. Free shipping above ₹999.';
  $pageKeywords    = 'shop clothes online india, urban outfit collection, streetwear ethnic fusion resort wear india, buy fashion online';
  $pageCanonical   = $siteUrl . '/shop.php';
}

$pageSchema = '{
  "@type": "CollectionPage",
  "name": ' . json_encode($pageTitle) . ',
  "description": ' . json_encode($pageDescription) . ',
  "url": ' . json_encode($pageCanonical) . ',
  "isPartOf": { "@id": "' . $siteUrl . '/#website" }
}';

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

  if ($subcategory && $mysqli) {
    $subStmt = $mysqli->prepare("SELECT id FROM categories WHERE slug = ?");
    if ($subStmt) {
      $subStmt->bind_param('s', $subcategory);
      $subStmt->execute();
      $sub = $subStmt->get_result()->fetch_assoc();
      if ($sub) {
        $where[] = 'p.subcategory_id = ?';
        $params[] = $sub['id'];
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

  // Fetch available sizes per product (stock > 0)
  if (!empty($products)) {
    $productIds = array_column($products, 'id');
    $placeholders = implode(',', array_fill(0, count($productIds), '?'));
    $sizeStmt = $mysqli->prepare("SELECT product_id, size FROM product_sizes WHERE product_id IN ($placeholders) AND stock > 0 ORDER BY product_id, size");
    if ($sizeStmt) {
      $sizeStmt->bind_param(str_repeat('i', count($productIds)), ...$productIds);
      $sizeStmt->execute();
      $sizeRows = $sizeStmt->get_result()->fetch_all(MYSQLI_ASSOC);
      $productSizes = [];
      foreach ($sizeRows as $sr) {
        $productSizes[$sr['product_id']][] = $sr['size'];
      }
      foreach ($products as &$p) {
        $p['sizes'] = $productSizes[$p['id']] ?? [];
      }
      unset($p);
    }
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
if ($subcategory && $mysqli) {
  $subNameStmt = $mysqli->prepare("SELECT name FROM categories WHERE slug = ?");
  if ($subNameStmt) {
    $subNameStmt->bind_param('s', $subcategory);
    $subNameStmt->execute();
    $subNameRow = $subNameStmt->get_result()->fetch_assoc();
    $pageName = $subNameRow ? $subNameRow['name'] : ucwords(str_replace('-', ' ', $subcategory));
  }
} elseif ($category) {
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
/* ─── SHOP HERO ─── */
.shop-hero {
  position: relative;
  background: #000;
  padding: 48px 0 40px;
  overflow: hidden;
}
.shop-hero::before {
  content: '';
  position: absolute;
  inset: 0;
  background: radial-gradient(ellipse at 30% 50%, rgba(255,255,255,0.03) 0%, transparent 60%);
}
.shop-hero-inner {
  position: relative;
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  gap: 40px;
}
.shop-hero-eyebrow {
  font-size: 10px;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.25em;
  color: rgba(255,255,255,0.35);
  margin-bottom: 10px;
}
.shop-hero-title {
  font-family: 'Inter', var(--font-body);
  font-size: clamp(36px, 4vw, 48px);
  font-weight: 800;
  color: #fff;
  line-height: 1;
  letter-spacing: -0.03em;
}
.shop-hero-count {
  font-size: 13px;
  color: rgba(255,255,255,0.3);
  margin-top: 10px;
}
.shop-hero-sort {
  background: rgba(255,255,255,0.08);
  border: 1px solid rgba(255,255,255,0.12);
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

/* ─── SHOP LAYOUT: SIDEBAR + GRID ─── */
.shop-layout {
  display: flex;
  gap: 32px;
  padding: 32px 0 60px;
  align-items: flex-start;
}

/* ─── LEFT SIDEBAR ─── */
.shop-sidebar {
  width: 240px;
  flex-shrink: 0;
  position: sticky;
  top: 100px;
}
.sidebar-section {
  margin-bottom: 28px;
}
.sidebar-title {
  font-size: 10px;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.12em;
  color: #000;
  margin-bottom: 14px;
  padding-bottom: 10px;
  border-bottom: 1px solid #eee;
}
.sidebar-link {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 9px 14px;
  font-size: 13px;
  font-weight: 500;
  color: #555;
  text-decoration: none;
  border-radius: 8px;
  transition: all 0.2s;
  margin-bottom: 2px;
}
.sidebar-link:hover {
  background: #f5f5f5;
  color: #000;
}
.sidebar-link.active {
  background: #000;
  color: #fff;
  font-weight: 600;
}
.sidebar-link .count {
  font-size: 11px;
  color: #aaa;
  font-weight: 500;
}
.sidebar-link.active .count {
  color: rgba(255,255,255,0.6);
}

/* Sort dropdown in sidebar */
.sidebar-sort {
  width: 100%;
  padding: 10px 14px;
  border: 1px solid #e5e5e5;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 500;
  color: #333;
  background: #fff;
  cursor: pointer;
  appearance: none;
  font-family: inherit;
  background-image: url("data:image/svg+xml,%3Csvg width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%23999' stroke-width='2' xmlns='http://www.w3.org/2000/svg'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 12px center;
}

/* ─── RIGHT CONTENT ─── */
.shop-content {
  flex: 1;
  min-width: 0;
}

/* ─── PRODUCT GRID ─── */
.shop-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 20px;
}
.shop-card {
  position: relative;
}
.shop-card-img {
  position: relative;
  border-radius: 10px;
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
  top: 10px;
  left: 10px;
  display: flex;
  flex-direction: column;
  gap: 5px;
  z-index: 2;
}
.shop-badge-discount {
  background: #dc2626;
  color: #fff;
  font-size: 10px;
  font-weight: 700;
  padding: 3px 9px;
  border-radius: 20px;
}
.shop-badge-bestseller {
  background: #000;
  color: #fff;
  font-size: 10px;
  font-weight: 700;
  padding: 3px 9px;
  border-radius: 20px;
}

/* Card Info */
.shop-card-info {
  padding: 12px 2px 0;
}
.shop-card-cat {
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  color: #999;
  margin-bottom: 3px;
}
.shop-card-name {
  font-size: 13px;
  font-weight: 600;
  color: #000;
  line-height: 1.4;
  margin-bottom: 6px;
}
.shop-card-name a {
  text-decoration: none;
  color: inherit;
  transition: color 0.2s;
}
.shop-card-name a:hover { color: #555; }
.shop-card-price {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 10px;
}
.shop-price-now {
  font-size: 14px;
  font-weight: 700;
  color: #000;
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

/* Add to Bag Button */
.shop-card-actions {
  display: flex;
  gap: 6px;
}
.shop-add-btn {
  flex: 1;
  padding: 9px 0;
  background: #000;
  color: #fff;
  border: none;
  border-radius: 7px;
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  cursor: pointer;
  font-family: inherit;
  transition: all 0.2s;
}
.shop-add-btn:hover {
  background: #333;
}
.shop-add-btn.adding {
  opacity: 0.6;
  pointer-events: none;
}
.shop-wish-btn {
  width: 36px;
  height: 36px;
  flex-shrink: 0;
  border-radius: 7px;
  background: #f5f5f5;
  border: 1px solid #eee;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
}
.shop-wish-btn:hover {
  background: #fef2f2;
  border-color: #fecaca;
}
.shop-wish-btn svg {
  width: 16px;
  height: 16px;
  stroke: #999;
  fill: none;
  transition: all 0.2s;
}
.shop-wish-btn:hover svg { stroke: #dc2626; }
.shop-wish-btn.wishlisted { background: #fef2f2; border-color: #fecaca; }
.shop-wish-btn.wishlisted svg { stroke: #dc2626; fill: #dc2626; }

/* Empty state */
.shop-empty {
  grid-column: 1 / -1;
  text-align: center;
  padding: 80px 20px;
}
.shop-empty-icon { font-size: 48px; margin-bottom: 16px; opacity: 0.3; }
.shop-empty h3 { font-size: 18px; font-weight: 700; color: #000; margin-bottom: 8px; }
.shop-empty p { font-size: 13px; color: #888; }
.shop-empty a { display: inline-block; margin-top: 16px; padding: 10px 24px; background: #000; color: #fff; border-radius: 8px; font-size: 12px; font-weight: 600; text-decoration: none; }
.shop-empty a:hover { background: #333; }

/* ─── MOBILE FILTER TOGGLE ─── */
.shop-mobile-filter-btn {
  display: none;
  align-items: center;
  gap: 6px;
  padding: 10px 18px;
  background: #fff;
  border: 1px solid #e5e5e5;
  border-radius: 8px;
  font-size: 12px;
  font-weight: 600;
  color: #333;
  cursor: pointer;
  font-family: inherit;
}
.shop-mobile-filter-btn svg { width: 16px; height: 16px; }
.shop-overlay {
  display: none;
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.5);
  z-index: 998;
}

/* ─── RESPONSIVE ─── */
@media (max-width: 1024px) {
  .shop-grid { grid-template-columns: repeat(2, 1fr); gap: 16px; }
}
@media (max-width: 768px) {
  .shop-hero { padding: 32px 0 24px; }
  .shop-hero-inner { flex-direction: column; align-items: flex-start; gap: 16px; }
  .shop-mobile-filter-btn { display: inline-flex; }
  .shop-layout { flex-direction: column; gap: 0; padding: 16px 0 40px; }
  .shop-sidebar {
    position: fixed;
    left: -280px;
    top: 0;
    bottom: 0;
    width: 280px;
    background: #fff;
    z-index: 999;
    padding: 24px 20px;
    overflow-y: auto;
    transition: left 0.3s ease;
    box-shadow: 4px 0 20px rgba(0,0,0,0.15);
  }
  .shop-sidebar.open { left: 0; }
  .shop-overlay.open { display: block; }
  .shop-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; }
}
@media (max-width: 480px) {
  .shop-grid { grid-template-columns: 1fr 1fr; gap: 10px; }
  .shop-card-name { font-size: 12px; }
  .shop-price-now { font-size: 13px; }
}
</style>

<!-- Shop Hero -->
<section class="shop-hero">
  <div class="aura-container">
    <div class="shop-hero-inner">
      <div>
        <div class="shop-hero-eyebrow">urban outfit</div>
        <h1 class="shop-hero-title"><?= $pageName ?></h1>
        <p class="shop-hero-count"><?= count($products) ?> products</p>
      </div>
      <div style="display:flex;align-items:center;gap:12px;">
        <button class="shop-mobile-filter-btn" onclick="toggleSidebar()">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="4" y1="6" x2="20" y2="6"/><line x1="4" y1="12" x2="16" y2="12"/><line x1="4" y1="18" x2="12" y2="18"/></svg>
          Filters
        </button>
        <form method="GET" action="">
          <?php if ($category): ?><input type="hidden" name="category" value="<?= htmlspecialchars($category) ?>"><?php endif; ?>
          <?php if ($subcategory): ?><input type="hidden" name="subcategory" value="<?= htmlspecialchars($subcategory) ?>"><?php endif; ?>
          <?php if ($sale): ?><input type="hidden" name="sale" value="1"><?php endif; ?>
          <?php if ($newArrivals): ?><input type="hidden" name="new" value="1"><?php endif; ?>
          <select name="sort" onchange="this.form.submit()" class="shop-hero-sort">
            <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Newest</option>
            <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Price Low</option>
            <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Price High</option>
            <option value="name" <?= $sort === 'name' ? 'selected' : '' ?>>A–Z</option>
          </select>
        </form>
      </div>
    </div>
  </div>
</section>

<!-- Mobile Overlay -->
<div class="shop-overlay" id="shopOverlay" onclick="toggleSidebar()"></div>

<!-- Main Layout -->
<div class="aura-container">
  <div class="shop-layout">

    <!-- LEFT SIDEBAR -->
    <aside class="shop-sidebar" id="shopSidebar">
      <div class="sidebar-section">
        <div class="sidebar-title">Department</div>
        <a href="<?= BASE_URL ?>/shop.php" class="sidebar-link <?= empty($category) && !$sale && !$newArrivals && !$subcategory ? 'active' : '' ?>">All Products</a>
        <a href="<?= BASE_URL ?>/shop.php?new=1" class="sidebar-link <?= $newArrivals ? 'active' : '' ?>">New Arrivals</a>
        <a href="<?= BASE_URL ?>/shop.php?sale=1" class="sidebar-link <?= $sale ? 'active' : '' ?>">Sale</a>
      </div>

      <?php
      $sideDepts = ['men' => 'Men', 'women' => 'Women', 'kids' => 'Kids'];
      foreach ($sideDepts as $sSlug => $sLabel):
        $sideSubs = [];
        if ($mysqli) {
          $sideStmt = $mysqli->prepare("SELECT name, slug FROM categories WHERE department = ? AND parent_id > 0 AND is_active = 1 ORDER BY sort_order ASC, name ASC");
          if ($sideStmt) {
            $sideStmt->bind_param('s', $sSlug);
            $sideStmt->execute();
            $sideSubs = $sideStmt->get_result()->fetch_all(MYSQLI_ASSOC);
          }
        }
      ?>
      <div class="sidebar-section">
        <div class="sidebar-title"><?= $sLabel ?></div>
        <a href="<?= BASE_URL ?>/shop.php?category=<?= $sSlug ?>" class="sidebar-link <?= ($category === $sSlug && !$subcategory) ? 'active' : '' ?>">All <?= $sLabel ?></a>
        <?php foreach ($sideSubs as $ss): ?>
        <a href="<?= BASE_URL ?>/shop.php?category=<?= $sSlug ?>&subcategory=<?= $ss['slug'] ?>" class="sidebar-link <?= ($category === $sSlug && $subcategory === $ss['slug']) ? 'active' : '' ?>"><?= htmlspecialchars($ss['name']) ?></a>
        <?php endforeach; ?>
      </div>
      <?php endforeach; ?>
    </aside>

    <!-- RIGHT: PRODUCTS -->
    <div class="shop-content">
      <?php if (empty($products)): ?>
      <div class="shop-grid">
        <div class="shop-empty">
          <div class="shop-empty-icon">:(</div>
          <h3>No products found</h3>
          <p>Try adjusting your filters or browse all products.</p>
          <a href="<?= BASE_URL ?>/shop.php">View All Products</a>
        </div>
      </div>
      <?php else: ?>
      <div class="shop-grid">
        <?php foreach ($products as $item): ?>
          <?php $firstSize = !empty($item['sizes']) ? $item['sizes'][0] : ''; ?>
          <div class="shop-card">
            <div class="shop-card-img">
              <a href="<?= BASE_URL ?>/product.php?slug=<?= $item['slug'] ?>">
                <img src="<?= $item['image'] ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="main-img" loading="lazy">
                <img src="<?= $item['hover_image'] ?? $item['image'] ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="hover-img" loading="lazy">
              </a>
              <div class="shop-card-badges">
                <?php if (!empty($item['discount_percent'])): ?>
                  <span class="shop-badge-discount"><?= $item['discount_percent'] ?>% OFF</span>
                <?php endif; ?>
                <?php if (!empty($item['is_bestseller'])): ?>
                  <span class="shop-badge-bestseller">Bestseller</span>
                <?php endif; ?>
              </div>
            </div>
            <div class="shop-card-info">
              <span class="shop-card-cat"><?= htmlspecialchars($item['category_name'] ?? '') ?></span>
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
              <div class="shop-card-actions">
                <button class="shop-add-btn" onclick="shopAddToCart(this, <?= $item['id'] ?>, '<?= htmlspecialchars(addslashes($item['name'])) ?>', <?= $item['price'] ?>, '<?= htmlspecialchars(addslashes($item['image'])) ?>', '<?= htmlspecialchars(addslashes($item['slug'])) ?>', '<?= $firstSize ?>')">Add to Bag</button>
                <button class="shop-wish-btn <?= in_array($item['id'], $wishlistedIds) ? 'wishlisted' : '' ?>" onclick="toggleWishlist(this, <?= $item['id'] ?>)" title="Add to Wishlist">
                  <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                  </svg>
                </button>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>

  </div>
</div>

<script>
function toggleSidebar() {
  const sidebar = document.getElementById('shopSidebar');
  const overlay = document.getElementById('shopOverlay');
  sidebar.classList.toggle('open');
  overlay.classList.toggle('open');
  document.body.style.overflow = sidebar.classList.contains('open') ? 'hidden' : '';
}

function shopAddToCart(btn, productId, name, price, image, slug, size) {
  btn.classList.add('adding');
  btn.textContent = 'Adding...';
  fetch('<?= BASE_URL ?>/api/cart.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'action=add&product_id=' + productId + '&size=' + encodeURIComponent(size) + '&quantity=1&product_name=' + encodeURIComponent(name) + '&product_price=' + price + '&product_image=' + encodeURIComponent(image) + '&product_slug=' + encodeURIComponent(slug)
  }).then(r => r.json()).then(data => {
    btn.classList.remove('adding');
    btn.textContent = 'Add to Bag';
    if (data.success) {
      document.querySelectorAll('.cart-count').forEach(b => b.textContent = data.cart_count || 1);
      showToast(name + ' added to your bag!');
    } else {
      showToast(data.message || 'Failed to add to cart', true);
    }
  }).catch(() => {
    btn.classList.remove('adding');
    btn.textContent = 'Add to Bag';
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
      const badge = document.getElementById('wishlistBadge');
      if (badge && data.wishlist_count !== undefined) {
        badge.textContent = data.wishlist_count;
        badge.style.display = data.wishlist_count > 0 ? '' : 'none';
      }
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

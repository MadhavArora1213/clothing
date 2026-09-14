<?php
require_once dirname(__DIR__) . '/config/database.php';
header('Content-Type: application/json');

if (!$mysqli) {
  echo json_encode(['success' => false, 'html' => '']);
  exit;
}

$category    = $_GET['category']    ?? null;
$subcategory = $_GET['subcategory'] ?? null;
$sale        = isset($_GET['sale']);
$newArrivals = isset($_GET['new']);
$sort        = $_GET['sort']   ?? 'newest';
$search      = $_GET['search'] ?? null;
$page        = max(1, (int)($_GET['page'] ?? 1));
$perPage     = 12;
$offset      = ($page - 1) * $perPage;

$where = ['p.is_active = 1'];
$params = [];
$types = '';

if ($category) {
  $catStmt = $mysqli->prepare("SELECT id, parent_id FROM categories WHERE slug = ?");
  if ($catStmt) {
    $catStmt->bind_param('s', $category);
    $catStmt->execute();
    $cat = $catStmt->get_result()->fetch_assoc();
    if ($cat) {
      if ($cat['parent_id'] > 0) {
        $where[] = 'p.category_id = ?';
        $params[] = $cat['parent_id'];
        $types .= 'i';
        $where[] = 'p.subcategory_id = ?';
        $params[] = $cat['id'];
        $types .= 'i';
      } else {
        $where[] = 'p.category_id = ?';
        $params[] = $cat['id'];
        $types .= 'i';
      }
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
  'price_asc'  => 'p.price ASC',
  'price_desc' => 'p.price DESC',
  'name'       => 'p.name ASC',
  default      => 'p.created_at DESC'
};

$sql = "SELECT p.*,
          (SELECT image_url FROM product_images WHERE product_id = p.id ORDER BY is_primary DESC, sort_order LIMIT 1) as image,
          (SELECT image_url FROM product_images WHERE product_id = p.id ORDER BY sort_order LIMIT 1 OFFSET 1) as hover_image
        FROM products p
        WHERE $whereClause
        ORDER BY $orderBy
        LIMIT $perPage OFFSET $offset";

$stmt = $mysqli->prepare($sql);
if ($stmt) {
  if (!empty($params)) {
    $pClone = $params;
    $stmt->bind_param($types, ...$pClone);
  }
  $stmt->execute();
  $products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
} else {
  $products = [];
}

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

$wishlistedIds = [];
if (!empty($_SESSION['customer_id']) && $mysqli) {
  $wlStmt = $mysqli->prepare('SELECT product_id FROM wishlists WHERE customer_id = ?');
  if ($wlStmt) {
    $wlStmt->bind_param('i', $_SESSION['customer_id']);
    $wlStmt->execute();
    $wishlistedIds = array_column($wlStmt->get_result()->fetch_all(MYSQLI_ASSOC), 'product_id');
  }
}

ob_start();
if (!empty($products)) {
  foreach ($products as $item):
    $firstSize = !empty($item['sizes']) ? $item['sizes'][0] : '';
?>
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
          <button class="shop-add-btn" onclick="event.preventDefault();event.stopPropagation();openSizePicker(this,'cart')" data-id="<?= $item['id'] ?>" data-sizes='<?= htmlspecialchars(json_encode($item['sizes'] ?? []), ENT_QUOTES) ?>'>Add to Cart</button>
          <button class="shop-buy-btn" onclick="event.preventDefault();event.stopPropagation();openSizePicker(this,'buynow')" data-id="<?= $item['id'] ?>" data-sizes='<?= htmlspecialchars(json_encode($item['sizes'] ?? []), ENT_QUOTES) ?>'>Buy Now</button>
          <button class="shop-wish-btn <?= in_array($item['id'], $wishlistedIds) ? 'wishlisted' : '' ?>" onclick="toggleWishlist(this, <?= $item['id'] ?>)" title="Add to Wishlist">
            <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
            </svg>
          </button>
        </div>
      </div>
    </div>
<?php
  endforeach;
}
$html = ob_get_clean();

$countSql = "SELECT COUNT(*) as total FROM products p WHERE $whereClause";
$countStmt = $mysqli->prepare($countSql);
if ($countStmt) {
  if (!empty($params)) {
    $pClone = $params;
    $countStmt->bind_param($types, ...$pClone);
  }
  $countStmt->execute();
  $total = $countStmt->get_result()->fetch_assoc()['total'];
} else {
  $total = 0;
}

echo json_encode([
  'success' => true,
  'html'    => $html,
  'total'   => (int)$total,
  'page'    => $page,
  'hasMore' => ($offset + $perPage) < $total
]);

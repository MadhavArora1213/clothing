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

$pageTitle = $product['name'] . ' — AURA & CO.';
$pageDescription = $product['description'] ?? '';
include __DIR__ . '/includes/header.php';
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

  </div>
</main>

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
    body: 'action=add&product_id=<?= $product['id'] ?>&size=' + encodeURIComponent(activeSize) + '&quantity=1'
  }).then(r => r.json()).then(data => {
    alert('🎉 Added ' + <?= json_encode($product['name']) ?> + ' (Size ' + activeSize + ') to your bag!');
    window.location.href = '<?= BASE_URL ?>/customer/cart.php';
  }).catch(() => {
    window.location.href = '<?= BASE_URL ?>/customer/cart.php';
  });
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>

<?php
/**
 * Dynamic XML Sitemap — Urban Outfit Collection
 * Served at: https://urbanoutfitshop.com/sitemap.xml
 * Auto-updates whenever products/categories are added or edited.
 */

// Clean any stray output (whitespace, BOM, etc.) that would break XML
if (ob_get_level()) ob_end_clean();
ob_start();

require_once __DIR__ . '/config/database.php';

header('Content-Type: application/xml; charset=utf-8');
header('X-Robots-Tag: noindex');
// Cache for 1 hour on CDN/proxy, revalidate after
header('Cache-Control: public, max-age=3600, s-maxage=3600');

$base  = 'https://urbanoutfitshop.com';
$today = date('Y-m-d');

// ── Helper: get IST date from DB timestamp ──
function sitemapDate($ts) {
  global $today;
  if (empty($ts) || $ts === '0000-00-00 00:00:00') return $today;
  try {
    $dt = new DateTime($ts, new DateTimeZone('UTC'));
    $dt->setTimezone(new DateTimeZone('Asia/Kolkata'));
    return $dt->format('Y-m-d');
  } catch (Exception $e) {
    return $today;
  }
}

$urls = [];

// ════════════════════════════════════════
// STATIC PAGES
// ════════════════════════════════════════
$staticPages = [
  // Core
  ['loc' => $base . '/',                              'pri' => '1.0', 'freq' => 'daily'],
  ['loc' => $base . '/shop.php',                      'pri' => '0.9', 'freq' => 'daily'],

  // Gender departments
  ['loc' => $base . '/shop.php?category=men',         'pri' => '0.8', 'freq' => 'daily'],
  ['loc' => $base . '/shop.php?category=women',       'pri' => '0.8', 'freq' => 'daily'],
  ['loc' => $base . '/shop.php?category=kids',        'pri' => '0.7', 'freq' => 'weekly'],

  // Discovery
  ['loc' => $base . '/shop.php?category=new-arrivals','pri' => '0.8', 'freq' => 'daily'],
  ['loc' => $base . '/shop.php?category=bestsellers', 'pri' => '0.7', 'freq' => 'weekly'],
  ['loc' => $base . '/shop.php?sale=1',               'pri' => '0.8', 'freq' => 'daily'],

  // Style collections
  ['loc' => $base . '/shop.php?category=ethnic-fusion','pri' => '0.7', 'freq' => 'weekly'],
  ['loc' => $base . '/shop.php?category=oversized',   'pri' => '0.7', 'freq' => 'weekly'],
  ['loc' => $base . '/shop.php?category=co-ords',     'pri' => '0.7', 'freq' => 'weekly'],
  ['loc' => $base . '/shop.php?category=streetwear',  'pri' => '0.7', 'freq' => 'weekly'],

  // Info pages
  ['loc' => $base . '/pages/about.php',               'pri' => '0.5', 'freq' => 'monthly'],
  ['loc' => $base . '/pages/contact.php',             'pri' => '0.5', 'freq' => 'monthly'],
  ['loc' => $base . '/pages/shipping.php',            'pri' => '0.4', 'freq' => 'monthly'],
  ['loc' => $base . '/pages/returns.php',             'pri' => '0.4', 'freq' => 'monthly'],
  ['loc' => $base . '/pages/size-guide.php',          'pri' => '0.5', 'freq' => 'monthly'],
];

foreach ($staticPages as $p) {
  $urls[] = [
    'loc'     => $p['loc'],
    'lastmod' => $today,
    'freq'    => $p['freq'],
    'pri'     => $p['pri'],
    'image'   => null,
  ];
}

// ════════════════════════════════════════
// DYNAMIC: PRODUCTS (auto-updates on add/edit)
// ════════════════════════════════════════
if ($mysqli) {
  $productResult = $mysqli->query(
    "SELECT p.slug, p.name, p.updated_at,
            COALESCE(
              (SELECT image_url FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1),
              (SELECT image_url FROM product_images WHERE product_id = p.id ORDER BY sort_order LIMIT 1),
              p.image
            ) as main_image
     FROM products p
     WHERE p.is_active = 1
     ORDER BY p.updated_at DESC
     LIMIT 1000"
  );

  if ($productResult) {
    while ($row = $productResult->fetch_assoc()) {
      $urls[] = [
        'loc'     => $base . '/product.php?slug=' . rawurlencode($row['slug']),
        'lastmod' => sitemapDate($row['updated_at']),
        'freq'    => 'weekly',
        'pri'     => '0.8',
        'image'   => $row['main_image'] ?: null,
        'title'   => $row['name'],
      ];
    }
  }

  // ════════════════════════════════════════
  // DYNAMIC: CATEGORIES (auto-updates on add/edit)
  // ════════════════════════════════════════
  $catResult = $mysqli->query(
    "SELECT slug, department, name, updated_at
     FROM categories
     WHERE is_active = 1
     ORDER BY department, sort_order"
  );

  if ($catResult) {
    while ($row = $catResult->fetch_assoc()) {
      // Only add subcategory URLs (department is already in static pages above)
      if (!empty($row['slug']) && $row['slug'] !== $row['department']) {
        $urls[] = [
          'loc'     => $base . '/shop.php?category=' . rawurlencode($row['department']) . '&subcategory=' . rawurlencode($row['slug']),
          'lastmod' => sitemapDate($row['updated_at']),
          'freq'    => 'weekly',
          'pri'     => '0.6',
          'image'   => null,
        ];
      }
    }
  }
}

// ════════════════════════════════════════
// OUTPUT XML
// ════════════════════════════════════════
ob_end_clean();

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
<?php foreach ($urls as $url): ?>
  <url>
    <loc><?= htmlspecialchars($url['loc'], ENT_XML1, 'UTF-8') ?></loc>
    <lastmod><?= htmlspecialchars($url['lastmod'], ENT_XML1, 'UTF-8') ?></lastmod>
    <changefreq><?= $url['freq'] ?></changefreq>
    <priority><?= $url['pri'] ?></priority>
    <?php if (!empty($url['image'])): ?>
    <image:image>
      <image:loc><?= htmlspecialchars($url['image'], ENT_XML1, 'UTF-8') ?></image:loc>
      <?php if (!empty($url['title'])): ?>
      <image:title><?= htmlspecialchars($url['title'], ENT_XML1, 'UTF-8') ?></image:title>
      <?php endif; ?>
    </image:image>
    <?php endif; ?>
  </url>
<?php endforeach; ?>
</urlset>

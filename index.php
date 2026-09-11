<?php
require_once __DIR__ . '/config/database.php';

$pageTitle       = 'Urban Outfit — Contemporary Indian Streetwear & Ethnic Fusion';
$pageDescription = 'Discover premium streetwear, ethnic fusion, resort co-ords and everyday essentials for men, women and kids. Handcrafted in India.';
$pageKeywords    = 'urban outfit, streetwear india, ethnic fusion, oversized tee, chikankari, linen co-ord, fashion india';
$pageOgImage     = 'https://urbanoutfitshop.com/src/og-default.jpg';
$pageCanonical   = 'https://urbanoutfitshop.com/';
$pageSchema      = '{
  "@type": "CollectionPage",
  "name": "Urban Outfit",
  "url": "https://urbanoutfitshop.com/",
  "isPartOf": { "@id": "https://urbanoutfitshop.com/#website" }
}';

/* ─────────────────────────────────────────────────────────────
   DATA
   ───────────────────────────────────────────────────────────── */

function fetchProducts($mysqli, $where, $order, $limit) {
    $products = [];
    if (!$mysqli) return $products;

    /* $where/$order/$limit are internal, server-defined values. */
    $query = $mysqli->query("
        SELECT p.*,
          c.name AS category_name,
          (SELECT image_url FROM product_images
             WHERE product_id = p.id
             ORDER BY sort_order LIMIT 1) AS image,
          (SELECT image_url FROM product_images
             WHERE product_id = p.id
             ORDER BY sort_order LIMIT 1 OFFSET 1) AS hover_image
        FROM products p
        JOIN categories c ON p.category_id = c.id
        WHERE p.is_active = 1 $where
        ORDER BY $order
        LIMIT $limit
    ");

    if (!$query) return $products;

    $products = $query->fetch_all(MYSQLI_ASSOC);

    if (!empty($products)) {
        $ids = array_column($products, 'id');
        $ph  = implode(',', array_fill(0, count($ids), '?'));

        $ss = $mysqli->prepare("
            SELECT product_id, size
            FROM product_sizes
            WHERE product_id IN ($ph) AND stock > 0
            ORDER BY product_id, size
        ");

        if ($ss) {
            $types = str_repeat('i', count($ids));
            $ss->bind_param($types, ...$ids);
            $ss->execute();

            $rows = $ss->get_result()->fetch_all(MYSQLI_ASSOC);
            $szMap = [];

            foreach ($rows as $r) {
                $szMap[$r['product_id']][] = $r['size'];
            }

            foreach ($products as &$p) {
                $p['sizes'] = $szMap[$p['id']] ?? [];
            }
            unset($p);
        }
    }

    return $products;
}

$departments = [];

if ($mysqli) {
    $deptResult = $mysqli->query("
        SELECT c.id, c.name, c.slug, c.image, c.parent_id
        FROM categories c
        WHERE c.parent_id IN (1, 2, 3)
          AND c.is_active = 1
        ORDER BY c.parent_id, c.sort_order
    ");

    if ($deptResult) {
        foreach ($deptResult->fetch_all(MYSQLI_ASSOC) as $cat) {
            $deptName = match ((int)$cat['parent_id']) {
                2 => 'men',
                1 => 'women',
                3 => 'kids',
                default => 'other'
            };
            $departments[$deptName][] = $cat;
        }
    }
}

$subcats = [];

if ($mysqli) {
    $scResult = $mysqli->query("
        SELECT c.id, c.name, c.slug, c.image, c.parent_id,
               (
                   SELECT COUNT(*)
                   FROM products p
                   WHERE p.category_id = c.id
                     AND p.is_active = 1
               ) AS product_count
        FROM categories c
        WHERE c.parent_id IN (1, 2, 3)
          AND c.is_active = 1
        ORDER BY c.sort_order
    ");

    if ($scResult) {
        $subcats = $scResult->fetch_all(MYSQLI_ASSOC);
    }
}

$heroSlides = [
    [
        'image' => BASE_URL . '/images/hero1.png',
        'eyebrow' => '01 / New season',
        'title' => 'Dress outside the expected.',
        'copy' => 'Contemporary Indian clothing with a street-level attitude.',
        'cta' => 'Shop new arrivals',
        'url' => BASE_URL . '/shop.php?category=new-arrivals'
    ],
    [
        'image' => BASE_URL . '/images/hero2.png',
        'eyebrow' => '02 / Everyday uniform',
        'title' => 'Built for the way you move.',
        'copy' => 'Relaxed silhouettes, substantial fabrics and easy layers.',
        'cta' => 'Explore streetwear',
        'url' => BASE_URL . '/shop.php?category=streetwear'
    ],
    [
        'image' => BASE_URL . '/images/hero3.png',
        'eyebrow' => '03 / Heritage, remixed',
        'title' => 'Tradition. Re-cut.',
        'copy' => 'Craft-led pieces designed for a modern Indian wardrobe.',
        'cta' => 'Shop ethnic fusion',
        'url' => BASE_URL . '/shop.php?category=ethnic-fusion'
    ],
];

$newArrivals = fetchProducts($mysqli, '', 'p.created_at DESC', 8);
$bestsellers = fetchProducts($mysqli, '', 'p.is_featured DESC, p.created_at DESC', 8);

$menImage   = !empty($departments['men'][0]['image']) ? $departments['men'][0]['image'] : 'https://images.unsplash.com/photo-1617137984095-74e4e5e3613f?w=1000&h=1200&auto=format&fit=crop&q=85';
$womenImage = !empty($departments['women'][0]['image']) ? $departments['women'][0]['image'] : 'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=1000&h=1200&auto=format&fit=crop&q=85';
$kidsImage  = !empty($departments['kids'][0]['image']) ? $departments['kids'][0]['image'] : 'https://images.unsplash.com/photo-1503944583220-79d8926ad5e2?w=1000&h=1200&auto=format&fit=crop&q=85';

function uocProductCard(array $item, string $variant = 'grid'): void {
    $image = !empty($item['image'])
        ? htmlspecialchars($item['image'])
        : 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=900&auto=format&fit=crop&q=85';

    $hover = !empty($item['hover_image'])
        ? htmlspecialchars($item['hover_image'])
        : $image;

    $name = htmlspecialchars($item['name']);
    $slug = htmlspecialchars($item['slug']);
    $id   = (int)$item['id'];
    $sizes = $item['sizes'] ?? [];
    ?>
    <article class="uoc-product-card <?= $variant === 'rail' ? 'uoc-product-card--rail' : '' ?>">
        <a href="<?= BASE_URL ?>/product.php?slug=<?= $slug ?>" class="uoc-product-link" aria-label="<?= $name ?>">
            <div class="uoc-product-media">
                <img src="<?= $image ?>" alt="<?= $name ?>" class="uoc-img-main" loading="lazy">
                <img src="<?= $hover ?>" alt="" class="uoc-img-hover" loading="lazy">

                <div class="uoc-product-topline">
                    <?php if (!empty($item['discount_percent'])): ?>
                        <span class="uoc-product-badge">-<?= (int)$item['discount_percent'] ?>%</span>
                    <?php elseif (!empty($item['is_bestseller'])): ?>
                        <span class="uoc-product-badge">Bestseller</span>
                    <?php endif; ?>

                    <button
                        type="button"
                        class="uoc-wish"
                        onclick="event.preventDefault();event.stopPropagation();toggleWishlist(<?= $id ?>,this)"
                        aria-label="Add <?= $name ?> to wishlist">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                            <path d="M20.8 8.9c0 5.2-8.8 10-8.8 10s-8.8-4.8-8.8-10A4.9 4.9 0 0 1 8.1 4c1.7 0 3.1.9 3.9 2.2C12.9 4.9 14.3 4 16 4a4.9 4.9 0 0 1 4.8 4.9Z"/>
                        </svg>
                    </button>
                </div>

                <?php if (!empty($sizes)): ?>
                <div class="uoc-quick-add">
                    <span>Quick add</span>
                    <div>
                        <?php foreach ($sizes as $sz): ?>
                            <button
                                type="button"
                                onclick="event.preventDefault();event.stopPropagation();quickAddToCart(<?= $id ?>,'<?= htmlspecialchars($sz, ENT_QUOTES) ?>')">
                                <?= htmlspecialchars($sz) ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <div class="uoc-product-info">
                <div class="uoc-product-meta">
                    <span><?= htmlspecialchars($item['category_name'] ?? 'Urban Outfit') ?></span>
                    <?php if (!empty($item['is_bestseller'])): ?><span>Best seller</span><?php endif; ?>
                </div>

                <h3 class="uoc-product-name"><?= $name ?></h3>

                <div class="uoc-product-price">
                    <strong>₹<?= number_format((float)$item['price']) ?></strong>
                    <?php if (!empty($item['original_price']) && $item['original_price'] > $item['price']): ?>
                        <del>₹<?= number_format((float)$item['original_price']) ?></del>
                    <?php endif; ?>
                </div>
            </div>
        </a>

        <?php if ($variant !== 'rail'): ?>
        <div class="uoc-product-actions">
            <button
                type="button"
                onclick="event.preventDefault();event.stopPropagation();openSizePicker(this,'cart')"
                data-id="<?= $id ?>"
                data-sizes='<?= htmlspecialchars(json_encode($sizes), ENT_QUOTES) ?>'>
                Add to bag
            </button>
            <button
                type="button"
                onclick="event.preventDefault();event.stopPropagation();openSizePicker(this,'buynow')"
                data-id="<?= $id ?>"
                data-sizes='<?= htmlspecialchars(json_encode($sizes), ENT_QUOTES) ?>'>
                Buy now
            </button>
        </div>
        <?php endif; ?>
    </article>
    <?php
}

include __DIR__ . '/includes/header.php';
?>

<style>
/* ============================================================
   URBAN OUTFIT / EDITORIAL REDESIGN
   Hallmark-inspired principles:
   - editorial masthead instead of generic centered nav
   - serif display + sans body
   - warm paper palette + one controlled accent
   - asymmetric composition
   - solid CTAs, no gradient hero
   - restrained borders and motion
   - responsive layouts designed, not merely stacked
   ============================================================ */

:root {
    --uoc-paper: #ffffff;
    --uoc-paper-2: #f5f5f5;
    --uoc-ink: #171714;
    --uoc-muted: #726e65;
    --uoc-line: #d7d0c3;
    --uoc-accent: #c85c3f;
    --uoc-white: #ffffff;
    --uoc-serif: "DM Serif Display", Georgia, serif;
    --uoc-sans: "Manrope", "Helvetica Neue", Arial, sans-serif;
    --uoc-space: 24px;
    --uoc-ease: cubic-bezier(.16,1,.3,1);
}

html { overflow-x: clip; }
body {
    background: var(--uoc-paper);
    color: var(--uoc-ink);
    font-family: var(--uoc-sans);
}

.uoc-main {
    background: var(--uoc-paper);
}

.uoc-container {
    width: min(1360px, calc(100% - 48px));
    margin-inline: auto;
}

.uoc-eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 9px;
    margin-bottom: 12px;
    color: var(--uoc-muted);
    font: 700 10px/1 var(--uoc-sans);
    letter-spacing: .16em;
    text-transform: uppercase;
}

.uoc-eyebrow::before {
    content: "";
    width: 24px;
    height: 1px;
    background: currentColor;
}

.uoc-section-title {
    margin: 0;
    font: 400 clamp(36px, 5vw, 68px)/.94 var(--uoc-serif);
    letter-spacing: -.035em;
}

.uoc-view-all {
    color: var(--uoc-ink);
    font: 700 11px/1 var(--uoc-sans);
    letter-spacing: .12em;
    text-transform: uppercase;
    text-decoration: none;
    border-bottom: 1px solid currentColor;
    padding-bottom: 7px;
    white-space: nowrap;
}

.uoc-view-all:hover { color: var(--uoc-accent); }

/* ── Editorial masthead ── */
.uoc-editorial-bar {
    border-bottom: 1px solid var(--uoc-line);
    padding: 10px 0;
    font-size: 10px;
    letter-spacing: .12em;
    text-transform: uppercase;
    color: var(--uoc-muted);
}

.uoc-editorial-bar-inner {
    display: flex;
    justify-content: space-between;
    gap: 16px;
}

.uoc-masthead {
    border-bottom: 1px solid var(--uoc-line);
    background: var(--uoc-paper);
}

.uoc-masthead-inner {
    min-height: 92px;
    display: grid;
    grid-template-columns: 1fr auto 1fr;
    align-items: center;
    gap: 24px;
}

.uoc-masthead-left,
.uoc-masthead-right {
    display: flex;
    align-items: center;
    gap: 22px;
}

.uoc-masthead-right { justify-content: flex-end; }

.uoc-masthead-link {
    color: var(--uoc-ink);
    text-decoration: none;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .11em;
    text-transform: uppercase;
}

.uoc-masthead-link:hover { color: var(--uoc-accent); }

.uoc-wordmark {
    color: var(--uoc-ink);
    text-decoration: none;
    text-align: center;
    font: 400 clamp(34px, 4vw, 52px)/.8 var(--uoc-serif);
    letter-spacing: -.05em;
}

.uoc-wordmark small {
    display: block;
    margin-top: 8px;
    font: 700 8px/1 var(--uoc-sans);
    letter-spacing: .3em;
    text-transform: uppercase;
}

.uoc-cart-mark {
    position: relative;
    color: var(--uoc-ink);
    text-decoration: none;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: .08em;
    text-transform: uppercase;
}

.uoc-cart-mark .cart-count {
    margin-left: 5px;
}

/* ── Hero: full-bleed image, editorial copy block ── */
.uoc-hero {
    position: relative;
    min-height: min(760px, 78vh);
    overflow: hidden;
    background: #cfc8bc;
}

.uoc-hero-slider,
.uoc-hero-slide {
    position: absolute;
    inset: 0;
}

.uoc-hero-slide {
    opacity: 0;
    transition: opacity .9s ease;
}

.uoc-hero-slide.active { opacity: 1; }

.uoc-hero-slide img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
    filter: saturate(.86);
}

.uoc-hero::after {
    content: "";
    position: absolute;
    left: 0;
    right: 0;
    bottom: 0;
    height: 38%;
    background: linear-gradient(to top, rgba(0,0,0,.42), transparent);
    pointer-events: none;
}

.uoc-hero-copy {
    position: absolute;
    z-index: 2;
    left: max(24px, calc((100vw - 1360px) / 2));
    bottom: 64px;
    width: min(620px, calc(100% - 48px));
    color: white;
}

.uoc-hero-index {
    display: block;
    margin-bottom: 20px;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: .18em;
    text-transform: uppercase;
}

.uoc-hero-copy h1 {
    margin: 0;
    max-width: 650px;
    font: 400 clamp(52px, 8vw, 106px)/.86 var(--uoc-serif);
    letter-spacing: -.045em;
}

.uoc-hero-copy p {
    max-width: 470px;
    margin: 22px 0 28px;
    font-size: 15px;
    line-height: 1.6;
}

.uoc-hero-actions {
    display: flex;
    align-items: center;
    gap: 18px;
}

.uoc-btn-editorial {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 48px;
    padding: 0 22px;
    background: var(--uoc-white);
    color: var(--uoc-ink);
    border: 1px solid var(--uoc-white);
    text-decoration: none;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: .12em;
    text-transform: uppercase;
}

.uoc-btn-editorial:hover {
    background: var(--uoc-accent);
    border-color: var(--uoc-accent);
    color: white;
}

.uoc-hero-dots {
    position: absolute;
    z-index: 4;
    right: 28px;
    bottom: 30px;
    display: flex;
    gap: 7px;
}

.uoc-hero-dot {
    width: 7px;
    height: 7px;
    border: 0;
    border-radius: 50%;
    padding: 0;
    background: rgba(255,255,255,.45);
    cursor: pointer;
}

.uoc-hero-dot.active {
    width: 28px;
    border-radius: 10px;
    background: white;
}

/* ── Trust: typographic, not icon tiles ── */
.uoc-trust {
    border-bottom: 1px solid var(--uoc-line);
    background: var(--uoc-paper-2);
}

.uoc-trust-inner {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
}

.uoc-trust-item {
    min-height: 74px;
    padding: 16px 20px;
    border-right: 1px solid var(--uoc-line);
    display: flex;
    align-items: center;
    gap: 13px;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: .1em;
    text-transform: uppercase;
}

.uoc-trust-item:last-child { border-right: 0; }

.uoc-trust-num {
    font: 400 24px/1 var(--uoc-serif);
    color: var(--uoc-accent);
}

/* ── Intro: asymmetric ── */
.uoc-intro {
    padding: 110px 0 96px;
}

.uoc-intro-grid {
    display: grid;
    grid-template-columns: 1.1fr .9fr;
    gap: 9vw;
    align-items: end;
}

.uoc-intro h2 {
    max-width: 820px;
    margin: 0;
    font: 400 clamp(44px, 6.2vw, 88px)/.92 var(--uoc-serif);
    letter-spacing: -.045em;
}

.uoc-intro h2 em {
    color: var(--uoc-accent);
    font-style: normal;
}

.uoc-intro-copy {
    padding-bottom: 5px;
    border-top: 1px solid var(--uoc-line);
    padding-top: 18px;
}

.uoc-intro-copy p {
    max-width: 390px;
    margin: 0 0 24px;
    color: var(--uoc-muted);
    font-size: 14px;
    line-height: 1.8;
}

/* ── Category editorial cards ── */
.uoc-category-section {
    padding: 0 0 110px;
}

.uoc-category-head {
    display: flex;
    justify-content: space-between;
    align-items: end;
    gap: 20px;
    margin-bottom: 30px;
}

.uoc-category-grid {
    display: grid;
    grid-template-columns: 1fr 1.35fr 1fr;
    gap: 14px;
    align-items: end;
}

.uoc-category-card {
    position: relative;
    display: block;
    color: white;
    text-decoration: none;
    overflow: hidden;
    background: #d5cfc4;
}

.uoc-category-card:nth-child(1) { aspect-ratio: 4 / 5; }
.uoc-category-card:nth-child(2) { aspect-ratio: 4 / 5.35; }
.uoc-category-card:nth-child(3) { aspect-ratio: 4 / 5; }

.uoc-category-card img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    filter: saturate(.82);
    transition: transform .8s var(--uoc-ease);
}

.uoc-category-card:hover img { transform: scale(1.035); }

.uoc-category-card::after {
    content: "";
    position: absolute;
    inset: 55% 0 0;
    background: linear-gradient(to top, rgba(0,0,0,.58), transparent);
}

.uoc-category-copy {
    position: absolute;
    z-index: 2;
    inset: auto 20px 20px;
}

.uoc-category-copy small {
    display: block;
    margin-bottom: 7px;
    font-size: 9px;
    font-weight: 800;
    letter-spacing: .15em;
    text-transform: uppercase;
}

.uoc-category-copy h3 {
    margin: 0;
    font: 400 clamp(30px, 4vw, 54px)/.9 var(--uoc-serif);
}

/* ── Marquee ── */
.uoc-marquee {
    overflow: hidden;
    border-block: 1px solid var(--uoc-line);
    background: var(--uoc-ink);
    color: var(--uoc-paper);
}

.uoc-marquee-track {
    display: flex;
    width: max-content;
    animation: uoc-marquee 34s linear infinite;
}

.uoc-marquee-item {
    display: flex;
    align-items: center;
    gap: 24px;
    padding: 18px 24px;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: .14em;
    text-transform: uppercase;
}

.uoc-marquee-item::after {
    content: "✦";
    color: var(--uoc-accent);
}

@keyframes uoc-marquee {
    from { transform: translateX(0); }
    to { transform: translateX(-33.333%); }
}

/* ── Product sections ── */
.uoc-products-section {
    padding: 112px 0;
}

.uoc-products-section--alt {
    background: var(--uoc-paper-2);
    border-block: 1px solid var(--uoc-line);
}

.uoc-products-head {
    display: grid;
    grid-template-columns: 1fr auto;
    gap: 30px;
    align-items: end;
    margin-bottom: 42px;
}

.uoc-products-note {
    max-width: 350px;
    margin: 0;
    color: var(--uoc-muted);
    font-size: 13px;
    line-height: 1.65;
    justify-self: end;
}

.uoc-product-rail {
    display: grid;
    grid-template-columns: repeat(4, minmax(0,1fr));
    gap: 18px;
}

.uoc-product-card {
    min-width: 0;
}

.uoc-product-media {
    position: relative;
    overflow: hidden;
    aspect-ratio: 3 / 4;
    background: #ddd7cc;
}

.uoc-img-main,
.uoc-img-hover {
    width: 100%;
    height: 100%;
    display: block;
    object-fit: cover;
    transition: opacity .45s ease, transform .75s var(--uoc-ease);
}

.uoc-img-main { position: relative; }
.uoc-img-hover {
    position: absolute;
    inset: 0;
    opacity: 0;
    transform: scale(1.015);
}

.uoc-product-card:hover .uoc-img-main { transform: scale(1.025); }
.uoc-product-card:hover .uoc-img-hover {
    opacity: 1;
    transform: scale(1);
}

.uoc-product-topline {
    position: absolute;
    inset: 14px 14px auto;
    display: flex;
    justify-content: space-between;
    align-items: start;
    z-index: 2;
}

.uoc-product-badge {
    padding: 7px 9px;
    background: var(--uoc-white);
    color: var(--uoc-ink);
    font-size: 8px;
    font-weight: 900;
    letter-spacing: .1em;
    text-transform: uppercase;
}

.uoc-wish {
    width: 38px;
    height: 38px;
    display: grid;
    place-items: center;
    border: 0;
    background: var(--uoc-white);
    color: var(--uoc-ink);
    cursor: pointer;
}

.uoc-wish svg { width: 18px; height: 18px; }

.uoc-wish.active {
    background: var(--uoc-accent);
    color: white;
}

.uoc-quick-add {
    position: absolute;
    z-index: 3;
    left: 12px;
    right: 12px;
    bottom: 12px;
    padding: 10px;
    background: rgba(255,253,248,.94);
    opacity: 0;
    transform: translateY(8px);
    transition: opacity .3s ease, transform .3s var(--uoc-ease);
}

.uoc-product-card:hover .uoc-quick-add {
    opacity: 1;
    transform: translateY(0);
}

.uoc-quick-add > span {
    display: block;
    margin-bottom: 7px;
    color: var(--uoc-muted);
    font-size: 8px;
    font-weight: 800;
    letter-spacing: .12em;
    text-transform: uppercase;
}

.uoc-quick-add > div {
    display: flex;
    gap: 5px;
}

.uoc-quick-add button {
    min-width: 35px;
    height: 32px;
    border: 1px solid var(--uoc-line);
    background: transparent;
    color: var(--uoc-ink);
    cursor: pointer;
    font-size: 9px;
    font-weight: 800;
}

.uoc-quick-add button:hover {
    border-color: var(--uoc-ink);
    background: var(--uoc-ink);
    color: white;
}

.uoc-product-info { padding: 14px 0 0; }

.uoc-product-meta {
    display: flex;
    justify-content: space-between;
    gap: 10px;
    color: var(--uoc-muted);
    font-size: 8px;
    font-weight: 800;
    letter-spacing: .1em;
    text-transform: uppercase;
}

.uoc-product-name {
    margin: 7px 0 9px;
    font: 400 18px/1.1 var(--uoc-serif);
}

.uoc-product-price {
    display: flex;
    align-items: baseline;
    gap: 9px;
    font-size: 12px;
}

.uoc-product-price strong { font-weight: 800; }
.uoc-product-price del {
    color: var(--uoc-muted);
    font-size: 10px;
}

.uoc-product-actions {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 5px;
    margin-top: 10px;
}

.uoc-product-actions button {
    min-height: 40px;
    border: 1px solid var(--uoc-ink);
    background: transparent;
    color: var(--uoc-ink);
    cursor: pointer;
    font: 800 9px/1 var(--uoc-sans);
    letter-spacing: .1em;
    text-transform: uppercase;
}

.uoc-product-actions button:first-child {
    background: var(--uoc-ink);
    color: white;
}

.uoc-product-actions button:hover {
    background: var(--uoc-accent);
    border-color: var(--uoc-accent);
    color: white;
}

/* ── Collection index ── */
.uoc-collection-index {
    padding: 105px 0;
    border-top: 1px solid var(--uoc-line);
}

.uoc-collection-layout {
    display: grid;
    grid-template-columns: .75fr 1.25fr;
    gap: 80px;
}

.uoc-collection-intro h2 {
    margin: 0 0 20px;
    font: 400 clamp(42px, 5vw, 72px)/.92 var(--uoc-serif);
    letter-spacing: -.04em;
}

.uoc-collection-intro p {
    max-width: 330px;
    color: var(--uoc-muted);
    font-size: 13px;
    line-height: 1.75;
}

.uoc-collection-list {
    border-top: 1px solid var(--uoc-line);
}

.uoc-collection-row {
    display: grid;
    grid-template-columns: 48px 1fr auto 90px;
    gap: 20px;
    align-items: center;
    min-height: 88px;
    border-bottom: 1px solid var(--uoc-line);
    color: var(--uoc-ink);
    text-decoration: none;
}

.uoc-collection-row:hover { color: var(--uoc-accent); }

.uoc-collection-row-num {
    color: var(--uoc-muted);
    font-size: 9px;
    font-weight: 800;
    letter-spacing: .1em;
}

.uoc-collection-row h3 {
    margin: 0;
    font: 400 28px/1 var(--uoc-serif);
}

.uoc-collection-count {
    color: var(--uoc-muted);
    font-size: 9px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .08em;
}

.uoc-collection-arrow {
    text-align: right;
    font-size: 20px;
}

/* ── Heritage feature ── */
.uoc-feature-story {
    display: grid;
    grid-template-columns: 1.35fr .65fr;
    min-height: 650px;
    background: var(--uoc-ink);
    color: white;
}

.uoc-feature-story-image {
    min-height: 520px;
}

.uoc-feature-story-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    filter: saturate(.72);
}

.uoc-feature-story-copy {
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: clamp(36px, 6vw, 90px);
}

.uoc-feature-story-copy .uoc-eyebrow { color: #bdb7ac; }

.uoc-feature-story-copy h2 {
    margin: 0;
    font: 400 clamp(42px, 5.2vw, 76px)/.9 var(--uoc-serif);
    letter-spacing: -.04em;
}

.uoc-feature-story-copy p {
    max-width: 360px;
    margin: 25px 0 30px;
    color: #aaa59b;
    font-size: 13px;
    line-height: 1.75;
}

.uoc-btn-dark-story {
    width: fit-content;
    display: inline-flex;
    min-height: 46px;
    align-items: center;
    padding: 0 19px;
    background: var(--uoc-accent);
    color: white;
    text-decoration: none;
    font-size: 9px;
    font-weight: 900;
    letter-spacing: .12em;
    text-transform: uppercase;
}

.uoc-btn-dark-story:hover { background: white; color: var(--uoc-ink); }

/* ── Proof / testimonials ── */
.uoc-proof {
    padding: 112px 0;
}

.uoc-proof-head {
    display: flex;
    justify-content: space-between;
    align-items: end;
    margin-bottom: 44px;
}

.uoc-proof-grid {
    display: grid;
    grid-template-columns: 1.3fr 1fr 1fr;
    border-top: 1px solid var(--uoc-line);
    border-bottom: 1px solid var(--uoc-line);
}

.uoc-proof-card {
    padding: 34px 28px;
    border-right: 1px solid var(--uoc-line);
}

.uoc-proof-card:last-child { border-right: 0; }

.uoc-proof-card:first-child {
    background: var(--uoc-paper-2);
}

.uoc-proof-stars {
    margin-bottom: 22px;
    color: var(--uoc-accent);
    letter-spacing: 3px;
    font-size: 11px;
}

.uoc-proof-card blockquote {
    margin: 0 0 28px;
    font: 400 clamp(21px, 2.3vw, 30px)/1.08 var(--uoc-serif);
    letter-spacing: -.02em;
}

.uoc-proof-card p {
    margin: 0 0 26px;
    color: var(--uoc-muted);
    font-size: 13px;
    line-height: 1.7;
}

.uoc-proof-author {
    font-size: 9px;
    font-weight: 900;
    letter-spacing: .12em;
    text-transform: uppercase;
}

/* ── Instagram: raw grid, no fake icon tiles ── */
.uoc-social {
    padding-bottom: 0;
}

.uoc-social-head {
    padding: 0 0 30px;
    display: flex;
    justify-content: space-between;
    align-items: end;
}

.uoc-social-grid {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 3px;
}

.uoc-social-item {
    display: block;
    aspect-ratio: 1;
    overflow: hidden;
    background: #d7d0c4;
}

.uoc-social-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    filter: saturate(.8);
    transition: transform .7s var(--uoc-ease), filter .4s ease;
}

.uoc-social-item:hover img {
    transform: scale(1.05);
    filter: saturate(1);
}

/* ── Newsletter / footer lead ── */
.uoc-newsletter {
    margin-top: 0;
    padding: 105px 0;
    border-top: 1px solid var(--uoc-line);
}

.uoc-newsletter-grid {
    display: grid;
    grid-template-columns: 1.1fr .9fr;
    gap: 80px;
    align-items: end;
}

.uoc-newsletter h2 {
    margin: 0;
    max-width: 700px;
    font: 400 clamp(44px, 6vw, 82px)/.9 var(--uoc-serif);
    letter-spacing: -.045em;
}

.uoc-newsletter-copy {
    border-top: 1px solid var(--uoc-line);
    padding-top: 18px;
}

.uoc-newsletter-copy p {
    margin: 0 0 22px;
    color: var(--uoc-muted);
    font-size: 13px;
    line-height: 1.7;
}

.uoc-newsletter-form {
    display: flex;
    border-bottom: 1px solid var(--uoc-ink);
}

.uoc-newsletter-form input {
    min-width: 0;
    flex: 1;
    height: 50px;
    border: 0;
    outline: 0;
    background: transparent;
    color: var(--uoc-ink);
    font: 13px var(--uoc-sans);
}

.uoc-newsletter-form button {
    border: 0;
    background: transparent;
    color: var(--uoc-ink);
    cursor: pointer;
    font: 900 9px var(--uoc-sans);
    letter-spacing: .12em;
    text-transform: uppercase;
}

.uoc-newsletter-form button:hover { color: var(--uoc-accent); }

/* ── Size picker ── */
.uoc-sz-overlay {
    position: fixed;
    inset: 0;
    z-index: 9997;
    background: rgba(23,23,20,.52);
    opacity: 0;
    pointer-events: none;
    transition: opacity .25s ease;
}

.uoc-sz-overlay.show {
    opacity: 1;
    pointer-events: auto;
}

.uoc-sz-modal {
    position: fixed;
    z-index: 9998;
    left: 50%;
    top: 50%;
    width: min(430px, calc(100% - 32px));
    transform: translate(-50%, -46%);
    background: var(--uoc-paper);
    border: 1px solid var(--uoc-ink);
    opacity: 0;
    pointer-events: none;
    transition: opacity .25s ease, transform .35s var(--uoc-ease);
}

.uoc-sz-modal.show {
    opacity: 1;
    pointer-events: auto;
    transform: translate(-50%, -50%);
}

.uoc-sz-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    border-bottom: 1px solid var(--uoc-line);
}

.uoc-sz-title {
    font: 400 25px/1 var(--uoc-serif);
}

.uoc-sz-close {
    width: 34px;
    height: 34px;
    border: 1px solid var(--uoc-line);
    background: transparent;
    cursor: pointer;
    font-size: 20px;
}

.uoc-sz-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 7px;
    padding: 20px;
}

.uoc-sz-btn {
    min-height: 46px;
    border: 1px solid var(--uoc-line);
    background: transparent;
    cursor: pointer;
    font: 800 10px var(--uoc-sans);
}

.uoc-sz-btn:hover {
    background: var(--uoc-ink);
    border-color: var(--uoc-ink);
    color: white;
}

.uoc-sz-note {
    padding: 0 20px 20px;
    color: var(--uoc-muted);
    font-size: 10px;
}

/* ── Responsive ── */
@media (max-width: 1050px) {
    .uoc-masthead-inner { grid-template-columns: 1fr auto; }
    .uoc-masthead-left { display: none; }
    .uoc-masthead-right { justify-content: flex-end; }

    .uoc-intro-grid,
    .uoc-newsletter-grid { gap: 50px; }

    .uoc-product-rail { grid-template-columns: repeat(4, minmax(220px, 1fr)); overflow-x: auto; scrollbar-width: none; }
    .uoc-product-rail::-webkit-scrollbar { display: none; }
    .uoc-product-card--rail { min-width: 220px; }

    .uoc-social-grid { grid-template-columns: repeat(3, 1fr); }
}

@media (max-width: 760px) {
    .uoc-container { width: min(100% - 32px, 1360px); }

    .uoc-editorial-bar-inner span:last-child { display: none; }
    .uoc-masthead-inner { min-height: 72px; }
    .uoc-wordmark { font-size: 38px; }

    .uoc-hero { min-height: 650px; }
    .uoc-hero-copy {
        left: 16px;
        bottom: 48px;
        width: calc(100% - 32px);
    }
    .uoc-hero-copy h1 { font-size: clamp(50px, 15vw, 74px); }
    .uoc-hero-copy p { font-size: 13px; }
    .uoc-hero-actions { flex-wrap: wrap; }

    .uoc-trust-inner { grid-template-columns: 1fr 1fr; }
    .uoc-trust-item { min-height: 64px; border-bottom: 1px solid var(--uoc-line); }
    .uoc-trust-item:nth-child(2) { border-right: 0; }

    .uoc-intro { padding: 72px 0; }
    .uoc-intro-grid,
    .uoc-newsletter-grid,
    .uoc-collection-layout,
    .uoc-feature-story { grid-template-columns: 1fr; }

    .uoc-intro h2 { font-size: 48px; }
    .uoc-intro-copy { margin-top: 38px; }

    .uoc-category-section,
    .uoc-products-section,
    .uoc-collection-index,
    .uoc-proof,
    .uoc-newsletter { padding: 72px 0; }

    .uoc-category-grid {
        grid-template-columns: 1fr 1fr;
        gap: 8px;
    }

    .uoc-category-card:nth-child(1) { grid-column: span 1; }
    .uoc-category-card:nth-child(2) { grid-column: span 2; }
    .uoc-category-card:nth-child(3) { grid-column: span 1; }

    .uoc-products-head { grid-template-columns: 1fr; }
    .uoc-products-note { justify-self: start; }

    .uoc-collection-layout { gap: 36px; }
    .uoc-collection-row { grid-template-columns: 30px 1fr auto; }
    .uoc-collection-arrow { display: none; }
    .uoc-collection-row h3 { font-size: 23px; }

    .uoc-feature-story-image { min-height: 360px; }
    .uoc-feature-story-copy { min-height: 430px; }

    .uoc-proof-grid { grid-template-columns: 1fr; }
    .uoc-proof-card {
        border-right: 0;
        border-bottom: 1px solid var(--uoc-line);
    }
    .uoc-proof-card:last-child { border-bottom: 0; }

    .uoc-social-grid { grid-template-columns: repeat(2, 1fr); }

    .uoc-newsletter-grid { gap: 40px; }
}

@media (max-width: 480px) {
    .uoc-masthead-right .uoc-masthead-link:nth-child(1) { display: none; }
    .uoc-wordmark { font-size: 32px; }

    .uoc-hero { min-height: 600px; }
    .uoc-hero-dots { right: 16px; bottom: 22px; }

    .uoc-category-grid {
        grid-template-columns: 1fr;
    }
    .uoc-category-card:nth-child(1),
    .uoc-category-card:nth-child(2),
    .uoc-category-card:nth-child(3) {
        grid-column: span 1;
        aspect-ratio: 4 / 5;
    }

    .uoc-quick-add { display: none; }
    .uoc-product-rail { gap: 12px; }
    .uoc-product-card--rail { min-width: 72vw; }

    .uoc-social-grid { grid-template-columns: repeat(2, 1fr); }
}

@media (prefers-reduced-motion: reduce) {
    *,
    *::before,
    *::after {
        scroll-behavior: auto !important;
        animation-duration: .01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: .01ms !important;
    }
}
</style>



<main class="uoc-main">

    <!-- 01 / HERO -->
    <section class="uoc-hero" id="homeHero" aria-label="Featured collection">
        <div class="uoc-hero-slider">
            <?php foreach ($heroSlides as $i => $slide): ?>
                <div class="uoc-hero-slide <?= $i === 0 ? 'active' : '' ?>">
                    <img
                        src="<?= htmlspecialchars($slide['image']) ?>"
                        alt=""
                        <?= $i === 0 ? 'fetchpriority="high"' : 'loading="lazy"' ?>>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- 02 / TRUST -->
    <section class="uoc-trust" aria-label="Shopping benefits">
        <div class="uoc-trust-inner">
            <div class="uoc-trust-item"><span class="uoc-trust-num">01</span> Free shipping ₹999+</div>
            <div class="uoc-trust-item"><span class="uoc-trust-num">02</span> 7-day easy exchange</div>
            <div class="uoc-trust-item"><span class="uoc-trust-num">03</span> Genuine products</div>
            <div class="uoc-trust-item"><span class="uoc-trust-num">04</span> Secure checkout</div>
        </div>
    </section>

    <!-- 03 / BRAND INTRO -->
    <section class="uoc-intro">
        <div class="uoc-container uoc-intro-grid">
            <div>
                <span class="uoc-eyebrow">The Urban Outfit point of view</span>
                <h2>Indian craft. <em>City energy.</em> Clothes with a point of view.</h2>
            </div>
            <div class="uoc-intro-copy">
                <p>
                    We mix substantial everyday fabrics, relaxed street silhouettes
                    and Indian craft into clothing that feels current without chasing
                    the algorithm.
                </p>
                <a class="uoc-view-all" href="<?= BASE_URL ?>/shop.php">Enter the collection →</a>
            </div>
        </div>
    </section>

    <!-- 04 / DEPARTMENTS -->
    <section class="uoc-category-section">
        <div class="uoc-container">
            <div class="uoc-category-head">
                <div>
                    <span class="uoc-eyebrow">Shop by department</span>
                    <h2 class="uoc-section-title">Shop by department.</h2>
                </div>
                <a class="uoc-view-all" href="<?= BASE_URL ?>/shop.php">View all →</a>
            </div>

            <div class="uoc-category-grid">
                <a class="uoc-category-card" href="<?= BASE_URL ?>/shop.php?category=men">
                    <img src="<?= htmlspecialchars($menImage) ?>" alt="Men's collection" loading="lazy">
                    <div class="uoc-category-copy">
                        <small>01 / Street · Ethnic · Essentials</small>
                        <h3>Men</h3>
                    </div>
                </a>

                <a class="uoc-category-card" href="<?= BASE_URL ?>/shop.php?category=women">
                    <img src="<?= htmlspecialchars($womenImage) ?>" alt="Women's collection" loading="lazy">
                    <div class="uoc-category-copy">
                        <small>02 / Fusion · Modern · Elegant</small>
                        <h3>Women</h3>
                    </div>
                </a>

                <a class="uoc-category-card" href="<?= BASE_URL ?>/shop.php?category=kids">
                    <img src="<?= htmlspecialchars($kidsImage) ?>" alt="Kids' collection" loading="lazy">
                    <div class="uoc-category-copy">
                        <small>03 / Play · Comfort · Fun</small>
                        <h3>Kids</h3>
                    </div>
                </a>
            </div>
        </div>
    </section>

    <!-- 05 / MARQUEE -->
    <div class="uoc-marquee" aria-hidden="true">
        <div class="uoc-marquee-track">
            <?php
            $marqueeItems = [
                'Free shipping above ₹999',
                'Handcrafted in India',
                'Premium streetwear',
                'Chikankari heritage',
                'Organic linen fusion',
                '260+ GSM French Terry',
                'Free shipping above ₹999',
                'Handcrafted in India',
                'Premium streetwear',
                'Chikankari heritage',
                'Organic linen fusion',
                '260+ GSM French Terry',
                'Free shipping above ₹999',
                'Handcrafted in India',
                'Premium streetwear',
                'Chikankari heritage',
                'Organic linen fusion',
                '260+ GSM French Terry'
            ];
            foreach ($marqueeItems as $item):
            ?>
                <span class="uoc-marquee-item"><?= htmlspecialchars($item) ?></span>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- 06 / NEW ARRIVALS -->
    <?php if (!empty($newArrivals)): ?>
    <section class="uoc-products-section">
        <div class="uoc-container">
            <div class="uoc-products-head">
                <div>
                    <span class="uoc-eyebrow">Just dropped</span>
                    <h2 class="uoc-section-title">New arrivals.</h2>
                </div>
                <p class="uoc-products-note">
                    Fresh silhouettes and new fabric stories, selected for the current season.
                    No filler pieces.
                </p>
            </div>

            <div class="uoc-product-rail">
                <?php foreach ($newArrivals as $item) uocProductCard($item, 'rail'); ?>
            </div>

            <div style="margin-top:32px">
                <a class="uoc-view-all" href="<?= BASE_URL ?>/shop.php?sort=newest">See all new arrivals →</a>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- 07 / COLLECTION INDEX -->
    <?php if (!empty($subcats)): ?>
    <section class="uoc-collection-index">
        <div class="uoc-container uoc-collection-layout">
            <div class="uoc-collection-intro">
                <span class="uoc-eyebrow">The collection index</span>
                <h2>Shop by what you're actually looking for.</h2>
                <p>
                    From heavyweight tees to embroidered kurtas, browse the edit by
                    silhouette, craft and occasion.
                </p>
            </div>

            <div class="uoc-collection-list">
                <?php foreach (array_slice($subcats, 0, 8) as $idx => $sc):
                    $dept = match ((int)$sc['parent_id']) {
                        2 => 'men',
                        1 => 'women',
                        3 => 'kids',
                        default => 'shop'
                    };
                    $url = BASE_URL . '/shop.php?category=' . $dept . '&subcategory=' . urlencode($sc['slug']);
                ?>
                    <a class="uoc-collection-row" href="<?= htmlspecialchars($url) ?>">
                        <span class="uoc-collection-row-num"><?= str_pad((string)($idx + 1), 2, '0', STR_PAD_LEFT) ?></span>
                        <h3><?= htmlspecialchars($sc['name']) ?></h3>
                        <span class="uoc-collection-count"><?= (int)$sc['product_count'] ?> products</span>
                        <span class="uoc-collection-arrow">↗</span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- 08 / BESTSELLERS -->
    <?php if (!empty($bestsellers)): ?>
    <section class="uoc-products-section uoc-products-section--alt">
        <div class="uoc-container">
            <div class="uoc-products-head">
                <div>
                    <span class="uoc-eyebrow">Most wanted</span>
                    <h2 class="uoc-section-title">The pieces people keep buying.</h2>
                </div>
                <p class="uoc-products-note">
                    Customer favourites across streetwear, ethnic fusion and everyday essentials.
                </p>
            </div>

            <div class="uoc-product-rail">
                <?php foreach ($bestsellers as $item) uocProductCard($item, 'rail'); ?>
            </div>

            <div style="margin-top:32px">
                <a class="uoc-view-all" href="<?= BASE_URL ?>/shop.php?sort=bestseller">Shop the bestsellers →</a>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- 09 / HERITAGE STORY -->
    <section class="uoc-feature-story">
        <div class="uoc-feature-story-image">
            <img
                src="https://images.unsplash.com/photo-1596755094514-f87e34085b2c?w=1500&h=1000&auto=format&fit=crop&q=85"
                alt="Heritage collection"
                loading="lazy">
        </div>

        <div class="uoc-feature-story-copy">
            <span class="uoc-eyebrow">A closer look / Heritage</span>
            <h2>Tradition, re-cut for the street.</h2>
            <p>
                Chikankari textures, relaxed proportions and breathable fabrics —
                made for the person who wants heritage without looking costume-y.
            </p>
            <a class="uoc-btn-dark-story" href="<?= BASE_URL ?>/shop.php?category=ethnic-fusion">
                Explore heritage ↗
            </a>
        </div>
    </section>

    <!-- 10 / SOCIAL -->
    <section class="uoc-social">
        <div class="uoc-container">
            <div class="uoc-social-head">
                <div>
                    <span class="uoc-eyebrow">@urban_0utfit_mukerian</span>
                    <h2 class="uoc-section-title">Seen in the wild.</h2>
                </div>
                <a
                    class="uoc-view-all"
                    href="https://instagram.com/urban_0utfit_mukerian/"
                    target="_blank"
                    rel="noopener">
                    Follow Instagram →
                </a>
            </div>
        </div>

        <div class="uoc-social-grid">
            <?php
            $instaImages = [
                'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=600&h=600&auto=format&fit=crop&q=85',
                'https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?w=600&h=600&auto=format&fit=crop&q=85',
                'https://images.unsplash.com/photo-1509631179647-0177331693ae?w=600&h=600&auto=format&fit=crop&q=85',
                'https://images.unsplash.com/photo-1556905055-8f358a7a47b2?w=600&h=600&auto=format&fit=crop&q=85',
                'https://images.unsplash.com/photo-1517445312882-bc9910d016b7?w=600&h=600&auto=format&fit=crop&q=85',
                'https://images.unsplash.com/photo-1617137984095-74e4e5e3613f?w=600&h=600&auto=format&fit=crop&q=85',
            ];
            foreach ($instaImages as $img):
            ?>
                <a class="uoc-social-item" href="https://instagram.com/urban_0utfit_mukerian/" target="_blank" rel="noopener">
                    <img src="<?= htmlspecialchars($img) ?>" alt="Urban Outfit on Instagram" loading="lazy">
                </a>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- 11 / SOCIAL PROOF -->
    <section class="uoc-proof">
        <div class="uoc-container">
            <div class="uoc-proof-head">
                <div>
                    <span class="uoc-eyebrow">Real customer notes</span>
                    <h2 class="uoc-section-title">Good clothes. Better repeat orders.</h2>
                </div>
            </div>

            <div class="uoc-proof-grid">
                <article class="uoc-proof-card">
                    <div class="uoc-proof-stars">★★★★★</div>
                    <blockquote>“The acid-wash oversized tee feels properly premium. The fabric is substantial without being stiff.”</blockquote>
                    <div class="uoc-proof-author">Rohit S. · Verified buyer</div>
                </article>

                <article class="uoc-proof-card">
                    <div class="uoc-proof-stars">★★★★★</div>
                    <p>“Finally found ethnic fusion that doesn't feel overdone. The chikankari set is breathable and sharp.”</p>
                    <div class="uoc-proof-author">Ananya P. · Verified buyer</div>
                </article>

                <article class="uoc-proof-card">
                    <div class="uoc-proof-stars">★★★★★</div>
                    <p>“The linen co-ord became my airport and holiday uniform. Easy fit, easy compliments.”</p>
                    <div class="uoc-proof-author">Vikram K. · Verified buyer</div>
                </article>
            </div>
        </div>
    </section>



</main>

<script>
/* ─────────────────────────────────────────────────────────────
   CART / WISHLIST
   ───────────────────────────────────────────────────────────── */

let sizePickerOverlay = null;
let sizePickerModal = null;
let sizePickerData = { id: 0, sizes: [], mode: '' };

function ensureSizePicker() {
    if (sizePickerModal) return;

    sizePickerOverlay = document.createElement('div');
    sizePickerOverlay.className = 'uoc-sz-overlay';
    sizePickerOverlay.addEventListener('click', closeSizePicker);

    sizePickerModal = document.createElement('div');
    sizePickerModal.className = 'uoc-sz-modal';
    sizePickerModal.innerHTML = `
        <div class="uoc-sz-header">
            <span class="uoc-sz-title">Select size</span>
            <button type="button" class="uoc-sz-close" aria-label="Close" onclick="closeSizePicker()">&times;</button>
        </div>
        <div class="uoc-sz-grid" id="szGrid"></div>
        <div class="uoc-sz-note">Select a size to continue.</div>
    `;

    document.body.appendChild(sizePickerOverlay);
    document.body.appendChild(sizePickerModal);
}

function openSizePicker(btn, mode) {
    ensureSizePicker();

    const id = parseInt(btn.getAttribute('data-id'), 10);
    let sizes = [];

    try {
        sizes = JSON.parse(btn.getAttribute('data-sizes') || '[]');
    } catch (e) {
        sizes = [];
    }

    sizePickerData = { id, sizes, mode };

    const grid = document.getElementById('szGrid');
    grid.innerHTML = '';

    if (!sizes.length) {
        grid.innerHTML = '<div style="grid-column:1/-1;padding:20px;text-align:center;color:#726e65;font-size:12px">No sizes currently available.</div>';
    } else {
        sizes.forEach(function (size) {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'uoc-sz-btn';
            button.textContent = size;
            button.addEventListener('click', function () {
                selectSizeAndAct(size);
            });
            grid.appendChild(button);
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
    const { id, mode } = sizePickerData;
    closeSizePicker();

    if (mode === 'buynow') {
        doBuyNow(id, size);
    } else {
        doAddToCart(id, size);
    }
}

function doAddToCart(pid, size) {
    fetch('<?= BASE_URL ?>/api/cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body:
            'action=add' +
            '&product_id=' + encodeURIComponent(pid) +
            '&size=' + encodeURIComponent(size) +
            '&quantity=1'
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            document.querySelectorAll('.cart-count').forEach(el => {
                el.textContent = d.cart_count || 1;
            });
            showToast('Size ' + size + ' added to your bag!');
        } else {
            showToast(d.message || 'Could not add this item.', 'error');
        }
    })
    .catch(() => {
        window.location.href = '<?= BASE_URL ?>/customer/cart.php';
    });
}

function doBuyNow(pid, size) {
    fetch('<?= BASE_URL ?>/api/cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body:
            'action=add' +
            '&product_id=' + encodeURIComponent(pid) +
            '&size=' + encodeURIComponent(size) +
            '&quantity=1'
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            document.querySelectorAll('.cart-count').forEach(el => {
                el.textContent = d.cart_count || 1;
            });
            window.location.href = '<?= BASE_URL ?>/customer/checkout.php';
        } else {
            showToast(d.message || 'Could not add this item.', 'error');
        }
    })
    .catch(() => {
        window.location.href = '<?= BASE_URL ?>/customer/checkout.php';
    });
}

function quickAddToCart(pid, size) {
    doAddToCart(pid, size);
}

function toggleWishlist(pid, btn) {
    fetch('<?= BASE_URL ?>/api/wishlist.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=toggle&product_id=' + encodeURIComponent(pid)
    })
    .then(r => r.json())
    .then(d => {
        if (d.action === 'login_required') {
            window.location.href = '<?= BASE_URL ?>/customer/login.php';
            return;
        }

        if (d.success) {
            btn.classList.toggle('active');
            const svg = btn.querySelector('svg');
            if (svg) {
                svg.setAttribute(
                    'fill',
                    btn.classList.contains('active') ? 'currentColor' : 'none'
                );
            }

            if (typeof showToast === 'function') {
                showToast(d.message || 'Wishlist updated.');
            }

            const badge = document.getElementById('wishlistBadge');
            if (badge && d.wishlist_count !== undefined) {
                badge.textContent = d.wishlist_count;
                badge.style.display = d.wishlist_count > 0 ? '' : 'none';
            }
        }
    })
    .catch(() => {});
}

/* ─────────────────────────────────────────────────────────────
   HERO
   ───────────────────────────────────────────────────────────── */

const heroSlides = document.querySelectorAll('#homeHero .uoc-hero-slide');
let currentSlide = 0;
let heroInterval = null;

function goToSlide(index) {
    if (!heroSlides.length) return;
    heroSlides[currentSlide].classList.remove('active');
    currentSlide = index;
    heroSlides[currentSlide].classList.add('active');
}

function resetHeroInterval() {
    clearInterval(heroInterval);
    heroInterval = setInterval(function () {
        goToSlide((currentSlide + 1) % heroSlides.length);
    }, 4000);
}

resetHeroInterval();

document.addEventListener('visibilitychange', function () {
    if (document.hidden) {
        clearInterval(heroInterval);
    } else {
        resetHeroInterval();
    }
});

/* ─────────────────────────────────────────────────────────────
   NEWSLETTER
   ───────────────────────────────────────────────────────────── */

function subscribeNewsletter() {
    const input = document.getElementById('nlEmail');
    const email = input ? input.value.trim() : '';

    if (!email || !email.includes('@')) {
        showToast('Please enter a valid email address.', 'error');
        return;
    }

    /* Keep your existing newsletter API call here when available. */
    showToast('Welcome to the drop list.');
    input.value = '';
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>

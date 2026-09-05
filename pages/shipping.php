<?php
require_once dirname(__DIR__) . '/config/database.php';

$pageTitle       = 'Shipping Policy — Urban Outfit Collection | Delivery & Charges';
$pageDescription = 'Free express shipping on orders above ₹999. Standard delivery 3-7 days across India. Track your order anytime. Read our full shipping policy.';
$pageKeywords    = 'urban outfit shipping policy, free shipping india, delivery charges clothing, fast shipping fashion india';
$pageCanonical   = 'https://urbanoutfitshop.com/pages/shipping.php';
include dirname(__DIR__) . '/includes/header.php';

$freeThreshold = formatPrice(getSetting('shipping_free_min', 1999));
$standardFee = formatPrice(getSetting('shipping_standard', 149));
$expressFee = formatPrice(getSetting('shipping_express', 299));
?>

<style>
  body { background: var(--color-bg, #FAF9F6); }

  /* ══════ HERO ══════ */
  .sp-hero {
    margin-top: var(--header-height);
    background: linear-gradient(160deg, #1a1a1a 0%, #2a2a2a 50%, #1a1a1a 100%);
    overflow: hidden;
    position: relative;
  }
  .sp-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background: url("data:image/svg+xml,%3Csvg width='40' height='40' viewBox='0 0 40 40' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23D4AF37' fill-opacity='0.03'%3E%3Cpath d='M20 20.5V18H0v-2h20v-2l2 3-2 3z'/%3E%3C/g%3E%3C/svg%3E");
  }
  .sp-hero-inner {
    max-width: 1100px;
    margin: 0 auto;
    padding: 60px 24px 70px;
    display: grid;
    grid-template-columns: 1fr 380px;
    gap: 48px;
    align-items: center;
    position: relative;
    z-index: 1;
  }
  .sp-hero-eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.14em;
    color: var(--color-accent, #D4AF37);
    margin-bottom: 20px;
  }
  .sp-hero-eyebrow::before { content: ''; width: 28px; height: 2px; background: var(--color-accent, #D4AF37); }
  .sp-hero h1 {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: clamp(36px, 4.5vw, 52px);
    font-weight: 800;
    color: #fff;
    line-height: 1.1;
    margin: 0 0 18px;
  }
  .sp-hero h1 .gold { color: var(--color-accent, #D4AF37); }
  .sp-hero-desc { font-size: 15px; color: rgba(255,255,255,0.5); line-height: 1.7; max-width: 420px; margin-bottom: 28px; }
  .sp-hero-btns { display: flex; gap: 12px; flex-wrap: wrap; }
  .sp-btn-primary {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 13px 32px; background: var(--color-accent, #D4AF37); color: #fff;
    border: none; border-radius: 50px; font-size: 13px; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.06em; cursor: pointer;
    text-decoration: none; transition: all 0.3s; font-family: 'Plus Jakarta Sans', sans-serif;
  }
  .sp-btn-primary:hover { background: #fff; color: #1a1a1a; transform: translateY(-2px); }
  .sp-btn-outline {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 13px 28px; background: transparent; color: rgba(255,255,255,0.6);
    border: 1.5px solid rgba(255,255,255,0.15); border-radius: 50px;
    font-size: 13px; font-weight: 600; cursor: pointer; text-decoration: none;
    transition: all 0.3s; font-family: 'Plus Jakarta Sans', sans-serif;
  }
  .sp-btn-outline:hover { border-color: var(--color-accent, #D4AF37); color: var(--color-accent, #D4AF37); }

  /* Illustration */
  .sp-hero-illust { position: relative; height: 300px; display: flex; align-items: center; justify-content: center; }
  .sp-illust-glow { position: absolute; width: 260px; height: 260px; border-radius: 50%; background: radial-gradient(circle, rgba(212,175,55,0.12) 0%, transparent 70%); top: 50%; left: 50%; transform: translate(-50%,-50%); }
  .sp-illust-truck { position: absolute; z-index: 3; animation: spFloat 3s ease-in-out infinite; }
  @keyframes spFloat { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-8px)} }
  .sp-illust-box {
    position: absolute; width: 120px; height: 100px; background: #fff;
    border-radius: 10px; border: 1.5px solid #E5E7EB;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    bottom: 40px; left: 50%; transform: translateX(-65%) rotate(-3deg);
    z-index: 2; overflow: hidden;
  }
  .sp-box-head { background: linear-gradient(135deg, var(--color-accent, #D4AF37), #C9A227); padding: 10px 12px 8px; }
  .sp-box-label { font-size: 8px; font-weight: 800; color: #fff; text-transform: uppercase; letter-spacing: 0.08em; }
  .sp-box-body { padding: 12px; }
  .sp-box-line { height: 4px; background: #F0EDE6; border-radius: 2px; margin-bottom: 5px; }
  .sp-box-line:nth-child(2) { width: 80%; }
  .sp-box-line:nth-child(3) { width: 60%; }
  .sp-box-seal {
    position: absolute; bottom: 8px; right: 8px;
    width: 22px; height: 22px; border-radius: 50%;
    background: linear-gradient(135deg, var(--color-accent, #D4AF37), #C9A227);
    border: 2px solid #fff; box-shadow: 0 2px 6px rgba(212,175,55,0.3);
    display: flex; align-items: center; justify-content: center;
  }
  .sp-box-seal svg { width: 11px; height: 11px; stroke: #fff; }
  .sp-illust-speed {
    position: absolute; top: 15px; right: 50px; z-index: 5;
    filter: drop-shadow(0 6px 16px rgba(212,175,55,0.25));
    animation: spFloat 3.5s ease-in-out 0.3s infinite;
  }
  .sp-illust-dots { position: absolute; top: 5px; left: 20px; z-index: 1; display: grid; grid-template-columns: repeat(3,6px); gap: 8px; }
  .sp-illust-dots span { width: 6px; height: 6px; border-radius: 50%; background: var(--color-accent, #D4AF37); opacity: 0.25; }
  .sp-illust-dots span:nth-child(2) { opacity: 0.4; }
  .sp-illust-route {
    position: absolute; bottom: 30px; left: 10px; z-index: 1;
  }
  .sp-illust-pin {
    position: absolute; top: 5px; right: 10px; z-index: 1;
    animation: spFloatP 4s ease-in-out 0.6s infinite;
  }
  @keyframes spFloatP { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-6px)} }

  @media (max-width: 768px) {
    .sp-hero-inner { grid-template-columns: 1fr; }
    .sp-hero-illust { display: none; }
  }

  /* ══════ MAIN CONTENT ══════ */
  .sp-main {
    max-width: 1100px;
    margin: 0 auto;
    padding: 48px 24px 100px;
    display: grid;
    grid-template-columns: 240px 1fr;
    gap: 48px;
    align-items: start;
  }
  .sp-sidebar {
    position: sticky;
    top: calc(var(--header-height) + 24px);
    max-height: calc(100vh - var(--header-height) - 48px);
    overflow-y: auto;
  }
  .sp-toc {
    background: var(--color-surface, #fff);
    border: 1px solid var(--color-border, #E8E2D8);
    border-radius: 14px;
    padding: 20px;
  }
  .sp-toc-title {
    font-size: 11px; font-weight: 700; text-transform: uppercase;
    letter-spacing: 0.08em; color: var(--color-text-primary, #1a1a1a);
    margin-bottom: 14px; padding-bottom: 10px;
    border-bottom: 1px solid var(--color-border, #E8E2D8);
  }
  .sp-toc-list { list-style: none; margin: 0; padding: 0; }
  .sp-toc-list li { margin-bottom: 2px; }
  .sp-toc-list a {
    display: flex; align-items: baseline; gap: 8px;
    padding: 7px 10px; border-radius: 8px;
    font-size: 13px; color: var(--color-text-secondary, #5C5347);
    text-decoration: none; transition: all 0.2s; line-height: 1.35;
  }
  .sp-toc-list a:hover { background: rgba(212,175,55,0.06); color: var(--color-accent, #D4AF37); }
  .sp-toc-num { font-family: 'JetBrains Mono', monospace; font-size: 10px; font-weight: 600; color: var(--color-accent, #D4AF37); min-width: 18px; }

  .sp-intro {
    background: var(--color-surface, #fff);
    border: 1px solid var(--color-border, #E8E2D8);
    border-radius: 14px; padding: 24px 28px; margin-bottom: 32px;
    display: flex; align-items: flex-start; gap: 16px;
  }
  .sp-intro-icon {
    width: 40px; height: 40px; border-radius: 10px;
    background: rgba(212,175,55,0.1);
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
  }
  .sp-intro-icon svg { width: 20px; height: 20px; stroke: var(--color-accent, #D4AF37); }
  .sp-intro p { font-size: 14px; color: var(--color-text-secondary, #5C5347); line-height: 1.7; margin: 0; }
  .sp-intro strong { color: var(--color-text-primary, #1a1a1a); }

  .sp-section {
    background: var(--color-surface, #fff);
    border: 1px solid var(--color-border, #E8E2D8);
    border-radius: 14px; margin-bottom: 16px;
    scroll-margin-top: calc(var(--header-height) + 24px);
    overflow: hidden; transition: box-shadow 0.3s;
  }
  .sp-section:hover { box-shadow: 0 2px 12px rgba(0,0,0,0.04); }
  .sp-section-head {
    display: flex; align-items: center; gap: 14px;
    padding: 18px 24px; cursor: pointer; user-select: none; transition: background 0.2s;
  }
  .sp-section-head:hover { background: rgba(212,175,55,0.03); }
  .sp-section-num {
    font-family: 'JetBrains Mono', monospace; font-size: 12px; font-weight: 700;
    color: var(--color-accent, #D4AF37); background: rgba(212,175,55,0.08);
    width: 32px; height: 32px; border-radius: 8px;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
  }
  .sp-section-title {
    flex: 1; font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 15px; font-weight: 700; color: var(--color-text-primary, #1a1a1a);
  }
  .sp-section-toggle {
    width: 28px; height: 28px; border-radius: 6px;
    background: var(--color-surface-alt, #F5F0E8);
    display: flex; align-items: center; justify-content: center; transition: all 0.3s; flex-shrink: 0;
  }
  .sp-section-toggle svg { width: 14px; height: 14px; stroke: var(--color-text-tertiary, #9A8E7E); transition: transform 0.3s; }
  .sp-section.is-open .sp-section-toggle { background: var(--color-accent, #D4AF37); }
  .sp-section.is-open .sp-section-toggle svg { stroke: #fff; transform: rotate(180deg); }
  .sp-section-body { max-height: 0; overflow: hidden; transition: max-height 0.4s ease; }
  .sp-section.is-open .sp-section-body { max-height: 1200px; }
  .sp-section-inner {
    padding: 0 24px 24px; border-top: 1px solid var(--color-border, #E8E2D8); padding-top: 20px;
  }
  .sp-section-inner p { font-size: 14px; color: var(--color-text-secondary, #5C5347); line-height: 1.75; margin: 0 0 12px; }
  .sp-section-inner ul, .sp-section-inner ol { margin: 8px 0 14px; padding-left: 20px; font-size: 14px; color: var(--color-text-secondary, #5C5347); line-height: 1.75; }
  .sp-section-inner li { margin-bottom: 4px; }
  .sp-section-inner strong { color: var(--color-text-primary, #1a1a1a); }
  .sp-highlight {
    background: rgba(212,175,55,0.05); border-left: 3px solid var(--color-accent, #D4AF37);
    border-radius: 0 8px 8px 0; padding: 14px 18px; margin: 14px 0;
    font-size: 13px; color: var(--color-text-secondary, #5C5347); line-height: 1.65;
  }
  .sp-shipping-table {
    width: 100%; border-collapse: collapse; margin: 14px 0;
    font-size: 14px;
  }
  .sp-shipping-table th {
    text-align: left; padding: 10px 14px;
    font-size: 11px; font-weight: 700; text-transform: uppercase;
    letter-spacing: 0.06em; color: var(--color-text-primary, #1a1a1a);
    background: var(--color-surface-alt, #F5F0E8);
    border-bottom: 2px solid var(--color-border, #E8E2D8);
  }
  .sp-shipping-table td {
    padding: 10px 14px; color: var(--color-text-secondary, #5C5347);
    border-bottom: 1px solid var(--color-border, #E8E2D8);
  }
  .sp-shipping-table tr:last-child td { border-bottom: none; }
  .sp-shipping-table .sp-free { color: var(--color-accent, #D4AF37); font-weight: 600; }
  .sp-effective {
    text-align: center; padding: 28px;
    background: var(--color-surface, #fff); border: 1px solid var(--color-border, #E8E2D8);
    border-radius: 14px; margin-top: 32px;
  }
  .sp-effective p { font-size: 13px; color: var(--color-text-tertiary, #9A8E7E); margin: 0 0 6px; }
  .sp-effective .date { font-family: 'JetBrains Mono', monospace; font-size: 15px; font-weight: 600; color: var(--color-accent, #D4AF37); }
  .sp-back-top {
    position: fixed; bottom: 32px; right: 32px;
    width: 44px; height: 44px; border-radius: 50%;
    background: var(--color-accent, #D4AF37); color: #fff;
    border: none; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 4px 14px rgba(212,175,55,0.3);
    opacity: 0; pointer-events: none; transition: all 0.3s; z-index: 100;
  }
  .sp-back-top.show { opacity: 1; pointer-events: auto; }
  .sp-back-top:hover { transform: translateY(-3px); }
  .sp-back-top svg { width: 18px; height: 18px; }
  @media (max-width: 900px) {
    .sp-main { grid-template-columns: 1fr; }
    .sp-sidebar { position: static; max-height: none; }
    .sp-toc-list { columns: 2; column-gap: 8px; }
  }
</style>

<!-- HERO -->
<section class="sp-hero">
  <div class="sp-hero-inner">
    <div>
      <div class="sp-hero-eyebrow">Logistics</div>
      <h1>Shipping<br><span class="gold">Policy</span></h1>
      <p class="sp-hero-desc">Fast, reliable delivery across India and beyond. Every order is carefully packed and shipped with care.</p>
      <div class="sp-hero-btns">
        <a href="#shipping-body" class="sp-btn-primary">
          Read Policy
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
        </a>
        <a href="<?= BASE_URL ?>/pages/returns.php" class="sp-btn-outline">Returns & Refunds</a>
      </div>
    </div>

    <div class="sp-hero-illust">
      <div class="sp-illust-glow"></div>
      <div class="sp-illust-dots"><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span></div>

      <div class="sp-illust-box">
        <div class="sp-box-head"><div class="sp-box-label">ATELIER</div></div>
        <div class="sp-box-body">
          <div class="sp-box-line"></div><div class="sp-box-line"></div><div class="sp-box-line"></div><div class="sp-box-line"></div>
        </div>
        <div class="sp-box-seal"><svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></div>
      </div>

      <!-- Speed badge -->
      <div class="sp-illust-speed">
        <svg width="60" height="68" viewBox="0 0 64 72" fill="none">
          <path d="M32 4 L58 16 L58 36 C58 52 46 64 32 68 C18 64 6 52 6 36 L6 16 Z" fill="url(#spSG)"/>
          <path d="M32 9 L54 19 L54 36 C54 50 44 60 32 64 C20 60 10 50 10 36 L10 19 Z" fill="rgba(255,255,255,0.15)"/>
          <circle cx="32" cy="36" r="14" fill="none" stroke="#fff" stroke-width="2"/>
          <path d="M32 26 L32 36 L39 40" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
          <path d="M22 12 L18 6 M42 12 L46 6" stroke="rgba(255,255,255,0.4)" stroke-width="1.5" stroke-linecap="round"/>
          <defs><linearGradient id="spSG" x1="6" y1="4" x2="58" y2="68"><stop offset="0%" stop-color="#D4AF37"/><stop offset="100%" stop-color="#B8960B"/></linearGradient></defs>
        </svg>
      </div>

      <!-- Route line -->
      <div class="sp-illust-route">
        <svg width="160" height="40" viewBox="0 0 160 40" fill="none">
          <path d="M10 30 Q40 5 80 20 Q120 35 150 10" stroke="rgba(212,175,55,0.3)" stroke-width="2" stroke-dasharray="6 4" fill="none"/>
          <circle cx="10" cy="30" r="4" fill="#D4AF37"/>
          <circle cx="150" cy="10" r="4" fill="#D4AF37"/>
        </svg>
      </div>

      <!-- Location pin -->
      <div class="sp-illust-pin">
        <svg width="28" height="34" viewBox="0 0 28 34" fill="none">
          <path d="M14 0 C6.3 0 0 6.3 0 14 C0 24.5 14 34 14 34 C14 34 28 24.5 28 14 C28 6.3 21.7 0 14 0Z" fill="url(#spPin)"/>
          <circle cx="14" cy="13" r="5" fill="#fff"/>
          <defs><linearGradient id="spPin" x1="0" y1="0" x2="28" y2="34"><stop offset="0%" stop-color="#D4AF37"/><stop offset="100%" stop-color="#B8960B"/></linearGradient></defs>
        </svg>
      </div>
    </div>
  </div>
</section>

<!-- MAIN CONTENT -->
<div id="shipping-body" class="sp-main">
  <aside class="sp-sidebar">
    <nav class="sp-toc">
      <div class="sp-toc-title">Table of Contents</div>
      <ul class="sp-toc-list">
        <?php
        $toc = ['Delivery Timelines','Shipping Charges','Order Processing','Tracking Your Order','International Shipping','Delayed or Lost Packages','Contact Us'];
        foreach ($toc as $i => $t): ?>
          <li><a href="#ss<?= $i+1 ?>"><span class="sp-toc-num"><?= str_pad($i+1,2,'0',STR_PAD_LEFT) ?></span><?= $t ?></a></li>
        <?php endforeach; ?>
      </ul>
    </nav>
  </aside>

  <div class="sp-content">
    <div class="sp-intro">
      <div class="sp-intro-icon">
        <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
      </div>
      <p>At <strong>ATELIER</strong>, we partner with leading courier services to ensure your orders reach you safely and on time. Below you'll find everything you need to know about how we ship.</p>
    </div>

    <?php
    $sections = [];

    $sections[] = ['Delivery Timelines',
      'We offer multiple shipping options to suit your needs:',
      '<table class="sp-shipping-table"><thead><tr><th>Method</th><th>Timeline</th><th>Cost</th></tr></thead><tbody><tr><td><strong>Standard Shipping</strong></td><td>5-7 business days</td><td>' . $standardFee . '</td></tr><tr><td><strong>Express Shipping</strong></td><td>2-3 business days</td><td>' . $expressFee . '</td></tr><tr><td><strong>Free Shipping</strong></td><td>5-7 business days</td><td class="sp-free">Orders above ' . $freeThreshold . '</td></tr></tbody></table>',
      '<div class="sp-highlight">Timelines are estimates and begin from the date of dispatch, not the date of order. Orders placed before 2 PM IST are typically dispatched the same business day.</div>'
    ];

    $sections[] = ['Shipping Charges',
      'Shipping charges are calculated at checkout based on your delivery location and chosen shipping method.',
      '<ul><li><strong>Standard:</strong> ' . $standardFee . ' flat rate for all orders.</li><li><strong>Express:</strong> ' . $expressFee . ' flat rate for all orders.</li><li><strong>Free:</strong> Automatically applied when your order total exceeds ' . $freeThreshold . '.</li><li>COD (Cash on Delivery) orders may incur an additional ₹49 verification fee.</li></ul>'
    ];

    $sections[] = ['Order Processing',
      'Once you place an order, here is what happens next:',
      '<ol><li><strong>Order Confirmation:</strong> You receive an email and SMS confirming your order.</li><li><strong>Quality Check:</strong> Each item is inspected and carefully packed.</li><li><strong>Dispatch:</strong> Your order is handed to our courier partner within 2-3 business days.</li><li><strong>Shipping Notification:</strong> You receive tracking details via email and SMS.</li></ol>',
      '<div class="sp-highlight">During festive seasons and sales, processing may take an additional 1-2 business days. We appreciate your patience.</div>'
    ];

    $sections[] = ['Tracking Your Order',
      'Once your order is dispatched, you will receive a tracking number via:',
      '<ul><li><strong>Email:</strong> Sent to your registered email address.</li><li><strong>SMS:</strong> Sent to your registered mobile number.</li><li><strong>Account Dashboard:</strong> Log in to your ATELIER account and check "My Orders" for real-time tracking.</li></ul>',
      'You can track your package on our courier partner\'s website using the provided tracking number. Most tracking updates appear within 24 hours of dispatch.'
    ];

    $sections[] = ['International Shipping',
      'We currently ship within India. International shipping is coming soon.',
      '<ul><li>Delivery timelines for international orders will vary by destination.</li><li>Customs duties and import taxes, if applicable, are the responsibility of the customer.</li><li>We are actively working to expand our shipping reach to 30+ countries.</li></ul>'
    ];

    $sections[] = ['Delayed or Lost Packages',
      'If your order is delayed beyond the estimated timeline or appears lost in transit:',
      '<ol><li>Contact our support team with your order number.</li><li>We will investigate with our courier partner and provide an update within 48 hours.</li><li>If the package is confirmed lost, we will offer a full refund or reship the items at no extra cost.</li></ol>',
      '<div class="sp-highlight">Please ensure your shipping address and contact details are accurate at checkout. ATELIER is not responsible for deliveries to incorrect addresses provided by the customer.</div>'
    ];

    $sections[] = ['Contact Us',
      'For shipping-related queries, please reach out:',
      '<ul><li><strong>Email:</strong> shipping@atelier.com</li><li><strong>Phone:</strong> +91 98765 43210</li><li><strong>Hours:</strong> Monday - Saturday, 10 AM - 7 PM IST</li></ul>'
    ];

    foreach ($sections as $i => $sec):
      $num = str_pad($i+1, 2, '0', STR_PAD_LEFT);
      $isOpen = $i === 0 ? ' is-open' : '';
    ?>
      <div id="ss<?= $i+1 ?>" class="sp-section<?= $isOpen ?>">
        <div class="sp-section-head" onclick="toggleSP(this.parentElement)">
          <span class="sp-section-num"><?= $num ?></span>
          <span class="sp-section-title"><?= $sec[0] ?></span>
          <span class="sp-section-toggle">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
          </span>
        </div>
        <div class="sp-section-body">
          <div class="sp-section-inner">
            <?php for ($j = 1; $j < count($sec); $j++):
              $item = trim($sec[$j]);
              if (str_starts_with($item, '<')): ?>
                <?= $item ?>
              <?php else:
                $paragraphs = array_filter(array_map('trim', explode("\n", $item)));
                foreach ($paragraphs as $p): ?>
                  <p><?= $p ?></p>
                <?php endforeach;
              endif;
            endfor; ?>
          </div>
        </div>
      </div>
    <?php endforeach; ?>

    <div class="sp-effective">
      <p>This Shipping Policy is effective as of</p>
      <div class="date">January 1, 2026</div>
    </div>
  </div>
</div>

<button class="sp-back-top" id="spBackTop" onclick="window.scrollTo({top:0,behavior:'smooth'})">
  <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/></svg>
</button>

<script>
function toggleSP(el) { el.classList.toggle('is-open'); }
window.addEventListener('scroll', () => {
  document.getElementById('spBackTop').classList.toggle('show', window.scrollY > 400);
});
document.querySelectorAll('.sp-toc-list a').forEach(a => {
  a.addEventListener('click', e => {
    e.preventDefault();
    const el = document.getElementById(a.getAttribute('href').slice(1));
    if (el) {
      el.scrollIntoView({ behavior: 'smooth', block: 'start' });
      if (!el.classList.contains('is-open')) el.classList.add('is-open');
    }
  });
});
if (location.hash) {
  const el = document.querySelector(location.hash);
  if (el && el.classList.contains('sp-section')) {
    el.classList.add('is-open');
    setTimeout(() => el.scrollIntoView({ behavior: 'smooth', block: 'start' }), 100);
  }
}
</script>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>

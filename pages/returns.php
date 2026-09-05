<?php
require_once dirname(__DIR__) . '/config/database.php';

$pageTitle       = 'Returns & Exchange Policy — Urban Outfit Collection';
$pageDescription = '7-day easy exchange on all orders. Hassle-free returns, fast refunds. Read our complete returns & exchange policy at Urban Outfit Collection.';
$pageKeywords    = 'urban outfit returns policy, exchange clothing india, refund policy fashion, easy returns online shopping india';
$pageCanonical   = 'https://urbanoutfitshop.com/pages/returns.php';
include dirname(__DIR__) . '/includes/header.php';

$returnDays = (int)getSetting('return_days', 30);
$siteEmail = 'hello@atelier.com';
$sitePhone = '+91 98765 43210';
?>

<style>
  body { background: var(--color-bg, #FAF9F6); }

  /* ══════ HERO ══════ */
  .rf-hero {
    margin-top: var(--header-height);
    background: linear-gradient(160deg, #1a1a1a 0%, #2a2a2a 50%, #1a1a1a 100%);
    overflow: hidden;
    position: relative;
  }
  .rf-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background: url("data:image/svg+xml,%3Csvg width='40' height='40' viewBox='0 0 40 40' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23D4AF37' fill-opacity='0.03'%3E%3Cpath d='M20 20.5V18H0v-2h20v-2l2 3-2 3z'/%3E%3C/g%3E%3C/svg%3E");
  }
  .rf-hero-inner {
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
  .rf-hero-eyebrow {
    display: inline-flex; align-items: center; gap: 8px;
    font-size: 11px; font-weight: 700; text-transform: uppercase;
    letter-spacing: 0.14em; color: var(--color-accent, #D4AF37); margin-bottom: 20px;
  }
  .rf-hero-eyebrow::before { content: ''; width: 28px; height: 2px; background: var(--color-accent, #D4AF37); }
  .rf-hero h1 {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: clamp(36px, 4.5vw, 52px);
    font-weight: 800; color: #fff; line-height: 1.1; margin: 0 0 18px;
  }
  .rf-hero h1 .gold { color: var(--color-accent, #D4AF37); }
  .rf-hero-desc { font-size: 15px; color: rgba(255,255,255,0.5); line-height: 1.7; max-width: 420px; margin-bottom: 28px; }
  .rf-hero-btns { display: flex; gap: 12px; flex-wrap: wrap; }
  .rf-btn-primary {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 13px 32px; background: var(--color-accent, #D4AF37); color: #fff;
    border: none; border-radius: 50px; font-size: 13px; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.06em; cursor: pointer;
    text-decoration: none; transition: all 0.3s; font-family: 'Plus Jakarta Sans', sans-serif;
  }
  .rf-btn-primary:hover { background: #fff; color: #1a1a1a; transform: translateY(-2px); }
  .rf-btn-outline {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 13px 28px; background: transparent; color: rgba(255,255,255,0.6);
    border: 1.5px solid rgba(255,255,255,0.15); border-radius: 50px;
    font-size: 13px; font-weight: 600; cursor: pointer; text-decoration: none;
    transition: all 0.3s; font-family: 'Plus Jakarta Sans', sans-serif;
  }
  .rf-btn-outline:hover { border-color: var(--color-accent, #D4AF37); color: var(--color-accent, #D4AF37); }

  /* Illustration */
  .rf-hero-illust { position: relative; height: 300px; display: flex; align-items: center; justify-content: center; }
  .rf-illust-glow { position: absolute; width: 260px; height: 260px; border-radius: 50%; background: radial-gradient(circle, rgba(212,175,55,0.12) 0%, transparent 70%); top: 50%; left: 50%; transform: translate(-50%,-50%); }
  .rf-illust-box {
    position: absolute; width: 130px; height: 105px; background: #fff;
    border-radius: 10px; border: 1.5px solid #E5E7EB;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    bottom: 50px; left: 50%; transform: translateX(-55%) rotate(2deg);
    z-index: 2; overflow: hidden;
  }
  .rf-box-head { background: linear-gradient(135deg, var(--color-accent, #D4AF37), #C9A227); padding: 10px 12px 8px; }
  .rf-box-label { font-size: 8px; font-weight: 800; color: #fff; text-transform: uppercase; letter-spacing: 0.08em; }
  .rf-box-body { padding: 12px; }
  .rf-box-line { height: 4px; background: #F0EDE6; border-radius: 2px; margin-bottom: 5px; }
  .rf-box-line:nth-child(2) { width: 75%; }
  .rf-box-line:nth-child(3) { width: 60%; }
  .rf-box-arrow {
    position: absolute; top: 50%; right: -30px; transform: translateY(-50%); z-index: 3;
    animation: rfFloat 3s ease-in-out infinite;
  }
  @keyframes rfFloat { 0%,100%{transform:translateY(-50%) translateX(0)} 50%{transform:translateY(-50%) translateX(4px)} }
  .rf-illust-return {
    position: absolute; top: 20px; right: 40px; z-index: 5;
    filter: drop-shadow(0 6px 16px rgba(212,175,55,0.25));
    animation: rfFloatBadge 3.5s ease-in-out 0.3s infinite;
  }
  @keyframes rfFloatBadge { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-8px)} }
  .rf-illust-dots { position: absolute; top: 5px; left: 20px; z-index: 1; display: grid; grid-template-columns: repeat(3,6px); gap: 8px; }
  .rf-illust-dots span { width: 6px; height: 6px; border-radius: 50%; background: var(--color-accent, #D4AF37); opacity: 0.25; }
  .rf-illust-dots span:nth-child(2) { opacity: 0.4; }
  .rf-illust-tag {
    position: absolute; bottom: 40px; right: 20px; z-index: 1;
    animation: rfFloatP 4s ease-in-out 0.6s infinite;
  }
  .rf-illust-check {
    position: absolute; top: 10px; left: 30px; z-index: 1;
    animation: rfFloatP 3.5s ease-in-out 1.2s infinite;
  }
  @keyframes rfFloatP { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-6px)} }

  @media (max-width: 768px) {
    .rf-hero-inner { grid-template-columns: 1fr; }
    .rf-hero-illust { display: none; }
  }

  /* ══════ MAIN CONTENT ══════ */
  .rf-main {
    max-width: 1100px;
    margin: 0 auto;
    padding: 48px 24px 100px;
    display: grid;
    grid-template-columns: 240px 1fr;
    gap: 48px;
    align-items: start;
  }
  .rf-sidebar {
    position: sticky;
    top: calc(var(--header-height) + 24px);
    max-height: calc(100vh - var(--header-height) - 48px);
    overflow-y: auto;
  }
  .rf-toc {
    background: var(--color-surface, #fff);
    border: 1px solid var(--color-border, #E8E2D8);
    border-radius: 14px;
    padding: 20px;
  }
  .rf-toc-title {
    font-size: 11px; font-weight: 700; text-transform: uppercase;
    letter-spacing: 0.08em; color: var(--color-text-primary, #1a1a1a);
    margin-bottom: 14px; padding-bottom: 10px;
    border-bottom: 1px solid var(--color-border, #E8E2D8);
  }
  .rf-toc-list { list-style: none; margin: 0; padding: 0; }
  .rf-toc-list li { margin-bottom: 2px; }
  .rf-toc-list a {
    display: flex; align-items: baseline; gap: 8px;
    padding: 7px 10px; border-radius: 8px;
    font-size: 13px; color: var(--color-text-secondary, #5C5347);
    text-decoration: none; transition: all 0.2s; line-height: 1.35;
  }
  .rf-toc-list a:hover { background: rgba(212,175,55,0.06); color: var(--color-accent, #D4AF37); }
  .rf-toc-num { font-family: 'JetBrains Mono', monospace; font-size: 10px; font-weight: 600; color: var(--color-accent, #D4AF37); min-width: 18px; }

  .rf-intro {
    background: var(--color-surface, #fff);
    border: 1px solid var(--color-border, #E8E2D8);
    border-radius: 14px; padding: 24px 28px; margin-bottom: 32px;
    display: flex; align-items: flex-start; gap: 16px;
  }
  .rf-intro-icon {
    width: 40px; height: 40px; border-radius: 10px;
    background: rgba(212,175,55,0.1);
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
  }
  .rf-intro-icon svg { width: 20px; height: 20px; stroke: var(--color-accent, #D4AF37); }
  .rf-intro p { font-size: 14px; color: var(--color-text-secondary, #5C5347); line-height: 1.7; margin: 0; }
  .rf-intro strong { color: var(--color-text-primary, #1a1a1a); }

  .rf-section {
    background: var(--color-surface, #fff);
    border: 1px solid var(--color-border, #E8E2D8);
    border-radius: 14px; margin-bottom: 16px;
    scroll-margin-top: calc(var(--header-height) + 24px);
    overflow: hidden; transition: box-shadow 0.3s;
  }
  .rf-section:hover { box-shadow: 0 2px 12px rgba(0,0,0,0.04); }
  .rf-section-head {
    display: flex; align-items: center; gap: 14px;
    padding: 18px 24px; cursor: pointer; user-select: none; transition: background 0.2s;
  }
  .rf-section-head:hover { background: rgba(212,175,55,0.03); }
  .rf-section-num {
    font-family: 'JetBrains Mono', monospace; font-size: 12px; font-weight: 700;
    color: var(--color-accent, #D4AF37); background: rgba(212,175,55,0.08);
    width: 32px; height: 32px; border-radius: 8px;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
  }
  .rf-section-title {
    flex: 1; font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 15px; font-weight: 700; color: var(--color-text-primary, #1a1a1a);
  }
  .rf-section-toggle {
    width: 28px; height: 28px; border-radius: 6px;
    background: var(--color-surface-alt, #F5F0E8);
    display: flex; align-items: center; justify-content: center; transition: all 0.3s; flex-shrink: 0;
  }
  .rf-section-toggle svg { width: 14px; height: 14px; stroke: var(--color-text-tertiary, #9A8E7E); transition: transform 0.3s; }
  .rf-section.is-open .rf-section-toggle { background: var(--color-accent, #D4AF37); }
  .rf-section.is-open .rf-section-toggle svg { stroke: #fff; transform: rotate(180deg); }
  .rf-section-body { max-height: 0; overflow: hidden; transition: max-height 0.4s ease; }
  .rf-section.is-open .rf-section-body { max-height: 1200px; }
  .rf-section-inner {
    padding: 0 24px 24px; border-top: 1px solid var(--color-border, #E8E2D8); padding-top: 20px;
  }
  .rf-section-inner p { font-size: 14px; color: var(--color-text-secondary, #5C5347); line-height: 1.75; margin: 0 0 12px; }
  .rf-section-inner ul, .rf-section-inner ol { margin: 8px 0 14px; padding-left: 20px; font-size: 14px; color: var(--color-text-secondary, #5C5347); line-height: 1.75; }
  .rf-section-inner li { margin-bottom: 4px; }
  .rf-section-inner strong { color: var(--color-text-primary, #1a1a1a); }
  .rf-highlight {
    background: rgba(212,175,55,0.05); border-left: 3px solid var(--color-accent, #D4AF37);
    border-radius: 0 8px 8px 0; padding: 14px 18px; margin: 14px 0;
    font-size: 13px; color: var(--color-text-secondary, #5C5347); line-height: 1.65;
  }
  .rf-effective {
    text-align: center; padding: 28px;
    background: var(--color-surface, #fff); border: 1px solid var(--color-border, #E8E2D8);
    border-radius: 14px; margin-top: 32px;
  }
  .rf-effective p { font-size: 13px; color: var(--color-text-tertiary, #9A8E7E); margin: 0 0 6px; }
  .rf-effective .date { font-family: 'JetBrains Mono', monospace; font-size: 15px; font-weight: 600; color: var(--color-accent, #D4AF37); }
  .rf-back-top {
    position: fixed; bottom: 32px; right: 32px;
    width: 44px; height: 44px; border-radius: 50%;
    background: var(--color-accent, #D4AF37); color: #fff;
    border: none; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 4px 14px rgba(212,175,55,0.3);
    opacity: 0; pointer-events: none; transition: all 0.3s; z-index: 100;
  }
  .rf-back-top.show { opacity: 1; pointer-events: auto; }
  .rf-back-top:hover { transform: translateY(-3px); }
  .rf-back-top svg { width: 18px; height: 18px; }
  @media (max-width: 900px) {
    .rf-main { grid-template-columns: 1fr; }
    .rf-sidebar { position: static; max-height: none; }
    .rf-toc-list { columns: 2; column-gap: 8px; }
  }
</style>

<!-- HERO -->
<section class="rf-hero">
  <div class="rf-hero-inner">
    <div>
      <div class="rf-hero-eyebrow">Support</div>
      <h1>Returns &<br><span class="gold">Refunds</span></h1>
      <p class="rf-hero-desc">Not completely satisfied? We make returns hassle-free. Read our policy for a smooth return or refund experience.</p>
      <div class="rf-hero-btns">
        <a href="#returns-body" class="rf-btn-primary">
          Read Policy
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
        </a>
        <a href="<?= BASE_URL ?>/pages/shipping.php" class="rf-btn-outline">Shipping Policy</a>
      </div>
    </div>

    <div class="rf-hero-illust">
      <div class="rf-illust-glow"></div>
      <div class="rf-illust-dots"><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span></div>

      <div class="rf-illust-box">
        <div class="rf-box-head"><div class="rf-box-label">ATELIER</div></div>
        <div class="rf-box-body">
          <div class="rf-box-line"></div><div class="rf-box-line"></div><div class="rf-box-line"></div><div class="rf-box-line"></div>
        </div>
      </div>

      <!-- Return arrow -->
      <div class="rf-box-arrow">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
          <path d="M4 12 Q4 4 12 4 L20 4" stroke="#D4AF37" stroke-width="2.5" stroke-linecap="round" fill="none"/>
          <path d="M16 0 L20 4 L16 8" stroke="#D4AF37" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
        </svg>
      </div>

      <!-- Return badge -->
      <div class="rf-illust-return">
        <svg width="60" height="68" viewBox="0 0 64 72" fill="none">
          <path d="M32 4 L58 16 L58 36 C58 52 46 64 32 68 C18 64 6 52 6 36 L6 16 Z" fill="url(#rfSG)"/>
          <path d="M32 9 L54 19 L54 36 C54 50 44 60 32 64 C20 60 10 50 10 36 L10 19 Z" fill="rgba(255,255,255,0.15)"/>
          <path d="M20 32 L28 24 M20 32 L28 40 M44 32 L36 24 M44 32 L36 40" stroke="#fff" stroke-width="2.5" stroke-linecap="round" fill="none"/>
          <defs><linearGradient id="rfSG" x1="6" y1="4" x2="58" y2="68"><stop offset="0%" stop-color="#D4AF37"/><stop offset="100%" stop-color="#B8960B"/></linearGradient></defs>
        </svg>
      </div>

      <!-- Tag -->
      <div class="rf-illust-tag">
        <svg width="36" height="44" viewBox="0 0 36 44" fill="none">
          <rect x="2" y="2" width="32" height="40" rx="4" fill="#fff" stroke="#E5E7EB"/>
          <rect x="6" y="6" width="24" height="4" rx="2" fill="#F0EDE6"/>
          <rect x="6" y="14" width="18" height="3" rx="1.5" fill="#F0EDE6"/>
          <rect x="6" y="21" width="20" height="3" rx="1.5" fill="#F0EDE6"/>
          <circle cx="28" cy="34" r="6" fill="#D4AF37"/>
          <path d="M25 34 L27 36 L31 32" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
        </svg>
      </div>

      <!-- Check -->
      <div class="rf-illust-check">
        <svg width="28" height="28" viewBox="0 0 28 28" fill="none">
          <circle cx="14" cy="14" r="12" fill="rgba(212,175,55,0.15)" stroke="#D4AF37" stroke-width="1.5"/>
          <path d="M9 14 L13 18 L19 10" stroke="#D4AF37" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
        </svg>
      </div>
    </div>
  </div>
</section>

<!-- MAIN CONTENT -->
<div id="returns-body" class="rf-main">
  <aside class="rf-sidebar">
    <nav class="rf-toc">
      <div class="rf-toc-title">Table of Contents</div>
      <ul class="rf-toc-list">
        <?php
        $toc = ['Return Policy','Conditions for Return','How to Initiate a Return','Refund Process','Exchanges','Non-Returnable Items','Contact Us'];
        foreach ($toc as $i => $t): ?>
          <li><a href="#sr<?= $i+1 ?>"><span class="rf-toc-num"><?= str_pad($i+1,2,'0',STR_PAD_LEFT) ?></span><?= $t ?></a></li>
        <?php endforeach; ?>
      </ul>
    </nav>
  </aside>

  <div class="rf-content">
    <div class="rf-intro">
      <div class="rf-intro-icon">
        <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21l-7-5-7 5V5a2 2 0 012-2h10a2 2 0 012 2v16z"/></svg>
      </div>
      <p>At <strong>ATELIER</strong>, your satisfaction matters. If something isn't right, we offer a <strong><?= $returnDays ?>-day hassle-free return and exchange policy</strong> so you can shop with confidence.</p>
    </div>

    <?php
    $sections = [];

    $sections[] = ['Return Policy',
      'We offer a <strong>' . $returnDays . '-day hassle-free return/exchange</strong> policy from the date of delivery.',
      '<ul><li>Items must be in their original condition — unworn, unwashed, with all tags attached.</li><li>Returns must be initiated within ' . $returnDays . ' days of receiving the delivery.</li><li>Once we receive and inspect the returned item, your refund or exchange will be processed.</li></ul>',
      '<div class="rf-highlight">We recommend trying on items carefully without removing tags. Items with signs of wear, washing, or alteration cannot be accepted for return.</div>'
    ];

    $sections[] = ['Conditions for Return',
      'To qualify for a return, please ensure:',
      '<ul><li><strong>Original Condition:</strong> Items must be unworn, unwashed, and free of stains, odors, or damage.</li><li><strong>Tags Attached:</strong> All original tags must remain attached in their original placement.</li><li><strong>Original Packaging:</strong> Items should be returned in their original packaging where possible.</li><li><strong>Proof of Purchase:</strong> Order confirmation email or receipt must be provided.</li></ul>'
    ];

    $sections[] = ['How to Initiate a Return',
      'Starting a return is simple:',
      '<ol><li><strong>Contact Us:</strong> Email us at ' . $siteEmail . ' or call at ' . $sitePhone . ' with your order number.</li><li><strong>Return Authorization:</strong> We will provide a Return Authorization Number (RAN) and return instructions.</li><li><strong>Pack & Ship:</strong> Securely pack the item(s) in the original packaging and ship to the provided address.</li><li><strong>Track:</strong> Share the tracking number with us so we can monitor the return shipment.</li></ol>',
      '<div class="rf-highlight">Return shipping is free for the first return per order. Subsequent returns from the same order may incur standard shipping charges.</div>'
    ];

    $sections[] = ['Refund Process',
      'Once we receive your return, here is what to expect:',
      '<ol><li><strong>Inspection:</strong> Our team inspects the returned item(s) within 2-3 business days of receipt.</li><li><strong>Approval:</strong> You will receive an email notification once the return is approved.</li><li><strong>Refund:</strong> The refund is processed to your original payment method within 5-7 business days.</li></ol>',
      '<ul><li><strong>Online Payments:</strong> Refunded to the original payment method (credit/debit card, UPI, net banking).</li><li><strong>COD:</strong> Refunded via NEFT to your bank account (you will need to provide bank details).</li><li><strong>Store Credit:</strong> Available as an alternative — processed instantly upon return approval.</li></ul>'
    ];

    $sections[] = ['Exchanges',
      'Need a different size or color? We offer exchanges for:',
      '<ul><li>Same product in a different size.</li><li>Same product in a different color (subject to availability).</li><li>A different product of equal or lesser value (price difference will be refunded or charged).</li></ul>',
      '<div class="rf-highlight">Exchanges are subject to stock availability. If the desired item is out of stock, we will process a refund instead.</div>'
    ];

    $sections[] = ['Non-Returnable Items',
      'Certain items are not eligible for return due to hygiene and safety reasons:',
      '<ul><li><strong>Innerwear &amp; Swimwear:</strong> Non-returnable for hygiene reasons.</li><li><strong>Sale Items:</strong> Eligible for exchange only, not refunds (unless defective).</li><li><strong>Customized Products:</strong> Items made to order or personalized cannot be returned.</li><li><strong>Damaged by Customer:</strong> Items damaged due to misuse, improper care, or normal wear and tear.</li></ul>'
    ];

    $sections[] = ['Contact Us',
      'For return or refund queries, please reach out:',
      '<ul><li><strong>Email:</strong> returns@atelier.com</li><li><strong>Phone:</strong> ' . $sitePhone . '</li><li><strong>Hours:</strong> Monday - Saturday, 10 AM - 7 PM IST</li></ul>'
    ];

    foreach ($sections as $i => $sec):
      $num = str_pad($i+1, 2, '0', STR_PAD_LEFT);
      $isOpen = $i === 0 ? ' is-open' : '';
    ?>
      <div id="sr<?= $i+1 ?>" class="rf-section<?= $isOpen ?>">
        <div class="rf-section-head" onclick="toggleRF(this.parentElement)">
          <span class="rf-section-num"><?= $num ?></span>
          <span class="rf-section-title"><?= $sec[0] ?></span>
          <span class="rf-section-toggle">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
          </span>
        </div>
        <div class="rf-section-body">
          <div class="rf-section-inner">
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

    <div class="rf-effective">
      <p>This Returns &amp; Refunds Policy is effective as of</p>
      <div class="date">January 1, 2026</div>
    </div>
  </div>
</div>

<button class="rf-back-top" id="rfBackTop" onclick="window.scrollTo({top:0,behavior:'smooth'})">
  <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/></svg>
</button>

<script>
function toggleRF(el) { el.classList.toggle('is-open'); }
window.addEventListener('scroll', () => {
  document.getElementById('rfBackTop').classList.toggle('show', window.scrollY > 400);
});
document.querySelectorAll('.rf-toc-list a').forEach(a => {
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
  if (el && el.classList.contains('rf-section')) {
    el.classList.add('is-open');
    setTimeout(() => el.scrollIntoView({ behavior: 'smooth', block: 'start' }), 100);
  }
}
</script>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>

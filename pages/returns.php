<?php
require_once dirname(__DIR__) . '/config/database.php';

$pageTitle       = 'Returns & Exchange Policy — Urban Outfit Collection';
$pageDescription = '7-day easy exchange on all orders. Hassle-free returns, fast refunds. Read our complete returns & exchange policy at Urban Outfit Collection.';
$pageKeywords    = 'urban outfit returns policy, exchange clothing india, refund policy fashion, easy returns online shopping india';
$pageCanonical   = 'https://urbanoutfitshop.com/pages/returns.php';
include dirname(__DIR__) . '/includes/header.php';

$returnDays = 7;
$siteEmail = 'support@urbanoutfitshop.com';
$sitePhone = '+91 97807 04131';
?>

<style>
/* ====================== HERO ====================== */
.returns-hero {
  background: #000;
  padding: calc(var(--header-height, 70px) + 40px) 24px 48px;
  text-align: center;
  position: relative;
  overflow: hidden;
}
.returns-hero::before {
  content: '';
  position: absolute;
  top: -50%;
  left: -20%;
  width: 140%;
  height: 200%;
  background: radial-gradient(ellipse at center, rgba(255,255,255,0.03) 0%, transparent 60%);
  pointer-events: none;
}
.returns-hero h1 {
  font-family: 'Inter', var(--font-body);
  font-size: clamp(32px, 5vw, 48px);
  font-weight: 800;
  color: #fff;
  margin: 0 0 12px;
  letter-spacing: -0.03em;
  line-height: 1.1;
}
.returns-hero p {
  font-size: 15px;
  color: rgba(255,255,255,0.45);
  max-width: 420px;
  margin: 0 auto;
  line-height: 1.6;
}

/* ====================== STATS ====================== */
.returns-stats {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  max-width: 700px;
  margin: -28px auto 0;
  background: #fff;
  border-radius: 16px;
  box-shadow: 0 4px 24px rgba(0,0,0,0.08);
  position: relative;
  z-index: 2;
  border: 1px solid #f0f0f0;
}
.returns-stat {
  text-align: center;
  padding: 24px 12px;
  border-right: 1px solid #f0f0f0;
}
.returns-stat:last-child { border-right: none; }
.returns-stat-num {
  font-family: 'Inter', var(--font-body);
  font-size: 22px;
  font-weight: 800;
  color: #000;
  line-height: 1;
}
.returns-stat-label {
  font-size: 11px;
  color: #999;
  margin-top: 6px;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  font-weight: 500;
}

/* ====================== MAIN LAYOUT ====================== */
.returns-wrap {
  max-width: 900px;
  margin: 0 auto;
  padding: 48px 24px 80px;
  display: grid;
  grid-template-columns: 180px 1fr;
  gap: 32px;
  align-items: start;
}

/* ====================== SIDEBAR NAV ====================== */
.returns-nav {
  position: sticky;
  top: calc(var(--header-height, 70px) + 24px);
}
.returns-nav-label {
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  color: #bbb;
  margin-bottom: 12px;
  padding-left: 12px;
}
.returns-nav ul { list-style: none; padding: 0; margin: 0; }
.returns-nav li { margin-bottom: 2px; }
.returns-nav a {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 12px;
  font-size: 13px;
  color: #777;
  text-decoration: none;
  border-radius: 8px;
  transition: all 0.2s ease;
  font-weight: 500;
}
.returns-nav a:hover { background: #f5f5f5; color: #000; }
.returns-nav a.active { background: #000; color: #fff; }
.returns-nav-dot {
  width: 5px;
  height: 5px;
  border-radius: 50%;
  background: #ddd;
  flex-shrink: 0;
  transition: background 0.2s;
}
.returns-nav a.active .returns-nav-dot,
.returns-nav a:hover .returns-nav-dot { background: #fff; }

/* ====================== CONTENT ====================== */
.returns-content {
  min-width: 0;
}

/* Intro banner */
.returns-intro {
  background: #fafafa;
  border: 1px solid #f0f0f0;
  border-radius: 14px;
  padding: 20px 24px;
  margin-bottom: 20px;
  display: flex;
  align-items: center;
  gap: 16px;
}
.returns-intro-icon {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  background: #000;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.returns-intro-icon svg { width: 20px; height: 20px; stroke: #fff; }
.returns-intro p {
  font-size: 14px;
  color: #555;
  line-height: 1.7;
  margin: 0;
}
.returns-intro strong { color: #000; font-weight: 700; }

/* Section cards */
.returns-section {
  background: #fff;
  border: 1px solid #eee;
  border-radius: 14px;
  margin-bottom: 10px;
  overflow: hidden;
  scroll-margin-top: calc(var(--header-height, 70px) + 20px);
  transition: box-shadow 0.2s;
}
.returns-section:hover {
  box-shadow: 0 2px 16px rgba(0,0,0,0.04);
}
.returns-section-head {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 16px 20px;
  cursor: pointer;
  user-select: none;
  transition: background 0.15s;
}
.returns-section-head:hover { background: #fafafa; }
.returns-section-num {
  font-family: 'Inter', var(--font-body);
  font-size: 11px;
  font-weight: 800;
  color: #fff;
  background: #000;
  min-width: 30px;
  height: 30px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.returns-section-title {
  flex: 1;
  font-size: 14px;
  font-weight: 700;
  color: #000;
  letter-spacing: -0.01em;
}
.returns-section-arrow {
  width: 30px;
  height: 30px;
  border-radius: 8px;
  background: #f5f5f5;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s cubic-bezier(0.4,0,0.2,1);
  flex-shrink: 0;
}
.returns-section-arrow svg {
  width: 14px;
  height: 14px;
  stroke: #999;
  transition: transform 0.3s cubic-bezier(0.4,0,0.2,1);
}
.returns-section.is-open .returns-section-arrow {
  background: #000;
}
.returns-section.is-open .returns-section-arrow svg {
  stroke: #fff;
  transform: rotate(180deg);
}
.returns-section-body {
  max-height: 0;
  overflow: hidden;
  transition: max-height 0.45s cubic-bezier(0.4,0,0.2,1);
}
.returns-section.is-open .returns-section-body {
  max-height: 1500px;
}
.returns-section-inner {
  padding: 0 20px 20px;
  border-top: 1px solid #f0f0f0;
  padding-top: 16px;
}
.returns-section-inner p {
  font-size: 14px;
  color: #555;
  line-height: 1.8;
  margin: 0 0 10px;
}
.returns-section-inner ul,
.returns-section-inner ol {
  margin: 8px 0 12px;
  padding-left: 20px;
  font-size: 14px;
  color: #555;
  line-height: 1.8;
}
.returns-section-inner li { margin-bottom: 6px; }
.returns-section-inner strong { color: #000; font-weight: 600; }

/* Highlight box */
.returns-highlight {
  background: #f8f8f8;
  border-left: 3px solid #000;
  border-radius: 0 10px 10px 0;
  padding: 14px 18px;
  margin: 14px 0;
  font-size: 13px;
  color: #666;
  line-height: 1.7;
}

/* Effective date */
.returns-date-box {
  text-align: center;
  padding: 20px;
  background: #fafafa;
  border: 1px solid #f0f0f0;
  border-radius: 14px;
  margin-top: 20px;
}
.returns-date-box p {
  font-size: 11px;
  color: #bbb;
  margin: 0 0 4px;
  text-transform: uppercase;
  letter-spacing: 0.06em;
}
.returns-date-box .date {
  font-family: 'Inter', var(--font-body);
  font-size: 14px;
  font-weight: 700;
  color: #000;
}

/* Back to top */
.returns-btt {
  position: fixed;
  bottom: 24px;
  right: 24px;
  width: 44px;
  height: 44px;
  border-radius: 50%;
  background: #000;
  color: #fff;
  border: none;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 16px rgba(0,0,0,0.2);
  opacity: 0;
  pointer-events: none;
  transition: all 0.3s;
  z-index: 100;
}
.returns-btt.show { opacity: 1; pointer-events: auto; }
.returns-btt:hover { transform: translateY(-3px) scale(1.05); }
.returns-btt svg { width: 18px; height: 18px; }

/* ====================== RESPONSIVE ====================== */
@media (max-width: 768px) {
  .returns-stats {
    grid-template-columns: repeat(2, 1fr);
    margin: -20px 16px 0;
    border-radius: 14px;
  }
  .returns-stat:nth-child(2) { border-right: none; }
  .returns-stat:nth-child(1),
  .returns-stat:nth-child(2) { border-bottom: 1px solid #f0f0f0; }
  .returns-wrap {
    grid-template-columns: 1fr;
    padding: 32px 16px 60px;
  }
  .returns-nav { position: static; }
  .returns-nav ul {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
    margin-bottom: 8px;
  }
  .returns-nav a { padding: 7px 10px; font-size: 12px; }
}

@media (max-width: 480px) {
  .returns-hero { padding: calc(var(--header-height, 70px) + 32px) 16px 40px; }
  .returns-hero h1 { font-size: 28px; }
  .returns-stat-num { font-size: 18px; }
}
</style>

<div class="returns-hero">
  <h1>Returns & Exchanges</h1>
  <p>Not completely satisfied? We make returns simple. Read our policy for a smooth return or refund.</p>
</div>

<div class="returns-stats">
  <div class="returns-stat">
    <div class="returns-stat-num"><?= $returnDays ?> Days</div>
    <div class="returns-stat-label">Return Window</div>
  </div>
  <div class="returns-stat">
    <div class="returns-stat-num">Free</div>
    <div class="returns-stat-label">Return Shipping</div>
  </div>
  <div class="returns-stat">
    <div class="returns-stat-num">5–7</div>
    <div class="returns-stat-label">Days to Refund</div>
  </div>
  <div class="returns-stat">
    <div class="returns-stat-num">24h</div>
    <div class="returns-stat-label">Support</div>
  </div>
</div>

<div class="returns-wrap" id="returns-body">

  <aside class="returns-nav">
    <div class="returns-nav-label">On This Page</div>
    <ul>
      <?php
      $toc = ['Return Policy','Conditions','How to Return','Refunds','Exchanges','Non-Returnable','Contact'];
      foreach ($toc as $i => $t): ?>
        <li><a href="#sr<?= $i+1 ?>"><span class="returns-nav-dot"></span><?= $t ?></a></li>
      <?php endforeach; ?>
    </ul>
  </aside>

  <div class="returns-content">
    <div class="returns-intro">
      <div class="returns-intro-icon">
        <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21l-7-5-7 5V5a2 2 0 012-2h10a2 2 0 012 2v16z"/></svg>
      </div>
      <p>At <strong>Urban Outfit Collection</strong>, your satisfaction matters. We offer a <strong><?= $returnDays ?>-day hassle-free return & exchange policy</strong> so you can shop with confidence.</p>
    </div>

    <?php
    $sections = [];

    $sections[] = ['Return Policy',
      'We offer a <strong>' . $returnDays . '-day hassle-free return/exchange</strong> policy from the date of delivery.',
      '<ul><li>Items must be in original condition — unworn, unwashed, with all tags attached.</li><li>Returns must be initiated within ' . $returnDays . ' days of receiving delivery.</li><li>Once we receive and inspect the item, your refund or exchange will be processed.</li></ul>',
      '<div class="returns-highlight">Try items on carefully without removing tags. Items with signs of wear, washing, or alteration cannot be accepted.</div>'
    ];

    $sections[] = ['Conditions for Return',
      'To qualify for a return, please ensure:',
      '<ul><li><strong>Original Condition:</strong> Unworn, unwashed, free of stains, odors, or damage.</li><li><strong>Tags Attached:</strong> All original tags must remain in place.</li><li><strong>Original Packaging:</strong> Return in original packaging where possible.</li><li><strong>Proof of Purchase:</strong> Order confirmation email or receipt required.</li></ul>'
    ];

    $sections[] = ['How to Initiate a Return',
      'Starting a return is simple:',
      '<ol><li><strong>Contact Us:</strong> Email ' . $siteEmail . ' or call ' . $sitePhone . ' with your order number.</li><li><strong>Return Authorization:</strong> We will provide a Return Authorization Number (RAN) and instructions.</li><li><strong>Pack & Ship:</strong> Pack item(s) securely in original packaging and ship to the provided address.</li><li><strong>Track:</strong> Share the tracking number with us for monitoring.</li></ol>',
      '<div class="returns-highlight">First return per order ships free. Subsequent returns may incur standard shipping charges.</div>'
    ];

    $sections[] = ['Refund Process',
      'Once we receive your return:',
      '<ol><li><strong>Inspection:</strong> Our team inspects returned items within 2–3 business days.</li><li><strong>Approval:</strong> You will receive an email once the return is approved.</li><li><strong>Refund:</strong> Processed to your original payment method within 5–7 business days.</li></ol>',
      '<ul><li><strong>Online Payments:</strong> Refunded to original method (card, UPI, net banking).</li><li><strong>COD:</strong> Refunded via NEFT to your bank account.</li><li><strong>Store Credit:</strong> Available as an alternative — processed instantly.</li></ul>'
    ];

    $sections[] = ['Exchanges',
      'Need a different size or color? We exchange for:',
      '<ul><li>Same product in a different size.</li><li>Same product in a different color (subject to availability).</li><li>Different product of equal or lesser value (difference refunded or charged).</li></ul>',
      '<div class="returns-highlight">Exchanges depend on stock availability. If unavailable, we process a refund instead.</div>'
    ];

    $sections[] = ['Non-Returnable Items',
      'Some items cannot be returned due to hygiene and safety:',
      '<ul><li><strong>Innerwear &amp; Swimwear:</strong> Non-returnable for hygiene reasons.</li><li><strong>Sale Items:</strong> Exchange only, no refunds (unless defective).</li><li><strong>Customized Products:</strong> Made-to-order or personalized items cannot be returned.</li><li><strong>Customer Damage:</strong> Items damaged due to misuse or normal wear and tear.</li></ul>'
    ];

    $sections[] = ['Contact Us',
      'For return or refund queries:',
      '<ul><li><strong>Email:</strong> ' . $siteEmail . '</li><li><strong>Phone:</strong> ' . $sitePhone . '</li><li><strong>Hours:</strong> Mon–Sun, 9 AM – 9 PM</li></ul>'
    ];

    foreach ($sections as $i => $sec):
      $num = str_pad($i+1, 2, '0', STR_PAD_LEFT);
      $isOpen = $i === 0 ? ' is-open' : '';
    ?>
      <div id="sr<?= $i+1 ?>" class="returns-section<?= $isOpen ?>">
        <div class="returns-section-head" onclick="toggleReturns(this.parentElement)">
          <span class="returns-section-num"><?= $num ?></span>
          <span class="returns-section-title"><?= $sec[0] ?></span>
          <span class="returns-section-arrow">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
          </span>
        </div>
        <div class="returns-section-body">
          <div class="returns-section-inner">
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

    <div class="returns-date-box">
      <p>This policy is effective as of</p>
      <div class="date">January 1, 2026</div>
    </div>
  </div>

</div>

<button class="returns-btt" id="returnsBtt" onclick="window.scrollTo({top:0,behavior:'smooth'})">
  <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/></svg>
</button>

<script>
function toggleReturns(el) { el.classList.toggle('is-open'); }

window.addEventListener('scroll', () => {
  document.getElementById('returnsBtt').classList.toggle('show', window.scrollY > 400);
}, { passive: true });

const navLinks = document.querySelectorAll('.returns-nav a');
const sects = document.querySelectorAll('.returns-section');

window.addEventListener('scroll', () => {
  let cur = '';
  sects.forEach(s => { if (window.scrollY >= s.offsetTop - 140) cur = s.id; });
  navLinks.forEach(a => a.classList.toggle('active', a.getAttribute('href') === '#' + cur));
}, { passive: true });

navLinks.forEach(a => {
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
  if (el && el.classList.contains('returns-section')) {
    el.classList.add('is-open');
    setTimeout(() => el.scrollIntoView({ behavior: 'smooth', block: 'start' }), 100);
  }
}
</script>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>

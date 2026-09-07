<?php
require_once dirname(__DIR__) . '/config/database.php';
$pageTitle       = 'Terms & Conditions — Urban Outfit Collection';
$pageDescription = 'Read the terms and conditions for shopping at Urban Outfit Collection. Understand your rights, our policies and how we operate.';
$pageKeywords    = 'urban outfit terms and conditions, shopping terms india, clothing brand terms';
$pageCanonical   = 'https://urbanoutfitshop.com/pages/terms.php';
$pageRobots      = 'noindex, follow';
include dirname(__DIR__) . '/includes/header.php';
?>

<style>
/* ====================== HERO ====================== */
.tc-hero {
  background: #000;
  padding: calc(var(--header-height, 70px) + 40px) 24px 48px;
  text-align: center;
  position: relative;
  overflow: hidden;
}
.tc-hero::before {
  content: '';
  position: absolute;
  top: -50%;
  left: -20%;
  width: 140%;
  height: 200%;
  background: radial-gradient(ellipse at center, rgba(255,255,255,0.03) 0%, transparent 60%);
  pointer-events: none;
}
.tc-hero h1 {
  font-family: 'Inter', var(--font-body);
  font-size: clamp(32px, 5vw, 48px);
  font-weight: 800;
  color: #fff;
  margin: 0 0 12px;
  letter-spacing: -0.03em;
  line-height: 1.1;
}
.tc-hero p {
  font-size: 15px;
  color: rgba(255,255,255,0.45);
  max-width: 420px;
  margin: 0 auto;
  line-height: 1.6;
}

/* ====================== MAIN LAYOUT ====================== */
.tc-wrap {
  max-width: 900px;
  margin: 0 auto;
  padding: 48px 24px 80px;
  display: grid;
  grid-template-columns: 180px 1fr;
  gap: 32px;
  align-items: start;
}

/* ====================== SIDEBAR NAV ====================== */
.tc-nav {
  position: sticky;
  top: calc(var(--header-height, 70px) + 24px);
}
.tc-nav-label {
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  color: #bbb;
  margin-bottom: 12px;
  padding-left: 12px;
}
.tc-nav ul { list-style: none; padding: 0; margin: 0; }
.tc-nav li { margin-bottom: 2px; }
.tc-nav a {
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
.tc-nav a:hover { background: #f5f5f5; color: #000; }
.tc-nav a.active { background: #000; color: #fff; }
.tc-nav-dot {
  width: 5px;
  height: 5px;
  border-radius: 50%;
  background: #ddd;
  flex-shrink: 0;
  transition: background 0.2s;
}
.tc-nav a.active .tc-nav-dot,
.tc-nav a:hover .tc-nav-dot { background: #fff; }

/* ====================== CONTENT ====================== */
.tc-content { min-width: 0; }

/* Intro card */
.tc-intro {
  background: #fafafa;
  border: 1px solid #f0f0f0;
  border-radius: 14px;
  padding: 20px 24px;
  margin-bottom: 20px;
  display: flex;
  align-items: center;
  gap: 16px;
}
.tc-intro-icon {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  background: #000;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.tc-intro-icon svg { width: 20px; height: 20px; stroke: #fff; }
.tc-intro p {
  font-size: 14px;
  color: #555;
  line-height: 1.7;
  margin: 0;
}
.tc-intro strong { color: #000; font-weight: 700; }

/* Section cards */
.tc-section {
  background: #fff;
  border: 1px solid #eee;
  border-radius: 14px;
  margin-bottom: 10px;
  overflow: hidden;
  scroll-margin-top: calc(var(--header-height, 70px) + 20px);
  transition: box-shadow 0.2s;
}
.tc-section:hover { box-shadow: 0 2px 16px rgba(0,0,0,0.04); }
.tc-section-head {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 16px 20px;
  cursor: pointer;
  user-select: none;
  transition: background 0.15s;
}
.tc-section-head:hover { background: #fafafa; }
.tc-section-num {
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
.tc-section-title {
  flex: 1;
  font-size: 14px;
  font-weight: 700;
  color: #000;
  letter-spacing: -0.01em;
}
.tc-section-arrow {
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
.tc-section-arrow svg {
  width: 14px;
  height: 14px;
  stroke: #999;
  fill: none;
  transition: transform 0.3s cubic-bezier(0.4,0,0.2,1);
}
.tc-section.is-open .tc-section-arrow { background: #000; }
.tc-section.is-open .tc-section-arrow svg { stroke: #fff; transform: rotate(180deg); }
.tc-section-body {
  max-height: 0;
  overflow: hidden;
  transition: max-height 0.45s cubic-bezier(0.4,0,0.2,1);
}
.tc-section.is-open .tc-section-body { max-height: 1500px; }
.tc-section-inner {
  padding: 0 20px 20px;
  border-top: 1px solid #f0f0f0;
  padding-top: 16px;
}
.tc-section-inner p {
  font-size: 14px;
  color: #555;
  line-height: 1.8;
  margin: 0 0 10px;
}
.tc-section-inner ul,
.tc-section-inner ol {
  margin: 8px 0 12px;
  padding-left: 20px;
  font-size: 14px;
  color: #555;
  line-height: 1.8;
}
.tc-section-inner li { margin-bottom: 5px; }
.tc-section-inner strong { color: #000; font-weight: 600; }
.tc-section-inner a { color: #000; text-decoration: underline; }

/* Highlight box */
.tc-highlight {
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
.tc-date-box {
  text-align: center;
  padding: 20px;
  background: #fafafa;
  border: 1px solid #f0f0f0;
  border-radius: 14px;
  margin-top: 20px;
}
.tc-date-box p {
  font-size: 11px;
  color: #bbb;
  margin: 0 0 4px;
  text-transform: uppercase;
  letter-spacing: 0.06em;
}
.tc-date-box .date {
  font-family: 'Inter', var(--font-body);
  font-size: 14px;
  font-weight: 700;
  color: #000;
}

/* Back to top */
.tc-btt {
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
.tc-btt.show { opacity: 1; pointer-events: auto; }
.tc-btt:hover { transform: translateY(-3px) scale(1.05); }
.tc-btt svg { width: 18px; height: 18px; }

/* ====================== RESPONSIVE ====================== */
@media (max-width: 768px) {
  .tc-wrap {
    grid-template-columns: 1fr;
    padding: 32px 16px 60px;
  }
  .tc-nav { position: static; }
  .tc-nav ul { display: flex; flex-wrap: wrap; gap: 4px; margin-bottom: 8px; }
  .tc-nav a { padding: 7px 10px; font-size: 12px; }
}
@media (max-width: 480px) {
  .tc-hero { padding: calc(var(--header-height, 70px) + 32px) 16px 40px; }
  .tc-hero h1 { font-size: 28px; }
}
</style>

<!-- HERO -->
<section class="tc-hero">
  <h1>Terms & Conditions</h1>
  <p>Read these terms carefully before using our website. By accessing Urban Outfit Collection, you agree to these conditions.</p>
</section>

<!-- MAIN CONTENT -->
<div class="tc-wrap" id="terms-body">

  <aside class="tc-nav">
    <div class="tc-nav-label">On This Page</div>
    <ul>
      <?php
      $toc = ['Acceptance','Eligibility','Products','Orders','Shipping','Returns','Accounts','IP','Prohibited','Liability','Indemnity','Privacy','Changes','Law','Contact'];
      foreach ($toc as $i => $t): ?>
        <li><a href="#s<?= $i+1 ?>"><span class="tc-nav-dot"></span><?= $t ?></a></li>
      <?php endforeach; ?>
    </ul>
  </aside>

  <div class="tc-content">
    <div class="tc-intro">
      <div class="tc-intro-icon">
        <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      </div>
      <p>Welcome to <strong>Urban Outfit Collection</strong>. By using our website (<strong>www.urbanoutfitshop.com</strong>), you agree to these Terms and Conditions. If you do not agree, please do not use our website.</p>
    </div>

    <?php
    $privacyUrl = BASE_URL . '/pages/privacy.php';
    $sections = [];

    $sections[] = ['Acceptance of Terms',
      'By accessing or browsing this website, you agree to be bound by these Terms and our <a href="' . $privacyUrl . '">Privacy Policy</a>. We reserve the right to update these terms at any time without prior notice.',
      '<div class="tc-highlight">If you are using this website on behalf of an organization, you represent that you have the authority to bind that organization to these terms.</div>'
    ];

    $sections[] = ['Eligibility',
      'Our services are available only to individuals who can form legally binding contracts. By using this website, you represent that:',
      '<ul><li>You are at least 18 years of age.</li><li>You have the legal capacity to enter a binding agreement.</li><li>All information you provide is truthful and accurate.</li><li>You will maintain and update your information as needed.</li></ul>'
    ];

    $sections[] = ['Products & Pricing',
      'We strive to display products as accurately as possible. Colors and sizes may vary slightly from screen display. All prices are in <strong>Indian Rupees (₹)</strong> and inclusive of applicable taxes.',
      '<ul><li>Product images are for illustration and may differ slightly.</li><li>We reserve the right to limit quantities.</li><li>Pricing errors will be notified before order processing.</li><li>Offers cannot be combined unless stated.</li></ul>'
    ];

    $sections[] = ['Orders & Payments',
      'Placing an order is an offer to purchase. We reserve the right to accept or decline any order.',
      '<ul><li><strong>Online:</strong> Credit/debit cards, net banking, UPI, wallets.</li><li><strong>COD:</strong> Available for eligible orders in select pin codes.</li><li><strong>EMI:</strong> Available on select cards for orders above ₹3,000.</li></ul>',
      '<div class="tc-highlight">Order confirmation does not guarantee acceptance. We may cancel orders that appear fraudulent or contain pricing errors.</div>'
    ];

    $sections[] = ['Shipping & Delivery',
      'We aim to dispatch within <strong>2–3 business days</strong>. Timelines vary by location.',
      '<ul><li><strong>Standard:</strong> 5–7 business days, free shipping on all orders.</li><li><strong>Express:</strong> 2–3 business days, free shipping.</li><li><strong>COD:</strong> May take 1–2 extra days for verification.</li></ul>',
      'Tracking details shared via email and SMS once shipped.'
    ];

    $sections[] = ['Returns & Exchanges',
      'We offer a <strong>7-day hassle-free return/exchange</strong> from delivery.',
      '<ul><li>Items must be unworn, unwashed, with tags attached.</li><li>Sale items and innerwear: exchange only, no refunds.</li><li>First return per order ships free.</li><li>Refunds processed within 5–7 business days.</li><li>COD refunds via NEFT to bank account.</li></ul>',
      '<div class="tc-highlight">We may refuse returns that do not meet our conditions. Repeated returns of worn items may result in account suspension.</div>'
    ];

    $sections[] = ['User Accounts',
      'You are responsible for maintaining confidentiality of your credentials.',
      '<ul><li>Provide accurate registration information.</li><li>Notify us immediately of unauthorized access.</li><li>We may suspend accounts violating these terms.</li><li>You may delete your account by contacting support.</li></ul>'
    ];

    $sections[] = ['Intellectual Property',
      'All content on this website — text, graphics, logos, images, descriptions, software — is the property of <strong>Urban Outfit Collection</strong> and protected by copyright, trademark, and IP laws. You may not reproduce or exploit any content without written permission.'
    ];

    $sections[] = ['Prohibited Activities',
      'When using our website, you agree not to:',
      '<ul><li>Use the website for unlawful purposes.</li><li>Attempt unauthorized access to any part of the site.</li><li>Use bots or scrapers to collect data.</li><li>Damage or impair the website.</li><li>Post false or harmful content.</li><li>Resell content without authorization.</li></ul>'
    ];

    $sections[] = ['Limitation of Liability',
      'To the maximum extent permitted by law, <strong>Urban Outfit Collection</strong> shall not be liable for indirect, incidental, special, or punitive damages. Our total liability shall not exceed the amount paid for the specific product giving rise to the claim.'
    ];

    $sections[] = ['Indemnification',
      'You agree to indemnify and hold harmless <strong>Urban Outfit Collection</strong>, its directors, employees, and agents from any claims, damages, or expenses arising from your use of this website or violation of these terms.'
    ];

    $sections[] = ['Privacy Policy',
      'Your use of this website is also governed by our <a href="' . $privacyUrl . '">Privacy Policy</a>, which describes how we collect, use, and protect your personal information. By using this site, you consent to those practices.'
    ];

    $sections[] = ['Modifications',
      'We reserve the right to modify these terms at any time. Changes are effective immediately upon posting. Review this page periodically for updates.'
    ];

    $sections[] = ['Governing Law',
      'These Terms are governed by the laws of <strong>India</strong>. Disputes shall be subject to the exclusive jurisdiction of courts in <strong>Mukerian, Punjab</strong>.'
    ];

    $sections[] = ['Contact Us',
      'Questions about these Terms? Reach out:',
      '<ul><li><strong>Email:</strong> support@urbanoutfitshop.com</li><li><strong>Phone:</strong> +91 97807 04131</li><li><strong>Address:</strong> Opp. Sri Guru Nanak Girls Senior Secondary School, Mukerian, Punjab 144211</li></ul>'
    ];

    foreach ($sections as $i => $sec):
      $num = str_pad($i+1, 2, '0', STR_PAD_LEFT);
      $isOpen = $i === 0 ? ' is-open' : '';
    ?>
      <div id="s<?= $i+1 ?>" class="tc-section<?= $isOpen ?>">
        <div class="tc-section-head" onclick="toggleTC(this.parentElement)">
          <span class="tc-section-num"><?= $num ?></span>
          <span class="tc-section-title"><?= $sec[0] ?></span>
          <span class="tc-section-arrow">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
          </span>
        </div>
        <div class="tc-section-body">
          <div class="tc-section-inner">
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

    <div class="tc-date-box">
      <p>These Terms & Conditions are effective as of</p>
      <div class="date">January 1, 2026</div>
    </div>
  </div>

</div>

<button class="tc-btt" id="tcBtt" onclick="window.scrollTo({top:0,behavior:'smooth'})">
  <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/></svg>
</button>

<script>
function toggleTC(el) { el.classList.toggle('is-open'); }

window.addEventListener('scroll', () => {
  document.getElementById('tcBtt').classList.toggle('show', window.scrollY > 400);
}, { passive: true });

const navLinks = document.querySelectorAll('.tc-nav a');
const sects = document.querySelectorAll('.tc-section');

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
  if (el && el.classList.contains('tc-section')) {
    el.classList.add('is-open');
    setTimeout(() => el.scrollIntoView({ behavior: 'smooth', block: 'start' }), 100);
  }
}
</script>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>

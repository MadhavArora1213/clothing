<?php
require_once dirname(__DIR__) . '/config/database.php';

$pageTitle       = 'Privacy Policy — Urban Outfit Collection';
$pageDescription = 'Your privacy matters to us. Read how Urban Outfit Collection collects, uses and protects your personal information when you shop with us.';
$pageKeywords    = 'urban outfit privacy policy, data protection fashion india, customer privacy clothing brand';
$pageCanonical   = 'https://urbanoutfitshop.com/pages/privacy.php';
$pageRobots      = 'noindex, follow';
include dirname(__DIR__) . '/includes/header.php';

$siteEmail = 'support@urbanoutfitshop.com';
$sitePhone = '+91 97807 04131';
$siteAddress = 'Opp. Sri Guru Nanak Girls Senior Secondary School, Mukerian, Punjab 144211';
?>

<style>
/* ====================== HERO ====================== */
.pp-hero {
  background: #000;
  padding: calc(var(--header-height, 70px) + 40px) 24px 48px;
  text-align: center;
  position: relative;
  overflow: hidden;
}
.pp-hero::before {
  content: '';
  position: absolute;
  top: -50%;
  left: -20%;
  width: 140%;
  height: 200%;
  background: radial-gradient(ellipse at center, rgba(255,255,255,0.03) 0%, transparent 60%);
  pointer-events: none;
}
.pp-hero h1 {
  font-family: 'Inter', var(--font-body);
  font-size: clamp(32px, 5vw, 48px);
  font-weight: 800;
  color: #fff;
  margin: 0 0 12px;
  letter-spacing: -0.03em;
  line-height: 1.1;
}
.pp-hero p {
  font-size: 15px;
  color: rgba(255,255,255,0.45);
  max-width: 420px;
  margin: 0 auto;
  line-height: 1.6;
}

/* ====================== MAIN LAYOUT ====================== */
.pp-wrap {
  max-width: 900px;
  margin: 0 auto;
  padding: 48px 24px 80px;
  display: grid;
  grid-template-columns: 180px 1fr;
  gap: 32px;
  align-items: start;
}

/* ====================== SIDEBAR NAV ====================== */
.pp-nav {
  position: sticky;
  top: calc(var(--header-height, 70px) + 24px);
}
.pp-nav-label {
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  color: #bbb;
  margin-bottom: 12px;
  padding-left: 12px;
}
.pp-nav ul { list-style: none; padding: 0; margin: 0; }
.pp-nav li { margin-bottom: 2px; }
.pp-nav a {
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
.pp-nav a:hover { background: #f5f5f5; color: #000; }
.pp-nav a.active { background: #000; color: #fff; }
.pp-nav-dot {
  width: 5px;
  height: 5px;
  border-radius: 50%;
  background: #ddd;
  flex-shrink: 0;
  transition: background 0.2s;
}
.pp-nav a.active .pp-nav-dot,
.pp-nav a:hover .pp-nav-dot { background: #fff; }

/* ====================== CONTENT ====================== */
.pp-content { min-width: 0; }

/* Intro card */
.pp-intro {
  background: #fafafa;
  border: 1px solid #f0f0f0;
  border-radius: 14px;
  padding: 20px 24px;
  margin-bottom: 20px;
  display: flex;
  align-items: center;
  gap: 16px;
}
.pp-intro-icon {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  background: #000;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.pp-intro-icon svg { width: 20px; height: 20px; stroke: #fff; }
.pp-intro p {
  font-size: 14px;
  color: #555;
  line-height: 1.7;
  margin: 0;
}
.pp-intro strong { color: #000; font-weight: 700; }

/* Section cards */
.pp-section {
  background: #fff;
  border: 1px solid #eee;
  border-radius: 14px;
  margin-bottom: 10px;
  overflow: hidden;
  scroll-margin-top: calc(var(--header-height, 70px) + 20px);
  transition: box-shadow 0.2s;
}
.pp-section:hover { box-shadow: 0 2px 16px rgba(0,0,0,0.04); }
.pp-section-head {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 16px 20px;
  cursor: pointer;
  user-select: none;
  transition: background 0.15s;
}
.pp-section-head:hover { background: #fafafa; }
.pp-section-num {
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
.pp-section-title {
  flex: 1;
  font-size: 14px;
  font-weight: 700;
  color: #000;
  letter-spacing: -0.01em;
}
.pp-section-arrow {
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
.pp-section-arrow svg {
  width: 14px;
  height: 14px;
  stroke: #999;
  fill: none;
  transition: transform 0.3s cubic-bezier(0.4,0,0.2,1);
}
.pp-section.is-open .pp-section-arrow { background: #000; }
.pp-section.is-open .pp-section-arrow svg { stroke: #fff; transform: rotate(180deg); }
.pp-section-body {
  max-height: 0;
  overflow: hidden;
  transition: max-height 0.45s cubic-bezier(0.4,0,0.2,1);
}
.pp-section.is-open .pp-section-body { max-height: 1500px; }
.pp-section-inner {
  padding: 0 20px 20px;
  border-top: 1px solid #f0f0f0;
  padding-top: 16px;
}
.pp-section-inner p {
  font-size: 14px;
  color: #555;
  line-height: 1.8;
  margin: 0 0 10px;
}
.pp-section-inner ul,
.pp-section-inner ol {
  margin: 8px 0 12px;
  padding-left: 20px;
  font-size: 14px;
  color: #555;
  line-height: 1.8;
}
.pp-section-inner li { margin-bottom: 5px; }
.pp-section-inner strong { color: #000; font-weight: 600; }
.pp-section-inner a { color: #000; text-decoration: underline; }

/* Highlight box */
.pp-highlight {
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
.pp-date-box {
  text-align: center;
  padding: 20px;
  background: #fafafa;
  border: 1px solid #f0f0f0;
  border-radius: 14px;
  margin-top: 20px;
}
.pp-date-box p {
  font-size: 11px;
  color: #bbb;
  margin: 0 0 4px;
  text-transform: uppercase;
  letter-spacing: 0.06em;
}
.pp-date-box .date {
  font-family: 'Inter', var(--font-body);
  font-size: 14px;
  font-weight: 700;
  color: #000;
}

/* Back to top */
.pp-btt {
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
.pp-btt.show { opacity: 1; pointer-events: auto; }
.pp-btt:hover { transform: translateY(-3px) scale(1.05); }
.pp-btt svg { width: 18px; height: 18px; }

/* ====================== RESPONSIVE ====================== */
@media (max-width: 768px) {
  .pp-wrap {
    grid-template-columns: 1fr;
    padding: 32px 16px 60px;
  }
  .pp-nav { position: static; }
  .pp-nav ul { display: flex; flex-wrap: wrap; gap: 4px; margin-bottom: 8px; }
  .pp-nav a { padding: 7px 10px; font-size: 12px; }
}
@media (max-width: 480px) {
  .pp-hero { padding: calc(var(--header-height, 70px) + 32px) 16px 40px; }
  .pp-hero h1 { font-size: 28px; }
}
</style>

<!-- HERO -->
<section class="pp-hero">
  <h1>Privacy Policy</h1>
  <p>Your privacy matters to us. Learn how we collect, use, and protect your personal information.</p>
</section>

<!-- MAIN CONTENT -->
<div class="pp-wrap" id="privacy-body">

  <aside class="pp-nav">
    <div class="pp-nav-label">On This Page</div>
    <ul>
      <?php
      $toc = ['Information We Collect','How We Use Data','Cookies','Data Sharing','Security','Your Rights','Retention','Children','Changes','Contact'];
      foreach ($toc as $i => $t): ?>
        <li><a href="#ps<?= $i+1 ?>"><span class="pp-nav-dot"></span><?= $t ?></a></li>
      <?php endforeach; ?>
    </ul>
  </aside>

  <div class="pp-content">
    <div class="pp-intro">
      <div class="pp-intro-icon">
        <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
      </div>
      <p>At <strong>Urban Outfit Collection</strong>, we value your privacy. This policy explains what data we collect, why we collect it, and how we keep it safe.</p>
    </div>

    <?php
    $sections = [];

    $sections[] = ['Information We Collect',
      'We collect information you provide directly, such as when you create an account, make a purchase, or contact us.',
      '<ul><li><strong>Personal Details:</strong> Name, email, phone number, and shipping/billing address.</li><li><strong>Payment Info:</strong> Card details and UPI identifiers (processed securely via payment partners).</li><li><strong>Account Data:</strong> Username, encrypted password, order history, and wishlist.</li><li><strong>Communication:</strong> Messages, reviews, and feedback you send us.</li></ul>'
    ];

    $sections[] = ['How We Use Your Information',
      'We use your information to:',
      '<ul><li>Process and fulfill orders, including shipping and returns.</li><li>Communicate about purchases, account activity, and promotions.</li><li>Personalize your shopping experience and recommend products.</li><li>Improve our website, products, and services.</li><li>Detect and prevent fraud and unauthorized access.</li><li>Comply with legal obligations.</li></ul>'
    ];

    $sections[] = ['Cookies & Tracking',
      'We use cookies and similar technologies to enhance your experience:',
      '<ul><li><strong>Essential Cookies:</strong> Required for cart, login, and checkout.</li><li><strong>Analytics Cookies:</strong> Help us understand visitor behavior.</li><li><strong>Marketing Cookies:</strong> Deliver relevant ads and track campaigns.</li></ul>',
      '<div class="pp-highlight">You can manage cookie preferences via your browser. Disabling certain cookies may affect functionality.</div>'
    ];

    $sections[] = ['Data Sharing',
      'We do not sell your personal information. We may share data with:',
      '<ul><li><strong>Service Providers:</strong> Payment processors, shipping carriers, analytics partners.</li><li><strong>Legal Authorities:</strong> When required by law or to protect our rights.</li><li><strong>Business Transfers:</strong> In case of merger or acquisition (with prior notice).</li></ul>'
    ];

    $sections[] = ['Data Security',
      'We implement industry-standard security measures:',
      '<ul><li>SSL/TLS encryption for all data transmission.</li><li>PCI DSS compliant payment processing — we never store card details.</li><li>Regular security audits and vulnerability assessments.</li><li>Restricted access controls for authorized personnel only.</li></ul>',
      '<div class="pp-highlight">No method of transmission is 100% secure. Use strong passwords and keep credentials confidential.</div>'
    ];

    $sections[] = ['Your Rights',
      'You have the following rights regarding your data:',
      '<ul><li><strong>Access:</strong> Request a copy of all data we hold about you.</li><li><strong>Correction:</strong> Request correction of inaccurate data.</li><li><strong>Deletion:</strong> Request deletion (subject to legal requirements).</li><li><strong>Opt-Out:</strong> Unsubscribe from marketing emails anytime.</li><li><strong>Data Portability:</strong> Request your data in a machine-readable format.</li></ul>'
    ];

    $sections[] = ['Data Retention',
      'We retain data only as long as necessary:',
      '<ul><li><strong>Account Data:</strong> While active, plus 2 years after closure.</li><li><strong>Order Data:</strong> 7 years for tax and legal compliance.</li><li><strong>Marketing Data:</strong> Until you unsubscribe or request deletion.</li><li><strong>Analytics Data:</strong> Anonymized after 26 months.</li></ul>'
    ];

    $sections[] = ['Children\'s Privacy',
      'Our website is not intended for children under 18. We do not knowingly collect personal information from children. If you believe we have collected data from a child, contact us immediately and we will delete it.'
    ];

    $sections[] = ['Changes to This Policy',
      'We may update this policy from time to time. Changes will be posted with an updated "Effective Date." Material changes will be communicated via email to registered users.'
    ];

    $sections[] = ['Contact Us',
      'For questions about this policy or to exercise your rights:',
      '<ul><li><strong>Email:</strong> ' . $siteEmail . '</li><li><strong>Phone:</strong> ' . $sitePhone . '</li><li><strong>Address:</strong> ' . $siteAddress . '</li></ul>'
    ];

    foreach ($sections as $i => $sec):
      $num = str_pad($i+1, 2, '0', STR_PAD_LEFT);
      $isOpen = $i === 0 ? ' is-open' : '';
    ?>
      <div id="ps<?= $i+1 ?>" class="pp-section<?= $isOpen ?>">
        <div class="pp-section-head" onclick="togglePP(this.parentElement)">
          <span class="pp-section-num"><?= $num ?></span>
          <span class="pp-section-title"><?= $sec[0] ?></span>
          <span class="pp-section-arrow">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
          </span>
        </div>
        <div class="pp-section-body">
          <div class="pp-section-inner">
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

    <div class="pp-date-box">
      <p>This Privacy Policy is effective as of</p>
      <div class="date">January 1, 2026</div>
    </div>
  </div>

</div>

<button class="pp-btt" id="ppBtt" onclick="window.scrollTo({top:0,behavior:'smooth'})">
  <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/></svg>
</button>

<script>
function togglePP(el) { el.classList.toggle('is-open'); }

window.addEventListener('scroll', () => {
  document.getElementById('ppBtt').classList.toggle('show', window.scrollY > 400);
}, { passive: true });

const navLinks = document.querySelectorAll('.pp-nav a');
const sects = document.querySelectorAll('.pp-section');

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
  if (el && el.classList.contains('pp-section')) {
    el.classList.add('is-open');
    setTimeout(() => el.scrollIntoView({ behavior: 'smooth', block: 'start' }), 100);
  }
}
</script>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>

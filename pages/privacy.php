<?php
require_once dirname(__DIR__) . '/config/database.php';

$pageTitle = 'Privacy Policy — ATELIER';
$pageDescription = 'Learn how we collect, use, and protect your personal information.';
include dirname(__DIR__) . '/includes/header.php';

$siteEmail = 'hello@atelier.com';
$sitePhone = '+91 98765 43210';
$siteAddress = 'ATELIER Fashion Pvt. Ltd., 42 Design Studio, Hauz Khas Village, New Delhi - 110016';
?>

<style>
  body { background: var(--color-bg, #FAF9F6); }

  /* ══════ HERO ══════ */
  .pp-hero {
    margin-top: var(--header-height);
    background: linear-gradient(160deg, #1a1a1a 0%, #2a2a2a 50%, #1a1a1a 100%);
    overflow: hidden;
    position: relative;
  }
  .pp-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background: url("data:image/svg+xml,%3Csvg width='40' height='40' viewBox='0 0 40 40' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23D4AF37' fill-opacity='0.03'%3E%3Cpath d='M20 20.5V18H0v-2h20v-2l2 3-2 3z'/%3E%3C/g%3E%3C/svg%3E");
  }
  .pp-hero-inner {
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
  .pp-hero-eyebrow {
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
  .pp-hero-eyebrow::before {
    content: '';
    width: 28px;
    height: 2px;
    background: var(--color-accent, #D4AF37);
  }
  .pp-hero h1 {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: clamp(36px, 4.5vw, 52px);
    font-weight: 800;
    color: #fff;
    line-height: 1.1;
    margin: 0 0 18px;
  }
  .pp-hero h1 .gold { color: var(--color-accent, #D4AF37); }
  .pp-hero-desc {
    font-size: 15px;
    color: rgba(255,255,255,0.5);
    line-height: 1.7;
    max-width: 420px;
    margin-bottom: 28px;
  }
  .pp-hero-btns { display: flex; gap: 12px; flex-wrap: wrap; }
  .pp-btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 13px 32px;
    background: var(--color-accent, #D4AF37);
    color: #fff;
    border: none;
    border-radius: 50px;
    font-size: 13px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.3s;
    font-family: 'Plus Jakarta Sans', sans-serif;
  }
  .pp-btn-primary:hover { background: #fff; color: #1a1a1a; transform: translateY(-2px); }
  .pp-btn-outline {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 13px 28px;
    background: transparent;
    color: rgba(255,255,255,0.6);
    border: 1.5px solid rgba(255,255,255,0.15);
    border-radius: 50px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.3s;
    font-family: 'Plus Jakarta Sans', sans-serif;
  }
  .pp-btn-outline:hover { border-color: var(--color-accent, #D4AF37); color: var(--color-accent, #D4AF37); }

  /* Illustration */
  .pp-hero-illust { position: relative; height: 300px; display: flex; align-items: center; justify-content: center; }
  .pp-illust-glow {
    position: absolute; width: 260px; height: 260px; border-radius: 50%;
    background: radial-gradient(circle, rgba(212,175,55,0.12) 0%, transparent 70%);
    top: 50%; left: 50%; transform: translate(-50%,-50%);
  }
  .pp-illust-lock {
    position: absolute; z-index: 3;
    animation: ppFloat 3s ease-in-out infinite;
  }
  @keyframes ppFloat { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-8px)} }
  .pp-illust-doc {
    position: absolute; width: 170px; height: 220px; background: #fff;
    border-radius: 12px; border: 1.5px solid #E5E7EB;
    box-shadow: 0 14px 44px rgba(0,0,0,0.12);
    top: 30px; left: 50%; transform: translateX(-55%) rotate(-3deg);
    z-index: 2; overflow: hidden;
  }
  .pp-doc-head {
    background: linear-gradient(135deg, var(--color-accent, #D4AF37), #C9A227);
    padding: 14px 16px 12px;
  }
  .pp-doc-head-title { font-size: 11px; font-weight: 800; color: #fff; text-transform: uppercase; letter-spacing: 0.06em; }
  .pp-doc-head-sub { font-size: 9px; color: rgba(255,255,255,0.65); margin-top: 2px; }
  .pp-doc-body { padding: 16px; }
  .pp-doc-line { height: 5px; background: #F0EDE6; border-radius: 3px; margin-bottom: 7px; }
  .pp-doc-line:nth-child(2) { width: 80%; }
  .pp-doc-line:nth-child(3) { width: 65%; }
  .pp-doc-line:nth-child(4) { width: 90%; }
  .pp-doc-line:nth-child(5) { width: 50%; }
  .pp-doc-seal {
    position: absolute; bottom: 12px; right: 12px;
    width: 28px; height: 28px; border-radius: 50%;
    background: linear-gradient(135deg, var(--color-accent, #D4AF37), #C9A227);
    border: 2px solid #fff; box-shadow: 0 2px 8px rgba(212,175,55,0.3);
    display: flex; align-items: center; justify-content: center;
  }
  .pp-doc-seal svg { width: 14px; height: 14px; stroke: #fff; }
  .pp-illust-shield {
    position: absolute; top: 15px; right: 40px; z-index: 5;
    filter: drop-shadow(0 6px 16px rgba(212,175,55,0.25));
    animation: ppFloat 3.5s ease-in-out 0.3s infinite;
  }
  .pp-illust-dots {
    position: absolute; top: 5px; left: 20px; z-index: 1;
    display: grid; grid-template-columns: repeat(3,6px); gap: 8px;
  }
  .pp-illust-dots span { width: 6px; height: 6px; border-radius: 50%; background: var(--color-accent, #D4AF37); opacity: 0.25; }
  .pp-illust-dots span:nth-child(2) { opacity: 0.4; }
  .pp-illust-paper {
    position: absolute; background: #fff; border: 1px solid #E5E7EB;
    border-radius: 5px; box-shadow: 0 3px 10px rgba(0,0,0,0.05); z-index: 1;
  }
  .pp-p1 { width: 32px; height: 40px; top: 0; left: 35px; transform: rotate(-12deg); animation: ppFloatP 4s ease-in-out infinite; }
  .pp-p2 { width: 28px; height: 34px; top: 60px; right: 0; transform: rotate(14deg); animation: ppFloatP 3.5s ease-in-out 0.6s infinite; }
  .pp-p3 { width: 30px; height: 36px; bottom: 50px; left: 5px; transform: rotate(10deg); animation: ppFloatP 4.5s ease-in-out 1.2s infinite; }
  @keyframes ppFloatP { 0%,100%{transform:translateY(0) rotate(var(--r,0deg))} 50%{transform:translateY(-7px) rotate(var(--r,0deg))} }

  @media (max-width: 768px) {
    .pp-hero-inner { grid-template-columns: 1fr; }
    .pp-hero-illust { display: none; }
  }

  /* ══════ MAIN CONTENT ══════ */
  .pp-main {
    max-width: 1100px;
    margin: 0 auto;
    padding: 48px 24px 100px;
    display: grid;
    grid-template-columns: 240px 1fr;
    gap: 48px;
    align-items: start;
  }
  .pp-sidebar {
    position: sticky;
    top: calc(var(--header-height) + 24px);
    max-height: calc(100vh - var(--header-height) - 48px);
    overflow-y: auto;
  }
  .pp-toc {
    background: var(--color-surface, #fff);
    border: 1px solid var(--color-border, #E8E2D8);
    border-radius: 14px;
    padding: 20px;
  }
  .pp-toc-title {
    font-size: 11px; font-weight: 700; text-transform: uppercase;
    letter-spacing: 0.08em; color: var(--color-text-primary, #1a1a1a);
    margin-bottom: 14px; padding-bottom: 10px;
    border-bottom: 1px solid var(--color-border, #E8E2D8);
  }
  .pp-toc-list { list-style: none; margin: 0; padding: 0; }
  .pp-toc-list li { margin-bottom: 2px; }
  .pp-toc-list a {
    display: flex; align-items: baseline; gap: 8px;
    padding: 7px 10px; border-radius: 8px;
    font-size: 13px; color: var(--color-text-secondary, #5C5347);
    text-decoration: none; transition: all 0.2s; line-height: 1.35;
  }
  .pp-toc-list a:hover { background: rgba(212,175,55,0.06); color: var(--color-accent, #D4AF37); }
  .pp-toc-num {
    font-family: 'JetBrains Mono', monospace; font-size: 10px; font-weight: 600;
    color: var(--color-accent, #D4AF37); min-width: 18px;
  }

  .pp-intro {
    background: var(--color-surface, #fff);
    border: 1px solid var(--color-border, #E8E2D8);
    border-radius: 14px; padding: 24px 28px; margin-bottom: 32px;
    display: flex; align-items: flex-start; gap: 16px;
  }
  .pp-intro-icon {
    width: 40px; height: 40px; border-radius: 10px;
    background: rgba(212,175,55,0.1);
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
  }
  .pp-intro-icon svg { width: 20px; height: 20px; stroke: var(--color-accent, #D4AF37); }
  .pp-intro p { font-size: 14px; color: var(--color-text-secondary, #5C5347); line-height: 1.7; margin: 0; }
  .pp-intro strong { color: var(--color-text-primary, #1a1a1a); }

  .pp-section {
    background: var(--color-surface, #fff);
    border: 1px solid var(--color-border, #E8E2D8);
    border-radius: 14px; margin-bottom: 16px;
    scroll-margin-top: calc(var(--header-height) + 24px);
    overflow: hidden; transition: box-shadow 0.3s;
  }
  .pp-section:hover { box-shadow: 0 2px 12px rgba(0,0,0,0.04); }
  .pp-section-head {
    display: flex; align-items: center; gap: 14px;
    padding: 18px 24px; cursor: pointer; user-select: none; transition: background 0.2s;
  }
  .pp-section-head:hover { background: rgba(212,175,55,0.03); }
  .pp-section-num {
    font-family: 'JetBrains Mono', monospace; font-size: 12px; font-weight: 700;
    color: var(--color-accent, #D4AF37); background: rgba(212,175,55,0.08);
    width: 32px; height: 32px; border-radius: 8px;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
  }
  .pp-section-title {
    flex: 1; font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 15px; font-weight: 700; color: var(--color-text-primary, #1a1a1a);
  }
  .pp-section-toggle {
    width: 28px; height: 28px; border-radius: 6px;
    background: var(--color-surface-alt, #F5F0E8);
    display: flex; align-items: center; justify-content: center; transition: all 0.3s; flex-shrink: 0;
  }
  .pp-section-toggle svg { width: 14px; height: 14px; stroke: var(--color-text-tertiary, #9A8E7E); transition: transform 0.3s; }
  .pp-section.is-open .pp-section-toggle { background: var(--color-accent, #D4AF37); }
  .pp-section.is-open .pp-section-toggle svg { stroke: #fff; transform: rotate(180deg); }
  .pp-section-body { max-height: 0; overflow: hidden; transition: max-height 0.4s ease; }
  .pp-section.is-open .pp-section-body { max-height: 1200px; }
  .pp-section-inner {
    padding: 0 24px 24px; border-top: 1px solid var(--color-border, #E8E2D8); padding-top: 20px;
  }
  .pp-section-inner p {
    font-size: 14px; color: var(--color-text-secondary, #5C5347);
    line-height: 1.75; margin: 0 0 12px;
  }
  .pp-section-inner ul, .pp-section-inner ol {
    margin: 8px 0 14px; padding-left: 20px; font-size: 14px;
    color: var(--color-text-secondary, #5C5347); line-height: 1.75;
  }
  .pp-section-inner li { margin-bottom: 4px; }
  .pp-section-inner strong { color: var(--color-text-primary, #1a1a1a); }
  .pp-section-inner a { color: var(--color-accent, #D4AF37); }
  .pp-highlight {
    background: rgba(212,175,55,0.05); border-left: 3px solid var(--color-accent, #D4AF37);
    border-radius: 0 8px 8px 0; padding: 14px 18px; margin: 14px 0;
    font-size: 13px; color: var(--color-text-secondary, #5C5347); line-height: 1.65;
  }
  .pp-effective {
    text-align: center; padding: 28px;
    background: var(--color-surface, #fff); border: 1px solid var(--color-border, #E8E2D8);
    border-radius: 14px; margin-top: 32px;
  }
  .pp-effective p { font-size: 13px; color: var(--color-text-tertiary, #9A8E7E); margin: 0 0 6px; }
  .pp-effective .date {
    font-family: 'JetBrains Mono', monospace; font-size: 15px;
    font-weight: 600; color: var(--color-accent, #D4AF37);
  }
  .pp-back-top {
    position: fixed; bottom: 32px; right: 32px;
    width: 44px; height: 44px; border-radius: 50%;
    background: var(--color-accent, #D4AF37); color: #fff;
    border: none; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 4px 14px rgba(212,175,55,0.3);
    opacity: 0; pointer-events: none; transition: all 0.3s; z-index: 100;
  }
  .pp-back-top.show { opacity: 1; pointer-events: auto; }
  .pp-back-top:hover { transform: translateY(-3px); }
  .pp-back-top svg { width: 18px; height: 18px; }
  @media (max-width: 900px) {
    .pp-main { grid-template-columns: 1fr; }
    .pp-sidebar { position: static; max-height: none; }
    .pp-toc-list { columns: 2; column-gap: 8px; }
  }
</style>

<!-- HERO -->
<section class="pp-hero">
  <div class="pp-hero-inner">
    <div>
      <div class="pp-hero-eyebrow">Legal</div>
      <h1>Privacy<br><span class="gold">Policy</span></h1>
      <p class="pp-hero-desc">Your privacy matters to us. Learn how ATELIER collects, uses, and protects your personal information.</p>
      <div class="pp-hero-btns">
        <a href="#privacy-body" class="pp-btn-primary">
          Read Policy
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
        </a>
        <a href="<?= BASE_URL ?>/pages/terms.php" class="pp-btn-outline">Terms & Conditions</a>
      </div>
    </div>

    <div class="pp-hero-illust">
      <div class="pp-illust-glow"></div>
      <div class="pp-illust-dots"><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span></div>
      <div class="pp-illust-paper pp-p1"></div>
      <div class="pp-illust-paper pp-p2"></div>
      <div class="pp-illust-paper pp-p3"></div>

      <div class="pp-illust-doc">
        <div class="pp-doc-head">
          <div class="pp-doc-head-title">Privacy Policy</div>
          <div class="pp-doc-head-sub">ATELIER Fashion</div>
        </div>
        <div class="pp-doc-body">
          <div class="pp-doc-line"></div><div class="pp-doc-line"></div><div class="pp-doc-line"></div><div class="pp-doc-line"></div><div class="pp-doc-line"></div>
        </div>
        <div class="pp-doc-seal"><svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg></div>
      </div>

      <div class="pp-illust-shield">
        <svg width="64" height="74" viewBox="0 0 70 80" fill="none">
          <path d="M35 4 L66 17 L66 40 C66 58 52 72 35 78 C18 72 4 58 4 40 L4 17 Z" fill="url(#ppSG)"/>
          <path d="M35 10 L60 21 L60 40 C60 55 49 67 35 73 C21 67 10 55 10 40 L10 21 Z" fill="rgba(255,255,255,0.15)"/>
          <path d="M24 40 L32 48 L48 28" stroke="#fff" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
          <defs><linearGradient id="ppSG" x1="4" y1="4" x2="66" y2="78"><stop offset="0%" stop-color="#D4AF37"/><stop offset="100%" stop-color="#B8960B"/></linearGradient></defs>
        </svg>
      </div>
    </div>
  </div>
</section>

<!-- MAIN CONTENT -->
<div id="privacy-body" class="pp-main">
  <aside class="pp-sidebar">
    <nav class="pp-toc">
      <div class="pp-toc-title">Table of Contents</div>
      <ul class="pp-toc-list">
        <?php
        $toc = ['Information We Collect','How We Use Your Information','Cookies & Tracking','Data Sharing','Data Security','Your Rights','Data Retention','Children\'s Privacy','Changes to This Policy','Contact Us'];
        foreach ($toc as $i => $t): ?>
          <li><a href="#ps<?= $i+1 ?>"><span class="pp-toc-num"><?= str_pad($i+1,2,'0',STR_PAD_LEFT) ?></span><?= $t ?></a></li>
        <?php endforeach; ?>
      </ul>
    </nav>
  </aside>

  <div class="pp-content">
    <div class="pp-intro">
      <div class="pp-intro-icon">
        <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
      </div>
      <p>At <strong>ATELIER</strong>, we value your privacy. This policy explains what data we collect, why we collect it, and how we keep it safe. By using our website, you agree to the practices described here.</p>
    </div>

    <?php
    $sections = [];

    $sections[] = ['Information We Collect',
      'We collect information you provide directly to us, such as when you create an account, make a purchase, or contact us.',
      '<ul><li><strong>Personal Details:</strong> Name, email address, phone number, and shipping/billing address.</li><li><strong>Payment Information:</strong> Credit/debit card details and UPI identifiers (processed securely via our payment partners).</li><li><strong>Account Data:</strong> Username, password (encrypted), order history, and wishlist items.</li><li><strong>Communication:</strong> Messages, reviews, and feedback you send to us.</li></ul>'
    ];

    $sections[] = ['How We Use Your Information',
      'We use the information we collect for the following purposes:',
      '<ul><li>To process and fulfill your orders, including shipping and returns.</li><li>To communicate with you about your purchases, account activity, and promotions.</li><li>To personalize your shopping experience and recommend products.</li><li>To improve our website, products, and services.</li><li>To detect and prevent fraud, unauthorized access, and other illegal activities.</li><li>To comply with legal obligations and resolve disputes.</li></ul>'
    ];

    $sections[] = ['Cookies & Tracking',
      'We use cookies and similar technologies to enhance your browsing experience:',
      '<ul><li><strong>Essential Cookies:</strong> Required for the website to function (cart, login, checkout).</li><li><strong>Analytics Cookies:</strong> Help us understand how visitors interact with our site.</li><li><strong>Marketing Cookies:</strong> Used to deliver relevant advertisements and track campaign performance.</li></ul>',
      '<div class="pp-highlight">You can manage cookie preferences through your browser settings. Disabling certain cookies may affect website functionality.</div>'
    ];

    $sections[] = ['Data Sharing',
      'We do not sell your personal information. We may share your data with:',
      '<ul><li><strong>Service Providers:</strong> Payment processors, shipping carriers, and analytics partners who help us operate our business.</li><li><strong>Legal Authorities:</strong> When required by law, court order, or to protect our rights.</li><li><strong>Business Transfers:</strong> In connection with a merger, acquisition, or sale of assets (with prior notice).</li></ul>'
    ];

    $sections[] = ['Data Security',
      'We implement industry-standard security measures to protect your data:',
      '<ul><li>SSL/TLS encryption for all data transmitted between your browser and our servers.</li><li>PCI DSS compliant payment processing — we never store your card details.</li><li>Regular security audits and vulnerability assessments.</li><li>Restricted access controls — only authorized personnel can access personal data.</li></ul>',
      '<div class="pp-highlight">While we take every reasonable precaution, no method of transmission over the Internet is 100% secure. We encourage you to use strong passwords and keep your account credentials confidential.</div>'
    ];

    $sections[] = ['Your Rights',
      'You have the following rights regarding your personal data:',
      '<ul><li><strong>Access:</strong> Request a copy of all personal data we hold about you.</li><li><strong>Correction:</strong> Request correction of inaccurate or incomplete data.</li><li><strong>Deletion:</strong> Request deletion of your personal data (subject to legal retention requirements).</li><li><strong>Opt-Out:</strong> Unsubscribe from marketing emails at any time via the link in the email.</li><li><strong>Data Portability:</strong> Request your data in a structured, machine-readable format.</li></ul>'
    ];

    $sections[] = ['Data Retention',
      'We retain your personal data only for as long as necessary:',
      '<ul><li><strong>Account Data:</strong> Retained while your account is active, plus 2 years after closure.</li><li><strong>Order Data:</strong> Retained for 7 years for tax and legal compliance.</li><li><strong>Marketing Data:</strong> Until you unsubscribe or request deletion.</li><li><strong>Analytics Data:</strong> Anonymized after 26 months.</li></ul>'
    ];

    $sections[] = ['Children\'s Privacy',
      'Our website is not intended for children under 18 years of age. We do not knowingly collect personal information from children. If you believe we have collected data from a child, please contact us immediately and we will delete it promptly.'
    ];

    $sections[] = ['Changes to This Policy',
      'We may update this Privacy Policy from time to time. Changes will be posted on this page with an updated "Effective Date." We encourage you to review this policy periodically. Material changes will be communicated via email to registered users.'
    ];

    $sections[] = ['Contact Us',
      'If you have questions about this Privacy Policy or wish to exercise your rights, please contact us:',
      '<ul><li><strong>Email:</strong> privacy@atelier.com</li><li><strong>Phone:</strong> ' . $sitePhone . '</li><li><strong>Address:</strong> ' . $siteAddress . '</li></ul>'
    ];

    foreach ($sections as $i => $sec):
      $num = str_pad($i+1, 2, '0', STR_PAD_LEFT);
      $isOpen = $i === 0 ? ' is-open' : '';
    ?>
      <div id="ps<?= $i+1 ?>" class="pp-section<?= $isOpen ?>">
        <div class="pp-section-head" onclick="togglePP(this.parentElement)">
          <span class="pp-section-num"><?= $num ?></span>
          <span class="pp-section-title"><?= $sec[0] ?></span>
          <span class="pp-section-toggle">
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

    <div class="pp-effective">
      <p>This Privacy Policy is effective as of</p>
      <div class="date">January 1, 2026</div>
    </div>
  </div>
</div>

<button class="pp-back-top" id="ppBackTop" onclick="window.scrollTo({top:0,behavior:'smooth'})">
  <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/></svg>
</button>

<script>
function togglePP(el) { el.classList.toggle('is-open'); }
window.addEventListener('scroll', () => {
  document.getElementById('ppBackTop').classList.toggle('show', window.scrollY > 400);
});
document.querySelectorAll('.pp-toc-list a').forEach(a => {
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

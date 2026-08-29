<?php
$pageTitle = 'Terms & Conditions — ATELIER';
$pageDescription = 'Read our terms and conditions for using our website and services.';
include dirname(__DIR__) . '/includes/header.php';
?>

<style>
  body { background: var(--color-bg, #FAF9F6); }

  /* ══════════════════════════════ HERO ══════════════════════════════ */
  .tc-hero {
    margin-top: var(--header-height);
    background: linear-gradient(160deg, #1a1a1a 0%, #2a2a2a 50%, #1a1a1a 100%);
    overflow: hidden;
    position: relative;
  }
  .tc-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background: url("data:image/svg+xml,%3Csvg width='40' height='40' viewBox='0 0 40 40' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23D4AF37' fill-opacity='0.03'%3E%3Cpath d='M20 20.5V18H0v-2h20v-2l2 3-2 3z'/%3E%3C/g%3E%3C/svg%3E");
  }
  .tc-hero-inner {
    max-width: 1100px;
    margin: 0 auto;
    padding: 60px 24px 70px;
    display: grid;
    grid-template-columns: 1fr 400px;
    gap: 48px;
    align-items: center;
    position: relative;
    z-index: 1;
  }

  /* Hero Text */
  .tc-hero-text { }
  .tc-hero-eyebrow {
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
  .tc-hero-eyebrow::before {
    content: '';
    width: 28px;
    height: 2px;
    background: var(--color-accent, #D4AF37);
  }
  .tc-hero h1 {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: clamp(36px, 4.5vw, 52px);
    font-weight: 800;
    color: #fff;
    line-height: 1.1;
    margin: 0 0 18px;
  }
  .tc-hero h1 .gold { color: var(--color-accent, #D4AF37); }
  .tc-hero-desc {
    font-size: 15px;
    color: rgba(255,255,255,0.5);
    line-height: 1.7;
    max-width: 420px;
    margin-bottom: 28px;
  }
  .tc-hero-btns { display: flex; gap: 12px; flex-wrap: wrap; }
  .tc-btn-primary {
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
  .tc-btn-primary:hover { background: #fff; color: #1a1a1a; transform: translateY(-2px); }
  .tc-btn-outline {
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
  .tc-btn-outline:hover { border-color: var(--color-accent, #D4AF37); color: var(--color-accent, #D4AF37); }

  /* Hero Illustration */
  .tc-hero-illust {
    position: relative;
    height: 340px;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  /* Soft glow circle */
  .tc-illust-glow {
    position: absolute;
    width: 300px; height: 300px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(212,175,55,0.12) 0%, rgba(212,175,55,0) 70%);
    top: 50%; left: 50%;
    transform: translate(-50%,-50%);
    z-index: 0;
  }

  /* Main document card */
  .tc-illust-doc {
    position: absolute;
    width: 190px; height: 250px;
    background: #fff;
    border-radius: 14px;
    border: 1.5px solid #E5E7EB;
    box-shadow: 0 16px 48px rgba(0,0,0,0.12), 0 2px 8px rgba(0,0,0,0.04);
    top: 24px; left: 50%;
    transform: translateX(-60%) rotate(-2deg);
    z-index: 2;
    overflow: hidden;
  }
  .tc-doc-ribbon {
    background: linear-gradient(135deg, var(--color-accent, #D4AF37), #C9A227);
    padding: 16px 18px 14px;
    position: relative;
  }
  .tc-doc-ribbon::after {
    content: '';
    position: absolute;
    bottom: -8px; left: 0; right: 0;
    height: 8px;
    background: linear-gradient(180deg, rgba(0,0,0,0.06), transparent);
  }
  .tc-doc-ribbon-title {
    font-size: 11px; font-weight: 800; color: #fff;
    text-transform: uppercase; letter-spacing: 0.08em;
    font-family: 'Plus Jakarta Sans', sans-serif;
  }
  .tc-doc-ribbon-sub {
    font-size: 9px; color: rgba(255,255,255,0.7);
    margin-top: 2px; letter-spacing: 0.03em;
  }
  .tc-doc-content { padding: 20px 18px 18px; }
  .tc-doc-line {
    height: 5px; background: #F0EDE6; border-radius: 3px; margin-bottom: 7px;
  }
  .tc-doc-line:nth-child(2) { width: 85%; }
  .tc-doc-line:nth-child(3) { width: 70%; }
  .tc-doc-line:nth-child(4) { width: 92%; }
  .tc-doc-line:nth-child(5) { width: 55%; }
  .tc-doc-line:nth-child(6) { width: 78%; }
  .tc-doc-line:nth-child(7) { width: 40%; }
  .tc-doc-seal {
    width: 32px; height: 32px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--color-accent, #D4AF37), #C9A227);
    border: 2.5px solid #fff;
    box-shadow: 0 2px 8px rgba(212,175,55,0.3);
    position: absolute;
    bottom: 14px; right: 14px;
    display: flex; align-items: center; justify-content: center;
  }
  .tc-doc-seal svg { width: 16px; height: 16px; stroke: #fff; }

  /* Shield badge */
  .tc-illust-shield {
    position: absolute;
    top: 20px; right: 45px; z-index: 5;
    filter: drop-shadow(0 6px 16px rgba(212,175,55,0.25));
    animation: tcFloat 3.5s ease-in-out infinite;
  }
  @keyframes tcFloat { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-8px)} }

  /* Signing hand */
  .tc-illust-hand {
    position: absolute;
    bottom: 20px; right: 30px; z-index: 4;
    width: 140px;
    animation: tcFloat 4s ease-in-out 0.5s infinite;
  }
  .tc-illust-hand svg { width: 100%; height: auto; }

  /* Decorative dots */
  .tc-illust-dots {
    position: absolute; top: 10px; left: 15px; z-index: 1;
    display: grid; grid-template-columns: repeat(3,6px); gap: 8px;
  }
  .tc-illust-dots span {
    width: 6px; height: 6px; border-radius: 50%;
    background: var(--color-accent, #D4AF37); opacity: 0.25;
  }
  .tc-illust-dots span:nth-child(2) { opacity: 0.4; }
  .tc-illust-dots span:nth-child(5) { opacity: 0.15; }

  /* Corner accent lines */
  .tc-illust-corner {
    position: absolute; z-index: 1;
  }
  .tc-illust-corner.tl { top: 0; left: 0; }
  .tc-illust-corner.br { bottom: 40px; right: 0; }

  /* Floating mini papers */
  .tc-illust-paper {
    position: absolute;
    background: #fff; border: 1px solid #E5E7EB; border-radius: 5px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.05); z-index: 1;
  }
  .tc-p1 { width: 34px; height: 42px; top: 0; left: 30px; transform: rotate(-12deg); animation: tcFloatP 4s ease-in-out infinite; }
  .tc-p2 { width: 28px; height: 34px; top: 70px; right: 0; transform: rotate(14deg); animation: tcFloatP 3.5s ease-in-out 0.6s infinite; }
  .tc-p3 { width: 30px; height: 38px; bottom: 60px; left: 5px; transform: rotate(10deg); animation: tcFloatP 4.5s ease-in-out 1.2s infinite; }
  @keyframes tcFloatP { 0%,100%{transform:translateY(0) rotate(var(--r,0deg))} 50%{transform:translateY(-7px) rotate(var(--r,0deg))} }

  /* ══════════════════════════════ MAIN CONTENT ══════════════════════════════ */
  .tc-main {
    max-width: 1100px;
    margin: 0 auto;
    padding: 48px 24px 100px;
    display: grid;
    grid-template-columns: 240px 1fr;
    gap: 48px;
    align-items: start;
  }

  /* ── Sidebar TOC ── */
  .tc-sidebar {
    position: sticky;
    top: calc(var(--header-height) + 24px);
    max-height: calc(100vh - var(--header-height) - 48px);
    overflow-y: auto;
  }
  .tc-toc {
    background: var(--color-surface, #fff);
    border: 1px solid var(--color-border, #E8E2D8);
    border-radius: 14px;
    padding: 20px;
  }
  .tc-toc-title {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--color-text-primary, #1a1a1a);
    margin-bottom: 14px;
    padding-bottom: 10px;
    border-bottom: 1px solid var(--color-border, #E8E2D8);
  }
  .tc-toc-list { list-style: none; margin: 0; padding: 0; }
  .tc-toc-list li { margin-bottom: 2px; }
  .tc-toc-list a {
    display: flex;
    align-items: baseline;
    gap: 8px;
    padding: 7px 10px;
    border-radius: 8px;
    font-size: 13px;
    color: var(--color-text-secondary, #5C5347);
    text-decoration: none;
    transition: all 0.2s;
    line-height: 1.35;
  }
  .tc-toc-list a:hover {
    background: rgba(212,175,55,0.06);
    color: var(--color-accent, #D4AF37);
  }
  .tc-toc-list .tc-toc-num {
    font-family: 'JetBrains Mono', monospace;
    font-size: 10px;
    font-weight: 600;
    color: var(--color-accent, #D4AF37);
    min-width: 18px;
  }

  /* ── Content Area ── */
  .tc-content {}

  /* Intro Card */
  .tc-intro {
    background: var(--color-surface, #fff);
    border: 1px solid var(--color-border, #E8E2D8);
    border-radius: 14px;
    padding: 24px 28px;
    margin-bottom: 32px;
    display: flex;
    align-items: flex-start;
    gap: 16px;
  }
  .tc-intro-icon {
    width: 40px; height: 40px; border-radius: 10px;
    background: rgba(212,175,55,0.1);
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
  }
  .tc-intro-icon svg { width: 20px; height: 20px; stroke: var(--color-accent, #D4AF37); }
  .tc-intro p {
    font-size: 14px; color: var(--color-text-secondary, #5C5347);
    line-height: 1.7; margin: 0;
  }
  .tc-intro strong { color: var(--color-text-primary, #1a1a1a); }

  /* Section Card */
  .tc-section {
    background: var(--color-surface, #fff);
    border: 1px solid var(--color-border, #E8E2D8);
    border-radius: 14px;
    margin-bottom: 16px;
    scroll-margin-top: calc(var(--header-height) + 24px);
    overflow: hidden;
    transition: box-shadow 0.3s;
  }
  .tc-section:hover { box-shadow: 0 2px 12px rgba(0,0,0,0.04); }

  .tc-section-head {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 18px 24px;
    cursor: pointer;
    user-select: none;
    transition: background 0.2s;
  }
  .tc-section-head:hover { background: rgba(212,175,55,0.03); }
  .tc-section-num {
    font-family: 'JetBrains Mono', monospace;
    font-size: 12px; font-weight: 700;
    color: var(--color-accent, #D4AF37);
    background: rgba(212,175,55,0.08);
    width: 32px; height: 32px;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
  }
  .tc-section-title {
    flex: 1;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 15px; font-weight: 700;
    color: var(--color-text-primary, #1a1a1a);
  }
  .tc-section-toggle {
    width: 28px; height: 28px;
    border-radius: 6px;
    background: var(--color-surface-alt, #F5F0E8);
    display: flex; align-items: center; justify-content: center;
    transition: all 0.3s;
    flex-shrink: 0;
  }
  .tc-section-toggle svg {
    width: 14px; height: 14px;
    stroke: var(--color-text-tertiary, #9A8E7E);
    transition: transform 0.3s;
  }
  .tc-section.is-open .tc-section-toggle { background: var(--color-accent, #D4AF37); }
  .tc-section.is-open .tc-section-toggle svg { stroke: #fff; transform: rotate(180deg); }

  .tc-section-body {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.4s ease, padding 0.3s;
  }
  .tc-section.is-open .tc-section-body {
    max-height: 1200px;
  }
  .tc-section-inner {
    padding: 0 24px 24px;
    border-top: 1px solid var(--color-border, #E8E2D8);
    padding-top: 20px;
  }
  .tc-section-inner p {
    font-size: 14px;
    color: var(--color-text-secondary, #5C5347);
    line-height: 1.75;
    margin: 0 0 12px;
  }
  .tc-section-inner ul, .tc-section-inner ol {
    margin: 8px 0 14px;
    padding-left: 20px;
    font-size: 14px;
    color: var(--color-text-secondary, #5C5347);
    line-height: 1.75;
  }
  .tc-section-inner li { margin-bottom: 4px; }
  .tc-section-inner strong { color: var(--color-text-primary, #1a1a1a); }
  .tc-section-inner a { color: var(--color-accent, #D4AF37); }

  /* Highlight Box */
  .tc-highlight {
    background: rgba(212,175,55,0.05);
    border-left: 3px solid var(--color-accent, #D4AF37);
    border-radius: 0 8px 8px 0;
    padding: 14px 18px;
    margin: 14px 0;
    font-size: 13px;
    color: var(--color-text-secondary, #5C5347);
    line-height: 1.65;
  }

  /* Effective Date */
  .tc-effective {
    text-align: center;
    padding: 28px;
    background: var(--color-surface, #fff);
    border: 1px solid var(--color-border, #E8E2D8);
    border-radius: 14px;
    margin-top: 32px;
  }
  .tc-effective p {
    font-size: 13px;
    color: var(--color-text-tertiary, #9A8E7E);
    margin: 0 0 6px;
  }
  .tc-effective .date {
    font-family: 'JetBrains Mono', monospace;
    font-size: 15px;
    font-weight: 600;
    color: var(--color-accent, #D4AF37);
  }

  /* Back to Top */
  .tc-back-top {
    position: fixed;
    bottom: 32px;
    right: 32px;
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: var(--color-accent, #D4AF37);
    color: #fff;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 14px rgba(212,175,55,0.3);
    opacity: 0;
    pointer-events: none;
    transition: all 0.3s;
    z-index: 100;
  }
  .tc-back-top.show { opacity: 1; pointer-events: auto; }
  .tc-back-top:hover { transform: translateY(-3px); box-shadow: 0 6px 20px rgba(212,175,55,0.4); }
  .tc-back-top svg { width: 18px; height: 18px; }

  @media (max-width: 900px) {
    .tc-hero-inner { grid-template-columns: 1fr; }
    .tc-hero-illust { display: none; }
    .tc-main { grid-template-columns: 1fr; }
    .tc-sidebar { position: static; max-height: none; }
    .tc-toc { margin-bottom: 8px; }
    .tc-toc-list { columns: 2; column-gap: 8px; }
  }
  @media (max-width: 500px) {
    .tc-toc-list { columns: 1; }
    .tc-hero { padding: 0; }
  }
</style>

<!-- ════════════════════ HERO ════════════════════ -->
<section class="tc-hero">
  <div class="tc-hero-inner">
    <div class="tc-hero-text">
      <div class="tc-hero-eyebrow">Legal</div>
      <h1>Terms and<br><span class="gold">Conditions</span></h1>
      <p class="tc-hero-desc">Please read these terms carefully before using our website or services. By accessing ATELIER, you agree to be bound by these conditions.</p>
      <div class="tc-hero-btns">
        <a href="#terms-body" class="tc-btn-primary">
          Read Terms
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
        </a>
        <a href="<?= BASE_URL ?>/pages/privacy.php" class="tc-btn-outline">Privacy Policy</a>
      </div>
    </div>

    <div class="tc-hero-illust">
      <div class="tc-illust-glow"></div>

      <!-- Decorative dots -->
      <div class="tc-illust-dots">
        <span></span><span></span><span></span>
        <span></span><span></span><span></span>
        <span></span><span></span><span></span>
      </div>

      <!-- Floating papers -->
      <div class="tc-illust-paper tc-p1"></div>
      <div class="tc-illust-paper tc-p2"></div>
      <div class="tc-illust-paper tc-p3"></div>

      <!-- Corner accents -->
      <div class="tc-illust-corner tl">
        <svg width="40" height="40" viewBox="0 0 40 40" fill="none">
          <path d="M0 1V0H1" stroke="rgba(212,175,55,0.3)" stroke-width="1.5"/>
          <path d="M0 8V0H8" stroke="rgba(212,175,55,0.15)" stroke-width="1"/>
        </svg>
      </div>
      <div class="tc-illust-corner br">
        <svg width="40" height="40" viewBox="0 0 40 40" fill="none">
          <path d="M40 39V40H39" stroke="rgba(212,175,55,0.3)" stroke-width="1.5"/>
          <path d="M40 32V40H32" stroke="rgba(212,175,55,0.15)" stroke-width="1"/>
        </svg>
      </div>

      <!-- Main Document -->
      <div class="tc-illust-doc">
        <div class="tc-doc-ribbon">
          <div class="tc-doc-ribbon-title">Term And Conditions</div>
          <div class="tc-doc-ribbon-sub">ATELIER Fashion</div>
        </div>
        <div class="tc-doc-content">
          <div class="tc-doc-line"></div>
          <div class="tc-doc-line"></div>
          <div class="tc-doc-line"></div>
          <div class="tc-doc-line"></div>
          <div class="tc-doc-line"></div>
          <div class="tc-doc-line"></div>
          <div class="tc-doc-line"></div>
        </div>
        <div class="tc-doc-seal">
          <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
          </svg>
        </div>
      </div>

      <!-- Shield Badge -->
      <div class="tc-illust-shield">
        <svg width="64" height="74" viewBox="0 0 70 80" fill="none">
          <path d="M35 4 L66 17 L66 40 C66 58 52 72 35 78 C18 72 4 58 4 40 L4 17 Z" fill="url(#shieldGrad)"/>
          <path d="M35 10 L60 21 L60 40 C60 55 49 67 35 73 C21 67 10 55 10 40 L10 21 Z" fill="rgba(255,255,255,0.15)"/>
          <path d="M24 40 L32 48 L48 28" stroke="#fff" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
          <defs>
            <linearGradient id="shieldGrad" x1="4" y1="4" x2="66" y2="78">
              <stop offset="0%" stop-color="#D4AF37"/>
              <stop offset="100%" stop-color="#B8960B"/>
            </linearGradient>
          </defs>
        </svg>
      </div>

      <!-- Signing Hand -->
      <div class="tc-illust-hand">
        <svg viewBox="0 0 160 120" fill="none">
          <!-- Wrist/Arm -->
          <path d="M20 110 Q30 90 50 70 Q60 58 75 50" stroke="#F5D0A9" stroke-width="18" stroke-linecap="round" fill="none"/>
          <!-- Shirt sleeve -->
          <path d="M10 115 Q15 100 25 88 L45 80 Q38 95 32 110 Z" fill="#1a1a1a"/>
          <rect x="10" y="110" width="22" height="10" rx="4" fill="#fff" opacity="0.3"/>
          <!-- Hand -->
          <ellipse cx="78" cy="48" rx="12" ry="10" fill="#F5D0A9" transform="rotate(-20 78 48)"/>
          <!-- Pen -->
          <g transform="translate(72, 18) rotate(-55)">
            <rect width="8" height="80" rx="2" fill="#1a1a1a"/>
            <rect width="8" height="10" rx="2" fill="#D4AF37"/>
            <rect y="72" width="8" height="14" rx="1" fill="#F5D0A9"/>
            <polygon points="1.5,86 6.5,86 4,94" fill="#1a1a1a"/>
            <rect y="8" width="8" height="4" fill="#D4AF37"/>
          </g>
          <!-- Signature line -->
          <line x1="85" y1="85" x2="145" y2="70" stroke="rgba(212,175,55,0.4)" stroke-width="1.5" stroke-dasharray="3 2"/>
          <!-- Signature scribble -->
          <path d="M90 82 Q100 76 110 80 Q118 84 125 78 Q130 74 138 76" stroke="#D4AF37" stroke-width="2" stroke-linecap="round" fill="none"/>
        </svg>
      </div>
    </div>
  </div>
</section>

<!-- ════════════════════ MAIN CONTENT ════════════════════ -->
<div id="terms-body" class="tc-main">

  <!-- Sidebar TOC -->
  <aside class="tc-sidebar">
    <nav class="tc-toc">
      <div class="tc-toc-title">Table of Contents</div>
      <ul class="tc-toc-list">
        <?php
        $tocItems = [
          'Acceptance of Terms', 'Eligibility', 'Products & Pricing', 'Orders & Payments',
          'Shipping & Delivery', 'Returns & Exchanges', 'User Accounts', 'Intellectual Property',
          'Prohibited Activities', 'Limitation of Liability', 'Indemnification', 'Privacy Policy',
          'Modifications', 'Governing Law', 'Contact Us'
        ];
        foreach ($tocItems as $i => $item): ?>
          <li>
            <a href="#s<?= $i+1 ?>">
              <span class="tc-toc-num"><?= str_pad($i+1, 2, '0', STR_PAD_LEFT) ?></span>
              <?= $item ?>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </nav>
  </aside>

  <!-- Content -->
  <div class="tc-content">

    <!-- Intro -->
    <div class="tc-intro">
      <div class="tc-intro-icon">
        <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
      </div>
      <p>Welcome to <strong>ATELIER</strong>. By accessing or using our website (<strong>www.atelier.com</strong>) and our services, you agree to be bound by these Terms and Conditions. These terms apply to all visitors, users, and customers. If you do not agree, please do not use our website.</p>
    </div>

    <?php
    $privacyUrl = BASE_URL . '/pages/privacy.php';
    $sections = [];

    $sections[] = ['Acceptance of Terms',
      'By accessing, browsing, or using this website, you acknowledge that you have read, understood, and agree to be bound by these Terms and Conditions, along with our <a href="' . $privacyUrl . '">Privacy Policy</a>.
       We reserve the right to update or modify these terms at any time without prior notice. Your continued use of the website following any changes constitutes acceptance of those changes.',
      '<div class="tc-highlight">If you are using this website on behalf of an organization, you represent and warrant that you have the authority to bind that organization to these terms.</div>'
    ];

    $sections[] = ['Eligibility',
      'Our services are available only to individuals who can form legally binding contracts under applicable law. By using this website, you represent that:',
      '<ul><li>You are at least 18 years of age, or the age of legal majority in your jurisdiction.</li><li>You have the legal capacity to enter into a binding agreement.</li><li>All information you provide is truthful, accurate, and complete.</li><li>You will maintain and promptly update your information to keep it accurate.</li></ul>'
    ];

    $sections[] = ['Products &amp; Pricing',
      'We make every effort to display our products as accurately as possible, including colors, sizes, and descriptions. However, we cannot guarantee that your screen\'s display will accurately reflect the actual product.
       All prices displayed on the website are in <strong>Indian Rupees (₹)</strong> and are inclusive of applicable taxes unless stated otherwise.',
      '<ul><li>Product images are for illustration purposes and may differ slightly from actual items.</li><li>We reserve the right to limit the quantities of any products or services.</li><li>In the event of a pricing error, we will notify you before processing your order.</li><li>Offers and discounts cannot be combined unless explicitly stated.</li></ul>'
    ];

    $sections[] = ['Orders &amp; Payments',
      'Placing an order constitutes an offer to purchase the selected products. We reserve the right to accept or decline any order.
       Payment must be received in full before dispatch. We accept:',
      '<ul><li><strong>Online Payment:</strong> Credit cards, debit cards, net banking, UPI, and wallets.</li><li><strong>Cash on Delivery (COD):</strong> Available for eligible orders in select pin codes.</li><li><strong>EMI:</strong> Available on select credit cards for orders above ₹3,000.</li></ul>',
      '<div class="tc-highlight">An order confirmation email does not guarantee acceptance. We reserve the right to cancel orders that appear fraudulent or contain pricing errors.</div>'
    ];

    $sections[] = ['Shipping &amp; Delivery',
      'We aim to dispatch all orders within <strong>2-3 business days</strong>. Delivery timelines vary by location and shipping method.',
      '<ul><li><strong>Standard Shipping:</strong> 5-7 business days (free on orders above ₹999).</li><li><strong>Express Shipping:</strong> 2-3 business days (₹149 additional).</li><li><strong>COD Orders:</strong> May take 1-2 additional business days for verification.</li></ul>',
      'Tracking details will be shared via email and SMS once your order is shipped.'
    ];

    $sections[] = ['Returns &amp; Exchanges',
      'We offer a <strong>7-day hassle-free return/exchange</strong> policy from the date of delivery.',
      '<ul><li>Items must be unworn, unwashed, and in original condition with all tags attached.</li><li>Sale items and innerwear are eligible for exchange only, not refunds.</li><li>Return shipping is free for the first return per order.</li><li>Refunds are processed within 5-7 business days to the original payment method.</li><li>COD refunds will be credited to your bank account via NEFT.</li></ul>',
      '<div class="tc-highlight">We reserve the right to refuse returns that do not meet our conditions. Repeated returns of worn items may result in account suspension.</div>'
    ];

    $sections[] = ['User Accounts',
      'When you create an account, you are responsible for maintaining the confidentiality of your credentials and for all activities under your account.',
      '<ul><li>You must provide accurate and complete registration information.</li><li>You must notify us immediately of any unauthorized use of your account.</li><li>We reserve the right to suspend or terminate accounts that violate these terms.</li><li>You may delete your account at any time by contacting our support team.</li></ul>'
    ];

    $sections[] = ['Intellectual Property',
      'All content on this website, including text, graphics, logos, images, product descriptions, and software, is the property of <strong>ATELIER</strong> or its licensors and is protected by Indian and international copyright, trademark, and intellectual property laws.
       You may not reproduce, distribute, modify, create derivative works of, publicly display, or exploit any content without prior written permission.'
    ];

    $sections[] = ['Prohibited Activities',
      'When using our website, you agree not to:',
      '<ul><li>Use the website for any unlawful purpose or in violation of any law.</li><li>Attempt to gain unauthorized access to any part of the website or other accounts.</li><li>Use automated systems, bots, or scrapers to access or collect data.</li><li>Engage in any activity that could damage, disable, or impair the website.</li><li>Post false, misleading, or harmful content through reviews or forms.</li><li>Resell or commercially use any content without authorization.</li></ul>'
    ];

    $sections[] = ['Limitation of Liability',
      'To the maximum extent permitted by law, <strong>ATELIER</strong> shall not be liable for any indirect, incidental, special, consequential, or punitive damages arising out of your use of this website or purchase of products.
       Our total liability to you for any claim shall not exceed the amount you paid for the specific product or service giving rise to the claim.'
    ];

    $sections[] = ['Indemnification',
      'You agree to indemnify, defend, and hold harmless <strong>ATELIER</strong>, its directors, employees, agents, and affiliates from any claims, liabilities, damages, losses, or expenses arising from your use of this website or violation of these terms.'
    ];

    $sections[] = ['Privacy Policy',
      'Your use of this website is also governed by our <a href="' . $privacyUrl . '">Privacy Policy</a>, which describes how we collect, use, and protect your personal information. By using this website, you consent to the practices described therein.'
    ];

    $sections[] = ['Modifications',
      'We reserve the right to modify these Terms and Conditions at any time. Changes are effective immediately upon posting with an updated "Effective Date." We encourage you to review this page periodically.'
    ];

    $sections[] = ['Governing Law',
      'These Terms shall be governed by the laws of <strong>India</strong>. Any disputes shall be subject to the exclusive jurisdiction of the courts in <strong>New Delhi, India</strong>.'
    ];

    $sections[] = ['Contact Us',
      'If you have questions regarding these Terms, please contact us:',
      '<ul><li><strong>Email:</strong> legal@atelier.com</li><li><strong>Phone:</strong> +91 98765 43210</li><li><strong>Address:</strong> ATELIER Fashion Pvt. Ltd., 42 Design Studio, Hauz Khas Village, New Delhi - 110016</li></ul>'
    ];

    foreach ($sections as $i => $sec):
      $num = str_pad($i+1, 2, '0', STR_PAD_LEFT);
      $isOpen = $i === 0 ? ' is-open' : '';
    ?>
      <div id="s<?= $i+1 ?>" class="tc-section<?= $isOpen ?>">
        <div class="tc-section-head" onclick="toggleSection(this.parentElement)">
          <span class="tc-section-num"><?= $num ?></span>
          <span class="tc-section-title"><?= $sec[0] ?></span>
          <span class="tc-section-toggle">
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

    <!-- Effective Date -->
    <div class="tc-effective">
      <p>These Terms &amp; Conditions are effective as of</p>
      <div class="date">January 1, 2026</div>
    </div>

  </div>
</div>

<!-- Back to Top -->
<button class="tc-back-top" id="tcBackTop" onclick="window.scrollTo({top:0,behavior:'smooth'})">
  <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/></svg>
</button>

<script>
function toggleSection(el) {
  el.classList.toggle('is-open');
}

// Back to top visibility
window.addEventListener('scroll', () => {
  document.getElementById('tcBackTop').classList.toggle('show', window.scrollY > 400);
});

// Smooth scroll for TOC links
document.querySelectorAll('.tc-toc-list a').forEach(a => {
  a.addEventListener('click', e => {
    e.preventDefault();
    const id = a.getAttribute('href').slice(1);
    const target = document.getElementById(id);
    if (target) {
      target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      // Auto-open the section
      if (!target.classList.contains('is-open')) {
        target.classList.add('is-open');
      }
    }
  });
});

// Open section from URL hash
if (location.hash) {
  const el = document.querySelector(location.hash);
  if (el && el.classList.contains('tc-section')) {
    el.classList.add('is-open');
    setTimeout(() => el.scrollIntoView({ behavior: 'smooth', block: 'start' }), 100);
  }
}
</script>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>

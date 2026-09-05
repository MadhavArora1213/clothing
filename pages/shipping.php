<?php
require_once dirname(__DIR__) . '/config/database.php';

$pageTitle       = 'Shipping Policy — Urban Outfit Collection | Delivery & Charges';
$pageDescription = 'Free express shipping on orders above ₹999. Standard delivery 3-7 days across India. Read our full shipping policy.';
$pageKeywords    = 'urban outfit shipping policy, free shipping india, delivery charges clothing, fast shipping fashion india';
$pageCanonical   = 'https://urbanoutfitshop.com/pages/shipping.php';
include dirname(__DIR__) . '/includes/header.php';
?>

<style>
:root { --gold: #D4AF37; --gold-light: rgba(212,175,55,0.10); --dark: #111; --surface: #fff; --border: #ECEAE4; --muted: #6B6156; --bg: #F9F7F3; }

/* ── PAGE WRAPPER ── */
.ship-page { background: var(--bg); min-height: 100vh; padding-bottom: 100px; }

/* ── HERO ── */
.ship-hero {
  background: var(--dark);
  padding: calc(var(--header-height, 80px) + 60px) 0 70px;
  position: relative; overflow: hidden;
}
.ship-hero::before {
  content: '';
  position: absolute; inset: 0;
  background: radial-gradient(ellipse 70% 60% at 80% 50%, rgba(212,175,55,0.07) 0%, transparent 70%);
}
.ship-hero-inner {
  max-width: 1060px; margin: 0 auto; padding: 0 28px;
  display: flex; align-items: center; justify-content: space-between;
  gap: 40px; position: relative; z-index: 1;
}
.ship-hero-left { flex: 1; }
.ship-eyebrow {
  display: inline-flex; align-items: center; gap: 8px;
  font-size: 11px; font-weight: 700; text-transform: uppercase;
  letter-spacing: 0.14em; color: var(--gold); margin-bottom: 18px;
}
.ship-eyebrow::before { content:''; width: 26px; height: 2px; background: var(--gold); }
.ship-hero h1 {
  font-family: var(--font-display); font-size: clamp(38px,5vw,58px);
  font-weight: 700; color: #fff; line-height: 1.1; margin: 0 0 16px;
}
.ship-hero h1 span { color: var(--gold); font-style: italic; }
.ship-hero-sub { font-size: 15px; color: rgba(255,255,255,0.5); line-height: 1.7; max-width: 440px; margin-bottom: 30px; }
.ship-hero-pills { display: flex; gap: 10px; flex-wrap: wrap; }
.ship-pill {
  display: inline-flex; align-items: center; gap: 7px;
  padding: 8px 18px; border-radius: 999px;
  font-size: 12px; font-weight: 600;
  border: 1.5px solid rgba(255,255,255,0.12);
  color: rgba(255,255,255,0.75); background: rgba(255,255,255,0.05);
  backdrop-filter: blur(6px);
}
.ship-pill svg { width: 13px; height: 13px; stroke: var(--gold); }
.ship-pill.gold { background: var(--gold); border-color: var(--gold); color: #fff; }
.ship-pill.gold svg { stroke: #fff; }

/* Big right illustration */
.ship-hero-right { flex-shrink: 0; }
.ship-hero-cards { display: flex; flex-direction: column; gap: 12px; }
.ship-stat-card {
  background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1);
  border-radius: 14px; padding: 16px 22px; display: flex; align-items: center; gap: 14px;
  backdrop-filter: blur(10px); min-width: 220px;
}
.ship-stat-icon {
  width: 40px; height: 40px; border-radius: 10px;
  background: var(--gold-light); display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.ship-stat-icon svg { width: 20px; height: 20px; stroke: var(--gold); }
.ship-stat-val { font-size: 18px; font-weight: 800; color: #fff; line-height: 1.1; }
.ship-stat-label { font-size: 11px; color: rgba(255,255,255,0.45); font-weight: 500; margin-top: 2px; }

/* ── QUICK HIGHLIGHTS BAR ── */
.ship-highlights {
  background: var(--surface); border-bottom: 1px solid var(--border);
}
.ship-highlights-inner {
  max-width: 1060px; margin: 0 auto; padding: 0 28px;
  display: grid; grid-template-columns: repeat(4, 1fr);
}
.ship-hl {
  display: flex; align-items: center; gap: 14px;
  padding: 20px 16px; border-right: 1px solid var(--border);
}
.ship-hl:last-child { border-right: none; }
.ship-hl-icon {
  width: 42px; height: 42px; border-radius: 10px;
  background: var(--gold-light); display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.ship-hl-icon svg { width: 20px; height: 20px; stroke: var(--gold); }
.ship-hl-title { font-size: 13px; font-weight: 700; color: var(--dark); margin-bottom: 1px; }
.ship-hl-sub { font-size: 11px; color: var(--muted); }

/* ── CONTENT AREA ── */
.ship-content {
  max-width: 1060px; margin: 0 auto; padding: 52px 28px 0;
  display: grid; grid-template-columns: 220px 1fr; gap: 40px; align-items: start;
}

/* ── SIDEBAR TOC ── */
.ship-toc {
  position: sticky; top: calc(var(--header-height, 80px) + 24px);
  background: var(--surface); border: 1px solid var(--border);
  border-radius: 14px; padding: 20px; overflow: hidden;
}
.ship-toc-head {
  font-size: 10px; font-weight: 700; text-transform: uppercase;
  letter-spacing: 0.12em; color: var(--muted);
  margin-bottom: 14px; padding-bottom: 12px; border-bottom: 1px solid var(--border);
}
.ship-toc ul { list-style: none; margin: 0; padding: 0; }
.ship-toc li { margin-bottom: 2px; }
.ship-toc a {
  display: flex; align-items: center; gap: 10px;
  padding: 8px 10px; border-radius: 8px; font-size: 13px;
  color: var(--muted); text-decoration: none; transition: all 0.2s;
}
.ship-toc a:hover, .ship-toc a.active {
  background: var(--gold-light); color: var(--dark);
}
.ship-toc-num {
  font-size: 10px; font-weight: 700; color: var(--gold);
  min-width: 18px; font-family: monospace;
}

/* ── SECTIONS ── */
.ship-sections { min-width: 0; }
.ship-section {
  background: var(--surface); border: 1px solid var(--border);
  border-radius: 16px; margin-bottom: 14px; overflow: hidden;
  transition: box-shadow 0.25s; scroll-margin-top: calc(var(--header-height, 80px) + 28px);
}
.ship-section:hover { box-shadow: 0 4px 20px rgba(0,0,0,0.05); }

.ship-sec-head {
  display: flex; align-items: center; gap: 14px;
  padding: 18px 22px; cursor: pointer; user-select: none;
  transition: background 0.2s;
}
.ship-sec-head:hover { background: rgba(0,0,0,0.01); }
.ship-sec-num {
  width: 34px; height: 34px; border-radius: 9px;
  background: var(--gold-light); color: var(--gold);
  display: flex; align-items: center; justify-content: center;
  font-size: 11px; font-weight: 800; flex-shrink: 0; font-family: monospace;
}
.ship-sec-title {
  flex: 1; font-size: 15px; font-weight: 700; color: var(--dark);
}
.ship-sec-chevron {
  width: 30px; height: 30px; border-radius: 8px;
  background: #F5F3EF; display: flex; align-items: center; justify-content: center;
  transition: all 0.3s; flex-shrink: 0;
}
.ship-sec-chevron svg { width: 14px; height: 14px; stroke: #9A8E7E; transition: transform 0.3s; }
.ship-section.open .ship-sec-chevron { background: var(--gold); }
.ship-section.open .ship-sec-chevron svg { stroke: #fff; transform: rotate(180deg); }

.ship-sec-body { max-height: 0; overflow: hidden; transition: max-height 0.4s ease; }
.ship-section.open .ship-sec-body { max-height: 1000px; }
.ship-sec-inner {
  padding: 20px 22px 22px; border-top: 1px solid var(--border);
  font-size: 14px; color: var(--muted); line-height: 1.8;
}
.ship-sec-inner p { margin: 0 0 12px; }
.ship-sec-inner ul, .ship-sec-inner ol { margin: 8px 0 14px; padding-left: 22px; }
.ship-sec-inner li { margin-bottom: 5px; }
.ship-sec-inner strong { color: var(--dark); font-weight: 600; }

/* Table */
.ship-table { width: 100%; border-collapse: collapse; margin: 14px 0; font-size: 13px; }
.ship-table th {
  text-align: left; padding: 10px 14px; background: #F8F6F2;
  font-size: 10px; font-weight: 700; text-transform: uppercase;
  letter-spacing: 0.08em; color: var(--dark);
  border-bottom: 2px solid var(--border);
}
.ship-table td { padding: 11px 14px; border-bottom: 1px solid var(--border); color: var(--muted); }
.ship-table tr:last-child td { border-bottom: none; }
.ship-table tr:hover td { background: #FDFCFA; }
.ship-table .free { color: #16A34A; font-weight: 700; }

/* Callout box */
.ship-callout {
  display: flex; gap: 12px; align-items: flex-start;
  background: var(--gold-light); border-left: 3px solid var(--gold);
  border-radius: 0 10px 10px 0; padding: 14px 16px; margin: 14px 0;
  font-size: 13px; color: #5C4A1A; line-height: 1.65;
}
.ship-callout svg { width: 16px; height: 16px; stroke: var(--gold); flex-shrink: 0; margin-top: 2px; }
.ship-callout-green {
  background: #F0FDF4; border-left-color: #16A34A; color: #14532D;
}
.ship-callout-green svg { stroke: #16A34A; }

/* Steps */
.ship-steps { display: flex; flex-direction: column; gap: 12px; margin: 14px 0; }
.ship-step {
  display: flex; gap: 14px; align-items: flex-start;
  padding: 14px 16px; background: #FDFCFA; border: 1px solid var(--border); border-radius: 10px;
}
.ship-step-num {
  width: 28px; height: 28px; border-radius: 50%; background: var(--gold);
  color: #fff; font-size: 11px; font-weight: 800;
  display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.ship-step-title { font-size: 13px; font-weight: 700; color: var(--dark); margin-bottom: 2px; }
.ship-step-desc { font-size: 12px; color: var(--muted); line-height: 1.55; }

/* Contact cards */
.ship-contact-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin: 14px 0; }
.ship-contact-card {
  display: flex; gap: 12px; align-items: flex-start;
  padding: 14px 16px; background: #FDFCFA; border: 1px solid var(--border); border-radius: 10px;
}
.ship-contact-icon {
  width: 36px; height: 36px; border-radius: 9px;
  background: var(--gold-light); display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.ship-contact-icon svg { width: 16px; height: 16px; stroke: var(--gold); }
.ship-contact-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: var(--muted); margin-bottom: 3px; }
.ship-contact-val { font-size: 13px; font-weight: 600; color: var(--dark); }

/* Bottom bar */
.ship-footer-note {
  max-width: 1060px; margin: 32px auto 0; padding: 0 28px;
  display: flex; align-items: center; justify-content: space-between;
  padding-top: 24px; border-top: 1px solid var(--border);
  font-size: 12px; color: var(--muted); flex-wrap: wrap; gap: 10px;
}
.ship-footer-note a { color: var(--gold); text-decoration: none; font-weight: 600; }

/* Back to top */
.ship-btt {
  position: fixed; bottom: 28px; right: 28px;
  width: 42px; height: 42px; border-radius: 50%;
  background: var(--gold); border: none; cursor: pointer;
  display: flex; align-items: center; justify-content: center;
  box-shadow: 0 4px 16px rgba(212,175,55,0.35);
  opacity: 0; pointer-events: none; transition: all 0.3s; z-index: 99;
}
.ship-btt.visible { opacity: 1; pointer-events: auto; }
.ship-btt:hover { transform: translateY(-3px); }
.ship-btt svg { width: 16px; height: 16px; stroke: #fff; stroke-width: 2.5; }

/* Responsive */
@media (max-width: 900px) {
  .ship-content { grid-template-columns: 1fr; }
  .ship-toc { position: static; }
  .ship-highlights-inner { grid-template-columns: 1fr 1fr; }
  .ship-hl:nth-child(2) { border-right: none; }
  .ship-hero-right { display: none; }
}
@media (max-width: 560px) {
  .ship-highlights-inner { grid-template-columns: 1fr; }
  .ship-hl { border-right: none; border-bottom: 1px solid var(--border); }
  .ship-contact-grid { grid-template-columns: 1fr; }
}
</style>

<div class="ship-page">

  <!-- ═══ HERO ═══ -->
  <section class="ship-hero">
    <div class="ship-hero-inner">
      <div class="ship-hero-left">
        <div class="ship-eyebrow">Shipping & Delivery</div>
        <h1>Fast & Safe<br><span>Delivery</span> Across India</h1>
        <p class="ship-hero-sub">Every order packed with care and shipped through trusted courier partners. Free shipping on orders above ₹999.</p>
        <div class="ship-hero-pills">
          <span class="ship-pill gold">
            <svg viewBox="0 0 24 24" fill="none"><path d="M5 12h14M12 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Free above ₹999
          </span>
          <span class="ship-pill">
            <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            3–7 Day Delivery
          </span>
          <span class="ship-pill">
            <svg viewBox="0 0 24 24" fill="none"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Pan India
          </span>
        </div>
      </div>
      <div class="ship-hero-right">
        <div class="ship-hero-cards">
          <div class="ship-stat-card">
            <div class="ship-stat-icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.5"><path d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg></div>
            <div><div class="ship-stat-val">3–7 Days</div><div class="ship-stat-label">Standard Delivery</div></div>
          </div>
          <div class="ship-stat-card">
            <div class="ship-stat-icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.5"><path d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/></svg></div>
            <div><div class="ship-stat-val">Pan India</div><div class="ship-stat-label">All States Covered</div></div>
          </div>
          <div class="ship-stat-card">
            <div class="ship-stat-icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.5"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg></div>
            <div><div class="ship-stat-val">100% Safe</div><div class="ship-stat-label">Packed & Insured</div></div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ═══ HIGHLIGHTS BAR ═══ -->
  <div class="ship-highlights">
    <div class="ship-highlights-inner">
      <div class="ship-hl">
        <div class="ship-hl-icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.5"><path d="M5 12h14M12 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
        <div><div class="ship-hl-title">Free Shipping</div><div class="ship-hl-sub">On orders above ₹999</div></div>
      </div>
      <div class="ship-hl">
        <div class="ship-hl-icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div>
        <div><div class="ship-hl-title">Same Day Dispatch</div><div class="ship-hl-sub">Orders before 2 PM IST</div></div>
      </div>
      <div class="ship-hl">
        <div class="ship-hl-icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.5"><path d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg></div>
        <div><div class="ship-hl-title">Pan India Delivery</div><div class="ship-hl-sub">All 28 states & UTs</div></div>
      </div>
      <div class="ship-hl">
        <div class="ship-hl-icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.5"><path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg></div>
        <div><div class="ship-hl-title">Live Support</div><div class="ship-hl-sub">Mon–Sat, 10 AM–7 PM</div></div>
      </div>
    </div>
  </div>

  <!-- ═══ CONTENT ═══ -->
  <div class="ship-content">

    <!-- Sidebar TOC -->
    <aside>
      <nav class="ship-toc">
        <div class="ship-toc-head">On This Page</div>
        <ul>
          <?php
          $toc = ['Delivery Timelines','Shipping Charges','Order Processing','Tracking Your Order','Delayed or Lost Packages','Contact Us'];
          foreach ($toc as $i => $t):
          ?>
            <li>
              <a href="#s<?= $i+1 ?>" class="ship-toc-link">
                <span class="ship-toc-num"><?= str_pad($i+1,2,'0',STR_PAD_LEFT) ?></span>
                <?= $t ?>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      </nav>
    </aside>

    <!-- Sections -->
    <div class="ship-sections">

      <!-- 1. Delivery Timelines -->
      <div id="s1" class="ship-section open">
        <div class="ship-sec-head" onclick="toggleSection(this.parentElement)">
          <div class="ship-sec-num">01</div>
          <div class="ship-sec-title">Delivery Timelines</div>
          <div class="ship-sec-chevron"><svg viewBox="0 0 24 24" fill="none"><path d="M19 9l-7 7-7-7" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
        </div>
        <div class="ship-sec-body">
          <div class="ship-sec-inner">
            <p>We offer multiple shipping options to suit your needs. All timelines are from the date of dispatch.</p>
            <table class="ship-table">
              <thead><tr><th>Method</th><th>Timeline</th><th>Cost</th></tr></thead>
              <tbody>
                <tr><td><strong>Standard Shipping</strong></td><td>5–7 business days</td><td>₹99</td></tr>
                <tr><td><strong>Express Shipping</strong></td><td>2–3 business days</td><td>₹199</td></tr>
                <tr><td><strong>Free Shipping</strong></td><td>5–7 business days</td><td class="free">Orders above ₹999</td></tr>
              </tbody>
            </table>
            <div class="ship-callout">
              <svg viewBox="0 0 24 24" fill="none"><path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke-linecap="round" stroke-linejoin="round"/></svg>
              Orders placed before <strong>2 PM IST</strong> on business days are typically dispatched the same day.
            </div>
          </div>
        </div>
      </div>

      <!-- 2. Shipping Charges -->
      <div id="s2" class="ship-section">
        <div class="ship-sec-head" onclick="toggleSection(this.parentElement)">
          <div class="ship-sec-num">02</div>
          <div class="ship-sec-title">Shipping Charges</div>
          <div class="ship-sec-chevron"><svg viewBox="0 0 24 24" fill="none"><path d="M19 9l-7 7-7-7" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
        </div>
        <div class="ship-sec-body">
          <div class="ship-sec-inner">
            <p>Shipping charges are calculated at checkout based on your order total and chosen method.</p>
            <ul>
              <li><strong>Free Shipping</strong> — Automatically applied on orders above ₹999</li>
              <li><strong>Standard Shipping</strong> — Flat ₹99 for all other orders</li>
              <li><strong>Express Shipping</strong> — Flat ₹199 for priority delivery</li>
            </ul>
            <div class="ship-callout ship-callout-green">
              <svg viewBox="0 0 24 24" fill="none"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke-linecap="round" stroke-linejoin="round"/></svg>
              <strong>No hidden charges.</strong> The price you see at checkout is exactly what you pay — no surprises.
            </div>
          </div>
        </div>
      </div>

      <!-- 3. Order Processing -->
      <div id="s3" class="ship-section">
        <div class="ship-sec-head" onclick="toggleSection(this.parentElement)">
          <div class="ship-sec-num">03</div>
          <div class="ship-sec-title">Order Processing</div>
          <div class="ship-sec-chevron"><svg viewBox="0 0 24 24" fill="none"><path d="M19 9l-7 7-7-7" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
        </div>
        <div class="ship-sec-body">
          <div class="ship-sec-inner">
            <p>Here's what happens from the moment you place your order:</p>
            <div class="ship-steps">
              <div class="ship-step">
                <div class="ship-step-num">1</div>
                <div><div class="ship-step-title">Order Confirmed</div><div class="ship-step-desc">You receive an email & SMS with your order details and invoice.</div></div>
              </div>
              <div class="ship-step">
                <div class="ship-step-num">2</div>
                <div><div class="ship-step-title">Quality Check & Packing</div><div class="ship-step-desc">Each item is inspected and carefully packed in our branded packaging.</div></div>
              </div>
              <div class="ship-step">
                <div class="ship-step-num">3</div>
                <div><div class="ship-step-title">Dispatched</div><div class="ship-step-desc">Handed to our courier partner within 1–2 business days of order placement.</div></div>
              </div>
              <div class="ship-step">
                <div class="ship-step-num">4</div>
                <div><div class="ship-step-title">Out for Delivery</div><div class="ship-step-desc">Courier notifies you via SMS/call. Ensure someone is available at the address.</div></div>
              </div>
            </div>
            <div class="ship-callout">
              <svg viewBox="0 0 24 24" fill="none"><path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke-linecap="round" stroke-linejoin="round"/></svg>
              During festive seasons and sale events, processing may take an additional 1–2 days. We appreciate your patience!
            </div>
          </div>
        </div>
      </div>

      <!-- 4. Tracking -->
      <div id="s4" class="ship-section">
        <div class="ship-sec-head" onclick="toggleSection(this.parentElement)">
          <div class="ship-sec-num">04</div>
          <div class="ship-sec-title">Tracking Your Order</div>
          <div class="ship-sec-chevron"><svg viewBox="0 0 24 24" fill="none"><path d="M19 9l-7 7-7-7" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
        </div>
        <div class="ship-sec-body">
          <div class="ship-sec-inner">
            <p>Once dispatched, you'll receive a tracking number via email and SMS. You can track your order through:</p>
            <ul>
              <li><strong>Email</strong> — Dispatch confirmation with tracking link sent to your registered email</li>
              <li><strong>SMS</strong> — Tracking link sent to your registered mobile number</li>
              <li><strong>My Orders</strong> — Log in to your account → My Orders for live status updates</li>
            </ul>
            <div class="ship-callout ship-callout-green">
              <svg viewBox="0 0 24 24" fill="none"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke-linecap="round" stroke-linejoin="round"/></svg>
              Tracking updates typically appear within <strong>24 hours</strong> of dispatch from our warehouse.
            </div>
          </div>
        </div>
      </div>

      <!-- 5. Delayed / Lost -->
      <div id="s5" class="ship-section">
        <div class="ship-sec-head" onclick="toggleSection(this.parentElement)">
          <div class="ship-sec-num">05</div>
          <div class="ship-sec-title">Delayed or Lost Packages</div>
          <div class="ship-sec-chevron"><svg viewBox="0 0 24 24" fill="none"><path d="M19 9l-7 7-7-7" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
        </div>
        <div class="ship-sec-body">
          <div class="ship-sec-inner">
            <p>If your order is delayed beyond the estimated delivery window or appears lost:</p>
            <ul>
              <li>Contact our support team with your <strong>order number</strong></li>
              <li>We'll investigate with our courier partner and update you within <strong>48 hours</strong></li>
              <li>If confirmed lost, we'll offer a <strong>full refund or re-shipment</strong> at no extra cost</li>
            </ul>
            <div class="ship-callout">
              <svg viewBox="0 0 24 24" fill="none"><path d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke-linecap="round" stroke-linejoin="round"/></svg>
              Please double-check your shipping address at checkout. Urban Outfit Collection is not responsible for deliveries to incorrect addresses.
            </div>
          </div>
        </div>
      </div>

      <!-- 6. Contact -->
      <div id="s6" class="ship-section">
        <div class="ship-sec-head" onclick="toggleSection(this.parentElement)">
          <div class="ship-sec-num">06</div>
          <div class="ship-sec-title">Contact Us</div>
          <div class="ship-sec-chevron"><svg viewBox="0 0 24 24" fill="none"><path d="M19 9l-7 7-7-7" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
        </div>
        <div class="ship-sec-body">
          <div class="ship-sec-inner">
            <p>Have a question about your shipment? We're here to help.</p>
            <div class="ship-contact-grid">
              <div class="ship-contact-card">
                <div class="ship-contact-icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.5"><path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg></div>
                <div><div class="ship-contact-label">Email</div><div class="ship-contact-val">support@urbanoutfitshop.com</div></div>
              </div>
              <div class="ship-contact-card">
                <div class="ship-contact-icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.5"><path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg></div>
                <div><div class="ship-contact-label">Phone / WhatsApp</div><div class="ship-contact-val">+91 98765 43210</div></div>
              </div>
              <div class="ship-contact-card">
                <div class="ship-contact-icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div>
                <div><div class="ship-contact-label">Support Hours</div><div class="ship-contact-val">Mon – Sat, 10 AM – 7 PM IST</div></div>
              </div>
              <div class="ship-contact-card">
                <div class="ship-contact-icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.5"><path d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg></div>
                <div><div class="ship-contact-label">Live Chat</div><div class="ship-contact-val">Available on website</div></div>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div><!-- /ship-sections -->
  </div><!-- /ship-content -->

  <div class="ship-footer-note">
    <span>This policy is effective from <strong>January 1, 2026</strong>. Subject to change with notice.</span>
    <span>
      <a href="<?= BASE_URL ?>/pages/returns.php">Returns & Exchanges</a> &nbsp;·&nbsp;
      <a href="<?= BASE_URL ?>/pages/contact.php">Contact Support</a>
    </span>
  </div>

</div><!-- /ship-page -->

<button class="ship-btt" id="shipBtt" onclick="window.scrollTo({top:0,behavior:'smooth'})">
  <svg viewBox="0 0 24 24" fill="none"><path d="M5 15l7-7 7 7" stroke-linecap="round" stroke-linejoin="round"/></svg>
</button>

<script>
function toggleSection(el) { el.classList.toggle('open'); }

// TOC active state on scroll
const sections = document.querySelectorAll('.ship-section');
const tocLinks = document.querySelectorAll('.ship-toc-link');
window.addEventListener('scroll', () => {
  // Back to top btn
  document.getElementById('shipBtt').classList.toggle('visible', window.scrollY > 400);
  // Active TOC
  let current = '';
  sections.forEach(s => {
    if (window.scrollY >= s.offsetTop - 120) current = s.id;
  });
  tocLinks.forEach(a => {
    a.classList.toggle('active', a.getAttribute('href') === '#' + current);
  });
}, { passive: true });

// Smooth scroll + open section on TOC click
tocLinks.forEach(a => {
  a.addEventListener('click', e => {
    e.preventDefault();
    const target = document.querySelector(a.getAttribute('href'));
    if (!target) return;
    if (!target.classList.contains('open')) target.classList.add('open');
    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
  });
});
</script>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>

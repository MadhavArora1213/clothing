<?php
require_once dirname(__DIR__) . '/config/database.php';
$pageTitle       = 'Shipping Policy — Urban Outfit Collection | Delivery & Charges';
$pageDescription = 'Free shipping on all orders across India. Standard & express delivery available. Read our full shipping policy.';
$pageKeywords    = 'urban outfit shipping policy, free shipping india, delivery charges clothing, fast shipping fashion india';
$pageCanonical   = 'https://urbanoutfitshop.com/pages/shipping.php';
include dirname(__DIR__) . '/includes/header.php';
?>

<style>
/* ====================== HERO ====================== */
.sp-hero {
  background: #000;
  padding: calc(var(--header-height, 70px) + 40px) 24px 48px;
  text-align: center;
  position: relative;
  overflow: hidden;
}
.sp-hero::before {
  content: '';
  position: absolute;
  top: -50%;
  left: -20%;
  width: 140%;
  height: 200%;
  background: radial-gradient(ellipse at center, rgba(255,255,255,0.03) 0%, transparent 60%);
  pointer-events: none;
}
.sp-hero h1 {
  font-family: 'Inter', var(--font-body);
  font-size: clamp(32px, 5vw, 48px);
  font-weight: 800;
  color: #fff;
  margin: 0 0 12px;
  letter-spacing: -0.03em;
  line-height: 1.1;
}
.sp-hero p {
  font-size: 15px;
  color: rgba(255,255,255,0.45);
  max-width: 420px;
  margin: 0 auto;
  line-height: 1.6;
}

/* ====================== STATS ====================== */
.sp-stats {
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
.sp-stat {
  text-align: center;
  padding: 24px 12px;
  border-right: 1px solid #f0f0f0;
}
.sp-stat:last-child { border-right: none; }
.sp-stat-num {
  font-family: 'Inter', var(--font-body);
  font-size: 22px;
  font-weight: 800;
  color: #000;
  line-height: 1;
}
.sp-stat-label {
  font-size: 11px;
  color: #999;
  margin-top: 6px;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  font-weight: 500;
}

/* ====================== PROMISE STRIP ====================== */
.sp-strip {
  background: #f9f9f9;
  border-top: 1px solid #eee;
  border-bottom: 1px solid #eee;
}
.sp-strip-inner {
  max-width: 700px;
  margin: 0 auto;
  padding: 0 24px;
  display: grid;
  grid-template-columns: repeat(4, 1fr);
}
.sp-strip-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 20px 12px;
  border-right: 1px solid #eee;
}
.sp-strip-item:last-child { border-right: none; }
.sp-strip-icon {
  width: 36px;
  height: 36px;
  border-radius: 8px;
  background: #000;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.sp-strip-icon svg { width: 16px; height: 16px; stroke: #fff; fill: none; }
.sp-strip-title { font-size: 12px; font-weight: 700; color: #000; }
.sp-strip-sub { font-size: 11px; color: #999; margin-top: 2px; }

/* ====================== MAIN LAYOUT ====================== */
.sp-body {
  max-width: 900px;
  margin: 0 auto;
  padding: 48px 24px 80px;
  display: grid;
  grid-template-columns: 180px 1fr;
  gap: 32px;
  align-items: start;
}

/* ====================== SIDEBAR NAV ====================== */
.sp-nav {
  position: sticky;
  top: calc(var(--header-height, 70px) + 24px);
}
.sp-nav-label {
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  color: #bbb;
  margin-bottom: 12px;
  padding-left: 12px;
}
.sp-nav ul { list-style: none; padding: 0; margin: 0; }
.sp-nav li { margin-bottom: 2px; }
.sp-nav a {
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
.sp-nav a:hover { background: #f5f5f5; color: #000; }
.sp-nav a.active { background: #000; color: #fff; }
.sp-nav-dot {
  width: 5px;
  height: 5px;
  border-radius: 50%;
  background: #ddd;
  flex-shrink: 0;
  transition: background 0.2s;
}
.sp-nav a.active .sp-nav-dot,
.sp-nav a:hover .sp-nav-dot { background: #fff; }

/* ====================== SECTIONS ====================== */
.sp-sections { min-width: 0; }

.sp-sec {
  background: #fff;
  border: 1px solid #eee;
  border-radius: 14px;
  margin-bottom: 10px;
  overflow: hidden;
  scroll-margin-top: calc(var(--header-height, 70px) + 20px);
  transition: box-shadow 0.2s;
}
.sp-sec:hover { box-shadow: 0 2px 16px rgba(0,0,0,0.04); }
.sp-sec-head {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 16px 20px;
  cursor: pointer;
  user-select: none;
  transition: background 0.15s;
}
.sp-sec-head:hover { background: #fafafa; }
.sp-sec-num {
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
.sp-sec-title {
  flex: 1;
  font-size: 14px;
  font-weight: 700;
  color: #000;
  letter-spacing: -0.01em;
}
.sp-caret {
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
.sp-caret svg { width: 14px; height: 14px; stroke: #999; fill: none; transition: transform 0.3s cubic-bezier(0.4,0,0.2,1); }
.sp-sec.open .sp-caret { background: #000; }
.sp-sec.open .sp-caret svg { stroke: #fff; transform: rotate(180deg); }
.sp-sec-body { max-height: 0; overflow: hidden; transition: max-height 0.45s cubic-bezier(0.4,0,0.2,1); }
.sp-sec.open .sp-sec-body { max-height: 1500px; }
.sp-sec-inner {
  padding: 0 20px 20px;
  border-top: 1px solid #f0f0f0;
  padding-top: 16px;
  font-size: 14px;
  color: #555;
  line-height: 1.8;
}
.sp-sec-inner p { margin: 0 0 10px; }
.sp-sec-inner strong { color: #000; font-weight: 600; }
.sp-sec-inner ul, .sp-sec-inner ol { padding-left: 20px; margin: 8px 0 12px; }
.sp-sec-inner li { margin-bottom: 5px; }

/* Table */
.sp-table { width: 100%; border-collapse: collapse; margin: 14px 0; font-size: 13px; }
.sp-table th {
  text-align: left; padding: 10px 14px; background: #f9f9f9;
  font-size: 10px; font-weight: 700; text-transform: uppercase;
  letter-spacing: 0.07em; color: #000;
  border-bottom: 2px solid #eee;
}
.sp-table td { padding: 11px 14px; border-bottom: 1px solid #eee; color: #555; }
.sp-table tr:last-child td { border-bottom: none; }
.sp-table tr:hover td { background: #f9f9f9; }
.sp-table .free { color: #16A34A; font-weight: 700; }
.sp-badge {
  display: inline-block; padding: 2px 10px; border-radius: 999px;
  font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;
}
.sp-badge-black { background: #000; color: #fff; }
.sp-badge-green { background: #DCFCE7; color: #16A34A; }

/* Callout */
.sp-callout {
  display: flex; gap: 12px; align-items: flex-start;
  padding: 14px 16px; border-radius: 10px;
  margin: 14px 0; font-size: 13px; line-height: 1.65;
}
.sp-callout svg { width: 15px; height: 15px; flex-shrink: 0; margin-top: 2px; stroke-width: 2; fill: none; }
.sp-callout-amber { background: #FFFBEB; border-left: 3px solid #F59E0B; color: #78350F; }
.sp-callout-amber svg { stroke: #F59E0B; }
.sp-callout-green { background: #F0FDF4; border-left: 3px solid #16A34A; color: #14532D; }
.sp-callout-green svg { stroke: #16A34A; }
.sp-callout-blue { background: #EFF6FF; border-left: 3px solid #3B82F6; color: #1E3A5F; }
.sp-callout-blue svg { stroke: #3B82F6; }

/* Steps */
.sp-steps { display: flex; flex-direction: column; gap: 10px; margin: 14px 0; }
.sp-step {
  display: flex; gap: 14px; align-items: flex-start;
  padding: 14px; background: #fafafa;
  border: 1px solid #eee; border-radius: 10px;
  transition: all 0.2s;
}
.sp-step:hover { border-color: #000; background: #f5f5f5; }
.sp-step-n {
  width: 26px; height: 26px; border-radius: 50%; background: #000;
  color: #fff; font-size: 10px; font-weight: 800;
  display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.sp-step-t { font-size: 13px; font-weight: 700; color: #000; margin-bottom: 3px; }
.sp-step-d { font-size: 12px; color: #777; line-height: 1.55; }

/* Contact grid */
.sp-contact { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin: 14px 0; }
.sp-ccard {
  display: flex; gap: 12px; align-items: center;
  padding: 14px; background: #fafafa;
  border: 1px solid #eee; border-radius: 10px;
  transition: all 0.2s;
}
.sp-ccard:hover { border-color: #000; }
.sp-ccard-icon {
  width: 36px; height: 36px; border-radius: 8px; flex-shrink: 0;
  background: #000; display: flex; align-items: center; justify-content: center;
}
.sp-ccard-icon svg { width: 16px; height: 16px; stroke: #fff; fill: none; }
.sp-ccard-label { font-size: 10px; text-transform: uppercase; letter-spacing: 0.08em; color: #999; font-weight: 600; margin-bottom: 3px; }
.sp-ccard-val { font-size: 13px; font-weight: 600; color: #000; }

/* Effective date */
.sp-effective {
  text-align: center; padding: 20px;
  background: #fafafa; border: 1px solid #eee;
  border-radius: 14px; margin-top: 20px;
  display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;
}
.sp-effective p { font-size: 12px; color: #999; margin: 0; }
.sp-effective strong { color: #000; }
.sp-effective-links { display: flex; gap: 16px; }
.sp-effective-links a { font-size: 12px; color: #000; text-decoration: none; font-weight: 700; transition: opacity 0.2s; }
.sp-effective-links a:hover { opacity: 0.6; }

/* Back to top */
#spBtt {
  position: fixed; bottom: 24px; right: 24px; z-index: 99;
  width: 44px; height: 44px; border-radius: 50%; background: #000;
  border: none; cursor: pointer; display: flex; align-items: center; justify-content: center;
  box-shadow: 0 4px 16px rgba(0,0,0,0.2); opacity: 0; pointer-events: none; transition: all 0.3s;
}
#spBtt.on { opacity: 1; pointer-events: auto; }
#spBtt:hover { transform: translateY(-3px) scale(1.05); }
#spBtt svg { width: 18px; height: 18px; stroke: #fff; stroke-width: 2.5; fill: none; }

/* ====================== RESPONSIVE ====================== */
@media (max-width: 768px) {
  .sp-stats {
    grid-template-columns: repeat(2, 1fr);
    margin: -20px 16px 0;
    border-radius: 14px;
  }
  .sp-stat:nth-child(2) { border-right: none; }
  .sp-stat:nth-child(1),
  .sp-stat:nth-child(2) { border-bottom: 1px solid #eee; }
  .sp-strip-inner { grid-template-columns: 1fr 1fr; }
  .sp-strip-item:nth-child(2) { border-right: none; }
  .sp-strip-item:nth-child(3),
  .sp-strip-item:nth-child(4) { border-top: 1px solid #eee; }
  .sp-body {
    grid-template-columns: 1fr;
    padding: 32px 16px 60px;
  }
  .sp-nav { position: static; }
  .sp-nav ul { display: flex; flex-wrap: wrap; gap: 4px; margin-bottom: 8px; }
  .sp-nav a { padding: 7px 10px; font-size: 12px; }
}
@media (max-width: 480px) {
  .sp-hero { padding: calc(var(--header-height, 70px) + 32px) 16px 40px; }
  .sp-hero h1 { font-size: 28px; }
  .sp-stats { margin: -20px 12px 0; }
  .sp-stat-num { font-size: 18px; }
  .sp-strip-inner { grid-template-columns: 1fr; }
  .sp-strip-item { border-right: none !important; border-bottom: 1px solid #eee; }
  .sp-strip-item:last-child { border-bottom: none; }
  .sp-contact { grid-template-columns: 1fr; }
}
</style>

<!-- HERO -->
<section class="sp-hero">
  <h1>Shipping & Delivery</h1>
  <p>Every order packed with care, shipped free across India via trusted courier partners.</p>
</section>

<!-- STATS -->
<div class="sp-stats">
  <div class="sp-stat">
    <div class="sp-stat-num">Free</div>
    <div class="sp-stat-label">On all orders</div>
  </div>
  <div class="sp-stat">
    <div class="sp-stat-num">3–7 Days</div>
    <div class="sp-stat-label">Delivery across India</div>
  </div>
  <div class="sp-stat">
    <div class="sp-stat-num">2 PM</div>
    <div class="sp-stat-label">Same day dispatch</div>
  </div>
  <div class="sp-stat">
    <div class="sp-stat-num">100%</div>
    <div class="sp-stat-label">Insured & tracked</div>
  </div>
</div>

<!-- PROMISE STRIP -->
<div class="sp-strip">
  <div class="sp-strip-inner">
    <div class="sp-strip-item">
      <div class="sp-strip-icon">
        <svg viewBox="0 0 24 24"><path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke-linecap="round" stroke-linejoin="round"/></svg>
      </div>
      <div><div class="sp-strip-title">Free Shipping</div><div class="sp-strip-sub">On all orders</div></div>
    </div>
    <div class="sp-strip-item">
      <div class="sp-strip-icon">
        <svg viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z" stroke-linecap="round" stroke-linejoin="round"/></svg>
      </div>
      <div><div class="sp-strip-title">Same Day Dispatch</div><div class="sp-strip-sub">Before 2 PM IST</div></div>
    </div>
    <div class="sp-strip-item">
      <div class="sp-strip-icon">
        <svg viewBox="0 0 24 24"><path d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064" stroke-linecap="round" stroke-linejoin="round"/></svg>
      </div>
      <div><div class="sp-strip-title">Pan India</div><div class="sp-strip-sub">All 28 states & UTs</div></div>
    </div>
    <div class="sp-strip-item">
      <div class="sp-strip-icon">
        <svg viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" stroke-linecap="round" stroke-linejoin="round"/></svg>
      </div>
      <div><div class="sp-strip-title">Insured Delivery</div><div class="sp-strip-sub">Every package protected</div></div>
    </div>
  </div>
</div>

<!-- BODY -->
<div class="sp-body" id="sp-sections">

  <aside>
    <nav class="sp-nav">
      <div class="sp-nav-label">On This Page</div>
      <ul>
        <?php
        $navItems = [
          's1' => 'Delivery',
          's2' => 'Charges',
          's3' => 'Processing',
          's4' => 'Tracking',
          's5' => 'Delayed',
          's6' => 'Contact',
        ];
        foreach ($navItems as $id => $label): ?>
          <li><a href="#<?= $id ?>"><span class="sp-nav-dot"></span><?= $label ?></a></li>
        <?php endforeach; ?>
      </ul>
    </nav>
  </aside>

  <div class="sp-sections">

    <!-- 01 Delivery Timelines -->
    <div id="s1" class="sp-sec open">
      <div class="sp-sec-head" onclick="spToggle(this.parentElement)">
        <div class="sp-sec-num">01</div>
        <div class="sp-sec-title">Delivery Timelines</div>
        <div class="sp-caret"><svg viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
      </div>
      <div class="sp-sec-body">
        <div class="sp-sec-inner">
          <p>We ship all across India via trusted courier partners. Timelines are counted from <strong>dispatch date</strong>, not order date.</p>
          <table class="sp-table">
            <thead>
              <tr><th>Method</th><th>Time</th><th>Charge</th><th>Status</th></tr>
            </thead>
            <tbody>
              <tr>
                <td><strong>Standard Shipping</strong></td>
                <td>5–7 business days</td>
                <td class="free">FREE</td>
                <td><span class="sp-badge sp-badge-green">All Orders</span></td>
              </tr>
              <tr>
                <td><strong>Express Shipping</strong></td>
                <td>2–3 business days</td>
                <td class="free">FREE</td>
                <td><span class="sp-badge sp-badge-green">All Orders</span></td>
              </tr>
            </tbody>
          </table>
          <div class="sp-callout sp-callout-amber">
            <svg viewBox="0 0 24 24"><path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Orders placed before <strong>2 PM IST</strong> on business days are typically dispatched the same day. Weekends & public holidays excluded.
          </div>
        </div>
      </div>
    </div>

    <!-- 02 Shipping Charges -->
    <div id="s2" class="sp-sec">
      <div class="sp-sec-head" onclick="spToggle(this.parentElement)">
        <div class="sp-sec-num">02</div>
        <div class="sp-sec-title">Shipping Charges</div>
        <div class="sp-caret"><svg viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
      </div>
      <div class="sp-sec-body">
        <div class="sp-sec-inner">
          <p>All orders qualify for <strong>free shipping</strong> across India — no minimum order value required.</p>
          <ul>
            <li><strong>Standard Shipping</strong> — 5–7 business days, FREE</li>
            <li><strong>Express Shipping</strong> — 2–3 business days, FREE</li>
          </ul>
          <div class="sp-callout sp-callout-green">
            <svg viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <strong>No hidden fees.</strong> Every order ships free — no minimum cart value, no surprise charges on delivery.
          </div>
        </div>
      </div>
    </div>

    <!-- 03 Order Processing -->
    <div id="s3" class="sp-sec">
      <div class="sp-sec-head" onclick="spToggle(this.parentElement)">
        <div class="sp-sec-num">03</div>
        <div class="sp-sec-title">Order Processing</div>
        <div class="sp-caret"><svg viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
      </div>
      <div class="sp-sec-body">
        <div class="sp-sec-inner">
          <p>Here's what happens from the moment you hit "Place Order":</p>
          <div class="sp-steps">
            <div class="sp-step">
              <div class="sp-step-n">1</div>
              <div><div class="sp-step-t">Order Confirmed</div><div class="sp-step-d">Email & SMS with order summary and invoice sent instantly.</div></div>
            </div>
            <div class="sp-step">
              <div class="sp-step-n">2</div>
              <div><div class="sp-step-t">Quality Check & Packing</div><div class="sp-step-d">Every item is inspected and packed in our branded packaging.</div></div>
            </div>
            <div class="sp-step">
              <div class="sp-step-n">3</div>
              <div><div class="sp-step-t">Dispatched to Courier</div><div class="sp-step-d">Handed off within 1–2 business days. You get tracking details.</div></div>
            </div>
            <div class="sp-step">
              <div class="sp-step-n">4</div>
              <div><div class="sp-step-t">Out for Delivery</div><div class="sp-step-d">Courier notifies you via call/SMS. Ensure someone is home.</div></div>
            </div>
          </div>
          <div class="sp-callout sp-callout-amber">
            <svg viewBox="0 0 24 24"><path d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke-linecap="round" stroke-linejoin="round"/></svg>
            During sale events and festive seasons, processing may take an extra 1–2 days. We appreciate your patience!
          </div>
        </div>
      </div>
    </div>

    <!-- 04 Tracking -->
    <div id="s4" class="sp-sec">
      <div class="sp-sec-head" onclick="spToggle(this.parentElement)">
        <div class="sp-sec-num">04</div>
        <div class="sp-sec-title">Tracking Your Order</div>
        <div class="sp-caret"><svg viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
      </div>
      <div class="sp-sec-body">
        <div class="sp-sec-inner">
          <p>Once dispatched you'll receive your tracking number through:</p>
          <ul>
            <li><strong>Email</strong> — Dispatch confirmation with a direct tracking link</li>
            <li><strong>SMS</strong> — Tracking link to your registered mobile number</li>
            <li><strong>My Account → My Orders</strong> — Live order status in your dashboard</li>
          </ul>
          <div class="sp-callout sp-callout-green">
            <svg viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Tracking typically goes live within <strong>24 hours</strong> of dispatch from our warehouse.
          </div>
        </div>
      </div>
    </div>

    <!-- 05 Delayed / Lost -->
    <div id="s5" class="sp-sec">
      <div class="sp-sec-head" onclick="spToggle(this.parentElement)">
        <div class="sp-sec-num">05</div>
        <div class="sp-sec-title">Delayed or Lost Packages</div>
        <div class="sp-caret"><svg viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
      </div>
      <div class="sp-sec-body">
        <div class="sp-sec-inner">
          <p>If your order hasn't arrived within the estimated window:</p>
          <ul>
            <li>Contact us with your <strong>order number</strong></li>
            <li>We'll raise an investigation with our courier and update you in <strong>48 hours</strong></li>
            <li>If confirmed lost — we'll either <strong>reship or fully refund</strong> at no extra cost</li>
          </ul>
          <div class="sp-callout sp-callout-blue">
            <svg viewBox="0 0 24 24"><path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Always double-check your shipping address at checkout. Urban Outfit Collection cannot be held responsible for deliveries to incorrect addresses.
          </div>
        </div>
      </div>
    </div>

    <!-- 06 Contact -->
    <div id="s6" class="sp-sec">
      <div class="sp-sec-head" onclick="spToggle(this.parentElement)">
        <div class="sp-sec-num">06</div>
        <div class="sp-sec-title">Contact Us</div>
        <div class="sp-caret"><svg viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
      </div>
      <div class="sp-sec-body">
        <div class="sp-sec-inner">
          <p>Shipping-related questions? We respond fast.</p>
          <div class="sp-contact">
            <div class="sp-ccard">
              <div class="sp-ccard-icon"><svg viewBox="0 0 24 24"><path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg></div>
              <div><div class="sp-ccard-label">Email</div><div class="sp-ccard-val">support@urbanoutfitshop.com</div></div>
            </div>
            <div class="sp-ccard">
              <div class="sp-ccard-icon"><svg viewBox="0 0 24 24"><path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg></div>
              <div><div class="sp-ccard-label">WhatsApp / Phone</div><div class="sp-ccard-val">+91 97807 04131</div></div>
            </div>
            <div class="sp-ccard">
              <div class="sp-ccard-icon"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div>
              <div><div class="sp-ccard-label">Hours</div><div class="sp-ccard-val">Mon–Sun, 9 AM – 9 PM</div></div>
            </div>
            <div class="sp-ccard">
              <div class="sp-ccard-icon"><svg viewBox="0 0 24 24"><path d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg></div>
              <div><div class="sp-ccard-label">Live Chat</div><div class="sp-ccard-val">Available on website</div></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Effective date -->
    <div class="sp-effective">
      <p>Effective from <strong>January 1, 2026</strong>. Urban Outfit Collection reserves the right to update this policy.</p>
      <div class="sp-effective-links">
        <a href="<?= BASE_URL ?>/pages/returns.php">Returns & Exchanges →</a>
        <a href="<?= BASE_URL ?>/pages/contact.php">Contact Support →</a>
      </div>
    </div>

  </div>
</div>

<button id="spBtt" onclick="window.scrollTo({top:0,behavior:'smooth'})">
  <svg viewBox="0 0 24 24"><path d="M5 15l7-7 7 7" stroke-linecap="round" stroke-linejoin="round"/></svg>
</button>

<script>
function spToggle(el) { el.classList.toggle('open'); }

const navLinks = document.querySelectorAll('.sp-nav a');
const sects = document.querySelectorAll('.sp-sec');
const btt = document.getElementById('spBtt');

window.addEventListener('scroll', () => {
  btt.classList.toggle('on', window.scrollY > 400);
  let cur = '';
  sects.forEach(s => { if (window.scrollY >= s.offsetTop - 140) cur = s.id; });
  navLinks.forEach(a => a.classList.toggle('active', a.getAttribute('href') === '#' + cur));
}, { passive: true });

navLinks.forEach(a => a.addEventListener('click', e => {
  e.preventDefault();
  const el = document.getElementById(a.getAttribute('href').slice(1));
  if (!el) return;
  if (!el.classList.contains('open')) el.classList.add('open');
  el.scrollIntoView({ behavior: 'smooth', block: 'start' });
}));
</script>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>

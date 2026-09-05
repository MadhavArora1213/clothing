<?php
require_once dirname(__DIR__) . '/config/database.php';
$pageTitle       = 'Shipping Policy — Urban Outfit Collection | Delivery & Charges';
$pageDescription = 'Free express shipping on orders above ₹999. Standard delivery 3-7 days across India. Read our full shipping policy.';
$pageKeywords    = 'urban outfit shipping policy, free shipping india, delivery charges clothing, fast shipping fashion india';
$pageCanonical   = 'https://urbanoutfitshop.com/pages/shipping.php';
include dirname(__DIR__) . '/includes/header.php';
?>

<style>
/* ─── SHIPPING PAGE ─── */
.sp { background: var(--color-bg); overflow-x: hidden; }

/* HERO */
.sp-hero {
  position: relative;
  padding: calc(var(--header-height) + 72px) 0 80px;
  background: #0a0a0a;
  overflow: hidden;
}
.sp-hero-noise {
  position: absolute; inset: 0; opacity: .03;
  background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");
}
.sp-hero-glow {
  position: absolute; width: 600px; height: 600px; border-radius: 50%;
  background: radial-gradient(circle, rgba(212,175,55,.13) 0%, transparent 65%);
  top: -100px; right: -100px; pointer-events: none;
}
.sp-hero-inner {
  max-width: 1100px; margin: 0 auto; padding: 0 32px;
  display: grid; grid-template-columns: 1fr 420px; gap: 64px; align-items: center;
  position: relative; z-index: 2;
}
.sp-hero-tag {
  display: inline-flex; align-items: center; gap: 8px;
  font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .16em;
  color: var(--color-accent); margin-bottom: 20px;
}
.sp-hero-tag::before { content:''; width: 28px; height: 1.5px; background: var(--color-accent); }
.sp-hero h1 {
  font-family: var(--font-display);
  font-size: clamp(42px, 5vw, 62px);
  font-weight: 700; color: #fff; line-height: 1.08; margin-bottom: 18px;
  letter-spacing: -.02em;
}
.sp-hero h1 em { font-style: italic; color: var(--color-accent); }
.sp-hero-desc { font-size: 15px; color: rgba(255,255,255,.45); line-height: 1.75; max-width: 400px; margin-bottom: 36px; }
.sp-hero-links { display: flex; gap: 12px; flex-wrap: wrap; }
.sp-btn {
  display: inline-flex; align-items: center; gap: 8px;
  padding: 12px 28px; border-radius: var(--radius-full);
  font-size: 12px; font-weight: 700; text-transform: uppercase;
  letter-spacing: .08em; text-decoration: none; transition: var(--transition);
  font-family: var(--font-body); cursor: pointer; border: none;
}
.sp-btn-gold { background: var(--color-accent); color: #fff; }
.sp-btn-gold:hover { background: #c09a2a; transform: translateY(-2px); box-shadow: 0 8px 24px rgba(212,175,55,.3); }
.sp-btn-ghost { background: rgba(255,255,255,.06); color: rgba(255,255,255,.7); border: 1px solid rgba(255,255,255,.12); }
.sp-btn-ghost:hover { background: rgba(255,255,255,.1); color: #fff; }

/* Right side stat cards */
.sp-hero-stats { display: flex; flex-direction: column; gap: 14px; }
.sp-stat {
  display: flex; align-items: center; gap: 16px;
  padding: 18px 20px; border-radius: var(--radius-md);
  background: rgba(255,255,255,.04); border: 1px solid rgba(255,255,255,.08);
  backdrop-filter: blur(12px); transition: var(--transition);
}
.sp-stat:hover { background: rgba(255,255,255,.07); border-color: rgba(212,175,55,.25); }
.sp-stat-icon {
  width: 44px; height: 44px; border-radius: 12px; flex-shrink: 0;
  background: rgba(212,175,55,.1); display: flex; align-items: center; justify-content: center;
}
.sp-stat-icon svg { width: 22px; height: 22px; stroke: var(--color-accent); stroke-width: 1.5; fill: none; }
.sp-stat-val { font-size: 18px; font-weight: 800; color: #fff; line-height: 1.1; font-family: var(--font-display); }
.sp-stat-label { font-size: 11px; color: rgba(255,255,255,.4); margin-top: 2px; }

/* PROMISE STRIP */
.sp-strip {
  background: var(--color-surface); border-top: 1px solid var(--color-border); border-bottom: 1px solid var(--color-border);
}
.sp-strip-inner {
  max-width: 1100px; margin: 0 auto; padding: 0 32px;
  display: grid; grid-template-columns: repeat(4, 1fr);
}
.sp-strip-item {
  display: flex; align-items: center; gap: 14px;
  padding: 22px 16px; border-right: 1px solid var(--color-border);
}
.sp-strip-item:last-child { border-right: none; }
.sp-strip-icon {
  width: 40px; height: 40px; border-radius: 10px; flex-shrink: 0;
  background: var(--color-accent-light); display: flex; align-items: center; justify-content: center;
}
.sp-strip-icon svg { width: 18px; height: 18px; stroke: var(--color-accent); stroke-width: 1.5; fill: none; }
.sp-strip-title { font-size: 13px; font-weight: 700; color: var(--color-text-main); }
.sp-strip-sub { font-size: 11px; color: var(--color-text-muted); margin-top: 1px; }

/* BODY LAYOUT */
.sp-body {
  max-width: 1100px; margin: 0 auto; padding: 60px 32px 100px;
  display: grid; grid-template-columns: 200px 1fr; gap: 48px; align-items: start;
}

/* SIDEBAR */
.sp-nav {
  position: sticky; top: calc(var(--header-height) + 24px);
  background: var(--color-surface); border: 1px solid var(--color-border);
  border-radius: var(--radius-md); overflow: hidden;
}
.sp-nav-head {
  padding: 16px 18px; border-bottom: 1px solid var(--color-border);
  font-size: 10px; font-weight: 700; text-transform: uppercase;
  letter-spacing: .12em; color: var(--color-text-muted);
}
.sp-nav ul { list-style: none; padding: 10px 8px; margin: 0; }
.sp-nav li { margin-bottom: 2px; }
.sp-nav a {
  display: flex; align-items: center; gap: 10px; padding: 8px 10px;
  border-radius: var(--radius-sm); font-size: 12.5px;
  color: var(--color-text-muted); text-decoration: none; transition: var(--transition);
}
.sp-nav a:hover { background: var(--color-accent-light); color: var(--color-text-main); }
.sp-nav a.active { background: var(--color-accent-light); color: var(--color-accent); font-weight: 600; }
.sp-nav-dot {
  width: 6px; height: 6px; border-radius: 50%; background: var(--color-border); flex-shrink: 0; transition: var(--transition);
}
.sp-nav a.active .sp-nav-dot, .sp-nav a:hover .sp-nav-dot { background: var(--color-accent); }

/* SECTIONS */
.sp-sections { min-width: 0; }
.sp-sec {
  background: var(--color-surface); border: 1px solid var(--color-border);
  border-radius: var(--radius-md); margin-bottom: 12px; overflow: hidden;
  transition: box-shadow .25s; scroll-margin-top: calc(var(--header-height) + 28px);
}
.sp-sec:hover { box-shadow: var(--shadow-md); }
.sp-sec-head {
  display: flex; align-items: center; gap: 14px;
  padding: 18px 22px; cursor: pointer; user-select: none;
}
.sp-sec-num {
  font-family: var(--font-mono); font-size: 11px; font-weight: 700;
  color: var(--color-accent); background: var(--color-accent-light);
  width: 32px; height: 32px; border-radius: var(--radius-sm);
  display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.sp-sec-title {
  flex: 1; font-size: 14px; font-weight: 700; color: var(--color-text-main); font-family: var(--font-body);
}
.sp-caret {
  width: 28px; height: 28px; border-radius: var(--radius-sm);
  background: #F4F2EE; display: flex; align-items: center; justify-content: center;
  transition: var(--transition); flex-shrink: 0;
}
.sp-caret svg { width: 12px; height: 12px; stroke: var(--color-text-muted); stroke-width: 2.5; fill: none; transition: transform .3s; }
.sp-sec.open .sp-caret { background: var(--color-accent); }
.sp-sec.open .sp-caret svg { stroke: #fff; transform: rotate(180deg); }
.sp-sec-body { max-height: 0; overflow: hidden; transition: max-height .4s ease; }
.sp-sec.open .sp-sec-body { max-height: 900px; }
.sp-sec-inner {
  padding: 20px 22px 24px; border-top: 1px solid var(--color-border);
  font-size: 13.5px; color: var(--color-text-muted); line-height: 1.8;
}
.sp-sec-inner p { margin: 0 0 10px; }
.sp-sec-inner strong { color: var(--color-text-main); }
.sp-sec-inner ul, .sp-sec-inner ol { padding-left: 20px; margin: 8px 0 12px; }
.sp-sec-inner li { margin-bottom: 5px; }

/* Table */
.sp-table { width: 100%; border-collapse: collapse; margin: 14px 0; font-size: 13px; }
.sp-table th {
  text-align: left; padding: 10px 14px; background: var(--color-bg);
  font-size: 10px; font-weight: 700; text-transform: uppercase;
  letter-spacing: .07em; color: var(--color-text-main);
  border-bottom: 2px solid var(--color-border);
}
.sp-table td { padding: 11px 14px; border-bottom: 1px solid var(--color-border); color: var(--color-text-muted); }
.sp-table tr:last-child td { border-bottom: none; }
.sp-table tr:hover td { background: var(--color-bg); }
.sp-table .free { color: #16A34A; font-weight: 700; }
.sp-table .badge {
  display: inline-block; padding: 2px 10px; border-radius: 999px;
  font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em;
}
.sp-table .badge-gold { background: var(--color-accent-light); color: var(--color-accent); }
.sp-table .badge-green { background: #DCFCE7; color: #16A34A; }

/* Callout */
.sp-callout {
  display: flex; gap: 12px; align-items: flex-start;
  padding: 14px 16px; border-radius: var(--radius-sm);
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
  padding: 14px; background: var(--color-bg);
  border: 1px solid var(--color-border); border-radius: var(--radius-sm);
  transition: var(--transition);
}
.sp-step:hover { border-color: var(--color-accent); background: var(--color-accent-light); }
.sp-step-n {
  width: 26px; height: 26px; border-radius: 50%; background: var(--color-accent);
  color: #fff; font-size: 10px; font-weight: 800;
  display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-family: var(--font-mono);
}
.sp-step-t { font-size: 13px; font-weight: 700; color: var(--color-text-main); margin-bottom: 3px; }
.sp-step-d { font-size: 12px; color: var(--color-text-muted); line-height: 1.55; }

/* Contact grid */
.sp-contact { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin: 14px 0; }
.sp-ccard {
  display: flex; gap: 12px; align-items: center;
  padding: 14px; background: var(--color-bg);
  border: 1px solid var(--color-border); border-radius: var(--radius-sm);
  transition: var(--transition);
}
.sp-ccard:hover { border-color: var(--color-accent); }
.sp-ccard-icon {
  width: 36px; height: 36px; border-radius: 9px; flex-shrink: 0;
  background: var(--color-accent-light); display: flex; align-items: center; justify-content: center;
}
.sp-ccard-icon svg { width: 16px; height: 16px; stroke: var(--color-accent); stroke-width: 1.5; fill: none; }
.sp-ccard-label { font-size: 10px; text-transform: uppercase; letter-spacing: .08em; color: var(--color-text-muted); font-weight: 600; margin-bottom: 3px; }
.sp-ccard-val { font-size: 13px; font-weight: 600; color: var(--color-text-main); }

/* Effective date */
.sp-effective {
  text-align: center; padding: 28px;
  background: var(--color-surface); border: 1px solid var(--color-border);
  border-radius: var(--radius-md); margin-top: 24px;
  display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;
}
.sp-effective p { font-size: 12px; color: var(--color-text-muted); }
.sp-effective strong { color: var(--color-text-main); }
.sp-effective-links { display: flex; gap: 16px; }
.sp-effective-links a { font-size: 12px; color: var(--color-accent); text-decoration: none; font-weight: 600; transition: var(--transition); }
.sp-effective-links a:hover { opacity: .7; }

/* Back to top */
#spBtt {
  position: fixed; bottom: 28px; right: 28px; z-index: 99;
  width: 42px; height: 42px; border-radius: 50%; background: var(--color-accent);
  border: none; cursor: pointer; display: flex; align-items: center; justify-content: center;
  box-shadow: 0 6px 20px rgba(212,175,55,.35); opacity: 0; pointer-events: none; transition: var(--transition);
}
#spBtt.on { opacity: 1; pointer-events: auto; }
#spBtt:hover { transform: translateY(-3px); }
#spBtt svg { width: 16px; height: 16px; stroke: #fff; stroke-width: 2.5; fill: none; }

/* Responsive */
@media (max-width: 960px) {
  .sp-hero-inner { grid-template-columns: 1fr; }
  .sp-hero-stats { flex-direction: row; flex-wrap: wrap; }
  .sp-stat { flex: 1; min-width: 160px; }
  .sp-strip-inner { grid-template-columns: 1fr 1fr; }
  .sp-strip-item:nth-child(2) { border-right: none; }
  .sp-strip-item:nth-child(3) { border-bottom: 1px solid var(--color-border); }
  .sp-body { grid-template-columns: 1fr; }
  .sp-nav { position: static; }
}
@media (max-width: 560px) {
  .sp-strip-inner { grid-template-columns: 1fr; }
  .sp-strip-item { border-right: none !important; border-bottom: 1px solid var(--color-border); }
  .sp-contact { grid-template-columns: 1fr; }
  .sp-hero-inner, .sp-body { padding-left: 20px; padding-right: 20px; }
}
</style>

<div class="sp">

  <!-- ════ HERO ════ -->
  <section class="sp-hero">
    <div class="sp-hero-noise"></div>
    <div class="sp-hero-glow"></div>
    <div class="sp-hero-inner">
      <div>
        <div class="sp-hero-tag">Policies</div>
        <h1>Shipping &amp;<br><em>Delivery</em></h1>
        <p class="sp-hero-desc">Every order packed with care and shipped via trusted courier partners across India. Free shipping on orders above ₹999.</p>
        <div class="sp-hero-links">
          <a href="#sp-sections" class="sp-btn sp-btn-gold">
            Read Policy
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 9l-7 7-7-7" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </a>
          <a href="<?= BASE_URL ?>/pages/returns.php" class="sp-btn sp-btn-ghost">Returns Policy</a>
        </div>
      </div>
      <div class="sp-hero-stats">
        <div class="sp-stat">
          <div class="sp-stat-icon">
            <svg viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </div>
          <div><div class="sp-stat-val">Free</div><div class="sp-stat-label">On orders above ₹999</div></div>
        </div>
        <div class="sp-stat">
          <div class="sp-stat-icon">
            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </div>
          <div><div class="sp-stat-val">3–7 Days</div><div class="sp-stat-label">Standard delivery across India</div></div>
        </div>
        <div class="sp-stat">
          <div class="sp-stat-icon">
            <svg viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </div>
          <div><div class="sp-stat-val">100% Safe</div><div class="sp-stat-label">Carefully packed & insured</div></div>
        </div>
      </div>
    </div>
  </section>

  <!-- ════ PROMISE STRIP ════ -->
  <div class="sp-strip">
    <div class="sp-strip-inner">
      <div class="sp-strip-item">
        <div class="sp-strip-icon">
          <svg viewBox="0 0 24 24"><path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </div>
        <div><div class="sp-strip-title">Free Shipping</div><div class="sp-strip-sub">Orders above ₹999</div></div>
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

  <!-- ════ BODY ════ -->
  <div class="sp-body" id="sp-sections">

    <!-- Sidebar Nav -->
    <aside>
      <nav class="sp-nav">
        <div class="sp-nav-head">Contents</div>
        <ul>
          <?php
          $navItems = [
            's1' => 'Delivery Timelines',
            's2' => 'Shipping Charges',
            's3' => 'Order Processing',
            's4' => 'Tracking Your Order',
            's5' => 'Delayed or Lost',
            's6' => 'Contact Us',
          ];
          foreach ($navItems as $id => $label): ?>
            <li>
              <a href="#<?= $id ?>" class="sp-nav-link">
                <span class="sp-nav-dot"></span>
                <?= $label ?>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      </nav>
    </aside>

    <!-- Content Sections -->
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
                <tr><th>Shipping Method</th><th>Estimated Time</th><th>Charge</th><th>Status</th></tr>
              </thead>
              <tbody>
                <tr>
                  <td><strong>Standard Shipping</strong></td>
                  <td>5–7 business days</td>
                  <td>₹99</td>
                  <td><span class="badge badge-gold">Available</span></td>
                </tr>
                <tr>
                  <td><strong>Express Shipping</strong></td>
                  <td>2–3 business days</td>
                  <td>₹199</td>
                  <td><span class="badge badge-gold">Available</span></td>
                </tr>
                <tr>
                  <td><strong>Free Shipping</strong></td>
                  <td>5–7 business days</td>
                  <td class="free">FREE</td>
                  <td><span class="badge badge-green">Orders ≥ ₹999</span></td>
                </tr>
              </tbody>
            </table>
            <div class="sp-callout sp-callout-amber">
              <svg viewBox="0 0 24 24"><path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke-linecap="round" stroke-linejoin="round"/></svg>
              Orders placed before <strong>2 PM IST</strong> on business days are typically dispatched the same day. Weekends & public holidays are excluded.
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
            <p>Charges are calculated automatically at checkout based on your cart total and chosen method.</p>
            <ul>
              <li><strong>₹0</strong> — Free shipping on orders ₹999 and above (auto-applied)</li>
              <li><strong>₹99</strong> — Standard flat rate for all other orders</li>
              <li><strong>₹199</strong> — Express shipping, priority handling</li>
            </ul>
            <div class="sp-callout sp-callout-green">
              <svg viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke-linecap="round" stroke-linejoin="round"/></svg>
              <strong>No hidden fees.</strong> The total shown at checkout is exactly what you pay — no surprise charges on delivery.
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
              <li><strong>My Account</strong> → My Orders — Live order status in your dashboard</li>
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
                <div><div class="sp-ccard-label">WhatsApp / Phone</div><div class="sp-ccard-val">+91 98765 43210</div></div>
              </div>
              <div class="sp-ccard">
                <div class="sp-ccard-icon"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div>
                <div><div class="sp-ccard-label">Hours</div><div class="sp-ccard-val">Mon–Sat, 10 AM – 7 PM IST</div></div>
              </div>
              <div class="sp-ccard">
                <div class="sp-ccard-icon"><svg viewBox="0 0 24 24"><path d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg></div>
                <div><div class="sp-ccard-label">Live Chat</div><div class="sp-ccard-val">Available on website</div></div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer note -->
      <div class="sp-effective">
        <p>Effective from <strong>January 1, 2026</strong>. Urban Outfit Collection reserves the right to update this policy with notice.</p>
        <div class="sp-effective-links">
          <a href="<?= BASE_URL ?>/pages/returns.php">Returns &amp; Exchanges →</a>
          <a href="<?= BASE_URL ?>/pages/contact.php">Contact Support →</a>
        </div>
      </div>

    </div><!-- /sp-sections -->
  </div><!-- /sp-body -->
</div><!-- /sp -->

<button id="spBtt" onclick="window.scrollTo({top:0,behavior:'smooth'})">
  <svg viewBox="0 0 24 24"><path d="M5 15l7-7 7 7" stroke-linecap="round" stroke-linejoin="round"/></svg>
</button>

<script>
function spToggle(el) { el.classList.toggle('open'); }

const navLinks = document.querySelectorAll('.sp-nav-link');
const sections = document.querySelectorAll('.sp-sec');
const btt = document.getElementById('spBtt');

window.addEventListener('scroll', () => {
  btt.classList.toggle('on', window.scrollY > 400);
  let cur = '';
  sections.forEach(s => { if (window.scrollY >= s.offsetTop - 140) cur = s.id; });
  navLinks.forEach(a => a.classList.toggle('active', a.getAttribute('href') === '#' + cur));
}, { passive: true });

navLinks.forEach(a => a.addEventListener('click', e => {
  e.preventDefault();
  const t = document.querySelector(a.getAttribute('href'));
  if (!t) return;
  if (!t.classList.contains('open')) t.classList.add('open');
  t.scrollIntoView({ behavior: 'smooth', block: 'start' });
}));
</script>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>

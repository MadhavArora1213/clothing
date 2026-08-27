<?php
require_once dirname(__DIR__) . '/config/database.php';
$pageTitle = 'About Us — urban outfit';
$pageDescription = 'Learn about urban outfit — our story, craft, and vision for modern luxury fashion.';
include dirname(__DIR__) . '/includes/header.php';
?>

<style>
/* ─── ABOUT PAGE ─── */

/* ── HERO ── */
.ab-hero {
  position: relative;
  background: var(--color-text-main);
  overflow: hidden;
  min-height: 85vh;
  display: flex;
  align-items: flex-end;
}
.ab-hero-inner {
  width: 100%;
  position: relative;
  padding: 60px 64px 80px;
  z-index: 2;
}
.ab-hero-images {
  position: absolute;
  top: 0; left: 0; right: 0; bottom: 0;
  display: flex;
  gap: 24px;
  padding: 40px 64px;
  opacity: 0.65;
}
.ab-hero-card {
  flex: 1;
  position: relative;
  border-radius: 14px;
  overflow: hidden;
  background: #222;
  box-shadow: 0 20px 60px rgba(0,0,0,0.4);
}
.ab-hero-card:nth-child(1) { transform: rotate(-2deg) translateY(30px); }
.ab-hero-card:nth-child(2) { transform: translateY(-20px); }
.ab-hero-card:nth-child(3) { transform: rotate(1.5deg) translateY(40px); }
.ab-hero-card:nth-child(4) { transform: rotate(-1deg) translateY(10px); }
.ab-hero-card-dots {
  display: flex;
  gap: 6px;
  padding: 12px 16px;
  background: #1a1a1a;
}
.ab-hero-card-dots span {
  width: 10px; height: 10px;
  border-radius: 50%;
}
.ab-hero-card-dots span:nth-child(1) { background: #FF5F56; }
.ab-hero-card-dots span:nth-child(2) { background: #FFBD2E; }
.ab-hero-card-dots span:nth-child(3) { background: #27CA40; }
.ab-hero-card img {
  width: 100%;
  height: 280px;
  object-fit: cover;
  display: block;
}
.ab-hero-title {
  position: relative;
  z-index: 3;
  font-family: var(--font-display);
  font-size: clamp(52px, 8vw, 110px);
  font-weight: 500;
  line-height: 0.95;
  letter-spacing: -0.04em;
  color: #fff;
  text-transform: uppercase;
}
.ab-hero-title .stroke-text {
  display: block;
  -webkit-text-stroke: 2px rgba(255,255,255,0.3);
  color: transparent;
}
.ab-hero-sub {
  position: relative;
  z-index: 3;
  margin-top: 24px;
  font-size: 15px;
  color: rgba(255,255,255,0.5);
  max-width: 500px;
  line-height: 1.7;
}
.ab-hero-deco {
  position: absolute;
  top: 60px; right: 60px;
  width: 200px; height: 200px;
  border: 1px solid rgba(212,175,55,0.15);
  border-radius: 50%;
  z-index: 1;
}

/* ── ABOUT COLLAGE SECTION (reference Image 2) ── */
.ab-collage {
  padding: 100px 0;
  background: var(--color-bg);
}
.ab-collage-inner {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 24px;
  display: grid;
  grid-template-columns: 1.1fr 0.9fr;
  gap: 60px;
  align-items: center;
}
.ab-collage-left {
  position: relative;
  height: 560px;
}
.ab-collage-img {
  position: absolute;
  object-fit: cover;
  border-radius: 12px;
  box-shadow: 0 20px 60px rgba(0,0,0,0.12);
}
.ab-collage-img-1 {
  top: 0; left: 0;
  width: 65%; height: 70%;
  z-index: 2;
}
.ab-collage-img-2 {
  top: 15%; right: 0;
  width: 55%; height: 60%;
  z-index: 1;
  border-radius: 12px;
}
.ab-collage-img-3 {
  bottom: 0; left: 10%;
  width: 50%; height: 45%;
  z-index: 3;
  border-radius: 12px;
  border: 5px solid var(--color-bg);
}
.ab-collage-about-text {
  position: absolute;
  top: 50%; right: -10px;
  transform: translateY(-50%);
  font-family: var(--font-display);
  font-size: clamp(60px, 8vw, 100px);
  font-weight: 700;
  color: var(--color-accent);
  writing-mode: vertical-rl;
  text-orientation: mixed;
  letter-spacing: 0.04em;
  z-index: 4;
  opacity: 0.85;
  line-height: 1;
}
.ab-collage-num {
  position: absolute;
  bottom: 20px; left: 0;
  font-family: var(--font-display);
  font-size: 72px;
  font-weight: 700;
  color: var(--color-accent);
  opacity: 0.2;
  z-index: 4;
  line-height: 1;
}

.ab-collage-right {}
.ab-collage-right .ab-eyebrow { margin-bottom: 14px; }
.ab-collage-heading {
  font-family: var(--font-display);
  font-size: clamp(28px, 3.5vw, 42px);
  font-weight: 400;
  line-height: 1.15;
  letter-spacing: -0.02em;
  color: var(--color-text-main);
  margin-bottom: 20px;
}
.ab-collage-heading em { font-style: italic; color: var(--color-accent); }
.ab-collage-desc {
  font-size: 15px;
  color: var(--color-text-muted);
  line-height: 1.8;
  margin-bottom: 16px;
}
.ab-collage-features {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
  margin-top: 28px;
}
.ab-collage-feat {
  padding: 20px;
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-sm);
  transition: all 0.3s;
}
.ab-collage-feat:hover {
  border-color: var(--color-accent);
  transform: translateY(-2px);
}
.ab-collage-feat h4 {
  font-size: 13px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  color: var(--color-text-main);
  margin-bottom: 4px;
}
.ab-collage-feat p {
  font-size: 12px;
  color: var(--color-text-muted);
  line-height: 1.5;
}

/* ── WHAT MAKES US GREAT ── */
.ab-great {
  padding: 100px 0;
  background: var(--color-surface);
}
.ab-great-inner {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 24px;
}
.ab-great-head {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  gap: 40px;
  margin-bottom: 60px;
}
.ab-eyebrow {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  font-size: 11px;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.14em;
  color: var(--color-accent);
  margin-bottom: 14px;
}
.ab-eyebrow::before { content: ''; width: 24px; height: 2px; background: var(--color-accent); }
.ab-great-title {
  font-family: var(--font-display);
  font-size: clamp(32px, 4vw, 50px);
  font-weight: 400;
  line-height: 1.1;
  letter-spacing: -0.03em;
  color: var(--color-text-main);
}
.ab-great-title em { font-style: italic; color: var(--color-accent); }
.ab-great-sub {
  font-size: 15px;
  color: var(--color-text-muted);
  line-height: 1.7;
  max-width: 400px;
}

.ab-values-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 24px;
}
.ab-value-card {
  padding: 40px 32px;
  border: 1px solid var(--color-border);
  border-radius: var(--radius-md);
  transition: all 0.4s ease;
  position: relative;
  overflow: hidden;
}
.ab-value-card::before {
  content: '';
  position: absolute;
  top: 0; left: 0;
  width: 100%; height: 3px;
  background: var(--color-accent);
  transform: scaleX(0);
  transform-origin: left;
  transition: transform 0.4s ease;
}
.ab-value-card:hover::before { transform: scaleX(1); }
.ab-value-card:hover {
  border-color: var(--color-accent);
  transform: translateY(-4px);
  box-shadow: 0 16px 48px rgba(0,0,0,0.06);
}
.ab-value-num {
  font-family: var(--font-display);
  font-size: 48px;
  font-weight: 400;
  color: var(--color-accent);
  margin-bottom: 16px;
  line-height: 1;
}
.ab-value-card h3 {
  font-family: var(--font-display);
  font-size: 20px;
  font-weight: 500;
  color: var(--color-text-main);
  margin-bottom: 10px;
}
.ab-value-card p {
  font-size: 14px;
  color: var(--color-text-muted);
  line-height: 1.7;
}

/* ── STORY SECTION ── */
.ab-story {
  padding: 100px 0;
  background: var(--color-bg);
}
.ab-story-inner {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 24px;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 60px;
  align-items: center;
}
.ab-story-img-wrap { position: relative; }
.ab-story-img {
  width: 100%;
  height: 520px;
  object-fit: cover;
  border-radius: 12px;
  box-shadow: 0 20px 60px rgba(0,0,0,0.1);
}
.ab-story-img-accent {
  position: absolute;
  bottom: -20px; right: -20px;
  width: 200px; height: 200px;
  background: var(--color-accent-light);
  border-radius: var(--radius-md);
  z-index: -1;
}
.ab-story-heading {
  font-family: var(--font-display);
  font-size: clamp(28px, 3.5vw, 40px);
  font-weight: 400;
  line-height: 1.15;
  letter-spacing: -0.02em;
  color: var(--color-text-main);
  margin-bottom: 20px;
}
.ab-story-heading em { font-style: italic; color: var(--color-accent); }
.ab-story-desc {
  font-size: 15px;
  color: var(--color-text-muted);
  line-height: 1.8;
  margin-bottom: 16px;
}
.ab-story-stats {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 20px;
  margin-top: 32px;
  padding-top: 32px;
  border-top: 1px solid var(--color-border);
}
.ab-stat-num {
  font-family: var(--font-display);
  font-size: 32px;
  font-weight: 500;
  color: var(--color-text-main);
  margin-bottom: 4px;
}
.ab-stat-label {
  font-size: 12px;
  color: var(--color-text-muted);
  text-transform: uppercase;
  letter-spacing: 0.06em;
}

/* ── PHILOSOPHY ── */
.ab-philosophy {
  padding: 100px 0;
  background: #0F0F0F;
  color: #fff;
}
.ab-philosophy-inner {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 24px;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 80px;
  align-items: center;
}
.ab-philosophy-text .ab-eyebrow { color: var(--color-accent); }
.ab-philosophy-text .ab-eyebrow::before { background: var(--color-accent); }
.ab-philosophy-title {
  font-family: var(--font-display);
  font-size: clamp(28px, 3.5vw, 40px);
  font-weight: 400;
  line-height: 1.15;
  letter-spacing: -0.02em;
  color: #fff;
  margin-bottom: 20px;
}
.ab-philosophy-title em { font-style: italic; color: var(--color-accent); }
.ab-philosophy-desc {
  font-size: 15px;
  color: rgba(255,255,255,0.55);
  line-height: 1.8;
  margin-bottom: 16px;
}
.ab-philosophy-list {
  list-style: none;
  margin-top: 28px;
}
.ab-philosophy-list li {
  display: flex;
  align-items: flex-start;
  gap: 14px;
  padding: 14px 0;
  border-bottom: 1px solid rgba(255,255,255,0.06);
  font-size: 14px;
  color: rgba(255,255,255,0.7);
}
.ab-philosophy-list li:last-child { border-bottom: none; }
.ab-philosophy-list svg { color: var(--color-accent); flex-shrink: 0; margin-top: 2px; }
.ab-philosophy-imgs {
  position: relative;
  height: 480px;
}
.ab-phil-img-1 {
  position: absolute;
  top: 0; left: 0;
  width: 70%; height: 75%;
  object-fit: cover;
  border-radius: 12px;
  box-shadow: 0 20px 60px rgba(0,0,0,0.3);
}
.ab-phil-img-2 {
  position: absolute;
  bottom: 0; right: 0;
  width: 55%; height: 55%;
  object-fit: cover;
  border-radius: 12px;
  border: 4px solid #0F0F0F;
  box-shadow: 0 16px 50px rgba(0,0,0,0.4);
}

/* ── CTA ── */
.ab-cta {
  padding: 80px 0;
  background: var(--color-accent-light);
}
.ab-cta-inner {
  max-width: 800px;
  margin: 0 auto;
  padding: 0 24px;
  text-align: center;
}
.ab-cta-title {
  font-family: var(--font-display);
  font-size: clamp(28px, 3.5vw, 42px);
  font-weight: 400;
  line-height: 1.15;
  color: var(--color-text-main);
  margin-bottom: 16px;
}
.ab-cta-title em { font-style: italic; color: var(--color-accent); }
.ab-cta-desc {
  font-size: 15px;
  color: var(--color-text-muted);
  line-height: 1.7;
  margin-bottom: 32px;
}
.ab-cta-btn {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  padding: 16px 40px;
  background: var(--color-text-main);
  color: #fff;
  font-size: 12px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.14em;
  border: none;
  border-radius: 0;
  cursor: pointer;
  transition: all 0.4s cubic-bezier(0.16,1,0.3,1);
  text-decoration: none;
}
.ab-cta-btn:hover {
  background: var(--color-accent);
  transform: translateY(-2px);
  box-shadow: 0 8px 30px rgba(212,175,55,0.3);
}
.ab-cta-btn svg { transition: transform 0.3s; }
.ab-cta-btn:hover svg { transform: translateX(4px); }

/* ── RESPONSIVE ── */
@media (max-width: 1024px) {
  .ab-hero { min-height: 70vh; }
  .ab-hero-images { padding: 30px 40px; gap: 16px; }
  .ab-hero-card img { height: 200px; }
  .ab-hero-title { font-size: clamp(40px, 6vw, 80px); }
  .ab-collage-inner { grid-template-columns: 1fr; gap: 40px; }
  .ab-collage-left { height: 420px; }
  .ab-values-grid { grid-template-columns: 1fr 1fr; }
  .ab-story-inner { grid-template-columns: 1fr; gap: 40px; }
  .ab-story-img { height: 400px; }
  .ab-philosophy-inner { grid-template-columns: 1fr; gap: 48px; }
  .ab-philosophy-imgs { height: 380px; }
}
@media (max-width: 640px) {
  .ab-hero { min-height: 60vh; }
  .ab-hero-inner { padding: 40px 20px 60px; }
  .ab-hero-images { padding: 20px; gap: 12px; }
  .ab-hero-card img { height: 160px; }
  .ab-hero-title { font-size: clamp(32px, 10vw, 50px); }
  .ab-collage-left { height: 360px; }
  .ab-collage-about-text { font-size: 50px; }
  .ab-collage-features { grid-template-columns: 1fr; }
  .ab-great-head { flex-direction: column; align-items: flex-start; gap: 16px; }
  .ab-values-grid { grid-template-columns: 1fr; }
  .ab-story-stats { grid-template-columns: 1fr; gap: 16px; }
  .ab-philosophy-imgs { height: 300px; }
  .ab-phil-img-1 { width: 80%; height: 70%; }
  .ab-phil-img-2 { width: 60%; height: 50%; }
}
</style>

<!-- ═══ HERO ═══ -->
<section class="ab-hero">
  <div class="ab-hero-images">
    <div class="ab-hero-card">
      <div class="ab-hero-card-dots"><span></span><span></span><span></span></div>
      <img src="https://images.unsplash.com/photo-1523381210434-271e8be1f52b?w=600&auto=format&fit=crop&q=80" alt="Our Craft">
    </div>
    <div class="ab-hero-card">
      <div class="ab-hero-card-dots"><span></span><span></span><span></span></div>
      <img src="https://images.unsplash.com/photo-1441984904996-e0b6ba687e04?w=600&auto=format&fit=crop&q=80" alt="Our Store">
    </div>
    <div class="ab-hero-card">
      <div class="ab-hero-card-dots"><span></span><span></span><span></span></div>
      <img src="https://images.unsplash.com/photo-1490481651871-ab68de25d43d?w=600&auto=format&fit=crop&q=80" alt="Our Design">
    </div>
    <div class="ab-hero-card">
      <div class="ab-hero-card-dots"><span></span><span></span><span></span></div>
      <img src="https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=600&auto=format&fit=crop&q=80" alt="Our Model">
    </div>
  </div>
  <div class="ab-hero-deco"></div>
  <div class="ab-hero-inner">
    <h1 class="ab-hero-title">
      <span class="stroke-text">About Us</span>
      urban outfit
    </h1>
    <p class="ab-hero-sub">Crafting luxury streetwear and artisanal heritage fusion for the modern aesthetic wardrobe. Designed &amp; made in India.</p>
  </div>
</section>

<!-- ═══ ABOUT COLLAGE (reference style) ═══ -->
<section class="ab-collage">
  <div class="ab-collage-inner">
    <div class="ab-collage-left">
      <img class="ab-collage-img ab-collage-img-1" src="https://images.unsplash.com/photo-1529139574466-a303027c1d8b?w=700&auto=format&fit=crop&q=80" alt="Model Fashion">
      <img class="ab-collage-img ab-collage-img-2" src="https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=600&auto=format&fit=crop&q=80" alt="Streetwear Model">
      <img class="ab-collage-img ab-collage-img-3" src="https://images.unsplash.com/photo-1469334031218-e382a71b716b?w=600&auto=format&fit=crop&q=80" alt="Heritage Craft">
      <span class="ab-collage-about-text">About</span>
      <span class="ab-collage-num">01</span>
    </div>
    <div class="ab-collage-right">
      <span class="ab-eyebrow">Who We Are</span>
      <h2 class="ab-collage-heading">We Create Clothes That<br>Tell a <em>Story</em></h2>
      <p class="ab-collage-desc">urban outfit blends centuries-old Indian textile heritage with contemporary streetwear aesthetics. Every piece is a conversation between tradition and modernity.</p>
      <p class="ab-collage-desc">Founded in Mumbai, we work directly with artisan communities across 12 states, preserving craft techniques while creating designs for the modern wardrobe.</p>
      <div class="ab-collage-features">
        <div class="ab-collage-feat">
          <h4>Handcrafted</h4>
          <p>Every stitch by skilled artisans</p>
        </div>
        <div class="ab-collage-feat">
          <h4>Sustainable</h4>
          <p>Zero-waste &amp; low-water processes</p>
        </div>
        <div class="ab-collage-feat">
          <h4>Premium Fabrics</h4>
          <p>Organic linen &amp; French Terry</p>
        </div>
        <div class="ab-collage-feat">
          <h4>Made in India</h4>
          <p>500+ artisans across 12 states</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ═══ WHAT MAKES US GREAT ═══ -->
<section class="ab-great">
  <div class="ab-great-inner">
    <div class="ab-great-head">
      <div>
        <span class="ab-eyebrow">Our Values</span>
        <h2 class="ab-great-title">What Makes Us<br><em>Great</em></h2>
      </div>
      <p class="ab-great-sub">We don't just make clothes. We craft experiences that blend heritage artistry with contemporary design sensibility.</p>
    </div>
    <div class="ab-values-grid">
      <div class="ab-value-card">
        <div class="ab-value-num">01</div>
        <h3>Handcrafted Quality</h3>
        <p>Every piece is meticulously crafted by skilled artisans using traditional techniques passed down through generations.</p>
      </div>
      <div class="ab-value-card">
        <div class="ab-value-num">02</div>
        <h3>Premium Fabrics</h3>
        <p>We source only the finest organic linens, 260+ GSM French Terry, and hand-loomed Chikankari fabrics.</p>
      </div>
      <div class="ab-value-card">
        <div class="ab-value-num">03</div>
        <h3>Sustainable Practice</h3>
        <p>From low-water dyeing to zero-waste pattern cutting, sustainability is woven into every decision we make.</p>
      </div>
    </div>
  </div>
</section>

<!-- ═══ OUR STORY ═══ -->
<section class="ab-story">
  <div class="ab-story-inner">
    <div class="ab-story-img-wrap">
      <img class="ab-story-img" src="https://images.unsplash.com/photo-1441984904996-e0b6ba687e04?w=800&auto=format&fit=crop&q=80" alt="Our Store">
      <div class="ab-story-img-accent"></div>
    </div>
    <div>
      <span class="ab-eyebrow">Our Story</span>
      <h2 class="ab-story-heading">Born From a Love<br>for <em>Heritage</em></h2>
      <p class="ab-story-desc">urban outfit was born from a simple idea — to bring the richness of Indian textile heritage to modern streetwear. What started as a small studio in Mumbai has grown into a movement that celebrates craftsmanship.</p>
      <p class="ab-story-desc">We work directly with artisan communities across India, preserving centuries-old techniques while creating designs that resonate with the contemporary aesthetic.</p>
      <div class="ab-story-stats">
        <div>
          <div class="ab-stat-num">500+</div>
          <div class="ab-stat-label">Artisans Supported</div>
        </div>
        <div>
          <div class="ab-stat-num">50K+</div>
          <div class="ab-stat-label">Pieces Crafted</div>
        </div>
        <div>
          <div class="ab-stat-num">12</div>
          <div class="ab-stat-label">States in India</div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ═══ PHILOSOPHY ═══ -->
<section class="ab-philosophy">
  <div class="ab-philosophy-inner">
    <div class="ab-philosophy-text">
      <span class="ab-eyebrow">Our Philosophy</span>
      <h2 class="ab-philosophy-title">Where Tradition<br>Meets <em>Tomorrow</em></h2>
      <p class="ab-philosophy-desc">We believe fashion should tell a story. Every thread, every stitch, every detail carries the weight of tradition and the promise of innovation.</p>
      <ul class="ab-philosophy-list">
        <li>
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
          <span>Direct partnerships with 500+ artisan families across India</span>
        </li>
        <li>
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
          <span>Zero-waste pattern cutting and low-water dyeing processes</span>
        </li>
        <li>
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
          <span>100% genuine fabrics — organic linen, French Terry, Chikankari</span>
        </li>
        <li>
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
          <span>Fair wages and ethical working conditions for all team members</span>
        </li>
        <li>
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
          <span>Premium packaging using recycled and biodegradable materials</span>
        </li>
      </ul>
    </div>
    <div class="ab-philosophy-imgs">
      <img class="ab-phil-img-1" src="https://images.unsplash.com/photo-1558171813-4c088753af8f?w=700&auto=format&fit=crop&q=80" alt="Artisan at Work">
      <img class="ab-phil-img-2" src="https://images.unsplash.com/photo-1441984904996-e0b6ba687e04?w=600&auto=format&fit=crop&q=80" alt="Our Studio">
    </div>
  </div>
</section>

<!-- ═══ CTA ═══ -->
<section class="ab-cta">
  <div class="ab-cta-inner">
    <h2 class="ab-cta-title">Ready to Experience<br><em>urban outfit</em>?</h2>
    <p class="ab-cta-desc">Explore our collections and discover pieces that blend heritage craftsmanship with modern streetwear aesthetics.</p>
    <a href="<?= BASE_URL ?>/shop.php" class="ab-cta-btn">
      <span>Shop Now</span>
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
    </a>
  </div>
</section>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>

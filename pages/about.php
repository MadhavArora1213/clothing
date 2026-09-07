<?php
require_once dirname(__DIR__) . '/config/database.php';
$pageTitle       = 'About Urban Outfit Collection — Men\'s, Women\'s & Kids\' Fashion Store in Mukerian, Punjab';
$pageDescription = 'Urban Outfit Collection is a premium fashion store based in Mukerian, Punjab. We offer trendy men\'s, women\'s, and kids\' clothing with worldwide shipping. Shop streetwear, ethnic wear, and accessories.';
$pageKeywords    = 'urban outfit collection, mukerian clothing store, punjab fashion, mens wear mukerian, kids wear, womens fashion india, streetwear india';
$pageCanonical   = 'https://urbanoutfitshop.com/pages/about.php';
include dirname(__DIR__) . '/includes/header.php';
?>

<style>
/* ═══════════════════════════════════════════════════════════════
   URBAN OUTFIT — ABOUT PAGE v2.0
   A completely unique, never-before-seen modern UI
   ═══════════════════════════════════════════════════════════════ */

/* ── CSS VARIABLES ── */
:root {
  --ab-accent: #000;
  --ab-accent-rgb: 201,168,76;
  --ab-dark: #0a0a0a;
  --ab-light: #f5f2eb;
  --ab-gray: #1a1a1a;
  --ab-muted: #6b6b6b;
  --ab-glass: rgba(255,255,255,0.04);
  --ab-glass-border: rgba(255,255,255,0.08);
}

/* ── GLOBAL RESET FOR PAGE ── */
.ab-page * { box-sizing: border-box; margin: 0; padding: 0; }
.ab-page { overflow-x: hidden; background: var(--ab-dark); }

.ab-page img {
  background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
}

/* ── CUSTOM CURSOR ── */
.ab-cursor {
  position: fixed;
  width: 20px;
  height: 20px;
  border: 2px solid var(--ab-accent);
  border-radius: 50%;
  pointer-events: none;
  z-index: 10000;
  transition: transform 0.15s ease, opacity 0.3s ease;
  mix-blend-mode: difference;
}
.ab-cursor-dot {
  position: fixed;
  width: 6px;
  height: 6px;
  background: var(--ab-accent);
  border-radius: 50%;
  pointer-events: none;
  z-index: 10001;
}
.ab-cursor.hovering {
  transform: scale(2.5);
  background: rgba(201,168,76,0.1);
}

/* ── HERO — ORGANIC SPLIT ── */
.ab-hero-split {
  position: relative;
  min-height: 100vh;
  display: grid;
  grid-template-columns: 1fr 1fr;
  overflow: hidden;
}

.ab-hero-left {
  position: relative;
  display: flex;
  flex-direction: column;
  justify-content: flex-end;
  padding: 80px 60px;
  background: var(--ab-dark);
  z-index: 2;
}

.ab-hero-left::before {
  content: '';
  position: absolute;
  top: 0; left: 0;
  width: 100%; height: 100%;
  background: 
    radial-gradient(circle at 20% 30%, rgba(var(--ab-accent-rgb),0.08) 0%, transparent 50%),
    radial-gradient(circle at 80% 70%, rgba(var(--ab-accent-rgb),0.05) 0%, transparent 40%);
  z-index: 0;
}

.ab-hero-label {
  position: relative;
  z-index: 1;
  display: inline-flex;
  align-items: center;
  gap: 12px;
  font-size: 10px;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.3em;
  color: var(--ab-accent);
  margin-bottom: 32px;
}

.ab-hero-label::before {
  content: '';
  width: 40px;
  height: 1px;
  background: var(--ab-accent);
}

.ab-hero-title-mega {
  position: relative;
  z-index: 1;
  font-family: var(--font-display);
  font-size: clamp(64px, 9vw, 140px);
  font-weight: 200;
  line-height: 0.85;
  letter-spacing: -0.06em;
  color: #fff;
}

.ab-hero-title-mega span {
  display: block;
}

.ab-hero-title-mega .line-accent {
  font-style: italic;
  color: var(--ab-accent);
  font-weight: 300;
}

.ab-hero-title-mega .line-outline {
  -webkit-text-stroke: 1.5px rgba(255,255,255,0.2);
  color: transparent;
}

.ab-hero-tagline {
  position: relative;
  z-index: 1;
  margin-top: 40px;
  font-size: 14px;
  color: rgba(255,255,255,0.4);
  line-height: 1.8;
  max-width: 380px;
}

.ab-hero-right {
  position: relative;
  overflow: hidden;
}

.ab-hero-morph {
  position: absolute;
  top: 50%; left: 50%;
  transform: translate(-50%, -50%);
  width: 500px; height: 500px;
  animation: morphFloat 12s ease-in-out infinite;
}

@keyframes morphFloat {
  0%, 100% { border-radius: 42% 58% 70% 30% / 45% 45% 55% 55%; transform: translate(-50%, -50%) scale(1); }
  25% { border-radius: 70% 30% 50% 50% / 30% 70% 30% 70%; transform: translate(-50%, -50%) scale(1.03); }
  50% { border-radius: 30% 70% 30% 70% / 55% 30% 70% 45%; transform: translate(-50%, -50%) scale(1); }
  75% { border-radius: 55% 45% 60% 40% / 40% 60% 40% 60%; transform: translate(-50%, -50%) scale(1.03); }
}

.ab-hero-morph img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  filter: saturate(0.8);
}

.ab-hero-scroll-indicator {
  position: absolute;
  bottom: 40px;
  left: 50%;
  transform: translateX(-50%);
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  z-index: 5;
}

.ab-hero-scroll-indicator span {
  font-size: 9px;
  text-transform: uppercase;
  letter-spacing: 0.2em;
  color: rgba(255,255,255,0.3);
}

.ab-hero-scroll-line {
  width: 1px;
  height: 60px;
  background: linear-gradient(to bottom, var(--ab-accent), transparent);
  animation: scrollPulse 2s ease-in-out infinite;
}

@keyframes scrollPulse {
  0%, 100% { opacity: 1; transform: scaleY(1); }
  50% { opacity: 0.3; transform: scaleY(0.6); }
}

/* ── MARQUEE STRIP ── */
.ab-marquee-strip {
  padding: 24px 0;
  background: var(--ab-accent);
  overflow: hidden;
  white-space: nowrap;
}

.ab-marquee-track {
  display: flex;
  animation: marqueeScroll 30s linear infinite;
}

.ab-marquee-item {
  flex-shrink: 0;
  font-family: var(--font-display);
  font-size: 13px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.2em;
  color: var(--ab-dark);
  padding: 0 60px;
  display: flex;
  align-items: center;
  gap: 60px;
}

.ab-marquee-item::after {
  content: '◆';
  font-size: 8px;
  opacity: 0.4;
}

@keyframes marqueeScroll {
  0% { transform: translateX(0); }
  100% { transform: translateX(-50%); }
}

/* ── NARRATIVE SECTION — SCROLL REVEAL ── */
.ab-narrative {
  padding: 160px 0;
  background: var(--ab-dark);
  position: relative;
}

.ab-narrative-inner {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 60px;
}

.ab-narrative-quote {
  font-family: var(--font-display);
  font-size: clamp(36px, 5vw, 72px);
  font-weight: 200;
  line-height: 1.15;
  letter-spacing: -0.03em;
  color: rgba(255,255,255,0.9);
  text-align: center;
  max-width: 900px;
  margin: 0 auto 120px;
  position: relative;
}

.ab-narrative-quote::before {
  content: '"';
  position: absolute;
  top: -60px;
  left: 50%;
  transform: translateX(-50%);
  font-size: 120px;
  color: var(--ab-accent);
  opacity: 0.15;
  font-family: Georgia, serif;
  line-height: 1;
}

.ab-narrative-quote em {
  font-style: italic;
  color: var(--ab-accent);
}

.ab-narrative-columns {
  display: grid;
  grid-template-columns: 1fr 1px 1fr 1px 1fr;
  gap: 0;
}

.ab-narrative-divider {
  background: linear-gradient(to bottom, transparent, rgba(var(--ab-accent-rgb),0.3), transparent);
  margin: 0 40px;
}

.ab-narrative-col {
  padding: 40px;
}

.ab-narrative-col-num {
  font-family: var(--font-display);
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 0.15em;
  color: var(--ab-accent);
  margin-bottom: 24px;
  opacity: 0.6;
}

.ab-narrative-col h3 {
  font-family: var(--font-display);
  font-size: 24px;
  font-weight: 300;
  color: #fff;
  margin-bottom: 16px;
  letter-spacing: -0.01em;
}

.ab-narrative-col p {
  font-size: 14px;
  color: rgba(255,255,255,0.4);
  line-height: 1.8;
}

/* ── BENTO GRID — ORGANIC ── */
.ab-bento {
  padding: 120px 0;
  background: linear-gradient(180deg, var(--ab-dark) 0%, #0d0d0d 100%);
}

.ab-bento-inner {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 60px;
}

.ab-bento-header {
  text-align: center;
  margin-bottom: 80px;
}

.ab-bento-header .ab-eyebrow-alt {
  display: inline-flex;
  align-items: center;
  gap: 16px;
  font-size: 10px;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.3em;
  color: var(--ab-accent);
  margin-bottom: 20px;
}

.ab-bento-header .ab-eyebrow-alt::before,
.ab-bento-header .ab-eyebrow-alt::after {
  content: '';
  width: 40px;
  height: 1px;
  background: var(--ab-accent);
}

.ab-bento-header h2 {
  font-family: var(--font-display);
  font-size: clamp(36px, 5vw, 56px);
  font-weight: 200;
  color: #fff;
  letter-spacing: -0.03em;
}

.ab-bento-header h2 em {
  font-style: italic;
  color: var(--ab-accent);
}

.ab-bento-grid {
  display: grid;
  grid-template-columns: repeat(12, 1fr);
  grid-template-rows: auto;
  gap: 20px;
}

.ab-bento-card {
  position: relative;
  border-radius: 20px;
  overflow: hidden;
  background: var(--ab-glass);
  border: 1px solid var(--ab-glass-border);
  transition: all 0.6s cubic-bezier(0.16,1,0.3,1);
}

.ab-bento-card:hover {
  transform: translateY(-8px) scale(1.01);
  border-color: rgba(var(--ab-accent-rgb),0.3);
  box-shadow: 0 40px 80px rgba(0,0,0,0.4);
}

.ab-bento-card-1 { grid-column: 1 / 8; grid-row: 1 / 2; min-height: 400px; }
.ab-bento-card-2 { grid-column: 8 / 13; grid-row: 1 / 2; min-height: 400px; }
.ab-bento-card-3 { grid-column: 1 / 5; grid-row: 2 / 3; min-height: 350px; }
.ab-bento-card-4 { grid-column: 5 / 13; grid-row: 2 / 3; min-height: 350px; }

.ab-bento-card-inner {
  position: absolute;
  inset: 0;
  display: flex;
  flex-direction: column;
  justify-content: flex-end;
  padding: 40px;
  background: linear-gradient(180deg, transparent 30%, rgba(0,0,0,0.8) 100%);
  z-index: 2;
}

.ab-bento-card-bg {
  position: absolute;
  inset: 0;
  z-index: 1;
}

.ab-bento-card-bg img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.8s cubic-bezier(0.16,1,0.3,1);
}

.ab-bento-card:hover .ab-bento-card-bg img {
  transform: scale(1.08);
}

.ab-bento-card-tag {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  font-size: 9px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.2em;
  color: var(--ab-accent);
  margin-bottom: 12px;
  width: fit-content;
}

.ab-bento-card-tag::before {
  content: '';
  width: 6px;
  height: 6px;
  background: var(--ab-accent);
  border-radius: 50%;
}

.ab-bento-card h3 {
  font-family: var(--font-display);
  font-size: 28px;
  font-weight: 300;
  color: #fff;
  margin-bottom: 10px;
  letter-spacing: -0.01em;
}

.ab-bento-card p {
  font-size: 13px;
  color: rgba(255,255,255,0.5);
  line-height: 1.7;
  max-width: 350px;
}

/* ── TIMELINE — HORIZONTAL SCROLL ── */
.ab-timeline {
  padding: 140px 0 100px;
  background: var(--ab-dark);
  overflow: hidden;
}

.ab-timeline-inner {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 60px;
}

.ab-timeline-header {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  margin-bottom: 80px;
}

.ab-timeline-header h2 {
  font-family: var(--font-display);
  font-size: clamp(36px, 5vw, 56px);
  font-weight: 200;
  color: #fff;
  letter-spacing: -0.03em;
}

.ab-timeline-header h2 em {
  font-style: italic;
  color: var(--ab-accent);
}

.ab-timeline-year {
  font-family: var(--font-display);
  font-size: 14px;
  color: var(--ab-accent);
  letter-spacing: 0.1em;
}

.ab-timeline-track {
  display: flex;
  gap: 0;
  position: relative;
  padding-bottom: 60px;
}

.ab-timeline-track::before {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  width: 100%;
  height: 1px;
  background: linear-gradient(90deg, transparent, rgba(var(--ab-accent-rgb),0.4), transparent);
}

.ab-timeline-item {
  flex: 0 0 300px;
  position: relative;
  padding: 0 40px 0 0;
}

.ab-timeline-item::before {
  content: '';
  position: absolute;
  bottom: -8px;
  left: 0;
  width: 12px;
  height: 12px;
  background: var(--ab-accent);
  border-radius: 50%;
  border: 3px solid var(--ab-dark);
  z-index: 2;
}

.ab-timeline-item-num {
  font-family: var(--font-display);
  font-size: 56px;
  font-weight: 200;
  color: rgba(var(--ab-accent-rgb),0.15);
  line-height: 1;
  margin-bottom: 16px;
}

.ab-timeline-item h3 {
  font-family: var(--font-display);
  font-size: 18px;
  font-weight: 400;
  color: #fff;
  margin-bottom: 8px;
}

.ab-timeline-item p {
  font-size: 13px;
  color: rgba(255,255,255,0.4);
  line-height: 1.7;
}

/* ── TEAM SECTION — GLASSMORPHISM ── */
.ab-team {
  padding: 140px 0;
  background: linear-gradient(180deg, #0d0d0d 0%, var(--ab-dark) 100%);
}

.ab-team-inner {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 60px;
}

.ab-team-header {
  text-align: center;
  margin-bottom: 80px;
}

.ab-team-header .ab-eyebrow-alt {
  display: inline-flex;
  align-items: center;
  gap: 16px;
  font-size: 10px;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.3em;
  color: var(--ab-accent);
  margin-bottom: 20px;
}

.ab-team-header .ab-eyebrow-alt::before,
.ab-team-header .ab-eyebrow-alt::after {
  content: '';
  width: 40px;
  height: 1px;
  background: var(--ab-accent);
}

.ab-team-header h2 {
  font-family: var(--font-display);
  font-size: clamp(36px, 5vw, 56px);
  font-weight: 200;
  color: #fff;
  letter-spacing: -0.03em;
}

.ab-team-header h2 em {
  font-style: italic;
  color: var(--ab-accent);
}

.ab-team-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 24px;
}

.ab-team-card {
  position: relative;
  border-radius: 20px;
  overflow: hidden;
  background: var(--ab-glass);
  border: 1px solid var(--ab-glass-border);
  padding: 32px;
  transition: all 0.6s cubic-bezier(0.16,1,0.3,1);
}

.ab-team-card:hover {
  transform: translateY(-12px);
  border-color: rgba(var(--ab-accent-rgb),0.3);
  box-shadow: 0 30px 60px rgba(0,0,0,0.4);
}

.ab-team-card-avatar {
  width: 100%;
  aspect-ratio: 1;
  border-radius: 16px;
  overflow: hidden;
  margin-bottom: 24px;
  position: relative;
}

.ab-team-card-avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  filter: grayscale(0.6);
  transition: filter 0.6s ease;
}

.ab-team-card:hover .ab-team-card-avatar img {
  filter: grayscale(0);
}

.ab-team-card-avatar::after {
  content: '';
  position: absolute;
  inset: 0;
  background: linear-gradient(180deg, transparent 60%, rgba(0,0,0,0.6) 100%);
}

.ab-team-card-role {
  font-size: 9px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.2em;
  color: var(--ab-accent);
  margin-bottom: 8px;
}

.ab-team-card h3 {
  font-family: var(--font-display);
  font-size: 20px;
  font-weight: 300;
  color: #fff;
  margin-bottom: 12px;
}

.ab-team-card p {
  font-size: 13px;
  color: rgba(255,255,255,0.4);
  line-height: 1.7;
}

.ab-team-card-socials {
  display: flex;
  gap: 12px;
  margin-top: 20px;
}

.ab-team-card-socials a {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: rgba(255,255,255,0.05);
  border: 1px solid rgba(255,255,255,0.1);
  display: flex;
  align-items: center;
  justify-content: center;
  color: rgba(255,255,255,0.5);
  text-decoration: none;
  transition: all 0.3s ease;
}

.ab-team-card-socials a:hover {
  background: var(--ab-accent);
  border-color: var(--ab-accent);
  color: var(--ab-dark);
}

/* ── CTA — DIAGONAL SPLIT ── */
.ab-cta-split {
  position: relative;
  min-height: 70vh;
  display: grid;
  grid-template-columns: 1fr 1fr;
  overflow: hidden;
}

.ab-cta-split-left {
  position: relative;
  display: flex;
  flex-direction: column;
  justify-content: center;
  padding: 80px 80px;
  background: var(--ab-dark);
}

.ab-cta-split-left .ab-eyebrow-alt {
  display: inline-flex;
  align-items: center;
  gap: 16px;
  font-size: 10px;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.3em;
  color: var(--ab-accent);
  margin-bottom: 24px;
  width: fit-content;
}

.ab-cta-split-left .ab-eyebrow-alt::before {
  content: '';
  width: 40px;
  height: 1px;
  background: var(--ab-accent);
}

.ab-cta-split-left h2 {
  font-family: var(--font-display);
  font-size: clamp(40px, 5vw, 64px);
  font-weight: 200;
  line-height: 1.1;
  letter-spacing: -0.03em;
  color: #fff;
  margin-bottom: 24px;
}

.ab-cta-split-left h2 em {
  font-style: italic;
  color: var(--ab-accent);
}

.ab-cta-split-left p {
  font-size: 15px;
  color: rgba(255,255,255,0.4);
  line-height: 1.8;
  margin-bottom: 40px;
  max-width: 400px;
}

.ab-cta-split-btn {
  display: inline-flex;
  align-items: center;
  gap: 16px;
  padding: 20px 48px;
  background: var(--ab-accent);
  color: var(--ab-dark);
  font-size: 11px;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.2em;
  text-decoration: none;
  border-radius: 60px;
  transition: all 0.5s cubic-bezier(0.16,1,0.3,1);
  width: fit-content;
}

.ab-cta-split-btn:hover {
  transform: translateY(-4px) scale(1.02);
  box-shadow: 0 20px 60px rgba(var(--ab-accent-rgb),0.4);
}

.ab-cta-split-btn svg {
  transition: transform 0.3s ease;
}

.ab-cta-split-btn:hover svg {
  transform: translateX(6px);
}

.ab-cta-split-right {
  position: relative;
  overflow: hidden;
}

.ab-cta-split-right img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 1s cubic-bezier(0.16,1,0.3,1);
}

.ab-cta-split-right:hover img {
  transform: scale(1.05);
}

.ab-cta-split-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, rgba(var(--ab-accent-rgb),0.1) 0%, transparent 60%);
  z-index: 1;
}

/* ── FLOATING PARTICLES ── */
.ab-particles {
  position: fixed;
  top: 0; left: 0;
  width: 100%; height: 100%;
  pointer-events: none;
  z-index: 0;
  overflow: hidden;
}

.ab-particle {
  position: absolute;
  width: 3px;
  height: 3px;
  background: var(--ab-accent);
  border-radius: 50%;
  opacity: 0;
  animation: particleFloat 8s ease-in-out infinite;
}

.ab-particle:nth-child(1) { left: 10%; animation-delay: 0s; animation-duration: 7s; }
.ab-particle:nth-child(2) { left: 25%; animation-delay: 1s; animation-duration: 9s; }
.ab-particle:nth-child(3) { left: 40%; animation-delay: 2s; animation-duration: 6s; }
.ab-particle:nth-child(4) { left: 55%; animation-delay: 3s; animation-duration: 8s; }
.ab-particle:nth-child(5) { left: 70%; animation-delay: 4s; animation-duration: 10s; }
.ab-particle:nth-child(6) { left: 85%; animation-delay: 5s; animation-duration: 7s; }
.ab-particle:nth-child(7) { left: 15%; animation-delay: 6s; animation-duration: 9s; }
.ab-particle:nth-child(8) { left: 60%; animation-delay: 7s; animation-duration: 8s; }

@keyframes particleFloat {
  0% { transform: translateY(100vh) scale(0); opacity: 0; }
  10% { opacity: 0.6; }
  90% { opacity: 0.6; }
  100% { transform: translateY(-100vh) scale(1); opacity: 0; }
}

/* ── TEXT REVEAL ANIMATION ── */
.ab-reveal {
  opacity: 0;
  transform: translateY(60px);
  transition: all 1s cubic-bezier(0.16,1,0.3,1);
}

.ab-reveal.visible {
  opacity: 1;
  transform: translateY(0);
}

.ab-reveal-delay-1 { transition-delay: 0.1s; }
.ab-reveal-delay-2 { transition-delay: 0.2s; }
.ab-reveal-delay-3 { transition-delay: 0.3s; }
.ab-reveal-delay-4 { transition-delay: 0.4s; }

/* ── HORIZONTAL RULE ACCENT ── */
.ab-hr-accent {
  width: 100%;
  height: 1px;
  background: linear-gradient(90deg, transparent, rgba(var(--ab-accent-rgb),0.3), transparent);
  margin: 0;
  border: none;
}

/* ── RESPONSIVE ── */
@media (max-width: 1200px) {
  .ab-team-grid { grid-template-columns: repeat(2, 1fr); }
  .ab-bento-card-1 { grid-column: 1 / 13; }
  .ab-bento-card-2 { grid-column: 1 / 13; }
  .ab-bento-card-3 { grid-column: 1 / 7; }
  .ab-bento-card-4 { grid-column: 7 / 13; }
}

@media (max-width: 1024px) {
  .ab-hero-split { grid-template-columns: 1fr; min-height: auto; }
  .ab-hero-left { padding: 80px 40px 60px; }
  .ab-hero-right { height: 50vh; min-height: 300px; }
  .ab-hero-morph { width: 320px; height: 320px; }
  .ab-hero-scroll-indicator { display: none; }
  .ab-marquee-item { padding: 0 30px; font-size: 11px; }
  .ab-narrative { padding: 100px 0; }
  .ab-narrative-quote { font-size: clamp(28px, 4vw, 48px); margin-bottom: 80px; }
  .ab-narrative-columns { grid-template-columns: 1fr; gap: 0; }
  .ab-narrative-divider { display: none; }
  .ab-narrative-col { padding: 24px 0; border-bottom: 1px solid rgba(var(--ab-accent-rgb),0.1); }
  .ab-narrative-col:last-child { border-bottom: none; }
  .ab-bento { padding: 80px 0; }
  .ab-timeline { padding: 100px 0 80px; }
  .ab-timeline-header { flex-direction: column; align-items: flex-start; gap: 12px; }
  .ab-team { padding: 100px 0; }
  .ab-cta-split { grid-template-columns: 1fr; }
  .ab-cta-split-left { padding: 60px 40px; }
  .ab-cta-split-right { height: 50vh; min-height: 300px; }
}

@media (max-width: 768px) {
  .ab-hero-left { padding: 60px 24px 40px; }
  .ab-hero-title-mega { font-size: clamp(44px, 14vw, 80px); }
  .ab-hero-tagline { font-size: 13px; margin-top: 24px; }
  .ab-hero-morph { width: 350px; height: 350px; }
  .ab-marquee-strip { padding: 16px 0; }
  .ab-marquee-item { padding: 0 20px; font-size: 10px; letter-spacing: 0.15em; }
  .ab-narrative { padding: 80px 0; }
  .ab-narrative-inner { padding: 0 24px; }
  .ab-narrative-quote { font-size: clamp(24px, 6vw, 36px); margin-bottom: 60px; }
  .ab-narrative-quote::before { font-size: 80px; top: -40px; }
  .ab-narrative-col { padding: 20px 0; }
  .ab-bento { padding: 60px 0; }
  .ab-bento-inner { padding: 0 24px; }
  .ab-bento-header h2 { font-size: clamp(28px, 7vw, 40px); }
  .ab-bento-card-1, .ab-bento-card-2, .ab-bento-card-3, .ab-bento-card-4 { grid-column: 1 / 13; }
  .ab-bento-card { min-height: 320px !important; }
  .ab-bento-card-bg img { height: 320px !important; }
  .ab-bento-card h3 { font-size: 22px; }
  .ab-bento-card p { font-size: 12px; }
  .ab-bento-card-inner { padding: 24px; }
  .ab-timeline { padding: 80px 0 60px; }
  .ab-timeline-inner { padding: 0 24px; }
  .ab-timeline-track { overflow-x: auto; padding-bottom: 30px; scroll-snap-type: x mandatory; -webkit-overflow-scrolling: touch; }
  .ab-timeline-item { flex: 0 0 240px; scroll-snap-align: start; }
  .ab-timeline-item-num { font-size: 40px; }
  .ab-timeline-item h3 { font-size: 16px; }
  .ab-timeline-item p { font-size: 12px; }
  .ab-team { padding: 80px 0; }
  .ab-team-inner { padding: 0 24px; }
  .ab-team-header h2 { font-size: clamp(28px, 7vw, 40px); }
  .ab-team-grid { grid-template-columns: 1fr 1fr; gap: 16px; }
  .ab-team-card { padding: 20px; }
  .ab-team-card-avatar { height: 240px !important; }
  .ab-team-card-avatar img { height: 100% !important; }
  .ab-team-card h3 { font-size: 16px; }
  .ab-team-card p { font-size: 12px; line-height: 1.6; }
  .ab-team-card-role { font-size: 8px; }
  .ab-cta-split-left { padding: 48px 24px; }
  .ab-cta-split-left h2 { font-size: clamp(28px, 7vw, 40px); }
  .ab-cta-split-left p { font-size: 13px; }
  .ab-cta-split-right { height: 50vh; min-height: 300px; }
  .ab-cta-split-btn { padding: 16px 32px; font-size: 10px; }
}

@media (max-width: 480px) {
  .ab-hero-left { padding: 48px 16px 32px; }
  .ab-hero-title-mega { font-size: clamp(40px, 15vw, 64px); }
  .ab-hero-right { height: 35vh; min-height: 250px; }
  .ab-hero-morph { width: 320px; height: 320px; }
  .ab-marquee-item { padding: 0 14px; font-size: 9px; }
  .ab-narrative { padding: 60px 0; }
  .ab-narrative-inner { padding: 0 16px; }
  .ab-narrative-quote { font-size: 22px; margin-bottom: 48px; }
  .ab-narrative-col h3 { font-size: 18px; }
  .ab-narrative-col p { font-size: 12px; }
  .ab-narrative-col-num { font-size: 10px; }
  .ab-bento { padding: 48px 0; }
  .ab-bento-inner { padding: 0 16px; }
  .ab-bento-header { margin-bottom: 48px; }
  .ab-bento-card { min-height: 300px !important; }
  .ab-bento-card-inner { padding: 20px; }
  .ab-bento-card-bg img { height: 300px !important; }
  .ab-timeline { padding: 60px 0 48px; }
  .ab-timeline-inner { padding: 0 16px; }
  .ab-timeline-item { flex: 0 0 200px; }
  .ab-timeline-item-num { font-size: 32px; }
  .ab-team { padding: 60px 0; }
  .ab-team-inner { padding: 0 16px; }
  .ab-team-grid { grid-template-columns: 1fr; gap: 16px; }
  .ab-team-card { padding: 20px; }
  .ab-team-card-avatar { max-width: 220px; margin: 0 auto 20px; }
  .ab-team-card-avatar img { height: 220px !important; }
  .ab-team-card-socials { justify-content: center; }
  .ab-cta-split-left { padding: 40px 16px; }
  .ab-cta-split-right { height: 40vh; min-height: 280px; }
  .ab-cta-split-right img { height: 100% !important; }
  .ab-cta-split-btn { width: 100%; justify-content: center; }

  .ab-cursor, .ab-cursor-dot { display: none !important; }
  .ab-particles { display: none; }
}

/* ── FOOTER OVERRIDE FOR ABOUT PAGE ── */
.ab-page + .aura-footer,
.ab-page ~ .aura-footer,
footer.aura-footer {
  background: linear-gradient(180deg, var(--ab-dark) 0%, #050505 100%) !important;
  border-top: 1px solid rgba(var(--ab-accent-rgb),0.15) !important;
}

.ab-page ~ .aura-footer .aura-footer::before,
footer.aura-footer::before {
  background: linear-gradient(90deg, transparent, rgba(var(--ab-accent-rgb),0.4), transparent) !important;
}

.ab-page ~ .aura-footer .footer-col h4,
footer.aura-footer .footer-col h4 {
  color: var(--ab-accent) !important;
}

.ab-page ~ .aura-footer .footer-links a,
footer.aura-footer .footer-links a {
  color: rgba(255,255,255,0.5) !important;
}

.ab-page ~ .aura-footer .footer-links a:hover,
footer.aura-footer .footer-links a:hover {
  color: var(--ab-accent) !important;
}

.ab-page ~ .aura-footer .footer-feature-item:hover .feature-icon-box,
footer.aura-footer .footer-feature-item:hover .feature-icon-box {
  background: rgba(var(--ab-accent-rgb),0.15) !important;
  border-color: rgba(var(--ab-accent-rgb),0.3) !important;
}

.ab-page ~ .aura-footer .footer-social-links a:hover,
footer.aura-footer .footer-social-links a:hover {
  background: var(--ab-accent) !important;
  border-color: var(--ab-accent) !important;
}

.ab-page ~ .aura-footer .footer-bottom,
footer.aura-footer .footer-bottom {
  border-top: 1px solid rgba(var(--ab-accent-rgb),0.1) !important;
}

.ab-page ~ .aura-footer .footer-bottom-links a:hover,
footer.aura-footer .footer-bottom-links a:hover {
  color: var(--ab-accent) !important;
}

.ab-page ~ .aura-footer .btn-primary,
footer.aura-footer .btn-primary {
  background: var(--ab-accent) !important;
  border-color: var(--ab-accent) !important;
}

.ab-page ~ .aura-footer .btn-primary:hover,
footer.aura-footer .btn-primary:hover {
  background: #b8943f !important;
  box-shadow: 0 8px 30px rgba(var(--ab-accent-rgb),0.3) !important;
}
</style>

<div class="ab-page">

  <!-- CUSTOM CURSOR -->
  <div class="ab-cursor"></div>
  <div class="ab-cursor-dot"></div>

  <!-- ═══ HERO ═══ -->
  <section class="ab-hero-split">
    <div class="ab-hero-left">
      <h1 class="ab-hero-title-mega">
        <span class="ab-reveal">About</span>
        <span class="line-accent ab-reveal ab-reveal-delay-1">Urban</span>
        <span class="line-outline ab-reveal ab-reveal-delay-2">Outfit</span>
      </h1>
      <p class="ab-hero-tagline ab-reveal ab-reveal-delay-3">Urban Outfit Collection is a fashion and clothing store based in Mukerian, Punjab, offering clothing and accessories for men, women and kids.</p>
    </div>
    <div class="ab-hero-right">
      <div class="ab-hero-morph">
        <img src="../images/menhero1.png" alt="Urban Outfit Collection">
      </div>
    </div>
  </section>

  <!-- ═══ MARQUEE STRIP ═══ -->
  <div class="ab-marquee-strip">
    <div class="ab-marquee-track">
      <span class="ab-marquee-item">Men's Clothing</span>
      <span class="ab-marquee-item">Women's Wear</span>
      <span class="ab-marquee-item">Kids Collection</span>
      <span class="ab-marquee-item">Accessories</span>
      <span class="ab-marquee-item">Mukerian, Punjab</span>
      <span class="ab-marquee-item">Men's Clothing</span>
      <span class="ab-marquee-item">Women's Wear</span>
      <span class="ab-marquee-item">Kids Collection</span>
      <span class="ab-marquee-item">Accessories</span>
      <span class="ab-marquee-item">Mukerian, Punjab</span>
    </div>
  </div>

  <!-- ═══ ABOUT SECTION ═══ -->
  <section class="ab-narrative">
    <div class="ab-narrative-inner">
      <div class="ab-narrative-columns">
        <div class="ab-narrative-col ab-reveal">
          <div class="ab-narrative-col-num">01 — ABOUT US</div>
          <h3>Urban Outfit Collection</h3>
          <p>Urban Outfit Collection is a fashion and clothing store based in Mukerian, Punjab, offering clothing and accessories for men, women and kids. Located opposite Sri Guru Nanak Girls Senior Secondary School, Mukerian, the store brings together everyday fashion and contemporary clothing choices for the whole family.</p>
        </div>
        <div class="ab-narrative-divider"></div>
        <div class="ab-narrative-col ab-reveal ab-reveal-delay-1">
          <div class="ab-narrative-col-num">02 — WHAT WE OFFER</div>
          <h3>Something for Everyone</h3>
          <p>From casual everyday looks to outfits for different occasions, Urban Outfit Collection focuses on giving customers a convenient local destination for discovering clothing and accessories in one place. We stock men's, kids, women's and accessories.</p>
        </div>
        <div class="ab-narrative-divider"></div>
        <div class="ab-narrative-col ab-reveal ab-reveal-delay-2">
          <div class="ab-narrative-col-num">03 — VISIT US</div>
          <h3>Find Us in Mukerian</h3>
          <p>Visit us in Mukerian or connect with us on Instagram and Facebook to see our latest collections, new arrivals and updates. Open daily from 9:00 AM to 9:00 PM.</p>
        </div>
      </div>
    </div>
  </section>

  <hr class="ab-hr-accent">

  <!-- ═══ CATEGORIES ═══ -->
  <section class="ab-bento">
    <div class="ab-bento-inner">
      <div class="ab-bento-header ab-reveal">
        <div class="ab-eyebrow-alt">What We Offer</div>
        <h2>Our <em>Categories</em></h2>
      </div>
      <div class="ab-bento-grid">
        <div class="ab-bento-card ab-bento-card-1 ab-reveal">
          <div class="ab-bento-card-bg">
            <img src="../images/menhero1.png" alt="Men's Wear">
          </div>
          <div class="ab-bento-card-inner">
            <div class="ab-bento-card-tag">Men's Wear</div>
            <h3>Men's Clothing</h3>
            <p>Casual and contemporary clothing for men — from everyday basics to statement pieces.</p>
          </div>
        </div>
        <div class="ab-bento-card ab-bento-card-2 ab-reveal ab-reveal-delay-1">
          <div class="ab-bento-card-bg">
            <img src="../images/womenhero1.png" alt="Women's Wear">
          </div>
          <div class="ab-bento-card-inner">
            <div class="ab-bento-card-tag">Women's Wear</div>
            <h3>Women's Clothing</h3>
            <p>Trendy and stylish options for women — fashion that fits every occasion.</p>
          </div>
        </div>
        <div class="ab-bento-card ab-bento-card-3 ab-reveal ab-reveal-delay-2">
          <div class="ab-bento-card-bg">
            <img src="../images/kidshero1.png" alt="Kids Wear">
          </div>
          <div class="ab-bento-card-inner">
            <div class="ab-bento-card-tag">Kids Wear</div>
            <h3>Kids' Clothing</h3>
            <p>Comfortable and fun clothing for kids — because young ones deserve great style too.</p>
          </div>
        </div>

      </div>
    </div>
  </section>

  <hr class="ab-hr-accent">

  <!-- ═══ CONTACT / VISIT US ═══ -->
  <section class="ab-team">
    <div class="ab-team-inner">
      <div class="ab-team-header ab-reveal">
        <div class="ab-eyebrow-alt">Get in Touch</div>
        <h2>Visit <em>Us</em></h2>
      </div>
      <div style="max-width: 700px; margin: 0 auto; text-align: center;">
        <div class="ab-reveal" style="margin-bottom: 40px;">
          <h3 style="font-family: var(--font-display); font-size: 24px; color: rgba(255,255,255,0.9); margin-bottom: 16px;">Urban Outfit Collection</h3>
          <p style="color: rgba(255,255,255,0.6); line-height: 1.8; font-size: 15px;">
            Opp. Sri Guru Nanak Girls Senior Secondary School,<br>
            Mukerian, Punjab 144211
          </p>
        </div>
        <div class="ab-reveal ab-reveal-delay-1" style="display: flex; justify-content: center; gap: 40px; margin-bottom: 40px; flex-wrap: wrap;">
          <div>
            <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.15em; color: var(--ab-accent); margin-bottom: 8px;">Phone</div>
            <a href="tel:+919780704131" style="color: rgba(255,255,255,0.8); text-decoration: none; font-size: 18px; font-family: var(--font-display);">+91 97807 04131</a>
          </div>
          <div>
            <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.15em; color: var(--ab-accent); margin-bottom: 8px;">Rating</div>
            <div style="color: rgba(255,255,255,0.8); font-size: 18px; font-family: var(--font-display);">4.8 / 5 <span style="font-size: 13px; color: rgba(255,255,255,0.4);">(44 reviews)</span></div>
          </div>
          <div>
            <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.15em; color: var(--ab-accent); margin-bottom: 8px;">Hours</div>
            <div style="color: rgba(255,255,255,0.8); font-size: 18px; font-family: var(--font-display);">9 AM – 9 PM</div>
            <div style="color: rgba(255,255,255,0.4); font-size: 12px; margin-top: 4px;">Mon – Sun</div>
          </div>
        </div>
        <div class="ab-reveal ab-reveal-delay-2" style="display: flex; justify-content: center; gap: 16px; flex-wrap: wrap;">
          <a href="https://www.instagram.com/urban_0utfit_mukerian/" target="_blank" rel="noopener" style="display:inline-flex;align-items:center;gap:8px;padding:12px 24px;background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.1);border-radius:8px;color:rgba(255,255,255,0.8);text-decoration:none;font-size:14px;transition:all 0.3s;" onmouseover="this.style.borderColor='#000';this.style.color='#000'" onmouseout="this.style.borderColor='rgba(255,255,255,0.1)';this.style.color='rgba(255,255,255,0.8)'">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="5"/><circle cx="17.5" cy="6.5" r="1.5"/></svg>
            @urban_0utfit_mukerian
          </a>
          <a href="https://www.facebook.com/urban.outfit.2025" target="_blank" rel="noopener" style="display:inline-flex;align-items:center;gap:8px;padding:12px 24px;background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.1);border-radius:8px;color:rgba(255,255,255,0.8);text-decoration:none;font-size:14px;transition:all 0.3s;" onmouseover="this.style.borderColor='#000';this.style.color='#000'" onmouseout="this.style.borderColor='rgba(255,255,255,0.1)';this.style.color='rgba(255,255,255,0.8)'">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
            Urban Outfit
          </a>
        </div>
      </div>
    </div>
  </section>

</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  // Custom cursor
  const cursor = document.querySelector('.ab-cursor');
  const cursorDot = document.querySelector('.ab-cursor-dot');
  
  document.addEventListener('mousemove', (e) => {
    cursor.style.left = e.clientX - 10 + 'px';
    cursor.style.top = e.clientY - 10 + 'px';
    cursorDot.style.left = e.clientX - 3 + 'px';
    cursorDot.style.top = e.clientY - 3 + 'px';
  });

  // Hover effect on interactive elements
  const hoverTargets = document.querySelectorAll('a, .ab-bento-card, .ab-team-card, .ab-cta-split-btn');
  hoverTargets.forEach(el => {
    el.addEventListener('mouseenter', () => cursor.classList.add('hovering'));
    el.addEventListener('mouseleave', () => cursor.classList.remove('hovering'));
  });

  // Scroll reveal
  const revealElements = document.querySelectorAll('.ab-reveal');
  
  const revealOnScroll = () => {
    revealElements.forEach(el => {
      const rect = el.getBoundingClientRect();
      const windowHeight = window.innerHeight;
      if (rect.top < windowHeight * 0.85) {
        el.classList.add('visible');
      }
    });
  };

  window.addEventListener('scroll', revealOnScroll);
  revealOnScroll();

  // Parallax on hero morph
  const heroMorph = document.querySelector('.ab-hero-morph');
  window.addEventListener('scroll', () => {
    const scrolled = window.pageYOffset;
    if (heroMorph && scrolled < window.innerHeight) {
      heroMorph.style.transform = `translate(-50%, calc(-50% + ${scrolled * 0.3}px))`;
    }
  });

  // Hide cursor on mobile
  if ('ontouchstart' in window || window.innerWidth < 768) {
    if (cursor) cursor.style.display = 'none';
    if (cursorDot) cursorDot.style.display = 'none';
  }
});
</script>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
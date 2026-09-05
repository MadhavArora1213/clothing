<?php
require_once dirname(__DIR__) . '/config/database.php';
$pageTitle       = 'Contact Us — Urban Outfit Collection | Customer Support';
$pageDescription = 'Reach out to Urban Outfit Collection for order support, returns, product queries or collaborations. We respond within 24 hours. Email, phone & WhatsApp support available.';
$pageKeywords    = 'contact urban outfit, customer support fashion india, clothing brand contact, order help india';
$pageCanonical   = 'https://urbanoutfitshop.com/pages/contact.php';
include dirname(__DIR__) . '/includes/header.php';

$error = '';
$success = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  // 1. Honeypot check (bot detection)
  if (isHoneypotFilled()) {
    $success = 'Thank you for contacting us. We will get back to you within 24 hours.';
    goto skipProcessing;
  }

  // 2. Rate limiting: max 5 submissions per IP in 5 minutes
  if (rateLimit('contact_form', 5, 300)) {
    $error = 'Too many attempts. Please wait 5 minutes before trying again.';
    goto skipProcessing;
  }

  // 3. CSRF token validation
  if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
    $error = 'Invalid form submission. Please refresh and try again.';
    goto skipProcessing;
  }

  // 4. Sanitize all inputs (strip tags, control chars, enforce max length)
  $name    = sanitizeInput($_POST['name'] ?? '', 100);
  $email   = strtolower(trim($_POST['email'] ?? ''));
  $phone   = sanitizeInput($_POST['phone'] ?? '', 15);
  $subject = sanitizeInput($_POST['subject'] ?? '', 200);
  $message = sanitizeInput($_POST['message'] ?? '', 2000);

  // 5. Validate with regex
  if (!validateName($name)) {
    $errors[] = 'Name must be 2-100 characters (letters, spaces, hyphens only).';
  }

  if (!validateEmail($email)) {
    $errors[] = 'Please enter a valid email address.';
  }

  if (!validatePhone($phone)) {
    $errors[] = 'Phone must be a valid 10-digit Indian number (starting with 6-9).';
  }

  if (!validateSubject($subject)) {
    $errors[] = 'Subject must be 2-200 characters.';
  }

  if (!validateMessage($message)) {
    $errors[] = 'Message must be between 5 and 2000 characters.';
  }

  // 6. Check for spam patterns
  $spamPatterns = ['/viagra|casino|lottery|bitcoin|crypto|click here|buy now|free money/i'];
  $combinedInput = $name . ' ' . $email . ' ' . $subject . ' ' . $message;
  foreach ($spamPatterns as $pattern) {
    if (preg_match($pattern, $combinedInput)) {
      $errors[] = 'Your submission was flagged as spam.';
      break;
    }
  }

  // 7. Check for excessive links (spam indicator)
  if (preg_match_all('/https?:\/\//', $combinedInput) > 3) {
    $errors[] = 'Too many links in your message.';
  }

  // 8. If validation passed, insert
  if (empty($errors)) {
    // Regenerate CSRF token after successful use
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

    $stmt = $mysqli->prepare('INSERT INTO enquiries (name, email, phone, subject, message, status, created_at) VALUES (?, ?, ?, ?, ?, "new", NOW())');
    $stmt->bind_param('sssss', $name, $email, $phone, $subject, $message);
    $stmt->execute();

    $success = 'Thank you for contacting us. We will get back to you within 24 hours.';
    $_POST = []; // Clear form data after successful submit
  } else {
    $error = implode('<br>', $errors);
  }

  skipProcessing:
}

// Generate fresh CSRF token for form
$csrfToken = generateCSRFToken();
?>

<!-- Honeypot field (hidden from humans, visible to bots) -->
<style>.hp-field{position:absolute;left:-9999px;top:-9999px;opacity:0;height:0;width:0;overflow:hidden;pointer-events:none;tabindex:-1;}</style>

<style>
.ct-page { padding-top: calc(var(--header-height, 80px) + 10px); }

/* ── MAIN CONTACT SECTION (full viewport) ── */
.ct-main {
  display: grid;
  grid-template-columns: 1fr 1fr;
  min-height: calc(100vh - var(--header-height, 80px) - 50px);
  background: var(--color-bg);
}

/* ── LEFT: FORM SIDE ── */
.ct-left {
  display: flex;
  flex-direction: column;
  justify-content: center;
  padding: 48px 48px 48px 64px;
  position: relative;
}
.ct-breadcrumb {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.14em;
  color: var(--color-text-muted);
  margin-bottom: 28px;
}
.ct-breadcrumb a { color: var(--color-text-muted); transition: color 0.3s; }
.ct-breadcrumb a:hover { color: var(--color-accent); }
.ct-breadcrumb svg { color: var(--color-accent); }

.ct-eyebrow {
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
.ct-eyebrow::before { content: ''; width: 24px; height: 2px; background: var(--color-accent); }

.ct-title {
  font-family: var(--font-display);
  font-size: clamp(30px, 3.5vw, 44px);
  font-weight: 400;
  line-height: 1.1;
  letter-spacing: -0.03em;
  color: var(--color-text-main);
  margin-bottom: 6px;
}
.ct-title em { font-style: italic; color: var(--color-accent); }

/* Alert */
.ct-alert {
  padding: 10px 14px;
  border-radius: var(--radius-sm);
  font-size: 13px;
  font-weight: 500;
  margin-bottom: 16px;
  border-left: 3px solid;
}
.ct-alert-error { background: #FEF2F2; color: #991B1B; border-color: #DC2626; }
.ct-alert-success { background: #F0FDF4; color: #166534; border-color: #22C55E; }

/* Form */
.ct-form { margin-top: 20px; }
.ct-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}
.ct-fld {
  margin-bottom: 12px;
}
.ct-fld label {
  display: block;
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  color: var(--color-text-main);
  margin-bottom: 5px;
}
.ct-fld label span { color: var(--color-accent); }
.ct-fld input,
.ct-fld textarea {
  width: 100%;
  padding: 11px 14px;
  border: 1.5px solid #E5E5E5;
  border-radius: 0;
  font-size: 14px;
  font-family: var(--font-body);
  color: var(--color-text-main);
  background: #FAF9F6;
  transition: all 0.3s ease;
  outline: none;
}
.ct-fld input::placeholder,
.ct-fld textarea::placeholder { color: #AAA; }
.ct-fld input:focus,
.ct-fld textarea:focus {
  border-color: var(--color-text-main);
  background: #fff;
}
.ct-fld textarea { resize: vertical; min-height: 80px; }
.ct-submit {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  padding: 13px 36px;
  background: var(--color-text-main);
  color: #fff;
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.14em;
  border: none;
  border-radius: 0;
  cursor: pointer;
  transition: all 0.4s cubic-bezier(0.16,1,0.3,1);
  margin-top: 4px;
}
.ct-submit:hover {
  background: var(--color-accent);
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(212,175,55,0.3);
}
.ct-submit svg { transition: transform 0.3s; }
.ct-submit:hover svg { transform: translateX(4px); }

/* ── RIGHT: IMAGES + INFO ── */
.ct-right {
  position: relative;
  display: grid;
  grid-template-rows: 1fr auto;
  overflow: hidden;
}
.ct-img-area {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 40px 40px 40px 0;
  background: #F5F3EF;
  overflow: hidden;
}
.ct-imgs {
  position: relative;
  width: 100%;
  max-width: 420px;
  height: 440px;
}
.ct-img-main {
  position: absolute;
  top: 0; right: 0;
  width: 82%;
  height: 80%;
  object-fit: cover;
  border-radius: 100px 12px 12px 12px;
  box-shadow: 0 16px 50px rgba(0,0,0,0.12);
}
.ct-img-sm {
  position: absolute;
  bottom: 0; left: 0;
  width: 48%;
  height: 44%;
  object-fit: cover;
  border-radius: 12px 12px 12px 100px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.15);
  border: 4px solid #F5F3EF;
}
.ct-img-badge {
  position: absolute;
  top: 50%; right: 8px;
  transform: translateY(-50%);
  background: var(--color-text-main);
  color: #fff;
  padding: 14px 16px;
  text-align: center;
  border-radius: var(--radius-sm);
  box-shadow: 0 8px 24px rgba(0,0,0,0.2);
  z-index: 2;
}
.ct-img-badge h3 { font-family: var(--font-display); font-size: 13px; margin-bottom: 2px; letter-spacing: 0.06em; }
.ct-img-badge p { font-size: 9px; opacity: 0.6; text-transform: uppercase; letter-spacing: 0.12em; }

/* Bottom info strip on right */
.ct-info-strip {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  background: #0F0F0F;
}
.ct-info-item {
  padding: 22px 20px;
  text-align: center;
  border-right: 1px solid rgba(255,255,255,0.06);
}
.ct-info-item:last-child { border-right: none; }
.ct-info-item svg { margin-bottom: 8px; color: var(--color-accent); }
.ct-info-item h4 {
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: #fff;
  margin-bottom: 3px;
}
.ct-info-item p {
  font-size: 12px;
  color: rgba(255,255,255,0.55);
  line-height: 1.4;
}
.ct-info-item a {
  display: inline-block;
  margin-top: 6px;
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  color: var(--color-accent);
  transition: color 0.3s;
}
.ct-info-item a:hover { color: #fff; }

/* ── WHATSAPP FAB ── */
.ct-wa {
  position: fixed;
  bottom: 24px; right: 24px;
  width: 54px; height: 54px;
  background: #25D366;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #fff;
  box-shadow: 0 4px 20px rgba(37,211,102,0.4);
  z-index: 999;
  transition: all 0.3s;
}
.ct-wa:hover { transform: scale(1.1); box-shadow: 0 6px 28px rgba(37,211,102,0.5); }

/* ── RESPONSIVE ── */
@media (max-width: 1024px) {
  .ct-main { grid-template-columns: 1fr; min-height: auto; }
  .ct-left { padding: 48px 32px; }
  .ct-right { min-height: 420px; }
  .ct-img-area { padding: 40px 32px; }
  .ct-info-strip { grid-template-columns: 1fr; }
  .ct-info-item { border-right: none; border-bottom: 1px solid rgba(255,255,255,0.06); padding: 18px 20px; }
  .ct-info-item:last-child { border-bottom: none; }
}
@media (max-width: 640px) {
  .ct-left { padding: 36px 20px; }
  .ct-row { grid-template-columns: 1fr; }
  .ct-img-area { padding: 30px 20px; min-height: 320px; }
  .ct-imgs { max-width: 280px; height: 340px; }
  .ct-img-main { border-radius: 70px 8px 8px 8px; }
  .ct-img-sm { border-radius: 8px 8px 8px 70px; }
}
</style>

<!-- Footer Color Override -->
<style>
.aura-footer {
  background: linear-gradient(180deg, #0F0F0F 0%, #050505 100%) !important;
  border-top: 1px solid rgba(201,168,76,0.15) !important;
}
.aura-footer::before {
  background: linear-gradient(90deg, transparent, rgba(201,168,76,0.4), transparent) !important;
}
.aura-footer .footer-col h4 { color: #c9a84c !important; }
.aura-footer .footer-links a { color: rgba(255,255,255,0.5) !important; }
.aura-footer .footer-links a:hover { color: #c9a84c !important; }
.aura-footer .footer-feature-item:hover .feature-icon-box {
  background: rgba(201,168,76,0.15) !important;
  border-color: rgba(201,168,76,0.3) !important;
}
.aura-footer .footer-social-links a:hover {
  background: #c9a84c !important;
  border-color: #c9a84c !important;
}
.aura-footer .footer-bottom {
  border-top: 1px solid rgba(201,168,76,0.1) !important;
}
.aura-footer .footer-bottom-links a:hover { color: #c9a84c !important; }
.aura-footer .btn-primary {
  background: #c9a84c !important;
  border-color: #c9a84c !important;
}
.aura-footer .btn-primary:hover {
  background: #b8943f !important;
  box-shadow: 0 8px 30px rgba(201,168,76,0.3) !important;
}
</style>

<!-- ═══ SINGLE FULL-VIEWPORT CONTACT ═══ -->
<section class="ct-main">

  <!-- LEFT: FORM -->
  <div class="ct-left">
    <div class="ct-breadcrumb">
      <a href="<?= BASE_URL ?>/index.php">Home</a>
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
      <span>Contact</span>
    </div>

    <span class="ct-eyebrow">Get In Touch</span>
    <h1 class="ct-title">Let's Start a<br><em>Conversation</em></h1>

    <?php if ($error): ?>
      <div class="ct-alert ct-alert-error" style="margin-top:16px;"><?= $error ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
      <div class="ct-alert ct-alert-success" style="margin-top:16px;"><?= $success ?></div>
    <?php endif; ?>

    <form method="POST" class="ct-form" action="" autocomplete="off">
      <?= getCSRFInput() ?>
      <div class="hp-field" aria-hidden="true">
        <label>Do not fill this</label>
        <input type="text" name="website_url" tabindex="-1" autocomplete="off">
      </div>
      <div class="ct-row">
        <div class="ct-fld">
          <label>Full Name <span>*</span></label>
          <input type="text" name="name" required placeholder="Your name" maxlength="100" pattern="[a-zA-Z\s.\-']+" title="Letters, spaces, hyphens only" value="<?= sanitize($_POST['name'] ?? '') ?>">
        </div>
        <div class="ct-fld">
          <label>Email <span>*</span></label>
          <input type="email" name="email" required placeholder="you@email.com" maxlength="254" value="<?= sanitize($_POST['email'] ?? '') ?>">
        </div>
      </div>
      <div class="ct-row">
        <div class="ct-fld">
          <label>Phone</label>
          <input type="tel" name="phone" placeholder="+91 98765 43210" maxlength="15" pattern="[6-9]\d{9}" title="10-digit Indian mobile number" value="<?= sanitize($_POST['phone'] ?? '') ?>">
        </div>
        <div class="ct-fld">
          <label>Subject <span>*</span></label>
          <input type="text" name="subject" required placeholder="How can we help?" maxlength="200" value="<?= sanitize($_POST['subject'] ?? '') ?>">
        </div>
      </div>
      <div class="ct-fld">
        <label>Message <span>*</span></label>
        <textarea name="message" rows="3" required placeholder="Tell us more..." maxlength="2000" minlength="5"><?= sanitize($_POST['message'] ?? '') ?></textarea>
      </div>
      <button type="submit" class="ct-submit">
        <span>Send Message</span>
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
      </button>
    </form>
  </div>

  <!-- RIGHT: IMAGES + INFO -->
  <div class="ct-right">
    <div class="ct-img-area">
      <div class="ct-imgs">
        <img class="ct-img-main" src="https://images.unsplash.com/photo-1469334031218-e382a71b716b?w=800&auto=format&fit=crop&q=80" alt="Fashion Model">
        <img class="ct-img-sm" src="https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=600&auto=format&fit=crop&q=80" alt="Model in Streetwear">
        <div class="ct-img-badge">
          <h3>URBAN OUTFIT</h3>
          <p>Designed in India</p>
        </div>
      </div>
    </div>

    <div class="ct-info-strip">
      <div class="ct-info-item">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
        <h4>Email</h4>
        <p><?= sanitize(getSetting('site_email', 'hello@auraco.in')) ?></p>
      </div>
      <div class="ct-info-item">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
        <h4>Phone</h4>
        <p><?= sanitize(getSetting('site_phone', '+91 90237 21805')) ?></p>
        <a href="tel:<?= sanitize(getSetting('site_phone', '+919023721805')) ?>">Call Now</a>
      </div>
      <div class="ct-info-item">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
        <h4>Visit Us</h4>
        <p><?= sanitize(getSetting('site_address', 'Mumbai, India')) ?></p>
        <a href="#">Get Directions</a>
      </div>
    </div>
  </div>

</section>

<!-- WhatsApp FAB -->
<a href="https://wa.me/919023721805" target="_blank" class="ct-wa">
  <svg width="26" height="26" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
</a>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>

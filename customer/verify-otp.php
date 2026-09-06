<?php
require_once dirname(__DIR__) . '/config/database.php';

$email = $_SESSION['pending_verify_email'] ?? null;
if (!$email) {
  redirect('/customer/register.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
    $error = 'Invalid request. Please try again.';
  } elseif (isset($_POST['resend_otp'])) {
    if (rateLimit('otp_resend_' . $email, 3, 300)) {
      $error = 'Too many resend attempts. Please wait 5 minutes.';
    } else {
      $stmt = $mysqli->prepare('SELECT first_name FROM customers WHERE email = ? AND is_verified = 0');
      $stmt->bind_param('s', $email);
      $stmt->execute();
      $customer = $stmt->get_result()->fetch_assoc();

      if ($customer) {
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $otpExpiry = date('Y-m-d H:i:s', strtotime('+10 minutes'));

        $upd = $mysqli->prepare('UPDATE customers SET otp = ?, otp_expiry = ? WHERE email = ? AND is_verified = 0');
        $upd->bind_param('sss', $otp, $otpExpiry, $email);
        $upd->execute();

        $otpHtml = '
        <!DOCTYPE html>
        <html>
        <head><meta charset="UTF-8"></head>
        <body style="margin:0;padding:0;background:#f4f4f4;font-family:Arial,sans-serif;">
          <div style="max-width:500px;margin:40px auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,0.08);">
            <div style="background:#0f172a;padding:30px;text-align:center;">
              <h1 style="color:#D4AF37;font-size:24px;margin:0;">Urban Outfit Collection</h1>
            </div>
            <div style="padding:30px;text-align:center;">
              <h2 style="color:#0f172a;font-size:20px;margin:0 0 16px;">Verify Your Email</h2>
              <p style="color:#555;font-size:14px;line-height:1.6;">Here is your new OTP. This code expires in 10 minutes.</p>
              <div style="margin:30px 0;">
                <span style="display:inline-block;font-size:36px;font-weight:700;letter-spacing:12px;color:#0f172a;background:#f8f8f8;padding:16px 28px;border-radius:8px;border:2px dashed #D4AF37;">' . $otp . '</span>
              </div>
              <p style="color:#999;font-size:12px;line-height:1.5;">If you did not create an account, please ignore this email.</p>
              <hr style="border:none;border-top:1px solid #eee;margin:20px 0;">
              <p style="color:#aaa;font-size:11px;text-align:center;">Urban Outfit Collection — Fashion E-Commerce</p>
            </div>
          </div>
        </body>
        </html>';

        $otpText = "Your OTP for Urban Outfit Collection: {$otp}\nThis code expires in 10 minutes.";
        sendEmail($email, 'Verify Your Email — Urban Outfit Collection', $otpHtml, $otpText);

        $success = 'A new OTP has been sent to your email.';
      }
    }
  } else {
    $otpInput = trim($_POST['otp'] ?? '');

    if (empty($otpInput)) {
      $error = 'Please enter the OTP.';
    } elseif (strlen($otpInput) !== 6) {
      $error = 'OTP must be 6 digits.';
    } else {
      $stmt = $mysqli->prepare('SELECT id, first_name, last_name, otp, otp_expiry FROM customers WHERE email = ? AND is_verified = 0');
      $stmt->bind_param('s', $email);
      $stmt->execute();
      $customer = $stmt->get_result()->fetch_assoc();

      if (!$customer) {
        $error = 'Account not found or already verified.';
      } elseif ($customer['otp'] !== $otpInput) {
        $error = 'Invalid OTP. Please try again.';
      } elseif (strtotime($customer['otp_expiry']) < time()) {
        $error = 'OTP has expired. Please request a new one.';
      } else {
        $upd = $mysqli->prepare('UPDATE customers SET is_verified = 1, otp = NULL, otp_expiry = NULL WHERE id = ?');
        $upd->bind_param('i', $customer['id']);
        $upd->execute();

        unset($_SESSION['pending_verify_email']);

        session_regenerate_id(true);
        $_SESSION['customer_id'] = $customer['id'];
        $_SESSION['customer_name'] = $customer['first_name'] . ' ' . $customer['last_name'];

        redirect('/customer/account.php');
      }
    }
  }
}

$pageTitle       = 'Verify Email — Urban Outfit Collection';
$pageDescription = 'Enter the OTP sent to your email to verify your account.';
$pageRobots      = 'noindex, nofollow';
include dirname(__DIR__) . '/includes/header.php';
?>

<style>
  .verify-split {
    display: grid;
    grid-template-columns: 1fr 1fr;
    min-height: calc(100vh - var(--header-height));
    margin-top: calc(-1 * var(--space-6));
  }
  .verify-left {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    min-height: 700px;
  }
  .verify-left-bg {
    position: absolute;
    inset: 0;
    background: url('https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=800&h=1000&fit=crop') center/cover no-repeat;
  }
  .verify-left-bg::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(160deg, rgba(15,15,15,0.75) 0%, rgba(15,15,15,0.3) 50%, rgba(212,175,55,0.15) 100%);
  }
  .verify-left-content {
    position: relative;
    z-index: 2;
    text-align: center;
    padding: var(--space-10);
    max-width: 400px;
  }
  .verify-left-brand {
    font-family: var(--font-display);
    font-size: clamp(36px, 4vw, 52px);
    font-weight: 700;
    color: #fff;
    letter-spacing: 0.04em;
    margin-bottom: var(--space-3);
  }
  .verify-left-tagline {
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.18em;
    color: var(--color-accent);
    margin-bottom: var(--space-6);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
  }
  .verify-left-tagline::before,
  .verify-left-tagline::after {
    content: '';
    width: 32px;
    height: 1px;
    background: var(--color-accent);
  }
  .verify-left-desc {
    color: rgba(255,255,255,0.7);
    font-size: 14px;
    line-height: 1.7;
  }

  .verify-right {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: var(--space-10);
    background: var(--color-bg);
  }
  .verify-form-wrap {
    width: 100%;
    max-width: 420px;
  }
  .verify-form-header {
    text-align: center;
    margin-bottom: var(--space-6);
  }
  .verify-form-header .brand {
    font-family: var(--font-display);
    font-size: 22px;
    font-weight: 700;
    color: var(--color-text-main);
    margin-bottom: var(--space-2);
  }
  .verify-form-header h1 {
    font-family: var(--font-display);
    font-size: 26px;
    font-weight: 700;
    color: var(--color-text-main);
    margin-bottom: var(--space-2);
  }
  .verify-form-header p {
    color: var(--color-text-muted);
    font-size: 14px;
  }

  .otp-input-group {
    display: flex;
    gap: 10px;
    justify-content: center;
    margin-bottom: var(--space-4);
  }
  .otp-input-group input {
    width: 52px;
    height: 56px;
    text-align: center;
    font-size: 22px;
    font-weight: 700;
    border: 1px solid var(--color-border);
    border-radius: var(--radius-sm);
    background: var(--color-surface);
    color: var(--color-text-main);
    font-family: var(--font-body);
    transition: var(--transition);
    box-sizing: border-box;
  }
  .otp-input-group input:focus {
    outline: none;
    border-color: var(--color-accent);
    box-shadow: 0 0 0 3px rgba(212,175,55,0.12);
  }

  .verify-submit {
    width: 100%;
    padding: 14px;
    background: var(--color-text-main);
    color: #fff;
    border: none;
    border-radius: var(--radius-sm);
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition);
    font-family: var(--font-body);
    margin-top: var(--space-3);
  }
  .verify-submit:hover { background: #333; transform: translateY(-1px); }

  .resend-link {
    text-align: center;
    margin-top: var(--space-4);
    font-size: 13px;
    color: var(--color-text-muted);
  }
  .resend-link button {
    background: none;
    border: none;
    color: var(--color-accent);
    font-weight: 600;
    cursor: pointer;
    font-size: 13px;
    font-family: var(--font-body);
    text-decoration: underline;
    padding: 0;
  }
  .resend-link button:hover { opacity: 0.7; }

  .verify-error {
    background: #FEF2F2;
    border: 1px solid #FECACA;
    color: #991B1B;
    padding: 12px 16px;
    border-radius: var(--radius-sm);
    font-size: 13px;
    font-weight: 500;
    margin-bottom: var(--space-4);
    display: flex;
    align-items: center;
    gap: 8px;
  }
  .verify-success {
    background: #F0FDF4;
    border: 1px solid #BBF7D0;
    color: #166534;
    padding: 12px 16px;
    border-radius: var(--radius-sm);
    font-size: 13px;
    font-weight: 500;
    margin-bottom: var(--space-4);
    display: flex;
    align-items: center;
    gap: 8px;
  }

  @media (max-width: 900px) {
    .verify-split { grid-template-columns: 1fr; }
    .verify-left { display: none; }
    .verify-right { min-height: calc(100vh - var(--header-height)); }
  }
</style>

<div class="verify-split">
  <div class="verify-left">
    <div class="verify-left-bg"></div>
    <div class="verify-left-content">
      <div class="verify-left-brand">ATELIER</div>
      <div class="verify-left-tagline">Almost There</div>
      <p class="verify-left-desc">
        We've sent a verification code to your email. Enter it below to complete your registration.
      </p>
    </div>
  </div>

  <div class="verify-right">
    <div class="verify-form-wrap">
      <div class="verify-form-header">
        <div class="brand">ATELIER</div>
        <h1>Verify Email</h1>
        <p>Enter the 6-digit OTP sent to <strong><?= sanitize($email) ?></strong></p>
      </div>

      <?php if ($error): ?>
        <div class="verify-error">
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M15 9l-6 6M9 9l6 6"/></svg>
          <?= sanitize($error) ?>
        </div>
      <?php endif; ?>

      <?php if ($success): ?>
        <div class="verify-success">
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><path d="M22 4L12 14.01l-3-3"/></svg>
          <?= sanitize($success) ?>
        </div>
      <?php endif; ?>

      <form method="POST" id="otpForm">
        <?= getCSRFInput() ?>
        <div class="otp-input-group">
          <input type="text" maxlength="1" class="otp-digit" data-index="0" inputmode="numeric" pattern="[0-9]" autocomplete="one-time-code" required>
          <input type="text" maxlength="1" class="otp-digit" data-index="1" inputmode="numeric" pattern="[0-9]" required>
          <input type="text" maxlength="1" class="otp-digit" data-index="2" inputmode="numeric" pattern="[0-9]" required>
          <input type="text" maxlength="1" class="otp-digit" data-index="3" inputmode="numeric" pattern="[0-9]" required>
          <input type="text" maxlength="1" class="otp-digit" data-index="4" inputmode="numeric" pattern="[0-9]" required>
          <input type="text" maxlength="1" class="otp-digit" data-index="5" inputmode="numeric" pattern="[0-9]" required>
        </div>
        <input type="hidden" name="otp" id="otpHidden">
        <button type="submit" class="verify-submit">Verify Account</button>
      </form>

      <div class="resend-link">
        Didn't receive the code?
        <form method="POST" style="display:inline">
          <?= getCSRFInput() ?>
          <input type="hidden" name="resend_otp" value="1">
          <button type="submit">Resend OTP</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const digits = document.querySelectorAll('.otp-digit');
  const hidden = document.getElementById('otpHidden');
  const form = document.getElementById('otpForm');

  digits.forEach((input, i) => {
    input.addEventListener('input', function() {
      this.value = this.value.replace(/[^0-9]/g, '');
      if (this.value && i < digits.length - 1) {
        digits[i + 1].focus();
      }
      updateHidden();
    });
    input.addEventListener('keydown', function(e) {
      if (e.key === 'Backspace' && !this.value && i > 0) {
        digits[i - 1].focus();
      }
    });
    input.addEventListener('paste', function(e) {
      e.preventDefault();
      const text = (e.clipboardData || window.clipboardData).getData('text').replace(/[^0-9]/g, '');
      for (let j = 0; j < Math.min(text.length, 6); j++) {
        digits[j].value = text[j];
      }
      if (text.length >= 6) {
        digits[5].focus();
      } else if (text.length > 0) {
        digits[Math.min(text.length, 5)].focus();
      }
      updateHidden();
    });
  });

  function updateHidden() {
    hidden.value = Array.from(digits).map(d => d.value).join('');
  }

  form.addEventListener('submit', function(e) {
    updateHidden();
    if (hidden.value.length !== 6) {
      e.preventDefault();
      alert('Please enter the complete 6-digit OTP.');
    }
  });

  digits[0].focus();
});
</script>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>

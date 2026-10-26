<?php
/**
 * ─── Order Confirmation Email (Resend) ───
 *
 * Sends a branded order confirmation once an order is confirmed AND payment
 * is complete. Safe to call from every place that confirms payment
 * (Cashfree webhook, payment callback, order-success page, orders page):
 * orders.email_sent_at acts as a one-time guard so each order is emailed
 * exactly once no matter how many code paths confirm it.
 *
 * Set ORDER_EMAIL_OVERRIDE in .env to redirect every order email to a test
 * inbox (currently rs4065758@gmail.com). Remove that line to email the
 * customer from the order directly.
 */

if (!function_exists('uo_order_email_env')) {
  /** Read a value from .env, loading the file once if nothing loaded it yet. */
  function uo_order_email_env($key, $default = '') {
    static $loaded = false;
    if (!$loaded && empty($_ENV['SMTP_PASS'])) {
      $envFile = dirname(__DIR__) . '/.env';
      if (file_exists($envFile)) {
        foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
          if (strpos(trim($line), '#') === 0) continue;
          $pos = strpos($line, '=');
          if ($pos === false) continue;
          $k = trim(substr($line, 0, $pos));
          $v = trim(substr($line, $pos + 1));
          if (($v[0] ?? '') === '"' && substr($v, -1) === '"') $v = substr($v, 1, -1);
          if ($k !== '') $_ENV[$k] = $v;
        }
      }
      $loaded = true;
    }
    return $_ENV[$key] ?? $default;
  }
}

if (!function_exists('uo_ensure_order_email_column')) {
  /** Make sure the one-time-send guard column exists on orders. */
  function uo_ensure_order_email_column($db) {
    $check = @$db->query("SHOW COLUMNS FROM orders LIKE 'email_sent_at'");
    if ($check && $check->num_rows === 0) {
      @$db->query("ALTER TABLE orders ADD COLUMN email_sent_at DATETIME NULL AFTER notes");
    }
  }
}

if (!function_exists('sendOrderConfirmationEmail')) {
  /**
   * Send the order confirmation email for an order.
   *
   * @param mysqli $db      Open connection (used for order data + send guard).
   * @param int    $orderId orders.id
   * @return bool true if the email was sent (or already sent before), false on failure.
   */
  function sendOrderConfirmationEmail($db, $orderId) {
    $orderId = (int)$orderId;
    if (!$db || $db->connect_error || $orderId <= 0) {
      error_log("sendOrderConfirmationEmail: invalid db/order for id={$orderId}");
      return false;
    }

    $apiKey = defined('SMTP_PASS') ? SMTP_PASS : uo_order_email_env('SMTP_PASS', '');
    $fromName = defined('SMTP_FROM_NAME') ? SMTP_FROM_NAME : uo_order_email_env('SMTP_FROM_NAME', 'Urban Outfit Collection');
    $from = defined('SMTP_FROM') ? SMTP_FROM : uo_order_email_env('SMTP_FROM', '');

    if (empty($apiKey) || strpos($apiKey, 're_') !== 0) {
      error_log('sendOrderConfirmationEmail: Resend API key not configured in .env');
      return false;
    }
    if (empty($from)) {
      error_log('sendOrderConfirmationEmail: SMTP_FROM not configured in .env');
      return false;
    }

    uo_ensure_order_email_column($db);

    // ── Claim this send (atomic) — blocks duplicate emails when several
    //    code paths confirm the same payment (webhook + callback + pages).
    $claim = $db->prepare('UPDATE orders SET email_sent_at = NOW() WHERE id = ? AND email_sent_at IS NULL');
    if (!$claim) {
      error_log('sendOrderConfirmationEmail: claim query failed: ' . $db->error);
      return false;
    }
    $claim->bind_param('i', $orderId);
    $claim->execute();
    $claimed = $claim->affected_rows === 1;
    $claim->close();
    if (!$claimed) {
      // Already sent for this order — nothing to do.
      return true;
    }

    // ── Load order + items ──
    $stmt = $db->prepare('SELECT * FROM orders WHERE id = ?');
    if (!$stmt) { sendOrderConfirmationEmailRelease($db, $orderId); return false; }
    $stmt->bind_param('i', $orderId);
    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$order) {
      sendOrderConfirmationEmailRelease($db, $orderId);
      return false;
    }

    $items = [];
    $ist = $db->prepare('SELECT product_name, size, quantity, unit_price, total_price FROM order_items WHERE order_id = ? ORDER BY id');
    if ($ist) {
      $ist->bind_param('i', $orderId);
      $ist->execute();
      $items = $ist->get_result()->fetch_all(MYSQLI_ASSOC);
      $ist->close();
    }

    // ── Recipient: override inbox while testing, otherwise the customer ──
    $override = defined('ORDER_EMAIL_OVERRIDE') ? ORDER_EMAIL_OVERRIDE : uo_order_email_env('ORDER_EMAIL_OVERRIDE', '');
    $to = $override !== '' ? $override : ($order['customer_email'] ?? '');
    if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
      error_log("sendOrderConfirmationEmail: no valid recipient for order {$orderId} (got '{$to}')");
      sendOrderConfirmationEmailRelease($db, $orderId);
      return false;
    }

    // ── Build email ──
    $money  = fn($v) => '&#8377;' . number_format((float)$v, 2, '.', ',');
    $brand  = $fromName;
    $oNum   = htmlspecialchars($order['order_number'], ENT_QUOTES, 'UTF-8');
    $name   = htmlspecialchars($order['customer_name'] ?? 'there', ENT_QUOTES, 'UTF-8');
    $placed = date('d M Y, h:i A', strtotime($order['created_at']));

    $payLabel = ['online' => 'Online Payment (UPI / Card / Netbanking)', 'cod' => 'Cash on Delivery', 'upi' => 'UPI']
              [$order['payment_method'] ?? ''] ?? ucfirst($order['payment_method'] ?? 'Online');

    $ship = $order['shipping_address'] ?? '';
    if (is_string($ship) && $ship !== '') {
      $ship = json_decode($ship, true) ?: [];
    }
    if (!is_array($ship)) $ship = [];
    $shipLines = array_filter([
      $ship['address'] ?? '',
      trim(($ship['city'] ?? '') . ', ' . ($ship['state'] ?? '') . ' ' . ($ship['postal_code'] ?? '')),
      $ship['phone']  ?? '',
    ], fn($l) => trim((string)$l) !== '');

    // Item rows
    $itemRows = '';
    foreach ($items as $it) {
      $itemRows .= '
              <tr>
                <td style="padding:14px 0;border-bottom:1px solid #ECECE6;font-size:14px;color:#111111;">
                  ' . htmlspecialchars($it['product_name'], ENT_QUOTES, 'UTF-8') . '
                  ' . (!empty($it['size']) ? '<span style="display:inline-block;margin-left:8px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:#6B7280;border:1px solid #E5E5E0;border-radius:4px;padding:2px 7px;">Size ' . htmlspecialchars($it['size'], ENT_QUOTES, 'UTF-8') . '</span>' : '') . '
                  <div style="font-size:12px;color:#6B7280;margin-top:3px;">Qty: ' . (int)$it['quantity'] . ' &#215; ' . $money($it['unit_price']) . '</div>
                </td>
                <td align="right" style="padding:14px 0;border-bottom:1px solid #ECECE6;font-size:14px;font-weight:700;color:#111111;white-space:nowrap;">' . $money($it['total_price']) . '</td>
              </tr>';
    }
    if ($itemRows === '') {
      $itemRows = '<tr><td colspan="2" style="padding:14px 0;border-bottom:1px solid #ECECE6;font-size:14px;color:#6B7280;">Items unavailable</td></tr>';
    }

    $discount = (float)($order['discount_amount'] ?? 0);
    $coupon   = $order['coupon_code'] ?? '';
    $discountRow = $discount > 0
      ? '<tr><td style="padding:5px 0;font-size:13px;color:#6B7280;">Discount' . ($coupon !== '' ? ' (' . htmlspecialchars($coupon, ENT_QUOTES, 'UTF-8') . ')' : '') . '</td><td align="right" style="padding:5px 0;font-size:13px;color:#16A34A;">-' . $money($discount) . '</td></tr>'
      : '';
    $shipCost = (float)($order['shipping_amount'] ?? 0);
    $shipRow = '<tr><td style="padding:5px 0;font-size:13px;color:#6B7280;">Shipping</td><td align="right" style="padding:5px 0;font-size:13px;color:' . ($shipCost > 0 ? '#111111' : '#16A34A') . ';">' . ($shipCost > 0 ? $money($shipCost) : 'Free') . '</td></tr>';

    $html = '<!DOCTYPE html>
<html>
<body style="margin:0;padding:0;background:#F4F4F0;font-family:Arial,Helvetica,sans-serif;-webkit-font-smoothing:antialiased;">
  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#F4F4F0;padding:32px 12px;">
    <tr><td align="center">
      <table role="presentation" width="560" cellpadding="0" cellspacing="0" style="width:100%;max-width:560px;background:#FFFFFF;border-radius:14px;overflow:hidden;border:1px solid #E5E5E0;">

        <tr><td style="background:#0A0A0A;padding:24px 32px;text-align:center;">
          <span style="font-size:17px;font-weight:700;letter-spacing:0.24em;color:#FFFFFF;text-transform:uppercase;">' . htmlspecialchars($brand, ENT_QUOTES, 'UTF-8') . '</span>
        </td></tr>

        <tr><td style="padding:40px 32px 8px;">
          <div style="width:64px;height:64px;line-height:64px;border-radius:50%;background:#DCFCE7;color:#166534;font-size:30px;text-align:center;margin:0 auto 20px;">&#10003;</div>
          <h1 style="margin:0 0 10px;font-size:24px;color:#0A0A0A;text-align:center;letter-spacing:-0.02em;">Payment Confirmed</h1>
          <p style="margin:0;text-align:center;font-size:14px;line-height:1.7;color:#6B7280;">Hi ' . $name . ', thank you for your order.<br>We have received your payment and it is now being processed.</p>
        </td></tr>

        <tr><td style="padding:24px 32px 0;">
          <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#F9F9F6;border:1px solid #ECECE6;border-radius:10px;">
            <tr>
              <td style="padding:14px 18px;font-size:12px;color:#6B7280;">Order<br><strong style="font-size:14px;color:#111111;">' . $oNum . '</strong></td>
              <td style="padding:14px 18px;font-size:12px;color:#6B7280;border-left:1px solid #ECECE6;">Placed on<br><strong style="font-size:14px;color:#111111;">' . $placed . '</strong></td>
            </tr>
            <tr><td colspan="2" style="padding:0 18px 14px;font-size:12px;color:#6B7280;border-top:1px solid #ECECE6;padding-top:14px;">Payment method<br><strong style="font-size:13px;color:#111111;">' . htmlspecialchars($payLabel, ENT_QUOTES, 'UTF-8') . '</strong></td></tr>
          </table>
        </td></tr>

        <tr><td style="padding:28px 32px 0;">
          <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.14em;color:#6B7280;margin-bottom:6px;">Items</div>
          <table role="presentation" width="100%" cellpadding="0" cellspacing="0">' . $itemRows . '</table>

          <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-top:14px;">
            <tr><td style="padding:5px 0;font-size:13px;color:#6B7280;">Subtotal</td><td align="right" style="padding:5px 0;font-size:13px;color:#111111;">' . $money($order['subtotal']) . '</td></tr>
            ' . $discountRow . $shipRow . '
            <tr><td style="padding:12px 0 0;border-top:1.5px solid #111111;font-size:15px;font-weight:700;color:#111111;">Total Paid</td><td align="right" style="padding:12px 0 0;border-top:1.5px solid #111111;font-size:15px;font-weight:700;color:#111111;">' . $money($order['grand_total']) . '</td></tr>
          </table>
        </td></tr>

        ' . ($shipLines ? '
        <tr><td style="padding:28px 32px 0;">
          <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.14em;color:#6B7280;margin-bottom:8px;">Shipping To</div>
          <div style="font-size:13px;line-height:1.8;color:#374151;border:1px solid #ECECE6;border-radius:10px;padding:14px 18px;background:#FFFFFF;">
            <strong style="color:#111111;">' . $name . '</strong><br>' .
            implode('<br>', array_map(fn($l) => htmlspecialchars($l, ENT_QUOTES, 'UTF-8'), $shipLines)) . '
          </div>
        </td></tr>' : '') . '

        <tr><td style="padding:32px 32px 36px;">
          <p style="margin:0;font-size:13px;line-height:1.7;color:#6B7280;">Need help with your order? Just reply to this email or contact our support team and we will get back to you.</p>
        </td></tr>

        <tr><td style="background:#0A0A0A;padding:20px 32px;text-align:center;">
          <p style="margin:0;font-size:11px;color:#9CA3AF;letter-spacing:0.04em;">&copy; ' . date('Y') . ' ' . htmlspecialchars($brand, ENT_QUOTES, 'UTF-8') . ' &#8226; All rights reserved</p>
        </td></tr>

      </table>
    </td></tr>
  </table>
</body>
</html>';

    $text = "Payment Confirmed\n\n"
          . "Hi {$order['customer_name']}, thank you for your order. We have received your payment and it is now being processed.\n\n"
          . "Order: {$order['order_number']}\n"
          . "Placed on: {$placed}\n"
          . "Payment method: {$payLabel}\n\n";

    foreach ($items as $it) {
      $text .= "- {$it['product_name']}" . (!empty($it['size']) ? " (Size {$it['size']})" : '') . " x {$it['quantity']} = " . number_format((float)$it['total_price'], 2) . "\n";
    }

    $text .= "\nSubtotal: " . number_format((float)$order['subtotal'], 2) . "\n";
    if ($discount > 0) $text .= "Discount" . ($coupon !== '' ? " ({$coupon})" : '') . ": -" . number_format($discount, 2) . "\n";
    $text .= "Shipping: " . ($shipCost > 0 ? number_format($shipCost, 2) : 'Free') . "\n";
    $text .= "Total Paid: " . number_format((float)$order['grand_total'], 2) . "\n";
    if ($shipLines) $text .= "\nShipping to:\n" . $order['customer_name'] . "\n" . implode("\n", $shipLines) . "\n";

    // ── Send via Resend ──
    $payload = json_encode([
      'from'    => "{$fromName} <{$from}>",
      'to'      => [$to],
      'subject' => "Order {$order['order_number']} Confirmed — Payment Received",
      'html'    => $html,
      'text'    => $text,
    ]);

    $ch = curl_init('https://api.resend.com/emails');
    curl_setopt_array($ch, [
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_TIMEOUT        => 30,
      CURLOPT_CUSTOMREQUEST  => 'POST',
      CURLOPT_POSTFIELDS     => $payload,
      CURLOPT_HTTPHEADER     => [
        'Authorization: Bearer ' . $apiKey,
        'Content-Type: application/json',
      ],
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr  = curl_error($ch);
    curl_close($ch);

    if ($curlErr) {
      error_log("sendOrderConfirmationEmail: cURL error for order {$orderId}: {$curlErr}");
      sendOrderConfirmationEmailRelease($db, $orderId);
      return false;
    }

    if ($httpCode === 200) {
      error_log("sendOrderConfirmationEmail: order {$order['order_number']} confirmation sent to {$to}");
      return true;
    }

    $result = json_decode($response, true);
    $msg = $result['message'] ?? $response;
    error_log("sendOrderConfirmationEmail: FAILED for order {$orderId} (HTTP {$httpCode}): {$msg}");
    // Release the guard so a later trigger (or retry) can attempt the send again.
    sendOrderConfirmationEmailRelease($db, $orderId);
    return false;
  }
}

if (!function_exists('sendOrderConfirmationEmailRelease')) {
  /** Give back the one-time-send claim after a failed attempt. */
  function sendOrderConfirmationEmailRelease($db, $orderId) {
    $rel = @$db->prepare('UPDATE orders SET email_sent_at = NULL WHERE id = ?');
    if ($rel) {
      $rel->bind_param('i', $orderId);
      $rel->execute();
      $rel->close();
    }
  }
}

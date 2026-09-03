<?php
// Debug script - hosted server pe test karo
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Cashfree Payment Debug</h2>";

// 1. Check cURL
echo "<h3>1. cURL Extension</h3>";
echo function_exists('curl_init') ? '<span style="color:green">cURL Available ✓</span>' : '<span style="color:red">cURL NOT Available ✗</span>';

// 2. Load .env
echo "<h3>2. .env File</h3>";
$envFile = dirname(__DIR__) . '/.env';
echo "Path: $envFile<br>";
echo "Exists: " . (file_exists($envFile) ? 'Yes ✓' : 'No ✗') . "<br>";

if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($k, $v) = explode('=', $line, 2);
            $v = trim($v);
            if (($v[0] ?? '') === '"' && substr($v, -1) === '"') $v = substr($v, 1, -1);
            $_ENV[trim($k)] = $v;
            if (strpos($k, 'CF_') !== false) {
                echo trim($k) . " = " . substr($v, 0, 10) . "...<br>";
            }
        }
    }
}

echo "CF_APP_ID: " . ($_ENV['CF_APP_ID'] ?? 'NOT SET') . "<br>";
echo "CF_SECRET_KEY: " . (isset($_ENV['CF_SECRET_KEY']) ? substr($_ENV['CF_SECRET_KEY'], 0, 10) . '...' : 'NOT SET') . "<br>";
echo "CF_ENV: " . ($_ENV['CF_ENV'] ?? 'NOT SET') . "<br>";

// 3. Test Cashfree API
echo "<h3>3. Cashfree API Test</h3>";

$testPayload = [
    'order_id' => 'TEST_' . time(),
    'order_amount' => 1.00,
    'order_currency' => 'INR',
    'customer_details' => [
        'customer_id' => 'TEST_USER',
        'customer_name' => 'Test User',
        'customer_email' => 'test@example.com',
        'customer_phone' => '9999999999',
    ],
];

$ch = curl_init('https://api.cashfree.com/pg/orders');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($testPayload),
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'x-client-id: ' . ($_ENV['CF_APP_ID'] ?? ''),
        'x-client-secret: ' . ($_ENV['CF_SECRET_KEY'] ?? ''),
        'x-api-version: 2023-08-01',
    ],
]);

$response = curl_exec($ch);
$curlErr = curl_error($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode<br>";
echo "cURL Error: " . ($curlErr ?: 'None') . "<br>";
echo "Response: <pre>" . htmlspecialchars($response) . "</pre>";

$result = json_decode($response, true);
if (isset($result['cf_order_id'])) {
    echo '<span style="color:green">API Working! cf_order_id: ' . $result['cf_order_id'] . '</span><br>';
} else {
    echo '<span style="color:red">API Error: ' . htmlspecialchars($result['message'] ?? 'Unknown') . '</span><br>';
}
?>

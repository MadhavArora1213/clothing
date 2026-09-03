<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

error_reporting(E_ALL);
ini_set('display_errors', 0);

// Load .env
$envFile = dirname(__DIR__) . '/.env';
$envLines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
foreach ($envLines as $line) {
    if (strpos(trim($line), '#') === 0) continue;
    if (strpos($line, '=') !== false) {
        list($key, $value) = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($value);
    }
}

$cfAppId = $_ENV['CF_APP_ID'] ?? '';
$cfSecret = $_ENV['CF_SECRET_KEY'] ?? '';

// Test Cashfree API - Create a test order
$payload = [
    'order_id' => 'TEST_' . uniqid(),
    'order_amount' => 1.00,
    'order_currency' => 'INR',
    'customer_details' => [
        'customer_id' => 'TEST_USER_001',
        'customer_name' => 'Test User',
        'customer_email' => 'test@example.com',
        'customer_phone' => '9999999999',
    ],
];

$ch = curl_init('https://api.cashfree.com/pg/orders');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 15,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($payload),
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'x-client-id: ' . $cfAppId,
        'x-client-secret: ' . $cfSecret,
        'x-api-version: 2023-08-01',
    ],
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

$result = [
    'http_code' => $httpCode,
    'curl_error' => $curlError ?: 'none',
    'response' => json_decode($response, true),
];

if ($httpCode == 200 || $httpCode == 201) {
    $result['status'] = 'SUCCESS';
    $result['message'] = 'Cashfree API is working! Payment gateway ready.';
} else {
    $result['status'] = 'FAILED';
    $result['message'] = 'Cashfree API error - check keys';
}

echo json_encode($result, JSON_PRETTY_PRINT);

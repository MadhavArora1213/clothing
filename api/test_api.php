<?php
// Simple test endpoint - hosted pe upload karke test karo
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

echo json_encode([
    'status' => 'ok',
    'message' => 'API is working!',
    'php_version' => phpversion(),
    'curl_enabled' => function_exists('curl_init'),
    'server' => $_SERVER['HTTP_HOST'] ?? 'unknown',
    'time' => date('Y-m-d H:i:s')
]);

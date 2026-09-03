<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

error_reporting(E_ALL);
ini_set('display_errors', 0);

echo "{\n";

// 1. Check .env file
echo '"step1_env": ';
$envFile = dirname(__DIR__) . '/.env';
if (file_exists($envFile)) {
    $envLines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($envLines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
        }
    }
    echo '{"status":"ok","db_host":"'.($_ENV['DB_HOST'] ?? 'NOT SET').'","db_name":"'.($_ENV['DB_NAME'] ?? 'NOT SET').'"}';
} else {
    echo '{"status":"error","message":".env file not found at: '.$envFile.'"}';
}
echo ",\n";

// 2. Check DB Connection
echo '"step2_db": ';
$dbHost = $_ENV['DB_HOST'] ?? '127.0.0.1';
$dbName = $_ENV['DB_NAME'] ?? 'cloths';
$dbUser = $_ENV['DB_USER'] ?? 'root';
$dbPass = $_ENV['DB_PASS'] ?? '';

$conn = @new mysqli($dbHost, $dbUser, $dbPass, $dbName);
if ($conn->connect_error) {
    echo '{"status":"error","message":"DB Connection Failed: '. addslashes($conn->connect_error) .'"}';
} else {
    echo '{"status":"ok","message":"Database connected!"}';
    $conn->close();
}
echo ",\n";

// 3. Check Cashfree config
echo '"step3_cashfree": ';
$cfAppId = $_ENV['CF_APP_ID'] ?? 'NOT SET';
$cfSecret = $_ENV['CF_SECRET_KEY'] ?? 'NOT SET';
echo '{"status":"ok","app_id":"'.substr($cfAppId,0,10).'...","env":"production"}';
echo "\n}";


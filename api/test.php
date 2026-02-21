<?php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$envFile = __DIR__ . '/.env';
$envExists = file_exists($envFile);

if ($envExists) {
    $dotenv = parse_ini_file($envFile);
    if ($dotenv) {
        foreach ($dotenv as $key => $value) {
            $_ENV[$key] = $value;
        }
    }
}

$dbTest = null;
try {
    $host = $_ENV['DB_HOST'] ?? 'not set';
    $dbname = $_ENV['DB_DATABASE'] ?? 'not set';
    $username = $_ENV['DB_USERNAME'] ?? 'not set';

    if ($host !== 'not set' && $dbname !== 'not set' && $username !== 'not set') {
        $password = $_ENV['DB_PASSWORD'] ?? '';
        $dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";
        $pdo = new PDO($dsn, $username, $password);
        $dbTest = 'connected successfully';
    } else {
        $dbTest = 'env vars not set';
    }
} catch (Exception $e) {
    $dbTest = 'connection failed: ' . $e->getMessage();
}

echo json_encode([
    'status' => 'ok',
    'message' => 'API is working',
    'php_version' => phpversion(),
    'env_file' => $envExists ? 'exists' : 'not found',
    'db_host' => $_ENV['DB_HOST'] ?? 'not set',
    'db_name' => $_ENV['DB_DATABASE'] ?? 'not set',
    'db_test' => $dbTest,
    'cors_origins' => $_ENV['CORS_ALLOWED_ORIGINS'] ?? 'not set'
]);

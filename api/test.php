<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Testing API Components...\n\n";

$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    echo "✓ .env file found\n";
    $dotenv = parse_ini_file($envFile);
    foreach ($dotenv as $key => $value) {
        $_ENV[$key] = $value;
    }
} else {
    echo "✗ .env file not found\n";
}

echo "\nDatabase Config:\n";
echo "Host: " . ($_ENV['DB_HOST'] ?? 'NOT SET') . "\n";
echo "Database: " . ($_ENV['DB_DATABASE'] ?? 'NOT SET') . "\n";
echo "Username: " . ($_ENV['DB_USERNAME'] ?? 'NOT SET') . "\n\n";

try {
    require_once __DIR__ . '/app/Database.php';
    echo "✓ Database class loaded\n";

    $db = \App\Database::getInstance();
    echo "✓ Database connection successful!\n\n";

    $result = $db->fetchOne("SELECT COUNT(*) as count FROM users");
    echo "Users in database: " . $result['count'] . "\n";

} catch (\Exception $e) {
    echo "✗ Database error: " . $e->getMessage() . "\n";
}

echo "\nTesting Auth Middleware...\n";
try {
    require_once __DIR__ . '/app/Middleware/Auth.php';
    echo "✓ Auth middleware loaded\n";
} catch (\Exception $e) {
    echo "✗ Auth error: " . $e->getMessage() . "\n";
}

echo "\nAll tests complete!\n";

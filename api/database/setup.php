<?php

require __DIR__ . '/../vendor/autoload.php';

$config = require __DIR__ . '/../config/database.php';

try {
    $dsn = "mysql:host={$config['host']};port={$config['port']};charset={$config['charset']}";
    $pdo = new PDO($dsn, $config['username'], $config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    echo "Creating database if not exists...\n";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$config['database']}` CHARACTER SET {$config['charset']} COLLATE {$config['collation']}");
    echo "Database '{$config['database']}' is ready.\n\n";

    $pdo->exec("USE `{$config['database']}`");

    $migrationFiles = glob(__DIR__ . '/migrations/*.php');
    sort($migrationFiles);

    echo "Running migrations...\n";
    foreach ($migrationFiles as $file) {
        $migrationName = basename($file);
        echo "Running: $migrationName\n";

        $migration = require $file;
        $migration->up();

        echo "Completed: $migrationName\n\n";
    }

    echo "All migrations completed successfully!\n\n";

    echo "Running seeders...\n";
    $seederFiles = glob(__DIR__ . '/seeders/*.php');
    foreach ($seederFiles as $file) {
        $seederName = basename($file);
        echo "Running: $seederName\n";

        require_once $file;
        $className = 'Database\\Seeders\\' . pathinfo($seederName, PATHINFO_FILENAME);
        if (class_exists($className)) {
            $seeder = new $className();
            $seeder->run();
            echo "Completed: $seederName\n\n";
        }
    }

    echo "\n✅ Database setup completed successfully!\n";

} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}

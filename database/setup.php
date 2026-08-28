<?php

require_once __DIR__ . '/../config/app.php';

$host = (string) env('DB_HOST', '127.0.0.1');
$dbname = (string) env('DB_NAME', 'portfolio_db');
$user = (string) env('DB_USER', 'root');
$password = (string) env('DB_PASS', '');

try {
    $pdo = new PDO("mysql:host={$host};charset=utf8mb4", $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbname}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
    $pdo->exec("USE `{$dbname}`;");

    $schemaFile = __DIR__ . '/schema.sql';
    if (is_file($schemaFile)) {
        $sql = file_get_contents($schemaFile);
        if ($sql !== false && trim($sql) !== '') {
            $pdo->exec($sql);
        }
    }

    echo "Database and tables are ready.\n";
} catch (Throwable $e) {
    echo 'Database setup failed: ' . $e->getMessage() . "\n";
    exit(1);
}


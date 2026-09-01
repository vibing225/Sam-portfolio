<?php

require_once __DIR__ . '/app.php';

function getDbConnection(): PDO
{
    $host = (string) env('DB_HOST', '127.0.0.1');
    $dbname = (string) env('DB_NAME', 'portfolio_db');
    $user = (string) env('DB_USER', 'root');
    $pass = (string) env('DB_PASS', '');

    if ($host === '' || $dbname === '' || $user === '') {
        throw new RuntimeException('Missing required MySQL config values in .env.');
    }

    $dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', $host, $dbname);

    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_STRINGIFY_FETCHES => false,
    ]);

    return $pdo;
}

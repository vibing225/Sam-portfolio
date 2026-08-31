<?php

require_once __DIR__ . '/app.php';

function getDbConnection(): PDO
{
    $host = (string) env('DB_HOST', 'mysql-sowalphamoussa.alwaysdata.net');
    $dbname = (string) env('DB_NAME', 'sowalphamoussa_db');
    $user = (string) env('DB_USER', 'sowalphamoussa');
    $pass = (string) env('DB_PASS', 'HJBKBKByfvjhb785');

    $dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', $host, $dbname);

    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_STRINGIFY_FETCHES => false,
    ]);

    return $pdo;
}

<?php

require_once __DIR__ . '/../config/db.php';

enforceSecurityHeaders();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed.']);
    exit;
}

header('Content-Type: application/json; charset=utf-8');

try {
    $pdo = getDbConnection();
    $stmt = $pdo->prepare('SELECT * FROM technologies WHERE status = :status ORDER BY sort_order ASC, name ASC');
    $stmt->bindValue(':status', 'active', PDO::PARAM_STR);
    $stmt->execute();
    $items = $stmt->fetchAll();

    echo json_encode(array_map(static function (array $item): array {
        return [
            'id' => (int) $item['id'],
            'name' => $item['name'],
            'slug' => $item['slug'],
            'logo_url' => $item['logo_url'] ?? '',
            'logo_path' => $item['logo_path'] ?? '',
        ];
    }, $items), JSON_UNESCAPED_SLASHES);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Unable to load technologies.']);
}

<?php
require_once __DIR__ . '/../config/db.php';

enforceSecurityHeaders();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['projects' => [], 'error' => 'Method not allowed.']);
    exit;
}

header('Content-Type: application/json; charset=utf-8');

$queryValidator = Validator::make($_GET);
$queryValidator->validate([
    'featured' => 'nullable|bool',
    'limit' => 'nullable|int|min_val:1|max_val:12',
]);

$featuredOnly = (bool) $queryValidator->get('featured', false);
$limit = (int) $queryValidator->get('limit', 3);

try {
    $pdo = getDbConnection();

    $sql = 'SELECT * FROM projects WHERE status = :status';

    if ($featuredOnly) {
        $sql .= ' AND featured = 1';
    }

    $sql .= ' ORDER BY featured DESC, created_at DESC LIMIT :limit';

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':status', 'published', PDO::PARAM_STR);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    $projects = $stmt->fetchAll();

    echo json_encode([
        'projects' => array_map(static function (array $project): array {
            return [
                'id' => (int) ($project['id'] ?? 0),
                'title' => (string) ($project['title'] ?? ''),
                'slug' => (string) ($project['slug'] ?? ''),
                'category' => (string) ($project['category'] ?? ''),
                'short_description' => (string) ($project['short_description'] ?? ''),
                'description' => (string) ($project['description'] ?? ''),
                'technologies' => (string) ($project['technologies'] ?? ''),
                'project_url' => (string) ($project['project_url'] ?? ''),
                'github_url' => (string) ($project['github_url'] ?? ''),
                'image_path' => (string) ($project['image_path'] ?? ''),
                'featured' => (bool) ($project['featured'] ?? false),
            ];
        }, $projects),
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['projects' => [], 'error' => 'Unable to load projects.']);
}

<?php

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/db.php';

if (!adminIsLoggedIn()) {
    redirect('login.php');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('dashboard.php');
}

verifyCsrf();

$validator = Validator::make($_POST);
if (!$validator->validate(['id' => 'required|int|min_val:1'])) {
    setFlash('error', 'Identifiant de projet invalide.');
    redirect('dashboard.php');
}

$id = (int) $validator->get('id');
$pdo = getDbConnection();

$stmt = $pdo->prepare('SELECT image_path FROM projects WHERE id = :id LIMIT 1');
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$project = $stmt->fetch();

if ($project && !empty($project['image_path'])) {
    Validator::safeDeleteUploadedFile((string) $project['image_path']);
}

$deleteStmt = $pdo->prepare('DELETE FROM projects WHERE id = :id');
$deleteStmt->bindValue(':id', $id, PDO::PARAM_INT);
$deleteStmt->execute();

setFlash('success', 'Projet supprimé avec succès.');
redirect('dashboard.php');


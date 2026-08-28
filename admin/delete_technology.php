<?php

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/db.php';

if (!adminIsLoggedIn()) {
    redirect('login.php');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('technologies.php');
}

verifyCsrf();

$validator = Validator::make($_POST);
if (!$validator->validate(['id' => 'required|int|min_val:1'])) {
    setFlash('error', 'Identifiant de technologie invalide.');
    redirect('technologies.php');
}

$id = (int) $validator->get('id');
$pdo = getDbConnection();

$stmt = $pdo->prepare('SELECT logo_path FROM technologies WHERE id = :id LIMIT 1');
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$tech = $stmt->fetch();

if ($tech && !empty($tech['logo_path'])) {
    Validator::safeDeleteUploadedFile((string) $tech['logo_path']);
}

$deleteStmt = $pdo->prepare('DELETE FROM technologies WHERE id = :id');
$deleteStmt->bindValue(':id', $id, PDO::PARAM_INT);
$deleteStmt->execute();

setFlash('success', 'Technologie supprimée avec succès.');
redirect('technologies.php');


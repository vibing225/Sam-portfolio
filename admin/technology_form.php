<?php

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/db.php';

if (!adminIsLoggedIn()) {
    redirect('login.php');
}

$pdo = getDbConnection();

$getValidator = Validator::make($_GET);
$getValidator->validate(['id' => 'nullable|int|min_val:1']);
$id = (int) $getValidator->get('id', 0);
$tech = null;

if ($id > 0) {
    $stmt = $pdo->prepare('SELECT * FROM technologies WHERE id = :id LIMIT 1');
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $tech = $stmt->fetch();
}

function slugify(string $text): string
{
    $slug = strtolower(trim($text));
    $slug = preg_replace('/[^a-z0-9-]+/i', '-', $slug);
    $slug = trim((string) $slug, '-');
    return $slug !== '' ? $slug : 'tech';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    $postValidator = Validator::make($_POST);
    $schema = [
        'id' => 'nullable|int|min_val:0',
        'name' => 'required|string|min:1|max:100',
        'logo_url' => 'nullable|url|max:2048',
        'status' => 'required|in:active,inactive',
        'sort_order' => 'nullable|int|min_val:0|max_val:9999',
        'existing_logo_path' => 'nullable|string|max:255',
    ];

    if (!$postValidator->validate($schema)) {
        setFlash('error', $postValidator->firstError() ?? 'Veuillez corriger les erreurs du formulaire.');
        $redirectId = (int) $postValidator->get('id', 0);
        redirect('technology_form.php' . ($redirectId > 0 ? '?id=' . $redirectId : ''));
    }

    $id = (int) $postValidator->get('id', 0);
    $name = (string) $postValidator->get('name');
    $logoUrl = (string) ($postValidator->get('logo_url') ?? '');
    $status = (string) $postValidator->get('status', 'active');
    $sortOrder = (int) $postValidator->get('sort_order', 0);
    $logoPath = $postValidator->get('existing_logo_path');

    if (!empty($_FILES['logo']['name'])) {
        try {
            $newLogo = Validator::validateAndStoreFile(
                $_FILES['logo'],
                'tech',
                2 * 1024 * 1024,
                ['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml']
            );
            if ($newLogo !== null) {
                if (!empty($logoPath)) {
                    Validator::safeDeleteUploadedFile((string)$logoPath);
                }
                $logoPath = $newLogo;
                $logoUrl = '';
            }
        } catch (RuntimeException $e) {
            setFlash('error', $e->getMessage());
            redirect('technology_form.php' . ($id > 0 ? '?id=' . $id : ''));
        }
    }

    $slug = slugify($name);

    if ($id > 0) {
        $stmt = $pdo->prepare('UPDATE technologies SET name = :name, slug = :slug, logo_path = :logo_path, logo_url = :logo_url, status = :status, sort_order = :sort_order WHERE id = :id');
        $stmt->execute([
            'name' => $name,
            'slug' => $slug,
            'logo_path' => $logoPath,
            'logo_url' => $logoUrl,
            'status' => $status,
            'sort_order' => $sortOrder,
            'id' => $id,
        ]);
        setFlash('success', 'Technologie mise à jour avec succès.');
    } else {
        $stmt = $pdo->prepare('INSERT INTO technologies (name, slug, logo_path, logo_url, status, sort_order) VALUES (:name, :slug, :logo_path, :logo_url, :status, :sort_order)');
        $stmt->execute([
            'name' => $name,
            'slug' => $slug,
            'logo_path' => $logoPath,
            'logo_url' => $logoUrl,
            'status' => $status,
            'sort_order' => $sortOrder,
        ]);
        setFlash('success', 'Technologie ajoutée avec succès.');
    }

    redirect('technologies.php');
}
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= $tech ? 'Modifier la technologie' : 'Ajouter une technologie'; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="../css/tokens.css" />
    <link rel="stylesheet" href="../css/base.css" />
    <link rel="stylesheet" href="../css/components.css" />
    <link rel="stylesheet" href="../css/admin.css" />
</head>
<body class="admin-page">
    <header class="site-nav">
        <div class="container nav-shell">
            <a class="brand" href="../index.html">Alpha Moussa <span>Sow</span> <span class="tag tag-dim" style="margin-left:8px;">admin</span></a>
            <div class="admin-actions">
                <a class="btn btn-ghost" href="technologies.php">Retour</a>
                <a class="btn btn-primary" href="logout.php">Déconnexion</a>
            </div>
        </div>
    </header>

    <main id="main" class="admin-shell">
        <div class="admin-hero">
            <div>
                <span class="eyebrow">Technologie</span>
                <h1 class="admin-title"><?= $tech ? 'Modifier la technologie' : 'Ajouter une technologie'; ?></h1>
            </div>
        </div>

        <form method="post" enctype="multipart/form-data" class="form-shell">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8'); ?>" />
            <?php if ($tech): ?>
                <input type="hidden" name="id" value="<?= (int) $tech['id']; ?>" />
                <input type="hidden" name="existing_logo_path" value="<?= htmlspecialchars((string) ($tech['logo_path'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" />
            <?php endif; ?>

            <div class="form-grid">
                <label class="form-field">
                    <span>Nom</span>
                    <input type="text" name="name" value="<?= htmlspecialchars((string) ($tech['name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" required />
                </label>

                <label class="form-field">
                    <span>Ordre d'affichage</span>
                    <input type="number" name="sort_order" value="<?= (int) ($tech['sort_order'] ?? 0); ?>" min="0" />
                </label>

                <label class="form-field" style="grid-column: 1 / -1;">
                    <span>URL du logo</span>
                    <input type="url" name="logo_url" value="<?= htmlspecialchars((string) ($tech['logo_url'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" placeholder="https://..." />
                </label>

                <label class="form-field" style="grid-column: 1 / -1;">
                    <span>Uploader un logo</span>
                    <input type="file" name="logo" accept="image/*" />
                </label>

                <label class="form-field" style="grid-column: 1 / -1;">
                    <span>Statut</span>
                    <select name="status">
                        <option value="active" <?= (($tech['status'] ?? 'active') === 'active') ? 'selected' : ''; ?>>Actif</option>
                        <option value="inactive" <?= (($tech['status'] ?? 'active') === 'inactive') ? 'selected' : ''; ?>>Inactif</option>
                    </select>
                </label>
            </div>

            <div style="display:flex; gap:0.75rem; flex-wrap:wrap; margin-top: 1.5rem;">
                <button type="submit" class="btn btn-primary"><?= $tech ? 'Enregistrer' : 'Créer'; ?></button>
                <a class="btn btn-ghost" href="technologies.php">Annuler</a>
            </div>
        </form>
    </main>
</body>
</html>

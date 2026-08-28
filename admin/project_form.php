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
$project = null;

if ($id > 0) {
    $stmt = $pdo->prepare('SELECT * FROM projects WHERE id = :id LIMIT 1');
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $project = $stmt->fetch();
}

function makeSlug(string $text): string
{
    $slug = strtolower(trim($text));
    $slug = preg_replace('/[^a-z0-9]+/i', '-', $slug);
    $slug = trim((string) $slug, '-');
    return $slug !== '' ? $slug : 'projet';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    $postValidator = Validator::make($_POST);
    $schema = [
        'id' => 'nullable|int|min_val:0',
        'title' => 'required|string|min:2|max:255',
        'category' => 'required|in:' . implode(',', ALLOWED_CATEGORIES),
        'short_description' => 'required|string|min:3|max:500',
        'description' => 'required|string|min:3|max:10000',
        'technologies' => 'nullable|string|max:255',
        'project_url' => 'nullable|url|max:2048',
        'github_url' => 'nullable|url|max:2048',
        'status' => 'required|in:draft,published',
        'featured' => 'nullable|bool',
        'existing_image' => 'nullable|string|max:255',
    ];

    if (!$postValidator->validate($schema)) {
        setFlash('error', $postValidator->firstError() ?? 'Veuillez corriger les erreurs du formulaire.');
        $redirectId = (int) $postValidator->get('id', 0);
        redirect('project_form.php' . ($redirectId > 0 ? '?id=' . $redirectId : ''));
    }

    $id = (int) $postValidator->get('id', 0);
    $title = (string) $postValidator->get('title');
    $category = (string) $postValidator->get('category', 'Web');
    $short_description = (string) $postValidator->get('short_description');
    $description = (string) $postValidator->get('description');
    $technologies = (string) ($postValidator->get('technologies') ?? '');
    $project_url = $postValidator->get('project_url');
    $github_url = $postValidator->get('github_url');
    $status = (string) $postValidator->get('status', 'draft');
    $featured = !empty($_POST['featured']) ? 1 : 0;
    $imagePath = $postValidator->get('existing_image');

    try {
        if (!empty($_FILES['image']['name'])) {
            $newImage = Validator::validateAndStoreFile(
                $_FILES['image'],
                'projects',
                2 * 1024 * 1024,
                ['image/jpeg', 'image/png', 'image/webp']
            );
            if ($newImage !== null) {
                // Remove old image if replaced
                if (!empty($imagePath)) {
                    Validator::safeDeleteUploadedFile((string)$imagePath);
                }
                $imagePath = $newImage;
            }
        }
    } catch (RuntimeException $e) {
        setFlash('error', $e->getMessage());
        redirect('project_form.php' . ($id > 0 ? '?id=' . $id : ''));
    }

    $slug = makeSlug($title);
    $data = [
        'title' => $title,
        'slug' => $slug,
        'category' => $category,
        'short_description' => $short_description,
        'description' => $description,
        'technologies' => $technologies,
        'project_url' => $project_url,
        'github_url' => $github_url,
        'image_path' => $imagePath,
        'status' => $status,
        'featured' => $featured,
    ];

    if ($id > 0) {
        $sql = 'UPDATE projects SET title = :title, slug = :slug, category = :category, short_description = :short_description, description = :description, technologies = :technologies, project_url = :project_url, github_url = :github_url, image_path = :image_path, status = :status, featured = :featured WHERE id = :id';
        $data['id'] = $id;
        $stmt = $pdo->prepare($sql);
        $stmt->execute($data);
        setFlash('success', 'Projet mis à jour avec succès.');
    } else {
        $sql = 'INSERT INTO projects (title, slug, category, short_description, description, technologies, project_url, github_url, image_path, status, featured) VALUES (:title, :slug, :category, :short_description, :description, :technologies, :project_url, :github_url, :image_path, :status, :featured)';
        $stmt = $pdo->prepare($sql);
        $stmt->execute($data);
        setFlash('success', 'Projet ajouté avec succès.');
    }

    redirect('dashboard.php');
}
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= $project ? 'Modifier le projet' : 'Ajouter un projet'; ?></title>
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
                <a class="btn btn-ghost" href="dashboard.php">Retour au dashboard</a>
                <a class="btn btn-primary" href="logout.php">Déconnexion</a>
            </div>
        </div>
    </header>

    <main id="main" class="admin-shell">
        <div class="admin-hero">
            <div>
                <span class="eyebrow">Projet</span>
                <h1 class="admin-title"><?= $project ? 'Modifier le projet' : 'Ajouter un projet'; ?></h1>
            </div>
        </div>

        <form method="post" enctype="multipart/form-data" class="form-shell">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8'); ?>" />
            <?php if ($project): ?>
                <input type="hidden" name="id" value="<?= (int) $project['id']; ?>" />
                <input type="hidden" name="existing_image" value="<?= htmlspecialchars((string) ($project['image_path'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" />
            <?php endif; ?>

            <div class="form-grid" style="display:grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap:1rem;">
                <label class="form-field">
                    <span>Titre</span>
                    <input type="text" name="title" value="<?= htmlspecialchars((string) ($project['title'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" required />
                </label>

                <label class="form-field">
                    <span>Catégorie</span>
                    <select name="category">
                        <?php foreach (ALLOWED_CATEGORIES as $category): ?>
                            <option value="<?= htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?>" <?= (($project['category'] ?? 'Web') === $category) ? 'selected' : ''; ?>><?= htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <label class="form-field">
                    <span>Statut</span>
                    <select name="status">
                        <option value="draft" <?= (($project['status'] ?? 'draft') === 'draft') ? 'selected' : ''; ?>>Brouillon</option>
                        <option value="published" <?= (($project['status'] ?? 'published') === 'published') ? 'selected' : ''; ?>>Publié</option>
                    </select>
                </label>

                <label class="form-field" style="grid-column: 1 / -1;">
                    <span>Description courte</span>
                    <textarea name="short_description" rows="3" required><?= htmlspecialchars((string) ($project['short_description'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></textarea>
                </label>

                <label class="form-field" style="grid-column: 1 / -1;">
                    <span>Description détaillée</span>
                    <textarea name="description" rows="6" required><?= htmlspecialchars((string) ($project['description'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></textarea>
                </label>

                <label class="form-field" style="grid-column: 1 / -1;">
                    <span>Technologies</span>
                    <input type="text" name="technologies" value="<?= htmlspecialchars((string) ($project['technologies'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" placeholder="HTML, CSS, PHP, MySQL" />
                </label>

                <label class="form-field">
                    <span>Lien du projet</span>
                    <input type="url" name="project_url" value="<?= htmlspecialchars((string) ($project['project_url'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" placeholder="https://..." />
                </label>

                <label class="form-field">
                    <span>Lien GitHub</span>
                    <input type="url" name="github_url" value="<?= htmlspecialchars((string) ($project['github_url'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" placeholder="https://..." />
                </label>

                <label class="form-field" style="grid-column: 1 / -1;">
                    <span>Image du projet</span>
                    <input type="file" name="image" accept="image/*" />
                </label>

                <label class="form-field" style="grid-column: 1 / -1; display:flex; align-items:center; gap:0.75rem;">
                    <input type="checkbox" name="featured" value="1" <?= (!empty($project['featured'])) ? 'checked' : ''; ?> />
                    <span>Mettre en avant sur la page d'accueil</span>
                </label>
            </div>

            <div style="display:flex; gap:0.75rem; flex-wrap:wrap; margin-top: 1.5rem;">
                <button type="submit" class="btn btn-primary"><?= $project ? 'Enregistrer les modifications' : 'Créer le projet'; ?></button>
                <a class="btn btn-ghost" href="dashboard.php">Annuler</a>
            </div>
        </form>
    </main>
</body>
</html>

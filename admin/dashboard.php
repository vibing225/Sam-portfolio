<?php

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/db.php';

if (!adminIsLoggedIn()) {
    redirect('login.php');
}

$pdo = getDbConnection();
$projectsStmt = $pdo->prepare('SELECT * FROM projects ORDER BY created_at DESC');
$projectsStmt->execute();
$projects = $projectsStmt->fetchAll();

$techStmt = $pdo->prepare('SELECT * FROM technologies ORDER BY sort_order ASC, name ASC');
$techStmt->execute();
$techRows = $techStmt->fetchAll();

$flash = getFlash();
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard admin</title>
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
                <a class="btn btn-ghost" href="project_form.php">Ajouter un projet</a>
                <a class="btn btn-ghost" href="technologies.php">Technologies</a>
                <a class="btn btn-primary" href="logout.php">Déconnexion</a>
            </div>
        </div>
    </header>

    <main id="main" class="admin-shell">
        <div class="admin-hero">
            <div>
                <span class="eyebrow">Dashboard</span>
                <h1 class="admin-title">Gestion du portfolio</h1>
                <p class="admin-subtitle">Ajoutez, modifiez et organisez les contenus visibles sur le site.</p>
            </div>
        </div>

        <div class="admin-stats">
            <div class="stat-card">
                <span class="stat-number"><?= count($projects); ?></span>
                <span class="stat-label">Projets</span>
            </div>
            <div class="stat-card">
                <span class="stat-number"><?= count($techRows); ?></span>
                <span class="stat-label">Technologies</span>
            </div>
            <div class="stat-card">
                <span class="stat-number"><?= count(array_filter($projects, static fn($p) => (int) $p['featured'] === 1)); ?></span>
                <span class="stat-label">En avant</span>
            </div>
        </div>

        <div class="admin-panel panel-grid">
            <section class="panel-box">
                <div class="panel-header">
                    <h2>Projets récents</h2>
                    <a class="btn btn-ghost" href="project_form.php">Nouveau projet</a>
                </div>

                <?php if ($flash): ?>
                    <div class="alert alert-success">
                        <?= htmlspecialchars($flash['message'], ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                <?php endif; ?>

                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Titre</th>
                                <th>Catégorie</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($projects): ?>
                                <?php foreach ($projects as $project): ?>
                                    <tr>
                                        <td><strong><?= htmlspecialchars($project['title'], ENT_QUOTES, 'UTF-8'); ?></strong></td>
                                        <td><?= htmlspecialchars($project['category'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?= htmlspecialchars($project['status'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td>
                                            <div class="short-actions">
                                                <a class="btn btn-ghost" href="project_form.php?id=<?= (int) $project['id']; ?>">Modifier</a>
                                                <form method="post" action="delete_project.php" onsubmit="return confirm('Supprimer ce projet ?');" style="display:inline;">
                                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8'); ?>" />
                                                    <input type="hidden" name="id" value="<?= (int) $project['id']; ?>" />
                                                    <button type="submit" class="btn btn-primary">Supprimer</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" style="text-align:center; color: var(--admin-dim);">Aucun projet enregistré pour le moment.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <aside class="panel-box">
                <div class="panel-header">
                    <h3>Outils rapides</h3>
                </div>
                <div style="display:flex; flex-direction:column; gap:0.75rem;">
                    <a class="btn btn-primary" href="project_form.php">Créer un projet</a>
                    <a class="btn btn-ghost" href="technologies.php">Gérer les technologies</a>
                    <a class="btn btn-ghost" href="../index.html">Voir le site</a>
                </div>
            </aside>
        </div>
    </main>
</body>
</html>

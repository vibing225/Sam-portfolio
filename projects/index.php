<?php

require_once __DIR__ . '/../config/db.php';

enforceSecurityHeaders();

$validCategories = array_merge(['all'], ALLOWED_CATEGORIES);
$queryValidator = Validator::make($_GET);
$queryValidator->validate([
    'category' => 'nullable|in:' . implode(',', $validCategories),
]);

$categoryFilter = (string) $queryValidator->get('category', 'all');
if ($categoryFilter === '') {
    $categoryFilter = 'all';
}

$projects = [];
$dbError = null;

try {
    $pdo = getDbConnection();
    $sql = 'SELECT * FROM projects WHERE status = :status';
    $params = ['status' => 'published'];

    if ($categoryFilter !== 'all') {
        $sql .= ' AND category = :category';
        $params['category'] = $categoryFilter;
    }

    $sql .= ' ORDER BY featured DESC, created_at DESC';

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $projects = $stmt->fetchAll();
} catch (Throwable $e) {
    $projects = [];
    $dbError = 'Impossible de charger les projets.';
}
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Projets — Alpha Moussa Sow</title>
    <meta name="description" content="Projets et réalisations d'Alpha Moussa Sow — développement web, systèmes d'information et solutions métiers." />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="../css/tokens.css" />
    <link rel="stylesheet" href="../css/base.css" />
    <link rel="stylesheet" href="../css/components.css" />
    <link rel="stylesheet" href="../css/pages.css" />
</head>
<body>
    <a class="skip-link" href="#main">Aller au contenu</a>

    <div data-include="header"></div>

    <main id="main">
        <section class="page-hero">
            <div class="container">
                <div class="section-head reveal reveal-up">
                    <span class="eyebrow">Projets</span>
                    <h1>Solutions web concrètes</h1>
                    <p class="text-dim">Des sites vitrines, outils de gestion, plateformes de réservation et applications web pensées pour des besoins réels.</p>
                </div>
            </div>
        </section>

        <section class="projects-shell">
            <div class="container">
                <div class="filter-bar reveal reveal-up" style="display:flex; flex-wrap:wrap; gap:0.75rem; margin-bottom: 2rem;">
                    <?php foreach (['all', 'Web', 'Mobile', 'Gestion', 'Autres'] as $filter): ?>
                        <a class="filter-chip <?= ($categoryFilter === $filter) ? 'active' : ''; ?>" href="?category=<?= htmlspecialchars($filter, ENT_QUOTES, 'UTF-8'); ?>">
                            <?= $filter === 'all' ? 'Tous' : htmlspecialchars($filter, ENT_QUOTES, 'UTF-8'); ?>
                        </a>
                    <?php endforeach; ?>
                </div>

                <?php if (!empty($dbError)): ?>
                    <div class="alert alert-error" style="margin-bottom: 1rem; padding: 1rem; border-radius: 12px; border: 1px solid rgba(255, 94, 94, 0.3); background: rgba(255, 94, 94, 0.07);">
                        Impossible de charger les projets pour le moment. Merci de vérifier la base de données.
                    </div>
                <?php endif; ?>

                <?php if ($projects): ?>
                    <div class="project-grid" style="display:grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap:1.25rem;">
                        <?php foreach ($projects as $project): ?>
                            <article class="story-card reveal reveal-up" style="height:100%;">
                                <div class="project-image" style="margin-bottom: 1rem; min-height: 180px; border-radius: 16px; background: linear-gradient(135deg, rgba(232,163,61,0.15), rgba(59,130,246,0.12)); border: 1px solid rgba(255,255,255,0.08); display:flex; align-items:center; justify-content:center; color: var(--text-dim); font-size: 0.8rem; letter-spacing: 0.08em; text-transform: uppercase; overflow:hidden;">
                                    <?php if (!empty($project['image_path'])): ?>
                                        <?php $projectImageUrl = preg_match('#^https?://#i', (string) $project['image_path']) ? (string) $project['image_path'] : '/' . ltrim((string) $project['image_path'], '/'); ?>
                                        <img src="<?= htmlspecialchars($projectImageUrl, ENT_QUOTES, 'UTF-8'); ?>" alt="<?= htmlspecialchars($project['title'], ENT_QUOTES, 'UTF-8'); ?>" style="width:100%; height:100%; object-fit:cover; display:block;" />
                                    <?php else: ?>
                                        <span>Projet</span>
                                    <?php endif; ?>
                                </div>

                                <span class="eyebrow"><?= htmlspecialchars($project['category'], ENT_QUOTES, 'UTF-8'); ?></span>
                                <h3 style="margin-top:0.5rem; margin-bottom:0.6rem;"><?= htmlspecialchars($project['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                                <p class="text-dim" style="margin-bottom:1rem;"><?= htmlspecialchars($project['short_description'], ENT_QUOTES, 'UTF-8'); ?></p>

                                <?php if (!empty($project['technologies'])): ?>
                                    <div class="mini-stat" style="margin-bottom: 1rem;">
                                        <?= htmlspecialchars(str_replace(',', ' • ', $project['technologies']), ENT_QUOTES, 'UTF-8'); ?>
                                    </div>
                                <?php endif; ?>

                                <div class="project-actions" style="display:flex; flex-wrap:wrap; gap:0.5rem; margin-top:auto;">
                                    <?php if (!empty($project['project_url']) && $project['project_url'] !== '#'): ?>
                                        <a class="btn btn-ghost" href="<?= htmlspecialchars($project['project_url'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noreferrer">Voir le site</a>
                                    <?php endif; ?>
                                    <?php if (!empty($project['github_url']) && $project['github_url'] !== '#'): ?>
                                        <a class="btn btn-primary" href="<?= htmlspecialchars($project['github_url'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noreferrer">GitHub</a>
                                    <?php endif; ?>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state reveal reveal-up" style="padding: 2.5rem 1.5rem; text-align:center; border:1px dashed rgba(255,255,255,0.2); border-radius:16px; background: rgba(255,255,255,0.02);">
                        <h3 style="margin-bottom: 0.6rem;">Aucun projet publié pour cette catégorie.</h3>
                        <p class="text-dim">Les réalisations apparaîtront ici dès qu’un projet sera ajouté depuis l’admin.</p>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <div data-include="footer"></div>

    <script src="../js/i18n.js"></script>
    <script src="../js/theme.js"></script>
    <script src="../js/includes.js"></script>
    <script src="../js/main.js"></script>
</body>
</html>

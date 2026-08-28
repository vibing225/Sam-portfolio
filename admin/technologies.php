<?php

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/db.php';

if (!adminIsLoggedIn()) {
    redirect('login.php');
}

$pdo = getDbConnection();
$stmt = $pdo->prepare('SELECT * FROM technologies ORDER BY sort_order ASC, name ASC');
$stmt->execute();
$techs = $stmt->fetchAll();
$flash = getFlash();
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Technologies</title>
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
                <a class="btn btn-ghost" href="dashboard.php">Dashboard</a>
                <a class="btn btn-primary" href="logout.php">Déconnexion</a>
            </div>
        </div>
    </header>

    <main id="main" class="admin-shell">
        <div class="admin-hero">
            <div>
                <span class="eyebrow">Technologies</span>
                <h1 class="admin-title">Gestion des outils</h1>
                <p class="admin-subtitle">Ajoutez, modifiez ou supprimez les technologies visibles dans le carrousel et les compétences.</p>
            </div>
            <div class="admin-actions">
                <a class="btn btn-primary" href="technology_form.php">Ajouter une technologie</a>
            </div>
        </div>

        <div class="admin-panel">
            <?php if ($flash): ?>
                <div class="alert alert-success">
                    <?= htmlspecialchars($flash['message'], ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Logo</th>
                            <th>Statut</th>
                            <th>Ordre</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($techs): ?>
                            <?php foreach ($techs as $tech): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($tech['name'], ENT_QUOTES, 'UTF-8'); ?></strong></td>
                                    <td>
                                        <?php if (!empty($tech['logo_url'])): ?>
                                            <img src="<?= htmlspecialchars($tech['logo_url'], ENT_QUOTES, 'UTF-8'); ?>" alt="" style="width:28px; height:28px; object-fit:contain;" />
                                        <?php elseif (!empty($tech['logo_path'])): ?>
                                            <img src="../<?= htmlspecialchars($tech['logo_path'], ENT_QUOTES, 'UTF-8'); ?>" alt="" style="width:28px; height:28px; object-fit:contain;" />
                                        <?php else: ?>
                                            <span style="color: var(--admin-dim);">Aucun</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($tech['status'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?= (int) $tech['sort_order']; ?></td>
                                    <td>
                                        <div class="short-actions">
                                            <a class="btn btn-ghost" href="technology_form.php?id=<?= (int) $tech['id']; ?>">Modifier</a>
                                            <form method="post" action="delete_technology.php" onsubmit="return confirm('Supprimer cette technologie ?');" style="display:inline;">
                                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8'); ?>" />
                                                <input type="hidden" name="id" value="<?= (int) $tech['id']; ?>" />
                                                <button type="submit" class="btn btn-primary">Supprimer</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="text-align:center; color: var(--admin-dim);">Aucune technologie enregistrée.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>

<?php

require_once __DIR__ . '/../config/app.php';

if (!setupIsComplete()) {
    redirect('../setup.php');
}

if (adminIsLoggedIn()) {
    redirect('dashboard.php');
}

$message = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    if (isLoginRateLimited()) {
        $message = 'Trop de tentatives de connexion. Réessayez dans 10 minutes.';
    } else {
        $validator = Validator::make($_POST);
        $isValid = $validator->validate([
            'username' => 'required|string|min:3|max:64',
            'password' => 'required|string|min:1|max:255',
        ]);

        if (!$isValid) {
            recordLoginFailure();
            $message = $validator->firstError() ?? 'Identifiants invalides. Veuillez réessayer.';
        } else {
            $username = (string) $validator->get('username', '');
            $password = (string) $validator->get('password', '');

            if ($username === ADMIN_USERNAME && password_verify($password, adminPasswordHash())) {
                session_regenerate_id(true);
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_user'] = ADMIN_USERNAME;
                resetLoginFailures();
                redirect('dashboard.php');
            }

            recordLoginFailure();
            $message = 'Identifiants invalides. Veuillez réessayer.';
        }
    }
}
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Connexion admin</title>
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
        </div>
    </header>

    <main id="main" class="admin-shell">
        <div class="admin-card login-card">
            <div class="login-brand">
                <span class="eyebrow">Connexion</span>
                <h1>Panneau d'administration</h1>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-error">
                    <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>

            <form method="post" class="form-shell" style="margin-top: 1rem; background: rgba(10,15,28,0.35);">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8'); ?>" />

                <label class="form-field">
                    <span>Nom d'utilisateur</span>
                    <input type="text" name="username" value="admin" required />
                </label>

                <label class="form-field">
                    <span>Mot de passe</span>
                    <input type="password" name="password" placeholder="admin123" required />
                </label>

                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">Se connecter</button>
            </form>

            <div style="margin-top: 1.25rem; text-align:center;">
                <a class="btn btn-ghost" href="../index.html">← Retour au site</a>
            </div>
        </div>
    </main>
</body>
</html>

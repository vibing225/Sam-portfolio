<?php
require_once __DIR__ . '/config/app.php';

if (setupIsComplete()) {
    redirect('admin/login.php');
}

$error = null;
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    $postValidator = Validator::make($_POST);
    $isValid = $postValidator->validate([
        'admin_username' => 'required|string|min:3|max:64',
        'admin_password' => 'required|string|min:8|max:255',
        'admin_password_confirm' => 'required|string|min:8|max:255',
    ]);

    if (!$isValid) {
        $error = $postValidator->firstError();
    } else {
        $username = (string) $postValidator->get('admin_username');
        $password = (string) $postValidator->get('admin_password');
        $confirm = (string) $postValidator->get('admin_password_confirm');

        if ($password !== $confirm) {
            $error = 'Les deux mots de passe ne correspondent pas.';
        } else {
            $envPath = __DIR__ . '/.env';
            $envData = [];

            if (is_file($envPath)) {
                foreach (file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
                    $trimmed = trim($line);
                    if ($trimmed === '' || str_starts_with($trimmed, '#')) {
                        continue;
                    }

                    [$key, $value] = array_pad(explode('=', $trimmed, 2), 2, '');
                    $envData[trim($key)] = trim($value, " \t\n\r\0\x0B\"'");
                }
            }

            $envData['DB_HOST'] = $envData['DB_HOST'] ?? '127.0.0.1';
            $envData['DB_NAME'] = $envData['DB_NAME'] ?? 'portfolio_db';
            $envData['DB_USER'] = $envData['DB_USER'] ?? 'root';
            $envData['DB_PASS'] = $envData['DB_PASS'] ?? '';
            $envData['APP_TIMEZONE'] = $envData['APP_TIMEZONE'] ?? 'Africa/Conakry';
            $envData['ADMIN_USERNAME'] = $username;
            $envData['ADMIN_PASSWORD_HASH'] = password_hash($password, PASSWORD_BCRYPT);

            $content = "DB_HOST={$envData['DB_HOST']}\n";
            $content .= "DB_NAME={$envData['DB_NAME']}\n";
            $content .= "DB_USER={$envData['DB_USER']}\n";
            $content .= "DB_PASS={$envData['DB_PASS']}\n";
            $content .= "APP_TIMEZONE={$envData['APP_TIMEZONE']}\n";
            $content .= "ADMIN_USERNAME={$envData['ADMIN_USERNAME']}\n";
            $content .= "ADMIN_PASSWORD_HASH={$envData['ADMIN_PASSWORD_HASH']}\n";

            if (file_put_contents($envPath, $content, LOCK_EX) === false) {
                $error = 'Impossible d\'écrire le fichier de configuration. Vérifiez les droits du serveur.';
            } else {
                @chmod($envPath, 0600);
                file_put_contents(__DIR__ . '/.setup-complete', date('c') . PHP_EOL, LOCK_EX);
                $success = true;
            }
        }
    }
}
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Configuration initiale</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet" />
  <style>
    :root {
      --bg: #0a0f1c; --surface: #121a2c; --surface-2: #182238; --line: rgba(255,255,255,.08); --text: #e9edf6; --text-dim: #8d97ae; --accent: #e8a33d;
      --danger: #ff6b6b; --success: #64d39f;
    }
    * { box-sizing: border-box; }
    body {
      margin: 0; font-family: 'Inter', sans-serif; background: radial-gradient(circle at top, rgba(232,163,61,.15), transparent 30%), var(--bg); color: var(--text); min-height: 100vh; display: grid; place-items: center; padding: 2rem;
    }
    .card {
      width: min(100%, 540px); background: rgba(18,26,44,.9); border: 1px solid var(--line); border-radius: 24px; padding: 2rem; box-shadow: 0 30px 60px rgba(0,0,0,.25);
    }
    .eyebrow { font-size: .72rem; letter-spacing: .14em; text-transform: uppercase; color: var(--accent); font-weight: 700; }
    h1 { margin: .75rem 0 0; font-family: 'Space Grotesk', sans-serif; font-size: clamp(2rem, 3vw, 2.6rem); }
    p { color: var(--text-dim); line-height: 1.6; }
    .alert { margin-top: 1rem; padding: .9rem 1rem; border-radius: 12px; font-size: .95rem; }
    .alert.error { background: rgba(255,107,107,.1); border: 1px solid rgba(255,107,107,.25); color: #ffd6d6; }
    .alert.success { background: rgba(100,211,159,.08); border: 1px solid rgba(100,211,159,.2); color: #d8ffee; }
    form { display: grid; gap: 1rem; margin-top: 1.5rem; }
    label { display: grid; gap: .45rem; color: var(--text); font-weight: 600; }
    input {
      width: 100%; background: rgba(255,255,255,.02); border: 1px solid var(--line); border-radius: 12px; padding: .9rem 1rem; color: var(--text); font: inherit;
    }
    input:focus { outline: 2px solid rgba(232,163,61,.5); border-color: rgba(232,163,61,.7); }
    button {
      margin-top: .25rem; border: none; background: linear-gradient(135deg, var(--accent), #c97b1f); color: #141414; font-weight: 800; border-radius: 12px; padding: 1rem 1.1rem; cursor: pointer; font: inherit; transition: transform .2s ease, filter .2s ease;
    }
    button:hover { transform: translateY(-1px); filter: brightness(1.03); }
    small { color: var(--text-dim); }
  </style>
</head>
<body>
  <main class="card">
    <span class="eyebrow">First launch</span>
    <h1>Créer votre accès admin</h1>
    <p>Cette page ne s'affiche qu'une seule fois. Choisissez votre nom d'utilisateur et un mot de passe sécurisé pour finaliser le déploiement.</p>

    <?php if ($error): ?>
      <div class="alert error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php elseif ($success): ?>
      <div class="alert success">Configuration terminée. Vous allez être redirigé vers la page de connexion.</div>
      <meta http-equiv="refresh" content="2; url=admin/login.php" />
    <?php endif; ?>

    <form method="post" novalidate>
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8'); ?>" />
      <label>
        Nom d'utilisateur
        <input type="text" name="admin_username" value="admin" required />
      </label>

      <label>
        Mot de passe
        <input type="password" name="admin_password" placeholder="Minimum 8 caractères" required />
      </label>

      <label>
        Confirmer le mot de passe
        <input type="password" name="admin_password_confirm" required />
      </label>

      <button type="submit">Valider la configuration</button>
      <small>Le fichier de configuration sera créé localement sur le serveur. Cette étape ne doit être faite qu'une seule fois.</small>
    </form>
  </main>
</body>
</html>

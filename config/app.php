<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/Validator.php';

function env(string $key, mixed $default = null): mixed
{
    static $loaded = false;

    if (!$loaded) {
        $loaded = true;
        $envFile = __DIR__ . '/../.env';
        if (is_file($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                $trimmed = trim($line);
                if ($trimmed === '' || str_starts_with($trimmed, '#')) {
                    continue;
                }

                [$name, $value] = array_pad(explode('=', $trimmed, 2), 2, null);
                $name = trim($name);
                if ($name === '' || $value === null) {
                    continue;
                }

                $value = trim($value, " \t\n\r\0\x0B\"'");
                $_ENV[$name] = $value;
                $_SERVER[$name] = $value;
                putenv($name . '=' . $value);
            }
        }
    }

    $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);
    if ($value === false || $value === null || $value === '') {
        return $default;
    }

    return $value;
}

function secureSessionConfig(): void
{
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https'),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_name('portfolio_session');
    session_start();
}

secureSessionConfig();
date_default_timezone_set((string) env('APP_TIMEZONE', 'Africa/Conakry'));

const APP_NAME = 'Alpha Moussa Sow Portfolio';
const ADMIN_USERNAME = 'admin';
const ALLOWED_CATEGORIES = ['Web', 'Mobile', 'Gestion', 'Autres'];

function setupIsComplete(): bool
{
    $envPath = __DIR__ . '/../.env';
    $markerPath = __DIR__ . '/../.setup-complete';

    if (!is_file($envPath) || !is_file($markerPath)) {
        return false;
    }

    $contents = @file_get_contents($envPath);
    if ($contents === false) {
        return false;
    }

    return preg_match('/^ADMIN_USERNAME=/m', $contents) === 1 && preg_match('/^ADMIN_PASSWORD_HASH=/m', $contents) === 1;
}

function adminPasswordHash(): string
{
    $hash = (string) env('ADMIN_PASSWORD_HASH', '');
    if ($hash !== '') {
        return $hash;
    }

    $plainPassword = (string) env('ADMIN_PASSWORD', '');
    if ($plainPassword !== '') {
        return password_hash($plainPassword, PASSWORD_DEFAULT);
    }

    throw new RuntimeException('ADMIN_PASSWORD_HASH is not configured. Add it to your .env file or environment variables.');
}

function adminIsLoggedIn(): bool
{
    return !empty($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function redirect(string $path): void
{
    header('Location: ' . $path, true, 302);
    exit;
}

function setFlash(string $type, string $message): void
{
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message,
    ];
}

function getFlash(): ?array
{
    if (empty($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}

function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verifyCsrf(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }

    $token = $_POST['csrf_token'] ?? '';
    if (!is_string($token) || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(419);
        exit('CSRF validation failed.');
    }
}

function enforceSecurityHeaders(): void
{
    header('X-Frame-Options: SAMEORIGIN');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
    header('X-XSS-Protection: 0');
    header("Content-Security-Policy: default-src 'self'; img-src 'self' data: https:; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; script-src 'self' 'unsafe-inline'; connect-src 'self'; object-src 'none'; base-uri 'self'; frame-ancestors 'self';");

    if ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https')) {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }
}

function isLoginRateLimited(): bool
{
    $key = 'login_failures';
    $now = time();
    $failures = $_SESSION[$key] ?? ['count' => 0, 'time' => $now];

    if (($failures['count'] ?? 0) >= 5 && ($now - (int) ($failures['time'] ?? $now)) < 600) {
        return true;
    }

    return false;
}

function recordLoginFailure(): void
{
    $now = time();
    $failures = $_SESSION['login_failures'] ?? ['count' => 0, 'time' => $now];
    $last = (int) ($failures['time'] ?? $now);

    if ($now - $last > 600) {
        $_SESSION['login_failures'] = ['count' => 1, 'time' => $now];
        return;
    }

    $_SESSION['login_failures'] = ['count' => (int) ($failures['count'] ?? 0) + 1, 'time' => $last];
}

function resetLoginFailures(): void
{
    unset($_SESSION['login_failures']);
}

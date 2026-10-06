<?php
// Everything about signing in and out.

function current_user(): ?array
{
    static $user = false;
    if ($user !== false) {
        return $user;
    }
    $user = null;

    if (!empty($_SESSION['user_id'])) {
        $stmt = db()->prepare('SELECT id, first_name, last_name, email FROM users WHERE id = ?');
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch() ?: null;
    }
    return $user;
}

/** Put at the top of any page that needs a signed-in user. */
function require_login(): void
{
    if (!current_user()) {
        redirect('login.php');
    }
}

/** Put at the top of login/register so signed-in people skip them. */
function redirect_if_logged_in(): void
{
    if (current_user()) {
        redirect('dashboard.php');
    }
}

function login_user(int $userId, bool $remember): void
{
    session_regenerate_id(true);   // new session ID on login (stops session fixation)
    $_SESSION['user_id'] = $userId;

    if ($remember) {
        remember_me_issue($userId);
    }
}

function logout_user(): void
{
    remember_me_forget();
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 3600, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
}

// ------------------------------------------------------------
// "Remember me"
// The cookie holds selector:validator. Only a SHA-256 hash of the
// validator is stored, so a leaked database can't be used to log in.
// ------------------------------------------------------------

const REMEMBER_COOKIE = 'remember';

function remember_me_issue(int $userId): void
{
    $days      = app_config()['remember_days'];
    $selector  = bin2hex(random_bytes(12));
    $validator = random_bytes(32);
    $expires   = time() + $days * 86400;

    $stmt = db()->prepare(
        'INSERT INTO auth_tokens (selector, validator_hash, user_id, expires_at) VALUES (?, ?, ?, ?)'
    );
    $stmt->execute([$selector, hash('sha256', $validator), $userId, date('Y-m-d H:i:s', $expires)]);

    remember_set_cookie($selector . ':' . bin2hex($validator), $expires);
}

function auth_check_remember_cookie(): void
{
    if (!empty($_SESSION['user_id']) || empty($_COOKIE[REMEMBER_COOKIE])) {
        return;
    }

    $parts = explode(':', (string) $_COOKIE[REMEMBER_COOKIE], 2);
    if (count($parts) !== 2 || !ctype_xdigit($parts[1])) {
        remember_set_cookie('', time() - 3600);
        return;
    }
    [$selector, $validatorHex] = $parts;

    $stmt = db()->prepare('SELECT * FROM auth_tokens WHERE selector = ?');
    $stmt->execute([$selector]);
    $token = $stmt->fetch();

    $valid = $token
        && strtotime($token['expires_at']) > time()
        && hash_equals($token['validator_hash'], hash('sha256', hex2bin($validatorHex)));

    if (!$valid) {
        remember_set_cookie('', time() - 3600);
        return;
    }

    // Use the token once, then hand out a fresh one.
    db()->prepare('DELETE FROM auth_tokens WHERE id = ?')->execute([$token['id']]);
    login_user((int) $token['user_id'], true);
}

function remember_me_forget(): void
{
    if (!empty($_COOKIE[REMEMBER_COOKIE])) {
        $selector = explode(':', (string) $_COOKIE[REMEMBER_COOKIE], 2)[0];
        db()->prepare('DELETE FROM auth_tokens WHERE selector = ?')->execute([$selector]);
    }
    remember_set_cookie('', time() - 3600);
}

function remember_set_cookie(string $value, int $expires): void
{
    setcookie(REMEMBER_COOKIE, $value, [
        'expires'  => $expires,
        'path'     => '/',
        'secure'   => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}

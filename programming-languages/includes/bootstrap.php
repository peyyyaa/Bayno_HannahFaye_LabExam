<?php
// Every page starts with: require __DIR__ . '/../includes/bootstrap.php';

declare(strict_types=1);

function app_config(): array
{
    static $config = null;
    if ($config === null) {
        $config = require __DIR__ . '/../config.php';
    }
    return $config;
}

// Secure session cookie settings, set before the session starts.
$isHttps = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'secure'   => $isHttps,
    'httponly' => true,      // JavaScript can't read the session cookie
    'samesite' => 'Lax',
]);
session_start();

require __DIR__ . '/db.php';
require __DIR__ . '/helpers.php';
require __DIR__ . '/auth.php';
require __DIR__ . '/icons.php';
require __DIR__ . '/layout.php';

// Signs the person back in from a "Remember me" cookie if needed.
auth_check_remember_cookie();

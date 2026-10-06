<?php
// Returns one shared PDO connection.

function db(): PDO
{
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }

    $cfg = app_config()['db'];
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    if ($cfg['driver'] === 'sqlite') {
        $pdo = new PDO('sqlite:' . $cfg['sqlite'], null, null, $options);
    } else {
        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
            $cfg['host'], $cfg['port'], $cfg['name']
        );
        $pdo = new PDO($dsn, $cfg['user'], $cfg['pass'], $options);
    }

    return $pdo;
}

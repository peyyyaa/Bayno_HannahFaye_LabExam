<?php
// ------------------------------------------------------------
// App settings. Kept OUTSIDE public/ so the browser can never load it.
// Change the database values to match your XAMPP / Laragon setup.
// ------------------------------------------------------------

return [
    'app_name' => 'Programming Languages',

    'db' => [
        'driver'   => 'mysql',          // 'mysql' (XAMPP/Laragon) or 'sqlite'
        'host'     => '127.0.0.1',
        'port'     => 3306,
        'name'     => 'programming_languages',
        'user'     => 'root',
        'pass'     => '',               // XAMPP's root user has no password by default
        'sqlite'   => __DIR__ . '/storage/app.sqlite', // only used when driver = sqlite
    ],

    // How long "Remember me" keeps someone signed in.
    'remember_days' => 30,
];

<?php

declare(strict_types=1);

$settings = [
    'host' => '127.0.0.1',
    'port' => 3306,
    'database' => 'taskboard_php84',
    'username' => 'taskboard',
    'password' => '',
    'ssl_ca' => '',
    'ssl_verify_server_cert' => true,
];

$localPath = __DIR__ . '/database.local.php';

if (is_file($localPath)) {
    $local = require $localPath;

    if (!is_array($local)) {
        throw new RuntimeException('config/database.local.php must return an array.');
    }

    $settings = array_replace($settings, $local);
}

foreach ([
    'host' => 'DB_HOST',
    'port' => 'DB_PORT',
    'database' => 'DB_DATABASE',
    'username' => 'DB_USERNAME',
    'password' => 'DB_PASSWORD',
    'ssl_ca' => 'DB_SSL_CA',
] as $key => $environment) {
    $value = getenv($environment);

    if ($value !== false) {
        $settings[$key] = $value;
    }
}

return $settings;

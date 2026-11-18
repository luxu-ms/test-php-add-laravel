<?php

declare(strict_types=1);

final class Database
{
    public static function connect(): PDO
    {
        $settings = require dirname(__DIR__) . '/config/database.php';

        foreach (['host', 'database', 'username', 'password', 'ssl_ca'] as $key) {
            if (!is_string($settings[$key])) {
                throw new RuntimeException('Invalid MySQL configuration: ' . $key . ' must be a string.');
            }
        }

        $port = filter_var($settings['port'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 65535]]);

        if ($port === false || preg_match('/\A[a-zA-Z0-9_]{1,64}\z/', $settings['database']) !== 1
            || $settings['host'] === '' || strpbrk($settings['host'], ";\0") !== false || $settings['username'] === '') {
            throw new RuntimeException('Invalid MySQL host, port, database name, or username.');
        }

        if (!is_bool($settings['ssl_verify_server_cert'])) {
            throw new RuntimeException('ssl_verify_server_cert must be a boolean.');
        }

        if (!$settings['ssl_verify_server_cert']
            && !in_array($settings['host'], ['127.0.0.1', '::1'], true)) {
            throw new RuntimeException('Disabling MySQL certificate verification is only allowed on a loopback address.');
        }

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_STRINGIFY_FETCHES => false,
            PDO::MYSQL_ATTR_FOUND_ROWS => true,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET time_zone = '+00:00'",
        ];

        if ($settings['ssl_ca'] !== '') {
            if (!is_readable($settings['ssl_ca'])) {
                throw new RuntimeException('The configured MySQL CA certificate is not readable.');
            }

            $options[PDO::MYSQL_ATTR_SSL_CA] = $settings['ssl_ca'];
            $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = $settings['ssl_verify_server_cert'];
        } elseif (!$settings['ssl_verify_server_cert']) {
            throw new RuntimeException('A MySQL CA certificate is required for the local TLS configuration.');
        }

        return new PDO(
            sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', $settings['host'], $port, $settings['database']),
            $settings['username'],
            $settings['password'],
            $options,
        );
    }
}

<?php

declare(strict_types=1);

require dirname(__DIR__) . '/src/Database.php';

try {
    $schema = file_get_contents(dirname(__DIR__) . '/database/schema.sql');

    if ($schema === false) {
        throw new RuntimeException('Cannot read the database schema.');
    }

    $database = Database::connect();
    $exists = $database->query(
        "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'tasks'"
    )->fetchColumn();

    if ((int) $exists === 0) {
        $database->exec($schema);
    }

    echo "MySQL task schema is ready.\n";
} catch (Throwable $exception) {
    fwrite(STDERR, 'MySQL setup failed: ' . $exception->getMessage() . PHP_EOL);
    exit(1);
}

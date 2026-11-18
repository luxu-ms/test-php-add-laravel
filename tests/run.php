<?php

declare(strict_types=1);

require dirname(__DIR__) . '/src/TaskRepository.php';
require dirname(__DIR__) . '/src/Database.php';

function check(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

function rejects(callable $operation, string $exceptionClass): void
{
    try {
        $operation();
    } catch (Throwable $exception) {
        check($exception instanceof $exceptionClass, 'Unexpected exception: ' . $exception::class);
        return;
    }

    throw new RuntimeException('Expected ' . $exceptionClass);
}

$database = Database::connect();
$databaseName = $database->query('SELECT DATABASE()')->fetchColumn();

if (!str_ends_with($databaseName, '_test')) {
    throw new RuntimeException('Tests require a separate MySQL database with a name ending in _test. Set DB_DATABASE first.');
}

$schema = file_get_contents(dirname(__DIR__) . '/database/schema.sql');

if ($schema === false) {
    throw new RuntimeException('Cannot read the test database schema.');
}

$database->exec($schema);
$database->beginTransaction();

try {
    $repository = new TaskRepository($database);
    check($repository->all() === [], 'Database should start empty.');
    $id = $repository->save(null, '  First task  ', "Some details\nSecond line");
    check($repository->find($id)['title'] === 'First task', 'Titles should be trimmed.');
    check((int) $repository->find($id)['completed'] === 0, 'New tasks should be incomplete.');

    $repository = new TaskRepository($database);
    check($repository->find($id)['description'] === "Some details\nSecond line", 'Tasks should persist across repository instances.');
    $repository->save($id, 'Updated task', '');
    $repository->save($id, 'Updated task', '');
    check($repository->find($id)['title'] === 'Updated task', 'Editing should persist.');
    $repository->toggle($id);
    check((int) $repository->find($id)['completed'] === 1, 'Toggle should complete a task.');
    $secondId = $repository->save(null, 'Second task', '');
    check($repository->all()[0]['id'] === $secondId, 'Incomplete tasks should appear first.');
    $repository->toggle($id);
    check((int) $repository->find($id)['completed'] === 0, 'Toggle should reopen a task.');

    foreach (['', " \n ", str_repeat('a', 201), "\xFF"] as $title) {
        rejects(fn () => $repository->save(null, $title, ''), InvalidArgumentException::class);
    }
    $unicodeTitle = str_repeat("\u{00E9}", 200);
    $unicodeId = $repository->save(null, $unicodeTitle, str_repeat('d', 5000));
    check($repository->find($unicodeId)['title'] === $unicodeTitle, 'Limits should count Unicode characters.');
    rejects(fn () => $repository->save($id, 'Invalid edit', str_repeat('d', 5001)), InvalidArgumentException::class);
    check($repository->find($id)['title'] === 'Updated task', 'Invalid edits should leave stored data intact.');
    $repository->delete($id);
    check($repository->find($id) === null, 'Deleting should remove the task.');
    rejects(fn () => $repository->save($id, 'Missing', ''), OutOfBoundsException::class);
    rejects(fn () => $repository->toggle($id), OutOfBoundsException::class);
    rejects(fn () => $repository->delete($id), OutOfBoundsException::class);

    $repository->delete($secondId);
    $repository->delete($unicodeId);
    $organized = new TaskRepository($database);
    $report = $organized->save(null, 'Quarterly report', 'Review the budget', 'high', '2028-02-29');
    $shopping = $organized->save(null, 'Buy supplies', 'Paper for the REPORT', 'low');
    $literal = $organized->save(null, '100% done_yet', "Owner's notes");
    $organized->toggle($shopping);
    check($organized->find($report)['priority'] === 'high', 'Priority should persist.');
    check($organized->find($report)['due_date'] === '2028-02-29', 'Valid leap dates should persist.');
    check(count($organized->all('report')) === 2, 'Search should match titles and descriptions without ASCII case sensitivity.');
    check(array_column($organized->all('report', 'active'), 'id') === [$report], 'Search and active filters should combine.');
    check(array_column($organized->all('report', 'completed'), 'id') === [$shopping], 'Search and completed filters should combine.');
    check(array_column($organized->all('%'), 'id') === [$literal], 'Percent should match literally.');
    check(array_column($organized->all('_'), 'id') === [$literal], 'Underscore should match literally.');
    check(array_column($organized->all("Owner's"), 'id') === [$literal], 'Quotes should be treated as search text.');
    check($organized->all('not found') === [], 'Unmatched searches should return no tasks.');
    rejects(fn () => $organized->all('', 'invalid'), InvalidArgumentException::class);
    rejects(fn () => $organized->all(str_repeat('a', 201)), InvalidArgumentException::class);
    rejects(fn () => $organized->save($report, 'Changed', '', 'urgent'), InvalidArgumentException::class);

    foreach (['2027-02-29', '2028-02-30', '2028-13-01', '2028-2-01', '0000-01-01', '0999-12-31', "2028-01-01\0"] as $date) {
        rejects(fn () => $organized->save($report, 'Changed', '', 'high', $date), InvalidArgumentException::class);
    }

    check($organized->find($report)['title'] === 'Quarterly report', 'Invalid metadata should not modify a task.');
    $organized->save($shopping, 'Buy supplies', 'Updated notes', 'high', '2028-12-31');
    check((int) $organized->find($shopping)['completed'] === 1, 'Editing metadata should preserve completion.');
    $organized->save($shopping, 'Buy supplies', '', 'medium', '');
    check($organized->find($shopping)['due_date'] === null, 'Removing a due date should persist.');

    $emoji = $organized->save(null, "Plan \u{1F680}", "Notes \u{1F4DD}", 'low', '1000-01-01');
    check($organized->find($emoji)['title'] === "Plan \u{1F680}", 'MySQL should preserve four-byte UTF-8 characters.');
    $organized->save($emoji, "Plan \u{1F680}", '', 'low', '9999-12-31');
    check($organized->find($emoji)['due_date'] === '9999-12-31', 'MySQL should support the full allowed date range.');

    echo "All task repository tests passed.\n";
} finally {
    if ($database->inTransaction()) {
        $database->rollBack();
    }
}

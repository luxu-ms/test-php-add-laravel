<?php

declare(strict_types=1);

require dirname(__DIR__) . '/src/TaskRepository.php';
require dirname(__DIR__) . '/src/Database.php';

if (PHP_VERSION_ID < 80400 || PHP_VERSION_ID >= 80500) {
    http_response_code(500);
    exit('This application requires PHP 8.4.');
}

header('Content-Type: text/html; charset=UTF-8');
header('X-Content-Type-Options: nosniff');
header('Content-Security-Policy: default-src \'self\'; style-src \'self\'; form-action \'self\'; frame-ancestors \'none\'; base-uri \'none\'');
session_start([
    'use_strict_mode' => true,
    'cookie_httponly' => true,
    'cookie_samesite' => 'Lax',
    'cookie_secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
]);

function escape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function field(array $input, string $name): string
{
    $value = $input[$name] ?? '';

    if (!is_string($value)) {
        throw new InvalidArgumentException('Invalid form input.');
    }

    return $value;
}

function taskId(string $value): int
{
    $id = filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

    if ($id === false) {
        throw new InvalidArgumentException('Invalid task ID.');
    }

    return $id;
}

$_SESSION['csrf'] ??= bin2hex(random_bytes(32));
$csrf = $_SESSION['csrf'];
$error = null;
$editing = false;
$emptyForm = ['title' => '', 'description' => '', 'id' => null, 'priority' => 'medium', 'due_date' => ''];
$form = $emptyForm;
$search = '';
$status = 'all';
$filters = [];
$listUrl = '/';
$method = $_SERVER['REQUEST_METHOD'];

try {
    $repository = new TaskRepository(Database::connect());
    $allTasks = $repository->all();
    $tasks = $allTasks;
    $search = trim(field($_GET, 'q'));
    $status = field($_GET, 'status');
    $status = $status === '' ? 'all' : $status;
    $tasks = $repository->all($search, $status);
    $filters = array_filter(
        ['q' => $search, 'status' => $status === 'all' ? '' : $status],
        static fn (string $value): bool => $value !== '',
    );
    $listUrl = '/' . ($filters === [] ? '' : '?' . http_build_query($filters));

    if ($method === 'POST') {
        if (!hash_equals($csrf, field($_POST, 'csrf'))) {
            http_response_code(403);
            exit('Your session expired or the form token is invalid. Reload the page and try again.');
        }

        $action = field($_POST, 'action');

        if (!in_array($action, ['save', 'toggle', 'delete'], true)) {
            throw new InvalidArgumentException('Unknown task action.');
        }

        $rawId = field($_POST, 'id');
        $id = $rawId === '' && $action === 'save' ? null : taskId($rawId);

        if ($action === 'save') {
            $editing = $id !== null;
            $form = [
                'id' => $id,
                'title' => field($_POST, 'title'),
                'description' => field($_POST, 'description'),
                'priority' => field($_POST, 'priority'),
                'due_date' => field($_POST, 'due_date'),
            ];
            $repository->save($id, $form['title'], $form['description'], $form['priority'], $form['due_date']);
        } elseif ($action === 'toggle') {
            $repository->toggle($id);
        } else {
            $repository->delete($id);
        }

        $_SESSION['notice'] = match ($action) {
            'save' => $id === null ? 'Task added.' : 'Task updated.',
            'toggle' => 'Task status updated.',
            'delete' => 'Task deleted.',
        };
        header('Location: ' . $listUrl, true, 303);
        exit;
    }

    if ($method !== 'GET') {
        header('Allow: GET, POST');
        http_response_code(405);
        exit('Method not allowed.');
    }

    if (isset($_GET['edit'])) {
        $form = $repository->find(taskId(field($_GET, 'edit')));

        if ($form === null) {
            throw new OutOfBoundsException('This task no longer exists.');
        }

        $editing = true;
    }
} catch (InvalidArgumentException $exception) {
    http_response_code(422);
    $error = $exception->getMessage();
} catch (OutOfBoundsException $exception) {
    http_response_code(404);
    $error = $exception->getMessage();
    $form = $emptyForm;
    $editing = false;
} catch (Throwable $exception) {
    error_log((string) $exception);
    http_response_code(500);
    exit('The task manager is unavailable. Check the server logs, MySQL connection settings, and database setup.');
}

$notice = $_SESSION['notice'] ?? null;
unset($_SESSION['notice']);
$completed = count(array_filter($allTasks, static fn (array $task): bool => (int) $task['completed'] === 1));
$today = date('Y-m-d');

require dirname(__DIR__) . '/views/tasks.php';

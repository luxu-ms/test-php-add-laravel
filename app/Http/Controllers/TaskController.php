<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Middleware\TaskCsrf;
use Illuminate\Http\Request;
use InvalidArgumentException;
use OutOfBoundsException;
use Symfony\Component\HttpFoundation\Response;
use TaskRepository;

final class TaskController
{
    public function __invoke(Request $request, TaskRepository $repository): Response
    {
        $error = null;
        $editing = false;
        $emptyForm = ['title' => '', 'description' => '', 'id' => null, 'priority' => 'medium', 'due_date' => ''];
        $form = $emptyForm;
        $search = '';
        $status = 'all';
        $filters = [];
        $listUrl = '/';
        $statusCode = 200;
        $query = $request->query->all();
        $body = $request->request->all();

        try {
            $allTasks = $repository->all();
            $tasks = $allTasks;
            $search = trim(self::field($query, 'q'));
            $status = self::field($query, 'status');
            $status = $status === '' ? 'all' : $status;
            $tasks = $repository->all($search, $status);
            $filters = array_filter(
                ['q' => $search, 'status' => $status === 'all' ? '' : $status],
                static fn (string $value): bool => $value !== '',
            );
            $listUrl = '/' . ($filters === [] ? '' : '?' . http_build_query($filters));

            if ($request->isMethod('POST')) {
                if (($rejection = TaskCsrf::rejection($request)) !== null) {
                    return $rejection;
                }

                $action = self::field($body, 'action');

                if (!in_array($action, ['save', 'toggle', 'delete'], true)) {
                    throw new InvalidArgumentException('Unknown task action.');
                }

                $rawId = self::field($body, 'id');
                $id = $rawId === '' && $action === 'save' ? null : self::taskId($rawId);

                if ($action === 'save') {
                    $editing = $id !== null;
                    $form = [
                        'id' => $id,
                        'title' => self::field($body, 'title'),
                        'description' => self::field($body, 'description'),
                        'priority' => self::field($body, 'priority'),
                        'due_date' => self::field($body, 'due_date'),
                    ];
                    $repository->save($id, $form['title'], $form['description'], $form['priority'], $form['due_date']);
                } elseif ($action === 'toggle') {
                    $repository->toggle($id);
                } else {
                    $repository->delete($id);
                }

                $request->session()->put('notice', match ($action) {
                    'save' => $id === null ? 'Task added.' : 'Task updated.',
                    'toggle' => 'Task status updated.',
                    'delete' => 'Task deleted.',
                });

                return response('', 303, ['Location' => $listUrl]);
            }

            if (!$request->isMethod('GET')) {
                return response('Method not allowed.', 405, ['Allow' => 'GET, POST']);
            }

            if (isset($query['edit'])) {
                $form = $repository->find(self::taskId(self::field($query, 'edit')));

                if ($form === null) {
                    throw new OutOfBoundsException('This task no longer exists.');
                }

                $editing = true;
            }
        } catch (InvalidArgumentException $exception) {
            $statusCode = 422;
            $error = $exception->getMessage();
        } catch (OutOfBoundsException $exception) {
            $statusCode = 404;
            $error = $exception->getMessage();
            $form = $emptyForm;
            $editing = false;
        }

        return response()->view('tasks', [
            'allTasks' => $allTasks,
            'tasks' => $tasks,
            'search' => $search,
            'status' => $status,
            'filters' => $filters,
            'listUrl' => $listUrl,
            'form' => $form,
            'editing' => $editing,
            'error' => $error,
            'notice' => $request->session()->pull('notice'),
            'csrf' => $request->session()->token(),
            'completed' => count(array_filter($allTasks, static fn (array $task): bool => (int) $task['completed'] === 1)),
            'today' => date('Y-m-d'),
        ], $statusCode);
    }

    private static function field(array $input, string $name): string
    {
        $value = $input[$name] ?? '';

        if (!is_string($value)) {
            throw new InvalidArgumentException('Invalid form input.');
        }

        return $value;
    }

    private static function taskId(string $value): int
    {
        $id = filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

        if ($id === false) {
            throw new InvalidArgumentException('Invalid task ID.');
        }

        return $id;
    }
}

<?php

declare(strict_types=1);

use App\Http\Middleware\TaskCsrf;
use App\Http\Middleware\TaskResponseHeaders;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Http\Middleware\HandleCors;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(web: __DIR__ . '/../routes/web.php')
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->prepend(TaskResponseHeaders::class);
        $middleware->remove(HandleCors::class);
        $middleware->trimStrings(except: [fn (Request $request): bool => $request->is('/')]);
        $middleware->convertEmptyStringsToNull(except: [fn (Request $request): bool => $request->is('/')]);
        $middleware->web(replace: [PreventRequestForgery::class => TaskCsrf::class]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->report(function (Throwable $exception): bool {
            error_log((string) $exception);
            return false;
        });
        $exceptions->render(function (Throwable $exception, Request $request) {
            if ($request->is('/') && $exception instanceof MethodNotAllowedHttpException) {
                return response('Method not allowed.', 405, ['Allow' => 'GET, POST', ...TaskResponseHeaders::HEADERS]);
            }

            if ($exception instanceof HttpExceptionInterface && $exception->getStatusCode() < 500) {
                return null;
            }

            return response(
                'The task manager is unavailable. Check the server logs, MySQL connection settings, and database setup.',
                500,
                TaskResponseHeaders::HEADERS,
            );
        });
    })
    ->create();

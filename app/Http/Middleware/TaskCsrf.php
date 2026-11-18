<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;

final class TaskCsrf
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('POST')) {
            $token = $request->request->all()['csrf'] ?? '';
            // Defer rejection until after list/filter reads, preserving the original error ordering.
            $request->attributes->set(self::class, is_string($token)
                ? hash_equals($request->session()->token(), $token)
                : new InvalidArgumentException('Invalid form input.'));
        }

        return $next($request);
    }

    public static function rejection(Request $request): ?Response
    {
        $result = $request->attributes->get(self::class);

        if ($result instanceof InvalidArgumentException) {
            throw $result;
        }

        return $result === true ? null : response(
            'Your session expired or the form token is invalid. Reload the page and try again.',
            403,
        );
    }
}

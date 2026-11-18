<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class TaskResponseHeaders
{
    public const HEADERS = [
        'Content-Type' => 'text/html; charset=UTF-8',
        'X-Content-Type-Options' => 'nosniff',
        'Content-Security-Policy' => "default-src 'self'; style-src 'self'; form-action 'self'; frame-ancestors 'none'; base-uri 'none'",
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        $response->headers->add(self::HEADERS);

        return $response;
    }
}

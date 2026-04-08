<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class IdempotencyMiddleware
{
    private const CACHE_TTL = 86400;

    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('GET')) {
            return $next($request);
        }

        $key = $request->header('X-Idempotency-Key');

        if (!$key) {
            return $next($request);
        }

        $cacheKey = "idempotency_key:{$key}";

        if ($cachedResponse = Cache::get($cacheKey)) {
            return response()->json(
                json_decode($cachedResponse, true),
                Response::HTTP_OK
            )->header('X-Idempotency-Cache', 'HIT');
        }

        $lock = Cache::lock("{$cacheKey}_lock", 10);

        if (!$lock->get()) {
            return response()->json([
                'message' => 'Запрос уже обрабатывается. Пожалуйста, подождите.'
            ], Response::HTTP_TOO_MANY_REQUESTS);
        }

        try {
            /** @var Response $response */
            $response = $next($request);

            if ($response->isSuccessful()) {
                Cache::put($cacheKey, $response->getContent(), self::CACHE_TTL);
            }

            return $response->header('X-Idempotency-Cache', 'MISS');
        } finally {
            $lock->release();
        }
    }
}

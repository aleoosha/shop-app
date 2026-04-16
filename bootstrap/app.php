<?php

use Aleoosha\HiveMind\Http\Middleware\AltruismMiddleware;
use App\Http\Middleware\AssignGuestCartToken;
use App\Http\Middleware\IdempotencyMiddleware;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(AltruismMiddleware::class);
        $middleware->api(prepend: [
            IdempotencyMiddleware::class,
        ]);
        $middleware->append(AssignGuestCartToken::class);
        $middleware->encryptCookies(except: [
            'cart_token',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (DomainException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }
        });
        $exceptions->render(function (UniqueConstraintViolationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'Ресурс с такими данными уже существует (дубликат)',
                    'errors' => [
                        'database' => ['Нарушение уникальности данных в системе.'],
                    ],
                ], 422);
            }
        });
    })->create();

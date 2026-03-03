<?php

use App\Http\Middleware\RoleCheckMiddleware;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->statefulApi();
        $middleware->alias([
            'Role.check' => RoleCheckMiddleware::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Authentication Exception
        $exceptions->render(function (AuthenticationException $e) {
            return response()->json([
                'success' => false,
                'message' => "Unauthenticated",
                'status' => 401
            ], 401);
        });

        // Authorization Exception
        $exceptions->render(function (AuthorizationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage() ?: 'Forbidden',
                'status'  => 403,
            ], 403);
        });
    })->create();

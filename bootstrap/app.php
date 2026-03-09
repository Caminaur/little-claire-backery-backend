<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

use Illuminate\Http\Middleware\HandleCors;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // CORS globally
        $middleware->append(HandleCors::class);

        // Add Sanctum stateful middleware to the existing "api" group (don't override defaults)
        $middleware->appendToGroup('api', EnsureFrontendRequestsAreStateful::class);

        // Enable cookies + session on API requests (needed for $request->session() in API auth)
        $middleware->appendToGroup('api', EncryptCookies::class);
        $middleware->appendToGroup('api', AddQueuedCookiesToResponse::class);
        $middleware->appendToGroup('api', StartSession::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

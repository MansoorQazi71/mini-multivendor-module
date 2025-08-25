<?php

use App\Http\Middleware\EnsureRole;
use Illuminate\Foundation\Application;
use App\Providers\EventServiceProvider;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => EnsureRole::class,
        ]);
    })
    ->withProviders([
        EventServiceProvider::class, // ← register it here
    ])
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

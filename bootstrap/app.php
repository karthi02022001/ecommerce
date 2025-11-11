<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: [
            __DIR__ . '/../routes/web.php',
            __DIR__ . '/../routes/admin.php',
        ],
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

    // ✅ Apply global web middleware
    $middleware->web(append: [
        \App\Http\Middleware\SetLocale::class, // <-- Add this line
    ]);
        // Register middleware aliases
        $middleware->alias([
            'admin.permission' => \App\Http\Middleware\AdminPermissionMiddleware::class,
            'admin.role' => \App\Http\Middleware\AdminRoleMiddleware::class,
            'guest.admin' => \App\Http\Middleware\RedirectIfAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

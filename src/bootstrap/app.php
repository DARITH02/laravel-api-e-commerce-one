<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',

        health: '/up',
    )
    ->withProviders([
        // Add Cloudinary service provider here
        CloudinaryLabs\CloudinaryLaravel\CloudinaryServiceProvider::class,
    ])

    ->withMiddleware(function (Middleware $middleware): void {

        $middleware->group('api', [
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);

        // $middleware->alias([
        //     'auth' => \App\Http\Middleware\ApiAuth::class,
        // ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

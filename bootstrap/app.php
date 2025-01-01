<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',    // Fixed DIR to __DIR__
        api: __DIR__.'/../routes/api.php',     // Fixed DIR to __DIR__
        commands: __DIR__.'/../routes/console.php',  // Fixed DIR to __DIR__
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Apply SetLocale middleware to web group
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
            \App\Http\Middleware\DetectCountry::class,
        ]);

        // Define api middleware group with correct order
        $middleware->group('api', [
            \App\Http\Middleware\SetLocale::class,  // SetLocale should be early
            \App\Http\Middleware\ApiCountryDetection::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);

        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'country.detection' => \App\Http\Middleware\ApiCountryDetection::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
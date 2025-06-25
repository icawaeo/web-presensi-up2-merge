<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
            'karyawan' => App\Http\Middleware\Karyawan::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class, 
            'no-cache' => \App\Http\Middleware\PreventPageCaching::class, 
            'auth.any' => \App\Http\Middleware\AuthenticateAnyGuard::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            '/register',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

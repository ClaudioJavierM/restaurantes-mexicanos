<?php

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
        // Exclude webhooks from CSRF verification
        $middleware->validateCsrfTokens(except: [
            "webhooks/*",
            "stripe/webhook",
            "chat/*",
        ]);
        
        // Cache headers must be prepended to override session middleware
        $middleware->web(prepend: [
            \App\Http\Middleware\SetPublicCacheHeaders::class,
        ]);

        // Add locale detection middleware to web group
        $middleware->web(append: [
            \App\Http\Middleware\DomainLocale::class,
            \App\Http\Middleware\SetLocaleFromDomain::class,
            \App\Http\Middleware\LocaleDetect::class,
        ]);

        // Public cache headers: prepend globally so it runs LAST on response
        // (after Livewire's DisableBackButtonCache overwrites cache-control)
        $middleware->prepend(\App\Http\Middleware\PublicCacheHeaders::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Log all 403 errors for debugging
        $exceptions->report(function (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            if ($e->getStatusCode() === 403) {
                \Log::emergency('403 FORBIDDEN ERROR DETECTED', [
                    'url' => request()->fullUrl(),
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]);
            }
        });
    })->create();

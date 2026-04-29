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
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'specialist' => \App\Http\Middleware\SpecialistMiddleware::class,
            'client' => \App\Http\Middleware\ClientMiddleware::class,
            'rate.limit' => \App\Http\Middleware\CustomRateLimiter::class,
            'audit' => \App\Http\Middleware\AuditLogMiddleware::class,
            'suspicious.activity' => \App\Http\Middleware\DetectSuspiciousActivity::class,
            'onboarding' => \App\Http\Middleware\EnsureOnboardingComplete::class,
            'plan.limits' => \App\Http\Middleware\CheckPlanLimits::class,
        ]);
        
        // Add middleware to web group
        $middleware->web(append: [
            \App\Http\Middleware\TrackAffiliateReferral::class,
            \App\Http\Middleware\SetLocale::class,
            \App\Http\Middleware\TrackConversionEvents::class,
            // Temporar dezactivat pentru dezvoltare locală
            // \App\Http\Middleware\DetectSuspiciousActivity::class,
        ]);
        
        // Add audit logging to specific routes
        $middleware->appendToGroup('audit', [
            \App\Http\Middleware\AuditLogMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

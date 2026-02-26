<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up'
    )
    ->withMiddleware(function (Middleware $middleware): void {

        // ✅ Global web middlewares
        $middleware->web([
            \App\Http\Middleware\CaptureReferral::class,
            \App\Http\Middleware\TrackReferral::class,
            \App\Http\Middleware\PreventBackHistory::class,
        ]);

        // ✅ Route middleware alias (Laravel 11 replacement for Kernel.php)
        $middleware->alias([
            'require.referral' => \App\Http\Middleware\RequireReferral::class,
        ]);

        // ✅ Guest redirect handling
        $middleware->redirectGuestsTo(function (\Illuminate\Http\Request $request) {
            if ($request->is('admin') || $request->is('admin/*') || $request->is('staff') || $request->is('staff/*')) {
                return route('staff.login');
            }
            if ($request->is('billing') || $request->is('billing/*')) {
                return route('billing.login');
            }
            return route('login');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();

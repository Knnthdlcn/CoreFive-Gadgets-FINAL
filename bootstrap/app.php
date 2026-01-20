<?php

use App\Http\Middleware\HandleAppearance;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\EnsureNotBanned;
use App\Http\Middleware\ClearBuyNowOnNavigation;
use App\Http\Middleware\PreventAdminFromWeb;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Illuminate\Auth\Middleware\EnsureEmailIsVerified;
use Illuminate\Http\Request;
use Illuminate\Http\Exceptions\PostTooLargeException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->encryptCookies(except: ['appearance', 'sidebar_state']);

        $middleware->web(append: [
            PreventAdminFromWeb::class,
            EnsureNotBanned::class,
            ClearBuyNowOnNavigation::class,
            HandleAppearance::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);

        $middleware->alias([
            'admin' => IsAdmin::class,
            'verified' => EnsureEmailIsVerified::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (PostTooLargeException $e, Request $request) {
            // Happens before Laravel can validate; show a friendly UI message.
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Upload too large. Maximum allowed is 5MB.',
                ], 413);
            }

            return response()->view('errors.413', [
                'message' => 'Upload too large. Maximum allowed is 5MB.',
            ], 413);
        });
    })->create();

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
    ->withMiddleware(function (Middleware $middleware): void {
        // Web middleware group (automatically includes CSRF, sessions, etc.)
        $middleware->web(append: [
            \App\Http\Middleware\VerifyCsrfToken::class,
        ]);
        
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'account.status' => \App\Http\Middleware\AccountStatusMiddleware::class,
        ]);
    })
    ->withProviders([
        \App\Providers\AdminLTEServiceProvider::class,
    ])
    ->withExceptions(function (Exceptions $exceptions): void {
        // Redirect access-denied cases to landing page for web (HTML) requests
        $exceptions->render(function (\Illuminate\Auth\Access\AuthorizationException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
                return response()->json(['message' => 'Forbidden'], 403);
            }
            return redirect()->route('landing')->with('warning', 'Access denied.');
        });

        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
                return response()->json(['message' => 'Forbidden'], 403);
            }
            return redirect()->route('landing')->with('warning', 'Access denied.');
        });

        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, \Illuminate\Http\Request $request) {
            if (method_exists($e, 'getStatusCode') && $e->getStatusCode() === 403) {
                if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
                    return response()->json(['message' => 'Forbidden'], 403);
                }
                return redirect()->route('landing')->with('warning', 'Access denied.');
            }
        });
    })->create();

<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
            // Register custom route files with web middleware
            $routeFiles = [
                'properties.php',
                'optimized_properties.php',
                'agents.php',
                'agent_panel.php',
                'leads.php',
                'documents.php',
                'reports.php',
                'financial.php',
                'taxes.php',
                'analytics.php',
                'maintenance.php',
                'companies.php',
                'rentals.php',
                'insurance.php',
                'inspections.php',
                'appraisals.php',
                'warranties.php',
                'ai.php',
                'blockchain.php',
                'defi.php',
                'metaverse.php',
                'iot.php',
                'gamification.php',
                'geospatial.php',
                'marketing.php',
                'subscription.php',
                'user.php',
            ];

            foreach ($routeFiles as $file) {
                $path = base_path("routes/{$file}");
                if (file_exists($path)) {
                    Route::middleware('web')->group($path);
                }
            }
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'auth' => \App\Http\Middleware\Authenticate::class,
            'agent' => \App\Http\Middleware\AgentMiddleware::class,
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

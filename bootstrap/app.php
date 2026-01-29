<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        channels: __DIR__ . '/../routes/channels.php',
        health: '/up',
        then: function () {
            // Register custom route files with web middleware
            $routeFiles = [
                'properties.php',
                'optimized_properties.php',
                'projects.php',
                'agents.php',
                'agent_panel.php',
                'leads.php',
                'documents.php',
                'reports.php',
                'financial.php',
                'taxes.php',
                'analytics.php',
                'maintenance.php',
                'inventory.php',
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
                    Route::middleware(['web', 'request.logger'])->group($path);
                }
            }
            
            // Also apply to main web.php routes
            Route::middleware(['web', 'request.logger'])->group(base_path('routes/web.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'auth' => \App\Http\Middleware\Authenticate::class,
            'agent' => \App\Http\Middleware\AgentMiddleware::class,
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'trackactivity' => \App\Http\Middleware\TrackUserActivity::class,
            'banned' => \App\Http\Middleware\CheckBannedUser::class,
            '2fa' => \App\Http\Middleware\CheckTwoFactor::class,
            'fingerprint' => \App\Http\Middleware\TrackDeviceFingerprint::class,
            'request.logger' => \App\Http\Middleware\RequestLogger::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->reportable(function (Throwable $e) {
            try {
                \Illuminate\Support\Facades\DB::table('system_error_logs')->insert([
                    'message' => $e->getMessage(),
                    'code' => $e->getCode(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                    'url' => request()->fullUrl(),
                    'method' => request()->method(),
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'user_id' => auth()->id(),
                    'request_data' => json_encode(request()->all()),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } catch (\Exception $ex) {
                // Fail silently to avoid infinite loops if DB fails
            }
        });
    })->create();

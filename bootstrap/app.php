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
            // Register public routes first (without auth)
            Route::middleware(['web', 'request.logger'])->group(base_path('routes/public.php'));

            // Register custom route files with web middleware and auth
            $routeFiles = [
                'admin.php',
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
                'bigdata.php',
                'blockchain.php',
                'defi.php',
                'metaverse.php',
                'iot.php',
                'gamification.php',
                'geospatial.php',
                'marketing.php',
                'subscription.php',
                'user.php',
                'modules.php',
                'performance.php',
            ];

            foreach ($routeFiles as $file) {
                $path = base_path("routes/{$file}");
                if (file_exists($path)) {
                    Route::middleware(['web', 'request.logger', 'auth'])->group($path);
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
            'trackactivity' => \App\Http\Middleware\TrackUserActivity::class,
            'banned' => \App\Http\Middleware\CheckBannedUser::class,
            '2fa' => \App\Http\Middleware\CheckTwoFactor::class,
            'fingerprint' => \App\Http\Middleware\TrackDeviceFingerprint::class,
            'request.logger' => \App\Http\Middleware\RequestLogger::class,
            'permission' => \App\Http\Middleware\PermissionMiddleware::class,
            'financial.permission' => \App\Http\Middleware\FinancialPermissionMiddleware::class,
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

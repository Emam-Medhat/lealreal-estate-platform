<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

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
                'enterprise.php',
                'rentals.php',
                'insurance.php',
                'inspections.php',
                'appraisals.php',
                'warranties.php',
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
                'global_services.php',
                'developer.php',
                'auctions.php',
                'content.php',
                'web_services.php',
                'payment.php',
            ];

            foreach ($routeFiles as $file) {
                $path = base_path("routes/{$file}");
                if (file_exists($path)) {
                    Route::middleware(['web', 'request.logger', 'auth'])->group($path);
                }
            }
            
            // Register AI routes without auth middleware for public API endpoints
            Route::middleware(['web', 'request.logger'])->group(base_path('routes/ai.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->validateCsrfTokens(except: [
            '/lead-analytics/export',
            'api/ai/*',          // AI AJAX endpoints
            'webhooks/payment/*', // Payment webhooks
        ]);

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
            'ai.rate' => \App\Http\Middleware\AiRateLimitMiddleware::class,
            'localization' => \App\Http\Middleware\LocalizationMiddleware::class,
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\LocalizationMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->reportable(function (Throwable $e) {
            // Local/testing: skip DB writes so broken pages (e.g. missing route) return immediately.
            if (app()->environment(['local', 'testing'])) {
                return;
            }

            // Missing named routes are common while developing; full trace + request JSON is huge and slow.
            if ($e instanceof RouteNotFoundException) {
                return;
            }

            try {
                $trace = $e->getTraceAsString();
                if (strlen($trace) > 16_000) {
                    $trace = substr($trace, 0, 16_000)."\n...[truncated]";
                }

                try {
                    $requestData = json_encode(
                        request()->except(['password', 'password_confirmation', '_token']),
                        JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE
                    );
                } catch (\Throwable) {
                    $requestData = '{"encode":"failed"}';
                }
                if (strlen($requestData) > 8_000) {
                    $requestData = substr($requestData, 0, 8_000).'...[truncated]';
                }

                \Illuminate\Support\Facades\DB::table('system_error_logs')->insert([
                    'message' => substr($e->getMessage(), 0, 2000),
                    'code' => $e->getCode(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $trace,
                    'url' => (string) request()->fullUrl(),
                    'method' => request()->method(),
                    'ip' => request()->ip(),
                    'user_agent' => substr((string) request()->userAgent(), 0, 512),
                    'user_id' => auth()->id(),
                    'request_data' => $requestData,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } catch (\Exception $ex) {
                // Fail silently to avoid infinite loops if DB fails
            }
        });
    })->create();

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
                'taxes.php',
                'maintenance.php',
                'reports.php',
                'agents.php',
                'companies.php',
                'leads.php',
                'analytics.php',
                'financial.php',
                'rentals.php',
                'insurance.php',
                'documents.php',
                'inspections.php',
                'appraisals.php',
                'warranties.php',
                'agent_panel.php',
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
            'agent' => \App\Http\Middleware\AgentMiddleware::class,
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'verifyemail' => \App\Http\Middleware\VerifyEmailMiddleware::class,
            'kyc' => \App\Http\Middleware\CheckKycVerification::class,
            'profilecomplete' => \App\Http\Middleware\CheckProfileComplete::class,
            'trackactivity' => \App\Http\Middleware\TrackUserActivity::class,
            'role' => \App\Http\Middleware\CheckRole::class, // [NEW]
            'permission' => \App\Http\Middleware\CheckPermission::class, // [NEW]
            'banned' => \App\Http\Middleware\CheckBannedUser::class, // [NEW]
            '2fa' => \App\Http\Middleware\CheckTwoFactor::class, // [NEW]
            'fingerprint' => \App\Http\Middleware\CheckDeviceFingerprint::class, // [NEW]
            'property.owner' => \App\Http\Middleware\CheckPropertyOwnership::class,
            'property.status' => \App\Http\Middleware\CheckPropertyStatus::class,
            'property.subscription' => \App\Http\Middleware\CheckPropertySubscription::class,
            'email.verified' => \App\Http\Middleware\EnsureEmailIsVerified::class,
            'device.fingerprint' => \App\Http\Middleware\TrackDeviceFingerprint::class,
            'track.activity' => \App\Http\Middleware\TrackUserActivity::class,
            'premium' => \App\Http\Middleware\CheckPremiumSubscription::class,
            'cache.response' => \App\Http\Middleware\CacheResponse::class, // [NEW] For performance optimization
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

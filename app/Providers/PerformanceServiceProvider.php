<?php

namespace App\Providers;

use App\Services\CacheService;
use App\Http\Middleware\PerformanceMonitor;
use App\Http\Middleware\CacheMiddleware;
use App\Http\Middleware\RequestLoggerMiddleware;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class PerformanceServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register()
    {
        // Register CacheService as singleton
        $this->app->singleton(CacheService::class, function ($app) {
            return new CacheService();
        });

        // Register performance monitoring services
        $this->app->singleton('performance.monitor', function ($app) {
            return new \App\Services\PerformanceMonitorService();
        });

        // Register query logger
        $this->app->singleton('performance.query_logger', function ($app) {
            return new \App\Services\QueryLoggerService();
        });

        // Register metrics collector
        $this->app->singleton('performance.metrics', function ($app) {
            return new \App\Services\MetricsCollectorService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot()
    {
        // Register middleware
        $this->registerMiddleware();

        // Register performance routes
        $this->registerRoutes();

        // Register performance observers
        $this->registerObservers();

        // Configure performance settings
        $this->configurePerformance();
    }

    /**
     * Register middleware
     */
    private function registerMiddleware()
    {
        // Register middleware aliases
        $this->app['router']->aliasMiddleware('performance.monitor', PerformanceMonitor::class);
        $this->app['router']->aliasMiddleware('cache.middleware', CacheMiddleware::class);
        $this->app['router']->aliasMiddleware('request.logger', RequestLoggerMiddleware::class);

        // Apply middleware to specific route groups
        $this->app['router']->middlewareGroup('performance', [
            'web',
            'auth',
            'performance.monitor',
            'cache.middleware',
            'request.logger',
        ]);

        $this->app['router']->middlewareGroup('api.performance', [
            'api',
            'auth:api',
            'throttle:60,1',
            'performance.monitor',
            'request.logger',
        ]);
    }

    /**
     * Register routes
     */
    private function registerRoutes()
    {
        // Load performance routes
        if (file_exists(base_path('routes/performance.php'))) {
            require base_path('routes/performance.php');
        }
    }

    /**
     * Register observers
     */
    private function registerObservers()
    {
        // Register performance observers
        \App\Models\User::observe(\App\Observers\UserObserver::class);
        \App\Models\Lead::observe(\App\Observers\LeadObserver::class);
        \App\Models\Property::observe(\App\Observers\PropertyObserver::class);
        \App\Models\Appointment::observe(\App\Observers\AppointmentObserver::class);
    }

    /**
     * Configure performance settings
     */
    private function configurePerformance()
    {
        // Configure query logging
        if (app()->environment('local', 'testing')) {
            \DB::listen(function ($query) {
                $this->app['performance.query_logger']->logQuery($query);
            });
        }

        // Configure performance monitoring
        if (config('performance.monitoring.enabled', true)) {
            $this->app['performance.monitor']->startMonitoring();
        }

        // Configure cache warming
        if (config('performance.cache_warming.enabled', false)) {
            $this->scheduleCacheWarming();
        }
    }

    /**
     * Schedule cache warming
     */
    private function scheduleCacheWarming()
    {
        $schedule = $this->app->make(\Illuminate\Console\Scheduling\Schedule::class);
        
        $schedule->command('cache:warm-up')
            ->daily()
            ->at('02:00')
            ->withoutOverlapping()
            ->runInBackground();
    }
}

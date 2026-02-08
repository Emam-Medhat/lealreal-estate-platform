<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register performance monitoring service
        $this->app->singleton(\App\Services\CacheService::class, function ($app) {
            return new \App\Services\CacheService();
        });

        // Register optimized services
        $this->app->bind(\App\Services\OptimizedPropertyService::class, function ($app) {
            return new \App\Services\OptimizedPropertyService(
                $app->make(\App\Repositories\Contracts\PropertyRepositoryInterface::class)
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Set default string length for older MySQL versions
        Schema::defaultStringLength(191);

        // Register model observers for automatic cache invalidation and logging
        \App\Models\User::observe(\App\Observers\UserObserver::class);
        \App\Models\Property::observe(\App\Observers\PropertyObserver::class);
        \App\Models\Agent::observe(\App\Observers\AgentObserver::class);
        \App\Models\Developer::observe(\App\Observers\DeveloperObserver::class);
        \App\Models\AgentTask::observe(\App\Observers\AgentTaskObserver::class);
        \App\Models\Lead::observe(\App\Observers\LeadObserver::class);

        // Force eager loading in development to detect N+1 problems
        if (app()->environment('local')) {
            \Illuminate\Database\Eloquent\Model::preventLazyLoading(! app()->environment('testing'));
            \Illuminate\Database\Eloquent\Model::preventSilentlyDiscardingAttributes(! app()->environment('testing'));
        }
    }
}

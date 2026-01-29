<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \App\Models\User::observe(\App\Observers\UserObserver::class);
        \App\Models\Property::observe(\App\Observers\PropertyObserver::class);
        \App\Models\Agent::observe(\App\Observers\AgentObserver::class);
        \App\Models\Developer::observe(\App\Observers\DeveloperObserver::class);
        \App\Models\AgentTask::observe(\App\Observers\AgentTaskObserver::class);
    }
}

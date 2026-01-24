<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Company;
use App\Observers\CompanyObserver;
use App\Services\CompanyService;
use App\Services\CompanyAnalyticsService;
use App\Services\CompanyPortfolioService;

class CompanyServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(CompanyService::class, function ($app) {
            return new CompanyService();
        });

        $this->app->singleton(CompanyAnalyticsService::class, function ($app) {
            return new CompanyAnalyticsService();
        });

        $this->app->singleton(CompanyPortfolioService::class, function ($app) {
            return new CompanyPortfolioService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Company::observe(CompanyObserver::class);
        // CompanyMember::observe(CompanyMemberObserver::class);
    }
}

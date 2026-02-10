<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\AgentRepositoryInterface;
use App\Repositories\Eloquent\AgentRepository;

use App\Repositories\Contracts\CompanyRepositoryInterface;
use App\Repositories\Eloquent\CompanyRepository;
use App\Repositories\Contracts\AdAnalyticsRepositoryInterface;
use App\Repositories\Eloquent\AdAnalyticsRepository;
use App\Repositories\Contracts\ServiceProviderRepositoryInterface;
use App\Repositories\Eloquent\ServiceProviderRepository;
use App\Repositories\Contracts\InvestorRepositoryInterface;
use App\Repositories\Eloquent\InvestorRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Repositories\Contracts\LeadRepositoryInterface::class,
            \App\Repositories\Eloquent\LeadRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\PropertyRepositoryInterface::class,
            \App\Repositories\Eloquent\PropertyRepository::class
        );

        $this->app->bind(
            AgentRepositoryInterface::class,
            AgentRepository::class
        );

        $this->app->bind(
            CompanyRepositoryInterface::class,
            CompanyRepository::class
        );
        
        $this->app->bind(
            InvestorRepositoryInterface::class,
            InvestorRepository::class
        );

        $this->app->bind(
            AdAnalyticsRepositoryInterface::class,
            AdAnalyticsRepository::class
        );

        $this->app->bind(
            ServiceProviderRepositoryInterface::class,
            ServiceProviderRepository::class
        );

        // Check if User Repository files exist before binding to avoid errors if they are placeholders
        $this->app->bind(
            \App\Repositories\Contracts\UserRepositoryInterface::class,
            \App\Repositories\Eloquent\UserRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\AgentLeadRepositoryInterface::class,
            \App\Repositories\Eloquent\AgentLeadRepository::class
        );

        $this->app->bind(
            \App\Repositories\InvoiceRepositoryInterface::class,
            \App\Repositories\InvoiceRepository::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}

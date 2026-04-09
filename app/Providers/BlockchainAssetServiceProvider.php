<?php

namespace App\Providers;

use App\Services\FractionalOwnershipService;
use App\Services\VerificationService;
use Illuminate\Support\ServiceProvider;

class BlockchainAssetServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(FractionalOwnershipService::class, function ($app) {
            return new FractionalOwnershipService();
        });

        $this->app->singleton(VerificationService::class, function ($app) {
            return new VerificationService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}

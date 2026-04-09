<?php

namespace App\Providers;

use App\Services\UserService;
use App\Services\ProfileService;
use App\Services\KycService;
use App\Services\WalletService;
use Illuminate\Support\ServiceProvider;

class UserServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Bind User Service
        $this->app->singleton(UserService::class, function ($app) {
            return new UserService();
        });

        // Bind Profile Service
        $this->app->singleton(ProfileService::class, function ($app) {
            return new ProfileService();
        });

        // Bind KYC Service
        $this->app->singleton(KycService::class, function ($app) {
            return new KycService();
        });

        // Bind Wallet Service
        $this->app->singleton(WalletService::class, function ($app) {
            return new WalletService();
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

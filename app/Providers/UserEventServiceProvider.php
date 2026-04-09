<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use App\Events\ProfileUpdated;
use App\Events\KycVerificationSubmitted;
use App\Events\KycVerificationApproved;
use App\Events\KycVerificationRejected;
use App\Events\PreferencesUpdated;
use App\Events\AvatarChanged;
use App\Listeners\UpdateUserCache;
use App\Listeners\NotifyProfileCompletion;
use App\Listeners\SendKycVerificationEmail;
use App\Listeners\UpdateSearchPreferences;

class UserEventServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Profile Events
        Event::listen(
            ProfileUpdated::class,
            UpdateUserCache::class . '@handle'
        );

        Event::listen(
            ProfileUpdated::class,
            NotifyProfileCompletion::class . '@handle'
        );

        // KYC Events
        Event::listen(
            KycVerificationSubmitted::class,
            SendKycVerificationEmail::class . '@handle'
        );

        Event::listen(
            KycVerificationApproved::class,
            [SendKycVerificationEmail::class, 'handle']
        );

        Event::listen(
            KycVerificationRejected::class,
            [SendKycVerificationEmail::class, 'handle']
        );

        // Preferences Events
        Event::listen(
            PreferencesUpdated::class,
            UpdateSearchPreferences::class . '@handle'
        );

        // Avatar Events
        Event::listen(
            AvatarChanged::class,
            UpdateUserCache::class . '@handle'
        );
    }
}

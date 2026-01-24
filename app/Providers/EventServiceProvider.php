<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Events\UserRegistered;
use App\Events\UserLoggedIn;
use App\Events\UserLoggedOut;
use App\Events\PasswordReset;
use App\Events\TwoFactorEnabled;
use App\Events\SocialAccountLinked;
use App\Events\SuspiciousLoginDetected;
use App\Listeners\SendWelcomeEmail;
use App\Listeners\CreateWallet;
use App\Listeners\SetupDefaultPreferences;
use App\Listeners\LogLoginActivity;
use App\Listeners\NotifyNewDevice;
use App\Listeners\SendPasswordResetEmail;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        UserRegistered::class => [
            SendWelcomeEmail::class,
            CreateWallet::class,
            SetupDefaultPreferences::class,
        ],
        UserLoggedIn::class => [
            LogLoginActivity::class,
            NotifyNewDevice::class,
        ],
        UserLoggedOut::class => [
            // List listeners here
        ],
        PasswordReset::class => [
            SendPasswordResetEmail::class,
        ],
        TwoFactorEnabled::class => [
            // List listeners here
        ],
        SocialAccountLinked::class => [
            // List listeners here
        ],
        SuspiciousLoginDetected::class => [
            // Maybe notify admin?
        ],
        \App\Events\PropertyPublished::class => [
            \App\Listeners\NotifyPropertyOwner::class,
            \App\Listeners\UpdatePropertyAnalytics::class,
        ],
        \App\Events\PropertyViewed::class => [
            \App\Listeners\UpdatePropertyAnalytics::class,
        ],
        \App\Events\PropertyCreated::class => [
            // Listeners
        ],
        \App\Events\PropertyUpdated::class => [
            // Listeners
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}

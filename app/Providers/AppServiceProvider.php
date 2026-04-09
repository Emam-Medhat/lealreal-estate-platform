<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use App\Models\CustomReport;
use App\Policies\CustomReportPolicy;
// AI Services
use App\Services\AI\SmartSearchService;
use App\Services\AI\RecommendationService;
use App\Services\AI\PricePredictionService;
use App\Services\AI\ChatbotService;
use App\Services\AI\ImageAnalysisService;
use App\Services\AI\FilterTransformer;
// Fraud Detection Services
use App\Services\ImageHashService;
use App\Services\PriceAnomalyService;
use App\Services\DuplicateDetectionService;
use App\Services\AddressValidationService;
use App\Services\FakeAccountDetectionService;
use App\Services\PropertyFraudDetectionService;


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
            return new \App\Services\OptimizedPropertyService();
        });

        // ── AI Services (singletons for DI container) ──────────────────
        $this->app->singleton(SmartSearchService::class);
        $this->app->singleton(RecommendationService::class);
        $this->app->singleton(PricePredictionService::class);
        $this->app->singleton(ChatbotService::class);
        $this->app->singleton(ImageAnalysisService::class);
        $this->app->singleton(FilterTransformer::class);

        // Fraud Detection Pipeline
        $this->app->singleton(ImageHashService::class);
        $this->app->singleton(PriceAnomalyService::class);
        $this->app->singleton(DuplicateDetectionService::class);
        $this->app->singleton(AddressValidationService::class);
        $this->app->singleton(FakeAccountDetectionService::class);
        $this->app->singleton(PropertyFraudDetectionService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register policies
        Gate::policy(CustomReport::class, CustomReportPolicy::class);

        // Set default string length for older MySQL versions
        Schema::defaultStringLength(191);

        // Debugbar on exception pages can take minutes on large blades (collectors + stack).
        // Off by default on local unless DEBUGBAR_ENABLED=true in .env.
        if (app()->environment(['local', 'testing'])) {
            $explicit = env('DEBUGBAR_ENABLED');
            $debugbarOn = $explicit !== null && filter_var($explicit, FILTER_VALIDATE_BOOLEAN);
            $debugbarClass = 'Fruitcake\\LaravelDebugbar\\LaravelDebugbar';
            if (! $debugbarOn && class_exists($debugbarClass) && $this->app->bound($debugbarClass)) {
                $this->app->make($debugbarClass)->disable();
            }
        }

        // Register model observers for automatic cache invalidation and logging
        \App\Models\Property::observe(\App\Observers\PropertyObserver::class);
        \App\Models\PropertyMedia::observe(\App\Observers\PropertyMediaObserver::class);
        \App\Models\Agent::observe(\App\Observers\AgentObserver::class);
        \App\Models\Developer::observe(\App\Observers\DeveloperObserver::class);
        \App\Models\AgentTask::observe(\App\Observers\AgentTaskObserver::class);
        \App\Models\Lead::observe(\App\Observers\LeadObserver::class);

        // ── AI Rate Limiter ──────────────────────────────────────────────
        RateLimiter::for('ai-search', function (Request $request) {
            $limit = config('ai.rate_limits.search_per_minute', 20);
            $by = $request->user()?->id ?? $request->ip();
            return Limit::perMinute($limit)->by($by);
        });

        // Force eager loading in development to detect N+1 problems
        if (app()->environment('local')) {
            \Illuminate\Database\Eloquent\Model::preventSilentlyDiscardingAttributes(!app()->environment('testing'));
        }
    }
}

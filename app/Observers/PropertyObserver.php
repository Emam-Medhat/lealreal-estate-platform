<?php

namespace App\Observers;

use App\Models\Property;
use App\Models\PropertyAnalytic;
use App\Services\CacheService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class PropertyObserver
{
    /**
     * Handle the Property "creating" event.
     */
    public function creating(Property $property): void
    {
        // Generate unique property code if not set
        if (empty($property->property_code)) {
            $property->property_code = $this->generateUniquePropertyCode();
        }

        // Generate slug if not set
        if (empty($property->slug)) {
            $property->slug = $this->generateUniqueSlug($property->title);
        }

        // Default values
        $property->views_count = $property->views_count ?? 0;
        $property->inquiries_count = $property->inquiries_count ?? 0;
        $property->favorites_count = $property->favorites_count ?? 0;
    }

    /**
     * Handle the Property "created" event.
     */
    public function created(Property $property): void
    {
        // Clear property-related caches
        $this->clearPropertyCaches();

        // Create initial analytics record
        $this->createInitialAnalytics($property);

        // Log property creation
        $this->logPropertyActivity($property, 'created', 'Property created successfully');

        // Update agent statistics
        $this->updateAgentStats($property, 'property_created');

        // Fire event only if it exists
        if (class_exists('\App\Events\PropertyCreated')) {
            event(new \App\Events\PropertyCreated($property));
        }

        // Send notifications to admins and managers
        $this->sendPropertyNotifications($property, 'created');
    }

    /**
     * Handle the Property "updated" event.
     */
    public function updated(Property $property): void
    {
        // Clear property-related caches
        $this->clearPropertyCaches();

        // Log important changes
        $changes = $property->getDirty();

        if (isset($changes['status']) && $changes['status'] !== $property->getOriginal('status')) {
            $this->logPropertyActivity($property, 'status_changed', "Property status changed to {$property->status}");
            $this->updateAgentStats($property, 'status_changed');
        }

        if (isset($changes['featured']) && $changes['featured'] !== $property->getOriginal('featured')) {
            $this->logPropertyActivity($property, 'featured_changed', "Property featured status changed to " . ($property->featured ? 'Yes' : 'No'));
            $this->updatePropertyAnalytics($property, 'featured_changed');
        }

        if (isset($changes['price']) && $changes['price'] !== $property->getOriginal('price')) {
            $this->logPropertyActivity($property, 'price_changed', "Property price changed from {$property->getOriginal('price')} to {$property->price}");
            $this->updatePropertyAnalytics($property, 'price_changed');
        }

        // Update analytics for important field changes
        $this->updatePropertyAnalytics($property, 'updated');

        // Fire event only if it exists
        if (class_exists('\App\Events\PropertyUpdated')) {
            event(new \App\Events\PropertyUpdated($property));
        }
    }

    /**
     * Handle the Property "deleted" event.
     */
    public function deleted(Property $property): void
    {
        // Clear property-related caches
        $this->clearPropertyCaches();

        // Log property deletion
        $this->logPropertyActivity($property, 'deleted', 'Property deleted');

        // Update agent statistics
        $this->updateAgentStats($property, 'property_deleted');

        // Fire event only if it exists
        if (class_exists('\App\Events\PropertyDeleted')) {
            event(new \App\Events\PropertyDeleted($property));
        }

        // Send notifications (if applicable)
        $this->sendPropertyNotifications($property, 'deleted');
    }

    /**
     * Handle the Property "restored" event.
     */
    public function restored(Property $property): void
    {
        // Clear property-related caches
        $this->clearPropertyCaches();

        // Log property restoration
        $this->logPropertyActivity($property, 'restored', 'Property restored');

        // Update agent statistics
        $this->updateAgentStats($property, 'property_restored');
    }

    /**
     * Handle the Property "force deleted" event.
     */
    public function forceDeleted(Property $property): void
    {
        // Clear property-related caches
        $this->clearPropertyCaches();

        // Log permanent deletion
        $this->logPropertyActivity($property, 'force_deleted', 'Property permanently deleted');

        // Update agent statistics
        $this->updateAgentStats($property, 'property_force_deleted');
    }

    /**
     * Generate unique property code
     *
     * @return string
     */
    private function generateUniquePropertyCode(): string
    {
        do {
            $code = 'PROP-' . strtoupper(Str::random(8));
        } while (Property::where('property_code', $code)->exists());

        return $code;
    }

    /**
     * Generate unique slug
     *
     * @param string $title
     * @return string
     */
    private function generateUniqueSlug(string $title): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $counter = 1;

        while (Property::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }

    /**
     * Create initial analytics record
     *
     * @param Property $property
     * @return void
     */
    private function createInitialAnalytics(Property $property): void
    {
        try {
            PropertyAnalytic::create([
                'property_id' => $property->id,
                'views_count' => 0,
                'inquiries_count' => 0,
                'favorites_count' => 0,
                'average_rating' => 0,
                'total_reviews' => 0,
                'conversion_rate' => 0,
                'days_on_market' => 0,
                'price_per_sqft' => $this->calculatePricePerSqft($property),
                'views_per_day' => 0,
                'inquiries_per_day' => 0,
                'last_viewed_at' => null,
                'last_inquired_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create initial analytics for property ' . $property->id . ': ' . $e->getMessage());
        }
    }

    /**
     * Update property analytics
     *
     * @param Property $property
     * @param string $event
     * @return void
     */
    private function updatePropertyAnalytics(Property $property, string $event): void
    {
        try {
            $analytics = $property->analytics()->first();

            if (!$analytics) {
                $this->createInitialAnalytics($property);
                return;
            }

            switch ($event) {
                case 'viewed':
                    $analytics->increment('views_count');
                    $analytics->last_viewed_at = now();
                    $analytics->views_per_day = $this->calculateViewsPerDay($property);
                    break;

                case 'inquired':
                    $analytics->increment('inquiries_count');
                    $analytics->last_inquired_at = now();
                    $analytics->inquiries_per_day = $this->calculateInquiriesPerDay($property);
                    $analytics->conversion_rate = $this->calculateConversionRate($property);
                    break;

                case 'favorited':
                    $analytics->increment('favorites_count');
                    break;

                case 'featured_changed':
                    $analytics->featured = $property->featured;
                    $analytics->featured_at = $property->featured ? now() : null;
                    break;

                case 'price_changed':
                    $analytics->price_per_sqft = $this->calculatePricePerSqft($property);
                    break;

                case 'updated':
                    $analytics->days_on_market = $this->calculateDaysOnMarket($property);
                    break;
            }

            $analytics->save();
        } catch (\Exception $e) {
            Log::error('Failed to update analytics for property ' . $property->id . ': ' . $e->getMessage());
        }
    }

    /**
     * Update agent statistics
     *
     * @param Property $property
     * @param string $event
     * @return void
     */
    private function updateAgentStats(Property $property, string $event): void
    {
        if (!$property->agent_id) {
            return;
        }

        try {
            $agent = $property->agent;

            // Check if agent exists before trying to update stats
            if (!$agent) {
                Log::warning('No agent found for property ' . $property->id . ' - skipping stats update');
                return;
            }

            switch ($event) {
                case 'property_created':
                    if ($agent->properties_count !== null) {
                        $agent->increment('properties_count');
                    }
                    break;

                case 'property_deleted':
                    if ($agent->properties_count !== null && $agent->properties_count > 0) {
                        $agent->decrement('properties_count');
                    }
                    break;

                case 'property_restored':
                    if ($agent->properties_count !== null) {
                        $agent->increment('properties_count');
                    }
                    break;

                case 'property_force_deleted':
                    if ($agent->properties_count !== null && $agent->properties_count > 0) {
                        $agent->decrement('properties_count');
                    }
                    break;

                case 'status_changed':
                    if ($property->status === 'published') {
                        if ($agent->published_properties_count !== null) {
                            $agent->increment('published_properties_count');
                        }
                    } elseif ($property->getOriginal('status') === 'published') {
                        if ($agent->published_properties_count !== null && $agent->published_properties_count > 0) {
                            $agent->decrement('published_properties_count');
                        }
                    }
                    break;
            }

            $agent->touch();
        } catch (\Exception $e) {
            Log::error('Failed to update agent stats for property ' . $property->id . ': ' . $e->getMessage());
        }
    }

    /**
     * Log property activity
     *
     * @param Property $property
     * @param string $type
     * @param string $description
     * @return void
     */
    private function logPropertyActivity(Property $property, string $type, string $description): void
    {
        try {
            \App\Models\ActivityLog::create([
                'model_type' => Property::class,
                'model_id' => $property->id,
                'action' => $type,
                'description' => $description,
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log property activity: ' . $e->getMessage());
        }
    }

    /**
     * Send property notifications
     *
     * @param Property $property
     * @param string $event
     * @return void
     */
    private function sendPropertyNotifications(Property $property, string $event): void
    {
        if ($event === 'created') {
            try {
                // Find all admins to notify
                $admins = \App\Models\User::where('role', 'admin')
                    ->orWhere('user_type', 'admin')
                    ->get();
                
                if ($admins->count() > 0) {
                    \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\NewPropertyNotification($property));
                    Log::info("Sent new property notification to {$admins->count()} admins for property {$property->id}");
                }
            } catch (\Exception $e) {
                Log::error('Failed to send property notifications: ' . $e->getMessage());
            }
        }

        $notificationData = [
            'property_id' => $property->id,
            'property_title' => $property->title,
            'event' => $event,
            'agent_id' => $property->agent_id,
            'created_at' => now(),
        ];

        Log::info('Property notification', $notificationData);
    }

    /**
     * Clear property-related caches
     *
     * @return void
     */
    private function clearPropertyCaches(): void
    {
        try {
            CacheService::clearTags([
                CacheService::TAGS['properties'],
                CacheService::TAGS['dashboard'],
                CacheService::TAGS['analytics']
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to clear property caches: ' . $e->getMessage());
        }
    }

    /**
     * Calculate price per square foot
     *
     * @param Property $property
     * @return float|null
     */
    private function calculatePricePerSqft(Property $property): ?float
    {
        if (!$property->area || $property->area <= 0) {
            return null;
        }

        return $property->price / $property->area;
    }

    /**
     * Calculate days on market
     *
     * @param Property $property
     * @return int
     */
    private function calculateDaysOnMarket(Property $property): int
    {
        return $property->created_at->diffInDays(now());
    }

    /**
     * Calculate views per day
     *
     * @param Property $property
     * @return float
     */
    private function calculateViewsPerDay(Property $property): float
    {
        $daysOnMarket = $this->calculateDaysOnMarket($property);

        return $daysOnMarket > 0 ? $property->views_count / $daysOnMarket : 0;
    }

    /**
     * Calculate inquiries per day
     *
     * @param Property $property
     * @return float
     */
    private function calculateInquiriesPerDay(Property $property): float
    {
        $daysOnMarket = $this->calculateDaysOnMarket($property);

        return $daysOnMarket > 0 ? $property->inquiries_count / $daysOnMarket : 0;
    }

    /**
     * Calculate conversion rate
     *
     * @param Property $property
     * @return float
     */
    private function calculateConversionRate(Property $property): float
    {
        if ($property->views_count <= 0) {
            return 0;
        }

        return ($property->inquiries_count / $property->views_count) * 100;
    }
}

<?php

namespace App\Listeners;

use App\Events\PreferencesUpdated;
use Illuminate\Support\Facades\Cache;

class UpdateSearchPreferences
{
    /**
     * Handle the event.
     */
    public function handle(PreferencesUpdated $event): void
    {
        $user = $event->user;
        $preferences = $event->preferences;
        
        // Update search preferences cache
        Cache::tags(['user', 'user.' . $user->id, 'search_preferences'])->remember(
            'user.search_preferences.' . $user->id,
            now()->addDays(30),
            fn() => array_merge($user->search_preferences ?? [], $preferences)
        );
        
        // Update user preferences in database
        $user->update([
            'search_preferences' => array_merge($user->search_preferences ?? [], $preferences)
        ]);
        
        // Update search filters if they exist
        if (isset($preferences['search_filters'])) {
            $this->updateSearchFilters($user, $preferences['search_filters']);
        }
        
        // Update notification preferences
        if (isset($preferences['notifications'])) {
            $this->updateNotificationPreferences($user, $preferences['notifications']);
        }
        
        // Update display preferences
        if (isset($preferences['display'])) {
            $this->updateDisplayPreferences($user, $preferences['display']);
        }
    }
    
    /**
     * Update search filters
     */
    private function updateSearchFilters($user, array $filters): void
    {
        $currentFilters = $user->search_filters ?? [];
        
        // Merge and validate filters
        $updatedFilters = array_merge($currentFilters, $filters);
        
        // Validate filter structure
        $validatedFilters = $this->validateSearchFilters($updatedFilters);
        
        // Update user record
        $user->update(['search_filters' => $validatedFilters]);
        
        // Clear search cache for this user
        Cache::tags(['user_search', 'user.' . $user->id])->flush();
    }
    
    /**
     * Update notification preferences
     */
    private function updateNotificationPreferences($user, array $notifications): void
    {
        $currentNotifications = $user->notification_preferences ?? [];
        
        // Merge notification preferences
        $updatedNotifications = array_merge($currentNotifications, $notifications);
        
        // Validate notification preferences
        $validatedNotifications = $this->validateNotificationPreferences($updatedNotifications);
        
        // Update user record
        $user->update(['notification_preferences' => $validatedNotifications]);
    }
    
    /**
     * Update display preferences
     */
    private function updateDisplayPreferences($user, array $display): void
    {
        $currentDisplay = $user->display_preferences ?? [];
        
        // Merge display preferences
        $updatedDisplay = array_merge($currentDisplay, $display);
        
        // Validate display preferences
        $validatedDisplay = $this->validateDisplayPreferences($updatedDisplay);
        
        // Update user record
        $user->update(['display_preferences' => $validatedDisplay]);
    }
    
    /**
     * Validate search filters
     */
    private function validateSearchFilters(array $filters): array
    {
        $allowedFilters = [
            'property_type',
            'price_range',
            'location',
            'bedrooms',
            'bathrooms',
            'area_range',
            'amenities',
            'listing_type',
            'sort_by',
            'per_page'
        ];
        
        return array_intersect_key($filters, array_flip($allowedFilters));
    }
    
    /**
     * Validate notification preferences
     */
    private function validateNotificationPreferences(array $preferences): array
    {
        $allowedPreferences = [
            'email_notifications',
            'sms_notifications',
            'push_notifications',
            'marketing_emails',
            'property_alerts',
            'price_changes',
            'new_listings',
            'message_notifications'
        ];
        
        return array_intersect_key($preferences, array_flip($allowedPreferences));
    }
    
    /**
     * Validate display preferences
     */
    private function validateDisplayPreferences(array $preferences): array
    {
        $allowedPreferences = [
            'language',
            'theme',
            'timezone',
            'currency',
            'date_format',
            'time_format',
            'map_view',
            'grid_view'
        ];
        
        return array_intersect_key($preferences, array_flip($allowedPreferences));
    }
}

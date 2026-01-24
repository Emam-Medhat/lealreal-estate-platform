<?php

namespace App\Observers;

use App\Models\Property;
use App\Models\PropertyAnalytic;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class PropertyObserver
{
    /**
     * Handle the Property "creating" event.
     */
    public function creating(Property $property): void
    {
        // Skip slug generation as slug column doesn't exist
        // if (empty($property->slug)) {
        //     $property->slug = $this->generateUniqueSlug($property->title);
        // }

        // Default values if needed
        if ($property->views_count === null)
            $property->views_count = 0;
    }

    /**
     * Handle the Property "created" event.
     */
    public function created(Property $property): void
    {
        // Create initial analytics record (if table exists)
        // PropertyAnalytic::create(['property_id' => $property->id, ...]);

        event(new \App\Events\PropertyCreated($property));
    }

    /**
     * Handle the Property "updated" event.
     */
    public function updated(Property $property): void
    {
        // Clear cache
        Cache::forget('property_' . $property->id);
        // Skip slug cache as slug column doesn't exist
        // Cache::forget('property_slug_' . $property->slug);

        event(new \App\Events\PropertyUpdated($property));
    }

    /**
     * Handle the Property "deleted" event.
     */
    public function deleted(Property $property): void
    {
        // Cleanup relationships or let cascade handle it
        event(new \App\Events\PropertyDeleted($property));
    }

    /**
     * Handle the Property "restored" event.
     */
    public function restored(Property $property): void
    {
        //
    }

    /**
     * Handle the Property "force deleted" event.
     */
    public function forceDeleted(Property $property): void
    {
        // Delete images from storage
    }

    private function generateUniqueSlug($title)
    {
        $slug = Str::slug($title);
        $count = 2;
        while (Property::where('slug', $slug)->exists()) {
            $slug = Str::slug($title) . '-' . $count;
            $count++;
        }
        return $slug;
    }
}

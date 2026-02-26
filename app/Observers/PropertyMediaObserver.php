<?php

namespace App\Observers;

use App\Models\PropertyMedia;
use App\Jobs\Fraud\ProcessImageHashJob;
use Illuminate\Support\Facades\Log;

class PropertyMediaObserver
{
    /**
     * Handle the PropertyMedia "created" event.
     */
    public function created(PropertyMedia $media): void
    {
        // Only process images
        if ($media->media_type === 'image') {
            Log::info("PropertyMediaObserver: Dispatching ProcessImageHashJob for media #{$media->id}");
            ProcessImageHashJob::dispatch($media->id);
        }
    }

    /**
     * Handle the PropertyMedia "updated" event.
     */
    public function updated(PropertyMedia $media): void
    {
        // If the file path changed, re-hash it
        if ($media->isDirty('file_path') && $media->media_type === 'image') {
            Log::info("PropertyMediaObserver: File path changed for media #{$media->id}, re-dispatching hash job");
            ProcessImageHashJob::dispatch($media->id);
        }
    }
}

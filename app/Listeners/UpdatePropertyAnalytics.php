<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Services\PropertyAnalyticsService;

class UpdatePropertyAnalytics
{
    protected $analyticsService;

    public function __construct(PropertyAnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        // Inspect event type and act
        if (isset($event->property)) {
            // E.g. log status change
        }

        // If event is PropertyViewed
        // $this->analyticsService->trackView($event->property->id);
    }
}

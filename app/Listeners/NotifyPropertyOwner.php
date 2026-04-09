<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifyPropertyOwner
{
    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        // $event->property is available
        // $event->property->agent (User) ->notify(...)

        // Example:
        // if (isset($event->property) && $event->property->agent) {
        //     $event->property->agent->notify(new \App\Notifications\PropertyPublishedNotification($event->property));
        // }
    }
}

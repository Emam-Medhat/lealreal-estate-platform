<?php

namespace App\Events;

use App\Models\Property;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PropertyPublished
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $property;

    /**
     * Create a new event instance.
     */
    public function __construct(Property $property)
    {
        $this->property = $property;
    }
}

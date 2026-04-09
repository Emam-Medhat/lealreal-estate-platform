<?php

namespace App\Events;

use App\Models\Property;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PropertySold
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Property $property;
    public User $buyer;
    public float $salePrice;

    /**
     * Create a new event instance.
     *
     * @param Property $property   The sold property
     * @param User     $buyer      The buyer
     * @param float    $salePrice  Final sale price
     */
    public function __construct(Property $property, User $buyer, float $salePrice)
    {
        $this->property = $property;
        $this->buyer = $buyer;
        $this->salePrice = $salePrice;
    }
}

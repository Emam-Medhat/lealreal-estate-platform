<?php

namespace App\Events;

use App\Models\FractionalOwnership;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SharePurchased
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public FractionalOwnership $ownership;

    /**
     * Create a new event instance.
     *
     * @param FractionalOwnership $ownership  The newly created/updated ownership record
     */
    public function __construct(FractionalOwnership $ownership)
    {
        $this->ownership = $ownership;
    }
}

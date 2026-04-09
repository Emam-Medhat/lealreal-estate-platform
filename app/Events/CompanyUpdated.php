<?php

namespace App\Events;

use App\Models\Company;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CompanyUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $company;
    public $user;
    public $changes;

    /**
     * Create a new event instance.
     */
    public function __construct(Company $company, User $user = null, array $changes = [])
    {
        $this->company = $company;
        $this->user = $user ?? ($company->creator ?? $company->owner);
        $this->changes = $changes;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('company.' . $this->company->id),
            new PrivateChannel('user.' . $this->user->id),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'company.updated';
    }
}

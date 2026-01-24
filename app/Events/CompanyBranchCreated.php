<?php

namespace App\Events;

use App\Models\Company;
use App\Models\CompanyBranch;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CompanyBranchCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $company;
    public $user;
    public $branch;

    /**
     * Create a new event instance.
     */
    public function __construct(CompanyBranch $branch)
    {
        $this->branch = $branch;
        $this->company = $branch->company;
        $this->user = $branch->manager ?? $branch->company->owner;
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
        return 'company.branch.created';
    }
}

<?php

namespace App\Events;

use App\Models\Company;
use App\Models\User;
use App\Models\CompanyMember;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CompanyMemberAdded
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $company;
    public $user;
    public $member;

    /**
     * Create a new event instance.
     */
    public function __construct(CompanyMember $member)
    {
        $this->member = $member;
        $this->company = $member->company;
        $this->user = $member->user;
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
            new PrivateChannel('user.' . $this->member->user_id)
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'company.member.added';
    }
}

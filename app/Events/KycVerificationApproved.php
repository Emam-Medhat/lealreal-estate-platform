<?php

namespace App\Events;

use App\Models\User;
use App\Models\KycVerification;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class KycVerificationApproved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $kycVerification;
    public $approvedBy;

    /**
     * Create a new event instance.
     */
    public function __construct(User $user, KycVerification $kycVerification, ?User $approvedBy = null)
    {
        $this->user = $user;
        $this->kycVerification = $kycVerification;
        $this->approvedBy = $approvedBy;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->user->id),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'kyc.approved';
    }
}

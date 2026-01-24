<?php

namespace App\Events;

use App\Models\User;
use App\Models\KycVerification;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class KycVerificationRejected
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $kycVerification;
    public $rejectedBy;
    public $rejectionReason;

    /**
     * Create a new event instance.
     */
    public function __construct(User $user, KycVerification $kycVerification, ?User $rejectedBy = null, ?string $rejectionReason = null)
    {
        $this->user = $user;
        $this->kycVerification = $kycVerification;
        $this->rejectedBy = $rejectedBy;
        $this->rejectionReason = $rejectionReason;
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
        return 'kyc.rejected';
    }
}

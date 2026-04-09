<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VideoCall extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'appointment_id',
        'caller_id',
        'receiver_id',
        'room_id',
        'status',
        'started_at',
        'answered_at',
        'ended_at',
        'duration',
        'recording_url',
        'settings'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'answered_at' => 'datetime',
        'ended_at' => 'datetime',
        'duration' => 'integer',
        'settings' => 'array'
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function caller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'caller_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isWaiting(): bool
    {
        return $this->status === 'waiting';
    }

    public function isEnded(): bool
    {
        return $this->status === 'ended';
    }

    public function endCall()
    {
        if ($this->status !== 'ended') {
            $this->update([
                'status' => 'ended',
                'ended_at' => now(),
                'duration' => $this->started_at->diffInSeconds(now())
            ]);
        }
    }

    public function getFormattedDuration(): string
    {
        if (!$this->duration) {
            return '00:00:00';
        }

        return gmdate('H:i:s', $this->duration);
    }

    public function canUserAccess(User $user): bool
    {
        return $this->caller_id === $user->id || $this->receiver_id === $user->id;
    }

    public function getOtherParticipant(User $user): ?User
    {
        if ($this->caller_id === $user->id) {
            return $this->receiver;
        } elseif ($this->receiver_id === $user->id) {
            return $this->caller;
        }

        return null;
    }
}

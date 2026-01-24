<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        'chat_room_id',
        'user_id',
        'role',
        'joined_at',
        'last_read_at',
        'is_online',
        'last_seen_at'
    ];

    protected $casts = [
        'joined_at' => 'datetime',
        'last_read_at' => 'datetime',
        'last_seen_at' => 'datetime',
        'is_online' => 'boolean'
    ];

    public function chatRoom(): BelongsTo
    {
        return $this->belongsTo(ChatRoom::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function markAsRead()
    {
        $this->update(['last_read_at' => now()]);
    }

    public function getUnreadCount(): int
    {
        return $this->chatRoom->messages()
            ->where('created_at', '>', $this->last_read_at ?? $this->joined_at)
            ->where('user_id', '!=', $this->user_id)
            ->count();
    }

    public function isOnline(): bool
    {
        return $this->is_online || 
               ($this->last_seen_at && $this->last_seen_at->gt(now()->subMinutes(5)));
    }

    public function updateOnlineStatus(bool $isOnline)
    {
        $this->update([
            'is_online' => $isOnline,
            'last_seen_at' => now()
        ]);
    }
}

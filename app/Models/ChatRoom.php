<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ChatRoom extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'type',
        'created_by',
        'participants_count',
        'is_active',
        'settings'
    ];

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'chat_participants')
            ->withPivot(['role', 'joined_at', 'last_read_at'])
            ->withTimestamps();
    }

    public function chatParticipants(): HasMany
    {
        return $this->hasMany(ChatParticipant::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function latestMessage()
    {
        return $this->hasOne(Message::class)->latest();
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getUnreadCountForUser($userId)
    {
        if (!$userId) return 0;

        $participant = $this->chatParticipants()
            ->where('user_id', $userId)
            ->first();

        if (!$participant) return 0;

        return $this->messages()
            ->where('created_at', '>', $participant->last_read_at ?? $this->created_at)
            ->where('user_id', '!=', $userId)
            ->count();
    }

    public function addParticipant(User $user, string $role = 'member')
    {
        if ($this->participants()->where('user_id', $user->id)->exists()) {
            return false;
        }

        $this->participants()->attach($user->id, [
            'role' => $role,
            'joined_at' => now(),
            'last_read_at' => now()
        ]);

        $this->increment('participants_count');

        return true;
    }

    public function removeParticipant(User $user)
    {
        $participant = $this->chatParticipants()
            ->where('user_id', $user->id)
            ->first();

        if (!$participant) {
            return false;
        }

        $participant->delete();
        $this->decrement('participants_count');

        // Delete room if no participants left
        if ($this->participants_count === 0) {
            $this->delete();
        }

        return true;
    }

    public function isParticipant(User $user): bool
    {
        return $this->participants()->where('user_id', $user->id)->exists();
    }

    public function canUserAccess(User $user): bool
    {
        if ($this->type === 'public') {
            return true;
        }

        return $this->isParticipant($user);
    }
}

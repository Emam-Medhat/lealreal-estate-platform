<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'subject',
        'status',
        'last_message_at',
        'last_message_preview',
        'sender_deleted_at',
        'receiver_deleted_at',
        'is_archived_by_sender',
        'is_archived_by_receiver',
        'is_starred_by_sender',
        'is_starred_by_receiver',
        'metadata',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
        'sender_deleted_at' => 'datetime',
        'receiver_deleted_at' => 'datetime',
        'is_archived_by_sender' => 'boolean',
        'is_archived_by_receiver' => 'boolean',
        'is_starred_by_sender' => 'boolean',
        'is_starred_by_receiver' => 'boolean',
        'metadata' => 'json',
    ];

    protected $attributes = [
        'status' => 'active',
        'is_archived_by_sender' => false,
        'is_archived_by_receiver' => false,
        'is_starred_by_sender' => false,
        'is_starred_by_receiver' => false,
    ];

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function lastMessage(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'last_message_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'active' => __('Active'),
            'archived' => __('Archived'),
            'deleted' => __('Deleted'),
            'blocked' => __('Blocked'),
            default => __(ucfirst($this->status))
        };
    }

    public function getFormattedLastMessageAtAttribute(): string
    {
        if (!$this->last_message_at) {
            return __('No messages');
        }

        if ($this->last_message_at->isToday()) {
            return $this->last_message_at->format('g:i A');
        }

        if ($this->last_message_at->isYesterday()) {
            return __('Yesterday');
        }

        return $this->last_message_at->format('M j, Y');
    }

    public function getUnreadCountForUser(int $userId): int
    {
        return $this->messages()
            ->where('receiver_id', $userId)
            ->where('is_read', false)
            ->count();
    }

    public function hasUnreadMessagesForUser(int $userId): bool
    {
        return $this->getUnreadCountForUser($userId) > 0;
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isArchived(): bool
    {
        return $this->status === 'archived';
    }

    public function isDeleted(): bool
    {
        return $this->status === 'deleted';
    }

    public function isBlocked(): bool
    {
        return $this->status === 'blocked';
    }

    public function isArchivedByUser(int $userId): bool
    {
        if ($userId === $this->sender_id) {
            return $this->is_archived_by_sender;
        } elseif ($userId === $this->receiver_id) {
            return $this->is_archived_by_receiver;
        }

        return false;
    }

    public function isStarredByUser(int $userId): bool
    {
        if ($userId === $this->sender_id) {
            return $this->is_starred_by_sender;
        } elseif ($userId === $this->receiver_id) {
            return $this->is_starred_by_receiver;
        }

        return false;
    }

    public function isDeletedByUser(int $userId): bool
    {
        if ($userId === $this->sender_id) {
            return $this->sender_deleted_at !== null;
        } elseif ($userId === $this->receiver_id) {
            return $this->receiver_deleted_at !== null;
        }

        return false;
    }

    public function canBeViewedByUser(int $userId): bool
    {
        return in_array($userId, [$this->sender_id, $this->receiver_id]) && 
               !$this->isDeletedByUser($userId);
    }

    public function canBeRepliedToByUser(int $userId): bool
    {
        return $this->canBeViewedByUser($userId) && 
               $this->isActive() && 
               !$this->isBlocked();
    }

    public function markAsArchivedByUser(int $userId): bool
    {
        if ($userId === $this->sender_id) {
            return $this->update(['is_archived_by_sender' => true]);
        } elseif ($userId === $this->receiver_id) {
            return $this->update(['is_archived_by_receiver' => true]);
        }

        return false;
    }

    public function markAsUnarchivedByUser(int $userId): bool
    {
        if ($userId === $this->sender_id) {
            return $this->update(['is_archived_by_sender' => false]);
        } elseif ($userId === $this->receiver_id) {
            return $this->update(['is_archived_by_receiver' => false]);
        }

        return false;
    }

    public function toggleStarForUser(int $userId): bool
    {
        if ($userId === $this->sender_id) {
            $isStarred = $this->is_starred_by_sender;
            return $this->update(['is_starred_by_sender' => !$isStarred]);
        } elseif ($userId === $this->receiver_id) {
            $isStarred = $this->is_starred_by_receiver;
            return $this->update(['is_starred_by_receiver' => !$isStarred]);
        }

        return false;
    }

    public function markAsDeletedByUser(int $userId): bool
    {
        if ($userId === $this->sender_id) {
            return $this->update(['sender_deleted_at' => now()]);
        } elseif ($userId === $this->receiver_id) {
            return $this->update(['receiver_deleted_at' => now()]);
        }

        return false;
    }

    public function block(): bool
    {
        return $this->update(['status' => 'blocked']);
    }

    public function unblock(): bool
    {
        return $this->update(['status' => 'active']);
    }

    public function updateLastMessage(Message $message): bool
    {
        return $this->update([
            'last_message_id' => $message->id,
            'last_message_at' => $message->created_at,
            'last_message_preview' => strlen($message->content) > 50 
                ? substr($message->content, 0, 50) . '...' 
                : $message->content,
        ]);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('sender_id', $userId)
              ->orWhere('receiver_id', $userId);
        });
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeArchived($query)
    {
        return $query->where('status', 'archived');
    }

    public function scopeBlocked($query)
    {
        return $query->where('status', 'blocked');
    }

    public function scopeNotDeletedByUser($query, int $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where(function ($subQuery) use ($userId) {
                $subQuery->where('sender_id', $userId)
                        ->whereNull('sender_deleted_at');
            })
            ->orWhere(function ($subQuery) use ($userId) {
                $subQuery->where('receiver_id', $userId)
                        ->whereNull('receiver_deleted_at');
            });
        });
    }

    public function scopeStarredByUser($query, int $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where(function ($subQuery) use ($userId) {
                $subQuery->where('sender_id', $userId)
                        ->where('is_starred_by_sender', true);
            })
            ->orWhere(function ($subQuery) use ($userId) {
                $subQuery->where('receiver_id', $userId)
                        ->where('is_starred_by_receiver', true);
            });
        });
    }

    public function scopeWithUnreadMessagesForUser($query, int $userId)
    {
        return $query->whereHas('messages', function ($messageQuery) use ($userId) {
            $messageQuery->where('receiver_id', $userId)
                       ->where('is_read', false);
        });
    }

    public function scopeBetweenUsers($query, int $user1Id, int $user2Id)
    {
        return $query->where(function ($q) use ($user1Id, $user2Id) {
            $q->where('sender_id', $user1Id)
              ->where('receiver_id', $user2Id);
        })->orWhere(function ($q) use ($user1Id, $user2Id) {
            $q->where('sender_id', $user2Id)
              ->where('receiver_id', $user1Id);
        });
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($conversation) {
            // Create initial message if provided
            if (request()->has('initial_message')) {
                $conversation->messages()->create([
                    'sender_id' => $conversation->sender_id,
                    'receiver_id' => $conversation->receiver_id,
                    'content' => request()->input('initial_message'),
                    'type' => 'text',
                    'is_read' => false,
                ]);
            }
        });

        static::updated(function ($conversation) {
            // Update conversation status if both users have deleted it
            if ($conversation->sender_deleted_at && $conversation->receiver_deleted_at) {
                $conversation->update(['status' => 'deleted']);
            }
        });
    }
}

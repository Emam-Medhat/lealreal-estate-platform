<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'sender_id',
        'receiver_id',
        'content',
        'type',
        'is_read',
        'read_at',
        'is_edited',
        'edited_at',
        'is_deleted_by_sender',
        'is_deleted_by_receiver',
        'deleted_by_sender_at',
        'deleted_by_receiver_at',
        'reply_to_id',
        'forwarded_from_id',
        'priority',
        'metadata',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'is_edited' => 'boolean',
        'edited_at' => 'datetime',
        'is_deleted_by_sender' => 'boolean',
        'is_deleted_by_receiver' => 'boolean',
        'deleted_by_sender_at' => 'datetime',
        'deleted_by_receiver_at' => 'datetime',
        'metadata' => 'json',
    ];

    protected $attributes = [
        'type' => 'text',
        'is_read' => false,
        'is_edited' => false,
        'is_deleted_by_sender' => false,
        'is_deleted_by_receiver' => false,
        'priority' => 'normal',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(MessageAttachment::class);
    }

    public function replyTo(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'reply_to_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(Message::class, 'reply_to_id');
    }

    public function forwardedFrom(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'forwarded_from_id');
    }

    public function forwardedMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'forwarded_from_id');
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'text' => __('Text'),
            'image' => __('Image'),
            'video' => __('Video'),
            'audio' => __('Audio'),
            'file' => __('File'),
            'location' => __('Location'),
            'contact' => __('Contact'),
            'system' => __('System'),
            'notification' => __('Notification'),
            default => __(ucfirst($this->type))
        };
    }

    public function getPriorityLabelAttribute(): string
    {
        return match($this->priority) {
            'low' => __('Low'),
            'normal' => __('Normal'),
            'high' => __('High'),
            'urgent' => __('Urgent'),
            default => __(ucfirst($this->priority))
        };
    }

    public function getFormattedContentAttribute(): string
    {
        if ($this->type === 'text') {
            return nl2br(e($this->content));
        }

        return $this->content;
    }

    public function getShortContentAttribute(): string
    {
        $content = strip_tags($this->content);
        return strlen($content) > 100 ? substr($content, 0, 100) . '...' : $content;
    }

    public function getFormattedCreatedAtAttribute(): string
    {
        if ($this->created_at->isToday()) {
            return $this->created_at->format('g:i A');
        }

        if ($this->created_at->isYesterday()) {
            return __('Yesterday') . ' ' . $this->created_at->format('g:i A');
        }

        return $this->created_at->format('M j, Y g:i A');
    }

    public function getFormattedReadAtAttribute(): ?string
    {
        return $this->read_at ? $this->read_at->format('M j, Y g:i A') : null;
    }

    public function getFormattedEditedAtAttribute(): ?string
    {
        return $this->edited_at ? $this->edited_at->format('M j, Y g:i A') : null;
    }

    public function isText(): bool
    {
        return $this->type === 'text';
    }

    public function isImage(): bool
    {
        return $this->type === 'image';
    }

    public function isVideo(): bool
    {
        return $this->type === 'video';
    }

    public function isAudio(): bool
    {
        return $this->type === 'audio';
    }

    public function isFile(): bool
    {
        return $this->type === 'file';
    }

    public function isSystem(): bool
    {
        return $this->type === 'system';
    }

    public function isRead(): bool
    {
        return $this->is_read;
    }

    public function isEdited(): bool
    {
        return $this->is_edited;
    }

    public function hasAttachments(): bool
    {
        return $this->attachments()->count() > 0;
    }

    public function hasReplies(): bool
    {
        return $this->replies()->count() > 0;
    }

    public function isReply(): bool
    {
        return $this->reply_to_id !== null;
    }

    public function isForwarded(): bool
    {
        return $this->forwarded_from_id !== null;
    }

    public function canBeEditedByUser(int $userId): bool
    {
        return $this->sender_id === $userId && 
               !$this->is_deleted_by_sender && 
               $this->created_at->gt(now()->subMinutes(15)); // Can edit within 15 minutes
    }

    public function canBeDeletedByUser(int $userId): bool
    {
        return in_array($userId, [$this->sender_id, $this->receiver_id]) && 
               !$this->isDeletedByUser($userId);
    }

    public function isDeletedByUser(int $userId): bool
    {
        if ($userId === $this->sender_id) {
            return $this->is_deleted_by_sender;
        } elseif ($userId === $this->receiver_id) {
            return $this->is_deleted_by_receiver;
        }

        return false;
    }

    public function markAsRead(): bool
    {
        if ($this->is_read) {
            return true;
        }

        return $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    public function markAsUnread(): bool
    {
        return $this->update([
            'is_read' => false,
            'read_at' => null,
        ]);
    }

    public function editContent(string $newContent): bool
    {
        return $this->update([
            'content' => $newContent,
            'is_edited' => true,
            'edited_at' => now(),
        ]);
    }

    public function markAsDeletedByUser(int $userId): bool
    {
        if ($userId === $this->sender_id) {
            return $this->update([
                'is_deleted_by_sender' => true,
                'deleted_by_sender_at' => now(),
            ]);
        } elseif ($userId === $this->receiver_id) {
            return $this->update([
                'is_deleted_by_receiver' => true,
                'deleted_by_receiver_at' => now(),
            ]);
        }

        return false;
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('sender_id', $userId)
              ->orWhere('receiver_id', $userId);
        });
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    public function scopeEdited($query)
    {
        return $query->where('is_edited', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByPriority($query, string $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeNotDeletedByUser($query, int $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where(function ($subQuery) use ($userId) {
                $subQuery->where('sender_id', $userId)
                        ->where('is_deleted_by_sender', false);
            })
            ->orWhere(function ($subQuery) use ($userId) {
                $subQuery->where('receiver_id', $userId)
                        ->where('is_deleted_by_receiver', false);
            });
        });
    }

    public function scopeWithAttachments($query)
    {
        return $query->whereHas('attachments');
    }

    public function scopeReplies($query)
    {
        return $query->whereNotNull('reply_to_id');
    }

    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($message) {
            // Update conversation's last message info
            $message->conversation->updateLastMessage($message);

            // Create notification for receiver
            if ($message->sender_id !== $message->receiver_id) {
                UserNotification::create([
                    'user_id' => $message->receiver_id,
                    'title' => 'New Message',
                    'message' => "You have a new message from {$message->sender->name}",
                    'type' => 'message',
                    'action_url' => route('messages.show', $message->conversation),
                    'action_text' => 'View Message',
                    'is_read' => false,
                ]);
            }
        });

        static::updated(function ($message) {
            // Update conversation timestamp if message was edited
            if ($message->isDirty('is_edited') && $message->is_edited) {
                $message->conversation->touch();
            }
        });
    }
}

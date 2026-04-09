<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class UserNotification extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'is_read',
        'read_at',
        'notifiable_type',
        'notifiable_id',
        'priority',
        'category',
        'action_url',
        'action_text',
        'expires_at',
        'sent_via',
        'delivery_status',
        'click_count',
        'last_clicked_at',
        'metadata'
    ];

    protected $casts = [
        'data' => 'json',
        'metadata' => 'json',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'expires_at' => 'datetime',
        'last_clicked_at' => 'datetime',
        'click_count' => 'integer'
    ];

    protected $dates = [
        'read_at',
        'expires_at',
        'last_clicked_at',
        'created_at',
        'updated_at'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }

    public function markAsRead(): bool
    {
        return $this->update([
            'is_read' => true,
            'read_at' => now()
        ]);
    }

    public function markAsUnread(): bool
    {
        return $this->update([
            'is_read' => false,
            'read_at' => null
        ]);
    }

    public function incrementClickCount(): bool
    {
        return $this->update([
            'click_count' => $this->click_count + 1,
            'last_clicked_at' => now()
        ]);
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    public function scopeHighPriority($query)
    {
        return $query->where('priority', 'high');
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeNotExpired($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function getActionUrlAttribute(): ?string
    {
        return $this->attributes['action_url'] ?? null;
    }

    public function getActionTextAttribute(): ?string
    {
        return $this->attributes['action_text'] ?? null;
    }

    public function getDeliveryStatusAttribute(): string
    {
        return $this->attributes['delivery_status'] ?? 'pending';
    }

    public function getPriorityAttribute(): string
    {
        return $this->attributes['priority'] ?? 'normal';
    }

    public function getCategoryAttribute(): string
    {
        return $this->attributes['category'] ?? 'general';
    }

    public function getSentViaAttribute(): array
    {
        return $this->attributes['sent_via'] ? json_decode($this->attributes['sent_via'], true) : [];
    }

    public function setSentViaAttribute($value)
    {
        $this->attributes['sent_via'] = is_array($value) ? json_encode($value) : $value;
    }
}

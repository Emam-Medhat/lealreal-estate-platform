<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class UserFavorite extends Model
{
    protected $fillable = [
        'user_id',
        'favoritable_type',
        'favoritable_id',
        'category',
        'notes',
        'tags',
        'is_public',
        'priority',
        'metadata',
        'expires_at',
        'reminder_at',
        'source',
        'price_at_favoriting',
        'property_details_snapshot'
    ];

    protected $casts = [
        'tags' => 'json',
        'metadata' => 'json',
        'property_details_snapshot' => 'json',
        'is_public' => 'boolean',
        'expires_at' => 'datetime',
        'reminder_at' => 'datetime',
        'price_at_favoriting' => 'decimal:2'
    ];

    protected $dates = [
        'expires_at',
        'reminder_at',
        'created_at',
        'updated_at'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function favoritable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopePrivate($query)
    {
        return $query->where('is_public', false);
    }

    public function scopeByTag($query, $tag)
    {
        return $query->whereJsonContains('tags', $tag);
    }

    public function scopeNotExpired($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    public function scopeWithReminder($query)
    {
        return $query->whereNotNull('reminder_at')
                    ->where('reminder_at', '<=', now());
    }

    public function scopeHighPriority($query)
    {
        return $query->where('priority', 'high');
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function hasReminder(): bool
    {
        return $this->reminder_at && $this->reminder_at->isFuture();
    }

    public function addTag(string $tag): bool
    {
        $tags = $this->tags ?? [];
        if (!in_array($tag, $tags)) {
            $tags[] = $tag;
            return $this->update(['tags' => $tags]);
        }
        return true;
    }

    public function removeTag(string $tag): bool
    {
        $tags = $this->tags ?? [];
        $key = array_search($tag, $tags);
        if ($key !== false) {
            unset($tags[$key]);
            return $this->update(['tags' => array_values($tags)]);
        }
        return true;
    }

    public function setReminder($datetime): bool
    {
        return $this->update(['reminder_at' => $datetime]);
    }

    public function clearReminder(): bool
    {
        return $this->update(['reminder_at' => null]);
    }

    public function makePublic(): bool
    {
        return $this->update(['is_public' => true]);
    }

    public function makePrivate(): bool
    {
        return $this->update(['is_public' => false]);
    }

    public function getCategoryAttribute(): string
    {
        return $this->attributes['category'] ?? 'general';
    }

    public function getPriorityAttribute(): string
    {
        return $this->attributes['priority'] ?? 'normal';
    }

    public function getSourceAttribute(): string
    {
        return $this->attributes['source'] ?? 'manual';
    }

    public function getTagsAttribute(): array
    {
        return $this->attributes['tags'] ? json_decode($this->attributes['tags'], true) : [];
    }

    public function setTagsAttribute($value)
    {
        $this->attributes['tags'] = is_array($value) ? json_encode($value) : $value;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrowdfundingUpdate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'campaign_id',
        'title',
        'content',
        'update_type',
        'images',
        'documents',
        'published_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'images' => 'array',
        'documents' => 'array',
        'published_at' => 'datetime',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(InvestmentCrowdfunding::class);
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at')
                   ->where('published_at', '<=', now());
    }

    public function scopeByType($query, $type)
    {
        return $query->where('update_type', $type);
    }

    // Helper methods
    public function isPublished(): bool
    {
        return $this->published_at && $this->published_at->isPast();
    }

    public function getPublishedAtFormattedAttribute(): string
    {
        return $this->published_at ? $this->published_at->format('Y-m-d H:i:s') : '';
    }

    public function getImagesCountAttribute(): int
    {
        return count($this->images ?? []);
    }

    public function getDocumentsCountAttribute(): int
    {
        return count($this->documents ?? []);
    }

    public function getTitleAttribute(): string
    {
        return $this->title ?? '';
    }

    public function getContentAttribute(): string
    {
        return $this->content ?? '';
    }

    public function getUpdateTypeAttribute(): string
    {
        return $this->update_type ?? 'general';
    }

    public function getImagesAttribute(): array
    {
        return $this->images ?? [];
    }

    public function getDocumentsAttribute(): array
    {
        return $this->documents ?? [];
    }

    public function getExcerptAttribute(): string
    {
        $content = $this->content ?? '';
        return strlen($content) > 150 ? substr($content, 0, 150) . '...' : $content;
    }
}

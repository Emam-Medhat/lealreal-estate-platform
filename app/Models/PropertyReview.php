<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'user_id',
        'rating',
        'title',
        'review',
        'pros',
        'cons',
        'is_verified',
        'is_approved',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'rating' => 'integer',
        'pros' => 'array',
        'cons' => 'array',
        'is_verified' => 'boolean',
        'is_approved' => 'boolean',
        'approved_at' => 'datetime',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopePending($query)
    {
        return $query->where('is_approved', false);
    }

    public function scopeByRating($query, $rating)
    {
        return $query->where('rating', $rating);
    }

    public function getStarRatingAttribute(): string
    {
        return str_repeat('★', $this->rating) . str_repeat('☆', 5 - $this->rating);
    }

    public function approve($approvedBy = null): void
    {
        $this->update([
            'is_approved' => true,
            'approved_by' => $approvedBy,
            'approved_at' => now(),
        ]);
    }

    public function reject(): void
    {
        $this->update([
            'is_approved' => false,
            'approved_by' => null,
            'approved_at' => null,
        ]);
    }
}

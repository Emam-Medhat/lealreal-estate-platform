<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyDuplicate extends Model
{
    protected $table = 'property_duplicates';

    protected $fillable = [
        'property_id',
        'duplicate_property_id',
        'similarity_score',
        'image_similarity',
        'location_similarity',
        'title_similarity',
        'price_similarity',
        'matching_details',
        'status',
        'reviewed_by',
        'reviewed_at',
        'admin_notes',
    ];

    protected $casts = [
        'matching_details' => 'array',
        'reviewed_at' => 'datetime',
        'similarity_score' => 'integer',
        'image_similarity' => 'integer',
        'location_similarity' => 'integer',
        'title_similarity' => 'integer',
        'price_similarity' => 'integer',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function duplicateProperty(): BelongsTo
    {
        return $this->belongsTo(Property::class, 'duplicate_property_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeHighSimilarity($query, int $threshold = 85)
    {
        return $query->where('similarity_score', '>=', $threshold);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function confirm(int $adminId, string $notes = ''): void
    {
        $this->update([
            'status' => 'confirmed',
            'reviewed_by' => $adminId,
            'reviewed_at' => now(),
            'admin_notes' => $notes,
        ]);

        // Mark the duplicate property as blocked
        $this->duplicateProperty()->update([
            'is_duplicate' => true,
            'fraud_status' => 'blocked',
            'duplicate_of' => $this->property_id,
        ]);
    }

    public function reject(int $adminId, string $notes = ''): void
    {
        $this->update([
            'status' => 'rejected',
            'reviewed_by' => $adminId,
            'reviewed_at' => now(),
            'admin_notes' => $notes,
        ]);
    }
}

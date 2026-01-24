<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaxFiling extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_tax_id',
        'user_id',
        'filing_type',
        'tax_year',
        'status',
        'submission_date',
        'review_date',
        'reviewed_by',
        'review_notes',
        'approved_amount',
        'rejection_reason',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'submission_date' => 'date',
        'review_date' => 'date',
        'approved_amount' => 'decimal:2',
    ];

    public function propertyTax(): BelongsTo
    {
        return $this->belongsTo(PropertyTax::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(TaxFilingAttachment::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeSubmitted($query)
    {
        return $query->where('status', 'submitted');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function canBeEdited(): bool
    {
        return $this->status === 'draft';
    }

    public function isReviewed(): bool
    {
        return in_array($this->status, ['approved', 'rejected']);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaxAssessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'assessment_value',
        'market_value',
        'assessment_date',
        'assessor_id',
        'status',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'assessment_value' => 'decimal:2',
        'market_value' => 'decimal:2',
        'assessment_date' => 'date',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function assessor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assessor_id');
    }

    public function propertyTaxes(): HasMany
    {
        return $this->hasMany(PropertyTax::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function getAssessmentRatioAttribute(): float
    {
        if ($this->market_value == 0) {
            return 0;
        }
        
        return ($this->assessment_value / $this->market_value) * 100;
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'assessor_id' => auth()->id(),
        ]);
    }
}

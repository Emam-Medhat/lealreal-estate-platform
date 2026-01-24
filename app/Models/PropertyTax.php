<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyTax extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'tax_rate_id',
        'assessment_value',
        'tax_amount',
        'tax_year',
        'status',
        'due_date',
        'paid_date',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'assessment_value' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'due_date' => 'date',
        'paid_date' => 'date',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function taxRate(): BelongsTo
    {
        return $this->belongsTo(TaxRate::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(TaxPayment::class);
    }

    public function exemptions(): HasMany
    {
        return $this->hasMany(TaxExemption::class);
    }

    public function filings(): HasMany
    {
        return $this->hasMany(TaxFiling::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'pending')->where('due_date', '<', now());
    }

    public function getTotalPaidAttribute(): float
    {
        return $this->payments()->where('status', 'completed')->sum('amount');
    }

    public function getRemainingAmountAttribute(): float
    {
        return $this->tax_amount - $this->total_paid;
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->status === 'pending' && $this->due_date < now();
    }

    public function markAsPaid(): void
    {
        $this->update([
            'status' => 'paid',
            'paid_date' => now(),
        ]);
    }
}

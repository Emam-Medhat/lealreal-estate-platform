<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CapitalGainsTax extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'seller_id',
        'buyer_id',
        'purchase_price',
        'sale_price',
        'purchase_date',
        'sale_date',
        'improvement_costs',
        'selling_costs',
        'gain_amount',
        'tax_rate',
        'tax_amount',
        'status',
        'paid_date',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'improvement_costs' => 'decimal:2',
        'selling_costs' => 'decimal:2',
        'gain_amount' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'purchase_date' => 'date',
        'sale_date' => 'date',
        'paid_date' => 'date',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(CapitalGainsTaxPayment::class);
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

    public function getHoldingPeriodAttribute(): int
    {
        return $this->purchase_date->diffInYears($this->sale_date);
    }

    public function getNetGainAttribute(): float
    {
        return $this->gain_amount - $this->tax_amount;
    }

    public function getTotalCostsAttribute(): float
    {
        return $this->purchase_price + $this->improvement_costs + $this->selling_costs;
    }

    public function getProfitMarginAttribute(): float
    {
        if ($this->sale_price == 0) {
            return 0;
        }
        
        return ($this->gain_amount / $this->sale_price) * 100;
    }

    public function isLongTerm(): bool
    {
        return $this->holding_period >= 1;
    }

    public function isShortTerm(): bool
    {
        return $this->holding_period < 1;
    }

    public function markAsPaid(): void
    {
        $this->update([
            'status' => 'paid',
            'paid_date' => now(),
        ]);
    }

    public function recalculateTax(): void
    {
        $this->update([
            'gain_amount' => $this->sale_price - $this->purchase_price - $this->improvement_costs - $this->selling_costs,
            'tax_rate' => $this->isLongTerm() ? 20 : 30,
            'tax_amount' => max(0, ($this->sale_price - $this->purchase_price - $this->improvement_costs - $this->selling_costs) * ($this->isLongTerm() ? 0.20 : 0.30)),
        ]);
    }
}

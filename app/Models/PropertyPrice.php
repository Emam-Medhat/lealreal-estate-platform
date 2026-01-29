<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PropertyPrice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'property_id',
        'price',
        'currency',
        'price_type',
        'rent_period',
        'price_per_sqm',
        'is_negotiable',
        'original_price',
        'discount_percentage',
        'includes_vat',
        'vat_rate',
        'service_charges',
        'maintenance_fees',
        'payment_frequency',
        'payment_terms',
        'effective_date',
        'expiry_date',
        'is_active',
        'set_by',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'price_per_sqm' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'is_negotiable' => 'boolean',
        'includes_vat' => 'boolean',
        'vat_rate' => 'decimal:2',
        'service_charges' => 'decimal:2',
        'maintenance_fees' => 'decimal:2',
        'payment_terms' => 'array',
        'effective_date' => 'date',
        'expiry_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price, 2) . ' ' . $this->currency;
    }

    public function getFormattedPricePerSqmAttribute(): ?string
    {
        if (!$this->price_per_sqm) {
            return null;
        }
        return number_format($this->price_per_sqm, 2) . ' ' . $this->currency . '/m²';
    }

    public function getTotalPriceAttribute(): float
    {
        $total = $this->price;
        
        if ($this->includes_vat) {
            $total += ($total * $this->vat_rate / 100);
        }
        
        if ($this->service_charges) {
            $total += $this->service_charges;
        }
        
        if ($this->maintenance_fees) {
            $total += $this->maintenance_fees;
        }
        
        return $total;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeCurrent($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expiry_date')
              ->orWhere('expiry_date', '>=', now());
        });
    }

    public function scopeByType($query, $type)
    {
        return $query->where('price_type', $type);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FractionalOwnership extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_asset_id',
        'user_id',
        'shares_owned',
        'percentage',
        'purchase_price',
        'price_per_share',
        'dividends_earned',
        'dividends_withdrawn',
        'purchased_at',
    ];

    protected $casts = [
        'shares_owned' => 'integer',
        'percentage' => 'decimal:5',
        'purchase_price' => 'decimal:2',
        'price_per_share' => 'decimal:2',
        'dividends_earned' => 'decimal:2',
        'dividends_withdrawn' => 'decimal:2',
        'purchased_at' => 'datetime',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function propertyAsset(): BelongsTo
    {
        return $this->belongsTo(PropertyAsset::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ─── Accessors / Helpers ──────────────────────────────────────────────────

    /**
     * Dividends available to withdraw.
     */
    public function getWithdrawableDividendsAttribute(): float
    {
        return max(0, $this->dividends_earned - $this->dividends_withdrawn);
    }

    /**
     * Return on investment percentage based on dividends earned.
     */
    public function getRoiPercentageAttribute(): float
    {
        if ($this->purchase_price <= 0) {
            return 0;
        }
        return round(($this->dividends_earned / $this->purchase_price) * 100, 2);
    }

    /**
     * Add dividends to this ownership record.
     */
    public function addDividends(float $amount): self
    {
        $this->increment('dividends_earned', $amount);
        return $this;
    }
}

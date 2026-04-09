<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class PropertyAsset extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'token_id',
        'smart_contract_address',
        'network',
        'total_shares',
        'available_shares',
        'share_price',
        'total_value',
        'status',
        'metadata',
    ];

    protected $casts = [
        'total_shares' => 'integer',
        'available_shares' => 'integer',
        'share_price' => 'decimal:2',
        'total_value' => 'decimal:2',
        'metadata' => 'array',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function fractionalOwnerships(): HasMany
    {
        return $this->hasMany(FractionalOwnership::class);
    }

    public function escrowContract(): HasOne
    {
        return $this->hasOne(EscrowContract::class);
    }

    public function ledgerEntries(): MorphMany
    {
        return $this->morphMany(TransactionLedger::class, 'reference');
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeLocked($query)
    {
        return $query->where('status', 'locked');
    }

    // ─── Accessors / Helpers ──────────────────────────────────────────────────

    public function getRemainingSharesAttribute(): int
    {
        return $this->available_shares;
    }

    public function getSoldSharesAttribute(): int
    {
        return $this->total_shares - $this->available_shares;
    }

    public function getOwnershipPercentageSoldAttribute(): float
    {
        if ($this->total_shares === 0) {
            return 0;
        }
        return round(($this->sold_shares / $this->total_shares) * 100, 2);
    }

    public function isFullySold(): bool
    {
        return $this->available_shares <= 0;
    }

    public function isTradeable(): bool
    {
        return $this->status === 'active' && $this->available_shares > 0;
    }

    /**
     * Calculate the percentage a given number of shares represents.
     */
    public function sharesAsPercentage(int $shares): float
    {
        if ($this->total_shares === 0) {
            return 0;
        }
        return round(($shares / $this->total_shares) * 100, 5);
    }

    /**
     * Get all shareholders with their ownership details.
     */
    public function getShareholders()
    {
        return $this->fractionalOwnerships()
            ->with('user')
            ->orderByDesc('shares_owned')
            ->get();
    }
}

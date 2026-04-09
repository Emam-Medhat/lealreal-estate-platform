<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EscrowContract extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_asset_id',
        'smart_contract_address',
        'buyer_id',
        'seller_id',
        'amount',
        'currency',
        'state',
        'conditions',
        'notes',
        'locked_at',
        'executed_at',
        'disputed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'conditions' => 'array',
        'locked_at' => 'datetime',
        'executed_at' => 'datetime',
        'disputed_at' => 'datetime',
    ];

    // ─── State constants ──────────────────────────────────────────────────────

    const STATE_PENDING = 'pending';
    const STATE_LOCKED = 'locked';
    const STATE_EXECUTED = 'executed';
    const STATE_DISPUTED = 'disputed';

    // ─── Relationships ────────────────────────────────────────────────────────

    public function propertyAsset(): BelongsTo
    {
        return $this->belongsTo(PropertyAsset::class);
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    // ─── State machine helpers ────────────────────────────────────────────────

    public function isPending(): bool
    {
        return $this->state === self::STATE_PENDING;
    }

    public function isLocked(): bool
    {
        return $this->state === self::STATE_LOCKED;
    }

    public function isExecuted(): bool
    {
        return $this->state === self::STATE_EXECUTED;
    }

    public function isDisputed(): bool
    {
        return $this->state === self::STATE_DISPUTED;
    }

    /**
     * All required conditions have been satisfied.
     */
    public function allConditionsMet(): bool
    {
        $conditions = $this->conditions ?? [];
        if (empty($conditions)) {
            return false;
        }
        return !in_array(false, array_values($conditions), true);
    }

    /**
     * Check if the escrow can be executed (locked + all conditions met).
     */
    public function canExecute(): bool
    {
        return $this->isLocked() && $this->allConditionsMet();
    }

    /**
     * Check if the escrow can be disputed.
     */
    public function canDispute(): bool
    {
        return in_array($this->state, [self::STATE_PENDING, self::STATE_LOCKED]);
    }

    /**
     * Satisfy a specific named condition (e.g. 'property_inspection').
     */
    public function satisfyCondition(string $condition): self
    {
        $conditions = $this->conditions ?? [];
        $conditions[$condition] = true;
        $this->conditions = $conditions;
        $this->save();
        return $this;
    }

    /**
     * Transition state to locked and timestamp it.
     */
    public function lock(): self
    {
        $this->state = self::STATE_LOCKED;
        $this->locked_at = now();
        $this->save();
        return $this;
    }

    /**
     * Transition state to executed.
     */
    public function execute(): self
    {
        $this->state = self::STATE_EXECUTED;
        $this->executed_at = now();
        $this->save();
        return $this;
    }

    /**
     * Transition state to disputed.
     */
    public function dispute(string $reason = ''): self
    {
        $this->state = self::STATE_DISPUTED;
        $this->disputed_at = now();
        $this->notes = $reason;
        $this->save();
        return $this;
    }
}

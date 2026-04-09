<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class TransactionLedger extends Model
{
    /**
     * This table is IMMUTABLE — records are never updated after creation.
     * We disable the updated_at timestamp entirely.
     */
    const UPDATED_AT = null;

    protected $fillable = [
        'reference_type',
        'reference_id',
        'action',
        'previous_hash',
        'current_hash',
        'payload',
        'recorded_by',
    ];

    protected $casts = [
        'payload' => 'array',
        'created_at' => 'datetime',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    /**
     * The model that triggered this ledger entry (polymorphic).
     * e.g. PropertyAsset, Property, FractionalOwnership …
     */
    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeByAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    public function scopeChronological($query)
    {
        return $query->orderBy('id', 'asc');
    }

    // ─── Guards ───────────────────────────────────────────────────────────────

    /**
     * Prevent any update — ledger rows are write-once.
     *
     * @throws \RuntimeException
     */
    public function save(array $options = []): bool
    {
        if (!$this->exists) {
            return parent::save($options);
        }
        throw new \RuntimeException(
            'TransactionLedger records are immutable and cannot be updated.'
        );
    }

    /**
     * Prevent deletion.
     *
     * @throws \RuntimeException
     */
    public function delete(): bool|null
    {
        throw new \RuntimeException(
            'TransactionLedger records cannot be deleted — they are a permanent audit trail.'
        );
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    /**
     * Verify this entry's own hash is consistent.
     */
    public function isHashValid(): bool
    {
        $expected = hash('sha256', json_encode($this->payload) . $this->previous_hash);
        return hash_equals($expected, $this->current_hash);
    }
}

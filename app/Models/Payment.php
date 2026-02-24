<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'property_id',
        'payment_method_id',
        'transaction_id',
        'idempotency_key',
        'secure_token',
        'amount',
        'currency',
        'payment_method', // Enum from legacy table if still used, otherwise mapped via relation
        'status',
        'type',
        'description',
        'metadata',
        'gateway',
        'gateway_transaction_id',
        'ip_address',
        'user_agent',
        'fraud_status',
        'processed_at',
        'expires_at',
        'refunded_at',
        'refunded_amount',
    ];

    protected $casts = [
        'metadata' => 'array',
        'processed_at' => 'datetime',
        'expires_at' => 'datetime',
        'refunded_at' => 'datetime',
        'amount' => 'decimal:2',
        'refunded_amount' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function method(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }
}

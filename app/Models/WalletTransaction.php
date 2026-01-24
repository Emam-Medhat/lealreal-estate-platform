<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WalletTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'wallet_id',
        'type',
        'amount',
        'balance_before',
        'balance_after',
        'payment_method',
        'description',
        'status',
        'reference_id',
        'gateway',
        'gateway_transaction_id',
        'gateway_response',
        'fees',
        'net_amount',
        'sender_id',
        'recipient_id',
        'processed_at',
        'failed_at',
        'failure_reason',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:8',
        'balance_before' => 'decimal:8',
        'balance_after' => 'decimal:8',
        'fees' => 'decimal:8',
        'net_amount' => 'decimal:8',
        'processed_at' => 'datetime',
        'failed_at' => 'datetime',
        'gateway_response' => 'array',
        'metadata' => 'json',
    ];

    protected $attributes = [
        'status' => 'pending',
        'fees' => 0,
    ];

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(UserWallet::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'deposit' => __('Deposit'),
            'withdrawal' => __('Withdrawal'),
            'transfer_in' => __('Transfer In'),
            'transfer_out' => __('Transfer Out'),
            'payment' => __('Payment'),
            'refund' => __('Refund'),
            'bonus' => __('Bonus'),
            'penalty' => __('Penalty'),
            default => __(ucfirst($this->type))
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => __('Pending'),
            'processing' => __('Processing'),
            'completed' => __('Completed'),
            'failed' => __('Failed'),
            'cancelled' => __('Cancelled'),
            'refunded' => __('Refunded'),
            default => __(ucfirst($this->status))
        };
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return match($this->payment_method) {
            'credit_card' => __('Credit Card'),
            'debit_card' => __('Debit Card'),
            'bank_transfer' => __('Bank Transfer'),
            'paypal' => __('PayPal'),
            'stripe' => __('Stripe'),
            'crypto' => __('Cryptocurrency'),
            'wallet' => __('Wallet Transfer'),
            default => __(ucfirst(str_replace('_', ' ', $this->payment_method)))
        };
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isDeposit(): bool
    {
        return $this->type === 'deposit';
    }

    public function isWithdrawal(): bool
    {
        return $this->type === 'withdrawal';
    }

    public function isTransfer(): bool
    {
        return in_array($this->type, ['transfer_in', 'transfer_out']);
    }

    public function markAsCompleted(): bool
    {
        return $this->update([
            'status' => 'completed',
            'processed_at' => now(),
        ]);
    }

    public function markAsFailed(string $reason = null): bool
    {
        return $this->update([
            'status' => 'failed',
            'failed_at' => now(),
            'failure_reason' => $reason,
        ]);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByPaymentMethod($query, string $paymentMethod)
    {
        return $query->where('payment_method', $paymentMethod);
    }

    public function scopeByGateway($query, string $gateway)
    {
        return $query->where('gateway', $gateway);
    }

    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->whereHas('wallet', function ($walletQuery) use ($userId) {
            $walletQuery->where('user_id', $userId);
        });
    }
}

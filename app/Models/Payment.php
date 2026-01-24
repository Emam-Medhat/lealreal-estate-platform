<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'payment_method_id',
        'invoice_id',
        'amount',
        'currency',
        'reference_id',
        'description',
        'status',
        'gateway',
        'gateway_transaction_id',
        'gateway_response',
        'fees',
        'net_amount',
        'due_date',
        'paid_at',
        'failed_at',
        'failure_reason',
        'refunded_at',
        'refund_amount',
        'refund_reason',
        'metadata',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:8',
        'fees' => 'decimal:8',
        'net_amount' => 'decimal:8',
        'refund_amount' => 'decimal:8',
        'due_date' => 'datetime',
        'paid_at' => 'datetime',
        'failed_at' => 'datetime',
        'refunded_at' => 'datetime',
        'gateway_response' => 'array',
        'metadata' => 'json',
    ];

    protected $attributes = [
        'status' => 'pending',
        'currency' => 'USD',
        'fees' => 0,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(PaymentRefund::class);
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'payment' => __('Payment'),
            'refund' => __('Refund'),
            'chargeback' => __('Chargeback'),
            'dispute' => __('Dispute'),
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
            'partially_refunded' => __('Partially Refunded'),
            'disputed' => __('Disputed'),
            'charged_back' => __('Charged Back'),
            default => __(ucfirst($this->status))
        };
    }

    public function getGatewayLabelAttribute(): string
    {
        return match($this->gateway) {
            'stripe' => __('Stripe'),
            'paypal' => __('PayPal'),
            'square' => __('Square'),
            'authorize_net' => __('Authorize.net'),
            'braintree' => __('Braintree'),
            'bank_transfer' => __('Bank Transfer'),
            'crypto' => __('Cryptocurrency'),
            'cash' => __('Cash'),
            'check' => __('Check'),
            default => __(ucfirst(str_replace('_', ' ', $this->gateway)))
        };
    }

    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount, 2) . ' ' . $this->currency;
    }

    public function getFormattedFeesAttribute(): string
    {
        return number_format($this->fees, 2) . ' ' . $this->currency;
    }

    public function getFormattedNetAmountAttribute(): string
    {
        return number_format($this->net_amount, 2) . ' ' . $this->currency;
    }

    public function getFormattedRefundAmountAttribute(): string
    {
        return number_format($this->refund_amount, 2) . ' ' . $this->currency;
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

    public function isRefunded(): bool
    {
        return $this->status === 'refunded';
    }

    public function isPartiallyRefunded(): bool
    {
        return $this->status === 'partially_refunded';
    }

    public function isDisputed(): bool
    {
        return $this->status === 'disputed';
    }

    public function canBeRefunded(): bool
    {
        return $this->isCompleted() && !$this->isRefunded() && !$this->isDisputed();
    }

    public function canBePartiallyRefunded(): bool
    {
        return $this->isCompleted() && !$this->isRefunded() && !$this->isDisputed() && $this->refund_amount < $this->net_amount;
    }

    public function getTotalRefunded(): float
    {
        return $this->refunds()->sum('amount');
    }

    public function getRefundableAmount(): float
    {
        return $this->net_amount - $this->getTotalRefunded();
    }

    public function markAsCompleted(string $transactionId = null): bool
    {
        return $this->update([
            'status' => 'completed',
            'paid_at' => now(),
            'gateway_transaction_id' => $transactionId,
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

    public function markAsCancelled(): bool
    {
        return $this->update([
            'status' => 'cancelled',
        ]);
    }

    public function addRefund(float $amount, string $reason = null): PaymentRefund
    {
        $refund = $this->refunds()->create([
            'amount' => $amount,
            'reason' => $reason,
            'status' => 'completed',
            'processed_at' => now(),
        ]);

        $totalRefunded = $this->getTotalRefunded();
        
        if ($totalRefunded >= $this->net_amount) {
            $this->update([
                'status' => 'refunded',
                'refund_amount' => $totalRefunded,
                'refunded_at' => now(),
            ]);
        } else {
            $this->update([
                'status' => 'partially_refunded',
                'refund_amount' => $totalRefunded,
            ]);
        }

        return $refund;
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

    public function scopeRefunded($query)
    {
        return $query->where('status', 'refunded');
    }

    public function scopeByGateway($query, string $gateway)
    {
        return $query->where('gateway', $gateway);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    public function scopeAmountRange($query, float $minAmount, float $maxAmount = null)
    {
        $query->where('amount', '>=', $minAmount);
        
        if ($maxAmount) {
            $query->where('amount', '<=', $maxAmount);
        }
        
        return $query;
    }

    public function scopeWithInvoice($query)
    {
        return $query->whereNotNull('invoice_id');
    }

    public function scopeWithoutInvoice($query)
    {
        return $query->whereNull('invoice_id');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'pending')
                    ->where('due_date', '<', now());
    }

    public function scopeDueToday($query)
    {
        return $query->where('status', 'pending')
                    ->whereDate('due_date', today());
    }

    public function scopeDueSoon($query, int $days = 7)
    {
        return $query->where('status', 'pending')
                    ->whereBetween('due_date', [now(), now()->addDays($days)]);
    }
}

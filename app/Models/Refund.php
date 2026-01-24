<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Refund extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'payment_id',
        'reference',
        'type',
        'amount',
        'currency',
        'reason',
        'refund_method',
        'processor_id',
        'notes',
        'evidence',
        'status',
        'transaction_hash',
        'gateway_transaction_id',
        'gateway_response',
        'created_at',
        'completed_at',
        'failed_at',
        'approved_at',
        'rejected_at',
        'cancelled_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'evidence' => 'array',
        'amount' => 'decimal:15,2',
        'created_at' => 'datetime',
        'completed_at' => 'datetime',
        'failed_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'user_id' => 'integer',
        'payment_id' => 'integer',
        'processor_id' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processor_id');
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    // Helper methods
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isProcessing(): bool
    {
        return $this->status === 'processing';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function getAmountFormattedAttribute(): string
    {
        return number_format($this->amount, 2);
    }

    public function getCurrencySymbolAttribute(): string
    {
        $symbols = [
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'JPY' => '¥',
            'CNY' => '¥',
            'AUD' => 'A$',
            'CAD' => 'C$',
        ];

        return $symbols[$this->currency] ?? $this->currency;
    }

    public function getAmountWithCurrencyAttribute(): string
    {
        return $this->getCurrencySymbolAttribute() . $this->getAmountFormattedAttribute();
    }

    public function getReferenceAttribute(): string
    {
        return $this->reference ?? '';
    }

    public function getTypeDisplayAttribute(): string
    {
        $types = [
            'full' => 'Full Refund',
            'partial' => 'Partial Refund',
            'dispute' => 'Dispute Refund',
        ];

        return $types[$this->type] ?? 'Unknown';
    }

    public function getStatusDisplayAttribute(): string
    {
        $statuses = [
            'pending' => '🟡 Pending',
            'processing' => '🔵 Processing',
            'completed' => '🟢 Completed',
            'failed' => '🔴 Failed',
            'rejected' => '🔴 Rejected',
            'cancelled' => '⚫ Cancelled',
        ];

        return $statuses[$this->status] ?? '❓ Unknown';
    }

    public function getRefundMethodDisplayAttribute(): string
    {
        $methods = [
            'original_method' => 'Original Payment Method',
            'credit_card' => 'Credit Card',
            'bank_transfer' => 'Bank Transfer',
            'crypto' => 'Cryptocurrency',
            'paypal' => 'PayPal',
            'check' => 'Check',
        ];

        return $methods[$this->refund_method] ?? $this->refund_method;
    }

    public function getProcessingTimeAttribute(): ?string
    {
        if (!$this->completed_at || !$this->created_at) return null;
        
        $processingTime = $this->created_at->diffInHours($this->completed_at);
        
        if ($processingTime < 1) {
            return '< 1 hour';
        } elseif ($processingTime < 24) {
            return $processingTime . ' hours';
        } else {
            return round($processingTime / 24, 1) . ' days';
        }
    }

    public function getDaysSinceCreatedAttribute(): int
    {
        return $this->created_at->diffInDays(now());
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->isPending() && $this->getDaysSinceCreatedAttribute() > 7;
    }

    public function getEvidenceCountAttribute(): int
    {
        return count($this->evidence ?? []);
    }

    public function hasEvidence(): bool
    {
        return $this->getEvidenceCountAttribute() > 0;
    }

    public function getEvidenceTypesAttribute(): array
    {
        return collect($this->evidence ?? [])->pluck('type')->unique()->toArray();
    }

    public function getHasReceiptAttribute(): bool
    {
        return !empty($this->gateway_response) && 
               isset($this->gateway_response['receipt_url']);
    }

    public function getReceiptUrlAttribute(): ?string
    {
        return $this->getHasReceiptAttribute() ? 
               $this->gateway_response['receipt_url'] : null;
    }

    public function getTransactionUrlAttribute(): ?string
    {
        if (empty($this->transaction_hash)) return null;
        
        $explorers = [
            'ethereum' => 'https://etherscan.io/tx/',
            'polygon' => 'https://polygonscan.com/tx/',
            'binance' => 'https://bscscan.com/tx/',
            'bitcoin' => 'https://blockstream.info/tx/',
        ];

        // Determine blockchain from payment method or refund method
        $blockchain = 'ethereum'; // Default
        
        return ($explorers[$blockchain] ?? '') . $this->transaction_hash;
    }

    public function getCanRetryAttribute(): bool
    {
        return in_array($this->status, ['failed', 'cancelled']);
    }

    public function getCanCancelAttribute(): bool
    {
        return $this->status === 'pending';
    }

    public function getPriorityLevelAttribute(): string
    {
        if ($this->amount > 1000) return 'High';
        if ($this->amount > 100) return 'Medium';
        return 'Low';
    }

    public function getPriorityColorAttribute(): string
    {
        $colors = [
            'High' => 'text-red-600',
            'Medium' => 'text-yellow-600',
            'Low' => 'text-green-600',
        ];

        return $colors[$this->getPriorityLevelAttribute()] ?? 'text-gray-600';
    }

    public function getEstimatedCompletionDateAttribute(): ?string
    {
        if (!$this->isPending()) return null;
        
        // Estimate based on refund method
        $estimates = [
            'original_method' => 3, // 3 days
            'credit_card' => 5, // 5 days
            'bank_transfer' => 7, // 7 days
            'crypto' => 1, // 1 day
            'paypal' => 2, // 2 days
            'check' => 14, // 14 days
        ];

        $days = $estimates[$this->refund_method] ?? 5;
        return $this->created_at->addDays($days)->format('Y-m-d');
    }

    public function getIsExpeditedAttribute(): bool
    {
        return $this->priority === 'high' || $this->amount > 1000;
    }

    public function getFeeAmountAttribute(): float
    {
        // Calculate refund fee based on method and amount
        $feeRates = [
            'original_method' => 0.00, // Usually free
            'credit_card' => 0.03, // 3%
            'bank_transfer' => 0.01, // 1%
            'crypto' => 0.02, // 2%
            'paypal' => 0.025, // 2.5%
            'check' => 0.005, // 0.5%
        ];

        $rate = $feeRates[$this->refund_method] ?? 0;
        return $this->amount * $rate;
    }

    public function getFeeAmountFormattedAttribute(): string
    {
        return number_format($this->getFeeAmountAttribute(), 2);
    }

    public function getNetAmountAttribute(): float
    {
        return $this->amount - $this->getFeeAmountAttribute();
    }

    public function getNetAmountFormattedAttribute(): string
    {
        return number_format($this->getNetAmountAttribute(), 2);
    }

    public function getFeePercentageAttribute(): float
    {
        if ($this->amount == 0) return 0;
        return ($this->getFeeAmountAttribute() / $this->amount) * 100;
    }

    public function getFeePercentageFormattedAttribute(): string
    {
        return number_format($this->getFeePercentageAttribute(), 2) . '%';
    }

    public function getRefundSummaryAttribute(): array
    {
        return [
            'reference' => $this->reference,
            'amount' => $this->amount,
            'net_amount' => $this->getNetAmountAttribute(),
            'fee_amount' => $this->getFeeAmountAttribute(),
            'currency' => $this->currency,
            'status' => $this->status,
            'type' => $this->type,
            'method' => $this->refund_method,
            'processing_time' => $this->getProcessingTimeAttribute(),
            'evidence_count' => $this->getEvidenceCountAttribute(),
        ];
    }

    public function getTimelineAttribute(): array
    {
        $timeline = [];
        
        if ($this->created_at) {
            $timeline[] = [
                'event' => 'Refund Requested',
                'date' => $this->created_at,
                'description' => 'Refund request submitted',
                'icon' => '📋',
            ];
        }
        
        if ($this->approved_at) {
            $timeline[] = [
                'event' => 'Refund Approved',
                'date' => $this->approved_at,
                'description' => 'Refund request approved',
                'icon' => '✅',
            ];
        }
        
        if ($this->completed_at) {
            $timeline[] = [
                'event' => 'Refund Completed',
                'date' => $this->completed_at,
                'description' => 'Refund processed successfully',
                'icon' => '🎉',
            ];
        }
        
        if ($this->rejected_at) {
            $timeline[] = [
                'event' => 'Refund Rejected',
                'date' => $this->rejected_at,
                'description' => 'Refund request rejected',
                'icon' => '❌',
            ];
        }
        
        if ($this->cancelled_at) {
            $timeline[] = [
                'event' => 'Refund Cancelled',
                'date' => $this->cancelled_at,
                'description' => 'Refund request cancelled',
                'icon' => '⚫',
            ];
        }
        
        if ($this->failed_at) {
            $timeline[] = [
                'event' => 'Refund Failed',
                'date' => $this->failed_at,
                'description' => 'Refund processing failed',
                'icon' => '🔴',
            ];
        }
        
        return $timeline;
    }
}

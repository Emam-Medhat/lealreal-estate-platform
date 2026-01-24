<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SecurityDeposit extends Model
{
    use HasFactory;

    protected $fillable = [
        'lease_id',
        'tenant_id',
        'deposit_number',
        'amount',
        'received_amount',
        'refund_amount',
        'due_date',
        'received_date',
        'refund_date',
        'status',
        'payment_method',
        'refund_method',
        'deductions',
        'deduction_reasons',
        'bank_account',
        'receipt_number',
        'refund_receipt_number',
        'notes',
        'user_id',
    ];

    protected $casts = [
        'due_date' => 'date',
        'received_date' => 'datetime',
        'refund_date' => 'datetime',
        'amount' => 'decimal:2',
        'received_amount' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'deductions' => 'decimal:2',
        'deduction_reasons' => 'array',
    ];

    public function lease(): BelongsTo
    {
        return $this->belongsTo(Lease::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeReceived($query)
    {
        return $query->where('status', 'received');
    }

    public function scopeRefunded($query)
    {
        return $query->where('status', 'refunded');
    }

    public function scopePartiallyRefunded($query)
    {
        return $query->where('status', 'partially_refunded');
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())->where('status', 'pending');
    }

    // Attributes
    public function getIsReceivedAttribute(): bool
    {
        return $this->status === 'received';
    }

    public function getIsRefundedAttribute(): bool
    {
        return in_array($this->status, ['refunded', 'partially_refunded']);
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->due_date < now() && $this->status === 'pending';
    }

    public function getRemainingAmountAttribute(): float
    {
        return $this->amount - $this->received_amount;
    }

    public function getRefundableAmountAttribute(): float
    {
        return $this->received_amount - $this->deductions - $this->refund_amount;
    }

    public function getDaysOverdueAttribute(): int
    {
        if (!$this->is_overdue) return 0;
        return now()->diffInDays($this->due_date);
    }

    public function getIsFullyRefundedAttribute(): bool
    {
        return $this->status === 'refunded';
    }

    // Methods
    public function markAsReceived(float $amount, string $method, ?string $receiptNumber = null): void
    {
        $this->update([
            'received_amount' => $amount,
            'received_date' => now(),
            'payment_method' => $method,
            'receipt_number' => $receiptNumber ?? $this->generateReceiptNumber(),
            'status' => $amount >= $this->amount ? 'received' : 'partial',
        ]);
    }

    public function addDeduction(float $amount, string $reason): void
    {
        $currentDeductions = $this->deduction_reasons ?? [];
        $currentDeductions[] = [
            'amount' => $amount,
            'reason' => $reason,
            'date' => now()->toDateString(),
        ];

        $this->update([
            'deductions' => $this->deductions + $amount,
            'deduction_reasons' => $currentDeductions,
        ]);
    }

    public function processRefund(float $amount, string $method, ?string $receiptNumber = null): void
    {
        $totalRefund = $this->refund_amount + $amount;
        $status = $totalRefund >= $this->refundable_amount ? 'refunded' : 'partially_refunded';

        $this->update([
            'refund_amount' => $totalRefund,
            'refund_date' => now(),
            'refund_method' => $method,
            'refund_receipt_number' => $receiptNumber ?? $this->generateRefundReceiptNumber(),
            'status' => $status,
        ]);
    }

    public function generateReceiptNumber(): string
    {
        return 'DEP-' . date('Y') . '-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
    }

    public function generateRefundReceiptNumber(): string
    {
        return 'REF-' . date('Y') . '-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
    }

    public function canBeRefunded(): bool
    {
        return $this->status === 'received' && $this->refundable_amount > 0;
    }

    public function getStatusBadge(): string
    {
        return match($this->status) {
            'pending' => '<span class="badge badge-warning">معلق</span>',
            'received' => '<span class="badge badge-success">مستلم</span>',
            'refunded' => '<span class="badge badge-info">مسترد</span>',
            'partially_refunded' => '<span class="badge badge-primary">مسترد جزئياً</span>',
            'partial' => '<span class="badge badge-secondary">جزئي</span>',
            default => '<span class="badge badge-secondary">' . $this->status . '</span>',
        };
    }
}

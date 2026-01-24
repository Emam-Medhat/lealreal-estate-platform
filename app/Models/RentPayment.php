<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RentPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'lease_id',
        'payment_number',
        'due_date',
        'amount',
        'paid_amount',
        'late_fee',
        'payment_date',
        'payment_method',
        'status',
        'transaction_id',
        'receipt_number',
        'notes',
        'auto_processed',
        'reminder_sent',
        'late_fee_applied',
        'partial_payment',
        'user_id',
    ];

    protected $casts = [
        'due_date' => 'date',
        'payment_date' => 'datetime',
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'late_fee' => 'decimal:2',
        'auto_processed' => 'boolean',
        'reminder_sent' => 'boolean',
        'late_fee_applied' => 'boolean',
        'partial_payment' => 'boolean',
    ];

    public function lease(): BelongsTo
    {
        return $this->belongsTo(Lease::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue');
    }

    public function scopePartial($query)
    {
        return $query->where('partial_payment', true);
    }

    public function scopeDue($query)
    {
        return $query->where('due_date', '<=', now());
    }

    public function scopeOverdueDate($query)
    {
        return $query->where('due_date', '<', now())
                    ->where('status', '!=', 'paid');
    }

    // Attributes
    public function getIsOverdueAttribute(): bool
    {
        return $this->due_date < now() && $this->status !== 'paid';
    }

    public function getDaysOverdueAttribute(): int
    {
        if (!$this->is_overdue) return 0;
        return now()->diffInDays($this->due_date);
    }

    public function getRemainingAmountAttribute(): float
    {
        return $this->amount - $this->paid_amount;
    }

    public function getIsPaidAttribute(): bool
    {
        return $this->status === 'paid';
    }

    public function getIsPartialAttribute(): bool
    {
        return $this->partial_payment || ($this->paid_amount > 0 && $this->paid_amount < $this->amount);
    }

    public function getTotalAmountAttribute(): float
    {
        return $this->amount + $this->late_fee;
    }

    public function getTotalPaidAttribute(): float
    {
        return $this->paid_amount;
    }

    // Methods
    public function markAsPaid(float $amount, string $method, ?string $transactionId = null): void
    {
        $this->update([
            'paid_amount' => $amount,
            'payment_date' => now(),
            'payment_method' => $method,
            'transaction_id' => $transactionId,
            'status' => $amount >= $this->amount ? 'paid' : 'partial',
            'partial_payment' => $amount < $this->amount,
        ]);
    }

    public function applyLateFee(): void
    {
        if (!$this->late_fee_applied && $this->is_overdue) {
            $lateFee = $this->lease->calculateLateFee();
            $this->update([
                'late_fee' => $lateFee,
                'late_fee_applied' => true,
            ]);
        }
    }

    public function generateReceiptNumber(): string
    {
        return 'RCP-' . date('Y') . '-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
    }

    public function canBePaid(): bool
    {
        return in_array($this->status, ['pending', 'partial', 'overdue']);
    }

    public function getPaymentStatusBadge(): string
    {
        return match($this->status) {
            'paid' => '<span class="badge badge-success">مدفوع</span>',
            'pending' => '<span class="badge badge-warning">معلق</span>',
            'overdue' => '<span class="badge badge-danger">متأخر</span>',
            'partial' => '<span class="badge badge-info">جزئي</span>',
            default => '<span class="badge badge-secondary">' . $this->status . '</span>',
        };
    }
}

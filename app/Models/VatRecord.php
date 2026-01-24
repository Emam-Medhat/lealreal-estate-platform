<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VatRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'period',
        'taxable_sales',
        'taxable_purchases',
        'vat_rate',
        'vat_collected',
        'vat_paid',
        'vat_payable',
        'status',
        'submitted_at',
        'paid_at',
        'payment_method',
        'reference_number',
        'user_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'taxable_sales' => 'decimal:2',
        'taxable_purchases' => 'decimal:2',
        'vat_rate' => 'decimal:2',
        'vat_collected' => 'decimal:2',
        'vat_paid' => 'decimal:2',
        'vat_payable' => 'decimal:2',
        'submitted_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeSubmitted($query)
    {
        return $query->where('status', 'submitted');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopePayable($query)
    {
        return $query->where('vat_payable', '>', 0);
    }

    public function scopeRefundable($query)
    {
        return $query->where('vat_payable', '<', 0);
    }

    public function getNetSalesAttribute(): float
    {
        return $this->taxable_sales + $this->vat_collected;
    }

    public function getNetPurchasesAttribute(): float
    {
        return $this->taxable_purchases + $this->vat_paid;
    }

    public function getVatLiabilityAttribute(): float
    {
        return $this->vat_collected - $this->vat_paid;
    }

    public function isPayable(): bool
    {
        return $this->vat_payable > 0;
    }

    public function isRefundable(): bool
    {
        return $this->vat_payable < 0;
    }

    public function isSubmitted(): bool
    {
        return $this->status === 'submitted';
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function markAsSubmitted(): void
    {
        $this->update([
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);
    }

    public function markAsPaid(string $paymentMethod, ?string $referenceNumber = null): void
    {
        $this->update([
            'status' => 'paid',
            'paid_at' => now(),
            'payment_method' => $paymentMethod,
            'reference_number' => $referenceNumber,
        ]);
    }

    public function calculateVatPayable(): void
    {
        $vatPayable = $this->vat_collected - $this->vat_paid;
        
        $this->update([
            'vat_payable' => $vatPayable,
            'status' => $vatPayable > 0 ? 'payable' : 'refundable',
        ]);
    }
}

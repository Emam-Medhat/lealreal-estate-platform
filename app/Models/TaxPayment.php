<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaxPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_tax_id',
        'tax_filing_id',
        'user_id',
        'amount',
        'payment_method',
        'payment_date',
        'status',
        'transaction_id',
        'reference_number',
        'confirmation_number',
        'processing_fee',
        'receipt_path',
        'processed_at',
        'completed_at',
        'cancelled_at',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'processing_fee' => 'decimal:2',
        'payment_date' => 'date',
        'processed_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function propertyTax(): BelongsTo
    {
        return $this->belongsTo(PropertyTax::class);
    }

    public function taxFiling(): BelongsTo
    {
        return $this->belongsTo(TaxFiling::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function receipts(): HasMany
    {
        return $this->hasMany(TaxPaymentReceipt::class);
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

    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function getTotalAmountAttribute(): float
    {
        return $this->amount + ($this->processing_fee ?? 0);
    }

    public function canBeProcessed(): bool
    {
        return $this->status === 'pending';
    }

    public function canBeCompleted(): bool
    {
        return $this->status === 'processing';
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'processing']);
    }

    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    public function markAsCancelled(): void
    {
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);
    }
}

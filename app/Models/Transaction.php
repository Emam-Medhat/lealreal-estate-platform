<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'property_id',
        'type',
        'amount',
        'currency',
        'converted_amount',
        'user_currency',
        'exchange_rate',
        'status',
        'payment_method',
        'payment_id',
        'description',
        'metadata',
        'processed_at',
        'completed_at'
    ];

    protected $casts = [
        'amount' => 'decimal:8',
        'converted_amount' => 'decimal:8',
        'exchange_rate' => 'decimal:8',
        'metadata' => 'json',
        'processed_at' => 'datetime',
        'completed_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function currencyModel()
    {
        return $this->belongsTo(Currency::class, 'currency', 'code');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForProperty($query, $propertyId)
    {
        return $query->where('property_id', $propertyId);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function getFormattedAmount(): string
    {
        $currency = Currency::where('code', $this->currency)->first();
        return $currency ? $currency->formatAmount($this->amount) : number_format($this->amount, 2);
    }

    public function getFormattedConvertedAmount(): string
    {
        $currency = Currency::where('code', $this->user_currency)->first();
        return $currency ? $currency->formatAmount($this->converted_amount) : number_format($this->converted_amount, 2);
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

    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now()
        ]);
    }

    public function markAsFailed(string $reason = null): void
    {
        $this->update([
            'status' => 'failed',
            'metadata' => array_merge($this->metadata ?? [], [
                'failure_reason' => $reason,
                'failed_at' => now()->toISOString()
            ])
        ]);
    }

    public static function getTypes(): array
    {
        return [
            'property_purchase' => 'Property Purchase',
            'property_rental' => 'Property Rental',
            'investment' => 'Investment',
            'service_fee' => 'Service Fee',
            'commission' => 'Commission',
            'refund' => 'Refund',
            'bonus' => 'Bonus',
            'penalty' => 'Penalty'
        ];
    }

    public static function getStatuses(): array
    {
        return [
            'pending' => 'Pending',
            'processing' => 'Processing',
            'completed' => 'Completed',
            'failed' => 'Failed',
            'cancelled' => 'Cancelled',
            'refunded' => 'Refunded'
        ];
    }
}

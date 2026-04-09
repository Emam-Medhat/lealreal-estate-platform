<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_number',
        'user_id',
        'total_amount',
        'currency',
        'status',
        'payment_status',
        'payment_method',
        'transaction_id',
        'shipping_address',
        'billing_address',
        'notes',
        'paid_at',
        'shipped_at',
        'delivered_at',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'shipping_address' => 'array',
        'billing_address' => 'array',
        'paid_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $dates = [
        'paid_at',
        'shipped_at',
        'delivered_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function itemable(): MorphMany
    {
        return $this->morphMany(OrderItem::class, 'itemable');
    }

    // Scopes
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPaymentStatus($query, $paymentStatus)
    {
        return $query->where('payment_status', $paymentStatus);
    }

    // Accessors
    public function getStatusTextAttribute(): string
    {
        return match($this->status) {
            'pending' => 'قيد الانتظار',
            'processing' => 'قيد المعالجة',
            'completed' => 'مكتمل',
            'cancelled' => 'ملغي',
            'refunded' => 'مسترد',
            default => $this->status,
        };
    }

    public function getPaymentStatusTextAttribute(): string
    {
        return match($this->payment_status) {
            'pending' => 'قيد الانتظار',
            'paid' => 'مدفوع',
            'failed' => 'فشل',
            'refunded' => 'مسترد',
            default => $this->payment_status,
        };
    }

    public function getFormattedTotalAttribute(): string
    {
        return number_format($this->total_amount, 2) . ' ' . $this->currency;
    }

    // Methods
    public function markAsPaid(): void
    {
        $this->update([
            'payment_status' => 'paid',
            'paid_at' => now(),
            'status' => 'processing',
        ]);
    }

    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'delivered_at' => now(),
        ]);
    }

    public function markAsCancelled(): void
    {
        $this->update([
            'status' => 'cancelled',
        ]);
    }

    public function markAsRefunded(): void
    {
        $this->update([
            'status' => 'refunded',
            'payment_status' => 'refunded',
        ]);
    }

    public function generateOrderNumber(): string
    {
        do {
            $orderNumber = 'ORD-' . date('Ymd') . '-' . str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
        } while (static::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }

    public function calculateTotal(): float
    {
        return $this->items()->sum('total');
    }

    public function updateTotal(): void
    {
        $this->update(['total_amount' => $this->calculateTotal()]);
    }
}

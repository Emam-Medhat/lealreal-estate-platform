<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'itemable_type',
        'itemable_id',
        'item_name',
        'item_description',
        'price',
        'quantity',
        'total',
        'item_data',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'total' => 'decimal:2',
        'item_data' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function itemable(): MorphTo
    {
        return $this->morphTo();
    }

    // Accessors
    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price, 2);
    }

    public function getFormattedTotalAttribute(): string
    {
        return number_format($this->total, 2);
    }

    // Methods
    public function calculateTotal(): float
    {
        return $this->price * $this->quantity;
    }

    public function updateTotal(): void
    {
        $this->update(['total' => $this->calculateTotal()]);
    }
}

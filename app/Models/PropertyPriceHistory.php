<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyPriceHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'old_price',
        'new_price',
        'currency',
        'change_reason',
        'change_type',
        'change_percentage',
        'changed_by',
    ];

    protected $casts = [
        'old_price' => 'decimal:2',
        'new_price' => 'decimal:2',
        'change_percentage' => 'decimal:2',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    public function getFormattedOldPriceAttribute(): string
    {
        return number_format($this->old_price, 2) . ' ' . $this->currency;
    }

    public function getFormattedNewPriceAttribute(): string
    {
        return number_format($this->new_price, 2) . ' ' . $this->currency;
    }

    public function getFormattedChangePercentageAttribute(): string
    {
        $sign = $this->change_type === 'increase' ? '+' : '-';
        return $sign . $this->change_percentage . '%';
    }

    public function scopeByType($query, $type)
    {
        return $query->where('change_type', $type);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}

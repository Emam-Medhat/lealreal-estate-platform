<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadConversion extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'converted_to_type',
        'converted_to_id',
        'conversion_value',
        'conversion_date',
        'notes',
        'converted_by',
    ];

    protected $casts = [
        'conversion_value' => 'decimal:2',
        'conversion_date' => 'date',
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function convertedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'converted_by');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('converted_to_type', $type);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('conversion_date', [$startDate, $endDate]);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('conversion_date', '>=', now()->subDays($days));
    }
}

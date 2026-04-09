<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyStatusHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'old_status',
        'new_status',
        'reason',
        'changed_by',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('new_status', $status);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public static function recordStatusChange($propertyId, $oldStatus, $newStatus, $reason = null, $changedBy = null): self
    {
        return self::create([
            'property_id' => $propertyId,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'reason' => $reason,
            'changed_by' => $changedBy,
        ]);
    }
}

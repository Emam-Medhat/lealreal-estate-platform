<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FraudDetectionLog extends Model
{
    protected $table = 'fraud_detection_logs';

    protected $fillable = [
        'entity_type',
        'entity_id',
        'detection_type',
        'fraud_score',
        'details',
        'action_taken',
        'triggered_by',
    ];

    protected $casts = [
        'details' => 'array',
        'fraud_score' => 'integer',
    ];

    // Detection type constants
    const TYPE_IMAGE_HASH = 'image_hash';
    const TYPE_ADDRESS_VALIDATION = 'address_validation';
    const TYPE_PRICE_ANOMALY = 'price_anomaly';
    const TYPE_FAKE_ACCOUNT = 'fake_account';
    const TYPE_DUPLICATE_DETECTION = 'duplicate_detection';

    // Action constants
    const ACTION_FLAGGED = 'flagged';
    const ACTION_BLOCKED = 'blocked';
    const ACTION_NOTIFIED = 'notified';
    const ACTION_NONE = 'none';

    public function triggeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'triggered_by');
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('detection_type', $type);
    }

    public function scopeHighRisk($query, int $threshold = 70)
    {
        return $query->where('fraud_score', '>=', $threshold);
    }

    public function scopeRecentDays($query, int $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeForEntity($query, string $type, int $id)
    {
        return $query->where('entity_type', $type)->where('entity_id', $id);
    }

    /**
     * Get average fraud score by detection type over a date range.
     */
    public static function averageByType(string $type, int $days = 30): float
    {
        return static::where('detection_type', $type)
            ->where('created_at', '>=', now()->subDays($days))
            ->avg('fraud_score') ?? 0.0;
    }

    /**
     * Get count of flagged items per type.
     */
    public static function flaggedCountByType(int $days = 7): \Illuminate\Support\Collection
    {
        return static::where('created_at', '>=', now()->subDays($days))
            ->selectRaw('detection_type, COUNT(*) as count, AVG(fraud_score) as avg_score')
            ->groupBy('detection_type')
            ->get();
    }
}

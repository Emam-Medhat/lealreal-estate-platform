<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyAnalytic extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'metric_type',
        'count',
        'date',
        'metadata',
    ];

    protected $casts = [
        'count' => 'integer',
        'date' => 'date',
        'metadata' => 'array',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function scopeByMetric($query, $metricType)
    {
        return $query->where('metric_type', $metricType);
    }

    public function scopeForPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('date', '>=', now()->subDays($days));
    }

    public function scopeByDate($query, $date)
    {
        return $query->where('date', $date);
    }

    public static function recordMetric($propertyId, $metricType, $count = 1, $metadata = null): self
    {
        try {
            return self::updateOrCreate(
                [
                    'property_id' => $propertyId,
                    'metric_type' => $metricType,
                    'date' => now()->toDateString(),
                ],
                [
                    'count' => \DB::raw("count + {$count}"),
                    'metadata' => $metadata,
                ]
            );
        } catch (\Exception $e) {
            // Fallback: use increment instead of DB::raw
            $analytic = self::firstOrCreate(
                [
                    'property_id' => $propertyId,
                    'metric_type' => $metricType,
                    'date' => now()->toDateString(),
                ]
            );
            
            $analytic->increment('count', $count);
            
            if ($metadata) {
                $analytic->metadata = $metadata;
                $analytic->save();
            }
            
            return $analytic;
        }
    }

    public function incrementCount($amount = 1): void
    {
        $this->increment('count', $amount);
    }
}

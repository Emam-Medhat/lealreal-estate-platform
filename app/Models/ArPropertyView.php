<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class ArPropertyView extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'user_id',
        'title',
        'description',
        'view_mode',
        'tracking_type',
        'interaction_type',
        'ar_content',
        'marker_images',
        'tracking_targets',
        'interaction_zones',
        'device_compatibility',
        'quality_settings',
        'ar_metadata',
        'status',
        'launch_count',
        'average_session_duration',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'ar_content' => 'array',
        'marker_images' => 'array',
        'tracking_targets' => 'array',
        'interaction_zones' => 'array',
        'device_compatibility' => 'array',
        'quality_settings' => 'array',
        'ar_metadata' => 'array',
        'launch_count' => 'integer',
        'average_session_duration' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the property that owns the AR view.
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * Get the user that created the AR view.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who created the AR view.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who updated the AR view.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the AR content items for the view.
     */
    public function arContent(): HasMany
    {
        return $this->hasMany(ArContentItem::class);
    }

    /**
     * Get the tracking targets for the view.
     */
    public function trackingTargets(): HasMany
    {
        return $this->hasMany(ArTrackingTarget::class);
    }

    /**
     * Get the interaction zones for the view.
     */
    public function interactionZones(): HasMany
    {
        return $this->hasMany(ArInteractionZone::class);
    }

    /**
     * Scope a query to only include views with high tracking accuracy.
     */
    public function scopeHighAccuracy($query)
    {
        return $query->whereHas('trackingTargets', function ($q) {
            $q->where('confidence_threshold', '>=', 0.9);
        });
    }

    /**
     * Scope a query to only include recent views.
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', Carbon::now()->subDays($days));
    }

    /**
     * Scope a query to only include views with specific status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Get view mode label in Arabic.
     */
    public function getViewModeLabelAttribute(): string
    {
        $modes = [
            'marker_based' => 'قائم على العلامات',
            'markerless' => 'بدون علامات',
            'location_based' => 'قائم على الموقع',
            'image_based' => 'قائم على الصورة',
        ];

        return $modes[$this->view_mode] ?? 'غير معروف';
    }

    /**
     * Get tracking type label in Arabic.
     */
    public function getTrackingTypeLabelAttribute(): string
    {
        $types = [
            'plane_detection' => 'اكتشاف المستويات',
            'object_recognition' => 'التعرف على الكائنات',
            'face_detection' => 'اكتشاف الوجوه',
            'image_tracking' => 'تتبع الصور',
        ];

        return $types[$this->tracking_type] ?? 'غير معروف';
    }

    /**
     * Get interaction type label in Arabic.
     */
    public function getInteractionTypeLabelAttribute(): string
    {
        $types = [
            'touch_gesture' => 'اللمسات والإيماءات',
            'voice_command' => 'الأوامر الصوتية',
            'gaze_based' => 'قائم على النظرة',
            'hand_gesture' => 'إيماءات اليد',
        ];

        return $types[$this->interaction_type] ?? 'غير معروف';
    }

    /**
     * Get status label in Arabic.
     */
    public function getStatusLabelAttribute(): string
    {
        $statuses = [
            'processing' => 'قيد المعالجة',
            'active' => 'نشط',
            'completed' => 'مكتمل',
            'failed' => 'فشل',
            'cancelled' => 'ملغي',
        ];

        return $statuses[$this->status] ?? 'غير معروف';
    }

    /**
     * Check if view is recent (within last 30 days).
     */
    public function isRecent(): bool
    {
        return $this->created_at->diffInDays(Carbon::now()) <= 30;
    }

    /**
     * Get tracking accuracy score.
     */
    public function getTrackingAccuracyAttribute(): float
    {
        $targets = $this->trackingTargets;
        
        if ($targets->count() === 0) {
            return 0;
        }
        
        $totalConfidence = $targets->sum('confidence_threshold');
        return $totalConfidence / $targets->count();
    }

    /**
     * Get content count.
     */
    public function getContentCountAttribute(): int
    {
        return $this->arContent()->count();
    }

    /**
     * Get interaction zones count.
     */
    public function getInteractionZonesCountAttribute(): int
    {
        return $this->interactionZones()->count();
    }

    /**
     * Check if view has marker images.
     */
    public function hasMarkerImages(): bool
    {
        return !empty($this->marker_images);
    }

    /**
     * Check if view supports device type.
     */
    public function supportsDevice($deviceType): bool
    {
        return in_array($deviceType, $this->device_compatibility ?? []);
    }

    /**
     * Get supported device types.
     */
    public function getSupportedDevicesAttribute(): array
    {
        return $this->device_compatibility ?? ['mobile', 'tablet'];
    }

    /**
     * Get quality score.
     */
    public function getQualityScoreAttribute(): float
    {
        $score = 0;
        
        // Tracking accuracy contribution
        $score += $this->tracking_accuracy * 40;
        
        // Content quality contribution
        if ($this->quality_settings['rendering_quality'] === 'high') {
            $score += 30;
        } elseif ($this->quality_settings['rendering_quality'] === 'medium') {
            $score += 20;
        } else {
            $score += 10;
        }
        
        // Interaction features contribution
        if ($this->interaction_type === 'hand_gesture') {
            $score += 20;
        } elseif ($this->interaction_type === 'voice_command') {
            $score += 15;
        } else {
            $score += 10;
        }
        
        return min(100, $score);
    }

    /**
     * Create a new AR view with simulated data.
     */
    public static function createArView(array $data): self
    {
        $arContent = [
            [
                'name' => '3D Property Model',
                'type' => 'model',
                'model_path' => 'ar-content/' . uniqid('model_') . '.obj',
                'texture_path' => 'ar-content/' . uniqid('texture_') . '.jpg',
                'position' => [0, 0, 0],
                'rotation' => [0, 0, 0],
                'scale' => [1, 1, 1],
                'is_interactive' => true,
                'interaction_type' => 'view',
            ]
        ];

        $trackingTargets = [
            [
                'name' => 'Property Marker',
                'type' => 'image',
                'target_image' => 'ar-markers/' . uniqid('marker_') . '.jpg',
                'tracking_data' => [],
                'confidence_threshold' => 0.85,
                'tracking_quality' => 'high',
            ]
        ];

        $interactionZones = [
            [
                'name' => 'Property Info Zone',
                'type' => 'information',
                'position' => [0, 0, 0],
                'size' => [2, 2, 2],
                'trigger_type' => 'tap',
                'action' => 'show_info',
            ]
        ];

        $arMetadata = [
            'tracking_accuracy' => 0.92,
            'rendering_quality' => 'high',
            'performance_mode' => 'balanced',
            'tracking_features' => ['plane_detection', 'object_recognition'],
            'interaction_features' => ['touch_gesture', 'voice_command'],
            'content_size' => 1024000, // 1MB
            'loading_time' => 2.5,
            'created_at' => now()->toDateTimeString(),
        ];

        return static::create([
            'property_id' => $data['property_id'],
            'user_id' => $data['user_id'] ?? auth()->id(),
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'view_mode' => $data['view_mode'] ?? 'markerless',
            'tracking_type' => $data['tracking_type'] ?? 'plane_detection',
            'interaction_type' => $data['interaction_type'] ?? 'touch_gesture',
            'ar_content' => $arContent,
            'tracking_targets' => $trackingTargets,
            'interaction_zones' => $interactionZones,
            'device_compatibility' => ['mobile', 'tablet', 'ar_glasses'],
            'quality_settings' => [
                'rendering_quality' => 'high',
                'tracking_quality' => 'high',
                'interaction_quality' => 'medium',
            ],
            'ar_metadata' => $arMetadata,
            'status' => 'active',
            'launch_count' => 0,
            'average_session_duration' => 0,
            'created_by' => $data['created_by'] ?? auth()->id(),
        ]);
    }

    /**
     * Update AR view statistics.
     */
    public function updateStatistics(array $stats): bool
    {
        $updateData = [];
        
        if (isset($stats['launch_count'])) {
            $updateData['launch_count'] = $this->launch_count + $stats['launch_count'];
        }
        
        if (isset($stats['average_session_duration'])) {
            $currentDuration = $this->average_session_duration;
            $newDuration = $stats['average_session_duration'];
            $updateData['average_session_duration'] = ($currentDuration + $newDuration) / 2;
        }
        
        if (!empty($updateData)) {
            return $this->update($updateData);
        }
        
        return true;
    }

    /**
     * Get AR view analytics summary.
     */
    public function getAnalyticsSummary(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'property_id' => $this->property_id,
            'launch_count' => $this->launch_count,
            'average_session_duration' => $this->average_session_duration,
            'tracking_accuracy' => $this->tracking_accuracy,
            'quality_score' => $this->quality_score,
            'content_count' => $this->content_count,
            'interaction_zones_count' => $this->interaction_zones_count,
            'view_mode' => $this->view_mode_label,
            'tracking_type' => $this->tracking_type_label,
            'interaction_type' => $this->interaction_type_label,
            'status' => $this->status_label,
            'has_marker_images' => $this->has_marker_images,
            'supported_devices' => $this->supported_devices,
            'is_recent' => $this->isRecent(),
        ];
    }

    /**
     * Get AR performance metrics.
     */
    public function getPerformanceMetrics(): array
    {
        return [
            'tracking_accuracy' => $this->ar_metadata['tracking_accuracy'] ?? 0.9,
            'rendering_fps' => $this->ar_metadata['rendering_fps'] ?? 60,
            'loading_time' => $this->ar_metadata['loading_time'] ?? 2.5,
            'content_size' => $this->ar_metadata['content_size'] ?? 0,
            'device_compatibility' => $this->device_compatibility ?? [],
            'quality_settings' => $this->quality_settings ?? [],
        ];
    }

    /**
     * Check if AR view needs optimization.
     */
    public function needsOptimization(): bool
    {
        return $this->quality_score < 70;
    }

    /**
     * Get recommended optimizations.
     */
    public function getRecommendedOptimizations(): array
    {
        $optimizations = [];
        
        if ($this->tracking_accuracy < 0.8) {
            $optimizations[] = 'Improve tracking accuracy with better markers or lighting';
        }
        
        if ($this->ar_metadata['loading_time'] > 5) {
            $optimizations[] = 'Optimize content size for faster loading';
        }
        
        if ($this->quality_settings['rendering_quality'] === 'low') {
            $optimizations[] = 'Upgrade rendering quality for better experience';
        }
        
        return $optimizations;
    }

    /**
     * Get device compatibility score.
     */
    public function getCompatibilityScore($deviceType): float
    {
        if (!$this->supportsDevice($deviceType)) {
            return 0;
        }
        
        $scores = [
            'mobile' => 0.8,
            'tablet' => 0.9,
            'ar_glasses' => 1.0,
        ];
        
        return $scores[$deviceType] ?? 0.5;
    }

    /**
     * Get engagement metrics.
     */
    public function getEngagementMetrics(): array
    {
        return [
            'average_session_duration' => $this->average_session_duration,
            'interaction_rate' => $this->calculateInteractionRate(),
            'completion_rate' => $this->calculateCompletionRate(),
            'return_visits' => $this->calculateReturnVisits(),
        ];
    }

    /**
     * Calculate interaction rate.
     */
    private function calculateInteractionRate(): float
    {
        // This would be calculated from actual interaction data
        return 0.75; // Placeholder
    }

    /**
     * Calculate completion rate.
     */
    private function calculateCompletionRate(): float
    {
        // This would be calculated from actual completion data
        return 0.68; // Placeholder
    }

    /**
     * Calculate return visits percentage.
     */
    private function calculateReturnVisits(): float
    {
        // This would be calculated from actual visit data
        return 0.25; // Placeholder
    }
}

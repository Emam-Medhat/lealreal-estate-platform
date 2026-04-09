<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class VrShowroom extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'user_id',
        'title',
        'description',
        'showroom_type',
        'environment_type',
        'lighting_mode',
        'interaction_mode',
        'max_capacity',
        'duration_minutes',
        'showroom_assets',
        'furniture_items',
        'decor_elements',
        'lighting_setup',
        'audio_settings',
        'showroom_metadata',
        'status',
        'is_featured',
        'visit_count',
        'average_visit_duration',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'showroom_assets' => 'array',
        'furniture_items' => 'array',
        'decor_elements' => 'array',
        'lighting_setup' => 'array',
        'audio_settings' => 'array',
        'showroom_metadata' => 'array',
        'is_featured' => 'boolean',
        'visit_count' => 'integer',
        'average_visit_duration' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the property that owns the VR showroom.
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * Get the user that created the VR showroom.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who created the VR showroom.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who updated the VR showroom.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the furniture items for the showroom.
     */
    public function furnitureItems(): HasMany
    {
        return $this->hasMany(VrShowroomFurniture::class);
    }

    /**
     * Get the decor elements for the showroom.
     */
    public function decorElements(): HasMany
    {
        return $this->hasMany(VrShowroomDecor::class);
    }

    /**
     * Get the lighting setup for the showroom.
     */
    public function lightingSetup(): HasMany
    {
        return $this->hasMany(VrShowroomLighting::class);
    }

    /**
     * Get the audio settings for the showroom.
     */
    public function audioSettings(): HasMany
    {
        return $this->hasMany(VrShowroomAudio::class);
    }

    /**
     * Scope a query to only include featured showrooms.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to only include recent showrooms.
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', Carbon::now()->subDays($days));
    }

    /**
     * Scope a query to only include showrooms with specific status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Get showroom type label in Arabic.
     */
    public function getShowroomTypeLabelAttribute(): string
    {
        $types = [
            'residential' => 'سكني',
            'commercial' => 'تجاري',
            'luxury' => 'فاخم',
            'show_home' => 'معرض نموذجي',
            'model_unit' => 'وحدة نموذجية',
        ];

        return $types[$this->showroom_type] ?? 'غير معروف';
    }

    /**
     * Get environment type label in Arabic.
     */
    public function getEnvironmentTypeLabelAttribute(): string
    {
        $types = [
            'modern' => 'حديث',
            'classic' => 'كلاسيكي',
            'minimalist' => 'بسيط',
            'industrial' => 'صناعي',
            'traditional' => 'تقليدي',
        ];

        return $types[$this->environment_type] ?? 'غير معروف';
    }

    /**
     * Get lighting mode label in Arabic.
     */
    public function getLightingModeLabelAttribute(): string
    {
        $modes = [
            'natural' => 'طبيعي',
            'artificial' => 'اصطناعي',
            'mixed' => 'مختلط',
            'dynamic' => 'ديناميكي',
        ];

        return $modes[$this->lighting_mode] ?? 'غير معروف';
    }

    /**
     * Get interaction mode label in Arabic.
     */
    public function getInteractionModeLabelAttribute(): string
    {
        $modes = [
            'free_roam' => 'تجول حر',
            'guided_tour' => 'جولة موجهة',
            'interactive_stations' => 'محطات تفاعلية',
            'presentation_mode' => 'وضع العرض',
        ];

        return $modes[$this->interaction_mode] ?? 'غير معروف';
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
            'archived' => 'مؤرشف',
        ];

        return $statuses[$this->status] ?? 'غير معروف';
    }

    /**
     * Check if showroom is recent (within last 30 days).
     */
    public function isRecent(): bool
    {
        return $this->created_at->diffInDays(Carbon::now()) <= 30;
    }

    /**
     * Get formatted duration.
     */
    public function getFormattedDurationAttribute(): string
    {
        $hours = floor($this->duration_minutes / 60);
        $minutes = $this->duration_minutes % 60;
        
        if ($hours > 0) {
            return $hours . ' ساعة و ' . $minutes . ' دقيقة';
        }
        
        return $minutes . ' دقيقة';
    }

    /**
     * Get formatted max capacity.
     */
    public function getFormattedMaxCapacityAttribute(): string
    {
        return $this->max_capacity . ' زائر';
    }

    /**
     * Get furniture items count.
     */
    public function getFurnitureCountAttribute(): int
    {
        return $this->furnitureItems()->count();
    }

    /**
     * Get decor elements count.
     */
    public function getDecorCountAttribute(): int
    {
        return $this->decorElements()->count();
    }

    /**
     * Get lighting setup count.
     */
    public function getLightingCountAttribute(): int
    {
        return $this->lightingSetup()->count();
    }

    /**
     * Get audio settings count.
     */
    public function getAudioCountAttribute(): int
    {
        return $this->audioSettings()->count();
    }

    /**
     * Check if showroom is luxury.
     */
    public function isLuxury(): bool
    {
        return $this->showroom_type === 'luxury';
    }

    /**
     * Check if showroom has audio.
     */
    public function hasAudio(): bool
    {
        return $this->audioSettings()->count() > 0;
    }

    /**
     * Check if showroom has dynamic lighting.
     */
    public function hasDynamicLighting(): bool
    {
        return $this->lighting_mode === 'dynamic';
    }

    /**
     * Get showroom thumbnail URL.
     */
    public function getThumbnailUrlAttribute(): ?string
    {
        if ($this->showroom_assets && !empty($this->showroom_assets)) {
            $firstAsset = $this->showroom_assets[0];
            return asset('storage/' . $firstAsset['path']);
        }
        
        return null;
    }

    /**
     * Get file size in human readable format.
     */
    public function getFileSizeAttribute(): string
    {
        $totalSize = 0;
        
        if ($this->showroom_assets) {
            foreach ($this->showroom_assets as $asset) {
                $totalSize += $asset['size'] ?? 0;
            }
        }
        
        return $this->formatBytes($totalSize);
    }

    /**
     * Format bytes to human readable format.
     */
    private function formatBytes($bytes, $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }

    /**
     * Create a new VR showroom with simulated data.
     */
    public static function createVrShowroom(array $data): self
    {
        $showroomAssets = [
            [
                'path' => 'vr-showrooms/' . uniqid('showroom_') . '.glb',
                'type' => 'model/gltf2',
                'size' => rand(100000000, 500000000), // 100-500MB
                'original_name' => 'showroom_' . uniqid() . '.glb',
            ]
        ];

        $furnitureItems = [
            [
                'name' => 'Modern Sofa',
                'type' => 'seating',
                'model_path' => 'furniture/' . uniqid('furniture_') . '.obj',
                'position' => [2.5, 0, 3.0],
                'rotation' => [0, 180, 0],
                'scale' => [1, 1, 1],
                'is_interactive' => true,
                'interaction_type' => 'none',
            ],
            [
                'name' => 'Coffee Table',
                'type' => 'table',
                'model_path' => 'furniture/' . uniqid('furniture_') . '.obj',
                'position' => [2.5, 0, 2.0],
                'rotation' => [0, 0, 0],
                'scale' => [1, 1, 1],
                'is_interactive' => false,
                'interaction_type' => 'none',
            ]
        ];

        $decorElements = [
            [
                'name' => 'Wall Art',
                'type' => 'decoration',
                'model_path' => 'decor/' . uniqid('decor_') . '.obj',
                'position' => [0, 2.5, 1.5],
                'rotation' => [0, 0, 0],
                'scale' => [1, 1, 1],
                'material' => 'canvas',
                'color' => '#FFFFFF',
                'is_animated' => false,
            ]
        ];

        $lightingSetup = [
            [
                'name' => 'Ceiling Light',
                'type' => 'overhead',
                'position' => [2.5, 3.0, 0],
                'intensity' => 0.8,
                'color' => '#FFFFFF',
                'range' => 5,
                'is_dynamic' => false,
            ],
            [
                'name' => 'Accent Light',
                'type' => 'spotlight',
                'position' => [1.0, 2.5, 2.0],
                'intensity' => 0.6,
                'color' => '#FFE4B5',
                'range' => 3,
                'is_dynamic' => true,
            ]
        ];

        $showroomMetadata = [
            'furniture_count' => count($furnitureItems),
            'decor_count' => count($decorElements),
            'lighting_count' => count($lightingSetup),
            'space_utilization' => 78.5,
            'ambiance_score' => 8.2,
            'comfort_rating' => 9.0,
            'rendering_quality' => 'high',
            'performance_mode' => 'balanced',
            'created_at' => now()->toDateTimeString(),
        ];

        return static::create([
            'property_id' => $data['property_id'],
            'user_id' => $data['user_id'] ?? auth()->id(),
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'showroom_type' => $data['showroom_type'] ?? 'residential',
            'environment_type' => $data['environment_type'] ?? 'modern',
            'lighting_mode' => $data['lighting_mode'] ?? 'natural',
            'interaction_mode' => $data['interaction_mode'] ?? 'free_roam',
            'max_capacity' => $data['max_capacity'] ?? 10,
            'duration_minutes' => $data['duration_minutes'] ?? 30,
            'showroom_assets' => $showroomAssets,
            'furniture_items' => $furnitureItems,
            'decor_elements' => $decorElements,
            'lighting_setup' => $lightingSetup,
            'audio_settings' => [],
            'showroom_metadata' => $showroomMetadata,
            'status' => 'active',
            'is_featured' => $data['is_featured'] ?? false,
            'visit_count' => 0,
            'average_visit_duration' => 0,
            'created_by' => $data['created_by'] ?? auth()->id(),
        ]);
    }

    /**
     * Update showroom statistics.
     */
    public function updateStatistics(array $stats): bool
    {
        $updateData = [];
        
        if (isset($stats['visit_count'])) {
            $updateData['visit_count'] = $this->visit_count + $stats['visit_count'];
        }
        
        if (isset($stats['average_visit_duration'])) {
            $currentDuration = $this->average_visit_duration;
            $newDuration = $stats['average_visit_duration'];
            $updateData['average_visit_duration'] = ($currentDuration + $newDuration) / 2;
        }
        
        if (!empty($updateData)) {
            return $this->update($updateData);
        }
        
        return true;
    }

    /**
     * Get showroom analytics summary.
     */
    public function getAnalyticsSummary(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'property_id' => $this->property_id,
            'visit_count' => $this->visit_count,
            'average_visit_duration' => $this->average_visit_duration,
            'furniture_count' => $this->furniture_count,
            'decor_count' => $this->decor_count,
            'lighting_count' => $this->lighting_count,
            'audio_count' => $this->audio_count,
            'max_capacity' => $this->formatted_max_capacity,
            'showroom_type' => $this->showroom_type_label,
            'environment_type' => $this->environment_type_label,
            'lighting_mode' => $this->lighting_mode_label,
            'interaction_mode' => $this->interaction_mode_label,
            'status' => $this->status_label,
            'is_featured' => $this->is_featured,
            'is_luxury' => $this->isLuxury(),
            'has_audio' => $this->hasAudio(),
            'has_dynamic_lighting' => $this->hasDynamicLighting(),
            'duration' => $this->formatted_duration,
            'file_size' => $this->file_size,
            'is_recent' => $this->isRecent(),
            'thumbnail_url' => $this->thumbnail_url,
        ];
    }

    /**
     * Get showroom performance metrics.
     */
    public function getPerformanceMetrics(): array
    {
        return [
            'rendering_fps' => $this->showroom_metadata['rendering_fps'] ?? 75,
            'load_time' => $this->showroom_metadata['load_time'] ?? 4.2,
            'interaction_rate' => $this->showroom_metadata['interaction_rate'] ?? 0.82,
            'user_satisfaction' => $this->showroom_metadata['user_satisfaction'] ?? 4.5,
            'space_utilization' => $this->showroom_metadata['space_utilization'] ?? 0,
            'ambiance_score' => $this->showroom_metadata['ambiance_score'] ?? 8.0,
            'comfort_rating' => $this->showroom_metadata['comfort_rating'] ?? 8.0,
        ];
    }

    /**
     * Check if showroom needs optimization.
     */
    public function needsOptimization(): bool
    {
        $metrics = $this->getPerformanceMetrics();
        
        return $metrics['rendering_fps'] < 60 || 
               $metrics['load_time'] > 10 || 
               $metrics['space_utilization'] < 60;
    }

    /**
     * Get recommended optimizations.
     */
    public function getRecommendedOptimizations(): array
    {
        $optimizations = [];
        $metrics = $this->getPerformanceMetrics();
        
        if ($metrics['rendering_fps'] < 60) {
            $optimizations[] = 'Optimize 3D models for better performance';
        }
        
        if ($metrics['load_time'] > 10) {
            $optimizations[] = 'Compress textures and reduce model complexity';
        }
        
        if ($metrics['space_utilization'] < 60) {
            $optimizations[] = 'Improve furniture arrangement for better space usage';
        }
        
        return $optimizations;
    }

    /**
     * Get visitor capacity utilization.
     */
    public function getCapacityUtilizationAttribute(): float
    {
        if ($this->max_capacity === 0) {
            return 0;
        }
        
        // This would be calculated from actual visitor data
        $currentVisitors = rand(1, min($this->max_capacity, 8));
        return ($currentVisitors / $this->max_capacity) * 100;
    }

    /**
     * Get engagement score.
     */
    public function getEngagementScoreAttribute(): float
    {
        $metrics = $this->getPerformanceMetrics();
        
        // Calculate engagement based on multiple factors
        $engagementScore = (
            ($metrics['interaction_rate'] * 0.4) +
            ($metrics['user_satisfaction'] * 0.3) +
            ($this->average_visit_duration / 30) * 0.3
        ) * 10; // Scale to 0-100
        
        return min(100, $engagementScore);
    }

    /**
     * Get popularity ranking.
     */
    public function getPopularityRankingAttribute(): int
    {
        // This would be calculated based on visit count and engagement
        return $this->visit_count + ($this->engagement_score * 10);
    }

    /**
     * Check if showroom is suitable for virtual events.
     */
    public function isSuitableForEvents(): bool
    {
        return $this->max_capacity >= 5 && 
               $this->hasAudio() && 
               $this->interaction_mode === 'free_roam';
    }

    /**
     * Get estimated bandwidth requirement.
     */
    public function getEstimatedBandwidthAttribute(): float
    {
        $baseBandwidth = 10; // 10 Mbps base
        
        if ($this->showroom_type === 'luxury') {
            $baseBandwidth *= 2;
        }
        
        if ($this->lighting_mode === 'dynamic') {
            $baseBandwidth *= 1.5;
        }
        
        return $baseBandwidth;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class ThreeDPropertyModel extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'user_id',
        'title',
        'description',
        'model_type',
        'quality_level',
        'file_formats',
        'texture_files',
        'model_files',
        'polygon_count',
        'vertex_count',
        'texture_count',
        'material_count',
        'animation_count',
        'file_size',
        'model_metadata',
        'processing_time',
        'status',
        'view_count',
        'download_count',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'file_formats' => 'array',
        'texture_files' => 'array',
        'model_files' => 'array',
        'polygon_count' => 'integer',
        'vertex_count' => 'integer',
        'texture_count' => 'integer',
        'material_count' => 'integer',
        'animation_count' => 'integer',
        'file_size' => 'integer',
        'model_metadata' => 'array',
        'processing_time' => 'decimal:3',
        'view_count' => 'integer',
        'download_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the property that owns the 3D model.
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * Get the user that created the 3D model.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who created the 3D model.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who updated the 3D model.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the components for the 3D model.
     */
    public function components(): HasMany
    {
        return $this->hasMany(ThreeDModelComponent::class);
    }

    /**
     * Get the materials for the 3D model.
     */
    public function materials(): HasMany
    {
        return $this->hasMany(ThreeDModelMaterial::class);
    }

    /**
     * Get the animations for the 3D model.
     */
    public function animations(): HasMany
    {
        return $this->hasMany(ThreeDModelAnimation::class);
    }

    /**
     * Scope a query to only include models with high quality.
     */
    public function scopeHighQuality($query)
    {
        return $query->whereIn('quality_level', ['high', 'ultra']);
    }

    /**
     * Scope a query to only include recent models.
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', Carbon::now()->subDays($days));
    }

    /**
     * Scope a query to only include models with specific status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Get model type label in Arabic.
     */
    public function getModelTypeLabelAttribute(): string
    {
        $types = [
            'exterior' => 'خارجي',
            'interior' => 'داخلي',
            'furniture' => 'أثاثاث',
            'landscape' => 'مناظر طبيعي',
            'complete' => 'كامل',
        ];

        return $types[$this->model_type] ?? 'غير معروف';
    }

    /**
     * Get quality level label in Arabic.
     */
    public function getQualityLevelLabelAttribute(): string
    {
        $levels = [
            'low' => 'منخفض',
            'medium' => 'متوسط',
            'high' => 'عالي',
            'ultra' => 'فائق الجودة',
        ];

        return $levels[$this->quality_level] ?? 'غير معروف';
    }

    /**
     * Get status label in Arabic.
     */
    public function getStatusLabelAttribute(): string
    {
        $statuses = [
            'processing' => 'قيد المعالجة',
            'completed' => 'مكتمل',
            'failed' => 'فشل',
            'archived' => 'مؤرشف',
        ];

        return $statuses[$this->status] ?? 'غير معروف';
    }

    /**
     * Check if model is recent (within last 30 days).
     */
    public function isRecent(): bool
    {
        return $this->created_at->diffInDays(Carbon::now()) <= 30;
    }

    /**
     * Get formatted file size.
     */
    public function getFormattedFileSizeAttribute(): string
    {
        return $this->formatBytes($this->file_size);
    }

    /**
     * Get formatted polygon count.
     */
    public function getFormattedPolygonCountAttribute(): string
    {
        return number_format($this->polygon_count) . ' مضلع';
    }

    /**
     * Get formatted vertex count.
     */
    public function getFormattedVertexCountAttribute(): string
    {
        return number_format($this->vertex_count) . ' رأس';
    }

    /**
     * Get model complexity level.
     */
    public function getComplexityLevelAttribute(): string
    {
        $polygons = $this->polygon_count;
        
        if ($polygons < 1000) {
            return 'بسيط';
        } elseif ($polygons < 10000) {
            return 'متوسط';
        } elseif ($polygons < 100000) {
            return 'معقد';
        } else {
            return 'معقد جداً';
        }
    }

    /**
     * Check if model is optimized.
     */
    public function isOptimized(): bool
    {
        return $this->model_metadata['is_optimized'] ?? false;
    }

    /**
     * Check if model has animations.
     */
    public function hasAnimations(): bool
    {
        return $this->animation_count > 0;
    }

    /**
     * Check if model has materials.
     */
    public function hasMaterials(): bool
    {
        return $this->material_count > 0;
    }

    /**
     * Get supported file formats.
     */
    public function getSupportedFormatsAttribute(): array
    {
        return $this->file_formats ?? ['obj', 'fbx', 'gltf'];
    }

    /**
     * Get rendering requirements.
     */
    public function getRenderingRequirementsAttribute(): array
    {
        return $this->model_metadata['rendering_requirements'] ?? [];
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
     * Create a new 3D model with simulated data.
     */
    public static function create3DModel(array $data): self
    {
        $modelFiles = [
            [
                'path' => '3d-models/' . uniqid('model_') . '.obj',
                'format' => 'obj',
                'size' => rand(1000000, 10000000), // 1-10MB
                'original_name' => 'property_model_' . uniqid() . '.obj',
            ]
        ];

        $textureFiles = [
            [
                'path' => '3d-textures/' . uniqid('texture_') . '.jpg',
                'format' => 'jpg',
                'size' => rand(500000, 2000000), // 0.5-2MB
                'original_name' => 'texture_' . uniqid() . '.jpg',
                'resolution' => [rand(1024, 4096), rand(1024, 4096)],
            ]
        ];

        $modelMetadata = [
            'rendering_engine' => 'three.js',
            'optimization_level' => 'medium',
            'lod_levels' => [3, 2, 1],
            'bounding_box' => [
                'min' => [-5, 0, -5],
                'max' => [5, 3, 5],
            ],
            'rendering_requirements' => [
                'minimum_gpu' => 'GTX 1060',
                'minimum_ram' => 8,
                'recommended_cpu' => 'i5-8400',
            ],
            'is_optimized' => false,
            'created_at' => now()->toDateTimeString(),
        ];

        return static::create([
            'property_id' => $data['property_id'],
            'user_id' => $data['user_id'] ?? auth()->id(),
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'model_type' => $data['model_type'] ?? 'complete',
            'quality_level' => $data['quality_level'] ?? 'high',
            'file_formats' => ['obj', 'fbx', 'gltf'],
            'texture_files' => $textureFiles,
            'model_files' => $modelFiles,
            'polygon_count' => rand(1000, 50000),
            'vertex_count' => rand(5000, 100000),
            'texture_count' => rand(5, 20),
            'material_count' => rand(3, 15),
            'animation_count' => rand(0, 5),
            'file_size' => array_sum(array_column($modelFiles, 'size')),
            'model_metadata' => $modelMetadata,
            'processing_time' => rand(1.5, 8.5),
            'status' => 'completed',
            'view_count' => 0,
            'download_count' => 0,
            'created_by' => $data['created_by'] ?? auth()->id(),
        ]);
    }

    /**
     * Update 3D model statistics.
     */
    public function updateStatistics(array $stats): bool
    {
        $updateData = [];
        
        if (isset($stats['view_count'])) {
            $updateData['view_count'] = $this->view_count + $stats['view_count'];
        }
        
        if (isset($stats['download_count'])) {
            $updateData['download_count'] = $this->download_count + $stats['download_count'];
        }
        
        if (!empty($updateData)) {
            return $this->update($updateData);
        }
        
        return true;
    }

    /**
     * Get 3D model analytics summary.
     */
    public function getAnalyticsSummary(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'property_id' => $this->property_id,
            'view_count' => $this->view_count,
            'download_count' => $download_count,
            'polygon_count' => $this->formatted_polygon_count,
            'vertex_count' => $this->formatted_vertex_count,
            'texture_count' => $this->texture_count,
            'material_count' => $this->material_count,
            'animation_count' => $this->animation_count,
            'file_size' => $this->formatted_file_size,
            'model_type' => $this->model_type_label,
            'quality_level' => $this->quality_level_label,
            'status' => $this->status_label,
            'complexity_level' => $this->complexity_level,
            'is_optimized' => $this->isOptimized(),
            'has_animations' => $this->hasAnimations(),
            'has_materials' => $this->hasMaterials(),
            'supported_formats' => $this->supported_formats,
            'is_recent' => $this->isRecent(),
            'rendering_requirements' => $this->rendering_requirements,
        ];
    }

    /**
     * Get 3D model performance metrics.
     */
    public function getPerformanceMetrics(): array
    {
        return [
            'rendering_fps' => $this->model_metadata['rendering_fps'] ?? 60,
            'load_time' => $this->processing_time,
            'memory_usage' => $this->model_metadata['memory_usage'] ?? 0,
            'gpu_utilization' => $this->model_metadata['gpu_utilization'] ?? 0,
            'optimization_rate' => $this->calculateOptimizationRate(),
            'quality_score' => $this->calculateQualityScore(),
        ];
    }

    /**
     * Check if 3D model needs optimization.
     */
    public function needsOptimization(): bool
    {
        $metrics = $this->getPerformanceMetrics();
        
        return $metrics['rendering_fps'] < 30 || 
               $metrics['load_time'] > 10 || 
               !$this->isOptimized();
    }

    /**
     * Get recommended optimizations.
     */
    public function getRecommendedOptimizations(): array
    {
        $optimizations = [];
        $metrics = $this->getPerformanceMetrics();
        
        if ($metrics['rendering_fps'] < 30) {
            $optimizations[] = 'Reduce polygon count or use LOD levels';
        }
        
        if ($metrics['load_time'] > 10) {
            $optimizations[] = 'Compress textures and optimize geometry';
        }
        
        if (!$this->isOptimized()) {
            $optimizations[] = 'Apply mesh optimization and compression';
        }
        
        return $optimizations;
    }

    /**
     * Calculate optimization rate.
     */
    private function calculateOptimizationRate(): float
    {
        $originalSize = $this->model_metadata['original_size'] ?? $this->file_size;
        $optimizedSize = $this->file_size;
        
        if ($originalSize === 0) {
            return 0;
        }
        
        return (($originalSize - $optimizedSize) / $originalSize) * 100;
    }

    /**
     * Calculate quality score.
     */
    private function calculateQualityScore(): float
    {
        $score = 0;
        
        // Quality level contribution
        $qualityScores = [
            'low' => 25,
            'medium' => 50,
            'high' => 75,
            'ultra' => 100,
        ];
        
        $score += $qualityScores[$this->quality_level] ?? 50;
        
        // Polygon count contribution (inverse - fewer polygons is better for performance)
        $polygonScore = max(0, 100 - ($this->polygon_count / 1000));
        $score += $polygonScore * 0.3;
        
        // Texture resolution contribution
        if ($this->texture_count > 0) {
            $avgTextureSize = $this->model_metadata['average_texture_size'] ?? 2048;
            if ($avgTextureSize >= 4096) {
                $score += 10;
            } elseif ($avgTextureSize >= 2048) {
                $score += 5;
            }
        }
        
        return min(100, $score);
    }

    /**
     * Get model download statistics.
     */
    public function getDownloadStats(): array
    {
        return [
            'total_downloads' => $this->download_count,
            'recent_downloads' => $this->getRecentDownloads(),
            'popular_formats' => $this->getPopularFormats(),
            'download_trends' => $this->getDownloadTrends(),
        ];
    }

    /**
     * Get recent downloads.
     */
    private function getRecentDownloads(): array
    {
        // This would be calculated from actual download data
        return [
            'last_24h' => rand(5, 20),
            'last_7d' => rand(20, 50),
            'last_30d' => rand(50, 100),
        ];
    }

    /**
     * Get popular formats.
     */
    private function getPopularFormats(): array
    {
        $formats = $this->file_formats;
        $counts = [];
        
        foreach ($formats as $format) {
            $counts[$format] = rand(10, 100);
        }
        
        return $counts;
    }

    /**
     * Get download trends.
     */
    private function getDownloadTrends(): array
    {
        return [
            'daily' => $this->getDailyDownloads(),
            'weekly' => $this->getWeeklyDownloads(),
            'monthly' => $this->getMonthlyDownloads(),
        ];
    }

    /**
     * Get daily downloads.
     */
    private function getDailyDownloads(): array
    {
        $trends = [];
        for ($i = 0; $i < 7; $i++) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $trends[$date] = rand(5, 25);
        }
        return $trends;
    }

    /**
     * Get weekly downloads.
     */
    private function getWeeklyDownloads(): array
    {
        $trends = [];
        for ($i = 0; $i < 4; $i++) {
            $date = Carbon::now()->subWeeks($i)->format('Y-W');
            $trends[$date] = rand(20, 100);
        }
        return $trends;
    }

    /**
     * Get monthly downloads.
     */
    private function getMonthlyDownloads(): array
    {
        $trends = [];
        for ($i = 0; $i < 6; $i++) {
            $date = Carbon::now()->subMonths($i)->format('Y-m');
            $trends[$date] = rand(50, 200);
        }
        return $trends;
    }

    /**
     * Check if model is suitable for VR.
     */
    public function isSuitableForVR(): bool
    {
        return $this->quality_level === 'ultra' && 
               $this->polygon_count < 100000 && 
               $this->hasMaterials();
    }

    /**
     * Check if model is mobile-friendly.
     */
    public function isMobileFriendly(): bool
    {
        return $this->file_size < 5000000 && // Less than 5MB
               $this->polygon_count < 10000 &&
               $this->quality_level !== 'ultra';
    }

    /**
     * Get estimated VR performance score.
     */
    public function getVrPerformanceScoreAttribute(): float
    {
        $score = 0;
        
        // Quality level contribution
        if ($this->quality_level === 'ultra') {
            $score += 40;
        } elseif ($this->quality_level === 'high') {
            $score += 30;
        } else {
            $score += 20;
        }
        
        // Polygon count contribution (inverse)
        if ($this->polygon_count < 50000) {
            $score += 30;
        } elseif ($this->polygon_count < 100000) {
            $score += 20;
        } else {
            $score += 10;
        }
        
        // Material complexity contribution
        if ($this->material_count < 10) {
            $score += 20;
        } elseif ($this->material_count < 20) {
            $score += 15;
        } else {
            $score += 10;
        }
        
        return min(100, $score);
    }

    /**
     * Get estimated memory usage.
     */
    public function getEstimatedMemoryUsageAttribute(): int
    {
        // Estimate memory usage based on model complexity
        $baseMemory = 50; // 50MB base
        
        $memoryPerPolygon = 0.001; // 1KB per polygon
        $memoryPerVertex = 0.0001; // 0.1KB per vertex
        $memoryPerTexture = 2; // 2MB per texture
        
        $polygonMemory = $this->polygon_count * $memoryPerPolygon;
        $vertexMemory = $this->vertex_count * $memoryPerVertex;
        $textureMemory = $this->texture_count * $memoryPerTexture;
        
        return (int)($baseMemory + $polygonMemory + $vertexMemory + $textureMemory);
    }

    /**
     * Get LOD levels configuration.
     */
    public function getLodLevelsAttribute(): array
    {
        return $this->model_metadata['lod_levels'] ?? [3, 2, 1];
    }

    /**
     * Get model bounding box center.
     */
    public function getBoundingBoxCenterAttribute(): array
    {
        $boundingBox = $this->model_metadata['bounding_box'] ?? [];
        
        if (isset($boundingBox['min']) && isset($boundingBox['max'])) {
            return [
                'x' => ($boundingBox['min'][0] + $boundingBox['max'][0]) / 2,
                'y' => ($boundingBox['min'][1] + $boundingBox['max'][1]) / 2,
                'z' => ($boundingBox['min'][2] + $boundingBox['max'][2]) / 2,
            ];
        }
        
        return [0, 0, 0];
    }

    /**
     * Get model dimensions.
     */
    public function getDimensionsAttribute(): array
    {
        $boundingBox = $this->model_metadata['bounding_box'] ?? [];
        
        if (isset($boundingBox['min']) && isset($boundingBox['max'])) {
            return [
                'width' => $boundingBox['max'][0] - $boundingBox['min'][0],
                'height' => $boundingBox['max'][1] - $boundingBox['min'][1],
                'depth' => $boundingBox['max'][2] - $boundingBox['min'][2],
            ];
        }
        
        return [0, 0, 0];
    }

    /**
     * Get model volume.
     */
    public function getVolumeAttribute(): float
    {
        $dimensions = $this->dimensions;
        
        return $dimensions['width'] * $dimensions['height'] * $dimensions['depth'];
    }
}

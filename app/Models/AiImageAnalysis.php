<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class AiImageAnalysis extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'user_id',
        'image_path',
        'image_hash',
        'analysis_type',
        'detected_objects',
        'room_types',
        'quality_score',
        'aesthetic_score',
        'lighting_analysis',
        'color_analysis',
        'composition_analysis',
        'clutter_analysis',
        'renovation_suggestions',
        'staging_recommendations',
        'image_enhancements',
        'ai_model_version',
        'analysis_metadata',
        'processing_time',
        'confidence_level',
        'status',
        'enhanced_image_path',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'detected_objects' => 'array',
        'room_types' => 'array',
        'quality_score' => 'decimal:2',
        'aesthetic_score' => 'decimal:2',
        'lighting_analysis' => 'array',
        'color_analysis' => 'array',
        'composition_analysis' => 'array',
        'clutter_analysis' => 'array',
        'renovation_suggestions' => 'array',
        'staging_recommendations' => 'array',
        'image_enhancements' => 'array',
        'analysis_metadata' => 'array',
        'processing_time' => 'decimal:3',
        'confidence_level' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the property that owns the image analysis.
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * Get the user that requested the analysis.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who created the analysis.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who updated the analysis.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope a query to only include high-quality images.
     */
    public function scopeHighQuality($query, $threshold = 7.0)
    {
        return $query->where('quality_score', '>=', $threshold);
    }

    /**
     * Scope a query to only include analyses by type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('analysis_type', $type);
    }

    /**
     * Scope a query to only include recent analyses.
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', Carbon::now()->subDays($days));
    }

    /**
     * Get analysis type label in Arabic.
     */
    public function getAnalysisTypeLabelAttribute(): string
    {
        $types = [
            'room_detection' => 'كشف الغرف',
            'object_recognition' => 'التعرف على الكائنات',
            'quality_assessment' => 'تقييم الجودة',
            'aesthetic_analysis' => 'التحليل الجمالي',
            'renovation_analysis' => 'تحليل التجديد',
            'staging_analysis' => 'تحليل التنسيق',
            'comprehensive' => 'تحليل شامل',
        ];

        return $types[$this->analysis_type] ?? 'غير معروف';
    }

    /**
     * Get status label in Arabic.
     */
    public function getStatusLabelAttribute(): string
    {
        $statuses = [
            'pending' => 'قيد الانتظار',
            'processing' => 'قيد المعالجة',
            'completed' => 'مكتمل',
            'failed' => 'فشل',
            'enhanced' => 'تم التحسين',
        ];

        return $statuses[$this->status] ?? 'غير معروف';
    }

    /**
     * Get quality level text.
     */
    public function getQualityLevelAttribute(): string
    {
        if ($this->quality_score >= 9.0) return 'ممتاز';
        if ($this->quality_score >= 8.0) return 'جيد جداً';
        if ($this->quality_score >= 7.0) return 'جيد';
        if ($this->quality_score >= 6.0) return 'مقبول';
        return 'ضعيف';
    }

    /**
     * Get aesthetic level text.
     */
    public function getAestheticLevelAttribute(): string
    {
        if ($this->aesthetic_score >= 9.0) return 'ممتاز';
        if ($this->aesthetic_score >= 8.0) return 'جيد جداً';
        if ($this->aesthetic_score >= 7.0) return 'جيد';
        if ($this->aesthetic_score >= 6.0) return 'مقبول';
        return 'ضعيف';
    }

    /**
     * Get confidence level text.
     */
    public function getConfidenceLevelTextAttribute(): string
    {
        if ($this->confidence_level >= 0.9) return 'عالي جداً';
        if ($this->confidence_level >= 0.8) return 'عالي';
        if ($this->confidence_level >= 0.7) return 'متوسط';
        if ($this->confidence_level >= 0.6) return 'منخفض';
        return 'منخفض جداً';
    }

    /**
     * Get detected room types as string.
     */
    public function getDetectedRoomsAttribute(): string
    {
        $rooms = $this->room_types ?? [];
        return implode(', ', array_column($rooms, 'type'));
    }

    /**
     * Get primary room type.
     */
    public function getPrimaryRoomTypeAttribute(): ?string
    {
        $rooms = $this->room_types ?? [];
        
        if (empty($rooms)) {
            return null;
        }
        
        // Find room with highest confidence
        $primaryRoom = collect($rooms)->sortByDesc('confidence')->first();
        return $primaryRoom['type'] ?? null;
    }

    /**
     * Get lighting quality text.
     */
    public function getLightingQualityAttribute(): string
    {
        $lighting = $this->lighting_analysis ?? [];
        
        if (isset($lighting['quality_score'])) {
            $score = $lighting['quality_score'];
            
            if ($score >= 0.8) return 'ممتاز';
            if ($score >= 0.6) return 'جيد';
            if ($score >= 0.4) return 'مقبول';
            return 'ضعيف';
        }
        
        return 'غير محدد';
    }

    /**
     * Get clutter level text.
     */
    public function getClutterLevelAttribute(): string
    {
        $clutter = $this->clutter_analysis ?? [];
        
        if (isset($clutter['clutter_score'])) {
            $score = $clutter['clutter_score'];
            
            if ($score >= 0.8) return 'مرتفع جداً';
            if ($score >= 0.6) return 'مرتفع';
            if ($score >= 0.4) return 'متوسط';
            return 'منخفض';
        }
        
        return 'غير محدد';
    }

    /**
     * Get dominant colors.
     */
    public function getDominantColorsAttribute(): array
    {
        $colors = $this->color_analysis ?? [];
        return $colors['dominant_colors'] ?? [];
    }

    /**
     * Get color scheme.
     */
    public function getColorSchemeAttribute(): string
    {
        $colors = $this->color_analysis ?? [];
        return $colors['color_scheme'] ?? 'غير محدد';
    }

    /**
     * Check if image needs enhancement.
     */
    public function needsEnhancement(): bool
    {
        return $this->quality_score < 7.0 || 
               $this->aesthetic_score < 7.0 ||
               ($this->clutter_analysis['clutter_score'] ?? 0) > 0.6;
    }

    /**
     * Check if image is professional quality.
     */
    public function isProfessionalQuality(): bool
    {
        return $this->quality_score >= 8.0 && 
               $this->aesthetic_score >= 8.0 &&
               ($this->clutter_analysis['clutter_score'] ?? 1) <= 0.3;
    }

    /**
     * Get improvement suggestions.
     */
    public function getImprovementSuggestionsAttribute(): array
    {
        $suggestions = [];
        
        if ($this->quality_score < 7.0) {
            $suggestions[] = 'تحسين جودة الصورة ودقتها';
        }
        
        if ($this->aesthetic_score < 7.0) {
            $suggestions[] = 'تحسين التكوين والإضاءة';
        }
        
        $lighting = $this->lighting_analysis ?? [];
        if (($lighting['quality_score'] ?? 1) < 0.6) {
            $suggestions[] = 'تحسين الإضاءة الطبيعية أو الاصطناعية';
        }
        
        $clutter = $this->clutter_analysis ?? [];
        if (($clutter['clutter_score'] ?? 0) > 0.6) {
            $suggestions[] = 'تقليل الفوضى وتنظيم المساحة';
        }
        
        $composition = $this->composition_analysis ?? [];
        if (($composition['balance_score'] ?? 1) < 0.7) {
            $suggestions[] = 'تحسين توازن وتكوين الصورة';
        }
        
        return $suggestions;
    }

    /**
     * Get staging priority.
     */
    public function getStagingPriorityAttribute(): string
    {
        $score = ($this->quality_score + $this->aesthetic_score) / 2;
        
        if ($score >= 8.5) return 'منخفضة';
        if ($score >= 7.0) return 'متوسطة';
        return 'مرتفعة';
    }

    /**
     * Get estimated renovation cost.
     */
    public function getEstimatedRenovationCostAttribute(): array
    {
        $suggestions = $this->renovation_suggestions ?? [];
        $totalCost = 0;
        $breakdown = [];
        
        foreach ($suggestions as $suggestion) {
            $cost = $suggestion['estimated_cost'] ?? 0;
            $totalCost += $cost;
            $breakdown[] = [
                'item' => $suggestion['type'] ?? 'غير محدد',
                'cost' => $cost,
                'priority' => $suggestion['priority'] ?? 'medium',
            ];
        }
        
        return [
            'total_cost' => $totalCost,
            'breakdown' => $breakdown,
            'currency' => 'ريال سعودي',
        ];
    }

    /**
     * Create a new AI image analysis.
     */
    public static function analyzeImage(array $data): self
    {
        // Simulate AI image analysis
        $analysisType = $data['analysis_type'] ?? 'comprehensive';
        
        // Generate detected objects
        $detectedObjects = [
            ['object' => 'sofa', 'confidence' => rand(70, 95) / 100, 'position' => ['x' => rand(10, 80), 'y' => rand(10, 80)]],
            ['object' => 'table', 'confidence' => rand(60, 90) / 100, 'position' => ['x' => rand(10, 80), 'y' => rand(10, 80)]],
            ['object' => 'window', 'confidence' => rand(80, 98) / 100, 'position' => ['x' => rand(10, 80), 'y' => rand(10, 80)]],
            ['object' => 'lamp', 'confidence' => rand(50, 85) / 100, 'position' => ['x' => rand(10, 80), 'y' => rand(10, 80)]],
        ];
        
        // Generate room types
        $roomTypes = [
            ['type' => 'living_room', 'confidence' => rand(75, 95) / 100],
            ['type' => 'bedroom', 'confidence' => rand(60, 85) / 100],
            ['type' => 'kitchen', 'confidence' => rand(50, 80) / 100],
        ];
        
        // Generate quality and aesthetic scores
        $qualityScore = rand(5.5, 9.5);
        $aestheticScore = rand(6.0, 9.0);
        $confidenceLevel = rand(75, 95) / 100;
        
        // Generate lighting analysis
        $lightingAnalysis = [
            'brightness_level' => rand(40, 90) / 100,
            'natural_light_ratio' => rand(30, 80) / 100,
            'light_distribution' => rand(50, 90) / 100,
            'quality_score' => rand(50, 95) / 100,
            'light_sources' => ['natural', 'artificial'],
        ];
        
        // Generate color analysis
        $colorAnalysis = [
            'dominant_colors' => [
                ['color' => '#FFFFFF', 'percentage' => rand(20, 40)],
                ['color' => '#F5F5DC', 'percentage' => rand(15, 30)],
                ['color' => '#8B4513', 'percentage' => rand(10, 25)],
            ],
            'color_scheme' => ['warm', 'neutral', 'cool'][array_rand(['warm', 'neutral', 'cool'])],
            'contrast_level' => rand(50, 90) / 100,
            'harmony_score' => rand(60, 95) / 100,
        ];
        
        // Generate composition analysis
        $compositionAnalysis = [
            'balance_score' => rand(60, 95) / 100,
            'rule_of_thirds_score' => rand(50, 90) / 100,
            'symmetry_score' => rand(40, 85) / 100,
            'focal_point_strength' => rand(60, 95) / 100,
            'depth_perception' => rand(50, 85) / 100,
        ];
        
        // Generate clutter analysis
        $clutterAnalysis = [
            'clutter_score' => rand(20, 80) / 100,
            'clutter_areas' => [
                ['area' => 'floor', 'level' => rand(20, 70) / 100],
                ['area' => 'surfaces', 'level' => rand(15, 60) / 100],
                ['area' => 'walls', 'level' => rand(10, 40) / 100],
            ],
            'organization_score' => rand(40, 90) / 100,
        ];
        
        // Generate renovation suggestions
        $renovationSuggestions = [
            [
                'type' => 'painting',
                'description' => 'إعادة طلاء الجدران بألوان محايدة',
                'priority' => 'medium',
                'estimated_cost' => rand(2000, 8000),
                'impact_score' => rand(60, 85) / 100,
            ],
            [
                'type' => 'lighting',
                'description' => 'تحسين نظام الإضاءة',
                'priority' => 'high',
                'estimated_cost' => rand(1000, 5000),
                'impact_score' => rand(70, 90) / 100,
            ],
        ];
        
        // Generate staging recommendations
        $stagingRecommendations = [
            [
                'area' => 'living_room',
                'recommendation' => 'إضافة وسائد وأغطية أريكة عصرية',
                'priority' => 'medium',
                'cost_estimate' => rand(500, 2000),
            ],
            [
                'area' => 'bedroom',
                'recommendation' => 'تنظيم وتقليل الأثاث الزائد',
                'priority' => 'low',
                'cost_estimate' => rand(200, 1000),
            ],
        ];
        
        // Generate image enhancements
        $imageEnhancements = [
            'brightness_adjustment' => rand(-20, 20) / 100,
            'contrast_enhancement' => rand(10, 30) / 100,
            'color_correction' => true,
            'noise_reduction' => true,
            'sharpening' => rand(5, 25) / 100,
        ];

        return static::create([
            'property_id' => $data['property_id'],
            'user_id' => $data['user_id'] ?? auth()->id(),
            'image_path' => $data['image_path'],
            'image_hash' => md5($data['image_path']),
            'analysis_type' => $analysisType,
            'detected_objects' => $detectedObjects,
            'room_types' => $roomTypes,
            'quality_score' => round($qualityScore, 2),
            'aesthetic_score' => round($aestheticScore, 2),
            'lighting_analysis' => $lightingAnalysis,
            'color_analysis' => $colorAnalysis,
            'composition_analysis' => $compositionAnalysis,
            'clutter_analysis' => $clutterAnalysis,
            'renovation_suggestions' => $renovationSuggestions,
            'staging_recommendations' => $stagingRecommendations,
            'image_enhancements' => $imageEnhancements,
            'ai_model_version' => '6.2.4',
            'analysis_metadata' => [
                'processing_time' => rand(0.5, 2.8) . 's',
                'image_resolution' => '1920x1080',
                'file_size' => rand(500, 2000) . 'KB',
                'analysis_date' => now()->toDateTimeString(),
                'model_confidence' => $confidenceLevel,
            ],
            'processing_time' => rand(0.5, 2.8),
            'confidence_level' => round($confidenceLevel, 2),
            'status' => 'completed',
            'enhanced_image_path' => null, // Will be set if enhancement is applied
            'created_by' => $data['created_by'] ?? auth()->id(),
        ]);
    }

    /**
     * Apply image enhancements.
     */
    public function applyEnhancements(): bool
    {
        if (!$this->needsEnhancement()) {
            return false;
        }
        
        // Simulate enhancement process
        $enhancedPath = str_replace('.', '_enhanced.', $this->image_path);
        
        $this->enhanced_image_path = $enhancedPath;
        $this->status = 'enhanced';
        
        // Update scores after enhancement
        $this->quality_score = min(10.0, $this->quality_score + rand(0.5, 2.0));
        $this->aesthetic_score = min(10.0, $this->aesthetic_score + rand(0.5, 1.5));
        
        return $this->save();
    }

    /**
     * Get analysis summary for dashboard.
     */
    public function getDashboardSummary(): array
    {
        return [
            'id' => $this->id,
            'property_id' => $this->property_id,
            'image_path' => basename($this->image_path),
            'analysis_type' => $this->analysis_type_label,
            'quality_score' => $this->quality_score,
            'quality_level' => $this->quality_level,
            'aesthetic_score' => $this->aesthetic_score,
            'primary_room' => $this->primary_room_type,
            'confidence_level' => $this->confidence_level_text,
            'needs_enhancement' => $this->needsEnhancement(),
            'staging_priority' => $this->staging_priority,
            'status' => $this->status_label,
            'created_at' => $this->created_at->format('Y-m-d H:i'),
        ];
    }

    /**
     * Get detailed analysis report.
     */
    public function getDetailedReport(): array
    {
        return [
            'basic_info' => [
                'property_id' => $this->property_id,
                'image_path' => $this->image_path,
                'analysis_type' => $this->analysis_type_label,
                'processing_time' => $this->processing_time . 's',
                'confidence_level' => $this->confidence_level_text,
            ],
            'quality_assessment' => [
                'quality_score' => $this->quality_score,
                'quality_level' => $this->quality_level,
                'aesthetic_score' => $this->aesthetic_score,
                'aesthetic_level' => $this->aesthetic_level,
                'is_professional' => $this->isProfessionalQuality(),
            ],
            'room_detection' => [
                'detected_rooms' => $this->detected_rooms,
                'primary_room' => $this->primary_room_type,
                'room_types' => $this->room_types,
            ],
            'visual_analysis' => [
                'detected_objects' => $this->detected_objects,
                'lighting_analysis' => $this->lighting_analysis,
                'color_analysis' => $this->color_analysis,
                'composition_analysis' => $this->composition_analysis,
            ],
            'condition_analysis' => [
                'clutter_analysis' => $this->clutter_analysis,
                'clutter_level' => $this->clutter_level,
                'lighting_quality' => $this->lighting_quality,
            ],
            'recommendations' => [
                'improvement_suggestions' => $this->improvement_suggestions,
                'renovation_suggestions' => $this->renovation_suggestions,
                'staging_recommendations' => $this->staging_recommendations,
                'estimated_renovation_cost' => $this->estimated_renovation_cost,
            ],
            'enhancement' => [
                'needs_enhancement' => $this->needsEnhancement(),
                'enhancements_available' => $this->image_enhancements,
                'enhanced_image_path' => $this->enhanced_image_path,
            ],
        ];
    }
}

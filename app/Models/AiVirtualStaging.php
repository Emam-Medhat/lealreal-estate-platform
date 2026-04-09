<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class AiVirtualStaging extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'user_id',
        'original_image_path',
        'staged_image_path',
        'room_type',
        'staging_style',
        'furniture_style',
        'color_scheme',
        'target_audience',
        'quality_score',
        'realism_score',
        'aesthetic_appeal',
        'furniture_items',
        'decor_elements',
        'lighting_setup',
        'spatial_arrangement',
        'style_consistency',
        'market_appeal',
        'ai_model_version',
        'staging_metadata',
        'processing_time',
        'confidence_level',
        'status',
        'is_published',
        'published_at',
        'view_count',
        'engagement_score',
        'feedback_rating',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'furniture_items' => 'array',
        'decor_elements' => 'array',
        'lighting_setup' => 'array',
        'spatial_arrangement' => 'array',
        'staging_metadata' => 'array',
        'processing_time' => 'decimal:3',
        'confidence_level' => 'decimal:2',
        'quality_score' => 'decimal:2',
        'realism_score' => 'decimal:2',
        'aesthetic_appeal' => 'decimal:2',
        'style_consistency' => 'decimal:2',
        'market_appeal' => 'decimal:2',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'view_count' => 'integer',
        'engagement_score' => 'decimal:2',
        'feedback_rating' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the property that owns the virtual staging.
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * Get the user that requested the staging.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who created the staging.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who updated the staging.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope a query to only include published stagings.
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope a query to only include high-quality stagings.
     */
    public function scopeHighQuality($query, $threshold = 8.0)
    {
        return $query->where('quality_score', '>=', $threshold);
    }

    /**
     * Scope a query to only include stagings by style.
     */
    public function scopeByStyle($query, $style)
    {
        return $query->where('staging_style', $style);
    }

    /**
     * Scope a query to only include recent stagings.
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', Carbon::now()->subDays($days));
    }

    /**
     * Get room type label in Arabic.
     */
    public function getRoomTypeLabelAttribute(): string
    {
        $types = [
            'living_room' => 'غرفة معيشة',
            'bedroom' => 'غرفة نوم',
            'kitchen' => 'مطبخ',
            'dining_room' => 'غرفة طعام',
            'bathroom' => 'حمام',
            'office' => 'مكتب',
            'outdoor' => 'فضاء خارجي',
            'entryway' => 'مدخل',
        ];

        return $types[$this->room_type] ?? 'غير معروف';
    }

    /**
     * Get staging style label in Arabic.
     */
    public function getStagingStyleLabelAttribute(): string
    {
        $styles = [
            'modern' => 'عصري',
            'contemporary' => 'معاصر',
            'traditional' => 'تقليدي',
            'minimalist' => 'بسيط',
            'luxury' => 'فاخر',
            'scandinavian' => 'إسكندنافي',
            'industrial' => 'صناعي',
            'bohemian' => 'بوهيمي',
            'coastal' => 'ساحلي',
            'farmhouse' => 'ريفي',
        ];

        return $styles[$this->staging_style] ?? 'غير معروف';
    }

    /**
     * Get furniture style label in Arabic.
     */
    public function getFurnitureStyleLabelAttribute(): string
    {
        $styles = [
            'modern' => 'عصري',
            'classic' => 'كلاسيكي',
            'vintage' => 'عتيق',
            'contemporary' => 'معاصر',
            'industrial' => 'صناعي',
            'scandinavian' => 'إسكندنافي',
        ];

        return $styles[$this->furniture_style] ?? 'غير معروف';
    }

    /**
     * Get color scheme label in Arabic.
     */
    public function getColorSchemeLabelAttribute(): string
    {
        $schemes = [
            'neutral' => 'محايد',
            'warm' => 'دافئ',
            'cool' => 'بارد',
            'monochromatic' => 'أحادي اللون',
            'complementary' => 'مكمل',
            'analogous' => 'متشابه',
            'triadic' => 'ثلاثي',
        ];

        return $schemes[$this->color_scheme] ?? 'غير معروف';
    }

    /**
     * Get target audience label in Arabic.
     */
    public function getTargetAudienceLabelAttribute(): string
    {
        $audiences = [
            'families' => 'عائلات',
            'young_professionals' => 'شباب محترفين',
            'retirees' => 'متقاعدين',
            'investors' => 'مستثمرين',
            'students' => 'طلاب',
            'luxury_buyers' => 'مشترين فاخرين',
            'first_time_buyers' => 'مشترين لأول مرة',
        ];

        return $audiences[$this->target_audience] ?? 'عام';
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
            'reviewing' => 'قيد المراجعة',
            'approved' => 'موافق عليه',
            'rejected' => 'مرفوض',
            'failed' => 'فشل',
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
     * Get realism level text.
     */
    public function getRealismLevelAttribute(): string
    {
        if ($this->realism_score >= 9.0) return 'واقعي جداً';
        if ($this->realism_score >= 8.0) return 'واقعي';
        if ($this->realism_score >= 7.0) return 'جيد';
        if ($this->realism_score >= 6.0) return 'مقبول';
        return 'ضعيف';
    }

    /**
     * Get aesthetic level text.
     */
    public function getAestheticLevelAttribute(): string
    {
        if ($this->aesthetic_appeal >= 9.0) return 'جذاب جداً';
        if ($this->aesthetic_appeal >= 8.0) return 'جذاب';
        if ($this->aesthetic_appeal >= 7.0) return 'جيد';
        if ($this->aesthetic_appeal >= 6.0) return 'مقبول';
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
     * Get furniture count.
     */
    public function getFurnitureCountAttribute(): int
    {
        return count($this->furniture_items ?? []);
    }

    /**
     * Get decor count.
     */
    public function getDecorCountAttribute(): int
    {
        return count($this->decor_elements ?? []);
    }

    /**
     * Get overall score.
     */
    public function getOverallScoreAttribute(): float
    {
        return round((
            $this->quality_score * 0.3 +
            $this->realism_score * 0.3 +
            $this->aesthetic_appeal * 0.2 +
            $this->style_consistency * 0.1 +
            $this->market_appeal * 0.1
        ), 2);
    }

    /**
     * Get overall level.
     */
    public function getOverallLevelAttribute(): string
    {
        $score = $this->overall_score;
        
        if ($score >= 9.0) return 'ممتاز';
        if ($score >= 8.0) return 'جيد جداً';
        if ($score >= 7.0) return 'جيد';
        if ($score >= 6.0) return 'مقبول';
        return 'ضعيف';
    }

    /**
     * Check if staging is ready for publishing.
     */
    public function isReadyForPublishing(): bool
    {
        return $this->status === 'completed' && 
               $this->overall_score >= 7.0 &&
               !is_null($this->staged_image_path);
    }

    /**
     * Check if staging is high quality.
     */
    public function isHighQuality(): bool
    {
        return $this->overall_score >= 8.0;
    }

    /**
     * Get market appeal level.
     */
    public function getMarketAppealLevelAttribute(): string
    {
        if ($this->market_appeal >= 9.0) return 'جذاب جداً';
        if ($this->market_appeal >= 8.0) return 'جذاب';
        if ($this->market_appeal >= 7.0) return 'جيد';
        if ($this->market_appeal >= 6.0) return 'مقبول';
        return 'ضعيف';
    }

    /**
     * Get style consistency level.
     */
    public function getStyleConsistencyLevelAttribute(): string
    {
        if ($this->style_consistency >= 9.0) return 'متناسق جداً';
        if ($this->style_consistency >= 8.0) return 'متناسق';
        if ($this->style_consistency >= 7.0) return 'جيد';
        if ($this->style_consistency >= 6.0) return 'مقبول';
        return 'ضعيف';
    }

    /**
     * Get engagement level.
     */
    public function getEngagementLevelAttribute(): string
    {
        if ($this->engagement_score >= 8.0) return 'مرتفع جداً';
        if ($this->engagement_score >= 6.0) return 'مرتفع';
        if ($this->engagement_score >= 4.0) return 'متوسط';
        return 'منخفض';
    }

    /**
     * Increment view count.
     */
    public function incrementViewCount(): bool
    {
        $this->view_count++;
        return $this->save();
    }

    /**
     * Update engagement score.
     */
    public function updateEngagementScore(float $score): bool
    {
        $this->engagement_score = $score;
        return $this->save();
    }

    /**
     * Add feedback rating.
     */
    public function addFeedbackRating(float $rating): bool
    {
        if ($this->feedback_rating === null) {
            $this->feedback_rating = $rating;
        } else {
            // Average the ratings
            $this->feedback_rating = ($this->feedback_rating + $rating) / 2;
        }
        
        return $this->save();
    }

    /**
     * Create a new AI virtual staging.
     */
    public static function createStaging(array $data): self
    {
        // Simulate AI virtual staging process
        $roomType = $data['room_type'] ?? 'living_room';
        $stagingStyle = $data['staging_style'] ?? 'modern';
        $furnitureStyle = $data['furniture_style'] ?? 'contemporary';
        $colorScheme = $data['color_scheme'] ?? 'neutral';
        $targetAudience = $data['target_audience'] ?? 'families';
        
        // Generate furniture items
        $furnitureItems = [
            [
                'item' => 'sofa',
                'style' => $furnitureStyle,
                'color' => 'gray',
                'position' => ['x' => 200, 'y' => 300],
                'confidence' => rand(85, 98) / 100,
            ],
            [
                'item' => 'coffee_table',
                'style' => $furnitureStyle,
                'color' => 'brown',
                'position' => ['x' => 250, 'y' => 350],
                'confidence' => rand(80, 95) / 100,
            ],
            [
                'item' => 'armchair',
                'style' => $furnitureStyle,
                'color' => 'beige',
                'position' => ['x' => 150, 'y' => 320],
                'confidence' => rand(75, 92) / 100,
            ],
        ];
        
        // Generate decor elements
        $decorElements = [
            [
                'item' => 'rug',
                'style' => $stagingStyle,
                'color' => 'cream',
                'position' => ['x' => 200, 'y' => 400],
                'size' => 'large',
            ],
            [
                'item' => 'curtains',
                'style' => $stagingStyle,
                'color' => 'white',
                'position' => ['x' => 100, 'y' => 100],
                'size' => 'full_length',
            ],
            [
                'item' => 'wall_art',
                'style' => $stagingStyle,
                'type' => 'abstract',
                'position' => ['x' => 300, 'y' => 150],
                'size' => 'medium',
            ],
        ];
        
        // Generate lighting setup
        $lightingSetup = [
            'natural_light' => [
                'intensity' => rand(60, 90) / 100,
                'direction' => 'north',
                'quality' => 'soft',
            ],
            'artificial_light' => [
                'type' => 'warm_led',
                'intensity' => rand(40, 70) / 100,
                'placement' => 'ceiling',
            ],
            'accent_lighting' => [
                'type' => 'spotlight',
                'intensity' => rand(20, 50) / 100,
                'target' => 'wall_art',
            ],
        ];
        
        // Generate spatial arrangement
        $spatialArrangement = [
            'layout_type' => 'open_concept',
            'traffic_flow' => 'optimal',
            'space_utilization' => rand(75, 95) / 100,
            'balance_score' => rand(70, 90) / 100,
            'proportion_score' => rand(75, 92) / 100,
        ];
        
        // Calculate scores
        $qualityScore = rand(7.0, 9.8);
        $realismScore = rand(7.5, 9.5);
        $aestheticAppeal = rand(7.0, 9.2);
        $styleConsistency = rand(7.5, 9.0);
        $marketAppeal = rand(7.0, 9.5);
        $confidenceLevel = rand(80, 95) / 100;
        
        // Generate staged image path
        $originalPath = $data['original_image_path'];
        $stagedPath = str_replace('.', '_staged.', $originalPath);

        return static::create([
            'property_id' => $data['property_id'],
            'user_id' => $data['user_id'] ?? auth()->id(),
            'original_image_path' => $originalPath,
            'staged_image_path' => $stagedPath,
            'room_type' => $roomType,
            'staging_style' => $stagingStyle,
            'furniture_style' => $furnitureStyle,
            'color_scheme' => $colorScheme,
            'target_audience' => $targetAudience,
            'quality_score' => round($qualityScore, 2),
            'realism_score' => round($realismScore, 2),
            'aesthetic_appeal' => round($aestheticAppeal, 2),
            'furniture_items' => $furnitureItems,
            'decor_elements' => $decorElements,
            'lighting_setup' => $lightingSetup,
            'spatial_arrangement' => $spatialArrangement,
            'style_consistency' => round($styleConsistency, 2),
            'market_appeal' => round($marketAppeal, 2),
            'ai_model_version' => '7.4.2',
            'staging_metadata' => [
                'processing_time' => rand(2.5, 8.5) . 's',
                'image_resolution' => '2048x1536',
                'render_quality' => 'ultra_high',
                'style_transfer_model' => 'gan_v8',
                'furniture_library' => 'premium_v3',
                'staging_date' => now()->toDateTimeString(),
            ],
            'processing_time' => rand(2.5, 8.5),
            'confidence_level' => round($confidenceLevel, 2),
            'status' => 'completed',
            'is_published' => false,
            'view_count' => 0,
            'engagement_score' => 0.0,
            'feedback_rating' => null,
            'created_by' => $data['created_by'] ?? auth()->id(),
        ]);
    }

    /**
     * Publish the staging.
     */
    public function publish(): bool
    {
        if (!$this->isReadyForPublishing()) {
            return false;
        }
        
        $this->is_published = true;
        $this->published_at = now();
        $this->status = 'approved';
        
        return $this->save();
    }

    /**
     * Unpublish the staging.
     */
    public function unpublish(): bool
    {
        $this->is_published = false;
        $this->status = 'completed';
        
        return $this->save();
    }

    /**
     * Get staging summary for dashboard.
     */
    public function getDashboardSummary(): array
    {
        return [
            'id' => $this->id,
            'property_id' => $this->property_id,
            'room_type' => $this->room_type_label,
            'style' => $this->staging_style_label,
            'overall_score' => $this->overall_score,
            'overall_level' => $this->overall_level,
            'quality_score' => $this->quality_score,
            'realism_score' => $this->realism_score,
            'is_published' => $this->is_published,
            'view_count' => $this->view_count,
            'engagement_score' => $this->engagement_score,
            'status' => $this->status_label,
            'created_at' => $this->created_at->format('Y-m-d H:i'),
        ];
    }

    /**
     * Get detailed staging report.
     */
    public function getDetailedReport(): array
    {
        return [
            'basic_info' => [
                'property_id' => $this->property_id,
                'room_type' => $this->room_type_label,
                'staging_style' => $this->staging_style_label,
                'furniture_style' => $this->furniture_style_label,
                'color_scheme' => $this->color_scheme_label,
                'target_audience' => $this->target_audience_label,
                'processing_time' => $this->processing_time . 's',
                'confidence_level' => $this->confidence_level_text,
            ],
            'quality_assessment' => [
                'quality_score' => $this->quality_score,
                'quality_level' => $this->quality_level,
                'realism_score' => $this->realism_score,
                'realism_level' => $this->realism_level,
                'aesthetic_appeal' => $this->aesthetic_appeal,
                'aesthetic_level' => $this->aesthetic_level,
                'overall_score' => $this->overall_score,
                'overall_level' => $this->overall_level,
            ],
            'design_elements' => [
                'furniture_items' => $this->furniture_items,
                'furniture_count' => $this->furniture_count,
                'decor_elements' => $this->decor_elements,
                'decor_count' => $this->decor_count,
                'lighting_setup' => $this->lighting_setup,
                'spatial_arrangement' => $this->spatial_arrangement,
            ],
            'style_analysis' => [
                'style_consistency' => $this->style_consistency,
                'style_consistency_level' => $this->style_consistency_level,
                'market_appeal' => $this->market_appeal,
                'market_appeal_level' => $this->market_appeal_level,
            ],
            'performance' => [
                'is_published' => $this->is_published,
                'published_at' => $this->published_at?->format('Y-m-d H:i'),
                'view_count' => $this->view_count,
                'engagement_score' => $this->engagement_score,
                'engagement_level' => $this->engagement_level,
                'feedback_rating' => $this->feedback_rating,
            ],
            'images' => [
                'original_image' => $this->original_image_path,
                'staged_image' => $this->staged_image_path,
            ],
        ];
    }
}

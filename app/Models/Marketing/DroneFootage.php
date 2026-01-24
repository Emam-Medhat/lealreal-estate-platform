<?php

namespace App\Models\Marketing;

use App\Models\Property\Property;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DroneFootage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'property_id',
        'title',
        'description',
        'footage_type',
        'status',
        'video_file',
        'thumbnail',
        'quality',
        'duration',
        'tags',
        'featured',
        'allow_comments',
        'allow_downloads',
        'password_protected',
        'password',
        'flight_info',
        'editing_info',
        'music_info',
        'call_to_action',
        'seo_settings',
        'distribution_settings',
        'subtitles',
        'transcript',
        'additional_media',
        'behind_the_scenes',
        'views',
        'unique_viewers',
        'average_watch_time',
        'completion_rate',
        'engagement_rate',
        'shares',
        'likes',
        'comments',
        'downloads',
        'published_at',
        'processed_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tags' => 'array',
        'flight_info' => 'array',
        'editing_info' => 'array',
        'music_info' => 'array',
        'call_to_action' => 'array',
        'seo_settings' => 'array',
        'distribution_settings' => 'array',
        'additional_media' => 'array',
        'behind_the_scenes' => 'array',
        'featured' => 'boolean',
        'allow_comments' => 'boolean',
        'allow_downloads' => 'boolean',
        'password_protected' => 'boolean',
        'published_at' => 'datetime',
        'processed_at' => 'datetime',
        'average_watch_time' => 'integer',
        'completion_rate' => 'decimal:2',
        'engagement_rate' => 'decimal:2',
        'flight_info.permit_required' => 'boolean',
        'editing_info.color_grading' => 'boolean',
        'editing_info.sound_design' => 'boolean',
        'editing_info.special_effects' => 'boolean',
        'music_info.license_required' => 'boolean',
    ];

    // Relationships
    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    public function scopeByProperty($query, $propertyId)
    {
        return $query->where('property_id', $propertyId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('footage_type', $type);
    }

    public function scopeByQuality($query, $quality)
    {
        return $query->where('quality', $quality);
    }

    public function scopeWithPermit($query)
    {
        return $query->where('flight_info.permit_required', true);
    }

    public function scopeWithColorGrading($query)
    {
        return $query->where('editing_info.color_grading', true);
    }

    public function scopePublishedLastNDays($query, $days)
    {
        return $query->where('status', 'published')
                    ->where('published_at', '>=', now()->subDays($days));
    }

    // Methods
    public function publish()
    {
        $this->update([
            'status' => 'published',
            'published_at' => now(),
        ]);
    }

    public function process()
    {
        $this->update([
            'status' => 'processing',
        ]);
    }

    public function completeProcessing()
    {
        $this->update([
            'status' => 'published',
            'processed_at' => now(),
            'published_at' => now(),
        ]);
    }

    public function calculateMetrics()
    {
        if ($this->views > 0) {
            $this->engagement_rate = (($this->likes + $this->comments + $this->shares) / $this->views) * 100;
        }

        if ($this->duration > 0) {
            $this->completion_rate = ($this->average_watch_time / $this->duration) * 100;
        }

        $this->save();
    }

    public function incrementViews()
    {
        $this->increment('views');
    }

    public function incrementLikes()
    {
        $this->increment('likes');
    }

    public function incrementComments()
    {
        $this->increment('comments');
    }

    public function incrementShares()
    {
        $this->increment('shares');
    }

    public function incrementDownloads()
    {
        $this->increment('downloads');
    }

    public function getEngagementScoreAttribute()
    {
        // Calculate engagement score with drone-specific weighting
        $score = 0;
        
        // Views (15% weight) - drone footage typically gets fewer views but higher quality
        $score += min($this->views / 500 * 15, 15); // 500 views = full points
        
        // Engagement rate (35% weight) - higher weight for drone footage
        $score += min($this->engagement_rate / 3 * 35, 35); // 3% engagement = full points
        
        // Completion rate (30% weight) - drone footage completion is important
        $score += min($this->completion_rate / 60 * 30, 30); // 60% completion = full points
        
        // Shares (15% weight) - drone footage is highly shareable
        $score += min($this->shares / 50 * 15, 15); // 50 shares = full points
        
        // Comments (5% weight)
        $score += min($this->comments / 20 * 5, 5); // 20 comments = full points
        
        return round($score);
    }

    public function getPerformanceStatusAttribute()
    {
        $score = $this->engagement_score;
        
        if ($score >= 80) {
            return 'excellent';
        } elseif ($score >= 60) {
            return 'good';
        } elseif ($score >= 40) {
            return 'average';
        } else {
            return 'poor';
        }
    }

    public function getTypeDisplayNameAttribute()
    {
        return match($this->footage_type) {
            'aerial_tour' => 'جولة جوية',
            'neighborhood_overview' => 'نظرة عامة على الحي',
            'property_highlight' => 'أبرز ما في العقار',
            'construction_progress' => 'تقدم البناء',
            'before_after' => 'قبل وبعد',
            'cinematic' => 'سينمائي',
            default => $this->footage_type,
        };
    }

    public function getQualityDisplayNameAttribute()
    {
        return match($this->quality) {
            '720p' => 'HD',
            '1080p' => 'Full HD',
            '4k' => '4K Ultra HD',
            '8k' => '8K Ultra HD',
            default => $this->quality,
        };
    }

    public function getDurationFormattedAttribute()
    {
        $hours = floor($this->duration / 3600);
        $minutes = floor(($this->duration % 3600) / 60);
        $seconds = $this->duration % 60;
        
        if ($hours > 0) {
            return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
        }
        
        return sprintf('%02d:%02d', $minutes, $seconds);
    }

    public function getAverageWatchTimeFormattedAttribute()
    {
        $minutes = floor($this->average_watch_time / 60);
        $seconds = $this->average_watch_time % 60;
        
        if ($minutes > 0) {
            return "{$minutes}:{$seconds}";
        }
        
        return "0:{$seconds}";
    }

    public function getFlightDetailsAttribute()
    {
        $info = $this->flight_info ?? [];
        
        return [
            'drone_model' => $info['drone_model'] ?? 'غير محدد',
            'pilot_name' => $info['pilot_name'] ?? 'غير محدد',
            'flight_date' => isset($info['flight_date']) ? \Carbon\Carbon::parse($info['flight_date'])->format('Y-m-d') : null,
            'weather_condition' => $info['weather_condition'] ?? 'غير محدد',
            'altitude' => $info['altitude'] ?? 0,
            'flight_time' => $info['flight_time'] ?? 0,
            'location_coordinates' => $info['location_coordinates'] ?? 'غير محدد',
            'permit_required' => $info['permit_required'] ?? false,
            'permit_number' => $info['permit_number'] ?? null,
        ];
    }

    public function getEditingDetailsAttribute()
    {
        $info = $this->editing_info ?? [];
        
        return [
            'software_used' => $info['software_used'] ?? 'غير محدد',
            'editor_name' => $info['editor_name'] ?? 'غير محدد',
            'color_grading' => $info['color_grading'] ?? false,
            'sound_design' => $info['sound_design'] ?? false,
            'special_effects' => $info['special_effects'] ?? false,
            'editing_duration' => $info['editing_duration'] ?? 0,
        ];
    }

    public function getMusicDetailsAttribute()
    {
        $info = $this->music_info ?? [];
        
        return [
            'track_title' => $info['track_title'] ?? 'غير محدد',
            'artist' => $info['artist'] ?? 'غير محدد',
            'license_type' => $info['license_type'] ?? 'غير محدد',
            'license_number' => $info['license_number'] ?? null,
        ];
    }

    public function hasPermit()
    {
        return $this->flight_info['permit_required'] ?? false;
    }

    public function hasColorGrading()
    {
        return $this->editing_info['color_grading'] ?? false;
    }

    public function hasSoundDesign()
    {
        return $this->editing_info['sound_design'] ?? false;
    }

    public function hasSpecialEffects()
    {
        return $this->editing_info['special_effects'] ?? false;
    }

    public function hasLicensedMusic()
    {
        return !empty($this->music_info['license_number']);
    }

    public function getProductionQualityAttribute()
    {
        $score = 0;
        
        // Quality score (40%)
        $qualityScores = ['720p' => 25, '1080p' => 35, '4k' => 45, '8k' => 50];
        $score += $qualityScores[$this->quality] ?? 25;
        
        // Color grading (20%)
        $score += $this->hasColorGrading() ? 20 : 0;
        
        // Sound design (15%)
        $score += $this->hasSoundDesign() ? 15 : 0;
        
        // Special effects (15%)
        $score += $this->hasSpecialEffects() ? 15 : 0;
        
        // Licensed music (10%)
        $score += $this->hasLicensedMusic() ? 10 : 0;
        
        return match(true) {
            $score >= 80 => 'احترافي',
            $score >= 60 => 'جيد جداً',
            $score >= 40 => 'جيد',
            $score >= 20 => 'متوسط',
            default => 'يحتاج تحسين',
        };
    }

    public function getTechnicalSpecsAttribute()
    {
        return [
            'resolution' => $this->quality,
            'frame_rate' => $this->getFrameRate(),
            'bitrate' => $this->getBitrate(),
            'codec' => 'H.264',
            'file_format' => 'MP4',
            'color_space' => 'REC.709',
            'audio_format' => 'AAC',
            'audio_bitrate' => '128 kbps',
            'audio_channels' => 'Stereo',
        ];
    }

    private function getFrameRate()
    {
        return match($this->quality) {
            '720p' => '30 fps',
            '1080p' => '30 fps',
            '4k' => '30 fps',
            '8k' => '24 fps',
            default => '30 fps',
        };
    }

    private function getBitrate()
    {
        return match($this->quality) {
            '720p' => '5 Mbps',
            '1080p' => '10 Mbps',
            '4k' => '25 Mbps',
            '8k' => '50 Mbps',
            default => '5 Mbps',
        };
    }

    public function getAerialShotsAnalysisAttribute()
    {
        return [
            'total_shots' => rand(15, 50),
            'unique_angles' => rand(8, 25),
            'altitude_variety' => rand(3, 8),
            'smooth_transitions' => rand(10, 40),
            'cinematic_quality' => rand(70, 95) . '%',
            'coverage_completeness' => rand(75, 95) . '%',
            'lighting_quality' => rand(60, 90) . '%',
            'stabilization_score' => rand(70, 95) . '/100',
        ];
    }

    public function getLocationCoverageAttribute()
    {
        return [
            'property_coverage' => rand(80, 95) . '%',
            'neighborhood_visibility' => rand(60, 85) . '%',
            'landmark_inclusion' => rand(3, 8),
            'access_roads_shown' => rand(2, 5),
            'nearby_amenities' => rand(5, 15),
            'green_spaces_visible' => rand(1, 4),
            'transportation_links' => rand(2, 6),
            'orientation_clarity' => rand(70, 95) . '%',
        ];
    }

    public function getProductionTimelineAttribute()
    {
        return [
            'planning_phase' => rand(1, 3) . ' days',
            'permit_acquisition' => $this->hasPermit() ? rand(3, 7) . ' days' : 'N/A',
            'filming_duration' => rand(2, 6) . ' hours',
            'editing_time' => rand(8, 24) . ' hours',
            'post_production' => rand(2, 5) . ' days',
            'review_process' => rand(1, 3) . ' days',
            'total_production_time' => rand(5, 15) . ' days',
        ];
    }

    public function getCostBreakdownAttribute()
    {
        // Mock cost breakdown - in real implementation this would track actual costs
        $baseCost = match($this->quality) {
            '720p' => 2000,
            '1080p' => 3500,
            '4k' => 6000,
            '8k' => 10000,
            default => 2000,
        };
        
        return [
            'drone_operator' => $baseCost * 0.3,
            'equipment_rental' => $baseCost * 0.2,
            'permit_fees' => $this->hasPermit() ? $baseCost * 0.1 : 0,
            'editing_services' => $baseCost * 0.25,
            'music_licensing' => $baseCost * 0.05,
            'post_production' => $baseCost * 0.1,
            'total_cost' => $baseCost,
            'cost_per_minute' => $this->duration > 0 ? round($baseCost / $this->duration, 2) : 0,
        ];
    }

    public function getLegalComplianceAttribute()
    {
        return [
            'flight_permit' => $this->hasPermit() ? 'متوفر' : 'غير مطلوب',
            'privacy_compliance' => 'ممتاز',
            'airspace_clearance' => 'مؤكد',
            'insurance_coverage' => 'مؤكد',
            'data_protection' => 'ممتاز',
            'copyright_clearance' => $this->hasLicensedMusic() ? 'مؤكد' : 'يحتاج مراجعة',
            'compliance_score' => $this->hasPermit() && $this->hasLicensedMusic() ? '95%' : '80%',
        ];
    }

    public function getOptimizationSuggestionsAttribute()
    {
        $suggestions = [];

        if ($this->completion_rate < 40) {
            $suggestions[] = 'تحسين سرعة الإيقاع لزيادة نسبة الإكمال';
        }

        if ($this->engagement_rate < 3) {
            $suggestions[] = 'إضافة مؤثرات بصرية جذابة';
        }

        if (!$this->hasColorGrading()) {
            $suggestions[] = 'استخدام تعديل الألوان لتحسين الجودة البصرية';
        }

        if (!$this->hasSoundDesign()) {
            $suggestions[] = 'إضافة تصميم صوتي احترافي';
        }

        if (!$this->hasLicensedMusic()) {
            $suggestions[] = 'استخدام موسيقى مرخصة تجنباً للمشاكل القانونية';
        }

        if ($this->duration > 300) { // 5 minutes
            $suggestions[] = 'اختصار الفيديو لتحسين نسبة المشاهدة';
        }

        if ($this->quality === '720p') {
            $suggestions[] = 'الترقية إلى 4K لجودة أفضل';
        }

        return $suggestions;
    }

    public function getMarketValueAttribute()
    {
        // Calculate market value based on quality, engagement, and production value
        $baseValue = match($this->quality) {
            '720p' => 5000,
            '1080p' => 8000,
            '4k' => 15000,
            '8k' => 25000,
            default => 5000,
        };

        // Adjust for engagement
        if ($this->engagement_rate > 5) {
            $baseValue *= 1.5;
        }

        // Adjust for production quality
        $productionMultiplier = match($this->production_quality) {
            'احترافي' => 1.5,
            'جيد جداً' => 1.3,
            'جيد' => 1.1,
            default => 1.0,
        };

        return round($baseValue * $productionMultiplier);
    }

    // Events
    protected static function booted()
    {
        static::creating(function ($footage) {
            if (auth()->check()) {
                $footage->created_by = auth()->id();
            }
        });

        static::updating(function ($footage) {
            if (auth()->check()) {
                $footage->updated_by = auth()->id();
            }
        });
    }
}

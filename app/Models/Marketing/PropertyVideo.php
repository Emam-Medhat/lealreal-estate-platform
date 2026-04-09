<?php

namespace App\Models\Marketing;

use App\Models\Property\Property;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PropertyVideo extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'property_id',
        'title',
        'description',
        'video_type',
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
        'call_to_action',
        'seo_settings',
        'distribution_settings',
        'subtitles',
        'transcript',
        'additional_media',
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
        'call_to_action' => 'array',
        'seo_settings' => 'array',
        'distribution_settings' => 'array',
        'additional_media' => 'array',
        'featured' => 'boolean',
        'allow_comments' => 'boolean',
        'allow_downloads' => 'boolean',
        'password_protected' => 'boolean',
        'published_at' => 'datetime',
        'processed_at' => 'datetime',
        'average_watch_time' => 'integer',
        'completion_rate' => 'decimal:2',
        'engagement_rate' => 'decimal:2',
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
        return $query->where('video_type', $type);
    }

    public function scopeByQuality($query, $quality)
    {
        return $query->where('quality', $quality);
    }

    public function scopeWithComments($query)
    {
        return $query->where('allow_comments', true);
    }

    public function scopeProtected($query)
    {
        return $query->where('password_protected', true);
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
        // Calculate engagement score based on various metrics
        $score = 0;
        
        // Views (20% weight)
        $score += min($this->views / 1000 * 20, 20); // 1000 views = full points
        
        // Engagement rate (30% weight)
        $score += min($this->engagement_rate / 5 * 30, 30); // 5% engagement = full points
        
        // Completion rate (25% weight)
        $score += min($this->completion_rate / 50 * 25, 25); // 50% completion = full points
        
        // Shares (15% weight)
        $score += min($this->shares / 100 * 15, 15); // 100 shares = full points
        
        // Comments (10% weight)
        $score += min($this->comments / 50 * 10, 10); // 50 comments = full points
        
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
        return match($this->video_type) {
            'property_tour' => 'جولة عقارية',
            'neighborhood_tour' => 'جولة الحي',
            'testimonial' => 'شهادة عملاء',
            'agent_intro' => 'مقدمة الوكيل',
            'market_update' => 'تحديث السوق',
            'virtual_open_house' => 'بيت مفتوح افتراضي',
            default => $this->video_type,
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

    public function getFileSizeAttribute()
    {
        // Mock file size calculation - in real implementation this would get actual file size
        $sizePerMinute = match($this->quality) {
            '720p' => 5 * 1024 * 1024, // 5MB per minute
            '1080p' => 10 * 1024 * 1024, // 10MB per minute
            '4k' => 25 * 1024 * 1024, // 25MB per minute
            '8k' => 50 * 1024 * 1024, // 50MB per minute
            default => 5 * 1024 * 1024,
        };
        
        return $this->duration * $sizePerMinute;
    }

    public function getFileSizeFormattedAttribute()
    {
        $bytes = $this->file_size;
        
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        
        return $bytes . ' bytes';
    }

    public function getVideoUrlAttribute()
    {
        return $this->video_file ? storage_path('app/public/' . $this->video_file) : null;
    }

    public function getThumbnailUrlAttribute()
    {
        return $this->thumbnail ? storage_path('app/public/' . $this->thumbnail) : null;
    }

    public function getSubtitlesUrlAttribute()
    {
        return $this->subtitles ? storage_path('app/public/' . $this->subtitles) : null;
    }

    public function hasThumbnail()
    {
        return !empty($this->thumbnail);
    }

    public function hasSubtitles()
    {
        return !empty($this->subtitles);
    }

    public function hasTranscript()
    {
        return !empty($this->transcript);
    }

    public function hasAdditionalMedia()
    {
        return !empty($this->additional_media);
    }

    public function isPublished()
    {
        return $this->status === 'published' && $this->published_at;
    }

    public function isProcessing()
    {
        return $this->status === 'processing';
    }

    public function canBePublished()
    {
        return in_array($this->status, ['draft']) && 
               !empty($this->video_file) &&
               !empty($this->title);
    }

    public function getTagStringAttribute()
    {
        return implode(', ', $this->tags ?? []);
    }

    public function getDistributionPlatformsAttribute()
    {
        return array_keys(array_filter($this->distribution_settings ?? []));
    }

    public function getWatchTimeDistributionAttribute()
    {
        // Mock data - in real implementation this would track actual viewing patterns
        $distribution = [];
        $segments = 10;
        
        for ($i = 0; $i < $segments; $i++) {
            $startPercent = ($i / $segments) * 100;
            $endPercent = (($i + 1) / $segments) * 100;
            
            // Simulate drop-off rate
            $dropOffRate = 1 - ($i / $segments) * 0.7; // 70% drop-off by end
            $viewers = round($this->unique_viewers * $dropOffRate);
            
            $distribution[] = [
                'segment' => $i + 1,
                'start_percent' => $startPercent,
                'end_percent' => $endPercent,
                'viewers' => $viewers,
                'retention_rate' => round($dropOffRate * 100),
            ];
        }
        
        return $distribution;
    }

    public function getAudienceRetentionAttribute()
    {
        return [
            'average_retention' => $this->completion_rate,
            'first_quarter' => min(95, $this->completion_rate + 20),
            'second_quarter' => min(85, $this->completion_rate + 10),
            'third_quarter' => max(60, $this->completion_rate - 5),
            'fourth_quarter' => max(40, $this->completion_rate - 15),
            'peak_retention_time' => $this->getPeakRetentionTime(),
        ];
    }

    private function getPeakRetentionTime()
    {
        // Mock calculation - in real implementation this would analyze actual viewing data
        return rand($this->duration * 0.1, $this->duration * 0.3);
    }

    public function getTrafficSourcesAttribute()
    {
        // Mock traffic source data - in real implementation this would track actual sources
        return [
            'direct' => rand(20, 40),
            'search' => rand(15, 30),
            'social' => rand(20, 35),
            'referral' => rand(10, 25),
            'email' => rand(5, 15),
            'other' => rand(5, 10),
        ];
    }

    public function getDeviceBreakdownAttribute()
    {
        return [
            'desktop' => rand(40, 60),
            'mobile' => rand(30, 50),
            'tablet' => rand(5, 15),
            'tv' => rand(2, 8),
        ];
    }

    public function getGeographicDistributionAttribute()
    {
        return [
            'الرياض' => rand(20, 35),
            'جدة' => rand(15, 25),
            'الدمام' => rand(10, 20),
            'مكة' => rand(8, 15),
            'المدينة' => rand(5, 12),
            'أخرى' => rand(15, 25),
        ];
    }

    public function getEngagementTimelineAttribute()
    {
        // Mock timeline data - in real implementation this would track actual engagement over time
        $timeline = [];
        $days = 30;
        
        for ($i = $days; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $views = rand(0, 100);
            $engagement = rand(0, 20);
            
            $timeline[] = [
                'date' => $date,
                'views' => $views,
                'likes' => rand(0, $engagement),
                'comments' => rand(0, $engagement / 2),
                'shares' => rand(0, $engagement / 3),
            ];
        }
        
        return $timeline;
    }

    public function getOptimizationSuggestionsAttribute()
    {
        $suggestions = [];

        if ($this->completion_rate < 30) {
            $suggestions[] = 'تحسين المحتوى لزيادة معدل الإكمال';
        }

        if ($this->engagement_rate < 2) {
            $suggestions[] = 'إضافة دعوات لتحفيز التفاعل';
        }

        if (!$this->hasThumbnail()) {
            $suggestions[] = 'إضافة صورة مصغرة جذابة';
        }

        if (!$this->hasSubtitles()) {
            $suggestions[] = 'إضافة ترجمة لتوسيع الجمهور';
        }

        if (strlen($this->description) < 100) {
            $suggestions[] = 'تحسين الوصف لتحسين SEO';
        }

        if (empty($this->tags)) {
            $suggestions[] = 'إضافة علامات لتحسين اكتشاف الفيديو';
        }

        if ($this->duration > 600) { // 10 minutes
            $suggestions[] = 'اختصار الفيديو لتحسين نسبة المشاهدة';
        }

        return $suggestions;
    }

    public function getMonetizationPotentialAttribute()
    {
        // Mock calculation - in real implementation this would consider actual monetization options
        $basePotential = $this->views * 0.01; // $0.01 per view
        
        if ($this->engagement_rate > 5) {
            $basePotential *= 1.5; // Higher engagement = higher potential
        }
        
        if ($this->completion_rate > 50) {
            $basePotential *= 1.3; // Higher completion = higher potential
        }
        
        return [
            'estimated_revenue' => round($basePotential, 2),
            'cpm' => round($basePotential / ($this->views / 1000), 2),
            'potential_earnings_per_month' => round($basePotential * 30, 2),
        ];
    }

    // Events
    protected static function booted()
    {
        static::creating(function ($video) {
            if (auth()->check()) {
                $video->created_by = auth()->id();
            }
        });

        static::updating(function ($video) {
            if (auth()->check()) {
                $video->updated_by = auth()->id();
            }
        });
    }
}

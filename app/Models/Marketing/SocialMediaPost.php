<?php

namespace App\Models\Marketing;

use App\Models\Property\Property;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SocialMediaPost extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'property_id',
        'title',
        'content',
        'platform',
        'post_type',
        'status',
        'scheduled_at',
        'published_at',
        'hashtags',
        'mentions',
        'call_to_action',
        'target_audience',
        'budget',
        'boost_post',
        'media_files',
        'thumbnail',
        'video_url',
        'link_url',
        'location_tag',
        'language',
        'engagement_settings',
        'promotion_settings',
        'analytics_settings',
        'total_engagement',
        'reach',
        'impressions',
        'likes',
        'comments',
        'shares',
        'saves',
        'clicks',
        'video_views',
        'video_completion_rate',
        'carousel_swipes',
        'story_views',
        'story_replies',
        'story_shares',
        'story_exits',
        'boost_performance',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'published_at' => 'datetime',
        'hashtags' => 'array',
        'mentions' => 'array',
        'call_to_action' => 'array',
        'target_audience' => 'array',
        'budget' => 'decimal:2',
        'boost_post' => 'boolean',
        'media_files' => 'array',
        'engagement_settings' => 'array',
        'promotion_settings' => 'array',
        'analytics_settings' => 'array',
        'boost_performance' => 'array',
        'video_completion_rate' => 'decimal:2',
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

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeByPlatform($query, $platform)
    {
        return $query->where('platform', $platform);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('post_type', $type);
    }

    public function scopeByProperty($query, $propertyId)
    {
        return $query->where('property_id', $propertyId);
    }

    public function scopeBoosted($query)
    {
        return $query->where('boost_post', true);
    }

    public function scopeWithMedia($query)
    {
        return $query->whereNotNull('media_files')
                    ->where('media_files', '!=', '[]');
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

    public function schedule($dateTime)
    {
        $this->update([
            'status' => 'scheduled',
            'scheduled_at' => $dateTime,
        ]);
    }

    public function unpublish()
    {
        $this->update([
            'status' => 'draft',
            'published_at' => null,
        ]);
    }

    public function boost($budget)
    {
        $this->update([
            'boost_post' => true,
            'budget' => $budget,
        ]);
    }

    public function calculateEngagement()
    {
        $engagement = $this->likes + $this->comments + $this->shares + $this->saves;
        $this->total_engagement = $engagement;
        $this->save();
    }

    public function getEngagementRateAttribute()
    {
        return $this->reach > 0 
            ? (($this->total_engagement / $this->reach) * 100) 
            : 0;
    }

    public function getClickRateAttribute()
    {
        return $this->impressions > 0 
            ? (($this->clicks / $this->impressions) * 100) 
            : 0;
    }

    public function getShareRateAttribute()
    {
        return $this->impressions > 0 
            ? (($this->shares / $this->impressions) * 100) 
            : 0;
    }

    public function getCommentRateAttribute()
    {
        return $this->impressions > 0 
            ? (($this->comments / $this->impressions) * 100) 
            : 0;
    }

    public function getPerformanceScoreAttribute()
    {
        // Calculate performance score based on various metrics
        $score = 0;
        
        // Engagement rate (30% weight)
        $score += min($this->engagement_rate / 5 * 30, 30); // 5% engagement rate = full points
        
        // Click rate (25% weight)
        $score += min($this->click_rate / 2 * 25, 25); // 2% click rate = full points
        
        // Share rate (20% weight)
        $score += min($this->share_rate / 1 * 20, 20); // 1% share rate = full points
        
        // Reach vs Impressions (15% weight)
        $reachRatio = $this->impressions > 0 ? $this->reach / $this->impressions : 0;
        $score += $reachRatio * 15;
        
        // Video performance (10% weight) - only for video posts
        if ($this->post_type === 'video' && $this->video_completion_rate > 0) {
            $score += min($this->video_completion_rate / 50 * 10, 10); // 50% completion = full points
        }
        
        return round($score);
    }

    public function getPerformanceStatusAttribute()
    {
        $score = $this->performance_score;
        
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

    public function getPlatformIconAttribute()
    {
        return match($this->platform) {
            'facebook' => 'fab fa-facebook',
            'twitter' => 'fab fa-twitter',
            'instagram' => 'fab fa-instagram',
            'linkedin' => 'fab fa-linkedin',
            'youtube' => 'fab fa-youtube',
            'tiktok' => 'fab fa-tiktok',
            default => 'fas fa-globe',
        };
    }

    public function getPlatformColorAttribute()
    {
        return match($this->platform) {
            'facebook' => '#1877f2',
            'twitter' => '#1da1f2',
            'instagram' => '#e4405f',
            'linkedin' => '#0077b5',
            'youtube' => '#ff0000',
            'tiktok' => '#000000',
            default => '#6c757d',
        };
    }

    public function getHashtagStringAttribute()
    {
        return implode(' ', $this->hashtags ?? []);
    }

    public function getMentionStringAttribute()
    {
        return implode(' ', $this->mentions ?? []);
    }

    public function getMediaCountAttribute()
    {
        return count($this->media_files ?? []);
    }

    public function hasVideo()
    {
        return $this->post_type === 'video' || $this->post_type === 'reel' || !empty($this->video_url);
    }

    public function hasImages()
    {
        return $this->post_type === 'image' || $this->post_type === 'carousel' || $this->media_count > 0;
    }

    public function isScheduled()
    {
        return $this->status === 'scheduled' && $this->scheduled_at && $this->scheduled_at > now();
    }

    public function isOverdue()
    {
        return $this->status === 'scheduled' && 
               $this->scheduled_at && 
               $this->scheduled_at < now();
    }

    public function getTimeSincePublishedAttribute()
    {
        return $this->published_at ? $this->published_at->diffForHumans() : null;
    }

    public function getBestTimeToPostAttribute()
    {
        // Mock best time analysis - in real implementation this would analyze historical performance
        return match($this->platform) {
            'facebook' => '1:00 PM - 4:00 PM',
            'twitter' => '12:00 PM - 3:00 PM',
            'instagram' => '11:00 AM - 1:00 PM',
            'linkedin' => '9:00 AM - 11:00 AM',
            'youtube' => '2:00 PM - 4:00 PM',
            'tiktok' => '6:00 PM - 10:00 PM',
            default => '9:00 AM - 5:00 PM',
        };
    }

    public function getContentAnalysisAttribute()
    {
        return [
            'character_count' => strlen($this->content),
            'word_count' => str_word_count($this->content),
            'hashtag_count' => count($this->hashtags ?? []),
            'mention_count' => count($this->mentions ?? []),
            'has_call_to_action' => !empty($this->call_to_action),
            'has_emoji' => preg_match('/[\x{1F600}-\x{1F64F}]|[\x{1F300}-\x{1F5FF}]|[\x{1F680}-\x{1F6FF}]|[\x{1F1E0}-\x{1F1FF}]/u', $this->content),
            'has_link' => !empty($this->link_url),
            'has_location' => !empty($this->location_tag),
            'optimal_length' => $this->isOptimalLength(),
        ];
    }

    private function isOptimalLength()
    {
        $length = strlen($this->content);
        
        return match($this->platform) {
            'twitter' => $length <= 280,
            'instagram' => $length <= 2200,
            'facebook' => $length <= 63206,
            'linkedin' => $length <= 3000,
            default => true,
        };
    }

    public function getBoostROIAttribute()
    {
        if (!$this->boost_post || $this->budget <= 0) {
            return 0;
        }
        
        // Mock ROI calculation - in real implementation this would track actual conversions
        $estimatedValue = $this->total_engagement * 2; // $2 per engagement
        return (($estimatedValue - $this->budget) / $this->budget) * 100;
    }

    public function getAudienceInsightsAttribute()
    {
        // Mock audience insights - in real implementation this would come from platform analytics
        return [
            'age_groups' => [
                '18-24' => rand(10, 25),
                '25-34' => rand(25, 40),
                '35-44' => rand(20, 35),
                '45-54' => rand(10, 20),
                '55+' => rand(5, 15),
            ],
            'genders' => [
                'male' => rand(45, 55),
                'female' => rand(45, 55),
            ],
            'locations' => [
                'الرياض' => rand(20, 40),
                'جدة' => rand(15, 30),
                'الدمام' => rand(10, 20),
                'مكة' => rand(8, 15),
                'أخرى' => rand(15, 25),
            ],
            'languages' => [
                'العربية' => rand(60, 80),
                'English' => rand(20, 40),
            ],
        ];
    }

    // Events
    protected static function booted()
    {
        static::creating(function ($post) {
            if (auth()->check()) {
                $post->created_by = auth()->id();
            }
        });

        static::updating(function ($post) {
            if (auth()->check()) {
                $post->updated_by = auth()->id();
            }
        });

        static::saving(function ($post) {
            // Auto-publish scheduled posts that are due
            if ($post->status === 'scheduled' && 
                $post->scheduled_at && 
                $post->scheduled_at <= now()) {
                $post->status = 'published';
                $post->published_at = now();
            }
        });
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Advertisement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'campaign_id',
        'title',
        'description',
        'type',
        'image_url',
        'video_url',
        'thumbnail_url',
        'target_url',
        'start_date',
        'end_date',
        'daily_budget',
        'total_budget',
        'total_spent',
        'daily_spent',
        'status',
        'approval_status',
        'approved_at',
        'approved_by',
        'rejected_at',
        'rejected_by',
        'rejection_reason',
        'impressions_count',
        'clicks_count',
        'conversions_count',
        'banner_size',
        'width',
        'height',
        'animation_type',
        'video_duration',
        'autoplay',
        'muted',
        'controls',
        'loop',
        'click_tracking',
        'impression_tracking',
        'promotion_type',
        'playback_position',
        'skip_after'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'daily_budget' => 'decimal:2',
        'total_budget' => 'decimal:2',
        'total_spent' => 'decimal:2',
        'daily_spent' => 'decimal:2',
        'autoplay' => 'boolean',
        'muted' => 'boolean',
        'controls' => 'boolean',
        'loop' => 'boolean',
        'click_tracking' => 'boolean',
        'impression_tracking' => 'boolean'
    ];

    protected $appends = [
        'image_url_full',
        'video_url_full',
        'thumbnail_url_full',
        'is_active',
        'days_remaining',
        'budget_utilization'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function campaign()
    {
        return $this->belongsTo(AdCampaign::class);
    }

    public function placements()
    {
        return $this->belongsToMany(AdPlacement::class, 'ad_placement_advertisement');
    }

    public function clicks()
    {
        return $this->hasMany(AdClick::class);
    }

    public function impressions()
    {
        return $this->hasMany(AdImpression::class);
    }

    public function conversions()
    {
        return $this->hasMany(AdConversion::class);
    }

    public function targeting()
    {
        return $this->hasOne(AdTargeting::class);
    }

    public function budget()
    {
        return $this->hasOneThrough(AdBudget::class, AdCampaign::class, 'id', 'campaign_id', 'campaign_id', 'id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where('approval_status', 'approved')
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now());
    }

    public function scopePending($query)
    {
        return $query->where('approval_status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('approval_status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('approval_status', 'rejected');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeRunning($query)
    {
        return $query->where('status', 'active')
                    ->where('approval_status', 'approved')
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now());
    }

    // Accessors
    public function getImageUrlFullAttribute()
    {
        if ($this->image_url) {
            return Storage::url($this->image_url);
        }
        return null;
    }

    public function getVideoUrlFullAttribute()
    {
        if ($this->video_url) {
            if (str_contains($this->video_url, 'http')) {
                return $this->video_url;
            }
            return Storage::url($this->video_url);
        }
        return null;
    }

    public function getThumbnailUrlFullAttribute()
    {
        if ($this->thumbnail_url) {
            return Storage::url($this->thumbnail_url);
        }
        return null;
    }

    public function getIsActiveAttribute()
    {
        return $this->status === 'active' 
               && $this->approval_status === 'approved'
               && $this->start_date <= now()
               && $this->end_date >= now();
    }

    public function getDaysRemainingAttribute()
    {
        if ($this->end_date) {
            return max(0, $this->end_date->diffInDays(now()));
        }
        return 0;
    }

    public function getBudgetUtilizationAttribute()
    {
        if ($this->total_budget > 0) {
            return ($this->total_spent / $this->total_budget) * 100;
        }
        return 0;
    }

    // Methods
    public function approve($approvedBy = null)
    {
        $this->update([
            'approval_status' => 'approved',
            'status' => 'active',
            'approved_at' => now(),
            'approved_by' => $approvedBy
        ]);
    }

    public function reject($reason, $rejectedBy = null)
    {
        $this->update([
            'approval_status' => 'rejected',
            'status' => 'inactive',
            'rejected_at' => now(),
            'rejected_by' => $rejectedBy,
            'rejection_reason' => $reason
        ]);
    }

    public function pause()
    {
        $this->update(['status' => 'paused']);
    }

    public function resume()
    {
        if ($this->approval_status === 'approved') {
            $this->update(['status' => 'active']);
        }
    }

    public function canBeEdited()
    {
        return in_array($this->status, ['draft', 'paused', 'inactive']);
    }

    public function canBeDeleted()
    {
        return !in_array($this->status, ['active']);
    }

    public function incrementImpressions()
    {
        $this->increment('impressions_count');
    }

    public function incrementClicks()
    {
        $this->increment('clicks_count');
    }

    public function incrementConversions()
    {
        $this->increment('conversions_count');
    }

    public function addSpending($amount)
    {
        $this->increment('total_spent', $amount);
        $this->increment('daily_spent', $amount);

        // Update campaign budget if exists
        if ($this->campaign && $this->campaign->budget) {
            $this->campaign->budget->increment('spent_amount', $amount);
            $this->campaign->budget->increment('daily_spent', $amount);
            $this->campaign->budget->decrement('remaining_budget', $amount);
            $this->campaign->budget->decrement('daily_remaining', $amount);
        }
    }

    public function resetDailySpending()
    {
        $this->update(['daily_spent' => 0]);

        if ($this->campaign && $this->campaign->budget) {
            $this->campaign->budget->update([
                'daily_spent' => 0,
                'daily_remaining' => $this->campaign->budget->daily_budget
            ]);
        }
    }

    public function isWithinBudget()
    {
        if ($this->campaign && $this->campaign->budget) {
            return $this->campaign->budget->remaining_budget > 0 
                   && $this->campaign->budget->daily_remaining > 0;
        }
        return true;
    }

    public function getPerformanceMetrics()
    {
        $impressions = $this->impressions_count;
        $clicks = $this->clicks_count;
        $conversions = $this->conversions_count;

        return [
            'impressions' => $impressions,
            'clicks' => $clicks,
            'conversions' => $conversions,
            'ctr' => $impressions > 0 ? ($clicks / $impressions) * 100 : 0,
            'cpc' => $clicks > 0 ? $this->total_spent / $clicks : 0,
            'cpa' => $conversions > 0 ? $this->total_spent / $conversions : 0,
            'cpm' => $impressions > 0 ? ($this->total_spent / $impressions) * 1000 : 0,
            'conversion_rate' => $clicks > 0 ? ($conversions / $clicks) * 100 : 0
        ];
    }

    public function isEligibleForDisplay($request = null)
    {
        // Check basic eligibility
        if (!$this->is_active) {
            return false;
        }

        // Check budget
        if (!$this->isWithinBudget()) {
            return false;
        }

        // Check targeting criteria
        if ($this->targeting && $request) {
            return $this->targeting->isEligibleForUser($request);
        }

        return true;
    }

    public function getDisplayData()
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'type' => $this->type,
            'image_url' => $this->image_url_full,
            'video_url' => $this->video_url_full,
            'thumbnail_url' => $this->thumbnail_url_full,
            'target_url' => $this->target_url,
            'width' => $this->width,
            'height' => $this->height,
            'banner_size' => $this->banner_size,
            'video_duration' => $this->video_duration,
            'autoplay' => $this->autoplay,
            'muted' => $this->muted,
            'controls' => $this->controls,
            'loop' => $this->loop,
            'animation_type' => $this->animation_type,
            'click_tracking' => $this->click_tracking,
            'impression_tracking' => $this->impression_tracking,
            'promotion_type' => $this->promotion_type
        ];
    }
}

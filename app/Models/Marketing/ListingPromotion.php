<?php

namespace App\Models\Marketing;

use App\Models\Property\Property;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ListingPromotion extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'property_id',
        'title',
        'description',
        'promotion_type',
        'status',
        'priority_level',
        'budget',
        'currency',
        'start_date',
        'end_date',
        'target_regions',
        'target_audience',
        'promotion_channels',
        'ad_copy',
        'creative_assets',
        'bidding_strategy',
        'placement_settings',
        'optimization_goals',
        'tracking_settings',
        'boost_settings',
        'total_impressions',
        'total_clicks',
        'total_views',
        'total_inquiries',
        'conversion_rate',
        'click_through_rate',
        'cost_per_click',
        'cost_per_inquiry',
        'return_on_investment',
        'total_spent',
        'promoted_at',
        'paused_at',
        'resumed_at',
        'expired_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'target_regions' => 'array',
        'target_audience' => 'array',
        'promotion_channels' => 'array',
        'ad_copy' => 'array',
        'creative_assets' => 'array',
        'bidding_strategy' => 'array',
        'placement_settings' => 'array',
        'optimization_goals' => 'array',
        'tracking_settings' => 'array',
        'boost_settings' => 'array',
        'budget' => 'decimal:2',
        'total_spent' => 'decimal:2',
        'conversion_rate' => 'decimal:2',
        'click_through_rate' => 'decimal:2',
        'cost_per_click' => 'decimal:2',
        'cost_per_inquiry' => 'decimal:2',
        'return_on_investment' => 'decimal:2',
        'promoted_at' => 'datetime',
        'paused_at' => 'datetime',
        'resumed_at' => 'datetime',
        'expired_at' => 'datetime',
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
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePromoted($query)
    {
        return $query->where('status', 'promoted');
    }

    public function scopePaused($query)
    {
        return $query->where('status', 'paused');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    public function scopeByProperty($query, $propertyId)
    {
        return $query->where('property_id', $propertyId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('promotion_type', $type);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority_level', $priority);
    }

    public function scopeRunning($query)
    {
        return $query->where('status', 'promoted')
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now());
    }

    public function scopeHighPriority($query)
    {
        return $query->whereIn('priority_level', ['high', 'urgent']);
    }

    // Methods
    public function promote()
    {
        $this->update([
            'status' => 'promoted',
            'promoted_at' => now(),
        ]);
    }

    public function pause()
    {
        $this->update([
            'status' => 'paused',
            'paused_at' => now(),
        ]);
    }

    public function resume()
    {
        $this->update([
            'status' => 'promoted',
            'resumed_at' => now(),
        ]);
    }

    public function expire()
    {
        $this->update([
            'status' => 'expired',
            'expired_at' => now(),
        ]);
    }

    public function calculatePerformance()
    {
        if ($this->total_impressions > 0) {
            $this->click_through_rate = ($this->total_clicks / $this->total_impressions) * 100;
        }

        if ($this->total_clicks > 0) {
            $this->conversion_rate = ($this->total_inquiries / $this->total_clicks) * 100;
        }

        if ($this->total_clicks > 0) {
            $this->cost_per_click = $this->total_spent / $this->total_clicks;
        }

        if ($this->total_inquiries > 0) {
            $this->cost_per_inquiry = $this->total_spent / $this->total_inquiries;
        }

        if ($this->total_spent > 0) {
            // Mock calculation for ROI - in real implementation this would be based on actual revenue
            $revenue = $this->total_inquiries * 5000; // Assuming 5,000 per inquiry
            $this->return_on_investment = (($revenue - $this->total_spent) / $this->total_spent) * 100;
        }

        $this->save();
    }

    public function getDurationAttribute()
    {
        return $this->start_date && $this->end_date 
            ? $this->start_date->diffInDays($this->end_date) 
            : null;
    }

    public function getDaysRemainingAttribute()
    {
        return $this->end_date && $this->end_date > now()
            ? now()->diffInDays($this->end_date)
            : 0;
    }

    public function getBudgetUtilizationAttribute()
    {
        return $this->budget > 0 
            ? ($this->total_spent / $this->budget) * 100 
            : 0;
    }

    public function isOverBudget()
    {
        return $this->total_spent > $this->budget;
    }

    public function isExpired()
    {
        return $this->end_date && $this->end_date < now();
    }

    public function canBePromoted()
    {
        return in_array($this->status, ['draft', 'scheduled']) && 
               $this->start_date && 
               $this->start_date <= now();
    }

    public function getChannelListAttribute()
    {
        return implode(', ', $this->promotion_channels ?? []);
    }

    public function getRegionListAttribute()
    {
        return implode(', ', $this->target_regions ?? []);
    }

    public function getPerformanceStatusAttribute()
    {
        if ($this->click_through_rate >= 3 && $this->conversion_rate >= 5) {
            return 'excellent';
        } elseif ($this->click_through_rate >= 2 && $this->conversion_rate >= 3) {
            return 'good';
        } elseif ($this->click_through_rate >= 1 && $this->conversion_rate >= 1) {
            return 'average';
        } else {
            return 'poor';
        }
    }

    public function getPriorityColorAttribute()
    {
        return match($this->priority_level) {
            'urgent' => 'red',
            'high' => 'orange',
            'medium' => 'yellow',
            'low' => 'green',
            default => 'gray',
        };
    }

    public function getEfficiencyScoreAttribute()
    {
        // Calculate efficiency based on CTR, conversion rate, and cost metrics
        $ctrScore = min($this->click_through_rate / 5 * 100, 100); // 5% CTR = 100%
        $convScore = min($this->conversion_rate / 10 * 100, 100); // 10% conversion = 100%
        $costScore = $this->cost_per_click > 0 ? min(100 / $this->cost_per_click * 10, 100) : 100; // Lower CPC is better
        
        return round(($ctrScore + $convScore + $costScore) / 3);
    }

    public function getDailyBudgetAttribute()
    {
        $duration = $this->duration;
        return $duration > 0 ? $this->budget / $duration : 0;
    }

    public function getDailySpendAttribute()
    {
        $daysRunning = $this->promoted_at ? now()->diffInDays($this->promoted_at) + 1 : 0;
        return $daysRunning > 0 ? $this->total_spent / $daysRunning : 0;
    }

    public function isPerformingWell()
    {
        return $this->performance_status === 'excellent' || $this->performance_status === 'good';
    }

    public function needsOptimization()
    {
        return $this->performance_status === 'poor' || $this->isOverBudget();
    }

    // Events
    protected static function booted()
    {
        static::creating(function ($promotion) {
            if (auth()->check()) {
                $promotion->created_by = auth()->id();
            }
        });

        static::updating(function ($promotion) {
            if (auth()->check()) {
                $promotion->updated_by = auth()->id();
            }
        });

        static::saving(function ($promotion) {
            // Auto-expire promotions that have passed their end date
            if ($promotion->end_date && $promotion->end_date < now() && $promotion->status === 'promoted') {
                $promotion->status = 'expired';
                $promotion->expired_at = now();
            }
        });
    }
}

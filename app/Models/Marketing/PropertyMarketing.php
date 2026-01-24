<?php

namespace App\Models\Marketing;

use App\Models\Property\Property;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PropertyMarketing extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'property_id',
        'title',
        'description',
        'campaign_type',
        'status',
        'budget',
        'currency',
        'start_date',
        'end_date',
        'target_audience',
        'marketing_channels',
        'content_strategy',
        'creative_assets',
        'performance_goals',
        'tracking_settings',
        'automation_settings',
        'launch_settings',
        'total_impressions',
        'total_clicks',
        'total_conversions',
        'conversion_rate',
        'cost_per_conversion',
        'return_on_investment',
        'total_spent',
        'launched_at',
        'paused_at',
        'resumed_at',
        'completed_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'target_audience' => 'array',
        'marketing_channels' => 'array',
        'content_strategy' => 'array',
        'creative_assets' => 'array',
        'performance_goals' => 'array',
        'tracking_settings' => 'array',
        'automation_settings' => 'array',
        'launch_settings' => 'array',
        'budget' => 'decimal:2',
        'total_spent' => 'decimal:2',
        'conversion_rate' => 'decimal:2',
        'cost_per_conversion' => 'decimal:2',
        'return_on_investment' => 'decimal:2',
        'launched_at' => 'datetime',
        'paused_at' => 'datetime',
        'resumed_at' => 'datetime',
        'completed_at' => 'datetime',
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

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePaused($query)
    {
        return $query->where('status', 'paused');
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeByProperty($query, $propertyId)
    {
        return $query->where('property_id', $propertyId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('campaign_type', $type);
    }

    public function scopeRunning($query)
    {
        return $query->where('status', 'active')
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now());
    }

    // Methods
    public function launch()
    {
        $this->update([
            'status' => 'active',
            'launched_at' => now(),
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
            'status' => 'active',
            'resumed_at' => now(),
        ]);
    }

    public function complete()
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    public function calculatePerformance()
    {
        if ($this->total_clicks > 0) {
            $this->conversion_rate = ($this->total_conversions / $this->total_clicks) * 100;
        }

        if ($this->total_conversions > 0) {
            $this->cost_per_conversion = $this->total_spent / $this->total_conversions;
        }

        if ($this->total_spent > 0) {
            // Mock calculation for ROI - in real implementation this would be based on actual revenue
            $revenue = $this->total_conversions * 10000; // Assuming 10,000 per conversion
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

    public function canBeLaunched()
    {
        return in_array($this->status, ['draft', 'scheduled']) && 
               $this->start_date && 
               $this->start_date <= now();
    }

    public function getChannelListAttribute()
    {
        return implode(', ', $this->marketing_channels ?? []);
    }

    public function getPerformanceStatusAttribute()
    {
        if ($this->conversion_rate >= 5) {
            return 'excellent';
        } elseif ($this->conversion_rate >= 3) {
            return 'good';
        } elseif ($this->conversion_rate >= 1) {
            return 'average';
        } else {
            return 'poor';
        }
    }

    // Events
    protected static function booted()
    {
        static::creating(function ($marketing) {
            if (auth()->check()) {
                $marketing->created_by = auth()->id();
            }
        });

        static::updating(function ($marketing) {
            if (auth()->check()) {
                $marketing->updated_by = auth()->id();
            }
        });
    }
}

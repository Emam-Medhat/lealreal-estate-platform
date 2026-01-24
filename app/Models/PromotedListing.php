<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class PromotedListing extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'property_id',
        'user_id',
        'advertisement_id',
        'promotion_type',
        'duration',
        'start_date',
        'end_date',
        'daily_budget',
        'total_budget',
        'total_spent',
        'remaining_budget',
        'status',
        'promotion_text',
        'highlight_features',
        'call_to_action',
        'priority_level',
        'views_count',
        'clicks_count',
        'inquiries_count',
        'conversions_count',
        'featured_until',
        'spotlight_until',
        'premium_until'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'featured_until' => 'datetime',
        'spotlight_until' => 'datetime',
        'premium_until' => 'datetime',
        'daily_budget' => 'decimal:2',
        'total_budget' => 'decimal:2',
        'total_spent' => 'decimal:2',
        'remaining_budget' => 'decimal:2',
        'highlight_features' => 'array'
    ];

    protected $appends = [
        'is_active',
        'days_remaining',
        'budget_utilization',
        'performance_metrics'
    ];

    // Promotion Types
    const PROMOTION_TYPES = [
        'featured' => 'مميز',
        'premium' => 'مميز متميز',
        'spotlight' => 'في دائرة الضوء'
    ];

    // Statuses
    const STATUSES = [
        'active' => 'نشط',
        'paused' => 'موقف',
        'expired' => 'منتهي',
        'cancelled' => 'ملغي'
    ];

    // Priority Levels
    const PRIORITY_LEVELS = [
        1 => 'منخفض جداً',
        2 => 'منخفض',
        3 => 'متوسط منخفض',
        4 => 'متوسط',
        5 => 'متوسط مرتفع',
        6 => 'مرتفع',
        7 => 'مرتفع جداً',
        8 => 'أعلى',
        9 => 'أعلى جداً',
        10 => 'الأعلى'
    ];

    // Relationships
    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function advertisement()
    {
        return $this->belongsTo(Advertisement::class);
    }

    public function views()
    {
        return $this->hasMany(PromotedListingView::class);
    }

    public function inquiries()
    {
        return $this->hasMany(PromotedListingInquiry::class);
    }

    public function conversions()
    {
        return $this->hasMany(PromotedListingConversion::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now());
    }

    public function scopeFeatured($query)
    {
        return $query->where('promotion_type', 'featured')
                    ->where('featured_until', '>=', now());
    }

    public function scopePremium($query)
    {
        return $query->where('promotion_type', 'premium')
                    ->where('premium_until', '>=', now());
    }

    public function scopeSpotlight($query)
    {
        return $query->where('promotion_type', 'spotlight')
                    ->where('spotlight_until', '>=', now());
    }

    public function scopeByPriority($query, $order = 'desc')
    {
        return $query->orderBy('priority_level', $order);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Accessors
    public function getIsActiveAttribute()
    {
        return $this->status === 'active' 
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

    public function getPerformanceMetricsAttribute()
    {
        return [
            'views' => $this->views_count,
            'clicks' => $this->clicks_count,
            'inquiries' => $this->inquiries_count,
            'conversions' => $this->conversions_count,
            'ctr' => $this->views_count > 0 ? ($this->clicks_count / $this->views_count) * 100 : 0,
            'inquiry_rate' => $this->clicks_count > 0 ? ($this->inquiries_count / $this->clicks_count) * 100 : 0,
            'conversion_rate' => $this->inquiries_count > 0 ? ($this->conversions_count / $this->inquiries_count) * 100 : 0,
            'cost_per_view' => $this->views_count > 0 ? $this->total_spent / $this->views_count : 0,
            'cost_per_inquiry' => $this->inquiries_count > 0 ? $this->total_spent / $this->inquiries_count : 0,
            'cost_per_conversion' => $this->conversions_count > 0 ? $this->total_spent / $this->conversions_count : 0,
            'roi' => $this->calculateROI()
        ];
    }

    // Methods
    public function promote($type, $duration, $dailyBudget, $priority = 5)
    {
        $endDate = now()->addDays($duration);
        $totalBudget = $dailyBudget * $duration;

        $this->update([
            'promotion_type' => $type,
            'duration' => $duration,
            'start_date' => now(),
            'end_date' => $endDate,
            'daily_budget' => $dailyBudget,
            'total_budget' => $totalBudget,
            'remaining_budget' => $totalBudget,
            'priority_level' => $priority,
            'status' => 'active'
        ]);

        // Set promotion-specific dates
        switch ($type) {
            case 'featured':
                $this->update(['featured_until' => $endDate]);
                break;
            case 'premium':
                $this->update(['premium_until' => $endDate]);
                break;
            case 'spotlight':
                $this->update(['spotlight_until' => $endDate]);
                break;
        }

        return true;
    }

    public function pause()
    {
        $this->update(['status' => 'paused']);

        // Pause linked advertisement
        if ($this->advertisement) {
            $this->advertisement->pause();
        }

        return true;
    }

    public function resume()
    {
        if ($this->end_date >= now()) {
            $this->update(['status' => 'active']);

            // Resume linked advertisement
            if ($this->advertisement) {
                $this->advertisement->resume();
            }
        }

        return true;
    }

    public function extend($additionalDays, $additionalBudget = null)
    {
        $newEndDate = $this->end_date->addDays($additionalDays);
        
        $updateData = [
            'duration' => $this->duration + $additionalDays,
            'end_date' => $newEndDate
        ];

        if ($additionalBudget) {
            $updateData['total_budget'] = $this->total_budget + $additionalBudget;
            $updateData['remaining_budget'] = $this->remaining_budget + $additionalBudget;
        }

        $this->update($updateData);

        // Update promotion-specific dates
        switch ($this->promotion_type) {
            case 'featured':
                $this->update(['featured_until' => $newEndDate]);
                break;
            case 'premium':
                $this->update(['premium_until' => $newEndDate]);
                break;
            case 'spotlight':
                $this->update(['spotlight_until' => $newEndDate]);
                break;
        }

        // Extend linked advertisement
        if ($this->advertisement) {
            $this->advertisement->update([
                'end_date' => $newEndDate,
                'daily_budget' => $this->daily_budget
            ]);
        }

        return true;
    }

    public function upgrade($newType, $additionalBudget = null)
    {
        $this->update([
            'promotion_type' => $newType,
            'priority_level' => $this->getPriorityForType($newType)
        ]);

        if ($additionalBudget) {
            $this->increment('total_budget', $additionalBudget);
            $this->increment('remaining_budget', $additionalBudget);
        }

        // Update promotion-specific dates
        switch ($newType) {
            case 'featured':
                $this->update(['featured_until' => $this->end_date]);
                break;
            case 'premium':
                $this->update(['premium_until' => $this->end_date]);
                break;
            case 'spotlight':
                $this->update(['spotlight_until' => $this->end_date]);
                break;
        }

        return true;
    }

    public function trackView($request)
    {
        $this->views()->create([
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'referrer' => $request->header('referer'),
            'user_id' => $request->user()?->id
        ]);

        $this->increment('views_count');

        return true;
    }

    public function trackClick($request)
    {
        // This would be tracked by the advertisement system
        $this->increment('clicks_count');

        return true;
    }

    public function trackInquiry($request)
    {
        $inquiry = $this->inquiries()->create([
            'user_id' => $request->user()?->id,
            'contact_info' => $request->contact_info,
            'message' => $request->message,
            'inquiry_type' => $request->inquiry_type ?? 'general',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        $this->increment('inquiries_count');

        return $inquiry;
    }

    public function trackConversion($request, $conversionType = 'inquiry')
    {
        $conversion = $this->conversions()->create([
            'user_id' => $request->user()?->id,
            'conversion_type' => $conversionType,
            'conversion_value' => $request->conversion_value ?? 0,
            'conversion_data' => $request->conversion_data ?? [],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        $this->increment('conversions_count');

        return $conversion;
    }

    public function spend($amount)
    {
        if ($this->remaining_budget < $amount) {
            throw new \Exception('الميزانية غير كافية');
        }

        $this->decrement('remaining_budget', $amount);
        $this->increment('total_spent', $amount);

        // Check if promotion should expire
        if ($this->remaining_budget <= 0) {
            $this->expire();
        }

        return true;
    }

    public function expire()
    {
        $this->update([
            'status' => 'expired'
        ]);

        // Deactivate linked advertisement
        if ($this->advertisement) {
            $this->advertisement->update(['status' => 'inactive']);
        }

        return true;
    }

    public function cancel($reason = null)
    {
        $this->update([
            'status' => 'cancelled',
            'cancellation_reason' => $reason
        ]);

        // Deactivate linked advertisement
        if ($this->advertisement) {
            $this->advertisement->update(['status' => 'inactive']);
        }

        return true;
    }

    private function getPriorityForType($type)
    {
        switch ($type) {
            case 'featured':
                return 5;
            case 'premium':
                return 7;
            case 'spotlight':
                return 10;
            default:
                return 5;
        }
    }

    private function calculateROI()
    {
        // This would require actual conversion values
        // For now, we'll return a placeholder
        return 0;
    }

    public function getDailyPerformance($days = 30)
    {
        $startDate = Carbon::now()->subDays($days);
        
        return $this->views()
            ->selectRaw('DATE(created_at) as date, COUNT(*) as views')
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    public function getPromotionSummary()
    {
        return [
            'property' => $this->property,
            'promotion_type' => $this->promotion_type_label,
            'status' => $this->status_label,
            'priority_level' => $this->priority_level_label,
            'duration' => $this->duration,
            'days_remaining' => $this->days_remaining,
            'budget' => [
                'total' => $this->total_budget,
                'spent' => $this->total_spent,
                'remaining' => $this->remaining_budget,
                'utilization' => $this->budget_utilization
            ],
            'performance' => $this->performance_metrics,
            'dates' => [
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
                'featured_until' => $this->featured_until,
                'premium_until' => $this->premium_until,
                'spotlight_until' => $this->spotlight_until
            ]
        ];
    }

    public function getTypeLabel()
    {
        return self::PROMOTION_TYPES[$this->promotion_type] ?? $this->promotion_type;
    }

    public function getStatusLabel()
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function getPriorityLabel()
    {
        return self::PRIORITY_LEVELS[$this->priority_level] ?? $this->priority_level;
    }

    public function canBeEdited()
    {
        return in_array($this->status, ['active', 'paused']);
    }

    public function canBeExtended()
    {
        return in_array($this->status, ['active', 'paused']) && $this->days_remaining > 0;
    }

    public function canBeUpgraded()
    {
        return in_array($this->status, ['active', 'paused']) && $this->promotion_type !== 'spotlight';
    }

    public function isPromotionActive($type)
    {
        switch ($type) {
            case 'featured':
                return $this->featured_until && $this->featured_until >= now();
            case 'premium':
                return $this->premium_until && $this->premium_until >= now();
            case 'spotlight':
                return $this->spotlight_until && $this->spotlight_until >= now();
            default:
                return false;
        }
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserAnalytics extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'date',
        'total_users',
        'active_users',
        'new_users',
        'returning_users',
        'login_count',
        'avg_session_duration',
        'page_views',
        'unique_visitors',
        'bounce_rate',
        'conversion_rate',
        'revenue',
        'properties_viewed',
        'properties_saved',
        'leads_generated',
        'transactions_completed',
        'last_activity_at',
        'metadata',
    ];

    protected $casts = [
        'date' => 'date',
        'last_activity_at' => 'datetime',
        'metadata' => 'json',
        'revenue' => 'decimal:2',
        'avg_session_duration' => 'decimal:2',
        'bounce_rate' => 'decimal:2',
        'conversion_rate' => 'decimal:2',
    ];

    /**
     * Get the user that owns the analytics.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include analytics for a specific date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    /**
     * Scope a query to only include active users.
     */
    public function scopeActive($query)
    {
        return $query->where('active_users', '>', 0);
    }

    /**
     * Get the total revenue for the period.
     */
    public static function getTotalRevenue($startDate = null, $endDate = null)
    {
        $query = static::query();
        
        if ($startDate && $endDate) {
            $query->dateRange($startDate, $endDate);
        }
        
        return $query->sum('revenue');
    }

    /**
     * Get the average session duration.
     */
    public static function getAverageSessionDuration($startDate = null, $endDate = null)
    {
        $query = static::query();
        
        if ($startDate && $endDate) {
            $query->dateRange($startDate, $endDate);
        }
        
        return $query->avg('avg_session_duration');
    }

    /**
     * Get the conversion rate.
     */
    public static function getConversionRate($startDate = null, $endDate = null)
    {
        $query = static::query();
        
        if ($startDate && $endDate) {
            $query->dateRange($startDate, $endDate);
        }
        
        return $query->avg('conversion_rate');
    }

    /**
     * Get user growth metrics.
     */
    public static function getUserGrowth($days = 30)
    {
        $startDate = now()->subDays($days)->toDateString();
        $endDate = now()->toDateString();
        
        return static::dateRange($startDate, $endDate)
            ->orderBy('date')
            ->get(['date', 'new_users', 'active_users']);
    }

    /**
     * Get property engagement metrics.
     */
    public static function getPropertyEngagement($startDate = null, $endDate = null)
    {
        $query = static::query();
        
        if ($startDate && $endDate) {
            $query->dateRange($startDate, $endDate);
        }
        
        return [
            'properties_viewed' => $query->sum('properties_viewed'),
            'properties_saved' => $query->sum('properties_saved'),
            'leads_generated' => $query->sum('leads_generated'),
            'transactions_completed' => $query->sum('transactions_completed'),
        ];
    }

    /**
     * Update analytics data for a user event.
     */
    public static function recordEvent($userId, $event, $data = [])
    {
        $date = now()->toDateString();
        
        $analytics = static::firstOrCreate(
            ['user_id' => $userId, 'date' => $date],
            [
                'total_users' => 0,
                'active_users' => 0,
                'new_users' => 0,
                'returning_users' => 0,
                'login_count' => 0,
                'avg_session_duration' => 0,
                'page_views' => 0,
                'unique_visitors' => 0,
                'bounce_rate' => 0,
                'conversion_rate' => 0,
                'revenue' => 0,
                'properties_viewed' => 0,
                'properties_saved' => 0,
                'leads_generated' => 0,
                'transactions_completed' => 0,
                'metadata' => [],
            ]
        );

        switch ($event) {
            case 'login':
                $analytics->increment('login_count');
                $analytics->update(['last_activity_at' => now()]);
                break;
            case 'page_view':
                $analytics->increment('page_views');
                break;
            case 'property_view':
                $analytics->increment('properties_viewed');
                break;
            case 'property_save':
                $analytics->increment('properties_saved');
                break;
            case 'lead_generated':
                $analytics->increment('leads_generated');
                break;
            case 'transaction_completed':
                $analytics->increment('transactions_completed');
                $analytics->increment('revenue', $data['amount'] ?? 0);
                break;
        }

        return $analytics;
    }
}

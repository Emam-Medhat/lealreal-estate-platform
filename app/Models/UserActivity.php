<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class UserActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'ip_address',
        'user_agent',
        'method',
        'url',
        'full_url',
        'query_parameters',
        'request_data',
        'response_status',
        'response_size',
        'duration',
        'device_type',
        'browser',
        'platform',
        'location_country',
        'location_city',
        'is_mobile',
        'is_tablet',
        'is_desktop',
        'referrer',
        'landing_page',
        'exit_page',
        'page_views',
        'time_on_page',
        'bounce_rate',
        'conversion_rate',
        'activity_type',
        'activity_category',
        'activity_description',
        'tags',
        'metadata',
        'is_bot',
        'bot_name',
        'is_authenticated',
        'is_admin',
        'is_premium',
        'subscription_tier',
        'last_activity_at',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'query_parameters' => 'array',
        'request_data' => 'array',
        'metadata' => 'array',
        'tags' => 'array',
        'is_mobile' => 'boolean',
        'is_tablet' => 'boolean',
        'is_desktop' => 'boolean',
        'is_bot' => 'boolean',
        'is_authenticated' => 'boolean',
        'is_admin' => 'boolean',
        'is_premium' => 'boolean',
        'duration' => 'float',
        'response_size' => 'integer',
        'page_views' => 'integer',
        'time_on_page' => 'integer',
        'bounce_rate' => 'float',
        'conversion_rate' => 'float',
        'last_activity_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the activity.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include activities from today.
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', Carbon::today());
    }

    /**
     * Scope a query to only include activities from this week.
     */
    public function scopeThisWeek($query)
    {
        return $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
    }

    /**
     * Scope a query to only include activities from this month.
     */
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', Carbon::now()->month)
                    ->whereYear('created_at', Carbon::now()->year);
    }

    /**
     * Scope a query to only include authenticated user activities.
     */
    public function scopeAuthenticated($query)
    {
        return $query->where('is_authenticated', true);
    }

    /**
     * Scope a query to only include guest activities.
     */
    public function scopeGuest($query)
    {
        return $query->where('is_authenticated', false);
    }

    /**
     * Scope a query to only include mobile activities.
     */
    public function scopeMobile($query)
    {
        return $query->where('is_mobile', true);
    }

    /**
     * Scope a query to only include desktop activities.
     */
    public function scopeDesktop($query)
    {
        return $query->where('is_desktop', true);
    }

    /**
     * Scope a query to only include bot activities.
     */
    public function scopeBot($query)
    {
        return $query->where('is_bot', true);
    }

    /**
     * Scope a query to only include human activities.
     */
    public function scopeHuman($query)
    {
        return $query->where('is_bot', false);
    }

    /**
     * Scope a query to filter by activity type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('activity_type', $type);
    }

    /**
     * Scope a query to filter by activity category.
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('activity_category', $category);
    }

    /**
     * Get the most visited pages.
     */
    public static function getMostVisitedPages($limit = 10, $days = 30)
    {
        return self::select('url', \DB::raw('count(*) as visits'))
            ->where('created_at', '>=', Carbon::now()->subDays($days))
            ->groupBy('url')
            ->orderBy('visits', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get the most active users.
     */
    public static function getMostActiveUsers($limit = 10, $days = 30)
    {
        return self::with('user')
            ->select('user_id', \DB::raw('count(*) as activities'))
            ->where('created_at', '>=', Carbon::now()->subDays($days))
            ->whereNotNull('user_id')
            ->groupBy('user_id')
            ->orderBy('activities', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get user session duration.
     */
    public static function getSessionDuration($sessionId)
    {
        $firstActivity = self::where('session_id', $sessionId)->orderBy('created_at')->first();
        $lastActivity = self::where('session_id', $sessionId)->orderBy('created_at', 'desc')->first();

        if ($firstActivity && $lastActivity) {
            return $firstActivity->created_at->diffInSeconds($lastActivity->created_at);
        }

        return 0;
    }

    /**
     * Get bounce rate for a specific page.
     */
    public static function getBounceRate($url, $days = 30)
    {
        $totalVisits = self::where('url', $url)
            ->where('created_at', '>=', Carbon::now()->subDays($days))
            ->count();

        $singlePageVisits = self::where('url', $url)
            ->where('created_at', '>=', Carbon::now()->subDays($days))
            ->where('page_views', 1)
            ->count();

        if ($totalVisits > 0) {
            return ($singlePageVisits / $totalVisits) * 100;
        }

        return 0;
    }

    /**
     * Get device statistics.
     */
    public static function getDeviceStats($days = 30)
    {
        return [
            'mobile' => self::where('created_at', '>=', Carbon::now()->subDays($days))->where('is_mobile', true)->count(),
            'desktop' => self::where('created_at', '>=', Carbon::now()->subDays($days))->where('is_desktop', true)->count(),
            'tablet' => self::where('created_at', '>=', Carbon::now()->subDays($days))->where('is_tablet', true)->count(),
        ];
    }

    /**
     * Get browser statistics.
     */
    public static function getBrowserStats($days = 30)
    {
        return self::select('browser', \DB::raw('count(*) as count'))
            ->where('created_at', '>=', Carbon::now()->subDays($days))
            ->whereNotNull('browser')
            ->groupBy('browser')
            ->orderBy('count', 'desc')
            ->get();
    }

    /**
     * Get hourly activity distribution.
     */
    public static function getHourlyDistribution($days = 7)
    {
        return self::select(\DB::raw('HOUR(created_at) as hour'), \DB::raw('count(*) as count'))
            ->where('created_at', '>=', Carbon::now()->subDays($days))
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();
    }

    /**
     * Get daily activity trend.
     */
    public static function getDailyTrend($days = 30)
    {
        return self::select(\DB::raw('DATE(created_at) as date'), \DB::raw('count(*) as count'))
            ->where('created_at', '>=', Carbon::now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    /**
     * Check if the activity is a conversion.
     */
    public function isConversion(): bool
    {
        $conversionActions = [
            'property_submit',
            'offer_create',
            'contact_submit',
            'user_register',
            'user_subscribe',
            'property_favorite',
            'agent_contact',
        ];

        return in_array($this->activity_type, $conversionActions);
    }

    /**
     * Get the activity category color.
     */
    public function getCategoryColor(): string
    {
        $colors = [
            'property' => '#3B82F6',
            'user' => '#10B981',
            'search' => '#F59E0B',
            'admin' => '#EF4444',
            'api' => '#8B5CF6',
            'payment' => '#EC4899',
            'social' => '#14B8A6',
            'content' => '#F97316',
        ];

        return $colors[$this->activity_category] ?? '#6B7280';
    }

    /**
     * Format the duration in human readable format.
     */
    public function getFormattedDurationAttribute(): string
    {
        if ($this->duration < 1) {
            return '< 1s';
        } elseif ($this->duration < 60) {
            return round($this->duration) . 's';
        } elseif ($this->duration < 3600) {
            return round($this->duration / 60) . 'm';
        } else {
            return round($this->duration / 3600, 1) . 'h';
        }
    }

    /**
     * Get the user's current session activities.
     */
    public static function getCurrentSessionActivities($sessionId, $limit = 50)
    {
        return self::where('session_id', $sessionId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Create activity from request.
     */
    public static function createFromRequest($request, $response = null, $activityData = [])
    {
        $user = auth()->user();
        
        return self::create([
            'user_id' => $user?->id,
            'session_id' => session()->getId(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'method' => $request->method(),
            'url' => $request->path(),
            'full_url' => $request->fullUrl(),
            'query_parameters' => $request->query(),
            'request_data' => $request->except(['password', 'password_confirmation', '_token']),
            'response_status' => $response?->getStatusCode(),
            'duration' => $activityData['duration'] ?? null,
            'device_type' => $activityData['device_type'] ?? null,
            'browser' => $activityData['browser'] ?? null,
            'platform' => $activityData['platform'] ?? null,
            'location_country' => $activityData['country'] ?? null,
            'location_city' => $activityData['city'] ?? null,
            'is_mobile' => $activityData['is_mobile'] ?? false,
            'is_tablet' => $activityData['is_tablet'] ?? false,
            'is_desktop' => $activityData['is_desktop'] ?? false,
            'referrer' => $request->header('referer'),
            'activity_type' => $activityData['type'] ?? 'page_view',
            'activity_category' => $activityData['category'] ?? 'general',
            'activity_description' => $activityData['description'] ?? null,
            'metadata' => $activityData['metadata'] ?? [],
            'is_bot' => $activityData['is_bot'] ?? false,
            'bot_name' => $activityData['bot_name'] ?? null,
            'is_authenticated' => !is_null($user),
            'is_admin' => $user?->is_admin ?? false,
            'is_premium' => $user?->is_premium ?? false,
            'subscription_tier' => $user?->subscription_tier ?? null,
            'last_activity_at' => now(),
        ]);
    }
}

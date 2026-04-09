<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdImpression extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'advertisement_id',
        'user_id',
        'ip_address',
        'user_agent',
        'referrer',
        'page_url',
        'viewed_at',
        'device_type',
        'browser',
        'os',
        'location',
        'session_id',
        'impression_id',
        'is_unique',
        'is_bot',
        'is_fraud',
        'fraud_score',
        'view_duration',
        'viewport_visible',
        'ad_position',
        'page_load_time',
        'custom_data'
    ];

    protected $casts = [
        'viewed_at' => 'datetime',
        'is_unique' => 'boolean',
        'is_bot' => 'boolean',
        'is_fraud' => 'boolean',
        'fraud_score' => 'decimal:2',
        'view_duration' => 'integer',
        'viewport_visible' => 'boolean',
        'page_load_time' => 'decimal:2',
        'custom_data' => 'array'
    ];

    protected $appends = [
        'impression_quality_score'
    ];

    // Relationships
    public function advertisement()
    {
        return $this->belongsTo(Advertisement::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeUnique($query)
    {
        return $query->where('is_unique', true);
    }

    public function scopeBot($query)
    {
        return $query->where('is_bot', true);
    }

    public function scopeFraud($query)
    {
        return $query->where('is_fraud', true);
    }

    public function scopeValid($query)
    {
        return $query->where('is_bot', false)
                    ->where('is_fraud', false);
    }

    public function scopeByDevice($query, $device)
    {
        return $query->where('device_type', $device);
    }

    public function scopeByLocation($query, $location)
    {
        return $query->where('location', 'like', "%{$location}%");
    }

    public function scopeInPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('viewed_at', [$startDate, $endDate]);
    }

    public function scopeVisible($query)
    {
        return $query->where('viewport_visible', true);
    }

    // Accessors
    public function getImpressionQualityScoreAttribute()
    {
        $score = 100;
        
        // Deduct points for quality issues
        if ($this->is_bot) $score -= 100;
        if ($this->is_fraud) $score -= 100;
        if ($this->fraud_score > 50) $score -= $this->fraud_score;
        if (!$this->viewport_visible) $score -= 30;
        if ($this->view_duration < 1) $score -= 20;
        
        // Add points for quality indicators
        if ($this->is_unique) $score += 10;
        if ($this->view_duration > 5) $score += 10;
        
        return max(0, $score);
    }

    // Methods
    public static function trackImpression($advertisement, $request, $user = null)
    {
        $impressionData = [
            'advertisement_id' => $advertisement->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'referrer' => $request->header('referer'),
            'page_url' => $request->fullUrl(),
            'viewed_at' => now(),
            'device_type' => self::detectDeviceType($request->userAgent()),
            'browser' => self::detectBrowser($request->userAgent()),
            'os' => self::detectOS($request->userAgent()),
            'session_id' => $request->session()->getId(),
            'impression_id' => self::generateImpressionId(),
            'is_unique' => self::isUniqueImpression($advertisement->id, $request->ip()),
            'is_bot' => self::isBot($request->userAgent()),
            'is_fraud' => false,
            'fraud_score' => self::calculateFraudScore($request),
            'viewport_visible' => true, // Assume visible by default
            'page_load_time' => microtime(true) - LARAVEL_START
        ];

        if ($user) {
            $impressionData['user_id'] = $user->id;
        }

        $impression = self::create($impressionData);

        // Update advertisement impression count
        $advertisement->increment('impressions_count');

        // Process impression cost
        $impression->processImpressionCost();

        return $impression;
    }

    private static function detectDeviceType($userAgent)
    {
        if (preg_match('/Mobile|Android|iPhone|iPad/', $userAgent)) {
            if (preg_match('/iPad/', $userAgent)) {
                return 'tablet';
            }
            return 'mobile';
        }
        
        return 'desktop';
    }

    private static function detectBrowser($userAgent)
    {
        if (preg_match('/Chrome/', $userAgent)) return 'Chrome';
        if (preg_match('/Firefox/', $userAgent)) return 'Firefox';
        if (preg_match('/Safari/', $userAgent)) return 'Safari';
        if (preg_match('/Edge/', $userAgent)) return 'Edge';
        if (preg_match('/Opera/', $userAgent)) return 'Opera';
        
        return 'Other';
    }

    private static function detectOS($userAgent)
    {
        if (preg_match('/Windows/', $userAgent)) return 'Windows';
        if (preg_match('/Mac/', $userAgent)) return 'MacOS';
        if (preg_match('/Linux/', $userAgent)) return 'Linux';
        if (preg_match('/Android/', $userAgent)) return 'Android';
        if (preg_match('/iOS/', $userAgent)) return 'iOS';
        
        return 'Other';
    }

    private static function generateImpressionId()
    {
        return uniqid('imp_', true);
    }

    private static function isUniqueImpression($adId, $ipAddress)
    {
        $existingImpression = self::where('advertisement_id', $adId)
                                  ->where('ip_address', $ipAddress)
                                  ->where('viewed_at', '>=', now()->subHours(24))
                                  ->first();

        return !$existingImpression;
    }

    private static function isBot($userAgent)
    {
        $botPatterns = [
            'bot', 'crawl', 'spider', 'scraper', 'curl', 'wget',
            'facebookexternalhit', 'twitterbot', 'googlebot',
            'bingbot', 'slurp', 'duckduckbot', 'baiduspider'
        ];

        foreach ($botPatterns as $pattern) {
            if (stripos($userAgent, $pattern) !== false) {
                return true;
            }
        }

        return false;
    }

    private static function calculateFraudScore($request)
    {
        $score = 0;
        
        // Check for suspicious patterns
        $ipAddress = $request->ip();
        $userAgent = $request->userAgent();
        
        // High frequency impressions from same IP
        $recentImpressions = self::where('ip_address', $ipAddress)
                                ->where('viewed_at', '>=', now()->subMinutes(5))
                                ->count();
        
        if ($recentImpressions > 10) {
            $score += 30;
        }
        
        // Suspicious user agent
        if (strlen($userAgent) < 10 || strlen($userAgent) > 500) {
            $score += 20;
        }
        
        // Missing referer
        if (!$request->header('referer')) {
            $score += 10;
        }
        
        return min(100, $score);
    }

    public function processImpressionCost()
    {
        $advertisement = $this->advertisement;
        $placement = $advertisement->placements->first();
        
        if (!$placement) {
            return 0;
        }

        $cost = 0;
        
        switch ($placement->pricing_model) {
            case 'cpm':
                $cost = $placement->base_price / 1000;
                break;
            case 'cpc':
                // CPC is charged on click, not impression
                $cost = 0;
                break;
            case 'cpa':
                // CPA is charged on conversion, not impression
                $cost = 0;
                break;
        }
        
        if ($cost > 0) {
            $advertisement->addSpending($cost, $this->id, 'impression_cost');
        }
        
        return $cost;
    }

    public function updateViewDuration($duration)
    {
        $this->update(['view_duration' => $duration]);
    }

    public function markAsVisible()
    {
        $this->update(['viewport_visible' => true]);
    }

    public function markAsHidden()
    {
        $this->update(['viewport_visible' => false]);
    }

    public function markAsFraud($reason = null)
    {
        $this->update([
            'is_fraud' => true,
            'fraud_score' => 100,
            'fraud_reason' => $reason
        ]);
    }

    public static function getImpressionStats($advertisementId, $startDate = null, $endDate = null)
    {
        $query = self::where('advertisement_id', $advertisementId);
        
        if ($startDate) {
            $query->where('viewed_at', '>=', $startDate);
        }
        
        if ($endDate) {
            $query->where('viewed_at', '<=', $endDate);
        }
        
        return [
            'total_impressions' => $query->count(),
            'unique_impressions' => $query->unique()->count(),
            'valid_impressions' => $query->valid()->count(),
            'bot_impressions' => $query->bot()->count(),
            'fraud_impressions' => $query->fraud()->count(),
            'visible_impressions' => $query->visible()->count(),
            'average_view_duration' => $query->avg('view_duration') ?? 0,
            'impressions_by_device' => $query->groupBy('device_type')->selectRaw('device_type, COUNT(*) as count')->get(),
            'impressions_by_browser' => $query->groupBy('browser')->selectRaw('browser, COUNT(*) as count')->get(),
            'impressions_by_location' => $query->whereNotNull('location')->groupBy('location')->selectRaw('location, COUNT(*) as count')->limit(10)->get()
        ];
    }

    public static function getHourlyImpressions($advertisementId, $date)
    {
        return self::where('advertisement_id', $advertisementId)
                   ->whereDate('viewed_at', $date)
                   ->selectRaw('HOUR(viewed_at) as hour, COUNT(*) as impressions')
                   ->groupBy('hour')
                   ->orderBy('hour')
                   ->get();
    }

    public static function getDailyImpressions($advertisementId, $startDate, $endDate)
    {
        return self::where('advertisement_id', $advertisementId)
                   ->whereBetween('viewed_at', [$startDate, $endDate])
                   ->selectRaw('DATE(viewed_at) as date, COUNT(*) as impressions')
                   ->groupBy('date')
                   ->orderBy('date')
                   ->get();
    }

    public static function getTopPerformingPages($advertisementId, $limit = 10)
    {
        return self::where('advertisement_id', $advertisementId)
                   ->selectRaw('page_url, COUNT(*) as impressions')
                   ->groupBy('page_url')
                   ->orderBy('impressions', 'desc')
                   ->limit($limit)
                   ->get();
    }

    public static function detectInvalidTraffic($advertisementId, $hours = 24)
    {
        $since = now()->subHours($hours);
        
        // Get IPs with high impression counts
        $suspiciousIPs = self::where('advertisement_id', $advertisementId)
                           ->where('viewed_at', '>=', $since)
                           ->groupBy('ip_address')
                           ->havingRaw('COUNT(*) > 50')
                           ->pluck('ip_address');
        
        // Mark impressions from suspicious IPs as fraud
        if ($suspiciousIPs->isNotEmpty()) {
            self::where('advertisement_id', $advertisementId)
               ->where('viewed_at', '>=', $since)
               ->whereIn('ip_address', $suspiciousIPs)
               ->update(['is_fraud' => true, 'fraud_score' => 100]);
        }
        
        return $suspiciousIPs->count();
    }
}

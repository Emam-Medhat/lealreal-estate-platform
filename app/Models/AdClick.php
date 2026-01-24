<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdClick extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'advertisement_id',
        'user_id',
        'ip_address',
        'user_agent',
        'referrer',
        'page_url',
        'clicked_at',
        'conversion_time',
        'device_type',
        'browser',
        'os',
        'location',
        'session_id',
        'click_id',
        'is_unique',
        'is_bot',
        'is_fraud',
        'fraud_score',
        'click_value',
        'conversion_value',
        'attribution_model',
        'attribution_window',
        'custom_data'
    ];

    protected $casts = [
        'clicked_at' => 'datetime',
        'conversion_time' => 'datetime',
        'is_unique' => 'boolean',
        'is_bot' => 'boolean',
        'is_fraud' => 'boolean',
        'fraud_score' => 'decimal:2',
        'click_value' => 'decimal:2',
        'conversion_value' => 'decimal:2',
        'attribution_window' => 'integer',
        'custom_data' => 'array'
    ];

    protected $appends = [
        'time_to_conversion',
        'click_quality_score'
    ];

    // Attribution Models
    const ATTRIBUTION_MODELS = [
        'first_click' => 'النقرة الأولى',
        'last_click' => 'النقرة الأخيرة',
        'linear' => 'خطي',
        'time_decay' => 'تدهور زمني',
        'position_based' => 'موضعي'
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

    public function conversions()
    {
        return $this->hasMany(AdConversion::class);
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
        return $query->whereBetween('clicked_at', [$startDate, $endDate]);
    }

    public function scopeWithConversion($query)
    {
        return $query->whereHas('conversions');
    }

    // Accessors
    public function getTimeToConversionAttribute()
    {
        $conversion = $this->conversions()->first();
        
        if ($conversion && $this->clicked_at) {
            return $this->clicked_at->diffInSeconds($conversion->converted_at);
        }
        
        return null;
    }

    public function getClickQualityScoreAttribute()
    {
        $score = 100;
        
        // Deduct points for potential fraud indicators
        if ($this->is_bot) $score -= 100;
        if ($this->is_fraud) $score -= 100;
        if ($this->fraud_score > 50) $score -= $this->fraud_score;
        
        // Add points for quality indicators
        if ($this->is_unique) $score += 10;
        if ($this->conversion_value > 0) $score += 20;
        
        return max(0, $score);
    }

    // Methods
    public static function trackClick($advertisement, $request, $user = null)
    {
        $clickData = [
            'advertisement_id' => $advertisement->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'referrer' => $request->header('referer'),
            'page_url' => $request->fullUrl(),
            'clicked_at' => now(),
            'device_type' => self::detectDeviceType($request->userAgent()),
            'browser' => self::detectBrowser($request->userAgent()),
            'os' => self::detectOS($request->userAgent()),
            'session_id' => $request->session()->getId(),
            'click_id' => self::generateClickId(),
            'is_unique' => self::isUniqueClick($advertisement->id, $request->ip()),
            'is_bot' => self::isBot($request->userAgent()),
            'is_fraud' => false,
            'fraud_score' => self::calculateFraudScore($request),
            'attribution_model' => 'last_click',
            'attribution_window' => 30 // days
        ];

        if ($user) {
            $clickData['user_id'] = $user->id;
        }

        $click = self::create($clickData);

        // Update advertisement click count
        $advertisement->increment('clicks_count');

        // Process click cost
        $click->processClickCost();

        return $click;
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

    private static function generateClickId()
    {
        return uniqid('click_', true);
    }

    private static function isUniqueClick($adId, $ipAddress)
    {
        $existingClick = self::where('advertisement_id', $adId)
                            ->where('ip_address', $ipAddress)
                            ->where('clicked_at', '>=', now()->subHours(24))
                            ->first();

        return !$existingClick;
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
        $referer = $request->header('referer');
        
        // High frequency clicks from same IP
        $recentClicks = self::where('ip_address', $ipAddress)
                           ->where('clicked_at', '>=', now()->subMinutes(5))
                           ->count();
        
        if ($recentClicks > .0);_criteria) {
 
            $ .
            'criteria' => [
                'audience' => $this->audience_criteria,
                'location' => $this->location_criteria,
                'age_range' => $this->age_range,
                'gender' => $this->gender_criteria,
                'interest' => $this->interest_criteria,
                'behavior' => $this->behavior_criteria,
                'device' => $this->device_criteria,
                'time' => $this->time_criteria,
                'language' => $this->language_criteria,
                'income' => $this->income_criteria,
                'education' => $this->education_criteria,
                'custom' => $this->custom_criteria,
                'exclusion' => $this->exclusion_criteria
            ],
            'metrics' => [
                'estimated_reach' => $this->estimated_reach,
                'actual_reach' => $this->actual_reach,
                'match_rate' => $this->match_rate,
                'targeting_score' => $this->targeting_score
            ]
        ];
    }
}

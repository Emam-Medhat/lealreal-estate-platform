<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdConversion extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'advertisement_id',
        'click_id',
        'user_id',
        'conversion_type',
        'conversion_value',
        'conversion_currency',
        'converted_at',
        'ip_address',
        'user_agent',
        'page_url',
        'conversion_data',
        'attribution_model',
        'attribution_window',
        'attribution_score',
        'is_verified',
        'verification_method',
        'verified_at',
        'revenue',
        'cost',
        'profit',
        'custom_parameters',
        'tracking_id',
        'order_id',
        'product_id',
        'quantity',
        'discount_code',
        'payment_method'
    ];

    protected $casts = [
        'converted_at' => 'datetime',
        'verified_at' => 'datetime',
        'conversion_value' => 'decimal:2',
        'revenue' => 'decimal:2',
        'cost' => 'decimal:2',
        'profit' => 'decimal:2',
        'attribution_score' => 'decimal:2',
        'is_verified' => 'boolean',
        'conversion_data' => 'array',
        'custom_parameters' => 'array',
        'quantity' => 'integer'
    ];

    protected $appends = [
        'time_to_conversion',
        'conversion_roi',
        'attribution_label'
    ];

    // Conversion Types
    const CONVERSION_TYPES = [
        'lead' => 'عميل محتمل',
        'sale' => 'بيع',
        'signup' => 'تسجيل',
        'download' => 'تحميل',
        'inquiry' => 'استفسار',
        'booking' => 'حجز',
        'call' => 'مكالمة',
        'form_submission' => 'تقديم نموذج',
        'newsletter_signup' => 'اشتراك النشرة',
        'property_view' => 'مشاهدة عقار',
        'property_inquiry' => 'استفسار عقار',
        'property_booking' => 'حجز عقار'
    ];

    // Attribution Models
    const ATTRIBUTION_MODELS = [
        'first_click' => 'النقرة الأولى',
        'last_click' => 'النقرة الأخيرة',
        'linear' => 'خطي',
        'time_decay' => 'تدهور زمني',
        'position_based' => 'موضعي',
        'data_driven' => 'مدفوع بالبيانات'
    ];

    // Verification Methods
    const VERIFICATION_METHODS = [
        'pixel' => 'بكسل التتبع',
        'postback' => 'رد الاتصال',
        'api' => 'API',
        'manual' => 'يدوي',
        'server_to_server' => 'خادم لخادم'
    ];

    // Relationships
    public function advertisement()
    {
        return $this->belongsTo(Advertisement::class);
    }

    public function click()
    {
        return $this->belongsTo(AdClick::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeUnverified($query)
    {
        return $query->where('is_verified', false);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('conversion_type', $type);
    }

    public function scopeByValue($query, $minValue = null, $maxValue = null)
    {
        if ($minValue) {
            $query->where('conversion_value', '>=', $minValue);
        }
        if ($maxValue) {
            $query->where('conversion_value', '<=', $maxValue);
        }
        return $query;
    }

    public function scopeInPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('converted_at', [$startDate, $endDate]);
    }

    public function scopeWithRevenue($query)
    {
        return $query->where('revenue', '>', 0);
    }

    // Accessors
    public function getTimeToConversionAttribute()
    {
        if ($this->click && $this->click->clicked_at) {
            return $this->click->clicked_at->diffInSeconds($this->converted_at);
        }
        return null;
    }

    public function getConversionRoiAttribute()
    {
        if ($this->cost > 0) {
            return (($this->revenue - $this->cost) / $this->cost) * 100;
        }
        return 0;
    }

    public function getAttributionLabelAttribute()
    {
        return self::ATTRIBUTION_MODELS[$this->attribution_model] ?? $this->attribution_model;
    }

    // Methods
    public static function trackConversion($advertisement, $conversionData, $click = null, $user = null)
    {
        $data = [
            'advertisement_id' => $advertisement->id,
            'click_id' => $click?->id,
            'user_id' => $user?->id,
            'conversion_type' => $conversionData['type'] ?? 'lead',
            'conversion_value' => $conversionData['value'] ?? 0,
            'conversion_currency' => $conversionData['currency'] ?? 'SAR',
            'converted_at' => now(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'page_url' => request()->fullUrl(),
            'conversion_data' => $conversionData['data'] ?? [],
            'attribution_model' => $conversionData['attribution_model'] ?? 'last_click',
            'attribution_window' => $conversionData['attribution_window'] ?? 30,
            'attribution_score' => $conversionData['attribution_score'] ?? 100,
            'is_verified' => false,
            'revenue' => $conversionData['revenue'] ?? 0,
            'cost' => $conversionData['cost'] ?? 0,
            'custom_parameters' => $conversionData['custom_parameters'] ?? [],
            'tracking_id' => $conversionData['tracking_id'] ?? null,
            'order_id' => $conversionData['order_id'] ?? null,
            'product_id' => $conversionData['product_id'] ?? null,
            'quantity' => $conversionData['quantity'] ?? 1,
            'discount_code' => $conversionData['discount_code'] ?? null,
            'payment_method' => $conversionData['payment_method'] ?? null
        ];

        $conversion = self::create($data);

        // Calculate profit
        $conversion->update([
            'profit' => $conversion->revenue - $conversion->cost
        ]);

        // Update advertisement conversion count
        $advertisement->increment('conversions_count');

        // Process conversion cost
        $conversion->processConversionCost();

        return $conversion;
    }

    public function verify($method = 'manual', $verifiedBy = null)
    {
        $this->update([
            'is_verified' => true,
            'verification_method' => $method,
            'verified_at' => now(),
            'verified_by' => $verifiedBy
        ]);

        return true;
    }

    public function reject($reason = null)
    {
        $this->update([
            'is_verified' => false,
            'rejection_reason' => $reason,
            'verified_at' => now()
        ]);

        return true;
    }

    public function processConversionCost()
    {
        $advertisement = $this->advertisement;
        $placement = $advertisement->placements->first();
        
        if (!$placement) {
            return 0;
        }

        $cost = 0;
        
        switch ($placement->pricing_model) {
            case 'cpm':
                // CPM is charged on impression, not conversion
                $cost = 0;
                break;
            case 'cpc':
                // CPC is charged on click, not conversion
                $cost = 0;
                break;
            case 'cpa':
                $cost = $placement->base_price;
                break;
        }
        
        if ($cost > 0) {
            $this->update(['cost' => $cost]);
            $advertisement->addSpending($cost, $this->id, 'conversion_cost');
        }
        
        return $cost;
    }

    public function calculateAttributionScore()
    {
        $score = 100;
        
        switch ($this->attribution_model) {
            case 'first_click':
                $score = 100;
                break;
            case 'last_click':
                $score = 100;
                break;
            case 'linear':
                $score = 50;
                break;
            case 'time_decay':
                // Calculate based on time since click
                if ($this->time_to_conversion) {
                    $hours = $this->time_to_conversion / 3600;
                    $score = max(10, 100 - ($hours * 2));
                }
                break;
            case 'position_based':
                $score = 80;
                break;
            case 'data_driven':
                // Would require ML model
                $score = 75;
                break;
        }
        
        $this->update(['attribution_score' => $score]);
        
        return $score;
    }

    public function updateRevenue($newRevenue)
    {
        $this->update([
            'revenue' => $newRevenue,
            'profit' => $newRevenue - $this->cost
        ]);
    }

    public static function getConversionStats($advertisementId, $startDate = null, $endDate = null)
    {
        $query = self::where('advertisement_id', $advertisementId);
        
        if ($startDate) {
            $query->where('converted_at', '>=', $startDate);
        }
        
        if ($endDate) {
            $query->where('converted_at', '<=', $endDate);
        }
        
        return [
            'total_conversions' => $query->count(),
            'verified_conversions' => $query->verified()->count(),
            'total_value' => $query->sum('conversion_value'),
            'total_revenue' => $query->sum('revenue'),
            'total_cost' => $query->sum('cost'),
            'total_profit' => $query->sum('profit'),
            'average_value' => $query->avg('conversion_value') ?? 0,
            'average_cost' => $query->avg('cost') ?? 0,
            'average_profit' => $query->avg('profit') ?? 0,
            'conversion_rate' => self::calculateConversionRate($advertisementId, $startDate, $endDate),
            'cost_per_conversion' => self::calculateCostPerConversion($advertisementId, $startDate, $endDate),
            'revenue_per_conversion' => self::calculateRevenuePerConversion($advertisementId, $startDate, $endDate),
            'conversions_by_type' => $query->groupBy('conversion_type')->selectRaw('conversion_type, COUNT(*) as count')->get(),
            'conversions_by_value' => self::getValueDistribution($advertisementId, $startDate, $endDate),
            'attribution_breakdown' => $query->groupBy('attribution_model')->selectRaw('attribution_model, COUNT(*) as count, SUM(conversion_value) as total_value')->get()
        ];
    }

    private static function calculateConversionRate($advertisementId, $startDate = null, $endDate = null)
    {
        $clicksQuery = AdClick::where('advertisement_id', $advertisementId);
        $conversionsQuery = self::where('advertisement_id', $advertisementId);
        
        if ($startDate) {
            $clicksQuery->where('clicked_at', '>=', $startDate);
            $conversionsQuery->where('converted_at', '>=', $startDate);
        }
        
        if ($endDate) {
            $clicksQuery->where('clicked_at', '<=', $endDate);
            $conversionsQuery->where('converted_at', '<=', $endDate);
        }
        
        $clicks = $clicksQuery->count();
        $conversions = $conversionsQuery->count();
        
        return $clicks > 0 ? ($conversions / $clicks) * 100 : 0;
    }

    private static function calculateCostPerConversion($advertisementId, $startDate = null, $endDate = null)
    {
        $query = self::where('advertisement_id', $advertisementId);
        
        if ($startDate) {
            $query->where('converted_at', '>=', $startDate);
        }
        
        if ($endDate) {
            $query->where('converted_at', '<=', $endDate);
        }
        
        $totalCost = $query->sum('cost');
        $conversions = $query->count();
        
        return $conversions > 0 ? $totalCost / $conversions : 0;
    }

    private static function calculateRevenuePerConversion($advertisementId, $startDate = null, $endDate = null)
    {
        $query = self::where('advertisement_id', $advertisementId);
        
        if ($startDate) {
            $query->where('converted_at', '>=', $startDate);
        }
        
        if ($endDate) {
            $query->where('converted_at', '<=', $endDate);
        }
        
        $totalRevenue = $query->sum('revenue');
        $conversions = $query->count();
        
        return $conversions > 0 ? $totalRevenue / $conversions : 0;
    }

    private static function getValueDistribution($advertisementId, $startDate = null, $endDate = null)
    {
        $query = self::where('advertisement_id', $advertisementId);
        
        if ($startDate) {
            $query->where('converted_at', '>=', $startDate);
        }
        
        if ($endDate) {
            $query->where('converted_at', '<=', $endDate);
        }
        
        return [
            'low_value' => $query->where('conversion_value', '<', 100)->count(),
            'medium_value' => $query->whereBetween('conversion_value', [100, 500])->count(),
            'high_value' => $query->where('conversion_value', '>', 500)->count()
        ];
    }

    public static function getConversionPath($advertisementId, $limit = 10)
    {
        return self::with(['click'])
                   ->where('advertisement_id', $advertisementId)
                   ->whereNotNull('click_id')
                   ->selectRaw('click_id, COUNT(*) as conversions, AVG(conversion_value) as avg_value')
                   ->groupBy('click_id')
                   ->orderBy('conversions', 'desc')
                   ->limit($limit)
                   ->get();
    }

    public function getTypeLabel()
    {
        return self::CONVERSION_TYPES[$this->conversion_type] ?? $this->conversion_type;
    }

    public function getVerificationMethodLabel()
    {
        return self::VERIFICATION_METHODS[$this->verification_method] ?? $this->verification_method;
    }
}

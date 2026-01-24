<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdPlacement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'type',
        'position',
        'width',
        'height',
        'max_ads',
        'pricing_model',
        'base_price',
        'min_bid',
        'target_pages',
        'excluded_pages',
        'device_types',
        'is_active',
        'auto_rotate',
        'rotation_interval',
        'show_on_mobile',
        'show_on_desktop',
        'show_on_tablet',
        'custom_css',
        'custom_js',
        'tracking_enabled',
        'frequency_capping',
        'max_impressions_per_user',
        'max_clicks_per_user',
        'time_between_impressions',
        'time_between_clicks'
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'min_bid' => 'decimal:2',
        'is_active' => 'boolean',
        'auto_rotate' => 'boolean',
        'show_on_mobile' => 'boolean',
        'show_on_desktop' => 'boolean',
        'show_on_tablet' => 'boolean',
        'tracking_enabled' => 'boolean',
        'frequency_capping' => 'boolean',
        'rotation_interval' => 'integer',
        'max_impressions_per_user' => 'integer',
        'max_clicks_per_user' => 'integer',
        'time_between_impressions' => 'integer',
        'time_between_clicks' => 'integer'
    ];

    protected $appends = [
        'type_label',
        'pricing_model_label',
        'dimensions',
        'is_available'
    ];

    // Placement Types
    const TYPES = [
        'header' => 'رأس الصفحة',
        'sidebar' => 'الشريط الجانبي',
        'content' => 'المحتوى',
        'footer' => 'ذيل الصفحة',
        'popup' => 'نافذة منبثقة',
        'mobile' => 'موبايل'
    ];

    // Pricing Models
    const PRICING_MODELS = [
        'cpm' => 'CPM (التكلفة لكل ألف ظهور)',
        'cpc' => 'CPC (التكلفة لكل نقرة)',
        'cpa' => 'CPA (التكلفة لكل تحويل)'
    ];

    // Relationships
    public function ads()
    {
        return $this->belongsToMany(Advertisement::class, 'ad_placement_advertisement');
    }

    public function impressions()
    {
        return $this->hasManyThrough(
            AdImpression::class,
            Advertisement::class,
            'id',
            'advertisement_id',
            'id',
            'id'
        );
    }

    public function clicks()
    {
        return $this->hasManyThrough(
            AdClick::class,
            Advertisement::class,
            'id',
            'advertisement_id',
            'id',
            'id'
        );
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByPricingModel($query, $model)
    {
        return $query->where('pricing_model', $model);
    }

    public function scopeForDevice($query, $device)
    {
        return $query->where("show_on_{$device}", true);
    }

    // Accessors
    public function getTypeLabelAttribute()
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    public function getPricingModelLabelAttribute()
    {
        return self::PRICING_MODELS[$this->pricing_model] ?? $this->pricing_model;
    }

    public function getDimensionsAttribute()
    {
        return "{$this->width}x{$this->height}";
    }

    public function getIsAvailableAttribute()
    {
        return $this->is_active && $this->ads()->active()->count() < $this->max_ads;
    }

    // Methods
    public function getEligibleAds($request = null)
    {
        $query = $this->ads()->active();

        // Filter by device type
        if ($request && $device = $this->detectDevice($request)) {
            if (!$this->{"show_on_{$device}"}) {
                return collect();
            }
        }

        // Filter by page
        if ($request && $pageUrl = $request->header('referer')) {
            if ($this->isPageExcluded($pageUrl)) {
                return collect();
            }
        }

        // Apply frequency capping
        if ($this->frequency_capping && $request) {
            $query = $this->applyFrequencyCapping($query, $request);
        }

        return $query->get();
    }

    public function getRotationAds()
    {
        if (!$this->auto_rotate) {
            return $this->getEligibleAds()->take($this->max_ads);
        }

        return $this->getRotatedAds();
    }

    private function getRotatedAds()
    {
        $eligibleAds = $this->getEligibleAds();
        
        if ($eligibleAds->isEmpty()) {
            return collect();
        }

        // Implement rotation logic based on performance or budget
        return $eligibleAds->sortByDesc(function($ad) {
            return $ad->daily_budget;
        })->take($this->max_ads);
    }

    private function detectDevice($request)
    {
        $userAgent = $request->userAgent();
        
        if (preg_match('/Mobile|Android|iPhone|iPad/', $userAgent)) {
            if (preg_match('/iPad/', $userAgent)) {
                return 'tablet';
            }
            return 'mobile';
        }
        
        return 'desktop';
    }

    private function isPageExcluded($pageUrl)
    {
        if (empty($this->excluded_pages)) {
            return false;
        }

        foreach ($this->excluded_pages as $excludedPage) {
            if (str_contains($pageUrl, $excludedPage)) {
                return true;
            }
        }

        return false;
    }

    private function applyFrequencyCapping($query, $request)
    {
        $ip = $request->ip();
        $userAgent = $request->userAgent();

        // Check impression frequency
        if ($this->max_impressions_per_user > 0) {
            $recentImpressions = $this->impressions()
                ->where('ip_address', $ip)
                ->where('user_agent', $userAgent)
                ->where('viewed_at', '>=', now()->subMinutes($this->time_between_impressions ?? 60))
                ->count();

            if ($recentImpressions >= $this->max_impressions_per_user) {
                return $query->whereRaw('1 = 0'); // Return empty query
            }
        }

        // Check click frequency
        if ($this->max_clicks_per_user > 0) {
            $recentClicks = $this->clicks()
                ->where('ip_address', $pz)
               ');
                ->wheregary($this->time_between_clicks ?? 300))
                ->count();

            ifour($recent大学教授 >= $ferentiation-> rains) {
 '1 = .
        }

éro($queryExecutable->whereRaw
    }

    public function getPerformanceMetrics($startDate = null)
    {
        if (!$startDate) {
            $startDate = now()->subDays(30);
        }

        $impressions = $this->  ->where('viewed: '>=', $
        $click忍($startDate)->countOSC();
        $, $this->No->where('ioni >=', $ hasey)->count();
        $revenue = $this->calculateRevenue($impressions, $clicks);

        return [
            'impressions' => $impressions,
            'clicks' => $clicks,
            'ctr' => $impressions > 0 ? ($clicks / $impressions) * 100 : 0,
            'revenue' => $revenue,
            'ecpm' => $impressions > 0 ? ($revenue / $impressions) * 1000 : 0,
            'cpc' => $clicks > 0 ? $revenue / $clicks : 0,
            'fill_rate' => $this->calculateFillRate(),
            'active_ads' => $this->ads()->active()->count()
        ];
    }

    public function calculateRevenue($impressions, $clicks)
    {
        switch ($this->pricing_model) {
            case 'cpm':
                return ($impressions / 1000) * $this->base_price;
            case 'cpc':
                return $clicks * $this->base_price;
            case 'cpa':
                // CPA would require conversion tracking
                return 0;
            default:
                return 0;
        }
    }

    public function calculateFillRate()
    {
        $totalSlots = $this->max_ads;
        $activeAds = $this->ads()->active()->count();
        
        return $totalSlots > 0 ? ($activeAds / $totalSlots) * 100 : 0;
    }

    public function getRecommendedPricing()
    {
        $performance = $this->getPerformanceMetrics();
        $fillRate = $this->calculateFillRate();

        $recommendations = [];

        if ($fillRate < 50 && $this->base_price > $this->min_bid) {
            $recommendations[] = [
                'type' => 'price_decrease',
                'suggestion' => 'تخفيض السعر لزيادة نسبة الملء',
                'new_price' => max($this->min_bid, $this->base_price * 0.8)
            ];
        }

        if ($fillRate > 90 && $performance['ecpm'] < 5) {
            $recommendations[] = [
                'type' => 'price_increase',
                'suggestion' => 'زيادة السعر لتحقيق إيرادات أفضل',
                'new_price' => $this->base_price * 1.2
            ];
        }

        if ($performance['ctr'] < 1) {
            $recommendations[] = [
                'type' => 'placement_optimization',
                'suggestion' => 'تحسين موضع الإعلان لزيادة نسبة النقر',
                'details' => 'نظر في تغيير الموضع أو الحجم'
            ];
        }

        return $recommendations;
    }

    public function generateEmbedCode()
    {
        $placementId = $this->id;
        $baseUrl = url('/');
        $apiUrl = route('placements.get-ads', $this->id);

        return "
<div id='ad-placement-{$placementId}' class='ad-placement' data-placement-id='{$placementId}'>
    <!-- Ads will be loaded here -->
</div>

<script>
(function() {
    const placementElement = document.getElementById('ad-placement-{$placementId}');
    
    // Load ads for this placement
    fetch('{$apiUrl}', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.ads && data.ads.length > 0) {
            data.ads.forEach(ad => {
                const adElement = createAdElement(ad);
                placementElement.appendChild(adElement);
            });
        }
    })
    .catch(error => {
        console.error('Error loading ads:', error);
    });
    
    function createAdElement(ad) {
        const div = document.createElement('div');
        div.className = 'ad-item';
        div.dataset.adId = ad.id;
        
        if (ad.type === 'banner') {
            const img = document.createElement('img');
            img.src = ad.image_url;
            img.alt = ad.title;
            img.style.width = '100%';
            img.style.height = 'auto';
            
            const link = document.createElement('a');
            link.href = ad.target_url;
            link.target = '_blank';
            link.appendChild(img);
            
            div.appendChild(link);
        } else if (ad.type === 'video') {
            const video = document.createElement('video');
            video.src = ad.video_url;
            video.poster = ad.thumbnail_url;
            video.controls = true;
            video.style.width = '100%';
            video.style.height = 'auto';
            
            const link = document.createElement('a');
            link.href = ad.target_url;
            link.target = '_blank';
            link.appendChild(video);
            
            div.appendChild(link);
        }
        
        // Track impression
        if (ad.track_url) {
            fetch(ad.track_url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').getAttribute('content')
                }
            });
        }
        
        return div;
    }
})();
</script>

<style>
.ad-placement {
    width: {$this->width}px;
    height: {$this->height}px;
    overflow: hidden;
}

.ad-item {
    width: 100%;
    height: 100%;
}

.ad-item img,
.ad-item video {
    object-fit: cover;
}
</style>";
    }

    public function canAcceptAd($ad)
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->ads()->active()->count() >= $this->max_ads) {
            return false;
        }

        // Check ad compatibility with placement
        if ($this->type === 'header' && $ad->type === 'popup') {
            return false;
        }

        // Check size compatibility
        if ($ad->width && $ad->height) {
            if ($ad->width > $this->width || $ad->height > $this->height) {
                return false;
            }
        }

        return true;
    }

    public function getPlacementSummary()
    {
        return [
            'name' => $this->name,
            'type' => $this->type_label,
            'dimensions' => $this->dimensions,
            'pricing_model' => $this->pricing_model_label,
            'base_price' => $this->base_price,
            'max_ads' => $this->max_ads,
            'active_ads' => $this->ads()->active()->count(),
            'fill_rate' => $this->calculateFillRate(),
            'is_available' => $this->is_available,
            'performance' => $this->getPerformanceMetrics()
        ];
    }
}

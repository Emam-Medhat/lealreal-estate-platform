<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;

class AdTargeting extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'advertisement_id',
        'campaign_id',
        'target_type',
        'audience_criteria',
        'location_criteria',
        'age_range',
        'gender_criteria',
        'interest_criteria',
        'behavior_criteria',
        'device_criteria',
        'time_criteria',
        'language_criteria',
        'income_criteria',
        'education_criteria',
        'custom_criteria',
        'exclusion_criteria',
        'targeting_score',
        'estimated_reach',
        'actual_reach',
        'match_rate',
        'performance_data'
    ];

    protected $casts = [
        'audience_criteria' => 'array',
        'location_criteria' => 'array',
        'age_range' => 'array',
        'gender_criteria' => 'array',
        'interest_criteria' => 'array',
        'behavior_criteria' => 'array',
        'device_criteria' => 'array',
        'time_criteria' => 'array',
        'language_criteria' => 'array',
        'income_criteria' => 'array',
        'education_criteria' => 'array',
        'custom_criteria' => 'array',
        'exclusion_criteria' => 'array',
        'performance_data' => 'array',
        'targeting_score' => 'decimal:2',
        'estimated_reach' => 'integer',
        'actual_reach' => 'integer',
        'match_rate' => 'decimal:2'
    ];

    protected $appends = [
        'targeting_summary',
        'reach_efficiency',
        'targeting_effectiveness'
    ];

    // Target Types
    const TARGET_TYPES = [
        'advertisement' => 'إعلان',
        'campaign' => 'حملة'
    ];

    // Gender Options
    const GENDER_OPTIONS = [
        'male' => 'ذكر',
        'female' => 'أنثى',
        'other' => 'آخر'
    ];

    // Device Types
    const DEVICE_TYPES = [
        'desktop' => 'جهاز مكتبي',
        'mobile' => 'موبايل',
        'tablet' => 'تابلت'
    ];

    // Income Levels
    const INCOME_LEVELS = [
        'low' => 'منخفض',
        'medium' => 'متوسط',
        'high' => 'مرتفع',
        'very_high' => 'مرتفع جداً'
    ];

    // Education Levels
    const EDUCATION_LEVELS = [
        'high_school' => 'ثانوية',
        'bachelor' => 'بكالوريوس',
        'master' => 'ماجستير',
        'phd' => 'دكتوراه'
    ];

    // Relationships
    public function advertisement()
    {
        return $this->belongsTo(Advertisement::class);
    }

    public function campaign()
    {
        return $this->belongsTo(AdCampaign::class);
    }

    public function performanceLogs()
    {
        return $this->hasMany(TargetingPerformanceLog::class);
    }

    // Scopes
    public function scopeForAdvertisement($query, $adId)
    {
        return $query->where('advertisement_id', $adId);
    }

    public function scopeForCampaign($query, $campaignId)
    {
        return $query->where('campaign_id', $campaignId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('target_type', $type);
    }

    // Accessors
    public function getTargetingSummaryAttribute()
    {
        $summary = [];
        
        if (!empty($this->location_criteria)) {
            $summary[] = count($this->location_criteria) . ' مواقع';
        }
        
        if (!empty($this->age_range)) {
            $summary[] = 'العمر: ' . $this->age_range[0] . '-' . $this->age_range[1];
        }
        
        if (!empty($this->gender_criteria)) {
            $summary[] = count($this->gender_criteria) . ' جنس';
        }
        
        if (!empty($this->interest_criteria)) {
            $summary[] = count($this->interest_criteria) . ' اهتمام';
        }
        
        if (!empty($this->device_criteria)) {
            $summary[] = count($this->device_criteria) . ' جهاز';
        }
        
        return implode(' | ', $summary);
    }

    public function getReachEfficiencyAttribute()
    {
        if ($this->estimated_reach > 0) {
            return ($this->actual_reach / $this->estimated_reach) * 100;
        }
        return 0;
    }

    public function getTargetingEffectivenessAttribute()
    {
        $performance = $this->performance_data ?? [];
        
        $ctr = $performance['ctr'] ?? 0;
        $conversionRate = $performance['conversion_rate'] ?? 0;
        
        // Calculate effectiveness score based on performance
        $score = 0;
        
        if ($ctr > 2) $score += 40;
        elseif ($ctr > 1) $score += 30;
        elseif ($ctr > 0.5) $score += 20;
        
        if ($conversionRate > 5) $score += 40;
        elseif ($conversionRate > 3) $score += 30;
        elseif ($conversionRate > 1) $score += 20;
        
        if ($this->match_rate > 70) $score += 20;
        elseif ($this->match_rate > 50) $score += 10;
        
        return $score;
    }

    // Methods
    public function isEligibleForUser($request)
    {
        $user = $request->user();
        $ipAddress = $request->ip();
        $userAgent = $request->userAgent();

        // Check location targeting
        if (!$this->checkLocationTargeting($request)) {
            return false;
        }

        // Check age targeting
        if (!$this->checkAgeTargeting($user)) {
            return false;
        }

        // Check gender targeting
        if (!$this->checkGenderTargeting($user)) {
            return false;
        }

        // Check interest targeting
        if (!$this->checkInterestTargeting($user)) {
            return false;
        }

        // Check behavior targeting
        if (!$this->checkBehaviorTargeting($user, $request)) {
            return false;
        }

        // Check device targeting
        if (!$this->checkDeviceTargeting($request)) {
            return false;
        }

        // Check time targeting
        if (!$this->checkTimeTargeting()) {
            return false;
        }

        // Check language targeting
        if (!$this->checkLanguageTargeting($request)) {
            return false;
        }

        // Check income targeting
        if (!$this->checkIncomeTargeting($user)) {
            return false;
        }

        // Check education targeting
        if (!$this->checkEducationTargeting($user)) {
            return false;
        }

        // Check custom criteria
        if (!$this->checkCustomCriteria($user, $request)) {
            return false;
        }

        // Check exclusion criteria
        if (!$this->checkExclusionCriteria($user, $request)) {
            return false;
        }

        return true;
    }

    private function checkLocationTargeting($request)
    {
        if (empty($this->location_criteria)) {
            return true;
        }

        // This would require geolocation service
        // For now, we'll assume all locations are eligible
        return true;
    }

    private function checkAgeTargeting($user)
    {
        if (empty($this->age_range) || !$user) {
            return true;
        }

        // This would require user age data
        // For now, we'll assume all ages are eligible
        return true;
    }

    private function checkGenderTargeting($user)
    {
        if (empty($this->gender_criteria) || !$user) {
            return true;
        }

        // This would require user gender data
        // For now, we'll assume all genders are eligible
        return true;
    }

    private function checkInterestTargeting($user)
    {
        if (empty($this->interest_criteria) || !$user) {
            return true;
        }

        // This would require user interest data
        // For now, we'll assume all users have matching interests
        return true;
    }

    private function checkBehaviorTargeting($user, $request)
    {
        if (empty($this->behavior_criteria)) {
            return true;
        }

        // Check user behavior based on browsing history, past interactions, etc.
        // This would require behavior tracking system
        return true;
    }

    private function checkDeviceTargeting($request)
    {
        if (empty($this->device_criteria)) {
            return true;
        }

        $userAgent = $request->userAgent();
        $deviceType = $this->detectDeviceType($userAgent);

        return in_array($deviceType, $this->device_criteria);
    }

    private function checkTimeTargeting()
    {
        if (empty($this->time_criteria)) {
            return true;
        }

        $currentTime = now();
        $currentHour = $currentTime->format('H');
        $currentDay = $currentTime->format('D');

        foreach ($this->time_criteria as $criteria) {
            if (isset($criteria['days']) && !in_array($currentDay, $criteria['days'])) {
                continue;
            }

            if (isset($criteria['start_hour']) && isset($criteria['end_hour'])) {
                if ($currentHour >= $criteria['start_hour'] && $currentHour <= $criteria['end_hour']) {
                    return true;
                }
            }
        }

        return false;
    }

    private function checkLanguageTargeting($request)
    {
        if (empty($this->language_criteria)) {
            return true;
        }

        $language = $request->header('Accept-Language');
        $primaryLanguage = substr($language, 0, 2);

        return in_array($primaryLanguage, $this->language_criteria);
    }

    private function checkIncomeTargeting($user)
    {
        if (empty($this->income_criteria) || !$user) {
            return true;
        }

        // This would require user income data
        // For now, we'll assume all income levels are eligible
        return true;
    }

    private function checkEducationTargeting($user)
    {
        if (empty($this->education_criteria) || !$user) {
            return true;
        }

        // This would require user education data
        // For now, we'll assume all education levels are eligible
        return true;
    }

    private function checkCustomCriteria($user, $request)
    {
        if (empty($this->custom_criteria)) {
            return true;
        }

        // Check custom targeting criteria
        foreach ($this->custom_criteria as $criterion) {
            if (!$this->evaluateCustomCriterion($criterion, $user, $request)) {
                return false;
            }
        }

        return true;
    }

    private function checkExclusionCriteria($user, $request)
    {
        if (empty($this->exclusion_criteria)) {
            return true;
        }

        // If any exclusion criterion matches, user is not eligible
        foreach ($this->exclusion_criteria as $criterion) {
            if ($this->evaluateCustomCriterion($criterion, $user, $request)) {
                return false;
            }
        }

        return true;
    }

    private function evaluateCustomCriterion($criterion, $user, $request)
    {
        // This would implement custom logic for each criterion type
        // For now, we'll return true (eligible)
        return true;
    }

    private function detectDeviceType($userAgent)
    {
        if (preg_match('/Mobile|Android|iPhone|iPad/', $userAgent)) {
            if (preg_match('/iPad/', $userAgent)) {
                return 'tablet';
            }
            return 'mobile';
        }
        
        return 'desktop';
    }

    public function calculateEstimatedReach()
    {
        $totalUsers = 1000000; // This would be actual user count
        $reachMultiplier = 1.0;

        // Apply targeting criteria to estimate reach
        if (!empty($this->location_criteria)) {
            $reachMultiplier *= 0.6; // Location targeting reduces reach
        }

        if (!empty($this->age_range)) {
            $reachMultiplier *= 0.5; // Age targeting reduces reach
        }

        if (!empty($this->gender_criteria)) {
            $reachMultiplier *= 0.5; // Gender targeting reduces reach
        }

        if (!empty($this->interest_criteria)) {
            $reachMultiplier *= 0.4; // Interest targeting reduces reach
        }

        if (!empty($this->device_criteria)) {
            $reachMultiplier *= 0.7; // Device targeting reduces reach
        }

        $estimatedReach = floor($totalUsers * $reachMultiplier);

        $this->update(['estimated_reach' => $estimatedReach]);

        return $estimatedReach;
    }

    public function updatePerformance($metrics)
    {
        $performanceData = array_merge($this->performance_data ?? [], $metrics);
        
        $this->update([
            'performance_data' => $performanceData,
            'actual_reach' => $metrics['reach'] ?? $this->actual_reach,
            'match_rate' => $this->calculateMatchRate($metrics)
        ]);
    }

    private function calculateMatchRate($metrics)
    {
        $impressions = $metrics['impressions'] ?? 0;
        $eligibleImpressions = $metrics['eligible_impressions'] ?? $impressions;

        if ($eligibleImpressions > 0) {
            return ($impressions / $eligibleImpressions) * 100;
        }

        return 0;
    }

    public function getkinTargeting<|code_suffix|>
        0;

       oS
       .0;

       zee($criteria)_;_criteria) {
rer multiplier *= __; //nting reduces reachanska
    if inreachMult($this->Subjec multiplier *=  X;_targeting-Ting multiplier.
    }

   <|code_suffix|>
;($this-> .criteria)ANI multiplier *= .
   ;($this->device_criteria) multiplier *= 0.7;  targeting reduces reach
    }

    return floor($totalUsers * $reachMultiplier);
    }
    public function getTargetingInsights()
    {
        return [
            'criteria_count' => $this->getCriteriaCount(),
            'targeting_complexity' => $this->getTargetingComplexity(),
            'reach_efficiency' => $this->reach_efficiency,
            'targeting_effectiveness' => $this->targeting_effectiveness,
            'recommendations' => $this->getOptimizationRecommendations()
        ];
    }

    private function getCriteriaCount()
    {
        return [
            'locations' => count($this->location_criteria ?? []),
            'age_range' => !empty($this->age_range) ? 1 : 0,
            'genders' => count($this->gender_criteria ?? []),
            'interests' => count($this->interest_criteria ?? []),
            'behaviors' => count($this->behavior_criteria ?? []),
            'devices' => count($this->device_criteria ?? []),
            'time_slots' => count($this->time_criteria ?? []),
            'languages' => count($this->language_criteria ?? []),
            'income_levels' => count($this->income_criteria ?? []),
            'education_levels' => count($this->education_criteria ?? []),
            'custom' =>,
            'exclusions' => count($this->exclusion_criteria ?? [])
        ];
    }

    private function getTargetingComplexity()
    {
        $criteriaCount = array_sum($this->getCriteriaCount());
        
        if ($criteriaCount <= 2) return 'simple';
        if ($criteriaCount <= 5) return 'moderate';
        if ($criteriaCount <= 8) return 'complex';
        return 'very_complex';
    }

    private function getOptimizationRecommendations()
    {
        $recommendations = [];

        if ($this->reach_efficiency < 50) {
            $recommendations[] = [
                'type' => 'expand_audience',
                'priority' => 'high',
                'message' => 'كفاءة الوصول منخفضة، يمكن توسيع مع蒸馏ة الجم.扩大受众范围',
zec
           ',
                ' action' )
            ];
oric
        ];
        which         }

       contra         if ($this->targeting_effectiveness < 40) {
            $recommendations[] = [
                'type' => 'refine_targeting',
                'priority' => 'medium',
                'message' => 'فعالية الاستهداف منخفضة، يمكن تحسين معايير الاستهداف',
                'action' => 'refine_criteria'
            ];
        }

        if ($this->estimated_reach < 1000) {
            $recommendations[] = [
                'type' => 'increase_reach',
                'priority' => 'high',
                'message' => 'الوصول المقدر منخفض جداً، يجب تقليل معايير الاستهداف',
                'action' => 'relax_criteria'
            ];
        }

        return $recommendations;
    }

    public function cloneToAdvertisement($advertisementId)
    {
        $newTargeting = $this->replicate();
        $newTargeting->advertisement_id = $advertisementId;
        $newTargeting->campaign_id = null;
        $newTargeting->target_type = 'advertisement';
        $newTargeting->save();

        return $newTargeting;
    }

    public function cloneToCampaign($campaignId)
    {
        $newTargeting = $this->replicate();
        $newTargeting->advertisement_id = null;
        $newTargeting->campaign_id = $campaignId;
        $newTargeting->target_type = 'campaign';
        $newTargeting->save();

        return $newTargeting;
    }

    public function exportTargetingData()
    {
        return [
            'target_type' => $this->target_type,
            'target_id' => $this->advertisement_id ?: $this->campaign_id,
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

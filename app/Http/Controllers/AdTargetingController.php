<?php

namespace App\Http\Controllers;

use App\Models\AdTargeting;
use App\Models\Advertisement;
use App\Models\AdCampaign;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdTargetingController extends Controller
{
    public function index()
    {
        $targeting = AdTargeting::with(['advertisement', 'campaign'])
            ->whereHas('advertisement', function($query) {
                $query->where('user_id', Auth::id());
            })
            ->orWhereHas('campaign', function($query) {
                $query->where('user_id', Auth::id());
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('ads.targeting', compact('targeting'));
    }

    public function create()
    {
        $campaigns = AdCampaign::where('user_id', Auth::id())->get();
        $ads = Advertisement::where('user_id', Auth::id())->get();

        return view('ads.create-targeting', compact('campaigns', 'ads'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'target_type' => 'required|in:advertisement,campaign',
            'target_id' => 'required|integer',
            'audience_criteria' => 'nullable|array',
            'location_criteria' => 'nullable|array',
            'age_range' => 'nullable|array|min:2|max:2',
            'gender_criteria' => 'nullable|array',
            'interest_criteria' => 'nullable|array',
            'behavior_criteria' => 'nullable|array',
            'device_criteria' => 'nullable|array',
            'time_criteria' => 'nullable|array',
            'language_criteria' => 'nullable|array',
            'income_criteria' => 'nullable|array',
            'education_criteria' => 'nullable|array',
            'custom_criteria' => 'nullable|array'
        ]);

        // Validate age range
        if ($request->age_range) {
            $minAge = $request->age_range[0];
            $maxAge = $request->age_range[1];
            
            if ($minAge < 13 || $maxAge > 100 || $minAge >= $maxAge) {
                return back()->withErrors(['age_range' => 'نطاق العمر غير صالح']);
            }
        }

        $targetingData = [
            'target_type' => $request->target_type,
            'target_id' => $request->target_id,
            'audience_criteria' => $request->audience_criteria ?? [],
            'location_criteria' => $request->location_criteria ?? [],
            'age_range' => $request->age_range ?? [],
            'gender_criteria' => $request->gender_criteria ?? [],
            'interest_criteria' => $request->interest_criteria ?? [],
            'behavior_criteria' => $request->behavior_criteria ?? [],
            'device_criteria' => $request->device_criteria ?? [],
            'time_criteria' => $request->time_criteria ?? [],
            'language_criteria' => $request->language_criteria ?? [],
            'income_criteria' => $request->income_criteria ?? [],
            'education_criteria' => $request->education_criteria ?? [],
            'custom_criteria' => $request->custom_criteria ?? []
        ];

        // Set the appropriate foreign key
        if ($request->target_type === 'advertisement') {
            $ad = Advertisement::findOrFail($request->target_id);
            if ($ad->user_id !== Auth::id()) {
                abort(403);
            }
            $targetingData['advertisement_id'] = $request->target_id;
        } else {
            $campaign = AdCampaign::findOrFail($request->target_id);
            if ($campaign->user_id !== Auth::id()) {
                abort(403);
            }
            $targetingData['campaign_id'] = $request->target_id;
        }

        $targeting = AdTargeting::create($targetingData);

        return redirect()->route('targeting.show', $targeting->id)
            ->with('success', 'تم إنشاء معايير الاستهداف بنجاح');
    }

    public function show(AdTargeting $targeting)
    {
        if ($targeting->advertisement && $targeting->advertisement->user_id !== Auth::id() && 
            $targeting->campaign && $targeting->campaign->user_id !== Auth::id() && 
            !Auth::user()->role === 'admin') {
            abort(403);
        }

        $targeting->load(['advertisement', 'campaign']);

        // Get targeting performance
        $performance = $this->getTargetingPerformance($targeting);

        // Get audience insights
        $insights = $this->getAudienceInsights($targeting);

        return view('ads.show-targeting', compact('targeting', 'performance', 'insights'));
    }

    public function edit(AdTargeting $targeting)
    {
        if ($targeting->advertisement && $targeting->advertisement->user_id !== Auth::id() || 
            $targeting->campaign && $targeting->campaign->user_id !== Auth::id()) {
            abort(403);
        }

        return view('ads.edit-targeting', compact('targeting'));
    }

    public function update(Request $request, AdTargeting $targeting)
    {
        if ($targeting->advertisement && $targeting->advertisement->user_id !== Auth::id() || 
            $targeting->campaign && $targeting->campaign->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'audience_criteria' => 'nullable|array',
            'location_criteria' => 'nullable|array',
            'age_range' => 'nullable|array|min:2|max:2',
            'gender_criteria' => 'nullable|array',
            'interest_criteria' => 'nullable|array',
            'behavior_criteria' => 'nullable|array',
            'device_criteria' => 'nullable|array',
            'time_criteria' => 'nullable|array',
            'language_criteria' => 'nullable|array',
            'income_criteria' => 'nullable|array',
            'education_criteria' => 'nullable|array',
            'custom_criteria' => 'nullable|array'
        ]);

        $targeting->update([
            'audience_criteria' => $request->audience_criteria ?? [],
            'location_criteria' => $request->location_criteria ?? [],
            'age_range' => $request->age_range ?? [],
            'gender_criteria' => $request->gender_criteria ?? [],
            'interest_criteria' => $request->interest_criteria ?? [],
            'behavior_criteria' => $request->behavior_criteria ?? [],
            'device_criteria' => $request->device_criteria ?? [],
            'time_criteria' => $request->time_criteria ?? [],
            'language_criteria' => $request->language_criteria ?? [],
            'income_criteria' => $request->income_criteria ?? [],
            'education_criteria' => $request->education_criteria ?? [],
            'custom_criteria' => $request->custom_criteria ?? []
        ]);

        return redirect()->route('targeting.show', $targeting->id)
            ->with('success', 'تم تحديث معايير الاستهداف بنجاح');
    }

    public function destroy(AdTargeting $targeting)
    {
        if ($targeting->advertisement && $targeting->advertisement->user_id !== Auth::id() || 
            $targeting->campaign && $targeting->campaign->user_id !== Auth::id()) {
            abort(403);
        }

        $targeting->delete();

        return redirect()->route('targeting.index')
            ->with('success', 'تم حذف معايير الاستهداف بنجاح');
    }

    public function duplicate(AdTargeting $targeting)
    {
        if ($targeting->advertisement && $targeting->advertisement->user_id !== Auth::id() || 
            $targeting->campaign && $targeting->campaign->user_id !== Auth::id()) {
            abort(403);
        }

        $newTargeting = $targeting->replicate();
        $newTargeting->save();

        return redirect()->route('targeting.edit', $newTargeting->id)
            ->with('success', 'تم نسخ معايير الاستهداف بنجاح');
    }

    public function previewAudience(AdTargeting $targeting)
    {
        if ($targeting->advertisement && $targeting->advertisement->user_id !== Auth::id() || 
            $targeting->campaign && $targeting->campaign->user_id !== Auth::id()) {
            abort(403);
        }

        $estimatedAudience = $this->estimateAudienceSize($targeting);
        $audienceBreakdown = $this->getAudienceBreakdown($targeting);

        return response()->json([
            'estimated_size' => $estimatedAudience,
            'breakdown' => $audienceBreakdown
        ]);
    }

    public function optimizeTargeting(Request $request, AdTargeting $targeting)
    {
        if ($targeting->advertisement && $targeting->advertisement->user_id !== Auth::id() || 
            $targeting->campaign && $targeting->campaign->user_id !== Auth::id()) {
            abort(403);
        }

        $optimizations = $this->generateTargetingOptimizations($targeting);

        return response()->json($optimizations);
    }

    public function applyOptimizations(Request $request, AdTargeting $targeting)
    {
        if ($targeting->advertisement && $targeting->advertisement->user_id !== Auth::id() || 
            $targeting->campaign && $targeting->campaign->user_id !== Auth::id()) {
            abort(403);
        }

        $optimizations = $request->optimizations ?? [];

        foreach ($optimizations as $type => $data) {
            $this->applyTargetingOptimization($targeting, $type, $data);
        }

        return back()->with('success', 'تم تطبيق تحسينات الاستهداف بنجاح');
    }

    public function targetingTemplates()
    {
        $templates = [
            'first_time_buyers' => [
                'name' => 'المشترون لأول مرة',
                'criteria' => [
                    'behavior_criteria' => ['first_time_home_buyer'],
                    'age_range' => [25, 45]
                ]
            ],
            'luxury_buyers' => [
                'name' => 'المشترون الفاخرون',
                'criteria' => [
                    'income_criteria' => ['high', 'very_high'],
                    'location_criteria' => ['premium_areas']
                ]
            ],
            'investors' => [
                'name' => 'المستثمرون',
                'criteria' => [
                    'behavior_criteria' => ['property_investor'],
                    'interest_criteria' => ['real_estate', 'investment']
                ]
            ],
            'rental_seekers' => [
                'name' => 'الباحثون عن الإيجار',
                'criteria' => [
                    'behavior_criteria' => ['rental_seeker'],
                    'age_range' => [22, 35]
                ]
            ]
        ];

        return view('ads.targeting-templates', compact('templates'));
    }

    public function applyTemplate(Request $request)
    {
        $request->validate([
            'template' => 'required|string',
            'target_type' => 'required|in:advertisement,campaign',
            'target_id' => 'required|integer'
        ]);

        $templates = $this->getTargetingTemplates();
        $template = $templates[$request->template] ?? null;

        if (!$template) {
            return back()->withErrors(['template' => 'قالب غير صالح']);
        }

        $targetingData = array_merge($template['criteria'], [
            'target_type' => $request->target_type,
            'target_id' => $request->target_id
        ]);

        if ($request->target_type === 'advertisement') {
            $targetingData['advertisement_id'] = $request->target_id;
        } else {
            $targetingData['campaign_id'] = $request->target_id;
        }

        $targeting = AdTargeting::create($targetingData);

        return redirect()->route('targeting.edit', $targeting->id)
            ->with('success', 'تم تطبيق القالب بنجاح');
    }

    private function getTargetingPerformance($targeting)
    {
        // Get performance metrics for targeted audience
        return [
            'impressions' => 0,
            'clicks' => 0,
            'conversions' => 0,
            'ctr' => 0,
            'cpc' => 0,
            'cpa' => 0
        ];
    }

    private function getAudienceInsights($targeting)
    {
        // Get insights about the targeted audience
        return [
            'demographics' => [],
            'interests' => [],
            'behaviors' => [],
            'locations' => []
        ];
    }

    private function estimateAudienceSize($targeting)
    {
        // Estimate the size of the targeted audience
        $baseAudience = User::count();
        
        // Apply targeting criteria to estimate reach
        $reachMultiplier = $this->calculateReachMultiplier($targeting);
        
        return floor($baseAudience * $reachMultiplier);
    }

    private function getAudienceBreakdown($targeting)
    {
        return [
            'age_groups' => [],
            'genders' => [],
            'locations' => [],
            'interests' => [],
            'devices' => []
        ];
    }

    private function calculateReachMultiplier($targeting)
    {
        $multiplier = 1.0;
        
        // Apply various targeting criteria to calculate reach
        if (!empty($targeting->location_criteria)) {
            $multiplier *= 0.7; // Location targeting reduces reach
        }
        
        if (!empty($targeting->age_range)) {
            $multiplier *= 0.6; // Age targeting reduces reach
        }
        
        if (!empty($targeting->interest_criteria)) {
            $multiplier *= 0.5; // Interest targeting reduces reach
        }
        
        return max($multiplier, 0.05); // Minimum 5% reach
    }

    private function generateTargetingOptimizations($targeting)
    {
        return [
            'location_expansion' => [
                'suggestion' => 'توسيع نطاق الموقع الجغرافي',
                'impact' => 'زيادة الوصول بنسبة 20%',
                'confidence' => 0.8
            ],
            'age_adjustment' => [
                'suggestion' => 'تعديل نطاق العمر',
                'impact' => 'تحسين معدل التحويل بنسبة 15%',
                'confidence' => 0.7
            ]
        ];
    }

    private function applyTargetingOptimization($targeting, $type, $data)
    {
        switch ($type) {
            case 'location_expansion':
                // Apply location expansion
                break;
            case 'age_adjustment':
                // Apply age adjustment
                break;
        }
    }

    private function getTargetingTemplates()
    {
        return [
            'first_time_buyers' => [
                'name' => 'المشترون لأول مرة',
                'criteria' => [
                    'behavior_criteria' => ['first_time_home_buyer'],
                    'age_range' => [25, 45]
                ]
            ],
            'luxury_buyers' => [
                'name' => 'المشترون الفاخرون',
                'criteria' => [
                    'income_criteria' => ['high', 'very_high'],
                    'location_criteria' => ['premium_areas']
                ]
            ],
            'investors' => [
                'name' => 'المستثمرون',
                'criteria' => [
                    'behavior_criteria' => ['property_investor'],
                    'interest_criteria' => ['real_estate', 'investment']
                ]
            ],
            'rental_seekers' => [
                'name' => 'الباحثون عن الإيجار',
                'criteria' => [
                    'behavior_criteria' => ['rental_seeker'],
                    'age_range' => [22, 35]
                ]
            ]
        ];
    }
}

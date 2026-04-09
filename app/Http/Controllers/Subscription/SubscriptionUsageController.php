<?php

namespace App\Http\Controllers\Subscription;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\SubscriptionUsage;
use App\Models\SubscriptionFeature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SubscriptionUsageController extends Controller
{
    public function index(Subscription $subscription)
    {
        $this->authorize('view', $subscription);

        $usage = $subscription->usage()
            ->with(['feature'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $usageStats = $this->getUsageStats($subscription);
        $limits = $this->getSubscriptionLimits($subscription);

        return view('subscriptions.usage.index', compact('subscription', 'usage', 'usageStats', 'limits'));
    }

    public function show(SubscriptionUsage $usage)
    {
        $this->authorize('view', $usage);

        $usage->load(['subscription.plan', 'feature']);

        return view('subscriptions.usage.show', compact('usage'));
    }

    public function trackUsage(Request $request, Subscription $subscription)
    {
        $this->authorize('update', $subscription);

        $validated = $request->validate([
            'feature_id' => 'required|exists:subscription_features,id',
            'usage_amount' => 'required|numeric|min:0',
            'usage_unit' => 'required|string|max:50',
            'metadata' => 'nullable|array'
        ]);

        try {
            $feature = SubscriptionFeature::findOrFail($validated['feature_id']);
            
            // Check if usage is within limits
            if (!$this->isWithinLimits($subscription, $feature, $validated['usage_amount'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usage exceeds subscription limits',
                    'limit' => $this->getFeatureLimit($subscription, $feature),
                    'current_usage' => $this->getCurrentUsage($subscription, $feature)
                ], 422);
            }

            // Create usage record
            $usage = SubscriptionUsage::create([
                'subscription_id' => $subscription->id,
                'user_id' => Auth::id(),
                'feature_id' => $validated['feature_id'],
                'usage_amount' => $validated['usage_amount'],
                'usage_unit' => $validated['usage_unit'],
                'tracked_at' => now(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'metadata' => $validated['metadata'] ?? []
            ]);

            return response()->json([
                'success' => true,
                'usage' => $usage,
                'remaining_usage' => $this->getRemainingUsage($subscription, $feature)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to track usage: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getUsageReport(Subscription $subscription, Request $request)
    {
        $this->authorize('view', $subscription);

        $period = $request->get('period', 'month');
        $fromDate = $this->getFromDate($period);
        $toDate = now();

        $usageData = $subscription->usage()
            ->with(['feature'])
            ->whereBetween('tracked_at', [$fromDate, $toDate])
            ->get()
            ->groupBy('feature.name');

        $report = [
            'period' => $period,
            'from_date' => $fromDate,
            'to_date' => $toDate,
            'usage_by_feature' => $usageData->map(function ($usages, $featureName) {
                return [
                    'feature' => $featureName,
                    'total_usage' => $usages->sum('usage_amount'),
                    'usage_count' => $usages->count(),
                    'average_usage' => $usages->avg('usage_amount'),
                    'unit' => $usages->first()->usage_unit,
                    'daily_average' => $this->calculateDailyAverage($usages, $fromDate, $toDate)
                ];
            }),
            'total_usage' => $subscription->usage()
                ->whereBetween('tracked_at', [$fromDate, $toDate])
                ->sum('usage_amount'),
            'usage_trend' => $this->getUsageTrend($subscription, $fromDate, $toDate)
        ];

        return response()->json($report);
    }

    public function getUsageLimits(Subscription $subscription)
    {
        $this->authorize('view', $subscription);

        $limits = [];
        $plan = $subscription->plan;

        // Get all features for this plan
        $features = $plan->features;

        foreach ($features as $feature) {
            $currentUsage = $this->getCurrentUsage($subscription, $feature);
            $limit = $this->getFeatureLimit($subscription, $feature);
            $remaining = $limit - $currentUsage;
            $percentage = $limit > 0 ? ($currentUsage / $limit) * 100 : 0;

            $limits[] = [
                'feature_id' => $feature->id,
                'feature_name' => $feature->name,
                'feature_type' => $feature->type,
                'limit' => $limit,
                'current_usage' => $currentUsage,
                'remaining' => max(0, $remaining),
                'percentage' => min(100, $percentage),
                'unit' => $feature->unit,
                'is_unlimited' => $limit === -1,
                'is_over_limit' => $currentUsage > $limit && $limit !== -1
            ];
        }

        return response()->json($limits);
    }

    public function resetUsage(Request $request, Subscription $subscription)
    {
        $this->authorize('update', $subscription);

        $validated = $request->validate([
            'feature_id' => 'required|exists:subscription_features,id',
            'reset_type' => 'required|in:all,period'
        ]);

        try {
            $query = $subscription->usage()->where('feature_id', $validated['feature_id']);

            if ($validated['reset_type'] === 'period') {
                $query->where('tracked_at', '>=', now()->startOfMonth());
            }

            $deletedCount = $query->delete();

            return response()->json([
                'success' => true,
                'message' => "Usage reset successfully. {$deletedCount} records deleted."
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reset usage: ' . $e->getMessage()
            ], 500);
        }
    }

    public function exportUsage(Subscription $subscription, Request $request)
    {
        $this->authorize('view', $subscription);

        $format = $request->get('format', 'csv');
        $period = $request->get('period', 'month');
        $fromDate = $this->getFromDate($period);

        $usageData = $subscription->usage()
            ->with(['feature'])
            ->whereBetween('tracked_at', [$fromDate, now()])
            ->get();

        $filename = "usage_report_{$subscription->id}_{$period}." . $format;

        switch ($format) {
            case 'csv':
                return $this->exportCSV($usageData, $filename);
            case 'xlsx':
                return $this->exportExcel($usageData, $filename);
            case 'json':
                return $this->exportJSON($usageData, $filename);
            default:
                return response()->json(['error' => 'Invalid format'], 400);
        }
    }

    private function getUsageStats(Subscription $subscription)
    {
        $currentMonth = $subscription->usage()
            ->whereMonth('tracked_at', now()->month)
            ->whereYear('tracked_at', now()->year);

        return [
            'current_month_usage' => $currentMonth->sum('usage_amount'),
            'current_month_records' => $currentMonth->count(),
            'total_usage' => $subscription->usage()->sum('usage_amount'),
            'total_records' => $subscription->usage()->count(),
            'most_used_feature' => $this->getMostUsedFeature($subscription),
            'usage_growth' => $this->calculateUsageGrowth($subscription)
        ];
    }

    private function getSubscriptionLimits(Subscription $subscription)
    {
        $plan = $subscription->plan;
        
        return [
            'max_users' => $plan->max_users,
            'storage_limit' => $plan->storage_limit,
            'bandwidth_limit' => $plan->bandwidth_limit,
            'api_calls_limit' => $plan->api_calls_limit,
            'custom_limits' => $this->getCustomLimits($subscription)
        ];
    }

    private function isWithinLimits(Subscription $subscription, SubscriptionFeature $feature, $usageAmount)
    {
        $limit = $this->getFeatureLimit($subscription, $feature);
        $currentUsage = $this->getCurrentUsage($subscription, $feature);

        if ($limit === -1) {
            return true; // Unlimited
        }

        return ($currentUsage + $usageAmount) <= $limit;
    }

    private function getFeatureLimit(Subscription $subscription, SubscriptionFeature $feature)
    {
        $plan = $subscription->plan;
        
        // Get limit from pivot table or plan defaults
        $pivot = $plan->features()->where('feature_id', $feature->id)->first();
        
        if ($pivot && isset($pivot->pivot->limit)) {
            return $pivot->pivot->limit;
        }

        // Return default limits based on feature type
        switch ($feature->name) {
            case 'api_calls':
                return $plan->api_calls_limit ?? -1;
            case 'storage':
                return $plan->storage_limit ?? -1;
            case 'bandwidth':
                return $plan->bandwidth_limit ?? -1;
            case 'users':
                return $plan->max_users ?? -1;
            default:
                return $feature->default_value ?? -1;
        }
    }

    private function getCurrentUsage(Subscription $subscription, SubscriptionFeature $feature)
    {
        return $subscription->usage()
            ->where('feature_id', $feature->id)
            ->whereMonth('tracked_at', now()->month)
            ->whereYear('tracked_at', now()->year)
            ->sum('usage_amount');
    }

    private function getRemainingUsage(Subscription $subscription, SubscriptionFeature $feature)
    {
        $limit = $this->getFeatureLimit($subscription, $feature);
        $currentUsage = $this->getCurrentUsage($subscription, $feature);

        if ($limit === -1) {
            return 'Unlimited';
        }

        return max(0, $limit - $currentUsage);
    }

    private function getFromDate($period)
    {
        switch ($period) {
            case 'today':
                return now()->startOfDay();
            case 'week':
                return now()->startOfWeek();
            case 'month':
                return now()->startOfMonth();
            case 'quarter':
                return now()->startOfQuarter();
            case 'year':
                return now()->startOfYear();
            default:
                return now()->subDays(30);
        }
    }

    private function calculateDailyAverage($usages, $fromDate, $toDate)
    {
        $days = $fromDate->diffInDays($toDate) ?: 1;
        return $usages->sum('usage_amount') / $days;
    }

    private function getUsageTrend(Subscription $subscription, $fromDate, $toDate)
    {
        // Calculate usage trend over time
        return $subscription->usage()
            ->whereBetween('tracked_at', [$fromDate, $toDate])
            ->orderBy('tracked_at')
            ->get()
            ->groupBy(function ($usage) {
                return $usage->tracked_at->format('Y-m-d');
            })
            ->map(function ($dayUsages) {
                return $dayUsages->sum('usage_amount');
            });
    }

    private function getMostUsedFeature(Subscription $subscription)
    {
        return $subscription->usage()
            ->with('feature')
            ->get()
            ->groupBy('feature.name')
            ->map(function ($usages) {
                return $usages->sum('usage_amount');
            })
            ->sortDesc()
            ->first();
    }

    private function calculateUsageGrowth(Subscription $subscription)
    {
        $currentMonth = $subscription->usage()
            ->whereMonth('tracked_at', now()->month)
            ->whereYear('tracked_at', now()->year)
            ->sum('usage_amount');

        $lastMonth = $subscription->usage()
            ->whereMonth('tracked_at', now()->subMonth()->month)
            ->whereYear('tracked_at', now()->subMonth()->year)
            ->sum('usage_amount');

        if ($lastMonth == 0) {
            return $currentMonth > 0 ? 100 : 0;
        }

        return (($currentMonth - $lastMonth) / $lastMonth) * 100;
    }

    private function getCustomLimits(Subscription $subscription)
    {
        // Get any custom limits from subscription_limits table
        return [];
    }

    private function exportCSV($data, $filename)
    {
        // Implement CSV export
        return response()->streamDownload(function () use ($data) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Feature', 'Usage Amount', 'Unit', 'Date']);
            
            foreach ($data as $usage) {
                fputcsv($handle, [
                    $usage->feature->name,
                    $usage->usage_amount,
                    $usage->usage_unit,
                    $usage->tracked_at
                ]);
            }
            
            fclose($handle);
        }, $filename);
    }

    private function exportExcel($data, $filename)
    {
        // Implement Excel export using Laravel Excel
        return response()->json(['message' => 'Excel export not implemented'], 501);
    }

    private function exportJSON($data, $filename)
    {
        return response()->streamDownload(function () use ($data) {
            echo $data->toJson(JSON_PRETTY_PRINT);
        }, $filename);
    }
}

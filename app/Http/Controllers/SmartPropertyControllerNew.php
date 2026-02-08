<?php

namespace App\Http\Controllers;

use App\Models\SmartProperty;
use App\Models\IoTDevice;
use App\Models\EnergyMonitoringData;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class SmartPropertyController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = SmartProperty::with(['property', 'devices', 'energyData' => function($query) {
            $query->latest('recorded_at')->limit(10);
        }]);

        // Filters
        if ($request->smart_level) {
            $query->bySmartLevel($request->smart_level);
        }

        if ($request->property_id) {
            $query->where('property_id', $request->property_id);
        }

        if ($request->status) {
            $query->where('current_status', $request->status);
        }

        $smartProperties = $query->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => $smartProperties,
            'message' => 'Smart properties retrieved successfully'
        ]);
    }

    public function show(SmartProperty $smartProperty): JsonResponse
    {
        $smartProperty->load([
            'property',
            'devices',
            'energyData' => function($query) {
                $query->latest('recorded_at')->limit(24);
            },
            'alerts' => function($query) {
                $query->where('resolved_at', null)->latest();
            }
        ]);

        return response()->json([
            'success' => true,
            'data' => $smartProperty,
            'health_report' => $smartProperty->generateHealthReport(),
            'device_summary' => $smartProperty->getDeviceStatusSummary(),
            'current_energy_usage' => $smartProperty->getCurrentEnergyUsage(),
            'message' => 'Smart property details retrieved successfully'
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'property_id' => 'required|exists:properties,id',
            'smart_level' => 'required|in:basic,advanced,premium',
            'automation_enabled' => 'boolean',
            'energy_monitoring_enabled' => 'boolean',
            'security_enabled' => 'boolean',
            'ai_optimization_enabled' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Validation failed'
            ], 422);
        }

        try {
            DB::beginTransaction();

            $smartProperty = SmartProperty::create($request->all());

            // Initialize default settings based on smart level
            $this->initializeSmartPropertySettings($smartProperty);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $smartProperty->load('property'),
                'message' => 'Smart property created successfully'
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create smart property: ' . $e->getMessage()
            ], 500);
        }
    }

    public function dashboard(SmartProperty $smartProperty): JsonResponse
    {
        $dashboard = [
            'overview' => [
                'smart_level' => $smartProperty->smart_level,
                'status' => $smartProperty->current_status,
                'health_score' => $smartProperty->health_score,
                'last_sync' => $smartProperty->last_sync_at,
                'uptime' => $smartProperty->uptime_percentage,
            ],
            'devices' => $smartProperty->getDeviceStatusSummary(),
            'energy' => [
                'current_usage' => $smartProperty->getCurrentEnergyUsage(),
                'monthly_cost' => $smartProperty->getMonthlyEnergyCost(),
                'efficiency_score' => $smartProperty->energy_efficiency_score,
                'carbon_footprint' => $smartProperty->carbon_footprint,
                'savings_opportunity' => $smartProperty->optimizeEnergyUsage(),
            ],
            'security' => [
                'security_score' => $smartProperty->security_score,
                'security_status' => $smartProperty->getSecurityStatus(),
                'active_alerts' => $smartProperty->alerts()->where('resolved_at', null)->count(),
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => $dashboard,
            'message' => 'Smart property dashboard retrieved successfully'
        ]);
    }

    public function energyAnalytics(SmartProperty $smartProperty, Request $request): JsonResponse
    {
        $period = $request->period ?? '24h';
        $startDate = $this->getStartDate($period);
        $endDate = now();

        $energyData = $smartProperty->energyData()
            ->forPeriod($startDate, $endDate)
            ->orderBy('recorded_at')
            ->get();

        $analytics = [
            'period' => $period,
            'summary' => [
                'total_usage_kwh' => $energyData->sum('daily_usage_kwh'),
                'total_cost' => $energyData->sum('cost_amount'),
                'average_daily_usage' => $energyData->avg('daily_usage_kwh'),
                'peak_usage' => $energyData->max('peak_usage_kw'),
                'carbon_footprint' => $energyData->sum('carbon_footprint_kg'),
                'efficiency_score' => $energyData->avg('energy_efficiency_score'),
            ],
            'predictions' => [
                'next_month_usage' => $this->predictUsage($energyData, 'month'),
                'next_month_cost' => $this->predictCost($energyData, 'month'),
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => $analytics,
            'message' => 'Energy analytics retrieved successfully'
        ]);
    }

    public function optimizeEnergy(SmartProperty $smartProperty): JsonResponse
    {
        if (!$smartProperty->ai_optimization_enabled) {
            return response()->json([
                'success' => false,
                'message' => 'AI optimization is not enabled for this property'
            ], 400);
        }

        try {
            $optimization = $smartProperty->optimizeEnergyUsage();

            return response()->json([
                'success' => true,
                'data' => $optimization,
                'message' => 'Energy optimization analysis completed'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to optimize energy usage: ' . $e->getMessage()
            ], 500);
        }
    }

    // Helper methods
    private function initializeSmartPropertySettings(SmartProperty $smartProperty): void
    {
        $settings = match($smartProperty->smart_level) {
            'basic' => [
                'automation_enabled' => true,
                'energy_monitoring_enabled' => true,
                'security_enabled' => false,
                'ai_optimization_enabled' => false,
            ],
            'advanced' => [
                'automation_enabled' => true,
                'energy_monitoring_enabled' => true,
                'security_enabled' => true,
                'ai_optimization_enabled' => false,
            ],
            'premium' => [
                'automation_enabled' => true,
                'energy_monitoring_enabled' => true,
                'security_enabled' => true,
                'ai_optimization_enabled' => true,
            ],
            default => []
        };

        $smartProperty->update($settings);
    }

    private function getStartDate(string $period): Carbon
    {
        return match($period) {
            '24h' => now()->subHours(24),
            '7d' => now()->subDays(7),
            '30d' => now()->subDays(30),
            '90d' => now()->subDays(90),
            '1y' => now()->subYear(),
            default => now()->subHours(24),
        };
    }

    private function predictUsage($energyData, string $period): float
    {
        $recentData = $energyData->take(30);
        if ($recentData->count() < 7) return $energyData->avg('daily_usage_kwh');
        
        $avgUsage = $recentData->avg('daily_usage_kwh');
        
        return match($period) {
            'month' => $avgUsage * 30,
            'week' => $avgUsage * 7,
            default => $avgUsage,
        };
    }

    private function predictCost($energyData, string $period): float
    {
        $predictedUsage = $this->predictUsage($energyData, $period);
        $avgCostPerKwh = $energyData->avg('cost_per_kwh');
        
        return $predictedUsage * $avgCostPerKwh;
    }
}

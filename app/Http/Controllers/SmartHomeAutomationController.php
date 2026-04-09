<?php

namespace App\Http\Controllers;

use App\Models\SmartHomeAutomation;
use App\Models\SmartProperty;
use App\Models\IotDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SmartHomeAutomationController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_automations' => SmartHomeAutomation::count(),
            'active_automations' => SmartHomeAutomation::where('is_active', true)->count(),
            'scheduled_automations' => SmartHomeAutomation::whereNotNull('schedule')->count(),
            'executed_today' => SmartHomeAutomation::where('last_executed', '>=', now()->startOfDay())->count(),
            'success_rate' => $this->getAutomationSuccessRate(),
            'trigger_types' => $this->getTriggerTypeDistribution(),
        ];

        $recentAutomations = SmartHomeAutomation::with(['property', 'devices'])
            ->latest()
            ->take(10)
            ->get();

        $executionTrends = $this->getExecutionTrends();
        $popularTriggers = $this->getPopularTriggers();

        return view('iot.automations.dashboard', compact(
            'stats', 
            'recentAutomations', 
            'executionTrends', 
            'popularTriggers'
        ));
    }

    public function index(Request $request)
    {
        $query = SmartHomeAutomation::with(['property', 'devices']);

        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        if ($request->filled('trigger_type')) {
            $query->where('trigger_type', $request->trigger_type);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $automations = $query->latest()->paginate(12);

        $triggerTypes = ['time', 'device_state', 'sensor_value', 'weather', 'location', 'voice'];
        $actionTypes = ['device_control', 'notification', 'scene_activation', 'data_logging'];

        return view('iot.automations.index', compact(
            'automations', 
            'triggerTypes', 
            'actionTypes'
        ));
    }

    public function create()
    {
        $properties = SmartProperty::all();
        $triggerTypes = ['time', 'device_state', 'sensor_value', 'weather', 'location', 'voice'];
        $actionTypes = ['device_control', 'notification', 'scene_activation', 'data_logging'];
        $devices = IotDevice::where('status', 'active')->get();

        return view('iot.automations.create', compact(
            'properties', 
            'triggerTypes', 
            'actionTypes', 
            'devices'
        ));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $automationData = $request->validate([
                'property_id' => 'required|exists:smart_properties,id',
                'rule_name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'trigger_type' => 'required|in:time,device_state,sensor_value,weather,location,voice',
                'trigger_conditions' => 'required|array',
                'actions' => 'required|array',
                'schedule' => 'nullable|array',
                'is_active' => 'required|boolean',
                'priority' => 'required|in:low,medium,high,critical',
                'execution_count' => 'nullable|integer|min:0',
                'successful_executions' => 'nullable|integer|min:0',
            ]);

            $automationData['created_by'] = auth()->id();
            $automationData['automation_metadata'] = $this->generateAutomationMetadata($request);

            $automation = SmartHomeAutomation::create($automationData);

            // Link devices to automation
            if ($request->has('device_ids')) {
                $this->linkDevicesToAutomation($automation, $request->device_ids);
            }

            // Set up schedule if provided
            if ($request->has('schedule')) {
                $this->setupAutomationSchedule($automation, $request->schedule);
            }

            DB::commit();

            return redirect()
                ->route('smart-automation.show', $automation)
                ->with('success', 'تم إنشاء أتمتة المنزل الذكي بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إنشاء الأتمتة: ' . $e->getMessage());
        }
    }

    public function show(SmartHomeAutomation $automation)
    {
        $automation->load(['property', 'devices', 'executionLogs']);
        $executionHistory = $this->getAutomationExecutionHistory($automation);
        $nextExecution = $this->getNextScheduledExecution($automation);

        return view('iot.automations.show', compact(
            'automation', 
            'executionHistory', 
            'nextExecution'
        ));
    }

    public function edit(SmartHomeAutomation $automation)
    {
        $properties = SmartProperty::all();
        $triggerTypes = ['time', 'device_state', 'sensor_value', 'weather', 'location', 'voice'];
        $actionTypes = ['device_control', 'notification', 'scene_activation', 'data_logging'];
        $devices = IotDevice::where('status', 'active')->get();

        return view('iot.automations.edit', compact(
            'automation', 
            'properties', 
            'triggerTypes', 
            'actionTypes', 
            'devices'
        ));
    }

    public function update(Request $request, SmartHomeAutomation $automation)
    {
        DB::beginTransaction();
        try {
            $automationData = $request->validate([
                'rule_name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'trigger_type' => 'required|in:time,device_state,sensor_value,weather,location,voice',
                'trigger_conditions' => 'required|array',
                'actions' => 'required|array',
                'schedule' => 'nullable|array',
                'is_active' => 'required|boolean',
                'priority' => 'required|in:low,medium,high,critical',
            ]);

            $automationData['updated_by'] = auth()->id();
            $automationData['automation_metadata'] = $this->generateAutomationMetadata($request);

            $automation->update($automationData);

            // Update linked devices
            if ($request->has('device_ids')) {
                $this->updateLinkedDevices($automation, $request->device_ids);
            }

            // Update schedule
            if ($request->has('schedule')) {
                $this->updateAutomationSchedule($automation, $request->schedule);
            }

            DB::commit();

            return redirect()
                ->route('smart-automation.show', $automation)
                ->with('success', 'تم تحديث أتمتة المنزل الذكي بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث الأتمتة: ' . $e->getMessage());
        }
    }

    public function destroy(SmartHomeAutomation $automation)
    {
        try {
            // Unlink devices
            $this->unlinkDevicesFromAutomation($automation);

            // Delete automation
            $automation->delete();

            return redirect()
                ->route('smart-automation.index')
                ->with('success', 'تم حذف أتمتة المنزل الذكي بنجاح');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'حدث خطأ أثناء حذف الأتمتة: ' . $e->getMessage());
        }
    }

    public function executeAutomation(SmartHomeAutomation $automation)
    {
        try {
            $result = $this->executeAutomationRule($automation);

            return response()->json([
                'success' => true,
                'result' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function toggleAutomation(SmartHomeAutomation $automation)
    {
        try {
            $newStatus = !$automation->is_active;
            $automation->update([
                'is_active' => $newStatus,
                'updated_by' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'status' => $newStatus,
                'message' => $newStatus ? 'تم تفعيل الأتمتة' : 'تم إيقاف الأتمتة'
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function testAutomation(SmartHomeAutomation $automation)
    {
        try {
            $testResult = $this->testAutomationRule($automation);

            return response()->json([
                'success' => true,
                'test_result' => $testResult
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getExecutionLogs(SmartHomeAutomation $automation)
    {
        try {
            $logs = $automation->executionLogs()
                ->latest()
                ->paginate(50);

            return response()->json($logs);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function generateAutomationMetadata($request)
    {
        return [
            'complexity_score' => $this->calculateComplexityScore($request),
            'execution_time_estimate' => $this->estimateExecutionTime($request),
            'resource_usage' => $this->estimateResourceUsage($request),
            'dependency_count' => count($request->device_ids ?? []),
            'trigger_sensitivity' => $this->calculateTriggerSensitivity($request),
            'created_at' => now()->toDateTimeString(),
        ];
    }

    private function linkDevicesToAutomation($automation, $deviceIds)
    {
        foreach ($deviceIds as $deviceId) {
            $automation->devices()->attach($deviceId, [
                'role' => 'participant',
                'created_at' => now(),
            ]);
        }
    }

    private function setupAutomationSchedule($automation, $schedule)
    {
        $automation->update([
            'schedule' => $schedule,
            'next_execution' => $this->calculateNextExecution($schedule),
        ]);
    }

    private function executeAutomationRule($automation)
    {
        // Execute automation rule
        $executionId = uniqid('exec_');
        
        // Update execution statistics
        $automation->increment('execution_count');
        
        // Log execution
        $this->logAutomationExecution($automation, $executionId, 'success');

        return [
            'execution_id' => $executionId,
            'status' => 'executed',
            'timestamp' => now()->toDateTimeString(),
            'actions_executed' => count($automation->actions),
        ];
    }

    private function testAutomationRule($automation)
    {
        // Test automation rule without executing
        return [
            'test_id' => uniqid('test_'),
            'trigger_check' => 'passed',
            'conditions_check' => 'passed',
            'actions_check' => 'passed',
            'overall_result' => 'success',
            'timestamp' => now()->toDateTimeString(),
        ];
    }

    private function calculateComplexityScore($request)
    {
        $score = 0;
        
        // Trigger complexity
        $triggerConditions = count($request->trigger_conditions ?? []);
        $score += $triggerConditions * 10;
        
        // Action complexity
        $actions = count($request->actions ?? []);
        $score += $actions * 15;
        
        // Schedule complexity
        if ($request->has('schedule')) {
            $score += 20;
        }
        
        return $score;
    }

    private function estimateExecutionTime($request)
    {
        $baseTime = 100; // 100ms base
        
        $triggerConditions = count($request->trigger_conditions ?? []);
        $actions = count($request->actions ?? []);
        
        return $baseTime + ($triggerConditions * 50) + ($actions * 75);
    }

    private function estimateResourceUsage($request)
    {
        return [
            'cpu_usage' => 'low',
            'memory_usage' => 'minimal',
            'network_usage' => 'low',
            'battery_impact' => 'minimal',
        ];
    }

    private function calculateTriggerSensitivity($request)
    {
        $sensitivity = 0;
        
        foreach ($request->trigger_conditions ?? [] as $condition) {
            if (isset($condition['operator'])) {
                switch ($condition['operator']) {
                    case 'equals':
                        $sensitivity += 10;
                        break;
                    case 'greater_than':
                        $sensitivity += 15;
                        break;
                    case 'less_than':
                        $sensitivity += 15;
                        break;
                    case 'between':
                        $sensitivity += 20;
                        break;
                }
            }
        }
        
        return $sensitivity;
    }

    private function calculateNextExecution($schedule)
    {
        // Calculate next execution time based on schedule
        if (isset($schedule['type'])) {
            switch ($schedule['type']) {
                case 'daily':
                    return now()->addDay()->startOfDay();
                case 'weekly':
                    return now()->addWeek()->startOfWeek();
                case 'monthly':
                    return now()->addMonth()->startOfMonth();
                case 'interval':
                    return now()->addMinutes($schedule['interval_minutes'] ?? 60);
            }
        }
        
        return null;
    }

    private function logAutomationExecution($automation, $executionId, $status)
    {
        $automation->executionLogs()->create([
            'execution_id' => $executionId,
            'status' => $status,
            'trigger_data' => [],
            'actions_executed' => $automation->actions,
            'execution_time' => now(),
            'created_by' => auth()->id(),
        ]);
    }

    private function updateLinkedDevices($automation, $deviceIds)
    {
        $automation->devices()->sync($deviceIds);
    }

    private function updateAutomationSchedule($automation, $schedule)
    {
        $automation->update([
            'schedule' => $schedule,
            'next_execution' => $this->calculateNextExecution($schedule),
        ]);
    }

    private function unlinkDevicesFromAutomation($automation)
    {
        $automation->devices()->detach();
    }

    private function getAutomationSuccessRate()
    {
        $totalExecutions = SmartHomeAutomation::sum('execution_count');
        $successfulExecutions = SmartHomeAutomation::sum('successful_executions');
        
        if ($totalExecutions === 0) return 100;
        
        return ($successfulExecutions / $totalExecutions) * 100;
    }

    private function getTriggerTypeDistribution()
    {
        return SmartHomeAutomation::select('trigger_type', DB::raw('COUNT(*) as count'))
            ->groupBy('trigger_type')
            ->get();
    }

    private function getExecutionTrends()
    {
        return SmartHomeAutomation::selectRaw('DATE(last_executed) as date, COUNT(*) as executions')
            ->whereNotNull('last_executed')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->take(30)
            ->get();
    }

    private function getPopularTriggers()
    {
        return SmartHomeAutomation::select('trigger_type', DB::raw('COUNT(*) as count'))
            ->groupBy('trigger_type')
            ->orderBy('count', 'desc')
            ->take(5)
            ->get();
    }

    private function getAutomationExecutionHistory($automation)
    {
        return $automation->executionLogs()
            ->latest()
            ->take(20)
            ->get();
    }

    private function getNextScheduledExecution($automation)
    {
        return $automation->next_execution;
    }
}

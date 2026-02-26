<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiFraudAlert;
use App\Models\FraudDetectionLog;
use App\Models\Property;
use App\Models\PropertyDuplicate;
use App\Models\User;
use App\Services\PropertyFraudDetectionService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * FraudDashboardController
 *
 * Admin controller for the AI Fraud Detection & Smart Duplicate Detection dashboard.
 * Provides:
 *  - Dashboard overview with statistics
 *  - Fraud alerts management
 *  - Duplicate properties review
 *  - Manual analysis triggers
 *  - User risk management
 */
class FraudDashboardController extends Controller
{
    public function __construct(
        private readonly PropertyFraudDetectionService $fraudService
    ) {
    }

    // =========================================================================
    // DASHBOARD VIEWS
    // =========================================================================

    /**
     * Main fraud detection dashboard.
     */
    public function index(Request $request)
    {
        $stats = $this->fraudService->getDashboardStats();

        // Recent flagged properties
        $flaggedProperties = Property::where('fraud_status', '!=', 'clean')
            ->with(['user:id,name,email', 'media' => fn($q) => $q->where('is_primary', true)])
            ->orderByDesc('fraud_score')
            ->paginate(15, ['*'], 'flagged_page');

        // Recent fraud alerts
        $recentAlerts = AiFraudAlert::with(['user:id,name,email', 'property:id,title,fraud_score'])
            ->orderByDesc('created_at')
            ->paginate(10, ['*'], 'alert_page');

        // Top detection types
        $detectionByType = FraudDetectionLog::select('detection_type', DB::raw('COUNT(*) as count'), DB::raw('AVG(fraud_score) as avg_score'))
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('detection_type')
            ->orderByDesc('count')
            ->get();

        // Trend chart data (last 14 days)
        $trendData = $this->getTrendData(14);

        return view('admin.fraud.dashboard', compact(
            'stats',
            'flaggedProperties',
            'recentAlerts',
            'detectionByType',
            'trendData'
        ));
    }

    /**
     * Duplicate properties review center.
     */
    public function duplicates(Request $request)
    {
        $status = $request->get('status', 'pending');
        $minScore = $request->get('min_score', 80);

        $duplicates = PropertyDuplicate::with([
            'property:id,title,price,city,fraud_score,created_at',
            'duplicateProperty:id,title,price,city,fraud_score,created_at',
            'reviewer:id,name',
        ])
            ->where('status', $status)
            ->where('similarity_score', '>=', $minScore)
            ->orderByDesc('similarity_score')
            ->paginate(20);

        return view('admin.fraud.duplicates', compact('duplicates', 'status', 'minScore'));
    }

    /**
     * View individual property fraud analysis.
     */
    public function propertyDetail(int $propertyId)
    {
        $property = Property::with(['user', 'media'])
            ->findOrFail($propertyId);

        $fraudLogs = FraudDetectionLog::forEntity('property', $propertyId)
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        $duplicateMatches = PropertyDuplicate::where('property_id', $propertyId)
            ->orWhere('duplicate_property_id', $propertyId)
            ->with(['property:id,title,price', 'duplicateProperty:id,title,price'])
            ->get();

        $alerts = AiFraudAlert::where('property_id', $propertyId)
            ->orderByDesc('created_at')
            ->get();

        return view('admin.fraud.property-detail', compact(
            'property',
            'fraudLogs',
            'duplicateMatches',
            'alerts'
        ));
    }

    /**
     * High-risk users list.
     */
    public function highRiskUsers(Request $request)
    {
        $minRisk = $request->get('min_risk', 50);

        $users = User::where('risk_score', '>=', $minRisk)
            ->orderByDesc('risk_score')
            ->paginate(25);

        return view('admin.fraud.high-risk-users', compact('users', 'minRisk'));
    }

    // =========================================================================
    // API / AJAX ACTIONS
    // =========================================================================

    /**
     * Manually trigger fraud analysis on a property.
     *
     * POST /admin/fraud/analyze/property/{id}
     */
    public function analyzeProperty(Request $request, int $propertyId): JsonResponse
    {
        $property = Property::find($propertyId);
        if (!$property) {
            return response()->json(['error' => 'Property not found'], 404);
        }

        $user = $property->user ?? null;

        try {
            // Run synchronously for manual triggers
            $result = $this->fraudService->analyzeProperty($property, $user);

            return response()->json([
                'success' => true,
                'message' => 'Fraud analysis completed',
                'result' => [
                    'total_fraud_score' => $result['total_fraud_score'],
                    'final_status' => $result['final_status'],
                    'action' => $result['action'],
                    'fraud_flags' => $result['fraud_flags'],
                    'execution_ms' => $result['execution_ms'],
                    'modules' => array_map(fn($m) => [
                        'score' => $m['fraud_score'] ?? $m['anomaly_score'] ?? $m['risk_score'] ?? $m['highest_score'] ?? 0,
                        'flagged' => ($m['fraud_score'] ?? $m['anomaly_score'] ?? $m['risk_score'] ?? 0) > 30,
                    ], $result['modules']),
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('FraudDashboard: Analysis failed', [
                'property_id' => $propertyId,
                'error' => $e->getMessage(),
            ]);
            return response()->json(['error' => 'Analysis failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update fraud status for a property (admin override).
     *
     * PATCH /admin/fraud/properties/{id}/status
     */
    public function updatePropertyStatus(Request $request, int $propertyId): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:clean,suspicious,blocked,reviewed,cleared',
            'notes' => 'nullable|string|max:500',
        ]);

        $property = Property::find($propertyId);
        if (!$property) {
            return response()->json(['error' => 'Property not found'], 404);
        }

        $property->update(['fraud_status' => $request->status]);

        Log::info('FraudDashboard: Admin updated property fraud status', [
            'property_id' => $propertyId,
            'status' => $request->status,
            'admin_id' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Property status updated to '{$request->status}'",
        ]);
    }

    /**
     * Review a duplicate pair (confirm or reject).
     *
     * PATCH /admin/fraud/duplicates/{id}/review
     */
    public function reviewDuplicate(Request $request, int $duplicateId): JsonResponse
    {
        $request->validate([
            'action' => 'required|in:confirm,reject',
            'notes' => 'nullable|string|max:500',
        ]);

        $duplicate = PropertyDuplicate::find($duplicateId);
        if (!$duplicate) {
            return response()->json(['error' => 'Duplicate record not found'], 404);
        }

        $adminId = auth()->id();
        $notes = $request->notes ?? '';

        if ($request->action === 'confirm') {
            $duplicate->confirm($adminId, $notes);
            $message = 'Duplicate confirmed. The duplicate property has been blocked.';
        } else {
            $duplicate->reject($adminId, $notes);
            $message = 'Duplicate rejected. Properties remain active.';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }

    /**
     * Resolve a fraud alert.
     *
     * PATCH /admin/fraud/alerts/{id}/resolve
     */
    public function resolveAlert(Request $request, int $alertId): JsonResponse
    {
        $request->validate([
            'resolution' => 'required|in:resolved,dismissed,escalated',
            'notes' => 'nullable|string|max:1000',
        ]);

        $alert = AiFraudAlert::find($alertId);
        if (!$alert) {
            return response()->json(['error' => 'Alert not found'], 404);
        }

        $adminId = auth()->id();
        $notes = $request->notes ?? 'No notes provided';

        try {
            switch ($request->resolution) {
                case 'resolved':
                    $alert->verify($adminId);
                    $alert->confirmFraud(['notes' => $notes]);
                    break;
                case 'dismissed':
                    $alert->dismiss($notes);
                    break;
                case 'escalated':
                    // Just a dummy escalation for now
                    $alert->escalate($adminId);
                    $alert->addInvestigationNote("Escalated: " . $notes);
                    break;
            }

            return response()->json([
                'success' => true,
                'message' => "Alert marked as {$request->resolution}",
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Lift restriction on a user.
     *
     * PATCH /admin/fraud/users/{id}/lift-restriction
     */
    public function liftUserRestriction(Request $request, int $userId): JsonResponse
    {
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $user->update([
            'is_restricted' => false,
            'restriction_reason' => null,
            'requires_manual_verification' => false,
            'account_status' => 'active',
        ]);

        Log::info('FraudDashboard: Admin lifted user restriction', [
            'user_id' => $userId,
            'admin_id' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User restriction lifted successfully',
        ]);
    }

    /**
     * Get statistics for the dashboard AJAX refresh.
     *
     * GET /admin/fraud/stats
     */
    public function getStats(): JsonResponse
    {
        return response()->json($this->fraudService->getDashboardStats());
    }

    /**
     * Get fraud trend data for charting.
     *
     * GET /admin/fraud/chart-data?days=14
     */
    public function chartData(Request $request): JsonResponse
    {
        $days = min(90, max(7, (int) $request->get('days', 14)));
        return response()->json($this->getTrendData($days));
    }

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

    private function getTrendData(int $days): array
    {
        $dates = collect();
        for ($i = $days - 1; $i >= 0; $i--) {
            $dates->push(now()->subDays($i)->format('Y-m-d'));
        }

        $logs = FraudDetectionLog::select(
            DB::raw("DATE(created_at) as date"),
            DB::raw("COUNT(*) as count"),
            DB::raw("AVG(fraud_score) as avg_score"),
            'detection_type'
        )
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('date', 'detection_type')
            ->get();

        $alerts = AiFraudAlert::select(DB::raw("DATE(created_at) as date"), DB::raw("COUNT(*) as count"))
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('date')
            ->pluck('count', 'date');

        return [
            'labels' => $dates->toArray(),
            'detection_logs' => $logs,
            'alert_counts' => $dates->mapWithKeys(fn($d) => [$d => $alerts[$d] ?? 0])->values()->toArray(),
        ];
    }
}

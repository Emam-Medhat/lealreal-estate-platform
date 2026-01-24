<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Http\Requests\Agent\UpdateCommissionRequest;
use App\Models\Agent;
use App\Models\AgentCommission;
use App\Models\Property;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AgentCommissionController extends Controller
{
    public function index(Request $request)
    {
        $agent = Auth::user()->agent;
        
        $commissions = $agent->commissions()
            ->with(['property', 'transaction'])
            ->when($request->search, function ($query, $search) {
                $query->where('reference_id', 'like', "%{$search}%")
                    ->orWhereHas('property', function ($propertyQuery) use ($search) {
                        $propertyQuery->where('title', 'like', "%{$search}%");
                    });
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->type, function ($query, $type) {
                $query->where('type', $type);
            })
            ->when($request->date_from, function ($query, $date) {
                $query->whereDate('created_at', '>=', $date);
            })
            ->when($request->date_to, function ($query, $date) {
                $query->whereDate('created_at', '<=', $date);
            })
            ->latest()
            ->paginate(20);

        return view('agent.commissions.index', compact('commissions'));
    }

    public function show(AgentCommission $commission)
    {
        $this->authorize('view', $commission);
        
        $commission->load(['property', 'transaction', 'agent.profile']);
        
        return view('agent.commissions.show', compact('commission'));
    }

    public function create()
    {
        $agent = Auth::user()->agent;
        $properties = $agent->properties()->where('status', 'sold')->get(['id', 'title']);
        
        return view('agent.commissions.create', compact('properties'));
    }

    public function store(Request $request)
    {
        $agent = Auth::user()->agent;
        
        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'amount' => 'required|numeric|min:0',
            'type' => 'required|in:sale,rental,referral,bonus',
            'percentage' => 'nullable|numeric|min:0|max:100',
            'description' => 'required|string|max:1000',
            'commission_date' => 'required|date',
        ]);

        $commission = AgentCommission::create([
            'agent_id' => $agent->id,
            'property_id' => $request->property_id,
            'amount' => $request->amount,
            'type' => $request->type,
            'percentage' => $request->percentage,
            'description' => $request->description,
            'commission_date' => $request->commission_date,
            'status' => 'pending',
            'reference_id' => 'COM_' . strtoupper(uniqid()),
        ]);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'created_commission',
            'details' => "Created commission: {$commission->reference_id}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('agent.commissions.show', $commission)
            ->with('success', 'Commission created successfully.');
    }

    public function edit(AgentCommission $commission)
    {
        $this->authorize('update', $commission);
        
        $agent = Auth::user()->agent;
        $properties = $agent->properties()->where('status', 'sold')->get(['id', 'title']);
        $commission->load(['property']);
        
        return view('agent.commissions.edit', compact('commission', 'properties'));
    }

    public function update(UpdateCommissionRequest $request, AgentCommission $commission)
    {
        $this->authorize('update', $commission);
        
        $commission->update([
            'amount' => $request->amount,
            'type' => $request->type,
            'percentage' => $request->percentage,
            'description' => $request->description,
            'commission_date' => $request->commission_date,
        ]);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated_commission',
            'details' => "Updated commission: {$commission->reference_id}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('agent.commissions.show', $commission)
            ->with('success', 'Commission updated successfully.');
    }

    public function destroy(AgentCommission $commission)
    {
        $this->authorize('delete', $commission);
        
        $commissionReference = $commission->reference_id;
        $commission->delete();

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'deleted_commission',
            'details' => "Deleted commission: {$commissionReference}",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('agent.commissions.index')
            ->with('success', 'Commission deleted successfully.');
    }

    public function updateStatus(Request $request, AgentCommission $commission): JsonResponse
    {
        $this->authorize('update', $commission);
        
        $request->validate([
            'status' => 'required|in:pending,approved,paid,rejected',
        ]);

        $commission->update([
            'status' => $request->status,
            'status_updated_at' => now(),
        ]);

        if ($request->status === 'paid') {
            $commission->update(['paid_at' => now()]);
        }

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated_commission_status',
            'details' => "Updated commission {$commission->reference_id} status to {$request->status}",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'status' => $request->status,
            'message' => 'Commission status updated successfully'
        ]);
    }

    public function requestPayment(Request $request, AgentCommission $commission): JsonResponse
    {
        $this->authorize('update', $commission);
        
        if ($commission->status !== 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Commission must be approved before requesting payment'
            ]);
        }

        $commission->update([
            'payment_requested_at' => now(),
            'status' => 'payment_requested',
        ]);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'requested_commission_payment',
            'details' => "Requested payment for commission: {$commission->reference_id}",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment requested successfully'
        ]);
    }

    public function getCommissionStats(): JsonResponse
    {
        $agent = Auth::user()->agent;
        
        $stats = [
            'total_commissions' => $agent->commissions()->sum('amount'),
            'pending_commissions' => $agent->commissions()->where('status', 'pending')->sum('amount'),
            'approved_commissions' => $agent->commissions()->where('status', 'approved')->sum('amount'),
            'paid_commissions' => $agent->commissions()->where('status', 'paid')->sum('amount'),
            'this_month_commissions' => $agent->commissions()
                ->whereMonth('commission_date', now()->month)
                ->whereYear('commission_date', now()->year)
                ->sum('amount'),
            'this_year_commissions' => $agent->commissions()
                ->whereYear('commission_date', now()->year)
                ->sum('amount'),
            'total_count' => $agent->commissions()->count(),
            'paid_count' => $agent->commissions()->where('status', 'paid')->count(),
            'pending_count' => $agent->commissions()->where('status', 'pending')->count(),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    public function getCommissionChartData(): JsonResponse
    {
        $agent = Auth::user()->agent;
        
        // Monthly commissions for the last 12 months
        $monthlyData = $agent->commissions()
            ->where('commission_date', '>=', now()->subMonths(12))
            ->selectRaw('DATE_FORMAT(commission_date, "%Y-%m") as month, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Commission by type
        $typeData = $agent->commissions()
            ->selectRaw('type, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('type')
            ->get();

        // Commission status distribution
        $statusData = $agent->commissions()
            ->selectRaw('status, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        return response()->json([
            'success' => true,
            'monthly_data' => $monthlyData,
            'type_data' => $typeData,
            'status_data' => $statusData,
        ]);
    }

    public function getRecentCommissions(Request $request): JsonResponse
    {
        $agent = Auth::user()->agent;
        
        $commissions = $agent->commissions()
            ->with(['property'])
            ->latest()
            ->limit($request->limit ?? 5)
            ->get();

        return response()->json([
            'success' => true,
            'commissions' => $commissions
        ]);
    }

    public function exportCommissions(Request $request): JsonResponse
    {
        $request->validate([
            'format' => 'required|in:json,csv,xlsx',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        $agent = Auth::user()->agent;
        
        $query = $agent->commissions()->with(['property', 'transaction']);

        if ($request->date_from) {
            $query->whereDate('commission_date', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('commission_date', '<=', $request->date_to);
        }

        $commissions = $query->get();

        $filename = "agent_commissions_export_" . now()->format('Y-m-d_H-i-s');

        return response()->json([
            'success' => true,
            'data' => $commissions,
            'filename' => $filename,
            'message' => 'Commissions exported successfully'
        ]);
    }

    public function calculateCommission(Request $request): JsonResponse
    {
        $request->validate([
            'property_price' => 'required|numeric|min:0',
            'commission_rate' => 'required|numeric|min:0|max:100',
            'commission_type' => 'required|in:percentage,fixed',
        ]);

        $propertyPrice = $request->property_price;
        $commissionRate = $request->commission_rate;
        $commissionType = $request->commission_type;

        $commissionAmount = $commissionType === 'percentage' 
            ? ($propertyPrice * $commissionRate) / 100 
            : $commissionRate;

        return response()->json([
            'success' => true,
            'commission_amount' => $commissionAmount,
            'formatted_amount' => number_format($commissionAmount, 2),
            'calculation_details' => [
                'property_price' => $propertyPrice,
                'commission_rate' => $commissionRate,
                'commission_type' => $commissionType,
            ]
        ]);
    }
}

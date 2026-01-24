<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceRequest;
use App\Models\MaintenanceSchedule;
use App\Models\MaintenanceTicket;
use App\Models\WorkOrder;
use App\Models\ServiceProvider;
use App\Models\MaintenanceTeam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MaintenanceController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_requests' => MaintenanceRequest::count(),
            'pending_requests' => MaintenanceRequest::where('status', 'pending')->count(),
            'in_progress' => MaintenanceRequest::where('status', 'in_progress')->count(),
            'completed_today' => MaintenanceRequest::where('status', 'completed')
                ->whereDate('completed_at', today())->count(),
            'emergency_requests' => MaintenanceRequest::where('priority', 'emergency')->count(),
            'overdue_requests' => MaintenanceRequest::where('due_date', '<', now())
                ->where('status', '!=', 'completed')->count(),
        ];

        $recentRequests = MaintenanceRequest::with(['property', 'serviceProvider'])
            ->latest()->take(5)->get();

        $upcomingSchedule = MaintenanceSchedule::with(['property', 'maintenanceTeam'])
            ->where('scheduled_date', '>=', now())
            ->where('scheduled_date', '<=', now()->addDays(7))
            ->orderBy('scheduled_date')
            ->take(10)->get();

        return view('maintenance.dashboard', compact('stats', 'recentRequests', 'upcomingSchedule'));
    }

    public function index()
    {
        $requests = MaintenanceRequest::with(['property', 'serviceProvider', 'assignedTeam'])
            ->when(request('status'), function($query, $status) {
                $query->where('status', $status);
            })
            ->when(request('priority'), function($query, $priority) {
                $query->where('priority', $priority);
            })
            ->when(request('property_id'), function($query, $propertyId) {
                $query->where('property_id', $propertyId);
            })
            ->latest()->paginate(15);

        return view('maintenance.index', compact('requests'));
    }

    public function create()
    {
        $properties = \App\Models\Property::all();
        $serviceProviders = ServiceProvider::where('is_active', true)->get();
        $maintenanceTeams = MaintenanceTeam::where('is_active', true)->get();

        return view('maintenance.create', compact('properties', 'serviceProviders', 'maintenanceTeams'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high,emergency',
            'category' => 'required|in:plumbing,electrical,hvac,structural,general',
            'estimated_cost' => 'nullable|numeric|min:0',
            'due_date' => 'nullable|date|after:today',
            'service_provider_id' => 'nullable|exists:service_providers,id',
            'assigned_team_id' => 'nullable|exists:maintenance_teams,id',
            'notes' => 'nullable|string',
        ]);

        $validated['request_number'] = 'REQ-' . date('Y') . '-' . str_pad(MaintenanceRequest::count() + 1, 4, '0', STR_PAD_LEFT);
        $validated['status'] = 'pending';
        $validated['requested_by'] = auth()->id();

        MaintenanceRequest::create($validated);

        return redirect()->route('maintenance.index')
            ->with('success', 'تم إنشاء طلب الصيانة بنجاح');
    }

    public function show(MaintenanceRequest $maintenance)
    {
        $maintenance->load(['property', 'serviceProvider', 'assignedTeam', 'workOrders', 'tickets']);
        
        return view('maintenance.show', compact('maintenance'));
    }

    public function edit(MaintenanceRequest $maintenance)
    {
        $properties = \App\Models\Property::all();
        $serviceProviders = ServiceProvider::where('is_active', true)->get();
        $maintenanceTeams = MaintenanceTeam::where('is_active', true)->get();

        return view('maintenance.edit', compact('maintenance', 'properties', 'serviceProviders', 'maintenanceTeams'));
    }

    public function update(Request $request, MaintenanceRequest $maintenance)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high,emergency',
            'category' => 'required|in:plumbing,electrical,hvac,structural,general',
            'estimated_cost' => 'nullable|numeric|min:0',
            'due_date' => 'nullable|date|after:today',
            'service_provider_id' => 'nullable|exists:service_providers,id',
            'assigned_team_id' => 'nullable|exists:maintenance_teams,id',
            'notes' => 'nullable|string',
        ]);

        $maintenance->update($validated);

        return redirect()->route('maintenance.show', $maintenance)
            ->with('success', 'تم تحديث طلب الصيانة بنجاح');
    }

    public function destroy(MaintenanceRequest $maintenance)
    {
        if ($maintenance->status !== 'pending') {
            return back()->with('error', 'لا يمكن حذف طلب الصيانة الذي تم بدء العمل عليه');
        }

        $maintenance->delete();

        return redirect()->route('maintenance.index')
            ->with('success', 'تم حذف طلب الصيانة بنجاح');
    }

    public function assign(MaintenanceRequest $maintenance, Request $request)
    {
        $validated = $request->validate([
            'service_provider_id' => 'required|exists:service_providers,id',
            'assigned_team_id' => 'nullable|exists:maintenance_teams,id',
            'notes' => 'nullable|string',
        ]);

        $maintenance->update([
            'service_provider_id' => $validated['service_provider_id'],
            'assigned_team_id' => $validated['assigned_team_id'],
            'status' => 'assigned',
            'assigned_at' => now(),
            'assigned_by' => auth()->id(),
        ]);

        return redirect()->route('maintenance.show', $maintenance)
            ->with('success', 'تم تكليف مقدم الخدمة بنجاح');
    }

    public function startWork(MaintenanceRequest $maintenance)
    {
        if ($maintenance->status !== 'assigned') {
            return back()->with('error', 'يجب تكليف مقدم الخدمة أولاً');
        }

        $maintenance->update([
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        return redirect()->route('maintenance.show', $maintenance)
            ->with('success', 'تم بدء العمل على طلب الصيانة');
    }

    public function complete(MaintenanceRequest $maintenance, Request $request)
    {
        $validated = $request->validate([
            'actual_cost' => 'required|numeric|min:0',
            'completion_notes' => 'required|string',
            'next_maintenance_date' => 'nullable|date|after:today',
        ]);

        $maintenance->update([
            'status' => 'completed',
            'actual_cost' => $validated['actual_cost'],
            'completion_notes' => $validated['completion_notes'],
            'next_maintenance_date' => $validated['next_maintenance_date'],
            'completed_at' => now(),
            'completed_by' => auth()->id(),
        ]);

        return redirect()->route('maintenance.show', $maintenance)
            ->with('success', 'تم إكمال طلب الصيانة بنجاح');
    }

    public function export(Request $request)
    {
        $requests = MaintenanceRequest::with(['property', 'serviceProvider'])
            ->when($request->status, function($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->priority, function($query, $priority) {
                $query->where('priority', $priority);
            })
            ->when($request->date_from, function($query, $dateFrom) {
                $query->whereDate('created_at', '>=', $dateFrom);
            })
            ->when($request->date_to, function($query, $dateTo) {
                $query->whereDate('created_at', '<=', $dateTo);
            })
            ->get();

        $filename = 'maintenance_requests_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($requests) {
            $file = fopen('php://output', 'w');
            
            // CSV Header
            fputcsv($file, [
                'رقم الطلب',
                'العقار',
                'العنوان',
                'الأولوية',
                'الفئة',
                'الحالة',
                'مقدم الخدمة',
                'التكلفة التقديرية',
                'التاريخ',
            ]);

            // CSV Data
            foreach ($requests as $request) {
                fputcsv($file, [
                    $request->request_number,
                    $request->property->title ?? '',
                    $request->title,
                    $this->getPriorityLabel($request->priority),
                    $this->getCategoryLabel($request->category),
                    $this->getStatusLabel($request->status),
                    $request->serviceProvider->name ?? '',
                    $request->estimated_cost,
                    $request->created_at->format('Y-m-d'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function getPriorityLabel($priority)
    {
        $labels = [
            'low' => 'منخفض',
            'medium' => 'متوسط',
            'high' => 'عالي',
            'emergency' => 'طوارئ',
        ];

        return $labels[$priority] ?? $priority;
    }

    private function getCategoryLabel($category)
    {
        $labels = [
            'plumbing' => 'سباكة',
            'electrical' => 'كهرباء',
            'hvac' => 'تكييف',
            'structural' => 'إنشائي',
            'general' => 'عام',
        ];

        return $labels[$category] ?? $category;
    }

    private function getStatusLabel($status)
    {
        $labels = [
            'pending' => 'في انتظار',
            'assigned' => 'مكلف',
            'in_progress' => 'قيد التنفيذ',
            'completed' => 'مكتمل',
            'cancelled' => 'ملغي',
        ];

        return $labels[$status] ?? $status;
    }
}

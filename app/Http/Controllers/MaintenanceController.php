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

        // Get recent requests for the sidebar
        $recentRequests = MaintenanceRequest::with(['property', 'serviceProvider'])
            ->latest()->take(5)->get();

        // Get today's schedules
        $todaySchedules = MaintenanceSchedule::with(['property', 'maintenanceTeam'])
            ->whereDate('scheduled_date', today())
            ->orderBy('scheduled_time')
            ->get();

        return view('maintenance.index', compact('requests', 'recentRequests', 'todaySchedules'));
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
            'priority' => 'required|in:low,medium,high,urgent',
            'category' => 'required|in:plumbing,electrical,hvac,structural,painting,landscaping,other',
            'estimated_cost' => 'nullable|numeric|min:0',
            'scheduled_date' => 'nullable|date|after:today',
            'assigned_to' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
        ]);

        $validated['status'] = 'pending';
        
        // Only use fields that exist in the database
        $data = [
            'property_id' => $validated['property_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'priority' => $validated['priority'],
            'category' => $validated['category'],
            'estimated_cost' => $validated['estimated_cost'] ?? null,
            'scheduled_date' => $validated['scheduled_date'] ?? null,
            'assigned_to' => $validated['assigned_to'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'status' => 'pending',
            'user_id' => auth()->id(),
        ];
        
        MaintenanceRequest::create($data);

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

    // Schedule Management Methods
    public function scheduleIndex()
    {
        $schedules = MaintenanceSchedule::with(['property', 'serviceProvider', 'assignedTeam'])
            ->orderBy('scheduled_date')
            ->paginate(15);

        return view('maintenance.schedule.index', compact('schedules'));
    }

    public function scheduleCreate()
    {
        $properties = \App\Models\Property::where('status', 'active')->get();
        $serviceProviders = ServiceProvider::where('is_active', true)->get();
        $teams = MaintenanceTeam::where('is_active', true)->get();

        return view('maintenance.schedule.create', compact('properties', 'serviceProviders', 'teams'));
    }

    public function scheduleStore(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'service_provider_id' => 'nullable|exists:service_providers,id',
            'team_id' => 'nullable|exists:maintenance_teams,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'scheduled_date' => 'required|date',
            'estimated_duration' => 'nullable|integer',
            'priority' => 'required|in:low,medium,high,emergency',
            'category' => 'required|in:plumbing,electrical,hvac,structural,general',
        ]);

        MaintenanceSchedule::create($validated);

        return redirect()->route('maintenance.schedule.index')
            ->with('success', 'Maintenance schedule created successfully.');
    }

    public function scheduleShow(MaintenanceSchedule $schedule)
    {
        $schedule->load(['property', 'serviceProvider', 'assignedTeam']);
        return view('maintenance.schedule.show', compact('schedule'));
    }

    public function scheduleEdit(MaintenanceSchedule $schedule)
    {
        $properties = \App\Models\Property::where('status', 'active')->get();
        $serviceProviders = ServiceProvider::where('is_active', true)->get();
        $teams = MaintenanceTeam::where('is_active', true)->get();

        return view('maintenance.schedule.edit', compact('schedule', 'properties', 'serviceProviders', 'teams'));
    }

    public function scheduleUpdate(Request $request, MaintenanceSchedule $schedule)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'service_provider_id' => 'nullable|exists:service_providers,id',
            'team_id' => 'nullable|exists:maintenance_teams,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'scheduled_date' => 'required|date',
            'estimated_duration' => 'nullable|integer',
            'priority' => 'required|in:low,medium,high,emergency',
            'category' => 'required|in:plumbing,electrical,hvac,structural,general',
        ]);

        $schedule->update($validated);

        return redirect()->route('maintenance.schedule.index')
            ->with('success', 'Maintenance schedule updated successfully.');
    }

    public function scheduleDestroy(MaintenanceSchedule $schedule)
    {
        $schedule->delete();

        return redirect()->route('maintenance.schedule.index')
            ->with('success', 'Maintenance schedule deleted successfully.');
    }

    // Work Order Management Methods
    public function workOrderIndex()
    {
        $workOrders = WorkOrder::with(['property', 'serviceProvider', 'assignedTeam', 'maintenanceRequest'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('maintenance.workorders.index', compact('workOrders'));
    }

    public function workOrderCreate()
    {
        $properties = \App\Models\Property::where('status', 'active')->get();
        $serviceProviders = ServiceProvider::where('is_active', true)->get();
        $teams = MaintenanceTeam::where('is_active', true)->get();
        $maintenanceRequests = MaintenanceRequest::where('status', 'approved')->get();

        return view('maintenance.workorders.create', compact('properties', 'serviceProviders', 'teams', 'maintenanceRequests'));
    }

    public function workOrderStore(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'maintenance_request_id' => 'nullable|exists:maintenance_requests,id',
            'service_provider_id' => 'nullable|exists:service_providers,id',
            'team_id' => 'nullable|exists:maintenance_teams,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high,emergency',
            'category' => 'required|in:plumbing,electrical,hvac,structural,general',
            'estimated_cost' => 'nullable|numeric',
            'estimated_duration' => 'nullable|integer',
            'scheduled_date' => 'nullable|date',
        ]);

        $validated['status'] = 'pending';
        $validated['created_by'] = auth()->id();

        WorkOrder::create($validated);

        return redirect()->route('maintenance.workorders.index')
            ->with('success', 'Work order created successfully.');
    }

    public function workOrderShow(WorkOrder $workOrder)
    {
        $workOrder->load(['property', 'serviceProvider', 'assignedTeam', 'maintenanceRequest', 'creator']);
        return view('maintenance.workorders.show', compact('workOrder'));
    }

    public function workOrderEdit(WorkOrder $workOrder)
    {
        $properties = \App\Models\Property::where('status', 'active')->get();
        $serviceProviders = ServiceProvider::where('is_active', true)->get();
        $teams = MaintenanceTeam::where('is_active', true)->get();
        $maintenanceRequests = MaintenanceRequest::where('status', 'approved')->get();

        return view('maintenance.workorders.edit', compact('workOrder', 'properties', 'serviceProviders', 'teams', 'maintenanceRequests'));
    }

    public function workOrderUpdate(Request $request, WorkOrder $workOrder)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'maintenance_request_id' => 'nullable|exists:maintenance_requests,id',
            'service_provider_id' => 'nullable|exists:service_providers,id',
            'team_id' => 'nullable|exists:maintenance_teams,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high,emergency',
            'category' => 'required|in:plumbing,electrical,hvac,structural,general',
            'estimated_cost' => 'nullable|numeric',
            'estimated_duration' => 'nullable|integer',
            'scheduled_date' => 'nullable|date',
        ]);

        $workOrder->update($validated);

        return redirect()->route('maintenance.workorders.index')
            ->with('success', 'Work order updated successfully.');
    }

    public function workOrderDestroy(WorkOrder $workOrder)
    {
        $workOrder->delete();

        return redirect()->route('maintenance.workorders.index')
            ->with('success', 'Work order deleted successfully.');
    }

    // Work Order Actions
    public function workOrderAssign(Request $request, WorkOrder $workOrder)
    {
        $validated = $request->validate([
            'service_provider_id' => 'required|exists:service_providers,id',
            'team_id' => 'nullable|exists:maintenance_teams,id',
        ]);

        $workOrder->update([
            'service_provider_id' => $validated['service_provider_id'],
            'team_id' => $validated['team_id'] ?? null,
            'status' => 'assigned',
            'assigned_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Work order assigned successfully.');
    }

    public function workOrderStart(WorkOrder $workOrder)
    {
        $workOrder->update([
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Work order started successfully.');
    }

    public function workOrderComplete(Request $request, WorkOrder $workOrder)
    {
        $validated = $request->validate([
            'actual_cost' => 'nullable|numeric',
            'completion_notes' => 'nullable|string',
        ]);

        $workOrder->update([
            'status' => 'completed',
            'completed_at' => now(),
            'actual_cost' => $validated['actual_cost'],
            'completion_notes' => $validated['completion_notes'],
        ]);

        return redirect()->back()->with('success', 'Work order completed successfully.');
    }

    public function workOrderCancel(WorkOrder $workOrder)
    {
        $workOrder->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Work order cancelled successfully.');
    }

    public function workOrderApprove(WorkOrder $workOrder)
    {
        $workOrder->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Work order approved successfully.');
    }

    public function workOrderReject(WorkOrder $workOrder)
    {
        $workOrder->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'rejected_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Work order rejected successfully.');
    }

    // Maintenance Teams Management Methods
    public function teamIndex()
    {
        $teams = MaintenanceTeam::with(['members', 'workOrders'])
            ->orderBy('name')
            ->paginate(15);

        return view('maintenance.teams.index', compact('teams'));
    }

    public function teamCreate()
    {
        $users = \App\Models\User::where('status', 'active')->get();
        return view('maintenance.teams.create', compact('users'));
    }

    public function teamStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'leader_id' => 'nullable|exists:users,id',
            'contact_email' => 'nullable|email',
            'contact_phone' => 'nullable|string',
            'specializations' => 'nullable|array',
            'specializations.*' => 'in:plumbing,electrical,hvac,structural,general',
        ]);

        $validated['is_active'] = true;
        $team = MaintenanceTeam::create($validated);

        // Add members if provided
        if ($request->has('members')) {
            $team->members()->attach($request->members);
        }

        return redirect()->route('maintenance.teams.index')
            ->with('success', 'Maintenance team created successfully.');
    }

    public function teamShow(MaintenanceTeam $team)
    {
        $team->load(['members', 'workOrders' => function($query) {
            $query->latest()->limit(10);
        }]);
        
        return view('maintenance.teams.show', compact('team'));
    }

    public function teamEdit(MaintenanceTeam $team)
    {
        $users = \App\Models\User::where('status', 'active')->get();
        $team->load('members');
        
        return view('maintenance.teams.edit', compact('team', 'users'));
    }

    public function teamUpdate(Request $request, MaintenanceTeam $team)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'leader_id' => 'nullable|exists:users,id',
            'contact_email' => 'nullable|email',
            'contact_phone' => 'nullable|string',
            'specializations' => 'nullable|array',
            'specializations.*' => 'in:plumbing,electrical,hvac,structural,general',
        ]);

        $team->update($validated);

        // Update members if provided
        if ($request->has('members')) {
            $team->members()->sync($request->members);
        }

        return redirect()->route('maintenance.teams.index')
            ->with('success', 'Maintenance team updated successfully.');
    }

    public function teamDestroy(MaintenanceTeam $team)
    {
        // Check if team has active work orders
        $activeWorkOrders = $team->workOrders()->whereIn('status', ['pending', 'assigned', 'in_progress'])->count();
        
        if ($activeWorkOrders > 0) {
            return redirect()->back()->with('error', 'Cannot delete team with active work orders.');
        }

        $team->delete();

        return redirect()->route('maintenance.teams.index')
            ->with('success', 'Maintenance team deleted successfully.');
    }

    // Team Actions
    public function teamAddMember(Request $request, MaintenanceTeam $team)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'nullable|in:member,leader,supervisor',
        ]);

        // Check if user is already a member
        if ($team->members()->where('user_id', $validated['user_id'])->exists()) {
            return redirect()->back()->with('error', 'User is already a member of this team.');
        }

        $team->members()->attach($validated['user_id'], [
            'role' => $validated['role'] ?? 'member',
            'joined_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Team member added successfully.');
    }

    public function teamRemoveMember(MaintenanceTeam $team, \App\Models\User $user)
    {
        // Check if user is the leader
        if ($team->leader_id === $user->id) {
            return redirect()->back()->with('error', 'Cannot remove team leader. Assign a new leader first.');
        }

        $team->members()->detach($user->id);

        return redirect()->back()->with('success', 'Team member removed successfully.');
    }

    public function teamToggleStatus(MaintenanceTeam $team)
    {
        $team->update([
            'is_active' => !$team->is_active,
        ]);

        $status = $team->is_active ? 'activated' : 'deactivated';
        return redirect()->back()->with("success", "Team {$status} successfully.");
    }

    public function teamWorkload(MaintenanceTeam $team)
    {
        $workOrders = $team->workOrders()
            ->with(['property'])
            ->orderBy('priority', 'desc')
            ->orderBy('created_at')
            ->get();

        $stats = [
            'total' => $workOrders->count(),
            'pending' => $workOrders->where('status', 'pending')->count(),
            'in_progress' => $workOrders->where('status', 'in_progress')->count(),
            'completed_this_month' => $workOrders->where('status', 'completed')
                ->where('completed_at', '>=', now()->startOfMonth())->count(),
        ];

        return view('maintenance.teams.workload', compact('team', 'workOrders', 'stats'));
    }
}

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
            ->orderBy('scheduled_date', 'asc')
            ->get();

        return view('maintenance.index', compact('requests', 'recentRequests', 'todaySchedules'));
    }

    public function create()
    {
        $properties = \App\Models\Property::all();
        $serviceProviders = ServiceProvider::where('is_active', true)->get();
        $maintenanceTeams = MaintenanceTeam::where('status', 'active')->get();

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
        $maintenanceTeams = MaintenanceTeam::where('status', 'active')->get();

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
        $teams = MaintenanceTeam::where('status', 'active')->get();

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
        $teams = MaintenanceTeam::where('status', 'active')->get();

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
        $teams = MaintenanceTeam::where('status', 'active')->get();
        $maintenanceRequests = MaintenanceRequest::where('status', 'approved')->get();

        return view('maintenance.workorders.create', compact('properties', 'serviceProviders', 'teams', 'maintenanceRequests'));
    }

    public function workOrderStore(Request $request)
    {
        $validated = $request->validate([
            'work_order_number' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'title_ar' => 'nullable|string|max:255',
            'description' => 'required|string',
            'description_ar' => 'nullable|string',
            'priority' => 'required|in:low,medium,high,emergency',
            'type' => 'required|in:repair,maintenance,installation,inspection,replacement,other',
            'property_id' => 'nullable|exists:properties,id',
            'assigned_to' => 'nullable|exists:users,id',
            'assigned_team_id' => 'nullable|exists:maintenance_teams,id',
            'service_provider_id' => 'nullable|exists:service_providers,id',
            'maintenance_request_id' => 'nullable|exists:maintenance_requests,id',
            'location' => 'nullable|string|max:255',
            'location_ar' => 'nullable|string|max:255',
            'scheduled_date' => 'nullable|date',
            'scheduled_time' => 'nullable|date_format:H:i',
            'estimated_duration' => 'nullable|integer|min:1',
            'estimated_cost' => 'nullable|numeric|min:0',
            'labor_cost' => 'nullable|numeric|min:0',
            'material_cost' => 'nullable|numeric|min:0',
            'other_cost' => 'nullable|numeric|min:0',
            'access_instructions' => 'nullable|string',
            'access_instructions_ar' => 'nullable|string',
            'safety_requirements' => 'nullable|string',
            'safety_requirements_ar' => 'nullable|string',
            'notes' => 'nullable|string',
            'notes_ar' => 'nullable|string',
        ]);

        $validated['status'] = 'pending';
        $validated['created_by'] = auth()->id();

        WorkOrder::create($validated);

        return redirect()->route('maintenance.workorders.index')
            ->with('success', 'Work order created successfully.');
    }

    public function workOrderShow(WorkOrder $workOrder)
    {
        $workOrder->load(['property', 'serviceProvider', 'assignedTeam', 'maintenanceRequest', 'createdBy']);
        return view('maintenance.workorders.show', compact('workOrder'));
    }

    public function workOrderEdit(WorkOrder $workOrder)
    {
        $properties = \App\Models\Property::where('status', 'active')->get();
        $serviceProviders = ServiceProvider::where('is_active', true)->get();
        $teams = MaintenanceTeam::where('status', 'active')->get();
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
            'actual_cost' => $validated['actual_cost'] ?? null,
            'completion_notes' => $validated['completion_notes'] ?? null,
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
        $teams = MaintenanceTeam::with(['members', 'teamLeader', 'workOrders'])
            ->orderBy('name')
            ->paginate(15);

        return view('maintenance.teams.index', compact('teams'));
    }

    public function teamCreate()
    {
        $users = \App\Models\User::where('email_verified_at', '!=', null)->get();
        return view('maintenance.teams.create', compact('users'));
    }

    public function teamStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'team_code' => 'nullable|string|max:255',
            'team_leader_id' => 'nullable|exists:users,id',
            'specialization' => 'nullable|in:general,electrical,plumbing,hvac,structural,painting,landscaping',
            'contact_email' => 'nullable|email',
            'contact_phone' => 'nullable|string',
            'working_hours_start' => 'nullable|date_format:H:i',
            'working_hours_end' => 'nullable|date_format:H:i',
            'max_concurrent_jobs' => 'nullable|integer|min:1|max:20',
            'notes' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        // Map form fields to database columns
        $teamData = [
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'specialization' => $validated['specialization'] ?? 'general',
            'max_concurrent_jobs' => $validated['max_concurrent_jobs'] ?? 3,
            'notes' => $validated['notes'] ?? null,
            'is_active' => isset($validated['is_active']) ? $validated['is_active'] : true,
            'leader_name' => 'Not Assigned', // Default value
            'leader_email' => 'team@example.com', // Default value
        ];

        // Handle team leader
        if (!empty($validated['team_leader_id'])) {
            $leader = \App\Models\User::find($validated['team_leader_id']);
            if ($leader) {
                $teamData['leader_name'] = $leader->name;
                $teamData['leader_email'] = $leader->email;
                $teamData['leader_phone'] = $leader->phone ?? null;
            }
        }

        // Handle contact info (map to leader fields if no leader is set)
        if (empty($validated['team_leader_id'])) {
            if (!empty($validated['contact_email'])) {
                $teamData['leader_email'] = $validated['contact_email'];
            }
            if (!empty($validated['contact_phone'])) {
                $teamData['leader_phone'] = $validated['contact_phone'];
            }
        }

        // Ensure leader_name and leader_email are never null
        if (empty($teamData['leader_name'])) {
            $teamData['leader_name'] = 'Not Assigned';
        }
        if (empty($teamData['leader_email'])) {
            $teamData['leader_email'] = 'team@example.com';
        }

        // Handle working hours
        if (!empty($validated['working_hours_start']) || !empty($validated['working_hours_end'])) {
            $teamData['working_hours'] = json_encode([
                'start' => $validated['working_hours_start'] ?? '08:00',
                'end' => $validated['working_hours_end'] ?? '17:00'
            ]);
        }

        $team = MaintenanceTeam::create($teamData);

        // Add members if provided
        if ($request->has('members')) {
            $team->members()->attach($request->members);
        }

        return redirect()->route('maintenance.teams.index')
            ->with('success', 'Team created successfully.');
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
        $users = \App\Models\User::where('email_verified_at', '!=', null)->get();
        $team->load('members');
        
        return view('maintenance.teams.edit', compact('team', 'users'));
    }

    public function teamUpdate(Request $request, MaintenanceTeam $team)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'team_code' => 'nullable|string|max:255',
            'team_leader_id' => 'nullable|exists:users,id',
            'specialization' => 'nullable|in:general,electrical,plumbing,hvac,structural,painting,landscaping',
            'contact_email' => 'nullable|email',
            'contact_phone' => 'nullable|string',
            'working_hours_start' => 'nullable|date_format:H:i',
            'working_hours_end' => 'nullable|date_format:H:i',
            'max_concurrent_jobs' => 'nullable|integer|min:1|max:20',
            'notes' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'members_data' => 'nullable|string',
        ]);

        // Map form fields to database columns
        $teamData = [
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'specialization' => $validated['specialization'] ?? 'general',
            'max_concurrent_jobs' => $validated['max_concurrent_jobs'] ?? 3,
            'notes' => $validated['notes'] ?? null,
            'is_active' => isset($validated['is_active']) ? $validated['is_active'] : true,
        ];

        // Handle team leader
        if (!empty($validated['team_leader_id'])) {
            $leader = \App\Models\User::find($validated['team_leader_id']);
            if ($leader) {
                $teamData['leader_name'] = $leader->name;
                $teamData['leader_email'] = $leader->email;
                $teamData['leader_phone'] = $leader->phone ?? null;
            }
        }

        // Handle working hours
        if (!empty($validated['working_hours_start']) || !empty($validated['working_hours_end'])) {
            $teamData['working_hours'] = json_encode([
                'start' => $validated['working_hours_start'] ?? '08:00',
                'end' => $validated['working_hours_end'] ?? '17:00'
            ]);
        }

        // Handle contact info (map to leader fields if no leader is set)
        if (empty($validated['team_leader_id'])) {
            if (!empty($validated['contact_email'])) {
                $teamData['leader_email'] = $validated['contact_email'];
            }
            if (!empty($validated['contact_phone'])) {
                $teamData['leader_phone'] = $validated['contact_phone'];
            }
        }

        // Ensure leader_name and leader_email are never null
        if (empty($teamData['leader_name'])) {
            $teamData['leader_name'] = 'Not Assigned';
        }
        if (empty($teamData['leader_email'])) {
            $teamData['leader_email'] = 'team@example.com';
        }

        $team->update($teamData);

        // Update members if provided
        if (!empty($validated['members_data'])) {
            $membersData = json_decode($validated['members_data'], true);
            $memberIds = array_column($membersData, 'id');
            $team->members()->sync($memberIds);
            
            // Update member roles
            foreach ($membersData as $memberData) {
                $team->members()->updateExistingPivot($memberData['id'], [
                    'role' => $memberData['role']
                ]);
            }
        }

        return redirect()->route('maintenance.teams.show', $team)
            ->with('success', 'Team updated successfully.');
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
            'status' => $team->status === 'active' ? 'inactive' : 'active',
        ]);

        $status = $team->status === 'active' ? 'activated' : 'deactivated';
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

    // Maintenance Reports Methods
    public function reports()
    {
        return redirect()->route('maintenance.reports.index');
    }

    public function reportsIndex()
    {
        return view('maintenance.reports.index');
    }

    public function reportsWorkOrders()
    {
        $workOrders = WorkOrder::with(['property', 'assignedTeam', 'createdBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = [
            'total' => WorkOrder::count(),
            'pending' => WorkOrder::where('status', 'pending')->count(),
            'in_progress' => WorkOrder::where('status', 'in_progress')->count(),
            'completed' => WorkOrder::where('status', 'completed')->count(),
            'cancelled' => WorkOrder::where('status', 'cancelled')->count(),
        ];

        return view('maintenance.reports.workorders', compact('workOrders', 'stats'));
    }

    public function reportsTeams()
    {
        $teams = MaintenanceTeam::with(['members', 'workOrders'])
            ->orderBy('name')
            ->get();

        $stats = [
            'total_teams' => MaintenanceTeam::count(),
            'active_teams' => MaintenanceTeam::where('status', 'active')->count(),
            'total_members' => MaintenanceTeam::withCount('members')->get()->sum('members_count'),
            'total_workorders' => WorkOrder::whereNotNull('assigned_team_id')->count(),
        ];

        return view('maintenance.reports.teams', compact('teams', 'stats'));
    }

    public function reportsPerformance()
    {
        $monthlyStats = WorkOrder::selectRaw('
                DATE_FORMAT(created_at, "%Y-%m") as month,
                COUNT(*) as total,
                SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed,
                AVG(CASE WHEN status = "completed" AND completed_at IS NOT NULL 
                    THEN TIMESTAMPDIFF(HOUR, created_at, completed_at) 
                    ELSE NULL END) as avg_completion_hours
            ')
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $teamPerformance = MaintenanceTeam::with(['workOrders' => function($query) {
                $query->where('created_at', '>=', now()->subMonths(3));
            }])
            ->get()
            ->map(function($team) {
                $workOrders = $team->workOrders;
                return [
                    'name' => $team->name,
                    'total' => $workOrders->count(),
                    'completed' => $workOrders->where('status', 'completed')->count(),
                    'completion_rate' => $workOrders->count() > 0 
                        ? ($workOrders->where('status', 'completed')->count() / $workOrders->count()) * 100 
                        : 0,
                ];
            });

        return view('maintenance.reports.performance', compact('monthlyStats', 'teamPerformance'));
    }

    public function reportsCosts()
    {
        $monthlyCosts = WorkOrder::selectRaw('
                DATE_FORMAT(created_at, "%Y-%m") as month,
                COUNT(*) as total_orders,
                SUM(estimated_cost) as total_estimated,
                SUM(CASE WHEN actual_cost IS NOT NULL THEN actual_cost ELSE 0 END) as total_actual,
                AVG(CASE WHEN actual_cost IS NOT NULL THEN actual_cost ELSE estimated_cost END) as avg_cost
            ')
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $costsByType = WorkOrder::selectRaw('
                type,
                COUNT(*) as total,
                SUM(estimated_cost) as total_estimated,
                SUM(CASE WHEN actual_cost IS NOT NULL THEN actual_cost ELSE 0 END) as total_actual
            ')
            ->groupBy('type')
            ->orderBy('total_actual', 'desc')
            ->get();

        $costsByPriority = WorkOrder::selectRaw('
                priority,
                COUNT(*) as total,
                SUM(estimated_cost) as total_estimated,
                SUM(CASE WHEN actual_cost IS NOT NULL THEN actual_cost ELSE 0 END) as total_actual
            ')
            ->groupBy('priority')
            ->orderBy('total_actual', 'desc')
            ->get();

        return view('maintenance.reports.costs', compact('monthlyCosts', 'costsByType', 'costsByPriority'));
    }
}

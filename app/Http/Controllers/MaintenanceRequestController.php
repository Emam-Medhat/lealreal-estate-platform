<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceRequest;
use App\Models\MaintenanceTicket;
use App\Models\WorkOrder;
use App\Models\MaintenanceLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MaintenanceRequestController extends Controller
{
    public function index()
    {
        $requests = MaintenanceRequest::with(['property', 'serviceProvider', 'assignedTeam'])
            ->when(request('status'), function($query, $status) {
                $query->where('status', $status);
            })
            ->when(request('priority'), function($query, $priority) {
                $query->where('priority', $priority);
            })
            ->when(request('category'), function($query, $category) {
                $query->where('category', $category);
            })
            ->latest()->paginate(15);

        return view('maintenance.requests', compact('requests'));
    }

    public function create()
    {
        $properties = \App\Models\Property::all();
        $serviceProviders = \App\Models\ServiceProvider::where('is_active', true)->get();
        $maintenanceTeams = \App\Models\MaintenanceTeam::where('is_active', true)->get();

        return view('maintenance.requests-create', compact('properties', 'serviceProviders', 'maintenanceTeams'));
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
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
        ]);

        $validated['request_number'] = 'REQ-' . date('Y') . '-' . str_pad(MaintenanceRequest::count() + 1, 4, '0', STR_PAD_LEFT);
        $validated['status'] = 'pending';
        $validated['requested_by'] = auth()->id();

        DB::beginTransaction();
        try {
            $maintenanceRequest = MaintenanceRequest::create($validated);

            // Log the request creation
            MaintenanceLog::create([
                'maintenance_request_id' => $maintenanceRequest->id,
                'action' => 'created',
                'description' => 'تم إنشاء طلب الصيانة',
                'user_id' => auth()->id(),
            ]);

            // Handle attachments if any
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('maintenance_attachments', 'public');
                    // You might want to create an attachments table
                }
            }

            DB::commit();

            return redirect()->route('maintenance.requests.show', $maintenanceRequest)
                ->with('success', 'تم إنشاء طلب الصيانة بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'حدث خطأ أثناء إنشاء طلب الصيانة');
        }
    }

    public function show(MaintenanceRequest $maintenanceRequest)
    {
        $maintenanceRequest->load([
            'property', 
            'serviceProvider', 
            'assignedTeam', 
            'workOrders', 
            'tickets',
            'logs' => function($query) {
                $query->with('user')->latest();
            }
        ]);
        
        return view('maintenance.requests-show', compact('maintenanceRequest'));
    }

    public function edit(MaintenanceRequest $maintenanceRequest)
    {
        if ($maintenanceRequest->status === 'completed') {
            return back()->with('error', 'لا يمكن تعديل طلب الصيانة المكتمل');
        }

        $properties = \App\Models\Property::all();
        $serviceProviders = \App\Models\ServiceProvider::where('is_active', true)->get();
        $maintenanceTeams = \App\Models\MaintenanceTeam::where('is_active', true)->get();

        return view('maintenance.requests-edit', compact('maintenanceRequest', 'properties', 'serviceProviders', 'maintenanceTeams'));
    }

    public function update(Request $request, MaintenanceRequest $maintenanceRequest)
    {
        if ($maintenanceRequest->status === 'completed') {
            return back()->with('error', 'لا يمكن تعديل طلب الصيانة المكتمل');
        }

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

        $oldData = $maintenanceRequest->toArray();
        $maintenanceRequest->update($validated);

        // Log the update
        MaintenanceLog::create([
            'maintenance_request_id' => $maintenanceRequest->id,
            'action' => 'updated',
            'description' => 'تم تحديث طلب الصيانة',
            'old_values' => json_encode($oldData),
            'new_values' => json_encode($validated),
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('maintenance.requests.show', $maintenanceRequest)
            ->with('success', 'تم تحديث طلب الصيانة بنجاح');
    }

    public function destroy(MaintenanceRequest $maintenanceRequest)
    {
        if ($maintenanceRequest->status !== 'pending') {
            return back()->with('error', 'لا يمكن حذف طلب الصيانة الذي تم بدء العمل عليه');
        }

        DB::beginTransaction();
        try {
            // Log the deletion
            MaintenanceLog::create([
                'maintenance_request_id' => $maintenanceRequest->id,
                'action' => 'deleted',
                'description' => 'تم حذف طلب الصيانة',
                'old_values' => json_encode($maintenanceRequest->toArray()),
                'user_id' => auth()->id(),
            ]);

            $maintenanceRequest->delete();
            DB::commit();

            return redirect()->route('maintenance.requests.index')
                ->with('success', 'تم حذف طلب الصيانة بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'حدث خطأ أثناء حذف طلب الصيانة');
        }
    }

    public function assign(MaintenanceRequest $maintenanceRequest, Request $request)
    {
        if ($maintenanceRequest->status !== 'pending') {
            return back()->with('error', 'لا يمكن تكليف طلب الصيانة الذي تم بدء العمل عليه');
        }

        $validated = $request->validate([
            'service_provider_id' => 'required|exists:service_providers,id',
            'assigned_team_id' => 'nullable|exists:maintenance_teams,id',
            'notes' => 'nullable|string',
        ]);

        $oldData = $maintenanceRequest->toArray();
        
        $maintenanceRequest->update([
            'service_provider_id' => $validated['service_provider_id'],
            'assigned_team_id' => $validated['assigned_team_id'],
            'status' => 'assigned',
            'assigned_at' => now(),
            'assigned_by' => auth()->id(),
            'assignment_notes' => $validated['notes'],
        ]);

        // Log the assignment
        MaintenanceLog::create([
            'maintenance_request_id' => $maintenanceRequest->id,
            'action' => 'assigned',
            'description' => 'تم تكليف مقدم الخدمة',
            'old_values' => json_encode($oldData),
            'new_values' => json_encode($maintenanceRequest->toArray()),
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('maintenance.requests.show', $maintenanceRequest)
            ->with('success', 'تم تكليف مقدم الخدمة بنجاح');
    }

    public function startWork(MaintenanceRequest $maintenanceRequest)
    {
        if ($maintenanceRequest->status !== 'assigned') {
            return back()->with('error', 'يجب تكليف مقدم الخدمة أولاً');
        }

        $oldData = $maintenanceRequest->toArray();
        
        $maintenanceRequest->update([
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        // Log the start
        MaintenanceLog::create([
            'maintenance_request_id' => $maintenanceRequest->id,
            'action' => 'started',
            'description' => 'تم بدء العمل على طلب الصيانة',
            'old_values' => json_encode($oldData),
            'new_values' => json_encode($maintenanceRequest->toArray()),
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('maintenance.requests.show', $maintenanceRequest)
            ->with('success', 'تم بدء العمل على طلب الصيانة');
    }

    public function complete(MaintenanceRequest $maintenanceRequest, Request $request)
    {
        if ($maintenanceRequest->status !== 'in_progress') {
            return back()->with('error', 'يجب أن يكون طلب الصيانة قيد التنفيذ');
        }

        $validated = $request->validate([
            'actual_cost' => 'required|numeric|min:0',
            'completion_notes' => 'required|string',
            'next_maintenance_date' => 'nullable|date|after:today',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
        ]);

        $oldData = $maintenanceRequest->toArray();
        
        $maintenanceRequest->update([
            'status' => 'completed',
            'actual_cost' => $validated['actual_cost'],
            'completion_notes' => $validated['completion_notes'],
            'next_maintenance_date' => $validated['next_maintenance_date'],
            'completed_at' => now(),
            'completed_by' => auth()->id(),
        ]);

        // Log the completion
        MaintenanceLog::create([
            'maintenance_request_id' => $maintenanceRequest->id,
            'action' => 'completed',
            'description' => 'تم إكمال طلب الصيانة',
            'old_values' => json_encode($oldData),
            'new_values' => json_encode($maintenanceRequest->toArray()),
            'user_id' => auth()->id(),
        ]);

        // Handle completion attachments if any
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('maintenance_completion', 'public');
                // You might want to create an attachments table
            }
        }

        return redirect()->route('maintenance.requests.show', $maintenanceRequest)
            ->with('success', 'تم إكمال طلب الصيانة بنجاح');
    }

    public function cancel(MaintenanceRequest $maintenanceRequest, Request $request)
    {
        if ($maintenanceRequest->status === 'completed') {
            return back()->with('error', 'لا يمكن إلغاء طلب الصيانة المكتمل');
        }

        $validated = $request->validate([
            'cancellation_reason' => 'required|string|max:500',
        ]);

        $oldData = $maintenanceRequest->toArray();
        
        $maintenanceRequest->update([
            'status' => 'cancelled',
            'cancellation_reason' => $validated['cancellation_reason'],
            'cancelled_at' => now(),
            'cancelled_by' => auth()->id(),
        ]);

        // Log the cancellation
        MaintenanceLog::create([
            'maintenance_request_id' => $maintenanceRequest->id,
            'action' => 'cancelled',
            'description' => 'تم إلغاء طلب الصيانة: ' . $validated['cancellation_reason'],
            'old_values' => json_encode($oldData),
            'new_values' => json_encode($maintenanceRequest->toArray()),
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('maintenance.requests.show', $maintenanceRequest)
            ->with('success', 'تم إلغاء طلب الصيانة بنجاح');
    }

    public function createTicket(MaintenanceRequest $maintenanceRequest, Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high,emergency',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $validated['ticket_number'] = 'TCK-' . date('Y') . '-' . str_pad(MaintenanceTicket::count() + 1, 4, '0', STR_PAD_LEFT);
        $validated['maintenance_request_id'] = $maintenanceRequest->id;
        $validated['status'] = 'open';
        $validated['created_by'] = auth()->id();

        $ticket = MaintenanceTicket::create($validated);

        // Log the ticket creation
        MaintenanceLog::create([
            'maintenance_request_id' => $maintenanceRequest->id,
            'action' => 'ticket_created',
            'description' => 'تم إنشاء تذكرة: ' . $ticket->ticket_number,
            'new_values' => json_encode($validated),
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('maintenance.tickets.show', $ticket)
            ->with('success', 'تم إنشاء التذكرة بنجاح');
    }
}

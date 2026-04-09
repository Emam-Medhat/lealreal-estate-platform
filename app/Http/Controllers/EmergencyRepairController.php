<?php

namespace App\Http\Controllers;

use App\Models\EmergencyRepair;
use App\Models\MaintenanceRequest;
use App\Models\MaintenanceTeam;
use App\Models\ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EmergencyRepairController extends Controller
{
    public function index()
    {
        $repairs = EmergencyRepair::with(['property', 'maintenanceRequest', 'assignedTeam', 'assignedProvider'])
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

        return view('maintenance.emergency', compact('repairs'));
    }

    public function create()
    {
        $properties = \App\Models\Property::all();
        $maintenanceTeams = MaintenanceTeam::where('is_active', true)->get();
        $serviceProviders = ServiceProvider::where('is_active', true)->get();

        return view('maintenance.emergency-create', compact('properties', 'maintenanceTeams', 'serviceProviders'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'emergency_type' => 'required|in:water_damage,electrical_fire,gas_leak,structural_damage,security_breach,other',
            'severity' => 'required|in:low,medium,high,critical',
            'priority' => 'required|in:low,medium,high,emergency',
            'location_details' => 'required|string|max:500',
            'reported_by' => 'required|string|max:255',
            'contact_phone' => 'required|string|max:20',
            'assigned_team_id' => 'nullable|exists:maintenance_teams,id',
            'assigned_provider_id' => 'nullable|exists:service_providers,id',
            'estimated_cost' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
        ]);

        $validated['repair_number'] = 'ER-' . date('Y') . '-' . str_pad(EmergencyRepair::count() + 1, 4, '0', STR_PAD_LEFT);
        $validated['status'] = 'reported';
        $validated['reported_at'] = now();
        $validated['reported_by_user_id'] = auth()->id();

        DB::beginTransaction();
        try {
            $repair = EmergencyRepair::create($validated);

            // Create maintenance request
            $maintenanceRequest = MaintenanceRequest::create([
                'property_id' => $repair->property_id,
                'title' => '[EMERGENCY] ' . $repair->title,
                'description' => $repair->description,
                'priority' => 'emergency',
                'category' => 'general',
                'estimated_cost' => $repair->estimated_cost,
                'due_date' => now(),
                'service_provider_id' => $repair->assigned_provider_id,
                'assigned_team_id' => $repair->assigned_team_id,
                'request_number' => 'REQ-' . date('Y') . '-' . str_pad(MaintenanceRequest::count() + 1, 4, '0', STR_PAD_LEFT),
                'status' => 'assigned',
                'assigned_at' => now(),
                'assigned_by' => auth()->id(),
                'requested_by' => auth()->id(),
                'emergency_repair_id' => $repair->id,
            ]);

            $repair->update(['maintenance_request_id' => $maintenanceRequest->id]);

            // Handle attachments if any
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('emergency_attachments', 'public');
                    // You might want to create an attachments table
                }
            }

            DB::commit();

            return redirect()->route('maintenance.emergency.show', $repair)
                ->with('success', 'تم إنشاء طلب الإصلاح الطارئ بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'حدث خطأ أثناء إنشاء طلب الإصلاح الطارئ');
        }
    }

    public function show(EmergencyRepair $repair)
    {
        $repair->load([
            'property', 
            'maintenanceRequest', 
            'assignedTeam', 
            'assignedProvider',
            'timeLogs'
        ]);
        
        return view('maintenance.emergency-show', compact('repair'));
    }

    public function edit(EmergencyRepair $repair)
    {
        if ($repair->status === 'completed') {
            return back()->with('error', 'لا يمكن تعديل الإصلاح الطارئ المكتمل');
        }

        $properties = \App\Models\Property::all();
        $maintenanceTeams = MaintenanceTeam::where('is_active', true)->get();
        $serviceProviders = ServiceProvider::where('is_active', true)->get();

        return view('maintenance.emergency-edit', compact('repair', 'properties', 'maintenanceTeams', 'serviceProviders'));
    }

    public function update(Request $request, EmergencyRepair $repair)
    {
        if ($repair->status === 'completed') {
            return back()->with('error', 'لا يمكن تعديل الإصلاح الطارئ المكتمل');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'emergency_type' => 'required|in:water_damage,electrical_fire,gas_leak,structural_damage,security_breach,other',
            'severity' => 'required|in:low,medium,high,critical',
            'priority' => 'required|in:low,medium,high,emergency',
            'location_details' => 'required|string|max:500',
            'reported_by' => 'required|string|max:255',
            'contact_phone' => 'required|string|max:20',
            'assigned_team_id' => 'nullable|exists:maintenance_teams,id',
            'assigned_provider_id' => 'nullable|exists:service_providers,id',
            'estimated_cost' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $repair->update($validated);

        return redirect()->route('maintenance.emergency.show', $repair)
            ->with('success', 'تم تحديث طلب الإصلاح الطارئ بنجاح');
    }

    public function destroy(EmergencyRepair $repair)
    {
        if ($repair->status !== 'reported') {
            return back()->with('error', 'لا يمكن حذف الإصلاح الطارئ الذي تم بدء العمل عليه');
        }

        DB::beginTransaction();
        try {
            // Delete related maintenance request
            if ($repair->maintenanceRequest) {
                $repair->maintenanceRequest->delete();
            }

            $repair->delete();
            DB::commit();

            return redirect()->route('maintenance.emergency.index')
                ->with('success', 'تم حذف طلب الإصلاح الطارئ بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'حدث خطأ أثناء حذف طلب الإصلاح الطارئ');
        }
    }

    public function assign(EmergencyRepair $repair, Request $request)
    {
        if ($repair->status !== 'reported') {
            return back()->with('error', 'لا يمكن تكليف الإصلاح الطارئ الذي تم بدء العمل عليه');
        }

        $validated = $request->validate([
            'assigned_team_id' => 'required|exists:maintenance_teams,id',
            'assigned_provider_id' => 'nullable|exists:service_providers,id',
            'notes' => 'nullable|string',
        ]);

        $repair->update([
            'assigned_team_id' => $validated['assigned_team_id'],
            'assigned_provider_id' => $validated['assigned_provider_id'],
            'status' => 'assigned',
            'assigned_at' => now(),
            'assigned_by' => auth()->id(),
            'assignment_notes' => $validated['notes'],
        ]);

        // Update maintenance request
        if ($repair->maintenanceRequest) {
            $repair->maintenanceRequest->update([
                'assigned_team_id' => $validated['assigned_team_id'],
                'service_provider_id' => $validated['assigned_provider_id'],
                'status' => 'assigned',
                'assigned_at' => now(),
            ]);
        }

        return redirect()->route('maintenance.emergency.show', $repair)
            ->with('success', 'تم تكليف فريق الإصلاح الطارئ بنجاح');
    }

    public function start(EmergencyRepair $repair)
    {
        if ($repair->status !== 'assigned') {
            return back()->with('error', 'يجب تكليف فريق الإصلاح الطارئ أولاً');
        }

        $repair->update([
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        // Update maintenance request
        if ($repair->maintenanceRequest) {
            $repair->maintenanceRequest->update([
                'status' => 'in_progress',
                'started_at' => now(),
            ]);
        }

        return redirect()->route('maintenance.emergency.show', $repair)
            ->with('success', 'تم بدء العمل على الإصلاح الطارئ');
    }

    public function pause(EmergencyRepair $repair, Request $request)
    {
        if ($repair->status !== 'in_progress') {
            return back()->with('error', 'يجب أن يكون الإصلاح الطارئ قيد التنفيذ');
        }

        $validated = $request->validate([
            'pause_reason' => 'required|string|max:500',
        ]);

        $repair->update([
            'status' => 'paused',
            'paused_at' => now(),
            'pause_reason' => $validated['pause_reason'],
        ]);

        return redirect()->route('maintenance.emergency.show', $repair)
            ->with('success', 'تم إيقاف الإصلاح الطارئ مؤقتاً');
    }

    public function resume(EmergencyRepair $repair)
    {
        if ($repair->status !== 'paused') {
            return back()->with('error', 'يجب أن يكون الإصلاح الطارئ موقوفاً');
        }

        $repair->update([
            'status' => 'in_progress',
            'resumed_at' => now(),
        ]);

        return redirect()->route('maintenance.emergency.show', $repair)
            ->with('success', 'تم استئناف العمل على الإصلاح الطارئ');
    }

    public function complete(EmergencyRepair $repair, Request $request)
    {
        if ($repair->status !== 'in_progress') {
            return back()->with('error', 'يجب أن يكون الإصلاح الطارئ قيد التنفيذ');
        }

        $validated = $request->validate([
            'actual_cost' => 'required|numeric|min:0',
            'completion_notes' => 'required|string',
            'resolution_details' => 'required|string',
            'preventive_measures' => 'nullable|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
        ]);

        $repair->update([
            'status' => 'completed',
            'actual_cost' => $validated['actual_cost'],
            'completion_notes' => $validated['completion_notes'],
            'resolution_details' => $validated['resolution_details'],
            'preventive_measures' => $validated['preventive_measures'],
            'completed_at' => now(),
            'completed_by' => auth()->id(),
        ]);

        // Update maintenance request
        if ($repair->maintenanceRequest) {
            $repair->maintenanceRequest->update([
                'status' => 'completed',
                'actual_cost' => $validated['actual_cost'],
                'completion_notes' => $validated['completion_notes'],
                'completed_at' => now(),
                'completed_by' => auth()->id(),
            ]);
        }

        return redirect()->route('maintenance.emergency.show', $repair)
            ->with('success', 'تم إكمال الإصلاح الطارئ بنجاح');
    }

    public function addTimeLog(EmergencyRepair $repair, Request $request)
    {
        if ($repair->status !== 'in_progress') {
            return back()->with('error', 'يجب أن يكون الإصلاح الطارئ قيد التنفيذ');
        }

        $validated = $request->validate([
            'description' => 'required|string|max:500',
            'duration' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $repair->timeLogs()->create([
            'description' => $validated['description'],
            'duration' => $validated['duration'],
            'notes' => $validated['notes'],
            'user_id' => auth()->id(),
            'log_time' => now(),
        ]);

        return redirect()->route('maintenance.emergency.show', $repair)
            ->with('success', 'تم إضافة سجل الوقت بنجاح');
    }

    public function dashboard()
    {
        $stats = [
            'total_repairs' => EmergencyRepair::count(),
            'reported_repairs' => EmergencyRepair::where('status', 'reported')->count(),
            'assigned_repairs' => EmergencyRepair::where('status', 'assigned')->count(),
            'in_progress_repairs' => EmergencyRepair::where('status', 'in_progress')->count(),
            'completed_repairs' => EmergencyRepair::where('status', 'completed')->count(),
            'critical_repairs' => EmergencyRepair::where('severity', 'critical')->count(),
            'average_response_time' => EmergencyRepair::where('status', 'completed')
                ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, reported_at, started_at)) as avg_time')
                ->value('avg_time'),
            'average_completion_time' => EmergencyRepair::where('status', 'completed')
                ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, started_at, completed_at)) as avg_time')
                ->value('avg_time'),
        ];

        $recentRepairs = EmergencyRepair::with(['property'])
            ->latest()
            ->take(5)
            ->get();

        return view('maintenance.emergency-dashboard', compact('stats', 'recentRepairs'));
    }

    public function export(Request $request)
    {
        $repairs = EmergencyRepair::with(['property', 'assignedTeam', 'assignedProvider'])
            ->when($request->status, function($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->severity, function($query, $severity) {
                $query->where('severity', $severity);
            })
            ->when($request->date_from, function($query, $dateFrom) {
                $query->whereDate('reported_at', '>=', $dateFrom);
            })
            ->when($request->date_to, function($query, $dateTo) {
                $query->whereDate('reported_at', '<=', $dateTo);
            })
            ->get();

        $filename = 'emergency_repairs_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($repairs) {
            $file = fopen('php://output', 'w');
            
            // CSV Header
            fputcsv($file, [
                'رقم الإصلاح',
                'العنوان',
                'العقار',
                'نوع الطوارئ',
                'الشدة',
                'الأولوية',
                'الحالة',
                'الفريق',
                'مقدم الخدمة',
                'التكلفة التقديرية',
                'التكلفة الفعلية',
                'تاريخ الإبلاغ',
            ]);

            // CSV Data
            foreach ($repairs as $repair) {
                fputcsv($file, [
                    $repair->repair_number,
                    $repair->title,
                    $repair->property->title ?? '',
                    $this->getEmergencyTypeLabel($repair->emergency_type),
                    $this->getSeverityLabel($repair->severity),
                    $this->getPriorityLabel($repair->priority),
                    $this->getStatusLabel($repair->status),
                    $repair->assignedTeam->name ?? '',
                    $repair->assignedProvider->name ?? '',
                    $repair->estimated_cost,
                    $repair->actual_cost,
                    $repair->reported_at->format('Y-m-d H:i'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function getEmergencyTypeLabel($type)
    {
        $labels = [
            'water_damage' => 'ضرر مائي',
            'electrical_fire' => 'حريق كهربائي',
            'gas_leak' => 'تسرب غاز',
            'structural_damage' => 'ضرر إنشائي',
            'security_breach' => 'اختراق أمني',
            'other' => 'أخرى',
        ];

        return $labels[$type] ?? $type;
    }

    private function getSeverityLabel($severity)
    {
        $labels = [
            'low' => 'منخفض',
            'medium' => 'متوسط',
            'high' => 'عالي',
            'critical' => 'حرج',
        ];

        return $labels[$severity] ?? $severity;
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

    private function getStatusLabel($status)
    {
        $labels = [
            'reported' => 'تم الإبلاغ',
            'assigned' => 'مكلف',
            'in_progress' => 'قيد التنفيذ',
            'paused' => 'موقوف',
            'completed' => 'مكتمل',
        ];

        return $labels[$status] ?? $status;
    }
}

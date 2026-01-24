<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceSchedule;
use App\Models\MaintenanceRequest;
use App\Models\MaintenanceTeam;
use App\Models\ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MaintenanceScheduleController extends Controller
{
    public function index()
    {
        $schedules = MaintenanceSchedule::with(['property', 'maintenanceTeam', 'serviceProvider'])
            ->when(request('status'), function($query, $status) {
                $query->where('status', $status);
            })
            ->when(request('team_id'), function($query, $teamId) {
                $query->where('maintenance_team_id', $teamId);
            })
            ->when(request('date_from'), function($query, $dateFrom) {
                $query->whereDate('scheduled_date', '>=', $dateFrom);
            })
            ->when(request('date_to'), function($query, $dateTo) {
                $query->whereDate('scheduled_date', '<=', $dateTo);
            })
            ->orderBy('scheduled_date')
            ->paginate(15);

        return view('maintenance.schedule', compact('schedules'));
    }

    public function calendar()
    {
        $schedules = MaintenanceSchedule::with(['property', 'maintenanceTeam'])
            ->whereMonth('scheduled_date', now()->month)
            ->whereYear('scheduled_date', now()->year)
            ->get();

        $teams = MaintenanceTeam::where('is_active', true)->get();
        $properties = \App\Models\Property::all();

        return view('maintenance.schedule-calendar', compact('schedules', 'teams', 'properties'));
    }

    public function create()
    {
        $properties = \App\Models\Property::all();
        $maintenanceTeams = MaintenanceTeam::where('is_active', true)->get();
        $serviceProviders = ServiceProvider::where('is_active', true)->get();

        return view('maintenance.schedule-create', compact('properties', 'maintenanceTeams', 'serviceProviders'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'maintenance_type' => 'required|in:routine,preventive,corrective,emergency',
            'scheduled_date' => 'required|date|after:today',
            'estimated_duration' => 'required|integer|min:30|max:480',
            'priority' => 'required|in:low,medium,high,emergency',
            'maintenance_team_id' => 'required|exists:maintenance_teams,id',
            'service_provider_id' => 'nullable|exists:service_providers,id',
            'estimated_cost' => 'nullable|numeric|min:0',
            'recurrence_type' => 'nullable|in:daily,weekly,monthly,quarterly,yearly',
            'recurrence_end_date' => 'nullable|date|after:scheduled_date',
            'notes' => 'nullable|string',
        ]);

        $validated['schedule_number'] = 'SCH-' . date('Y') . '-' . str_pad(MaintenanceSchedule::count() + 1, 4, '0', STR_PAD_LEFT);
        $validated['status'] = 'scheduled';
        $validated['created_by'] = auth()->id();

        DB::beginTransaction();
        try {
            $schedule = MaintenanceSchedule::create($validated);

            // Handle recurring schedules
            if ($validated['recurrence_type'] && $validated['recurrence_end_date']) {
                $this->createRecurringSchedules($schedule);
            }

            DB::commit();

            return redirect()->route('maintenance.schedule.show', $schedule)
                ->with('success', 'تم إنشاء جدول الصيانة بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'حدث خطأ أثناء إنشاء جدول الصيانة');
        }
    }

    public function show(MaintenanceSchedule $schedule)
    {
        $schedule->load(['property', 'maintenanceTeam', 'serviceProvider', 'maintenanceRequest']);
        
        return view('maintenance.schedule-show', compact('schedule'));
    }

    public function edit(MaintenanceSchedule $schedule)
    {
        if ($schedule->status === 'completed') {
            return back()->with('error', 'لا يمكن تعديل جدول الصيانة المكتمل');
        }

        $properties = \App\Models\Property::all();
        $maintenanceTeams = MaintenanceTeam::where('is_active', true)->get();
        $serviceProviders = ServiceProvider::where('is_active', true)->get();

        return view('maintenance.schedule-edit', compact('schedule', 'properties', 'maintenanceTeams', 'serviceProviders'));
    }

    public function update(Request $request, MaintenanceSchedule $schedule)
    {
        if ($schedule->status === 'completed') {
            return back()->with('error', 'لا يمكن تعديل جدول الصيانة المكتمل');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'maintenance_type' => 'required|in:routine,preventive,corrective,emergency',
            'scheduled_date' => 'required|date|after:today',
            'estimated_duration' => 'required|integer|min:30|max:480',
            'priority' => 'required|in:low,medium,high,emergency',
            'maintenance_team_id' => 'required|exists:maintenance_teams,id',
            'service_provider_id' => 'nullable|exists:service_providers,id',
            'estimated_cost' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $schedule->update($validated);

        return redirect()->route('maintenance.schedule.show', $schedule)
            ->with('success', 'تم تحديث جدول الصيانة بنجاح');
    }

    public function destroy(MaintenanceSchedule $schedule)
    {
        if ($schedule->status === 'completed') {
            return back()->with('error', 'لا يمكن حذف جدول الصيانة المكتمل');
        }

        $schedule->delete();

        return redirect()->route('maintenance.schedule.index')
            ->with('success', 'تم حذف جدول الصيانة بنجاح');
    }

    public function start(MaintenanceSchedule $schedule)
    {
        if ($schedule->status !== 'scheduled') {
            return back()->with('error', 'يجب أن يكون جدول الصيانة مجدولاً');
        }

        // Create maintenance request from schedule
        $maintenanceRequest = MaintenanceRequest::create([
            'property_id' => $schedule->property_id,
            'title' => $schedule->title,
            'description' => $schedule->description,
            'priority' => $schedule->priority,
            'category' => $this->getCategoryFromType($schedule->maintenance_type),
            'estimated_cost' => $schedule->estimated_cost,
            'due_date' => $schedule->scheduled_date,
            'service_provider_id' => $schedule->service_provider_id,
            'assigned_team_id' => $schedule->maintenance_team_id,
            'request_number' => 'REQ-' . date('Y') . '-' . str_pad(MaintenanceRequest::count() + 1, 4, '0', STR_PAD_LEFT),
            'status' => 'assigned',
            'assigned_at' => now(),
            'assigned_by' => auth()->id(),
            'requested_by' => auth()->id(),
        ]);

        $schedule->update([
            'status' => 'in_progress',
            'started_at' => now(),
            'maintenance_request_id' => $maintenanceRequest->id,
        ]);

        return redirect()->route('maintenance.requests.show', $maintenanceRequest)
            ->with('success', 'تم بدء جدول الصيانة وإنشاء طلب صيانة');
    }

    public function complete(MaintenanceSchedule $schedule, Request $request)
    {
        if ($schedule->status !== 'in_progress') {
            return back()->with('error', 'يجب أن يكون جدول الصيانة قيد التنفيذ');
        }

        $validated = $request->validate([
            'actual_cost' => 'required|numeric|min:0',
            'completion_notes' => 'required|stringeder|string',
            'next_scheduled_date' => 'nullable|date|after:today',
        ]);

        $schedule->update([
            'status' => 'completed',
            'actual_cost' => $validated['actual_cost'],
            'completion_notes' => $validated['completion_notes'],
            'completed_at' => now(),
            'completed_by' => auth()->id(),
        ]);

        // Create next schedule if recurrence is set
        if ($schedule->recurrence_type && $validated['next_scheduled_date']) {
            $this->createNextSchedule($schedule, $validated['next_scheduled_date']);
        }

        return redirect()->route('maintenance.schedule.show', $schedule)
            ->with('success', 'تم إكمال جدول الصيانة بنجاح');
    }

    public function reschedule(MaintenanceSchedule $schedule, Request $request)
    {
        if ($schedule->status === 'completed') {
            return back()->with('error', 'لا يمكن إعادة جدولة الصيانة المكتملة');
        }

        $validated = $request->validate([
            'new_date' => 'required|date|after:today',
            'reason' => 'required|string|max:500',
        ]);

        $oldDate = $schedule->scheduled_date;
        
        $schedule->update([
            'scheduled_date' => $validated['new_date'],
            'reschedule_reason' => $validated['reason'],
            'rescheduled_at' => now(),
            'rescheduled_by' => auth()->id(),
        ]);

        return redirect()->route('maintenance.schedule.show', $schedule)
            ->with('success', 'تم إعادة جدولة الصيانة بنجاح');
    }

    public function cancel(MaintenanceSchedule $schedule, Request $request)
    {
        if ($schedule->status === 'completed') {
            return back()->with('error', 'لا يمكن إلغاء جدول الصيانة المكتمل');
        }

        $validated = $request->validate([
            'cancellation_reason' => 'required|string|max:500',
        ]);

        $schedule->update([
            'status' => 'cancelled',
            'cancellation_reason' => $validated['cancellation_reason'],
            'cancelled_at' => now(),
            'cancelled_by' => auth()->id(),
        ]);

        return redirect()->route('maintenance.schedule.show', $schedule)
            ->with('success', 'تم إلغاء جدول الصيانة بنجاح');
    }

    public function getTeamSchedule(MaintenanceTeam $team, Request $request)
    {
        $date = $request->date ?? now()->format('Y-m-d');
        
        $schedules = MaintenanceSchedule::where('maintenance_team_id', $team->id)
            ->whereDate('scheduled_date', $date)
            ->with(['property'])
            ->orderBy('scheduled_date')
            ->get();

        return response()->json($schedules);
    }

    public function export(Request $request)
    {
        $schedules = MaintenanceSchedule::with(['property', 'maintenanceTeam', 'serviceProvider'])
            ->when($request->status, function($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->date_from, function($query, $dateFrom) {
                $query->whereDate('scheduled_date', '>=', $dateFrom);
            })
            ->when($request->date_to, function($query, $dateTo) {
                $query->whereDate('scheduled_date', '<=', $dateTo);
            })
            ->get();

        $filename = 'maintenance_schedules_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($schedules) {
            $file = fopen('php://output', 'w');
            
            // CSV Header
            fputcsv($file, [
                'رقم الجدول',
                'العقار',
                'العنوان',
                'نوع الصيانة',
                'التاريخ المجدول',
                'المدة التقديرية',
                'الأولوية',
                'الفريق',
                'مقدم الخدمة',
                'الحالة',
                'التكلفة التقديرية',
            ]);

            // CSV Data
            foreach ($schedules as $schedule) {
                fputcsv($file, [
                    $schedule->schedule_number,
                    $schedule->property->title ?? '',
                    $schedule->title,
                    $this->getTypeLabel($schedule->maintenance_type),
                    $schedule->scheduled_date->format('Y-m-d H:i'),
                    $schedule->estimated_duration . ' دقيقة',
                    $this->getPriorityLabel($schedule->priority),
                    $schedule->maintenanceTeam->name ?? '',
                    $schedule->serviceProvider->name ?? '',
                    $this->getStatusLabel($schedule->status),
                    $schedule->estimated_cost,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function createRecurringSchedules($schedule)
    {
        $startDate = Carbon::parse($schedule->scheduled_date);
        $endDate = Carbon::parse($schedule->recurrence_end_date);
        $interval = $this->getRecurrenceInterval($schedule->recurrence_type);

        $currentDate = $startDate->copy()->add($interval);

        while ($currentDate <= $endDate) {
            MaintenanceSchedule::create([
                'property_id' => $schedule->property_id,
                'title' => $schedule->title,
                'description' => $schedule->description,
                'maintenance_type' => $schedule->maintenance_type,
                'scheduled_date' => $currentDate,
                'estimated_duration' => $schedule->estimated_duration,
                'priority' => $schedule->priority,
                'maintenance_team_id' => $schedule->maintenance_team_id,
                'service_provider_id' => $schedule->service_provider_id,
                'estimated_cost' => $schedule->estimated_cost,
                'recurrence_type' => null, // Don't create recurrence from recurrence
                'notes' => $schedule->notes,
                'parent_schedule_id' => $schedule->id,
                'schedule_number' => 'SCH-' . date('Y') . '-' . str_pad(MaintenanceSchedule::count() + 1, 4, '0', STR_PAD_LEFT),
                'status' => 'scheduled',
                'created_by' => $schedule->created_by,
            ]);

            $currentDate->add($interval);
        }
    }

    private function createNextSchedule($schedule, $nextDate)
    {
        MaintenanceSchedule::create([
            'property_id' => $schedule->property_id,
            'title' => $schedule->title,
            'description' => $schedule->description,
            'maintenance_type' => $schedule->maintenance_type,
            'scheduled_date' => $nextDate,
            'estimated_duration' => $schedule->estimated_duration,
            'priority' => $schedule->priority,
            'maintenance_team_id' => $schedule->maintenance_team_id,
            'service_provider_id' => $schedule->service_provider_id,
            'estimated_cost' => $schedule->estimated_cost,
            'recurrence_type' => $schedule->recurrence_type,
            'recurrence_end_date' => $schedule->recurrence_end_date,
            'notes' => $schedule->notes,
            'parent_schedule_id' => $schedule->id,
            'schedule_number' => 'SCH-' . date('Y') . '-' . str_pad(MaintenanceSchedule::count() + 1, 4, '0', STR_PAD_LEFT),
            'status' => 'scheduled',
            'created_by' => $schedule->created_by,
        ]);
    }

    private function getRecurrenceInterval($type)
    {
        $intervals = [
            'daily' => '1 day',
            'weekly' => '1 week',
            'monthly' => '1 month',
            'quarterly' => '3 months',
            'yearly' => '1 year',
        ];

        return $intervals[$type] ?? '1 month';
    }

    private function getCategoryFromType($type)
    {
        $categories = [
            'routine' => 'general',
            'preventive' => 'general',
            'corrective' => 'general',
            'emergency' => 'general',
        ];

        return $categories[$type] ?? 'general';
    }

    private function getTypeLabel($type)
 {
        $labels = [
            'routine' => 'روتيني',
            'preventive' => 'وقائي',
            'corrective' => 'تصحيحي',
            'emergency' => 'طوارئ',
        ];

        return $labels[$type] ?? $type;
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
            'scheduled' => 'مجدول',
            'in_progress' => 'قيد التنفيذ',
            'completed' => 'مكتمل',
            'cancelled' => 'ملغي',
        ];

        return $labels[$status] ?? $status;
    }
}

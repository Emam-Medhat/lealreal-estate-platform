<?php

namespace App\Http\Controllers;

use App\Models\PreventiveMaintenance;
use App\Models\MaintenanceSchedule;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PreventiveMaintenanceController extends Controller
{
    public function index()
    {
        $plans = PreventiveMaintenance::with(['property', 'maintenanceTeam'])
            ->when(request('status'), function($query, $status) {
                $query->where('status', $status);
            })
            ->when(request('frequency'), function($query, $frequency) {
                $query->where('frequency', $frequency);
            })
            ->when(request('property_id'), function($query, $propertyId) {
                $query->where('property_id', $propertyId);
            })
            ->latest()->paginate(15);

        return view('maintenance.preventive', compact('plans'));
    }

    public function create()
    {
        $properties = Property::all();
        $maintenanceTeams = \App\Models\MaintenanceTeam::where('is_active', true)->get();

        return view('maintenance.preventive-create', compact('properties', 'maintenanceTeams'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'frequency' => 'required|in:daily,weekly,monthly,quarterly,yearly',
            'maintenance_type' => 'required|in:inspection,cleaning,service,replacement,testing',
            'priority' => 'required|in:low,medium,high',
            'estimated_duration' => 'required|integer|min:30|max:480',
            'estimated_cost' => 'required|numeric|min:0',
            'maintenance_team_id' => 'required|exists:maintenance_teams,id',
            'start_date' => 'required|date|after:today',
            'end_date' => 'nullable|date|after:start_date',
            'checklist_items' => 'nullable|array',
            'checklist_items.*' => 'string|max:255',
            'materials_needed' => 'nullable|array',
            'materials_needed.*.name' => 'required|string|max:255',
            'materials_needed.*.quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $validated['plan_number'] = 'PM-' . date('Y') . '-' . str_pad(PreventiveMaintenance::count() + 1, 4, '0', STR_PAD_LEFT);
        $validated['status'] = 'active';
        $validated['created_by'] = auth()->id();
        $validated['checklist_items'] = json_encode($validated['checklist_items'] ?? []);
        $validated['materials_needed'] = json_encode($validated['materials_needed'] ?? []);

        DB::beginTransaction();
        try {
            $plan = PreventiveMaintenance::create($validated);

            // Generate initial schedules based on frequency
            $this->generateSchedules($plan);

            DB::commit();

            return redirect()->route('maintenance.preventive.show', $plan)
                ->with('success', 'تم إنشاء خطة الصيانة الوقائية بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'حدث خطأ أثناء إنشاء خطة الصيانة الوقائية');
        }
    }

    public function show(PreventiveMaintenance $plan)
    {
        $plan->load(['property', 'maintenanceTeam', 'schedules' => function($query) {
            $query->latest()->take(10);
        }]);
        
        $stats = [
            'total_schedules' => $plan->schedules()->count(),
            'completed_schedules' => $plan->schedules()->where('status', 'completed')->count(),
            'pending_schedules' => $plan->schedules()->where('status', 'scheduled')->count(),
            'overdue_schedules' => $plan->schedules()->where('scheduled_date', '<', now())->where('status', '!=', 'completed')->count(),
            'next_scheduled_date' => $plan->schedules()->where('scheduled_date', '>=', now())->min('scheduled_date'),
            'average_completion_time' => $plan->schedules()->where('status', 'completed')->selectRaw('AVG(TIMESTAMPDIFF(HOUR, started_at, completed_at)) as avg_time')->value('avg_time'),
        ];

        return view('maintenance.preventive-show', compact('plan', 'stats'));
    }

    public function edit(PreventiveMaintenance $plan)
    {
        if ($plan->status === 'completed') {
            return back()->with('error', 'لا يمكن تعديل خطة الصيانة الوقائية المكتملة');
        }

        $properties = Property::all();
        $maintenanceTeams = \App\Models\MaintenanceTeam::where('is_active', true)->get();
        $plan->checklist_items = json_decode($plan->checklist_items ?? '[]', true);
        $plan->materials_needed = json_decode($plan->materials_needed ?? '[]', true);

        return view('maintenance.preventive-edit', compact('plan', 'properties', 'maintenanceTeams'));
    }

    public function update(Request $request, PreventiveMaintenance $plan)
    {
        if ($plan->status === 'completed') {
            return back()->with('error', 'لا يمكن تعديل خطة الصيانة الوقائية المكتملة');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'frequency' => 'required|in:daily,weekly,monthly,quarterly,yearly',
            'maintenance_type' => 'required|in:inspection,cleaning,service,replacement,testing',
            'priority' => 'required|in:low,medium,high',
            'estimated_duration' => 'required|integer|min:30|max:480',
            'estimated_cost' => 'required|numeric|min:0',
            'maintenance_team_id' => 'required|exists:maintenance_teams,id',
            'end_date' => 'nullable|date|after:start_date',
            'checklist_items' => 'nullable|array',
            'checklist_items.*' => 'string|max:255',
            'materials_needed' => 'nullable|array',
            'materials_needed.*.name' => 'required|string|max:255',
            'materials_needed.*.quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $validated['checklist_items'] = json_encode($validated['checklist_items'] ?? []);
        $validated['materials_needed'] = json_encode($validated['materials_needed'] ?? []);

        $plan->update($validated);

        return redirect()->route('maintenance.preventive.show', $plan)
            ->with('success', 'تم تحديث خطة الصيانة الوقائية بنجاح');
    }

    public function destroy(PreventiveMaintenance $plan)
    {
        if ($plan->schedules()->where('status', 'in_progress')->exists()) {
            return back()->with('error', 'لا يمكن حذف خطة الصيانة الوقائية التي لديها جداول نشطة');
        }

        DB::beginTransaction();
        try {
            // Delete all related schedules
            $plan->schedules()->delete();
            
            $plan->delete();
            DB::commit();

            return redirect()->route('maintenance.preventive.index')
                ->with('success', 'تم حذف خطة الصيانة الوقائية بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'حدث خطأ أثناء حذف خطة الصيانة الوقائية');
        }
    }

    public function activate(PreventiveMaintenance $plan)
    {
        if ($plan->status === 'active') {
            return back()->with('error', 'الخطة نشطة بالفعل');
        }

        $plan->update([
            'status' => 'active',
            'activated_at' => now(),
            'activated_by' => auth()->id(),
        ]);

        // Generate schedules if needed
        $this->generateSchedules($plan);

        return redirect()->route('maintenance.preventive.show', $plan)
            ->with('success', 'تم تفعيل خطة الصيانة الوقائية بنجاح');
    }

    public function deactivate(PreventiveMaintenance $plan, Request $request)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $plan->update([
            'status' => 'inactive',
            'deactivated_at' => now(),
            'deactivated_by' => auth()->id(),
            'deactivation_reason' => $validated['reason'],
        ]);

        return redirect()->route('maintenance.preventive.show', $plan)
            ->with('success', 'تم إيقاف خطة الصيانة الوقائية بنجاح');
    }

    public function complete(PreventiveMaintenance $plan, Request $request)
    {
        $validated = $request->validate([
            'completion_notes' => 'required|string',
            'total_cost' => 'required|numeric|min:0',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
        ]);

        $plan->update([
            'status' => 'completed',
            'completion_notes' => $validated['completion_notes'],
            'total_cost' => $validated['total_cost'],
            'completed_at' => now(),
            'completed_by' => auth()->id(),
        ]);

        return redirect()->route('maintenance.preventive.show', $plan)
            ->with('success', 'تم إكمال خطة الصيانة الوقائية بنجاح');
    }

    public function generateNextSchedule(PreventiveMaintenance $plan)
    {
        if ($plan->status !== 'active') {
            return back()->with('error', 'يجب تفعيل الخطة أولاً');
        }

        $lastSchedule = $plan->schedules()->latest('scheduled_date')->first();
        $nextDate = $this->calculateNextDate($plan->frequency, $lastSchedule ? $lastSchedule->scheduled_date : $plan->start_date);

        if ($plan->end_date && $nextDate > $plan->end_date) {
            return back()->with('error', 'تم الوصول إلى تاريخ انتهاء الخطة');
        }

        $schedule = MaintenanceSchedule::create([
            'property_id' => $plan->property_id,
            'title' => $plan->title,
            'description' => $plan->description,
            'maintenance_type' => $plan->maintenance_type,
            'scheduled_date' => $nextDate,
            'estimated_duration' => $plan->estimated_duration,
            'priority' => $plan->priority,
            'maintenance_team_id' => $plan->maintenance_team_id,
            'estimated_cost' => $plan->estimated_cost,
            'preventive_maintenance_id' => $plan->id,
            'schedule_number' => 'SCH-' . date('Y') . '-' . str_pad(MaintenanceSchedule::count() + 1, 4, '0', STR_PAD_LEFT),
            'status' => 'scheduled',
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('maintenance.schedule.show', $schedule)
            ->with('success', 'تم إنشاء الجدول التالي بنجاح');
    }

    public function calendar()
    {
        $plans = PreventiveMaintenance::where('status', 'active')->get();
        $schedules = MaintenanceSchedule::whereHas('preventiveMaintenance')
            ->with(['property', 'preventiveMaintenance'])
            ->whereMonth('scheduled_date', now()->month)
            ->whereYear('scheduled_date', now()->year)
            ->get();

        return view('maintenance.preventive-calendar', compact('plans', 'schedules'));
    }

    public function reports()
    {
        $plans = PreventiveMaintenance::with(['property', 'maintenanceTeam'])
            ->where('status', 'completed')
            ->get();

        $stats = [
            'total_plans' => PreventiveMaintenance::count(),
            'active_plans' => PreventiveMaintenance::where('status', 'active')->count(),
            'completed_plans' => PreventiveMaintenance::where('status', 'completed')->count(),
            'total_schedules' => MaintenanceSchedule::whereHas('preventiveMaintenance')->count(),
            'completed_schedules' => MaintenanceSchedule::whereHas('preventiveMaintenance')->where('status', 'completed')->count(),
            'overdue_schedules' => MaintenanceSchedule::whereHas('preventiveMaintenance')->where('scheduled_date', '<', now())->where('status', '!=', 'completed')->count(),
        ];

        return view('maintenance.preventive-reports', compact('plans', 'stats'));
    }

    public function export(Request $request)
    {
        $plans = PreventiveMaintenance::with(['property', 'maintenanceTeam'])
            ->when($request->status, function($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->frequency, function($query, $frequency) {
                $query->where('frequency', $frequency);
            })
            ->get();

        $filename = 'preventive_maintenance_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($plans) {
            $file = fopen('php://output', 'w');
            
            // CSV Header
            fputcsv($file, [
                'رقم الخطة',
                'العنوان',
                'العقار',
                'نوع الصيانة',
                'التكرار',
                'الأولوية',
                'الفريق',
                'الحالة',
                'تاريخ البدء',
                'تاريخ الانتهاء',
            ]);

            // CSV Data
            foreach ($plans as $plan) {
                fputcsv($file, [
                    $plan->plan_number,
                    $plan->title,
                    $plan->property->title ?? '',
                    $this->getMaintenanceTypeLabel($plan->maintenance_type),
                    $this->getFrequencyLabel($plan->frequency),
                    $this->getPriorityLabel($plan->priority),
                    $plan->maintenanceTeam->name ?? '',
                    $this->getStatusLabel($plan->status),
                    $plan->start_date->format('Y-m-d'),
                    $plan->end_date ? $plan->end_date->format('Y-m-d') : '',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function generateSchedules(PreventiveMaintenance $plan)
    {
        $startDate = Carbon::parse($plan->start_date);
        $endDate = $plan->end_date ? Carbon::parse($plan->end_date) : Carbon::parse($plan->start_date)->addYear();
        $interval = $this->getFrequencyInterval($plan->frequency);

        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            MaintenanceSchedule::create([
                'property_id' => $plan->property_id,
                'title' => $plan->title,
                'description' => $plan->description,
                'maintenance_type' => $plan->maintenance_type,
                'scheduled_date' => $currentDate,
                'estimated_duration' => $plan->estimated_duration,
                'priority' => $plan->priority,
                'maintenance_team_id' => $plan->maintenance_team_id,
                'estimated_cost' => $plan->estimated_cost,
                'preventive_maintenance_id' => $plan->id,
                'schedule_number' => 'SCH-' . date('Y') . '-' . str_pad(MaintenanceSchedule::count() + 1, 4, '0', STR_PAD_LEFT),
                'status' => 'scheduled',
                'created_by' => $plan->created_by,
            ]);

            $currentDate->add($interval);
        }
    }

    private function calculateNextDate($frequency, $lastDate)
    {
        $interval = $this->getFrequencyInterval($frequency);
        return Carbon::parse($lastDate)->add($interval);
    }

    private function getFrequencyInterval($frequency)
    {
        $intervals = [
            'daily' => '1 day',
            'weekly' => '1 week',
            'monthly' => '1 month',
            'quarterly' => '3 months',
            'yearly' => '1 year',
        ];

        return $intervals[$frequency] ?? '1 month';
    }

    private function getMaintenanceTypeLabel($type)
    {
        $labels = [
            'inspection' => 'فحص',
            'cleaning' => 'تنظيف',
            'service' => 'خدمة',
            'replacement' => 'استبدال',
            'testing' => 'اختبار',
        ];

        return $labels[$type] ?? $type;
    }

    private function getFrequencyLabel($frequency)
    {
        $labels = [
            'daily' => 'يومي',
            'weekly' => 'أسبوعي',
            'monthly' => 'شهري',
            'quarterly' => 'ربع سنوي',
            'yearly' => 'سنوي',
        ];

        return $labels[$frequency] ?? $frequency;
    }

    private function getPriorityLabel($priority)
    {
        $labels = [
            'low' => 'منخفض',
            'medium' => 'متوسط',
            'high' => 'عالي',
        ];

        return $labels[$priority] ?? $priority;
    }

    private function getStatusLabel($status)
    {
        $labels = [
            'active' => 'نشط',
            'inactive' => 'غير نشط',
            'completed' => 'مكتمل',
        ];

        return $labels[$status] ?? $status;
    }
}

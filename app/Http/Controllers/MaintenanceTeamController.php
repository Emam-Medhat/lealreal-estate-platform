<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceTeam;
use App\Models\MaintenanceSchedule;
use App\Models\MaintenanceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MaintenanceTeamController extends Controller
{
    public function index()
    {
        $teams = MaintenanceTeam::with(['teamLeader', 'members', 'schedules'])
            ->when(request('is_active'), function($query, $isActive) {
                $query->where('is_active', $isActive);
            })
            ->when(request('specialization'), function($query, $specialization) {
                $query->where('specialization', $specialization);
            })
            ->latest()->paginate(15);

        return view('maintenance.teams', compact('teams'));
    }

    public function create()
    {
        $users = \App\Models\User::all();
        
        return view('maintenance.teams-create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'team_leader_id' => 'required|exists:users,id',
            'members' => 'nullable|array',
            'members.*' => 'exists:users,id',
            'specialization' => 'required|in:plumbing,electrical,hvac,structural,general,multi',
            'contact_phone' => 'required|string|max:20',
            'contact_email' => 'required|email',
            'max_concurrent_jobs' => 'required|integer|min:1|max:10',
            'working_hours_start' => 'required|date_format:H:i',
            'working_hours_end' => 'required|date_format:H:i|after:working_hours_start',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $validated['team_code'] = 'TEAM-' . date('Y') . '-' . str_pad(MaintenanceTeam::count() + 1, 4, '0', STR_PAD_LEFT);
        $validated['is_active'] = $validated['is_active'] ?? true;

        DB::beginTransaction();
        try {
            $team = MaintenanceTeam::create($validated);

            // Attach team members
            if (isset($validated['members'])) {
                $team->members()->attach($validated['members']);
            }

            DB::commit();

            return redirect()->route('maintenance.teams.show', $team)
                ->with('success', 'تم إنشاء فريق الصيانة بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'حدث خطأ أثناء إنشاء فريق الصيانة');
        }
    }

    public function show(MaintenanceTeam $team)
    {
        $team->load(['teamLeader', 'members', 'schedules' => function($query) {
            $query->where('scheduled_date', '>=', now())->orderBy('scheduled_date')->take(10);
        }, 'maintenanceRequests' => function($query) {
            $query->latest()->take(10);
        }]);
        
        $stats = [
            'total_members' => $team->members()->count(),
            'active_schedules' => $team->schedules()->where('status', 'in_progress')->count(),
            'upcoming_schedules' => $team->schedules()->where('scheduled_date', '>=', now())->count(),
            'completed_requests' => $team->maintenanceRequests()->where('status', 'completed')->count(),
            'pending_requests' => $team->maintenanceRequests()->where('status', 'pending')->count(),
            'in_progress_requests' => $team->maintenanceRequests()->where('status', 'in_progress')->count(),
        ];

        return view('maintenance.teams-show', compact('team', 'stats'));
    }

    public function edit(MaintenanceTeam $team)
    {
        $users = \App\Models\User::all();
        $team->load('members');
        
        return view('maintenance.teams-edit', compact('team', 'users'));
    }

    public function update(Request $request, MaintenanceTeam $team)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'team_leader_id' => 'required|exists:users,id',
            'members' => 'nullable|array',
            'members.*' => 'exists:users,id',
            'specialization' => 'required|in:plumbing,electrical,hvac,structural,general,multi',
            'contact_phone' => 'required|string|max:20',
            'contact_email' => 'required|email',
            'max_concurrent_jobs' => 'required|integer|min:1|max:10',
            'working_hours_start' => 'required|date_format:H:i',
            'working_hours_end' => 'required|date_format:H:i|after:working_hours_start',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $validated['is_active'] = $validated['is_active'] ?? $team->is_active;

        DB::beginTransaction();
        try {
            $team->update($validated);

            // Sync team members
            if (isset($validated['members'])) {
                $team->members()->sync($validated['members']);
            }

            DB::commit();

            return redirect()->route('maintenance.teams.show', $team)
                ->with('success', 'تم تحديث فريق الصيانة بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'حدث خطأ أثناء تحديث فريق الصيانة');
        }
    }

    public function destroy(MaintenanceTeam $team)
    {
        if ($team->schedules()->where('status', 'in_progress')->exists()) {
            return back()->with('error', 'لا يمكن حذف الفريق الذي لديه جداول صيانة نشطة');
        }

        if ($team->maintenanceRequests()->where('status', '!=', 'completed')->exists()) {
            return back()->with('error', 'لا يمكن حذف الفريق الذي لديه طلبات صيانة نشطة');
        }

        DB::beginTransaction();
        try {
            // Detach all members
            $team->members()->detach();
            
            $team->delete();
            DB::commit();

            return redirect()->route('maintenance.teams.index')
                ->with('success', 'تم حذف فريق الصيانة بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'حدث خطأ أثناء حذف فريق الصيانة');
        }
    }

    public function toggleStatus(MaintenanceTeam $team)
    {
        $team->update(['is_active' => !$team->is_active]);

        $status = $team->is_active ? 'تفعيل' : 'تعطيل';
        
        return redirect()->route('maintenance.teams.show', $team)
            ->with('success', 'تم ' . $status . ' فريق الصيانة بنجاح');
    }

    public function addMember(MaintenanceTeam $team, Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id|unique:maintenance_team_members,user_id,NULL,NULL,maintenance_team_id,' . $team->id,
            'role' => 'nullable|in:leader,member,specialist',
        ]);

        $team->members()->attach($validated['user_id'], ['role' => $validated['role'] ?? 'member']);

        return redirect()->route('maintenance.teams.show', $team)
            ->with('success', 'تم إضافة العضو إلى الفريق بنجاح');
    }

    public function removeMember(MaintenanceTeam $team, $userId)
    {
        $member = $team->members()->where('user_id', $userId)->first();
        
        if (!$member) {
            return back()->with('error', 'العضو غير موجود في الفريق');
        }

        if ($member->pivot->role === 'leader') {
            return back()->with('error', 'لا يمكن إزالة قائد الفريق');
        }

        $team->members()->detach($userId);

        return redirect()->route('maintenance.teams.show', $team)
            ->with('success', 'تم إزالة العضو من الفريق بنجاح');
    }

    public function updateMemberRole(MaintenanceTeam $team, $userId, Request $request)
    {
        $validated = $request->validate([
            'role' => 'required|in:leader,member,specialist',
        ]);

        $team->members()->updateExistingPivot($userId, ['role' => $validated['role']]);

        return redirect()->route('maintenance.teams.show', $team)
            ->with('success', 'تم تحديث دور العضو بنجاح');
    }

    public function getTeamAvailability(MaintenanceTeam $team, Request $request)
    {
        $date = $request->date ?? now()->format('Y-m-d');
        
        $schedules = $team->schedules()
            ->whereDate('scheduled_date', $date)
            ->with(['property'])
            ->orderBy('scheduled_date')
            ->get();

        $currentJobs = $schedules->where('status', 'in_progress')->count();
        $maxJobs = $team->max_concurrent_jobs;
        $isAvailable = $currentJobs < $maxJobs;

        return response()->json([
            'team' => $team,
            'schedules' => $schedules,
            'current_jobs' => $currentJobs,
            'max_jobs' => $maxJobs,
            'is_available' => $isAvailable,
            'available_slots' => $maxJobs - $currentJobs,
        ]);
    }

    public function performance(MaintenanceTeam $team)
    {
        $stats = [
            'monthly_completed' => $team->maintenanceRequests()
                ->where('status', 'completed')
                ->whereMonth('completed_at', now()->month)
                ->count(),
            'monthly_in_progress' => $team->maintenanceRequests()
                ->where('status', 'in_progress')
                ->whereMonth('created_at', now()->month)
                ->count(),
            'average_completion_time' => $team->maintenanceRequests()
                ->where('status', 'completed')
                ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, started_at, completed_at)) as avg_time')
                ->value('avg_time'),
            'completion_rate' => $team->maintenanceRequests()
                ->where('status', 'completed')
                ->count() > 0 ? 
                ($team->maintenanceRequests()->where('status', 'completed')->count() / 
                 $team->maintenanceRequests()->count()) * 100 : 0,
            'total_revenue' => $team->maintenanceRequests()
                ->where('status', 'completed')
                ->sum('actual_cost'),
        ];

        $monthlyData = $team->maintenanceRequests()
            ->where('status', 'completed')
            ->selectRaw('MONTH(completed_at) as month, YEAR(completed_at) as year, COUNT(*) as count, SUM(actual_cost) as revenue')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->take(12)
            ->get();

        return view('maintenance.teams-performance', compact('team', 'stats', 'monthlyData'));
    }

    public function schedule(MaintenanceTeam $team, Request $request)
    {
        $date = $request->date ?? now()->format('Y-m-d');
        
        $schedules = $team->schedules()
            ->whereDate('scheduled_date', $date)
            ->with(['property', 'serviceProvider'])
            ->orderBy('scheduled_date')
            ->get();

        $availability = $this->getTeamAvailability($team, $request)->getData();

        return view('maintenance.teams-schedule', compact('team', 'schedules', 'date', 'availability'));
    }

    public function export(Request $request)
    {
        $teams = MaintenanceTeam::with(['teamLeader', 'members'])
            ->when($request->is_active, function($query, $isActive) {
                $query->where('is_active', $isActive);
            })
            ->when($request->specialization, function($query, $specialization) {
                $query->where('specialization', $specialization);
            })
            ->get();

        $filename = 'maintenance_teams_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($teams) {
            $file = fopen('php://output', 'w');
            
            // CSV Header
            fputcsv($file, [
                'كود الفريق',
                'الاسم',
                'قائد الفريق',
                'التخصص',
                'الهاتف',
                'البريد الإلكتروني',
                'عدد الأعضاء',
                'الحد الأقصى للوظائف',
                'الحالة',
            ]);

            // CSV Data
            foreach ($teams as $team) {
                fputcsv($file, [
                    $team->team_code,
                    $team->name,
                    $team->teamLeader->name ?? '',
                    $this->getSpecializationLabel($team->specialization),
                    $team->contact_phone,
                    $team->contact_email,
                    $team->members->count(),
                    $team->max_concurrent_jobs,
                    $team->is_active ? 'نشط' : 'غير نشط',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function getSpecializationLabel($specialization)
    {
        $labels = [
            'plumbing' => 'سباكة',
            'electrical' => 'كهرباء',
            'hvac' => 'تكييف',
            'structural' => 'إنشائي',
            'general' => 'عام',
            'multi' => 'متعدد',
        ];

        return $labels[$specialization] ?? $specialization;
    }
}

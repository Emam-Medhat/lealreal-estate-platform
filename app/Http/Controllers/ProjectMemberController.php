<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectMemberController extends Controller
{
    public function __construct()
    {
        // Add middleware as needed
    }

    /**
     * Display a listing of project members.
     */
    public function index(Project $project)
    {
        $members = $project->members()->with('user')->get();
        
        return view('projects.members.index', compact('project', 'members'));
    }

    /**
     * Show the form for creating a new project member.
     */
    public function create(Project $project)
    {
        $users = User::whereDoesntHave('projectMembers', function ($query) use ($project) {
            $query->where('project_id', $project->id);
        })->get();

        $roles = ['manager', 'developer', 'designer', 'tester', 'analyst'];

        return view('projects.members.create', compact('project', 'users', 'roles'));
    }

    /**
     * Store a newly created project member in storage.
     */
    public function store(Request $request, Project $project)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'hourly_rate' => 'nullable|numeric|min:0',
        ]);

        $project->members()->create([
            'user_id' => $request->user_id,
            'role' => $request->role,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'hourly_rate' => $request->hourly_rate,
            'created_by' => Auth::id(),
        ]);

        return redirect()
            ->route('projects.members.index', $project)
            ->with('success', 'تم إضافة عضو المشروع بنجاح');
    }

    /**
     * Display the specified project member.
     */
    public function show(Project $project, ProjectMember $member)
    {
        $member->load(['user', 'timeLogs', 'tasks']);

        return view('projects.members.show', compact('project', 'member'));
    }

    /**
     * Show the form for editing the specified project member.
     */
    public function edit(Project $project, ProjectMember $member)
    {
        $roles = ['manager', 'developer', 'designer', 'tester', 'analyst'];

        return view('projects.members.edit', compact('project', 'member', 'roles'));
    }

    /**
     * Update the specified project member in storage.
     */
    public function update(Request $request, Project $project, ProjectMember $member)
    {
        $request->validate([
            'role' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'hourly_rate' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive,completed',
        ]);

        $member->update([
            'role' => $request->role,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'hourly_rate' => $request->hourly_rate,
            'status' => $request->status,
            'updated_by' => Auth::id(),
        ]);

        return redirect()
            ->route('projects.members.show', [$project, $member])
            ->with('success', 'تم تحديث عضو المشروع بنجاح');
    }

    /**
     * Remove the specified project member from storage.
     */
    public function destroy(Project $project, ProjectMember $member)
    {
        $member->delete();

        return redirect()
            ->route('projects.members.index', $project)
            ->with('success', 'تم حذف عضو المشروع بنجاح');
    }

    /**
     * Get member performance metrics.
     */
    public function performance(Project $project, ProjectMember $member)
    {
        $metrics = $member->getPerformanceMetrics();

        return response()->json($metrics);
    }

    /**
     * Track time for member.
     */
    public function trackTime(Request $request, Project $project, ProjectMember $member)
    {
        $request->validate([
            'hours' => 'required|numeric|min:0.1',
            'description' => 'required|string',
            'date' => 'required|date',
        ]);

        $timeLog = $member->timeLogs()->create([
            'hours' => $request->hours,
            'description' => $request->description,
            'date' => $request->date,
            'created_by' => Auth::id(),
        ]);

        return back()->with('success', 'تم تسجيل الوقت بنجاح');
    }

    /**
     * Get member time logs.
     */
    public function timeLogs(Project $project, ProjectMember $member, Request $request)
    {
        $logs = $member->timeLogs()
            ->when($request->date_from, function ($query, $date) {
                $query->whereDate('date', '>=', $date);
            })
            ->when($request->date_to, function ($query, $date) {
                $query->whereDate('date', '<=', $date);
            })
            ->latest()
            ->paginate(20);

        return view('projects.members.time-logs', compact('project', 'member', 'logs'));
    }

    /**
     * Update member status.
     */
    public function updateStatus(Request $request, Project $project, ProjectMember $member)
    {
        $request->validate([
            'status' => 'required|in:active,inactive,completed'
        ]);

        $member->update([
            'status' => $request->status,
            'updated_by' => Auth::id(),
        ]);

        return back()->with('success', 'تم تحديث الحالة بنجاح');
    }
}

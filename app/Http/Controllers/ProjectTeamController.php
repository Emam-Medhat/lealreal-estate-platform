<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectTeam;
use App\Models\ProjectMember;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectTeamController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Project $project)
    {
        $team = $project->team()->with(['members.user', 'members.role'])->first();
        
        if (!$team) {
            $team = $project->team()->create([
                'name' => 'فريق ' . $project->name,
                'created_by' => Auth::id(),
            ]);
            $team->refresh();
        }

        return view('projects.teams.index', compact('project', 'team'));
    }

    public function create(Project $project)
    {
        return view('projects.teams.create', compact('project'));
    }

    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'leader_id' => 'required|exists:users,id',
        ]);

        $validated['created_by'] = Auth::id();

        $team = $project->team()->create($validated);

        // Add leader as team member
        $team->members()->create([
            'user_id' => $validated['leader_id'],
            'role_id' => 1, // Assuming role_id 1 is team leader
            'joined_at' => now(),
            'created_by' => Auth::id(),
        ]);

        return redirect()
            ->route('projects.teams.show', [$project, $team])
            ->with('success', 'تم إنشاء الفريق بنجاح');
    }

    public function show(Project $project, ProjectTeam $team)
    {
        $team->load(['members.user', 'members.role', 'members.tasks']);
        
        $availableUsers = User::whereNotIn('id', $team->members->pluck('user_id'))->get();

        return view('projects.teams.show', compact('project', 'team', 'availableUsers'));
    }

    public function edit(Project $project, ProjectTeam $team)
    {
        return view('projects.teams.edit', compact('project', 'team'));
    }

    public function update(Request $request, Project $project, ProjectTeam $team)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'leader_id' => 'required|exists:users,id',
        ]);

        $team->update($validated);

        return redirect()
            ->route('projects.teams.show', [$project, $team])
            ->with('success', 'تم تحديث الفريق بنجاح');
    }

    public function addMember(Request $request, Project $project, ProjectTeam $team)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role_id' => 'required|exists:roles,id',
            'hourly_rate' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
        ]);

        // Check if user is already a member
        if ($team->members()->where('user_id', $validated['user_id'])->exists()) {
            return back()->with('error', 'المستخدم بالفعل عضو في الفريق');
        }

        $team->members()->create([
            'user_id' => $validated['user_id'],
            'role_id' => $validated['role_id'],
            'hourly_rate' => $validated['hourly_rate'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'joined_at' => now(),
            'created_by' => Auth::id(),
        ]);

        return back()->with('success', 'تم إضافة العضو بنجاح');
    }

    public function updateMember(Request $request, Project $project, ProjectTeam $team, ProjectMember $member)
    {
        $validated = $request->validate([
            'role_id' => 'required|exists:roles,id',
            'hourly_rate' => 'nullable|numeric|min:0',
            'end_date' => 'nullable|date|after:start_date',
            'status' => 'required|in:active,inactive,completed',
        ]);

        $member->update($validated);

        return back()->with('success', 'تم تحديث العضو بنجاح');
    }

    public function removeMember(Project $project, ProjectTeam $team, ProjectMember $member)
    {
        $member->delete();

        return back()->with('success', 'تم إزالة العضو بنجاح');
    }

    public function assignTask(Request $request, Project $project, ProjectTeam $team)
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:project_members,id',
            'task_id' => 'required|exists:project_tasks,id',
        ]);

        $member = $team->members()->findOrFail($validated['member_id']);
        $task = $project->tasks()->findOrFail($validated['task_id']);

        $task->update([
            'assignee_id' => $member->user_id,
            'updated_by' => Auth::id(),
        ]);

        return back()->with('success', 'تم تعيين المهمة بنجاح');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectPhase;
use App\Models\ProjectTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ProjectPhaseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Project $project)
    {
        $phases = $project->phases()
            ->with(['tasks', 'milestones'])
            ->orderBy('order')
            ->get();

        return view('projects.phases.index', compact('project', 'phases'));
    }

    public function dashboard()
    {
        $phases = ProjectPhase::with(['project', 'tasks', 'milestones'])
            ->orderBy('order')
            ->get();

        $stats = [
            'total_phases' => $phases->count(),
            'active_phases' => $phases->where('status', 'active')->count(),
            'completed_phases' => $phases->where('status', 'completed')->count(),
            'upcoming_phases' => $phases->where('status', 'pending')->count(),
        ];

        return view('projects.phases.dashboard', compact('phases', 'stats'));
    }

    public function create(Project $project)
    {
        return view('projects.phases.create', compact('project'));
    }

    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'required|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'duration_days' => 'required|integer|min:1',
            'status' => 'required|in:not_started,in_progress,completed,on_hold,cancelled',
            'progress_percentage' => 'required|integer|min:0|max:100',
            'notes' => 'nullable|string',
        ]);

        $validated['created_by'] = Auth::id();

        $phase = $project->phases()->create($validated);

        return redirect()
            ->route('projects.phases.show', [$project, $phase])
            ->with('success', 'تم إنشاء المرحلة بنجاح');
    }

    public function show(Project $project, ProjectPhase $phase)
    {
        $phase->load([
            'tasks.assignee',
            'tasks.dependencies',
            'milestones',
            'documents'
        ]);

        $stats = [
            'total_tasks' => $phase->tasks()->count(),
            'completed_tasks' => $phase->tasks()->where('status', 'completed')->count(),
            'total_milestones' => $phase->milestones()->count(),
            'completed_milestones' => $phase->milestones()->where('status', 'completed')->count(),
            'days_remaining' => Carbon::now()->diffInDays($phase->end_date, false),
        ];

        return view('projects.phases.show', compact('project', 'phase', 'stats'));
    }

    public function edit(Project $project, ProjectPhase $phase)
    {
        return view('projects.phases.edit', compact('project', 'phase'));
    }

    public function update(Request $request, Project $project, ProjectPhase $phase)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'required|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'duration_days' => 'required|integer|min:1',
            'status' => 'required|in:not_started,in_progress,completed,on_hold,cancelled',
            'progress_percentage' => 'required|integer|min:0|max:100',
            'notes' => 'nullable|string',
        ]);

        $validated['updated_by'] = Auth::id();

        $phase->update($validated);

        return redirect()
            ->route('projects.phases.show', [$project, $phase])
            ->with('success', 'تم تحديث المرحلة بنجاح');
    }

    public function destroy(Project $project, ProjectPhase $phase)
    {
        // Check if phase can be deleted
        if ($phase->tasks()->count() > 0) {
            return back()->with('error', 'لا يمكن حذف المرحلة لوجود مهام مرتبطة بها');
        }

        $phase->delete();

        return redirect()
            ->route('projects.phases.index', $project)
            ->with('success', 'تم حذف المرحلة بنجاح');
    }

    public function updateStatus(Request $request, Project $project, ProjectPhase $phase)
    {
        $validated = $request->validate([
            'status' => 'required|in:not_started,in_progress,completed,on_hold,cancelled',
            'notes' => 'nullable|string',
        ]);

        $phase->update([
            'status' => $validated['status'],
            'notes' => $validated['notes'],
            'updated_by' => Auth::id(),
        ]);

        // Update progress based on status
        if ($validated['status'] === 'completed') {
            $phase->update(['progress_percentage' => 100]);
        } elseif ($validated['status'] === 'not_started') {
            $phase->update(['progress_percentage' => 0]);
        }

        return back()->with('success', 'تم تحديث حالة المرحلة بنجاح');
    }

    public function updateProgress(Request $request, Project $project, ProjectPhase $phase)
    {
        $validated = $request->validate([
            'progress_percentage' => 'required|integer|min:0|max:100',
        ]);

        $phase->update([
            'progress_percentage' => $validated['progress_percentage'],
            'updated_by' => Auth::id(),
        ]);

        // Auto-update status based on progress
        if ($validated['progress_percentage'] == 100 && $phase->status != 'completed') {
            $phase->update(['status' => 'completed']);
        } elseif ($validated['progress_percentage'] > 0 && $phase->status == 'not_started') {
            $phase->update(['status' => 'in_progress']);
        }

        return back()->with('success', 'تم تحديث تقدم المرحلة بنجاح');
    }

    public function reorder(Request $request, Project $project)
    {
        $validated = $request->validate([
            'phases' => 'required|array',
            'phases.*.id' => 'required|exists:project_phases,id',
            'phases.*.order' => 'required|integer|min:1',
        ]);

        foreach ($validated['phases'] as $phaseData) {
            ProjectPhase::where('id', $phaseData['id'])
                ->where('project_id', $project->id)
                ->update(['order' => $phaseData['order']]);
        }

        return back()->with('success', 'تم إعادة ترتيب المراحل بنجاح');
    }

    public function duplicate(Project $project, ProjectPhase $phase)
    {
        $newPhase = $phase->replicate();
        $newPhase->name = $phase->name . ' (نسخة)';
        $newPhase->status = 'not_started';
        $newPhase->progress_percentage = 0;
        $newPhase->created_by = Auth::id();
        $newPhase->updated_by = Auth::id();
        $newPhase->save();

        // Duplicate tasks
        foreach ($phase->tasks as $task) {
            $newTask = $task->replicate();
            $newTask->phase_id = $newPhase->id;
            $newTask->status = 'not_started';
            $newTask->progress_percentage = 0;
            $newTask->created_by = Auth::id();
            $newTask->updated_by = Auth::id();
            $newTask->save();
        }

        return redirect()
            ->route('projects.phases.show', [$project, $newPhase])
            ->with('success', 'تم نسخ المرحلة بنجاح');
    }

    public function timeline(Project $project, ProjectPhase $phase)
    {
        $phase->load(['tasks', 'milestones']);

        $timelineData = $this->preparePhaseTimeline($phase);

        return view('projects.phases.timeline', compact('project', 'phase', 'timelineData'));
    }

    private function preparePhaseTimeline(ProjectPhase $phase)
    {
        $events = [];

        // Add phase itself
        $events[] = [
            'type' => 'phase',
            'title' => $phase->name,
            'start' => $phase->start_date,
            'end' => $phase->end_date,
            'status' => $phase->status,
            'progress' => $phase->progress_percentage
        ];

        // Add tasks
        foreach ($phase->tasks as $task) {
            $events[] = [
                'type' => 'task',
                'title' => $task->name,
                'start' => $task->start_date,
                'end' => $task->end_date,
                'status' => $task->status,
                'progress' => $task->progress_percentage,
                'assignee' => $task->assignee->name ?? 'غير محدد'
            ];
        }

        // Add milestones
        foreach ($phase->milestones as $milestone) {
            $events[] = [
                'type' => 'milestone',
                'title' => $milestone->name,
                'date' => $milestone->due_date,
                'status' => $milestone->status
            ];
        }

        // Sort by date
        usort($events, function ($a, $b) {
            $dateA = $a['start'] ?? $a['date'];
            $dateB = $b['start'] ?? $b['date'];
            return strtotime($dateA) - strtotime($dateB);
        });

        return $events;
    }
}

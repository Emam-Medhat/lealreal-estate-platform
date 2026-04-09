<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectPhase;
use App\Models\ProjectMilestone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ProjectMilestoneController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Project $project)
    {
        $milestones = $project->milestones()
            ->with(['phase', 'tasks'])
            ->orderBy('due_date')
            ->get();

        return view('projects.milestones.index', compact('project', 'milestones'));
    }

    public function dashboard()
    {
        $milestones = ProjectMilestone::with(['project', 'phase', 'tasks'])
            ->orderBy('due_date')
            ->get();

        $stats = [
            'total_milestones' => $milestones->count(),
            'completed_milestones' => $milestones->where('status', 'completed')->count(),
            'upcoming_milestones' => $milestones->where('status', 'pending')->count(),
            'overdue_milestones' => $milestones->where('due_date', '<', now())->where('status', '!=', 'completed')->count(),
        ];

        return view('projects.milestones.dashboard', compact('milestones', 'stats'));
    }

    public function create(Project $project)
    {
        $phases = $project->phases()->orderBy('order')->get();
        
        return view('projects.milestones.create', compact('project', 'phases'));
    }

    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'phase_id' => 'nullable|exists:project_phases,id',
            'due_date' => 'required|date',
            'priority' => 'required|in:low,medium,high,critical',
            'status' => 'required|in:not_started,in_progress,completed,overdue',
            'completion_percentage' => 'required|integer|min:0|max:100',
            'deliverables' => 'nullable|array',
            'dependencies' => 'nullable|array',
            'dependencies.*' => 'exists:project_milestones,id',
            'notes' => 'nullable|string',
        ]);

        $validated['created_by'] = Auth::id();

        $milestone = $project->milestones()->create($validated);

        return redirect()
            ->route('projects.milestones.show', [$project, $milestone])
            ->with('success', 'تم إنشاء المعلم الرئيسي بنجاح');
    }

    public function show(Project $project, ProjectMilestone $milestone)
    {
        $milestone->load([
            'phase',
            'tasks',
            'dependencies.dependentMilestone',
            'dependents.milestone',
            'documents'
        ]);

        $stats = [
            'total_tasks' => $milestone->tasks()->count(),
            'completed_tasks' => $milestone->tasks()->where('status', 'completed')->count(),
            'days_remaining' => Carbon::now()->diffInDays($milestone->due_date, false),
            'is_overdue' => $milestone->due_date < now() && $milestone->status != 'completed',
        ];

        return view('projects.milestones.show', compact('project', 'milestone', 'stats'));
    }

    public function edit(Project $project, ProjectMilestone $milestone)
    {
        $phases = $project->phases()->orderBy('order')->get();
        $milestones = $project->milestones()->where('id', '!=', $milestone->id)->get();
        
        return view('projects.milestones.edit', compact('project', 'milestone', 'phases', 'milestones'));
    }

    public function update(Request $request, Project $project, ProjectMilestone $milestone)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'phase_id' => 'nullable|exists:project_phases,id',
            'due_date' => 'required|date',
            'priority' => 'required|in:low,medium,high,critical',
            'status' => 'required|in:not_started,in_progress,completed,overdue',
            'completion_percentage' => 'required|integer|min:0|max:100',
            'deliverables' => 'nullable|array',
            'dependencies' => 'nullable|array',
            'dependencies.*' => 'exists:project_milestones,id',
            'notes' => 'nullable|string',
        ]);

        $validated['updated_by'] = Auth::id();

        $milestone->update($validated);

        return redirect()
            ->route('projects.milestones.show', [$project, $milestone])
            ->with('success', 'تم تحديث المعلم الرئيسي بنجاح');
    }

    public function destroy(Project $project, ProjectMilestone $milestone)
    {
        // Check if milestone can be deleted
        if ($milestone->tasks()->count() > 0) {
            return back()->with('error', 'لا يمكن حذف المعلم الرئيسي لوجود مهام مرتبطة به');
        }

        $milestone->delete();

        return redirect()
            ->route('projects.milestones.index', $project)
            ->with('success', 'تم حذف المعلم الرئيسي بنجاح');
    }

    public function updateStatus(Request $request, Project $project, ProjectMilestone $milestone)
    {
        $validated = $request->validate([
            'status' => 'required|in:not_started,in_progress,completed,overdue',
            'notes' => 'nullable|string',
        ]);

        $milestone->update([
            'status' => $validated['status'],
            'notes' => $validated['notes'],
            'updated_by' => Auth::id(),
        ]);

        // Update completion based on status
        if ($validated['status'] === 'completed') {
            $milestone->update(['completion_percentage' => 100]);
        } elseif ($validated['status'] === 'not_started') {
            $milestone->update(['completion_percentage' => 0]);
        }

        return back()->with('success', 'تم تحديث حالة المعلم الرئيسي بنجاح');
    }

    public function updateProgress(Request $request, Project $project, ProjectMilestone $milestone)
    {
        $validated = $request->validate([
            'completion_percentage' => 'required|integer|min:0|max:100',
        ]);

        $milestone->update([
            'completion_percentage' => $validated['completion_percentage'],
            'updated_by' => Auth::id(),
        ]);

        // Auto-update status based on progress
        if ($validated['completion_percentage'] == 100 && $milestone->status != 'completed') {
            $milestone->update(['status' => 'completed']);
        } elseif ($validated['completion_percentage'] > 0 && $milestone->status == 'not_started') {
            $milestone->update(['status' => 'in_progress']);
        }

        return back()->with('success', 'تم تحديث تقدم المعلم الرئيسي بنجاح');
    }

    public function markCompleted(Request $request, Project $project, ProjectMilestone $milestone)
    {
        $validated = $request->validate([
            'completion_notes' => 'required|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:10240',
        ]);

        $milestone->update([
            'status' => 'completed',
            'completion_percentage' => 100,
            'completion_notes' => $validated['completion_notes'],
            'completed_at' => now(),
            'completed_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        // Handle attachments
        if (!empty($validated['attachments'])) {
            foreach ($validated['attachments'] as $file) {
                $path = $file->store('milestone-attachments', 'public');
                $milestone->documents()->create([
                    'filename' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'uploaded_by' => Auth::id(),
                ]);
            }
        }

        return back()->with('success', 'تم إكمال المعلم الرئيسي بنجاح');
    }

    public function addDeliverable(Request $request, Project $project, ProjectMilestone $milestone)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,in_progress,completed',
            'due_date' => 'nullable|date',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $deliverable = $milestone->deliverables()->create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'status' => $validated['status'],
            'due_date' => $validated['due_date'],
            'assigned_to' => $validated['assigned_to'],
            'created_by' => Auth::id(),
        ]);

        return back()->with('success', 'تم إضافة المخرج بنجاح');
    }

    public function updateDeliverable(Request $request, Project $project, ProjectMilestone $milestone, $deliverableId)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,in_progress,completed',
            'due_date' => 'nullable|date',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $deliverable = $milestone->deliverables()->findOrFail($deliverableId);
        $deliverable->update($validated);

        return back()->with('success', 'تم تحديث المخرج بنجاح');
    }

    public function deleteDeliverable(Project $project, ProjectMilestone $milestone, $deliverableId)
    {
        $deliverable = $milestone->deliverables()->findOrFail($deliverableId);
        $deliverable->delete();

        return back()->with('success', 'تم حذف المخرج بنجاح');
    }
}

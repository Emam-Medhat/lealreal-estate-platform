<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectPhase;
use App\Models\ProjectTask;
use App\Models\ProjectTaskDependency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ProjectTaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request, Project $project)
    {
        $query = $project->tasks()
            ->with(['assignee', 'phase', 'dependencies', 'dependents'])
            ->withCount(['comments', 'attachments']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Filter by phase
        if ($request->filled('phase_id')) {
            $query->where('phase_id', $request->phase_id);
        }

        // Filter by assignee
        if ($request->filled('assignee_id')) {
            $query->where('assignee_id', $request->assignee_id);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $tasks = $query->orderBy('priority', 'desc')
            ->orderBy('due_date')
            ->paginate(20);

        return view('projects.tasks.index', compact('project', 'tasks'));
    }

    public function create(Project $project)
    {
        $phases = $project->phases()->orderBy('order')->get();
        $tasks = $project->tasks()->get();
        
        return view('projects.tasks.create', compact('project', 'phases', 'tasks'));
    }

    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'phase_id' => 'required|exists:project_phases,id',
            'assignee_id' => 'nullable|exists:users,id',
            'priority' => 'required|in:low,medium,high,critical',
            'status' => 'required|in:not_started,in_progress,completed,on_hold,cancelled',
            'start_date' => 'required|date',
            'due_date' => 'required|date|after:start_date',
            'estimated_hours' => 'nullable|integer|min:0',
            'actual_hours' => 'nullable|integer|min:0',
            'progress_percentage' => 'required|integer|min:0|max:100',
            'dependencies' => 'nullable|array',
            'dependencies.*' => 'exists:project_tasks,id',
            'tags' => 'nullable|array',
            'notes' => 'nullable|string',
        ]);

        $validated['created_by'] = Auth::id();

        $task = $project->tasks()->create($validated);

        // Add dependencies
        if (!empty($validated['dependencies'])) {
            foreach ($validated['dependencies'] as $dependencyId) {
                ProjectTaskDependency::create([
                    'task_id' => $task->id,
                    'dependency_task_id' => $dependencyId,
                ]);
            }
        }

        return redirect()
            ->route('projects.tasks.show', [$project, $task])
            ->with('success', 'تم إنشاء المهمة بنجاح');
    }

    public function show(Project $project, ProjectTask $task)
    {
        $task->load([
            'assignee',
            'phase',
            'dependencies.dependentTask',
            'dependents.task',
            'comments.author',
            'attachments.uploadedBy',
            'timeLogs.user',
            'checklists.items'
        ]);

        return view('projects.tasks.show', compact('project', 'task'));
    }

    public function edit(Project $project, ProjectTask $task)
    {
        $phases = $project->phases()->orderBy('order')->get();
        $tasks = $project->tasks()->where('id', '!=', $task->id)->get();
        
        return view('projects.tasks.edit', compact('project', 'task', 'phases', 'tasks'));
    }

    public function update(Request $request, Project $project, ProjectTask $task)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'phase_id' => 'required|exists:project_phases,id',
            'assignee_id' => 'nullable|exists:users,id',
            'priority' => 'required|in:low,medium,high,critical',
            'status' => 'required|in:not_started,in_progress,completed,on_hold,cancelled',
            'start_date' => 'required|date',
            'due_date' => 'required|date|after:start_date',
            'estimated_hours' => 'nullable|integer|min:0',
            'actual_hours' => 'nullable|integer|min:0',
            'progress_percentage' => 'required|integer|min:0|max:100',
            'dependencies' => 'nullable|array',
            'dependencies.*' => 'exists:project_tasks,id',
            'tags' => 'nullable|array',
            'notes' => 'nullable|string',
        ]);

        $validated['updated_by'] = Auth::id();

        $task->update($validated);

        // Update dependencies
        $task->dependencies()->delete();
        if (!empty($validated['dependencies'])) {
            foreach ($validated['dependencies'] as $dependencyId) {
                ProjectTaskDependency::create([
                    'task_id' => $task->id,
                    'dependency_task_id' => $dependencyId,
                ]);
            }
        }

        return redirect()
            ->route('projects.tasks.show', [$project, $task])
            ->with('success', 'تم تحديث المهمة بنجاح');
    }

    public function destroy(Project $project, ProjectTask $task)
    {
        // Check if task can be deleted
        if ($task->dependents()->count() > 0) {
            return back()->with('error', 'لا يمكن حذف المهمة لوجود مهام تعتمد عليها');
        }

        $task->delete();

        return redirect()
            ->route('projects.tasks.index', $project)
            ->with('success', 'تم حذف المهمة بنجاح');
    }

    public function updateStatus(Request $request, Project $project, ProjectTask $task)
    {
        $validated = $request->validate([
            'status' => 'required|in:not_started,in_progress,completed,on_hold,cancelled',
            'notes' => 'nullable|string',
        ]);

        $task->update([
            'status' => $validated['status'],
            'notes' => $validated['notes'],
            'updated_by' => Auth::id(),
        ]);

        // Update progress based on status
        if ($validated['status'] === 'completed') {
            $task->update(['progress_percentage' => 100]);
        } elseif ($validated['status'] === 'not_started') {
            $task->update(['progress_percentage' => 0]);
        }

        return back()->with('success', 'تم تحديث حالة المهمة بنجاح');
    }

    public function updateProgress(Request $request, Project $project, ProjectTask $task)
    {
        $validated = $request->validate([
            'progress_percentage' => 'required|integer|min:0|max:100',
            'actual_hours' => 'nullable|integer|min:0',
        ]);

        $updateData = [
            'progress_percentage' => $validated['progress_percentage'],
            'updated_by' => Auth::id(),
        ];

        if (isset($validated['actual_hours'])) {
            $updateData['actual_hours'] = $validated['actual_hours'];
        }

        $task->update($updateData);

        // Auto-update status based on progress
        if ($validated['progress_percentage'] == 100 && $task->status != 'completed') {
            $task->update(['status' => 'completed']);
        } elseif ($validated['progress_percentage'] > 0 && $task->status == 'not_started') {
            $task->update(['status' => 'in_progress']);
        }

        return back()->with('success', 'تم تحديث تقدم المهمة بنجاح');
    }

    public function assign(Request $request, Project $project, ProjectTask $task)
    {
        $validated = $request->validate([
            'assignee_id' => 'required|exists:users,id',
            'notes' => 'nullable|string',
        ]);

        $task->update([
            'assignee_id' => $validated['assignee_id'],
            'notes' => $validated['notes'],
            'updated_by' => Auth::id(),
        ]);

        return back()->with('success', 'تم تعيين المهمة بنجاح');
    }

    public function addComment(Request $request, Project $project, ProjectTask $task)
    {
        $validated = $request->validate([
            'content' => 'required|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:10240',
        ]);

        $comment = $task->comments()->create([
            'content' => $validated['content'],
            'author_id' => Auth::id(),
        ]);

        // Handle attachments
        if (!empty($validated['attachments'])) {
            foreach ($validated['attachments'] as $file) {
                $path = $file->store('task-attachments', 'public');
                $comment->attachments()->create([
                    'filename' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'uploaded_by' => Auth::id(),
                ]);
            }
        }

        return back()->with('success', 'تم إضافة التعليق بنجاح');
    }

    public function logTime(Request $request, Project $project, ProjectTask $task)
    {
        $validated = $request->validate([
            'hours' => 'required|numeric|min:0.1|max:24',
            'description' => 'required|string',
            'date' => 'required|date',
        ]);

        $task->timeLogs()->create([
            'hours' => $validated['hours'],
            'description' => $validated['description'],
            'date' => $validated['date'],
            'user_id' => Auth::id(),
        ]);

        // Update actual hours
        $totalHours = $task->timeLogs()->sum('hours');
        $task->update(['actual_hours' => $totalHours]);

        return back()->with('success', 'تم تسجيل الوقت بنجاح');
    }

    public function duplicate(Project $project, ProjectTask $task)
    {
        $newTask = $task->replicate();
        $newTask->name = $task->name . ' (نسخة)';
        $newTask->status = 'not_started';
        $newTask->progress_percentage = 0;
        $newTask->actual_hours = 0;
        $newTask->created_by = Auth::id();
        $newTask->updated_by = Auth::id();
        $newTask->save();

        // Copy dependencies
        foreach ($task->dependencies as $dependency) {
            ProjectTaskDependency::create([
                'task_id' => $newTask->id,
                'dependency_task_id' => $dependency->dependency_task_id,
            ]);
        }

        return redirect()
            ->route('projects.tasks.show', [$project, $newTask])
            ->with('success', 'تم نسخ المهمة بنجاح');
    }

    public function kanbanUpdate(Request $request, Project $project)
    {
        $validated = $request->validate([
            'task_id' => 'required|exists:project_tasks,id',
            'status' => 'required|in:not_started,in_progress,completed,on_hold,cancelled',
            'order' => 'nullable|array',
        ]);

        $task = $project->tasks()->findOrFail($validated['task_id']);
        
        $updateData = [
            'status' => $validated['status'],
            'updated_by' => Auth::id(),
        ];

        // Update progress based on status
        if ($validated['status'] === 'completed') {
            $updateData['progress_percentage'] = 100;
        } elseif ($validated['status'] === 'not_started') {
            $updateData['progress_percentage'] = 0;
        }

        $task->update($updateData);

        return response()->json(['success' => true]);
    }
}

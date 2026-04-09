<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Http\Requests\Agent\StoreTaskRequest;
use App\Http\Requests\Agent\UpdateTaskRequest;
use App\Models\Agent;
use App\Models\AgentTask;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AgentTaskController extends Controller
{
    public function index(Request $request)
    {
        $agent = Auth::user()->agent;
        
        $tasks = $agent->tasks()
            ->with(['property', 'lead', 'client'])
            ->when($request->search, function ($query, $search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->priority, function ($query, $priority) {
                $query->where('priority', $priority);
            })
            ->when($request->type, function ($query, $type) {
                $query->where('type', $type);
            })
            ->when($request->date_from, function ($query, $date) {
                $query->whereDate('due_date', '>=', $date);
            })
            ->when($request->date_to, function ($query, $date) {
                $query->whereDate('due_date', '<=', $date);
            })
            ->orderBy('priority', 'desc')
            ->orderBy('due_date')
            ->paginate(20);

        return view('agent.tasks.index', compact('tasks'));
    }

    public function create()
    {
        $agent = Auth::user()->agent;
        
        return view('agent.tasks.create', compact('agent'));
    }

    public function store(StoreTaskRequest $request)
    {
        $agent = Auth::user()->agent;
        
        $task = AgentTask::create([
            'agent_id' => $agent->id,
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
            'priority' => $request->priority,
            'status' => $request->status ?? 'pending',
            'due_date' => $request->due_date,
            'property_id' => $request->property_id,
            'lead_id' => $request->lead_id,
            'client_id' => $request->client_id,
            'estimated_hours' => $request->estimated_hours,
            'actual_hours' => $request->actual_hours,
            'tags' => $request->tags ?? [],
            'checklist' => $request->checklist ?? [],
            'attachments' => $request->attachments ?? [],
            'notes' => $request->notes,
            'assigned_by' => Auth::id(),
            'assigned_at' => now(),
        ]);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'created_task',
            'details' => "Created task: {$task->title}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('agent.tasks.show', $task)
            ->with('success', 'Task created successfully.');
    }

    public function show(AgentTask $task)
    {
        $this->authorize('view', $task);
        
        $task->load(['property', 'lead', 'client', 'subtasks', 'notes']);
        
        return view('agent.tasks.show', compact('task'));
    }

    public function edit(AgentTask $task)
    {
        $this->authorize('update', $task);
        
        return view('agent.tasks.edit', compact('task'));
    }

    public function update(UpdateTaskRequest $request, AgentTask $task)
    {
        $this->authorize('update', $task);
        
        $task->update([
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
            'priority' => $request->priority,
            'status' => $request->status,
            'due_date' => $request->due_date,
            'property_id' => $request->property_id,
            'lead_id' => $request->lead_id,
            'client_id' => $request->client_id,
            'estimated_hours' => $request->estimated_hours,
            'actual_hours' => $request->actual_hours,
            'tags' => $request->tags ?? [],
            'checklist' => $request->checklist ?? [],
            'attachments' => $request->attachments ?? [],
            'notes' => $request->notes,
        ]);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated_task',
            'details' => "Updated task: {$task->title}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('agent.tasks.show', $task)
            ->with('success', 'Task updated successfully.');
    }

    public function destroy(AgentTask $task)
    {
        $this->authorize('delete', $task);
        
        $taskTitle = $task->title;
        $task->delete();

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'deleted_task',
            'details' => "Deleted task: {$taskTitle}",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('agent.tasks.index')
            ->with('success', 'Task deleted successfully.');
    }

    public function updateStatus(Request $request, AgentTask $task): JsonResponse
    {
        $this->authorize('update', $task);
        
        $request->validate([
            'status' => 'required|in:pending,in_progress,completed,cancelled,on_hold',
        ]);

        $task->update([
            'status' => $request->status,
            'status_updated_at' => now(),
        ]);

        if ($request->status === 'completed') {
            $task->update(['completed_at' => now()]);
        }

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated_task_status',
            'details' => "Updated task {$task->title} status to {$request->status}",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'status' => $request->status,
            'message' => 'Task status updated successfully'
        ]);
    }

    public function updatePriority(Request $request, AgentTask $task): JsonResponse
    {
        $this->authorize('update', $task);
        
        $request->validate([
            'priority' => 'required|in:low,medium,high,urgent',
        ]);

        $task->update(['priority' => $request->priority]);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated_task_priority',
            'details' => "Updated task {$task->title} priority to {$request->priority}",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'priority' => $request->priority,
            'message' => 'Task priority updated successfully'
        ]);
    }

    public function addSubtask(Request $request, AgentTask $task): JsonResponse
    {
        $this->authorize('update', $task);
        
        $request->validate([
            'title' => 'required|string|max:200',
            'description' => 'nullable|string|max:1000',
            'due_date' => 'nullable|date|after:now',
        ]);

        $subtask = $task->subtasks()->create([
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'status' => 'pending',
            'agent_id' => Auth::user()->agent->id,
        ]);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'added_subtask',
            'details' => "Added subtask to: {$task->title}",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'subtask' => $subtask,
            'message' => 'Subtask added successfully'
        ]);
    }

    public function updateChecklist(Request $request, AgentTask $task): JsonResponse
    {
        $this->authorize('update', $task);
        
        $request->validate([
            'checklist' => 'required|array',
            'checklist.*.id' => 'required|string',
            'checklist.*.text' => 'required|string',
            'checklist.*.completed' => 'required|boolean',
        ]);

        $task->update(['checklist' => $request->checklist]);

        // Check if all checklist items are completed
        $allCompleted = collect($request->checklist)->every(fn($item) => $item['completed']);
        if ($allCompleted && $task->status !== 'completed') {
            $task->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);
        }

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated_task_checklist',
            'details' => "Updated checklist for: {$task->title}",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Checklist updated successfully'
        ]);
    }

    public function logTime(Request $request, AgentTask $task): JsonResponse
    {
        $this->authorize('update', $task);
        
        $request->validate([
            'hours' => 'required|numeric|min:0.1|max:24',
            'notes' => 'nullable|string|max:500',
        ]);

        $timeLog = $task->timeLogs()->create([
            'agent_id' => Auth::user()->agent->id,
            'hours' => $request->hours,
            'notes' => $request->notes,
            'logged_at' => now(),
        ]);

        // Update actual hours
        $totalHours = $task->timeLogs()->sum('hours');
        $task->update(['actual_hours' => $totalHours]);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'logged_task_time',
            'details' => "Logged {$request->hours} hours for: {$task->title}",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'time_log' => $timeLog,
            'total_hours' => $totalHours,
            'message' => 'Time logged successfully'
        ]);
    }

    public function addNote(Request $request, AgentTask $task): JsonResponse
    {
        $this->authorize('update', $task);
        
        $request->validate([
            'note' => 'required|string|max:1000',
        ]);

        $note = $task->notes()->create([
            'agent_id' => Auth::user()->agent->id,
            'content' => $request->note,
        ]);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'added_task_note',
            'details' => "Added note to: {$task->title}",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'note' => $note,
            'message' => 'Note added successfully'
        ]);
    }

    public function getTaskStats(): JsonResponse
    {
        $agent = Auth::user()->agent;
        
        $stats = [
            'total_tasks' => $agent->tasks()->count(),
            'pending_tasks' => $agent->tasks()->where('status', 'pending')->count(),
            'in_progress_tasks' => $agent->tasks()->where('status', 'in_progress')->count(),
            'completed_tasks' => $agent->tasks()->where('status', 'completed')->count(),
            'overdue_tasks' => $agent->tasks()
                ->where('due_date', '<', now())
                ->where('status', '!=', 'completed')
                ->count(),
            'due_today_tasks' => $agent->tasks()
                ->whereDate('due_date', today())
                ->where('status', '!=', 'completed')
                ->count(),
            'urgent_tasks' => $agent->tasks()->where('priority', 'urgent')->count(),
            'high_priority_tasks' => $agent->tasks()->where('priority', 'high')->count(),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    public function getUpcomingTasks(): JsonResponse
    {
        $agent = Auth::user()->agent;
        
        $tasks = $agent->tasks()
            ->where('due_date', '>=', now())
            ->where('due_date', '<=', now()->addDays(7))
            ->where('status', '!=', 'completed')
            ->orderBy('priority', 'desc')
            ->orderBy('due_date')
            ->limit(10)
            ->get(['id', 'title', 'due_date', 'priority', 'status']);

        return response()->json([
            'success' => true,
            'tasks' => $tasks
        ]);
    }

    public function exportTasks(Request $request): JsonResponse
    {
        $request->validate([
            'format' => 'required|in:json,csv,xlsx',
            'status' => 'nullable|in:pending,in_progress,completed,cancelled,on_hold',
            'priority' => 'nullable|in:low,medium,high,urgent',
        ]);

        $agent = Auth::user()->agent;
        
        $query = $agent->tasks()->with(['property', 'lead', 'client']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->priority) {
            $query->where('priority', $request->priority);
        }

        $tasks = $query->get();

        $filename = "agent_tasks_export_" . now()->format('Y-m-d_H-i-s');

        return response()->json([
            'success' => true,
            'data' => $tasks,
            'filename' => $filename,
            'message' => 'Tasks exported successfully'
        ]);
    }

    public function bulkUpdate(Request $request): JsonResponse
    {
        $request->validate([
            'task_ids' => 'required|array',
            'task_ids.*' => 'exists:agent_tasks,id',
            'action' => 'required|in:complete,cancel,delete,update_priority',
            'priority' => 'required_if:action,update_priority|in:low,medium,high,urgent',
        ]);

        $agent = Auth::user()->agent;
        $taskIds = $request->task_ids;
        $action = $request->action;

        switch ($action) {
            case 'complete':
                $agent->tasks()->whereIn('id', $taskIds)->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                ]);
                $message = 'Tasks marked as completed';
                break;
            
            case 'cancel':
                $agent->tasks()->whereIn('id', $taskIds)->update([
                    'status' => 'cancelled',
                ]);
                $message = 'Tasks cancelled';
                break;
            
            case 'delete':
                $agent->tasks()->whereIn('id', $taskIds)->delete();
                $message = 'Tasks deleted';
                break;
            
            case 'update_priority':
                $agent->tasks()->whereIn('id', $taskIds)->update([
                    'priority' => $request->priority,
                ]);
                $message = "Tasks priority updated to {$request->priority}";
                break;
        }

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'bulk_updated_tasks',
            'details' => "Bulk {$action} on " . count($taskIds) . " tasks",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }
}

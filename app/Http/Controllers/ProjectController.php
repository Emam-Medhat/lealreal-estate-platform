<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::with(['client', 'manager', 'team'])
            ->when(request('status'), function($query, $status) {
                $query->where('status', $status);
            })
            ->when(request('priority'), function($query, $priority) {
                $query->where('priority', $priority);
            })
            ->when(request('client_id'), function($query, $clientId) {
                $query->where('client_id', $clientId);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $stats = [
            'total_projects' => Project::count(),
            'active_projects' => Project::where('status', 'active')->count(),
            'completed_projects' => Project::where('status', 'completed')->count(),
            'overdue_projects' => Project::where('end_date', '<', now())
                ->where('status', '!=', 'completed')
                ->count(),
        ];

        return view('projects.index', compact('projects', 'stats'));
    }

    public function create()
    {
        return view('projects.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'client_id' => 'required|exists:users,id',
            'manager_id' => 'required|exists:users,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'budget' => 'required|numeric|min:0',
            'priority' => 'required|in:low,medium,high,critical',
            'status' => 'required|in:planning,active,on_hold,completed,cancelled',
        ]);

        $validated['created_by'] = auth()->id();
        Project::create($validated);

        return redirect()->route('projects.index')
            ->with('success', 'Project created successfully.');
    }

    public function show(Project $project)
    {
        $project->load(['client', 'manager', 'team', 'tasks', 'milestones']);

        // Calculate project statistics
        $stats = [
            'progress_percentage' => $project->getProgressPercentage(),
            'total_tasks' => $project->tasks()->count(),
            'completed_tasks' => $project->tasks()->where('status', 'completed')->count(),
            'total_milestones' => $project->milestones()->count(),
            'completed_milestones' => $project->milestones()->where('status', 'completed')->count(),
            'total_spent' => $project->expenses()->sum('amount'),
            'budget_remaining' => $project->budget - $project->expenses()->sum('amount'),
            'days_remaining' => $project->end_date ? now()->diffInDays($project->end_date, false) : null,
        ];

        return view('projects.show', compact('project', 'stats'));
    }

    public function edit(Project $project)
    {
        return view('projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'client_id' => 'required|exists:users,id',
            'manager_id' => 'required|exists:users,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'budget' => 'required|numeric|min:0',
            'priority' => 'required|in:low,medium,high,critical',
            'status' => 'required|in:planning,active,on_hold,completed,cancelled',
        ]);

        $project->update($validated);

        return redirect()->route('projects.index')
            ->with('success', 'Project updated successfully.');
    }

    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'Project deleted successfully.');
    }

    public function dashboard()
    {
        $stats = [
            'total_projects' => Project::count(),
            'active_projects' => Project::where('status', 'active')->count(),
            'completed_this_month' => Project::where('status', 'completed')
                ->whereMonth('updated_at', now()->month)
                ->count(),
            'overdue_projects' => Project::where('end_date', '<', now())
                ->where('status', '!=', 'completed')
                ->count(),
        ];

        $recentProjects = Project::with(['client', 'manager'])
            ->latest()
            ->limit(5)
            ->get();

        $upcomingDeadlines = Project::with(['client', 'manager'])
            ->where('end_date', '>', now())
            ->where('end_date', '<=', now()->addDays(30))
            ->where('status', 'active')
            ->orderBy('end_date')
            ->limit(5)
            ->get();

        return view('projects.dashboard', compact('stats', 'recentProjects', 'upcomingDeadlines'));
    }
}
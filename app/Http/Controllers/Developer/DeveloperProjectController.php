<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Developer\StoreProjectRequest;
use App\Http\Requests\Developer\UpdateProjectRequest;
use App\Models\Developer;
use App\Models\DeveloperProject;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DeveloperProjectController extends Controller
{
    public function index(Request $request)
    {
        $developer = Auth::user()->developer;
        
        $projects = $developer->projects()
            ->with(['phases', 'units'])
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->type, function ($query, $type) {
                $query->where('project_type', $type);
            })
            ->latest()
            ->paginate(20);

        return view('developer.projects.index', compact('projects'));
    }

    public function create()
    {
        $developer = Auth::user()->developer;
        return view('developer.projects.create', compact('developer'));
    }

    public function store(StoreProjectRequest $request)
    {
        $developer = Auth::user()->developer;
        
        $project = DeveloperProject::create([
            'developer_id' => $developer->id,
            'name' => $request->name,
            'description' => $request->description,
            'project_type' => $request->project_type,
            'status' => $request->status ?? 'planning',
            'location' => $request->location,
            'city' => $request->city,
            'state' => $request->state,
            'country' => $request->country,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'land_area' => $request->land_area,
            'total_units' => $request->total_units,
            'total_value' => $request->total_value,
            'total_investment' => $request->total_investment,
            'expected_roi' => $request->expected_roi,
            'start_date' => $request->start_date,
            'completion_date' => $request->completion_date,
            'handover_date' => $request->handover_date,
            'architecture_style' => $request->architecture_style,
            'building_materials' => $request->building_materials ?? [],
            'amenities' => $request->amenities ?? [],
            'facilities' => $request->facilities ?? [],
            'nearby_places' => $request->nearby_places ?? [],
            'payment_plans' => $request->payment_plans ?? [],
            'financing_options' => $request->financing_options ?? [],
            'legal_documents' => $request->legal_documents ?? [],
            'permits' => $request->permits ?? [],
            'contractors' => $request->contractors ?? [],
            'units_sold' => 0,
            'total_revenue' => 0,
            'completion_percentage' => 0,
        ]);

        // Handle project images
        if ($request->hasFile('images')) {
            $images = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('project-images', 'public');
                $images[] = [
                    'path' => $path,
                    'name' => $image->getClientOriginalName(),
                    'uploaded_at' => now()->toISOString(),
                ];
            }
            $project->update(['images' => $images]);
        }

        // Handle master plan
        if ($request->hasFile('master_plan')) {
            $masterPlanPath = $request->file('master_plan')->store('project-master-plans', 'public');
            $project->update(['master_plan' => $masterPlanPath]);
        }

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'created_project',
            'details' => "Created project: {$project->name}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('developer.projects.show', $project)
            ->with('success', 'Project created successfully.');
    }

    public function show(DeveloperProject $project)
    {
        $this->authorize('view', $project);
        
        $project->load(['developer', 'phases', 'units', 'permits', 'contractors', 'constructionUpdates']);
        
        return view('developer.projects.show', compact('project'));
    }

    public function edit(DeveloperProject $project)
    {
        $this->authorize('update', $project);
        
        return view('developer.projects.edit', compact('project'));
    }

    public function update(UpdateProjectRequest $request, DeveloperProject $project)
    {
        $this->authorize('update', $project);
        
        $project->update([
            'name' => $request->name,
            'description' => $request->description,
            'project_type' => $request->project_type,
            'status' => $request->status,
            'location' => $request->location,
            'city' => $request->city,
            'state' => $request->state,
            'country' => $request->country,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'land_area' => $request->land_area,
            'total_units' => $request->total_units,
            'total_value' => $request->total_value,
            'total_investment' => $request->total_investment,
            'expected_roi' => $request->expected_roi,
            'start_date' => $request->start_date,
            'completion_date' => $request->completion_date,
            'handover_date' => $request->handover_date,
            'architecture_style' => $request->architecture_style,
            'building_materials' => $request->building_materials ?? [],
            'amenities' => $request->amenities ?? [],
            'facilities' => $request->facilities ?? [],
            'nearby_places' => $request->nearby_places ?? [],
            'payment_plans' => $request->payment_plans ?? [],
            'financing_options' => $request->financing_options ?? [],
            'legal_documents' => $request->legal_documents ?? [],
            'permits' => $request->permits ?? [],
            'contractors' => $request->contractors ?? [],
        ]);

        // Handle new project images
        if ($request->hasFile('images')) {
            $existingImages = $project->images ?? [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('project-images', 'public');
                $existingImages[] = [
                    'path' => $path,
                    'name' => $image->getClientOriginalName(),
                    'uploaded_at' => now()->toISOString(),
                ];
            }
            $project->update(['images' => $existingImages]);
        }

        // Handle master plan update
        if ($request->hasFile('master_plan')) {
            if ($project->master_plan) {
                Storage::disk('public')->delete($project->master_plan);
            }
            $masterPlanPath = $request->file('master_plan')->store('project-master-plans', 'public');
            $project->update(['master_plan' => $masterPlanPath]);
        }

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated_project',
            'details' => "Updated project: {$project->name}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('developer.projects.show', $project)
            ->with('success', 'Project updated successfully.');
    }

    public function destroy(DeveloperProject $project)
    {
        $this->authorize('delete', $project);
        
        $projectName = $project->name;
        
        // Delete project images
        if ($project->images) {
            foreach ($project->images as $image) {
                Storage::disk('public')->delete($image['path']);
            }
        }
        
        // Delete master plan
        if ($project->master_plan) {
            Storage::disk('public')->delete($project->master_plan);
        }
        
        $project->delete();

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'deleted_project',
            'details' => "Deleted project: {$projectName}",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('developer.projects.index')
            ->with('success', 'Project deleted successfully.');
    }

    public function updateStatus(Request $request, DeveloperProject $project): JsonResponse
    {
        $this->authorize('update', $project);
        
        $request->validate([
            'status' => 'required|in:planning,under_construction,completed,on_hold,cancelled',
        ]);

        $project->update(['status' => $request->status]);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated_project_status',
            'details' => "Updated project {$project->name} status to {$request->status}",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'status' => $request->status,
            'message' => 'Project status updated successfully'
        ]);
    }

    public function updateProgress(Request $request, DeveloperProject $project): JsonResponse
    {
        $this->authorize('update', $project);
        
        $request->validate([
            'completion_percentage' => 'required|integer|min:0|max:100',
        ]);

        $project->update(['completion_percentage' => $request->completion_percentage]);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated_project_progress',
            'details' => "Updated project {$project->name} progress to {$request->completion_percentage}%",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'completion_percentage' => $request->completion_percentage,
            'message' => 'Project progress updated successfully'
        ]);
    }

    public function getProjectStats(DeveloperProject $project): JsonResponse
    {
        $this->authorize('view', $project);
        
        $stats = [
            'total_units' => $project->units()->count(),
            'available_units' => $project->units()->where('status', 'available')->count(),
            'reserved_units' => $project->units()->where('status', 'reserved')->count(),
            'sold_units' => $project->units()->where('status', 'sold')->count(),
            'under_construction_units' => $project->units()->where('status', 'under_construction')->count(),
            'ready_units' => $project->units()->where('status', 'ready')->count(),
            'total_phases' => $project->phases()->count(),
            'completed_phases' => $project->phases()->where('status', 'completed')->count(),
            'in_progress_phases' => $project->phases()->where('status', 'in_progress')->count(),
            'total_revenue' => $project->units()->where('status', 'sold')->sum('price'),
            'average_unit_price' => $project->units()->where('status', 'sold')->avg('price'),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    public function exportProjects(Request $request): JsonResponse
    {
        $request->validate([
            'format' => 'required|in:json,csv,xlsx',
            'status' => 'nullable|in:planning,under_construction,completed,on_hold,cancelled',
        ]);

        $developer = Auth::user()->developer;
        
        $query = $developer->projects()->with(['phases', 'units']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $projects = $query->get();

        $filename = "developer_projects_export_" . now()->format('Y-m-d_H-i-s');

        return response()->json([
            'success' => true,
            'data' => $projects,
            'filename' => $filename,
            'message' => 'Projects exported successfully'
        ]);
    }
}

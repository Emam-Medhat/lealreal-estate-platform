<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Developer\AddUnitRequest;
use App\Http\Requests\Developer\UpdateUnitRequest;
use App\Models\DeveloperProject;
use App\Models\DeveloperProjectUnit;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DeveloperProjectUnitController extends Controller
{
    public function index(Request $request, DeveloperProject $project)
    {
        $this->authorize('view', $project);
        
        $units = $project->units()
            ->when($request->search, function ($query, $search) {
                $query->where('unit_number', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->unit_type, function ($query, $unitType) {
                $query->where('unit_type', $unitType);
            })
            ->orderBy('unit_number')
            ->paginate(20);

        return view('developer.units.index', compact('project', 'units'));
    }

    public function create(DeveloperProject $project)
    {
        $this->authorize('update', $project);
        
        return view('developer.units.create', compact('project'));
    }

    public function store(AddUnitRequest $request, DeveloperProject $project)
    {
        $this->authorize('update', $project);
        
        $unit = DeveloperProjectUnit::create([
            'project_id' => $project->id,
            'unit_number' => $request->unit_number,
            'unit_type' => $request->unit_type,
            'status' => $request->status ?? 'available',
            'floor_number' => $request->floor_number,
            'block_number' => $request->block_number,
            'bedrooms' => $request->bedrooms,
            'bathrooms' => $request->bathrooms,
            'total_area' => $request->total_area,
            'net_area' => $request->net_area,
            'price' => $request->price,
            'price_per_sqm' => $request->price_per_sqm ?? round($request->price / $request->total_area, 2),
            'orientation' => $request->orientation,
            'view' => $request->view,
            'balcony_area' => $request->balcony_area,
            'garden_area' => $request->garden_area,
            'parking_spaces' => $request->parking_spaces,
            'storage_rooms' => $request->storage_rooms,
            'features' => $request->features ?? [],
            'finishing_level' => $request->finishing_level,
            'kitchen_type' => $request->kitchen_type,
            'furniture_included' => $request->furniture_included ?? false,
            'appliances_included' => $request->appliances_included ?? false,
            'delivery_date' => $request->delivery_date,
            'maintenance_fee' => $request->maintenance_fee,
            'service_charges' => $request->service_charges,
            'description' => $request->description,
            'virtual_tour' => $request->virtual_tour,
            'specifications' => $request->specifications ?? [],
            'custom_fields' => $request->custom_fields ?? [],
        ]);

        // Handle floor plan upload
        if ($request->hasFile('floor_plan')) {
            $floorPlanPath = $request->file('floor_plan')->store('unit-floor-plans', 'public');
            $unit->update(['floor_plan' => $floorPlanPath]);
        }

        // Handle unit images
        if ($request->hasFile('images')) {
            $images = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('unit-images', 'public');
                $images[] = [
                    'path' => $path,
                    'name' => $image->getClientOriginalName(),
                    'uploaded_at' => now()->toISOString(),
                ];
            }
            $unit->update(['images' => $images]);
        }

        // Update project statistics
        $this->updateProjectStats($project);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'created_project_unit',
            'details' => "Created unit '{$unit->unit_number}' for project: {$project->name}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('developer.units.show', [$project, $unit])
            ->with('success', 'Unit created successfully.');
    }

    public function show(DeveloperProject $project, DeveloperProjectUnit $unit)
    {
        $this->authorize('view', $project);
        
        return view('developer.units.show', compact('project', 'unit'));
    }

    public function edit(DeveloperProject $project, DeveloperProjectUnit $unit)
    {
        $this->authorize('update', $project);
        
        return view('developer.units.edit', compact('project', 'unit'));
    }

    public function update(UpdateUnitRequest $request, DeveloperProject $project, DeveloperProjectUnit $unit)
    {
        $this->authorize('update', $project);
        
        $unit->update([
            'unit_number' => $request->unit_number,
            'unit_type' => $request->unit_type,
            'status' => $request->status,
            'floor_number' => $request->floor_number,
            'block_number' => $request->block_number,
            'bedrooms' => $request->bedrooms,
            'bathrooms' => $request->bathrooms,
            'total_area' => $request->total_area,
            'net_area' => $request->net_area,
            'price' => $request->price,
            'price_per_sqm' => $request->price_per_sqm ?? round($request->price / $request->total_area, 2),
            'orientation' => $request->orientation,
            'view' => $request->view,
            'balcony_area' => $request->balcony_area,
            'garden_area' => $request->garden_area,
            'parking_spaces' => $request->parking_spaces,
            'storage_rooms' => $request->storage_rooms,
            'features' => $request->features ?? [],
            'finishing_level' => $request->finishing_level,
            'kitchen_type' => $request->kitchen_type,
            'furniture_included' => $request->furniture_included,
            'appliances_included' => $request->appliances_included,
            'delivery_date' => $request->delivery_date,
            'maintenance_fee' => $request->maintenance_fee,
            'service_charges' => $request->service_charges,
            'description' => $request->description,
            'virtual_tour' => $request->virtual_tour,
            'specifications' => $request->specifications ?? [],
            'custom_fields' => $request->custom_fields ?? [],
        ]);

        // Handle floor plan update
        if ($request->hasFile('floor_plan')) {
            if ($unit->floor_plan) {
                Storage::disk('public')->delete($unit->floor_plan);
            }
            $floorPlanPath = $request->file('floor_plan')->store('unit-floor-plans', 'public');
            $unit->update(['floor_plan' => $floorPlanPath]);
        }

        // Handle new unit images
        if ($request->hasFile('images')) {
            $existingImages = $unit->images ?? [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('unit-images', 'public');
                $existingImages[] = [
                    'path' => $path,
                    'name' => $image->getClientOriginalName(),
                    'uploaded_at' => now()->toISOString(),
                ];
            }
            $unit->update(['images' => $existingImages]);
        }

        // Update project statistics
        $this->updateProjectStats($project);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated_project_unit',
            'details' => "Updated unit '{$unit->unit_number}' for project: {$project->name}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('developer.units.show', [$project, $unit])
            ->with('success', 'Unit updated successfully.');
    }

    public function destroy(DeveloperProject $project, DeveloperProjectUnit $unit)
    {
        $this->authorize('update', $project);
        
        $unitNumber = $unit->unit_number;
        
        // Delete unit images
        if ($unit->images) {
            foreach ($unit->images as $image) {
                Storage::disk('public')->delete($image['path']);
            }
        }
        
        // Delete floor plan
        if ($unit->floor_plan) {
            Storage::disk('public')->delete($unit->floor_plan);
        }
        
        $unit->delete();

        // Update project statistics
        $this->updateProjectStats($project);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'deleted_project_unit',
            'details' => "Deleted unit '{$unitNumber}' from project: {$project->name}",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('developer.units.index', $project)
            ->with('success', 'Unit deleted successfully.');
    }

    public function updateStatus(Request $request, DeveloperProject $project, DeveloperProjectUnit $unit): JsonResponse
    {
        $this->authorize('update', $project);
        
        $request->validate([
            'status' => 'required|in:available,reserved,sold,under_construction,ready,maintenance',
        ]);

        $unit->update(['status' => $request->status]);

        // Update project statistics
        $this->updateProjectStats($project);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated_unit_status',
            'details' => "Updated unit '{$unit->unit_number}' status to {$request->status}",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'status' => $request->status,
            'message' => 'Unit status updated successfully'
        ]);
    }

    public function updatePrice(Request $request, DeveloperProject $project, DeveloperProjectUnit $unit): JsonResponse
    {
        $this->authorize('update', $project);
        
        $request->validate([
            'price' => 'required|numeric|min:0',
        ]);

        $unit->update([
            'price' => $request->price,
            'price_per_sqm' => round($request->price / $unit->total_area, 2),
        ]);

        // Update project statistics
        $this->updateProjectStats($project);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated_unit_price',
            'details' => "Updated unit '{$unit->unit_number}' price to {$request->price}",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'price' => $request->price,
            'price_per_sqm' => $unit->price_per_sqm,
            'message' => 'Unit price updated successfully'
        ]);
    }

    public function getUnitStats(DeveloperProject $project): JsonResponse
    {
        $this->authorize('view', $project);
        
        $stats = [
            'total_units' => $project->units()->count(),
            'available_units' => $project->units()->where('status', 'available')->count(),
            'reserved_units' => $project->units()->where('status', 'reserved')->count(),
            'sold_units' => $project->units()->where('status', 'sold')->count(),
            'under_construction_units' => $project->units()->where('status', 'under_construction')->count(),
            'ready_units' => $project->units()->where('status', 'ready')->count(),
            'maintenance_units' => $project->units()->where('status', 'maintenance')->count(),
            'total_value' => $project->units()->sum('price'),
            'sold_value' => $project->units()->where('status', 'sold')->sum('price'),
            'average_price' => $project->units()->avg('price'),
            'average_price_per_sqm' => $project->units()->avg('price_per_sqm'),
            'units_by_type' => $project->units()
                ->groupBy('unit_type')
                ->map(function ($group) {
                    return [
                        'count' => $group->count(),
                        'total_value' => $group->sum('price'),
                        'average_price' => $group->avg('price'),
                    ];
                }),
            'units_by_status' => $project->units()
                ->groupBy('status')
                ->map(function ($group) {
                    return [
                        'count' => $group->count(),
                        'total_value' => $group->sum('price'),
                        'average_price' => $group->avg('price'),
                    ];
                }),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    public function exportUnits(Request $request, DeveloperProject $project): JsonResponse
    {
        $this->authorize('view', $project);
        
        $request->validate([
            'format' => 'required|in:json,csv,xlsx',
            'status' => 'nullable|in:available,reserved,sold,under_construction,ready,maintenance',
            'unit_type' => 'nullable|in:apartment,villa,townhouse,penthouse,studio,duplex,office,retail,warehouse',
        ]);

        $query = $project->units();

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->unit_type) {
            $query->where('unit_type', $request->unit_type);
        }

        $units = $query->get();

        $filename = "project_{$project->id}_units_export_" . now()->format('Y-m-d_H-i-s');

        return response()->json([
            'success' => true,
            'data' => $units,
            'filename' => $filename,
            'message' => 'Units exported successfully'
        ]);
    }

    private function updateProjectStats(DeveloperProject $project): void
    {
        $units = $project->units;
        
        $project->update([
            'units_sold' => $units->where('status', 'sold')->count(),
            'total_revenue' => $units->where('status', 'sold')->sum('price'),
        ]);
    }
}

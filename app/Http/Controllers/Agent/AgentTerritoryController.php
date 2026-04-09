<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Http\Requests\Agent\StoreTerritoryRequest;
use App\Http\Requests\Agent\UpdateTerritoryRequest;
use App\Models\Agent;
use App\Models\AgentTerritory;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AgentTerritoryController extends Controller
{
    public function index(Request $request)
    {
        $agent = Auth::user()->agent;
        
        $territories = $agent->territories()
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%")
                    ->orWhere('state', 'like', "%{$search}%");
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->type, function ($query, $type) {
                $query->where('type', $type);
            })
            ->latest()
            ->paginate(20);

        return view('agent.territories.index', compact('territories'));
    }

    public function create()
    {
        return view('agent.territories.create');
    }

    public function store(StoreTerritoryRequest $request)
    {
        $agent = Auth::user()->agent;
        
        $territory = AgentTerritory::create([
            'agent_id' => $agent->id,
            'name' => $request->name,
            'description' => $request->description,
            'type' => $request->type,
            'status' => $request->status ?? 'active',
            'city' => $request->city,
            'state' => $request->state,
            'country' => $request->country,
            'postal_codes' => $request->postal_codes ?? [],
            'neighborhoods' => $request->neighborhoods ?? [],
            'boundaries' => [
                'north_lat' => $request->north_lat,
                'south_lat' => $request->south_lat,
                'east_lng' => $request->east_lng,
                'west_lng' => $request->west_lng,
            ],
            'coordinates' => $request->coordinates ?? [],
            'population_density' => $request->population_density,
            'average_income' => $request->average_income,
            'property_types' => $request->property_types ?? [],
            'price_range' => [
                'min' => $request->min_price,
                'max' => $request->max_price,
            ],
            'competition_level' => $request->competition_level,
            'market_potential' => $request->market_potential,
            'notes' => $request->notes,
            'assigned_date' => now(),
        ]);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'created_territory',
            'details' => "Created territory: {$territory->name}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('agent.territories.show', $territory)
            ->with('success', 'Territory created successfully.');
    }

    public function show(AgentTerritory $territory)
    {
        $this->authorize('view', $territory);
        
        $territory->load(['properties', 'leads', 'performance']);
        
        return view('agent.territories.show', compact('territory'));
    }

    public function edit(AgentTerritory $territory)
    {
        $this->authorize('update', $territory);
        
        return view('agent.territories.edit', compact('territory'));
    }

    public function update(UpdateTerritoryRequest $request, AgentTerritory $territory)
    {
        $this->authorize('update', $territory);
        
        $territory->update([
            'name' => $request->name,
            'description' => $request->description,
            'type' => $request->type,
            'status' => $request->status,
            'city' => $request->city,
            'state' => $request->state,
            'country' => $request->country,
            'postal_codes' => $request->postal_codes ?? [],
            'neighborhoods' => $request->neighborhoods ?? [],
            'boundaries' => [
                'north_lat' => $request->north_lat,
                'south_lat' => $request->south_lat,
                'east_lng' => $request->east_lng,
                'west_lng' => $request->west_lng,
            ],
            'coordinates' => $request->coordinates ?? [],
            'population_density' => $request->population_density,
            'average_income' => $request->average_income,
            'property_types' => $request->property_types ?? [],
            'price_range' => [
                'min' => $request->min_price,
                'max' => $request->max_price,
            ],
            'competition_level' => $request->competition_level,
            'market_potential' => $request->market_potential,
            'notes' => $request->notes,
        ]);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated_territory',
            'details' => "Updated territory: {$territory->name}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('agent.territories.show', $territory)
            ->with('success', 'Territory updated successfully.');
    }

    public function destroy(AgentTerritory $territory)
    {
        $this->authorize('delete', $territory);
        
        $territoryName = $territory->name;
        $territory->delete();

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'deleted_territory',
            'details' => "Deleted territory: {$territoryName}",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('agent.territories.index')
            ->with('success', 'Territory deleted successfully.');
    }

    public function updateStatus(Request $request, AgentTerritory $territory): JsonResponse
    {
        $this->authorize('update', $territory);
        
        $request->validate([
            'status' => 'required|in:active,inactive,suspended',
        ]);

        $territory->update([
            'status' => $request->status,
            'status_updated_at' => now(),
        ]);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated_territory_status',
            'details' => "Updated territory {$territory->name} status to {$request->status}",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'status' => $request->status,
            'message' => 'Territory status updated successfully'
        ]);
    }

    public function getTerritoryStats(AgentTerritory $territory): JsonResponse
    {
        $this->authorize('view', $territory);
        
        $stats = [
            'total_properties' => $territory->properties()->count(),
            'active_properties' => $territory->properties()->where('status', 'active')->count(),
            'sold_properties' => $territory->properties()->where('status', 'sold')->count(),
            'total_leads' => $territory->leads()->count(),
            'converted_leads' => $territory->leads()->where('status', 'converted')->count(),
            'conversion_rate' => 0,
            'average_property_price' => $territory->properties()
                ->with('price')
                ->get()
                ->avg(function ($property) {
                    return $property->price?->price ?? 0;
                }),
            'total_sales_value' => $territory->properties()->where('status', 'sold')
                ->with('price')
                ->get()
                ->sum(function ($property) {
                    return $property->price?->price ?? 0;
                }),
        ];

        if ($stats['total_leads'] > 0) {
            $stats['conversion_rate'] = round(($stats['converted_leads'] / $stats['total_leads']) * 100, 2);
        }

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    public function getTerritoryMap(AgentTerritory $territory): JsonResponse
    {
        $this->authorize('view', $territory);
        
        $mapData = [
            'name' => $territory->name,
            'boundaries' => $territory->boundaries,
            'coordinates' => $territory->coordinates,
            'center' => [
                'lat' => ($territory->boundaries['north_lat'] + $territory->boundaries['south_lat']) / 2,
                'lng' => ($territory->boundaries['east_lng'] + $territory->boundaries['west_lng']) / 2,
            ],
            'properties' => $territory->properties()
                ->with(['location', 'price'])
                ->get(['id', 'title', 'status', 'location_id', 'price_id'])
                ->map(function ($property) {
                    return [
                        'id' => $property->id,
                        'title' => $property->title,
                        'status' => $property->status,
                        'price' => $property->price?->price,
                        'lat' => $property->location?->latitude,
                        'lng' => $property->location?->longitude,
                    ];
                }),
            'leads' => $territory->leads()
                ->get(['id', 'name', 'status', 'latitude', 'longitude'])
                ->map(function ($lead) {
                    return [
                        'id' => $lead->id,
                        'name' => $lead->name,
                        'status' => $lead->status,
                        'lat' => $lead->latitude,
                        'lng' => $lead->longitude,
                    ];
                }),
        ];

        return response()->json([
            'success' => true,
            'map_data' => $mapData
        ]);
    }

    public function getAllTerritories(): JsonResponse
    {
        $agent = Auth::user()->agent;
        
        $territories = $agent->territories()
            ->where('status', 'active')
            ->get(['id', 'name', 'city', 'state', 'boundaries', 'coordinates']);

        return response()->json([
            'success' => true,
            'territories' => $territories
        ]);
    }

    public function getTerritoryPerformance(AgentTerritory $territory, Request $request): JsonResponse
    {
        $this->authorize('view', $territory);
        
        $period = $request->period ?? 'monthly';
        $months = $request->months ?? 12;
        
        $startDate = now()->subMonths($months);
        
        $performance = [
            'sales_trend' => $territory->properties()
                ->where('status', 'sold')
                ->where('updated_at', '>=', $startDate)
                ->selectRaw('DATE_FORMAT(updated_at, "%Y-%m") as month, COUNT(*) as count')
                ->groupBy('month')
                ->orderBy('month')
                ->get(),
            
            'lead_trend' => $territory->leads()
                ->where('created_at', '>=', $startDate)
                ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
                ->groupBy('month')
                ->orderBy('month')
                ->get(),
            
            'price_distribution' => $territory->properties()
                ->with('price')
                ->get()
                ->groupBy(function ($property) {
                    $price = $property->price?->price ?? 0;
                    if ($price < 100000) return '0-100k';
                    if ($price < 250000) return '100k-250k';
                    if ($price < 500000) return '250k-500k';
                    if ($price < 1000000) return '500k-1m';
                    return '1m+';
                })
                ->map(function ($group) {
                    return $group->count();
                }),
        ];

        return response()->json([
            'success' => true,
            'performance' => $performance
        ]);
    }

    public function exportTerritories(Request $request): JsonResponse
    {
        $request->validate([
            'format' => 'required|in:json,csv,xlsx',
            'status' => 'nullable|in:active,inactive,suspended',
        ]);

        $agent = Auth::user()->agent;
        
        $query = $agent->territories();

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $territories = $query->get();

        $filename = "agent_territories_export_" . now()->format('Y-m-d_H-i-s');

        return response()->json([
            'success' => true,
            'data' => $territories,
            'filename' => $filename,
            'message' => 'Territories exported successfully'
        ]);
    }
}

<?php

namespace App\Http\Controllers\Sustainability;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Sustainability\PropertySustainability;
use App\Models\Sustainability\SustainableMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class SustainableMaterialController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:manage_sustainability');
    }

    public function index()
    {
        $materials = SustainableMaterial::with(['propertySustainability.property'])
            ->when(Auth::user()->hasRole('agent'), function($query) {
                $query->whereHas('propertySustainability.property', function($q) {
                    $q->where('agent_id', Auth::id());
                });
            })
            ->latest('installation_date')
            ->paginate(15);

        $stats = [
            'total_materials' => SustainableMaterial::count(),
            'recycled_materials' => SustainableMaterial::where('material_type', 'recycled')->count(),
            'certified_materials' => SustainableMaterial::whereNotNull('certification')->count(),
            'local_materials' => SustainableMaterial::where('is_local', true)->count(),
        ];

        return view('sustainability.materials.index', compact('materials', 'stats'));
    }

    public function create()
    {
        $properties = PropertySustainability::with('property')
            ->when(Auth::user()->hasRole('agent'), function($query) {
                $query->whereHas('property', function($q) {
                    $q->where('agent_id', Auth::id());
                });
            })
            ->get();

        $materialTypes = [
            'recycled' => 'مواد معاد تدويرها',
            'renewable' => 'مواد متجددة',
            'natural' => 'مواد طبيعية',
            'low_impact' => 'مواد منخفضة التأثير',
            'certified' => 'مواد معتمدة',
            'local' => 'مواد محلية',
        ];

        $sustainabilityRatings = [
            'A+' => 'A+ - ممتاز',
            'A' => 'A - ممتاز',
            'B' => 'B - جيد جداً',
            'C' => 'C - جيد',
            'D' => 'D - مقبول',
        ];

        return view('sustainability.materials.create', compact('properties', 'materialTypes', 'sustainabilityRatings'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_sustainability_id' => 'required|exists:property_sustainability,id',
            'material_name' => 'required|string|max:255',
            'material_type' => 'required|string|in:recycled,renewable,natural,low_impact,certified,local',
            'category' => 'required|string|max:255',
            'manufacturer' => 'required|string|max:255',
            'supplier' => 'required|string|max:255',
            'quantity' => 'required|numeric|min:0',
            'unit' => 'required|string|in:kg,tons,meters,square_meters,cubic_meters,pieces',
            'cost_per_unit' => 'required|numeric|min:0',
            'total_cost' => 'required|numeric|min:0',
            'sustainability_rating' => 'required|string|in:A+,A,B,C,D',
            'recycled_content_percentage' => 'nullable|integer|min:0|max:100',
            'renewable_content_percentage' => 'nullable|integer|min:0|max:100',
            'local_content_percentage' => 'nullable|integer|min:0|max:100',
            'certification' => 'nullable|string|max:255',
            'certification_body' => 'nullable|string|max:255',
            'certification_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after:certification_date',
            'lifespan_years' => 'required|integer|min:1|max:100',
            'maintenance_requirements' => 'nullable|string',
            'installation_date' => 'required|date|before_or_equal:today',
            'installation_location' => 'required|string|max:255',
            'carbon_footprint' => 'nullable|numeric|min:0',
            'energy_consumption' => 'nullable|numeric|min:0',
            'water_usage' => 'nullable|numeric|min:0',
            'waste_generated' => 'nullable|numeric|min:0',
            'is_local' => 'required|boolean',
            'local_distance_km' => 'nullable|numeric|min:0',
            'end_of_life_plan' => 'nullable|string',
            'recyclable' => 'required|boolean',
            'biodegradable' => 'required|boolean',
            'health_safety_rating' => 'required|integer|min:1|max:10',
            'fire_resistance_rating' => 'required|integer|min:1|max:10',
            'durability_rating' => 'required|integer|min:1|max:10',
            'maintenance_cost_per_year' => 'nullable|numeric|min:0',
            'warranty_period' => 'nullable|integer|min:0',
            'technical_specifications' => 'nullable|array',
            'environmental_impact' => 'nullable|array',
            'documents' => 'nullable|array',
            'documents.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpg,jpeg,png|max:2048',
            'notes' => 'nullable|string',
        ]);

        // Handle document uploads
        $documents = [];
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $document) {
                $path = $document->store('materials/documents', 'public');
                $documents[] = $path;
            }
        }

        // Handle image uploads
        $images = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('materials/images', 'public');
                $images[] = $path;
            }
        }

        $material = SustainableMaterial::create([
            'property_sustainability_id' => $validated['property_sustainability_id'],
            'material_name' => $validated['material_name'],
            'material_type' => $validated['material_type'],
            'category' => $validated['category'],
            'manufacturer' => $validated['manufacturer'],
            'supplier' => $validated['supplier'],
            'quantity' => $validated['quantity'],
            'unit' => $validated['unit'],
            'cost_per_unit' => $validated['cost_per_unit'],
            'total_cost' => $validated['total_cost'],
            'sustainability_rating' => $validated['sustainability_rating'],
            'recycled_content_percentage' => $validated['recycled_content_percentage'] ?? 0,
            'renewable_content_percentage' => $validated['renewable_content_percentage'] ?? 0,
            'local_content_percentage' => $validated['local_content_percentage'] ?? 0,
            'certification' => $validated['certification'],
            'certification_body' => $validated['certification_body'],
            'certification_date' => $validated['certification_date'],
            'expiry_date' => $validated['expiry_date'],
            'lifespan_years' => $validated['lifespan_years'],
            'maintenance_requirements' => $validated['maintenance_requirements'],
            'installation_date' => $validated['installation_date'],
            'installation_location' => $validated['installation_location'],
            'carbon_footprint' => $validated['carbon_footprint'] ?? 0,
            'energy_consumption' => $validated['energy_consumption'] ?? 0,
            'water_usage' => $validated['water_usage'] ?? 0,
            'waste_generated' => $validated['waste_generated'] ?? 0,
            'is_local' => $validated['is_local'],
            'local_distance_km' => $validated['local_distance_km'] ?? 0,
            'end_of_life_plan' => $validated['end_of_life_plan'],
            'recyclable' => $validated['recyclable'],
            'biodegradable' => $validated['biodegradable'],
            'health_safety_rating' => $validated['health_safety_rating'],
            'fire_resistance_rating' => $validated['fire_resistance_rating'],
            'durability_rating' => $validated['durability_rating'],
            'maintenance_cost_per_year' => $validated['maintenance_cost_per_year'] ?? 0,
            'warranty_period' => $validated['warranty_period'] ?? 0,
            'technical_specifications' => $validated['technical_specifications'] ?? [],
            'environmental_impact' => $validated['environmental_impact'] ?? [],
            'documents' => $documents,
            'images' => $images,
            'created_by' => Auth::id(),
            'notes' => $validated['notes'] ?? null,
        ]);

        // Update property sustainability materials percentage
        $this->updatePropertyMaterialsPercentage($validated['property_sustainability_id']);

        return redirect()
            ->route('sustainable-materials.show', $material)
            ->with('success', 'تم إضافة المادة المستدامة بنجاح');
    }

    public function show(SustainableMaterial $material)
    {
        $material->load(['propertySustainability.property']);
        
        // Calculate material metrics
        $metrics = $this->calculateMaterialMetrics($material);
        
        // Get similar materials for comparison
        $similarMaterials = SustainableMaterial::where('category', $material->category)
            ->where('id', '!=', $material->id)
            ->take(5)
            ->get();

        return view('sustainability.materials.show', compact('material', 'metrics', 'similarMaterials'));
    }

    public function edit(SustainableMaterial $material)
    {
        $material->load('propertySustainability.property');
        
        $materialTypes = [
            'recycled' => 'مواد معاد تدويرها',
            'renewable' => 'مواد متجددة',
            'natural' => 'مواد طبيعية',
            'low_impact' => 'مواد منخفضة التأثير',
            'certified' => 'مواد معتمدة',
            'local' => 'مواد محلية',
        ];

        $sustainabilityRatings = [
            'A+' => 'A+ - ممتاز',
            'A' => 'A - ممتاز',
            'B' => 'B - جيد جداً',
            'C' => 'C - جيد',
            'D' => 'D - مقبول',
        ];

        return view('sustainability.materials.edit', compact('material', 'materialTypes', 'sustainabilityRatings'));
    }

    public function update(Request $request, SustainableMaterial $material)
    {
        $validated = $request->validate([
            'material_name' => 'required|string|max:255',
            'material_type' => 'required|string|in:recycled,renewable,natural,low_impact,certified,local',
            'category' => 'required|string|max:255',
            'quantity' => 'required|numeric|min:0',
            'cost_per_unit' => 'required|numeric|min:0',
            'total_cost' => 'required|numeric|min:0',
            'sustainability_rating' => 'required|string|in:A+,A,B,C,D',
            'recycled_content_percentage' => 'nullable|integer|min:0|max:100',
            'renewable_content_percentage' => 'nullable|integer|min:0|max:100',
            'local_content_percentage' => 'nullable|integer|min:0|max:100',
            'lifespan_years' => 'required|integer|min:1|max:100',
            'maintenance_cost_per_year' => 'nullable|numeric|min:0',
            'warranty_period' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        $material->update($validated);

        // Update property sustainability materials percentage
        $this->updatePropertyMaterialsPercentage($material->property_sustainability_id);

        return redirect()
            ->route('sustainable-materials.show', $material)
            ->with('success', 'تم تحديث المادة المستدامة بنجاح');
    }

    public function destroy(SustainableMaterial $material)
    {
        // Delete associated documents and images
        if ($material->documents) {
            foreach ($material->documents as $document) {
                Storage::disk('public')->delete($document);
            }
        }

        if ($material->images) {
            foreach ($material->images as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        $propertySustainabilityId = $material->property_sustainability_id;
        $material->delete();

        // Update property sustainability materials percentage
        $this->updatePropertyMaterialsPercentage($propertySustainabilityId);

        return redirect()
            ->route('sustainable-materials.index')
            ->with('success', 'تم حذف المادة المستدامة بنجاح');
    }

    public function analytics()
    {
        $materialTypeDistribution = SustainableMaterial::selectRaw('material_type, COUNT(*) as count, SUM(total_cost) as total_cost')
            ->groupBy('material_type')
            ->get();

        $sustainabilityRatingDistribution = SustainableMaterial::selectRaw('sustainability_rating, COUNT(*) as count')
            ->groupBy('sustainability_rating')
            ->get();

        $costAnalysis = SustainableMaterial::selectRaw('
            AVG(cost_per_unit) as avg_cost_per_unit,
            AVG(total_cost) as avg_total_cost,
            SUM(total_cost) as total_investment,
            COUNT(*) as total_materials
        ')->first();

        $environmentalImpact = SustainableMaterial::selectRaw('
            AVG(carbon_footprint) as avg_carbon_footprint,
            AVG(energy_consumption) as avg_energy_consumption,
            AVG(water_usage) as avg_water_usage,
            SUM(waste_generated) as total_waste
        ')->first();

        $topSuppliers = SustainableMaterial::selectRaw('supplier, COUNT(*) as count, SUM(total_cost) as total_cost')
            ->groupBy('supplier')
            ->orderBy('total_cost', 'desc')
            ->take(10)
            ->get();

        return view('sustainability.materials.analytics', compact(
            'materialTypeDistribution',
            'sustainabilityRatingDistribution',
            'costAnalysis',
            'environmentalImpact',
            'topSuppliers'
        ));
    }

    public function compare(Request $request)
    {
        $materialIds = $request->input('materials', []);
        
        if (count($materialIds) < 2) {
            return back()->with('error', 'يرجى اختيار مادتين على الأقل للمقارنة');
        }

        $materials = SustainableMaterial::with(['propertySustainability.property'])
            ->whereIn('id', $materialIds)
            ->get();

        $comparisonData = $materials->map(function($material) {
            return [
                'material' => $material,
                'sustainability_score' => $this->calculateSustainabilityScore($material),
                'cost_efficiency' => $this->calculateCostEfficiency($material),
                'environmental_impact' => $this->calculateEnvironmentalImpact($material),
            ];
        });

        return view('sustainability.materials.compare', compact('comparisonData'));
    }

    public function lifecycle(SustainableMaterial $material)
    {
        $lifecycleData = $this->generateLifecycleData($material);
        
        return view('sustainability.materials.lifecycle', compact('material', 'lifecycleData'));
    }

    private function updatePropertyMaterialsPercentage($propertySustainabilityId)
    {
        $propertySustainability = PropertySustainability::find($propertySustainabilityId);
        $materials = $propertySustainability->sustainableMaterials;
        
        if ($materials->isEmpty()) {
            $propertySustainability->update(['sustainable_materials_percentage' => 0]);
            return;
        }

        // Calculate sustainable materials percentage based on ratings and types
        $totalScore = 0;
        $maxScore = 0;

        foreach ($materials as $material) {
            $materialScore = 0;
            
            // Base score from sustainability rating
            $ratingScores = ['A+' => 100, 'A' => 90, 'B' => 80, 'C' => 70, 'D' => 60];
            $materialScore += $ratingScores[$material->sustainability_rating] ?? 60;
            
            // Bonus points for special features
            if ($material->recyclable) $materialScore += 10;
            if ($material->biodegradable) $materialScore += 10;
            if ($material->is_local) $materialScore += 5;
            if ($material->certification) $materialScore += 15;
            
            $totalScore += $materialScore;
            $maxScore += 115; // Maximum possible score per material
        }

        $sustainablePercentage = ($totalScore / $maxScore) * 100;
        $propertySustainability->update(['sustainable_materials_percentage' => round($sustainablePercentage, 1)]);
    }

    private function calculateMaterialMetrics($material)
    {
        $metrics = [];
        
        // Calculate sustainability score
        $metrics['sustainability_score'] = $this->calculateSustainabilityScore($material);
        
        // Calculate cost efficiency
        $metrics['cost_efficiency'] = $this->calculateCostEfficiency($material);
        
        // Calculate environmental impact score
        $metrics['environmental_impact_score'] = $this->calculateEnvironmentalImpact($material);
        
        // Calculate lifecycle cost
        $metrics['lifecycle_cost'] = $material->total_cost + 
                                     ($material->maintenance_cost_per_year * $material->lifespan_years);
        
        return $metrics;
    }

    private function calculateSustainabilityScore($material)
    {
        $score = 0;
        
        // Base score from rating
        $ratingScores = ['A+' => 40, 'A' => 35, 'B' => 30, 'C' => 25, 'D' => 20];
        $score += $ratingScores[$material->sustainability_rating] ?? 20;
        
        // Content scores
        $score += ($material->recycled_content_percentage / 100) * 15;
        $score += ($material->renewable_content_percentage / 100) * 15;
        $score += ($material->local_content_percentage / 100) * 10;
        
        // Feature scores
        if ($material->recyclable) $score += 10;
        if ($material->biodegradable) $score += 10;
        if ($material->certification) $score += 10;
        
        return min(100, $score);
    }

    private function calculateCostEfficiency($material)
    {
        // Lower cost per unit with higher sustainability rating = better efficiency
        $ratingMultipliers = ['A+' => 1.5, 'A' => 1.3, 'B' => 1.1, 'C' => 0.9, 'D' => 0.7];
        $multiplier = $ratingMultipliers[$material->sustainability_rating] ?? 0.7;
        
        return ($material->cost_per_unit / $multiplier) * 100; // Normalized score
    }

    private function calculateEnvironmentalImpact($material)
    {
        // Lower impact = higher score
        $impactScore = 100;
        
        // Deduct points for environmental factors
        if ($material->carbon_footprint > 0) {
            $impactScore -= min(30, $material->carbon_footprint / 10);
        }
        
        if ($material->energy_consumption > 0) {
            $impactScore -= min(20, $material->energy_consumption / 100);
        }
        
        if ($material->water_usage > 0) {
            $impactScore -= min(20, $material->water_usage / 1000);
        }
        
        if ($material->waste_generated > 0) {
            $impactScore -= min(30, $material->waste_generated / 10);
        }
        
        return max(0, $impactScore);
    }

    private function generateLifecycleData($material)
    {
        $lifecycle = [
            'production' => [
                'phase' => 'الإنتاج',
                'duration' => '1-3 أشهر',
                'impact' => $material->carbon_footprint * 0.3,
                'cost' => $material->total_cost * 0.7,
            ],
            'transportation' => [
                'phase' => 'النقل',
                'duration' => '1-2 أسابيع',
                'impact' => $material->local_distance_km * 0.1,
                'cost' => $material->total_cost * 0.1,
            ],
            'installation' => [
                'phase' => 'التركيب',
                'duration' => '1-4 أسابيع',
                'impact' => $material->carbon_footprint * 0.1,
                'cost' => $material->total_cost * 0.2,
            ],
            'operation' => [
                'phase' => 'التشغيل',
                'duration' => $material->lifespan_years . ' سنوات',
                'impact' => $material->energy_consumption * $material->lifespan_years,
                'cost' => $material->maintenance_cost_per_year * $material->lifespan_years,
            ],
            'end_of_life' => [
                'phase' => 'نهاية العمر',
                'duration' => '1-2 أشهر',
                'impact' => $material->recyclable ? -$material->carbon_footprint * 0.2 : $material->carbon_footprint * 0.3,
                'cost' => $material->recyclable ? -$material->total_cost * 0.1 : $material->total_cost * 0.15,
            ],
        ];

        return $lifecycle;
    }
}

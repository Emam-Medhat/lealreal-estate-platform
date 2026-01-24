<?php

namespace App\Http\Controllers;

use App\Models\SustainableMaterial;
use App\Models\SmartProperty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SustainableMaterialController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_materials' => SustainableMaterial::count(),
            'certified_materials' => SustainableMaterial::where('certification_status', 'certified')->count(),
            'average_sustainability_score' => SustainableMaterial::avg('sustainability_score'),
            'total_recycled_content' => SustainableMaterial::avg('recycled_content'),
            'materials_by_category' => $this->getMaterialsByCategory(),
            'certification_trends' => $this->getCertificationTrends(),
        ];

        $recentMaterials = SustainableMaterial::with(['property'])
            ->latest()
            ->take(10)
            ->get();

        $topPerformers = $this->getTopPerformers();
        $ecoFriendlyMaterials = $this->getEcoFriendlyMaterials();

        return view('sustainability.sustainable-materials-dashboard', compact(
            'stats', 
            'recentMaterials', 
            'topPerformers', 
            'ecoFriendlyMaterials'
        ));
    }

    public function index(Request $request)
    {
        $query = SustainableMaterial::with(['property']);

        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        if ($request->filled('material_category')) {
            $query->where('material_category', $request->material_category);
        }

        if ($request->filled('certification_status')) {
            $query->where('certification_status', $request->certification_status);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('sustainability_score_min')) {
            $query->where('sustainability_score', '>=', $request->sustainability_score_min);
        }

        if ($request->filled('sustainability_score_max')) {
            $query->where('sustainability_score', '<=', $request->sustainability_score_max);
        }

        if ($request->filled('is_renewable')) {
            $query->where('is_renewable', $request->boolean('is_renewable'));
        }

        if ($request->filled('is_locally_sourced')) {
            $query->where('is_locally_sourced', $request->boolean('is_locally_sourced'));
        }

        $materials = $query->latest()->paginate(12);

        $materialCategories = ['insulation', 'flooring', 'roofing', 'walls', 'windows', 'doors', 'paints', 'adhesives', 'structural', 'finishing'];
        $certificationStatuses = ['none', 'pending', 'certified', 'expired'];
        $statuses = ['planned', 'installed', 'in_use', 'replaced', 'disposed'];

        return view('sustainability.sustainable-materials-index', compact(
            'materials', 
            'materialCategories', 
            'certificationStatuses', 
            'statuses'
        ));
    }

    public function create()
    {
        $properties = SmartProperty::all();

        return view('sustainability.sustainable-materials-create', compact(
            'properties'
        ));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $materialData = $request->validate([
                'property_id' => 'required|exists:smart_properties,id',
                'material_name' => 'required|string|max:255',
                'material_category' => 'required|string|max:255',
                'supplier' => 'nullable|string|max:255',
                'sustainability_score' => 'required|numeric|min:0|max:100',
                'recycled_content' => 'required|numeric|min:0|max:100',
                'certification_status' => 'required|in:none,pending,certified,expired',
                'certification_body' => 'nullable|string|max:255',
                'environmental_impact' => 'nullable|array',
                'material_properties' => 'nullable|array',
                'lifespan_years' => 'nullable|numeric|min:0',
                'is_renewable' => 'required|boolean',
                'is_locally_sourced' => 'required|boolean',
                'carbon_footprint_data' => 'nullable|array',
                'installation_date' => 'nullable|date',
                'status' => 'required|in:planned,installed,in_use,replaced,disposed',
            ]);

            $materialData['created_by'] = auth()->id();
            $materialData['environmental_impact'] = $this->generateEnvironmentalImpact($request);
            $materialData['material_properties'] = $this->generateMaterialProperties($request);
            $materialData['carbon_footprint_data'] = $this->generateCarbonFootprintData($request);

            $material = SustainableMaterial::create($materialData);

            DB::commit();

            return redirect()
                ->route('sustainable-material.show', $material)
                ->with('success', 'تم إضافة المادة المستدامة بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إضافة المادة: ' . $e->getMessage());
        }
    }

    public function show(SustainableMaterial $material)
    {
        $material->load(['property']);
        $materialDetails = $this->getMaterialDetails($material);
        $sustainabilityAnalysis = $this->getSustainabilityAnalysis($material);
        $environmentalImpact = $this->getEnvironmentalImpactAnalysis($material);

        return view('sustainability.sustainable-materials-show', compact(
            'material', 
            'materialDetails', 
            'sustainabilityAnalysis', 
            'environmentalImpact'
        ));
    }

    public function edit(SustainableMaterial $material)
    {
        $properties = SmartProperty::all();

        return view('sustainability.sustainable-materials-edit', compact(
            'material', 
            'properties'
        ));
    }

    public function update(Request $request, SustainableMaterial $material)
    {
        DB::beginTransaction();
        try {
            $materialData = $request->validate([
                'material_name' => 'required|string|max:255',
                'material_category' => 'required|string|max:255',
                'supplier' => 'nullable|string|max:255',
                'sustainability_score' => 'required|numeric|min:0|max:100',
                'recycled_content' => 'required|numeric|min:0|max:100',
                'certification_status' => 'required|in:none,pending,certified,expired',
                'certification_body' => 'nullable|string|max:255',
                'environmental_impact' => 'nullable|array',
                'material_properties' => 'nullable|array',
                'lifespan_years' => 'nullable|numeric|min:0',
                'is_renewable' => 'required|boolean',
                'is_locally_sourced' => 'required|boolean',
                'carbon_footprint_data' => 'nullable|array',
                'installation_date' => 'nullable|date',
                'status' => 'required|in:planned,installed,in_use,replaced,disposed',
            ]);

            $materialData['updated_by'] = auth()->id();
            $materialData['environmental_impact'] = $this->generateEnvironmentalImpact($request);
            $materialData['material_properties'] = $this->generateMaterialProperties($request);
            $materialData['carbon_footprint_data'] = $this->generateCarbonFootprintData($request);

            $material->update($materialData);

            DB::commit();

            return redirect()
                ->route('sustainable-material.show', $material)
                ->with('success', 'تم تحديث المادة المستدامة بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث المادة: ' . $e->getMessage());
        }
    }

    public function destroy(SustainableMaterial $material)
    {
        try {
            $material->delete();

            return redirect()
                ->route('sustainable-material.index')
                ->with('success', 'تم حذف المادة المستدامة بنجاح');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'حدث خطأ أثناء حذف المادة: ' . $e->getMessage());
        }
    }

    public function assessSustainability(Request $request)
    {
        $materialData = $request->input('material_data', []);
        $assessment = $this->performSustainabilityAssessment($materialData);

        return response()->json([
            'success' => true,
            'assessment' => $assessment
        ]);
    }

    public function calculateCarbonFootprint(SustainableMaterial $material)
    {
        $footprint = $this->calculateMaterialCarbonFootprint($material);

        return response()->json([
            'success' => true,
            'carbon_footprint' => $footprint
        ]);
    }

    public function getAlternatives(SustainableMaterial $material)
    {
        $alternatives = $this->findSustainableAlternatives($material);

        return response()->json([
            'success' => true,
            'alternatives' => $alternatives
        ]);
    }

    public function generateReport(SustainableMaterial $material)
    {
        try {
            $reportData = $this->generateMaterialReport($material);
            
            return response()->json([
                'success' => true,
                'report' => $reportData
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function generateEnvironmentalImpact($request)
    {
        return [
            'embodied_energy' => $request->input('embodied_energy', 0),
            'embodied_carbon' => $request->input('embodied_carbon', 0),
            'water_usage' => $request->input('water_usage', 0),
            'waste_generation' => $request->input('waste_generation', 0),
            'toxicity_level' => $request->input('toxicity_level', 'low'),
            'air_pollution' => $request->input('air_pollution', 0),
            'water_pollution' => $request->input('water_pollution', 0),
            'land_use_impact' => $request->input('land_use_impact', 0),
            'biodiversity_impact' => $request->input('biodiversity_impact', 0),
        ];
    }

    private function generateMaterialProperties($request)
    {
        return [
            'thermal_conductivity' => $request->input('thermal_conductivity', 0),
            'durability_rating' => $request->input('durability_rating', 0),
            'maintenance_requirement' => $request->input('maintenance_requirement', 'low'),
            'fire_resistance' => $request->input('fire_resistance', 0),
            'moisture_resistance' => $request->input('moisture_resistance', 0),
            'uv_resistance' => $request->input('uv_resistance', 0),
            'recyclability' => $request->input('recyclability', 0),
            'biodegradability' => $request->input('biodegradability', 0),
            'health_safety_rating' => $request->input('health_safety_rating', 0),
        ];
    }

    private function generateCarbonFootprintData($request)
    {
        return [
            'total_carbon' => $request->input('total_carbon', 0),
            'production_carbon' => $request->input('production_carbon', 0),
            'transport_carbon' => $request->input('transport_carbon', 0),
            'installation_carbon' => $request->input('installation_carbon', 0),
            'maintenance_carbon' => $request->input('maintenance_carbon', 0),
            'disposal_carbon' => $request->input('disposal_carbon', 0),
            'carbon_sequestration' => $request->input('carbon_sequestration', 0),
            'net_carbon' => $request->input('net_carbon', 0),
        ];
    }

    private function getMaterialsByCategory()
    {
        return SustainableMaterial::select('material_category', DB::raw('COUNT(*) as count, AVG(sustainability_score) as avg_score'))
            ->groupBy('material_category')
            ->get();
    }

    private function getCertificationTrends()
    {
        return SustainableMaterial::selectRaw('MONTH(created_at) as month, COUNT(*) as certified_count')
            ->where('certification_status', 'certified')
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }

    private function getTopPerformers()
    {
        return SustainableMaterial::with(['property'])
            ->select('material_category', DB::raw('AVG(sustainability_score) as avg_score'))
            ->where('sustainability_score', '>', 70)
            ->groupBy('material_category')
            ->orderBy('avg_score', 'desc')
            ->take(5)
            ->get();
    }

    private function getEcoFriendlyMaterials()
    {
        return SustainableMaterial::with(['property'])
            ->where('sustainability_score', '>=', 70)
            ->where('recycled_content', '>=', 50)
            ->where('is_renewable', true)
            ->orderBy('sustainability_score', 'desc')
            ->take(10)
            ->get();
    }

    private function getMaterialDetails($material)
    {
        return [
            'is_eco_friendly' => $material->isEcoFriendly(),
            'is_locally_sourced' => $material->isLocallySourced(),
            'is_renewable' => $material->isRenewable(),
            'material_grade' => $material->getMaterialGrade(),
            'carbon_footprint' => $material->getCarbonFootprint(),
            'age_in_use' => $material->installation_date ? 
                $material->installation_date->diffInDays(now()) : 0,
        ];
    }

    private function getSustainabilityAnalysis($material)
    {
        return [
            'sustainability_score' => $material->sustainability_score,
            'recycled_content' => $material->recycled_content,
            'certification_status' => $material->certification_status,
            'lifespan_years' => $material->lifespan_years,
            'renewable_status' => $material->is_renewable,
            'local_sourcing' => $material->is_locally_sourced,
            'overall_rating' => $this->calculateOverallRating($material),
        ];
    }

    private function getEnvironmentalImpactAnalysis($material)
    {
        $impact = $material->environmental_impact ?? [];
        
        return [
            'embodied_energy' => $impact['embodied_energy'] ?? 0,
            'embodied_carbon' => $impact['embodied_carbon'] ?? 0,
            'water_usage' => $impact['water_usage'] ?? 0,
            'waste_generation' => $impact['waste_generation'] ?? 0,
            'toxicity_level' => $impact['toxicity_level'] ?? 'low',
            'overall_impact_score' => $this->calculateImpactScore($impact),
            'impact_grade' => $this->getImpactGrade($impact),
        ];
    }

    private function calculateOverallRating($material)
    {
        $score = $material->sustainability_score;
        $recycledBonus = $material->recycled_content * 0.3;
        $renewableBonus = $material->is_renewable ? 10 : 0;
        $localBonus = $material->is_locally_sourced ? 5 : 0;
        
        return min(100, $score + $recycledBonus + $renewableBonus + $localBonus);
    }

    private function calculateImpactScore($impact)
    {
        $energyWeight = 0.3;
        $carbonWeight = 0.3;
        $waterWeight = 0.2;
        $wasteWeight = 0.2;
        
        $energyScore = ($impact['embodied_energy'] ?? 0) / 100; // Normalize
        $carbonScore = ($impact['embodied_carbon'] ?? 0) / 100;
        $waterScore = ($impact['water_usage'] ?? 0) / 100;
        $wasteScore = ($impact['waste_generation'] ?? 0) / 100;
        
        return ($energyScore * $energyWeight) + ($carbonScore * $carbonWeight) + 
               ($waterScore * $waterWeight) + ($wasteScore * $wasteWeight);
    }

    private function getImpactGrade($impact)
    {
        $score = $this->calculateImpactScore($impact);
        
        if ($score <= 2) return 'A';
        if ($score <= 4) return 'B';
        if ($score <= 6) return 'C';
        if ($score <= 8) return 'D';
        return 'F';
    }

    private function performSustainabilityAssessment($materialData)
    {
        $baseScore = 50;
        
        // Calculate sustainability score based on various factors
        if ($materialData['recycled_content'] ?? 0 > 50) $baseScore += 15;
        if ($materialData['is_renewable'] ?? false) $baseScore += 10;
        if ($materialData['is_locally_sourced'] ?? false) $baseScore += 10;
        if ($materialData['certification_status'] === 'certified') $baseScore += 10;
        if ($materialData['low_toxicity'] ?? false) $baseScore += 5;

        return [
            'sustainability_score' => min(100, $baseScore),
            'assessment_date' => now()->toDateString(),
            'recommendations' => $this->generateAssessmentRecommendations($baseScore),
        ];
    }

    private function generateAssessmentRecommendations($score)
    {
        $recommendations = [];
        
        if ($score < 70) {
            $recommendations[] = 'Increase recycled content';
            $recommendations[] = 'Choose renewable materials';
            $recommendations[] = 'Source materials locally';
        }
        
        if ($score < 85) {
            $recommendations[] = 'Obtain green certifications';
            $recommendations[] = 'Reduce toxicity levels';
            $recommendations[] = 'Improve material durability';
        }
        
        return $recommendations;
    }

    private function calculateMaterialCarbonFootprint($material)
    {
        $footprint = $material->carbon_footprint_data ?? [];
        
        return [
            'total_carbon' => $material->getCarbonFootprint(),
            'production_phase' => $footprint['production_carbon'] ?? 0,
            'transport_phase' => $footprint['transport_carbon'] ?? 0,
            'installation_phase' => $footprint['installation_carbon'] ?? 0,
            'maintenance_phase' => $footprint['maintenance_carbon'] ?? 0,
            'disposal_phase' => $footprint['disposal_carbon'] ?? 0,
            'carbon_sequestration' => $footprint['carbon_sequestration'] ?? 0,
            'net_carbon' => $footprint['net_carbon'] ?? 0,
        ];
    }

    private function findSustainableAlternatives($material)
    {
        return SustainableMaterial::where('material_category', $material->material_category)
            ->where('sustainability_score', '>', $material->sustainability_score)
            ->where('id', '!=', $material->id)
            ->orderBy('sustainability_score', 'desc')
            ->take(5)
            ->get();
    }

    private function generateMaterialReport($material)
    {
        return [
            'report_id' => uniqid('material_report_'),
            'material_name' => $material->material_name,
            'material_category' => $material->material_category,
            'property_name' => $material->property->property_name,
            'sustainability_score' => $material->sustainability_score,
            'recycled_content' => $material->recycled_content,
            'certification_status' => $material->certification_status,
            'environmental_impact' => $material->environmental_impact,
            'material_properties' => $material->material_properties,
            'carbon_footprint' => $material->getCarbonFootprint(),
            'generated_at' => now()->toDateTimeString(),
        ];
    }
}

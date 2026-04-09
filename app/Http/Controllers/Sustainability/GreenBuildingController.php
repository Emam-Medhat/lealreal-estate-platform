<?php

namespace App\Http\Controllers\Sustainability;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Sustainability\PropertySustainability;
use App\Models\Sustainability\GreenBuilding;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class GreenBuildingController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:manage_sustainability');
    }

    public function index()
    {
        $greenBuildings = GreenBuilding::with(['propertySustainability.property'])
            ->when(Auth::user()->hasRole('agent'), function($query) {
                $query->whereHas('propertySustainability.property', function($q) {
                    $q->where('agent_id', Auth::id());
                });
            })
            ->latest('certification_date')
            ->paginate(15);

        $stats = [
            'total_buildings' => GreenBuilding::count(),
            'certified_buildings' => GreenBuilding::where('certification_status', 'certified')->count(),
            'platinum_buildings' => GreenBuilding::where('certification_level', 'platinum')->count(),
            'gold_buildings' => GreenBuilding::where('certification_level', 'gold')->count(),
        ];

        return view('sustainability.green-buildings.index', compact('greenBuildings', 'stats'));
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

        $certificationStandards = [
            'leed' => 'LEED (Leadership in Energy and Environmental Design)',
            'breeam' => 'BREEAM (Building Research Establishment Environmental Assessment Method)',
            'estidama' => 'Estidama (Abu Dhabi Sustainability Rating System)',
            'green_globes' => 'Green Globes',
            'energy_star' => 'ENERGY STAR',
            'passive_house' => 'Passive House',
            'living_building' => 'Living Building Challenge',
            'well' => 'WELL Building Standard',
            'local_green' => 'شهادة خضراء محلية',
        ];

        $certificationLevels = [
            'platinum' => 'Platinum - البلاتيني',
            'gold' => 'Gold - الذهبي',
            'silver' => 'Silver - الفضي',
            'bronze' => 'Bronze - البرونزي',
            'certified' => 'Certified - معتمد',
        ];

        return view('sustainability.green-buildings.create', compact('properties', 'certificationStandards', 'certificationLevels'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_sustainability_id' => 'required|exists:property_sustainability,id',
            'building_name' => 'required|string|max:255',
            'certification_standard' => 'required|string|in:leed,breeam,estidama,green_globes,energy_star,passive_house,living_building,well,local_green',
            'certification_level' => 'required|string|in:platinum,gold,silver,bronze,certified',
            'certification_number' => 'required|string|max:255',
            'certification_date' => 'required|date|before_or_equal:today',
            'expiry_date' => 'nullable|date|after:certification_date',
            'certification_body' => 'required|string|max:255',
            'assessor_name' => 'required|string|max:255',
            'assessment_date' => 'required|date|before_or_equal:today',
            'total_score' => 'required|numeric|min:0|max:100',
            'energy_score' => 'required|numeric|min:0|max:100',
            'water_score' => 'required|numeric|min:0|max:100',
            'materials_score' => 'required|numeric|min:0|max:100',
            'indoor_environment_score' => 'required|numeric|min:0|max:100',
            'site_score' => 'required|numeric|min:0|max:100',
            'innovation_score' => 'required|numeric|min:0|max:100',
            'regional_priority_score' => 'nullable|numeric|min:0|max:100',
            'building_type' => 'required|string|max:255',
            'building_size' => 'required|numeric|min:0',
            'size_unit' => 'required|string|in:sq_m,sq_ft',
            'construction_year' => 'required|integer|min:1900|max:' . date('Y'),
            'renovation_year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'occupancy_type' => 'required|string|max:255',
            'occupancy_rate' => 'required|numeric|min:0|max:100',
            'green_features' => 'required|array',
            'green_features.*' => 'string',
            'sustainable_materials_used' => 'required|array',
            'sustainable_materials_used.*' => 'string',
            'energy_efficiency_measures' => 'required|array',
            'energy_efficiency_measures.*' => 'string',
            'water_conservation_measures' => 'required|array',
            'water_conservation_measures.*' => 'string',
            'waste_management_systems' => 'required|array',
            'waste_management_systems.*' => 'string',
            'indoor_air_quality_measures' => 'required|array',
            'indoor_air_quality_measures.*' => 'string',
            'site_sustainability_features' => 'required|array',
            'site_sustainability_features.*' => 'string',
            'innovation_features' => 'required|array',
            'innovation_features.*' => 'string',
            'performance_monitoring' => 'required|boolean',
            'monitoring_systems' => 'nullable|array',
            'monitoring_systems.*' => 'string',
            'commissioning_date' => 'nullable|date',
            'commissioning_type' => 'nullable|string|in:fundamental,enhanced,retrocommissioning',
            'energy_modeling_performed' => 'required|boolean',
            'energy_modeling_results' => 'nullable|array',
            'daylighting_analysis_performed' => 'required|boolean',
            'daylighting_results' => 'nullable|array',
            'thermal_comfort_analysis_performed' => 'required|boolean',
            'thermal_comfort_results' => 'nullable|array',
            'acoustic_analysis_performed' => 'required|boolean',
            'acoustic_results' => 'nullable|array',
            'life_cycle_assessment_performed' => 'required|boolean',
            'life_cycle_results' => 'nullable|array',
            'cost_benefit_analysis_performed' => 'required|boolean',
            'cost_benefit_results' => 'nullable|array',
            'maintenance_plan' => 'required|boolean',
            'maintenance_plan_document' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'user_training_provided' => 'required|boolean',
            'training_materials' => 'nullable|array',
            'training_materials.*' => 'file|mimes:pdf,doc,docx,ppt,pptx|max:2048',
            'certification_documents' => 'required|array',
            'certification_documents.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
            'building_photos' => 'nullable|array',
            'building_photos.*' => 'image|mimes:jpg,jpeg,png|max:2048',
            'verification_url' => 'nullable|url',
            'annual_energy_consumption' => 'nullable|numeric|min:0',
            'annual_water_consumption' => 'nullable|numeric|min:0',
            'annual_waste_generated' => 'nullable|numeric|min:0',
            'annual_carbon_emissions' => 'nullable|numeric|min:0',
            'renewable_energy_percentage' => 'nullable|numeric|min:0|max:100',
            'recycling_rate' => 'nullable|numeric|min:0|max:100',
            'green_space_ratio' => 'nullable|numeric|min:0|max:1',
            'stormwater_management' => 'required|boolean',
            'heat_island_reduction' => 'required|boolean',
            'light_pollution_reduction' => 'required|boolean',
            'biodiversity_protection' => 'required|boolean',
            'certification_status' => 'required|string|in:pending,active,suspended,expired,revoked',
            'next_assessment_date' => 'nullable|date|after:today',
            'notes' => 'nullable|string',
        ]);

        // Handle file uploads
        $documents = [];
        if ($request->hasFile('certification_documents')) {
            foreach ($request->file('certification_documents') as $document) {
                $path = $document->store('green-buildings/documents', 'public');
                $documents[] = $path;
            }
        }

        $photos = [];
        if ($request->hasFile('building_photos')) {
            foreach ($request->file('building_photos') as $photo) {
                $path = $photo->store('green-buildings/photos', 'public');
                $photos[] = $path;
            }
        }

        $trainingMaterials = [];
        if ($request->hasFile('training_materials')) {
            foreach ($request->file('training_materials') as $material) {
                $path = $material->store('green-buildings/training', 'public');
                $trainingMaterials[] = $path;
            }
        }

        $maintenancePlan = null;
        if ($request->hasFile('maintenance_plan_document')) {
            $maintenancePlan = $request->file('maintenance_plan_document')->store('green-buildings/maintenance', 'public');
        }

        $greenBuilding = GreenBuilding::create([
            'property_sustainability_id' => $validated['property_sustainability_id'],
            'building_name' => $validated['building_name'],
            'certification_standard' => $validated['certification_standard'],
            'certification_level' => $validated['certification_level'],
            'certification_number' => $validated['certification_number'],
            'certification_date' => $validated['certification_date'],
            'expiry_date' => $validated['expiry_date'],
            'certification_body' => $validated['certification_body'],
            'assessor_name' => $validated['assessor_name'],
            'assessment_date' => $validated['assessment_date'],
            'total_score' => $validated['total_score'],
            'energy_score' => $validated['energy_score'],
            'water_score' => $validated['water_score'],
            'materials_score' => $validated['materials_score'],
            'indoor_environment_score' => $validated['indoor_environment_score'],
            'site_score' => $validated['site_score'],
            'innovation_score' => $validated['innovation_score'],
            'regional_priority_score' => $validated['regional_priority_score'] ?? 0,
            'building_type' => $validated['building_type'],
            'building_size' => $validated['building_size'],
            'size_unit' => $validated['size_unit'],
            'construction_year' => $validated['construction_year'],
            'renovation_year' => $validated['renovation_year'],
            'occupancy_type' => $validated['occupancy_type'],
            'occupancy_rate' => $validated['occupancy_rate'],
            'green_features' => $validated['green_features'],
            'sustainable_materials_used' => $validated['sustainable_materials_used'],
            'energy_efficiency_measures' => $validated['energy_efficiency_measures'],
            'water_conservation_measures' => $validated['water_conservation_measures'],
            'waste_management_systems' => $validated['waste_management_systems'],
            'indoor_air_quality_measures' => $validated['indoor_air_quality_measures'],
            'site_sustainability_features' => $validated['site_sustainability_features'],
            'innovation_features' => $validated['innovation_features'],
            'performance_monitoring' => $validated['performance_monitoring'],
            'monitoring_systems' => $validated['monitoring_systems'] ?? [],
            'commissioning_date' => $validated['commissioning_date'],
            'commissioning_type' => $validated['commissioning_type'],
            'energy_modeling_performed' => $validated['energy_modeling_performed'],
            'energy_modeling_results' => $validated['energy_modeling_results'] ?? [],
            'daylighting_analysis_performed' => $validated['daylighting_analysis_performed'],
            'daylighting_results' => $validated['daylighting_results'] ?? [],
            'thermal_comfort_analysis_performed' => $validated['thermal_comfort_analysis_performed'],
            'thermal_comfort_results' => $validated['thermal_comfort_results'] ?? [],
            'acoustic_analysis_performed' => $validated['acoustic_analysis_performed'],
            'acoustic_results' => $validated['acoustic_results'] ?? [],
            'life_cycle_assessment_performed' => $validated['life_cycle_assessment_performed'],
            'life_cycle_results' => $validated['life_cycle_results'] ?? [],
            'cost_benefit_analysis_performed' => $validated['cost_benefit_analysis_performed'],
            'cost_benefit_results' => $validated['cost_benefit_results'] ?? [],
            'maintenance_plan' => $validated['maintenance_plan'],
            'maintenance_plan_document' => $maintenancePlan,
            'user_training_provided' => $validated['user_training_provided'],
            'training_materials' => $trainingMaterials,
            'certification_documents' => $documents,
            'building_photos' => $photos,
            'verification_url' => $validated['verification_url'],
            'annual_energy_consumption' => $validated['annual_energy_consumption'] ?? 0,
            'annual_water_consumption' => $validated['annual_water_consumption'] ?? 0,
            'annual_waste_generated' => $validated['annual_waste_generated'] ?? 0,
            'annual_carbon_emissions' => $validated['annual_carbon_emissions'] ?? 0,
            'renewable_energy_percentage' => $validated['renewable_energy_percentage'] ?? 0,
            'recycling_rate' => $validated['recycling_rate'] ?? 0,
            'green_space_ratio' => $validated['green_space_ratio'] ?? 0,
            'stormwater_management' => $validated['stormwater_management'],
            'heat_island_reduction' => $validated['heat_island_reduction'],
            'light_pollution_reduction' => $validated['light_pollution_reduction'],
            'biodiversity_protection' => $validated['biodiversity_protection'],
            'certification_status' => $validated['certification_status'],
            'next_assessment_date' => $validated['next_assessment_date'],
            'created_by' => Auth::id(),
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()
            ->route('green-buildings.show', $greenBuilding)
            ->with('success', 'تم تسجيل المبنى الأخضر بنجاح');
    }

    public function show(GreenBuilding $greenBuilding)
    {
        $greenBuilding->load(['propertySustainability.property']);
        
        // Calculate performance metrics
        $performanceMetrics = $this->calculatePerformanceMetrics($greenBuilding);
        
        // Get compliance status
        $complianceStatus = $this->checkComplianceStatus($greenBuilding);

        return view('sustainability.green-buildings.show', compact('greenBuilding', 'performanceMetrics', 'complianceStatus'));
    }

    public function edit(GreenBuilding $greenBuilding)
    {
        $greenBuilding->load('propertySustainability.property');
        
        $certificationStandards = [
            'leed' => 'LEED (Leadership in Energy and Environmental Design)',
            'breeam' => 'BREEAM (Building Research Establishment Environmental Assessment Method)',
            'estidama' => 'Estidama (Abu Dhabi Sustainability Rating System)',
            'green_globes' => 'Green Globes',
            'energy_star' => 'ENERGY STAR',
            'passive_house' => 'Passive House',
            'living_building' => 'Living Building Challenge',
            'well' => 'WELL Building Standard',
            'local_green' => 'شهادة خضراء محلية',
        ];

        $certificationLevels = [
            'platinum' => 'Platinum - البلاتيني',
            'gold' => 'Gold - الذهبي',
            'silver' => 'Silver - الفضي',
            'bronze' => 'Bronze - البرونزي',
            'certified' => 'Certified - معتمد',
        ];

        return view('sustainability.green-buildings.edit', compact('greenBuilding', 'certificationStandards', 'certificationLevels'));
    }

    public function update(Request $request, GreenBuilding $greenBuilding)
    {
        $validated = $request->validate([
            'building_name' => 'required|string|max:255',
            'certification_level' => 'required|string|in:platinum,gold,silver,bronze,certified',
            'expiry_date' => 'nullable|date|after:today',
            'total_score' => 'required|numeric|min:0|max:100',
            'energy_score' => 'required|numeric|min:0|max:100',
            'water_score' => 'required|numeric|min:0|max:100',
            'materials_score' => 'required|numeric|min:0|max:100',
            'indoor_environment_score' => 'required|numeric|min:0|max:100',
            'site_score' => 'required|numeric|min:0|max:100',
            'innovation_score' => 'required|numeric|min:0|max:100',
            'occupancy_rate' => 'required|numeric|min:0|max:100',
            'annual_energy_consumption' => 'nullable|numeric|min:0',
            'annual_water_consumption' => 'nullable|numeric|min:0',
            'annual_waste_generated' => 'nullable|numeric|min:0',
            'annual_carbon_emissions' => 'nullable|numeric|min:0',
            'renewable_energy_percentage' => 'nullable|numeric|min:0|max:100',
            'recycling_rate' => 'nullable|numeric|min:0|max:100',
            'certification_status' => 'required|string|in:pending,active,suspended,expired,revoked',
            'next_assessment_date' => 'nullable|date|after:today',
            'notes' => 'nullable|string',
        ]);

        $greenBuilding->update($validated);

        return redirect()
            ->route('green-buildings.show', $greenBuilding)
            ->with('success', 'تم تحديث بيانات المبنى الأخضر بنجاح');
    }

    public function destroy(GreenBuilding $greenBuilding)
    {
        // Delete associated files
        if ($greenBuilding->certification_documents) {
            foreach ($greenBuilding->certification_documents as $document) {
                Storage::disk('public')->delete($document);
            }
        }

        if ($greenBuilding->building_photos) {
            foreach ($greenBuilding->building_photos as $photo) {
                Storage::disk('public')->delete($photo);
            }
        }

        if ($greenBuilding->training_materials) {
            foreach ($greenBuilding->training_materials as $material) {
                Storage::disk('public')->delete($material);
            }
        }

        if ($greenBuilding->maintenance_plan_document) {
            Storage::disk('public')->delete($greenBuilding->maintenance_plan_document);
        }

        $greenBuilding->delete();

        return redirect()
            ->route('green-buildings.index')
            ->with('success', 'تم حذف المبنى الأخضر بنجاح');
    }

    public function verify(GreenBuilding $greenBuilding)
    {
        // External verification logic
        $verificationData = [
            'certification_number' => $greenBuilding->certification_number,
            'certification_standard' => $greenBuilding->certification_standard,
            'certification_level' => $greenBuilding->certification_level,
            'certification_date' => $greenBuilding->certification_date,
            'expiry_date' => $greenBuilding->expiry_date,
            'status' => $greenBuilding->certification_status,
            'verification_url' => $greenBuilding->verification_url,
        ];

        return response()->json($verificationData);
    }

    public function performance(GreenBuilding $greenBuilding)
    {
        $performanceData = $this->getDetailedPerformanceData($greenBuilding);
        
        return view('sustainability.green-buildings.performance', compact('greenBuilding', 'performanceData'));
    }

    public function compliance(GreenBuilding $greenBuilding)
    {
        $complianceData = $this->getComplianceData($greenBuilding);
        
        return view('sustainability.green-buildings.compliance', compact('greenBuilding', 'complianceData'));
    }

    public function analytics()
    {
        $certificationStats = GreenBuilding::selectRaw('certification_standard, certification_level, COUNT(*) as count, AVG(total_score) as avg_score')
            ->groupBy('certification_standard', 'certification_level')
            ->get();

        $performanceTrends = GreenBuilding::selectRaw('DATE_FORMAT(certification_date, "%Y") as year, AVG(total_score) as avg_score, COUNT(*) as count')
            ->groupBy('year')
            ->orderBy('year')
            ->get();

        $buildingTypeAnalysis = GreenBuilding::selectRaw('building_type, COUNT(*) as count, AVG(building_size) as avg_size, AVG(total_score) as avg_score')
            ->groupBy('building_type')
            ->get();

        $topPerformers = GreenBuilding::with(['propertySustainability.property'])
            ->orderBy('total_score', 'desc')
            ->take(10)
            ->get();

        return view('sustainability.green-buildings.analytics', compact(
            'certificationStats',
            'performanceTrends',
            'buildingTypeAnalysis',
            'topPerformers'
        ));
    }

    private function calculatePerformanceMetrics($greenBuilding)
    {
        $metrics = [];
        
        // Calculate energy performance index
        if ($greenBuilding->annual_energy_consumption > 0 && $greenBuilding->building_size > 0) {
            $sizeInSqM = $greenBuilding->size_unit === 'sq_ft' ? $greenBuilding->building_size * 0.092903 : $greenBuilding->building_size;
            $metrics['energy_performance_index'] = $greenBuilding->annual_energy_consumption / $sizeInSqM; // kWh per sqm per year
        }
        
        // Calculate water performance index
        if ($greenBuilding->annual_water_consumption > 0 && $greenBuilding->building_size > 0) {
            $sizeInSqM = $greenBuilding->size_unit === 'sq_ft' ? $greenBuilding->building_size * 0.092903 : $greenBuilding->building_size;
            $metrics['water_performance_index'] = $greenBuilding->annual_water_consumption / $sizeInSqM; // liters per sqm per year
        }
        
        // Calculate carbon intensity
        if ($greenBuilding->annual_carbon_emissions > 0 && $greenBuilding->building_size > 0) {
            $sizeInSqM = $greenBuilding->size_unit === 'sq_ft' ? $greenBuilding->building_size * 0.092903 : $greenBuilding->building_size;
            $metrics['carbon_intensity'] = $greenBuilding->annual_carbon_emissions / $sizeInSqM; // kg CO2 per sqm per year
        }
        
        // Calculate overall sustainability score
        $metrics['sustainability_score'] = $greenBuilding->total_score;
        
        return $metrics;
    }

    private function checkComplianceStatus($greenBuilding)
    {
        $compliance = [];
        
        // Check certification expiry
        if ($greenBuilding->expiry_date) {
            $daysUntilExpiry = now()->diffInDays($greenBuilding->expiry_date, false);
            $compliance['certification_valid'] = $daysUntilExpiry > 0;
            $compliance['days_until_expiry'] = $daysUntilExpiry;
            $compliance['expiry_warning'] = $daysUntilExpiry > 0 && $daysUntilExpiry <= 90;
        }
        
        // Check next assessment
        if ($greenBuilding->next_assessment_date) {
            $daysUntilAssessment = now()->diffInDays($greenBuilding->next_assessment_date, false);
            $compliance['assessment_due'] = $daysUntilAssessment <= 30;
            $compliance['days_until_assessment'] = $daysUntilAssessment;
        }
        
        // Check performance thresholds
        $compliance['energy_compliant'] = $greenBuilding->energy_score >= 70;
        $compliance['water_compliant'] = $greenBuilding->water_score >= 70;
        $compliance['materials_compliant'] = $greenBuilding->materials_score >= 70;
        $compliance['overall_compliant'] = $greenBuilding->total_score >= 75;
        
        return $compliance;
    }

    private function getDetailedPerformanceData($greenBuilding)
    {
        return [
            'energy_performance' => [
                'score' => $greenBuilding->energy_score,
                'annual_consumption' => $greenBuilding->annual_energy_consumption,
                'renewable_percentage' => $greenBuilding->renewable_energy_percentage,
                'efficiency_measures' => $greenBuilding->energy_efficiency_measures,
            ],
            'water_performance' => [
                'score' => $greenBuilding->water_score,
                'annual_consumption' => $greenBuilding->annual_water_consumption,
                'conservation_measures' => $greenBuilding->water_conservation_measures,
            ],
            'materials_performance' => [
                'score' => $greenBuilding->materials_score,
                'sustainable_materials' => $greenBuilding->sustainable_materials_used,
            ],
            'indoor_environment' => [
                'score' => $greenBuilding->indoor_environment_score,
                'air_quality_measures' => $greenBuilding->indoor_air_quality_measures,
            ],
            'site_performance' => [
                'score' => $greenBuilding->site_score,
                'green_space_ratio' => $greenBuilding->green_space_ratio,
                'site_features' => $greenBuilding->site_sustainability_features,
            ],
        ];
    }

    private function getComplianceData($greenBuilding)
    {
        return [
            'certification_compliance' => [
                'status' => $greenBuilding->certification_status,
                'level' => $greenBuilding->certification_level,
                'score' => $greenBuilding->total_score,
                'expiry_date' => $greenBuilding->expiry_date,
                'next_assessment' => $greenBuilding->next_assessment_date,
            ],
            'performance_compliance' => [
                'energy' => $greenBuilding->energy_score >= 70,
                'water' => $greenBuilding->water_score >= 70,
                'materials' => $greenBuilding->materials_score >= 70,
                'indoor_environment' => $greenBuilding->indoor_environment_score >= 70,
                'site' => $greenBuilding->site_score >= 70,
            ],
            'monitoring_compliance' => [
                'performance_monitoring' => $greenBuilding->performance_monitoring,
                'monitoring_systems' => $greenBuilding->monitoring_systems,
                'maintenance_plan' => $greenBuilding->maintenance_plan,
            ],
        ];
    }
}

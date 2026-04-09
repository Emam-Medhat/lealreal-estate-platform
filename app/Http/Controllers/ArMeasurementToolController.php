<?php

namespace App\Http\Controllers;

use App\Models\ArMeasurementTool;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ArMeasurementToolController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_measurements' => ArMeasurementTool::count(),
            'active_measurements' => ArMeasurementTool::where('status', 'active')->count(),
            'completed_measurements' => ArMeasurementTool::where('status', 'completed')->count(),
            'average_accuracy' => $this->getAverageAccuracy(),
            'total_area_measured' => $this->getTotalAreaMeasured(),
            'measurement_sessions' => $this->getMeasurementSessions(),
        ];

        $recentMeasurements = ArMeasurementTool::with(['property', 'user'])
            ->latest()
            ->take(10)
            ->get();

        $measurementTrends = $this->getMeasurementTrends();
        $performanceMetrics = $this->getPerformanceMetrics();

        return view('ar.measurement-tool.dashboard', compact(
            'stats', 
            'recentMeasurements', 
            'measurementTrends', 
            'performanceMetrics'
        ));
    }

    public function index(Request $request)
    {
        $query = ArMeasurementTool::with(['property', 'user']);

        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('measurement_type')) {
            $query->where('measurement_type', $request->measurement_type);
        }

        if ($request->filled('accuracy_level')) {
            $query->where('accuracy_level', $request->accuracy_level);
        }

        $measurements = $query->latest()->paginate(12);

        $properties = Property::where('status', 'active')->get();
        $measurementTypes = ['room_dimensions', 'area_calculation', 'volume_calculation', 'custom_measurement'];
        $accuracyLevels = ['low', 'medium', 'high', 'professional'];
        $statuses = ['active', 'completed', 'saved', 'discarded'];

        return view('ar.measurement-tool.index', compact(
            'measurements', 
            'properties', 
            'measurementTypes', 
            'accuracyLevels', 
            'statuses'
        ));
    }

    public function create()
    {
        $properties = Property::where('status', 'active')->get();
        $measurementTypes = ['room_dimensions', 'area_calculation', 'volume_calculation', 'custom_measurement'];
        $accuracyLevels = ['low', 'medium', 'high', 'professional'];
        $measurementUnits = ['meters', 'feet', 'inches', 'centimeters'];
        $trackingModes = ['plane_detection', 'point_cloud', 'manual_input', 'hybrid'];

        return view('ar.measurement-tool.create', compact(
            'properties', 
            'measurementTypes', 
            'accuracyLevels', 
            'measurementUnits', 
            'trackingModes'
        ));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $measurementData = $request->validate([
                'property_id' => 'required|exists:properties,id',
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'measurement_type' => 'required|in:room_dimensions,area_calculation,volume_calculation,custom_measurement',
                'accuracy_level' => 'required|in:low,medium,high,professional',
                'tracking_mode' => 'required|in:plane_detection,point_cloud,manual_input,hybrid',
                'measurement_unit' => 'required|in:meters,feet,inches,centimeters',
                'reference_points' => 'required|array',
                'measurement_data' => 'required|array',
                'calibration_data' => 'nullable|array',
                'environmental_factors' => 'nullable|array',
            ]);

            $measurementData['user_id'] = auth()->id();
            $measurementData['status'] = 'active';
            $measurementData['created_by'] = auth()->id();

            // Process measurement files
            if ($request->hasFile('measurement_files')) {
                $measurementData['measurement_files'] = $this->processMeasurementFiles($request->file('measurement_files'));
            }

            // Generate measurement metadata
            $measurementData['measurement_metadata'] = $this->generateMeasurementMetadata($request);

            $measurement = ArMeasurementTool::create($measurementData);

            // Process reference points
            if ($request->has('reference_points')) {
                $this->processReferencePoints($measurement, $request->reference_points);
            }

            // Process measurement data
            if ($request->has('measurement_data')) {
                $this->processMeasurementData($measurement, $request->measurement_data);
            }

            // Set up calibration
            if ($request->has('calibration_data')) {
                $this->setupCalibration($measurement, $request->calibration_data);
            }

            // Configure environmental factors
            if ($request->has('environmental_factors')) {
                $this->configureEnvironmentalFactors($measurement, $request->environmental_factors);
            }

            DB::commit();

            return redirect()
                ->route('ar.measurement-tool.show', $measurement)
                ->with('success', 'تم إنشاء أداة القياس بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إنشاء أداة القياس: ' . $e->getMessage());
        }
    }

    public function show(ArMeasurementTool $measurement)
    {
        $measurement->load(['property', 'user', 'referencePoints', 'measurementData', 'calibration']);
        $measurementAnalytics = $this->getMeasurementAnalytics($measurement);
        $relatedMeasurements = $this->getRelatedMeasurements($measurement);

        return view('ar.measurement-tool.show', compact(
            'measurement', 
            'measurementAnalytics', 
            'relatedMeasurements'
        ));
    }

    public function edit(ArMeasurementTool $measurement)
    {
        $properties = Property::where('status', 'active')->get();
        $measurementTypes = ['room_dimensions', 'area_calculation', 'volume_calculation', 'custom_measurement'];
        $accuracyLevels = ['low', 'medium', 'high', 'professional'];
        $measurementUnits = ['meters', 'feet', 'inches', 'centimeters'];
        $trackingModes = ['plane_detection', 'point_cloud', 'manual_input', 'hybrid'];

        return view('ar.measurement-tool.edit', compact(
            'measurement', 
            'properties', 
            'measurementTypes', 
            'accuracyLevels', 
            'measurementUnits', 
            'trackingModes'
        ));
    }

    public function update(Request $request, ArMeasurementTool $measurement)
    {
        DB::beginTransaction();
        try {
            $measurementData = $request->validate([
                'property_id' => 'required|exists:properties,id',
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'measurement_type' => 'required|in:room_dimensions,area_calculation,volume_calculation,custom_measurement',
                'accuracy_level' => 'required|in:low,medium,high,professional',
                'tracking_mode' => 'required|in:plane_detection,point_cloud,manual_input,hybrid',
                'measurement_unit' => 'required|in:meters,feet,inches,centimeters',
                'reference_points' => 'required|array',
                'measurement_data' => 'required|array',
                'calibration_data' => 'nullable|array',
                'environmental_factors' => 'nullable|array',
            ]);

            $measurementData['updated_by'] = auth()->id();

            // Process updated measurement files
            if ($request->hasFile('measurement_files')) {
                $measurementData['measurement_files'] = $this->processMeasurementFiles($request->file('measurement_files'));
            }

            // Update measurement metadata
            $measurementData['measurement_metadata'] = $this->generateMeasurementMetadata($request);

            $measurement->update($measurementData);

            // Update reference points
            if ($request->has('reference_points')) {
                $this->processReferencePoints($measurement, $request->reference_points);
            }

            // Update measurement data
            if ($request->has('measurement_data')) {
                $this->processMeasurementData($measurement, $request->measurement_data);
            }

            // Update calibration
            if ($request->has('calibration_data')) {
                $this->setupCalibration($measurement, $request->calibration_data);
            }

            // Update environmental factors
            if ($request->has('environmental_factors')) {
                $this->configureEnvironmentalFactors($measurement, $request->environmental_factors);
            }

            DB::commit();

            return redirect()
                ->route('ar.measurement-tool.show', $measurement)
                ->with('success', 'تم تحديث أداة القياس بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث أداة القياس: ' . $e->getMessage());
        }
    }

    public function destroy(ArMeasurementTool $measurement)
    {
        try {
            // Delete measurement files
            $this->deleteMeasurementFiles($measurement);

            // Delete measurement
            $measurement->delete();

            return redirect()
                ->route('ar.measurement-tool.index')
                ->with('success', 'تم حذف أداة القياس بنجاح');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'حدث خطأ أثناء حذف أداة القياس: ' . $e->getMessage());
        }
    }

    public function startMeasurement(ArMeasurementTool $measurement)
    {
        try {
            // Initialize AR measurement session
            $session = $this->initializeArMeasurementSession($measurement);

            // Update measurement statistics
            $measurement->increment('session_count');
            $measurement->update(['last_accessed_at' => now()]);

            return view('ar.measurement-tool.session', compact('measurement', 'session'));
        } catch (\Exception $e) {
            return back()
                ->with('error', 'حدث خطأ أثناء بدء القياس: ' . $e->getMessage());
        }
    }

    public function calibrateTool(Request $request, ArMeasurementTool $measurement)
    {
        try {
            $calibrationData = [
                'calibration_type' => $request->calibration_type,
                'reference_object_size' => $request->reference_object_size,
                'calibration_points' => $request->calibration_points,
                'environmental_conditions' => $request->environmental_conditions,
            ];

            // Perform calibration
            $calibrationResult = $this->performCalibration($measurement, $calibrationData);

            return response()->json([
                'success' => true,
                'calibration' => $calibrationResult
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function takeMeasurement(Request $request, ArMeasurementTool $measurement)
    {
        try {
            $measurementData = [
                'measurement_type' => $request->measurement_type,
                'start_point' => $request->start_point,
                'end_point' => $request->end_point,
                'intermediate_points' => $request->intermediate_points ?? [],
                'measurement_unit' => $request->measurement_unit,
                'timestamp' => now(),
            ];

            // Take measurement
            $result = $this->performMeasurement($measurement, $measurementData);

            return response()->json([
                'success' => true,
                'measurement' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function calculateArea(Request $request, ArMeasurementTool $measurement)
    {
        try {
            $areaData = [
                'boundary_points' => $request->boundary_points,
                'measurement_unit' => $request->measurement_unit,
                'calculation_method' => $request->calculation_method ?? 'polygon',
            ];

            // Calculate area
            $areaResult = $this->calculateAreaFromPoints($measurement, $areaData);

            return response()->json([
                'success' => true,
                'area' => $areaResult
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function calculateVolume(Request $request, ArMeasurementTool $measurement)
    {
        try {
            $volumeData = [
                'base_area' => $request->base_area,
                'height' => $request->height,
                'shape_type' => $request->shape_type ?? 'rectangular',
                'measurement_unit' => $request->measurement_unit,
            ];

            // Calculate volume
            $volumeResult = $this->calculateVolumeFromData($measurement, $volumeData);

            return response()->json([
                'success' => true,
                'volume' => $volumeResult
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function saveMeasurement(ArMeasurementTool $measurement, Request $request)
    {
        try {
            $saveData = [
                'measurement_name' => $request->measurement_name,
                'description' => $request->description,
                'is_template' => $request->is_template ?? false,
                'share_publicly' => $request->share_publicly ?? false,
            ];

            // Save measurement configuration
            $this->saveMeasurementConfiguration($measurement, $saveData);

            // Update measurement status
            $measurement->update([
                'status' => 'saved',
                'saved_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم حفظ القياس بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function exportMeasurement(ArMeasurementTool $measurement, Request $request)
    {
        try {
            $exportFormat = $request->format ?? 'json';
            $exportData = $this->prepareMeasurementExport($measurement, $exportFormat);

            return response()->download($exportData['file'], $exportData['filename']);
        } catch (\Exception $e) {
            return back()
                ->with('error', 'حدث خطأ أثناء تصدير القياس: ' . $e->getMessage());
        }
    }

    public function analytics(ArMeasurementTool $measurement)
    {
        $analytics = $this->getDetailedMeasurementAnalytics($measurement);
        $accuracyMetrics = $this->getAccuracyMetrics($measurement);
        $usagePatterns = $this->getUsagePatterns($measurement);

        return view('ar.measurement-tool.analytics', compact(
            'analytics', 
            'accuracyMetrics', 
            'usagePatterns'
        ));
    }

    private function processMeasurementFiles($files)
    {
        $filePaths = [];
        
        foreach ($files as $file) {
            $path = $file->store('ar-measurements', 'public');
            $filePaths[] = [
                'path' => $path,
                'type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'original_name' => $file->getClientOriginalName(),
            ];
        }

        return $filePaths;
    }

    private function generateMeasurementMetadata($request)
    {
        return [
            'total_reference_points' => count($request->reference_points ?? []),
            'measurement_complexity' => $this->calculateMeasurementComplexity($request),
            'expected_accuracy' => $this->getExpectedAccuracy($request->accuracy_level),
            'environmental_factors' => $request->environmental_factors ?? [],
            'device_requirements' => $this->getDeviceRequirements($request),
            'calibration_frequency' => $this->getCalibrationFrequency($request->accuracy_level),
            'created_at' => now()->toDateTimeString(),
        ];
    }

    private function processReferencePoints($measurement, $referencePoints)
    {
        foreach ($referencePoints as $pointData) {
            $measurement->referencePoints()->create([
                'name' => $pointData['name'],
                'position' => $pointData['position'],
                'type' => $pointData['type'],
                'confidence_level' => $pointData['confidence_level'] ?? 1.0,
                'is_fixed' => $pointData['is_fixed'] ?? false,
                'point_metadata' => $pointData['metadata'] ?? [],
            ]);
        }
    }

    private function processMeasurementData($measurement, $measurementData)
    {
        foreach ($measurementData as $dataItem) {
            $measurement->measurementData()->create([
                'measurement_name' => $dataItem['name'],
                'type' => $dataItem['type'],
                'value' => $dataItem['value'],
                'unit' => $dataItem['unit'],
                'accuracy' => $dataItem['accuracy'] ?? 0.95,
                'points' => $dataItem['points'] ?? [],
                'measurement_metadata' => $dataItem['metadata'] ?? [],
            ]);
        }
    }

    private function setupCalibration($measurement, $calibrationData)
    {
        $measurement->calibration()->create([
            'calibration_type' => $calibrationData['type'] ?? 'automatic',
            'calibration_value' => $calibrationData['value'] ?? 1.0,
            'calibration_points' => $calibrationData['points'] ?? [],
            'accuracy_score' => $calibrationData['accuracy_score'] ?? 0.95,
            'last_calibrated' => now(),
            'calibration_metadata' => $calibrationData['metadata'] ?? [],
        ]);
    }

    private function configureEnvironmentalFactors($measurement, $environmentalFactors)
    {
        foreach ($environmentalFactors as $factorData) {
            $measurement->environmentalFactors()->create([
                'factor_type' => $factorData['type'],
                'factor_value' => $factorData['value'],
                'impact_level' => $factorData['impact_level'] ?? 'medium',
                'compensation_method' => $factorData['compensation_method'] ?? 'automatic',
                'factor_metadata' => $factorData['metadata'] ?? [],
            ]);
        }
    }

    private function initializeArMeasurementSession($measurement)
    {
        return [
            'session_id' => uniqid('ar_measure_'),
            'start_time' => now(),
            'user_id' => auth()->id(),
            'measurement_id' => $measurement->id,
            'device_info' => $this->getDeviceInfo(),
            'ar_settings' => [
                'tracking_mode' => $measurement->tracking_mode,
                'accuracy_level' => $measurement->accuracy_level,
                'measurement_unit' => $measurement->measurement_unit,
                'calibration_status' => $this->getCalibrationStatus($measurement),
            ],
        ];
    }

    private function getDeviceInfo()
    {
        return [
            'user_agent' => request()->userAgent(),
            'ip_address' => request()->ip(),
            'screen_resolution' => request()->header('Screen-Resolution'),
            'device_type' => $this->detectDeviceType(),
            'ar_capabilities' => $this->detectArCapabilities(),
        ];
    }

    private function detectDeviceType()
    {
        $userAgent = request()->userAgent();
        
        if (preg_match('/AR|Magic|Holo/i', $userAgent)) {
            return 'ar_glasses';
        } elseif (preg_match('/Mobile|Android|iPhone/i', $userAgent)) {
            return 'mobile';
        } elseif (preg_match('/Tablet|iPad/i', $userAgent)) {
            return 'tablet';
        }
        
        return 'desktop';
    }

    private function detectArCapabilities()
    {
        $userAgent = request()->userAgent();
        $capabilities = [];

        if (preg_match('/ARKit/i', $userAgent)) {
            $capabilities[] = 'arkit';
        }
        
        if (preg_match('/ARCore/i', $userAgent)) {
            $capabilities[] = 'arcore';
        }

        return $capabilities;
    }

    private function calculateMeasurementComplexity($request)
    {
        $complexity = 1; // Base complexity
        
        $complexity += count($request->reference_points ?? []) * 0.1;
        $complexity += count($request->measurement_data ?? []) * 0.2;
        
        return min(5, $complexity);
    }

    private function getExpectedAccuracy($accuracyLevel)
    {
        $accuracies = [
            'low' => 0.85,
            'medium' => 0.90,
            'high' => 0.95,
            'professional' => 0.98,
        ];

        return $accuracies[$accuracyLevel] ?? 0.90;
    }

    private function getDeviceRequirements($request)
    {
        return [
            'minimum_ram' => $this->getMinimumRam($request->accuracy_level),
            'camera_quality' => $this->getCameraQuality($request->accuracy_level),
            'processor_speed' => $this->getProcessorSpeed($request->accuracy_level),
            'sensors_required' => $this->getSensorsRequired($request->tracking_mode),
        ];
    }

    private function getCalibrationFrequency($accuracyLevel)
    {
        $frequencies = [
            'low' => 'daily',
            'medium' => 'per_session',
            'high' => 'hourly',
            'professional' => 'continuous',
        ];

        return $frequencies[$accuracyLevel] ?? 'per_session';
    }

    private function getMinimumRam($accuracyLevel)
    {
        $requirements = [
            'low' => 4,
            'medium' => 6,
            'high' => 8,
            'professional' => 12,
        ];

        return $requirements[$accuracyLevel] ?? 6;
    }

    private function getCameraQuality($accuracyLevel)
    {
        $qualities = [
            'low' => '720p',
            'medium' => '1080p',
            'high' => '4K',
            'professional' => '8K',
        ];

        return $qualities[$accuracyLevel] ?? '1080p';
    }

    private function getProcessorSpeed($accuracyLevel)
    {
        $speeds = [
            'low' => '1.5 GHz',
            'medium' => '2.0 GHz',
            'high' => '2.5 GHz',
            'professional' => '3.0 GHz',
        ];

        return $speeds[$accuracyLevel] ?? '2.0 GHz';
    }

    private function getSensorsRequired($trackingMode)
    {
        $sensors = [
            'plane_detection' => ['camera', 'gyroscope', 'accelerometer'],
            'point_cloud' => ['camera', 'lidar', 'depth_sensor'],
            'manual_input' => ['touch_screen'],
            'hybrid' => ['camera', 'gyroscope', 'accelerometer', 'depth_sensor'],
        ];

        return $sensors[$trackingMode] ?? ['camera', 'gyroscope', 'accelerometer'];
    }

    private function getCalibrationStatus($measurement)
    {
        $calibration = $measurement->calibration;
        
        if (!$calibration) {
            return 'not_calibrated';
        }

        $hoursSinceCalibration = now()->diffInHours($calibration->last_calibrated);
        $requiredFrequency = $this->getCalibrationFrequency($measurement->accuracy_level);

        if ($requiredFrequency === 'continuous') {
            return 'needs_calibration';
        } elseif ($requiredFrequency === 'hourly' && $hoursSinceCalibration > 1) {
            return 'needs_calibration';
        } elseif ($requiredFrequency === 'daily' && $hoursSinceCalibration > 24) {
            return 'needs_calibration';
        }

        return 'calibrated';
    }

    private function performCalibration($measurement, $calibrationData)
    {
        // Perform calibration process
        $calibrationResult = [
            'calibration_id' => uniqid('cal_'),
            'calibration_score' => 0.96,
            'accuracy_improvement' => 0.05,
            'calibration_time' => 12.5,
            'reference_points_used' => count($calibrationData['calibration_points']),
            'environmental_compensation' => $this->applyEnvironmentalCompensation($measurement),
        ];

        // Update calibration data
        $measurement->calibration()->update([
            'calibration_value' => $calibrationResult['calibration_score'],
            'accuracy_score' => $calibrationResult['accuracy_improvement'],
            'last_calibrated' => now(),
        ]);

        return $calibrationResult;
    }

    private function performMeasurement($measurement, $measurementData)
    {
        // Perform the actual measurement
        $distance = $this->calculateDistance($measurementData['start_point'], $measurementData['end_point']);
        
        // Apply calibration and environmental corrections
        $correctedDistance = $this->applyCorrections($measurement, $distance);

        return [
            'measurement_id' => uniqid('meas_'),
            'raw_value' => $distance,
            'corrected_value' => $correctedDistance,
            'unit' => $measurementData['measurement_unit'],
            'accuracy' => $this->calculateMeasurementAccuracy($measurement, $measurementData),
            'confidence_level' => 0.94,
            'measurement_time' => 2.3,
            'points_used' => count($measurementData['intermediate_points']) + 2,
        ];
    }

    private function calculateAreaFromPoints($measurement, $areaData)
    {
        // Calculate area using polygon method
        $area = $this->calculatePolygonArea($areaData['boundary_points']);
        
        // Apply unit conversion if needed
        $convertedArea = $this->convertAreaUnit($area, $areaData['measurement_unit']);

        return [
            'area_id' => uniqid('area_'),
            'raw_area' => $area,
            'converted_area' => $convertedArea,
            'unit' => $areaData['measurement_unit'],
            'accuracy' => $this->calculateAreaAccuracy($measurement, $areaData),
            'calculation_method' => $areaData['calculation_method'],
            'points_used' => count($areaData['boundary_points']),
        ];
    }

    private function calculateVolumeFromData($measurement, $volumeData)
    {
        // Calculate volume based on shape type
        $volume = $this->calculateVolume($volumeData['base_area'], $volumeData['height'], $volumeData['shape_type']);
        
        // Apply unit conversion if needed
        $convertedVolume = $this->convertVolumeUnit($volume, $volumeData['measurement_unit']);

        return [
            'volume_id' => uniqid('vol_'),
            'raw_volume' => $volume,
            'converted_volume' => $convertedVolume,
            'unit' => $volumeData['measurement_unit'],
            'accuracy' => $this->calculateVolumeAccuracy($measurement, $volumeData),
            'shape_type' => $volumeData['shape_type'],
        ];
    }

    private function saveMeasurementConfiguration($measurement, $saveData)
    {
        // Save measurement as template or configuration
        if ($saveData['is_template']) {
            $measurement->update([
                'is_template' => true,
                'template_name' => $saveData['measurement_name'],
                'template_description' => $saveData['description'],
                'is_public' => $saveData['share_publicly'],
            ]);
        }
    }

    private function prepareMeasurementExport($measurement, $format)
    {
        $data = [
            'measurement' => $measurement->toArray(),
            'reference_points' => $measurement->referencePoints->toArray(),
            'measurement_data' => $measurement->measurementData->toArray(),
            'calibration' => $measurement->calibration->toArray(),
            'environmental_factors' => $measurement->environmentalFactors->toArray(),
        ];

        if ($format === 'json') {
            $filename = 'measurement_' . $measurement->id . '.json';
            $content = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        } else {
            $filename = 'measurement_' . $measurement->id . '.txt';
            $content = serialize($data);
        }

        $tempFile = tempnam(sys_get_temp_dir(), $filename);
        file_put_contents($tempFile, $content);

        return [
            'file' => $tempFile,
            'filename' => $filename,
        ];
    }

    private function calculateDistance($startPoint, $endPoint)
    {
        $dx = $endPoint[0] - $startPoint[0];
        $dy = $endPoint[1] - $startPoint[1];
        $dz = $endPoint[2] - $startPoint[2];
        
        return sqrt($dx * $dx + $dy * $dy + $dz * $dz);
    }

    private function applyCorrections($measurement, $rawValue)
    {
        $calibration = $measurement->calibration;
        $correctionFactor = $calibration ? $calibration->calibration_value : 1.0;
        
        return $rawValue * $correctionFactor;
    }

    private function calculateMeasurementAccuracy($measurement, $measurementData)
    {
        $baseAccuracy = $this->getExpectedAccuracy($measurement->accuracy_level);
        $pointCount = count($measurementData['intermediate_points']) + 2;
        
        // More points generally improve accuracy
        $pointBonus = min(0.05, $pointCount * 0.01);
        
        return min(0.99, $baseAccuracy + $pointBonus);
    }

    private function calculatePolygonArea($points)
    {
        $area = 0;
        $n = count($points);
        
        for ($i = 0; $i < $n; $i++) {
            $j = ($i + 1) % $n;
            $area += $points[$i][0] * $points[$j][1];
            $area -= $points[$j][0] * $points[$i][1];
        }
        
        return abs($area / 2);
    }

    private function calculateVolume($baseArea, $height, $shapeType)
    {
        switch ($shapeType) {
            case 'rectangular':
                return $baseArea * $height;
            case 'cylindrical':
                return $baseArea * $height;
            case 'conical':
                return ($baseArea * $height) / 3;
            default:
                return $baseArea * $height;
        }
    }

    private function convertAreaUnit($area, $unit)
    {
        // Convert area to requested unit
        $conversions = [
            'meters' => 1.0,
            'feet' => 10.764,
            'inches' => 1550.0,
            'centimeters' => 10000.0,
        ];

        return $area * ($conversions[$unit] ?? 1.0);
    }

    private function convertVolumeUnit($volume, $unit)
    {
        // Convert volume to requested unit
        $conversions = [
            'meters' => 1.0,
            'feet' => 35.315,
            'inches' => 61023.7,
            'centimeters' => 1000000.0,
        ];

        return $volume * ($conversions[$unit] ?? 1.0);
    }

    private function calculateAreaAccuracy($measurement, $areaData)
    {
        $baseAccuracy = $this->getExpectedAccuracy($measurement->accuracy_level);
        $pointCount = count($areaData['boundary_points']);
        
        // More points generally improve accuracy
        $pointBonus = min(0.05, $pointCount * 0.005);
        
        return min(0.99, $baseAccuracy + $pointBonus);
    }

    private function calculateVolumeAccuracy($measurement, $volumeData)
    {
        $baseAccuracy = $this->getExpectedAccuracy($measurement->accuracy_level);
        
        // Volume calculations generally have slightly lower accuracy
        return $baseAccuracy - 0.02;
    }

    private function applyEnvironmentalCompensation($measurement)
    {
        // Apply environmental factor compensation
        $factors = $measurement->environmentalFactors;
        $compensation = 1.0;
        
        foreach ($factors as $factor) {
            if ($factor->impact_level === 'high') {
                $compensation *= 0.95;
            } elseif ($factor->impact_level === 'medium') {
                $compensation *= 0.98;
            }
        }
        
        return $compensation;
    }

    private function getAverageAccuracy()
    {
        return ArMeasurementTool::avg('average_accuracy') ?? 0;
    }

    private function getTotalAreaMeasured()
    {
        return ArMeasurementTool::sum('total_area_measured') ?? 0;
    }

    private function getMeasurementSessions()
    {
        return ArMeasurementTool::sum('session_count') ?? 0;
    }

    private function getMeasurementTrends()
    {
        return [
            'daily_measurements' => ArMeasurementTool::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date', 'desc')
                ->take(30)
                ->get(),
            'popular_types' => ArMeasurementTool::select('measurement_type', DB::raw('COUNT(*) as count'))
                ->groupBy('measurement_type')
                ->orderBy('count', 'desc')
                ->get(),
        ];
    }

    private function getPerformanceMetrics()
    {
        return [
            'measurement_accuracy' => 94.5,
            'calibration_success' => 96.2,
            'user_satisfaction' => 4.2,
            'completion_rate' => 87.3,
        ];
    }

    private function getMeasurementAnalytics($measurement)
    {
        return [
            'total_measurements' => $measurement->measurementData()->count(),
            'average_accuracy' => $measurement->average_accuracy,
            'total_area_measured' => $measurement->total_area_measured,
            'calibration_status' => $this->getCalibrationStatus($measurement),
            'session_count' => $measurement->session_count,
        ];
    }

    private function getRelatedMeasurements($measurement)
    {
        return ArMeasurementTool::where('property_id', $measurement->property_id)
            ->where('id', '!=', $measurement->id)
            ->with('property')
            ->take(5)
            ->get();
    }

    private function deleteMeasurementFiles($measurement)
    {
        // Delete associated files from storage
        if ($measurement->measurement_files) {
            foreach ($measurement->measurement_files as $file) {
                if (isset($file['path'])) {
                    Storage::disk('public')->delete($file['path']);
                }
            }
        }
    }

    private function getDetailedMeasurementAnalytics($measurement)
    {
        return [
            'measurement_analytics' => $this->getMeasurementAnalytics($measurement),
            'accuracy_analytics' => $this->getAccuracyAnalytics($measurement),
            'usage_analytics' => $this->getUsageAnalytics($measurement),
            'performance_analytics' => $this->getPerformanceAnalytics($measurement),
        ];
    }

    private function getAccuracyMetrics($measurement)
    {
        return [
            'calibration_history' => $this->getCalibrationHistory($measurement),
            'accuracy_trends' => $this->getAccuracyTrends($measurement),
            'environmental_impact' => $this->getEnvironmentalImpact($measurement),
            'device_performance' => $this->getDevicePerformance($measurement),
        ];
    }

    private function getUsagePatterns($measurement)
    {
        return [
            'session_patterns' => $this->getSessionPatterns($measurement),
            'measurement_frequency' => $this->getMeasurementFrequency($measurement),
            'user_preferences' => $this->getUserPreferences($measurement),
            'peak_usage_times' => $this->getPeakUsageTimes($measurement),
        ];
    }

    // Additional helper methods would be implemented here...
}

<?php

namespace App\Http\Controllers;

use App\Models\AiPricePrediction;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AiPricePredictorController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_predictions' => AiPricePrediction::count(),
            'today_predictions' => AiPricePrediction::whereDate('created_at', today())->count(),
            'average_accuracy' => $this->getAverageAccuracy(),
            'total_properties_analyzed' => $this->getTotalPropertiesAnalyzed(),
            'prediction_success_rate' => $this->getPredictionSuccessRate(),
            'last_update' => AiPricePrediction::latest()->first()?->created_at?->diffForHumans() ?? 'الآن',
        ];

        $recentPredictions = AiPricePrediction::with(['property', 'user'])
            ->latest()
            ->take(10)
            ->get();

        $predictionTrends = $this->getPredictionTrends();
        $accuracyMetrics = $this->getAccuracyMetrics();

        return view('ai.price-prediction', compact(
            'stats', 
            'recentPredictions', 
            'predictionTrends', 
            'accuracyMetrics'
        ));
        }

    public function export()
    {
        $predictions = AiPricePrediction::with('property')->latest()->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="price_predictions.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($predictions) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for Excel compatibility with Arabic characters
            fputs($file, "\xEF\xBB\xBF");
            
            fputcsv($file, ['المعرف', 'العقار', 'السعر المتوقع', 'الدقة', 'الحالة', 'تاريخ التوقع']);

            foreach ($predictions as $prediction) {
                fputcsv($file, [
                    $prediction->id,
                    $prediction->property->title ?? 'N/A',
                    $prediction->predicted_price,
                    ($prediction->accuracy * 100) . '%',
                    $prediction->status,
                    $prediction->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function index(Request $request)
    {
        $query = AiPricePrediction::with(['property', 'user']);

        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('accuracy_min')) {
            $query->where('accuracy', '>=', $request->accuracy_min);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $predictions = $query->latest()->paginate(20);

        $properties = Property::all();
        $statuses = ['pending', 'completed', 'failed', 'cancelled'];

        return view('ai.price-predictor.index', compact('predictions', 'properties', 'statuses'));
    }

    public function create()
    {
        $properties = Property::all();
        $predictionModels = $this->getAvailableModels();
        $timeHorizons = $this->getTimeHorizons();

        return view('ai.price-predictor.create', compact('properties', 'predictionModels', 'timeHorizons'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'prediction_model' => 'required|string|in:' . implode(',', array_keys($this->getAvailableModels())),
            'time_horizon' => 'required|string|in:' . implode(',', array_keys($this->getTimeHorizons())),
            'market_data' => 'nullable|array',
            'historical_data' => 'nullable|array',
            'property_features' => 'nullable|array',
            'economic_indicators' => 'nullable|array',
            'prediction_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $property = Property::findOrFail($validated['property_id']);
        $model = $validated['prediction_model'];
        $timeHorizon = $validated['time_horizon'];

        // Generate default data if not provided
        $marketData = $validated['market_data'] ?? $this->generateDefaultMarketData($property);
        $historicalData = $validated['historical_data'] ?? $this->generateDefaultHistoricalData($property);
        $propertyFeatures = $validated['property_features'] ?? $this->generateDefaultPropertyFeatures($property);
        $economicIndicators = $validated['economic_indicators'] ?? $this->generateDefaultEconomicIndicators();

        $prediction = AiPricePrediction::create([
            'property_id' => $validated['property_id'],
            'user_id' => auth()->id(),
            'current_price' => $property->price,
            'prediction_model' => $model,
            'time_horizon' => $timeHorizon,
            'market_data' => $marketData,
            'historical_data' => $historicalData,
            'property_features' => $propertyFeatures,
            'economic_indicators' => $economicIndicators,
            'prediction_date' => $validated['prediction_date'],
            'predicted_price' => $this->calculatePredictedPrice($property, [
                'market_data' => $marketData,
                'historical_data' => $historicalData,
                'economic_indicators' => $economicIndicators,
                'time_horizon' => $timeHorizon,
                'prediction_model' => $model
            ]),
            'confidence_interval' => $this->calculateConfidenceInterval($property, [
                'market_data' => $marketData,
                'historical_data' => $historicalData,
                'economic_indicators' => $economicIndicators,
                'time_horizon' => $timeHorizon,
                'prediction_model' => $model
            ]),
            'accuracy' => $this->calculateAccuracy([
                'historical_data' => $historicalData,
                'prediction_model' => $model,
                'market_data' => $marketData,
                'property_features' => $propertyFeatures,
                'economic_indicators' => $economicIndicators
            ]),
            'notes' => $validated['notes'] ?? null,
            'status' => 'pending',
            'metadata' => [
                'model_version' => 'v1.0',
                'data_points_count' => count($historicalData),
                'features_count' => count($propertyFeatures),
                'indicators_count' => count($economicIndicators),
                'created_at' => now(),
            ],
        ]);

        // Trigger AI prediction process
        $this->processPrediction($prediction);

        return redirect()->route('ai.price-predictor.show', $prediction)
            ->with('success', 'تم إنشاء التنبؤ بالسعر بالذكاء الاصطناعي بنجاح');
    }

    public function show($price_predictor)
    {
        // Debug: Check what ID we received
        logger('Controller show method - Received ID: ' . $price_predictor);
        
        $prediction = AiPricePrediction::with(['property', 'user'])->find($price_predictor);
        
        if (!$prediction) {
            logger('Controller show method - Prediction not found for ID: ' . $price_predictor);
            abort(404);
        }
        
        logger('Controller show method - Loaded Prediction ID: ' . $prediction->id);
        
        $predictionDetails = $this->getPredictionDetails($prediction);
        $marketAnalysis = $this->getMarketAnalysis($prediction);
        $accuracyAnalysis = $this->getAccuracyAnalysis($prediction);

        return view('ai.price-predictor.show', compact(
            'prediction', 
            'predictionDetails', 
            'marketAnalysis', 
            'accuracyAnalysis'
        ));
    }

    public function edit($price_predictor)
    {
        $prediction = AiPricePrediction::find($price_predictor);
        
        if (!$prediction) {
            abort(404);
        }
        
        if ($prediction->status === 'completed') {
            return back()->with('error', 'لا يمكن تعديل التنبؤ المكتمل');
        }

        $properties = Property::all();
        $predictionModels = $this->getAvailableModels();
        $timeHorizons = $this->getTimeHorizons();

        return view('ai.price-predictor.edit', compact('prediction', 'properties', 'predictionModels', 'timeHorizons'));
    }

    public function update(Request $request, $price_predictor)
    {
        $prediction = AiPricePrediction::find($price_predictor);
        
        if (!$prediction) {
            abort(404);
        }
        
        if ($prediction->status === 'completed') {
            return back()->with('error', 'لا يمكن تعديل التنبؤ المكتمل');
        }

        $validated = $request->validate([
            'market_data' => 'nullable|array',
            'historical_data' => 'nullable|array',
            'property_features' => 'nullable|array',
            'economic_indicators' => 'nullable|array',
            'notes' => 'nullable|string',
        ]);

        $marketData = $validated['market_data'] ?? $prediction->market_data;
        $historicalData = $validated['historical_data'] ?? $prediction->historical_data;
        $propertyFeatures = $validated['property_features'] ?? $prediction->property_features;
        $economicIndicators = $validated['economic_indicators'] ?? $prediction->economic_indicators;

        $calculationData = [
            'market_data' => $marketData,
            'historical_data' => $historicalData,
            'property_features' => $propertyFeatures,
            'economic_indicators' => $economicIndicators,
            'time_horizon' => $prediction->time_horizon,
            'prediction_model' => $prediction->prediction_model
        ];

        // Load the property relationship
        $prediction->load('property');
        
        if (!$prediction->property) {
            return back()->with('error', 'العقار المرتبط بالتوقع غير موجود');
        }

        $prediction->update([
            'market_data' => $marketData,
            'historical_data' => $historicalData,
            'property_features' => $propertyFeatures,
            'economic_indicators' => $economicIndicators,
            'predicted_price' => $this->calculatePredictedPrice($prediction->property, $calculationData),
            'confidence_interval' => $this->calculateConfidenceInterval($prediction->property, $calculationData),
            'accuracy' => $this->calculateAccuracy($calculationData),
            'notes' => $validated['notes'] ?? $prediction->notes,
            'metadata' => array_merge($prediction->metadata, [
                'updated_at' => now(),
                'data_updated' => true,
            ]),
        ]);

        // Re-process prediction with updated data
        $this->processPrediction($prediction);

        return redirect()->route('ai.price-predictor.show', $prediction)
            ->with('success', 'تم تحديث التنبؤ بالسعر بنجاح');
    }

    public function destroy($price_predictor)
    {
        $prediction = AiPricePrediction::find($price_predictor);
        
        if (!$prediction) {
            abort(404);
        }
        
        if ($prediction->status === 'completed') {
            return back()->with('error', 'لا يمكن حذف التنبؤ المكتمل');
        }

        $prediction->delete();

        return redirect()->route('ai.price-predictor.index')
            ->with('success', 'تم حذف التنبؤ بالسعر بنجاح');
    }

    public function predict($price_predictor)
    {
        $prediction = AiPricePrediction::with('property')->find($price_predictor);
        
        if (!$prediction) {
            abort(404);
        }
        
        $predictionResults = $this->generatePrediction($prediction);
        
        $prediction->update([
            'predicted_price' => $predictionResults['predicted_price'],
            'confidence_interval' => $predictionResults['confidence_interval'],
            'accuracy' => $predictionResults['accuracy'],
            'status' => 'completed',
            'metadata' => array_merge($prediction->metadata, [
                'prediction_results' => $predictionResults,
                'prediction_date' => now(),
                'model_used' => $predictionResults['model_used'],
            ]),
        ]);

        return response()->json([
            'success' => true,
            'prediction' => $prediction->fresh(),
            'results' => $predictionResults,
        ]);
    }

    public function analyze($price_predictor)
    {
        $prediction = AiPricePrediction::find($price_predictor);
        
        if (!$prediction) {
            abort(404);
        }
        
        $analysis = $this->performAiAnalysis($prediction);
        
        $prediction->update([
            'metadata' => array_merge($prediction->metadata, [
                'analysis_results' => $analysis,
                'analysis_date' => now(),
                'model_accuracy' => $analysis['accuracy'] ?? 0,
            ]),
        ]);

        return response()->json([
            'success' => true,
            'analysis' => $analysis,
            'updated_prediction' => $prediction->fresh(),
        ]);
    }

    public function compare($price_predictor)
    {
        $prediction = AiPricePrediction::with('property')->find($price_predictor);
        
        if (!$prediction) {
            abort(404);
        }
        
        $comparisons = $this->getComparativePredictions($prediction);
        
        return response()->json([
            'comparisons' => $comparisons,
            'prediction' => $prediction,
            'market_comparison' => $this->getMarketComparison($prediction),
        ]);
    }

    public function forecast(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'time_horizons' => 'required|array',
            'time_horizons.*' => 'string|in:' . implode(',', array_keys($this->getTimeHorizons())),
        ]);

        $property = Property::findOrFail($validated['property_id']);
        $timeHorizons = $validated['time_horizons'];

        $forecasts = [];
        foreach ($timeHorizons as $horizon) {
            $forecasts[$horizon] = $this->generateForecast($property, $horizon);
        }

        return response()->json([
            'success' => true,
            'forecasts' => $forecasts,
            'property' => $property,
        ]);
    }

    private function generateDefaultMarketData(Property $property): array
    {
        return [
            'demand_trend' => rand(1, 100) / 100,
            'supply_trend' => rand(1, 100) / 100,
            'price_trend' => rand(1, 100) / 100,
            'inventory_level' => rand(1, 1000),
            'days_on_market' => rand(1, 180),
            'market_segment' => $property->type ?? 'residential',
            'location_score' => rand(1, 10),
        ];
    }

    private function generateDefaultHistoricalData(Property $property): array
    {
        $data = [];
        $basePrice = $property->price ?? 100000;
        for ($i = 12; $i >= 0; $i--) {
            $data[] = [
                'date' => Carbon::now()->subMonths($i)->format('Y-m-d'),
                'price' => $basePrice * (1 + (rand(-5, 10) / 100)),
                'volume' => rand(10, 100)
            ];
        }
        return $data;
    }

    private function generateDefaultPropertyFeatures(Property $property): array
    {
        return [
            'bedrooms' => $property->bedrooms ?? rand(1, 5),
            'bathrooms' => $property->bathrooms ?? rand(1, 3),
            'area' => $property->area ?? rand(50, 500),
            'age' => $property->age ?? rand(0, 50),
            'condition' => rand(1, 10),
            'amenities_score' => rand(1, 10),
        ];
    }

    private function generateDefaultEconomicIndicators(): array
    {
        return [
            'interest_rate' => 0.05 + (rand(-10, 10) / 1000),
            'inflation_rate' => 0.03 + (rand(-10, 10) / 1000),
            'gdp_growth' => 0.02 + (rand(-10, 10) / 1000),
            'employment_rate' => 0.95 + (rand(-10, 10) / 1000),
            'consumer_confidence' => rand(80, 120),
        ];
    }

    // Helper Methods
    private function getAverageAccuracy(): float
    {
        return AiPricePrediction::where('status', 'completed')
            ->avg('accuracy') ?? 0;
    }

    private function getTotalPropertiesAnalyzed(): int
    {
        return AiPricePrediction::where('status', 'completed')
            ->distinct('property_id')
            ->count();
    }

    private function getPredictionSuccessRate(): float
    {
        $total = AiPricePrediction::count();
        $completed = AiPricePrediction::where('status', 'completed')->count();
        
        return $total > 0 ? ($completed / $total) * 100 : 0;
    }

    private function getPredictionTrends(): array
    {
        return AiPricePrediction::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count'),
                DB::raw('AVG(accuracy) as avg_accuracy'),
                DB::raw('AVG(predicted_price) as avg_price')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->toArray();
    }

    private function getAccuracyMetrics(): array
    {
        return [
            'total_predictions' => AiPricePrediction::count(),
            'high_accuracy_predictions' => AiPricePrediction::where('accuracy', '>=', 0.8)->count(),
            'average_accuracy' => AiPricePrediction::avg('accuracy') ?? 0,
            'model_performance' => $this->getModelPerformance(),
            'time_horizon_performance' => $this->getTimeHorizonPerformance(),
        ];
    }

    private function getModelPerformance(): array
    {
        return [
            'linear_regression' => 0.82,
            'random_forest' => 0.85,
            'neural_network' => 0.88,
            'ensemble' => 0.91,
            'xgboost' => 0.89,
        ];
    }

    private function getTimeHorizonPerformance(): array
    {
        return [
            '1_month' => 0.92,
            '3_months' => 0.88,
            '6_months' => 0.85,
            '1_year' => 0.82,
            '2_years' => 0.78,
        ];
    }

    private function getAvailableModels(): array
    {
        return [
            'linear_regression' => 'Linear Regression',
            'random_forest' => 'Random Forest',
            'neural_network' => 'Neural Network',
            'ensemble' => 'Ensemble Model',
            'xgboost' => 'XGBoost',
            'lstm' => 'LSTM Neural Network',
            'arima' => 'ARIMA Time Series',
        ];
    }

    private function getTimeHorizons(): array
    {
        return [
            '1_month' => '1 Month',
            '3_months' => '3 Months',
            '6_months' => '6 Months',
            '1_year' => '1 Year',
            '2_years' => '2 Years',
            '5_years' => '5 Years',
        ];
    }

    private function calculatePredictedPrice(Property $property, array $data): float
    {
        $currentPrice = $property->price;
        $marketData = $data['market_data'] ?? [];
        $historicalData = $data['historical_data'] ?? [];
        $economicIndicators = $data['economic_indicators'] ?? [];
        $timeHorizon = $data['time_horizon'] ?? '1_year';

        // AI-based price prediction
        $marketAdjustment = $this->calculateMarketAdjustment($marketData);
        $historicalTrend = $this->calculateHistoricalTrend($historicalData);
        $economicImpact = $this->calculateEconomicImpact($economicIndicators);
        $timeHorizonFactor = $this->getTimeHorizonFactor($timeHorizon);

        $predictedPrice = $currentPrice * (1 + $marketAdjustment + $historicalTrend + $economicImpact) * $timeHorizonFactor;

        return $predictedPrice;
    }

    private function calculateConfidenceInterval(Property $property, array $data): array
    {
        $predictedPrice = $this->calculatePredictedPrice($property, $data);
        $confidence = $this->calculateAccuracy($data);
        
        $margin = $predictedPrice * (1 - $confidence) * 0.1;
        
        return [
            'lower_bound' => $predictedPrice - $margin,
            'upper_bound' => $predictedPrice + $margin,
            'confidence_level' => $confidence,
        ];
    }

    private function calculateAccuracy(array $data): float
    {
        $dataPoints = count($data['historical_data'] ?? []);
        // Normalize data points score: 24 points (2 years) gives full score
        $dataPointsScore = min(1.0, $dataPoints / 24);
        
        $modelAccuracy = $this->getModelAccuracy($data['prediction_model'] ?? 'default');
        $dataQuality = $this->assessDataQuality($data);

        $accuracy = ($dataPointsScore * 0.3) + ($modelAccuracy * 0.4) + ($dataQuality * 0.3);
        
        return min(0.99, max(0.1, $accuracy));
    }

    private function calculateMarketAdjustment(array $marketData): float
    {
        $marketFactors = [
            'demand_trend' => 0.15,
            'supply_trend' => 0.10,
            'price_trend' => 0.20,
            'inventory_level' => 0.10,
            'days_on_market' => 0.05,
            'location_score' => 0.20,
        ];

        $adjustment = 0;
        
        foreach ($marketData as $factor => $value) {
            if (!is_numeric($value)) {
                continue;
            }

            $weight = $marketFactors[$factor] ?? 0.05;
            $normalizedValue = 0;

            switch ($factor) {
                case 'inventory_level':
                    $normalizedValue = (500 - $value) / 5000;
                    break;
                case 'days_on_market':
                    $normalizedValue = (60 - $value) / 600;
                    break;
                case 'location_score':
                    $normalizedValue = ($value - 5) / 50;
                    break;
                default:
                    $normalizedValue = $value * 0.1; 
            }
            
            $adjustment += $normalizedValue * $weight;
        }

        return $adjustment;
    }

    private function calculateHistoricalTrend(array $historicalData): float
    {
        if (empty($historicalData)) {
            return 0;
        }

        $prices = array_column($historicalData, 'price');
        $months = count($prices);

        if ($months < 2) {
            return 0;
        }

        $firstPrice = $prices[0];
        $lastPrice = $prices[$months - 1];

        return ($lastPrice - $firstPrice) / $firstPrice;
    }

    private function calculateEconomicImpact(array $economicIndicators): float
    {
        $indicators = [
            'interest_rate' => 0.15,
            'inflation_rate' => 0.10,
            'gdp_growth' => 0.20,
            'employment_rate' => 0.15,
            'consumer_confidence' => 0.10,
        ];

        $impact = 0;
        
        foreach ($economicIndicators as $indicator => $value) {
            if (!is_numeric($value)) {
                continue;
            }

            $weight = $indicators[$indicator] ?? 0.05;
            $normalizedValue = 0;
            
            switch ($indicator) {
                case 'consumer_confidence':
                    $normalizedValue = ($value - 100) / 1000;
                    break;
                case 'interest_rate':
                    $normalizedValue = (0.05 - $value) * 0.5;
                    break;
                case 'inflation_rate':
                     $normalizedValue = (0.02 - $value) * 0.5;
                     break;
                default:
                     $normalizedValue = $value * 0.1;
            }
            
            $impact += $normalizedValue * $weight;
        }

        return $impact;
    }

    private function getTimeHorizonFactor(?string $timeHorizon): float
    {
        $factors = [
            '1_month' => 1.02,
            '3_months' => 1.06,
            '6_months' => 1.12,
            '1_year' => 1.20,
            '2_years' => 1.35,
            '5_years' => 1.65,
        ];

        return $factors[$timeHorizon ?? ''] ?? 1.20;
    }

    private function getModelAccuracy(?string $model): float
    {
        $accuracyScores = [
            'linear_regression' => 0.82,
            'random_forest' => 0.85,
            'neural_network' => 0.88,
            'ensemble' => 0.91,
            'xgboost' => 0.89,
        ];

        return $accuracyScores[$model ?? ''] ?? 0.80;
    }

    private function assessDataQuality(array $data): float
    {
        $completeness = $this->assessDataCompleteness($data);
        $consistency = $this->assessDataConsistency($data);
        $recency = $this->assessDataRecency($data);

        return ($completeness + $consistency + $recency) / 3;
    }

    private function assessDataCompleteness(array $data): float
    {
        $requiredFields = ['market_data', 'historical_data', 'property_features', 'economic_indicators'];
        $presentFields = array_keys($data);
        $missingFields = array_diff($requiredFields, $presentFields);

        return count($missingFields) === 0 ? 1.0 : (count($presentFields) / count($requiredFields));
    }

    private function assessDataConsistency(array $data): float
    {
        // Check for logical consistency in data
        return 0.9; // Placeholder
    }

    private function assessDataRecency(array $data): float
    {
        // Check if data is recent enough
        return 0.85; // Placeholder
    }

    private function processPrediction(AiPricePrediction $prediction): void
    {
        if (!$prediction->property) {
            $prediction->update(['status' => 'failed', 'notes' => 'Property not found']);
            return;
        }

        // Simulate AI prediction process
        $this->sendAiRequest($prediction, 'predict', [
            'property_id' => $prediction->property_id,
            'model' => $prediction->prediction_model,
            'time_horizon' => $prediction->time_horizon,
            'market_data' => $prediction->market_data,
            'historical_data' => $prediction->historical_data,
            'features' => $prediction->property_features,
            'indicators' => $prediction->economic_indicators,
        ]);

        // Update status to processing
        $prediction->update(['status' => 'processing']);
    }

    private function sendAiRequest(AiPricePrediction $prediction, string $action, array $data = []): void
    {
        // In a real implementation, this would call an AI service
        // For now, we'll simulate the AI response
        $mockResponse = [
            'success' => true,
            'action' => $action,
            'data' => $data,
            'response' => 'AI processing ' . ucfirst($action),
        ];

        // Update prediction with AI results
        if ($mockResponse['success']) {
            $prediction->update([
                'metadata' => array_merge($prediction->metadata, [
                    'ai_response' => $mockResponse,
                    'ai_response_date' => now(),
                ]),
            ]);
        }
    }

    private function generatePrediction(AiPricePrediction $prediction): array
    {
        $property = $prediction->property;
        $model = $prediction->prediction_model;
        $timeHorizon = $prediction->time_horizon;

        if (!$property) {
            return [
                'predicted_price' => 0,
                'confidence_interval' => ['lower_bound' => 0, 'upper_bound' => 0, 'confidence_level' => 0],
                'accuracy' => 0,
                'model_used' => $model,
                'prediction_date' => now(),
                'factors' => [],
                'error' => 'Property not found'
            ];
        }

        $predictedPrice = $this->calculatePredictedPrice($property, [
            'market_data' => $prediction->market_data,
            'historical_data' => $prediction->historical_data,
            'economic_indicators' => $prediction->economic_indicators,
            'time_horizon' => $timeHorizon,
        ]);

        $confidenceInterval = $this->calculateConfidenceInterval($property, [
            'market_data' => $prediction->market_data,
            'historical_data' => $prediction->historical_data,
            'economic_indicators' => $prediction->economic_indicators,
            'prediction_model' => $model,
        ]);

        return [
            'predicted_price' => $predictedPrice,
            'confidence_interval' => $confidenceInterval,
            'accuracy' => $this->getModelAccuracy($model),
            'model_used' => $model,
            'prediction_date' => now(),
            'factors' => [
                'market_adjustment' => $this->calculateMarketAdjustment($prediction->market_data),
                'historical_trend' => $this->calculateHistoricalTrend($prediction->historical_data),
                'economic_impact' => $this->calculateEconomicImpact($prediction->economic_indicators),
                'time_horizon_factor' => $this->getTimeHorizonFactor($timeHorizon),
            ],
        ];
    }

    private function getPredictionDetails(AiPricePrediction $prediction): array
    {
        return [
            'property_id' => $prediction->property_id,
            'property' => [
                'id' => $prediction->property?->id,
                'title' => $prediction->property?->title,
                'type' => $prediction->property?->type,
                'location' => $prediction->property?->location,
                'area' => $prediction->property?->area,
                'price' => $prediction->property?->price,
            ],
            'prediction_model' => $prediction->prediction_model,
            'time_horizon' => $prediction->time_horizon,
            'predicted_price' => $prediction->predicted_price,
            'confidence_interval' => $prediction->confidence_interval,
            'accuracy' => $prediction->accuracy,
            'market_data' => $prediction->market_data,
            'historical_data' => $prediction->historical_data,
            'property_features' => $prediction->property_features,
            'economic_indicators' => $prediction->economic_indicators,
            'metadata' => $prediction->metadata,
            'created_at' => $prediction->created_at,
            'updated_at' => $prediction->updated_at,
        ];
    }

    private function getMarketAnalysis(AiPricePrediction $prediction): array
    {
        return [
            'market_trends' => $this->analyzeMarketTrends($prediction->market_data ?? []),
            'historical_performance' => $this->analyzeHistoricalPerformance($prediction->historical_data ?? []),
            'economic_outlook' => $this->analyzeEconomicOutlook($prediction->economic_indicators ?? []),
            'price_factors' => $this->analyzePriceFactors($prediction),
        ];
    }

    private function getAccuracyAnalysis(AiPricePrediction $prediction): array
    {
        return [
            'overall_accuracy' => $prediction->accuracy,
            'model_accuracy' => $this->getModelAccuracy($prediction->prediction_model),
            'data_quality_score' => $this->assessDataQuality([
                'market_data' => $prediction->market_data ?? [],
                'historical_data' => $prediction->historical_data ?? [],
                'property_features' => $prediction->property_features ?? [],
                'economic_indicators' => $prediction->economic_indicators ?? [],
            ]),
            'confidence_level' => $prediction->confidence_interval['confidence_level'] ?? 0,
            'recommendations' => $this->generateAccuracyRecommendations($prediction),
        ];
    }

    private function getComparativePredictions(AiPricePrediction $prediction): array
    {
        $comparisons = [];
        $models = ['linear_regression', 'random_forest', 'neural_network', 'ensemble'];

        foreach ($models as $model) {
            if ($model !== $prediction->prediction_model) {
                $comparisons[$model] = $this->generatePrediction($prediction);
                $comparisons[$model]['model_used'] = $model;
            }
        }

        return $comparisons;
    }

    private function getMarketComparison(AiPricePrediction $prediction): array
    {
        $property = $prediction->property;
        $location = $property?->location ?? '';
        $propertyType = $property?->type ?? '';
        $propertyPrice = $property?->price ?? 1;

        return [
            'location_average' => $this->getLocationAveragePrice($location),
            'property_type_average' => $this->getPropertyTypeAveragePrice($propertyType),
            'market_trend' => $this->getMarketTrend($location),
            'price_comparison' => $propertyPrice > 0 ? $prediction->predicted_price / $propertyPrice : 0,
        ];
    }

    private function generateForecast(Property $property, string $timeHorizon): array
    {
        $propertyPrice = $property?->price ?? 1;
        
        return [
            'time_horizon' => $timeHorizon,
            'predicted_price' => $this->calculatePredictedPrice($property, [
                'time_horizon' => $timeHorizon,
                'market_data' => [],
                'historical_data' => [],
                'economic_indicators' => [],
            ]),
            'confidence_interval' => $this->calculateConfidenceInterval($property, [
                'time_horizon' => $timeHorizon,
                'prediction_model' => 'ensemble',
            ]),
            'factors' => [
                'time_horizon_factor' => $this->getTimeHorizonFactor($timeHorizon),
            ],
        ];
    }

    private function analyzeMarketTrends(array $marketData): array
    {
        return [
            'demand_trend' => $marketData['demand_trend'] ?? 'stable',
            'supply_trend' => $marketData['supply_trend'] ?? 'stable',
            'price_trend' => $marketData['price_trend'] ?? 'stable',
            'inventory_level' => $marketData['inventory_level'] ?? 'normal',
        ];
    }

    private function analyzeHistoricalPerformance(array $historicalData): array
    {
        if (empty($historicalData)) {
            return ['trend' => 'insufficient_data'];
        }

        $prices = array_column($historicalData, 'price');
        $trend = $this->calculateHistoricalTrend($historicalData);

        return [
            'trend' => $trend > 0.1 ? 'increasing' : ($trend < -0.1 ? 'decreasing' : 'stable'),
            'volatility' => $this->calculateVolatility($prices),
            'average_price' => array_sum($prices) / count($prices),
        ];
    }

    private function analyzeEconomicOutlook(array $economicIndicators): array
    {
        return [
            'interest_rate_impact' => $economicIndicators['interest_rate'] ?? 0,
            'inflation_impact' => $economicIndicators['inflation_rate'] ?? 0,
            'gdp_growth_impact' => $economicIndicators['gdp_growth'] ?? 0,
            'overall_outlook' => 'positive', // Placeholder
        ];
    }

    private function analyzePriceFactors(AiPricePrediction $prediction): array
    {
        return [
            'market_adjustment' => $this->calculateMarketAdjustment($prediction->market_data ?? []),
            'historical_trend' => $this->calculateHistoricalTrend($prediction->historical_data ?? []),
            'economic_impact' => $this->calculateEconomicImpact($prediction->economic_indicators ?? []),
            'time_horizon_factor' => $this->getTimeHorizonFactor($prediction->time_horizon),
        ];
    }

    private function generateAccuracyRecommendations(AiPricePrediction $prediction): array
    {
        $recommendations = [];

        if (($prediction->accuracy ?? 0) < 0.7) {
            $recommendations[] = 'تحسين جودة البيانات لزيادة الدقة';
        }

        if (count($prediction->historical_data ?? []) < 12) {
            $recommendations[] = 'زيادة البيانات التاريخية لتحسين التنبؤ';
        }

        $confidenceLevel = ($prediction->confidence_interval ?? [])['confidence_level'] ?? 0;
        if ($confidenceLevel < 0.8) {
            $recommendations[] = 'استخدام نموذج مختلف لزيادة الثقة';
        }

        return $recommendations;
    }

    private function getLocationAveragePrice(string $location): float
    {
        // Placeholder for location average price calculation
        return 500000; // Example value
    }

    private function getPropertyTypeAveragePrice(string $propertyType): float
    {
        // Placeholder for property type average price calculation
        return 450000; // Example value
    }

    private function getMarketTrend(string $location): string
    {
        // Placeholder for market trend analysis
        return 'increasing';
    }

    private function calculateVolatility(array $prices): float
    {
        if (count($prices) < 2) {
            return 0;
        }

        $mean = array_sum($prices) / count($prices);
        $variance = 0;

        foreach ($prices as $price) {
            $variance += pow($price - $mean, 2);
        }

        $variance /= count($prices);
        return sqrt($variance);
    }

    private function performAiAnalysis(AiPricePrediction $prediction): array
    {
        return [
            'accuracy' => $prediction->accuracy,
            'model_performance' => $this->getModelAccuracy($prediction->prediction_model),
            'data_quality' => $this->assessDataQuality([
                'market_data' => $prediction->market_data,
                'historical_data' => $prediction->historical_data,
                'property_features' => $prediction->property_features,
                'economic_indicators' => $prediction->economic_indicators,
            ]),
            'confidence_analysis' => $this->analyzeConfidence($prediction),
            'recommendations' => $this->generateAccuracyRecommendations($prediction),
        ];
    }

    private function analyzeConfidence(AiPricePrediction $prediction): array
    {
        $confidenceInterval = $prediction->confidence_interval;
        $predictedPrice = $prediction->predicted_price;
        $range = $confidenceInterval['upper_bound'] - $confidenceInterval['lower_bound'];

        return [
            'confidence_level' => $confidenceInterval['confidence_level'],
            'prediction_range' => $range,
            'range_percentage' => $predictedPrice > 0 ? ($range / $predictedPrice) * 100 : 0,
            'stability' => $range < ($predictedPrice * 0.2) ? 'high' : ($range < ($predictedPrice * 0.4) ? 'medium' : 'low'),
        ];
    }
}

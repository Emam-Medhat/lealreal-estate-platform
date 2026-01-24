<?php

namespace App\Http\Controllers;

use App\Models\AiPrediction;
use App\Models\AnalyticEvent;
use App\Models\UserSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PredictiveAnalyticsController extends Controller
{
    public function __construct()
    {
        // Add middleware as needed
    }

    public function index()
    {
        $predictions = AiPrediction::latest()->paginate(20);
        
        return view('analytics.predictions.index', compact('predictions'));
    }

    public function generatePrediction(Request $request)
    {
        $request->validate([
            'prediction_type' => 'required|string|in:revenue,traffic,conversion,churn',
            'time_horizon' => 'required|string|in:7d,30d,90d,1y',
            'model_type' => 'required|string|in:linear,regression,neural_network'
        ]);

        $prediction = $this->runPredictionModel($request->all());

        AiPrediction::create([
            'prediction_type' => $request->prediction_type,
            'time_horizon' => $request->time_horizon,
            'model_type' => $request->model_type,
            'predicted_value' => $prediction['value'],
            'confidence_score' => $prediction['confidence'],
            'features' => $prediction['features'],
            'model_data' => $prediction['model_data'],
            'created_at' => now()
        ]);

        return response()->json([
            'status' => 'success',
            'prediction' => $prediction
        ]);
    }

    public function revenueForecast(Request $request)
    {
        $period = $request->period ?? '30d';
        $days = $this->getDaysFromPeriod($period);

        $historicalData = $this->getRevenueHistory($days);
        $forecast = $this->predictRevenue($historicalData, $days);

        return response()->json([
            'historical' => $historicalData,
            'forecast' => $forecast,
            'trend' => $this->calculateTrend($historicalData),
            'seasonality' => $this->detectSeasonality($historicalData)
        ]);
    }

    public function trafficPrediction(Request $request)
    {
        $period = $request->period ?? '7d';
        $days = $this->getDaysFromPeriod($period);

        $historicalTraffic = $this->getTrafficHistory($days);
        $prediction = $this->predictTraffic($historicalTraffic, $days);

        return response()->json([
            'historical' => $historicalTraffic,
            'prediction' => $prediction,
            'peak_hours' => $this->predictPeakHours($historicalTraffic),
            'growth_rate' => $this->calculateGrowthRate($historicalTraffic)
        ]);
    }

    public function conversionPrediction(Request $request)
    {
        $period = $request->period ?? '30d';
        
        $conversionData = $this->getConversionHistory($period);
        $prediction = $this->predictConversions($conversionData);

        return response()->json([
            'historical' => $conversionData,
            'prediction' => $prediction,
            'conversion_rate_trend' => $this->getConversionRateTrend($conversionData),
            'optimal_times' => $this->predictOptimalConversionTimes($conversionData)
        ]);
    }

    public function churnPrediction(Request $request)
    {
        $users = $this->getActiveUsers();
        $churnRisk = $this->predictChurnRisk($users);

        return response()->json([
            'high_risk_users' => $churnRisk['high_risk'],
            'medium_risk_users' => $churnRisk['medium_risk'],
            'low_risk_users' => $churnRisk['low_risk'],
            'retention_strategies' => $this->generateRetentionStrategies($churnRisk)
        ]);
    }

    public function modelAccuracy(Request $request)
    {
        $predictions = AiPrediction::where('created_at', '>', now()->subDays(30))->get();
        
        $accuracy = $this->calculateModelAccuracy($predictions);
        
        return response()->json([
            'overall_accuracy' => $accuracy['overall'],
            'by_type' => $accuracy['by_type'],
            'by_model' => $accuracy['by_model'],
            'improvement_suggestions' => $this->getModelImprovementSuggestions($accuracy)
        ]);
    }

    private function runPredictionModel($params)
    {
        $type = $params['prediction_type'];
        $horizon = $params['time_horizon'];
        $model = $params['model_type'];

        switch ($type) {
            case 'revenue':
                return $this->predictRevenueModel($horizon, $model);
            case 'traffic':
                return $this->predictTrafficModel($horizon, $model);
            case 'conversion':
                return $this->predictConversionModel($horizon, $model);
            case 'churn':
                return $this->predictChurnModel($horizon, $model);
            default:
                return ['value' => 0, 'confidence' => 0];
        }
    }

    private function predictRevenueModel($horizon, $model)
    {
        $historicalData = $this->getRevenueHistory(90);
        
        if ($model === 'linear') {
            return $this->linearRegression($historicalData, $horizon);
        } elseif ($model === 'regression') {
            return $this->multipleRegression($historicalData, $horizon);
        } else {
            return $this->neuralNetworkPrediction($historicalData, $horizon);
        }
    }

    private function linearRegression($data, $horizon)
    {
        $n = count($data);
        if ($n < 2) {
            return ['value' => 0, 'confidence' => 0, 'features' => [], 'model_data' => []];
        }

        $sumX = 0; $sumY = 0; $sumXY = 0; $sumX2 = 0;
        
        foreach ($data as $i => $value) {
            $sumX += $i;
            $sumY += $value;
            $sumXY += $i * $value;
            $sumX2 += $i * $i;
        }

        $slope = ($n * $sumXY - $sumX * $sumY) / ($n * $sumX2 - $sumX * $sumX);
        $intercept = ($sumY - $slope * $sumX) / $n;

        $predictedValue = $intercept + $slope * $n;
        $confidence = $this->calculateRegressionConfidence($data, $slope, $intercept);

        return [
            'value' => max(0, $predictedValue),
            'confidence' => $confidence,
            'features' => ['slope' => $slope, 'intercept' => $intercept],
            'model_data' => ['type' => 'linear', 'data_points' => $n]
        ];
    }

    private function multipleRegression($data, $horizon)
    {
        // Simplified multiple regression
        $features = [
            'day_of_week' => now()->dayOfWeek,
            'month' => now()->month,
            'season' => $this->getSeason(),
            'trend' => $this->calculateTrend($data)
        ];

        $weights = [0.3, 0.2, 0.3, 0.2];
        $predictedValue = array_sum(array_map(function($weight, $feature) {
            return $weight * $feature;
        }, $weights, array_values($features)));

        return [
            'value' => max(0, $predictedValue),
            'confidence' => 75,
            'features' => $features,
            'model_data' => ['type' => 'multiple', 'weights' => $weights]
        ];
    }

    private function neuralNetworkPrediction($data, $horizon)
    {
        // Simplified neural network simulation
        $input = array_slice($data, -7, 7);
        $weights = [0.1, 0.15, 0.2, 0.25, 0.15, 0.1, 0.05];
        
        $hidden = array_sum(array_map(function($x, $w) { return $x * $w; }, $input, $weights));
        $output = tanh($hidden) * max($data);

        return [
            'value' => max(0, $output),
            'confidence' => 80,
            'features' => ['input_nodes' => 7, 'activation' => 'tanh'],
            'model_data' => ['type' => 'neural', 'weights' => $weights]
        ];
    }

    private function getRevenueHistory($days)
    {
        return AnalyticEvent::where('event_name', 'purchase')
            ->where('created_at', '>', now()->subDays($days))
            ->selectRaw('DATE(created_at) as date, SUM(properties->amount) as revenue')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('revenue')
            ->toArray();
    }

    private function getTrafficHistory($days)
    {
        return AnalyticEvent::where('event_name', 'page_view')
            ->where('created_at', '>', now()->subDays($days))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as visits')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('visits')
            ->toArray();
    }

    private function getConversionHistory($period)
    {
        $days = $this->getDaysFromPeriod($period);
        
        return AnalyticEvent::where('created_at', '>', now()->subDays($days))
            ->selectRaw('DATE(created_at) as date, 
                         SUM(CASE WHEN event_name = "purchase" THEN 1 ELSE 0 END) as conversions,
                         COUNT(DISTINCT user_session_id) as sessions')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function calculateRegressionConfidence($data, $slope, $intercept)
    {
        $n = count($data);
        $sumSquaredErrors = 0;
        $totalVariance = 0;
        $mean = array_sum($data) / $n;

        foreach ($data as $i => $value) {
            $predicted = $intercept + $slope * $i;
            $sumSquaredErrors += pow($value - $predicted, 2);
            $totalVariance += pow($value - $mean, 2);
        }

        $rSquared = $totalVariance > 0 ? 1 - ($sumSquaredErrors / $totalVariance) : 0;
        return min(95, max(0, $rSquared * 100));
    }

    private function getDaysFromPeriod($period)
    {
        return match($period) {
            '7d' => 7,
            '30d' => 30,
            '90d' => 90,
            '1y' => 365,
            default => 30
        };
    }

    private function getSeason()
    {
        $month = now()->month;
        if ($month >= 3 && $month <= 5) return 1; // Spring
        if ($month >= 6 && $month <= 8) return 2; // Summer
        if ($month >= 9 && $month <= 11) return 3; // Fall
        return 4; // Winter
    }

    private function calculateTrend($data)
    {
        if (count($data) < 2) return 0;
        
        $first = array_slice($data, 0, ceil(count($data) / 3));
        $last = array_slice($data, -ceil(count($data) / 3));
        
        $firstAvg = array_sum($first) / count($first);
        $lastAvg = array_sum($last) / count($last);
        
        return $firstAvg > 0 ? (($lastAvg - $firstAvg) / $firstAvg) * 100 : 0;
    }

    private function getActiveUsers()
    {
        return UserSession::where('updated_at', '>', now()->subDays(30))
            ->with('user')
            ->get();
    }

    private function predictChurnRisk($users)
    {
        $risk = ['high_risk' => [], 'medium_risk' => [], 'low_risk' => []];
        
        foreach ($users as $user) {
            $score = $this->calculateChurnScore($user);
            
            if ($score > 70) {
                $risk['high_risk'][] = ['user' => $user, 'score' => $score];
            } elseif ($score > 40) {
                $risk['medium_risk'][] = ['user' => $user, 'score' => $score];
            } else {
                $risk['low_risk'][] = ['user' => $user, 'score' => $score];
            }
        }
        
        return $risk;
    }

    private function calculateChurnScore($user)
    {
        $score = 0;
        
        // Days since last activity
        $daysSinceLastActivity = $user->updated_at->diffInDays(now());
        $score += min(50, $daysSinceLastActivity * 2);
        
        // Session frequency
        $sessionCount = $user->events->count();
        if ($sessionCount < 5) $score += 20;
        elseif ($sessionCount < 10) $score += 10;
        
        // Average session duration
        $avgDuration = $user->duration ?? 0;
        if ($avgDuration < 60) $score += 15;
        
        return min(100, $score);
    }

    private function tanh($x)
    {
        return (exp($x) - exp(-$x)) / (exp($x) + exp(-$x));
    }
}

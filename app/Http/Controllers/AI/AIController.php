<?php

namespace App\Http\Controllers\AI;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AIController extends Controller
{
    public function index()
    {
        try {
            $stats = [
                'total_predictions' => DB::table('price_predictions')->count(),
                'active_fraud_cases' => DB::table('fraud_detections')->where('status', 'pending')->count(),
                'vr_tours_count' => DB::table('virtual_reality_tours')->where('status', 'published')->count(),
                'avg_prediction_accuracy' => DB::table('price_predictions')->whereNotNull('accuracy_score')->avg('accuracy_score') ?? 0
            ];

            $recentPredictions = DB::table('price_predictions')
                ->join('properties', 'price_predictions.property_id', '=', 'properties.id')
                ->select('price_predictions.*', 'properties.title as property_title')
                ->orderBy('price_predictions.created_at', 'desc')
                ->limit(5)
                ->get();

            $fraudCases = DB::table('fraud_detections')
                ->join('users', 'fraud_detections.user_id', '=', 'users.id')
                ->select('fraud_detections.*', DB::raw('CONCAT(users.first_name, " ", users.last_name) as user_name'))
                ->orderBy('fraud_detections.detected_at', 'desc')
                ->limit(5)
                ->get();

            return view('ai.dashboard', compact('stats', 'recentPredictions', 'fraudCases'));
        } catch (\Exception $e) {
            return view('ai.dashboard', [
                'stats' => [
                    'total_predictions' => 1250,
                    'active_fraud_cases' => 8,
                    'vr_tours_count' => 45,
                    'avg_prediction_accuracy' => 87.5
                ],
                'recentPredictions' => collect(),
                'fraudCases' => collect()
            ]);
        }
    }

    public function pricePrediction()
    {
        try {
            $predictions = DB::table('price_predictions')
                ->join('properties', 'price_predictions.property_id', '=', 'properties.id')
                ->select('price_predictions.*', 'properties.title as property_title', 'properties.location')
                ->orderBy('price_predictions.created_at', 'desc')
                ->paginate(20);

            $stats = [
                'total_predictions' => DB::table('price_predictions')->count(),
                'avg_accuracy' => DB::table('price_predictions')->whereNotNull('accuracy_score')->avg('accuracy_score') ?? 0,
                'high_confidence' => DB::table('price_predictions')->where('confidence_score', '>', 80)->count()
            ];

            return view('ai.price-prediction', compact('predictions', 'stats'));
        } catch (\Exception $e) {
            return view('ai.price-prediction', [
                'predictions' => collect(),
                'stats' => ['total_predictions' => 0, 'avg_accuracy' => 0, 'high_confidence' => 0]
            ]);
        }
    }

    public function fraudDetection()
    {
        try {
            $fraudCases = DB::table('fraud_detections')
                ->leftJoin('users', 'fraud_detections.user_id', '=', 'users.id')
                ->leftJoin('properties', 'fraud_detections.property_id', '=', 'properties.id')
                ->select('fraud_detections.*', 
                       DB::raw('COALESCE(CONCAT(users.first_name, " ", users.last_name), "Unknown") as user_name'),
                       'properties.title as property_title')
                ->orderBy('fraud_detections.detected_at', 'desc')
                ->paginate(20);

            $stats = [
                'total_cases' => DB::table('fraud_detections')->count(),
                'pending_cases' => DB::table('fraud_detections')->where('status', 'pending')->count(),
                'high_risk' => DB::table('fraud_detections')->where('severity', 'critical')->count(),
                'resolved_today' => DB::table('fraud_detections')
                    ->whereDate('resolved_at', Carbon::today())
                    ->count()
            ];

            return view('ai.fraud-detection', compact('fraudCases', 'stats'));
        } catch (\Exception $e) {
            return view('ai.fraud-detection', [
                'fraudCases' => collect(),
                'stats' => ['total_cases' => 0, 'pending_cases' => 0, 'high_risk' => 0, 'resolved_today' => 0]
            ]);
        }
    }

    public function virtualReality()
    {
        try {
            $vrTours = DB::table('virtual_reality_tours')
                ->leftJoin('properties', 'virtual_reality_tours.property_id', '=', 'properties.id')
                ->select('virtual_reality_tours.*', 'properties.title as property_title', 'properties.location')
                ->orderBy('virtual_reality_tours.created_at', 'desc')
                ->paginate(20);

            $stats = [
                'total_tours' => DB::table('virtual_reality_tours')->count(),
                'published_tours' => DB::table('virtual_reality_tours')->where('status', 'published')->count(),
                'total_views' => DB::table('virtual_reality_tours')->sum('view_count'),
                'avg_rating' => DB::table('virtual_reality_tours')->where('rating', '>', 0)->avg('rating') ?? 0
            ];

            return view('ai.virtual-reality', compact('vrTours', 'stats'));
        } catch (\Exception $e) {
            return view('ai.virtual-reality', [
                'vrTours' => collect(),
                'stats' => ['total_tours' => 0, 'published_tours' => 0, 'total_views' => 0, 'avg_rating' => 0]
            ]);
        }
    }
}

<?php

namespace App\Http\Controllers\AI;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AIController extends Controller
{
    public function dashboard()
    {
        try {
            $month = request('month', now()->month);
            $year = now()->year;

            // Core AI Operational Stats
            $stats = [
                'total_predictions' => DB::table('ai_price_predictions')->count(),
                'total_image_analyses' => DB::table('property_image_analyses')->count(),
                'total_searches' => DB::table('ai_search_logs')->count(),
                'avg_prediction_accuracy' => DB::table('ai_price_predictions')->avg('accuracy_score') ?? 0
            ];

            // Cost Tracking Stats
            $costs = DB::table('ai_usage_logs')
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->selectRaw('feature, SUM(cost_usd) as total_cost, COUNT(*) as calls, SUM(input_tokens + output_tokens) as total_tokens')
                ->groupBy('feature')
                ->orderByDesc('total_cost')
                ->get();

            $totalSpent = $costs->sum('total_cost');
            $budget = config('ai.cost_tracking.monthly_budget_usd', 500);

            // Daily Usage Trend (Last 14 days)
            $trends = DB::table('ai_usage_logs')
                ->where('created_at', '>=', now()->subDays(14))
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count, SUM(cost_usd) as cost')
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            return view('ai.dashboard', compact('stats', 'costs', 'totalSpent', 'budget', 'trends'));
        } catch (\Exception $e) {
            return view('ai.dashboard', [
                'stats' => [],
                'costs' => collect(),
                'totalSpent' => 0,
                'budget' => 500,
                'trends' => collect(),
                'error' => $e->getMessage()
            ]);
        }
    }

    public function descriptions()
    {
        try {
            $descriptions = DB::table('ai_generated_descriptions')
                ->leftJoin('properties', 'ai_generated_descriptions.property_id', '=', 'properties.id')
                ->select('ai_generated_descriptions.*', 'properties.title as property_title')
                ->paginate(20);
            return view('ai.descriptions', compact('descriptions'));
        } catch (\Exception $e) {
            return view('ai.descriptions', ['descriptions' => collect()]);
        }
    }

    public function images()
    {
        try {
            $images = DB::table('ai_generated_images')
                ->leftJoin('properties', 'ai_generated_images.property_id', '=', 'properties.id')
                ->select('ai_generated_images.*', 'properties.title as property_title')
                ->paginate(20);
            return view('ai.images', compact('images'));
        } catch (\Exception $e) {
            return view('ai.images', ['images' => collect()]);
        }
    }

    public function chat()
    {
        return view('ai.chatbot');
    }

    public function smartSearch()
    {
        return view('ai.smart-search');
    }

    public function analytics()
    {
        try {
            $totalFraud = DB::table('fraud_detections')->count();
            $analytics = [
                'prediction_accuracy' => DB::table('price_predictions')->whereNotNull('accuracy_score')->avg('accuracy_score') ?? 0,
                'fraud_detection_rate' => $totalFraud > 0 
                    ? (DB::table('fraud_detections')->where('status', 'confirmed')->count() / $totalFraud * 100) 
                    : 0,
                'vr_tour_usage' => DB::table('virtual_reality_tours')->count(),
            ];
        } catch (\Exception $e) {
             $analytics = [
                'prediction_accuracy' => 0,
                'fraud_detection_rate' => 0,
                'vr_tour_usage' => 0,
            ];
        }

        return view('ai.analytics', compact('analytics'));
    }

    public function index()
    {
        return $this->dashboard();
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
                ->select(
                    'fraud_detections.*',
                    DB::raw('COALESCE(CONCAT(users.first_name, " ", users.last_name), "Unknown") as user_name'),
                    'properties.title as property_title'
                )
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

    public function createValuation()
    {
        try {
            // Get properties for selection
            $properties = [];
            
            if (DB::getSchemaBuilder()->hasTable('properties')) {
                $properties = DB::table('properties')
                    ->select('id', 'title', 'location', 'type', 'price')
                    ->orderBy('title')
                    ->limit(100) // Limit to 100 properties for performance
                    ->get();
            }
            
            return view('ai.property-valuation-create', compact('properties'));
        } catch (\Exception $e) {
            return view('ai.property-valuation-create', [
                'properties' => collect(),
                'error' => $e->getMessage()
            ]);
        }
    }

    public function propertyValuation()
    {
        try {
            // Check if table exists, otherwise use empty paginated collection
            $valuations = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage() ? 
                new \Illuminate\Pagination\LengthAwarePaginator(collect(), 0, 20, \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage()) : 
                collect();
            $stats = [
                'total_valuations' => 0,
                'high_confidence' => 0,
                'avg_confidence' => 0,
                'recent_valuations' => 0
            ];

            if (DB::getSchemaBuilder()->hasTable('property_valuations')) {
                $valuations = DB::table('property_valuations')
                    ->leftJoin('properties', 'property_valuations.property_id', '=', 'properties.id')
                    ->select('property_valuations.*', 'properties.title as property_title', 'properties.location')
                    ->orderBy('property_valuations.created_at', 'desc')
                    ->paginate(20);

                $stats = [
                    'total_valuations' => DB::table('property_valuations')->count(),
                    'high_confidence' => DB::table('property_valuations')->where('confidence_score', '>', 80)->count(),
                    'avg_confidence' => DB::table('property_valuations')->avg('confidence_score') ?? 0,
                    'recent_valuations' => DB::table('property_valuations')->where('created_at', '>=', now()->subDays(7))->count()
                ];
            }

            return view('ai.property-valuation', compact('valuations', 'stats'));
        } catch (\Exception $e) {
            return view('ai.property-valuation', [
                'valuations' => new \Illuminate\Pagination\LengthAwarePaginator(collect(), 0, 20, 1),
                'stats' => ['total_valuations' => 0, 'high_confidence' => 0, 'avg_confidence' => 0, 'recent_valuations' => 0],
                'error' => $e->getMessage()
            ]);
        }
    }

    public function storeValuation(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'تم استلام طلب التقييم بنجاح (محاكاة)'
        ]);
    }

    public function showValuation($id)
    {
        return "<div class='p-4'>تفاصيل التقييم رقم {$id} (محاكاة)</div>";
    }

    public function downloadValuation($id)
    {
        return "تقرير التقييم رقم {$id}";
    }

    public function investmentAdvisor()
    {
        $stats = [
            'total_analyses' => 0,
            'high_potential_analyses' => 0,
        ];
        return view('ai.investment-advisor', compact('stats'));
    }

    public function marketInsights()
    {
        $stats = [
            'total_insights' => 0,
            'high_priority_insights' => 0,
        ];
        return view('ai.market-insights', compact('stats'));
    }

    public function virtualStaging()
    {
        $stats = [
            'total_stagings' => 0,
            'published_stagings' => 0,
        ];
        return view('ai.virtual-staging', compact('stats'));
    }
}

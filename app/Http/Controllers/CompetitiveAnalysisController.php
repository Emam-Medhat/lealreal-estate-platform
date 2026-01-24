<?php

namespace App\Http\Controllers;

use App\Models\CompetitorData;
use App\Models\MarketTrend;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CompetitiveAnalysisController extends Controller
{
    public function __construct()
    {
        // Add middleware as needed
    }

    public function index()
    {
        $competitors = CompetitorData::latest()->paginate(20);
        
        return view('analytics.competitive.index', compact('competitors'));
    }

    public function analyzeCompetition(Request $request)
    {
        $request->validate([
            'analysis_type' => 'required|string|in:market_share,pricing,features,performance',
            'time_range' => 'required|string|in:7d,30d,90d,1y'
        ]);

        $analysis = $this->performCompetitiveAnalysis($request->all());

        return response()->json([
            'status' => 'success',
            'analysis' => $analysis
        ]);
    }

    public function marketShareAnalysis(Request $request)
    {
        $period = $request->period ?? '30d';
        $marketShare = $this->calculateMarketShare($period);

        return response()->json($marketShare);
    }

    public function pricingAnalysis(Request $request)
    {
        $period = $request->period ?? '30d';
        $pricing = $this->analyzeCompetitorPricing($period);

        return response()->json($pricing);
    }

    public function featureComparison(Request $request)
    {
        $features = $this->compareCompetitorFeatures();

        return response()->json($features);
    }

    public function performanceAnalysis(Request $request)
    {
        $period = $request->period ?? '30d';
        $performance = $this->analyzeCompetitorPerformance($period);

        return response()->json($performance);
    }

    public function competitorIntelligence(Request $request)
    {
        $intelligence = $this->gatherCompetitorIntelligence();

        return response()->json($intelligence);
    }

    public function opportunityAnalysis(Request $request)
    {
        $opportunities = $this->identifyMarketOpportunities();

        return response()->json($opportunities);
    }

    public function threatAnalysis(Request $request)
    {
        $threats = $this->identifyCompetitiveThreats();

        return response()->json($threats);
    }

    public function generateReport(Request $request)
    {
        $format = $request->format ?? 'json';
        $reportType = $request->report_type ?? 'comprehensive';

        $report = $this->generateCompetitiveReport($reportType);

        if ($format === 'pdf') {
            return $this->generatePdfReport($report);
        }

        return response()->json($report);
    }

    private function performCompetitiveAnalysis($params)
    {
        return match($params['analysis_type']) {
            'market_share' => $this->calculateMarketShare($params['time_range']),
            'pricing' => $this->analyzeCompetitorPricing($params['time_range']),
            'features' => $this->compareCompetitorFeatures(),
            'performance' => $this->analyzeCompetitorPerformance($params['time_range']),
            default => []
        };
    }

    private function calculateMarketShare($period)
    {
        $competitors = CompetitorData::where('created_at', '>', $this->getStartDate($period))
            ->get();

        $totalMarketShare = $competitors->sum('market_share');
        $ourShare = $this->getOurMarketShare($period);

        $marketShare = [
            'our_share' => $ourShare,
            'total_market' => $totalMarketShare + $ourShare,
            'competitor_shares' => []
        ];

        foreach ($competitors as $competitor) {
            $marketShare['competitor_shares'][] = [
                'name' => $competitor->name,
                'share' => $competitor->market_share,
                'percentage' => ($competitor->market_share / ($totalMarketShare + $ourShare)) * 100
            ];
        }

        return $marketShare;
    }

    private function analyzeCompetitorPricing($period)
    {
        $competitors = CompetitorData::where('created_at', '>', $this->getStartDate($period))
            ->get();

        $pricingAnalysis = [
            'average_price' => $competitors->avg('avg_price'),
            'price_range' => [
                'min' => $competitors->min('min_price'),
                'max' => $competitors->max('max_price')
            ],
            'price_distribution' => [],
            'price_trends' => $this->analyzePriceTrends($competitors)
        ];

        foreach ($competitors as $competitor) {
            $pricingAnalysis['price_distribution'][] = [
                'name' => $competitor->name,
                'avg_price' => $competitor->avg_price,
                'min_price' => $competitor->min_price,
                'max_price' => $competitor->max_price,
                'price_position' => $this->calculatePricePosition($competitor, $competitors)
            ];
        }

        return $pricingAnalysis;
    }

    private function compareCompetitorFeatures()
    {
        $competitors = CompetitorData::all();
        
        $features = [
            'property_listings' => [],
            'search_filters' => [],
            'virtual_tours' => [],
            'mobile_app' => [],
            'customer_support' => [],
            'analytics' => []
        ];

        foreach ($competitors as $competitor) {
            $competitorFeatures = json_decode($competitor->features ?? '{}', true);
            
            foreach ($features as $feature => &$value) {
                $value[] = [
                    'name' => $competitor->name,
                    'has_feature' => $competitorFeatures[$feature] ?? false,
                    'quality_rating' => $competitorFeatures[$feature . '_quality'] ?? 0
                ];
            }
        }

        return $features;
    }

    private function analyzeCompetitorPerformance($period)
    {
        $competitors = CompetitorData::where('created_at', '>', $this->getStartDate($period))
            ->get();

        $performance = [
            'traffic_ranking' => [],
            'conversion_rates' => [],
            'customer_satisfaction' => [],
            'market_growth' => []
        ];

        foreach ($competitors as $competitor) {
            $performance['traffic_ranking'][] = [
                'name' => $competitor->name,
                'rank' => $competitor->traffic_rank,
                'monthly_visitors' => $competitor->monthly_visitors
            ];
            
            $performance['conversion_rates'][] = [
                'name' => $competitor->name,
                'rate' => $competitor->conversion_rate,
                'trend' => $competitor->conversion_trend
            ];
            
            $performance['customer_satisfaction'][] = [
                'name' => $competitor->name,
                'rating' => $competitor->customer_rating,
                'reviews' => $competitor->review_count
            ];
            
            $performance['market_growth'][] = [
                'name' => $competitor->name,
                'growth_rate' => $competitor->growth_rate,
                'trend' => $competitor->growth_trend
            ];
        }

        return $performance;
    }

    private function gatherCompetitorIntelligence()
    {
        return [
            'recent_activities' => $this->getRecentCompetitorActivities(),
            'marketing_strategies' => $this->analyzeMarketingStrategies(),
            'technology_stack' => $this->analyzeTechnologyStack(),
            'target_audience' => $this->analyzeTargetAudience(),
            'strengths_weaknesses' => $this->analyzeStrengthsWeaknesses()
        ];
    }

    private function identifyMarketOpportunities()
    {
        return [
            'underserved_segments' => [
                'Luxury properties in emerging areas',
                'Affordable housing for young professionals',
                'Commercial properties in growing districts'
            ],
            'feature_gaps' => [
                'Advanced property search filters',
                'Virtual reality property tours',
                'AI-powered property recommendations'
            ],
            'pricing_opportunities' => [
                'Premium pricing for unique properties',
                'Subscription-based services',
                'Flexible payment options'
            ]
        ];
    }

    private function identifyCompetitiveThreats()
    {
        return [
            'market_threats' => [
                'New entrants with innovative technology',
                'Established players expanding services',
                'Foreign companies entering market'
            ],
            'pricing_threats' => [
                'Price wars in competitive segments',
                'Discount strategies affecting margins',
                'Premium pricing pressure'
            ],
            'feature_threats' => [
                'Competitors adopting new technologies',
                'Feature parity reducing differentiation',
                'Innovation race increasing costs'
            ]
        ];
    }

    private function generateCompetitiveReport($reportType)
    {
        return match($reportType) {
            'comprehensive' => [
                'market_share' => $this->calculateMarketShare('30d'),
                'pricing' => $this->analyzeCompetitorPricing('30d'),
                'features' => $this->compareCompetitorFeatures(),
                'performance' => $this->analyzeCompetitorPerformance('30d'),
                'intelligence' => $this->gatherCompetitorIntelligence(),
                'opportunities' => $this->identifyMarketOpportunities(),
                'threats' => $this->identifyCompetitiveThreats()
            ],
            'market_share' => $this->calculateMarketShare('30d'),
            'pricing' => $this->analyzeCompetitorPricing('30d'),
            'features' => $this->compareCompetitorFeatures(),
            'performance' => $this->analyzeCompetitorPerformance('30d'),
            default => []
        };
    }

    private function getOurMarketShare($period)
    {
        // Simplified calculation - in real implementation this would use actual data
        return 15.5; // 15.5% market share
    }

    private function calculatePricePosition($competitor, $allCompetitors)
    {
        $prices = $allCompetitors->pluck('avg_price')->sort()->values();
        $position = $prices->search($competitor->avg_price);
        
        if ($position === false) return 'unknown';
        
        $total = count($prices);
        if ($position === 0) return 'lowest';
        if ($position === $total - 1) return 'highest';
        if ($position < $total / 3) return 'low';
        if ($position > ($total * 2 / 3)) return 'high';
        return 'medium';
    }

    private function analyzePriceTrends($competitors)
    {
        return [
            'overall_trend' => 'stable',
            'price_changes' => [
                'increasing' => 2,
                'decreasing' => 1,
                'stable' => 5
            ],
            'volatility' => 'low'
        ];
    }

    private function getRecentCompetitorActivities()
    {
        return [
            'Company A' => 'Launched new mobile app',
            'Company B' => 'Introduced AI-powered search',
            'Company C' => 'Expanded to new markets',
            'Company D' => 'Lowered prices by 5%'
        ];
    }

    private function analyzeMarketingStrategies()
    {
        return [
            'digital_marketing' => [
                'SEO optimization',
                'Social media campaigns',
                'Content marketing'
            ],
            'traditional_marketing' => [
                'Print advertising',
                'Television commercials',
                'Radio spots'
            ],
            'partnership_marketing' => [
                'Real estate agent partnerships',
                'Bank collaborations',
                'Influencer marketing'
            ]
        ];
    }

    private function analyzeTechnologyStack()
    {
        return [
            'frontend_technologies' => ['React', 'Vue.js', 'Angular'],
            'backend_technologies' => ['Node.js', 'Python', 'Java'],
            'mobile_technologies' => ['React Native', 'Flutter', 'Swift'],
            'database_technologies' => ['PostgreSQL', 'MongoDB', 'MySQL']
        ];
    }

    private function analyzeTargetAudience()
    {
        return [
            'primary_audience' => 'Property buyers aged 25-45',
            'secondary_audience' => 'Real estate investors',
            'geographic_focus' => 'Urban and suburban areas',
            'income_level' => 'Middle to high income'
        ];
    }

    private function analyzeStrengthsWeaknesses()
    {
        return [
            'strengths' => [
                'Brand recognition',
                'Technology innovation',
                'Customer service'
            ],
            'weaknesses' => [
                'Limited geographic presence',
                'Higher pricing',
                'Slower innovation cycle'
            ]
        ];
    }

    private function getStartDate($period)
    {
        return match($period) {
            '7d' => now()->subDays(7),
            '30d' => now()->subDays(30),
            '90d' => now()->subDays(90),
            '1y' => now()->subYear(),
            default => now()->subDays(30)
        };
    }
}

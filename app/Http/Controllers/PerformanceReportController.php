<?php

namespace App\Http\Controllers;

use App\Models\PerformanceReport;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PerformanceReportController extends Controller
{
    public function index()
    {
        $reports = PerformanceReport::with(['property', 'user'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(15);
            
        return view('reports.performance.index', compact('reports'));
    }

    public function dashboard()
    {
        $user = Auth::user();
        
        // Property performance metrics
        $totalProperties = $user->properties()->count();
        $activeListings = $user->properties()->where('status', 'active')->count();
        $soldProperties = $user->properties()->where('status', 'sold')->count();
        $pendingProperties = $user->properties()->where('status', 'pending')->count();
        
        // Average metrics
        $averageDaysOnMarket = $user->properties()
            ->whereNotNull('sold_at')
            ->avg(DB::raw('DATEDIFF(sold_at, created_at)'));
            
        $averageViews = $user->properties()->avg('views_count');
        $averageInquiries = $user->properties()->avg('inquiries_count');
        
        // Conversion rates
        $viewToInquiryRate = $this->calculateViewToInquiryRate($user);
        $inquiryToSaleRate = $this->calculateInquiryToSaleRate($user);
        
        // Top performing properties
        $topViewedProperties = $user->properties()
            ->orderBy('views_count', 'desc')
            ->limit(10)
            ->get();
            
        $topInquiredProperties = $user->properties()
            ->orderBy('inquiries_count', 'desc')
            ->limit(10)
            ->get();
            
        // Performance by property type
        $performanceByType = $user->properties()
            ->selectRaw('type, COUNT(*) as count, AVG(views_count) as avg_views, AVG(inquiries_count) as avg_inquiries')
            ->groupBy('type')
            ->get();
            
        // Monthly performance trend
        $monthlyPerformance = $this->getMonthlyPerformanceTrend($user);
        
        return view('reports.performance.dashboard', compact(
            'totalProperties', 'activeListings', 'soldProperties', 'pendingProperties',
            'averageDaysOnMarket', 'averageViews', 'averageInquiries',
            'viewToInquiryRate', 'inquiryToSaleRate',
            'topViewedProperties', 'topInquiredProperties', 'performanceByType', 'monthlyPerformance'
        ));
    }

    public function create()
    {
        $properties = Auth::user()->properties()->get();
        return view('reports.performance.create', compact('properties'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'property_ids' => 'nullable|array',
            'property_ids.*' => 'exists:properties,id',
            'date_range' => 'nullable|array',
            'date_range.start' => 'nullable|date',
            'date_range.end' => 'nullable|date|after_or_equal:date_range.start',
            'metrics' => 'required|array',
            'metrics.*' => 'in:views,inquiries,conversion_rate,days_on_market,price_performance',
            'format' => 'required|in:pdf,excel,csv'
        ]);

        $report = Report::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'user_id' => Auth::id(),
            'template_id' => 2, // Performance report template
            'parameters' => [
                'property_ids' => $validated['property_ids'] ?? [],
                'date_range' => $validated['date_range'] ?? [],
                'metrics' => $validated['metrics']
            ],
            'format' => $validated['format'],
            'status' => 'pending'
        ]);

        return redirect()->route('reports.performance.show', $report->id)
            ->with('success', 'تم إنشاء تقرير الأداء بنجاح');
    }

    public function show(Report $report)
    {
        $this->authorize('view', $report);
        
        if ($report->template_id !== 2) {
            return back()->with('error', 'هذا ليس تقرير أداء');
        }
        
        $performanceData = $this->getPerformanceReportData($report);
        
        return view('reports.performance.show', compact('report', 'performanceData'));
    }

    public function analytics()
    {
        $user = Auth::user();
        
        // Performance scores
        $scores = [
            'overall_score' => $this->calculateOverallPerformanceScore($user),
            'listing_score' => $this->calculateListingPerformanceScore($user),
            'marketing_score' => $this->calculateMarketingPerformanceScore($user),
            'conversion_score' => $this->calculateConversionPerformanceScore($user)
        ];
        
        // Trends
        $trends = [
            'views_trend' => $this->getViewsTrend($user),
            'inquiries_trend' => $this->getInquiriesTrend($user),
            'conversion_trend' => $this->getConversionTrend($user)
        ];
        
        // Benchmarks
        $benchmarks = [
            'vs_market' => $this->compareWithMarket($user),
            'vs_category' => $this->compareWithCategory($user),
            'vs_region' => $this->compareWithRegion($user)
        ];
        
        // Recommendations
        $recommendations = $this->generatePerformanceRecommendations($user);
        
        return view('reports.performance.analytics', compact(
            'scores', 'trends', 'benchmarks', 'recommendations'
        ));
    }

    public function insights()
    {
        $user = Auth::user();
        
        // Key insights
        $insights = [
            'best_performing_month' => $this->getBestPerformingMonth($user),
            'worst_performing_month' => $this->getWorstPerformingMonth($user),
            'peak_activity_hours' => $this->getPeakActivityHours($user),
            'most_effective_features' => $this->getMostEffectiveFeatures($user),
            'underperforming_properties' => $this->getUnderperformingProperties($user)
        ];
        
        // Anomalies
        $anomalies = $this->detectPerformanceAnomalies($user);
        
        // Opportunities
        $opportunities = $this->identifyPerformanceOpportunities($user);
        
        return view('reports.performance.insights', compact(
            'insights', 'anomalies', 'opportunities'
        ));
    }

    private function getPerformanceReportData(Report $report)
    {
        $parameters = $report->parameters ?? [];
        $dateRange = $parameters['date_range'] ?? [];
        $propertyIds = $parameters['property_ids'] ?? [];
        $metrics = $parameters['metrics'] ?? [];
        
        $query = Auth::user()->properties();
        
        if (!empty($propertyIds)) {
            $query->whereIn('id', $propertyIds);
        }
        
        if (isset($dateRange['start'])) {
            $query->where('created_at', '>=', $dateRange['start']);
        }
        
        if (isset($dateRange['end'])) {
            $query->where('created_at', '<=', $dateRange['end']);
        }
        
        $properties = $query->get();
        
        $data = [
            'properties' => $properties,
            'summary' => $this->getPerformanceSummary($properties),
            'metrics' => $this->calculateMetrics($properties, $metrics),
            'trends' => $this->getPerformanceTrends($properties),
            'comparisons' => $this->getPropertyComparisons($properties)
        ];
        
        return $data;
    }

    private function getPerformanceSummary($properties)
    {
        return [
            'total_properties' => $properties->count(),
            'active_listings' => $properties->where('status', 'active')->count(),
            'sold_properties' => $properties->where('status', 'sold')->count(),
            'total_views' => $properties->sum('views_count'),
            'total_inquiries' => $properties->sum('inquiries_count'),
            'average_days_on_market' => $properties->whereNotNull('sold_at')
                ->avg(function($property) {
                    return Carbon::parse($property->sold_at)->diffInDays($property->created_at);
                }),
            'overall_conversion_rate' => $this->calculateOverallConversionRate($properties)
        ];
    }

    private function calculateMetrics($properties, $requestedMetrics)
    {
        $metrics = [];
        
        foreach ($requestedMetrics as $metric) {
            switch ($metric) {
                case 'views':
                    $metrics['views'] = [
                        'total' => $properties->sum('views_count'),
                        'average' => $properties->avg('views_count'),
                        'highest' => $properties->max('views_count'),
                        'lowest' => $properties->min('views_count')
                    ];
                    break;
                    
                case 'inquiries':
                    $metrics['inquiries'] = [
                        'total' => $properties->sum('inquiries_count'),
                        'average' => $properties->avg('inquiries_count'),
                        'highest' => $properties->max('inquiries_count'),
                        'lowest' => $properties->min('inquiries_count')
                    ];
                    break;
                    
                case 'conversion_rate':
                    $metrics['conversion_rate'] = [
                        'overall' => $this->calculateOverallConversionRate($properties),
                        'by_property' => $properties->map(function($property) {
                            return [
                                'property_id' => $property->id,
                                'property_title' => $property->title,
                                'rate' => $this->calculatePropertyConversionRate($property)
                            ];
                        })
                    ];
                    break;
                    
                case 'days_on_market':
                    $metrics['days_on_market'] = [
                        'average' => $properties->whereNotNull('sold_at')
                            ->avg(function($property) {
                                return Carbon::parse($property->sold_at)->diffInDays($property->created_at);
                            }),
                        'fastest' => $properties->whereNotNull('sold_at')
                            ->min(function($property) {
                                return Carbon::parse($property->sold_at)->diffInDays($property->created_at);
                            }),
                        'slowest' => $properties->whereNotNull('sold_at')
                            ->max(function($property) {
                                return Carbon::parse($property->sold_at)->diffInDays($property->created_at);
                            })
                    ];
                    break;
                    
                case 'price_performance':
                    $metrics['price_performance'] = [
                        'average_price' => $properties->avg('price'),
                        'price_per_view' => $properties->map(function($property) {
                            return $property->views_count > 0 ? $property->price / $property->views_count : 0;
                        })->avg(),
                        'price_per_inquiry' => $properties->map(function($property) {
                            return $property->inquiries_count > 0 ? $property->price / $property->inquiries_count : 0;
                        })->avg()
                    ];
                    break;
            }
        }
        
        return $metrics;
    }

    private function getPerformanceTrends($properties)
    {
        return [
            'monthly_views' => $this->getMonthlyViewsTrend($properties),
            'monthly_inquiries' => $this->getMonthlyInquiriesTrend($properties),
            'monthly_conversions' => $this->getMonthlyConversionsTrend($properties)
        ];
    }

    private function getPropertyComparisons($properties)
    {
        return $properties->map(function($property) {
            return [
                'property' => $property,
                'performance_score' => $this->calculatePropertyPerformanceScore($property),
                'vs_average' => $this->comparePropertyToAverage($property, $properties)
            ];
        })->sortByDesc('performance_score');
    }

    private function calculateViewToInquiryRate($user)
    {
        $totalViews = $user->properties()->sum('views_count');
        $totalInquiries = $user->properties()->sum('inquiries_count');
        
        return $totalViews > 0 ? ($totalInquiries / $totalViews) * 100 : 0;
    }

    private function calculateInquiryToSaleRate($user)
    {
        $totalInquiries = $user->properties()->sum('inquiries_count');
        $totalSold = $user->properties()->where('status', 'sold')->count();
        
        return $totalInquiries > 0 ? ($totalSold / $totalInquiries) * 100 : 0;
    }

    private function getMonthlyPerformanceTrend($user)
    {
        return $user->properties()
            ->where('created_at', '>=', now()->subYear())
            ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as properties_count, SUM(views_count) as total_views, SUM(inquiries_count) as total_inquiries')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();
    }

    private function calculateOverallPerformanceScore($user)
    {
        $viewScore = min($this->calculateViewToInquiryRate($user) * 2, 50);
        $conversionScore = min($this->calculateInquiryToSaleRate($user) * 2, 50);
        
        return $viewScore + $conversionScore;
    }

    private function calculateListingPerformanceScore($user)
    {
        $totalProperties = $user->properties()->count();
        $soldProperties = $user->properties()->where('status', 'sold')->count();
        
        return $totalProperties > 0 ? ($soldProperties / $totalProperties) * 100 : 0;
    }

    private function calculateMarketingPerformanceScore($user)
    {
        $averageViews = $user->properties()->avg('views_count');
        $marketAverageViews = DB::table('properties')->avg('views_count');
        
        return $marketAverageViews > 0 ? ($averageViews / $marketAverageViews) * 100 : 0;
    }

    private function calculateConversionPerformanceScore($user)
    {
        return $this->calculateInquiryToSaleRate($user);
    }

    private function getViewsTrend($user)
    {
        // Implementation for views trend analysis
        return [];
    }

    private function getInquiriesTrend($user)
    {
        // Implementation for inquiries trend analysis
        return [];
    }

    private function getConversionTrend($user)
    {
        // Implementation for conversion trend analysis
        return [];
    }

    private function compareWithMarket($user)
    {
        // Implementation for market comparison
        return [];
    }

    private function compareWithCategory($user)
    {
        // Implementation for category comparison
        return [];
    }

    private function compareWithRegion($user)
    {
        // Implementation for region comparison
        return [];
    }

    private function generatePerformanceRecommendations($user)
    {
        // Implementation for generating recommendations
        return [];
    }

    private function getBestPerformingMonth($user)
    {
        // Implementation for finding best performing month
        return [];
    }

    private function getWorstPerformingMonth($user)
    {
        // Implementation for finding worst performing month
        return [];
    }

    private function getPeakActivityHours($user)
    {
        // Implementation for finding peak activity hours
        return [];
    }

    private function getMostEffectiveFeatures($user)
    {
        // Implementation for finding most effective features
        return [];
    }

    private function getUnderperformingProperties($user)
    {
        // Implementation for finding underperforming properties
        return [];
    }

    private function detectPerformanceAnomalies($user)
    {
        // Implementation for anomaly detection
        return [];
    }

    private function identifyPerformanceOpportunities($user)
    {
        // Implementation for opportunity identification
        return [];
    }

    private function calculateOverallConversionRate($properties)
    {
        $totalInquiries = $properties->sum('inquiries_count');
        $totalSold = $properties->where('status', 'sold')->count();
        
        return $totalInquiries > 0 ? ($totalSold / $totalInquiries) * 100 : 0;
    }

    private function calculatePropertyConversionRate($property)
    {
        return $property->inquiries_count > 0 ? 
            (($property->status === 'sold' ? 1 : 0) / $property->inquiries_count) * 100 : 0;
    }

    private function calculatePropertyPerformanceScore($property)
    {
        $viewScore = min($property->views_count / 100, 30);
        $inquiryScore = min($property->inquiries_count / 10, 40);
        $saleScore = $property->status === 'sold' ? 30 : 0;
        
        return $viewScore + $inquiryScore + $saleScore;
    }

    private function comparePropertyToAverage($property, $allProperties)
    {
        $averageViews = $allProperties->avg('views_count');
        $averageInquiries = $allProperties->avg('inquiries_count');
        
        return [
            'views_vs_average' => $averageViews > 0 ? (($property->views_count - $averageViews) / $averageViews) * 100 : 0,
            'inquiries_vs_average' => $averageInquiries > 0 ? (($property->inquiries_count - $averageInquiries) / $averageInquiries) * 100 : 0
        ];
    }

    private function getMonthlyViewsTrend($properties)
    {
        // Implementation for monthly views trend
        return [];
    }

    private function getMonthlyInquiriesTrend($properties)
    {
        // Implementation for monthly inquiries trend
        return [];
    }

    private function getMonthlyConversionsTrend($properties)
    {
        // Implementation for monthly conversions trend
        return [];
    }
}

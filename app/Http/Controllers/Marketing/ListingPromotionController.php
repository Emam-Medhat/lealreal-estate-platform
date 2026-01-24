<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Marketing\ListingPromotion;
use App\Models\Property\Property;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ListingPromotionController extends Controller
{
    /**
     * Display a listing of the listing promotions.
     */
    public function index(Request $request): Response
    {
        $query = ListingPromotion::with(['property', 'promotionType', 'targetAudience'])
            ->latest('created_at');

        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        if ($request->has('promotion_type_id')) {
            $query->where('promotion_type_id', $request->promotion_type_id);
        }

        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('start_date', [
                $request->start_date,
                $request->end_date
            ]);
        }

        $promotions = $query->paginate(15);

        // Get statistics
        $stats = [
            'total_promotions' => ListingPromotion::count(),
            'active_promotions' => ListingPromotion::where('status', 'active')->count(),
            'total_properties' => Property::count(),
            'promoted_properties' => ListingPromotion::distinct('property_id')->count(),
            'promotion_types' => ListingPromotion::select('promotion_type_id', DB::raw('count(*) as count'))
                ->groupBy('promotion_type_id')
                ->get(),
            'priorities' => ListingPromotion::select('priority', DB::raw('count(*) as count'))
                ->groupBy('priority')
                ->get(),
            'monthly_performance' => ListingPromotion::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('count(*) as count'),
                DB::raw('SUM(views) as total_views'),
                DB::raw('SUM(clicks) as total_clicks'),
                DB::raw('SUM(conversions) as total_conversions')
            )
                ->where('created_at', '>=', now()->subMonths(12))
                ->groupBy('month')
                ->orderBy('month', 'desc')
                ->get(),
        ];

        return Inertia::render('marketing/listing-promotions', [
            'promotions' => $promotions,
            'stats' => $stats,
            'filters' => $request->only(['status', 'property_id', 'promotion_type_id', 'priority', 'start_date', 'end_date']),
            'properties' => Property::select('id', 'title', 'reference_number', 'status')->get(),
            'promotionTypes' => [
                'featured' => 'ممميز',
                'highlighted' => 'ممميز',
                'urgent' => 'عاجل',
                'standard' => 'عادي',
                'premium' => 'ممميز',
                'exclusive' => 'حصري',
            ],
            'priorities' => [
                'high' => 'عالي',
                'medium' => 'متوسط',
                'low' => 'منخفض',
            ],
        ]);
    }

    /**
     * Show the form for creating a new listing promotion.
     */
    public function create(): Response
    {
        $properties = Property::select('id', 'title', 'reference_number', 'status')
            ->where('status', 'active')
            ->get();

        return Inertia::render('marketing/listing-promotion-create', [
            'properties' => $properties,
            'promotionTypes' => [
                'featured' => 'ممميز',
                'highlighted' => 'ممميز',
                'urgent' => 'عاجل',
                'standard' => 'عادي',
                'premium' => 'ممميز',
                'exclusive' => 'حصري',
            ],
            'priorities' => [
                'high' => 'عالي',
                'medium' => 'متوسط',
                'low' => 'منخفض',
            ],
            'targetAudiences' => [
                'first_time_buyers' => 'المشترون لأول مرة',
                'investors' => 'المستثمرون',
                'families' => 'العائلات',
                'professionals' => 'المحترفين',
                'students' => 'الطلاب',
                'retirees' => 'المتقاعدين',
                'expats' => 'المغرباء',
            ],
        ]);
    }

    /**
     * Store a newly created listing promotion in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'promotion_type_id' => 'required|exists:promotion_types,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'required|string|in:draft,active,paused,expired,completed',
            'priority' => 'required|string|in:high,medium,low',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'budget' => 'required|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'promoted_price' => 'nullable|numeric|min:0',
            'target_audience' => 'nullable|array',
            'promotion_features' => 'nullable|array',
            'visual_assets' => 'nullable|array',
            'call_to_action' => 'nullable|string|max:255',
            'terms_conditions' => 'nullable|string',
            'metadata' => 'nullable|array',
        ]);

        // Calculate promoted price if discount percentage is provided
        if (isset($validated['discount_percentage']) && isset($validated['original_price'])) {
            $validated['promoted_price'] = $validated['original_price'] - ($validated['original_price'] * $validated['discount_percentage'] / 100);
        }

        $promotion = ListingPromotion::create([
            'property_id' => $validated['property_id'],
            'promotion_type_id' => $validated['promotion_type_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'status' => $validated['status'],
            'priority' => $validated['priority'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'budget' => $validated['budget'],
            'discount_percentage' => $validated['discount_percentage'] ?? 0,
            'discount_amount' => $validated['discount_amount'] ?? 0,
            'original_price' => $validated['original_price'] ?? 0,
            'promoted_price' => $validated['promoted_price'] ?? 0,
            'target_audience' => $validated['target_audience'] ?? [],
            'promotion_features' => $validated['promotion_features'] ?? [],
            'visual_assets' => $validated['visual_assets'] ?? [],
            'call_to_action' => $validated['call_to_action'] ?? '',
            'terms_conditions' => $validated['terms_conditions'] ?? '',
            'metadata' => $validated['metadata'] ?? [],
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        // Update property status to promoted if promotion is active
        if ($promotion->status === 'active') {
            $promotion->property->update(['status' => 'promoted']);
        }

        return response()->json([
            'success' => 'true',
            'message' => 'تم إنشاء الترويج بنجاح',
            'promotion' => $promotion->load('property', 'promotionType'),
        ]);
    }

    /**
     * Display the specified listing promotion.
     */
    public function show(ListingPromotion $listingPromotion): Response
    {
        $listingPromotion->load([
            'property',
            'promotionType',
            'targetAudience',
            'promotionFeatures',
            'visualAssets',
            'analytics',
        ]);

        // Get promotion performance data
        $performance = [
            'views' => $this->getPromotionViews($listingPromotion),
            'clicks' => $this->getPromotionClicks($listingPromotion),
            'conversions' => $this->getPromotionConversions($listingPromotion),
            'engagement_rate' => $this->getEngagementRate($listingPromotion),
            'roi' => $this->calculateROI($listingPromotion),
            'cost_per_acquisition' => $this->getCostPerAcquisition($listingPromotion),
            'conversion_rate' => $this->getConversionRate($listingPromotion),
        ];

        return Inertia::render('marketing/listing-promotion-show', [
            'promotion' => $listingPromotion,
            'performance' => $performance,
        ]);
    }

    /**
     * Show the form for editing the specified listing promotion.
     */
    public function edit(ListingPromotion $listingPromotion): Response
    {
        $properties = Property::select('id', 'title', 'reference_number', 'status')
            ->where('status', 'active')
            ->get();

        return Inertia::render('marketing/listing-promotion-edit', [
            'promotion' => $listingPromotion,
            'properties' => $properties,
            'promotionTypes' => [
                'featured' => 'ممميز',
                'highlighted' => 'ممميز',
                'urgent' => 'عاجل',
                'standard' => 'عادي',
                'premium' => 'ممميز',
                'exclusive' => 'حصري',
            ],
            'priorities' => [
                'high' => 'عالي',
                'medium' => 'متوسط',
                'low' => 'منخفض',
            ],
            'targetAudiences' => [
                'first_time_buyers' => 'المشترون لأول مرة',
                'investors' => 'المستثمرون',
                'families' => 'العائلات',
                'professionals' => 'المحترفين',
                'students' => 'الطلاب',
                'retirees' => 'المتقاعدين',
                'expats' => 'الغرباء',
            ],
        ]);
    }

    /**
     * Update the specified listing promotion in storage.
     */
    public function update(Request $request, ListingPromotion $listingPromotion): JsonResponse
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'promotion_type_id' => 'required|exists:promotion_types,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'required|string|in:draft,active,paused,expired,completed',
            'priority' => 'required|string|in:high,medium,low',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'budget' => 'required|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'promoted_price' => 'nullable|numeric|min:0',
            'target_audience' => 'nullable|array',
            'promotion_features' => 'nullable|array',
            'visual_assets' => 'nullable|array',
            'call_to_action' => 'nullable|string|max:255',
            'terms_conditions' => 'nullable|string',
            'metadata' => 'nullable|array',
        ]);

        // Calculate promoted price if discount percentage is provided
        if (isset($validated['discount_percentage']) && isset($validated['original_price'])) {
            $validated['promoted_price'] = $validated['original_price'] - ($validated['original_price'] * $validated['discount_percentage'] / 100);
        }

        $listingPromotion->update([
            'property_id' => $validated['property_id'],
            'promotion_type_id' => $validated['promotion_type_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'status' => $validated['status'],
            'priority' => $validated['priority'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'budget' => $validated['budget'],
            'discount_percentage' => $validated['discount_percentage'] ?? 0,
            'discount_amount' => $validated['discount_amount'] ?? 0,
            'original_price' => $validated['original_price'] ?? 0,
            'promoted_price' => $validated['promoted'] ?? 0,
            'target_audience' => $validated['target_audience'] ?? [],
            'promotion_features' => $validated['promotion_features'] ?? [],
            'visual_assets' => $validated['visual_assets'] ?? [],
            'call_to_action' => $validated['call_to_action'] ?? '',
            'terms_conditions' => $validated['terms_conditions'] ?? '',
            'metadata' => $validated['metadata'] ?? [],
            'updated_by' => auth()->id(),
        ]);

        // Update property status based on promotion status
        if ($listingPromotion->status === 'active') {
            $listingPromotion->property->update(['status' => 'promoted']);
        } elseif ($listingPromotion->status === 'expired') {
            $listingPromotion->property->update(['status' => 'active']);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الترويج بنجاح',
            'promotion' => $listingPromotion->load('property', 'promotionType'),
        ]);
    }

    /**
     * Remove the specified listing promotion from storage.
     */
    public function destroy(ListingPromotion $listingPromotion): JsonResponse
    {
        // Restore property status if promotion was active
        if ($listingPromotion->status === 'active') {
            $listingPromotion->property->update(['status' => 'active']);
        }

        $listingPromotion->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف الترويج بنجاح',
        ]);
    }

    /**
     * Activate the specified listing promotion.
     */
    public function activate(ListingPromotion $listingPromotion): JsonResponse
    {
        if ($listingPromotion->status !== 'draft' && $listingPromotion->status !== 'paused') {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن تفعيل الترويج في حالته الحالية',
            ]);
        }

        $listingPromotion->update([
            'status' => 'active',
            'activated_at' => now(),
            'updated_by' => auth()->id(),
        ]);

        // Update property status to promoted
        $listingPromotion->property->update(['status' => 'promoted']);

        return response()->json([
            'success' => true,
            'message' => 'تم تفعيل الترويج بنجاح',
            'promotion' => $listingPromotion->load('property', 'promotionType'),
        ]);
    }

    /**
     * Pause the specified listing promotion.
     */
    public function pause(ListingPromotion $listingPromotion): JsonResponse
    {
        if ($listingPromotion->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن إيقاف الترويج في حالته الحالية',
            ]);
        }

        $listingPromotion->update([
            'status' => 'paused',
            'paused_at' => now(),
            'updated_by' => auth()->id(),
        ]);

        // Restore property status
        $listingPromotion->property->update(['status' => 'active']);

        return response()->json([
            'success' => true,
            'message' => 'تم إيقاف الترويج بنجاح',
            'promotion' => $listingPromotion,
        ]);
    }

    /**
     * Resume the specified listing promotion.
     */
    public function resume(ListingPromotion $listingPromotion): JsonResponse
    {
        if ($listingPromotion->status !== 'paused') {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن استئناف الترويج في حالته الحالية',
            ]);
        }

        $listingPromotion->update([
            'status' => 'active',
            'resumed_at' => now(),
            'updated_by' => auth()->id(),
        ]);

        // Update property status to promoted
        $listingPromotion->property->update(['status' => 'promoted']);

        return response()->json([
            'success' => true,
            'message' => 'تم استئناف الترويج بنجاح',
            'promotion' => $listingPromotion->load('property', 'promotionType'),
        ]);
    }

    /**
     * Get promotion analytics and performance data.
     */
    public function getAnalytics(ListingPromotion $listingPromotion): JsonResponse
    {
        $analytics = [
            'overview' => [
                'total_views' => $this->getPromotionViews($listingPromotion),
                'total_clicks' => $this->getPromotionClicks($listingPromotion),
                'total_conversions' => $this->getPromotionConversions($listingPromotion),
                'engagement_rate' => $this->getEngagementRate($listingPromotion),
                'roi' => $this->calculateROI($listingPromotion),
                'cost_per_acquisition' => $this->getCostPerAcquisition($listingPromotion),
                'conversion_rate' => $this->getConversionRate($listingPromotion),
                'savings' => $this->calculateTotalSavings($listingPromotion),
            ],
            'daily_performance' => $this->getDailyPerformance($listingPromotion),
            'audience_performance' => $this->getAudiencePerformance($listingPromotion),
            'channel_performance' => $this->getChannelPerformance($listingPromotion),
            'time_series_data' => $this->getTimeSeriesData($listingPromotion),
        ];

        return response()->json([
            'success' => true,
            'analytics' => $analytics,
        ]);
    }

    /**
     * Get promotion assets and media.
     */
    public function getAssets(ListingPromotion $listingPromotion): JsonResponse
    {
        $assets = [
            'images' => $this->getPromotionImages($listingPromotion),
            'videos' => $this->getPromotionVideos($listingPromotion),
            'documents' => $this->getPromotionDocuments($listingPromotion),
            'virtual_tours' => $this->getPromotionVirtualTours($listingPromotion),
        ];

        return response()->json([
            'success' => true,
            'assets' => $assets,
        ]);
    }

    /**
     * Duplicate the specified listing promotion.
     */
    public function duplicate(ListingPromotion $listingPromotion): JsonResponse
    {
        $newPromotion = $listingPromotion->replicate([
            'title' => $listingPromotion->title . ' (نسخة)',
            'status' => 'draft',
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم نسخ الترويج بنجاح',
            'promotion' => $newPromotion->load('property', 'promotionType'),
        ]);
    }

    /**
     * Export promotion data to various formats.
     */
    public function export(Request $request, ListingPromotion $listingPromotion): JsonResponse
    {
        $format = $request->get('format', 'csv');
        
        $data = [
            'promotion' => $listingPromotion->toArray(),
            'property' => $listingPromotion->property->toArray(),
            'analytics' => $this->getPromotionAnalytics($listingPromotion),
            'assets' => $this->getPromotionAssets($listingPromotion),
        ];

        switch ($format) {
            case 'csv':
                $filename = 'promotion_' . $listingPromotion->id . '_export.csv';
                $content = $this->exportToCsv($data);
                break;
            case 'xlsx':
                $filename = 'promotion_' . $listingPromotion->id . '_export.xlsx';
                $content = $this->exportToXlsx($data);
                break;
            case 'json':
                $filename = 'promotion_' . $listingPromotion->id . '_export.json';
                $content = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                break;
            default:
                return response()->json([
                    'success' => false,
                    'message' => 'تنسيق التصدير غير مدعوم',
                ]);
        }

        return response()->json([
            'success' => true,
            'filename' => $filename,
            'content' => $content,
        ]);
    }

    /**
     * Get promotion views count.
     */
    private function getPromotionViews(ListingPromotion $promotion): int
    {
        // Mock implementation - in real app, this would come from analytics
        return rand(500, 5000);
    }

    /**
     * Get promotion clicks count.
     */
    private function getPromotionClicks(ListingPromotion $promotion): int
    {
        // Mock implementation - in real app, this would come from analytics
        return rand(100, 1000);
    }

    /**
     * Get promotion conversions count.
     */
    private function getPromotionConversions(ListingPromotion $promotion): int
    {
        // Mock implementation - in real app, this would come from analytics
        return rand(10, 100);
    }

    /**
     * Get engagement rate.
     */
    private function getEngagementRate(ListingPromotion $promotion): float
    {
        $views = $this->getPromotionViews($promotion);
        $clicks = $this->getPromotionClicks($promotion);
        
        return $views > 0 ? ($clicks / $views) * 100 : 0;
    }

    /**
     * Calculate ROI.
     */
    private function calculateROI(ListingPromotion $promotion): float
    {
        $cost = $promotion->budget;
        $conversions = $this->getPromotionConversions($promotion);
        $avgPropertyValue = 500000; // Mock average property value
        
        $revenue = $conversions * $avgPropertyValue;
        
        return $cost > 0 ? (($revenue - $cost) / $cost) * 100 : 0;
    }

    /**
     * Get cost per acquisition.
     */
    private function getCostPerAcquisition(ListingPromotion $promotion): float
    {
        $cost = $promotion->budget;
        $conversions = $this->getPromotionConversions($promotion);
        
        return $conversions > 0 ? $cost / $conversions : 0;
    }

    /**
     * Get conversion rate.
     */
    private function getConversionRate(ListingPromotion $promotion): float
    {
        $views = $this->getPromotionViews($promotion);
        $conversions = $this->getPromotionConversions($promotion);
        
        return $views > 0 ? ($conversions / $views) * 100 : 0;
    }

    /**
     * Calculate total savings.
     */
    private function calculateTotalSavings(ListingPromotion $promotion): float
    {
        $originalPrice = $promotion->original_price ?? 0;
        $promotedPrice = $promotion->promoted_price ?? 0;
        
        return $originalPrice - $promotedPrice;
    }

    /**
     * Get daily performance data.
     */
    private function getDailyPerformance(ListingPromotion $promotion): array
    {
        // Mock implementation - in real app, this would come from analytics
        $data = [];
        for ($i = 0; $i < 30; $i++) {
            $data[] = [
                'date' => now()->subDays($i)->format('Y-m-d'),
                'views' => rand(50, 200),
                'clicks' => rand(25, 100),
                'conversions' => rand(5, 20),
                'cost' => rand(100, 500),
            ];
        }
        return array_reverse($data);
    }

    /**
     * Get audience performance data.
     */
    private function getAudiencePerformance(ListingPromotion $promotion): array
    {
        // Mock implementation
        return [
            'first_time_buyers' => [
                'views' => rand(100, 500),
                'clicks' => rand(50, 200),
                'conversions' => rand(5, 50),
                'conversion_rate' => rand(5, 15),
            ],
            'investors' => [
                'views' => rand(200, 800),
                'clicks' => rand(100, 400),
                'conversions' => rand(20, 100),
                'conversion_rate' => rand(10, 30),
            ],
            'families' => [
                'views' => rand(300, 1000),
                'clicks' => rand(150, 600),
                'conversions' => rand(30, 150),
                'conversion_rate' => rand(10, 20),
            ],
            'professionals' => [
                'views' => rand(150, 600),
                'clicks' => rand(75, 300),
                'conversions' => rand(10, 50),
                'conversion_rate' => rand(5, 15),
            ],
        ];
    }

    /**
     * Get channel performance data.
     */
    private function getChannelPerformance(ListingPromotion $promotion): array
    {
        // Mock implementation
        return [
            'website' => [
                'views' => rand(500, 2000),
                'clicks' => rand(250, 1000),
                'conversions' => rand(50, 200),
                'conversion_rate' => rand(10, 40),
            ],
            'social_media' => [
                'views' => rand(1000, 5000),
                'clicks' => rand(500, 2000),
                'conversions' => rand(100, 500),
                'conversion_rate' => rand(10, 20),
            ],
            'email_marketing' => [
                'views' => rand(200, 1000),
                'clicks' => rand(100, 500),
                'conversions' => rand(20, 100),
                'conversion_rate' => rand(10, 30),
            ],
            'digital_ads' => [
                'views' => rand(1000, 5000),
                'clicks' => rand(500, 2000),
                'conversions' => rand(50, 200),
                'conversion_rate' => rand(5, 15),
            ],
        ];
    }

    /**
     * Get time series data.
     */
    private function getTimeSeriesData(ListingPromotion $promotion): array
    {
        // Mock implementation
        $data = [];
        for ($i = 0; $i < 90; $i++) {
            $data[] = [
                'date' => now()->subDays($i)->format('Y-m-d'),
                'views' => rand(50, 200),
                'clicks' => rand(25, 100),
                'conversions' => rand(5, 20),
            ];
        }
        return array_reverse($data);
    }

    /**
     * Get promotion images.
     */
    private function getPromotionImages(ListingPromotion $promotion): array
    {
        // Mock implementation
        return [
            ['url' => 'https://example.com/promotion1.jpg', 'title' => 'صورة الترويج 1'],
            ['url' => 'https://example.com/promotion2.jpg', 'title' => 'صورة الترويج 2'],
        ];
    }

    /**
     * Get promotion videos.
     */
    private function getPromotionVideos(ListingPromotion $promotion): array
    {
        // Mock implementation
        return [
            ['url' => 'https://example.com/promotion1.mp4', 'title' => 'فيديو الترويج 1'],
            ['url' => 'https://example.com/promotion2.mp4', 'title' => 'فيديو الترويج 2'],
        ];
    }

    /**
     * Get promotion documents.
     */
    private function getPromotionDocuments(ListingPromotion $promotion): array
    {
        // Mock implementation
        return [
            ['url' => 'https://example.com/promotion1.pdf', 'title' => 'وثيقة الترويج 1'],
            ['url' => 'https://example.com/promotion2.pdf', 'title' => 'وثيقة الترويج 2'],
        ];
    }

    /**
     * Get promotion virtual tours.
     */
    private function getPromotionVirtualTours(ListingPromotion $promotion): array
    {
        // Mock implementation
        return [
            ['url' => 'https://example.com/tour1.html', 'title' => 'جولة افتراضية 1'],
            ['url' => 'https://example.com/tour2.html', 'title' => 'جولة افتراضية 2'],
        ];
    }

    /**
     * Export data to CSV format.
     */
    private function exportToCsv(array $data): string
    {
        $csv = '';
        $headers = array_keys($data['promotion']);
        $csv .= implode(',', $headers) . "\n";
        
        $csv .= implode(',', [
            $data['promotion']['id'],
            $data['promotion']['title'],
            $data['promotion']['campaign_type'],
            $data['promotion']['status'],
            $data['promotion']['budget'],
            $data['promotion']['discount_percentage'],
            $data['promotion']['original_price'],
            $data['promotion']['promoted_price'],
            $data['promotion']['start_date'],
            $data['promotion']['end_date'],
        ]) . "\n";
        
        return $csv;
    }

    /**
     * Export data to XLSX format.
     */
    private function exportToXlsx(array $data): string
    {
        // Mock implementation
        return 'Mock XLSX content';
    }
}

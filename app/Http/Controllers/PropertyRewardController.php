<?php

namespace App\Http\Controllers;

use App\Models\PropertyReward;
use App\Models\UserPropertyGamification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Carbon\Carbon;

class PropertyRewardController extends Controller
{
    public function index(Request $request): View
    {
        $query = PropertyReward::withCount('redemptions');

        // Filter by search
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }

        // Filter by category
        if ($request->has('category')) {
            $query->where('category', $request->get('category'));
        }

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->get('type'));
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->get('status'));
        }

        // Filter by availability
        if ($request->has('available')) {
            $query->where('available', $request->get('available'));
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $rewards = $query->paginate(20);

        // Get statistics
        $stats = [
            'total_rewards' => PropertyReward::count(),
            'available_rewards' => PropertyReward::where('available', true)->count(),
            'total_redeemed' => PropertyReward::withCount('redemptions')->get()->sum('redemptions_count'),
            'total_points_spent' => \DB::table('user_rewards')->sum('points_cost'),
            'rewards_by_category' => PropertyReward::selectRaw('category, COUNT(*) as count')
                ->groupBy('category')
                ->get(),
            'rewards_by_type' => PropertyReward::selectRaw('type, COUNT(*) as count')
                ->groupBy('type')
                ->get(),
        ];

        return view('gamification.rewards.index', compact('rewards', 'stats'));
    }

    public function create(): View
    {
        return view('gamification.rewards.create');
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'type' => 'required|in:points,badge,discount,product,service,custom',
            'category' => 'required|string|max:100',
            'points_cost' => 'required|integer|min:0',
            'value' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive,limited',
            'available' => 'required|boolean',
            'stock_quantity' => 'nullable|integer|min:0',
            'max_redemptions_per_user' => 'nullable|integer|min:1',
            'expiry_date' => 'nullable|date|after:now',
            'image' => 'nullable|string|max:255',
            'terms_conditions' => 'nullable|string',
            'redemption_instructions' => 'nullable|string',
            'metadata' => 'nullable|array',
        ]);

        $reward = PropertyReward::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء المكافأة بنجاح',
            'data' => $reward
        ]);
    }

    public function show($id): View
    {
        $reward = PropertyReward::with(['redemptions' => function ($query) {
            $query->with('user')->orderBy('created_at', 'desc')->take(20);
        }])->findOrFail($id);

        // Get redemption statistics
        $redemptionStats = [
            'total_redemptions' => $reward->redemptions()->count(),
            'unique_users' => $reward->redemptions()->distinct('user_id')->count('user_id'),
            'total_points_spent' => $reward->redemptions()->sum('points_cost'),
            'redemptions_this_month' => $reward->redemptions()
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];

        return view('gamification.rewards.show', compact('reward', 'redemptionStats'));
    }

    public function edit($id): View
    {
        $reward = PropertyReward::findOrFail($id);
        return view('gamification.rewards.edit', compact('reward'));
    }

    public function update(Request $request, $id): JsonResponse
    {
        $reward = PropertyReward::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'type' => 'required|in:points,badge,discount,product,service,custom',
            'category' => 'required|string|max:100',
            'points_cost' => 'required|integer|min:0',
            'value' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive,limited',
            'available' => 'required|boolean',
            'stock_quantity' => 'nullable|integer|min:0',
            'max_redemptions_per_user' => 'nullable|integer|min:1',
            'expiry_date' => 'nullable|date|after:now',
            'image' => 'nullable|string|max:255',
            'terms_conditions' => 'nullable|string',
            'redemption_instructions' => 'nullable|string',
            'metadata' => 'nullable|array',
        ]);

        $reward->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث المكافأة بنجاح',
            'data' => $reward
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $reward = PropertyReward::findOrFail($id);
        
        // Check if reward has redemptions
        if ($reward->redemptions()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن حذف المكافأة التي تم استبدالها'
            ], 422);
        }

        $reward->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف المكافأة بنجاح'
        ]);
    }

    public function redeemReward(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'reward_id' => 'required|exists:property_rewards,id',
            'notes' => 'nullable|string|max:500',
        ]);

        $reward = PropertyReward::findOrFail($validated['reward_id']);
        $user = auth()->user();

        // Check if reward is available
        if (!$reward->available || $reward->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'المكافأة غير متاحة حالياً'
            ], 422);
        }

        // Check if reward is expired
        if ($reward->expiry_date && $reward->expiry_date->isPast()) {
            return response()->json([
                'success' => false,
                'message' => 'المكافأة منتهية الصلاحية'
            ], 422);
        }

        // Check stock quantity
        if ($reward->stock_quantity && $reward->stock_quantity <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'المكافأة غير متوفرة في المخزون'
            ], 422);
        }

        // Get user's gamification data
        $gamification = UserPropertyGamification::where('user_id', $user->id)->first();
        if (!$gamification || $gamification->total_points < $reward->points_cost) {
            return response()->json([
                'success' => false,
                'message' => 'ليس لديك نقاط كافية لهذه المكافأة'
            ], 422);
        }

        // Check max redemptions per user
        $userRedemptions = $reward->redemptions()->where('user_id', $user->id)->count();
        if ($reward->max_redemptions_per_user && $userRedemptions >= $reward->max_redemptions_per_user) {
            return response()->json([
                'success' => false,
                'message' => 'لقد وصلت إلى الحد الأقصى لاستبدال هذه المكافأة'
            ], 422);
        }

        // Process redemption
        $redemption = $reward->redemptions()->create([
            'user_id' => $user->id,
            'points_cost' => $reward->points_cost,
            'status' => 'pending',
            'notes' => $validated['notes'],
            'redeemed_at' => now(),
        ]);

        // Deduct points
        $gamification->total_points -= $reward->points_cost;
        $gamification->save();

        // Update stock if applicable
        if ($reward->stock_quantity) {
            $reward->stock_quantity--;
            $reward->save();
        }

        // Create points record
        \App\Models\PropertyPoints::create([
            'user_id' => $user->id,
            'points' => $reward->points_cost,
            'type' => 'penalty',
            'reason' => "استبدال مكافأة: {$reward->name}",
            'awarded_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم استبدال المكافأة بنجاح',
            'data' => $redemption
        ]);
    }

    public function getUserRedemptions($userId): JsonResponse
    {
        $user = \App\Models\User::findOrFail($userId);

        $redemptions = $user->rewards()
            ->with('reward')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = [
            'total_redemptions' => $user->rewards()->count(),
            'total_points_spent' => $user->rewards()->sum('points_cost'),
            'pending_redemptions' => $user->rewards()->where('status', 'pending')->count(),
            'completed_redemptions' => $user->rewards()->where('status', 'completed')->count(),
            'redemptions_by_category' => $user->rewards()
                ->join('property_rewards', 'user_rewards.reward_id', '=', 'property_rewards.id')
                ->selectRaw('property_rewards.category, COUNT(*) as count')
                ->groupBy('property_rewards.category')
                ->get(),
        ];

        return response()->json([
            'redemptions' => $redemptions,
            'stats' => $stats,
        ]);
    }

    public function getAvailableRewards($userId): JsonResponse
    {
        $user = \App\Models\User::findOrFail($userId);
        $gamification = UserPropertyGamification::where('user_id', $userId)->first();

        if (!$gamification) {
            return response()->json([]);
        }

        $availableRewards = PropertyReward::where('available', true)
            ->where('status', 'active')
            ->where(function ($query) use ($gamification) {
                $query->whereNull('expiry_date')
                      ->orWhere('expiry_date', '>', now());
            })
            ->where(function ($query) {
                $query->whereNull('stock_quantity')
                      ->orWhere('stock_quantity', '>', 0);
            })
            ->get()
            ->map(function ($reward) use ($gamification, $userId) {
                $userRedemptions = $reward->redemptions()->where('user_id', $userId)->count();
                
                $reward->can_afford = $gamification->total_points >= $reward->points_cost;
                $reward->can_redeem = $reward->can_afford && 
                    (!$reward->max_redemptions_per_user || $userRedemptions < $reward->max_redemptions_per_user) &&
                    (!$reward->stock_quantity || $reward->stock_quantity > 0);
                $reward->user_redemptions_count = $userRedemptions;
                $reward->points_shortage = max(0, $reward->points_cost - $gamification->total_points);
                
                return $reward;
            });

        return response()->json($availableRewards);
    }

    public function updateRedemptionStatus(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'redemption_id' => 'required|exists:user_rewards,id',
            'status' => 'required|in:pending,processing,completed,cancelled',
            'notes' => 'nullable|string|max:500',
        ]);

        $redemption = \App\Models\UserReward::findOrFail($validated['redemption_id']);
        
        $redemption->update([
            'status' => $validated['status'],
            'notes' => $validated['notes'],
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث حالة الاستبدال بنجاح',
            'data' => $redemption
        ]);
    }

    public function getRewardAnalytics(Request $request): JsonResponse
    {
        $period = $request->get('period', 'month');
        $dateRange = $this->getDateRange($period);

        $analytics = [
            'redemption_trends' => $this->getRedemptionTrends($dateRange),
            'popular_rewards' => $this->getPopularRewards($dateRange),
            'revenue_from_points' => $this->getRevenueFromPoints($dateRange),
            'category_performance' => $this->getCategoryPerformance($dateRange),
            'user_engagement' => $this->getUserEngagementStats($dateRange),
        ];

        return response()->json($analytics);
    }

    public function exportRedemptions(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'format' => 'required|in:csv,xlsx,pdf',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'reward_id' => 'nullable|exists:property_rewards,id',
            'status' => 'nullable|in:pending,processing,completed,cancelled',
        ]);

        $query = \App\Models\UserReward::with(['user', 'reward']);

        if (isset($validated['start_date'])) {
            $query->where('created_at', '>=', $validated['start_date']);
        }
        if (isset($validated['end_date'])) {
            $query->where('created_at', '<=', $validated['end_date']);
        }
        if (isset($validated['reward_id'])) {
            $query->where('reward_id', $validated['reward_id']);
        }
        if (isset($validated['status'])) {
            $query->where('status', $validated['status']);
        }

        $redemptions = $query->get();

        $filename = "redemptions_export_" . date('Y-m-d_H-i-s') . ".{$validated['format']}";

        return response()->json([
            'success' => true,
            'message' => 'تم تصدير عمليات الاستبدال بنجاح',
            'filename' => $filename,
            'count' => $redemptions->count(),
        ]);
    }

    private function getDateRange($period): array
    {
        $now = now();
        
        switch ($period) {
            case 'week':
                return [$now->startOfWeek(), $now->endOfWeek()];
            case 'month':
                return [$now->startOfMonth(), $now->endOfMonth()];
            case 'quarter':
                return [$now->startOfQuarter(), $now->endOfQuarter()];
            case 'year':
                return [$now->startOfYear(), $now->endOfYear()];
            default:
                return [$now->subMonth(), $now];
        }
    }

    private function getRedemptionTrends($dateRange): array
    {
        return \DB::table('user_rewards')
            ->whereBetween('created_at', $dateRange)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count, SUM(points_cost) as total_points')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->toArray();
    }

    private function getPopularRewards($dateRange): array
    {
        return \DB::table('user_rewards')
            ->join('property_rewards', 'user_rewards.reward_id', '=', 'property_rewards.id')
            ->whereBetween('user_rewards.created_at', $dateRange)
            ->selectRaw('property_rewards.name, property_rewards.category, COUNT(*) as redemption_count, SUM(user_rewards.points_cost) as total_points')
            ->groupBy('property_rewards.id', 'property_rewards.name', 'property_rewards.category')
            ->orderBy('redemption_count', 'desc')
            ->take(10)
            ->get()
            ->toArray();
    }

    private function getRevenueFromPoints($dateRange): array
    {
        return \DB::table('user_rewards')
            ->whereBetween('created_at', $dateRange)
            ->selectRaw('SUM(points_cost) as total_points, COUNT(*) as total_redemptions')
            ->first()
            ->toArray();
    }

    private function getCategoryPerformance($dateRange): array
    {
        return \DB::table('user_rewards')
            ->join('property_rewards', 'user_rewards.reward_id', '=', 'property_rewards.id')
            ->whereBetween('user_rewards.created_at', $dateRange)
            ->selectRaw('property_rewards.category, COUNT(*) as redemption_count, SUM(user_rewards.points_cost) as total_points')
            ->groupBy('property_rewards.category')
            ->orderBy('total_points', 'desc')
            ->get()
            ->toArray();
    }

    private function getUserEngagementStats($dateRange): array
    {
        return [
            'unique_users' => \DB::table('user_rewards')
                ->whereBetween('created_at', $dateRange)
                ->distinct('user_id')
                ->count('user_id'),
            'repeat_users' => \DB::table('user_rewards')
                ->whereBetween('created_at', $dateRange)
                ->selectRaw('user_id, COUNT(*) as redemption_count')
                ->having('redemption_count', '>', 1)
                ->distinct('user_id')
                ->count('user_id'),
            'average_redemptions_per_user' => \DB::table('user_rewards')
                ->whereBetween('created_at', $dateRange)
                ->selectRaw('COUNT(*) / COUNT(DISTINCT user_id) as avg_redemptions')
                ->first()
                ->avg_redemptions,
        ];
    }
}

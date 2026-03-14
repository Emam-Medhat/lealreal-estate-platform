<?php

namespace App\Services;

use App\Models\Property;
use App\Models\User;
use App\Models\UserActivityLog;
use App\Models\UserComparison;
use App\Models\UserFavorite;
use App\Models\UserNotification;
use App\Models\UserWallet;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class DashboardService
{
    /**
     * Get user dashboard statistics and data with caching.
     */
    public function getUserDashboardData(User $user): array
    {
        $cacheKey = "user_dashboard_data_{$user->id}";

        return Cache::remember($cacheKey, 300, function () use ($user) {
            $today = Carbon::today();

            // Stats from Activity Logs (Optimized: use range comparison instead of whereDate for index utilization)
            $activityStats = UserActivityLog::where('user_id', $user->id)
                ->where('created_at', '>=', $today)
                ->selectRaw("
                    COUNT(CASE WHEN action = 'property_view' THEN 1 END) as views_today,
                    COUNT(CASE WHEN action = 'search' THEN 1 END) as searches_today
                ")
                ->first();

            // Total Searches (Optimization: Separate query instance to avoid pollution)
            $totalSearches = UserActivityLog::where('user_id', $user->id)
                ->where('action', 'search')
                ->count();

            // Recent Activity
            $recentActivity = UserActivityLog::where('user_id', $user->id)
                ->latest()
                ->limit(5)
                ->get()
                ->map(function ($activity) {
                    return [
                        'icon' => $this->getActivityIcon($activity->action),
                        'message' => $activity->details ?? 'Activity logged',
                        'time' => $activity->created_at->diffForHumans(),
                    ];
                });

            // Recently Saved Properties (Eager Loading favoritable.media to avoid N+1 in main_image accessor)
            $favoriteProperties = UserFavorite::where('user_id', $user->id)
                ->with(['favoritable.media'])
                ->latest()
                ->limit(6)
                ->get();

            $recentlySaved = $favoriteProperties->map(function ($favorite) {
                $property = $favorite->favoritable;

                return [
                    'title' => $property->title ?? 'Unknown Property',
                    'price' => $property->price ?? 0,
                    'image' => $property->main_image ?? null,
                ];
            });

            // Optimized: Consolidated count for favorites and comparisons
            $favoriteStats = UserFavorite::where('user_id', $user->id)
                ->selectRaw('
                    COUNT(*) as total,
                    COUNT(CASE WHEN created_at >= ? THEN 1 END) as today
                ', [$today])
                ->first();

            // Notifications
            $unreadNotifications = UserNotification::where('user_id', $user->id)
                ->where('is_read', false)
                ->latest()
                ->limit(5)
                ->get();

            $latestNotifications = $unreadNotifications->map(function ($notification) {
                return [
                    'message' => $notification->message,
                    'time' => $notification->created_at->diffForHumans(),
                ];
            });

            // Optimized: Consolidated count for notifications
            $notificationStats = UserNotification::where('user_id', $user->id)
                ->selectRaw('
                    COUNT(*) as total,
                    COUNT(CASE WHEN is_read = ? THEN 1 END) as unread
                ', [false])
                ->first();

            // Comparisons (Corrected relationship name to comparableItems and consolidated count)
            $comparisonStats = UserComparison::where('user_id', $user->id)
                ->selectRaw('COUNT(*) as total')
                ->first();

            $comparisons = UserComparison::where('user_id', $user->id)
                ->with('comparableItems.media')
                ->latest()
                ->limit(3)
                ->get();

            // Wallet
            $wallet = UserWallet::where('user_id', $user->id)->first();

            // User Properties (if agent)
            $userProperties = Property::whereHas('agent', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
                ->with('media')
                ->latest()
                ->limit(5)
                ->get();

            // Aggregated Statistics
            return [
                'properties_viewed' => $user->properties_views_count ?? 0,
                'properties_viewed_today' => $activityStats->views_today ?? 0,
                'saved_properties' => $favoriteStats->total ?? 0,
                'saved_today' => $favoriteStats->today ?? 0,
                'searches' => $totalSearches,
                'searches_today' => $activityStats->searches_today ?? 0,
                'notifications' => $notificationStats->total ?? 0,
                'unread_notifications' => $notificationStats->unread ?? 0,
                'comparisons_count' => $comparisonStats->total ?? 0,
                'recent_activity' => $recentActivity,
                'recently_saved' => $recentlySaved,
                'latest_notifications' => $latestNotifications,
                'recommendations' => [],
                'favorite_properties' => $favoriteProperties,
                'comparisons' => $comparisons,
                'unread_notifications_list' => $unreadNotifications,
                'wallet' => $wallet,
                'user_properties' => $userProperties,
                'wallet_balance' => $wallet->balance ?? 0,
            ];
        });
    }

    /**
     * Get quick stats for the user.
     */
    public function getQuickStats(User $user): array
    {
        $cacheKey = "user_quick_stats_{$user->id}";

        return Cache::remember($cacheKey, 60, function () use ($user) {
            $wallet = UserWallet::where('user_id', $user->id)->first();

            return [
                'favorites_count' => UserFavorite::where('user_id', $user->id)->count(),
                'comparisons_count' => UserComparison::where('user_id', $user->id)->count(),
                'properties_count' => Property::whereHas('agent', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })->count(),
                'notifications_count' => UserNotification::where('user_id', $user->id)
                    ->where('is_read', false)
                    ->count(),
                'wallet_balance' => $wallet->balance ?? 0,
            ];
        });
    }

    /**
     * Get activity icon based on action.
     */
    private function getActivityIcon(string $action): string
    {
        return match ($action) {
            'property_view' => 'fas fa-eye',
            'search' => 'fas fa-search',
            'property_favorite' => 'fas fa-heart',
            'comparison_add' => 'fas fa-exchange-alt',
            'profile_update' => 'fas fa-user-edit',
            'login' => 'fas fa-sign-in-alt',
            default => 'fas fa-info-circle',
        };
    }
}

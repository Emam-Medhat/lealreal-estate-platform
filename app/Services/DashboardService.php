<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserActivityLog;
use App\Models\UserFavorite;
use App\Models\UserComparison;
use App\Models\UserNotification;
use App\Models\UserWallet;
use App\Models\Property;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class DashboardService
{
    /**
     * Get user dashboard statistics and data with caching.
     *
     * @param User $user
     * @return array
     */
    public function getUserDashboardData(User $user): array
    {
        $cacheKey = "user_dashboard_data_{$user->id}";
        
        return Cache::remember($cacheKey, 300, function () use ($user) {
            $today = Carbon::today();
            
            /**
             * PERFORMANCE OPTIMIZATION:
             * 1. Using range comparison (>= $today) instead of whereDate() to utilize composite index on [user_id, created_at].
             * 2. Aggregating today's stats and total searches in a more optimized way if possible.
             */
            $activityStats = UserActivityLog::where('user_id', $user->id)
                ->where('created_at', '>=', $today)
                ->selectRaw("
                    COUNT(CASE WHEN action = 'property_view' THEN 1 END) as views_today,
                    COUNT(CASE WHEN action = 'search' THEN 1 END) as searches_today
                ")
                ->first();

            // Total Stats - using index on [user_id, action]
            $totalSearches = UserActivityLog::where('user_id', $user->id)
                ->where('action', 'search')
                ->count();

            // Recent Activity - using index on [user_id, created_at] (implicitly by latest() which uses created_at)
            $recentActivity = UserActivityLog::where('user_id', $user->id)
                ->latest()
                ->limit(5)
                ->get()
                ->map(function ($activity) {
                    return [
                        'icon' => $this->getActivityIcon($activity->action),
                        'message' => $activity->details ?? 'Activity logged', // Changed description to details
                        'time' => $activity->created_at->diffForHumans(),
                    ];
                });

            // Recently Saved Properties (Eager Loading)
            $favoriteProperties = UserFavorite::where('user_id', $user->id)
                ->with(['favoritable']) // Ensure favoritable is loaded
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

            // Comparisons
            $comparisons = UserComparison::where('user_id', $user->id)
                ->with('properties')
                ->latest()
                ->limit(3)
                ->get();

            // Wallet
            $wallet = UserWallet::where('user_id', $user->id)->first();

            // User Properties (if agent)
            $userProperties = Property::whereHas('agent', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
                ->latest()
                ->limit(5)
                ->get();

            /**
             * PERFORMANCE OPTIMIZATION:
             * Consolidated multiple count queries into a single query with conditional aggregation where it makes sense,
             * and used range comparisons for date filters to leverage composite indexes.
             */
            $favoriteStats = UserFavorite::where('user_id', $user->id)
                ->selectRaw("
                    COUNT(*) as total,
                    COUNT(CASE WHEN created_at >= ? THEN 1 END) as today
                ", [$today->toDateTimeString()])
                ->first();

            $notificationStats = UserNotification::where('user_id', $user->id)
                ->selectRaw("
                    COUNT(*) as total,
                    COUNT(CASE WHEN is_read = 0 THEN 1 END) as unread
                ")
                ->first();

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
                'comparisons_count' => UserComparison::where('user_id', $user->id)->count(),
                'recent_activity' => $recentActivity,
                'recently_saved' => $recentlySaved,
                'latest_notifications' => $latestNotifications,
                'recommendations' => [], // To be implemented
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
     *
     * @param User $user
     * @return array
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
     *
     * @param string $action
     * @return string
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

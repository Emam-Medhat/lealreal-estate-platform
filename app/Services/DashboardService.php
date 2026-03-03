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
            
            // Consolidate activity and favorite counts into a single query if possible,
            // but since they are different tables, we'll keep them separate but optimized.

            // Activity Stats: Consolidating views today, searches today and total searches
            // Optimization: Use >= instead of whereDate for better index utilization
            $activityStats = UserActivityLog::where('user_id', $user->id)
                ->selectRaw("
                    COUNT(CASE WHEN created_at >= ? AND action = 'property_view' THEN 1 END) as views_today,
                    COUNT(CASE WHEN created_at >= ? AND action = 'search' THEN 1 END) as searches_today,
                    COUNT(CASE WHEN action = 'search' THEN 1 END) as total_searches
                ", [$today, $today])
                ->first();

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

            // Recently Saved Properties (Eager Loading)
            $favoriteProperties = UserFavorite::where('user_id', $user->id)
                ->with(['favoritable'])
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

            // Consolidate Favorite Stats
            $favoriteStats = UserFavorite::where('user_id', $user->id)
                ->selectRaw("
                    COUNT(*) as total_favorites,
                    COUNT(CASE WHEN created_at >= ? THEN 1 END) as saved_today
                ", [$today])
                ->first();

            // Consolidate Notification Stats and Unread List
            $unreadNotifications = UserNotification::where('user_id', $user->id)
                ->where('is_read', false)
                ->latest()
                ->limit(5)
                ->get();

            $notificationStats = UserNotification::where('user_id', $user->id)
                ->selectRaw("
                    COUNT(*) as total_notifications,
                    COUNT(CASE WHEN is_read = ? THEN 1 END) as unread_count
                ", [false])
                ->first();

            $latestNotifications = $unreadNotifications->map(function ($notification) {
                return [
                    'message' => $notification->message,
                    'time' => $notification->created_at->diffForHumans(),
                ];
            });

            // Comparisons
            $comparisonStats = UserComparison::where('user_id', $user->id)
                ->selectRaw("COUNT(*) as total_comparisons")
                ->first();

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

            // Aggregated Statistics
            return [
                'properties_viewed' => $user->properties_views_count ?? 0,
                'properties_viewed_today' => $activityStats->views_today ?? 0,
                'saved_properties' => $favoriteStats->total_favorites ?? 0,
                'saved_today' => $favoriteStats->saved_today ?? 0,
                'searches' => $activityStats->total_searches ?? 0,
                'searches_today' => $activityStats->searches_today ?? 0,
                'notifications' => $notificationStats->total_notifications ?? 0,
                'unread_notifications' => $notificationStats->unread_count ?? 0,
                'comparisons_count' => $comparisonStats->total_comparisons ?? 0,
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
     *
     * @param User $user
     * @return array
     */
    public function getQuickStats(User $user): array
    {
        $cacheKey = "user_quick_stats_{$user->id}";

        return Cache::remember($cacheKey, 60, function () use ($user) {
            // Optimized quick stats with consolidated queries
            $favoriteCount = UserFavorite::where('user_id', $user->id)->count();
            $comparisonCount = UserComparison::where('user_id', $user->id)->count();

            $propertyCount = Property::whereHas('agent', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->count();

            $notificationCount = UserNotification::where('user_id', $user->id)
                ->where('is_read', false)
                ->count();

            $wallet = UserWallet::where('user_id', $user->id)->select('balance')->first();
            
            return [
                'favorites_count' => $favoriteCount,
                'comparisons_count' => $comparisonCount,
                'properties_count' => $propertyCount,
                'notifications_count' => $notificationCount,
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

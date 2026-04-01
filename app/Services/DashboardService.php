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
            $today = Carbon::today()->toDateTimeString();
            
            // Stats from Activity Logs: Combining daily counts into one query
            // We keep the where condition for the user to use the user_id index
            // We use parameterized queries to avoid string interpolation in selectRaw
            $activityStats = UserActivityLog::where('user_id', $user->id)
                ->selectRaw("
                    COUNT(CASE WHEN action = 'property_view' AND created_at >= ? THEN 1 END) as views_today,
                    COUNT(CASE WHEN action = 'search' AND created_at >= ? THEN 1 END) as searches_today,
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

            // Recently Saved Properties (Eager Loading with nested media to avoid N+1)
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

            // Notifications: Combining counts using conditional aggregation
            $notificationsStats = UserNotification::where('user_id', $user->id)
                ->selectRaw("
                    COUNT(*) as total_notifications,
                    COUNT(CASE WHEN is_read = 0 THEN 1 END) as unread_count
                ")
                ->first();

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

            // Comparisons: Memory confirms relationship is comparableItems
            // but we'll stick to properties if that was original code's intention
            // for backward compatibility, except we verified comparableItems exists
            // in the model and properties does not.
            $comparisons = UserComparison::where('user_id', $user->id)
                ->with('comparableItems')
                ->latest()
                ->limit(3)
                ->get();

            // Wallet and Comparison counts
            $wallet = UserWallet::where('user_id', $user->id)->first();
            $comparisonsCount = UserComparison::where('user_id', $user->id)->count();

            // User Favorites counts: Consolidated using conditional aggregation
            $favoriteStats = UserFavorite::where('user_id', $user->id)
                ->selectRaw("
                    COUNT(*) as total_favorites,
                    COUNT(CASE WHEN created_at >= ? THEN 1 END) as saved_today
                ", [$today])
                ->first();

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
                'notifications' => $notificationsStats->total_notifications ?? 0,
                'unread_notifications' => $notificationsStats->unread_count ?? 0,
                'comparisons_count' => $comparisonsCount,
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

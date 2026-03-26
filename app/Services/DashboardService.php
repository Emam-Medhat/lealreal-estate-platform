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
use Illuminate\Support\Facades\DB;

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

            // Performance: Consolidate UserActivityLog counts to reduce database round-trips
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

            // Performance: Eager load 'favoritable.media' to prevent N+1 queries when accessing 'main_image' accessor
            $favoriteProperties = UserFavorite::where('user_id', $user->id)
                ->with(['favoritable.media'])
                ->latest()
                ->limit(6)
                ->get();

            // Performance: Consolidate UserFavorite counts
            $favoriteStats = UserFavorite::where('user_id', $user->id)
                ->selectRaw('
                    COUNT(*) as total_saved,
                    COUNT(CASE WHEN created_at >= ? THEN 1 END) as saved_today
                ', [$today])
                ->first();

            $recentlySaved = $favoriteProperties->map(function ($favorite) {
                $property = $favorite->favoritable;

                return [
                    'title' => $property->title ?? 'Unknown Property',
                    'price' => $property->price ?? 0,
                    'image' => $property->main_image ?? null,
                ];
            });

            // Performance: Consolidate UserNotification counts
            $notificationStats = UserNotification::where('user_id', $user->id)
                ->selectRaw('
                    COUNT(*) as total_notifications,
                    COUNT(CASE WHEN is_read = ? THEN 1 END) as unread_notifications
                ', [false])
                ->first();

            // Fetch recent unread notifications
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

            // Comparisons - Performance: Fixed relationship name 'properties' to 'comparableItems'
            $comparisons = UserComparison::where('user_id', $user->id)
                ->with('comparableItems')
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
                'saved_properties' => $favoriteStats->total_saved ?? 0,
                'saved_today' => $favoriteStats->saved_today ?? 0,
                'searches' => $activityStats->total_searches ?? 0,
                'searches_today' => $activityStats->searches_today ?? 0,
                'notifications' => $notificationStats->total_notifications ?? 0,
                'unread_notifications' => $notificationStats->unread_notifications ?? 0,
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
     */
    public function getQuickStats(User $user): array
    {
        $cacheKey = "user_quick_stats_{$user->id}";

        return Cache::remember($cacheKey, 60, function () use ($user) {
            $wallet = UserWallet::where('user_id', $user->id)->first();

            // Performance: Consolidate multiple count queries into a single data structure
            $stats = DB::table('users')
                ->where('users.id', $user->id)
                ->select([
                    DB::raw("(SELECT COUNT(*) FROM user_favorites WHERE user_id = {$user->id}) as favorites_count"),
                    DB::raw("(SELECT COUNT(*) FROM user_comparisons WHERE user_id = {$user->id}) as comparisons_count"),
                    DB::raw("(SELECT COUNT(*) FROM user_notifications WHERE user_id = {$user->id} AND is_read = 0) as notifications_count"),
                    DB::raw("(SELECT COUNT(*) FROM properties p JOIN agents a ON p.agent_id = a.id WHERE a.user_id = {$user->id}) as properties_count"),
                ])
                ->first();

            return [
                'favorites_count' => (int) ($stats->favorites_count ?? 0),
                'comparisons_count' => (int) ($stats->comparisons_count ?? 0),
                'properties_count' => (int) ($stats->properties_count ?? 0),
                'notifications_count' => (int) ($stats->notifications_count ?? 0),
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

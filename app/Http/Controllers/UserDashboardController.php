<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Property;
use App\Models\UserFavorite;
use App\Models\UserComparison;
use App\Models\UserActivityLog;
use App\Models\UserNotification;
use App\Models\UserWallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Get user's recent activity
        $recentActivity = UserActivityLog::where('user_id', $user->id)
            ->latest()
            ->limit(10)
            ->get();

        // Get user's favorite properties
        $favoriteProperties = UserFavorite::where('user_id', $user->id)
            ->with('property')
            ->latest()
            ->limit(6)
            ->get();

        // Get user's property comparisons
        $comparisons = UserComparison::where('user_id', $user->id)
            ->with('properties')
            ->latest()
            ->limit(3)
            ->get();

        // Get unread notifications
        $unreadNotifications = UserNotification::where('user_id', $user->id)
            ->where('is_read', false)
            ->latest()
            ->limit(5)
            ->get();

        // Get wallet balance if exists
        $wallet = UserWallet::where('user_id', $user->id)->first();

        // Get user's properties if they are an agent/developer
        $userProperties = Property::where('user_id', $user->id)
            ->latest()
            ->limit(5)
            ->get();

        // Statistics
        $stats = [
            'total_favorites' => UserFavorite::where('user_id', $user->id)->count(),
            'total_comparisons' => UserComparison::where('user_id', $user->id)->count(),
            'total_properties' => Property::where('user_id', $user->id)->count(),
            'unread_notifications' => UserNotification::where('user_id', $user->id)
                ->where('is_read', false)
                ->count(),
        ];

        return view('user.dashboard', compact(
            'user',
            'recentActivity',
            'favoriteProperties',
            'comparisons',
            'unreadNotifications',
            'wallet',
            'userProperties',
            'stats'
        ));
    }

    public function getQuickStats(Request $request)
    {
        $user = Auth::user();
        
        $stats = [
            'favorites_count' => UserFavorite::where('user_id', $user->id)->count(),
            'comparisons_count' => UserComparison::where('user_id', $user->id)->count(),
            'properties_count' => Property::where('user_id', $user->id)->count(),
            'notifications_count' => UserNotification::where('user_id', $user->id)
                ->where('is_read', false)
                ->count(),
        ];

        if ($wallet = UserWallet::where('user_id', $user->id)->first()) {
            $stats['wallet_balance'] = $wallet->balance;
        }

        return response()->json($stats);
    }

    public function getRecentActivity(Request $request)
    {
        $activities = UserActivityLog::where('user_id', Auth::id())
            ->latest()
            ->limit($request->limit ?? 10)
            ->get();

        return response()->json($activities);
    }

    public function markNotificationAsRead(UserNotification $notification)
    {
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }

        $notification->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    public function markAllNotificationsAsRead()
    {
        UserNotification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    public function getWalletInfo()
    {
        $wallet = UserWallet::where('user_id', Auth::id())->first();
        
        if (!$wallet) {
            return response()->json([
                'balance' => 0,
                'currency' => 'USD',
                'last_transaction' => null
            ]);
        }

        return response()->json([
            'balance' => $wallet->balance,
            'currency' => $wallet->currency,
            'last_transaction' => $wallet->transactions()->latest()->first()
        ]);
    }
}

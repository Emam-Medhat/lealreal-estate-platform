<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserFavorite;
use App\Models\UserComparison;
use App\Models\UserNotification;
use App\Models\UserWallet;
use App\Models\UserActivityLog;
use App\Models\Property;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class UserDashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Get all dashboard data from service (optimized and cached)
        $stats = $this->dashboardService->getUserDashboardData($user);

        return view('user.dashboard', [
            'user' => $user,
            'stats' => $stats
        ]);
    }


    public function getQuickStats(Request $request)
    {
        $user = Auth::user();
        $stats = $this->dashboardService->getQuickStats($user);

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

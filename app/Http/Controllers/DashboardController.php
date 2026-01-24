<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the user dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get user statistics based on role
        $stats = $this->getUserStats($user);
        
        return view('dashboard.index', compact('user', 'stats'));
    }

    /**
     * Show the user profile.
     */
    public function profile()
    {
        $user = Auth::user();
        return view('dashboard.profile', compact('user'));
    }

    /**
     * Update user profile.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:20', 'unique:users,phone,' . $user->id],
            'whatsapp' => ['nullable', 'string', 'max:20'],
            'country' => ['required', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:100'],
            'language' => ['required', 'string', 'in:ar,en,fr'],
            'currency' => ['required', 'string', 'in:EGP,SAR,AED,USD,EUR'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif', 'max:2048'],
        ]);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $avatarPath;
        }

        $user->update($validated);

        return redirect()->route('dashboard.profile')
            ->with('success', 'تم تحديث ملفك الشخصي بنجاح');
    }

    /**
     * Show settings page.
     */
    public function settings()
    {
        $user = Auth::user();
        return view('dashboard.settings', compact('user'));
    }

    /**
     * Update user settings.
     */
    public function updateSettings(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'marketing_consent' => ['nullable', 'boolean'],
            'newsletter_subscribed' => ['nullable', 'boolean'],
            'sms_notifications' => ['nullable', 'boolean'],
            'two_factor_enabled' => ['nullable', 'boolean'],
        ]);

        $user->update($validated);

        return redirect()->route('dashboard.settings')
            ->with('success', 'تم تحديث الإعدادات بنجاح');
    }

    /**
     * Get user statistics based on role.
     */
    private function getUserStats(User $user)
    {
        $stats = [
            'properties_count' => $user->properties_count ?? 0,
            'favorites_count' => $user->favorites_count ?? 0,
            'wallet_balance' => $user->wallet_balance ?? 0,
            'login_count' => $user->login_count ?? 0,
        ];

        // Add role-specific stats
        if ($user->is_agent) {
            $stats['properties_sold'] = $user->properties_sold ?? 0;
            $stats['properties_rented'] = $user->properties_rented ?? 0;
            $stats['total_commission'] = $user->total_commission_earned ?? 0;
            $stats['client_count'] = $user->client_count ?? 0;
        }

        if ($user->is_investor) {
            $stats['properties_invested'] = $user->properties_invested ?? 0;
            $stats['total_investments'] = $user->total_investments ?? 0;
            $stats['investment_returns'] = $user->investment_returns ?? 0;
        }

        return $stats;
    }
}

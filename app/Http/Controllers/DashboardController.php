<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Property;
use App\Models\Agent;
use App\Models\Company;
use App\Models\Project;
use App\Models\ProjectTask;
use App\Models\Investor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the comprehensive dashboard with all site controls.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Redirect to appropriate dashboard based on user role
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->is_agent) {
            return redirect()->route('agents.dashboard');
        } else {
            // Regular user dashboard
            $stats = $this->getComprehensiveStats($user);
            return view('dashboard.index', compact('user', 'stats'));
        }
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
     * Get comprehensive statistics for all site controls.
     */
    private function getComprehensiveStats(User $user)
    {
        // Cache heavy site-wide counts for 5 minutes
        $siteStats = \Illuminate\Support\Facades\Cache::remember(
            'dashboard_site_stats',
            300,
            fn() => [
                'total_users'          => User::count(),
                'total_properties'     => Property::count(),
                'total_agents'         => \App\Models\Agent::count(),
                'total_companies'      => \App\Models\Company::count(),
                'total_projects'       => $this->safeCount('Project'),
                'total_tasks'          => $this->safeCount('ProjectTask'),
                'total_investors'      => \App\Models\Investor::count(),
                'new_users_today'      => User::whereDate('created_at', today())->count(),
                'new_properties_today' => Property::whereDate('created_at', today())->count(),
                'new_investors_today'  => \App\Models\Investor::whereDate('created_at', today())->count(),
                'new_companies_today'  => \App\Models\Company::whereDate('created_at', today())->count(),
                'total_revenue'        => 0,
                'revenue_today'        => 0,
            ]
        );

        $stats = [
            'user' => [
                'properties_count'      => $user->properties_count ?? 0,
                'favorites_count'       => $user->favorites_count ?? 0,
                'wallet_balance'        => $user->wallet_balance ?? 0,
                'login_count'           => $user->login_count ?? 0,
                'unread_notifications'  => $user->unreadNotifications()->count(),
            ],
            
            'site' => $siteStats,
            
            // Recent activity — cached 2 minutes
            'recent_users'      => \Illuminate\Support\Facades\Cache::remember('dashboard_recent_users', 120, fn() => User::latest()->take(5)->get()),
            'recent_properties' => \Illuminate\Support\Facades\Cache::remember('dashboard_recent_props', 120, fn() => Property::latest()->take(5)->get()),
            'recent_projects'   => $this->safeGet('Project', 5),
            'recent_tasks'      => $this->safeGet('ProjectTask', 5),
            
            'quick_links' => [
                'users_management' => [
                    'title' => 'إدارة المستخدمين',
                    'icon'  => 'fas fa-users',
                    'color' => 'blue',
                    'links' => [
                        ['title' => 'جميع المستخدمين', 'route' => 'admin.users.index',   'icon' => 'fas fa-list'],
                        ['title' => 'إضافة مستخدم',    'route' => 'admin.users.create',  'icon' => 'fas fa-plus'],
                        ['title' => 'الوكلاء',          'route' => 'admin.agents.index',  'icon' => 'fas fa-user-tie'],
                        ['title' => 'الشركات',          'route' => 'companies.index',     'icon' => 'fas fa-building'],
                    ]
                ],
                'properties_management' => [
                    'title' => 'إدارة العقارات',
                    'icon'  => 'fas fa-home',
                    'color' => 'green',
                    'links' => [
                        ['title' => 'جميع العقارات', 'route' => 'properties.index',        'icon' => 'fas fa-list'],
                        ['title' => 'إضافة عقار',    'route' => 'properties.create',       'icon' => 'fas fa-plus'],
                        ['title' => 'المفضلة',        'route' => 'properties.favorites',    'icon' => 'fas fa-heart'],
                        ['title' => 'البحث المتقدم',  'route' => 'properties.search.index','icon' => 'fas fa-search'],
                    ]
                ],
                'projects_management' => [
                    'title' => 'إدارة المشاريع',
                    'icon'  => 'fas fa-project-diagram',
                    'color' => 'purple',
                    'links' => [
                        ['title' => 'جميع المشاريع', 'route' => 'projects.index',  'icon' => 'fas fa-list'],
                        ['title' => 'إضافة مشروع',   'route' => 'projects.create', 'icon' => 'fas fa-plus'],
                    ]
                ],
                'system_management' => [
                    'title' => 'إدارة النظام',
                    'icon'  => 'fas fa-cogs',
                    'color' => 'yellow',
                    'links' => [
                        ['title' => 'الإعدادات', 'route' => 'settings.index',        'icon' => 'fas fa-cog'],
                        ['title' => 'الصيانة',   'route' => 'maintenance.index',     'icon' => 'fas fa-tools'],
                        ['title' => 'التقارير',   'route' => 'reports.index',         'icon' => 'fas fa-chart-bar'],
                        ['title' => 'السجلات',    'route' => 'admin.settings.logs',   'icon' => 'fas fa-file-alt'],
                    ]
                ],
            ],
            
            'system_status' => [
                'database' => 'Online',
                'storage'  => '65% Used',
                'api'      => 'Online',
                'queue'    => '12 Jobs',
                'cache'    => 'Active',
                'mail'     => 'Configured',
            ],
            
            'charts' => [
                'user_growth'     => [12, 19, 3, 5, 2, 3],
                'property_growth' => [8, 15, 12, 18, 25, 20],
                'revenue'         => [12000, 19000, 30000, 50000, 42000, 38000],
            ]
        ];

        // Add role-specific stats
        if ($user->is_agent) {
            $stats['user']['properties_sold']   = $user->properties_sold ?? 0;
            $stats['user']['properties_rented'] = $user->properties_rented ?? 0;
            $stats['user']['total_commission']  = $user->total_commission_earned ?? 0;
            $stats['user']['client_count']      = $user->client_count ?? 0;
        }

        if ($user->is_investor) {
            $stats['user']['properties_invested'] = $user->properties_invested ?? 0;
            $stats['user']['total_investments']   = $user->total_investments ?? 0;
            $stats['user']['investment_returns']  = $user->investment_returns ?? 0;
        }

        return $stats;
    }

    /**
     * Safely count records from a table that might not exist
     */
    private function safeCount($model)
    {
        try {
            $modelClass = "App\\Models\\{$model}";
            if (class_exists($modelClass)) {
                return $modelClass::count();
            }
        } catch (\Exception $e) {
            // Table doesn't exist or other database error
        }
        return 0;
    }

    /**
     * Safely get records from a table that might not exist
     */
    private function safeGet($model, $limit = 5)
    {
        try {
            $modelClass = "App\\Models\\{$model}";
            if (class_exists($modelClass)) {
                return $modelClass::latest()->take($limit)->get();
            }
        } catch (\Exception $e) {
            // Table doesn't exist or other database error
        }
        return collect();
    }
}

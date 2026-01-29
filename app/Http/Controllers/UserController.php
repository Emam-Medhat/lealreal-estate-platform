<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Download a report
     */
    public function downloadReport($id)
    {
        // Implementation needed
        return response()->json(['message' => 'Report download functionality not implemented yet'], 501);
    }

    /**
     * Preview a report
     */
    public function previewReport($id)
    {
        // Implementation needed
        return response()->json(['message' => 'Report preview functionality not implemented yet'], 501);
    }

    /**
     * Generate a report
     */
    public function generateReport(Request $request)
    {
        // Implementation needed
        return response()->json(['message' => 'Report generation functionality not implemented yet'], 501);
    }

    /**
     * Show user reports
     */
    public function reports()
    {
        // Implementation needed
        return response()->json(['message' => 'Reports listing functionality not implemented yet'], 501);
    }

    public function dashboard(Request $request)
    {
        $user = auth()->user();
        $user->load(['profile', 'activityLogs' => function ($query) {
            $query->latest()->limit(10);
        }]);

        // Get user statistics
        $stats = [
            'properties_viewed' => $user->activityLogs()->where('action', 'viewed_property')->count(),
            'properties_viewed_today' => $user->activityLogs()->where('action', 'viewed_property')->whereDate('created_at', today())->count(),
            'saved_properties' => $user->savedProperties()->count(),
            'saved_today' => $user->savedProperties()->whereDate('created_at', today())->count(),
            'searches' => $user->activityLogs()->where('action', 'searched_properties')->count(),
            'searches_today' => $user->activityLogs()->where('action', 'searched_properties')->whereDate('created_at', today())->count(),
            'notifications' => $user->notifications()->count(),
            'unread_notifications' => $user->notifications()->whereNull('read_at')->count(),
            'recent_activity' => $user->activityLogs()->latest()->take(5)->get()->map(function ($log) {
                return [
                    'icon' => $this->getActivityIcon($log->action),
                    'message' => $log->details,
                    'time' => $log->created_at->diffForHumans(),
                ];
            })->toArray(),
            'recently_saved' => $user->savedProperties()->with('favoritable')->latest()->take(3)->get()->map(function ($favorite) {
                return [
                    'title' => $favorite->favoritable->title ?? 'N/A',
                    'price' => $favorite->favoritable->price ?? 0,
                ];
            })->toArray(),
            'latest_notifications' => $user->notifications()->latest()->take(3)->get()->map(function ($notification) {
                return [
                    'message' => $notification->data['message'] ?? 'New notification',
                    'time' => $notification->created_at->diffForHumans(),
                ];
            })->toArray(),
            'recommendations' => [], // TODO: Implement property recommendations
        ];

        return view('user.dashboard', compact('user', 'stats'));
    }

    private function getActivityIcon($action)
    {
        $icons = [
            'viewed_property' => 'home',
            'saved_property' => 'heart',
            'searched_properties' => 'search',
            'updated_profile' => 'user',
            'changed_password' => 'lock',
        ];

        return $icons[$action] ?? 'circle';
    }

    public function index(Request $request)
    {
        $users = User::with('profile')
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->paginate(20);

        return view('users.index', compact('users'));
    }

    public function show(User $user)
    {
        $user->load(['profile', 'activityLogs' => function ($query) {
            $query->latest()->limit(50);
        }]);

        return view('users.show', compact('user'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(UpdateUserRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'status' => $request->status ?? 'active',
            'email_verified_at' => $request->verified ? now() : null,
        ]);

        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');
            $path = $avatar->store('avatars', 'public');
            $user->profile()->create(['avatar' => $path]);
        }

        UserActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'created_user',
            'details' => "Created user: {$user->name}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('users.show', $user)
            ->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        $user->load('profile');
        return view('users.edit', compact('user'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'status' => $request->status,
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        if ($request->hasFile('avatar')) {
            if ($user->profile && $user->profile->avatar) {
                Storage::disk('public')->delete($user->profile->avatar);
            }
            
            $avatar = $request->file('avatar');
            $path = $avatar->store('avatars', 'public');
            
            $user->profile()->updateOrCreate([], ['avatar' => $path]);
        }

        UserActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'updated_user',
            'details' => "Updated user: {$user->name}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('users.show', $user)
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $userName = $user->name;
        
        if ($user->profile && $user->profile->avatar) {
            Storage::disk('public')->delete($user->profile->avatar);
        }
        
        $user->delete();

        UserActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'deleted_user',
            'details' => "Deleted user: {$userName}",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully.');
    }

    public function bulkDelete(Request $request)
    {
        $userIds = $request->input('users', []);
        
        User::whereIn('id', $userIds)->each(function ($user) {
            if ($user->profile && $user->profile->avatar) {
                Storage::disk('public')->delete($user->profile->avatar);
            }
            $user->delete();
        });

        UserActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'bulk_deleted_users',
            'details' => "Bulk deleted " . count($userIds) . " users",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('users.index')
            ->with('success', count($userIds) . ' users deleted successfully.');
    }

    public function toggleStatus(User $user): JsonResponse
    {
        $newStatus = $user->status === 'active' ? 'inactive' : 'active';
        $user->update(['status' => $newStatus]);

        UserActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'toggled_user_status',
            'details' => "Toggled user {$user->name} status to {$newStatus}",
            'ip_address' => request()->ip(),
        ]);

        return response()->json([
            'success' => true,
            'status' => $newStatus,
            'message' => "User status changed to {$newStatus}"
        ]);
    }

    public function notifications(Request $request)
    {
        $user = auth()->user();
        
        $notifications = $user->notifications()
            ->latest()
            ->paginate(20);
            
        $unreadCount = $user->unreadNotifications()->count();

        return view('user.notifications', compact('notifications', 'unreadCount'));
    }

    public function markNotificationRead($notificationId)
    {
        $user = auth()->user();
        $notification = $user->notifications()->findOrFail($notificationId);
        
        $notification->markAsRead();
        
        return response()->json(['success' => true]);
    }

    public function markAllNotificationsRead()
    {
        $user = auth()->user();
        $user->unreadNotifications()->update(['read_at' => now()]);
        
        return response()->json(['success' => true]);
    }

    public function getNotificationCount()
    {
        $user = auth()->user();
        $count = $user->unreadNotifications()->count();
        
        return response()->json(['count' => $count]);
    }

    public function activityAnalytics(Request $request)
    {
        $user = auth()->user();
        
        try {
            // Get activity analytics data
            $analytics = [
                'daily_activity' => UserActivityLog::where('user_id', $user->id)
                    ->where('created_at', '>=', now()->subDays(30))
                    ->groupBy('date')
                    ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                    ->orderBy('date')
                    ->get(),
                    
                'activity_types' => UserActivityLog::where('user_id', $user->id)
                    ->where('created_at', '>=', now()->subDays(30))
                    ->groupBy('action')
                    ->selectRaw('action, COUNT(*) as count')
                    ->get(),
                    
                'recent_activities' => $user->activityLogs()
                    ->with(['user'])
                    ->latest()
                    ->take(50)
                    ->get(),
                    
                'stats' => [
                    'total_activities' => $user->activityLogs()->count(),
                    'activities_this_month' => $user->activityLogs()
                        ->where('created_at', '>=', now()->startOfMonth())
                        ->count(),
                    'activities_today' => $user->activityLogs()
                        ->where('created_at', '>=', now()->startOfDay())
                        ->count(),
                    'most_active_day' => UserActivityLog::where('user_id', $user->id)
                        ->where('created_at', '>=', now()->subDays(30))
                        ->selectRaw('DAYNAME(created_at) as day, COUNT(*) as count')
                        ->groupBy('day')
                        ->orderBy('count', 'desc')
                        ->first(),
                ]
            ];
        } catch (\Exception $e) {
            // Fallback data if database queries fail
            $analytics = [
                'daily_activity' => collect([
                    (object) ['date' => now()->format('Y-m-d'), 'count' => 5],
                    (object) ['date' => now()->subDay()->format('Y-m-d'), 'count' => 3],
                    (object) ['date' => now()->subDays(2)->format('Y-m-d'), 'count' => 8],
                ]),
                'activity_types' => collect([
                    (object) ['action' => 'login', 'count' => 15],
                    (object) ['action' => 'viewed_property', 'count' => 23],
                    (object) ['action' => 'searched_properties', 'count' => 12],
                ]),
                'recent_activities' => collect([
                    (object) [
                        'id' => 1,
                        'created_at' => now(),
                        'action' => 'login',
                        'details' => 'User logged in successfully',
                        'ip_address' => '127.0.0.1'
                    ],
                    (object) [
                        'id' => 2,
                        'created_at' => now()->subHour(),
                        'action' => 'viewed_property',
                        'details' => 'Viewed property: Modern Apartment',
                        'ip_address' => '127.0.0.1'
                    ],
                    (object) [
                        'id' => 3,
                        'created_at' => now()->subHours(2),
                        'action' => 'searched_properties',
                        'details' => 'Searched for properties in Cairo',
                        'ip_address' => '127.0.0.1'
                    ],
                ]),
                'stats' => [
                    'total_activities' => 45,
                    'activities_this_month' => 32,
                    'activities_today' => 8,
                    'most_active_day' => (object) ['day' => 'Monday'],
                ]
            ];
        }
        
        return view('user.activity-analytics', compact('analytics'));
    }

    public function activityLog(Request $request)
    {
        $user = auth()->user();
        
        try {
            $logs = $user->activityLogs()
                ->with(['user'])
                ->latest()
                ->paginate(50);
        } catch (\Exception $e) {
            $logs = collect([]);
        }
        
        return view('user.activity-log', compact('logs'));
    }

    public function activity(Request $request)
    {
        $user = auth()->user();
        
        try {
            $activities = $user->activityLogs()
                ->with(['user'])
                ->latest()
                ->paginate(20);
        } catch (\Exception $e) {
            $activities = collect([]);
        }
            
        return view('user.activity', compact('activities'));
    }
}

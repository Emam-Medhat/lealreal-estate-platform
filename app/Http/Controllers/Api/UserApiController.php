<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\CacheService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class UserController extends ApiController
{
    /**
     * Get authenticated user profile
     */
    public function profile(Request $request): JsonResponse
    {
        $this->rateLimit($request, 200, 5);

        $user = $request->user();
        
        $userData = $this->getCachedData(
            "user_profile:{$user->id}",
            function () use ($user) {
                return $user->load([
                    'agentProfile',
                    'companyProfile',
                    'preferences',
                    'permissions'
                ]);
            },
            'medium'
        );

        return $this->apiResponse($userData, 'User profile retrieved successfully');
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $this->rateLimit($request, 50, 5);

        $user = $request->user();
        
        $validated = $this->validateApiRequest($request, [
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20',
            'whatsapp' => 'sometimes|string|max:20',
            'bio' => 'sometimes|string|max:1000',
            'preferences' => 'sometimes|array',
            'avatar' => 'sometimes|image|mimes:jpg,jpeg,png,gif|max:2048',
            'language' => 'sometimes|string|in:en,ar',
            'timezone' => 'sometimes|string',
        ]);

        $user->update($validated);
        
        // Clear user cache
        $this->clearApiCache("user_profile:{$user->id}");

        return $this->apiResponse($user->fresh(), 'Profile updated successfully');
    }

    /**
     * Change password
     */
    public function changePassword(Request $request): JsonResponse
    {
        $this->rateLimit($request, 10, 5);

        $validated = $this->validateApiRequest($request, [
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string|same:password',
        ]);

        $user = $request->user();

        // Verify current password
        if (!Hash::check($validated['current_password'], $user->password)) {
            return $this->errorResponse('Current password is incorrect', 422);
        }

        // Update password
        $user->update([
            'password' => Hash::make($validated['password']),
            'password_changed_at' => now(),
        ]);

        // Clear user cache
        $this->clearApiCache("user_profile:{$user->id}");

        return $this->apiResponse(null, 'Password changed successfully');
    }

    /**
     * Get user preferences
     */
    public function preferences(Request $request): JsonResponse
    {
        $this->rateLimit($request, 100, 5);

        $user = $request->user();
        
        $preferences = $this->getCachedData(
            "user_preferences:{$user->id}",
            function () use ($user) {
                return $user->preferences ?? $this->getDefaultPreferences();
            },
            'long'
        );

        return $this->apiResponse($preferences, 'User preferences retrieved successfully');
    }

    /**
     * Update user preferences
     */
    public function updatePreferences(Request $request): JsonResponse
    {
        $this->rateLimit($request, 50, 5);

        $user = $request->user();
        
        $validated = $this->validateApiRequest($request, [
            'language' => 'sometimes|string|in:en,ar',
            'timezone' => 'sometimes|string|timezone',
            'notifications' => 'sometimes|array',
            'theme' => 'sometimes|string|in:light,dark,auto',
            'currency' => 'sometimes|string|in:USD,EUR,EGP,SAR',
            'date_format' => 'sometimes|string|in:d/m/Y,m/d/Y,Y-m-d',
            'items_per_page' => 'sometimes|integer|min:10|max:100',
            'email_notifications' => 'sometimes|boolean',
            'push_notifications' => 'sometimes|boolean',
            'sms_notifications' => 'sometimes|boolean',
        ]);

        $user->update(['preferences' => $validated]);
        
        // Clear user cache
        $this->clearApiCache("user_preferences:{$user->id}");
        $this->clearApiCache("user_profile:{$user->id}");

        return $this->apiResponse($user->fresh()->preferences, 'Preferences updated successfully');
    }

    /**
     * Get user activity log
     */
    public function activityLog(Request $request): JsonResponse
    {
        $this->rateLimit($request, 50, 5);

        $user = $request->user();
        $limit = min($request->get('limit', 50), 100);

        $activities = $this->getCachedData(
            "user_activity:{$user->id}:{$limit}",
            function () use ($user, $limit) {
                return $user->activities()
                    ->with(['causer:id,full_name'])
                    ->latest()
                    ->take($limit)
                    ->get(['id', 'action', 'description', 'created_at']);
            },
            'short'
        );

        return $this->apiResponse($activities, 'Activity log retrieved successfully');
    }

    /**
     * Get user statistics
     */
    public function statistics(Request $request): JsonResponse
    {
        $this->rateLimit($request, 60, 5);

        $user = $request->user();

        $stats = $this->getCachedData(
            "user_stats:{$user->id}",
            function () use ($user) {
                return [
                    'total_leads' => $user->leads()->count(),
                    'total_properties' => $user->properties()->count(),
                    'total_appointments' => $user->appointments()->count(),
                    'total_tasks' => $user->tasks()->count(),
                    'total_notes' => $user->notes()->count(),
                    'login_count' => $user->login_count ?? 0,
                    'last_login_at' => $user->last_login_at?->toISOString(),
                    'created_at' => $user->created_at->toISOString(),
                    'updated_at' => $user->updated_at->toISOString(),
                ];
            },
            'medium'
        );

        return $this->apiResponse($stats, 'User statistics retrieved successfully');
    }

    /**
     * Get default user preferences
     *
     * @return array
     */
    private function getDefaultPreferences(): array
    {
        return [
            'language' => 'en',
            'timezone' => 'UTC',
            'theme' => 'auto',
            'currency' => 'USD',
            'date_format' => 'Y-m-d',
            'items_per_page' => 20,
            'email_notifications' => true,
            'push_notifications' => true,
            'sms_notifications' => false,
            'dashboard_widgets' => [
                'leads_stats' => true,
                'recent_activities' => true,
                'performance_metrics' => true,
                'quick_actions' => true,
            ],
            'email_digest' => 'daily',
            'marketing_emails' => false,
        ];
    }
}

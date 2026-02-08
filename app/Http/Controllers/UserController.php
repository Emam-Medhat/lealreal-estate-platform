<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\UserActivityLog;
use App\Services\UserService;
use App\Services\CacheService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UserController extends BaseController
{
    protected $userService;
    protected $userRepository;

    public function __construct(UserService $userService, \App\Repositories\Contracts\UserRepositoryInterface $userRepository)
    {
        $this->userService = $userService;
        $this->userRepository = $userRepository;
        $this->middleware('auth')->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->rateLimit($request, 100, 5);

        $filters = $request->only(['search', 'user_type', 'account_status', 'kyc_status', 'created_at']);
        $perPage = $this->getPerPage($request, 20, 100);

        $users = $this->getCachedData(
            'users_index:' . md5(serialize($filters) . $perPage),
            function () use ($filters, $perPage) {
                return $this->userRepository->getFilteredUsers($filters, $perPage);
            },
            'short'
        );

        $userStats = $this->getCachedData(
            'user_stats_index',
            function () {
                return $this->userRepository->getUserStats();
            },
            'medium'
        );

        return view('users.index', compact('users', 'userStats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $this->rateLimit($request, 50, 5);

        $userTypes = $this->getCachedData(
            'user_types',
            function () {
                return [
                    'admin' => 'Administrator',
                    'agent' => 'Real Estate Agent',
                    'company' => 'Company',
                    'developer' => 'Property Developer',
                    'investor' => 'Investor',
                    'buyer' => 'Property Buyer',
                    'tenant' => 'Tenant'
                ];
            },
            'long'
        );

        return view('users.create', compact('userTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $this->rateLimit($request, 30, 5);

        try {
            DB::beginTransaction();

            $userData = $request->validated();
            
            // Hash password
            $userData['password'] = Hash::make($userData['password']);
            
            // Generate UUID
            $userData['uuid'] = (string) Str::uuid();
            
            // Create user
            $user = $this->userService->createUser($userData);

            // Handle avatar upload
            if ($request->hasFile('avatar')) {
                $this->handleAvatarUpload($request->file('avatar'), $user);
            }

            // Handle profile data
            if ($request->has('profile')) {
                $this->createUserProfile($user, $request->input('profile'));
            }

            DB::commit();

            // Clear relevant caches
            $this->clearCache('users_index');
            $this->clearCache('user_stats');

            return redirect()
                ->route('users.show', $user)
                ->with('success', 'User created successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('User creation failed: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'user_id' => auth()->id()
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create user: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        $this->rateLimit($request, 100, 5);

        $user = $this->getCachedData(
            "user_show_{$id}",
            function () use ($id) {
                return $this->userRepository->findById($id, ['*'], [
                    'profile:id,user_id,bio,avatar,avatar_thumbnail,cover_image,social_links,preferences',
                    'company:id,name,logo,website',
                    'subscriptionPlan:id,name,features,price',
                    'devices:id,user_id,device_type,device_name,last_used_at',
                    'socialAccounts:id,user_id,provider,provider_id,provider_nickname',
                    'activityLogs' => function ($query) {
                        return $query->latest()->take(10);
                    }
                ]);
            },
            'medium'
        );

        if (!$user) {
            return $this->errorResponse('User not found', 404);
        }

        $userActivity = $this->getCachedData(
            "user_activity_{$id}",
            function () use ($id) {
                return $this->userRepository->getActivityLogs($id, 20);
            },
            'short'
        );

        return view('users.show', compact('user', 'userActivity'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, $id)
    {
        $this->rateLimit($request, 50, 5);

        $user = $this->userRepository->findById($id, ['*'], [
            'profile:id,user_id,bio,avatar,avatar_thumbnail,cover_image,social_links,preferences',
            'company:id,name,logo'
        ]);

        if (!$user) {
            return $this->errorResponse('User not found', 404);
        }

        $userTypes = $this->getCachedData(
            'user_types',
            function () {
                return [
                    'admin' => 'Administrator',
                    'agent' => 'Real Estate Agent',
                    'company' => 'Company',
                    'developer' => 'Property Developer',
                    'investor' => 'Investor',
                    'buyer' => 'Property Buyer',
                    'tenant' => 'Tenant'
                ];
            },
            'long'
        );

        return view('users.edit', compact('user', 'userTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, $id)
    {
        $this->rateLimit($request, 50, 5);

        try {
            DB::beginTransaction();

            $userData = $request->validated();
            
            // Hash password if being changed
            if (isset($userData['password'])) {
                $userData['password'] = Hash::make($userData['password']);
            }

            $user = $this->userService->updateUser($id, $userData);

            if (!$user) {
                return $this->errorResponse('User not found', 404);
            }

            // Handle avatar upload
            if ($request->hasFile('avatar')) {
                $this->handleAvatarUpload($request->file('avatar'), $user);
            }

            // Handle profile data update
            if ($request->has('profile')) {
                $this->updateUserProfile($user, $request->input('profile'));
            }

            DB::commit();

            // Clear relevant caches
            $this->clearCache("user_show_{$id}");
            $this->clearCache('users_index');
            $this->clearCache('user_stats');

            return redirect()
                ->route('users.show', $user)
                ->with('success', 'User updated successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('User update failed: ' . $e->getMessage(), [
                'user_id' => $id,
                'request_data' => $request->all(),
                'updated_by' => auth()->id()
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update user: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        $this->rateLimit($request, 30, 5);

        try {
            $success = $this->userService->deleteUser($id);

            if (!$success) {
                return $this->errorResponse('User not found', 404);
            }

            // Clear relevant caches
            $this->clearCache("user_show_{$id}");
            $this->clearCache('users_index');
            $this->clearCache('user_stats');

            return redirect()
                ->route('users.index')
                ->with('success', 'User deleted successfully');

        } catch (\Exception $e) {
            Log::error('User deletion failed: ' . $e->getMessage(), [
                'user_id' => $id,
                'deleted_by' => auth()->id()
            ]);

            return redirect()
                ->back()
                ->with('error', 'Failed to delete user: ' . $e->getMessage());
        }
    }

    /**
     * Search users with advanced filtering
     */
    public function search(Request $request)
    {
        $this->rateLimit($request, 100, 5);

        $query = $request->get('q', '');
        $filters = $request->only(['user_type', 'account_status', 'kyc_status']);
        $limit = min($request->get('limit', 20), 50);

        if (empty($query)) {
            return $this->jsonResponse([], 'Search query is required', 400);
        }

        $users = $this->getCachedData(
            'user_search_' . md5($query . serialize($filters) . $limit),
            function () use ($query, $filters, $limit) {
                return $this->userRepository->searchUsers($query, $filters, $limit);
            },
            'short'
        );

        return $this->jsonResponse($users, 'Search results retrieved successfully');
    }

    /**
     * Get user statistics
     */
    public function stats(Request $request)
    {
        $this->rateLimit($request, 60, 5);

        $stats = $this->getCachedData(
            'user_stats_detailed',
            function () {
                return [
                    'overview' => $this->userRepository->getUserStats(),
                    'performance' => $this->userRepository->getUserPerformanceMetrics(),
                    'recent_users' => $this->userRepository->getRecentUsers(10),
                    'active_users' => $this->userRepository->getActiveUsers(10),
                    'users_by_type' => $this->getUserTypesDistribution(),
                    'users_by_status' => $this->getUserStatusDistribution()
                ];
            },
            'medium'
        );

        return $this->jsonResponse($stats, 'User statistics retrieved successfully');
    }

    /**
     * Export users with memory-efficient processing
     */
    public function export(Request $request)
    {
        $this->rateLimit($request, 10, 5);

        $filters = $request->only(['user_type', 'account_status', 'kyc_status', 'created_at']);
        $format = $request->get('format', 'csv');

        try {
            $job = new \App\Jobs\ExportUsersJob($filters, $format, auth()->id());
            dispatch($job);

            return $this->jsonResponse(['job_id' => $job->getJobId()], 'Export job started successfully');

        } catch (\Exception $e) {
            Log::error('User export failed: ' . $e->getMessage(), [
                'filters' => $filters,
                'format' => $format,
                'user_id' => auth()->id()
            ]);

            return $this->errorResponse('Export failed', 500);
        }
    }

    /**
     * Toggle user account status
     */
    public function toggleStatus(Request $request, $id)
    {
        $this->rateLimit($request, 30, 5);

        try {
            $user = $this->userRepository->findById($id);
            
            if (!$user) {
                return $this->errorResponse('User not found', 404);
            }

            $newStatus = $user->account_status === 'active' ? 'inactive' : 'active';
            
            $user->update(['account_status' => $newStatus]);

            // Clear relevant caches
            $this->clearCache("user_show_{$id}");
            $this->clearCache('users_index');
            $this->clearCache('user_stats');

            return $this->jsonResponse($user, 'User status updated successfully');

        } catch (\Exception $e) {
            Log::error('User status toggle failed: ' . $e->getMessage(), [
                'user_id' => $id,
                'updated_by' => auth()->id()
            ]);

            return $this->errorResponse('Failed to toggle user status', 500);
        }
    }

    /**
     * Get user activity logs
     */
    public function activity(Request $request, $id)
    {
        $this->rateLimit($request, 60, 5);

        $limit = min($request->get('limit', 20), 100);
        
        $activity = $this->getCachedData(
            "user_activity_{$id}_{$limit}",
            function () use ($id, $limit) {
                return $this->userRepository->getActivityLogs($id, $limit);
            },
            'short'
        );

        return $this->jsonResponse($activity, 'User activity retrieved successfully');
    }

    /**
     * Handle avatar upload
     */
    private function handleAvatarUpload($avatar, User $user): void
    {
        try {
            // Delete old avatar if exists
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Upload new avatar
            $path = $avatar->store('avatars/' . $user->id, 'public');
            
            // Generate thumbnail
            $this->generateAvatarThumbnail($avatar, $path, $user);

            $user->update([
                'avatar' => $path,
                'avatar_thumbnail' => 'thumbnails/' . basename($path)
            ]);

        } catch (\Exception $e) {
            Log::error('Avatar upload failed: ' . $e->getMessage(), [
                'user_id' => $user->id
            ]);
        }
    }

    /**
     * Generate avatar thumbnail
     */
    private function generateAvatarThumbnail($avatar, string $path, User $user): void
    {
        try {
            // Implementation would depend on image processing library
            // For now, just copy the original as thumbnail
            $thumbnailPath = 'thumbnails/' . basename($path);
            Storage::disk('public')->copy($path, $thumbnailPath);
        } catch (\Exception $e) {
            Log::error('Avatar thumbnail generation failed: ' . $e->getMessage());
        }
    }

    /**
     * Create user profile
     */
    private function createUserProfile(User $user, array $profileData): void
    {
        try {
            UserProfile::create([
                'user_id' => $user->id,
                'bio' => $profileData['bio'] ?? null,
                'social_links' => $profileData['social_links'] ?? [],
                'preferences' => $profileData['preferences'] ?? []
            ]);
        } catch (\Exception $e) {
            Log::error('User profile creation failed: ' . $e->getMessage(), [
                'user_id' => $user->id
            ]);
        }
    }

    /**
     * Update user profile
     */
    private function updateUserProfile(User $user, array $profileData): void
    {
        try {
            if ($user->profile) {
                $user->profile->update([
                    'bio' => $profileData['bio'] ?? $user->profile->bio,
                    'social_links' => array_merge($user->profile->social_links ?? [], $profileData['social_links'] ?? []),
                    'preferences' => array_merge($user->profile->preferences ?? [], $profileData['preferences'] ?? [])
                ]);
            } else {
                $this->createUserProfile($user, $profileData);
            }
        } catch (\Exception $e) {
            Log::error('User profile update failed: ' . $e->getMessage(), [
                'user_id' => $user->id
            ]);
        }
    }

    /**
     * Get user types distribution
     */
    private function getUserTypesDistribution(): array
    {
        return $this->getCachedData(
            'user_types_distribution',
            function () {
                return User::selectRaw('user_type, COUNT(*) as count')
                    ->groupBy('user_type')
                    ->orderBy('count', 'desc')
                    ->get()
                    ->toArray();
            },
            'medium'
        );
    }

    /**
     * Get user status distribution
     */
    private function getUserStatusDistribution(): array
    {
        return $this->getCachedData(
            'user_status_distribution',
            function () {
                return User::selectRaw('account_status, COUNT(*) as count')
                    ->groupBy('account_status')
                    ->orderBy('count', 'desc')
                    ->get()
                    ->toArray();
            },
            'medium'
        );
    }
}

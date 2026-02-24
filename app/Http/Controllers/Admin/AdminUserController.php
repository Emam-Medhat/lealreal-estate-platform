<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserService;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminUserController extends Controller
{
    protected $userService;
    protected $userRepository;
    protected $user;

    public function __construct(UserService $userService, UserRepositoryInterface $userRepository)
    {
        $this->userService = $userService;
        $this->userRepository = $userRepository;
        $this->user = null; // Initialize user property
    }

    public function index(Request $request)
    {
        try {
            $users = $this->userRepository->getFilteredUsers($request->all(), 20);
        } catch (\Exception $e) {
            \Log::error('Failed to fetch users: ' . $e->getMessage());
            $users = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20);
        }
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        try {
            // Validation
            $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'username' => 'nullable|string|max:255|unique:users,username',
                'email' => 'required|email|max:255|unique:users,email',
                'phone' => 'nullable|string|max:20',
                'whatsapp' => 'nullable|string|max:20',
                'gender' => 'nullable|in:male,female,other',
                'birth_date' => 'nullable|date|before:today',
                'user_type' => 'required|in:user,agent,admin',
                'account_status' => 'required|in:active,inactive,suspended',
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
                'city' => 'nullable|string|max:255',
                'state' => 'nullable|string|max:255',
                'country' => 'nullable|string|max:255',
                'address' => 'nullable|string|max:500',
                'postal_code' => 'nullable|string|max:20',
                'id_document_type' => 'nullable|in:passport,national_id,driving_license',
                'id_document_number' => 'nullable|string|max:255',
                'job_title' => 'nullable|string|max:255',
                'bio' => 'nullable|string|max:1000',
                'website' => 'nullable|url|max:255',
                'twitter' => 'nullable|string|max:255',
                'facebook_url' => 'nullable|url|max:255',
                'linkedin_url' => 'nullable|url|max:255',
                'instagram_url' => 'nullable|url|max:255',
                'preferred_language' => 'nullable|string|max:10',
                'preferred_currency' => 'nullable|string|max:10',
                'timezone' => 'nullable|string|max:50',
                'is_agent' => 'nullable|boolean',
                'is_company' => 'nullable|boolean',
                'is_developer' => 'nullable|boolean',
                'is_investor' => 'nullable|boolean',
                'password' => 'required|string|min:8|confirmed',
            ]);

            // Prepare data
            $userData = $request->except(['_token', 'password_confirmation', 'avatar', 'birth_date']);

            // Handle booleans
            $booleans = ['is_agent', 'is_company', 'is_developer', 'is_investor'];
            foreach ($booleans as $bool) {
                $userData[$bool] = $request->has($bool);
            }

            $userData['date_of_birth'] = $request->birth_date;
            $userData['full_name'] = $request->first_name . ' ' . $request->last_name;
            $userData['password'] = Hash::make($request->password);

            // Call service to create user
            $this->userService->createUser($userData, $request->file('avatar'));

            return redirect()->route('admin.users.index')
                ->with('success', 'User created successfully');
        } catch (\Exception $e) {
            \Log::error('User creation failed: ' . $e->getMessage());
            return back()
                ->withInput()
                ->with('error', 'Failed to create user: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $user = $this->userRepository->findById($id);

            if (!$user) {
                return redirect()->route('admin.users.index')
                    ->with('error', 'User not found.');
            }

            if ($this->user === null) {
                $this->user = $user;
            }

            return view('admin.users.show', compact('user'));
        } catch (\Exception $e) {
            return redirect()->route('admin.users.index')
                ->with('error', 'User not found.');
        }
    }

    public function edit($id)
    {
        try {
            $user = $this->userRepository->findById($id);

            if (!$user) {
                return redirect()->route('admin.users.index')
                    ->with('error', 'User not found.');
            }

            // Set user property for the view
            if ($this->user !== null) {
                $this->user = $user;
            }

            return view('admin.users.edit', compact('user'));
        } catch (\Exception $e) {
            return redirect()->route('admin.users.index')
                ->with('error', 'User not found.');
        }
    }

    public function update(Request $request, $id)
    {
        // Log the incoming request data for debugging
        \Log::info('User update request', [
            'user_id' => $id,
            'request_data' => $request->all(),
            'has_file' => $request->hasFile('avatar'), // Changed from 'image' to 'avatar' based on context
        ]);

        $request->validate([
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'username' => 'sometimes|string|max:255|unique:users,username,' . $id,
            'phone' => 'nullable|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'user_type' => 'sometimes|string|in:admin,user,agent',
            'account_status' => 'sometimes|string|in:active,inactive,suspended',
            'kyc_status' => 'sometimes|string|in:pending,verified,rejected',
            'password' => 'nullable|string|min:8',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            'address' => 'nullable|string|max:500',
            'bio' => 'nullable|string|max:1000',
            'country' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'is_agent' => 'sometimes|boolean',
            'is_company' => 'sometimes|boolean',
            'is_developer' => 'sometimes|boolean',
            'is_investor' => 'sometimes|boolean',
            'gender' => 'nullable|in:male,female,other',
            'birth_date' => 'nullable|date|before:today',
            'postal_code' => 'nullable|string|max:20',
            'national_id' => 'nullable|string|max:255', // Kept for compatibility if used elsewhere, but primarily use id_document_number
            'id_document_number' => 'nullable|string|max:255',
            'job_title' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'twitter' => 'nullable|string|max:255',
            'id_document_type' => 'nullable|in:passport,national_id,driving_license',
            'id_document_number' => 'nullable|string|max:255',
            'preferred_language' => 'nullable|string|max:10',
            'preferred_currency' => 'nullable|string|max:10',
            'timezone' => 'nullable|string|max:50',
        ]);

        try {
            // Prepare data
            $data = $request->except(['_token', 'password_confirmation', 'avatar']);

            // Handle booleans (checkboxes not present in request if unchecked)
            $booleans = ['is_agent', 'is_company', 'is_developer', 'is_investor'];
            foreach ($booleans as $bool) {
                $data[$bool] = $request->has($bool);
            }

            // Call Service to update user
            $this->userService->updateUser($id, $data, $request->file('avatar'));

            return redirect()->route('admin.users.index')
                ->with('success', 'User updated successfully');

        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            \Log::error('User update failed with exception', [
                'user_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Failed to update user: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $this->userService->deleteUser($id);

            return redirect()->route('admin.users.index')
                ->with('success', 'User deleted successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete user');
        }
    }

    public function bulkDelete(Request $request)
    {
        try {
            $userIds = explode(',', $request->input('user_ids'));
            
            // Validate that user IDs are provided
            if (empty($userIds) || empty($request->input('user_ids'))) {
                return redirect()->route('admin.users.index')
                    ->with('error', 'لم يتم تحديد أي مستخدمين للحذف');
            }

            // Prevent admin from deleting themselves
            $currentUser = auth()->user();
            if (in_array($currentUser->id, $userIds)) {
                return redirect()->route('admin.users.index')
                    ->with('error', 'لا يمكنك حذف حسابك الحالي');
            }

            // Convert to integers and filter valid IDs
            $userIds = array_filter(array_map('intval', $userIds));
            
            $deletedCount = 0;
            foreach ($userIds as $userId) {
                try {
                    $this->userService->deleteUser($userId);
                    $deletedCount++;
                } catch (\Exception $e) {
                    \Log::error("Failed to delete user {$userId}: " . $e->getMessage());
                }
            }

            if ($deletedCount > 0) {
                return redirect()->route('admin.users.index')
                    ->with('success', "تم حذف {$deletedCount} مستخدم بنجاح");
            } else {
                return redirect()->route('admin.users.index')
                    ->with('error', 'فشل في حذف المستخدمين المحددين');
            }

        } catch (\Exception $e) {
            \Log::error('Bulk delete failed: ' . $e->getMessage());
            return redirect()->route('admin.users.index')
                ->with('error', 'حدث خطأ أثناء محاولة حذف المستخدمين');
        }
    }
}
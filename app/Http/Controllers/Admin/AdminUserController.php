<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserService;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    protected $userService;
    protected $userRepository;

    public function __construct(UserService $userService, UserRepositoryInterface $userRepository)
    {
        $this->userService = $userService;
        $this->userRepository = $userRepository;
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
        // Validation
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'user_type' => 'required|string|in:admin,user,agent', // Treating user_type as role for consistency
            'phone' => 'nullable|string|max:20|unique:users',
            'birth_date' => 'nullable|date|before:today',
            'city' => 'nullable|string|max:100',
            'region' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:500',
        ]);

        try {
            $userData = $request->except(['_token', 'password_confirmation', 'region', 'birth_date']);
            $userData['date_of_birth'] = $request->birth_date;
            $userData['state'] = $request->region;
            // Map user_type to role if needed, or assume service handles it.
            // UserService uses 'role' or 'user_type'.
            $userData['role'] = $request->user_type; 

            $this->userService->createUser($userData);

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
        } catch (\Exception $e) {
            return redirect()->route('admin.users.index')
                ->with('error', 'User not found.');
        }

        return view('admin.users.show', compact('user'));
    }

    public function edit($id)
    {
        try {
            $user = $this->userRepository->findById($id);
        } catch (\Exception $e) {
            return redirect()->route('admin.users.index')
                ->with('error', 'User not found.');
        }

        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'role' => 'required|string|in:admin,user,agent',
        ]);

        try {
            $this->userService->updateProfile($id, $request->all());

            return redirect()->route('admin.users.index')
                ->with('success', 'User updated successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update user');
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
}
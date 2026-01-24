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
}

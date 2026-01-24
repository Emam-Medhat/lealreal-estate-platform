<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class RegisteredUserController extends Controller
{
    protected $authService;

    public function __construct(\App\Services\AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Display the registration view.
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(RegisterRequest $request)
    {
        try {
            // Handle avatar upload
            $avatarPath = null;
            if ($request->hasFile('avatar')) {
                $avatarPath = $request->file('avatar')->store('avatars', 'public');
            }

            $data = $request->validated();
            $data['avatar'] = $avatarPath;

            $user = $this->authService->register($data);

            // Auto-login after registration
            Auth::login($user);

            // Flash success message
            session()->flash('success', 'تم إنشاء حسابك بنجاح! مرحباً بك في منصة العقارات العالمية.');

            return redirect()->route('dashboard');

        } catch (\Exception $e) {
            Log::error('Registration Error: ' . $e->getMessage());

            return back()
                ->withInput()
                ->withErrors(['error' => 'حدث خطأ أثناء التسجيل. يرجى المحاولة مرة أخرى.']);
        }
    }
}

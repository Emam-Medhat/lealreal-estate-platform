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

            // Flash success message
            session()->flash('success', 'تم إنشاء حسابك بنجاح! يرجى تسجيل الدخول لاستخدام المنصة.');

            // Redirect to login page instead of auto-login
            return redirect()->route('login')->with('success', 'تم إنشاء حسابك بنجاح! يمكنك الآن تسجيل الدخول.');

        } catch (\Exception $e) {
            Log::error('Registration Error: ' . $e->getMessage());

            $errorMessage = 'حدث خطأ أثناء التسجيل. يرجى المحاولة مرة أخرى.';
            
            if (app()->environment('local', 'staging')) {
                $errorMessage .= ' Error: ' . $e->getMessage();
            }

            return back()
                ->withInput()
                ->withErrors(['error' => $errorMessage]);
        }
    }
}

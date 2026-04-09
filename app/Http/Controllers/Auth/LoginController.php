<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function showAdminLoginForm()
    {
        return view('auth.admin-login');
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        // Ensure 'login' field is present or mapped to email/username
        if (!isset($credentials['login']) && isset($credentials['email'])) {
            $credentials['login'] = $credentials['email'];
        }

        $user = $this->authService->login($credentials, $request->boolean('remember'));

        $request->session()->regenerate();

        return redirect()->intended(route('home'));
    }

    public function adminLogin(LoginRequest $request)
    {
        $credentials = $request->validated();

        // Ensure 'login' field is present or mapped to email/username
        if (!isset($credentials['login']) && isset($credentials['email'])) {
            $credentials['login'] = $credentials['email'];
        }

        // Add admin-specific validation
        $credentials['user_type'] = 'admin';

        $user = $this->authService->login($credentials, $request->boolean('remember'));

        if (!$user || $user->user_type !== 'admin') {
            return back()->withErrors([
                'email' => 'Invalid admin credentials or access denied.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }


}

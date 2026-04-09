<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request)
    {
        try {
            $user = $this->authService->register($request->validated());

            event(new Registered($user));

            return redirect()->route('login')->with('success', 'Registration successful. Please login to continue.');
        } catch (\Exception $e) {
            // Log error for debugging
            \Log::error('Registration controller error: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            // Show error to user in development
            if (app()->environment('local')) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Registration failed: ' . $e->getMessage());
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Registration failed. Please try again.');
        }
    }
}

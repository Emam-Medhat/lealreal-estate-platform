<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResetPasswordRequest;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{


    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.reset-password')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }

    protected $authService;

    public function __construct(\App\Services\AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function reset(ResetPasswordRequest $request)
    {
        try {
            $this->authService->resetPassword(
                $request->email,
                $request->password,
                $request->token
            );

            return redirect()->route('dashboard')->with('status', trans(Password::PASSWORD_RESET));

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors($e->errors());
        }
    }


}

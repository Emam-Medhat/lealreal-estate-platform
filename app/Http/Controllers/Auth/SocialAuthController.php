<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Auth\UserSocialAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    protected $socialAuthService;

    public function __construct(\App\Services\SocialAuthService $socialAuthService)
    {
        $this->socialAuthService = $socialAuthService;
    }

    public function redirect($provider)
    {
        return $this->socialAuthService->redirect($provider);
    }

    public function callback($provider)
    {
        try {
            $user = $this->socialAuthService->callback($provider);
            Auth::login($user);
            return redirect()->route('dashboard');
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors([
                'social' => 'Unable to authenticate with ' . ucfirst($provider),
            ]);
        }
    }

    public function unlink(Request $request, $provider)
    {
        $result = $this->socialAuthService->unlinkSocialAccount(Auth::user(), $provider);

        if (!$result['success']) {
            return back()->withErrors([
                'social' => $result['message'],
            ]);
        }

        return back()->with('status', $result['message']);
    }

    public function linkedAccounts()
    {
        $socialAccounts = $this->socialAuthService->getLinkedAccounts(Auth::id());

        return view('auth.social-accounts', compact('socialAccounts'));
    }
}

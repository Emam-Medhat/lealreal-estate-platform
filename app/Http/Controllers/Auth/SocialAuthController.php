<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserSocialAccount;
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
        // Existing unlink logic might be okay, or move to Service too.
        // For now, let's keep it here or simplify.
        // The previous implementation was direct DB access.

        $socialAccount = \App\Models\UserSocialAccount::where('user_id', auth()->id())
            ->where('provider', $provider)
            ->firstOrFail();

        // Check if user has other social accounts or password
        $hasOtherAuth = \App\Models\UserSocialAccount::where('user_id', auth()->id())
            ->where('provider', '!=', $provider)
            ->exists() || auth()->user()->password;

        if (!$hasOtherAuth) {
            return back()->withErrors([
                'social' => 'You cannot unlink your only authentication method. Please set a password first.',
            ]);
        }

        $socialAccount->delete();

        return back()->with('status', ucfirst($provider) . ' account unlinked successfully');
    }

    public function linkedAccounts()
    {
        $socialAccounts = \App\Models\UserSocialAccount::where('user_id', auth()->id())->get();

        return view('auth.social-accounts', compact('socialAccounts'));
    }
}

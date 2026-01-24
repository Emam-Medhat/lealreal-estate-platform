<?php

namespace App\Services;

use App\Models\UserSocialAccount;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class SocialAuthService
{
    /**
     * Authenticate user via social provider.
     *
     * @param string $provider
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirect(string $provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    /**
     * Handle callback from social provider.
     *
     * @param string $provider
     * @return \App\Models\User
     */
    public function callback(string $provider)
    {
        $socialUser = Socialite::driver($provider)->user();

        return $this->findOrCreateUser($socialUser, $provider);
    }

    /**
     * Find existing user or create a new one based on social data.
     */
    protected function findOrCreateUser($socialUser, $provider)
    {
        $account = UserSocialAccount::where('provider', $provider)
            ->where('provider_id', $socialUser->getId())
            ->first();

        if ($account) {
            $account->update([
                'access_token' => $socialUser->token,
                'refresh_token' => $socialUser->refreshToken,
                'expires_at' => property_exists($socialUser, 'expiresIn') ? now()->addSeconds($socialUser->expiresIn) : null,
                'last_synced_at' => now(),
            ]);

            return $account->user;
        }

        // Check if user with same email exists
        $user = User::where('email', $socialUser->getEmail())->first();

        if (!$user) {
            // Register new user
            $user = User::create([
                'username' => $this->generateUniqueUsername($socialUser->getName()),
                'email' => $socialUser->getEmail(),
                'first_name' => $socialUser->getName(),
                'last_name' => '',
                'password' => Hash::make(Str::random(24)),
                'email_verified_at' => now(),
                'avatar' => $socialUser->getAvatar(),
                'user_type' => 'user',
                'account_status' => 'active',
            ]);
        }

        // Link social account
        $this->linkSocialAccount($user, $socialUser, $provider);

        return $user;
    }

    /**
     * Link a social account to a user.
     */
    public function linkSocialAccount(User $user, $socialUser, string $provider)
    {
        $user->socialAccounts()->create([
            'provider' => $provider,
            'provider_id' => $socialUser->getId(),
            'provider_name' => $socialUser->getName(),
            'provider_email' => $socialUser->getEmail(),
            'provider_avatar' => $socialUser->getAvatar(),
            'access_token' => $socialUser->token,
            'refresh_token' => $socialUser->refreshToken,
            'expires_at' => property_exists($socialUser, 'expiresIn') ? now()->addSeconds($socialUser->expiresIn) : null,
            'last_synced_at' => now(),
            'is_primary' => $user->socialAccounts()->count() === 0,
        ]);
    }

    private function generateUniqueUsername($name)
    {
        $username = Str::slug($name);
        $count = User::where('username', 'LIKE', "{$username}%")->count();
        return $count ? "{$username}-{$count}" : $username;
    }

    public function authenticateWithGoogle()
    {
        return $this->redirect('google');
    }
    public function authenticateWithFacebook()
    {
        return $this->redirect('facebook');
    }
}

<?php

namespace App\Services;
 
use App\Models\Auth\UserSocialAccount;
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
    protected function getSocialAccount($socialUser, string $provider): ?UserSocialAccount
    {
        return UserSocialAccount::where('provider', $provider)
            ->where('provider_id', $socialUser->getId())
            ->first();
    }

    protected function getUserByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    protected function createNewUserFromSocialite($socialUser): User
    {
        return User::create([
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

    /**
     * Find existing user or create a new one based on social data.
     */
    protected function findOrCreateUser($socialUser, $provider)
    {
        $account = $this->getSocialAccount($socialUser, $provider);

        if ($account) {
            $account->update([
                'access_token' => $socialUser->token,
                'refresh_token' => $socialUser->refreshToken,
                'expires_at' => property_exists($socialUser, 'expiresIn') ? now()->addSeconds($socialUser->expiresIn) : null,
                'last_synced_at' => now(),
            ]);

            return $account->user;
        }

        $user = $this->getUserByEmail($socialUser->getEmail());

        if (!$user) {
            $user = $this->createNewUserFromSocialite($socialUser);
        }

        $this->linkSocialAccount($user, $socialUser, $provider);

        return $user;
    }

    /**
     * Get linked accounts for a user.
     *
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getLinkedAccounts(int $userId)
    {
        return UserSocialAccount::where('user_id', $userId)->get();
    }

    /**
     * Unlink a social account from a user.
     *
     * @param User $user
     * @param string $provider
     * @return array
     */
    public function unlinkSocialAccount(User $user, string $provider): array
    {
        $socialAccount = UserSocialAccount::where('user_id', $user->id)
            ->where('provider', $provider)
            ->first();

        if (!$socialAccount) {
            return [
                'success' => false,
                'message' => 'Social account not found'
            ];
        }

        // Check if user has other social accounts or password
        $hasOtherAuth = UserSocialAccount::where('user_id', $user->id)
            ->where('provider', '!=', $provider)
            ->exists() || $user->password;

        if (!$hasOtherAuth) {
            return [
                'success' => false,
                'message' => 'You cannot unlink your only authentication method. Please set a password first.'
            ];
        }

        $socialAccount->delete();

        return [
            'success' => true,
            'message' => ucfirst($provider) . ' account unlinked successfully'
        ];
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


}

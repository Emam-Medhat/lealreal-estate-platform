<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use App\Events\UserRegistered;
use App\Events\UserLoggedIn;
use App\Events\UserLoggedOut;
use Illuminate\Support\Str;

class AuthService
{
    protected $passwordService;

    public function __construct(PasswordService $passwordService)
    {
        $this->passwordService = $passwordService;
    }

    /**
     * Handle user login.
     *
     * @param array $credentials
     * @param bool $remember
     * @return User
     * @throws ValidationException
     */
    public function login(array $credentials, bool $remember = false)
    {
        $field = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (!Auth::attempt([$field => $credentials['login'], 'password' => $credentials['password']], $remember)) {
            throw ValidationException::withMessages([
                'login' => [__('auth.failed')],
            ]);
        }

        $user = Auth::user();

        if ($user->account_status !== 'active') {
            Auth::logout();
            throw ValidationException::withMessages([
                'login' => [__('Your account is ' . $user->account_status)],
            ]);
        }

        event(new UserLoggedIn($user));

        return $user;
    }

    /**
     * Handle user registration.
     *
     * @param array $data
     * @return User
     */
    public function register(array $data): User
    {
        return DB::transaction(function () use ($data) {
            // Generate UUID and referral code if not provided
            $uuid = Str::uuid();
            $referralCode = $this->generateUniqueReferralCode();

            $userData = array_merge($data, [
                'uuid' => $uuid,
                'password' => $this->passwordService->hash($data['password']),
                'referral_code' => $referralCode,
                'full_name' => $data['first_name'] . ' ' . $data['last_name'],
                'username' => $data['username'] ?? $this->generateUsername($data['first_name'], $data['last_name']),
                'account_status' => 'active', // Defaulting to active for now
                'kyc_status' => 'pending',
                'subscription_status' => 'trial',
                'subscription_start_date' => now(),
                'subscription_end_date' => now()->addDays(30),
                'wallet_balance' => 0,
                'login_count' => 0,
            ]);

            // Set role-specific booleans
            $userData = $this->setRoleFlags($userData, $data['user_type'] ?? 'user');

            $user = User::create($userData);

            // Handle role-specific additional data
            $this->handleRoleSpecificData($user, $data);

            event(new UserRegistered($user));

            return $user;
        });
    }

    private function setRoleFlags(array $userData, string $userType): array
    {
        $userData['is_agent'] = false;
        $userData['is_company'] = false;
        $userData['is_developer'] = false;
        $userData['is_investor'] = false;

        switch ($userType) {
            case 'agent':
                $userData['is_agent'] = true;
                break;
            case 'company':
                $userData['is_company'] = true;
                break;
            case 'developer':
                $userData['is_developer'] = true;
                break;
            case 'investor':
                $userData['is_investor'] = true;
                break;
        }

        return $userData;
    }

    private function handleRoleSpecificData(User $user, array $data): void
    {
        $userType = $data['user_type'] ?? 'user';

        switch ($userType) {
            case 'agent':
                $user->update([
                    'agent_license_number' => $data['agent_license_number'] ?? null,
                    'agent_license_expiry' => $data['agent_license_expiry'] ?? null,
                    'agent_company' => $data['agent_company'] ?? null,
                    'agent_commission_rate' => $data['agent_commission_rate'] ?? 2.5,
                ]);
                break;

            case 'company':
                $user->update([
                    'agent_company' => $data['agent_company'] ?? null,
                    'company_registration_number' => $data['company_registration_number'] ?? null,
                    'company_tax_number' => $data['company_tax_number'] ?? null,
                    'company_employees_count' => $data['company_employees_count'] ?? 1,
                    'company_established_date' => now(),
                ]);
                break;

            case 'developer':
                $user->update([
                    'agent_company' => $data['developer_name'] ?? ($data['agent_company'] ?? null),
                    'developer_certification' => $data['developer_certification'] ?? null,
                    'developer_license_number' => $data['developer_license_number'] ?? null,
                    'developer_license_expiry' => $data['developer_license_expiry'] ?? null,
                ]);
                break;

            case 'investor':
                $user->update([
                    'investor_type' => $data['investor_type'] ?? 'individual',
                    'investment_portfolio_value' => $data['investment_portfolio_value'] ?? 0,
                ]);
                break;
        }
    }

    private function generateUniqueReferralCode(): string
    {
        do {
            $code = strtoupper(substr(md5(uniqid()), 0, 8));
        } while (User::where('referral_code', $code)->exists());

        return $code;
    }

    /**
     * Handle user logout.
     */
    /**
     * Handle user logout.
     */
    public function logout()
    {
        $user = Auth::user();
        $sessionId = Session::getId();

        // Deactivate current session
        if ($user) {
            \App\Models\Auth\UserSession::where('session_id', $sessionId)
                ->where('user_id', $user->id)
                ->update(['is_active' => false]);

            event(new UserLoggedOut($user));
        }

        Auth::logout();
    }

    /**
     * Handle logout from all devices.
     */
    public function logoutAllDevices()
    {
        $user = Auth::user();

        if ($user) {
            // Deactivate all sessions
            \App\Models\Auth\UserSession::where('user_id', $user->id)
                ->update(['is_active' => false]);

            event(new UserLoggedOut($user));
        }

        Auth::logout();
    }

    /**
     * Verify 2FA code.
     *
     * @param User $user
     * @param string $code
     * @return bool
     */
    public function verifyTwoFactor(User $user, string $code): bool
    {
        if (!$user->two_factor_secret) {
            return false;
        }

        // Assuming use of a 2FA library like PragmaRX/Google2FA
        // return app(\PragmaRX\Google2FA\Google2FA::class)->verifyKey($user->two_factor_secret, $code);

        // Placeholder for now
        return true;
    }

    /**
     * Reset user password.
     *
     * @param string $email
     * @param string $password
     * @param string $token
     * @return void
     * @throws ValidationException
     */
    public function resetPassword(string $email, string $password, string $token)
    {
        if (!$this->passwordService->validateToken($token)) {
            throw ValidationException::withMessages([
                'token' => [__('Invalid or expired password reset token.')],
            ]);
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => [__('We can\'t find a user with that email address.')],
            ]);
        }

        $user->forceFill([
            'password' => $this->passwordService->hash($password),
            'remember_token' => Str::random(60),
        ])->save();

        // Invalidate token logic would go here
    }

    protected function generateUsername($first, $last)
    {
        return Str::slug($first . $last) . rand(1000, 9999);
    }
}
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'username' => $request->username,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'full_name' => trim($request->first_name . ' ' . $request->last_name),
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'email_verified_at' => null,
            'remember_token' => Str::random(60),
            'account_status' => 'pending_verification',
            'user_type' => $request->user_type,
            'language' => 'en',
            'currency' => 'USD',
            'timezone' => 'UTC',
            'wallet_balance' => 0.00,
            'wallet_currency' => 'USD',
            'saved_searches_count' => 0,
            'favorites_count' => 0,
            'properties_count' => 0,
            'properties_views_count' => 0,
            'leads_count' => 0,
            'transactions_count' => 0,
            'reviews_count' => 0,
            'average_rating' => 0.00,
            'referral_count' => 0,
            'referral_earnings' => 0.00,
            'two_factor_enabled' => false,
            'biometric_enabled' => false,
            'marketing_consent' => false,
            'newsletter_subscribed' => false,
            'is_agent' => false,
            'is_company' => false,
            'is_developer' => false,
            'is_investor' => false,
            'is_first_time_buyer' => false,
            'is_look_to_rent' => false,
            'is_look_to_buy' => false,
            'registration_ip' => $request->ip(),
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
            'last_login_device' => $request->userAgent(),
            'login_count' => 1,
        ]);

        // Generate referral code
        $user->referral_code = $user->generateReferralCode();
        $user->save();

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard'));
    }
}

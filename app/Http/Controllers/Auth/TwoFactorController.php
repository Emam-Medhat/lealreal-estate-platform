<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class TwoFactorController extends Controller
{
    public function show()
    {
        return view('auth.two-factor-challenge');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        // For demo purposes, accept any 6-digit code
        if (!preg_match('/^\d{6}$/', $request->code)) {
            return back()->withErrors(['code' => 'Invalid verification code. Please enter 6 digits.']);
        }

        $request->session()->put('auth.2fa_verified', true);

        return redirect()->intended(route('dashboard'));
    }

    public function setup()
    {
        if (!auth()->user()->two_factor_secret) {
            $this->generateSecret();
        }

        // Generate a simple QR code URL (placeholder)
        $qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode(auth()->user()->two_factor_secret);

        return view('auth.two-factor-setup', [
            'qrCodeUrl' => $qrCodeUrl,
            'secret' => auth()->user()->two_factor_secret,
        ]);
    }

    public function enable(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        // For demo purposes, accept any 6-digit code
        if (!preg_match('/^\d{6}$/', $request->code)) {
            return back()->withErrors(['code' => 'Invalid verification code. Please enter 6 digits.']);
        }

        auth()->user()->update([
            'two_factor_enabled' => true,
        ]);

        $request->session()->put('auth.2fa_verified', true);

        return back()->with('status', 'Two-factor authentication enabled successfully');
    }

    public function disable(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
            'code' => 'required|string',
        ]);

        if (!Hash::check($request->password, auth()->user()->password)) {
            return back()->withErrors(['password' => 'Invalid password']);
        }

        // For demo purposes, accept any 6-digit code
        if (!preg_match('/^\d{6}$/', $request->code)) {
            return back()->withErrors(['code' => 'Invalid verification code. Please enter 6 digits.']);
        }

        auth()->user()->update([
            'two_factor_enabled' => false,
            'two_factor_secret' => null,
        ]);

        $request->session()->forget('auth.2fa_verified');

        return back()->with('status', 'Two-factor authentication disabled successfully');
    }

    private function generateSecret()
    {
        // Generate a simple 16-character secret
        $secret = strtoupper(substr(md5(uniqid(rand(), true)), 0, 16));

        auth()->user()->update([
            'two_factor_secret' => $secret,
        ]);

        return $secret;
    }
}

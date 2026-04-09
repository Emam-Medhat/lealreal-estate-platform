<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\UserDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class BiometricAuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'device_id' => 'required|string',
            'biometric_data' => 'required|string',
            'device_name' => 'required|string',
        ]);

        $user = auth()->user();
        
        // Store biometric data for the device
        $device = UserDevice::updateOrCreate([
            'user_id' => $user->id,
            'device_id' => $request->device_id,
        ], [
            'device_name' => $request->device_name,
            'biometric_data' => Hash::make($request->biometric_data),
            'is_trusted' => true,
            'last_used_at' => now(),
        ]);

        return response()->json([
            'message' => 'Biometric authentication registered successfully',
            'device_id' => $device->id,
        ]);
    }

    public function authenticate(Request $request)
    {
        $request->validate([
            'device_id' => 'required|string',
            'biometric_data' => 'required|string',
        ]);

        $device = UserDevice::where('device_id', $request->device_id)
            ->where('is_trusted', true)
            ->first();

        if (!$device || !Hash::check($request->biometric_data, $device->biometric_data)) {
            return response()->json([
                'error' => 'Invalid biometric data or device not found',
            ], 401);
        }

        // Log the user in
        Auth::login($device->user);

        // Update device usage
        $device->update(['last_used_at' => now()]);

        return response()->json([
            'message' => 'Biometric authentication successful',
            'user' => $device->user,
        ]);
    }

    public function revoke(Request $request, $deviceId)
    {
        $device = UserDevice::where('id', $deviceId)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $device->update([
            'biometric_data' => null,
            'is_trusted' => false,
        ]);

        return back()->with('status', 'Biometric access revoked for this device');
    }

    public function devices()
    {
        $devices = UserDevice::where('user_id', auth()->id())
            ->whereNotNull('biometric_data')
            ->where('is_trusted', true)
            ->get();

        return view('auth.biometric-devices', compact('devices'));
    }
}

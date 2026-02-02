<?php

namespace App\Listeners;

use App\Events\UserLoggedIn;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Models\UserDevice;
use App\Models\UserSession;

class LogLoginActivity
{
    public function handle(UserLoggedIn $event): void
    {
        $user = $event->user;
        $user->incrementLoginCount();

        $request = request();
        $this->recordUserDevice($user, $request);
        $this->recordUserSession($user, $request);
    }

    private function recordUserDevice($user, $request)
    {
        $userAgent = $request->header('User-Agent');
        $deviceInfo = $this->parseUserAgent($userAgent);

        UserDevice::updateOrCreate([
            'user_id' => $user->id,
            'ip_address' => $request->ip(),
            'device_name' => $deviceInfo['device'],
        ], [
            'platform' => $deviceInfo['platform'],
            'browser' => $deviceInfo['browser'],
            'browser_version' => $deviceInfo['browser_version'] ?? null,
            'user_agent' => $userAgent,
            'last_seen_at' => now(),
            'is_active' => true,
        ]);
    }

    private function recordUserSession($user, $request)
    {
        $sessionId = Session::getId();

        UserSession::updateOrCreate([
            'session_id' => $sessionId,
        ], [
            'user_id' => $user->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
            'last_activity_at' => now(),
            'expires_at' => now()->addMinutes(config('session.lifetime')),
            'is_active' => true,
            'login_method' => 'password', // Could be dynamic if event had method info
        ]);
    }

    private function parseUserAgent($userAgent)
    {
        $device = 'Unknown';
        $type = 'desktop';
        $platform = 'Unknown';
        $browser = 'Unknown';

        // Detect mobile devices
        if (preg_match('/Mobile|Android|iPhone|iPad|iPod/', $userAgent)) {
            $type = 'mobile';
            if (preg_match('/iPad/', $userAgent)) {
                $type = 'tablet';
            }
        }

        // Detect platform
        if (preg_match('/Windows/', $userAgent)) {
            $platform = 'Windows';
        } elseif (preg_match('/Mac/', $userAgent)) {
            $platform = 'macOS';
        } elseif (preg_match('/Linux/', $userAgent)) {
            $platform = 'Linux';
        } elseif (preg_match('/Android/', $userAgent)) {
            $platform = 'Android';
        } elseif (preg_match('/iOS|iPhone|iPad/', $userAgent)) {
            $platform = 'iOS';
        }

        // Detect browser
        if (preg_match('/Chrome/', $userAgent)) {
            $browser = 'Chrome';
        } elseif (preg_match('/Firefox/', $userAgent)) {
            $browser = 'Firefox';
        } elseif (preg_match('/Safari/', $userAgent)) {
            $browser = 'Safari';
        } elseif (preg_match('/Edge/', $userAgent)) {
            $browser = 'Edge';
        }

        // Detect browser version
        $browser_version = null;
        if ($browser === 'Chrome' && preg_match('/Chrome\/(\d+\.\d+)/', $userAgent, $matches)) {
            $browser_version = $matches[1];
        } elseif ($browser === 'Firefox' && preg_match('/Firefox\/(\d+\.\d+)/', $userAgent, $matches)) {
            $browser_version = $matches[1];
        } elseif ($browser === 'Safari' && preg_match('/Version\/(\d+\.\d+)/', $userAgent, $matches)) {
            $browser_version = $matches[1];
        } elseif ($browser === 'Edge' && preg_match('/Edge\/(\d+\.\d+)/', $userAgent, $matches)) {
            $browser_version = $matches[1];
        }

        return [
            'device' => $device,
            'platform' => $platform,
            'browser' => $browser,
            'browser_version' => $browser_version,
        ];
    }
}

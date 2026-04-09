<?php

namespace App\Http\Middleware;

use App\Helpers\NotificationHelper;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckNotificationPreferences
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $type = 'email')
    {
        if (!Auth::check()) {
            return $next($request);
        }

        $canSend = false;
        
        switch ($type) {
            case 'email':
                $canSend = NotificationHelper::canSendEmail();
                break;
            case 'sms':
                $canSend = NotificationHelper::canSendSMS();
                break;
            case 'push':
                $canSend = NotificationHelper::canSendPush();
                break;
            case 'property':
                $canSend = NotificationHelper::canSendPropertyAlerts();
                break;
            default:
                $canSend = true;
        }

        if (!$canSend) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Notifications are disabled for this user',
                    'disabled' => true
                ], 403);
            }
            
            return back()->with('error', 'هذا النوع من الإشعارات معطل في تفضيلاتك');
        }

        return $next($request);
    }
}

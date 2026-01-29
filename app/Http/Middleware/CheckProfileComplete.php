<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

class CheckProfileComplete
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();

        // Admin can bypass profile completion check
        if ($user->hasRole('admin')) {
            return $next($request);
        }

        // Check if profile is incomplete (less than 80% complete)
        if ($user->profile_completion_percentage < 80) {
            $message = $this->getCompletionMessage($user->profile_completion_percentage);

            if ($request->ajax() || $request->wantsJson()) {
                $redirectUrl = Route::has('user.profile.edit') ? route('user.profile.edit') : route('home');
                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'code' => 403,
                    'profile_completion' => $user->profile_completion_percentage,
                    'requires_profile_completion' => true,
                    'redirect_url' => $redirectUrl
                ], 403);
            }

            // Allow access to profile completion pages
            $allowedRoutes = [
                'user.profile.edit',
                'user.profile.update',
                'user.profile.avatar.upload',
                'settings.index',
                'kyc.create',
                'kyc.store',
                'kyc.status'
            ];

            if (!in_array($request->route()?->getName(), $allowedRoutes)) {
                $route = Route::has('user.profile.edit') ? 'user.profile.edit' : 'home';
                return redirect()->route($route)
                    ->with('info', $message);
            }
        }

        return $next($request);
    }

    /**
     * Get completion message based on percentage
     *
     * @param  int  $percentage
     * @return string
     */
    private function getCompletionMessage($percentage)
    {
        if ($percentage < 50) {
            return 'ملفك الشخصي غير مكتمل. يرجى إكمال البيانات الأساسية للحصول على أفضل تجربة';
        } elseif ($percentage < 80) {
            return 'ملفك الشخصي يحتاج لبعض التحسين. يرجى إكمال المزيد من البيانات';
        } else {
            return 'ملفك الشخصي شبه مكتمل. يرجى إكمال البيانات المتبقية';
        }
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Models\KycVerification;

class CheckKycVerification
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string|null  $level
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, $level = null)
    {
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'يجب تسجيل الدخول أولاً');
        }

        $user = Auth::user();

        // Admin can bypass KYC check
        if ($user->hasRole('admin')) {
            return $next($request);
        }

        $kycVerification = $user->kycVerification;

        if (!$kycVerification) {
            if ($request->ajax() || $request->wantsJson()) {
                $redirectUrl = Route::has('kyc.create') ? route('kyc.create') : route('home');
                return response()->json([
                    'success' => false,
                    'message' => 'يجب إكمال عملية التحقق من الهوية (KYC) أولاً',
                    'code' => 403,
                    'requires_kyc' => true,
                    'redirect_url' => $redirectUrl
                ], 403);
            }

            $route = Route::has('kyc.create') ? 'kyc.create' : 'home';
            return redirect()->route($route)
                ->with('warning', 'يجب إكمال عملية التحقق من الهوية (KYC) أولاً');
        }

        // Check KYC status
        if ($kycVerification->status !== 'approved') {
            $message = $this->getStatusMessage($kycVerification->status);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'code' => 403,
                    'kyc_status' => $kycVerification->status,
                    'requires_kyc' => true
                ], 403);
            }

            $route = Route::has('kyc.status') ? 'kyc.status' : 'home';
            return redirect()->route($route)
                ->with('warning', $message);
        }

        // Check if specific KYC level is required
        if ($level && !$this->hasRequiredLevel($kycVerification, $level)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'هذه الميزة تتطلب مستوى تحقق أعلى',
                    'code' => 403,
                    'required_level' => $level,
                    'current_level' => $kycVerification->level
                ], 403);
            }

            $route = Route::has('kyc.upgrade') ? 'kyc.upgrade' : 'home';
            return redirect()->route($route)
                ->with('error', 'هذه الميزة تتطلب مستوى تحقق أعلى');
        }

        // Check if KYC is still valid (not expired)
        if ($kycVerification->isExpired()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'انتهت صلاحية التحقق من الهوية. يرجى تجديده',
                    'code' => 403,
                    'kyc_expired' => true
                ], 403);
            }

            $route = Route::has('kyc.renew') ? 'kyc.renew' : 'home';
            return redirect()->route($route)
                ->with('warning', 'انتهت صلاحية التحقق من الهوية. يرجى تجديده');
        }

        // Add KYC info to request
        $request->merge([
            'kyc_verified' => true,
            'kyc_level' => $kycVerification->level,
            'kyc_verification' => $kycVerification
        ]);

        return $next($request);
    }

    /**
     * Get status message in Arabic
     *
     * @param  string  $status
     * @return string
     */
    private function getStatusMessage($status)
    {
        $messages = [
            'pending' => 'جاري مراجعة مستندات التحقق من الهوية. سيتم إعلامك عند الانتهاء',
            'rejected' => 'تم رفض مستندات التحقق من الهوية. يرجى تحميل المستندات الصحيحة',
            'expired' => 'انتهت صلاحية التحقق من الهوية. يرجى تجديده',
            'suspended' => 'تم تعليق التحقق من الهوية. يرجى التواصل مع الدعم',
            'under_review' => 'مستنداتك قيد المراجعة المفصلة. سيتم إعلامك بالنتيجة قريباً'
        ];

        return $messages[$status] ?? 'حالة التحقق من الهوية غير معروفة';
    }

    /**
     * Check if user has required KYC level
     *
     * @param  \App\Models\KycVerification  $kycVerification
     * @param  string  $requiredLevel
     * @return bool
     */
    private function hasRequiredLevel($kycVerification, $requiredLevel)
    {
        $levels = [
            'basic' => 1,
            'standard' => 2,
            'enhanced' => 3,
            'premium' => 4
        ];

        $currentLevel = $levels[$kycVerification->level] ?? 0;
        $requiredLevelValue = $levels[$requiredLevel] ?? 0;

        return $currentLevel >= $requiredLevelValue;
    }
}

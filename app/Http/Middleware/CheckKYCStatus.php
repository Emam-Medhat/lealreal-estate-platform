<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\HttpFoundation\Response;

class CheckKYCStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        if (! $user || ! $user->kyc_verified) {
            return $request->expectsJson()
                ? abort(403, 'KYC verification required.')
                : Redirect::route('kyc.verification')->with('error', 'KYC verification is required to access this feature.');
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Cache\RateLimiter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ThrottleLogin
{
    protected $limiter;

    public function __construct(RateLimiter $limiter)
    {
        $this->limiter = $limiter;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, int $maxAttempts = 5, int $decayMinutes = 1): Response
    {
        $key = $this->resolveRequestSignature($request);

        if ($this->limiter->tooManyAttempts($key, $maxAttempts)) {
            throw ValidationException::withMessages([
                'email' => __('auth.throttle', [
                    'seconds' => $this->limiter->availableIn($key),
                    'minutes' => ceil($this->limiter->availableIn($key) / 60),
                ]),
            ])->status(429);
        }

        $this->limiter->hit($key, $decayMinutes * 60);

        $response = $next($request);

        if (Auth::check()) {
            $this->limiter->clear($key);
        }

        return $response;
    }

    /**
     * Resolve the request signature.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function resolveRequestSignature(Request $request): string
    {
        return sha1(
            $request->method() .
            '|' . $request->route()->uri() .
            '|' . $request->ip()
        );
    }
}

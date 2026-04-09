<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Property;

class CheckPropertyStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $status = 'active'): Response
    {
        $property = $request->route('property');
        if (!($property instanceof Property)) {
            $property = Property::find($request->route('property'));
        }

        if (!$property) {
            abort(404);
        }

        // Allow owner/admin to view even if not active
        if ($request->user() && ($request->user()->id === $property->agent_id || $request->user()->is_admin)) {
            return $next($request);
        }

        if ($property->status !== $status) {
            abort(404); // Hide non-active properties from public
        }

        return $next($request);
    }
}

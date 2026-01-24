<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Property;

class CheckPropertyOwnership
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'يجب تسجيل الدخول أولاً');
        }

        $user = Auth::user();

        // Admin can access all properties
        if ($user->hasRole('admin')) {
            return $next($request);
        }

        // Get property ID from route
        $propertyId = $this->getPropertyId($request);

        if (!$propertyId) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'لم يتم تحديد العقار',
                    'code' => 400
                ], 400);
            }

            return redirect()->back()
                ->with('error', 'لم يتم تحديد العقار');
        }

        $property = Property::find($propertyId);

        if (!$property) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'العقار غير موجود',
                    'code' => 404
                ], 404);
            }

            return redirect()->route('properties.index')
                ->with('error', 'العقار غير موجود');
        }

        // Check if user owns the property or has permission
        if (!$this->canAccessProperty($user, $property)) {
            // Log unauthorized access attempt
            activity()
                ->causedBy($user)
                ->withProperties([
                    'property_id' => $propertyId,
                    'property_title' => $property->title,
                    'route' => $request->route()->getName(),
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ])
                ->log('محاولة وصول غير مصرح بها للعقار');

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'ليس لديك صلاحية للوصول إلى هذا العقار',
                    'code' => 403,
                    'property_id' => $propertyId
                ], 403);
            }

            return redirect()->route('properties.index')
                ->with('error', 'ليس لديك صلاحية للوصول إلى هذا العقار');
        }

        // Add property to request for easy access
        $request->merge(['property' => $property]);

        return $next($request);
    }

    /**
     * Get property ID from request
     *
     * @param  \Illuminate\Http\Request  $request
     * @return int|null
     */
    private function getPropertyId($request)
    {
        // Try to get from route parameter
        if ($request->route('property')) {
            return $request->route('property');
        }

        // Try to get from request input
        if ($request->input('property_id')) {
            return $request->input('property_id');
        }

        // Try to get from request input (alternative name)
        if ($request->input('id')) {
            return $request->input('id');
        }

        return null;
    }

    /**
     * Check if user can access the property
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Property  $property
     * @return bool
     */
    private function canAccessProperty($user, $property)
    {
        // Property owner (Agent)
        if ($property->agent_id === $user->id) {
            return true;
        }

        // Company agent can access company properties
        if ($user->hasRole('agent') && $user->company_id) {
            if ($property->company_id === $user->company_id) {
                return true;
            }
        }

        // Company owner can access company properties
        if ($user->hasRole('company') && $property->company_id === $user->company_id) {
            return true;
        }

        // Check if user has specific permission for this property
        if ($user->hasPermission('properties.all')) {
            return true;
        }

        // Check if user is assigned to this property
        if ($property->assignedAgents()->where('user_id', $user->id)->exists()) {
            return true;
        }

        return false;
    }
}

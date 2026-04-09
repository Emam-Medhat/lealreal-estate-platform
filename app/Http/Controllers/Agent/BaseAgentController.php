<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

abstract class BaseAgentController extends Controller
{
    /**
     * Get the authenticated user's agent profile
     * 
     * @return Agent|null
     */
    protected function getAgent(): ?Agent
    {
        return Auth::user()->agent;
    }

    /**
     * Get the authenticated user's agent profile or return error response
     * 
     * @return Agent|JsonResponse
     */
    protected function getAgentOrError()
    {
        $agent = $this->getAgent();
        
        if (!$agent) {
            return response()->json(['error' => 'Agent profile not found. Please contact administrator.'], 404);
        }
        
        return $agent;
    }

    /**
     * Check if user has agent profile, if not redirect with error
     * 
     * @return \Illuminate\Http\RedirectResponse|null
     */
    protected function requireAgent()
    {
        if (!$this->getAgent()) {
            return redirect()->route('dashboard')->with('error', 'Agent profile not found. Please contact administrator.');
        }
        
        return null;
    }
}

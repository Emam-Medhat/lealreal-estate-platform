<?php

namespace App\Http\Controllers\Api\Intelligence;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Intelligence\RecommendationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * API: Recommendations
 */
class RecommendationController extends Controller
{
    public function feed(RecommendationService $service)
    {
        $user = Auth::user();
        $feed = $service->feed($user);
        return response()->json([
            'user_id' => $user->id,
            'feed' => $feed,
        ]);
    }
}


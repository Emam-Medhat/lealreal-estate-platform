<?php

namespace App\Http\Controllers\Tools;

use App\Http\Controllers\Controller;
use App\Models\NeighborhoodProfile;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class NeighborhoodScoreController extends Controller
{
    public function index(): View
    {
        return view('tools.neighborhood_score');
    }

    public function calculate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'safety' => 'required|numeric|min:0|max:1',
            'schools' => 'required|numeric|min:0|max:1',
            'commute' => 'required|numeric|min:0|max:1',
            'cost' => 'required|numeric|min:0|max:1',
            'green' => 'required|numeric|min:0|max:1',
        ]);

        $weights = $validated;
        $scores = [
            'safety' => 0.7,
            'schools' => 0.6,
            'commute' => 0.5,
            'cost' => 0.55,
            'green' => 0.65,
        ];
        $total = 0;
        $sumWeights = 0;
        foreach ($weights as $k => $w) {
            $total += $w * ($scores[$k] ?? 0);
            $sumWeights += $w;
        }
        $score = $sumWeights > 0 ? round($total / $sumWeights, 3) : 0;

        $profile = NeighborhoodProfile::create([
            'weights_json' => $weights,
            'result_json' => [
                'score' => $score,
                'breakdown' => $scores,
            ],
            'user_id' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'id' => $profile->id,
            'score' => $score,
            'breakdown' => $scores,
        ]);
    }
}

<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\AgentReview;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AgentReviewController extends Controller
{
    /**
     * Store a new agent review.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Validation (implicit or explicit)
            // Ideally we should validate. The closure didn't validate explicitly but passed raw data.
            // I'll add basic validation.
            $request->validate([
                'agent_id' => 'required|exists:agents,id',
                'rating' => 'required|numeric|min:1|max:5',
                'review_text' => 'required|string',
                'reviewer_name' => 'required|string',
                'reviewer_email' => 'required|email',
            ]);

            $review = AgentReview::create([
                'agent_id' => $request->agent_id,
                'rating' => $request->rating,
                'review_text' => $request->review_text,
                'reviewer_name' => $request->reviewer_name,
                'reviewer_email' => $request->reviewer_email,
                'status' => 'pending', // Requires admin approval
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Review submitted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error submitting review: ' . $e->getMessage()
            ], 500);
        }
    }
}

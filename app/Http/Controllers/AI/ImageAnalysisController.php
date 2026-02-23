<?php

namespace App\Http\Controllers\AI;

use App\Http\Controllers\Controller;
use App\Jobs\AI\AnalyzePropertyImageJob;
use App\Services\AI\ImageAnalysisService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Image Analysis Controller
 *
 * POST /api/ai/images/analyze         → upload & queue analysis
 * GET  /api/ai/images/{id}/result     → retrieve analysis result
 * POST /api/ai/images/analyze-url     → analyze by URL (admin only)
 */
class ImageAnalysisController extends Controller
{
    public function __construct(
        private readonly ImageAnalysisService $service
    ) {
    }

    /**
     * POST /api/ai/images/analyze
     *
     * Accepts a file upload, stores it, and dispatches a background job.
     * Returns a job token so the client can poll for the result.
     */
    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'image' => ['required', 'file', 'image', 'max:10240', 'mimes:jpg,jpeg,png,webp'],
            'property_id' => ['sometimes', 'nullable', 'integer', 'exists:properties,id'],
        ]);

        $path = $request->file('image')->store('ai-analysis/pending', 'local');

        $recordId = DB::table('property_image_analyses')->insertGetId([
            'property_id' => $request->input('property_id'),
            'image_path' => $path,
            'status' => 'pending',
            'submitted_by' => auth()->id(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Dispatch background job
        AnalyzePropertyImageJob::dispatch($recordId, $path)
            ->onQueue(config('ai.image_analysis.queue', 'image-analysis'));

        return response()->json([
            'success' => true,
            'analysis_id' => $recordId,
            'status' => 'pending',
            'message' => 'Image queued for analysis. Poll /api/ai/images/{id}/result',
        ], 202);
    }

    /**
     * GET /api/ai/images/{id}/result
     * Poll for analysis result.
     */
    public function result(int $id): JsonResponse
    {
        $record = DB::table('property_image_analyses')->find($id);

        if (!$record) {
            return response()->json(['success' => false, 'message' => 'Not found'], 404);
        }

        if ($record->status === 'pending' || $record->status === 'processing') {
            return response()->json([
                'success' => true,
                'status' => $record->status,
                'message' => 'Analysis in progress',
            ]);
        }

        return response()->json([
            'success' => true,
            'status' => $record->status,
            'property_id' => $record->property_id,
            'room_type' => $record->room_type,
            'furniture_quality' => $record->furniture_quality,
            'brightness_level' => $record->brightness_level,
            'quality_score' => $record->quality_score,
            'issues' => json_decode($record->issues, true) ?? [],
            'source' => $record->source,
            'analyzed_at' => $record->updated_at,
        ]);
    }

    /**
     * POST /api/ai/images/analyze-url  (admin)
     * Analyze a property photo from an existing URL (e.g. already-uploaded images).
     */
    public function analyzeUrl(Request $request): JsonResponse
    {
        $request->validate([
            'url' => ['required', 'url'],
            'property_id' => ['sometimes', 'nullable', 'integer'],
        ]);

        $result = $this->service->analyze($request->input('url'));

        if (!$result['success']) {
            return response()->json(['success' => false, 'message' => 'Analysis failed'], 503);
        }

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }
}

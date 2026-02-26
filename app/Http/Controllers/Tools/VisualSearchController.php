<?php

namespace App\Http\Controllers\Tools;

use App\Http\Controllers\Controller;
use App\Models\VisualSearchRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class VisualSearchController extends Controller
{
    public function index(): View
    {
        return view('tools.visual_search');
    }

    public function search(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'image' => 'required|image|max:4096',
        ]);

        $path = $request->file('image')->store('visual_search', 'public');

        $req = VisualSearchRequest::create([
            'image_path' => $path,
            'result_json' => [
                'similar_listings' => [],
                'note' => 'Stub results – integrate CLIP + Pinecone',
            ],
        ]);

        return response()->json([
            'success' => true,
            'id' => $req->id,
            'preview_url' => Storage::disk('public')->url($path),
            'results' => $req->result_json,
        ]);
    }
}

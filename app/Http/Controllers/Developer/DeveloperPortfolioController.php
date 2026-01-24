<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Developer\StorePortfolioRequest;
use App\Http\Requests\Developer\UpdatePortfolioRequest;
use App\Models\Developer;
use App\Models\DeveloperPortfolio;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DeveloperPortfolioController extends Controller
{
    public function index(Request $request)
    {
        $developer = Auth::user()->developer;
        
        $portfolios = $developer->portfolios()
            ->when($request->search, function ($query, $search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->when($request->category, function ($query, $category) {
                $query->where('category', $category);
            })
            ->latest()
            ->paginate(20);

        return view('developer.portfolios.index', compact('portfolios'));
    }

    public function create()
    {
        $developer = Auth::user()->developer;
        return view('developer.portfolios.create', compact('developer'));
    }

    public function store(StorePortfolioRequest $request)
    {
        $developer = Auth::user()->developer;
        
        $portfolio = DeveloperPortfolio::create([
            'developer_id' => $developer->id,
            'title' => $request->title,
            'description' => $request->description,
            'category' => $request->category,
            'project_name' => $request->project_name,
            'location' => $request->location,
            'completion_date' => $request->completion_date,
            'project_value' => $request->project_value,
            'project_area' => $request->project_area,
            'project_type' => $request->project_type,
            'client_name' => $request->client_name,
            'architect' => $request->architect,
            'contractor' => $request->contractor,
            'consultants' => $request->consultants ?? [],
            'key_features' => $request->key_features ?? [],
            'challenges' => $request->challenges ?? [],
            'solutions' => $request->solutions ?? [],
            'technologies' => $request->technologies ?? [],
            'materials' => $request->materials ?? [],
            'awards' => $request->awards ?? [],
            'testimonials' => $request->testimonials ?? [],
            'media_coverage' => $request->media_coverage ?? [],
            'sustainability_features' => $request->sustainability_features ?? [],
            'innovation_highlights' => $request->innovation_highlights ?? [],
            'status' => $request->status ?? 'published',
            'is_featured' => $request->is_featured ?? false,
            'sort_order' => $request->sort_order ?? 0,
        ]);

        // Handle portfolio images
        if ($request->hasFile('images')) {
            $images = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('portfolio-images', 'public');
                $images[] = [
                    'path' => $path,
                    'name' => $image->getClientOriginalName(),
                    'caption' => '',
                    'uploaded_at' => now()->toISOString(),
                ];
            }
            $portfolio->update(['images' => $images]);
        }

        // Handle cover image
        if ($request->hasFile('cover_image')) {
            $coverPath = $request->file('cover_image')->store('portfolio-covers', 'public');
            $portfolio->update(['cover_image' => $coverPath]);
        }

        // Handle video upload
        if ($request->hasFile('video')) {
            $videoPath = $request->file('video')->store('portfolio-videos', 'public');
            $portfolio->update(['video' => $videoPath]);
        }

        // Handle documents
        if ($request->hasFile('documents')) {
            $documents = [];
            foreach ($request->file('documents') as $document) {
                $path = $document->store('portfolio-documents', 'public');
                $documents[] = [
                    'path' => $path,
                    'name' => $document->getClientOriginalName(),
                    'type' => $document->getClientOriginalExtension(),
                    'uploaded_at' => now()->toISOString(),
                ];
            }
            $portfolio->update(['documents' => $documents]);
        }

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'created_developer_portfolio',
            'details' => "Created portfolio: {$portfolio->title}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('developer.portfolios.show', $portfolio)
            ->with('success', 'Portfolio created successfully.');
    }

    public function show(DeveloperPortfolio $portfolio)
    {
        $this->authorize('view', $portfolio);
        
        return view('developer.portfolios.show', compact('portfolio'));
    }

    public function edit(DeveloperPortfolio $portfolio)
    {
        $this->authorize('update', $portfolio);
        
        return view('developer.portfolios.edit', compact('portfolio'));
    }

    public function update(UpdatePortfolioRequest $request, DeveloperPortfolio $portfolio)
    {
        $this->authorize('update', $portfolio);
        
        $portfolio->update([
            'title' => $request->title,
            'description' => $request->description,
            'category' => $request->category,
            'project_name' => $request->project_name,
            'location' => $request->location,
            'completion_date' => $request->completion_date,
            'project_value' => $request->project_value,
            'project_area' => $request->project_area,
            'project_type' => $request->project_type,
            'client_name' => $request->client_name,
            'architect' => $request->architect,
            'contractor' => $request->contractor,
            'consultants' => $request->consultants ?? [],
            'key_features' => $request->key_features ?? [],
            'challenges' => $request->challenges ?? [],
            'solutions' => $request->solutions ?? [],
            'technologies' => $request->technologies ?? [],
            'materials' => $request->materials ?? [],
            'awards' => $request->awards ?? [],
            'testimonials' => $request->testimonials ?? [],
            'media_coverage' => $request->media_coverage ?? [],
            'sustainability_features' => $request->sustainability_features ?? [],
            'innovation_highlights' => $request->innovation_highlights ?? [],
            'status' => $request->status,
            'is_featured' => $request->is_featured,
            'sort_order' => $request->sort_order,
        ]);

        // Handle new images
        if ($request->hasFile('images')) {
            $existingImages = $portfolio->images ?? [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('portfolio-images', 'public');
                $existingImages[] = [
                    'path' => $path,
                    'name' => $image->getClientOriginalName(),
                    'caption' => '',
                    'uploaded_at' => now()->toISOString(),
                ];
            }
            $portfolio->update(['images' => $existingImages]);
        }

        // Handle cover image update
        if ($request->hasFile('cover_image')) {
            if ($portfolio->cover_image) {
                Storage::disk('public')->delete($portfolio->cover_image);
            }
            $coverPath = $request->file('cover_image')->store('portfolio-covers', 'public');
            $portfolio->update(['cover_image' => $coverPath]);
        }

        // Handle video update
        if ($request->hasFile('video')) {
            if ($portfolio->video) {
                Storage::disk('public')->delete($portfolio->video);
            }
            $videoPath = $request->file('video')->store('portfolio-videos', 'public');
            $portfolio->update(['video' => $videoPath]);
        }

        // Handle new documents
        if ($request->hasFile('documents')) {
            $existingDocuments = $portfolio->documents ?? [];
            foreach ($request->file('documents') as $document) {
                $path = $document->store('portfolio-documents', 'public');
                $existingDocuments[] = [
                    'path' => $path,
                    'name' => $document->getClientOriginalName(),
                    'type' => $document->getClientOriginalExtension(),
                    'uploaded_at' => now()->toISOString(),
                ];
            }
            $portfolio->update(['documents' => $existingDocuments]);
        }

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated_developer_portfolio',
            'details' => "Updated portfolio: {$portfolio->title}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('developer.portfolios.show', $portfolio)
            ->with('success', 'Portfolio updated successfully.');
    }

    public function destroy(DeveloperPortfolio $portfolio)
    {
        $this->authorize('delete', $portfolio);
        
        $portfolioTitle = $portfolio->title;
        
        // Delete portfolio images
        if ($portfolio->images) {
            foreach ($portfolio->images as $image) {
                Storage::disk('public')->delete($image['path']);
            }
        }
        
        // Delete cover image
        if ($portfolio->cover_image) {
            Storage::disk('public')->delete($portfolio->cover_image);
        }
        
        // Delete video
        if ($portfolio->video) {
            Storage::disk('public')->delete($portfolio->video);
        }
        
        // Delete documents
        if ($portfolio->documents) {
            foreach ($portfolio->documents as $document) {
                Storage::disk('public')->delete($document['path']);
            }
        }
        
        $portfolio->delete();

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'deleted_developer_portfolio',
            'details' => "Deleted portfolio: {$portfolioTitle}",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('developer.portfolios.index')
            ->with('success', 'Portfolio deleted successfully.');
    }

    public function toggleFeatured(Request $request, DeveloperPortfolio $portfolio): JsonResponse
    {
        $this->authorize('update', $portfolio);
        
        $portfolio->update(['is_featured' => !$portfolio->is_featured]);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'toggled_portfolio_featured',
            'details' => ($portfolio->is_featured ? 'Featured' : 'Unfeatured') . " portfolio: {$portfolio->title}",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'is_featured' => $portfolio->is_featured,
            'message' => 'Portfolio featured status updated successfully'
        ]);
    }

    public function updateStatus(Request $request, DeveloperPortfolio $portfolio): JsonResponse
    {
        $this->authorize('update', $portfolio);
        
        $request->validate([
            'status' => 'required|in:draft,published,archived',
        ]);

        $portfolio->update(['status' => $request->status]);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated_portfolio_status',
            'details' => "Updated portfolio '{$portfolio->title}' status to {$request->status}",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'status' => $request->status,
            'message' => 'Portfolio status updated successfully'
        ]);
    }

    public function getPortfolioStats(): JsonResponse
    {
        $developer = Auth::user()->developer;
        
        $stats = [
            'total_portfolios' => $developer->portfolios()->count(),
            'published_portfolios' => $developer->portfolios()->where('status', 'published')->count(),
            'featured_portfolios' => $developer->portfolios()->where('is_featured', true)->count(),
            'draft_portfolios' => $developer->portfolios()->where('status', 'draft')->count(),
            'archived_portfolios' => $developer->portfolios()->where('status', 'archived')->count(),
            'total_project_value' => $developer->portfolios()->sum('project_value'),
            'by_category' => $developer->portfolios()
                ->groupBy('category')
                ->map(function ($group) {
                    return $group->count();
                }),
            'by_project_type' => $developer->portfolios()
                ->groupBy('project_type')
                ->map(function ($group) {
                    return $group->count();
                }),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    public function exportPortfolios(Request $request): JsonResponse
    {
        $request->validate([
            'format' => 'required|in:json,csv,xlsx',
            'status' => 'nullable|in:draft,published,archived',
            'category' => 'nullable|string|max:100',
        ]);

        $developer = Auth::user()->developer;
        
        $query = $developer->portfolios();

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->category) {
            $query->where('category', $request->category);
        }

        $portfolios = $query->get();

        $filename = "developer_portfolios_export_" . now()->format('Y-m-d_H-i-s');

        return response()->json([
            'success' => true,
            'data' => $portfolios,
            'filename' => $filename,
            'message' => 'Portfolios exported successfully'
        ]);
    }
}

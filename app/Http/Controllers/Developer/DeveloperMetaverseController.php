<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Developer\StoreMetaverseRequest;
use App\Http\Requests\Developer\UpdateMetaverseRequest;
use App\Models\Developer;
use App\Models\DeveloperProject;
use App\Models\DeveloperMetaverse;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DeveloperMetaverseController extends Controller
{
    public function index(Request $request)
    {
        $developer = Auth::user()->developer;
        
        $metaverses = $developer->metaverses()
            ->with(['project'])
            ->when($request->search, function ($query, $search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->when($request->project_id, function ($query, $projectId) {
                $query->where('project_id', $projectId);
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(20);

        $projects = $developer->projects()->pluck('name', 'id');

        return view('developer.metaverses.index', compact('metaverses', 'projects'));
    }

    public function create()
    {
        $developer = Auth::user()->developer;
        $projects = $developer->projects()->pluck('name', 'id');
        
        return view('developer.metaverses.create', compact('developer', 'projects'));
    }

    public function store(StoreMetaverseRequest $request)
    {
        $developer = Auth::user()->developer;
        
        $metaverse = DeveloperMetaverse::create([
            'developer_id' => $developer->id,
            'project_id' => $request->project_id,
            'title' => $request->title,
            'description' => $request->description,
            'metaverse_type' => $request->metaverse_type,
            'platform' => $request->platform,
            'access_url' => $request->access_url,
            'status' => $request->status ?? 'draft',
            'visibility' => $request->visibility ?? 'private',
            'version' => $request->version ?? '1.0.0',
            'compatibility' => $request->compatibility ?? [],
            'features' => $request->features ?? [],
            'assets' => $request->assets ?? [],
            'environments' => $request->environments ?? [],
            'interactions' => $request->interactions ?? [],
            'avatar_options' => $request->avatar_options ?? [],
            'navigation_options' => $request->navigation_options ?? [],
            'multiplayer_enabled' => $request->multiplayer_enabled ?? false,
            'max_concurrent_users' => $request->max_concurrent_users,
            'access_requirements' => $request->access_requirements ?? [],
            'pricing_model' => $request->pricing_model,
            'subscription_required' => $request->subscription_required ?? false,
            'subscription_price' => $request->subscription_price,
            'trial_period_days' => $request->trial_period_days,
            'technical_specs' => $request->technical_specs ?? [],
            'system_requirements' => $request->system_requirements ?? [],
            'supported_devices' => $request->supported_devices ?? [],
            'languages' => $request->languages ?? [],
            'analytics_enabled' => $request->analytics_enabled ?? false,
            'privacy_settings' => $request->privacy_settings ?? [],
            'moderation_level' => $request->moderation_level,
            'content_guidelines' => $request->content_guidelines ?? [],
            'integration_options' => $request->integration_options ?? [],
            'api_endpoints' => $request->api_endpoints ?? [],
            'webhook_urls' => $request->webhook_urls ?? [],
            'notes' => $request->notes,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        // Handle 3D model files
        if ($request->hasFile('model_files')) {
            $modelFiles = [];
            foreach ($request->file('model_files') as $file) {
                $path = $file->store('metaverse-models', 'public');
                $modelFiles[] = [
                    'path' => $path,
                    'name' => $file->getClientOriginalName(),
                    'type' => $file->getClientOriginalExtension(),
                    'size' => $file->getSize(),
                    'uploaded_at' => now()->toISOString(),
                ];
            }
            $metaverse->update(['model_files' => $modelFiles]);
        }

        // Handle texture files
        if ($request->hasFile('texture_files')) {
            $textureFiles = [];
            foreach ($request->file('texture_files') as $file) {
                $path = $file->store('metaverse-textures', 'public');
                $textureFiles[] = [
                    'path' => $path,
                    'name' => $file->getClientOriginalName(),
                    'type' => $file->getClientOriginalExtension(),
                    'uploaded_at' => now()->toISOString(),
                ];
            }
            $metaverse->update(['texture_files' => $textureFiles]);
        }

        // Handle preview images
        if ($request->hasFile('preview_images')) {
            $previewImages = [];
            foreach ($request->file('preview_images') as $image) {
                $path = $image->store('metaverse-previews', 'public');
                $previewImages[] = [
                    'path' => $path,
                    'name' => $image->getClientOriginalName(),
                    'caption' => '',
                    'uploaded_at' => now()->toISOString(),
                ];
            }
            $metaverse->update(['preview_images' => $previewImages]);
        }

        // Handle thumbnail
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('metaverse-thumbnails', 'public');
            $metaverse->update(['thumbnail' => $thumbnailPath]);
        }

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'created_developer_metaverse',
            'details' => "Created metaverse: {$metaverse->title}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('developer.metaverses.show', $metaverse)
            ->with('success', 'Metaverse created successfully.');
    }

    public function show(DeveloperMetaverse $metaverse)
    {
        $this->authorize('view', $metaverse);
        
        $metaverse->load(['project', 'creator', 'updater']);
        
        return view('developer.metaverses.show', compact('metaverse'));
    }

    public function edit(DeveloperMetaverse $metaverse)
    {
        $this->authorize('update', $metaverse);
        
        $developer = Auth::user()->developer;
        $projects = $developer->projects()->pluck('name', 'id');
        
        return view('developer.metaverses.edit', compact('metaverse', 'projects'));
    }

    public function update(UpdateMetaverseRequest $request, DeveloperMetaverse $metaverse)
    {
        $this->authorize('update', $metaverse);
        
        $metaverse->update([
            'project_id' => $request->project_id,
            'title' => $request->title,
            'description' => $request->description,
            'metaverse_type' => $request->metaverse_type,
            'platform' => $request->platform,
            'access_url' => $request->access_url,
            'status' => $request->status,
            'visibility' => $request->visibility,
            'version' => $request->version,
            'compatibility' => $request->compatibility ?? [],
            'features' => $request->features ?? [],
            'assets' => $request->assets ?? [],
            'environments' => $request->environments ?? [],
            'interactions' => $request->interactions ?? [],
            'avatar_options' => $request->avatar_options ?? [],
            'navigation_options' => $request->navigation_options ?? [],
            'multiplayer_enabled' => $request->multiplayer_enabled,
            'max_concurrent_users' => $request->max_concurrent_users,
            'access_requirements' => $request->access_requirements ?? [],
            'pricing_model' => $request->pricing_model,
            'subscription_required' => $request->subscription_required,
            'subscription_price' => $request->subscription_price,
            'trial_period_days' => $request->trial_period_days,
            'technical_specs' => $request->technical_specs ?? [],
            'system_requirements' => $request->system_requirements ?? [],
            'supported_devices' => $request->supported_devices ?? [],
            'languages' => $request->languages ?? [],
            'analytics_enabled' => $request->analytics_enabled,
            'privacy_settings' => $request->privacy_settings ?? [],
            'moderation_level' => $request->moderation_level,
            'content_guidelines' => $request->content_guidelines ?? [],
            'integration_options' => $request->integration_options ?? [],
            'api_endpoints' => $request->api_endpoints ?? [],
            'webhook_urls' => $request->webhook_urls ?? [],
            'notes' => $request->notes,
            'updated_by' => Auth::id(),
        ]);

        // Handle new model files
        if ($request->hasFile('model_files')) {
            $existingModelFiles = $metaverse->model_files ?? [];
            foreach ($request->file('model_files') as $file) {
                $path = $file->store('metaverse-models', 'public');
                $existingModelFiles[] = [
                    'path' => $path,
                    'name' => $file->getClientOriginalName(),
                    'type' => $file->getClientOriginalExtension(),
                    'size' => $file->getSize(),
                    'uploaded_at' => now()->toISOString(),
                ];
            }
            $metaverse->update(['model_files' => $existingModelFiles]);
        }

        // Handle new texture files
        if ($request->hasFile('texture_files')) {
            $existingTextureFiles = $metaverse->texture_files ?? [];
            foreach ($request->file('texture_files') as $file) {
                $path = $file->store('metaverse-textures', 'public');
                $existingTextureFiles[] = [
                    'path' => $path,
                    'name' => $file->getClientOriginalName(),
                    'type' => $file->getClientOriginalExtension(),
                    'uploaded_at' => now()->toISOString(),
                ];
            }
            $metaverse->update(['texture_files' => $existingTextureFiles]);
        }

        // Handle new preview images
        if ($request->hasFile('preview_images')) {
            $existingPreviewImages = $metaverse->preview_images ?? [];
            foreach ($request->file('preview_images') as $image) {
                $path = $image->store('metaverse-previews', 'public');
                $existingPreviewImages[] = [
                    'path' => $path,
                    'name' => $image->getClientOriginalName(),
                    'caption' => '',
                    'uploaded_at' => now()->toISOString(),
                ];
            }
            $metaverse->update(['preview_images' => $existingPreviewImages]);
        }

        // Handle thumbnail update
        if ($request->hasFile('thumbnail')) {
            if ($metaverse->thumbnail) {
                Storage::disk('public')->delete($metaverse->thumbnail);
            }
            $thumbnailPath = $request->file('thumbnail')->store('metaverse-thumbnails', 'public');
            $metaverse->update(['thumbnail' => $thumbnailPath]);
        }

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated_developer_metaverse',
            'details' => "Updated metaverse: {$metaverse->title}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('developer.metaverses.show', $metaverse)
            ->with('success', 'Metaverse updated successfully.');
    }

    public function destroy(DeveloperMetaverse $metaverse)
    {
        $this->authorize('delete', $metaverse);
        
        $metaverseTitle = $metaverse->title;
        
        // Delete model files
        if ($metaverse->model_files) {
            foreach ($metaverse->model_files as $file) {
                Storage::disk('public')->delete($file['path']);
            }
        }
        
        // Delete texture files
        if ($metaverse->texture_files) {
            foreach ($metaverse->texture_files as $file) {
                Storage::disk('public')->delete($file['path']);
            }
        }
        
        // Delete preview images
        if ($metaverse->preview_images) {
            foreach ($metaverse->preview_images as $image) {
                Storage::disk('public')->delete($image['path']);
            }
        }
        
        // Delete thumbnail
        if ($metaverse->thumbnail) {
            Storage::disk('public')->delete($metaverse->thumbnail);
        }
        
        $metaverse->delete();

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'deleted_developer_metaverse',
            'details' => "Deleted metaverse: {$metaverseTitle}",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('developer.metaverses.index')
            ->with('success', 'Metaverse deleted successfully.');
    }

    public function publish(Request $request, DeveloperMetaverse $metaverse): JsonResponse
    {
        $this->authorize('update', $metaverse);
        
        $metaverse->update([
            'status' => 'published',
            'visibility' => 'public',
            'updated_by' => Auth::id(),
        ]);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'published_metaverse',
            'details' => "Published metaverse: {$metaverse->title}",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'status' => 'published',
            'visibility' => 'public',
            'message' => 'Metaverse published successfully'
        ]);
    }

    public function generateAccessLink(Request $request, DeveloperMetaverse $metaverse): JsonResponse
    {
        $this->authorize('view', $metaverse);
        
        $request->validate([
            'expires_in_hours' => 'nullable|integer|min:1|max:168',
            'max_uses' => 'nullable|integer|min:1|max:1000',
        ]);

        $expiresAt = $request->expires_in_hours 
            ? now()->addHours($request->expires_in_hours)
            : null;

        $accessToken = bin2hex(random_bytes(16));
        $accessLink = route('metaverse.access', ['token' => $accessToken]);

        // Store access token logic would go here
        // For now, just return the generated link

        return response()->json([
            'success' => true,
            'access_link' => $accessLink,
            'access_token' => $accessToken,
            'expires_at' => $expiresAt,
            'max_uses' => $request->max_uses,
            'message' => 'Access link generated successfully'
        ]);
    }

    public function getUsageStats(DeveloperMetaverse $metaverse): JsonResponse
    {
        $this->authorize('view', $metaverse);
        
        // Simulate usage statistics
        $stats = [
            'total_visits' => rand(100, 10000),
            'unique_users' => rand(50, 5000),
            'average_session_duration' => rand(5, 60) . ' minutes',
            'peak_concurrent_users' => rand(10, 100),
            'daily_active_users' => rand(10, 200),
            'weekly_active_users' => rand(50, 800),
            'monthly_active_users' => rand(100, 2000),
            'bounce_rate' => rand(20, 60) . '%',
            'user_retention_rate' => rand(40, 80) . '%',
            'popular_environments' => [
                'Lobby' => rand(20, 100),
                'Showroom' => rand(15, 80),
                'Meeting Room' => rand(10, 60),
                'Outdoor Area' => rand(5, 40),
            ],
            'device_usage' => [
                'Desktop' => rand(40, 70) . '%',
                'Mobile ' => rand(20, 40) . '%',
                'VR Headset' => rand(5, 20) . '%',
            ],
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    public function getProjectMetaverses(DeveloperProject $project): JsonResponse
    {
        $this->authorize('view', $project);
        
        $metaverses = $project->metaverses()
            ->latest()
            ->get(['id', 'title', 'metaverse_type', 'status', 'visibility']);

        return response()->json([
            'success' => true,
            'metaverses' => $metaverses
        ]);
    }

    public function getMetaverseStats(): JsonResponse
    {
        $developer = Auth::user()->developer;
        
        $stats = [
            'total_metaverses' => $developer->metaverses()->count(),
            'published_metaverses' => $developer->metaverses()->where('status', 'published')->count(),
            'draft_metaverses' => $developer->metaverses()->where('status', 'draft')->count(),
            'public_metaverses' => $developer->metaverses()->where('visibility', 'public')->count(),
            'private_metaverses' => $developer->metaverses()->where('visibility', 'private')->count(),
            'by_type' => $developer->metaverses()
                ->groupBy('metaverse_type')
                ->map(function ($group) {
                    return $group->count();
                }),
            'by_platform' => $developer->metaverses()
                ->groupBy('platform')
                ->map(function ($group) {
                    return $group->count();
                }),
            'multiplayer_enabled' => $developer->metaverses()->where('multiplayer_enabled', true)->count(),
            'subscription_required' => $developer->metaverses()->where('subscription_required', true)->count(),
            'analytics_enabled' => $developer->metaverses()->where('analytics_enabled', true)->count(),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    public function exportMetaverses(Request $request): JsonResponse
    {
        $request->validate([
            'format' => 'required|in:json,csv,xlsx',
            'status' => 'nullable|in:draft,published,archived',
            'project_id' => 'nullable|exists:developer_projects,id',
        ]);

        $developer = Auth::user()->developer;
        
        $query = $developer->metaverses()->with(['project']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->project_id) {
            $query->where('project_id', $request->project_id);
        }

        $metaverses = $query->get();

        $filename = "developer_metaverses_export_" . now()->format('Y-m-d_H-i-s');

        return response()->json([
            'success' => true,
            'data' => $metaverses,
            'filename' => $filename,
            'message' => 'Metaverses exported successfully'
        ]);
    }
}

<?php

namespace App\Services;

use App\Models\Property;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\PropertyMedia;

class AgentPropertyService
{
    protected $cacheTTL = 3600; // Cache Time To Live in seconds (1 hour)

    public function getAgentProperties(User $agent, Request $request, int $perPage = 12)
    {
        $cacheKey = 'agent_properties_' . $agent->id . '_' . md5(json_encode($request->all()) . $perPage);

        return Cache::remember($cacheKey, $this->cacheTTL, function () use ($agent, $request, $perPage) {
            $properties = Property::where('agent_id', $agent->id)
                ->with([
                    'media',
                    'location',
                    'pricing',
                    'details',
                    'agent:id,name',
                    'company:id,name,logo'
                ])
                ->when($request->boolean('featured'), function($query) {
                    $query->where('featured', true);
                })
                ->when($request->status, function($query, $status) {
                    $query->where('status', $status);
                })
                ->when($request->type, function($query, $type) {
                    $query->where('property_type', $type);
                })
                ->when($request->search, function($query, $search) {
                    $query->where(function($q) use ($search) {
                        $q->where('title', 'like', "%{$search}%")
                          ->orWhere('description', 'like', "%{$search}%")
                          ->orWhere('property_code', 'like', "%{$search}%");
                    });
                })
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            return $properties;
        });
    }

    public function invalidateCache(int $agentId)
    {
        // Invalidate all caches related to this agent's properties
        // Laravel doesn't support wildcard cache deletion, so we need to track cache keys
        // For now, we'll use a more specific approach by clearing common cache patterns
        $cachePrefix = 'agent_properties_' . $agentId;
        
        // Clear common cache patterns (this is a simplified approach)
        // In a production environment, consider using Redis with scan command or a cache tagging system
        Cache::forget($cachePrefix . '_default');
        Cache::forget($cachePrefix . '_featured');
        Cache::forget($cachePrefix . '_active');
        Cache::forget($cachePrefix . '_draft');
        
        // Clear any other specific cache patterns that might be used
        $this->clearSearchCaches($agentId);
    }

    protected function clearSearchCaches(int $agentId)
    {
        // Clear search-related caches with common search parameters
        $commonSearches = ['', 'sale', 'rent', 'apartment', 'villa', 'house'];
        $commonStatuses = ['active', 'draft', 'inactive'];
        
        foreach ($commonSearches as $search) {
            foreach ($commonStatuses as $status) {
                $cacheKey = 'agent_properties_' . $agentId . '_' . md5(json_encode(['search' => $search, 'status' => $status]) . '12');
                Cache::forget($cacheKey);
            }
        }
    }

    public function createProperty(User $agent, array $validatedData)
    {
        $validatedData['agent_id'] = $agent->id;
        $validatedData['property_code'] = $this->generatePropertyCode();
        $validatedData['views_count'] = 0;
        $validatedData['inquiries_count'] = 0;
        $validatedData['favorites_count'] = 0;

        $property = Property::create($validatedData);
        $this->invalidateCache($agent->id);
        return $property;
    }

    public function updateProperty(Property $property, array $validatedData)
    {
        $property->update($validatedData);
        $this->invalidateCache($property->agent_id);
        return $property;
    }

    public function deleteProperty(Property $property)
    {
        // Delete associated media files
        foreach ($property->media as $media) {
            Storage::disk('public')->delete($media->file_path);
            $media->delete();
        }

        $property->delete();
        $this->invalidateCache($property->agent_id);
    }

    public function duplicateProperty(Property $property)
    {
        $newProperty = $property->replicate();
        $newProperty->title = $property->title . ' (Copy)';
        $newProperty->property_code = $this->generatePropertyCode();
        $newProperty->status = 'draft';
        $newProperty->views_count = 0;
        $newProperty->inquiries_count = 0;
        $newProperty->favorites_count = 0;
        $newProperty->save();
        $this->invalidateCache($property->agent_id);
        return $newProperty;
    }

    public function publishProperty(Property $property)
    {
        $property->update(['status' => 'active']);
        $this->invalidateCache($property->agent_id);
        return $property;
    }

    public function archiveProperty(Property $property)
    {
        $property->update(['status' => 'inactive']);
        $this->invalidateCache($property->agent_id);
        return $property;
    }

    public function uploadPropertyImages(Property $property, array $images)
    {
        $uploadedImages = [];
        foreach ($images as $image) {
            $path = $image->store('properties/images', 'public');
            $media = PropertyMedia::create([
                'property_id' => $property->id,
                'file_path' => $path,
                'file_name' => $image->getClientOriginalName(),
                'file_type' => $image->getMimeType(),
                'file_size' => $image->getSize(),
                'media_type' => 'image',
                'uploaded_by' => Auth::id(),
            ]);
            $uploadedImages[] = [
                'id' => $media->id,
                'url' => Storage::url($path),
                'name' => $image->getClientOriginalName(),
            ];
        }
        $this->invalidateCache($property->agent_id);
        return $uploadedImages;
    }

    public function deletePropertyImage(PropertyMedia $media)
    {
        $agentId = $media->property->agent_id;
        Storage::disk('public')->delete($media->file_path);
        $media->delete();
        $this->invalidateCache($agentId);
    }

    public function setPrimaryPropertyImage(Property $property, PropertyMedia $media)
    {
        // Remove primary status from all other images
        PropertyMedia::where('property_id', $property->id)
            ->where('media_type', 'image')
            ->update(['is_primary' => false]);

        // Set this image as primary
        $media->update(['is_primary' => true]);
        $this->invalidateCache($property->agent_id);
    }

    private function generatePropertyCode()
    {
        do {
            $code = 'PROP-' . strtoupper(uniqid());
        } while (Property::where('property_code', $code)->exists());

        return $code;
    }
}

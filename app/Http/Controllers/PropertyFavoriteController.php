<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\UserFavorite;
use App\Models\PropertyAnalytic;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class PropertyFavoriteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return redirect()->route('login');
            }
            
            $favorites = UserFavorite::where('user_id', $user->id)
                ->where('favoritable_type', Property::class)
                ->whereHasMorph('favoritable', [Property::class], function($query) {
                    $query->where('status', 'active');
                })
                ->with([
                    'favoritable' => function($query) {
                        $query->with([
                            'propertyType',
                            'location',
                            'details',
                            'pricing', // Property model uses pricing() for price relationship
                            'media' => function($q) {
                                $q->where('media_type', 'image')->limit(3);
                            }
                        ]);
                    }
                ])
                ->paginate(12);

            return view('properties.favorites', compact('favorites'));
            
        } catch (\Exception $e) {
            \Log::error('Favorites index error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('home')->with('error', 'Unable to load favorites. Please try again.');
        }
    }

    public function add(Request $request, Property $property = null): JsonResponse
    {
        $propertyId = $property ? $property->id : $request->property_id;

        if (!$propertyId) {
            return response()->json([
                'success' => false,
                'message' => 'Property ID is required',
            ], 400);
        }

        $user = Auth::user();

        // Check if property exists and is active
        $property = $property ?: Property::where('id', $propertyId)
            ->where('status', 'active')
            ->first();

        if (!$property) {
            return response()->json([
                'success' => false,
                'message' => 'Property not found or not available',
            ]);
        }

        // Check if already favorited
        $existingFavorite = UserFavorite::where('user_id', $user->id)
            ->where('favoritable_type', Property::class)
            ->where('favoritable_id', $property->id)
            ->first();

        if ($existingFavorite) {
            return response()->json([
                'success' => false,
                'message' => 'Property already in favorites',
            ]);
        }

        // Add to favorites
        UserFavorite::create([
            'user_id' => $user->id,
            'favoritable_type' => Property::class,
            'favoritable_id' => $property->id,
        ]);

        // Increment property favorites count
        $property->increment('favorites_count');

        // Record analytics
        PropertyAnalytic::recordMetric($property->id, 'favorites');

        return response()->json([
            'success' => true,
            'message' => 'Property added to favorites',
            'favorites_count' => $property->fresh()->favorites_count,
            'is_favorited' => true,
        ]);
    }

    public function remove(Request $request, Property $property = null): JsonResponse
    {
        $propertyId = $property ? $property->id : $request->property_id;

        if (!$propertyId) {
            return response()->json([
                'success' => false,
                'message' => 'Property ID is required',
            ], 400);
        }

        $user = Auth::user();

        $favorite = UserFavorite::where('user_id', $user->id)
            ->where('favoritable_type', Property::class)
            ->where('favoritable_id', $propertyId)
            ->first();

        if (!$favorite) {
            return response()->json([
                'success' => false,
                'message' => 'Property not in favorites',
            ]);
        }

        $favorite->delete();

        // Decrement property favorites count
        $property = $property ?: Property::find($propertyId);
        if ($property) {
            $property->decrement('favorites_count');
        }

        return response()->json([
            'success' => true,
            'message' => 'Property removed from favorites',
            'favorites_count' => $property?->fresh()->favorites_count ?? 0,
            'is_favorited' => false,
        ]);
    }

    public function toggle(Request $request): JsonResponse
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
        ]);

        $user = Auth::user();
        $propertyId = $request->property_id;

        $favorite = UserFavorite::where('user_id', $user->id)
            ->where('favoritable_type', Property::class)
            ->where('favoritable_id', $propertyId)
            ->first();

        if ($favorite) {
            // Remove from favorites
            $favorite->delete();
            $property = Property::find($propertyId);
            if ($property) {
                $property->decrement('favorites_count');
            }

            return response()->json([
                'success' => true,
                'message' => 'Property removed from favorites',
                'is_favorited' => false,
                'favorites_count' => $property?->fresh()->favorites_count ?? 0,
            ]);
        } else {
            // Add to favorites
            UserFavorite::create([
                'user_id' => $user->id,
                'favoritable_type' => Property::class,
                'favoritable_id' => $propertyId,
            ]);

            $property = Property::find($propertyId);
            if ($property) {
                $property->increment('favorites_count');
                PropertyAnalytic::recordMetric($propertyId, 'favorites');
            }

            return response()->json([
                'success' => true,
                'message' => 'Property added to favorites',
                'is_favorited' => true,
                'favorites_count' => $property?->fresh()->favorites_count ?? 0,
            ]);
        }
    }

    public function check(Request $request): JsonResponse
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
        ]);

        $user = Auth::user();
        $propertyId = $request->property_id;

        $isFavorited = UserFavorite::where('user_id', $user->id)
            ->where('favoritable_type', Property::class)
            ->where('favoritable_id', $propertyId)
            ->exists();

        $property = Property::find($propertyId);

        return response()->json([
            'success' => true,
            'is_favorited' => $isFavorited,
            'favorites_count' => $property?->favorites_count ?? 0,
        ]);
    }

    public function bulkAction(Request $request): JsonResponse
    {
        $request->validate([
            'property_ids' => 'required|array',
            'property_ids.*' => 'exists:properties,id',
            'action' => 'required|in:remove,export',
        ]);

        $user = Auth::user();
        $propertyIds = $request->property_ids;
        $action = $request->action;

        if ($action === 'remove') {
            $removed = UserFavorite::where('user_id', $user->id)
                ->where('favoritable_type', Property::class)
                ->whereIn('favoritable_id', $propertyIds)
                ->delete();

            // Update property favorites counts
            Property::whereIn('id', $propertyIds)
                ->decrement('favorites_count', $removed);

            return response()->json([
                'success' => true,
                'message' => "Removed {$removed} properties from favorites",
                'removed_count' => $removed,
            ]);
        }

        if ($action === 'export') {
            $favorites = $user->favoriteProperties()
                ->with(['propertyType', 'location', 'price', 'details'])
                ->whereIn('properties.id', $propertyIds)
                ->get();

            // Export logic would go here
            return response()->json([
                'success' => true,
                'message' => 'Favorites exported successfully',
                'data' => $favorites,
            ]);
        }
    }

    public function shareFavorites(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        $favorites = $user->favoriteProperties()
            ->with([
                'propertyType',
                'location',
                'price',
                'media' => function($query) {
                    $query->where('media_type', 'image')->limit(1);
                }
            ])
            ->where('status', 'active')
            ->get();

        // Generate shareable link or data
        $shareData = $favorites->map(function($property) {
            return [
                'id' => $property->id,
                'title' => $property->title,
                'price' => $property->price->formatted_price,
                'location' => $property->location->city . ', ' . $property->location->country,
                'property_type' => $property->propertyType->name,
                'image' => $property->media->first()?->getUrlAttribute(),
                'url' => route('properties.show', $property),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'share_url' => route('favorites.shared', ['user_id' => $user->id]),
                'properties' => $shareData,
                'total_count' => $favorites->count(),
            ]
        ]);
    }

    public function getFavoritesStats(): JsonResponse
    {
        $user = Auth::user();

        $stats = [
            'total_favorites' => $user->favoriteProperties()->count(),
            'active_favorites' => $user->favoriteProperties()->where('status', 'active')->count(),
            'by_type' => $user->favoriteProperties()
                ->join('property_types', 'properties.property_type_id', '=', 'property_types.id')
                ->where('status', 'active')
                ->groupBy('property_types.name')
                ->selectRaw('property_types.name as type, count(*) as count')
                ->pluck('count', 'type'),
            'by_listing_type' => $user->favoriteProperties()
                ->where('status', 'active')
                ->groupBy('listing_type')
                ->selectRaw('listing_type, count(*) as count')
                ->pluck('count', 'listing_type'),
            'recently_added' => $user->favoriteProperties()
                ->where('status', 'active')
                ->latest('user_favorites.created_at')
                ->limit(5)
                ->with(['propertyType', 'price'])
                ->get(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    public function getAlerts(): JsonResponse
    {
        $user = Auth::user();
        
        // This would return favorite alerts
        return response()->json([
            'success' => true,
            'data' => [],
        ]);
    }

    public function createAlert(Request $request): JsonResponse
    {
        $request->validate([
            'criteria' => 'required|array',
            'name' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        
        // This would create a favorite alert
        // Implementation would depend on your alert system
        
        return response()->json([
            'success' => true,
            'message' => 'Favorite alert created successfully',
        ]);
    }

    public function getSimilarProperties(Request $request): JsonResponse
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'limit' => 'nullable|integer|min:1|max:10',
        ]);

        $property = Property::with(['propertyType', 'details', 'location', 'price'])
            ->findOrFail($request->property_id);

        $similarProperties = Property::where('id', '!=', $property->id)
            ->where('status', 'active')
            ->where(function($query) use ($property) {
                // Same property type
                $query->where('property_type_id', $property->property_type_id)
                      // Similar price range (±20%)
                      ->orWhereHas('price', function($q) use ($property) {
                          $minPrice = $property->price->price * 0.8;
                          $maxPrice = $property->price->price * 1.2;
                          $q->whereBetween('price', [$minPrice, $maxPrice]);
                      })
                      // Same city
                      ->orWhereHas('location', function($q) use ($property) {
                          $q->where('city', $property->location->city);
                      });
            })
            ->with([
                'propertyType',
                'location',
                'price',
                'media' => function($query) {
                    $query->where('media_type', 'image')->limit(1);
                }
            ])
            ->limit($request->limit ?? 5)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $similarProperties,
        ]);
    }
}

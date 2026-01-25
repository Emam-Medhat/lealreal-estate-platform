<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePropertyRequest;
use App\Http\Requests\UpdatePropertyRequest;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\PropertyDetail;
use App\Models\PropertyLocation;
use App\Models\PropertyMedia;
use App\Models\PropertyPrice;
use App\Models\PropertyPriceHistory;
use App\Models\PropertyAmenity;
use App\Models\PropertyFeature;
use App\Models\PropertyDocument;
use App\Models\PropertyVirtualTour;
use App\Models\PropertyFloorPlan;
use App\Models\PropertyNeighborhood;
use App\Models\PropertyView;
use App\Models\PropertyAnalytic;
use App\Models\PropertyStatusHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PropertyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show', 'search']);
    }

    public function index(Request $request)
    {
        $query = Property::with([
            'agent.profile',
            'location',
            'propertyType',
            'media' => function ($query) {
                $query->where('media_type', 'image')->orderBy('sort_order');
            },
            'propertyAmenities',
            'features'
        ]);

        // Apply filters
        if ($request->property_type) {
            $query->whereHas('propertyType', function ($q) use ($request) {
                $q->where('slug', $request->property_type);
            });
        }

        if ($request->listing_type) {
            $query->where('listing_type', $request->listing_type);
        }

        if ($request->min_price) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->max_price) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->city) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }

        if ($request->bedrooms) {
            $query->where('bedrooms', '>=', $request->bedrooms);
        }

        if ($request->featured) {
            $query->where('featured', true);
        }

        if ($request->premium) {
            $query->where('premium', true);
        }

        // Sort
        $sort = $request->sort ?? 'created_at';
        $order = $request->order ?? 'desc';

        switch ($sort) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'area':
                $query->orderBy('area', 'desc');
                break;
            case 'views':
                $query->orderBy('views_count', 'desc');
                break;
            default:
                $query->orderBy($sort, $order);
        }

        $properties = $query->paginate(12);
        $propertyTypes = PropertyType::active()->ordered()->get();

        return view('properties.index', compact('properties', 'propertyTypes'));
    }

    public function show(Property $property)
    {
        $property->load([
            'propertyType',
            'media' => function ($query) {
                $query->orderBy('sort_order');
            },
            'propertyAmenities.amenity',
            'features',
            'agent'
        ]);

        // Increment views
        $property->incrementViews();

        // Get similar properties
        $similarProperties = Property::where('id', '!=', $property->id)
            ->where('property_type', $property->property_type)
            ->where('status', 'active')
            ->with([
                'media' => function ($query) {
                    $query->where('media_type', 'image')->limit(1);
                }
            ])
            ->limit(6)
            ->get();

        return view('properties.show', compact('property', 'similarProperties'));
    }

    public function create()
    {
        $propertyTypes = PropertyType::active()->ordered()->get();
        $amenities = PropertyAmenity::active()->ordered()->get();
        $features = PropertyFeature::active()->ordered()->get();

        return view('properties.create', compact('propertyTypes', 'amenities', 'features'));
    }

    public function store(StorePropertyRequest $request)
    {
        try {
            DB::beginTransaction();

            $user = Auth::user();

            // Create property
            $property = Property::create([
                'agent_id' => $user->id,
                'property_type' => $request->property_type,
                'title' => $request->title,
                'description' => $request->description,
                'listing_type' => $request->listing_type,
                'status' => $request->status ?? 'draft',
                'featured' => $request->featured ?? false,
                'premium' => $request->premium ?? false,
                'property_code' => $this->generatePropertyCode(),
                'views_count' => 0,
                'favorites_count' => 0,
                'inquiries_count' => 0,
                'price' => $request->price,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'country' => $request->country,
                'postal_code' => $request->postal_code,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ]);

            // Create property details
            PropertyDetail::create([
                'property_id' => $property->id,
                'bedrooms' => $request->bedrooms,
                'bathrooms' => $request->bathrooms,
                'floors' => $request->floors,
                'parking_spaces' => $request->parking_spaces,
                'year_built' => $request->year_built,
                'area' => $request->area,
                'area_unit' => $request->area_unit,
                'land_area' => $request->land_area,
                'land_area_unit' => $request->land_area_unit,
                'specifications' => $request->specifications,
                'materials' => $request->materials,
                'interior_features' => $request->interior_features,
                'exterior_features' => $request->exterior_features,
            ]);

            // Create property location
            PropertyLocation::create([
                'property_id' => $property->id,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'country' => $request->country,
                'postal_code' => $request->postal_code,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'neighborhood' => $request->neighborhood,
                'district' => $request->district,
                'coordinates' => $request->coordinates,
                'nearby_landmarks' => $request->nearby_landmarks,
                'transportation' => $request->transportation,
            ]);

            // Create property price
            PropertyPrice::create([
                'property_id' => $property->id,
                'price' => $request->price,
                'currency' => $request->currency,
                'price_type' => $request->listing_type,
                'price_per_sqm' => $request->area > 0 ? $request->price / $request->area : null,
                'is_negotiable' => $request->is_negotiable ?? false,
                'includes_vat' => $request->includes_vat ?? false,
                'vat_rate' => $request->vat_rate ?? 0,
                'service_charges' => $request->service_charges,
                'maintenance_fees' => $request->maintenance_fees,
                'payment_frequency' => $request->payment_frequency,
                'payment_terms' => $request->payment_terms,
                'effective_date' => now(),
                'is_active' => true,
            ]);

            // Attach amenities
            if ($request->amenities) {
                $property->amenities()->attach($request->amenities);
            }

            // Attach features
            if ($request->features) {
                $property->features()->attach($request->features);
            }

            // Upload images
            if ($request->hasFile('images')) {
                $this->uploadImages($property, $request->file('images'));
            }

            // Upload documents
            if ($request->hasFile('documents')) {
                $this->uploadDocuments($property, $request->file('documents'));
            }

            DB::commit();

            return redirect()
                ->route('properties.show', $property)
                ->with('success', 'Property created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Failed to create property: ' . $e->getMessage());
        }
    }

    public function edit(Property $property)
    {
        $this->authorize('update', $property);

        $property->load([
            'details',
            'location',
            'price',
            'amenities',
            'features',
            'documents',
            'virtualTours',
            'floorPlans',
            'neighborhoods'
        ]);

        $propertyTypes = PropertyType::active()->ordered()->get();
        $amenities = PropertyAmenity::active()->ordered()->get();
        $features = PropertyFeature::active()->ordered()->get();

        return view('properties.edit', compact('property', 'propertyTypes', 'amenities', 'features'));
    }

    public function update(UpdatePropertyRequest $request, Property $property)
    {
        $this->authorize('update', $property);

        try {
            DB::beginTransaction();

            $oldStatus = $property->status;

            // Update property
            $property->update([
                'property_type' => $request->property_type,
                'title' => $request->title,
                'description' => $request->description,
                'listing_type' => $request->listing_type,
                'status' => $request->status,
                'featured' => $request->featured ?? false,
                'premium' => $request->premium ?? false,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'country' => $request->country,
                'postal_code' => $request->postal_code,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ]);

            // Update details
            $property->details->update([
                'bedrooms' => $request->bedrooms,
                'bathrooms' => $request->bathrooms,
                'floors' => $request->floors,
                'parking_spaces' => $request->parking_spaces,
                'year_built' => $request->year_built,
                'area' => $request->area,
                'area_unit' => $request->area_unit,
                'land_area' => $request->land_area,
                'land_area_unit' => $request->land_area_unit,
                'specifications' => $request->specifications,
                'materials' => $request->materials,
                'interior_features' => $request->interior_features,
                'exterior_features' => $request->exterior_features,
            ]);

            // Update location
            $property->location->update([
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'country' => $request->country,
                'postal_code' => $request->postal_code,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'neighborhood' => $request->neighborhood,
                'district' => $request->district,
                'coordinates' => $request->coordinates,
                'nearby_landmarks' => $request->nearby_landmarks,
                'transportation' => $request->transportation,
            ]);

            // Update price
            // Save to main table as well
            $property->update(['price' => $request->price]);

            $propertyPrice = $property->price()->first();

            if ($propertyPrice) {
                $oldPrice = $propertyPrice->price;
                $newPrice = $request->price;

                $propertyPrice->update([
                    'price' => $newPrice,
                    'currency' => $request->currency,
                    'price_type' => $request->listing_type,
                    'price_per_sqm' => $request->area > 0 ? $newPrice / $request->area : null,
                    'is_negotiable' => $request->is_negotiable ?? false,
                    'includes_vat' => $request->includes_vat ?? false,
                    'vat_rate' => $request->vat_rate ?? 0,
                    'service_charges' => $request->service_charges,
                    'maintenance_fees' => $request->maintenance_fees,
                    'payment_frequency' => $request->payment_frequency,
                    'payment_terms' => $request->payment_terms,
                ]);

                // Record price history if changed
                if ($oldPrice != $newPrice) {
                    $changeType = $newPrice > $oldPrice ? 'increase' : 'decrease';
                    $changePercentage = abs(($newPrice - $oldPrice) / $oldPrice * 100);

                    PropertyPriceHistory::create([
                        'property_id' => $property->id,
                        'old_price' => $oldPrice,
                        'new_price' => $newPrice,
                        'currency' => $request->currency,
                        'change_reason' => $request->price_change_reason,
                        'change_type' => $changeType,
                        'change_percentage' => $changePercentage,
                        'changed_by' => Auth::id(),
                    ]);
                }
            } else {
                // Create price if it doesn't exist
                PropertyPrice::create([
                    'property_id' => $property->id,
                    'price' => $request->price,
                    'currency' => $request->currency,
                    'price_type' => $request->listing_type,
                    'price_per_sqm' => $request->area > 0 ? $request->price / $request->area : null,
                    'is_negotiable' => $request->is_negotiable ?? false,
                    'includes_vat' => $request->includes_vat ?? false,
                    'vat_rate' => $request->vat_rate ?? 0,
                    'service_charges' => $request->service_charges,
                    'maintenance_fees' => $request->maintenance_fees,
                    'payment_frequency' => $request->payment_frequency,
                    'payment_terms' => $request->payment_terms,
                    'effective_date' => now()->toDateString(),
                    'is_active' => true,
                ]);
            }

            // Sync amenities
            $property->amenities()->sync($request->amenities ?? []);

            // Sync features
            $property->features()->sync($request->features ?? []);

            // Upload new images
            if ($request->hasFile('images')) {
                $this->uploadImages($property, $request->file('images'));
            }

            // Upload new documents
            if ($request->hasFile('documents')) {
                $this->uploadDocuments($property, $request->file('documents'));
            }

            // Record status change if changed
            if ($oldStatus != $request->status) {
                PropertyStatusHistory::recordStatusChange(
                    $property->id,
                    $oldStatus,
                    $request->status,
                    $request->status_change_reason,
                    Auth::id()
                );
            }

            DB::commit();

            return redirect()
                ->route('properties.show', $property)
                ->with('success', 'Property updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Failed to update property: ' . $e->getMessage());
        }
    }

    public function destroy(Property $property)
    {
        $this->authorize('delete', $property);

        try {
            DB::beginTransaction();

            // Soft delete property (cascade will handle related records)
            $property->delete();

            DB::commit();

            return redirect()
                ->route('properties.index')
                ->with('success', 'Property deleted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->with('error', 'Failed to delete property: ' . $e->getMessage());
        }
    }

    private function uploadImages(Property $property, $images)
    {
        foreach ($images as $index => $image) {
            $path = $image->store('properties/images', 'public');

            PropertyMedia::create([
                'property_id' => $property->id,
                'media_type' => 'image',
                'file_name' => $image->getClientOriginalName(),
                'file_path' => $path,
                'file_size' => $image->getSize(),
                'mime_type' => $image->getMimeType(),
                'width' => getimagesize($image)[0] ?? null,
                'height' => getimagesize($image)[1] ?? null,
                'is_primary' => $index === 0, // First image is primary
                'sort_order' => $index,
                'uploaded_by' => Auth::id(),
            ]);
        }
    }

    private function uploadDocuments(Property $property, $documents)
    {
        foreach ($documents as $document) {
            $path = $document->store('properties/documents', 'public');

            PropertyDocument::create([
                'property_id' => $property->id,
                'title' => $document->getClientOriginalName(),
                'file_name' => $document->getClientOriginalName(),
                'file_path' => $path,
                'file_type' => pathinfo($document->getClientOriginalName(), PATHINFO_EXTENSION),
                'file_size' => $document->getSize(),
                'mime_type' => $document->getMimeType(),
            ]);
        }
    }

    private function recordView(Property $property)
    {
        PropertyView::create([
            'property_id' => $property->id,
            'session_id' => session()->getId(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'user_id' => Auth::id(),
            'referrer' => request()->header('referer'),
            'view_type' => 'detail',
        ]);

        // Increment view count
        $property->increment('views_count');

        // Record analytics
        PropertyAnalytic::recordMetric($property->id, 'views');
    }

    private function generatePropertyCode(): string
    {
        do {
            $code = 'PROP-' . strtoupper(Str::random(8));
        } while (Property::where('property_code', $code)->exists());

        return $code;
    }
}

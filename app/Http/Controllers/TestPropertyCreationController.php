<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\PropertyMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class TestPropertyCreationController extends Controller
{
    /**
     * Show test property creation page
     */
    public function create()
    {
        return view('test_property_creation');
    }

    /**
     * Handle property creation with images
     */
    public function store(Request $request)
    {
        try {
            Log::info('Starting property creation test');
            Log::info('Request data: ' . json_encode($request->all()));
            
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'property_type_id' => 'required|exists:property_types,id',
                'listing_type' => 'required|in:sale,rent',
                'price' => 'required|numeric|min:0',
                'currency' => 'required|string|size:3',
                'address' => 'required|string|max:500',
                'bedrooms' => 'nullable|integer|min:0',
                'bathrooms' => 'nullable|integer|min:0',
                'area' => 'nullable|numeric|min:0',
                'area_unit' => 'nullable|string|in:sq_m,sq_ft,acre,hectare',
                'description' => 'nullable|string',
                'images.*' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:10240',
            ]);

            Log::info('Validation passed');

            // Create property
            $property = Property::create([
                'title' => $validated['title'],
                'property_type' => $validated['property_type_id'],
                'listing_type' => $validated['listing_type'],
                'price' => $validated['price'],
                'currency' => $validated['currency'],
                'address' => $validated['address'],
                'bedrooms' => $validated['bedrooms'],
                'bathrooms' => $validated['bathrooms'],
                'area' => $validated['area'],
                'area_unit' => $validated['area_unit'],
                'description' => $validated['description'],
                'agent_id' => auth()->id() ?? 4,
                'status' => 'active',
            ]);

            Log::info('Property created with ID: ' . $property->id);

            // Handle images
            if ($request->hasFile('images')) {
                Log::info('Processing images...');
                $images = $request->file('images');
                $hasImages = PropertyMedia::where('property_id', $property->id)
                    ->where('media_type', 'image')
                    ->exists();

                foreach ($images as $index => $image) {
                    Log::info('Processing image ' . $index . ': ' . $image->getClientOriginalName());
                    
                    $path = $image->store('properties/' . $property->id, 'public');
                    Log::info('Image stored at: ' . $path);

                    $media = PropertyMedia::create([
                        'property_id' => $property->id,
                        'media_type' => 'image',
                        'file_path' => $path,
                        'file_name' => $image->getClientOriginalName(),
                        'file_size' => $image->getSize(),
                        'file_type' => $image->getMimeType(),
                        'sort_order' => $index,
                        'is_featured' => !$hasImages && $index === 0,
                        'is_primary' => !$hasImages && $index === 0,
                        'uploaded_by' => auth()->id() ?? null
                    ]);

                    Log::info('Media record created with ID: ' . $media->id);
                }

                Log::info('All images processed');
            }

            return redirect()
                ->route('optimized.properties.show', $property->id)
                ->with('success', 'تم إنشاء العقار "' . $property->title . '" مع ' . ($request->hasFile('images') ? count($request->file('images')) : 0) . ' صور بنجاح!');

        } catch (\Exception $e) {
            Log::error('Property creation error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return redirect()
                ->route('test.property.create')
                ->with('error', 'خطأ في إنشاء العقار: ' . $e->getMessage());
        }
    }
}

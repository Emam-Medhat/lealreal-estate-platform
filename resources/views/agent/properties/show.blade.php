@extends('layouts.app')

@section('title', 'Property Details')

@section('content')
<div class="container mx-auto px-6 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $property->title }}</h1>
                <p class="text-gray-600 mt-2">Property Code: {{ $property->property_code }}</p>
            </div>
            <div class="flex space-x-4">
                <a href="{{ route('agent.properties.edit', $property) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-edit mr-2"></i>
                    Edit Property
                </a>
                <a href="{{ route('agent.properties.add-photos', $property) }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                    <i class="fas fa-camera mr-2"></i>
                    Add Photos
                </a>
                <a href="{{ route('agent.properties.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Properties
                </a>
            </div>
        </div>
    </div>

    <!-- Property Details -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6 border-b pb-3">Property Information</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-gray-600">Property Type</p>
                        <p class="font-medium">{{ ucfirst($property->property_type) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Listing Type</p>
                        <p class="font-medium">{{ ucfirst($property->listing_type) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Price</p>
                        <p class="font-medium text-2xl text-blue-600">{{ $property->price?->formatted_price ?? number_format($property->price ?? 0, 2) . ' ' . ($property->currency ?? 'USD') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Area</p>
                        <p class="font-medium">{{ $property->details?->formatted_area ?? number_format($property->area, 2) . ' ' . $property->area_unit }}</p>
                    </div>
                    @if($property->bedrooms)
                    <div>
                        <p class="text-sm text-gray-600">Bedrooms</p>
                        <p class="font-medium">{{ $property->bedrooms }}</p>
                    </div>
                    @endif
                    @if($property->bathrooms)
                    <div>
                        <p class="text-sm text-gray-600">Bathrooms</p>
                        <p class="font-medium">{{ $property->bathrooms }}</p>
                    </div>
                    @endif
                    @if($property->year_built)
                    <div>
                        <p class="text-sm text-gray-600">Year Built</p>
                        <p class="font-medium">{{ $property->year_built }}</p>
                    </div>
                    @endif
                    <div>
                        <p class="text-sm text-gray-600">Status</p>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                            @if($property->status == 'active') bg-green-100 text-green-800
                            @elseif($property->status == 'draft') bg-gray-100 text-gray-800
                            @elseif($property->status == 'sold') bg-red-100 text-red-800
                            @elseif($property->status == 'rented') bg-blue-100 text-blue-800
                            @else bg-yellow-100 text-yellow-800
                            @endif">
                            {{ ucfirst($property->status) }}
                        </span>
                    </div>
                </div>

                <div class="mt-6">
                    <p class="text-sm text-gray-600 mb-2">Description</p>
                    <p class="text-gray-700">{{ $property->description }}</p>
                </div>
            </div>

            <!-- Location Information -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6 border-b pb-3">Location</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <p class="text-sm text-gray-600">Address</p>
                        <p class="font-medium">{{ $property->full_address }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">City</p>
                        <p class="font-medium">{{ $property->location?->city ?? $property->city }}</p>
                    </div>
                    @if($property->state)
                    <div>
                        <p class="text-sm text-gray-600">State</p>
                        <p class="font-medium">{{ $property->location?->state ?? $property->state }}</p>
                    </div>
                    @endif
                    <div>
                        <p class="text-sm text-gray-600">Country</p>
                        <p class="font-medium">{{ $property->location?->country ?? $property->country }}</p>
                    </div>
                    @if($property->postal_code)
                    <div>
                        <p class="text-sm text-gray-600">Postal Code</p>
                        <p class="font-medium">{{ $property->location?->postal_code ?? $property->postal_code }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Amenities -->
            @if($property->amenities && count($property->amenities) > 0)
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6 border-b pb-3">Amenities</h2>
                
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    @foreach($property->amenities as $amenity)
                    <div class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-2"></i>
                        <span>{{ $amenity }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Nearby Places -->
            @if($property->nearby_places && count($property->nearby_places) > 0)
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6 border-b pb-3">Nearby Places</h2>
                
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    @foreach($property->nearby_places as $place)
                    <div class="flex items-center">
                        <i class="fas fa-map-marker-alt text-blue-500 mr-2"></i>
                        <span>{{ $place }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Media/Images -->
            @if($property->media && $property->media->count() > 0)
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6 border-b pb-3">Property Images</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($property->images as $image)
                    <div class="relative">
                        <img src="{{ asset('storage/' . $image->file_path) }}" 
                             alt="{{ $image?->file_name ?? 'Property Image' }}" 
                             class="w-full h-48 object-cover rounded-lg">
                        @if($image->is_primary)
                        <span class="absolute top-2 right-2 bg-blue-600 text-white px-2 py-1 text-xs rounded">
                            Primary
                        </span>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Quick Stats -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistics</h3>
                
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Views</span>
                        <span class="font-medium">{{ $property->views_count }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Inquiries</span>
                        <span class="font-medium">{{ $property->inquiries_count }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Favorites</span>
                        <span class="font-medium">{{ $property->favorites_count }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Days on Market</span>
                        <span class="font-medium">{{ $property->days_on_market }}</span>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions</h3>
                
                <div class="space-y-3">
                    @if($property->status == 'draft')
                    <form action="{{ route('agent.properties.publish', $property) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                            <i class="fas fa-check mr-2"></i>
                            Publish Property
                        </button>
                    </form>
                    @endif
                    
                    @if($property->status == 'active')
                    <form action="{{ route('agent.properties.archive', $property) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="w-full bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition">
                            <i class="fas fa-archive mr-2"></i>
                            Archive Property
                        </button>
                    </form>
                    @endif
                    
                    <form action="{{ route('agent.properties.duplicate', $property) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="w-full bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition">
                            <i class="fas fa-copy mr-2"></i>
                            Duplicate Property
                        </button>
                    </form>
                    
                    <form action="{{ route('agent.properties.destroy', $property) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this property?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition">
                            <i class="fas fa-trash mr-2"></i>
                            Delete Property
                        </button>
                    </form>
                </div>
            </div>

            <!-- Featured/Premium Status -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Property Status</h3>
                
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Featured</span>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                            @if($property->featured) bg-yellow-100 text-yellow-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ $property->featured ? 'Yes' : 'No' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Premium</span>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                            @if($property->premium) bg-purple-100 text-purple-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ $property->premium ? 'Yes' : 'No' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

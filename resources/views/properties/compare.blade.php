@extends('layouts.app')

@section('title', 'Compare Properties')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Compare Properties</h1>
                    <p class="text-gray-600">Side-by-side comparison of selected properties</p>
                </div>
                <div class="flex items-center space-x-3">
                    <button onclick="clearComparison()" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                        <i class="fas fa-times mr-2"></i>
                        Clear All
                    </button>
                    <a href="{{ route('optimized.properties.index') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Add More
                    </a>
                </div>
            </div>
        </div>

        @forelse ($properties as $property)
            <!-- Comparison Table -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">
                                    Feature
                                </th>
                                @foreach ($properties as $prop)
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <div class="relative">
                                            {{ Str::limit($prop->title, 20) }}
                                            <button onclick="removeFromComparison({{ $prop->id }})" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 text-xs hover:bg-red-600">
                                                ×
                                            </button>
                                        </div>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <!-- Images -->
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    Image
                                </td>
                                @foreach ($properties as $prop)
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="h-32 w-full bg-gray-200 rounded-lg overflow-hidden">
                                            @if($prop->images->isNotEmpty())
                                                <img src="{{ $prop->images->first()->url }}" alt="{{ $prop->title }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                                    <i class="fas fa-home text-gray-400 text-2xl"></i>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                @endforeach
                            </tr>

                            <!-- Price -->
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    Price
                                </td>
                                @foreach ($properties as $prop)
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-lg font-bold text-gray-800">
                                            ${{ number_format($prop->price, 0) }}
                                        </div>
                                        @if($prop->status === 'for_rent')
                                            <div class="text-sm text-gray-600">/month</div>
                                        @endif
                                        @if($prop->price_per_sqft)
                                            <div class="text-sm text-gray-500">${{ number_format($prop->price_per_sqft, 0) }}/sqft</div>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>

                            <!-- Basic Info -->
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    Beds / Baths
                                </td>
                                @foreach ($properties as $prop)
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div class="flex space-x-4">
                                            <span><i class="fas fa-bed mr-1"></i>{{ $prop->bedrooms }}</span>
                                            <span><i class="fas fa-bath mr-1"></i>{{ $prop->bathrooms }}</span>
                                        </div>
                                    </td>
                                @endforeach
                            </tr>

                            <!-- Square Feet -->
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    Square Feet
                                </td>
                                @foreach ($properties as $prop)
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ number_format($prop->square_feet) }} sqft
                                    </td>
                                @endforeach
                            </tr>

                            <!-- Property Type -->
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    Property Type
                                </td>
                                @foreach ($properties as $prop)
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $prop->propertyType->name ?? 'N/A' }}
                                    </td>
                                @endforeach
                            </tr>

                            <!-- Year Built -->
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    Year Built
                                </td>
                                @foreach ($properties as $prop)
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $prop->year_built ?? 'N/A' }}
                                    </td>
                                @endforeach
                            </tr>

                            <!-- Location -->
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    Location
                                </td>
                                @foreach ($properties as $prop)
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div>
                                            <div>{{ $prop->address }}</div>
                                            <div class="text-gray-500">{{ $prop->city }}, {{ $prop->state }}</div>
                                        </div>
                                    </td>
                                @endforeach
                            </tr>

                            <!-- Description -->
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    Description
                                </td>
                                @foreach ($properties as $prop)
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        {{ Str::limit($prop->description, 150) }}
                                    </td>
                                @endforeach
                            </tr>

                            <!-- Features -->
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    Key Features
                                </td>
                                @foreach ($properties as $prop)
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        <div class="flex flex-wrap gap-1">
                                            @foreach ($prop->features->take(5) as $feature)
                                                <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs">
                                                    {{ $feature->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </td>
                                @endforeach
                            </tr>

                            <!-- Amenities -->
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    Amenities
                                </td>
                                @foreach ($properties as $prop)
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        <div class="flex flex-wrap gap-1">
                                            @foreach ($prop->amenities->take(5) as $amenity)
                                                <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs">
                                                    {{ $amenity->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </td>
                                @endforeach
                            </tr>

                            <!-- Parking -->
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    Parking
                                </td>
                                @foreach ($properties as $prop)
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $prop->parking_spaces ?? $prop->parking_type ?? 'N/A' }}
                                    </td>
                                @endforeach
                            </tr>

                            <!-- HOA Fees -->
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    HOA Fees
                                </td>
                                @foreach ($properties as $prop)
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @if($prop->hoa_fees)
                                            ${{ number_format($prop->hoa_fees, 0) }}/month
                                        @else
                                            None
                                        @endif
                                    </td>
                                @endforeach
                            </tr>

                            <!-- Status -->
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    Status
                                </td>
                                @foreach ($properties as $prop)
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                            @if($prop->status === 'for_sale')
                                                bg-green-100 text-green-800
                                            @elseif($prop->status === 'for_rent')
                                                bg-blue-100 text-blue-800
                                            @elseif($prop->status === 'sold')
                                                bg-red-100 text-red-800
                                            @else
                                                bg-gray-100 text-gray-800
                                            @endif
                                        ">
                                            {{ ucfirst(str_replace('_', ' ', $prop->status)) }}
                                        </span>
                                    </td>
                                @endforeach
                            </tr>

                            <!-- Actions -->
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    Actions
                                </td>
                                @foreach ($properties as $prop)
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('properties.show', $prop) }}" class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700">
                                                View
                                            </a>
                                            <button onclick="addToFavorites({{ $prop->id }})" class="border border-gray-300 text-gray-700 px-3 py-1 rounded text-sm hover:bg-gray-50">
                                                Save
                                            </button>
                                        </div>
                                    </td>
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                <i class="fas fa-balance-scale text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No Properties to Compare</h3>
                <p class="text-gray-500 mb-6">Select at least 2 properties to compare them side by side.</p>
                <a href="{{ route('optimized.properties.index') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                    Browse Properties
                </a>
            </div>
        @endforelse

        <!-- Add More Properties -->
        @if($properties->isNotEmpty() && $properties->count() < 4)
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Add More Properties</h3>
                <p class="text-gray-600 mb-4">You can compare up to 4 properties at once.</p>
                <a href="{{ route('optimized.properties.index') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>
                    Add Property to Compare
                </a>
            </div>
        @endif
    </div>
</div>

<script>
function removeFromComparison(propertyId) {
    if (!confirm('Remove this property from comparison?')) {
        return;
    }
    
    const url = new URL(window.location);
    const currentIds = url.searchParams.get('properties')?.split(',') || [];
    const newIds = currentIds.filter(id => id != propertyId);
    
    if (newIds.length > 0) {
        url.searchParams.set('properties', newIds.join(','));
    } else {
        url.searchParams.delete('properties');
    }
    
    window.location.href = url.toString();
}

function clearComparison() {
    if (!confirm('Clear all properties from comparison?')) {
        return;
    }
    
    window.location.href = '{{ route('properties.compare') }}';
}

function addToFavorites(propertyId) {
    fetch('/properties/' + propertyId + '/favorite', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Property added to favorites!');
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}
</script>
@endsection

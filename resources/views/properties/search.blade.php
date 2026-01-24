@extends('layouts.app')

@section('title', 'Property Search')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Property Search</h1>
                    <p class="text-gray-600">Find your perfect property from our extensive collection</p>
                </div>
                <div class="flex items-center space-x-3">
                    <button onclick="toggleAdvancedSearch()" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                        <i class="fas fa-sliders-h mr-2"></i>
                        Advanced Search
                    </button>
                    <a href="{{ route('properties.map') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-map mr-2"></i>
                        Map View
                    </a>
                </div>
            </div>
        </div>

        <!-- Search Form -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <form action="{{ route('properties.search') }}" method="GET" class="space-y-4">
                <!-- Main Search Row -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                        <input type="text" name="location" value="{{ request('location') }}" placeholder="City, neighborhood, or address" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Property Type</label>
                        <select name="property_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">All Types</option>
                            @foreach ($propertyTypes as $type)
                                <option value="{{ $type->id }}" {{ request('property_type') == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Price Range</label>
                        <select name="price_range" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Any Price</option>
                            <option value="0-100000" {{ request('price_range') === '0-100000' ? 'selected' : '' }}>Under $100K</option>
                            <option value="100000-250000" {{ request('price_range') === '100000-250000' ? 'selected' : '' }}>$100K - $250K</option>
                            <option value="250000-500000" {{ request('price_range') === '250000-500000' ? 'selected' : '' }}>$250K - $500K</option>
                            <option value="500000-1000000" {{ request('price_range') === '500000-1000000' ? 'selected' : '' }}>$500K - $1M</option>
                            <option value="1000000+" {{ request('price_range') === '1000000+' ? 'selected' : '' }}>Over $1M</option>
                        </select>
                    </div>
                </div>

                <!-- Advanced Search (Hidden by default) -->
                <div id="advancedSearch" class="hidden space-y-4 border-t pt-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Bedrooms</label>
                            <select name="bedrooms" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Any</option>
                                <option value="1" {{ request('bedrooms') === '1' ? 'selected' : '' }}>1+</option>
                                <option value="2" {{ request('bedrooms') === '2' ? 'selected' : '' }}>2+</option>
                                <option value="3" {{ request('bedrooms') === '3' ? 'selected' : '' }}>3+</option>
                                <option value="4" {{ request('bedrooms') === '4' ? 'selected' : '' }}>4+</option>
                                <option value="5" {{ request('bedrooms') === '5' ? 'selected' : '' }}>5+</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Bathrooms</label>
                            <select name="bathrooms" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Any</option>
                                <option value="1" {{ request('bathrooms') === '1' ? 'selected' : '' }}>1+</option>
                                <option value="2" {{ request('bathrooms') === '2' ? 'selected' : '' }}>2+</option>
                                <option value="3" {{ request('bathrooms') === '3' ? 'selected' : '' }}>3+</option>
                                <option value="4" {{ request('bathrooms') === '4' ? 'selected' : '' }}>4+</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Square Feet</label>
                            <select name="square_feet" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Any</option>
                                <option value="0-1000" {{ request('square_feet') === '0-1000' ? 'selected' : '' }}>Under 1,000</option>
                                <option value="1000-1500" {{ request('square_feet') === '1000-1500' ? 'selected' : '' }}>1,000 - 1,500</option>
                                <option value="1500-2500" {{ request('square_feet') === '1500-2500' ? 'selected' : '' }}>1,500 - 2,500</option>
                                <option value="2500-3500" {{ request('square_feet') === '2500-3500' ? 'selected' : '' }}>2,500 - 3,500</option>
                                <option value="3500+" {{ request('square_feet') === '3500+' ? 'selected' : '' }}>Over 3,500</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Keywords</label>
                            <input type="text" name="keywords" value="{{ request('keywords') }}" placeholder="Pool, garage, waterfront..." 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Property Status</label>
                            <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Any Status</option>
                                <option value="for_sale" {{ request('status') === 'for_sale' ? 'selected' : '' }}>For Sale</option>
                                <option value="for_rent" {{ request('status') === 'for_rent' ? 'selected' : '' }}>For Rent</option>
                                <option value="sold" {{ request('status') === 'sold' ? 'selected' : '' }}>Sold</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-6">
                        <label class="flex items-center">
                            <input type="checkbox" name="featured" {{ request('featured') ? 'checked' : '' }} class="mr-2">
                            <span class="text-sm text-gray-700">Featured only</span>
                        </label>
                        
                        <label class="flex items-center">
                            <input type="checkbox" name="open_house" {{ request('open_house') ? 'checked' : '' }} class="mr-2">
                            <span class="text-sm text-gray-700">Open house</span>
                        </label>
                        
                        <label class="flex items-center">
                            <input type="checkbox" name="new_construction" {{ request('new_construction') ? 'checked' : '' }} class="mr-2">
                            <span class="text-sm text-gray-700">New construction</span>
                        </label>
                        
                        <label class="flex items-center">
                            <input type="checkbox" name="price_reduced" {{ request('price_reduced') ? 'checked' : '' }} class="mr-2">
                            <span class="text-sm text-gray-700">Price reduced</span>
                        </label>
                    </div>
                </div>

                <!-- Search Actions -->
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-4">
                        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-search mr-2"></i>
                            Search Properties
                        </button>
                        <button type="button" onclick="clearFilters()" class="text-gray-600 hover:text-gray-800">
                            <i class="fas fa-times mr-2"></i>
                            Clear Filters
                        </button>
                    </div>
                    
                    <div class="flex items-center space-x-3">
                        <label class="text-sm text-gray-600">Sort by:</label>
                        <select name="sort" onchange="this.form.submit()" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                            <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Newest</option>
                            <option value="price_low" {{ request('sort') === 'price_low' ? 'selected' : '' }}>Price (Low to High)</option>
                            <option value="price_high" {{ request('sort') === 'price_high' ? 'selected' : '' }}>Price (High to Low)</option>
                            <option value="bedrooms" {{ request('sort') === 'bedrooms' ? 'selected' : '' }}>Bedrooms</option>
                            <option value="square_feet" {{ request('sort') === 'square_feet' ? 'selected' : '' }}>Square Feet</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>

        <!-- Search Results -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="p-6 border-b">
                <div class="flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-800">
                        Search Results 
                        @if($properties->hasPages())
                            ({{ $properties->firstItem() }}-{{ $properties->lastItem() }} of {{ $properties->total() }})
                        @else
                            ({{ $properties->count() }} properties)
                        @endif
                    </h2>
                    
                    <div class="flex items-center space-x-3">
                        <button onclick="toggleView('grid')" id="gridViewBtn" class="text-blue-600 hover:text-blue-800">
                            <i class="fas fa-th"></i>
                        </button>
                        <button onclick="toggleView('list')" id="listViewBtn" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-list"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Results Grid/List -->
            <div id="propertyResults" class="p-6">
                @forelse ($properties as $property)
                    <div class="property-item border rounded-lg overflow-hidden mb-6 hover:shadow-lg transition-shadow">
                        <div class="md:flex">
                            <!-- Property Image -->
                            <div class="md:w-1/3">
                                <div class="h-48 md:h-full bg-gray-200 relative">
                                    @if($property->images->isNotEmpty())
                                        <img src="{{ $property->images->first()->url }}" alt="{{ $property->title }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                            <i class="fas fa-home text-gray-400 text-4xl"></i>
                                        </div>
                                    @endif
                                    
                                    <!-- Status Badge -->
                                    @if($property->status)
                                        <div class="absolute top-2 left-2">
                                            <span class="bg-{{ $property->status === 'for_sale' ? 'green' : ($property->status === 'for_rent' ? 'blue' : 'gray') }}-500 text-white px-2 py-1 rounded text-xs">
                                                {{ ucfirst(str_replace('_', ' ', $property->status)) }}
                                            </span>
                                        </div>
                                    @endif
                                    
                                    <!-- Favorite Button -->
                                    <button onclick="toggleFavorite({{ $property->id }})" class="absolute top-2 right-2 bg-white rounded-full p-2 shadow-md hover:bg-gray-100">
                                        <i class="fas fa-heart text-gray-400 hover:text-red-500"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Property Details -->
                            <div class="md:w-2/3 p-6">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <h3 class="text-xl font-semibold text-gray-800 mb-2">
                                            <a href="{{ route('properties.show', $property) }}" class="hover:text-blue-600">
                                                {{ $property->title }}
                                            </a>
                                        </h3>
                                        <p class="text-gray-600 mb-2">{{ $property->address }}, {{ $property->city }}</p>
                                        <div class="flex items-center space-x-4 text-sm text-gray-500">
                                            <span><i class="fas fa-bed mr-1"></i>{{ $property->bedrooms }} beds</span>
                                            <span><i class="fas fa-bath mr-1"></i>{{ $property->bathrooms }} baths</span>
                                            <span><i class="fas fa-ruler-combined mr-1"></i>{{ number_format($property->square_feet) }} sqft</span>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-2xl font-bold text-gray-800">
                                            ${{ number_format($property->price, 0) }}
                                        </div>
                                        @if($property->status === 'for_rent')
                                            <div class="text-sm text-gray-600">/month</div>
                                        @endif
                                    </div>
                                </div>
                                
                                <!-- Property Description -->
                                <p class="text-gray-600 mb-4 line-clamp-2">
                                    {{ Str::limit($property->description, 150) }}
                                </p>
                                
                                <!-- Features -->
                                @if($property->features->isNotEmpty())
                                    <div class="flex flex-wrap gap-2 mb-4">
                                        @foreach ($property->features->take(3) as $feature)
                                            <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs">
                                                {{ $feature->name }}
                                            </span>
                                        @endforeach
                                        @if($property->features->count() > 3)
                                            <span class="text-gray-500 text-xs">+{{ $property->features->count() - 3 }} more</span>
                                        @endif
                                    </div>
                                @endif
                                
                                <!-- Actions -->
                                <div class="flex justify-between items-center">
                                    <div class="flex space-x-3">
                                        <a href="{{ route('properties.show', $property) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm">
                                            View Details
                                        </a>
                                        <button onclick="scheduleTour({{ $property->id }})" class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors text-sm">
                                            Schedule Tour
                                        </button>
                                    </div>
                                    <div class="flex space-x-2">
                                        <button onclick="shareProperty({{ $property->id }})" class="text-gray-600 hover:text-gray-800">
                                            <i class="fas fa-share-alt"></i>
                                        </button>
                                        <button onclick="compareProperty({{ $property->id }})" class="text-gray-600 hover:text-gray-800">
                                            <i class="fas fa-balance-scale"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12">
                        <i class="fas fa-search text-6xl text-gray-300 mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No properties found</h3>
                        <p class="text-gray-500 mb-6">Try adjusting your search criteria or browse all properties.</p>
                        <a href="{{ route('optimized.properties.index') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                            Browse All Properties
                        </a>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($properties->hasPages())
                <div class="bg-white px-4 py-3 border-t sm:px-6">
                    {{ $properties->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function toggleAdvancedSearch() {
    const advancedSearch = document.getElementById('advancedSearch');
    advancedSearch.classList.toggle('hidden');
}

function toggleView(view) {
    const gridBtn = document.getElementById('gridViewBtn');
    const listBtn = document.getElementById('listViewBtn');
    const results = document.getElementById('propertyResults');
    
    if (view === 'grid') {
        gridBtn.className = 'text-blue-600 hover:text-blue-800';
        listBtn.className = 'text-gray-400 hover:text-gray-600';
        results.className = 'p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6';
    } else {
        gridBtn.className = 'text-gray-400 hover:text-gray-600';
        listBtn.className = 'text-blue-600 hover:text-blue-800';
        results.className = 'p-6';
    }
}

function clearFilters() {
    window.location.href = '{{ route('properties.search') }}';
}

function toggleFavorite(propertyId) {
    fetch('/properties/' + propertyId + '/favorite', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        }
    })
    .then(response => response.json())
    .then(data => {
        // Update favorite button state
        event.target.classList.toggle('text-red-500');
        event.target.classList.toggle('text-gray-400');
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function scheduleTour(propertyId) {
    // Open tour scheduling modal or redirect
    window.location.href = '/properties/' + propertyId + '/tour';
}

function shareProperty(propertyId) {
    // Open share modal
    window.location.href = '/properties/' + propertyId + '/share';
}

function compareProperty(propertyId) {
    // Add to comparison
    window.location.href = '/properties/compare?add=' + propertyId;
}
</script>
@endsection

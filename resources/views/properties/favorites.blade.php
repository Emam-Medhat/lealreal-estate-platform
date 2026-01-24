@extends('layouts.app')

@section('title', 'Favorite Properties')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">My Favorite Properties</h1>
                    <p class="text-gray-600">Properties you've saved for later</p>
                </div>
                <div class="flex items-center space-x-3">
                    <button onclick="compareSelected()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-balance-scale mr-2"></i>
                        Compare Selected
                    </button>
                    <button onclick="shareFavorites()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-share-alt mr-2"></i>
                        Share List
                    </button>
                </div>
            </div>
        </div>

        <!-- Stats -->
        @if($favorites->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="bg-blue-100 rounded-full p-3 mr-4">
                            <i class="fas fa-heart text-blue-600"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Total Favorites</p>
                            <p class="text-2xl font-bold text-gray-800">{{ $favorites->count() }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="bg-green-100 rounded-full p-3 mr-4">
                            <i class="fas fa-dollar-sign text-green-600"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Avg. Price</p>
                            <p class="text-2xl font-bold text-gray-800">${{ number_format($favorites->avg('price'), 0) }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="bg-purple-100 rounded-full p-3 mr-4">
                            <i class="fas fa-bed text-purple-600"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Avg. Beds</p>
                            <p class="text-2xl font-bold text-gray-800">{{ number_format($favorites->avg('bedrooms'), 1) }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="bg-yellow-100 rounded-full p-3 mr-4">
                            <i class="fas fa-ruler-combined text-yellow-600"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Avg. Sqft</p>
                            <p class="text-2xl font-bold text-gray-800">{{ number_format($favorites->avg('square_feet'), 0) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Filter and Sort -->
        @if($favorites->isNotEmpty())
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-4">
                        <label class="text-sm text-gray-600">Filter by:</label>
                        <select onchange="filterFavorites(this.value)" class="px-3 py-2 border rounded-lg text-sm">
                            <option value="">All Properties</option>
                            <option value="for_sale">For Sale</option>
                            <option value="for_rent">For Rent</option>
                            <option value="price_low">Price (Low to High)</option>
                            <option value="price_high">Price (High to Low)</option>
                            <option value="newest">Newest First</option>
                            <option value="oldest">Oldest First</option>
                        </select>
                    </div>
                    
                    <div class="flex items-center space-x-3">
                        <button onclick="selectAll()" class="text-sm text-gray-600 hover:text-gray-800">
                            <i class="fas fa-check-square mr-1"></i>
                            Select All
                        </button>
                        <button onclick="deselectAll()" class="text-sm text-gray-600 hover:text-gray-800">
                            <i class="fas fa-square mr-1"></i>
                            Deselect All
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <!-- Favorites Grid -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="p-6">
                @forelse ($favorites as $favorite)
                    <div class="border rounded-lg overflow-hidden mb-6 hover:shadow-lg transition-shadow">
                        <div class="md:flex">
                            <!-- Checkbox -->
                            <div class="md:w-1/12 flex items-center justify-center p-4">
                                <input type="checkbox" class="property-checkbox" value="{{ $favorite->property->id }}" 
                                    class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500">
                            </div>
                            
                            <!-- Property Image -->
                            <div class="md:w-2/6">
                                <div class="h-48 md:h-full bg-gray-200 relative">
                                    @if($favorite->property->images->isNotEmpty())
                                        <img src="{{ $favorite->property->images->first()->url }}" alt="{{ $favorite->property->title }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                            <i class="fas fa-home text-gray-400 text-4xl"></i>
                                        </div>
                                    @endif
                                    
                                    <!-- Status Badge -->
                                    @if($favorite->property->status)
                                        <div class="absolute top-2 left-2">
                                            <span class="bg-{{ $favorite->property->status === 'for_sale' ? 'green' : ($favorite->property->status === 'for_rent' ? 'blue' : 'gray') }}-500 text-white px-2 py-1 rounded text-xs">
                                                {{ ucfirst(str_replace('_', ' ', $favorite->property->status)) }}
                                            </span>
                                        </div>
                                    @endif
                                    
                                    <!-- Remove Favorite Button -->
                                    <button onclick="removeFavorite({{ $favorite->id }})" class="absolute top-2 right-2 bg-white rounded-full p-2 shadow-md hover:bg-red-50">
                                        <i class="fas fa-heart text-red-500"></i>
                                    </button>
                                    
                                    <!-- Price Change Badge -->
                                    @if($favorite->property->price_changed)
                                        <div class="absolute bottom-2 left-2">
                                            <span class="bg-{{ $favorite->property->price_change > 0 ? 'red' : 'green' }}-500 text-white px-2 py-1 rounded text-xs">
                                                {{ $favorite->property->price_change > 0 ? '+' : '' }}{{ number_format($favorite->property->price_change, 0) }}%
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Property Details -->
                            <div class="md:w-3/6 p-6">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <h3 class="text-xl font-semibold text-gray-800 mb-2">
                                            <a href="{{ route('properties.show', $favorite->property) }}" class="hover:text-blue-600">
                                                {{ $favorite->property->title }}
                                            </a>
                                        </h3>
                                        <p class="text-gray-600 mb-2">{{ $favorite->property->address }}, {{ $favorite->property->city }}</p>
                                        <div class="flex items-center space-x-4 text-sm text-gray-500">
                                            <span><i class="fas fa-bed mr-1"></i>{{ $favorite->property->bedrooms }} beds</span>
                                            <span><i class="fas fa-bath mr-1"></i>{{ $favorite->property->bathrooms }} baths</span>
                                            <span><i class="fas fa-ruler-combined mr-1"></i>{{ number_format($favorite->property->square_feet) }} sqft</span>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-2xl font-bold text-gray-800">
                                            ${{ number_format($favorite->property->price, 0) }}
                                        </div>
                                        @if($favorite->property->status === 'for_rent')
                                            <div class="text-sm text-gray-600">/month</div>
                                        @endif
                                        <div class="text-xs text-gray-500 mt-1">
                                            Favorited {{ $favorite->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Property Description -->
                                <p class="text-gray-600 mb-4 line-clamp-2">
                                    {{ Str::limit($favorite->property->description, 150) }}
                                </p>
                                
                                <!-- Features -->
                                @if($favorite->property->features->isNotEmpty())
                                    <div class="flex flex-wrap gap-2 mb-4">
                                        @foreach ($favorite->property->features->take(3) as $feature)
                                            <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs">
                                                {{ $feature->name }}
                                            </span>
                                        @endforeach
                                        @if($favorite->property->features->count() > 3)
                                            <span class="text-gray-500 text-xs">+{{ $favorite->property->features->count() - 3 }} more</span>
                                        @endif
                                    </div>
                                @endif
                                
                                <!-- Actions -->
                                <div class="flex justify-between items-center">
                                    <div class="flex space-x-3">
                                        <a href="{{ route('properties.show', $favorite->property) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm">
                                            View Details
                                        </a>
                                        <button onclick="scheduleTour({{ $favorite->property->id }})" class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors text-sm">
                                            Schedule Tour
                                        </button>
                                    </div>
                                    <div class="flex space-x-2">
                                        <button onclick="shareProperty({{ $favorite->property->id }})" class="text-gray-600 hover:text-gray-800">
                                            <i class="fas fa-share-alt"></i>
                                        </button>
                                        <button onclick="addToComparison({{ $favorite->property->id }})" class="text-gray-600 hover:text-gray-800">
                                            <i class="fas fa-balance-scale"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12">
                        <i class="fas fa-heart text-6xl text-gray-300 mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No favorite properties yet</h3>
                        <p class="text-gray-500 mb-6">Start browsing and save properties you're interested in.</p>
                        <a href="{{ route('optimized.properties.index') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                            Browse Properties
                        </a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script>
function filterFavorites(filter) {
    const url = new URL(window.location);
    if (filter) {
        url.searchParams.set('filter', filter);
    } else {
        url.searchParams.delete('filter');
    }
    window.location.href = url.toString();
}

function selectAll() {
    document.querySelectorAll('.property-checkbox').forEach(checkbox => {
        checkbox.checked = true;
    });
}

function deselectAll() {
    document.querySelectorAll('.property-checkbox').forEach(checkbox => {
        checkbox.checked = false;
    });
}

function compareSelected() {
    const selected = document.querySelectorAll('.property-checkbox:checked');
    if (selected.length < 2) {
        alert('Please select at least 2 properties to compare');
        return;
    }
    
    const ids = Array.from(selected).map(cb => cb.value);
    window.location.href = '/properties/compare?properties=' + ids.join(',');
}

function shareFavorites() {
    window.location.href = '/favorites/share';
}

function removeFavorite(favoriteId) {
    if (!confirm('Are you sure you want to remove this property from favorites?')) {
        return;
    }
    
    fetch('/favorites/' + favoriteId, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function scheduleTour(propertyId) {
    window.location.href = '/properties/' + propertyId + '/tour';
}

function shareProperty(propertyId) {
    window.location.href = '/properties/' + propertyId + '/share';
}

function addToComparison(propertyId) {
    window.location.href = '/properties/compare?add=' + propertyId;
}
</script>
@endsection

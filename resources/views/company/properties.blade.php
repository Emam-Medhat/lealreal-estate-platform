@extends('layouts.app')

@section('title', 'Company Properties')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Company Properties</h1>
                    <p class="text-gray-600">Manage your company's property listings</p>
                </div>
                <div class="flex items-center space-x-3">
                    <button onclick="addProperty()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Add Property
                    </button>
                    <a href="{{ route('company.dashboard') }}" class="text-gray-600 hover:text-gray-800">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Dashboard
                    </a>
                </div>
            </div>
        </div>

        <!-- Property Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-blue-100 rounded-full p-3 mr-4">
                        <i class="fas fa-home text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Properties</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $properties->count() }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-green-100 rounded-full p-3 mr-4">
                        <i class="fas fa-dollar-sign text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Value</p>
                        <p class="text-2xl font-bold text-gray-800">${{ number_format($totalValue, 0) }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-purple-100 rounded-full p-3 mr-4">
                        <i class="fas fa-sold text-purple-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Sold This Month</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $soldThisMonth }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-yellow-100 rounded-full p-3 mr-4">
                        <i class="fas fa-eye text-yellow-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Views</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $totalViews }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and Search -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                <div class="flex flex-col md:flex-row md:items-center space-y-4 md:space-y-0 md:space-x-4">
                    <input type="text" placeholder="Search properties..." class="px-3 py-2 border rounded-lg text-sm w-full md:w-64">
                    <select class="px-3 py-2 border rounded-lg text-sm">
                        <option>All Types</option>
                        <option>Residential</option>
                        <option>Commercial</option>
                        <option>Industrial</option>
                        <option>Land</option>
                    </select>
                    <select class="px-3 py-2 border rounded-lg text-sm">
                        <option>All Status</option>
                        <option>For Sale</option>
                        <option>For Rent</option>
                        <option>Sold</option>
                        <option>Rented</option>
                    </select>
                    <select class="px-3 py-2 border rounded-lg text-sm">
                        <option>Price Range</option>
                        <option>Under $100K</option>
                        <option>$100K - $250K</option>
                        <option>$250K - $500K</option>
                        <option>$500K - $1M</option>
                        <option>Over $1M</option>
                    </select>
                </div>
                <div class="flex space-x-2">
                    <button class="bg-gray-600 text-white px-3 py-2 rounded hover:bg-gray-700 transition-colors text-sm">
                        <i class="fas fa-filter mr-1"></i>
                        Filter
                    </button>
                    <button class="bg-green-600 text-white px-3 py-2 rounded hover:bg-green-700 transition-colors text-sm">
                        <i class="fas fa-download mr-1"></i>
                        Export
                    </button>
                </div>
            </div>
        </div>

        <!-- Properties Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($properties as $property)
                <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-lg transition-shadow">
                    <!-- Property Image -->
                    <div class="relative h-48 bg-gray-200">
                        @if($property->images->isNotEmpty())
                            <img src="{{ $property->images->first()->url }}" alt="{{ $property->title }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                <i class="fas fa-home text-gray-400 text-4xl"></i>
                            </div>
                        @endif
                        
                        <!-- Status Badge -->
                        <div class="absolute top-2 left-2">
                            <span class="bg-{{ $property->status === 'for_sale' ? 'green' : ($property->status === 'for_rent' ? 'blue' : 'gray') }}-500 text-white px-2 py-1 rounded text-xs">
                                {{ ucfirst(str_replace('_', ' ', $property->status)) }}
                            </span>
                        </div>
                        
                        <!-- Featured Badge -->
                        @if($property->featured)
                            <div class="absolute top-2 right-2">
                                <span class="bg-yellow-500 text-white px-2 py-1 rounded text-xs">
                                    <i class="fas fa-star mr-1"></i>Featured
                                </span>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Property Details -->
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">
                            <a href="{{ route('properties.show', $property) }}" class="hover:text-blue-600">
                                {{ $property->title }}
                            </a>
                        </h3>
                        <p class="text-gray-600 mb-3">{{ Str::limit($property->description, 80) }}</p>
                        <p class="text-gray-600 text-sm mb-3">
                            <i class="fas fa-map-marker-alt mr-1"></i>{{ $property->address }}, {{ $property->city }}
                        </p>
                        
                        <div class="flex items-center space-x-4 text-sm text-gray-600 mb-3">
                            <span><i class="fas fa-bed mr-1"></i>{{ $property->bedrooms }}</span>
                            <span><i class="fas fa-bath mr-1"></i>{{ $property->bathrooms }}</span>
                            <span><i class="fas fa-ruler-combined mr-1"></i>{{ number_format($property->square_feet) }} sqft</span>
                        </div>
                        
                        <div class="flex justify-between items-center mb-4">
                            <div class="text-2xl font-bold text-gray-800">
                                ${{ number_format($property->price, 0) }}
                            </div>
                            @if($property->status === 'for_rent')
                                <div class="text-sm text-gray-600">/month</div>
                            @endif
                        </div>
                        
                        <!-- Property Stats -->
                        <div class="flex justify-between items-center text-sm text-gray-600 mb-4">
                            <span><i class="fas fa-eye mr-1"></i>{{ $property->views ?? 0 }} views</span>
                            <span><i class="fas fa-heart mr-1"></i>{{ $property->favorites ?? 0 }} saves</span>
                            <span><i class="fas fa-calendar mr-1"></i>{{ $property->created_at->format('M j') }}</span>
                        </div>
                        
                        <!-- Assigned Agent -->
                        @if($property->agent)
                            <div class="flex items-center mb-4 p-2 bg-gray-50 rounded">
                                <div class="bg-gray-200 rounded-full w-8 h-8 mr-2 flex items-center justify-center">
                                    @if($property->agent->avatar)
                                        <img src="{{ $property->agent->avatar }}" alt="" class="w-8 h-8 rounded-full object-cover">
                                    @else
                                        <i class="fas fa-user text-gray-400 text-xs"></i>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-800">{{ $property->agent->name }}</p>
                                    <p class="text-xs text-gray-600">{{ $property->agent->role }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Property Actions -->
                    <div class="p-6 border-t bg-gray-50">
                        <div class="flex space-x-2">
                            <button onclick="viewProperty({{ $property->id }})" class="flex-1 bg-blue-600 text-white px-3 py-2 rounded hover:bg-blue-700 transition-colors text-sm">
                                View Details
                            </button>
                            <button onclick="editProperty({{ $property->id }})" class="flex-1 border border-gray-300 text-gray-700 px-3 py-2 rounded hover:bg-gray-50 transition-colors text-sm">
                                Edit
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <i class="fas fa-home text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Properties Yet</h3>
                    <p class="text-gray-500 mb-6">Add your first property to start building your portfolio.</p>
                    <button onclick="addProperty()" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Add First Property
                    </button>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($properties->hasPages())
            <div class="mt-6">
                {{ $properties->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Add Property Modal -->
<div id="propertyModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg p-6 max-w-2xl mx-4 w-full max-h-screen overflow-y-auto">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Add New Property</h3>
        
        <form action="{{ route('company.properties.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Property Title</label>
                    <input type="text" name="title" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" rows="4" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Property Type</label>
                        <select name="property_type_id" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select Type</option>
                            @foreach ($propertyTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="status" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="for_sale">For Sale</option>
                            <option value="for_rent">For Rent</option>
                        </select>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Price</label>
                        <input type="number" name="price" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Bedrooms</label>
                        <input type="number" name="bedrooms" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Bathrooms</label>
                        <input type="number" name="bathrooms" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Square Feet</label>
                        <input type="number" name="square_feet" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Year Built</label>
                        <input type="number" name="year_built"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                    <input type="text" name="address" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">City</label>
                        <input type="text" name="city" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">State</label>
                        <input type="text" name="state" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Assigned Agent</label>
                    <select name="agent_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select Agent</option>
                        @foreach ($agents as $agent)
                            <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Property Images</label>
                    <input type="file" name="images[]" multiple accept="image/*"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" name="featured" class="mr-2">
                    <label class="text-sm text-gray-700">Featured Property</label>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 mt-6">
                <button type="button" onclick="closePropertyModal()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    Add Property
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function addProperty() {
    document.getElementById('propertyModal').classList.remove('hidden');
}

function closePropertyModal() {
    document.getElementById('propertyModal').classList.add('hidden');
}

function viewProperty(propertyId) {
    window.location.href = '/properties/' + propertyId;
}

function editProperty(propertyId) {
    window.location.href = '/properties/' + propertyId + '/edit';
}
</script>
@endsection

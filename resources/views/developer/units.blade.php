@extends('layouts.app')

@section('title', 'Property Units')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Property Units</h1>
                    <p class="text-gray-600">Manage and track all property units across projects</p>
                </div>
                <div class="flex items-center space-x-3">
                    <button onclick="createUnit()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Add Unit
                    </button>
                    <a href="{{ route('developer.dashboard') }}" class="text-gray-600 hover:text-gray-800">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Dashboard
                    </a>
                </div>
            </div>
        </div>

        <!-- Units Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-blue-100 rounded-full p-3 mr-4">
                        <i class="fas fa-home text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Units</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $units->count() }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-green-100 rounded-full p-3 mr-4">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Sold</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $units->where('status', 'sold')->count() }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-yellow-100 rounded-full p-3 mr-4">
                        <i class="fas fa-clock text-yellow-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Reserved</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $units->where('status', 'reserved')->count() }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-purple-100 rounded-full p-3 mr-4">
                        <i class="fas fa-dollar-sign text-purple-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Avg. Price</p>
                        <p class="text-2xl font-bold text-gray-800">${{ number_format($avgPrice, 0) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and Search -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                <div class="flex flex-col md:flex-row md:items-center space-y-4 md:space-y-0 md:space-x-4">
                    <input type="text" placeholder="Search units..." class="px-3 py-2 border rounded-lg text-sm w-full md:w-64">
                    <select onchange="filterByProject(this.value)" class="px-3 py-2 border rounded-lg text-sm">
                        <option value="">All Projects</option>
                        @foreach ($projects as $project)
                            <option value="{{ $project->id }}" {{ request('project') == $project->id ? 'selected' : '' }}>
                                {{ $project->name }}
                            </option>
                        @endforeach
                    </select>
                    <select class="px-3 py-2 border rounded-lg text-sm">
                        <option>All Status</option>
                        <option>Available</option>
                        <option>Reserved</option>
                        <option>Sold</option>
                        <option>Under Contract</option>
                    </select>
                    <select class="px-3 py-2 border rounded-lg text-sm">
                        <option>All Types</option>
                        <option>Studio</option>
                        <option>1 Bedroom</option>
                        <option>2 Bedrooms</option>
                        <option>3 Bedrooms</option>
                        <option>Penthouse</option>
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

        <!-- Units Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($units as $unit)
                <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-lg transition-shadow">
                    <!-- Unit Image -->
                    <div class="relative h-48 bg-gray-200">
                        @if($unit->images->isNotEmpty())
                            <img src="{{ $unit->images->first()->url }}" alt="{{ $unit->unit_number }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                <i class="fas fa-home text-gray-400 text-4xl"></i>
                            </div>
                        @endif
                        
                        <!-- Status Badge -->
                        <div class="absolute top-2 left-2">
                            <span class="bg-{{ $unit->status === 'sold' ? 'green' : ($unit->status === 'reserved' ? 'yellow' : ($unit->status === 'under_contract' ? 'orange' : 'blue')) }}-500 text-white px-2 py-1 rounded text-xs">
                                {{ ucfirst(str_replace('_', ' ', $unit->status)) }}
                            </span>
                        </div>
                        
                        <!-- Featured Badge -->
                        @if($unit->featured)
                            <div class="absolute top-2 right-2">
                                <span class="bg-yellow-500 text-white px-2 py-1 rounded text-xs">
                                    <i class="fas fa-star mr-1"></i>Featured
                                </span>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Unit Details -->
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">
                            Unit {{ $unit->unit_number }}
                        </h3>
                        <p class="text-gray-600 mb-3">{{ $unit->project->name }}</p>
                        <p class="text-gray-600 text-sm mb-3">
                            <i class="fas fa-map-marker-alt mr-1"></i>{{ $unit->project->location }}
                        </p>
                        
                        <div class="flex items-center space-x-4 text-sm text-gray-600 mb-3">
                            <span><i class="fas fa-bed mr-1"></i>{{ $unit->bedrooms }}</span>
                            <span><i class="fas fa-bath mr-1"></i>{{ $unit->bathrooms }}</span>
                            <span><i class="fas fa-expand mr-1"></i>{{ number_format($unit->square_feet) }} sqft</span>
                        </div>
                        
                        <div class="flex justify-between items-center mb-4">
                            <div class="text-2xl font-bold text-gray-800">
                                ${{ number_format($unit->price, 0) }}
                            </div>
                            <div class="text-sm text-gray-600">
                                ${{ number_format($unit->price_per_sqft) }}/sqft
                            </div>
                        </div>
                        
                        <!-- Unit Features -->
                        @if($unit->features)
                            <div class="flex flex-wrap gap-1 mb-4">
                                @foreach (array_slice($unit->features, 0, 3) as $feature)
                                    <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs">
                                        {{ $feature }}
                                    </span>
                                @endforeach
                                @if(count($unit->features) > 3)
                                    <span class="text-xs text-gray-500">+{{ count($unit->features) - 3 }} more</span>
                                @endif
                            </div>
                        @endif
                        
                        <!-- Buyer Information -->
                        @if($unit->buyer)
                            <div class="mb-4 p-3 bg-gray-50 rounded">
                                <p class="text-sm font-medium text-gray-800">Buyer Information</p>
                                <p class="text-sm text-gray-600">{{ $unit->buyer->name }}</p>
                                <p class="text-xs text-gray-500">Purchased: {{ $unit->sale_date }}</p>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Unit Actions -->
                    <div class="p-6 border-t bg-gray-50">
                        <div class="flex space-x-2">
                            <button onclick="viewUnit({{ $unit->id }})" class="flex-1 bg-blue-600 text-white px-3 py-2 rounded hover:bg-blue-700 transition-colors text-sm">
                                View Details
                            </button>
                            <button onclick="editUnit({{ $unit->id }})" class="flex-1 border border-gray-300 text-gray-700 px-3 py-2 rounded hover:bg-gray-50 transition-colors text-sm">
                                Edit
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <i class="fas fa-home text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Units Yet</h3>
                    <p class="text-gray-500 mb-6">Start by adding your first property unit.</p>
                    <button onclick="createUnit()" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Add First Unit
                    </button>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($units->hasPages())
            <div class="mt-6">
                {{ $units->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Add Unit Modal -->
<div id="unitModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg p-6 max-w-2xl mx-4 w-full max-h-screen overflow-y-auto">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Add New Unit</h3>
        
        <form action="{{ route('developer.units.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Unit Number</label>
                        <input type="text" name="unit_number" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Project</label>
                        <select name="project_id" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select Project</option>
                            @foreach ($projects as $project)
                                <option value="{{ $project->id }}" {{ request('project') == $project->id ? 'selected' : '' }}>
                                    {{ $project->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Square Feet</label>
                        <input type="number" name="square_feet" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Price</label>
                        <input type="number" name="price" step="0.01" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Unit Type</label>
                        <select name="unit_type" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select Type</option>
                            <option value="studio">Studio</option>
                            <option value="1_bedroom">1 Bedroom</option>
                            <option value="2_bedrooms">2 Bedrooms</option>
                            <option value="3_bedrooms">3 Bedrooms</option>
                            <option value="penthouse">Penthouse</option>
                            <option value="townhouse">Townhouse</option>
                        </select>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Floor</label>
                    <input type="number" name="floor"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Features (comma separated)</label>
                    <input type="text" name="features" placeholder="e.g., Balcony, Parking, Storage"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="available">Available</option>
                        <option value="reserved">Reserved</option>
                        <option value="under_contract">Under Contract</option>
                        <option value="sold">Sold</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Unit Images</label>
                    <input type="file" name="images[]" multiple accept="image/*"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" name="featured" class="mr-2">
                    <label class="text-sm text-gray-700">Featured Unit</label>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 mt-6">
                <button type="button" onclick="closeUnitModal()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    Add Unit
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function createUnit() {
    document.getElementById('unitModal').classList.remove('hidden');
}

function closeUnitModal() {
    document.getElementById('unitModal').classList.add('hidden');
}

function filterByProject(projectId) {
    window.location.href = '?project=' + projectId;
}

function viewUnit(unitId) {
    window.location.href = '/developer/units/' + unitId;
}

function editUnit(unitId) {
    window.location.href = '/developer/units/' + unitId + '/edit';
}
</script>
@endsection

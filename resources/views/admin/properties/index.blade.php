@extends('layouts.app')

@section('title', 'Properties Management')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Properties Management</h1>
                    <p class="text-gray-600">Manage all property listings</p>
                </div>
                <a href="{{ route('properties.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>
                    Add Property
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input type="text" placeholder="Search properties..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Types</option>
                        <option value="apartment">Apartment</option>
                        <option value="house">House</option>
                        <option value="villa">Villa</option>
                        <option value="land">Land</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="pending">Pending</option>
                        <option value="sold">Sold</option>
                        <option value="rented">Rented</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Agent</label>
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Agents</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Properties Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($properties as $property)
                <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                    @if ($property->media && $property->media->isNotEmpty())
                        <div class="h-48 bg-gray-200">
                            <img src="{{ $property->media->first()->url }}" alt="{{ $property->title }}" class="w-full h-full object-cover">
                        </div>
                    @else
                        <div class="h-48 bg-gray-200 flex items-center justify-center">
                            <i class="fas fa-home text-gray-400 text-4xl"></i>
                        </div>
                    @endif
                    
                    <div class="p-4">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-lg font-semibold text-gray-800 line-clamp-1">{{ $property->title }}</h3>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                {{ $property->status === 'active' ? 'bg-green-100 text-green-800' : 
                                   ($property->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                {{ ucfirst($property->status) }}
                            </span>
                        </div>
                        
                        <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ $property->description }}</p>
                        
                        <div class="flex items-center justify-between text-sm text-gray-500 mb-3">
                            <span><i class="fas fa-map-marker-alt mr-1"></i> {{ $property->location ?? 'N/A' }}</span>
                            <span><i class="fas fa-bed mr-1"></i> {{ $property->bedrooms ?? 0 }} beds</span>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-xl font-bold text-blue-600">${{ number_format($property->price, 0) }}</span>
                            @if ($property->agent)
                                <div class="flex items-center text-sm text-gray-600">
                                    <i class="fas fa-user mr-1"></i>
                                    {{ $property->agent->first_name }} {{ $property->agent->last_name }}
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="px-4 py-3 bg-gray-50 border-t border-gray-200">
                        <div class="flex justify-between items-center">
                            <a href="{{ route('properties.show', $property) }}" class="text-blue-600 hover:text-blue-800 text-sm">View Details</a>
                            <div class="flex space-x-2">
                                <a href="#" class="text-gray-600 hover:text-gray-800">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="#" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Are you sure?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <i class="fas fa-home text-gray-400 text-5xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No properties found</h3>
                    <p class="text-gray-600 mb-4">Get started by adding your first property.</p>
                    <a href="{{ route('properties.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Add Property
                    </a>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if ($properties->hasPages())
            <div class="mt-8">
                {{ $properties->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Properties')

@section('content')
<div class="bg-gray-50 min-h-screen">
    <!-- Page Header -->
    <div class="bg-white border-b border-gray-200">
        <div class="container mx-auto px-6 py-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Properties</h1>
                    <p class="mt-1 text-gray-600">Browse our comprehensive property listings</p>
                </div>
                @auth
                    <div class="mt-4 md:mt-0">
                        <a href="{{ route('properties.create') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            <i class="fas fa-plus mr-2"></i>Add Property
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </div>

    <!-- Search & Filters -->
    <div class="container mx-auto px-6 py-8">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-8">
            <form method="GET" action="{{ route('properties.index') }}" id="searchForm">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 gap-6">
                    <!-- Basic Search -->
                    <div class="lg:col-span-2">
                        <label for="q" class="block text-sm font-semibold text-gray-700 mb-2">Search</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input type="text" name="q" id="q" value="{{ request('q') }}" 
                                   class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm" 
                                   placeholder="Keywords, location...">
                        </div>
                    </div>

                    <!-- Property Type -->
                    <div>
                        <label for="property_type" class="block text-sm font-semibold text-gray-700 mb-2">Type</label>
                        <select name="property_type" id="property_type" 
                                class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm appearance-none bg-no-repeat bg-right pr-8"
                                style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%2224%22%20height%3D%2224%22%20viewBox%3D%220%200%2024%2024%22%20fill%3D%22none%22%20stroke%3D%22%236B7280%22%20stroke-width%3D%222%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%3E%3Cpolyline%20points%3D%226%209%2012%2015%2018%209%22%3E%3C%2Fpolyline%3E%3C%2Fsvg%3E'); background-size: 1.25rem;">
                            <option value="">All Types</option>
                            @foreach($propertyTypes as $type)
                                <option value="{{ $type->slug }}" {{ request('property_type') == $type->slug ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Listing Type -->
                    <div>
                        <label for="listing_type" class="block text-sm font-semibold text-gray-700 mb-2">For</label>
                        <select name="listing_type" id="listing_type" 
                                class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm appearance-none bg-no-repeat bg-right pr-8"
                                style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%2224%22%20height%3D%2224%22%20viewBox%3D%220%200%2024%2024%22%20fill%3D%22none%22%20stroke%3D%22%236B7280%22%20stroke-width%3D%222%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%3E%3Cpolyline%20points%3D%226%209%2012%2015%2018%209%22%3E%3C%2Fpolyline%3E%3C%2Fsvg%3E'); background-size: 1.25rem;">
                            <option value="">All</option>
                            <option value="sale" {{ request('listing_type') == 'sale' ? 'selected' : '' }}>Sale</option>
                            <option value="rent" {{ request('listing_type') == 'rent' ? 'selected' : '' }}>Rent</option>
                        </select>
                    </div>

                    <!-- Max Price -->
                    <div>
                        <label for="max_price" class="block text-sm font-semibold text-gray-700 mb-2">Max Price</label>
                        <input type="number" name="max_price" id="max_price" value="{{ request('max_price') }}" 
                               class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm" 
                               placeholder="Max price">
                    </div>

                    <!-- Search Actions -->
                    <div class="flex items-end space-x-2">
                        <button type="submit" class="flex-grow bg-blue-600 text-white px-4 py-2.5 rounded-lg font-semibold hover:bg-blue-700 focus:ring-4 focus:ring-blue-200 transition-all duration-300 flex items-center justify-center">
                            <i class="fas fa-search mr-2 text-sm"></i>Search
                        </button>
                        <button type="button" onclick="toggleAdvancedFilters()" class="px-4 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                            <i class="fas fa-sliders-h"></i>
                        </button>
                    </div>
                </div>

                <!-- Advanced Filters (Hidden by default) -->
                <div id="advancedFilters" class="hidden mt-6 pt-6 border-t border-gray-100">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div>
                            <label for="min_price" class="block text-sm font-semibold text-gray-700 mb-2">Min Price</label>
                            <input type="number" name="min_price" id="min_price" value="{{ request('min_price') }}" 
                                   class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm" 
                                   placeholder="Min price">
                        </div>
                        <div>
                            <label for="bedrooms" class="block text-sm font-semibold text-gray-700 mb-2">Bedrooms</label>
                            <select name="bedrooms" id="bedrooms" 
                                    class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                                <option value="">Any</option>
                                @for($i = 1; $i <= 10; $i++)
                                    <option value="{{ $i }}" {{ request('bedrooms') == $i ? 'selected' : '' }}>{{ $i }}+</option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label for="bathrooms" class="block text-sm font-semibold text-gray-700 mb-2">Bathrooms</label>
                            <select name="bathrooms" id="bathrooms" 
                                    class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                                <option value="">Any</option>
                                @for($i = 1; $i <= 10; $i++)
                                    <option value="{{ $i }}" {{ request('bathrooms') == $i ? 'selected' : '' }}>{{ $i }}+</option>
                                @endfor
                            </select>
                        </div>
                        <div class="flex items-center space-x-6 mt-8">
                            <label class="flex items-center cursor-pointer group">
                                <input type="checkbox" name="featured" value="1" {{ request('featured') ? 'checked' : '' }} class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <span class="ml-2 text-sm font-medium text-gray-700 group-hover:text-blue-600 transition-colors">Featured</span>
                            </label>
                            <label class="flex items-center cursor-pointer group">
                                <input type="checkbox" name="premium" value="1" {{ request('premium') ? 'checked' : '' }} class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <span class="ml-2 text-sm font-medium text-gray-700 group-hover:text-blue-600 transition-colors">Premium</span>
                            </label>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Property Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
            @forelse($properties as $property)
                @include('properties.partials.card', ['property' => $property])
            @empty
                <div class="col-span-full py-20">
                    <div class="text-center">
                        <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 rounded-full mb-6">
                            <i class="fas fa-search text-gray-400 text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">No Properties Found</h3>
                        <p class="text-gray-500 mb-8">We couldn't find any properties matching your current filters.</p>
                        <a href="{{ route('properties.index') }}" class="text-blue-600 font-semibold hover:text-blue-800 transition-colors">
                            Clear all filters
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-12">
            {{ $properties->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script>
    function toggleAdvancedFilters() {
        const filters = document.getElementById('advancedFilters');
        filters.classList.toggle('hidden');
    }
</script>
@endpush
@endsection

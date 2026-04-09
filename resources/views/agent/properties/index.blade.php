@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8">
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">{{ __('Properties') }}</h1>
            <p class="text-gray-600 mt-2">{{ __('Manage your property listings') }}</p>
        </div>
        <a href="{{ route('agent.properties.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">
            {{ __('Add New Property') }}
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Search') }}</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search properties...') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Type') }}</label>
                <select name="type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                    <option value="">{{ __('All Types') }}</option>
                    <option value="apartment" {{ request('type') == 'apartment' ? 'selected' : '' }}>{{ __('Apartment') }}</option>
                    <option value="house" {{ request('type') == 'house' ? 'selected' : '' }}>{{ __('House') }}</option>
                    <option value="villa" {{ request('type') == 'villa' ? 'selected' : '' }}>{{ __('Villa') }}</option>
                    <option value="commercial" {{ request('type') == 'commercial' ? 'selected' : '' }}>{{ __('Commercial') }}</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Status') }}</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                    <option value="">{{ __('All Status') }}</option>
                    <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>{{ __('Available') }}</option>
                    <option value="sold" {{ request('status') == 'sold' ? 'selected' : '' }}>{{ __('Sold') }}</option>
                    <option value="rented" {{ request('status') == 'rented' ? 'selected' : '' }}>{{ __('Rented') }}</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-gray-600 text-white py-2 px-4 rounded-lg hover:bg-gray-700 transition duration-300">
                    {{ __('Filter') }}
                </button>
            </div>
        </form>
    </div>

    <!-- Properties Grid -->
    @if(isset($properties) && count($properties) > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($properties as $property)
                <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition duration-300">
                    <div class="relative">
                        @if($property->images && count($property->images) > 0)
                            <img src="{{ $property->images->first()->url }}" alt="{{ $property->title }}" class="w-full h-48 object-cover">
                        @else
                            <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                        @endif
                        <div class="absolute top-4 right-4">
                            <span class="px-3 py-1 text-xs font-semibold rounded-full 
                                @if($property->status == 'available') bg-green-100 text-green-800
                                @elseif($property->status == 'sold') bg-red-100 text-red-800
                                @elseif($property->status == 'rented') bg-blue-100 text-blue-800
                                @else bg-yellow-100 text-yellow-800 @endif">
                                {{ ucfirst($property->status) }}
                            </span>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-2">{{ $property->title }}</h3>
                        <p class="text-gray-600 mb-4">{{ Str::limit($property->description, 100) }}</p>
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-xl font-bold text-blue-600">${{ number_format($property->price) }}</span>
                            <div class="flex gap-2 text-sm text-gray-500">
                                <span>{{ $property->bedrooms }} {{ __('Beds') }}</span>
                                <span>•</span>
                                <span>{{ $property->bathrooms }} {{ __('Baths') }}</span>
                                <span>•</span>
                                <span>{{ $property->area }} {{ __('sqft') }}</span>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('agent.properties.show', $property) }}" class="flex-1 bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition duration-300 text-center">
                                {{ __('View') }}
                            </a>
                            <a href="{{ route('agent.properties.edit', $property) }}" class="flex-1 bg-gray-200 text-gray-800 py-2 px-4 rounded-lg hover:bg-gray-300 transition duration-300 text-center">
                                {{ __('Edit') }}
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Pagination -->
        <div class="mt-8">
            {{ $properties->links() }}
        </div>
    @else
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
            <h3 class="text-xl font-semibold text-gray-800 mb-2">{{ __('No Properties Found') }}</h3>
            <p class="text-gray-600 mb-6">{{ __('Get started by adding your first property listing.') }}</p>
            <a href="{{ route('agent.properties.create') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition duration-300 inline-block">
                {{ __('Add Your First Property') }}
            </a>
        </div>
    @endif
</div>
@endsection

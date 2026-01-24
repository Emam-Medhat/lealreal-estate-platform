@extends('layouts.app', ['showNavbar' => false])

@section('content')
<!-- Include Navbar Component -->
<x-navbar />

<!-- Hero Section -->
<section class="relative bg-gradient-to-r from-blue-600 to-indigo-700 text-white">
    <div class="absolute inset-0 bg-black opacity-40"></div>
    <div class="relative container mx-auto px-6 py-24">
        <div class="text-center">
            <h1 class="text-5xl font-bold mb-6 animate-fade-in">
                {{ __('Welcome to Real Estate Pro') }}
            </h1>
            <p class="text-xl mb-8 max-w-2xl mx-auto">
                {{ __('Find your dream home with our comprehensive real estate platform. Browse properties, connect with agents, and make informed decisions.') }}
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                @guest
                    <a href="{{ route('register') }}" class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition duration-300">
                        {{ __('Get Started') }}
                    </a>
                    <a href="{{ route('login') }}" class="border border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition duration-300">
                        {{ __('Sign In') }}
                    </a>
                @else
                    <a href="{{ route('agent.dashboard') }}" class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition duration-300">
                        {{ __('Go to Dashboard') }}
                    </a>
                @endguest
            </div>
        </div>
    </div>
</section>

<!-- Search Section -->
<section class="bg-gray-50 py-12">
    <div class="container mx-auto px-6">
        <div class="bg-white rounded-lg shadow-lg p-6 -mt-16 relative z-10">
            <form class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-2">
                        {{ __('Location') }}
                    </label>
                    <input type="text" placeholder="{{ __('Enter city or area') }}" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-2">
                        {{ __('Property Type') }}
                    </label>
                    <select class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                        <option>{{ __('All Types') }}</option>
                        <option>{{ __('Apartment') }}</option>
                        <option>{{ __('House') }}</option>
                        <option>{{ __('Villa') }}</option>
                        <option>{{ __('Commercial') }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-2">
                        {{ __('Price Range') }}
                    </label>
                    <select class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                        <option>{{ __('Any Price') }}</option>
                        <option>{{ __('Under $100K') }}</option>
                        <option>{{ __('$100K - $250K') }}</option>
                        <option>{{ __('$250K - $500K') }}</option>
                        <option>{{ __('Over $500K') }}</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition duration-300">
                        {{ __('Search Properties') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Featured Properties -->
<section class="py-16">
    <div class="container mx-auto px-6">
        <div class="text-center mb-12">
            <h2 class="text-4xl font-bold text-gray-800 mb-4">
                {{ __('Featured Properties') }}
            </h2>
            <p class="text-gray-600 max-w-2xl mx-auto">
                {{ __('Discover our handpicked selection of premium properties in prime locations') }}
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($featuredProperties as $property)
            <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition duration-300">
                <div class="relative">
                    @if($property->media && $property->media->where('media_type', 'image')->count() > 0)
                        <img src="{{ $property->media->where('media_type', 'image')->first()->url ?? asset('images/default-property.jpg') }}" 
                             alt="{{ $property->title }}" 
                             class="w-full h-56 object-cover">
                    @else
                        <img src="{{ asset('images/default-property.jpg') }}" alt="Property" class="w-full h-56 object-cover">
                    @endif
                    @if($property->featured)
                    <div class="absolute top-4 left-4 bg-blue-600 text-white px-3 py-1 rounded-full text-sm">
                        {{ __('Featured') }}
                    </div>
                    @endif
                    @if($property->premium)
                    <div class="absolute top-4 right-4 bg-purple-600 text-white px-3 py-1 rounded-full text-sm">
                        {{ __('Premium') }}
                    </div>
                    @endif
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-semibold mb-2">{{ $property->title }}</h3>
                    <p class="text-gray-600 mb-4">{{ Str::limit($property->description ?? 'No description available', 100) }}</p>
                    <div class="flex justify-between items-center mb-4">
                        <span class="text-2xl font-bold text-blue-600">
                            {{ number_format($property->price ?? 0, 2) }} {{ $property->currency ?? 'USD' }}
                        </span>
                        <div class="flex gap-2 text-sm text-gray-500">
                            @if($property->bedrooms)
                            <span>{{ $property->bedrooms }} {{ __('Beds') }}</span>
                            <span>•</span>
                            @endif
                            @if($property->bathrooms)
                            <span>{{ $property->bathrooms }} {{ __('Baths') }}</span>
                            <span>•</span>
                            @endif
                            <span>{{ number_format($property->area ?? 0, 2) }} {{ $property->area_unit ?? 'm²' }}</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center text-sm text-gray-500">
                            <i class="fas fa-map-marker-alt mr-1"></i>
                            <span>{{ $property->city ?? 'Unknown City' }}, {{ $property->country ?? 'Unknown Country' }}</span>
                        </div>
                        <span class="text-sm text-gray-500">
                            {{ ucfirst($property->property_type ?? 'property') }}
                        </span>
                    </div>
                    <a href="{{ route('properties.show', $property) }}" class="block w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition duration-300 text-center">
                        {{ __('View Details') }}
                    </a>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-12">
                <div class="text-gray-500">
                    <i class="fas fa-home text-4xl mb-4"></i>
                    <p class="text-lg">{{ __('No properties available at the moment.') }}</p>
                    <p class="text-sm mt-2">{{ __('Please check back later or contact our agents.') }}</p>
                </div>
            </div>
            @endforelse
        </div>
    </div>
</section>

<!-- Services Section -->
<section class="bg-gray-50 py-16">
    <div class="container mx-auto px-6">
        <div class="text-center mb-12">
            <h2 class="text-4xl font-bold text-gray-800 mb-4">
                {{ __('Our Services') }}
            </h2>
            <p class="text-gray-600 max-w-2xl mx-auto">
                {{ __('Comprehensive real estate solutions to meet all your property needs') }}
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="text-center">
                <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-2">{{ __('Buy Property') }}</h3>
                <p class="text-gray-600">{{ __('Find your perfect home from our extensive listings') }}</p>
            </div>
            
            <div class="text-center">
                <div class="bg-green-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-2">{{ __('Sell Property') }}</h3>
                <p class="text-gray-600">{{ __('Get the best value for your property with our expert agents') }}</p>
            </div>
            
            <div class="text-center">
                <div class="bg-purple-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-2">{{ __('Find Agents') }}</h3>
                <p class="text-gray-600">{{ __('Connect with experienced real estate professionals') }}</p>
            </div>
            
            <div class="text-center">
                <div class="bg-orange-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-2">{{ __('Property Management') }}</h3>
                <p class="text-gray-600">{{ __('Professional management services for your investments') }}</p>
            </div>
        </div>
    </div>
</section>

<!-- Statistics Section -->
<section class="bg-blue-600 text-white py-16">
    <div class="container mx-auto px-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 text-center">
            <div>
                <div class="text-4xl font-bold mb-2">1,200+</div>
                <div class="text-blue-100">{{ __('Properties Sold') }}</div>
            </div>
            <div>
                <div class="text-4xl font-bold mb-2">850+</div>
                <div class="text-blue-100">{{ __('Happy Clients') }}</div>
            </div>
            <div>
                <div class="text-4xl font-bold mb-2">50+</div>
                <div class="text-blue-100">{{ __('Expert Agents') }}</div>
            </div>
            <div>
                <div class="text-4xl font-bold mb-2">15+</div>
                <div class="text-blue-100">{{ __('Years Experience') }}</div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="bg-gray-900 text-white py-16">
    <div class="container mx-auto px-6 text-center">
        <h2 class="text-3xl font-bold mb-4">
            {{ __('Ready to Find Your Dream Home?') }}
        </h2>
        <p class="text-gray-300 mb-8 max-w-2xl mx-auto">
            {{ __('Join thousands of satisfied customers who found their perfect property through our platform') }}
        </p>
        @guest
            <a href="{{ route('register') }}" class="bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-700 transition duration-300 inline-block">
                {{ __('Get Started Today') }}
            </a>
        @else
            <a href="{{ route('agent.dashboard') }}" class="bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-700 transition duration-300 inline-block">
                {{ __('Browse Properties') }}
            </a>
        @endguest
    </div>
</section>

<style>
    @keyframes fade-in {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .animate-fade-in {
        animation: fade-in 1s ease-out;
    }
</style>
@endsection

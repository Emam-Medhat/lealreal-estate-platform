@extends('layouts.app', ['showNavbar' => false])

@section('content')
<!-- Include Navbar Component -->
<x-navbar />

<!-- Hero Section -->
<section class="relative bg-gradient-to-r from-blue-600 to-indigo-700 text-white overflow-hidden">
    <div class="absolute inset-0 bg-black/40"></div>
    <div class="relative container mx-auto px-6 py-32">
        <div class="max-w-3xl">
            <h1 class="text-5xl md:text-6xl font-extrabold mb-6 leading-tight animate-fade-in">
                {{ __('Find Your Dream Home') }} <br>
                <span class="text-blue-200">{{ __('With Real Estate Pro') }}</span>
            </h1>
            <p class="text-xl mb-10 text-gray-100 leading-relaxed">
                {{ __('Discover the perfect property from our extensive collection of homes, apartments, and commercial spaces. Expert guidance for every step of your journey.') }}
            </p>
            <div class="flex flex-col sm:flex-row gap-4">
                <a href="{{ route('properties.index') }}" class="bg-white text-blue-600 px-8 py-4 rounded-xl font-bold hover:bg-gray-100 transition duration-300 shadow-lg text-center">
                    {{ __('Browse Properties') }}
                </a>
                @guest
                    <a href="{{ route('register') }}" class="bg-blue-500/20 backdrop-blur-md border border-white/30 text-white px-8 py-4 rounded-xl font-bold hover:bg-blue-500/30 transition duration-300 text-center">
                        {{ __('Get Started') }}
                    </a>
                @else
                    <a href="{{ route('agent.dashboard') }}" class="bg-blue-500/20 backdrop-blur-md border border-white/30 text-white px-8 py-4 rounded-xl font-bold hover:bg-blue-500/30 transition duration-300 text-center">
                        {{ __('Go to Dashboard') }}
                    </a>
                @endguest
            </div>
        </div>
    </div>
    <!-- Decorative element -->
    <div class="absolute bottom-0 right-0 w-1/3 h-full opacity-10 pointer-events-none">
        <i class="fas fa-city text-[20rem] transform translate-y-1/4 translate-x-1/4"></i>
    </div>
</section>

<!-- Quick Search -->
<section class="relative z-10 -mt-12 container mx-auto px-6">
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
        <form action="{{ route('properties.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    {{ __('Location') }}
                </label>
                <div class="relative">
                    <i class="fas fa-map-marker-alt absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" name="city" placeholder="{{ __('Enter city or area') }}" class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                </div>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    {{ __('Property Type') }}
                </label>
                <div class="relative">
                    <i class="fas fa-home absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <select name="property_type" class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 appearance-none">
                        <option value="">{{ __('All Types') }}</option>
                        <option value="apartment">{{ __('Apartment') }}</option>
                        <option value="house">{{ __('House') }}</option>
                        <option value="villa">{{ __('Villa') }}</option>
                        <option value="commercial">{{ __('Commercial') }}</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    {{ __('Max Price') }}
                </label>
                <div class="relative">
                    <i class="fas fa-tag absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="number" name="max_price" placeholder="{{ __('Max price') }}" class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 transition-all">
                </div>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 text-white py-3.5 px-6 rounded-xl font-bold hover:bg-blue-700 shadow-lg shadow-blue-200 transition-all transform hover:-translate-y-0.5">
                    {{ __('Search Properties') }}
                </button>
            </div>
        </form>
    </div>
</section>

<!-- Featured Properties -->
<section class="py-24 bg-white">
    <div class="container mx-auto px-6">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-12">
            <div>
                <h2 class="text-4xl font-extrabold text-gray-900 mb-4">
                    {{ __('Featured Properties') }}
                </h2>
                <p class="text-gray-600 text-lg max-w-2xl">
                    {{ __('Discover our handpicked selection of premium properties in prime locations') }}
                </p>
            </div>
            <a href="{{ route('properties.index', ['featured' => 1]) }}" class="mt-4 md:mt-0 text-blue-600 font-bold flex items-center hover:text-blue-800 transition-colors">
                {{ __('View All Featured') }} <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
            @forelse($featuredProperties as $property)
                @include('properties.partials.card', ['property' => $property])
            @empty
                <div class="col-span-full text-center py-20 bg-gray-50 rounded-3xl border-2 border-dashed border-gray-200">
                    <div class="text-gray-400">
                        <i class="fas fa-home text-6xl mb-4"></i>
                        <p class="text-xl font-bold">{{ __('No properties available at the moment.') }}</p>
                        <p class="mt-2">{{ __('Please check back later or contact our agents.') }}</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</section>

<!-- Services Section -->
<section class="bg-gray-50 py-24">
    <div class="container mx-auto px-6">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-extrabold text-gray-900 mb-4">
                {{ __('Our Services') }}
            </h2>
            <p class="text-gray-600 text-lg max-w-2xl mx-auto">
                {{ __('Comprehensive real estate solutions to meet all your property needs') }}
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            @php
                $services = [
                    ['icon' => 'home', 'color' => 'blue', 'title' => 'Buy Property', 'desc' => 'Find your perfect home from our extensive listings'],
                    ['icon' => 'hand-holding-usd', 'color' => 'green', 'title' => 'Sell Property', 'desc' => 'Get the best value for your property with our marketing'],
                    ['icon' => 'key', 'color' => 'purple', 'title' => 'Rent Property', 'desc' => 'Discover great rental options in your preferred area'],
                    ['icon' => 'chart-line', 'color' => 'amber', 'title' => 'Investments', 'desc' => 'Expert advice on real estate investment opportunities']
                ];
            @endphp

            @foreach($services as $service)
            <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:shadow-xl transition-all duration-300 group">
                <div class="bg-{{ $service['color'] }}-100 w-16 h-16 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                    <i class="fas fa-{{ $service['icon'] }} text-2xl text-{{ $service['color'] }}-600"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-blue-600 transition-colors">{{ __($service['title']) }}</h3>
                <p class="text-gray-600 leading-relaxed">{{ __($service['desc']) }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Latest Properties -->
<section class="py-24 bg-white">
    <div class="container mx-auto px-6">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-12">
            <div>
                <h2 class="text-4xl font-extrabold text-gray-900 mb-4">
                    {{ __('Latest Properties') }}
                </h2>
                <p class="text-gray-600 text-lg max-w-2xl">
                    {{ __('The newest additions to our market, fresh from the source') }}
                </p>
            </div>
            <a href="{{ route('properties.index', ['sort' => 'created_at', 'order' => 'desc']) }}" class="mt-4 md:mt-0 text-blue-600 font-bold flex items-center hover:text-blue-800 transition-colors">
                {{ __('View All New') }} <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
            @foreach($latestProperties as $property)
                @include('properties.partials.card', ['property' => $property])
            @endforeach
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-24 bg-blue-600 relative overflow-hidden">
    <div class="container mx-auto px-6 relative z-10 text-center">
        <h2 class="text-4xl font-extrabold text-white mb-6">
            {{ __('Are you a real estate professional?') }}
        </h2>
        <p class="text-xl text-blue-100 mb-10 max-w-2xl mx-auto leading-relaxed">
            {{ __('Join our platform today to list your properties, manage leads, and grow your business with our advanced tools.') }}
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('register', ['type' => 'agent']) }}" class="bg-white text-blue-600 px-10 py-4 rounded-xl font-bold hover:bg-gray-100 transition shadow-xl">
                {{ __('Join as an Agent') }}
            </a>
            <a href="{{ route('contact') }}" class="bg-blue-500 text-white border border-blue-400 px-10 py-4 rounded-xl font-bold hover:bg-blue-400 transition">
                {{ __('Contact Sales') }}
            </a>
        </div>
    </div>
    <!-- Decorative -->
    <div class="absolute top-0 left-0 w-64 h-64 bg-white/10 rounded-full -translate-x-1/2 -translate-y-1/2"></div>
    <div class="absolute bottom-0 right-0 w-96 h-96 bg-white/10 rounded-full translate-x-1/3 translate-y-1/3"></div>
</section>
@endsection

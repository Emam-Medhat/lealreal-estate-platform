@extends('layouts.app')

@section('content')

<!-- Premium Hero Section -->
<section class="relative min-h-screen bg-gradient-to-br from-slate-900 via-blue-900 to-slate-900 text-white overflow-hidden">
    <!-- Background Video/Image -->
    <div class="absolute inset-0">
        <div class="absolute inset-0 bg-gradient-to-r from-black/60 via-black/40 to-black/60"></div>
        <img src="https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80" 
             alt="Luxury Real Estate" 
             class="w-full h-full object-cover">
    </div>
    
    <!-- Hero Content -->
    <div class="relative container mx-auto px-6 py-24 lg:py-32">
        <div class="max-w-4xl">
            <!-- Trust Badge -->
            <div class="inline-flex items-center bg-white/10 backdrop-blur-md rounded-full px-4 py-2 mb-6 border border-white/20">
                <i class="fas fa-award text-yellow-400 mr-2"></i>
                <span class="text-sm font-medium">{{ __('Trusted by 50,000+ Property Seekers') }}</span>
            </div>
            
            <!-- Main Headline -->
            <h1 class="text-5xl lg:text-7xl font-bold mb-6 leading-tight">
                {{ __('Find Your Perfect') }} <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-cyan-400">
                    {{ __('Property') }}
                </span>
            </h1>
            
            <!-- Subheadline -->
            <p class="text-xl lg:text-2xl text-gray-200 mb-12 max-w-3xl leading-relaxed">
                {{ __('Discover premium properties, trusted agents, and seamless transactions. Your dream property awaits.') }}
            </p>
            
            <!-- Advanced Search Bar -->
            <div class="bg-white rounded-3xl shadow-2xl p-8 mb-8">
                <!-- Property Type Tabs -->
                <div class="flex flex-wrap gap-2 mb-6">
                    <button type="button" class="px-6 py-2 bg-blue-600 text-white rounded-full font-medium hover:bg-blue-700 transition-colors">
                        {{ __('Buy') }}
                    </button>
                    <button type="button" class="px-6 py-2 bg-gray-100 text-gray-700 rounded-full font-medium hover:bg-gray-200 transition-colors">
                        {{ __('Rent') }}
                    </button>
                    <button type="button" class="px-6 py-2 bg-gray-100 text-gray-700 rounded-full font-medium hover:bg-gray-200 transition-colors">
                        {{ __('Commercial') }}
                    </button>
                    <button type="button" class="px-6 py-2 bg-gray-100 text-gray-700 rounded-full font-medium hover:bg-gray-200 transition-colors">
                        {{ __('Projects') }}
                    </button>
                </div>
                
                <!-- Search Form -->
                <form action="{{ route('properties.index') }}" method="GET" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="relative">
                            <i class="fas fa-map-marker-alt absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="text" 
                                   name="location" 
                                   placeholder="{{ __('Location, City, or Area') }}" 
                                   class="w-full pl-12 pr-4 py-4 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                        </div>
                        
                        <div class="relative">
                            <i class="fas fa-home absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <select name="property_type" class="w-full pl-12 pr-4 py-4 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 appearance-none">
                                <option value="">{{ __('All Property Types') }}</option>
                                <option value="apartment">{{ __('Apartments') }}</option>
                                <option value="villa">{{ __('Villas') }}</option>
                                <option value="house">{{ __('Houses') }}</option>
                                <option value="commercial">{{ __('Commercial') }}</option>
                                <option value="land">{{ __('Land') }}</option>
                                <option value="off-plan">{{ __('Off-Plan Projects') }}</option>
                            </select>
                        </div>
                        
                        <div class="relative">
                            <i class="fas fa-dollar-sign absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="text" 
                                   name="price_range" 
                                   placeholder="{{ __('Price Range') }}" 
                                   class="w-full pl-12 pr-4 py-4 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 transition-all">
                        </div>
                        
                        <button type="submit" class="bg-blue-600 text-white py-4 px-8 rounded-xl font-bold hover:bg-blue-700 shadow-lg shadow-blue-200 transition-all transform hover:-translate-y-0.5">
                            <i class="fas fa-search mr-2"></i>
                            {{ __('Search') }}
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Quick Links -->
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('properties.index') }}" class="text-white hover:text-blue-300 transition-colors">
                    <i class="fas fa-fire mr-2"></i>{{ __('Hot Deals') }}
                </a>
                <a href="{{ route('properties.index', ['featured' => 1]) }}" class="text-white hover:text-blue-300 transition-colors">
                    <i class="fas fa-star mr-2"></i>{{ __('Featured') }}
                </a>
                <a href="{{ route('properties.index', ['new' => 1]) }}" class="text-white hover:text-blue-300 transition-colors">
                    <i class="fas fa-sparkles mr-2"></i>{{ __('New Listings') }}
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Platform Stats & Trust Indicators -->
<section class="py-16 bg-gradient-to-r from-blue-600 to-indigo-700 text-white">
    <div class="container mx-auto px-6">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold mb-4">{{ __('Trusted by Millions') }}</h2>
            <p class="text-blue-100 text-lg">{{ __('Join the leading real estate platform') }}</p>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
            <div class="text-center">
                <div class="text-4xl lg:text-5xl font-bold mb-2" data-counter="{{ $stats->total_properties ?? 15000 }}">0</div>
                <div class="text-blue-100">{{ __('Properties Listed') }}</div>
            </div>
            <div class="text-center">
                <div class="text-4xl lg:text-5xl font-bold mb-2" data-counter="{{ $stats->verified_agents ?? 2500 }}">0</div>
                <div class="text-blue-100">{{ __('Verified Agents') }}</div>
            </div>
            <div class="text-center">
                <div class="text-4xl lg:text-5xl font-bold mb-2" data-counter="{{ $stats->total_developers ?? 180 }}">0</div>
                <div class="text-blue-100">{{ __('Developers') }}</div>
            </div>
            <div class="text-center">
                <div class="text-4xl lg:text-5xl font-bold mb-2" data-counter="{{ $stats->cities_covered ?? 45 }}">0</div>
                <div class="text-blue-100">{{ __('Cities Covered') }}</div>
            </div>
        </div>
        
        <!-- Trust Badges -->
        <div class="flex flex-wrap justify-center gap-6 mt-12">
            <div class="flex items-center bg-white/10 backdrop-blur-md rounded-full px-4 py-2 border border-white/20">
                <i class="fas fa-shield-alt text-green-400 mr-2"></i>
                <span class="text-sm">{{ __('Secure Transactions') }}</span>
            </div>
            <div class="flex items-center bg-white/10 backdrop-blur-md rounded-full px-4 py-2 border border-white/20">
                <i class="fas fa-certificate text-yellow-400 mr-2"></i>
                <span class="text-sm">{{ __('Verified Listings') }}</span>
            </div>
            <div class="flex items-center bg-white/10 backdrop-blur-md rounded-full px-4 py-2 border border-white/20">
                <i class="fas fa-award text-purple-400 mr-2"></i>
                <span class="text-sm">{{ __('Award Winning') }}</span>
            </div>
        </div>
    </div>
</section>

<!-- Featured Properties -->
<section class="py-20 bg-gray-50">
    <div class="container mx-auto px-6">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-12">
            <div>
                <h2 class="text-4xl font-bold text-gray-900 mb-4">
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
        
        <!-- Premium Property Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse($featuredProperties ?? [] as $property)
                <div class="bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden group">
                    <!-- Property Image -->
                    <div class="relative h-56 overflow-hidden">
                        <img src="{{ $property->featured_image ?? 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80' }}" 
                             alt="{{ $property->title }}" 
                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        
                        <!-- Badges -->
                        <div class="absolute top-4 left-4 flex gap-2">
                            @if($property->featured)
                                <span class="bg-yellow-500 text-white px-3 py-1 rounded-full text-xs font-bold">
                                    {{ __('Featured') }}
                                </span>
                            @endif
                            @if($property->premium)
                                <span class="bg-purple-600 text-white px-3 py-1 rounded-full text-xs font-bold">
                                    {{ __('Premium') }}
                                </span>
                            @endif
                        </div>
                        
                        <!-- Quick Actions -->
                        <div class="absolute top-4 right-4 flex gap-2">
                            <button class="bg-white/90 backdrop-blur-sm p-2 rounded-full hover:bg-white transition-colors">
                                <i class="far fa-heart text-gray-700 hover:text-red-500"></i>
                            </button>
                            <button class="bg-white/90 backdrop-blur-sm p-2 rounded-full hover:bg-white transition-colors">
                                <i class="fas fa-share-alt text-gray-700"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Property Details -->
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-3">
                            <h3 class="text-lg font-bold text-gray-900 line-clamp-1">{{ $property->title }}</h3>
                            <div class="text-right">
                                <div class="text-xl font-bold text-blue-600">
                                    ${{ number_format($property->price ?? 0) }}
                                </div>
                                @if($property->listing_type)
                                    <div class="text-xs text-gray-500">{{ ucfirst($property->listing_type) }}</div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="flex items-center text-gray-600 text-sm mb-3">
                            <i class="fas fa-map-marker-alt mr-2"></i>
                            <span class="line-clamp-1">{{ $property->address ?? $property->city ?? 'Location' }}</span>
                        </div>
                        
                        <!-- Property Specs -->
                        <div class="flex items-center gap-4 text-sm text-gray-600 mb-4">
                            @if($property->bedrooms)
                                <div class="flex items-center">
                                    <i class="fas fa-bed mr-1"></i>
                                    <span>{{ $property->bedrooms }}</span>
                                </div>
                            @endif
                            @if($property->bathrooms)
                                <div class="flex items-center">
                                    <i class="fas fa-bath mr-1"></i>
                                    <span>{{ $property->bathrooms }}</span>
                                </div>
                            @endif
                            @if($property->area)
                                <div class="flex items-center">
                                    <i class="fas fa-ruler-combined mr-1"></i>
                                    <span>{{ $property->area }} {{ $property->area_unit ?? 'sqft' }}</span>
                                </div>
                            @endif
                        </div>
                        
                        <a href="{{ route('properties.show', $property->id) }}" 
                           class="block w-full bg-blue-600 text-white text-center py-2 rounded-lg hover:bg-blue-700 transition-colors">
                            {{ __('View Details') }}
                        </a>
                    </div>
                </div>
            @empty
                <!-- Sample Properties for Demo -->
                @for($i = 1; $i <= 4; $i++)
                    <div class="bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden group">
                        <div class="relative h-56 overflow-hidden">
                            <img src="https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" 
                                 alt="Luxury Property" 
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            <div class="absolute top-4 left-4 flex gap-2">
                                <span class="bg-yellow-500 text-white px-3 py-1 rounded-full text-xs font-bold">
                                    {{ __('Featured') }}
                                </span>
                            </div>
                        </div>
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-2">Luxury Villa {{ $i }}</h3>
                            <div class="text-xl font-bold text-blue-600 mb-3">${{ number_format(500000 * $i) }}</div>
                            <div class="flex items-center text-gray-600 text-sm mb-4">
                                <i class="fas fa-map-marker-alt mr-2"></i>
                                <span>Downtown District</span>
                            </div>
                            <div class="flex items-center gap-4 text-sm text-gray-600 mb-4">
                                <div class="flex items-center">
                                    <i class="fas fa-bed mr-1"></i>
                                    <span>{{ 3 + $i }}</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-bath mr-1"></i>
                                    <span>{{ 2 + $i }}</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-ruler-combined mr-1"></i>
                                    <span>{{ 2000 * $i }} sqft</span>
                                </div>
                            </div>
                            <button class="block w-full bg-blue-600 text-white text-center py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                {{ __('View Details') }}
                            </button>
                        </div>
                    </div>
                @endfor
            @endforelse
        </div>
    </div>
</section>

<!-- Browse by Property Type -->
<section class="py-20 bg-white">
    <div class="container mx-auto px-6">
        <div class="text-center mb-12">
            <h2 class="text-4xl font-bold text-gray-900 mb-4">
                {{ __('Browse by Property Type') }}
            </h2>
            <p class="text-gray-600 text-lg max-w-2xl mx-auto">
                {{ __('Find exactly what you are looking for with our comprehensive property categories') }}
            </p>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
            @forelse($propertyTypes ?? [] as $type)
                <a href="{{ route('properties.index', ['type' => $type->slug]) }}" 
                   class="bg-gray-50 rounded-2xl p-6 text-center hover:bg-blue-50 hover:shadow-lg transition-all group">
                    <div class="w-16 h-16 bg-{{ $type->color ?? 'blue' }}-100 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:bg-{{ $type->color ?? 'blue' }}-600 transition-colors">
                        <i class="fas fa-{{ $type->icon ?? 'home' }} text-2xl text-{{ $type->color ?? 'blue' }}-600 group-hover:text-white"></i>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-1">{{ $type->name }}</h3>
                    <p class="text-sm text-gray-500">{{ $type->properties_count ?? 0 }}+</p>
                </a>
            @empty
                <!-- Default Property Types for Demo -->
                @php
                    $defaultTypes = [
                        ['slug' => 'apartment', 'name' => __('Apartments'), 'icon' => 'building', 'color' => 'blue', 'count' => 2450],
                        ['slug' => 'villa', 'name' => __('Villas'), 'icon' => 'home', 'color' => 'green', 'count' => 1200],
                        ['slug' => 'commercial', 'name' => __('Commercial'), 'icon' => 'store', 'color' => 'purple', 'count' => 850],
                        ['slug' => 'office', 'name' => __('Offices'), 'icon' => 'briefcase', 'color' => 'orange', 'count' => 650],
                        ['slug' => 'land', 'name' => __('Land'), 'icon' => 'mountain', 'color' => 'yellow', 'count' => 420],
                        ['slug' => 'off-plan', 'name' => __('New Projects'), 'icon' => 'hard-hat', 'color' => 'red', 'count' => 180]
                    ];
                @endphp
                
                @foreach($defaultTypes as $type)
                    <a href="{{ route('properties.index', ['type' => $type['slug']]) }}" 
                       class="bg-gray-50 rounded-2xl p-6 text-center hover:bg-blue-50 hover:shadow-lg transition-all group">
                        <div class="w-16 h-16 bg-{{ $type['color'] }}-100 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:bg-{{ $type['color'] }}-600 transition-colors">
                            <i class="fas fa-{{ $type['icon'] }} text-2xl text-{{ $type['color'] }}-600 group-hover:text-white"></i>
                        </div>
                        <h3 class="font-bold text-gray-900 mb-1">{{ $type['name'] }}</h3>
                        <p class="text-sm text-gray-500">{{ $type['count'] }}+</p>
                    </a>
                @endforeach
            @endforelse
        </div>
    </div>
</section>

<!-- Top Real Estate Agents -->
<section class="py-20 bg-gray-50">
    <div class="container mx-auto px-6">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-12">
            <div>
                <h2 class="text-4xl font-bold text-gray-900 mb-4">
                    {{ __('Top Real Estate Agents') }}
                </h2>
                <p class="text-gray-600 text-lg max-w-2xl">
                    {{ __('Connect with our verified and experienced agents for expert guidance') }}
                </p>
            </div>
            <a href="{{ route('agents.directory') }}" class="mt-4 md:mt-0 text-blue-600 font-bold flex items-center hover:text-blue-800 transition-colors">
                {{ __('View All Agents') }} <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @forelse($topAgents ?? [] as $agent)
                <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all p-6">
                    <!-- Agent Profile -->
                    <div class="flex items-center mb-4">
                        <div class="relative">
                            <img src="{{ $agent->profile_photo ?? 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&auto=format&fit=crop&w=150&q=80' }}" 
                                 alt="{{ $agent->name }}" 
                                 class="w-16 h-16 rounded-full object-cover">
                            @if($agent->verified)
                                <div class="absolute -bottom-1 -right-1 bg-green-500 w-5 h-5 rounded-full border-2 border-white flex items-center justify-center">
                                    <i class="fas fa-check text-white text-xs"></i>
                                </div>
                            @endif
                        </div>
                        <div class="ml-4">
                            <h3 class="font-bold text-gray-900">{{ $agent->name }}</h3>
                            <div class="flex items-center text-sm text-gray-600">
                                <div class="flex text-yellow-400 mr-1">
                                    @for($j = 1; $j <= round($agent->rating ?? 4.5); $j++)
                                        <i class="fas fa-star text-xs"></i>
                                    @endfor
                                    @if($agent->rating && $agent->rating < 5)
                                        <i class="fas fa-star-half-alt text-xs"></i>
                                    @endif
                                </div>
                                <span>({{ $agent->rating ?? '4.5' }})</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Agent Stats -->
                    <div class="space-y-2 mb-4">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">{{ __('Active Listings') }}</span>
                            <span class="font-bold text-gray-900">{{ $agent->active_listings ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">{{ __('Properties Sold') }}</span>
                            <span class="font-bold text-gray-900">{{ $agent->properties_sold ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">{{ __('Experience') }}</span>
                            <span class="font-bold text-gray-900">{{ $agent->experience_years ?? 0 }} {{ __('Years') }}</span>
                        </div>
                    </div>
                    
                    <a href="{{ route('agents.show', $agent->id) }}" class="block w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition-colors text-center">
                        {{ __('View Profile') }}
                    </a>
                </div>
            @empty
                <!-- Sample Agents for Demo -->
                @for($i = 1; $i <= 4; $i++)
                    <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all p-6">
                        <div class="flex items-center mb-4">
                            <div class="relative">
                                <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&auto=format&fit=crop&w=150&q=80" 
                                     alt="Agent {{ $i }}" 
                                     class="w-16 h-16 rounded-full object-cover">
                                <div class="absolute -bottom-1 -right-1 bg-green-500 w-5 h-5 rounded-full border-2 border-white flex items-center justify-center">
                                    <i class="fas fa-check text-white text-xs"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h3 class="font-bold text-gray-900">Sarah Johnson</h3>
                                <div class="flex items-center text-sm text-gray-600">
                                    <div class="flex text-yellow-400 mr-1">
                                        @for($j = 1; $j <= 5; $j++)
                                            <i class="fas fa-star text-xs"></i>
                                        @endfor
                                    </div>
                                    <span>(4.9)</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="space-y-2 mb-4">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">{{ __('Active Listings') }}</span>
                                <span class="font-bold text-gray-900">{{ 25 + $i * 5 }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">{{ __('Properties Sold') }}</span>
                                <span class="font-bold text-gray-900">{{ 100 + $i * 20 }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">{{ __('Experience') }}</span>
                                <span class="font-bold text-gray-900">{{ 3 + $i }} {{ __('Years') }}</span>
                            </div>
                        </div>
                        
                        <button class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition-colors">
                            {{ __('View Profile') }}
                        </button>
                    </div>
                @endfor
            @endforelse
        </div>
    </div>
</section>

<!-- Featured Developers -->
<section class="py-20 bg-white">
    <div class="container mx-auto px-6">
        <div class="text-center mb-12">
            <h2 class="text-4xl font-bold text-gray-900 mb-4">
                {{ __('Featured Developers') }}
            </h2>
            <p class="text-gray-600 text-lg max-w-2xl mx-auto">
                {{ __('Partner with leading developers for premium projects and investments') }}
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($featuredDevelopers ?? [] as $developer)
                <div class="bg-gray-50 rounded-2xl p-8 hover:shadow-lg transition-all">
                    <!-- Developer Logo -->
                    <div class="w-20 h-20 bg-white rounded-2xl flex items-center justify-center mb-6 shadow-sm">
                        @if($developer->logo)
                            <img src="{{ $developer->logo }}" alt="{{ $developer->name }}" class="w-full h-full object-cover rounded-xl">
                        @else
                            <i class="fas fa-building text-3xl text-blue-600"></i>
                        @endif
                    </div>
                    
                    <h3 class="text-xl font-bold text-gray-900 mb-3">{{ $developer->name }}</h3>
                    <p class="text-gray-600 mb-4">
                        {{ $developer->description ?? __('Leading real estate developer specializing in luxury residential and commercial projects.') }}
                    </p>
                    
                    <div class="space-y-2 mb-6">
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-building mr-2 text-blue-600"></i>
                            <span>{{ $developer->active_projects ?? 0 }} {{ __('Active Projects') }}</span>
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-check-circle mr-2 text-green-600"></i>
                            <span>{{ $developer->certified ? __('Certified & Verified') : __('Registered') }}</span>
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-award mr-2 text-yellow-600"></i>
                            <span>{{ $developer->award_winning ? __('Award Winning') : __('Established') }}</span>
                        </div>
                    </div>
                    
                    <a href="{{ route('developers.show', $developer->id) }}" class="block w-full bg-gray-900 text-white py-2 rounded-lg hover:bg-gray-800 transition-colors text-center">
                        {{ __('View Projects') }}
                    </a>
                </div>
            @empty
                <!-- Sample Developers for Demo -->
                @for($i = 1; $i <= 3; $i++)
                    <div class="bg-gray-50 rounded-2xl p-8 hover:shadow-lg transition-all">
                        <div class="w-20 h-20 bg-white rounded-2xl flex items-center justify-center mb-6 shadow-sm">
                            <i class="fas fa-building text-3xl text-blue-600"></i>
                        </div>
                        
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Premium Developer {{ $i }}</h3>
                        <p class="text-gray-600 mb-4">
                            {{ __('Leading real estate developer specializing in luxury residential and commercial projects with over 20 years of experience.') }}
                        </p>
                        
                        <div class="space-y-2 mb-6">
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-building mr-2 text-blue-600"></i>
                                <span>{{ 15 + $i * 5 }} {{ __('Active Projects') }}</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-check-circle mr-2 text-green-600"></i>
                                <span>{{ __('Certified & Verified') }}</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-award mr-2 text-yellow-600"></i>
                                <span>{{ __('Award Winning') }}</span>
                            </div>
                        </div>
                        
                        <button class="w-full bg-gray-900 text-white py-2 rounded-lg hover:bg-gray-800 transition-colors">
                            {{ __('View Projects') }}
                        </button>
                    </div>
                @endfor
            @endforelse
        </div>
    </div>
</section>

<!-- Why Choose Us -->
<section class="py-20 bg-gradient-to-r from-blue-600 to-indigo-700 text-white">
    <div class="container mx-auto px-6">
        <div class="text-center mb-12">
            <h2 class="text-4xl font-bold mb-4">
                {{ __('Why Choose Our Platform') }}
            </h2>
            <p class="text-blue-100 text-lg max-w-2xl mx-auto">
                {{ __('Experience the difference with our comprehensive real estate ecosystem') }}
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="text-center">
                <div class="w-20 h-20 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-shield-alt text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">{{ __('Verified Listings') }}</h3>
                <p class="text-blue-100">{{ __('Every property is verified by our expert team for authenticity and accuracy') }}</p>
            </div>
            
            <div class="text-center">
                <div class="w-20 h-20 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-user-tie text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">{{ __('Trusted Agents') }}</h3>
                <p class="text-blue-100">{{ __('Work with certified professionals who understand your needs perfectly') }}</p>
            </div>
            
            <div class="text-center">
                <div class="w-20 h-20 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-lock text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">{{ __('Secure Platform') }}</h3>
                <p class="text-blue-100">{{ __('Bank-level security for all transactions and personal information') }}</p>
            </div>
            
            <div class="text-center">
                <div class="w-20 h-20 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-chart-line text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">{{ __('Market Insights') }}</h3>
                <p class="text-blue-100">{{ __('Real-time data and analytics to make informed decisions') }}</p>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials -->
<section class="py-20 bg-gray-50">
    <div class="container mx-auto px-6">
        <div class="text-center mb-12">
            <h2 class="text-4xl font-bold text-gray-900 mb-4">
                {{ __('What Our Clients Say') }}
            </h2>
            <p class="text-gray-600 text-lg max-w-2xl mx-auto">
                {{ __('Real stories from real people who found their dream properties') }}
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @for($i = 1; $i <= 3; $i++)
                <div class="bg-white rounded-2xl shadow-lg p-8">
                    <!-- Rating -->
                    <div class="flex text-yellow-400 mb-4">
                        @for($j = 1; $j <= 5; $j++)
                            <i class="fas fa-star"></i>
                        @endfor
                    </div>
                    
                    <!-- Testimonial -->
                    <p class="text-gray-700 mb-6 italic">
                        "{{ __('Excellent service! Found my dream home within 2 weeks. The agent was very professional and understood exactly what I was looking for.') }}"
                    </p>
                    
                    <!-- Client Info -->
                    <div class="flex items-center">
                        <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-4.0.3&auto=format&fit=crop&w=150&q=80" 
                             alt="Client {{ $i }}" 
                             class="w-12 h-12 rounded-full object-cover mr-4">
                        <div>
                            <h4 class="font-bold text-gray-900">Michael Chen</h4>
                            <p class="text-sm text-gray-600">{{ __('Property Buyer') }}</p>
                        </div>
                    </div>
                </div>
            @endfor
        </div>
    </div>
</section>

<!-- Strong Call To Action -->
<section class="py-24 bg-gradient-to-r from-gray-900 to-gray-800 text-white relative overflow-hidden">
    <div class="absolute inset-0">
        <div class="absolute inset-0 bg-gradient-to-r from-blue-600/20 to-purple-600/20"></div>
    </div>
    
    <div class="container mx-auto px-6 relative z-10">
        <div class="text-center mb-16">
            <h2 class="text-4xl lg:text-5xl font-bold mb-6">
                {{ __('Ready to Get Started?') }}
            </h2>
            <p class="text-xl text-gray-300 max-w-3xl mx-auto">
                {{ __('Join thousands of satisfied clients and agents who trust our platform for their real estate needs') }}
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto">
            <div class="bg-white/10 backdrop-blur-md rounded-2xl p-8 text-center hover:bg-white/20 transition-all">
                <div class="w-16 h-16 bg-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-plus text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-4">{{ __('List Your Property') }}</h3>
                <p class="text-gray-300 mb-6">{{ __('Reach thousands of potential buyers and renters') }}</p>
                <button class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition-colors">
                    {{ __('Get Started') }}
                </button>
            </div>
            
            <div class="bg-white/10 backdrop-blur-md rounded-2xl p-8 text-center hover:bg-white/20 transition-all">
                <div class="w-16 h-16 bg-green-600 rounded-2xl flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-user-tie text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-4">{{ __('Become an Agent') }}</h3>
                <p class="text-gray-300 mb-6">{{ __('Grow your business with our powerful tools') }}</p>
                <a href="{{ route('register', ['type' => 'agent']) }}" class="block w-full bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 transition-colors text-center">
                    {{ __('Join Now') }}
                </a>
            </div>
            
            <div class="bg-white/10 backdrop-blur-md rounded-2xl p-8 text-center hover:bg-white/20 transition-all">
                <div class="w-16 h-16 bg-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-handshake text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-4">{{ __('Partner as Developer') }}</h3>
                <p class="text-gray-300 mb-6">{{ __('Showcase your projects to premium audience') }}</p>
                <a href="{{ route('contact') }}" class="block w-full bg-purple-600 text-white py-3 rounded-lg hover:bg-purple-700 transition-colors text-center">
                    {{ __('Contact Us') }}
                </a>
            </div>
        </div>
    </div>
</section>

<!-- JavaScript for Animated Counters -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animated Counter Function
    function animateCounter(element, target) {
        let current = 0;
        const increment = target / 100;
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            element.textContent = Math.floor(current).toLocaleString();
        }, 20);
    }
    
    // Intersection Observer for Counter Animation
    const counterElements = document.querySelectorAll('[data-counter]');
    const observerOptions = {
        threshold: 0.5,
        rootMargin: '0px'
    };
    
    const counterObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && !entry.target.classList.contains('animated')) {
                const target = parseInt(entry.target.getAttribute('data-counter'));
                animateCounter(entry.target, target);
                entry.target.classList.add('animated');
            }
        });
    }, observerOptions);
    
    counterElements.forEach(element => {
        counterObserver.observe(element);
    });
    
    // Property Type Tab Switching
    const propertyTabs = document.querySelectorAll('.property-type-tab');
    propertyTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            // Remove active class from all tabs
            propertyTabs.forEach(t => {
                t.classList.remove('bg-blue-600', 'text-white');
                t.classList.add('bg-gray-100', 'text-gray-700');
            });
            
            // Add active class to clicked tab
            this.classList.remove('bg-gray-100', 'text-gray-700');
            this.classList.add('bg-blue-600', 'text-white');
        });
    });
    
    // Smooth Scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});
</script>

@endsection

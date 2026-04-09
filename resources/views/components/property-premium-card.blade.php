@props(['property'])

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden hover:shadow-xl transition-all duration-300 group transform hover:-translate-y-1">
    <div class="relative">
        <!-- Property Image -->
        <div class="aspect-w-16 aspect-h-9 overflow-hidden">
            @if($property->media && $property->media->where('media_type', 'image')->count() > 0)
                <img src="{{ $property->media->where('media_type', 'image')->first()->url }}" 
                     alt="{{ $property->title }}" 
                     class="w-full h-64 object-cover transform group-hover:scale-105 transition-transform duration-500">
            @else
                <div class="w-full h-64 bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                    <i class="fas fa-building text-gray-400 text-6xl"></i>
                </div>
            @endif
        </div>
        
        <!-- Premium Badge -->
        <div class="absolute top-4 left-4">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-gradient-to-r from-yellow-400 to-orange-500 text-white shadow-lg">
                <i class="fas fa-crown mr-1"></i>
                Premium
            </span>
        </div>
        
        <!-- Status & Price -->
        <div class="absolute top-4 right-4 flex flex-col space-y-2">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-white/90 text-blue-600 shadow-sm backdrop-blur-sm">
                For {{ ucfirst($property->listing_type) }}
            </span>
            @if($property->price)
                <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-3 py-1 rounded-full text-sm font-bold shadow-lg">
                    {{ number_format($property->price) }} {{ $property->currency ?? 'USD' }}
                </div>
            @endif
        </div>
    </div>
    
    <!-- Property Content -->
    <div class="p-6">
        <!-- Title -->
        <h3 class="text-xl font-bold text-gray-900 mb-2 line-clamp-1 group-hover:text-blue-600 transition-colors">
            {{ $property->title }}
        </h3>
        
        <!-- Location -->
        <div class="flex items-center text-gray-600 text-sm mb-4">
            <i class="fas fa-map-marker-alt mr-2 text-blue-500"></i>
            <span class="line-clamp-1">
                {{ $property->address ?? 'Location not specified' }}
            </span>
        </div>
        
        <!-- Property Features -->
        <div class="grid grid-cols-3 gap-4 mb-4">
            @if($property->bedrooms)
                <div class="flex items-center text-gray-700">
                    <i class="fas fa-bed mr-2 text-blue-500"></i>
                    <span class="text-sm">{{ $property->bedrooms }} Beds</span>
                </div>
            @endif
            @if($property->bathrooms)
                <div class="flex items-center text-gray-700">
                    <i class="fas fa-bath mr-2 text-blue-500"></i>
                    <span class="text-sm">{{ $property->bathrooms }} Baths</span>
                </div>
            @endif
            @if($property->area)
                <div class="flex items-center text-gray-700">
                    <i class="fas fa-ruler-combined mr-2 text-blue-500"></i>
                    <span class="text-sm">{{ $property->area }} m²</span>
                </div>
            @endif
        </div>
        
        <!-- Description -->
        <p class="text-gray-600 text-sm mb-4 line-clamp-2">
            {{ $property->description ?? 'No description available' }}
        </p>
        
        <!-- Action Buttons -->
        <div class="flex space-x-3">
            <a href="{{ route('properties.show', $property->id) }}" 
               class="flex-1 bg-gradient-to-r from-blue-600 to-purple-600 text-white text-center py-2 px-4 rounded-lg font-semibold hover:from-blue-700 hover:to-purple-700 transition-all duration-300 transform hover:scale-105">
                View Details
            </a>
            <button class="p-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors group">
                <i class="far fa-heart text-gray-400 group-hover:text-red-500 transition-colors"></i>
            </button>
        </div>
    </div>
</div>

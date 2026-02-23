@extends('layouts.app')

@section('title', 'My Favorites - Shared Properties')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 mb-8">
            <div class="text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full mb-4">
                    <i class="fas fa-heart text-white text-2xl"></i>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $user->name }}'s Favorite Properties</h1>
                <p class="text-gray-600 mb-6">Discover {{ $total_count }} handpicked properties that caught my eye</p>
                
                <!-- Share URL -->
                <div class="bg-gray-50 rounded-xl p-4 max-w-md mx-auto">
                    <div class="flex items-center justify-between">
                        <input type="text" value="{{ $share_url }}" readonly 
                               class="bg-transparent flex-1 text-sm text-gray-600 outline-none" 
                               id="shareUrl">
                        <button onclick="copyShareUrl()" 
                                class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:from-blue-600 hover:to-blue-700 transition-all duration-300">
                            <i class="fas fa-copy mr-2"></i>Copy Link
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        @if($total_count > 0)
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02]">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-100 text-sm font-medium mb-1">Total Properties</p>
                            <p class="text-3xl font-bold">{{ $total_count }}</p>
                        </div>
                        <div class="bg-white/20 rounded-full p-3">
                            <i class="fas fa-building text-2xl"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02]">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-100 text-sm font-medium mb-1">Property Types</p>
                            <p class="text-3xl font-bold">{{ $favorites->pluck('property_type')->unique()->count() }}</p>
                        </div>
                        <div class="bg-white/20 rounded-full p-3">
                            <i class="fas fa-home text-2xl"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02]">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-purple-100 text-sm font-medium mb-1">Locations</p>
                            <p class="text-3xl font-bold">{{ $favorites->pluck('location')->unique()->count() }}</p>
                        </div>
                        <div class="bg-white/20 rounded-full p-3">
                            <i class="fas fa-map-marker-alt text-2xl"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02]">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-orange-100 text-sm font-medium mb-1">Shared Today</p>
                            <p class="text-3xl font-bold">{{ date('M j') }}</p>
                        </div>
                        <div class="bg-white/20 rounded-full p-3">
                            <i class="fas fa-share-alt text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Properties Grid -->
        @if($total_count > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($favorites as $property)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                        <!-- Property Image -->
                        <div class="relative h-48 overflow-hidden">
                            @if($property['image'])
                                <img src="{{ $property['image'] }}" 
                                     alt="{{ $property['title'] }}" 
                                     class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                                    <i class="fas fa-home text-gray-400 text-4xl"></i>
                                </div>
                            @endif
                            
                            <!-- Price Badge -->
                            <div class="absolute top-4 left-4 bg-white/90 backdrop-blur-sm rounded-lg px-3 py-1 shadow-lg">
                                <span class="text-sm font-bold text-gray-900">{{ $property['price'] }}</span>
                            </div>
                        </div>

                        <!-- Property Details -->
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2">{{ $property['title'] }}</h3>
                            
                            <div class="flex items-center text-gray-600 text-sm mb-4">
                                <i class="fas fa-map-marker-alt mr-2 text-blue-500"></i>
                                <span>{{ $property['location'] }}</span>
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $property['property_type'] }}
                                </span>
                                
                                <a href="{{ $property['url'] }}" 
                                   class="text-blue-600 hover:text-blue-700 font-medium text-sm transition-colors">
                                    View Details →
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 rounded-full mb-6">
                    <i class="fas fa-heart text-gray-400 text-3xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No Favorites Yet</h3>
                <p class="text-gray-600 mb-6">{{ $user->name }} hasn't added any properties to their favorites yet.</p>
                <a href="{{ route('properties.index') }}" 
                   class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-6 py-3 rounded-xl font-medium hover:from-blue-600 hover:to-blue-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-[1.02]">
                    <i class="fas fa-search mr-2"></i>
                    Browse Properties
                </a>
            </div>
        @endif

        <!-- Footer -->
        <div class="mt-12 text-center text-gray-500 text-sm">
            <p>Shared by {{ $user->name }} via Real Estate Platform</p>
            <p class="mt-2">© {{ date('Y') }} All rights reserved</p>
        </div>
    </div>
</div>

<script>
function copyShareUrl() {
    const shareUrl = document.getElementById('shareUrl');
    shareUrl.select();
    document.execCommand('copy');
    
    // Show feedback
    const button = event.target.closest('button');
    const originalContent = button.innerHTML;
    button.innerHTML = '<i class="fas fa-check mr-2"></i>Copied!';
    button.classList.add('from-green-500', 'to-green-600');
    button.classList.remove('from-blue-500', 'to-blue-600');
    
    setTimeout(() => {
        button.innerHTML = originalContent;
        button.classList.remove('from-green-500', 'to-green-600');
        button.classList.add('from-blue-500', 'to-blue-600');
    }, 2000);
}
</script>
@endsection

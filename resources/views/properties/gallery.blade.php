@extends('layouts.app')

@section('title', 'Property Gallery')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Property Gallery</h1>
                    <p class="text-gray-600">{{ $property->title }} - Photo Gallery</p>
                </div>
                <a href="{{ route('properties.show', $property) }}" class="text-gray-600 hover:text-gray-800">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Property
                </a>
            </div>
        </div>

        <!-- Main Gallery -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <!-- Gallery Stats -->
            <div class="p-6 border-b">
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-6">
                        <span class="text-sm text-gray-600">
                            <i class="fas fa-images mr-2"></i>
                            {{ $media->count() }} Photos
                        </span>
                        <span class="text-sm text-gray-600">
                            <i class="fas fa-video mr-2"></i>
                            {{ $media->where('type', 'video')->count() }} Videos
                        </span>
                        <span class="text-sm text-gray-600">
                            <i class="fas fa-cube mr-2"></i>
                            {{ $media->where('type', '3d_tour')->count() }} 3D Tours
                        </span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <button onclick="toggleSlideshow()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-play mr-2"></i>
                            Slideshow
                        </button>
                        <button onclick="downloadAll()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                            <i class="fas fa-download mr-2"></i>
                            Download All
                        </button>
                    </div>
                </div>
            </div>

            <!-- Gallery Grid -->
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse ($media as $item)
                        <div class="gallery-item relative group cursor-pointer" onclick="openLightbox({{ $loop->index }})">
                            <div class="aspect-w-16 aspect-h-12 bg-gray-200 rounded-lg overflow-hidden">
                                @if($item->type === 'image')
                                    <img src="{{ $item->url }}" alt="{{ $item->caption ?? $property->title }}" 
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                @elseif($item->type === 'video')
                                    <div class="w-full h-full bg-gray-900 flex items-center justify-center relative">
                                        @if($item->thumbnail)
                                            <img src="{{ $item->thumbnail }}" alt="" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full bg-gray-800 flex items-center justify-center">
                                                <i class="fas fa-video text-gray-600 text-4xl"></i>
                                            </div>
                                        @endif
                                        <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center">
                                            <div class="bg-white rounded-full p-3">
                                                <i class="fas fa-play text-gray-900 ml-1"></i>
                                            </div>
                                        </div>
                                    </div>
                                @elseif($item->type === '3d_tour')
                                    <div class="w-full h-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                                        <div class="text-center text-white">
                                            <i class="fas fa-cube text-4xl mb-2"></i>
                                            <p class="text-sm font-medium">3D Tour</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Overlay -->
                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all duration-300 rounded-lg flex items-end">
                                <div class="p-4 text-white transform translate-y-full group-hover:translate-y-0 transition-transform duration-300">
                                    <p class="text-sm font-medium">{{ $item->caption ?? 'Property Media' }}</p>
                                    <p class="text-xs opacity-75">{{ $item->created_at->format('M j, Y') }}</p>
                                </div>
                            </div>
                            
                            <!-- Type Badge -->
                            <div class="absolute top-2 right-2">
                                <span class="bg-black bg-opacity-60 text-white px-2 py-1 rounded text-xs">
                                    @if($item->type === 'image')
                                        Photo
                                    @elseif($item->type === 'video')
                                        Video
                                    @elseif($item->type === '3d_tour')
                                        3D Tour
                                    @endif
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-12">
                            <i class="fas fa-images text-6xl text-gray-300 mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No Media Available</h3>
                            <p class="text-gray-500">This property doesn't have any photos or videos yet.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Virtual Tour Section -->
            @if($virtualTours->isNotEmpty())
                <div class="border-t p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Virtual Tours</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach ($virtualTours as $tour)
                            <div class="border rounded-lg overflow-hidden">
                                <div class="aspect-w-16 aspect-h-9 bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                                    <div class="text-center text-white">
                                        <i class="fas fa-cube text-5xl mb-3"></i>
                                        <h4 class="text-lg font-medium">{{ $tour->title }}</h4>
                                        <p class="text-sm opacity-75">{{ $tour->description }}</p>
                                    </div>
                                </div>
                                <div class="p-4">
                                    <button onclick="startVirtualTour({{ $tour->id }})" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                        <i class="fas fa-play mr-2"></i>
                                        Start Virtual Tour
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Floor Plans Section -->
            @if($floorPlans->isNotEmpty())
                <div class="border-t p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Floor Plans</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach ($floorPlans as $plan)
                            <div class="border rounded-lg overflow-hidden">
                                <div class="aspect-w-16 aspect-h-12 bg-gray-200">
                                    @if($plan->image)
                                        <img src="{{ $plan->image }}" alt="{{ $plan->title }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                            <i class="fas fa-floor-plan text-gray-400 text-3xl"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="p-4">
                                    <h4 class="font-medium text-gray-800 mb-1">{{ $plan->title }}</h4>
                                    <p class="text-sm text-gray-600 mb-2">{{ $plan->bedrooms }} beds, {{ $plan->bathrooms }} baths</p>
                                    <p class="text-sm text-gray-600 mb-3">{{ $plan->square_feet }} sqft</p>
                                    <button onclick="viewFloorPlan({{ $plan->id }})" class="w-full bg-gray-600 text-white px-3 py-2 rounded hover:bg-gray-700 transition-colors text-sm">
                                        View Details
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Lightbox Modal -->
<div id="lightboxModal" class="fixed inset-0 bg-black bg-opacity-90 flex items-center justify-center z-50 hidden">
    <div class="relative max-w-6xl mx-auto p-4">
        <!-- Close Button -->
        <button onclick="closeLightbox()" class="absolute top-4 right-4 text-white text-2xl hover:text-gray-300 z-10">
            <i class="fas fa-times"></i>
        </button>
        
        <!-- Navigation -->
        <button onclick="previousItem()" class="absolute left-4 top-1/2 transform -translate-y-1/2 text-white text-3xl hover:text-gray-300">
            <i class="fas fa-chevron-left"></i>
        </button>
        <button onclick="nextItem()" class="absolute right-4 top-1/2 transform -translate-y-1/2 text-white text-3xl hover:text-gray-300">
            <i class="fas fa-chevron-right"></i>
        </button>
        
        <!-- Media Content -->
        <div id="lightboxContent" class="bg-white rounded-lg overflow-hidden">
            <!-- Content will be inserted here -->
        </div>
        
        <!-- Caption -->
        <div id="lightboxCaption" class="text-white text-center mt-4">
            <!-- Caption will be inserted here -->
        </div>
    </div>
</div>

<!-- Slideshow Modal -->
<div id="slideshowModal" class="fixed inset-0 bg-black bg-opacity-90 flex items-center justify-center z-50 hidden">
    <div class="relative max-w-6xl mx-auto p-4">
        <!-- Close Button -->
        <button onclick="closeSlideshow()" class="absolute top-4 right-4 text-white text-2xl hover:text-gray-300 z-10">
            <i class="fas fa-times"></i>
        </button>
        
        <!-- Slideshow Content -->
        <div id="slideshowContent" class="bg-white rounded-lg overflow-hidden">
            <!-- Content will be inserted here -->
        </div>
        
        <!-- Controls -->
        <div class="flex justify-center items-center space-x-4 mt-4">
            <button onclick="previousSlide()" class="text-white text-2xl hover:text-gray-300">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button onclick="togglePlayPause()" class="text-white text-2xl hover:text-gray-300">
                <i id="playPauseIcon" class="fas fa-pause"></i>
            </button>
            <button onclick="nextSlide()" class="text-white text-2xl hover:text-gray-300">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>
</div>

<script>
// Media data
const mediaItems = @json($media->toArray());
let currentIndex = 0;
let slideshowInterval = null;
let isPlaying = false;

function openLightbox(index) {
    currentIndex = index;
    updateLightbox();
    document.getElementById('lightboxModal').classList.remove('hidden');
}

function closeLightbox() {
    document.getElementById('lightboxModal').classList.add('hidden');
}

function updateLightbox() {
    const item = mediaItems[currentIndex];
    const content = document.getElementById('lightboxContent');
    const caption = document.getElementById('lightboxCaption');
    
    if (item.type === 'image') {
        content.innerHTML = '<img src="' + item.url + '" alt="' + (item.caption || '') + '" class="w-full h-auto max-h-screen object-contain">';
    } else if (item.type === 'video') {
        content.innerHTML = '<video controls class="w-full h-auto max-h-screen"><source src="' + item.url + '" type="video/mp4">Your browser does not support the video tag.</video>';
    } else if (item.type === '3d_tour') {
        content.innerHTML = '<div class="w-full h-96 bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center"><div class="text-center text-white"><i class="fas fa-cube text-6xl mb-3"></i><p class="text-lg">3D Tour</p><p class="text-sm opacity-75">' + (item.caption || 'Virtual Tour') + '</p></div></div>';
    }
    
    caption.textContent = item.caption || (item.type === 'image' ? 'Photo' : (item.type === 'video' ? 'Video' : '3D Tour'));
}

function previousItem() {
    currentIndex = (currentIndex - 1 + mediaItems.length) % mediaItems.length;
    updateLightbox();
}

function nextItem() {
    currentIndex = (currentIndex + 1) % mediaItems.length;
    updateLightbox();
}

function toggleSlideshow() {
    document.getElementById('slideshowModal').classList.remove('hidden');
    startSlideshow();
}

function closeSlideshow() {
    stopSlideshow();
    document.getElementById('slideshowModal').classList.add('hidden');
}

function startSlideshow() {
    isPlaying = true;
    updateSlideshow();
    slideshowInterval = setInterval(nextSlide, 3000);
    document.getElementById('playPauseIcon').className = 'fas fa-pause';
}

function stopSlideshow() {
    isPlaying = false;
    if (slideshowInterval) {
        clearInterval(slideshowInterval);
        slideshowInterval = null;
    }
    document.getElementById('playPauseIcon').className = 'fas fa-play';
}

function togglePlayPause() {
    if (isPlaying) {
        stopSlideshow();
    } else {
        startSlideshow();
    }
}

function updateSlideshow() {
    const item = mediaItems[currentIndex];
    const content = document.getElementById('slideshowContent');
    
    if (item.type === 'image') {
        content.innerHTML = '<img src="' + item.url + '" alt="' + (item.caption || '') + '" class="w-full h-auto max-h-screen object-contain">';
    } else {
        // Skip non-image items in slideshow
        nextSlide();
        return;
    }
}

function previousSlide() {
    currentIndex = (currentIndex - 1 + mediaItems.length) % mediaItems.length;
    updateSlideshow();
}

function nextSlide() {
    currentIndex = (currentIndex + 1) % mediaItems.length;
    updateSlideshow();
}

function downloadAll() {
    // Implement download functionality
    alert('Download feature coming soon!');
}

function startVirtualTour(tourId) {
    window.location.href = '/properties/virtual-tour/' + tourId;
}

function viewFloorPlan(planId) {
    window.location.href = '/properties/floor-plan/' + planId;
}

// Keyboard navigation
document.addEventListener('keydown', function(e) {
    if (!document.getElementById('lightboxModal').classList.contains('hidden')) {
        if (e.key === 'ArrowLeft') previousItem();
        if (e.key === 'ArrowRight') nextItem();
        if (e.key === 'Escape') closeLightbox();
    }
    
    if (!document.getElementById('slideshowModal').classList.contains('hidden')) {
        if (e.key === 'ArrowLeft') previousSlide();
        if (e.key === 'ArrowRight') nextSlide();
        if (e.key === 'Escape') closeSlideshow();
        if (e.key === ' ') {
            e.preventDefault();
            togglePlayPause();
        }
    }
});
</script>
@endsection

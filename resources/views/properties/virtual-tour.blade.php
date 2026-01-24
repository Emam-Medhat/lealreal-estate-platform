@extends('layouts.app')

@section('title', 'Virtual Tour')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Virtual Tour</h1>
                    <p class="text-gray-600">{{ $property->title }} - Interactive 3D Tour</p>
                </div>
                <a href="{{ route('properties.show', $property) }}" class="text-gray-600 hover:text-gray-800">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Property
                </a>
            </div>
        </div>

        <!-- Tour Container -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <!-- Tour Controls -->
            <div class="p-6 border-b">
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-4">
                        <h2 class="text-lg font-semibold text-gray-800">Interactive Tour</h2>
                        <div class="flex items-center space-x-2">
                            <button onclick="toggleFullscreen()" class="bg-gray-600 text-white px-3 py-2 rounded hover:bg-gray-700 transition-colors">
                                <i class="fas fa-expand"></i>
                            </button>
                            <button onclick="toggleVRMode()" class="bg-purple-600 text-white px-3 py-2 rounded hover:bg-purple-700 transition-colors">
                                <i class="fas fa-vr-cardboard"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-3">
                        <select onchange="changeTour(this.value)" class="px-3 py-2 border rounded-lg">
                            @foreach ($virtualTours as $tour)
                                <option value="{{ $tour->id }}" {{ $tour->id === $currentTour->id ? 'selected' : '' }}>
                                    {{ $tour->title }}
                                </option>
                            @endforeach
                        </select>
                        <button onclick="shareTour()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition-colors">
                            <i class="fas fa-share mr-2"></i>
                            Share
                        </button>
                    </div>
                </div>
            </div>

            <!-- 3D Tour Viewer -->
            <div class="relative bg-gray-900" style="height: 600px;">
                <!-- Tour Placeholder (Replace with actual 3D tour embed) -->
                <div id="tourViewer" class="w-full h-full flex items-center justify-center">
                    <div class="text-center text-white">
                        <i class="fas fa-cube text-6xl mb-4"></i>
                        <h3 class="text-xl font-medium mb-2">{{ $currentTour->title }}</h3>
                        <p class="text-gray-300 mb-4">{{ $currentTour->description }}</p>
                        <button onclick="startTour()" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-play mr-2"></i>
                            Start Tour
                        </button>
                    </div>
                </div>

                <!-- Tour Navigation Overlay -->
                <div id="tourControls" class="absolute bottom-4 left-4 right-4 bg-black bg-opacity-60 rounded-lg p-4 hidden">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center space-x-4">
                            <button onclick="previousRoom()" class="text-white hover:text-gray-300">
                                <i class="fas fa-chevron-left text-xl"></i>
                            </button>
                            <div class="text-white">
                                <span id="currentRoom">Living Room</span>
                                <span class="text-gray-300">|</span>
                                <span id="tourProgress">1 / 8</span>
                            </div>
                            <button onclick="nextRoom()" class="text-white hover:text-gray-300">
                                <i class="fas fa-chevron-right text-xl"></i>
                            </button>
                        </div>
                        
                        <div class="flex items-center space-x-3">
                            <button onclick="toggleAutoPlay()" class="text-white hover:text-gray-300">
                                <i id="autoPlayIcon" class="fas fa-play"></i>
                            </button>
                            <button onclick="toggleMute()" class="text-white hover:text-gray-300">
                                <i id="muteIcon" class="fas fa-volume-up"></i>
                            </button>
                            <button onclick="toggleInfo()" class="text-white hover:text-gray-300">
                                <i class="fas fa-info-circle"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Hotspots -->
                <div class="absolute top-1/2 left-1/4 transform -translate-x-1/2 -translate-y-1/2">
                    <button onclick="showHotspotInfo('kitchen')" class="bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center hover:bg-blue-700 pulse">
                        i
                    </button>
                </div>
                <div class="absolute top-1/3 right-1/3 transform translate-x-1/2 -translate-y-1/2">
                    <button onclick="showHotspotInfo('bedroom')" class="bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center hover:bg-blue-700 pulse">
                        i
                    </button>
                </div>
            </div>

            <!-- Tour Information -->
            <div class="p-6 border-b">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Tour Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <h4 class="font-medium text-gray-800 mb-2">Features</h4>
                        <ul class="text-sm text-gray-600 space-y-1">
                            <li><i class="fas fa-check text-green-500 mr-2"></i>360° panoramic views</li>
                            <li><i class="fas fa-check text-green-500 mr-2"></i>Interactive hotspots</li>
                            <li><i class="fas fa-check text-green-500 mr-2"></i>Room-by-room navigation</li>
                            <li><i class="fas fa-check text-green-500 mr-2"></i>HD quality imagery</li>
                        </ul>
                    </div>
                    
                    <div>
                        <h4 class="font-medium text-gray-800 mb-2">Rooms Included</h4>
                        <div class="flex flex-wrap gap-2">
                            <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs">Living Room</span>
                            <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs">Kitchen</span>
                            <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs">Master Bedroom</span>
                            <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs">Bathroom</span>
                            <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs">Balcony</span>
                        </div>
                    </div>
                    
                    <div>
                        <h4 class="font-medium text-gray-800 mb-2">Tour Stats</h4>
                        <div class="text-sm text-gray-600 space-y-1">
                            <div>Duration: ~5 minutes</div>
                            <div>Rooms: 8</div>
                            <div>Hotspots: 12</div>
                            <div>Views: {{ $currentTour->views ?? 0 }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Available Tours -->
            @if($virtualTours->count() > 1)
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Available Tours</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach ($virtualTours as $tour)
                            <div class="border rounded-lg p-4 hover:bg-gray-50 cursor-pointer {{ $tour->id === $currentTour->id ? 'border-blue-500 bg-blue-50' : '' }}" onclick="changeTour({{ $tour->id }})">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="font-medium text-gray-800">{{ $tour->title }}</h4>
                                        <p class="text-sm text-gray-600">{{ $tour->description }}</p>
                                    </div>
                                    @if($tour->id === $currentTour->id)
                                        <span class="bg-blue-600 text-white px-2 py-1 rounded text-xs">Current</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Property Summary -->
        <div class="bg-white rounded-lg shadow-sm p-6 mt-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Property Summary</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-800">${{ number_format($property->price, 0) }}</div>
                    <div class="text-sm text-gray-600">Price</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-800">{{ $property->bedrooms }}</div>
                    <div class="text-sm text-gray-600">Bedrooms</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-800">{{ $property->bathrooms }}</div>
                    <div class="text-sm text-gray-600">Bathrooms</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-800">{{ number_format($property->square_feet) }}</div>
                    <div class="text-sm text-gray-600">Square Feet</div>
                </div>
            </div>
            
            <div class="flex justify-center space-x-4 mt-6">
                <a href="{{ route('properties.show', $property) }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-home mr-2"></i>
                    View Property Details
                </a>
                <button onclick="scheduleTour()" class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-calendar mr-2"></i>
                    Schedule In-Person Tour
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Hotspot Info Modal -->
<div id="hotspotModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg p-6 max-w-md mx-4 w-full">
        <h3 id="hotspotTitle" class="text-lg font-semibold text-gray-800 mb-4"></h3>
        <div id="hotspotContent" class="text-gray-600 mb-4"></div>
        <button onclick="closeHotspotModal()" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 transition-colors">
            Close
        </button>
    </div>
</div>

<script>
let currentRoomIndex = 0;
let isAutoPlaying = false;
let isMuted = false;
let autoPlayInterval = null;

const rooms = [
    { name: 'Living Room', description: 'Spacious open-concept living area with natural light' },
    { name: 'Kitchen', description: 'Modern kitchen with stainless steel appliances and granite countertops' },
    { name: 'Master Bedroom', description: 'Large master suite with walk-in closet and en-suite bathroom' },
    { name: 'Bathroom', description: 'Luxurious bathroom with modern fixtures and finishes' },
    { name: 'Dining Room', description: 'Elegant dining space perfect for entertaining' },
    { name: 'Office', description: 'Home office with built-in shelving and natural light' },
    { name: 'Balcony', description: 'Private balcony with stunning city views' },
    { name: 'Guest Room', description: 'Comfortable guest room with ample closet space' }
];

function startTour() {
    document.getElementById('tourViewer').innerHTML = `
        <div class="w-full h-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
            <div class="text-center text-white">
                <i class="fas fa-cube text-8xl mb-4 animate-pulse"></i>
                <h3 class="text-2xl font-medium mb-2">Tour Loading...</h3>
                <p class="text-blue-100">Preparing your virtual experience</p>
            </div>
        </div>
    `;
    
    // Simulate loading
    setTimeout(() => {
        document.getElementById('tourViewer').innerHTML = `
            <div class="w-full h-full bg-cover bg-center" style="background-image: url('https://via.placeholder.com/1200x600/4F46E5/FFFFFF?text=Virtual+Tour+Room')">
                <div class="w-full h-full bg-black bg-opacity-30 flex items-center justify-center">
                    <div class="text-center text-white">
                        <h3 class="text-3xl font-medium mb-2">` + rooms[currentRoomIndex].name + `</h3>
                        <p class="text-xl text-blue-100">` + rooms[currentRoomIndex].description + `</p>
                    </div>
                </div>
            </div>
        `;
        document.getElementById('tourControls').classList.remove('hidden');
        updateTourProgress();
    }, 2000);
}

function changeTour(tourId) {
    window.location.href = '/properties/virtual-tour/' + tourId;
}

function previousRoom() {
    currentRoomIndex = (currentRoomIndex - 1 + rooms.length) % rooms.length;
    updateRoom();
}

function nextRoom() {
    currentRoomIndex = (currentRoomIndex + 1) % rooms.length;
    updateRoom();
}

function updateRoom() {
    const viewer = document.getElementById('tourViewer');
    viewer.innerHTML = `
        <div class="w-full h-full bg-cover bg-center transition-all duration-500" style="background-image: url('https://via.placeholder.com/1200x600/4F46E5/FFFFFF?text=` + rooms[currentRoomIndex].name.replace(' ', '+') + `')">
            <div class="w-full h-full bg-black bg-opacity-30 flex items-center justify-center">
                <div class="text-center text-white">
                    <h3 class="text-3xl font-medium mb-2">` + rooms[currentRoomIndex].name + `</h3>
                    <p class="text-xl text-blue-100">` + rooms[currentRoomIndex].description + `</p>
                </div>
            </div>
        </div>
    `;
    updateTourProgress();
}

function updateTourProgress() {
    document.getElementById('currentRoom').textContent = rooms[currentRoomIndex].name;
    document.getElementById('tourProgress').textContent = (currentRoomIndex + 1) + ' / ' + rooms.length;
}

function toggleAutoPlay() {
    isAutoPlaying = !isAutoPlaying;
    const icon = document.getElementById('autoPlayIcon');
    
    if (isAutoPlaying) {
        icon.className = 'fas fa-pause';
        autoPlayInterval = setInterval(nextRoom, 5000);
    } else {
        icon.className = 'fas fa-play';
        if (autoPlayInterval) {
            clearInterval(autoPlayInterval);
            autoPlayInterval = null;
        }
    }
}

function toggleMute() {
    isMuted = !isMuted;
    const icon = document.getElementById('muteIcon');
    icon.className = isMuted ? 'fas fa-volume-mute' : 'fas fa-volume-up';
}

function toggleInfo() {
    alert('Tour Info: Use arrow keys or buttons to navigate between rooms. Click on hotspots for more information.');
}

function toggleFullscreen() {
    const viewer = document.getElementById('tourViewer').parentElement;
    if (!document.fullscreenElement) {
        viewer.requestFullscreen();
    } else {
        document.exitFullscreen();
    }
}

function toggleVRMode() {
    alert('VR Mode requires a VR headset. This feature is coming soon!');
}

function shareTour() {
    if (navigator.share) {
        navigator.share({
            title: 'Virtual Tour - ' + '{{ $property->title }}',
            text: 'Check out this amazing property virtual tour!',
            url: window.location.href
        });
    } else {
        // Fallback
        navigator.clipboard.writeText(window.location.href);
        alert('Tour link copied to clipboard!');
    }
}

function showHotspotInfo(room) {
    const modal = document.getElementById('hotspotModal');
    const title = document.getElementById('hotspotTitle');
    const content = document.getElementById('hotspotContent');
    
    title.textContent = rooms.find(r => r.name.toLowerCase().includes(room))?.name || 'Room Information';
    content.textContent = rooms.find(r => r.name.toLowerCase().includes(room))?.description || 'Additional information about this room.';
    
    modal.classList.remove('hidden');
}

function closeHotspotModal() {
    document.getElementById('hotspotModal').classList.add('hidden');
}

function scheduleTour() {
    window.location.href = '/properties/' + {{ $property->id }} + '/tour';
}

// Keyboard navigation
document.addEventListener('keydown', function(e) {
    if (e.key === 'ArrowLeft') previousRoom();
    if (e.key === 'ArrowRight') nextRoom();
    if (e.key === ' ') {
        e.preventDefault();
        toggleAutoPlay();
    }
    if (e.key === 'm') toggleMute();
    if (e.key === 'f') toggleFullscreen();
    if (e.key === 'Escape') closeHotspotModal();
});
</script>
@endsection

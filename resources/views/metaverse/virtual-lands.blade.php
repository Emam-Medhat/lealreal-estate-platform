@extends('layouts.app')

@section('title', 'الأراضي الافتراضية')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="bg-gradient-to-r from-green-600 to-blue-600 rounded-lg p-8 mb-8 text-white">
        <h1 class="text-4xl font-bold mb-4">الأراضي الافتراضية</h1>
        <p class="text-xl opacity-90">استكشف واشتري الأراضي في العوالم الافتراضية</p>
        
        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-8">
            <div class="bg-white/20 backdrop-blur rounded-lg p-4">
                <div class="text-3xl font-bold">{{ $stats['total_lands'] }}</div>
                <div class="text-sm opacity-90">أراضي متاحة</div>
            </div>
            <div class="bg-white/20 backdrop-blur rounded-lg p-4">
                <div class="text-3xl font-bold">{{ $stats['for_sale'] }}</div>
                <div class="text-sm opacity-90">للبيع</div>
            </div>
            <div class="bg-white/20 backdrop-blur rounded-lg p-4">
                <div class="text-3xl font-bold">{{ $stats['prime_locations'] }}</div>
                <div class="text-sm opacity-90">مواقع مميزة</div>
            </div>
            <div class="bg-white/20 backdrop-blur rounded-lg p-4">
                <div class="text-3xl font-bold">{{ $stats['total_area'] }}</div>
                <div class="text-sm opacity-90">إجمالي المساحة</div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
        <h2 class="text-2xl font-bold mb-4">البحث والتصفية</h2>
        <form method="GET" action="{{ route('metaverse.lands.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">العالم الافتراضي</label>
                <select name="world_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="">كل العوالم</option>
                    @foreach($worlds as $world)
                        <option value="{{ $world->id }}" {{ request('world_id') == $world->id ? 'selected' : '' }}>
                            {{ $world->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">نوع الأرض</label>
                <select name="land_type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="">كل الأنواع</option>
                    <option value="residential" {{ request('land_type') == 'residential' ? 'selected' : '' }}>سكني</option>
                    <option value="commercial" {{ request('land_type') == 'commercial' ? 'selected' : '' }}>تجاري</option>
                    <option value="industrial" {{ request('land_type') == 'industrial' ? 'selected' : '' }}>صناعي</option>
                    <option value="mixed" {{ request('land_type') == 'mixed' ? 'selected' : '' }}>مختلط</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">السعر الأدنى</label>
                <input type="number" name="price_min" value="{{ request('price_min') }}" placeholder="0" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">السعر الأقصى</label>
                <input type="number" name="price_max" value="{{ request('price_max') }}" placeholder="1000000" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">المساحة الدنيا</label>
                <input type="number" name="area_min" value="{{ request('area_min') }}" placeholder="100" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">المساحة القصوى</label>
                <input type="number" name="area_max" value="{{ request('area_max') }}" placeholder="10000" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الميزات</label>
                <div class="space-y-2">
                    <label class="flex items-center">
                        <input type="checkbox" name="prime_location" value="1" {{ request('prime_location') ? 'checked' : '' }} class="mr-2">
                        <span class="text-sm">موقع مميز</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="waterfront" value="1" {{ request('waterfront') ? 'checked' : '' }} class="mr-2">
                        <span class="text-sm">على الماء</span>
                    </label>
                </div>
            </div>
            
            <div class="md:col-span-4">
                <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-md hover:bg-green-700 transition-colors">
                    بحث
                </button>
                <a href="{{ route('metaverse.lands.index') }}" class="ml-2 text-gray-600 hover:text-gray-800">
                    إعادة تعيين
                </a>
            </div>
        </form>
    </div>

    <!-- Lands Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        @forelse($lands as $land)
            <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                <!-- Land Image -->
                <div class="relative h-48 bg-gray-200">
                    <img src="{{ $land->getThumbnailUrl() }}" alt="{{ $land->title }}" class="w-full h-full object-cover">
                    @if($land->is_prime_location)
                        <div class="absolute top-2 right-2 bg-yellow-500 text-white px-2 py-1 rounded-full text-xs">
                            مميز
                        </div>
                    @endif
                    @if($land->is_waterfront)
                        <div class="absolute top-2 left-2 bg-blue-500 text-white px-2 py-1 rounded-full text-xs">
                            على الماء
                        </div>
                    @endif
                </div>
                
                <!-- Land Info -->
                <div class="p-4">
                    <h3 class="font-bold text-lg mb-2">{{ $land->title }}</h3>
                    <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ $land->description }}</p>
                    
                    <div class="flex items-center justify-between mb-3">
                        <div class="text-sm text-gray-500">
                            {{ $land->virtualWorld->name }}
                        </div>
                        <div class="text-sm text-gray-500">
                            {{ $land->getLandTypeTextAttribute() }}
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between mb-3">
                        <div class="text-xl font-bold text-green-600">
                            {{ $land->getFormattedPriceAttribute() }}
                        </div>
                        <div class="text-sm text-gray-500">
                            {{ $land->getFormattedAreaAttribute() }}
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                        <div>👁 {{ $land->view_count }}</div>
                        <div>💬 {{ $land->inquiry_count }}</div>
                        <div>💰 {{ $land->offer_count }}</div>
                    </div>
                    
                    <!-- Land Features -->
                    @if($land->zoning_types)
                        <div class="mb-3">
                            <div class="text-xs text-gray-500 mb-1">الاستخدامات المسموحة:</div>
                            <div class="flex flex-wrap gap-1">
                                @foreach(array_slice($land->zoning_types, 0, 3) as $type)
                                    <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs">
                                        {{ $type }}
                                    </span>
                                @endforeach
                                @if(count($land->zoning_types) > 3)
                                    <span class="text-gray-400 text-xs">+{{ count($land->zoning_types) - 3 }}</span>
                                @endif
                            </div>
                        </div>
                    @endif
                    
                    <div class="flex gap-2">
                        <a href="{{ route('metaverse.lands.show', $land) }}" 
                           class="flex-1 bg-green-600 text-white text-center py-2 rounded-md hover:bg-green-700 transition-colors">
                            عرض التفاصيل
                        </a>
                        @if($land->ownership_status === 'for_sale')
                            <a href="{{ route('metaverse.marketplace.land', $land) }}" 
                               class="flex-1 bg-blue-600 text-white text-center py-2 rounded-md hover:bg-blue-700 transition-colors">
                                شراء
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <div class="text-gray-500 text-lg">لم يتم العثور على أراضي</div>
                <p class="text-gray-400 mt-2">حاول تعديل معايير البحث</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($lands->hasPages())
        <div class="flex justify-center">
            {{ $lands->links() }}
        </div>
    @endif
</div>

<!-- Map View Toggle -->
<div class="fixed bottom-8 left-8">
    <button id="mapToggle" class="bg-blue-600 text-white p-4 rounded-full shadow-lg hover:bg-blue-700 transition-colors">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
        </svg>
    </button>
</div>

<!-- Quick Actions Floating Button -->
<div class="fixed bottom-8 right-8 flex flex-col gap-2">
    <a href="{{ route('metaverse.lands.create') }}" 
       class="bg-green-600 text-white p-4 rounded-full shadow-lg hover:bg-green-700 transition-colors">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
    </a>
    <a href="{{ route('metaverse.marketplace.index') }}" 
       class="bg-blue-600 text-white p-4 rounded-full shadow-lg hover:bg-blue-700 transition-colors">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
        </svg>
    </a>
</div>

<script>
// Map view functionality
document.getElementById('mapToggle').addEventListener('click', function() {
    // Toggle between grid and map view
    const gridView = document.querySelector('.grid');
    if (gridView.style.display === 'none') {
        gridView.style.display = 'grid';
        // Hide map
        const mapContainer = document.getElementById('mapContainer');
        if (mapContainer) {
            mapContainer.remove();
        }
    } else {
        gridView.style.display = 'none';
        // Show map (this would integrate with a mapping library)
        showMapView();
    }
});

function showMapView() {
    const mapContainer = document.createElement('div');
    mapContainer.id = 'mapContainer';
    mapContainer.className = 'fixed inset-0 bg-white z-50';
    mapContainer.innerHTML = `
        <div class="h-full relative">
            <button onclick="closeMapView()" class="absolute top-4 right-4 bg-red-500 text-white p-2 rounded-full z-10">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            <div class="h-full bg-gray-200 flex items-center justify-center">
                <div class="text-center">
                    <div class="text-6xl mb-4">🗺️</div>
                    <h3 class="text-2xl font-bold mb-2">خريط الأراضي الافتراضية</h3>
                    <p class="text-gray-600">سيتم عرض الأراضي على الخريط قريباً</p>
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(mapContainer);
}

function closeMapView() {
    const mapContainer = document.getElementById('mapContainer');
    if (mapContainer) {
        mapContainer.remove();
        document.querySelector('.grid').style.display = 'grid';
    }
}

// Real-time updates
setInterval(() => {
    // Update land availability
    fetch('/api/metaverse/lands/stats')
        .then(response => response.json())
        .then(data => {
            // Update stats in the UI
            console.log('Updated land stats:', data);
        });
}, 60000); // Update every minute
</script>
@endsection

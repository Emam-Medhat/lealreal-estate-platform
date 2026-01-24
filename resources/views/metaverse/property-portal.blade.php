@extends('layouts.app')

@section('title', 'بوابة العقارات الافتراضية')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="bg-gradient-to-r from-purple-600 to-blue-600 rounded-lg p-8 mb-8 text-white">
        <h1 class="text-4xl font-bold mb-4">بوابة العقارات الافتراضية</h1>
        <p class="text-xl opacity-90">استكشف واشتري وبيع العقارات في العوالم الافتراضية</p>
        
        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-8">
            <div class="bg-white/20 backdrop-blur rounded-lg p-4">
                <div class="text-3xl font-bold">{{ $stats['total_properties'] }}</div>
                <div class="text-sm opacity-90">عقارات متاحة</div>
            </div>
            <div class="bg-white/20 backdrop-blur rounded-lg p-4">
                <div class="text-3xl font-bold">{{ $stats['total_lands'] }}</div>
                <div class="text-sm opacity-90">أراضي للبيع</div>
            </div>
            <div class="bg-white/20 backdrop-blur rounded-lg p-4">
                <div class="text-3xl font-bold">{{ $stats['total_nfts'] }}</div>
                <div class="text-sm opacity-90">NFTs متاحة</div>
            </div>
            <div class="bg-white/20 backdrop-blur rounded-lg p-4">
                <div class="text-3xl font-bold">{{ number_format($stats['total_volume'], 2) }}</div>
                <div class="text-sm opacity-90">إجمالي المعاملات</div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
        <h2 class="text-2xl font-bold mb-4">البحث والتصفية</h2>
        <form method="GET" action="{{ route('metaverse.properties.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">العالم الافتراضي</label>
                <select name="world_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                    <option value="">كل العوالم</option>
                    @foreach($worlds as $world)
                        <option value="{{ $world->id }}" {{ request('world_id') == $world->id ? 'selected' : '' }}>
                            {{ $world->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">نوع العقار</label>
                <select name="property_type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                    <option value="">كل الأنواع</option>
                    <option value="residential" {{ request('property_type') == 'residential' ? 'selected' : '' }}>سكني</option>
                    <option value="commercial" {{ request('property_type') == 'commercial' ? 'selected' : '' }}>تجاري</option>
                    <option value="mixed" {{ request('property_type') == 'mixed' ? 'selected' : '' }}>مختلط</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">السعر الأدنى</label>
                <input type="number" name="price_min" value="{{ request('price_min') }}" placeholder="0" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">السعر الأقصى</label>
                <input type="number" name="price_max" value="{{ request('price_max') }}" placeholder="1000000" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
            </div>
            
            <div class="md:col-span-4">
                <button type="submit" class="bg-purple-600 text-white px-6 py-2 rounded-md hover:bg-purple-700 transition-colors">
                    بحث
                </button>
                <a href="{{ route('metaverse.properties.index') }}" class="ml-2 text-gray-600 hover:text-gray-800">
                    إعادة تعيين
                </a>
            </div>
        </form>
    </div>

    <!-- Properties Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
        @forelse($properties as $property)
            <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                <!-- Property Image -->
                <div class="relative h-48 bg-gray-200">
                    <img src="{{ $property->getThumbnailUrl() }}" alt="{{ $property->title }}" class="w-full h-full object-cover">
                    @if($property->is_nft)
                        <div class="absolute top-2 right-2 bg-purple-600 text-white px-2 py-1 rounded-full text-xs">
                            NFT
                        </div>
                    @endif
                    @if($property->is_featured)
                        <div class="absolute top-2 left-2 bg-yellow-500 text-white px-2 py-1 rounded-full text-xs">
                            مميز
                        </div>
                    @endif
                </div>
                
                <!-- Property Info -->
                <div class="p-4">
                    <h3 class="font-bold text-lg mb-2">{{ $property->title }}</h3>
                    <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ $property->description }}</p>
                    
                    <div class="flex items-center justify-between mb-3">
                        <div class="text-sm text-gray-500">
                            {{ $property->virtualWorld->name }}
                        </div>
                        <div class="text-sm text-gray-500">
                            {{ $property->getPropertyTypeTextAttribute() }}
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between mb-3">
                        <div class="text-xl font-bold text-purple-600">
                            {{ $property->getFormattedPriceAttribute() }}
                        </div>
                        @if($property->rating_average > 0)
                            <div class="flex items-center text-sm text-yellow-500">
                                ⭐ {{ number_format($property->rating_average, 1) }}
                            </div>
                        @endif
                    </div>
                    
                    <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                        <div>👁 {{ $property->view_count }}</div>
                        <div>❤️ {{ $property->like_count }}</div>
                        <div>👥 {{ $property->tours_count }}</div>
                    </div>
                    
                    <div class="flex gap-2">
                        <a href="{{ route('metaverse.properties.show', $property) }}" 
                           class="flex-1 bg-purple-600 text-white text-center py-2 rounded-md hover:bg-purple-700 transition-colors">
                            عرض التفاصيل
                        </a>
                        @if($property->is_for_sale)
                            <a href="{{ route('metaverse.marketplace.property', $property) }}" 
                               class="flex-1 bg-green-600 text-white text-center py-2 rounded-md hover:bg-green-700 transition-colors">
                                شراء
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <div class="text-gray-500 text-lg">لم يتم العثور على عقارات</div>
                <p class="text-gray-400 mt-2">حاول تعديل معايير البحث</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($properties->hasPages())
        <div class="flex justify-center">
            {{ $properties->links() }}
        </div>
    @endif
</div>

<!-- Quick Actions Floating Button -->
<div class="fixed bottom-8 right-8 flex flex-col gap-2">
    <a href="{{ route('metaverse.properties.create') }}" 
       class="bg-purple-600 text-white p-4 rounded-full shadow-lg hover:bg-purple-700 transition-colors">
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
// Real-time updates
setInterval(() => {
    // Update stats periodically
    fetch('/api/metaverse/stats')
        .then(response => response.json())
        .then(data => {
            // Update stats in the UI
            document.querySelector('.text-3xl').textContent = data.total_properties;
        });
}, 30000); // Update every 30 seconds

// Search functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function(e) {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                const form = e.target.closest('form');
                form.submit();
            }, 500);
        });
    }
});
</script>
@endsection

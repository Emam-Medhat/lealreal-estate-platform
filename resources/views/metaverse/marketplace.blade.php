@extends('layouts.app')

@section('title', 'سوق العقارات الافتراضية')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="bg-gradient-to-r from-orange-600 to-red-600 rounded-lg p-8 mb-8 text-white">
        <h1 class="text-4xl font-bold mb-4">سوق العقارات الافتراضية</h1>
        <p class="text-xl opacity-90">اشترِ وبيع العقارات والأراضي الافتراضية</p>
        
        <!-- Market Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-8">
            <div class="bg-white/20 backdrop-blur rounded-lg p-4">
                <div class="text-3xl font-bold">{{ $stats['active_listings'] }}</div>
                <div class="text-sm opacity-90">عروض نشطة</div>
            </div>
            <div class="bg-white/20 backdrop-blur rounded-lg p-4">
                <div class="text-3xl font-bold">{{ number_format($stats['total_volume'], 2) }}</div>
                <div class="text-sm opacity-90">إجمالي المعاملات</div>
            </div>
            <div class="bg-white/20 backdrop-blur rounded-lg p-4">
                <div class="text-3xl font-bold">{{ $stats['total_transactions'] }}</div>
                <div class="text-sm opacity-90">إجمالي المعاملات</div>
            </div>
            <div class="bg-white/20 backdrop-blur rounded-lg p-4">
                <div class="text-3xl font-bold">{{ $stats['average_price'] }}</div>
                <div class="text-sm opacity-90">متوسط السعر</div>
            </div>
        </div>
    </div>

    <!-- Market Tabs -->
    <div class="bg-white rounded-lg shadow-lg mb-8">
        <div class="border-b border-gray-200">
            <nav class="flex space-x-8 px-6" aria-label="Tabs">
                <button onclick="switchTab('properties')" id="properties-tab" 
                        class="tab-button py-4 px-1 border-b-2 font-medium text-sm border-orange-500 text-orange-600">
                    العقارات
                </button>
                <button onclick="switchTab('lands')" id="lands-tab" 
                        class="tab-button py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    الأراضي
                </button>
                <button onclick="switchTab('nfts')" id="nfts-tab" 
                        class="tab-button py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    NFTs
                </button>
                <button onclick="switchTab('tours')" id="tours-tab" 
                        class="tab-button py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    الجولات
                </button>
            </nav>
        </div>

        <!-- Filters -->
        <div class="p-6">
            <form method="GET" action="{{ route('metaverse.marketplace.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الفئة</label>
                    <select name="category" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <option value="">كل الفئات</option>
                        <option value="properties" {{ request('category') == 'properties' ? 'selected' : '' }}>العقارات</option>
                        <option value="lands" {{ request('category') == 'lands' ? 'selected' : '' }}>الأراضي</option>
                        <option value="nfts" {{ request('category') == 'nfts' ? 'selected' : '' }}>NFTs</option>
                        <option value="tours" {{ request('category') == 'tours' ? 'selected' : '' }}>الجولات</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">السعر الأدنى</label>
                    <input type="number" name="price_min" value="{{ request('price_min') }}" placeholder="0" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">السعر الأقصى</label>
                    <input type="number" name="price_max" value="{{ request('price_max') }}" placeholder="1000000" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">العملة</label>
                    <select name="currency" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <option value="">كل العملات</option>
                        <option value="USD" {{ request('currency') == 'USD' ? 'selected' : '' }}>USD</option>
                        <option value="ETH" {{ request('currency') == 'ETH' ? 'selected' : '' }}>ETH</option>
                        <option value="BTC" {{ request('currency') == 'BTC' ? 'selected' : '' }}>BTC</option>
                    </select>
                </div>
                
                <div class="md:col-span-4">
                    <button type="submit" class="bg-orange-600 text-white px-6 py-2 rounded-md hover:bg-orange-700 transition-colors">
                        بحث
                    </button>
                    <a href="{{ route('metaverse.marketplace.index') }}" class="ml-2 text-gray-600 hover:text-gray-800">
                        إعادة تعيين
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Properties Tab Content -->
    <div id="properties-content" class="tab-content">
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
                        @if($property->is_for_sale)
                            <div class="absolute top-2 left-2 bg-green-500 text-white px-2 py-1 rounded-full text-xs">
                                للبيع
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
                            <div class="text-xl font-bold text-orange-600">
                                {{ $property->getFormattedPriceAttribute() }}
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ number_format($property->getPricePerSquareMeter(), 2) }}/م²
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                            <div>👁 {{ $property->view_count }}</div>
                            <div>❤️ {{ $property->like_count }}</div>
                            <div>⭐ {{ number_format($property->rating_average, 1) }}</div>
                        </div>
                        
                        <div class="flex gap-2">
                            <a href="{{ route('metaverse.marketplace.property', $property) }}" 
                               class="flex-1 bg-orange-600 text-white text-center py-2 rounded-md hover:bg-orange-700 transition-colors">
                                عرض التفاصيل
                            </a>
                            <button onclick="makeOffer({{ $property->id }}, 'property')" 
                                    class="flex-1 bg-blue-600 text-white text-center py-2 rounded-md hover:bg-blue-700 transition-colors">
                                عرض
                            </button>
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
    </div>

    <!-- Lands Tab Content -->
    <div id="lands-content" class="tab-content hidden">
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
                        @if($land->ownership_status === 'for_sale')
                            <div class="absolute top-2 left-2 bg-green-500 text-white px-2 py-1 rounded-full text-xs">
                                للبيع
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
                            <div class="text-xl font-bold text-orange-600">
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
                        
                        <div class="flex gap-2">
                            <a href="{{ route('metaverse.marketplace.land', $land) }}" 
                               class="flex-1 bg-orange-600 text-white text-center py-2 rounded-md hover:bg-orange-700 transition-colors">
                                عرض التفاصيل
                            </a>
                            <button onclick="makeOffer({{ $land->id }}, 'land')" 
                                    class="flex-1 bg-blue-600 text-white text-center py-2 rounded-md hover:bg-blue-700 transition-colors">
                                عرض
                            </button>
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
    </div>

    <!-- NFTs Tab Content -->
    <div id="nfts-content" class="tab-content hidden">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
            @forelse($nfts as $nft)
                <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                    <!-- NFT Image -->
                    <div class="relative h-48 bg-gray-200">
                        <img src="{{ $nft->getImageUrl() }}" alt="{{ $nft->token_id }}" class="w-full h-full object-cover">
                        <div class="absolute top-2 right-2 bg-purple-600 text-white px-2 py-1 rounded-full text-xs">
                            {{ $nft->getBlockchainTextAttribute() }}
                        </div>
                        @if($nft->is_for_sale)
                            <div class="absolute top-2 left-2 bg-green-500 text-white px-2 py-1 rounded-full text-xs">
                                للبيع
                            </div>
                        @endif
                    </div>
                    
                    <!-- NFT Info -->
                    <div class="p-4">
                        <h3 class="font-bold text-lg mb-2">NFT #{{ $nft->token_id }}</h3>
                        <p class="text-gray-600 text-sm mb-3">{{ $nft->metaverseProperty->title ?? 'NFT' }}</p>
                        
                        <div class="flex items-center justify-between mb-3">
                            <div class="text-sm text-gray-500">
                                {{ $nft->getBlockchainTextAttribute() }}
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ $nft->getRarityAttribute() }}
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between mb-3">
                            <div class="text-xl font-bold text-orange-600">
                                {{ $nft->getFormattedPriceAttribute() }}
                            </div>
                            @if($nft->highest_bid > 0)
                                <div class="text-sm text-gray-500">
                                    أعلى عرض: {{ $nft->getFormattedHighestBidAttribute() }}
                                </div>
                            @endif
                        </div>
                        
                        <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                            <div>👁 {{ $nft->view_count }}</div>
                            <div>❤️ {{ $nft->like_count }}</div>
                            <div>💰 {{ $nft->total_sales_count }}</div>
                        </div>
                        
                        <div class="flex gap-2">
                            <a href="{{ route('metaverse.marketplace.nft', $nft) }}" 
                               class="flex-1 bg-orange-600 text-white text-center py-2 rounded-md hover:bg-orange-700 transition-colors">
                                عرض التفاصيل
                            </a>
                            @if($nft->is_for_sale)
                                <button onclick="placeBid({{ $nft->id }})" 
                                        class="flex-1 bg-purple-600 text-white text-center py-2 rounded-md hover:bg-purple-700 transition-colors">
                                    مزايدة
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <div class="text-gray-500 text-lg">لم يتم العثور على NFTs</div>
                    <p class="text-gray-400 mt-2">حاول تعديل معايير البحث</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Tours Tab Content -->
    <div id="tours-content" class="tab-content hidden">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            @forelse($tours as $tour)
                <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                    <!-- Tour Image -->
                    <div class="relative h-48 bg-gray-200">
                        <img src="{{ $tour->getThumbnailUrl() }}" alt="{{ $tour->title }}" class="w-full h-full object-cover">
                        @if($tour->is_featured)
                            <div class="absolute top-2 right-2 bg-yellow-500 text-white px-2 py-1 rounded-full text-xs">
                                مميز
                            </div>
                        @endif
                        <div class="absolute top-2 left-2 bg-blue-500 text-white px-2 py-1 rounded-full text-xs">
                            {{ $tour->getTourTypeTextAttribute() }}
                        </div>
                    </div>
                    
                    <!-- Tour Info -->
                    <div class="p-4">
                        <h3 class="font-bold text-lg mb-2">{{ $tour->title }}</h3>
                        <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ $tour->description }}</p>
                        
                        <div class="flex items-center justify-between mb-3">
                            <div class="text-sm text-gray-500">
                                {{ $tour->getDifficultyLevelTextAttribute() }}
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ $tour->getFormattedDurationAttribute() }}
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between mb-3">
                            <div class="text-xl font-bold text-orange-600">
                                {{ $tour->getFormattedPriceAttribute() }}
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ $tour->participant_count }}/{{ $tour->max_participants }}
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                            <div>👁 {{ $tour->view_count }}</div>
                            <div>👥 {{ $tour->participant_count }}</div>
                            <div>⭐ {{ number_format($tour->rating_average, 1) }}</div>
                        </div>
                        
                        <div class="flex gap-2">
                            <a href="{{ route('metaverse.tours.show', $tour) }}" 
                               class="flex-1 bg-orange-600 text-white text-center py-2 rounded-md hover:bg-orange-700 transition-colors">
                                عرض التفاصيل
                            </a>
                            @if($tour->canBeBooked())
                                <button onclick="bookTour({{ $tour->id }})" 
                                        class="flex-1 bg-green-600 text-white text-center py-2 rounded-md hover:bg-green-700 transition-colors">
                                    حجز
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <div class="text-gray-500 text-lg">لم يتم العثور على جولات</div>
                    <p class="text-gray-400 mt-2">حاول تعديل معايير البحث</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Pagination -->
    @if(isset($properties) && $properties->hasPages())
        <div class="flex justify-center">
            {{ $properties->links() }}
        </div>
    @endif
</div>

<!-- Quick Actions Floating Button -->
<div class="fixed bottom-8 right-8 flex flex-col gap-2">
    <a href="{{ route('metaverse.marketplace.create') }}" 
       class="bg-orange-600 text-white p-4 rounded-full shadow-lg hover:bg-orange-700 transition-colors">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
    </a>
    <a href="{{ route('metaverse.properties.index') }}" 
       class="bg-blue-600 text-white p-4 rounded-full shadow-lg hover:bg-blue-700 transition-colors">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1H2m0 0V5a1 1 0 011-1h6l4 4z"></path>
        </svg>
    </a>
</div>

<script>
// Tab switching functionality
function switchTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    // Remove active state from all tabs
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('border-orange-500', 'text-orange-600');
        button.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Show selected tab content
    document.getElementById(tabName + '-content').classList.remove('hidden');
    
    // Add active state to selected tab
    const activeTab = document.getElementById(tabName + '-tab');
    activeTab.classList.remove('border-transparent', 'text-gray-500');
    activeTab.classList.add('border-orange-500', 'text-orange-600');
}

// Make offer function
function makeOffer(itemId, type) {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    modal.innerHTML = `
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <h3 class="text-lg font-bold mb-4">تقديم عرض</h3>
            <form id="offer-form" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">سعر العرض</label>
                    <input type="number" name="offer_price" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">العملة</label>
                    <select name="currency" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <option value="USD">USD</option>
                        <option value="ETH">ETH</option>
                        <option value="BTC">BTC</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">رسالة (اختياري)</label>
                    <textarea name="message" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500"></textarea>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-orange-600 text-white py-2 rounded-md hover:bg-orange-700 transition-colors">
                        إرسال العرض
                    </button>
                    <button type="button" onclick="closeModal()" class="flex-1 bg-gray-300 text-gray-700 py-2 rounded-md hover:bg-gray-400 transition-colors">
                        إلغاء
                    </button>
                </div>
            </form>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Handle form submission
    document.getElementById('offer-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(e.target);
        
        fetch(`/metaverse/marketplace/${type}/${itemId}/offer`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                offer_price: formData.get('offer_price'),
                currency: formData.get('currency'),
                message: formData.get('message'),
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeModal();
                alert('تم إرسال العرض بنجاح!');
            } else {
                alert(data.message || 'فشل إرسال العرض');
            }
        })
        .catch(error => {
            console.error('Error making offer:', error);
            alert('حدث خطأ أثناء إرسال العرض');
        });
    });
}

// Place bid function
function placeBid(nftId) {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    modal.innerHTML = `
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <h3 class="text-lg font-bold mb-4">تقديم مزايدة</h3>
            <form id="bid-form" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">سعر المزايدة</label>
                    <input type="number" name="bid_amount" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">العملة</label>
                    <select name="currency" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <option value="ETH">ETH</option>
                        <option value="USD">USD</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-purple-600 text-white py-2 rounded-md hover:bg-purple-700 transition-colors">
                        إرسال المزايدة
                    </button>
                    <button type="button" onclick="closeModal()" class="flex-1 bg-gray-300 text-gray-700 py-2 rounded-md hover:bg-gray-400 transition-colors">
                        إلغاء
                    </button>
                </div>
            </form>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Handle form submission
    document.getElementById('bid-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(e.target);
        
        fetch(`/metaverse/marketplace/nft/${nftId}/bid`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                amount: formData.get('bid_amount'),
                currency: formData.get('currency'),
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeModal();
                alert('تم إرسال المزايدة بنجاح!');
            } else {
                alert(data.message || 'فشل إرسال المزايدة');
            }
        })
        .catch(error => {
            console.error('Error placing bid:', error);
            alert('حدث خطأ أثناء إرسال المزايدة');
        });
    });
}

// Book tour function
function bookTour(tourId) {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    modal.innerHTML = `
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <h3 class="text-lg font-bold mb-4">حجز الجولة</h3>
            <form id="booking-form" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">عدد المشاركين</label>
                    <input type="number" name="participant_count" min="1" value="1" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">التاريخ والوقت</label>
                    <input type="datetime-local" name="scheduled_time" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">متطلبات خاصة (اختياري)</label>
                    <textarea name="special_requirements" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-green-600 text-white py-2 rounded-md hover:bg-green-700 transition-colors">
                        حجز الجولة
                    </button>
                    <button type="button" onclick="closeModal()" class="flex-1 bg-gray-300 text-gray-700 py-2 rounded-md hover:bg-gray-400 transition-colors">
                        إلغاء
                    </button>
                </div>
            </form>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Handle form submission
    document.getElementById('booking-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(e.target);
        
        fetch(`/metaverse/tours/${tourId}/book`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                participant_count: formData.get('participant_count'),
                scheduled_time: formData.get('scheduled_time'),
                special_requirements: formData.get('special_requirements'),
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeModal();
                alert('تم حجز الجولة بنجاح!');
            } else {
                alert(data.message || 'فشل حجز الجولة');
            }
        })
        .catch(error => {
            console.error('Error booking tour:', error);
            alert('حدث خطأ أثناء حجز الجولة');
        });
    });
}

// Close modal function
function closeModal() {
    const modal = document.querySelector('.fixed.inset-0');
    if (modal) {
        modal.remove();
    }
}

// Real-time updates
setInterval(() => {
    // Update market stats
    fetch('/api/metaverse/marketplace/stats')
        .then(response => response.json())
        .then(data => {
            // Update stats in the UI
            console.log('Updated market stats:', data);
        });
}, 60000); // Update every minute
</script>
@endsection

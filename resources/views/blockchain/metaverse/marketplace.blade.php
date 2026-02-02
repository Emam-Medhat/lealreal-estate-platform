@extends('admin.layouts.admin')

@section('title', 'السوق الافتراضي')

@section('content')
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'Metaverse Marketplace')</title>
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100">
    <div class="container mx-auto px-4 py-8 space-y-8">
        <!-- Header Section -->
        <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 p-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center">
                            <i class="fas fa-store text-white text-xl"></i>
                        </div>
                        <h1 class="text-4xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 bg-clip-text text-transparent">
                            السوق الافتراضي
                        </h1>
                    </div>
                    <p class="text-gray-600 text-lg max-w-2xl">
                        اكتشف واشتر وبيع الأصول الرقمية في العالم الافتراضي
                    </p>
                    <div class="flex items-center gap-4 text-sm text-gray-500">
                        <span class="flex items-center gap-1">
                            <i class="fas fa-shield-alt text-green-500"></i>
                            آمن 100%
                        </span>
                        <span class="flex items-center gap-1">
                            <i class="fas fa-bolt text-yellow-500"></i>
                            سريع
                        </span>
                        <span class="flex items-center gap-1">
                            <i class="fas fa-gem text-purple-500"></i>
                            فريد
                        </span>
                    </div>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-3">
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <meta name="csrf-token" content="{{ csrf_token() }}">
                    <title>@yield('title', 'Metaverse Marketplace')</title>
                    <a href="{{ route('blockchain.blockchain.metaverse.marketplace.create') }}" class="bg-gradient-to-r from-green-600 to-emerald-600 text-white px-8 py-4 rounded-2xl hover:from-green-700 hover:to-emerald-700 transition-all duration-300 font-semibold shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center justify-center gap-2">
                        <i class="fas fa-plus-circle"></i>
                        إضافة منتج
                    </a>
                    <button onclick="toggleCart()" class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-8 py-4 rounded-2xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 font-semibold shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center justify-center gap-2 relative">
                        <i class="fas fa-shopping-cart"></i>
                        سلة التسوق
                        <span id="cart-count" class="bg-white/20 px-2 py-1 rounded-full text-xs">0</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Items -->
            <div class="group bg-gradient-to-br from-blue-500 via-blue-600 to-indigo-700 rounded-3xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:scale-105 border border-white/10">
                <div class="flex items-start justify-between mb-4">
                    <div class="bg-white/20 backdrop-blur-sm p-3 rounded-2xl group-hover:bg-white/30 transition-colors duration-300">
                        <i class="fas fa-cube text-2xl"></i>
                    </div>
                    <div class="bg-green-400/20 backdrop-blur-sm px-3 py-1 rounded-full">
                        <span class="text-xs font-medium text-green-100">+12%</span>
                    </div>
                </div>
                <div>
                    <p class="text-blue-100 text-sm font-medium mb-2">إجمالي العناصر</p>
                    <p class="text-4xl font-bold mb-1">{{ number_format($stats['total_properties']) }}</p>
                    <p class="text-blue-100 text-xs opacity-75">
                        <i class="fas fa-arrow-up ml-1"></i>
                        زيادة هذا الشهر
                    </p>
                </div>
            </div>

            <!-- For Sale -->
            <div class="group bg-gradient-to-br from-emerald-500 via-green-600 to-teal-700 rounded-3xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:scale-105 border border-white/10">
                <div class="flex items-start justify-between mb-4">
                    <div class="bg-white/20 backdrop-blur-sm p-3 rounded-2xl group-hover:bg-white/30 transition-colors duration-300">
                        <i class="fas fa-tag text-2xl"></i>
                    </div>
                    <div class="bg-yellow-400/20 backdrop-blur-sm px-3 py-1 rounded-full">
                        <span class="text-xs font-medium text-yellow-100">+8%</span>
                    </div>
                </div>
                <div>
                    <p class="text-green-100 text-sm font-medium mb-2">للبيع</p>
                    <p class="text-4xl font-bold mb-1">{{ number_format($stats['for_sale']) }}</p>
                    <p class="text-green-100 text-xs opacity-75">
                        <i class="fas fa-fire ml-1"></i>
                        عقارات ساخنة
                    </p>
                </div>
            </div>

            <!-- For Rent -->
            <div class="group bg-gradient-to-br from-purple-500 via-violet-600 to-indigo-700 rounded-3xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:scale-105 border border-white/10">
                <div class="flex items-start justify-between mb-4">
                    <div class="bg-white/20 backdrop-blur-sm p-3 rounded-2xl group-hover:bg-white/30 transition-colors duration-300">
                        <i class="fas fa-home text-2xl"></i>
                    </div>
                    <div class="bg-blue-400/20 backdrop-blur-sm px-3 py-1 rounded-full">
                        <span class="text-xs font-medium text-blue-100">+15%</span>
                    </div>
                </div>
                <div>
                    <p class="text-purple-100 text-sm font-medium mb-2">للإيجار</p>
                    <p class="text-4xl font-bold mb-1">{{ number_format($stats['for_rent']) }}</p>
                    <p class="text-purple-100 text-xs opacity-75">
                        <i class="fas fa-chart-line ml-1"></i>
                        طلب مرتفع
                    </p>
                </div>
            </div>

            <!-- Total Value -->
            <div class="group bg-gradient-to-br from-orange-500 via-amber-600 to-red-700 rounded-3xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:scale-105 border border-white/10">
                <div class="flex items-start justify-between mb-4">
                    <div class="bg-white/20 backdrop-blur-sm p-3 rounded-2xl group-hover:bg-white/30 transition-colors duration-300">
                        <i class="fas fa-chart-line text-2xl"></i>
                    </div>
                    <div class="bg-purple-400/20 backdrop-blur-sm px-3 py-1 rounded-full">
                        <span class="text-xs font-medium text-purple-100">+25%</span>
                    </div>
                </div>
                <div>
                    <p class="text-orange-100 text-sm font-medium mb-2">القيمة الإجمالية</p>
                    <p class="text-4xl font-bold mb-1">${{ number_format($stats['total_value'], 0) }}</p>
                    <p class="text-orange-100 text-xs opacity-75">
                        <i class="fas fa-trending-up ml-1"></i>
                        نمو قوي
                    </p>
                </div>
            </div>
        </div>

        <!-- Filters and Search -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">الفئات</h2>
            <div class="flex flex-wrap gap-3">
                <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                    <i class="fas fa-cube ml-2"></i>
                    الكل
                </button>
                @foreach($categories as $category)
                <button class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors duration-200">
                    <i class="fas fa-cube ml-2"></i>
                    {{ $category->property_type }} ({{ $category->count }})
                </button>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Search and Sort -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <input type="text" placeholder="بحث عن منتج أو عنصر..." class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div class="flex gap-2">
                <select class="px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option>جميع الفئات</option>
                    <option>فنون</option>
                    <option>موسيقى</option>
                    <option>ألعاب</option>
                    <option>عقارات</option>
                </select>
                <select class="px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option>السعر من الأقل للأعلى</option>
                    <option>السعر من الأعلى للأقل</option>
                    <option>الأحدث</option>
                    <option>الأكثر مبيعاً</option>
                </select>
            </div>
        </div>
    </div>

        <!-- Marketplace Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
            @forelse($properties as $property)
            <div class="group bg-white/80 backdrop-blur-xl rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:scale-105 border border-white/20 overflow-hidden">
                <!-- Image Section -->
                <div class="relative h-56 overflow-hidden">
                    <img src="{{ $property->getThumbnailUrl() }}" alt="{{ $property->title }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    
                    <!-- Status Badges -->
                    <div class="absolute top-4 right-4 flex gap-2">
                        @if($property->is_for_sale)
                        <span class="bg-gradient-to-r from-green-500 to-emerald-600 text-white px-3 py-1 rounded-full text-xs font-bold shadow-lg backdrop-blur-sm">
                            <i class="fas fa-check-circle ml-1"></i>
                            متاح
                        </span>
                        @endif
                        @if($property->is_new)
                        <span class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white px-3 py-1 rounded-full text-xs font-bold shadow-lg backdrop-blur-sm">
                            <i class="fas fa-sparkles ml-1"></i>
                            جديد
                        </span>
                        @endif
                    </div>
                    
                    <!-- Category Badge -->
                    <div class="absolute bottom-4 left-4">
                        <span class="bg-black/70 backdrop-blur-md text-white px-4 py-2 rounded-2xl text-sm font-semibold shadow-xl">
                            {{ $property->property_type_text }}
                        </span>
                    </div>
                    
                    <!-- Quick Actions Overlay -->
                    <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <div class="flex gap-3">
                            <button class="bg-white/90 backdrop-blur-sm p-3 rounded-2xl shadow-xl hover:bg-white transition-colors duration-300 transform hover:scale-110">
                                <i class="fas fa-heart text-red-500"></i>
                            </button>
                            <button class="bg-white/90 backdrop-blur-sm p-3 rounded-2xl shadow-xl hover:bg-white transition-colors duration-300 transform hover:scale-110">
                                <i class="fas fa-share-alt text-blue-500"></i>
                            </button>
                            <button class="bg-white/90 backdrop-blur-sm p-3 rounded-2xl shadow-xl hover:bg-white transition-colors duration-300 transform hover:scale-110">
                                <i class="fas fa-eye text-gray-700"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Content Section -->
                <div class="p-6 space-y-4">
                    <!-- Title and Description -->
                    <div>
                        <h3 class="font-bold text-xl text-gray-900 mb-2 group-hover:text-blue-600 transition-colors duration-300">
                            {{ $property->title }}
                        </h3>
                        <p class="text-gray-600 text-sm leading-relaxed">{{ Str::limit($property->description, 80) }}</p>
                    </div>
                    
                    <!-- Price Section -->
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-3xl font-bold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent">
                                ${{ number_format($property->price, 2) }}
                            </p>
                            @if($property->is_for_rent && $property->rent_price)
                            <p class="text-sm text-gray-500">
                                <span class="line-through">${{ number_format($property->rent_price, 2) }}</span>/{{ $property->rent_period }}
                            </p>
                            @endif
                        </div>
                        <div class="text-right">
                            @if($property->rating_average > 0)
                            <div class="flex items-center gap-1 mb-1">
                                @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star text-sm {{ $i <= floor($property->rating_average) ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                @endfor
                            </div>
                            <span class="text-xs text-gray-500">({{ $property->rating_average }}/5)</span>
                            @endif
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-3">
                        <a href="{{ route('blockchain.metaverse.properties') }}/{{ $property->id }}" class="flex-1 bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-3 rounded-2xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 font-semibold shadow-lg hover:shadow-xl transform hover:scale-105 text-center flex items-center justify-center gap-2">
                            <i class="fas fa-eye"></i>
                            عرض
                        </a>
                        <button onclick="addToCart({{ $property->id }}, '{{ $property->title }}', {{ $property->price }})" data-property-id="{{ $property->id }}" class="flex-1 bg-gradient-to-r from-green-600 to-emerald-600 text-white py-3 rounded-2xl hover:from-green-700 hover:to-emerald-700 transition-all duration-300 font-semibold shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center justify-center gap-2">
                            <i class="fas fa-shopping-cart"></i>
                            شراء
                        </button>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-20">
                <div class="max-w-md mx-auto space-y-6">
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto">
                        <i class="fas fa-cube text-4xl text-gray-400"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">لا توجد عقارات متاحة</h3>
                        <p class="text-gray-600">سيتم إضافة عقارات جديدة قريباً. تابعنا للحصول على آخر التحديثات!</p>
                    </div>
                    <button class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-8 py-3 rounded-2xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 font-semibold shadow-lg hover:shadow-xl transform hover:scale-105">
                        <i class="fas fa-bell ml-2"></i>
                        إشعارني عند توفر عقارات
                    </button>
                </div>
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-xl border border-white/20 p-8">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-gray-600">
                    <span class="font-semibold">{{ $properties->count() }}</span> عنصر معروض من إجمالي <span class="font-semibold">{{ number_format($properties->total()) }}</span> عنصر
                </div>
                <div class="flex items-center gap-4">
                    <button class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-colors duration-300 disabled:opacity-50 disabled:cursor-not-allowed" {{ $properties->onFirstPage() ? 'disabled' : '' }}>
                        <i class="fas fa-chevron-right"></i>
                    </button>
                    <span class="text-gray-700 font-medium">
                        صفحة <span class="text-blue-600 font-bold">{{ $properties->currentPage() }}</span> من <span class="text-blue-600 font-bold">{{ $properties->lastPage() }}</span>
                    </span>
                    <button class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-colors duration-300 disabled:opacity-50 disabled:cursor-not-allowed" {{ $properties->hasMorePages() ? '' : 'disabled' }}>
                        <i class="fas fa-chevron-left"></i>
                    </button>
                </div>
            </div>
            
            <!-- Pagination Links -->
            @if($properties->hasPages())
            <div class="flex justify-center mt-6">
                {{ $properties->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Shopping Cart Sidebar -->
<div id="cart-sidebar" class="fixed right-0 top-0 h-full w-96 bg-white shadow-2xl transform translate-x-full transition-transform duration-300 z-50">
    <div class="p-6 border-b">
        <div class="flex items-center justify-between">
            <h3 class="text-xl font-bold text-gray-900">سلة التسوق</h3>
            <button onclick="toggleCart()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
    </div>
    <div id="cart-items" class="flex-1 overflow-y-auto p-6">
        <div class="text-center py-12">
            <i class="fas fa-shopping-cart text-6xl text-gray-300 mb-4"></i>
            <p class="text-gray-500">سلة التسوق فارغة</p>
        </div>
    </div>
    <div class="border-t p-6 space-y-4">
        <div class="flex justify-between mb-4">
            <span class="font-semibold text-gray-700">الإجمالي:</span>
            <span id="cart-total" class="font-bold text-xl text-gray-900">$0.00</span>
        </div>
        <div class="flex items-center justify-between text-sm text-gray-600 mb-4">
            <span>المنتجات:</span>
            <span id="cart-items-count" class="font-medium">0</span>
        </div>
        <button onclick="checkout()" id="checkout-btn" class="w-full bg-gradient-to-r from-green-600 to-emerald-600 text-white py-3 rounded-2xl hover:from-green-700 hover:to-emerald-700 transition-all duration-300 font-semibold shadow-lg hover:shadow-xl transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none flex items-center justify-center gap-2">
            <i class="fas fa-credit-card"></i>
            إتمام الشراء
        </button>
        <button onclick="clearCart()" class="w-full bg-gray-200 text-gray-700 py-2 rounded-xl hover:bg-gray-300 transition-all duration-300 font-medium text-sm">
            <i class="fas fa-trash-alt ml-2"></i>
            إفراغ السلة
        </button>
    </div>
</div>

<!-- Overlay -->
<div id="cart-overlay" class="fixed inset-0 bg-black/50 opacity-0 pointer-events-none transition-opacity duration-300 z-40" onclick="toggleCart()"></div>

<script>
let cart = [];
let cartOpen = false;

function toggleCart() {
    const sidebar = document.getElementById('cart-sidebar');
    const overlay = document.getElementById('cart-overlay');
    
    cartOpen = !cartOpen;
    
    if (cartOpen) {
        sidebar.classList.remove('translate-x-full');
        overlay.classList.remove('opacity-0', 'pointer-events-none');
        overlay.classList.add('opacity-100');
    } else {
        sidebar.classList.add('translate-x-full');
        overlay.classList.add('opacity-0', 'pointer-events-none');
        overlay.classList.remove('opacity-100');
    }
}

function addToCart(propertyId, title, price) {
    const existingItem = cart.find(item => item.id === propertyId);
    
    if (existingItem) {
        existingItem.quantity++;
    } else {
        // Get additional property data from the page
        const propertyElement = document.querySelector(`[onclick*="addToCart(${propertyId}"]`);
        const propertyCard = propertyElement?.closest('.group') || propertyElement?.closest('[data-property-id]');
        
        // Extract data safely
        const titleElement = propertyCard?.querySelector('h3, h4, .font-semibold');
        const descriptionElement = propertyCard?.querySelector('.text-gray-600, .text-sm');
        const imageElement = propertyCard?.querySelector('img');
        
        cart.push({
            id: propertyId,
            title: title || titleElement?.textContent?.trim() || 'عقار افتراضي',
            price: price,
            quantity: 1,
            description: descriptionElement?.textContent?.trim() || '',
            image_path: imageElement?.src?.replace(window.location.origin, '') || '/images/default-property.jpg',
            property_type: descriptionElement?.textContent?.trim() || 'عقار',
            virtual_world_id: null,
            location_coordinates: null
        });
    }
    
    updateCartUI();
    showNotification('تمت إضافة المنتج إلى سلة التسوق');
}

function removeFromCart(propertyId) {
    cart = cart.filter(item => item.id !== propertyId);
    updateCartUI();
}

function updateCartUI() {
    const cartCount = document.getElementById('cart-count');
    const cartItems = document.getElementById('cart-items');
    const cartTotal = document.getElementById('cart-total');
    const cartItemsCount = document.getElementById('cart-items-count');
    const checkoutBtn = document.getElementById('checkout-btn');
    
    // Update count
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    cartCount.textContent = totalItems;
    cartItemsCount.textContent = totalItems;
    
    // Update checkout button state
    if (cart.length === 0) {
        checkoutBtn.disabled = true;
        checkoutBtn.innerHTML = '<i class="fas fa-shopping-cart"></i> السلة فارغة';
    } else {
        checkoutBtn.disabled = false;
        checkoutBtn.innerHTML = '<i class="fas fa-credit-card"></i> إتمام الشراء';
    }
    
    // Update cart items
    if (cart.length === 0) {
        cartItems.innerHTML = `
            <div class="text-center py-12">
                <i class="fas fa-shopping-cart text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-500">سلة التسوق فارغة</p>
                <p class="text-sm text-gray-400 mt-2">أضف بعض العقارات للبدء</p>
            </div>
        `;
    } else {
        cartItems.innerHTML = cart.map(item => `
            <div class="flex items-center justify-between mb-4 p-3 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors duration-200">
                <div class="flex-1">
                    <h4 class="font-semibold text-gray-900 text-sm">${item.title}</h4>
                    <p class="text-gray-600 text-xs">$${item.price.toFixed(2)} × ${item.quantity}</p>
                </div>
                <div class="flex items-center gap-2">
                    <button onclick="updateQuantity(${item.id}, -1)" class="w-8 h-8 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200 flex items-center justify-center">
                        <i class="fas fa-minus text-xs"></i>
                    </button>
                    <span class="w-8 text-center font-medium">${item.quantity}</span>
                    <button onclick="updateQuantity(${item.id}, 1)" class="w-8 h-8 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200 flex items-center justify-center">
                        <i class="fas fa-plus text-xs"></i>
                    </button>
                    <button onclick="removeFromCart(${item.id})" class="text-red-500 hover:text-red-700 transition-colors duration-200 p-1">
                        <i class="fas fa-trash text-sm"></i>
                    </button>
                </div>
            </div>
        `).join('');
    }
    
    // Update total
    const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    cartTotal.textContent = `$${total.toFixed(2)}`;
}

function checkout() {
    if (cart.length === 0) {
        showNotification('سلة التسوق فارغة', 'error');
        return;
    }
    
    const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    const itemsCount = cart.reduce((sum, item) => sum + item.quantity, 0);
    
    // Show checkout confirmation
    if (confirm(`هل تريد إتمام شراء ${itemsCount} منتجات بإجمالي $${total.toFixed(2)}؟`)) {
        // Show processing state
        const checkoutBtn = document.getElementById('checkout-btn');
        checkoutBtn.disabled = true;
        checkoutBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري المعالجة...';
        
        // Prepare cart data for API
        const cartData = cart.map(item => ({
            itemable_type: 'App\\Models\\Metaverse\\MetaverseProperty',
            itemable_id: item.id,
            item_name: item.title,
            item_description: item.description || '',
            price: item.price,
            quantity: item.quantity,
            total: item.price * item.quantity,
            item_data: {
                image_path: item.image_path || null,
                property_type: item.property_type || null,
                virtual_world_id: item.virtual_world_id || null,
                location_coordinates: item.location_coordinates || null
            }
        }));
        
        // Send order to server
        fetch('/orders/create', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                cart: cartData,
                notes: 'طلب من السوق الافتراضي'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Clear cart after successful order
                cart = [];
                updateCartUI();
                toggleCart();
                
                showNotification('تم إنشاء الطلب بنجاح! رقم الطلب: ' + data.order_number, 'success');
                
                // Redirect to order details page
                setTimeout(() => {
                    window.location.href = '/orders/' + data.order_id;
                }, 2000);
            } else {
                throw new Error(data.message || 'فشل في إنشاء الطلب');
            }
        })
        .catch(error => {
            console.error('Checkout error:', error);
            showNotification('حدث خطأ أثناء إتمام الشراء: ' + error.message, 'error');
            
            // Reset button state
            checkoutBtn.disabled = false;
            checkoutBtn.innerHTML = '<i class="fas fa-credit-card"></i> إتمام الشراء';
        });
    }
}

function clearCart() {
    if (cart.length === 0) {
        showNotification('سلة التسوق فارغة بالفعل', 'info');
        return;
    }
    
    if (confirm('هل أنت متأكد من إفراغ سلة التسوق؟')) {
        cart = [];
        updateCartUI();
        showNotification('تم إفراغ سلة التسوق', 'success');
    }
}

function updateQuantity(propertyId, change) {
    const item = cart.find(item => item.id === propertyId);
    if (item) {
        item.quantity += change;
        if (item.quantity <= 0) {
            removeFromCart(propertyId);
        } else {
            updateCartUI();
        }
    }
}

function showNotification(message) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = 'fixed top-4 left-1/2 transform -translate-x-1/2 bg-green-500 text-white px-6 py-3 rounded-xl shadow-lg z-50 transition-all duration-300';
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.remove();
    }, 3000);
}
</script>

@endsection

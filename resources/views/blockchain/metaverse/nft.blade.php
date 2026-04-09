@extends('admin.layouts.admin')

@section('title', 'سجل NFT')

@section('content')
<!-- Cache buster: {{ time() }} -->
<div class="space-y-6">
    <!-- Header Section -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">سجل NFT</h1>
                <p class="text-gray-600">إدارة وتتبع الرموز غير القابلة للتبديل (NFT)</p>
            </div>
            
            <div class="flex gap-2">
                <button onclick="showCreateModal()" class="bg-green-600 text-white px-6 py-3 rounded-xl hover:bg-green-700 transition-colors duration-200 font-medium">
                    <i class="fas fa-plus ml-2"></i>
                    إنشاء NFT
                </button>
                <button onclick="exportNFTs()" class="bg-blue-600 text-white px-6 py-3 rounded-xl hover:bg-blue-700 transition-colors duration-200 font-medium">
                    <i class="fas fa-download ml-2"></i>
                    تصدير
                </button>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Total NFTs -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium mb-1">إجمالي NFTs</p>
                    <p class="text-3xl font-bold">{{ $nfts->count() }}</p>
                    <p class="text-blue-100 text-xs mt-2">
                        <i class="fas fa-certificate ml-1"></i>
                        جميع الرموز
                    </p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-certificate text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Owned NFTs -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium mb-1">الرموز المملوكة</p>
                    <p class="text-3xl font-bold">{{ $nfts->where('owner_address', auth()->user()->wallet_address ?? '')->count() }}</p>
                    <p class="text-green-100 text-xs mt-2">
                        <i class="fas fa-user ml-1"></i>
                        {{ auth()->user()->name ?? 'المستخدم' }}
                    </p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-user text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Value -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium mb-1">إجمالي القيمة</p>
                    <p class="text-3xl font-bold">${{ number_format($nfts->sum('price'), 2) }}</p>
                    <p class="text-purple-100 text-xs mt-2">
                        <i class="fas fa-dollar-sign ml-1"></i>
                        بالدولار
                    </p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-dollar-sign text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Active Collections -->
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm font-medium mb-1">المجموعات النشطة</p>
                    <p class="text-3xl font-bold">{{ $nfts->pluck('category')->filter()->unique()->count() }}</p>
                    <p class="text-orange-100 text-xs mt-2">
                        <i class="fas fa-layer-group ml-1"></i>
                        هذا الشهر
                    </p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-layer-group text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <form method="GET" action="{{ route('blockchain.metaverse.nft') }}" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="بحث عن NFT..." class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div class="flex gap-2">
                <select name="type" class="px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">جميع الأنواع</option>
                    <option value="art" {{ request('type') == 'art' ? 'selected' : '' }}>فن</option>
                    <option value="music" {{ request('type') == 'music' ? 'selected' : '' }}>موسيقى</option>
                    <option value="game" {{ request('type') == 'game' ? 'selected' : '' }}>ألعاب</option>
                    <option value="real_estate" {{ request('type') == 'real_estate' ? 'selected' : '' }}>عقارات</option>
                    <option value="collectible" {{ request('type') == 'collectible' ? 'selected' : '' }}>مقتنيات</option>
                </select>
                <select name="sort" class="px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>السعر من الأقل للأعلى</option>
                    <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>السعر من الأعلى للأقل</option>
                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>الأحدث</option>
                    <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>الأكثر مبيعاً</option>
                </select>
                <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded-xl hover:bg-gray-700 transition-colors duration-200">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>
    </div>

    <!-- NFT Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($nfts as $nft)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg transition-all duration-300">
            <div class="relative">
                @if($nft->image_url)
                    <img src="{{ $nft->image_url }}" alt="{{ $nft->name }}" class="w-full h-48 object-cover">
                @else
                    <div class="w-full h-48 bg-gradient-to-br from-blue-400 to-purple-600 flex items-center justify-center">
                        <i class="fas fa-cube text-white text-4xl"></i>
                    </div>
                @endif
                
                <div class="absolute top-2 right-2">
                    <span class="bg-blue-500 text-white px-2 py-1 rounded-full text-xs font-medium">
                        {{ $nft->metadata['rarity'] ?? 'عادي' }}
                    </span>
                </div>
                <div class="absolute bottom-2 left-2">
                    <span class="bg-black/70 text-white px-2 py-1 rounded-full text-xs font-medium">
                        #{{ str_pad($nft->token_id ?? $nft->id, 6, '0', STR_PAD_LEFT) }}
                    </span>
                </div>
            </div>
            
            <div class="p-4">
                <h3 class="font-bold text-lg text-gray-900 mb-2">{{ $nft->name }}</h3>
                <p class="text-gray-600 text-sm mb-3">{{ Str::limit($nft->description, 80) }}</p>
                
                <div class="flex items-center justify-between mb-3">
                    <div class="text-2xl font-bold text-blue-600">{{ number_format($nft->price ?? 0, 4) }} ETH</div>
                    <div class="text-right">
                        <div class="text-sm text-gray-500">${{ number_format(($nft->price ?? 0) * 2000, 2) }}</div>
                    </div>
                </div>

                <div class="flex items-center gap-2 mb-3">
                    <div class="flex items-center">
                        <i class="fas fa-cube text-blue-500"></i>
                        <span class="text-sm text-gray-600">ERC-721</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-layer-group text-purple-500"></i>
                        <span class="text-sm text-gray-600">{{ $nft->category ?? 'غير محدد' }}</span>
                    </div>
                </div>

                <div class="flex space-x-2 space-x-reverse">
                    <button onclick="showNFTDetails({{ $nft->id }})" class="flex-1 bg-gradient-to-r from-blue-500 to-blue-600 text-white py-3 rounded-xl hover:from-blue-600 hover:to-blue-700 transition-all duration-300 font-medium text-sm shadow-lg hover:shadow-xl transform hover:-translate-y-1 flex items-center justify-center">
                        <i class="fas fa-eye ml-2"></i>
                        عرض التفاصيل
                    </button>
                    @php
    $cartItems = session('cart', []);
    $inCart = in_array($nft->id, array_keys($cartItems));
@endphp

                    @if($nft->owner_address == (auth()->user()->wallet_address ?? ''))
                        <button onclick="sellNFT({{ $nft->id }})" class="flex-1 bg-gradient-to-r from-orange-500 to-red-600 text-white py-3 rounded-xl hover:from-orange-600 hover:to-red-700 transition-all duration-300 font-medium text-sm shadow-lg hover:shadow-xl transform hover:-translate-y-1 flex items-center justify-center">
                            <i class="fas fa-exchange-alt ml-2"></i>
                            بيع
                        </button>
                    @else
                        @if(!$inCart)
                            <button onclick="addToCart({{ $nft->id }}, this)" class="flex-1 bg-gradient-to-r from-green-500 to-emerald-600 text-white py-3 rounded-xl hover:from-green-600 hover:to-emerald-700 transition-all duration-300 font-medium text-sm shadow-lg hover:shadow-xl transform hover:-translate-y-1 flex items-center justify-center">
                                <i class="fas fa-shopping-cart ml-2"></i>
                                إضافة للسلة
                            </button>
                        @else
                            <button disabled class="flex-1 bg-gradient-to-r from-gray-400 to-gray-500 text-white py-3 rounded-xl cursor-not-allowed font-medium text-sm shadow-lg flex items-center justify-center">
                                <i class="fas fa-check-circle ml-2"></i>
                                في السلة
                            </button>
                        @endif
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-12">
            <div class="bg-gray-100 rounded-full p-6 w-24 h-24 mx-auto mb-4 flex items-center justify-center">
                <i class="fas fa-cube text-gray-400 text-3xl"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">لا توجد NFTs حالياً</h3>
            <p class="text-gray-500 mb-4">ابدأ بإنشاء أول NFT الخاص بك</p>
            <button onclick="showCreateModal()" class="bg-blue-600 text-white px-6 py-3 rounded-xl hover:bg-blue-700 transition-colors duration-200 font-medium">
                <i class="fas fa-plus ml-2"></i>
                إنشاء NFT
            </button>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($nfts->hasPages())
    <div class="flex justify-center mt-6">
        {{ $nfts->links() }}
    </div>
    @endif
</div>

<!-- Create NFT Modal -->
<div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden" id="createModal">
    <div class="bg-white rounded-3xl shadow-2xl max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-purple-600 via-pink-600 to-red-600 p-8 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-full">
                <div class="absolute top-4 left-4 w-32 h-32 bg-white/10 rounded-full blur-2xl animate-pulse"></div>
                <div class="absolute bottom-4 right-4 w-24 h-24 bg-white/10 rounded-full blur-2xl animate-pulse delay-75"></div>
                <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-40 h-40 bg-white/5 rounded-full blur-3xl"></div>
            </div>
            
            <div class="relative z-10">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="flex items-center justify-center w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl border-2 border-white/30">
                            <i class="fas fa-cube text-white text-2xl"></i>
                        </div>
                        <h2 class="text-3xl font-bold text-white mr-4">إنشاء NFT جديد</h2>
                    </div>
                    <button onclick="closeCreateModal()" class="text-white/80 hover:text-white transition-colors">
                        <i class="fas fa-times text-2xl"></i>
                    </button>
                </div>
                <p class="text-white/90 mt-2">قم بإنشاء رمز غير قابل للتبديل فريد من نوعك</p>
            </div>
        </div>
        
        <!-- Modal Body -->
        <form action="{{ route('blockchain.blockchain.metaverse.nft.store') }}" method="POST" enctype="multipart/form-data" class="p-8">
            @csrf
            
            <!-- Basic Information -->
            <div class="mb-8">
                <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <div class="flex items-center justify-center w-8 h-8 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl mr-3">
                        <i class="fas fa-info text-white text-sm"></i>
                    </div>
                    المعلومات الأساسية
                    <span class="text-red-500 ml-2 animate-pulse">*</span>
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="relative group">
                        <label class="block text-lg font-medium text-gray-900 mb-3">
                            اسم NFT
                            <span class="text-red-500 ml-2 animate-pulse">*</span>
                        </label>
                        <input type="text" 
                               name="name" 
                               required 
                               class="w-full px-4 py-4 text-lg border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-300 bg-white/50 backdrop-blur-sm group-hover:bg-blue-50"
                               placeholder="أدخل اسم NFT">
                        <div class="absolute right-4 top-1/2 transform -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-all duration-300">
                            <i class="fas fa-signature text-gray-400 text-xl"></i>
                        </div>
                        @error('name')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="relative group">
                        <label class="block text-lg font-medium text-gray-900 mb-3">
                            السعر (ETH)
                            <span class="text-red-500 ml-2 animate-pulse">*</span>
                        </label>
                        <input type="number" 
                               name="price" 
                               required 
                               step="0.0001" 
                               min="0"
                               class="w-full px-4 py-4 text-lg border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-300 bg-white/50 backdrop-blur-sm group-hover:bg-blue-50"
                               placeholder="0.0000">
                        <div class="absolute right-4 top-1/2 transform -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-all duration-300">
                            <i class="fab fa-ethereum text-gray-400 text-xl"></i>
                        </div>
                        @error('price')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="relative group">
                    <label class="block text-lg font-medium text-gray-900 mb-3">
                        الوصف
                        <span class="text-red-500 ml-2 animate-pulse">*</span>
                    </label>
                    <textarea name="description" 
                              required 
                              rows="4"
                              class="w-full px-4 py-4 text-lg border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-300 bg-white/50 backdrop-blur-sm group-hover:bg-blue-50 resize-none"
                              placeholder="وصف مفصل لـ NFT الخاص بك"></textarea>
                    <div class="absolute right-4 top-4 opacity-0 group-hover:opacity-100 transition-all duration-300">
                        <i class="fas fa-align-left text-gray-400 text-xl"></i>
                    </div>
                    @error('description')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <!-- NFT Properties -->
            <div class="mb-8">
                <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <div class="flex items-center justify-center w-8 h-8 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl mr-3">
                        <i class="fas fa-palette text-white text-sm"></i>
                    </div>
                    خصائص NFT
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="relative group">
                        <label class="block text-lg font-medium text-gray-900 mb-3">
                            النوع
                            <span class="text-red-500 ml-2 animate-pulse">*</span>
                        </label>
                        <select name="type" 
                                required 
                                class="w-full px-4 py-4 text-lg border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-purple-500/20 focus:border-purple-500 transition-all duration-300 bg-white/50 backdrop-blur-sm appearance-none group-hover:bg-purple-50">
                            <option value="">اختر النوع</option>
                            <option value="art">فن</option>
                            <option value="music">موسيقى</option>
                            <option value="game">ألعاب</option>
                            <option value="real_estate">عقارات</option>
                            <option value="collectible">مقتنيات</option>
                            <option value="sports">رياضة</option>
                            <option value="photography">تصوير</option>
                            <option value="video">فيديو</option>
                        </select>
                        <div class="absolute right-4 top-1/2 transform -translate-y-1/2 pointer-events-none opacity-0 group-hover:opacity-100 transition-all duration-300">
                            <i class="fas fa-chevron-down text-gray-400 text-xl"></i>
                        </div>
                        @error('type')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="relative group">
                        <label class="block text-lg font-medium text-gray-900 mb-3">
                            الندرة
                        </label>
                        <select name="rarity" 
                                class="w-full px-4 py-4 text-lg border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-purple-500/20 focus:border-purple-500 transition-all duration-300 bg-white/50 backdrop-blur-sm appearance-none group-hover:bg-purple-50">
                            <option value="common">عادي</option>
                            <option value="uncommon">غير شائع</option>
                            <option value="rare">نادر</option>
                            <option value="epic">بطولي</option>
                            <option value="legendary">أسطوري</option>
                            <option value="mythic">خارق</option>
                        </select>
                        <div class="absolute right-4 top-1/2 transform -translate-y-1/2 pointer-events-none opacity-0 group-hover:opacity-100 transition-all duration-300">
                            <i class="fas fa-gem text-gray-400 text-xl"></i>
                        </div>
                        @error('rarity')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="relative group">
                    <label class="block text-lg font-medium text-gray-900 mb-3">
                        المجموعة
                    </label>
                    <input type="text" 
                           name="collection" 
                           class="w-full px-4 py-4 text-lg border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-purple-500/20 focus:border-purple-500 transition-all duration-300 bg-white/50 backdrop-blur-sm group-hover:bg-purple-50"
                           placeholder="اسم المجموعة (اختياري)">
                    <div class="absolute right-4 top-1/2 transform -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-all duration-300">
                        <i class="fas fa-layer-group text-gray-400 text-xl"></i>
                    </div>
                    @error('collection')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <!-- Image Upload -->
            <div class="mb-8">
                <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <div class="flex items-center justify-center w-8 h-8 bg-gradient-to-br from-green-500 to-teal-600 rounded-xl mr-3">
                        <i class="fas fa-image text-white text-sm"></i>
                    </div>
                    الصورة
                </h3>
                
                <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-green-500 transition-colors group">
                    <div class="mb-4">
                        <div class="flex items-center justify-center w-20 h-20 bg-green-100 rounded-full mx-auto mb-4 group-hover:bg-green-200 transition-colors">
                            <i class="fas fa-cloud-upload-alt text-green-600 text-3xl"></i>
                        </div>
                        <p class="text-lg font-medium text-gray-900 mb-2">اسحب وأفلت الصورة هنا</p>
                        <p class="text-gray-600 mb-4">أو</p>
                        <label class="bg-green-500 text-white px-6 py-3 rounded-xl hover:bg-green-600 transition-colors cursor-pointer inline-block">
                            <i class="fas fa-folder-open ml-2"></i>
                            اختر ملف
                            <input type="file" name="image" class="hidden" accept="image/*" onchange="previewImage(this)">
                        </label>
                    </div>
                    <p class="text-sm text-gray-500">PNG, JPG, GIF (Max 10MB)</p>
                    
                    <!-- Image Preview -->
                    <div id="imagePreview" class="hidden mt-6">
                        <img id="previewImg" src="" alt="Preview" class="w-full h-64 object-cover rounded-xl">
                        <button type="button" onclick="removeImage()" class="mt-4 text-red-500 hover:text-red-700">
                            <i class="fas fa-trash ml-2"></i>
                            إزالة الصورة
                        </button>
                    </div>
                </div>
                @error('image')
                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>
            
            <!-- Modal Footer -->
            <div class="flex justify-between items-center pt-8 border-t-2 border-gray-200">
                <div class="flex items-center text-gray-600">
                    <div class="flex items-center justify-center w-10 h-10 bg-green-100 rounded-xl mr-3">
                        <i class="fas fa-shield-alt text-green-600"></i>
                    </div>
                    <span class="text-sm font-medium">بياناتك آمنة ومشفرة</span>
                </div>
                <div class="flex gap-4">
                    <button type="button" onclick="closeCreateModal()" class="px-8 py-4 border-2 border-gray-300 text-gray-700 rounded-2xl hover:bg-gray-50 hover:border-gray-400 transition-all duration-300 transform hover:-translate-y-1">
                        <i class="fas fa-times ml-2"></i>
                        إلغاء
                    </button>
                    <button type="submit" class="bg-gradient-to-r from-purple-500 via-pink-600 to-red-600 text-white px-12 py-4 rounded-2xl hover:from-purple-600 hover:via-pink-700 hover:to-red-700 transition-all duration-300 shadow-2xl hover:shadow-3xl transform hover:-translate-y-2 flex items-center">
                        <i class="fas fa-plus ml-3 text-xl"></i>
                        <span class="text-lg font-bold">إنشاء NFT</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function closeCreateModal() {
    document.getElementById('createModal').classList.add('hidden');
}

function showCreateModal() {
    document.getElementById('createModal').classList.remove('hidden');
}

function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('previewImg').src = e.target.result;
            document.getElementById('imagePreview').classList.remove('hidden');
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function removeImage() {
    document.getElementById('imagePreview').classList.add('hidden');
    document.querySelector('input[name="image"]').value = '';
}
</script>
@endsection

@push('scripts')
<script>
// Cache buster: {{ time() }}
function exportNFTs() {
    window.location.href = '{{ route('blockchain.metaverse.nft') }}?export=1';
}

function showNFTDetails(id) {
    console.log('Show NFT details called with ID:', id);
    // Find NFT data and show in modal
    const nftData = @json($nfts->items());
    const nft = nftData.find(n => n.id == id);
    
    if(nft) {
        // Create modal with NFT details
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
        modal.innerHTML = `
            <div class="bg-white rounded-2xl p-6 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-start mb-4">
                    <h2 class="text-2xl font-bold text-gray-900">تفاصيل NFT</h2>
                    <button onclick="closeModal(this)" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <img src="${nft.image_url || '/placeholder-nft.jpg'}" alt="${nft.name}" class="w-full h-64 object-cover rounded-xl">
                    </div>
                    <div class="space-y-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">${nft.name}</h3>
                            <p class="text-gray-600">${nft.description || 'لا يوجد وصف'}</p>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500">السعر</p>
                                <p class="text-xl font-bold text-green-600">${nft.price || 0} ETH</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">الفئة</p>
                                <p class="text-lg font-medium">${nft.category || 'غير محدد'}</p>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">المالك</p>
                            <p class="text-sm font-mono bg-gray-100 p-2 rounded">${nft.owner_address || 'غير محدد'}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">الرمز التعريفي</p>
                            <p class="text-sm font-mono bg-gray-100 p-2 rounded">#${nft.token_id || 'N/A'}</p>
                        </div>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
    }
}

function closeModal(button) {
    button.closest('.fixed').remove();
}

function addToCart(id, button) {
    console.log('Add to cart called with ID:', id);
    
    // Disable button immediately
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin ml-2"></i> جاري الإضافة...';
    
    // Send AJAX request to add to cart
    fetch('{{ route("cart.add") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            nft_id: id,
            type: 'nft'
        })
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            // Update button state
            button.className = 'flex-1 bg-gradient-to-r from-gray-400 to-gray-500 text-white py-3 rounded-xl cursor-not-allowed font-medium text-sm shadow-lg flex items-center justify-center';
            button.innerHTML = '<i class="fas fa-check-circle ml-2"></i> في السلة';
            
            // Update cart counter in navbar
            updateCartCounter(data.cart_count);
            
            // Show success message
            showNotification('تمت إضافة NFT إلى السلة بنجاح', 'success');
            
            // Refresh page after 1 second to update cart state
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            // Re-enable button on error
            button.disabled = false;
            button.innerHTML = '<i class="fas fa-shopping-cart ml-2"></i> إضافة للسلة';
            showNotification(data.message || 'حدث خطأ', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        // Re-enable button on error
        button.disabled = false;
        button.innerHTML = '<i class="fas fa-shopping-cart ml-2"></i> إضافة للسلة';
        showNotification('حدث خطأ أثناء إضافة إلى السلة', 'error');
    });
}

function updateCartCounter(count) {
    const cartCounter = document.querySelector('.cart-counter');
    if(cartCounter) {
        cartCounter.textContent = count;
        cartCounter.classList.remove('hidden');
    }
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
        type === 'success' ? 'bg-green-500' : 
        type === 'error' ? 'bg-red-500' : 'bg-blue-500'
    } text-white`;
    notification.innerHTML = `
        <div class="flex items-center">
            <i class="fas fa-${
                type === 'success' ? 'check-circle' : 
                type === 'error' ? 'exclamation-circle' : 'info-circle'
            } ml-2"></i>
            ${message}
        </div>
    `;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

function buyNFT(id) {
    console.log('Buy NFT called with ID:', id);
    if(confirm('هل أنت متأكد من شراء هذا NFT؟')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route('blockchain.metaverse.nfts.buy', ':nft') }}'.replace(':nft', id);
        console.log('Buy action URL:', form.action);
        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = '{{ csrf_token() }}';
        form.appendChild(csrf);
        document.body.appendChild(form);
        form.submit();
    }
}

function sellNFT(id) {
    console.log('Sell NFT called with ID:', id);
    const price = prompt('أدخل سعر البيع (ETH):');
    if(price && price > 0) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route('blockchain.metaverse.nfts.sell', ':nft') }}'.replace(':nft', id);
        console.log('Sell action URL:', form.action);
        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = '{{ csrf_token() }}';
        form.appendChild(csrf);
        const priceInput = document.createElement('input');
        priceInput.type = 'hidden';
        priceInput.name = 'price';
        priceInput.value = price;
        form.appendChild(priceInput);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush

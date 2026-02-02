@extends('admin.layouts.admin')

@section('title', 'سجل NFT')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text3xl font-bold text-gray-900 mb-2">سجل NFT</h1>
                <p class="textحق-600">إدارة وتتبعع الرموز غير القابلة للتبديل (NFT)</p>
            </div>
            
            <div class="flex gap-2">
                <button class="bg-green-600 text-white px-6 py-3 rounded-xl hover:bg-green-700 transition-colors duration-200 font-medium">
                    <i class="fas fa-plus ml-2"></i>
                    إنشاء NFT
                </button>
                <button class="bg-blue-600 text-white px-6 py-3 rounded-xl hover:bg-blue-700 transition-colors duration-200 font-medium">
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
                    <p class="text-3xl font-bold">12,456</p>
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
                    <p class="text-3xl font-bold">3,789</p>
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
                    <p class="text-3xl font-bold">$456.7K</p>
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
p class="text-3xl font-bold">234</p>
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
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <input type="text" placeholder="بحث عن NFT..." class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div class="flex gap-2">
                <select class="px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option>جميع الأنواعل</option>
                    <option>فن</option>
option>موسيقى</option>
                    <option>ألعاب</option>
                    <option>عقارات</option>
                    <option>مقتنيات</option>
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

    <!-- NFT Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @for($i = 1; $i <= 12; $i++)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg transition-all duration-300">
            <div class="relative">
                <img src="https://picsum.photos/seed/nft{{ $i }}/400/400.jpg" alt="NFT {{ $i }}" class="w-full h-48 object-cover">
                <div class="absolute top-2 right-2">
                    <span class="bg-blue-500 text-white px-2 py-1 rounded-full text-xs font-medium">
                        {{ ['فريد', 'أحمر', 'مملوك', 'نادر'][array_rand(['فريد', 'أحمر', 'مملوك', 'نادر'])] }}
                    </span>
                </div>
                <div class="absolute bottom-2 left-2">
                    <span class="bg-black/70 text-white px-2 py-1 rounded-full text-xs font-medium">
                        #{{ str_pad($i, 6, '0', STR_PAD_LEFT) }}
                    </span>
                </div>
            </div>
            
            <div class="p-4">
                <h3 class="font-bold text-lg text-gray-900 mb-2">NFT #{{ str_pad($i, 6, '0', STR_PAD_LEFT) }}</h3>
                <p class="text-gray-600 text-sm mb-3">هذا NFT فريد من نوع {{ collect(['فن', 'موسيقى', 'ألعاب', 'عقارات', 'مقتنيات'])->random() }}</p>
                
                <div class="flex items-center justify-between mb-3">
                    <div class="text-2xl font-bold text-blue-600">{{ number_format(mt_rand(0.01, 10), 4) }} ETH</div>
                    <div class="text-right">
                        <div class="text-sm text-gray-500">${{ number_format(mt_rand(0.01, 10) * 2000, 2) }}</div>
                    </div>
                </div>

                <div class="flex items-center gap-2 mb-3">
                    <div class="flex items-center">
                        <i class="fas fa-cube text-blue-500"></i>
                        <span class="text-sm text-gray-600">ERC-721</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-link text-purple-500"></i>
                        <span class="text-sm text-gray-600">{{ substr('0x' . bin2hex(random_bytes(20)), 0, 10) }}...</span>
                    </div>
                </div>

                <div class="flex space-x-2 space-x-reverse">
                    <button class="flex-1 bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition-colors duration-200 font-medium text-sm">
                        <i class="fas fa-eye ml-2"></i>
                        عرض
                    </button>
                    <button class="flex-1 bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 transition-colors duration-200 font-medium text-sm">
                        <i class="fas fa-exchange-alt ml-2"></i>
                        بيع
                    </button>
                </div>
            </div>
        </div>
        @endfor
    </div>

    <!-- Pagination -->
    <div class="flex justify-center mt-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 px-4 py-3">
            <p class="text-sm text-gray-600">
                عرض 12 NFTs من إجمالي 12,456 NFT
            </p>
        </div>
    </div>
</div>
@endsection

@extends('admin.layouts.admin')

@section('title', 'العقارات الافتراضية')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">العقارات الافتراضية</h1>
                <p class="text-gray-600">استكشف وشراء العقارات في العالم الافتراضي</p>
            </div>
            
            <div class="flex gap-2">
                <button class="bg-green-600 text-white px-6 py-3 rounded-xl hover:bg-green-700 transition-colors duration-200 font-medium">
                    <i class="fas fa-plus ml-2"></i>
                    إضافة عقار
                </button>
                <button class="bg-blue-600 text-white px-6 py-3 rounded-xl hover:bg-blue-700 transition-colors duration-200 font-medium">
                    <i class="fas fa-search ml-2"></i>
                    بحث متقدم
                </button>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Total Properties -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium mb-1">إجمالي العقارات</p>
                    <p class="text-3xl font-bold">1,234</p>
                    <p class="text-blue-100 text-xs mt-2">
                        <i class="fas fa-home ml-1"></i>
                        جميع العقارات
                    </p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-home text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Available Properties -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium mb-1">العقارات المتاحة</p>
                    <p class="text-3xl font-bold">856</p>
                    <p class="text-green-100 text-xs mt-2">
                        <i class="fas fa-check-circle ml-1"></i>
                        للبيع
                    </p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-check-circle text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Value -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium mb-1">إجمالي القيمة</p>
                    <p class="text-3xl font-bold">$2.5M</p>
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

        <!-- Active Users -->
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm font-medium mb-1">المستخدمون النشطون</p>
                    <p class="text-3xl font-bold">456</p>
                    <p class="text-orange-100 text-xs mt-2">
                        <i class="fas fa-users ml-1"></i>
                        هذا الشهر
                    </p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-users text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <input type="text" placeholder="بحث عن عقار..." class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div class="flex gap-2">
                <select class="px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option>جميع الأنواع</option>
                    <option>شقة</option>
                    <option>فيلا</option>
option>تجارية</option>
                    <option>صناعية</option>
                </select>
                <select class="px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option>جميع المناطق</option>
                    <option>وسط المدينة</option>
                    <option>الأحياء السكنية</option>
                    <option>المناطق التجارية</option>
                </select>
                <select class="px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option>السعر من الأقل للأعلى</option>
                    <option>السعر من الأعلى للأقل</option>
                    <option>الأحدث</option>
                    <option>الأقدم</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Properties Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @for($i = 1; $i <= 6; $i++)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-all duration-300">
            <div class="relative">
                <img src="https://picsum.photos/seed/metaverse{{ $i }}/400/250.jpg" alt="Property {{ $i }}" class="w-full h-48 object-cover">
                <div class="absolute top-2 right-2">
                    <span class="bg-green-500 text-white px-2 py-1 rounded-full text-xs font-medium">
                        متاح
                    </span>
                </div>
            </div>
            
            <div class="p-6">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h3 class="font-bold text-lg text-gray-900 mb-1">عقار افتراضي {{ $i }}</h3>
                        <p class="text-gray-600 text-sm mb-2">
                            <i class="fas fa-map-marker-alt ml-1"></i>
                            المنطقة {{ $i }}
                        </p>
                        <div class="flex items-center gap-2 text-sm">
                            <span class="text-blue-600 font-semibold">{{ rand(50, 500) }} م²</span>
                            <span class="text-gray-400">|</span>
                            <span class="text-purple-600 font-semibold">{{ rand(1, 10) }} غرف</span>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl font-bold text-green-600">${{ number_format(rand(50000, 500000), 0) }}</p>
                        <p class="text-sm text-gray-500">${{ number_format(rand(50000, 500000) / rand(50, 500), 2) }}/م²</p>
                    </div>
                </div>

                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-4">
                        <div class="flex items-center">
                            <i class="fas fa-bed text-gray-400 ml-2"></i>
                            <span class="text-sm text-gray-600">{{ rand(1, 5) }} غرف نوم</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-bath text-gray-400 ml-2"></i>
                            <span class="text-sm text-gray-600">{{ rand(1, 3) }} حمام</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-car text-gray-400 ml-2"></i>
                            <span class="text-sm text-gray-600">{{ rand(0, 2) }} موقف</span>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <div class="flex items-center">
                            <i class="fas fa-wifi text-green-500 ml-2"></i>
                            <span class="text-sm text-green-600">إنترنت عالي السرعة</span>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap gap-2 mb-4">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        <i class="fas fa-home ml-1"></i>
                        {{ ['شقة', 'فيلا', 'تجارية', 'صناعية'][array_rand(['شقة', 'فيلا', 'تجارية', 'صناعية'])] }}
                    </span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                        <i class="fas fa-star ml-1"></i>
                        {{ ['مميز', 'حديث', 'مفروش', 'فاخر'][array_rand(['مميز', 'حديث', 'مفروش', 'فاخر'])] }}
                    </span>
                </div>

                <div class="flex space-x-2 space-x-reverse">
                    <button class="flex-1 bg-blue-600 text-white py-2 rounded-xl hover:bg-blue-700 transition-colors duration-200 font-medium text-sm">
                        <i class="fas fa-eye ml-2"></i>
                        عرض
                    </button>
                    <button class="flex-1 bg-green-600 text-white py-2 rounded-xl hover:bg-green-700 transition-colors duration-200 font-medium text-sm">
                        <i class="fas fa-shopping-cart ml-2"></i>
                        شراء
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
                عرض 6 عقارات من إجمالي 1,234 عقار
            </p>
        </div>
    </div>
</div>
@endsection

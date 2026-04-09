@extends('admin.layouts.admin')

@section('title', 'المنظمات اللامركزية - DAOs')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">المنظمات اللامركزية - DAOs</h1>
                <p class="text-gray-600">إدارة وتنظيم المنظمات اللامركزية</p>
            </div>
            
            <div class="flex gap-2">
                <button class="bg-blue-600 text-white px-6 py-3 rounded-xl hover:bg-blue-700 transition-colors duration-200 font-medium">
                    <i class="fas fa-plus ml-2"></i>
                    إنشاء منظمة جديدة
                </button>
                <button class="bg-green-600 text-white px-6 py-3 rounded-xl hover:bg-green-700 transition-colors duration-200 font-medium">
                    <i class="fas fa-search ml-2"></i>
                    بحث متقدم
                </button>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total DAOs -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium mb-1">إجمالي المنظمات</p>
                    <p class="text-3xl font-bold">{{ $daos->total() }}</p>
                    <p class="text-blue-100 text-xs mt-2">
                        <i class="fas fa-arrow-up ml-1"></i>
                        +12% هذا الشهر
                    </p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-users text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Active DAOs -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium mb-1">المنظمات النشطة</p>
                    <p class="text-3xl font-bold">{{ $daos->where('status', 'active')->count() }}</p>
                    <p class="text-green-100 text-xs mt-2">
                        <i class="fas fa-check-circle ml-1"></i>
                        تعمل بكامل طاقتها
                    </p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-check-circle text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Members -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium mb-1">إجمالي الأعضاء</p>
                    <p class="text-3xl font-bold">{{ number_format($daos->sum('total_members'), 0) }}</p>
                    <p class="text-purple-100 text-xs mt-2">
                        <i class="fas fa-user-plus ml-1"></i>
                        +25% هذا الشهر
                    </p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-users text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Treasury -->
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm font-medium mb-1">إجمالي الخزينة</p>
                    <p class="text-3xl font-bold">{{ number_format($daos->sum('treasury_balance'), 2) }} ETH</p>
                    <p class="text-orange-100 text-xs mt-2">
                        <i class="fas fa-coins ml-1"></i>
                        +8% هذا الشهر
                    </p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-vault text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <input type="text" placeholder="بحث عن منظمة..." class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div class="flex gap-2">
                <select class="px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option>جميع الحالات</option>
                    <option>نشط</option>
                    <option>غير نشط</option>
                    <option>مقترح</option>
                </select>
                <select class="px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option>ترتيب حسب</option>
                    <option>الأحدث</option>
                    <option>الأكبر خزينة</option>
                    <option>الأكثر أعضاء</option>
                </select>
            </div>
        </div>
    </div>

    <!-- DAOs Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($daos as $dao)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-all duration-300">
            <div class="p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center space-x-3 space-x-reverse">
                        <div class="bg-blue-100 rounded-full p-3">
                            <i class="fas fa-building text-blue-600 text-lg"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900 text-lg">{{ $dao->name }}</h3>
                            <p class="text-sm text-gray-500">{{ $dao->purpose }}</p>
                        </div>
                    </div>
                    @if($dao->status === 'active')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <span class="w-2 h-2 bg-green-400 rounded-full ml-2"></span>
                            نشط
                        </span>
                    @elseif($dao->status === 'inactive')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                            غير نشط
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            مقترح
                        </span>
                    @endif
                </div>

                <div class="space-y-3 mb-4">
                    <div class="flex justify-between">
                        <span class="text-gray-600">الرمز:</span>
                        <span class="font-semibold">{{ $dao->token_symbol }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">الأعضاء:</span>
                        <span class="font-semibold">{{ $dao->total_members }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">الخزينة:</span>
                        <span class="font-semibold">{{ number_format($dao->treasury_balance, 2) }} ETH</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">النصاب:</span>
                        <span class="font-semibold">{{ number_format($dao->quorum, 2) }}%</span>
                    </div>
                </div>

                <div class="flex space-x-2 space-x-reverse">
                    <a href="{{ route('blockchain.dao.show', $dao->id) }}" class="flex-1 bg-blue-600 text-white py-2 rounded-xl hover:bg-blue-700 transition-colors duration-200 font-medium text-sm text-center">
                        <i class="fas fa-eye ml-2"></i>
                        عرض
                    </a>
                    <a href="{{ route('blockchain.dao.members', $dao->id) }}" class="flex-1 bg-green-600 text-white py-2 rounded-xl hover:bg-green-700 transition-colors duration-200 font-medium text-sm text-center">
                        <i class="fas fa-users ml-2"></i>
                        عضوية
                    </a>
                    <a href="{{ route('blockchain.dao.proposals', $dao->id) }}" class="flex-1 bg-purple-600 text-white py-2 rounded-xl hover:bg-purple-700 transition-colors duration-200 font-medium text-sm text-center">
                        <i class="fas fa-vote-yea ml-2"></i>
                        تصويت
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="flex justify-center">
        {{ $daos->links() }}
    </div>
</div>
@endsection

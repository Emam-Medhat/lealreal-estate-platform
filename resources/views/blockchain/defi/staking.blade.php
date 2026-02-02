@extends('admin.layouts.admin')

@section('title', 'تخزين الأصول - Staking')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">تخزين الأصول - Staking</h1>
                <p class="text-gray-600">تخزين أصولك وكسب مكافآت</p>
            </div>
            
            <!-- Navigation Tabs -->
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('blockchain.defi.lending') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors duration-200">
                    <i class="fas fa-hand-holding-usd ml-2"></i>
                    القروض
                </a>
                <a href="{{ route('blockchain.defi.staking') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-colors duration-200">
                    <i class="fas fa-coins ml-2"></i>
                    التخزين
                </a>
                <a href="{{ route('blockchain.defi.yield') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors duration-200">
                    <i class="fas fa-chart-line ml-2"></i>
                    زراعة العوائد
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Staked -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium mb-1">إجمالي المخزون</p>
                    <p class="text-3xl font-bold">{{ number_format($totalStaked, 2) }} ETH</p>
                    <p class="text-green-100 text-xs mt-2">
                        <i class="fas fa-arrow-up ml-1"></i>
                        +15% هذا الشهر
                    </p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-lock text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Active Stakes -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium mb-1">المراكز النشطة</p>
                    <p class="text-3xl font-bold">{{ $activeStakes }}</p>
                    <p class="text-blue-100 text-xs mt-2">
                        <i class="fas fa-check-circle ml-1"></i>
                        جميعها نشطة
                    </p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-bullseye text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Average APY -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium mb-1">متوسط العائد السنوي</p>
                    <p class="text-3xl font-bold">{{ number_format($averageAPY, 2) }}%</p>
                    <p class="text-purple-100 text-xs mt-2">
                        <i class="fas fa-chart-line ml-1"></i>
                        +5% هذا الشهر
                    </p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-percentage text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Daily Rewards -->
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm font-medium mb-1">المكافآت اليومية</p>
                    <p class="text-3xl font-bold">{{ number_format($totalStaked * ($averageAPY / 100) / 365, 4) }} ETH</p>
                    <p class="text-orange-100 text-xs mt-2">
                        <i class="fas fa-calendar-day ml-1"></i>
                        كل يوم
                    </p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-gift text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Staking Options -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach($stakingPools as $pool)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-all duration-300">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-6">
                <div class="flex items-center justify-between">
                    <div class="text-white">
                        <h3 class="text-xl font-bold mb-1">{{ $pool->name }}</h3>
                        <p class="text-blue-100 text-sm">{{ $pool->description }}</p>
                    </div>
                    <div class="bg-white/20 rounded-full p-3">
                        <i class="fab fa-ethereum text-white text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-3 mb-4">
                    <div class="flex justify-between">
                        <span class="text-gray-600">معدل المكافأة:</span>
                        <span class="font-semibold text-green-600">{{ number_format($pool->apy, 2) }}%</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">السيولة الكلية:</span>
                        <span class="font-semibold">{{ number_format($pool->total_liquidity, 4) }} {{ explode('/', $pool->token_pair)[0] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">الحد الأدنى:</span>
                        <span class="font-semibold">{{ number_format($pool->min_deposit, 4) }} {{ explode('/', $pool->token_pair)[0] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">البروتوكول:</span>
                        <span class="font-semibold">{{ ucfirst($pool->protocol) }}</span>
                    </div>
                </div>
                <button onclick="window.location.href='{{ route('blockchain.defi.pool.deposit', $pool->id) }}'" class="w-full bg-blue-600 text-white py-3 rounded-xl hover:bg-blue-700 transition-colors duration-200 font-medium">
                    <i class="fas fa-plus ml-2"></i>
                    تخزين الآن
                </button>
            </div>
        </div>
        @endforeach
    </div>

    <!-- My Stakes -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold text-gray-900">مراكز التخزين الخاصة بي</h2>
                <button class="text-green-600 hover:text-green-700 text-sm font-medium transition-colors duration-200">
                    سجل المكافآت
                    <i class="fas fa-download mr-1"></i>
                </button>
            </div>
        </div>
        
        <div class="p-6">
            <div class="text-center py-12">
                <i class="fas fa-coins text-gray-300 text-6xl mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">لا توجد مراكز تخزين</h3>
                <p class="text-gray-600 mb-6">ابدأ بتخزين أصولك لكسب المكافآت</p>
                <button class="bg-green-600 text-white px-6 py-3 rounded-xl hover:bg-green-700 transition-colors duration-200 font-medium">
                    <i class="fas fa-plus ml-2"></i>
                    بدء التخزين
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('admin.layouts.admin')

@section('title', 'زراعة العوائد - Yield Farming')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">زراعة العوائد - Yield Farming</h1>
                <p class="text-gray-600">توفير السيولة للمجمعات وكسب العوائد</p>
            </div>
            
            <!-- Navigation Tabs -->
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('blockchain.defi.lending') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors duration-200">
                    <i class="fas fa-hand-holding-usd ml-2"></i>
                    القروض
                </a>
                <a href="{{ route('blockchain.defi.staking') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors duration-200">
                    <i class="fas fa-coins ml-2"></i>
                    التخزين
                </a>
                <a href="{{ route('blockchain.defi.yield') }}" class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-xl hover:bg-purple-700 transition-colors duration-200">
                    <i class="fas fa-chart-line ml-2"></i>
                    زراعة العوائد
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Yield -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium mb-1">إجمالي العائد</p>
                    <p class="text-3xl font-bold">{{ number_format($totalYield, 2) }} ETH</p>
                    <p class="text-purple-100 text-xs mt-2">
                        <i class="fas fa-arrow-up ml-1"></i>
                        +25% هذا الشهر
                    </p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-chart-line text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Active Pools -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium mb-1">المجمعات النشطة</p>
                    <p class="text-3xl font-bold">{{ $yieldPools->count() }}</p>
                    <p class="text-green-100 text-xs mt-2">
                        <i class="fas fa-check-circle ml-1"></i>
                        جميعها نشطة
                    </p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-water text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Average Yield -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium mb-1">متوسط العائد</p>
                    <p class="text-3xl font-bold">{{ number_format($averageYield, 2) }}%</p>
                    <p class="text-blue-100 text-xs mt-2">
                        <i class="fas fa-chart-line ml-1"></i>
                        +8% هذا الشهر
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
                    <p class="text-3xl font-bold">{{ number_format($totalYield / 365, 4) }} ETH</p>
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

    <!-- Yield Farming Options -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach($yieldPools as $pool)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-all duration-300">
            <div class="bg-gradient-to-r from-purple-500 to-purple-600 p-6">
                <div class="flex items-center justify-between">
                    <div class="text-white">
                        <h3 class="text-xl font-bold mb-1">{{ $pool->name }}</h3>
                        <p class="text-purple-100 text-sm">{{ $pool->description }}</p>
                    </div>
                    <div class="bg-white/20 rounded-full p-3">
                        <i class="fas fa-seedling text-white text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-3 mb-4">
                    <div class="flex justify-between">
                        <span class="text-gray-600">معدل العائد:</span>
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
                        <span class="text-gray-600">حجم التداول 24س:</span>
                        <span class="font-semibold">${{ number_format($pool->volume_24h, 2) }}</span>
                    </div>
                </div>
                <button onclick="window.location.href='{{ route('blockchain.defi.pool.deposit', $pool->id) }}'" class="w-full bg-purple-600 text-white py-3 rounded-xl hover:bg-purple-700 transition-colors duration-200 font-medium">
                    <i class="fas fa-plus ml-2"></i>
                    زراعة الآن
                </button>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Popular Pools -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold text-gray-900">المجمعات الشائعة</h2>
                <button onclick="window.location.href='{{ route('blockchain.defi.pools') }}'" class="text-purple-600 hover:text-purple-700 text-sm font-medium transition-colors duration-200">
                    عرض الكل
                    <i class="fas fa-arrow-left mr-1"></i>
                </button>
            </div>
        </div>
        
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- ETH/USDC Pool -->
                <div class="border border-gray-200 rounded-xl p-4 hover:shadow-md transition-all duration-300">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2 space-x-reverse">
                            <div class="bg-blue-100 rounded-full p-2">
                                <i class="fab fa-ethereum text-blue-600 text-sm"></i>
                            </div>
                            <div class="bg-green-100 rounded-full p-2">
                                <i class="fas fa-dollar-sign text-green-600 text-sm"></i>
                            </div>
                        </div>
                        <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full font-medium">Hot</span>
                    </div>
                    <h4 class="font-semibold text-gray-900 mb-1">ETH/USDC</h4>
                    <p class="text-sm text-gray-600 mb-3">Uniswap V3</p>
                    <div class="flex justify-between items-center">
                        <span class="text-green-600 font-bold">25.8%</span>
                        <button class="text-purple-600 hover:text-purple-700 text-sm font-medium">
                            زراعة
                        </button>
                    </div>
                </div>

                <!-- ETH/DAI Pool -->
                <div class="border border-gray-200 rounded-xl p-4 hover:shadow-md transition-all duration-300">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2 space-x-reverse">
                            <div class="bg-blue-100 rounded-full p-2">
                                <i class="fab fa-ethereum text-blue-600 text-sm"></i>
                            </div>
                            <div class="bg-yellow-100 rounded-full p-2">
                                <span class="text-yellow-600 text-xs font-bold">D</span>
                            </div>
                        </div>
                        <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full font-medium">Popular</span>
                    </div>
                    <h4 class="font-semibold text-gray-900 mb-1">ETH/DAI</h4>
                    <p class="text-sm text-gray-600 mb-3">SushiSwap</p>
                    <div class="flex justify-between items-center">
                        <span class="text-green-600 font-bold">18.5%</span>
                        <button class="text-purple-600 hover:text-purple-700 text-sm font-medium">
                            زراعة
                        </button>
                    </div>
                </div>

                <!-- USDC/USDT Pool -->
                <div class="border border-gray-200 rounded-xl p-4 hover:shadow-md transition-all duration-300">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2 space-x-reverse">
                            <div class="bg-green-100 rounded-full p-2">
                                <i class="fas fa-dollar-sign text-green-600 text-sm"></i>
                            </div>
                            <div class="bg-orange-100 rounded-full p-2">
                                <span class="text-orange-600 text-xs font-bold">₮</span>
                            </div>
                        </div>
                        <span class="bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded-full font-medium">Stable</span>
                    </div>
                    <h4 class="font-semibold text-gray-900 mb-1">USDC/USDT</h4>
                    <p class="text-sm text-gray-600 mb-3">Curve</p>
                    <div class="flex justify-between items-center">
                        <span class="text-green-600 font-bold">8.2%</span>
                        <button class="text-purple-600 hover:text-purple-700 text-sm font-medium">
                            زراعة
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('admin.layouts.admin')

@section('title', 'مجمعات DeFi')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100">
    <!-- Animated Background Elements -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-20 left-20 w-96 h-96 bg-gradient-to-r from-blue-400 to-purple-400 rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-pulse"></div>
        <div class="absolute top-60 right-20 w-80 h-80 bg-gradient-to-r from-purple-400 to-pink-400 rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-pulse animation-delay-2000"></div>
        <div class="absolute bottom-40 left-1/3 w-72 h-72 bg-gradient-to-r from-indigo-400 to-blue-400 rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-pulse animation-delay-4000"></div>
    </div>

    <div class="relative z-10 min-h-screen">
        <div class="container mx-auto px-4 py-8">
            <!-- Header Section -->
            <div class="bg-white/95 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 p-8 mb-8">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                    <div>
                        <h1 class="text-5xl font-black bg-gradient-to-r from-blue-600 via-purple-600 to-indigo-600 bg-clip-text text-transparent mb-4">
                            <i class="fas fa-water ml-4"></i>
                            مجمعات DeFi
                        </h1>
                        <p class="text-xl text-gray-600">اكتشف أفضل فرص الربح في عالم التمويل اللامركزي</p>
                    </div>
                    
                    <!-- Navigation Tabs -->
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('blockchain.defi.index') }}" class="px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-2xl hover:from-blue-700 hover:to-blue-800 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                            <i class="fas fa-chart-line ml-2"></i>
                            لوحة التحكم
                        </a>
                        <a href="{{ route('blockchain.defi.lending') }}" class="px-6 py-3 bg-white border-2 border-gray-200 text-gray-700 rounded-2xl hover:border-blue-500 hover:text-blue-600 transition-all duration-300">
                            <i class="fas fa-hand-holding-usd ml-2"></i>
                            القروض
                        </a>
                        <a href="{{ route('blockchain.defi.staking') }}" class="px-6 py-3 bg-white border-2 border-gray-200 text-gray-700 rounded-2xl hover:border-green-500 hover:text-green-600 transition-all duration-300">
                            <i class="fas fa-coins ml-2"></i>
                            التخزين
                        </a>
                        <a href="{{ route('blockchain.defi.yield') }}" class="px-6 py-3 bg-white border-2 border-gray-200 text-gray-700 rounded-2xl hover:border-orange-500 hover:text-orange-600 transition-all duration-300">
                            <i class="fas fa-seedling ml-2"></i>
                            زراعة العوائد
                        </a>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-2xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                            <i class="fas fa-wallet text-white text-xl"></i>
                        </div>
                        <span class="bg-white/20 px-3 py-1 rounded-full text-sm font-medium">+12.5%</span>
                    </div>
                    <h3 class="text-3xl font-bold mb-1">{{ number_format($activePools->sum('total_liquidity'), 0) }}</h3>
                    <p class="text-blue-100">إجمالي السيولة</p>
                </div>

                <div class="bg-gradient-to-br from-emerald-600 to-emerald-700 rounded-2xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                            <i class="fas fa-percentage text-white text-xl"></i>
                        </div>
                        <span class="bg-white/20 px-3 py-1 rounded-full text-sm font-medium">+8.3%</span>
                    </div>
                    <h3 class="text-3xl font-bold mb-1">{{ number_format($activePools->avg('apy'), 1) }}%</h3>
                    <p class="text-emerald-100">متوسط العائد السنوي</p>
                </div>

                <div class="bg-gradient-to-br from-purple-600 to-purple-700 rounded-2xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                            <i class="fas fa-users text-white text-xl"></i>
                        </div>
                        <span class="bg-white/20 px-3 py-1 rounded-full text-sm font-medium">+24.1%</span>
                    </div>
                    <h3 class="text-3xl font-bold mb-1">{{ $activePools->sum('active_positions_count') }}</h3>
                    <p class="text-purple-100">مستثمر نشط</p>
                </div>

                <div class="bg-gradient-to-br from-amber-600 to-orange-700 rounded-2xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                            <i class="fas fa-fire text-white text-xl"></i>
                        </div>
                        <span class="bg-white/20 px-3 py-1 rounded-full text-sm font-medium">+15.7%</span>
                    </div>
                    <h3 class="text-3xl font-bold mb-1">{{ $activePools->where('is_hot', true)->count() }}</h3>
                    <p class="text-amber-100">مجمعات ساخنة</p>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="bg-white/95 backdrop-blur-xl rounded-2xl shadow-xl border border-white/20 p-6 mb-8">
                <div class="flex flex-col lg:flex-row gap-4">
                    <div class="flex-1">
                        <div class="relative">
                            <input type="text" placeholder="بحث عن مجمع..." 
                                class="w-full px-6 py-4 bg-gray-50 border-2 border-gray-200 rounded-2xl text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all duration-300 text-lg">
                            <i class="fas fa-search absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <select class="px-6 py-4 bg-gray-50 border-2 border-gray-200 rounded-2xl text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all duration-300">
                            <option>جميع الأنواع</option>
                            <option value="liquidity">سيولة (Liquidity)</option>
                            <option value="staking">تخزين (Staking)</option>
                            <option value="lending">إقراض (Lending)</option>
                            <option value="yield">زراعة العوائد (Yield Farming)</option>
                        </select>
                        <select class="px-6 py-4 bg-gray-50 border-2 border-gray-200 rounded-2xl text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all duration-300">
                            <option>ترتيب حسب</option>
                            <option>الأعلى APY</option>
                            <option>الأكبر سيولة</option>
                            <option>الأحدث</option>
                            <option>الأكثر شعبية</option>
                        </select>
                        <button class="px-6 py-4 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-2xl hover:from-blue-700 hover:to-purple-700 transition-all duration-300 transform hover:scale-105 font-medium shadow-xl">
                            <i class="fas fa-filter ml-2"></i>
                            فلترة
                        </button>
                    </div>
                </div>
            </div>

            <!-- Pools Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                @foreach($activePools as $pool)
                <div class="group bg-white/95 backdrop-blur-xl rounded-2xl border border-white/20 overflow-hidden hover:bg-white hover:shadow-2xl transition-all duration-500 transform hover:scale-105">
                    <!-- Pool Header -->
                    <div class="p-6 border-b border-gray-100">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center space-x-4 space-x-reverse">
                                <div class="relative">
                                    <div class="w-16 h-16 bg-gradient-to-br from-blue-600 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg">
                                        <i class="fas fa-water text-white text-2xl"></i>
                                    </div>
                                    @if($pool->is_hot)
                                        <div class="absolute -top-1 -right-1 w-6 h-6 bg-red-500 rounded-full flex items-center justify-center">
                                            <i class="fas fa-fire text-white text-xs"></i>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900 mb-1">{{ $pool->name }}</h3>
                                    <p class="text-gray-600 text-sm">{{ $pool->token_pair }}</p>
                                </div>
                            </div>
                            @if($pool->is_active)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    <span class="w-2 h-2 bg-emerald-500 rounded-full ml-2 animate-pulse"></span>
                                    نشط
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800 border border-gray-200">
                                    غير نشط
                                </span>
                            @endif
                        </div>

                        <!-- Pool Stats -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-gray-500 text-sm mb-1">السيولة الإجمالية</p>
                                <p class="text-gray-900 font-bold text-lg">{{ number_format($pool->total_liquidity, 2) }} {{ explode('/', $pool->token_pair)[0] }}</p>
                                @if($pool->total_liquidity_usd)
                                    <p class="text-gray-500 text-sm">${{ number_format($pool->total_liquidity_usd, 2) }}</p>
                                @endif
                            </div>
                            <div>
                                <p class="text-gray-500 text-sm mb-1">معدل العائد السنوي</p>
                                <p class="text-emerald-600 font-bold text-lg">{{ number_format($pool->apy, 2) }}%</p>
                            </div>
                        </div>
                    </div>

                    <!-- Pool Type Badge -->
                    <div class="px-6 py-3">
                        @php
                            $typeConfig = [
                                'liquidity' => ['color' => 'blue', 'icon' => 'fa-water', 'name' => 'سيولة'],
                                'staking' => ['color' => 'emerald', 'icon' => 'fa-coins', 'name' => 'تخزين'],
                                'lending' => ['color' => 'purple', 'icon' => 'fa-hand-holding-usd', 'name' => 'إقراض'],
                                'yield' => ['color' => 'amber', 'icon' => 'fa-seedling', 'name' => 'زراعة عوائد'],
                            ];
                            $config = $typeConfig[$pool->type] ?? ['color' => 'gray', 'icon' => 'fa-circle', 'name' => 'أخرى'];
                        @endphp
                        <div class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-medium bg-{{ $config['color'] }}-100 text-{{ $config['color'] }}-800 border border-{{ $config['color'] }}-200">
                            <i class="fas {{ $config['icon'] }} ml-2"></i>
                            {{ $config['name'] }}
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="p-6 bg-gray-50">
                        <div class="grid grid-cols-3 gap-3">
                            <button onclick="window.location.href='{{ route('blockchain.defi.pool.show', $pool->id) }}'" 
                                class="py-3 bg-white border-2 border-gray-200 text-gray-700 rounded-xl hover:border-blue-500 hover:text-blue-600 transition-all duration-300 font-medium">
                                <i class="fas fa-eye ml-2"></i>
                                عرض
                            </button>
                            <button onclick="window.location.href='{{ route('blockchain.defi.pool.deposit', $pool->id) }}'" 
                                class="py-3 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition-all duration-300 font-medium">
                                <i class="fas fa-plus ml-2"></i>
                                إيداع
                            </button>
                            <button onclick="window.location.href='{{ route('blockchain.defi.pool.withdraw', $pool->id) }}'" 
                                class="py-3 bg-amber-600 text-white rounded-xl hover:bg-amber-700 transition-all duration-300 font-medium">
                                <i class="fas fa-minus ml-2"></i>
                                سحب
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Load More -->
            <div class="text-center mb-8">
                <button class="px-8 py-4 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-2xl hover:from-blue-700 hover:to-purple-700 transition-all duration-300 transform hover:scale-105 font-medium shadow-xl">
                    <i class="fas fa-arrow-down ml-2"></i>
                    تحميل المزيد
                </button>
            </div>

            <!-- Back to DeFi -->
            <div class="text-center">
                <a href="{{ route('blockchain.defi.index') }}" class="inline-flex items-center px-6 py-3 bg-white border-2 border-gray-200 text-gray-700 rounded-2xl hover:border-blue-500 hover:text-blue-600 transition-all duration-300 font-medium">
                    <i class="fas fa-arrow-right ml-2"></i>
                    العودة إلى DeFi
                </a>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fade-in 0.6s ease-out;
}

.animation-delay-2000 {
    animation-delay: 2s;
}

.animation-delay-4000 {
    animation-delay: 4s;
}

.backdrop-blur-xl {
    backdrop-filter: blur(24px);
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 4px;
}

::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.3);
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.5);
}
</style>
@endsection

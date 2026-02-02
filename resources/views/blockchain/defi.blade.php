@extends('admin.layouts.admin')

@section('title', 'التمويل اللامركزي - DeFi')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">التمويل اللامركزي - DeFi</h1>
                <p class="text-gray-600">إدارة القروض والتخزين وزراعة العوائد</p>
            </div>
            
            <!-- Navigation Tabs -->
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('blockchain.defi.lending') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors duration-200">
                    <i class="fas fa-hand-holding-usd ml-2"></i>
                    القروض
                </a>
                <a href="{{ route('blockchain.defi.staking') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors duration-200">
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
        <!-- Total Liquidity -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium mb-1">إجمالي السيولة</p>
                    <p class="text-3xl font-bold">{{ number_format($totalLiquidity, 2) }} ETH</p>
                    <p class="text-blue-100 text-xs mt-2">
                        <i class="fas fa-arrow-up ml-1"></i>
                        +12% هذا الشهر
                    </p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-water text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Borrowed -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium mb-1">إجمالي المقترض</p>
                    <p class="text-3xl font-bold">{{ number_format($totalBorrowed, 2) }} ETH</p>
                    <p class="text-green-100 text-xs mt-2">
                        <i class="fas fa-arrow-up ml-1"></i>
                        +8% هذا الأسبوع
                    </p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-hand-holding-usd text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Staked -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium mb-1">إجمالي المخزون</p>
                    <p class="text-3xl font-bold">{{ number_format($totalStaked, 2) }} ETH</p>
                    <p class="text-purple-100 text-xs mt-2">
                        <i class="fas fa-arrow-up ml-1"></i>
                        +15% هذا الشهر
                    </p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-lock text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Average APY -->
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm font-medium mb-1">متوسط العائد السنوي</p>
                    <p class="text-3xl font-bold">{{ number_format($averageAPY, 2) }}%</p>
                    <p class="text-orange-100 text-xs mt-2">
                        <i class="fas fa-chart-line ml-1"></i>
                        +5% هذا الشهر
                    </p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-percentage text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Lending Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-all duration-300">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-6">
                <div class="flex items-center justify-between">
                    <div class="text-white">
                        <h3 class="text-xl font-bold mb-1">إقرض الأموال</h3>
                        <p class="text-blue-100 text-sm">أقرض أصولك وكسب الفائدة</p>
                    </div>
                    <div class="bg-white/20 rounded-full p-3">
                        <i class="fas fa-hand-holding-usd text-white text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-3 mb-4">
                    <div class="flex justify-between">
                        <span class="text-gray-600">معدل الفائدة:</span>
                        <span class="font-semibold">5-15%</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">إجمالي القروض:</span>
                        <span class="font-semibold">{{ number_format($totalLent, 2) }} ETH</span>
                    </div>
                </div>
                <button onclick="window.location.href='{{ route('blockchain.defi.lending') }}'" class="w-full bg-blue-600 text-white py-3 rounded-xl hover:bg-blue-700 transition-colors duration-200 font-medium">
                    <i class="fas fa-plus ml-2"></i>
                    إقرض الآن
                </button>
            </div>
        </div>

        <!-- Staking Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-all duration-300">
            <div class="bg-gradient-to-r from-green-500 to-green-600 p-6">
                <div class="flex items-center justify-between">
                    <div class="text-white">
                        <h3 class="text-xl font-bold mb-1">تخزين الأصول</h3>
                        <p class="text-green-100 text-sm">تخزين أصولك وكسب مكافآت</p>
                    </div>
                    <div class="bg-white/20 rounded-full p-3">
                        <i class="fas fa-coins text-white text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-3 mb-4">
                    <div class="flex justify-between">
                        <span class="text-gray-600">معدل المكافأة:</span>
                        <span class="font-semibold">8-25%</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">إجمالي المخزون:</span>
                        <span class="font-semibold">{{ number_format($totalStaked, 2) }} ETH</span>
                    </div>
                </div>
                <button onclick="window.location.href='{{ route('blockchain.defi.staking') }}'" class="w-full bg-green-600 text-white py-3 rounded-xl hover:bg-green-700 transition-colors duration-200 font-medium">
                    <i class="fas fa-plus ml-2"></i>
                    تخزين الآن
                </button>
            </div>
        </div>

        <!-- Yield Farming Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-all duration-300">
            <div class="bg-gradient-to-r from-purple-500 to-purple-600 p-6">
                <div class="flex items-center justify-between">
                    <div class="text-white">
                        <h3 class="text-xl font-bold mb-1">زراعة العوائد</h3>
                        <p class="text-purple-100 text-sm">توفير السيولة وكسب العوائد</p>
                    </div>
                    <div class="bg-white/20 rounded-full p-3">
                        <i class="fas fa-chart-line text-white text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-3 mb-4">
                    <div class="flex justify-between">
                        <span class="text-gray-600">معدل العائد:</span>
                        <span class="font-semibold">20-150%</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">إجمالي العائد:</span>
                        <span class="font-semibold">{{ number_format($totalYield, 2) }} ETH</span>
                    </div>
                </div>
                <button onclick="window.location.href='{{ route('blockchain.defi.yield') }}'" class="w-full bg-purple-600 text-white py-3 rounded-xl hover:bg-purple-700 transition-colors duration-200 font-medium">
                    <i class="fas fa-plus ml-2"></i>
                    زراعة الآن
                </button>
            </div>
        </div>
    </div>

    <!-- Active Pools -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold text-gray-900">المجمعات النشطة</h2>
                <button onclick="window.location.href='{{ route('blockchain.defi.pools') }}'" class="text-blue-600 hover:text-blue-700 text-sm font-medium transition-colors duration-200">
                    عرض الكل
                    <i class="fas fa-arrow-left mr-1"></i>
                </button>
            </div>
        </div>
        
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="text-right py-3 px-4 font-semibold text-gray-900">المجمع</th>
                            <th class="text-right py-3 px-4 font-semibold text-gray-900">النوع</th>
                            <th class="text-right py-3 px-4 font-semibold text-gray-900">السيولة الكلية</th>
                            <th class="text-right py-3 px-4 font-semibold text-gray-900">معدل العائد السنوي</th>
                            <th class="text-right py-3 px-4 font-semibold text-gray-900">الحالة</th>
                            <th class="text-right py-3 px-4 font-semibold text-gray-900">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pools as $pool)
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors duration-200">
                            <td class="py-4 px-4">
                                <div class="flex items-center space-x-3 space-x-reverse">
                                    <div class="bg-blue-100 rounded-full p-2">
                                        <i class="fas fa-water text-blue-600 text-sm"></i>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-gray-900">{{ $pool->name }}</div>
                                        <small class="text-gray-500">{{ $pool->token_pair }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $pool->type === 'liquidity' ? 'bg-blue-100 text-blue-800' : ($pool->type === 'staking' ? 'bg-green-100 text-green-800' : ($pool->type === 'lending' ? 'bg-purple-100 text-purple-800' : 'bg-orange-100 text-orange-800')) }}">
                                    {{ $pool->type === 'liquidity' ? 'سيولة' : ($pool->type === 'staking' ? 'تخزين' : ($pool->type === 'lending' ? 'إقراض' : 'زراعة')) }}
                                </span>
                            </td>
                            <td class="py-4 px-4">
                                <div>
                                    <div class="font-semibold text-gray-900">{{ number_format($pool->total_liquidity, 4) }} {{ explode('/', $pool->token_pair)[0] }}</div>
                                    <small class="text-gray-500">${{ number_format($pool->total_liquidity_usd, 2) }}</small>
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                <span class="font-semibold text-green-600">{{ number_format($pool->apy, 2) }}%</span>
                            </td>
                            <td class="py-4 px-4">
                                @if($pool->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <span class="w-2 h-2 bg-green-400 rounded-full ml-2"></span>
                                        نشط
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        غير نشط
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-4">
                                <div class="flex space-x-2 space-x-reverse">
                                    <button onclick="window.location.href='{{ route('blockchain.defi.pool.show', $pool->id) }}'" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-200">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button onclick="window.location.href='{{ route('blockchain.defi.pool.deposit', $pool->id) }}'" class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors duration-200">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                    <button onclick="window.location.href='{{ route('blockchain.defi.pool.withdraw', $pool->id) }}'" class="p-2 text-orange-600 hover:bg-orange-50 rounded-lg transition-colors duration-200">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Portfolio Overview -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-xl font-bold text-gray-900">نظرة عامة على المحفظة</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="text-center">
                    <div class="bg-gray-50 rounded-xl p-4">
                        <i class="fas fa-wallet text-blue-500 text-2xl mb-2"></i>
                        <p class="text-sm text-gray-600 mb-1">قيمة المحفظة</p>
                        <p class="text-xl font-bold text-gray-900">{{ number_format($portfolioValue, 2) }} ETH</p>
                    </div>
                </div>
                
                <div class="text-center">
                    <div class="bg-gray-50 rounded-xl p-4">
                        <i class="fas fa-chart-line text-green-500 text-2xl mb-2"></i>
                        <p class="text-sm text-gray-600 mb-1">إجمالي الأرباح</p>
                        <p class="text-xl font-bold text-green-600">{{ number_format($totalEarnings, 2) }} ETH</p>
                    </div>
                </div>
                
                <div class="text-center">
                    <div class="bg-gray-50 rounded-xl p-4">
                        <i class="fas fa-bullseye text-purple-500 text-2xl mb-2"></i>
                        <p class="text-sm text-gray-600 mb-1">المراكز النشطة</p>
                        <p class="text-xl font-bold text-gray-900">{{ number_format($activePositions) }}</p>
                    </div>
                </div>
                
                <div class="text-center">
                    <div class="bg-gray-50 rounded-xl p-4">
                        <i class="fas fa-shield-alt text-orange-500 text-2xl mb-2"></i>
                        <p class="text-sm text-gray-600 mb-1">مستوى المخاطرة</p>
                        <p class="text-xl font-bold text-orange-600">{{ number_format($riskScore, 1) }}/10</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Auto-refresh DeFi data
let refreshInterval;

function startAutoRefresh() {
    refreshInterval = setInterval(() => {
        fetch('{{ route("blockchain.defi.refresh") }}')
            .then(response => response.json())
            .then(data => {
                // Update statistics with animation
                updateStatsWithAnimation(data);
            })
            .catch(error => {
                console.error('Error refreshing data:', error);
            });
    }, 30000); // Refresh every 30 seconds
}

function updateStatsWithAnimation(data) {
    // Animate number changes
    const elements = {
        'total-liquidity': data.total_liquidity,
        'total-borrowed': data.total_borrowed,
        'total-staked': data.total_staked,
        'average-apy': data.average_apy
    };
    
    Object.keys(elements).forEach(id => {
        const element = document.querySelector(`#${id}`);
        if (element && elements[id]) {
            const current = parseFloat(element.textContent.replace(/[^0-9.]/g, ''));
            const target = parseFloat(elements[id]);
            if (!isNaN(current) && !isNaN(target)) {
                animateValue(element, current, target, 1000);
            }
        }
    });
}

function animateValue(el, start, end, duration) {
    const range = end - start;
    const minTimer = 50;
    let stepTime = Math.abs(Math.floor(duration / range));
    stepTime = Math.max(stepTime, minTimer);
    const startTime = new Date().getTime();
    const endTime = startTime + duration;
    
    function run() {
        const now = new Date().getTime();
        const remaining = Math.max((endTime - now) / duration, 0);
        const value = Math.round(end - (remaining * range));
        el.textContent = value.toLocaleString();
        if (value !== end) {
            setTimeout(run, stepTime);
        }
    }
    
    run();
}

// Start auto-refresh when page loads
document.addEventListener('DOMContentLoaded', function() {
    startAutoRefresh();
});

// Cleanup on page unload
window.addEventListener('beforeunload', function() {
    if (refreshInterval) {
        clearInterval(refreshInterval);
    }
});
</script>
@endsection

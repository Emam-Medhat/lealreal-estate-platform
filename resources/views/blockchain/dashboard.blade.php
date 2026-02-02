@extends('admin.layouts.admin')

@section('title', 'لوحة تحكم البلوكشين')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">لوحة تحكم البلوكشين</h1>
                <p class="text-gray-600">مراقبة وإدارة شبكة البلوكشين والمعاملات</p>
            </div>
            
            <!-- Navigation Tabs -->
            <div class="flex flex-wrap gap-2">
                <a href="/blockchain" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors duration-200">
                    <i class="fas fa-home ml-2"></i>
                    الرئيسية
                </a>
                <a href="/blockchain/defi" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors duration-200">
                    <i class="fas fa-chart-line ml-2"></i>
                    DeFi
                </a>
                <a href="/blockchain/dao" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors duration-200">
                    <i class="fas fa-users ml-2"></i>
                    DAO
                </a>
                <a href="{{ url('blockchain/wallets') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors duration-200">
                    <i class="fas fa-wallet ml-2"></i>
                    المحافظ
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Blocks -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium mb-1">إجمالي الكتل</p>
                    <p class="text-3xl font-bold">{{ number_format($stats['total_blocks'] ?? 0) }}</p>
                    <p class="text-blue-100 text-xs mt-2">
                        <i class="fas fa-arrow-up ml-1"></i>
                        +12% هذا الشهر
                    </p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-cube text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Transactions -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium mb-1">إجمالي المعاملات</p>
                    <p class="text-3xl font-bold">{{ number_format($stats['total_transactions'] ?? 0) }}</p>
                    <p class="text-green-100 text-xs mt-2">
                        <i class="fas fa-arrow-up ml-1"></i>
                        +8% هذا الأسبوع
                    </p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-exchange-alt text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Smart Contracts -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium mb-1">العقود الذكية</p>
                    <p class="text-3xl font-bold">{{ number_format($stats['total_contracts'] ?? 0) }}</p>
                    <p class="text-purple-100 text-xs mt-2">
                        <i class="fas fa-arrow-up ml-1"></i>
                        +5% هذا الشهر
                    </p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-file-contract text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Digital Wallets -->
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm font-medium mb-1">المحافظ الرقمية</p>
                    <p class="text-3xl font-bold">{{ number_format($stats['total_wallets'] ?? 0) }}</p>
                    <p class="text-orange-100 text-xs mt-2">
                        <i class="fas fa-arrow-up ml-1"></i>
                        +15% هذا الشهر
                    </p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-wallet text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Network Status -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold text-gray-900">حالة الشبكة</h2>
                <div class="flex items-center space-x-2">
                    <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                    <span class="text-sm text-gray-600">نشط</span>
                </div>
            </div>
        </div>
        
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="text-center">
                    <div class="bg-gray-50 rounded-xl p-4">
                        <i class="fas fa-cube text-blue-500 text-2xl mb-2"></i>
                        <p class="text-sm text-gray-600 mb-1">آخر كتلة</p>
                        <p class="text-xl font-bold text-gray-900">{{ $stats['latest_block']->height ?? 'N/A' }}</p>
                    </div>
                </div>
                
                <div class="text-center">
                    <div class="bg-gray-50 rounded-xl p-4">
                        <i class="fas fa-tachometer-alt text-green-500 text-2xl mb-2"></i>
                        <p class="text-sm text-gray-600 mb-1">معدل الهاش</p>
                        <p class="text-xl font-bold text-gray-900">{{ $stats['network_hashrate'] ?? 'N/A' }}</p>
                    </div>
                </div>
                
                <div class="text-center">
                    <div class="bg-gray-50 rounded-xl p-4">
                        <i class="fas fa-chart-line text-purple-500 text-2xl mb-2"></i>
                        <p class="text-sm text-gray-600 mb-1">الصعوبة</p>
                        <p class="text-xl font-bold text-gray-900">{{ number_format($stats['difficulty'] ?? 0) }}</p>
                    </div>
                </div>
                
                <div class="text-center">
                    <div class="bg-gray-50 rounded-xl p-4">
                        <i class="fas fa-fire text-orange-500 text-2xl mb-2"></i>
                        <p class="text-sm text-gray-600 mb-1">سعر الغاز</p>
                        <p class="text-xl font-bold text-gray-900">{{ $stats['gas_price'] ?? 0 }} Gwei</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Blocks -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-gray-900">الكتل الأخيرة</h2>
                    <button class="text-blue-600 hover:text-blue-700 text-sm font-medium transition-colors duration-200">
                        عرض الكل
                        <i class="fas fa-arrow-left mr-1"></i>
                    </button>
                </div>
            </div>
            
            <div class="p-6">
                <div class="space-y-4">
                    @forelse($recentBlocks ?? [] as $block)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors duration-200">
                        <div class="flex items-center space-x-4 space-x-reverse">
                            <div class="bg-blue-100 rounded-full p-2">
                                <i class="fas fa-cube text-blue-600"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">كتلة #{{ $block->height ?? 'N/A' }}</p>
                                <p class="text-sm text-gray-500">{{ $block->created_at->diffForHumans() ?? 'الآن' }}</p>
                            </div>
                        </div>
                        <div class="text-left">
                            <p class="text-sm text-gray-600">{{ $block->transaction_count ?? 0 }} معاملة</p>
                            <p class="text-xs text-gray-400 font-mono">{{ substr($block->hash ?? '', 0, 10) }}...</p>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8">
                        <i class="fas fa-cube text-gray-300 text-4xl mb-3"></i>
                        <p class="text-gray-500">لا توجد كتل متاحة</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-gray-900">المعاملات الأخيرة</h2>
                    <button class="text-blue-600 hover:text-blue-700 text-sm font-medium transition-colors duration-200">
                        عرض الكل
                        <i class="fas fa-arrow-left mr-1"></i>
                    </button>
                </div>
            </div>
            
            <div class="p-6">
                <div class="space-y-4">
                    @forelse($recentTransactions ?? [] as $transaction)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors duration-200">
                        <div class="flex items-center space-x-4 space-x-reverse">
                            <div class="bg-green-100 rounded-full p-2">
                                <i class="fas fa-exchange-alt text-green-600"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">{{ $transaction->amount ?? 0 }} ETH</p>
                                <p class="text-sm text-gray-500">{{ $transaction->created_at->diffForHumans() ?? 'الآن' }}</p>
                            </div>
                        </div>
                        <div class="text-left">
                            <p class="text-xs text-gray-400 font-mono">{{ substr($transaction->hash ?? '', 0, 10) }}...</p>
                            <p class="text-xs text-gray-500">{{ substr($transaction->from_address ?? '', 0, 8) }} → {{ substr($transaction->to_address ?? '', 0, 8) }}</p>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8">
                        <i class="fas fa-exchange-alt text-gray-300 text-4xl mb-3"></i>
                        <p class="text-gray-500">لا توجد معاملات متاحة</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-xl font-bold text-gray-900">إجراءات سريعة</h2>
        </div>
        
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <button class="group flex flex-col items-center p-6 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl hover:from-blue-100 hover:to-blue-200 transition-all duration-300 transform hover:scale-105">
                    <div class="bg-blue-600 rounded-full p-3 mb-3 group-hover:bg-blue-700 transition-colors duration-300">
                        <i class="fas fa-file-contract text-white text-xl"></i>
                    </div>
                    <span class="font-medium text-gray-900">العقود الذكية</span>
                    <span class="text-xs text-gray-600 mt-1">إدارة العقود</span>
                </button>
                
                <button class="group flex flex-col items-center p-6 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl hover:from-purple-100 hover:to-purple-200 transition-all duration-300 transform hover:scale-105">
                    <div class="bg-purple-600 rounded-full p-3 mb-3 group-hover:bg-purple-700 transition-colors duration-300">
                        <i class="fas fa-image text-white text-xl"></i>
                    </div>
                    <span class="font-medium text-gray-900">NFTs</span>
                    <span class="text-xs text-gray-600 mt-1">الأصول الرقمية</span>
                </button>
                
                <button class="group flex flex-col items-center p-6 bg-gradient-to-br from-green-50 to-green-100 rounded-xl hover:from-green-100 hover:to-green-200 transition-all duration-300 transform hover:scale-105">
                    <div class="bg-green-600 rounded-full p-3 mb-3 group-hover:bg-green-700 transition-colors duration-300">
                        <i class="fas fa-wallet text-white text-xl"></i>
                    </div>
                    <span class="font-medium text-gray-900">المحافظ</span>
                    <span class="text-xs text-gray-600 mt-1">إدارة المحافظ</span>
                </button>
                
                <button class="group flex flex-col items-center p-6 bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl hover:from-orange-100 hover:to-orange-200 transition-all duration-300 transform hover:scale-105">
                    <div class="bg-orange-600 rounded-full p-3 mb-3 group-hover:bg-orange-700 transition-colors duration-300">
                        <i class="fas fa-chart-line text-white text-xl"></i>
                    </div>
                    <span class="font-medium text-gray-900">DeFi</span>
                    <span class="text-xs text-gray-600 mt-1">التمويل اللامركزي</span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Auto-refresh dashboard data
let refreshInterval;

function startAutoRefresh() {
    refreshInterval = setInterval(() => {
        // Show loading indicator
        const indicators = document.querySelectorAll('.animate-pulse');
        indicators.forEach(el => el.classList.add('opacity-75'));
        
        // Refresh data via AJAX instead of full page reload
        fetch('/blockchain/refresh-stats')
            .then(response => response.json())
            .then(data => {
                // Update statistics with animation
                updateStatsWithAnimation(data);
            })
            .catch(error => {
                console.error('Error refreshing data:', error);
                // Fallback to page reload after 3 failed attempts
                location.reload();
            });
    }, 30000); // Refresh every 30 seconds
}

function updateStatsWithAnimation(data) {
    // Animate number changes
    const statElements = document.querySelectorAll('[data-stat]');
    statElements.forEach(el => {
        const statName = el.dataset.stat;
        const newValue = data[statName];
        if (newValue !== undefined) {
            animateValue(el, parseInt(el.textContent.replace(/,/g, '')), newValue, 1000);
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
    
    // Add hover effects to cards
    const cards = document.querySelectorAll('.hover\\:scale-\\[1\\.02\\]');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.02)';
        });
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });
});

// Cleanup on page unload
window.addEventListener('beforeunload', function() {
    if (refreshInterval) {
        clearInterval(refreshInterval);
    }
});
</script>
@endsection

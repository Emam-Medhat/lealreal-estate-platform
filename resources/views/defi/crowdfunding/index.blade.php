@extends('admin.layouts.admin')

@section('title', 'التمويل الجماعي')
@section('page-title', 'التمويل الجماعي')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">
                    <i class="fas fa-users text-violet-500 ml-3"></i>
                    التمويل الجماعي
                </h1>
                <p class="text-gray-600 mt-2">منصة استثمار جماعي للعقارات والفرص الاستثماري</p>
            </div>
            <div class="flex space-x-reverse space-x-3">
                <a href="{{ route('defi.dashboard.index') }}" class="bg-gradient-to-r from-gray-500 to-gray-600 text-white px-6 py-3 rounded-lg hover:from-gray-600 hover:to-gray-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    <i class="fas fa-chart-line ml-2"></i>
                    لوحة التحكم
                </a>
                <button onclick="refreshCampaigns()" class="bg-gradient-to-r from-violet-500 to-violet-600 text-white px-6 py-3 rounded-lg hover:from-violet-600 hover:to-violet-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    <i class="fas fa-sync-alt ml-2"></i>
                    تحديث
                </button>
            </div>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-gradient-to-br from-violet-500 to-violet-600 rounded-2xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-violet-100 text-sm font-medium">إجمالي الحملات</p>
                        <p class="text-3xl font-bold mt-2">{{ number_format($stats['total_campaigns']) }}</p>
                        <p class="text-violet-100 text-sm mt-2">
                            <i class="fas fa-chart-line ml-1"></i>
                            {{ $stats['active_campaigns'] }} نشطة حالياً
                        </p>
                    </div>
                    <div class="bg-white/20 rounded-full p-4">
                        <i class="fas fa-users text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm font-medium">إجمالي المستثمرين</p>
                        <p class="text-3xl font-bold mt-2">{{ number_format($stats['total_investors']) }}</p>
                        <p class="text-green-100 text-sm mt-2">
                            <i class="fas fa-user-plus ml-1"></i>
                            {{ $stats['average_investment'] }} ريال متوسط
                        </p>
                    </div>
                    <div class="bg-white/20 rounded-full p-4">
                        <i class="fas fa-user-friends text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-medium">إجمالي الاستثمار</p>
                        <p class="text-3xl font-bold mt-2">{{ number_format($stats['total_invested']) }}</p>
                        <p class="text-blue-100 text-sm mt-2">
                            <i class="fas fa-coins ml-1"></i>
                            {{ $stats['success_rate'] }}% نسبة نجاح
                        </p>
                    </div>
                    <div class="bg-white/20 rounded-full p-4">
                        <i class="fas fa-chart-pie text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-2xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-amber-100 text-sm font-medium">متوسط العائد</p>
                        <p class="text-3xl font-bold mt-2">{{ $stats['average_return'] }}%</p>
                        <p class="text-amber-100 text-sm mt-2">
                            <i class="fas fa-percentage ml-1"></i>
                            سنوياً
                        </p>
                    </div>
                    <div class="bg-white/20 rounded-full p-4">
                        <i class="fas fa-chart-line text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Campaigns -->
        <div class="bg-white rounded-2xl shadow-xl p-6 mb-8">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-800">
                    <i class="fas fa-fire text-orange-500 ml-3"></i>
                    الحملات النشطة
                </h3>
                <div class="bg-orange-100 rounded-full p-2">
                    <i class="fas fa-fire text-orange-600"></i>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($activeCampaigns as $campaign)
                <div class="bg-white rounded-lg p-6 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex-1">
                            <h4 class="text-lg font-bold text-gray-800">{{ $campaign->title }}</h4>
                            <p class="text-sm text-gray-600 mt-1">{{ $campaign->property_title }}</p>
                            <p class="text-xs text-gray-500 mt-2">{{ $campaign->location }}</p>
                        </div>
                        <span class="bg-{{ $campaign->progress >= 75 ? 'green' : ($campaign->progress >= 50 ? 'yellow' : 'red') }}-100 text-{{ $campaign->progress >= 75 ? 'green' : ($campaign->progress >= 50 ? 'yellow' : 'red') }}-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                            {{ $campaign->progress >= 75 ? 'ناجح' : ($campaign->progress >= 50 ? 'جاري' : 'بطيء') }}
                        </span>
                    </div>
                    
                    <div class="mb-4">
                        <div class="flex justify-between text-sm text-gray-600 mb-2">
                            <span>الهدف: {{ number_format($campaign->target_amount) }} ريال</span>
                            <span>{{ number_format($campaign->current_amount) }} ريال</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-gradient-to-r from-violet-400 to-violet-600 h-3 rounded-full" style="width: {{ $campaign->progress }}%"></div>
                        </div>
                    </div>
                    
                    <div class="flex justify-between items-center text-sm">
                        <div>
                            <span class="text-gray-600">عائد: {{ $campaign->return_rate }}%</span>
                            <span class="text-gray-600">المدة: {{ $campaign->duration_months }} شهر</span>
                        </div>
                        <div class="text-sm text-gray-500">
                            {{ $campaign->days_left }} يوم متبقي
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <a href="{{ route('defi.crowdfunding.show', $campaign->id) }}" class="block w-full bg-gradient-to-r from-violet-500 to-violet-600 text-white px-4 py-2 rounded-lg hover:from-violet-600 hover:to-violet-700 transition-all duration-300 text-center">
                            <i class="fas fa-eye ml-2"></i>
                            عرض التفاصيل
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Top Performers -->
        <div class="bg-white rounded-2xl shadow-xl p-6 mb-8">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-800">
                    <i class="fas fa-trophy text-yellow-500 ml-3"></i>
                    أفضل الحملات
                </h3>
                <div class="bg-yellow-100 rounded-full p-2">
                    <i class="fas fa-star text-yellow-600"></i>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach($topPerformers as $performer)
                <div class="bg-gradient-to-r from-green-50 to-green-100 rounded-lg p-4 border border-green-200">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <h4 class="font-bold text-green-800">{{ $performer->title }}</h4>
                            <p class="text-xs text-green-600">{{ number_format($performer->current_amount) }} / {{ number_format($performer->target_amount) }} ريال</p>
                        </div>
                        <span class="bg-green-500 text-white text-xs font-bold px-2.5 py-0.5 rounded-full">#{{ $performer->id }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-green-600">عائد: {{ $performer->return_rate }}%</span>
                        <span class="text-green-600">{{ $performer->duration_months }} شهر</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Recent Investments -->
        <div class="bg-white rounded-2xl shadow-xl p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-800">
                    <i class="fas fa-history text-blue-500 ml-3"></i>
                    الاستثمارات الأخيرة
                </h3>
                <div class="bg-blue-100 rounded-full p-2">
                    <i class="fas fa-history text-blue-600"></i>
                </div>
            </div>
            <div class="space-y-4">
                @foreach($recentInvestments as $investment)
                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                    <div>
                        <h6 class="font-medium text-gray-800">{{ $investment->investor_name }}</h6>
                        <p class="text-xs text-gray-500">{{ $investment->campaign_title }}</p>
                    </div>
                    <div class="text-right">
                        <span class="text-lg font-bold text-blue-600">{{ number_format($investment->amount) }} ريال</span>
                        <p class="text-xs text-gray-500">{{ $investment->created_at }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
function refreshCampaigns() {
    const button = event.target;
    const originalContent = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin ml-2"></i> جاري التحديث...';
    button.disabled = true;
    
    setTimeout(() => {
        location.reload();
    }, 1500);
}

// Auto-refresh every 30 seconds
setInterval(function() {
    console.log('Refreshing campaigns data...');
    fetch('/defi/crowdfunding/refresh-data')
        .then(response => response.json())
        .then(data => {
            console.log('Campaigns refreshed:', data);
            // Update UI with new data
        })
        .catch(error => {
            console.error('Error refreshing campaigns:', error);
        });
}, 30000);

// Interactive hover effects
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.bg-white.rounded-lg');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
        });
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
});
</script>
@endpush

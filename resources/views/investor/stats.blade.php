@extends('admin.layouts.admin')

@section('title', 'إحصائيات المستثمرين')
@section('page-title', 'إحصائيات المستثمرين')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800">إحصائيات المستثمرين</h1>
                        <p class="mt-2 text-gray-600">نظرة شاملة على أداء المستثمرين وحركة الاستثمارات في المنصة</p>
                    </div>
                    <div class="flex gap-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            <i class="fas fa-users ml-2"></i>
                            {{ $stats['total_investors'] }} مستثمر
                        </span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                            <i class="fas fa-chart-pie ml-2"></i>
                            {{ $stats['active_investors'] }} نشط
                        </span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                            <i class="fas fa-shield-alt ml-2"></i>
                            {{ $stats['verified_investors'] }} موثق
                        </span>
                    </div>
                </div>
            </div>

            <!-- Main Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Investors -->
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-100 text-sm font-medium">إجمالي المستثمرين</p>
                            <p class="text-3xl font-bold mt-2">{{ number_format($stats['total_investors']) }}</p>
                        </div>
                        <div class="bg-white/20 rounded-full p-4">
                            <i class="fas fa-users text-2xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Active Investors -->
                <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-100 text-sm font-medium">مستثمرون نشطون</p>
                            <p class="text-3xl font-bold mt-2">{{ number_format($stats['active_investors']) }}</p>
                        </div>
                        <div class="bg-white/20 rounded-full p-4">
                            <i class="fas fa-user-check text-2xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Total Invested -->
                <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-2xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-indigo-100 text-sm font-medium">إجمالي الاستثمارات</p>
                            <p class="text-3xl font-bold mt-2">${{ number_format($stats['total_invested'], 2) }}</p>
                        </div>
                        <div class="bg-white/20 rounded-full p-4">
                            <i class="fas fa-dollar-sign text-2xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Total Returns -->
                <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-2xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-yellow-100 text-sm font-medium">إجمالي العوائد</p>
                            <p class="text-3xl font-bold mt-2">${{ number_format($stats['total_returns'], 2) }}</p>
                        </div>
                        <div class="bg-white/20 rounded-full p-4">
                            <i class="fas fa-chart-line text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <!-- By Type Chart -->
                <div class="bg-white rounded-2xl shadow-xl p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-bold text-gray-800">المستثمرون حسب النوع</h3>
                            <p class="text-sm text-gray-600">Investor Types</p>
                        </div>
                        <div class="bg-blue-100 rounded-full p-2">
                            <i class="fas fa-users-cog text-blue-600"></i>
                        </div>
                    </div>
                    <div class="space-y-3">
                        @if($stats['by_type']->count() > 0)
                            @foreach($stats['by_type'] as $type => $count)
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium text-gray-700">{{ ucfirst($type) }}</span>
                                    <div class="flex items-center">
                                        <div class="flex-1 mr-3">
                                            <div class="bg-gray-200 rounded-full h-2">
                                                <div class="bg-blue-500 h-2 rounded-full" style="width: {{ ($count / $stats['total_investors']) * 100 }}%"></div>
                                            </div>
                                        </div>
                                        <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2 py-1 rounded-full">{{ $count }}</span>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-gray-500 text-center py-4">لا توجد بيانات</p>
                        @endif
                    </div>
                </div>

                <!-- By Risk Tolerance Chart -->
                <div class="bg-white rounded-2xl shadow-xl p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-bold text-gray-800">المستثمرون حسب تحمل المخاطرة</h3>
                            <p class="text-sm text-gray-600">Risk Tolerance</p>
                        </div>
                        <div class="bg-yellow-100 rounded-full p-2">
                            <i class="fas fa-shield-alt text-yellow-600"></i>
                        </div>
                    </div>
                    <div class="space-y-3">
                        @if($stats['by_risk_tolerance']->count() > 0)
                            @foreach($stats['by_risk_tolerance'] as $risk => $count)
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium text-gray-700">{{ ucfirst($risk) }}</span>
                                    <div class="flex items-center">
                                        <div class="flex-1 mr-3">
                                            <div class="bg-gray-200 rounded-full h-2">
                                                <div class="bg-yellow-500 h-2 rounded-full" style="width: {{ ($count / $stats['total_investors']) * 100 }}%"></div>
                                            </div>
                                        </div>
                                        <span class="bg-yellow-100 text-yellow-800 text-xs font-semibold px-2 py-1 rounded-full">{{ $count }}</span>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-gray-500 text-center py-4">لا توجد بيانات</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Additional Stats -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <!-- Average Investment -->
                <div class="bg-white rounded-2xl shadow-xl p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-bold text-gray-800">متوسط الاستثمار</h3>
                            <p class="text-sm text-gray-600">Average Investment</p>
                        </div>
                        <div class="bg-indigo-100 rounded-full p-2">
                            <i class="fas fa-calculator text-indigo-600"></i>
                        </div>
                    </div>
                    <div class="text-center">
                        <p class="text-3xl font-bold text-indigo-600">${{ number_format($stats['average_investment'], 2) }}</p>
                    </div>
                </div>

                <!-- Verified Investors -->
                <div class="bg-white rounded-2xl shadow-xl p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-bold text-gray-800">نسبة الموثقين</h3>
                            <p class="text-sm text-gray-600">Verified Percentage</p>
                        </div>
                        <div class="bg-green-100 rounded-full p-2">
                            <i class="fas fa-certificate text-green-600"></i>
                        </div>
                    </div>
                    <div class="text-center">
                        <p class="text-3xl font-bold text-green-600">
                            {{ $stats['total_investors'] > 0 ? round(($stats['verified_investors'] / $stats['total_investors']) * 100, 1) : 0 }}%
                        </p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-2xl shadow-xl p-6 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">إجراءات سريعة</h3>
                        <p class="text-sm text-gray-600">Quick Actions</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <a href="{{ route('investor.index') }}" 
                       class="bg-blue-50 border border-blue-100 rounded-xl p-4 hover:bg-blue-100 transition-all text-center group">
                        <i class="fas fa-list text-blue-600 text-2xl mb-2 group-hover:scale-110 transition-transform"></i>
                        <p class="text-sm font-bold text-gray-800">عرض جميع المستثمرين</p>
                    </a>
                    <a href="{{ route('investor.opportunities.index') }}" 
                       class="bg-green-50 border border-green-100 rounded-xl p-4 hover:bg-green-100 transition-all text-center group">
                        <i class="fas fa-lightbulb text-green-600 text-2xl mb-2 group-hover:scale-110 transition-transform"></i>
                        <p class="text-sm font-bold text-gray-800">فرص الاستثمار</p>
                    </a>
                    <button onclick="exportStats()" 
                            class="bg-gray-50 border border-gray-100 rounded-xl p-4 hover:bg-gray-100 transition-all text-center group">
                        <i class="fas fa-download text-gray-600 text-2xl mb-2 group-hover:scale-110 transition-transform"></i>
                        <p class="text-sm font-bold text-gray-800">تصدير الإحصائيات</p>
                    </button>
                </div>
            </div>
        </div>
    </div>

<script>
function exportStats() {
    const stats = @json($stats);
    const dataStr = JSON.stringify(stats, null, 2);
    const dataUri = 'data:application/json;charset=utf-8,'+ encodeURIComponent(dataStr);
    
    const exportFileDefaultName = 'investor-stats-' + new Date().toISOString().slice(0, 10) + '.json';
    
    const linkElement = document.createElement('a');
    linkElement.setAttribute('href', dataUri);
    linkElement.setAttribute('download', exportFileDefaultName);
    linkElement.click();
}
</script>
@endsection

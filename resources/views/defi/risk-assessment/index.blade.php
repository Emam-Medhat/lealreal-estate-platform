@extends('admin.layouts.admin')

@section('title', 'تقييم المخاطر')

@section('content')
<div class="container mx-auto px-4 py-8" dir="rtl">
<!-- Page Header -->
<div class="mb-8">
    <div class="bg-gradient-to-r from-blue-50 via-white to-indigo-50 rounded-2xl shadow-sm border border-gray-200 p-8 relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-5">
            <div class="absolute top-0 right-0 w-64 h-64 bg-blue-200 rounded-full -mr-32 -mt-32"></div>
            <div class="absolute bottom-0 left-0 w-48 h-48 bg-indigo-200 rounded-full -ml-24 -mb-24"></div>
        </div>
        
        <div class="relative z-10">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
                <div>
                    <div class="flex items-center mb-4">
                        <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl p-4 ml-4 shadow-lg">
                            <i class="fas fa-shield-alt text-white text-2xl"></i>
                        </div>
                        <div>
                            <h1 class="text-4xl font-bold text-gray-900 mb-2">تقييم المخاطر</h1>
                            <p class="text-gray-600 text-lg">تحليل وتقييم المخاطر المالية والاستثمارية</p>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <div class="bg-blue-50 rounded-lg px-4 py-2 border border-blue-200">
                            <span class="text-sm text-blue-600">إجمالي المخاطر:</span>
                            <span class="text-sm font-bold text-gray-900">{{ $stats['total_risks'] ?? 0 }}</span>
                        </div>
                        <div class="bg-red-50 rounded-lg px-4 py-2 border border-red-200">
                            <span class="text-sm text-red-600">عالية الخطورة:</span>
                            <span class="text-sm font-bold text-gray-900">{{ $stats['high_risks'] ?? 0 }}</span>
                        </div>
                        <div class="bg-green-50 rounded-lg px-4 py-2 border border-green-200">
                            <span class="text-sm text-green-600">تم التقييم:</span>
                            <span class="text-sm font-bold text-gray-900">{{ $stats['assessed_risks'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('defi.dashboard.index') }}" class="inline-flex items-center px-6 py-3 bg-white text-gray-700 rounded-xl text-sm font-medium hover:bg-gray-50 border border-gray-300 transition-all duration-300 transform hover:scale-105 shadow-sm">
                        <i class="fas fa-arrow-right ml-2"></i>
                        العودة للوحة
                    </a>
                    <button class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl text-sm font-medium hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                        <i class="fas fa-plus ml-2"></i>
                        تقييم جديد
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Risk Overview Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Risks -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 transform hover:scale-105 transition-all duration-300 relative overflow-hidden group hover:shadow-md">
        <div class="absolute inset-0 bg-gradient-to-br from-blue-50 to-indigo-50 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
        <div class="relative z-10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-semibold mb-2">إجمالي المخاطر</p>
                    <p class="text-4xl font-bold text-gray-900">{{ $stats['total_risks'] ?? 0 }}</p>
                    <div class="mt-3 flex items-center text-sm text-gray-500">
                        <i class="fas fa-exclamation-triangle ml-2 text-blue-500"></i>
                        <span>جميع المخاطر</span>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-blue-100 to-blue-200 rounded-2xl p-4 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-exclamation-triangle text-blue-600 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- High Risks -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 transform hover:scale-105 transition-all duration-300 relative overflow-hidden group hover:shadow-md">
        <div class="absolute inset-0 bg-gradient-to-br from-red-50 to-pink-50 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
        <div class="relative z-10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-semibold mb-2">مخاطر عالية</p>
                    <p class="text-4xl font-bold text-red-600">{{ $stats['high_risks'] ?? 0 }}</p>
                    <div class="mt-3 flex items-center text-sm text-gray-500">
                        <i class="fas fa-exclamation-circle ml-2 text-red-500"></i>
                        <span>خطورة عالية</span>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-red-100 to-red-200 rounded-2xl p-4 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-exclamation-circle text-red-600 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Medium Risks -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 transform hover:scale-105 transition-all duration-300 relative overflow-hidden group hover:shadow-md">
        <div class="absolute inset-0 bg-gradient-to-br from-amber-50 to-yellow-50 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
        <div class="relative z-10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-semibold mb-2">مخاطر متوسطة</p>
                    <p class="text-4xl font-bold text-amber-600">{{ $stats['medium_risks'] ?? 0 }}</p>
                    <div class="mt-3 flex items-center text-sm text-gray-500">
                        <i class="fas fa-exclamation ml-2 text-amber-500"></i>
                        <span>خطورة متوسطة</span>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-amber-100 to-amber-200 rounded-2xl p-4 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-exclamation text-amber-600 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Low Risks -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 transform hover:scale-105 transition-all duration-300 relative overflow-hidden group hover:shadow-md">
        <div class="absolute inset-0 bg-gradient-to-br from-green-50 to-emerald-50 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
        <div class="relative z-10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-semibold mb-2">مخاطر منخفضة</p>
                    <p class="text-4xl font-bold text-green-600">{{ $stats['low_risks'] ?? 0 }}</p>
                    <div class="mt-3 flex items-center text-sm text-gray-500">
                        <i class="fas fa-check-circle ml-2 text-green-500"></i>
                        <span>خطورة منخفضة</span>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-green-100 to-green-200 rounded-2xl p-4 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Risk Categories -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- Risk Categories Chart -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-black mb-6 flex items-center">
            <div class="bg-slate-600 rounded-lg p-2 ml-3 shadow-md">
                <i class="fas fa-chart-pie text-white"></i>
            </div>
            تصنيفات المخاطر
        </h3>
        <div class="space-y-4">
            @if(isset($riskCategories) && count($riskCategories) > 0)
                @foreach($riskCategories as $category)
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="flex items-center">
                        <div class="bg-{{ $category->color ?? 'blue' }}-100 rounded-lg p-2 ml-3">
                            <i class="fas fa-{{ $category->icon ?? 'chart-line' }} text-{{ $category->color ?? 'blue' }}-600"></i>
                        </div>
                        <div>
                            <h4 class="text-black font-medium">{{ $category->risk_level }}</h4>
                            <p class="text-black text-sm">{{ $category->count }} مخاطر</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-black font-bold">{{ $category->percentage ?? 0 }}%</div>
                        <div class="w-20 bg-gray-200 rounded-full h-2 mt-1">
                            <div class="bg-{{ $category->color ?? 'blue' }}-500 h-2 rounded-full" style="width: {{ $category->percentage ?? 0 }}%"></div>
                        </div>
                    </div>
                </div>
                @endforeach
            @else
                <div class="text-center py-8">
                    <i class="fas fa-chart-pie text-gray-400 text-4xl mb-4"></i>
                    <p class="text-black">لا توجد بيانات تصنيف</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Recent Assessments -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-black mb-6 flex items-center">
            <div class="bg-slate-600 rounded-lg p-2 ml-3 shadow-md">
                <i class="fas fa-history text-white"></i>
            </div>
            التقييمات الحديثة
        </h3>
        <div class="space-y-3">
            @if(isset($recentAssessments) && count($recentAssessments) > 0)
                @foreach($recentAssessments as $assessment)
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200 hover:bg-gray-100 transition-colors">
                    <div class="flex items-center">
                        <div class="bg-{{ $assessment->risk_color ?? 'blue' }}-100 rounded-lg p-2 ml-3">
                            <i class="fas fa-{{ $assessment->risk_icon ?? 'shield-alt' }} text-{{ $assessment->risk_color ?? 'blue' }}-600"></i>
                        </div>
                        <div>
                            <h4 class="text-black font-medium">{{ $assessment->property_title }}</h4>
                            <p class="text-black text-sm">{{ $assessment->location }}</p>
                        </div>
                    </div>
                    <div class="text-left">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-{{ $assessment->risk_color ?? 'blue' }}-100 text-black border border-{{ $assessment->risk_color ?? 'blue' }}-200">
                            {{ $assessment->risk_level }}
                        </span>
                    </div>
                </div>
                @endforeach
            @else
                <div class="text-center py-8">
                    <i class="fas fa-history text-gray-400 text-4xl mb-4"></i>
                    <p class="text-black">لا توجد تقييمات حديثة</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Risk Trends -->
<div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-8 mb-8">
    <h3 class="text-2xl font-bold text-black mb-6 flex items-center">
        <div class="bg-slate-600 rounded-lg p-2 ml-3 shadow-md">
            <i class="fas fa-chart-line text-white"></i>
        </div>
        اتجاهات المخاطر
    </h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @if(isset($riskTrends) && count($riskTrends) > 0)
            @foreach($riskTrends as $trend)
            <div class="text-center p-6 bg-gray-50 rounded-lg border border-gray-200">
                <div class="bg-{{ $trend->color ?? 'blue' }}-100 rounded-lg p-3 inline-block mb-4">
                    <i class="fas fa-{{ $trend->icon ?? 'trending-up' }} text-{{ $trend->color ?? 'blue' }}-600 text-2xl"></i>
                </div>
                <h4 class="text-black font-semibold mb-2">{{ $trend->title }}</h4>
                <p class="text-3xl font-black text-black mb-2">{{ $trend->value }}</p>
                <p class="text-black text-sm">{{ $trend->description }}</p>
                @if($trend->change > 0)
                    <p class="text-green-600 text-sm mt-2">
                        <i class="fas fa-arrow-up ml-1"></i>
                        {{ $trend->change }}% زيادة
                    </p>
                @else
                    <p class="text-red-600 text-sm mt-2">
                        <i class="fas fa-arrow-down ml-1"></i>
                        {{ abs($trend->change) }}% انخفاض
                    </p>
                @endif
            </div>
            @endforeach
        @else
            <div class="col-span-3 text-center py-8">
                <i class="fas fa-chart-line text-gray-400 text-6xl mb-4"></i>
                <p class="text-black">لا توجد بيانات اتجاهات</p>
            </div>
        @endif
    </div>
</div>

<!-- Actions -->
<div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
    <h3 class="text-lg font-semibold text-black mb-6 flex items-center">
        <div class="bg-slate-600 rounded-lg p-2 ml-3 shadow-md">
            <i class="fas fa-cogs text-white"></i>
        </div>
        إجراءات سريعة
    </h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <button class="flex items-center justify-center p-6 bg-blue-50 rounded-lg border border-blue-200 hover:bg-blue-100 transition-colors group">
            <i class="fas fa-plus-circle text-blue-600 text-xl ml-3 group-hover:scale-110 transition-transform"></i>
            <span class="text-black font-medium">تقييم جديد</span>
        </button>
        <button class="flex items-center justify-center p-6 bg-green-50 rounded-lg border border-green-200 hover:bg-green-100 transition-colors group">
            <i class="fas fa-download text-green-600 text-xl ml-3 group-hover:scale-110 transition-transform"></i>
            <span class="text-black font-medium">تصدير التقرير</span>
        </button>
        <button class="flex items-center justify-center p-6 bg-purple-50 rounded-lg border border-purple-200 hover:bg-purple-100 transition-colors group">
            <i class="fas fa-sync text-purple-600 text-xl ml-3 group-hover:scale-110 transition-transform"></i>
            <span class="text-black font-medium">تحديث البيانات</span>
        </button>
    </div>
</div>

</div>
@endsection

@extends('admin.layouts.admin')

@section('title', 'خرائط أسعار العقارات')
@section('page-title', 'خرائط أسعار العقارات')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">
                    <i class="fas fa-home text-blue-500 ml-3"></i>
                    خرائط أسعار العقارات
                </h1>
                <p class="text-gray-600 mt-2">تحليل وتصور أسعار العقارات عبر المناطق المختلفة</p>
            </div>
            <div class="flex space-x-reverse space-x-3">
                <a href="{{ route('bigdata.heatmaps') }}" class="bg-gradient-to-r from-gray-500 to-gray-600 text-white px-6 py-3 rounded-lg hover:from-gray-600 hover:to-gray-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    <i class="fas fa-arrow-right ml-2"></i>
                    العودة للخرائط
                </a>
                <button onclick="refreshHeatmap()" class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-6 py-3 rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    <i class="fas fa-sync-alt ml-2"></i>
                    تحديث
                </button>
            </div>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-medium">متوسط السعر</p>
                        <p class="text-3xl font-bold mt-2">{{ number_format($heatmapData['average_price'] ?? 1250000) }} ريال</p>
                        <p class="text-blue-100 text-sm mt-2">
                            <i class="fas fa-chart-line ml-1"></i>
                            +5.2% هذا الشهر
                        </p>
                    </div>
                    <div class="bg-white/20 rounded-full p-4">
                        <i class="fas fa-money-bill-wave text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm font-medium">أعلى سعر</p>
                        <p class="text-3xl font-bold mt-2">{{ number_format($heatmapData['max_price'] ?? 5000000) }} ريال</p>
                        <p class="text-green-100 text-sm mt-2">
                            <i class="fas fa-arrow-up ml-1"></i>
                            الرياض الشمالية
                        </p>
                    </div>
                    <div class="bg-white/20 rounded-full p-4">
                        <i class="fas fa-crown text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-2xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-amber-100 text-sm font-medium">أقل سعر</p>
                        <p class="text-3xl font-bold mt-2">{{ number_format($heatmapData['min_price'] ?? 350000) }} ريال</p>
                        <p class="text-amber-100 text-sm mt-2">
                            <i class="fas fa-arrow-down ml-1"></i>
                            الأحياء الناشئة
                        </p>
                    </div>
                    <div class="bg-white/20 rounded-full p-4">
                        <i class="fas fa-tag text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm font-medium">نقاط بيانات</p>
                        <p class="text-3xl font-bold mt-2">{{ number_format($heatmapData['data_points'] ?? 15420) }}</p>
                        <p class="text-purple-100 text-sm mt-2">
                            <i class="fas fa-database ml-1"></i>
                            محدثة الآن
                        </p>
                    </div>
                    <div class="bg-white/20 rounded-full p-4">
                        <i class="fas fa-chart-area text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Heatmap -->
        <div class="bg-white rounded-2xl shadow-xl p-6 mb-8">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-800">
                    <i class="fas fa-map text-blue-500 ml-3"></i>
                    خريطة أسعار العقارات
                </h3>
                <div class="flex space-x-reverse space-x-3">
                    <select class="bg-gray-100 border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option>جميع المدن</option>
                        <option>الرياض</option>
                        <option>جدة</option>
                        <option>الدمام</option>
                        <option>مكة المكرمة</option>
                    </select>
                    <select class="bg-gray-100 border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option>جميع الأنواع</option>
                        <option>شقق</option>
                        <option>فلل</option>
                        <option>أراضٍ</option>
                        <option>مكاتب</option>
                    </select>
                </div>
            </div>
            
            <div class="mb-4">
                <img src="https://via.placeholder.com/1200x600/4e73df/ffffff?text=Property+Prices+Heatmap+Map" class="w-full h-96 object-cover rounded-lg" alt="Property Prices Heatmap">
            </div>
            
            <!-- Legend -->
            <div class="flex items-center justify-between mt-6 p-4 bg-gray-50 rounded-lg">
                <div class="flex items-center space-x-reverse space-x-6">
                    <span class="text-sm font-medium text-gray-700">مستوى السعر:</span>
                    <div class="flex items-center space-x-reverse space-x-2">
                        <div class="w-4 h-4 bg-green-500 rounded"></div>
                        <span class="text-xs text-gray-600">منخفض</span>
                    </div>
                    <div class="flex items-center space-x-reverse space-x-2">
                        <div class="w-4 h-4 bg-yellow-500 rounded"></div>
                        <span class="text-xs text-gray-600">متوسط</span>
                    </div>
                    <div class="flex items-center space-x-reverse space-x-2">
                        <div class="w-4 h-4 bg-orange-500 rounded"></div>
                        <span class="text-xs text-gray-600">مرتفع</span>
                    </div>
                    <div class="flex items-center space-x-reverse space-x-2">
                        <div class="w-4 h-4 bg-red-500 rounded"></div>
                        <span class="text-xs text-gray-600">مرتفع جداً</span>
                    </div>
                </div>
                <div class="text-sm text-gray-600">
                    <i class="fas fa-info-circle ml-1"></i>
                    آخر تحديث: {{ now()->diffForHumans() }}
                </div>
            </div>
        </div>

        <!-- Price Distribution -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <div class="bg-white rounded-2xl shadow-xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-800">
                        <i class="fas fa-chart-bar text-green-500 ml-3"></i>
                        توزيع الأسعار حسب المدينة
                    </h3>
                    <div class="bg-green-100 rounded-full p-2">
                        <i class="fas fa-city text-green-600"></i>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">الرياض</span>
                            <span class="text-sm font-bold text-blue-600">1,850,000 ريال</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: 75%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">جدة</span>
                            <span class="text-sm font-bold text-green-600">1,650,000 ريال</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-green-600 h-2 rounded-full" style="width: 65%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">الدمام</span>
                            <span class="text-sm font-bold text-cyan-600">1,250,000 ريال</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-cyan-600 h-2 rounded-full" style="width: 50%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">مكة المكرمة</span>
                            <span class="text-sm font-bold text-purple-600">980,000 ريال</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-purple-600 h-2 rounded-full" style="width: 40%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-800">
                        <i class="fas fa-chart-pie text-orange-500 ml-3"></i>
                        توزيع حسب نوع العقار
                    </h3>
                    <div class="bg-orange-100 rounded-full p-2">
                        <i class="fas fa-building text-orange-600"></i>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">فلل</span>
                            <span class="text-sm font-bold text-blue-600">2,850,000 ريال</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: 85%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">شقق</span>
                            <span class="text-sm font-bold text-green-600">850,000 ريال</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-green-600 h-2 rounded-full" style="width: 45%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">أراضٍ</span>
                            <span class="text-sm font-bold text-cyan-600">1,450,000 ريال</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-cyan-600 h-2 rounded-full" style="width: 60%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">مكاتب</span>
                            <span class="text-sm font-bold text-purple-600">1,150,000 ريال</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-purple-600 h-2 rounded-full" style="width: 55%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Areas -->
        <div class="bg-white rounded-2xl shadow-xl p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-800">
                    <i class="fas fa-trophy text-yellow-500 ml-3"></i>
                    أعلى المناطق سعراً
                </h3>
                <div class="bg-yellow-100 rounded-full p-2">
                    <i class="fas fa-star text-yellow-600"></i>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-gradient-to-r from-red-50 to-red-100 rounded-lg p-4 border border-red-200">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-bold text-red-800">الرياض الشمالية</h4>
                        <span class="bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">#1</span>
                    </div>
                    <p class="text-2xl font-bold text-red-600">4,250,000 ريال</p>
                    <p class="text-sm text-red-600 mt-1">فلل فاخرة</p>
                </div>
                
                <div class="bg-gradient-to-r from-orange-50 to-orange-100 rounded-lg p-4 border border-orange-200">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-bold text-orange-800">جدة الشاطئية</h4>
                        <span class="bg-orange-500 text-white text-xs font-bold px-2 py-1 rounded-full">#2</span>
                    </div>
                    <p class="text-2xl font-bold text-orange-600">3,850,000 ريال</p>
                    <p class="text-sm text-orange-600 mt-1">شقق بحرية</p>
                </div>
                
                <div class="bg-gradient-to-r from-yellow-50 to-yellow-100 rounded-lg p-4 border border-yellow-200">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-bold text-yellow-800">الدمام التجارية</h4>
                        <span class="bg-yellow-500 text-white text-xs font-bold px-2 py-1 rounded-full">#3</span>
                    </div>
                    <p class="text-2xl font-bold text-yellow-600">2,950,000 ريال</p>
                    <p class="text-sm text-yellow-600 mt-1">مكاتب فاخرة</p>
                </div>
                
                <div class="bg-gradient-to-r from-green-50 to-green-100 rounded-lg p-4 border border-green-200">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-bold text-green-800">مكة المركزية</h4>
                        <span class="bg-green-500 text-white text-xs font-bold px-2 py-1 rounded-full">#4</span>
                    </div>
                    <p class="text-2xl font-bold text-green-600">2,450,000 ريال</p>
                    <p class="text-sm text-green-600 mt-1">عقارات تجارية</p>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
function refreshHeatmap() {
    const button = event.target;
    const originalContent = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin ml-2"></i> جاري التحديث...';
    button.disabled = true;
    
    setTimeout(() => {
        location.reload();
    }, 1000);
}

// Interactive map filters
document.addEventListener('DOMContentLoaded', function() {
    const citySelect = document.querySelector('select');
    const typeSelect = document.querySelectorAll('select')[1];
    
    citySelect.addEventListener('change', function() {
        console.log('Filtering by city:', this.value);
        // Add AJAX call to filter heatmap
    });
    
    typeSelect.addEventListener('change', function() {
        console.log('Filtering by type:', this.value);
        // Add AJAX call to filter heatmap
    });
});
</script>
@endpush

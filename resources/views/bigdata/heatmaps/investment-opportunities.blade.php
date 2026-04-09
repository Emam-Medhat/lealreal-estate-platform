@extends('admin.layouts.admin')

@section('title', 'خرائط فرص الاستثمار')
@section('page-title', 'خرائط فرص الاستثمار')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">
                    <i class="fas fa-coins text-purple-500 ml-3"></i>
                    خرائط فرص الاستثمار
                </h1>
                <p class="text-gray-600 mt-2">تحديد أفضل المناطق والأنواع للاستثمار العقاري</p>
            </div>
            <div class="flex space-x-reverse space-x-3">
                <a href="{{ route('bigdata.heatmaps') }}" class="bg-gradient-to-r from-gray-500 to-gray-600 text-white px-6 py-3 rounded-lg hover:from-gray-600 hover:to-gray-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    <i class="fas fa-arrow-right ml-2"></i>
                    العودة للخرائط
                </a>
                <button onclick="refreshHeatmap()" class="bg-gradient-to-r from-purple-500 to-purple-600 text-white px-6 py-3 rounded-lg hover:from-purple-600 hover:to-purple-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    <i class="fas fa-sync-alt ml-2"></i>
                    تحديث
                </button>
            </div>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm font-medium">متوسط العائد</p>
                        <p class="text-3xl font-bold mt-2">18.7%</p>
                        <p class="text-purple-100 text-sm mt-2">
                            <i class="fas fa-chart-line ml-1"></i>
                            +3.2% هذا الشهر
                        </p>
                    </div>
                    <div class="bg-white/20 rounded-full p-4">
                        <i class="fas fa-percentage text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm font-medium">أفضل فرصة</p>
                        <p class="text-3xl font-bold mt-2">28.5%</p>
                        <p class="text-green-100 text-sm mt-2">
                            <i class="fas fa-trophy ml-1"></i>
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
                        <p class="text-amber-100 text-sm font-medium">فرص متاحة</p>
                        <p class="text-3xl font-bold mt-2">147</p>
                        <p class="text-amber-100 text-sm mt-2">
                            <i class="fas fa-briefcase ml-1"></i>
                            23 جديدة اليوم
                        </p>
                    </div>
                    <div class="bg-white/20 rounded-full p-4">
                        <i class="fas fa-briefcase text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-medium">مستوى المخاطرة</p>
                        <p class="text-3xl font-bold mt-2">منخفض</p>
                        <p class="text-blue-100 text-sm mt-2">
                            <i class="fas fa-shield-alt ml-1"></i>
                            آمن نسبياً
                        </p>
                    </div>
                    <div class="bg-white/20 rounded-full p-4">
                        <i class="fas fa-shield-alt text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Heatmap -->
        <div class="bg-white rounded-2xl shadow-xl p-6 mb-8">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-800">
                    <i class="fas fa-chart-pie text-purple-500 ml-3"></i>
                    خريطة فرص الاستثمار
                </h3>
                <div class="flex space-x-reverse space-x-3">
                    <select class="bg-gray-100 border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <option>جميع المدن</option>
                        <option>الرياض</option>
                        <option>جدة</option>
                        <option>الدمام</option>
                        <option>مكة المكرمة</option>
                    </select>
                    <select class="bg-gray-100 border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <option>جميع الأنواع</option>
                        <option>شقق</option>
                        <option>فلل</option>
                        <option>أراضٍ</option>
                        <option>مكاتب</option>
                    </select>
                </div>
            </div>
            
            <div class="mb-4">
                <img src="https://via.placeholder.com/1200x600/1cc88a/ffffff?text=Investment+Opportunities+Heatmap+Map" class="w-full h-96 object-cover rounded-lg" alt="Investment Opportunities Heatmap">
            </div>
            
            <!-- Legend -->
            <div class="flex items-center justify-between mt-6 p-4 bg-gray-50 rounded-lg">
                <div class="flex items-center space-x-reverse space-x-6">
                    <span class="text-sm font-medium text-gray-700">مستوى العائد:</span>
                    <div class="flex items-center space-x-reverse space-x-2">
                        <div class="w-4 h-4 bg-gray-500 rounded"></div>
                        <span class="text-xs text-gray-600">منخفض جداً</span>
                    </div>
                    <div class="flex items-center space-x-reverse space-x-2">
                        <div class="w-4 h-4 bg-blue-500 rounded"></div>
                        <span class="text-xs text-gray-600">منخفض</span>
                    </div>
                    <div class="flex items-center space-x-reverse space-x-2">
                        <div class="w-4 h-4 bg-green-500 rounded"></div>
                        <span class="text-xs text-gray-600">متوسط</span>
                    </div>
                    <div class="flex items-center space-x-reverse space-x-2">
                        <div class="w-4 h-4 bg-yellow-500 rounded"></div>
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

        <!-- Investment Analysis -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <div class="bg-white rounded-2xl shadow-xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-800">
                        <i class="fas fa-chart-bar text-green-500 ml-3"></i>
                        العائد حسب المدينة
                    </h3>
                    <div class="bg-green-100 rounded-full p-2">
                        <i class="fas fa-city text-green-600"></i>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">الرياض</span>
                            <span class="text-sm font-bold text-red-600">22.5%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-red-600 h-2 rounded-full" style="width: 85%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">جدة</span>
                            <span class="text-sm font-bold text-orange-600">18.7%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-orange-600 h-2 rounded-full" style="width: 72%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">الدمام</span>
                            <span class="text-sm font-bold text-yellow-600">15.3%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-yellow-600 h-2 rounded-full" style="width: 58%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">مكة المكرمة</span>
                            <span class="text-sm font-bold text-green-600">12.8%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-green-600 h-2 rounded-full" style="width: 45%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-800">
                        <i class="fas fa-chart-pie text-purple-500 ml-3"></i>
                        العائد حسب النوع
                    </h3>
                    <div class="bg-purple-100 rounded-full p-2">
                        <i class="fas fa-building text-purple-600"></i>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">أراضٍ</span>
                            <span class="text-sm font-bold text-red-600">25.8%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-red-600 h-2 rounded-full" style="width: 92%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">مكاتب</span>
                            <span class="text-sm font-bold text-orange-600">20.4%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-orange-600 h-2 rounded-full" style="width: 78%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">فلل</span>
                            <span class="text-sm font-bold text-yellow-600">16.7%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-yellow-600 h-2 rounded-full" style="width: 65%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">شقق</span>
                            <span class="text-sm font-bold text-green-600">11.2%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-green-600 h-2 rounded-full" style="width: 42%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Opportunities -->
        <div class="bg-white rounded-2xl shadow-xl p-6 mb-8">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-800">
                    <i class="fas fa-star text-yellow-500 ml-3"></i>
                    أفضل فرص الاستثمار
                </h3>
                <div class="bg-yellow-100 rounded-full p-2">
                    <i class="fas fa-trophy text-yellow-600"></i>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-gradient-to-r from-red-50 to-red-100 rounded-lg p-4 border border-red-200">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-bold text-red-800">أراضٍ الرياض الشمالية</h4>
                        <span class="bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">#1</span>
                    </div>
                    <p class="text-2xl font-bold text-red-600">28.5%</p>
                    <p class="text-sm text-red-600 mt-1">عائد سنوي</p>
                    <div class="mt-2 text-xs text-red-500">
                        <i class="fas fa-shield-alt ml-1"></i>
                        مخاطرة منخفضة
                    </div>
                </div>
                
                <div class="bg-gradient-to-r from-orange-50 to-orange-100 rounded-lg p-4 border border-orange-200">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-bold text-orange-800">مكاتب جدة التجارية</h4>
                        <span class="bg-orange-500 text-white text-xs font-bold px-2 py-1 rounded-full">#2</span>
                    </div>
                    <p class="text-2xl font-bold text-orange-600">24.3%</p>
                    <p class="text-sm text-orange-600 mt-1">عائد سنوي</p>
                    <div class="mt-2 text-xs text-orange-500">
                        <i class="fas fa-shield-alt ml-1"></i>
                        مخاطرة متوسطة
                    </div>
                </div>
                
                <div class="bg-gradient-to-r from-yellow-50 to-yellow-100 rounded-lg p-4 border border-yellow-200">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-bold text-yellow-800">فلل الدمام الفاخرة</h4>
                        <span class="bg-yellow-500 text-white text-xs font-bold px-2 py-1 rounded-full">#3</span>
                    </div>
                    <p class="text-2xl font-bold text-yellow-600">21.7%</p>
                    <p class="text-sm text-yellow-600 mt-1">عائد سنوي</p>
                    <div class="mt-2 text-xs text-yellow-500">
                        <i class="fas fa-shield-alt ml-1"></i>
                        مخاطرة متوسطة
                    </div>
                </div>
                
                <div class="bg-gradient-to-r from-green-50 to-green-100 rounded-lg p-4 border border-green-200">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-bold text-green-800">شقق الرياض الجديدة</h4>
                        <span class="bg-green-500 text-white text-xs font-bold px-2 py-1 rounded-full">#4</span>
                    </div>
                    <p class="text-2xl font-bold text-green-600">18.9%</p>
                    <p class="text-sm text-green-600 mt-1">عائد سنوي</p>
                    <div class="mt-2 text-xs text-green-500">
                        <i class="fas fa-shield-alt ml-1"></i>
                        مخاطرة منخفضة
                    </div>
                </div>
            </div>
        </div>

        <!-- Risk Analysis -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <div class="bg-white rounded-2xl shadow-xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-800">
                        <i class="fas fa-exclamation-triangle text-amber-500 ml-3"></i>
                        تحليل المخاطر
                    </h3>
                    <div class="bg-amber-100 rounded-full p-2">
                        <i class="fas fa-chart-line text-amber-600"></i>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                        <div>
                            <h6 class="font-medium text-green-800">مخاطرة منخفضة</h6>
                            <p class="text-xs text-green-600">45 فرصة استثمارية</p>
                        </div>
                        <span class="text-green-600 font-bold">15-20%</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-yellow-50 rounded-lg">
                        <div>
                            <h6 class="font-medium text-yellow-800">مخاطرة متوسطة</h6>
                            <p class="text-xs text-yellow-600">78 فرصة استثمارية</p>
                        </div>
                        <span class="text-yellow-600 font-bold">20-25%</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-red-50 rounded-lg">
                        <div>
                            <h6 class="font-medium text-red-800">مخاطرة مرتفعة</h6>
                            <p class="text-xs text-red-600">24 فرصة استثمارية</p>
                        </div>
                        <span class="text-red-600 font-bold">25%+</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-800">
                        <i class="fas fa-clock text-blue-500 ml-3"></i>
                        استحقاق الاستثمار
                    </h3>
                    <div class="bg-blue-100 rounded-full p-2">
                        <i class="fas fa-calendar text-blue-600"></i>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">قصير الأجل (1-3 سنوات)</span>
                            <span class="text-sm font-bold text-green-600">12-18%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-green-600 h-2 rounded-full" style="width: 65%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">متوسط الأجل (3-7 سنوات)</span>
                            <span class="text-sm font-bold text-yellow-600">18-25%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-yellow-600 h-2 rounded-full" style="width: 75%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">طويل الأجل (7+ سنوات)</span>
                            <span class="text-sm font-bold text-red-600">25-35%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-red-600 h-2 rounded-full" style="width: 85%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Investments -->
        <div class="bg-white rounded-2xl shadow-xl p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-800">
                    <i class="fas fa-history text-purple-500 ml-3"></i>
                    الاستثمارات الأخيرة
                </h3>
                <div class="bg-purple-100 rounded-full p-2">
                    <i class="fas fa-briefcase text-purple-600"></i>
                </div>
            </div>
            
            <div class="space-y-4">
                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                    <div>
                        <h6 class="font-medium text-gray-800">أرض سكنية - الرياض الشمالية</h6>
                        <p class="text-xs text-gray-500">منذ يومين</p>
                    </div>
                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">مكتمل</span>
                </div>
                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                    <div>
                        <h6 class="font-medium text-gray-800">مكتب تجاري - جدة</h6>
                        <p class="text-xs text-gray-500">منذ 3 أيام</p>
                    </div>
                    <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">قيد التنفيذ</span>
                </div>
                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                    <div>
                        <h6 class="font-medium text-gray-800">فيلا سكنية - الدمام</h6>
                        <p class="text-xs text-gray-500">منذ أسبوع</p>
                    </div>
                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">مكتمل</span>
                </div>
                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                    <div>
                        <h6 class="font-medium text-gray-800">شقق سكنية - الرياض</h6>
                        <p class="text-xs text-gray-500">منذ أسبوعين</p>
                    </div>
                    <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded-full">قيد المراجعة</span>
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

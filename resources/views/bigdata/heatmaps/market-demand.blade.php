@extends('admin.layouts.admin')

@section('title', 'خرائط الطلب في السوق')
@section('page-title', 'خرائط الطلب في السوق')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">
                    <i class="fas fa-chart-area text-green-500 ml-3"></i>
                    خرائط الطلب في السوق
                </h1>
                <p class="text-gray-600 mt-2">تحليل الطلب على العقارات حسب النوع والموقع</p>
            </div>
            <div class="flex space-x-reverse space-x-3">
                <a href="{{ route('bigdata.heatmaps') }}" class="bg-gradient-to-r from-gray-500 to-gray-600 text-white px-6 py-3 rounded-lg hover:from-gray-600 hover:to-gray-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    <i class="fas fa-arrow-right ml-2"></i>
                    العودة للخرائط
                </a>
                <button onclick="refreshHeatmap()" class="bg-gradient-to-r from-green-500 to-green-600 text-white px-6 py-3 rounded-lg hover:from-green-600 hover:to-green-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    <i class="fas fa-sync-alt ml-2"></i>
                    تحديث
                </button>
            </div>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm font-medium">مؤشر الطلب</p>
                        <p class="text-3xl font-bold mt-2">87.3%</p>
                        <p class="text-green-100 text-sm mt-2">
                            <i class="fas fa-arrow-up ml-1"></i>
                            +12.5% هذا الأسبوع
                        </p>
                    </div>
                    <div class="bg-white/20 rounded-full p-4">
                        <i class="fas fa-chart-line text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-medium">أعلى طلب</p>
                        <p class="text-3xl font-bold mt-2">شقق</p>
                        <p class="text-blue-100 text-sm mt-2">
                            <i class="fas fa-home ml-1"></i>
                            45% من السوق
                        </p>
                    </div>
                    <div class="bg-white/20 rounded-full p-4">
                        <i class="fas fa-building text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-2xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-amber-100 text-sm font-medium">المنطقة الأكثر طلباً</p>
                        <p class="text-3xl font-bold mt-2">الرياض</p>
                        <p class="text-amber-100 text-sm mt-2">
                            <i class="fas fa-map-marker-alt ml-1"></i>
                            32% من الطلبات
                        </p>
                    </div>
                    <div class="bg-white/20 rounded-full p-4">
                        <i class="fas fa-map text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm font-medium">استفسارات اليوم</p>
                        <p class="text-3xl font-bold mt-2">1,247</p>
                        <p class="text-purple-100 text-sm mt-2">
                            <i class="fas fa-phone ml-1"></i>
                            نشط حالياً
                        </p>
                    </div>
                    <div class="bg-white/20 rounded-full p-4">
                        <i class="fas fa-headset text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Heatmap -->
        <div class="bg-white rounded-2xl shadow-xl p-6 mb-8">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-800">
                    <i class="fas fa-fire text-orange-500 ml-3"></i>
                    خريطة الطلب في السوق
                </h3>
                <div class="flex space-x-reverse space-x-3">
                    <select class="bg-gray-100 border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option>جميع المدن</option>
                        <option>الرياض</option>
                        <option>جدة</option>
                        <option>الدمام</option>
                        <option>مكة المكرمة</option>
                    </select>
                    <select class="bg-gray-100 border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option>جميع الأنواع</option>
                        <option>شقق</option>
                        <option>فلل</option>
                        <option>أراضٍ</option>
                        <option>مكاتب</option>
                    </select>
                </div>
            </div>
            
            <div class="mb-4">
                <img src="https://via.placeholder.com/1200x600/36b9cc/ffffff?text=Market+Demand+Heatmap+Map" class="w-full h-96 object-cover rounded-lg" alt="Market Demand Heatmap">
            </div>
            
            <!-- Legend -->
            <div class="flex items-center justify-between mt-6 p-4 bg-gray-50 rounded-lg">
                <div class="flex items-center space-x-reverse space-x-6">
                    <span class="text-sm font-medium text-gray-700">مستوى الطلب:</span>
                    <div class="flex items-center space-x-reverse space-x-2">
                        <div class="w-4 h-4 bg-blue-500 rounded"></div>
                        <span class="text-xs text-gray-600">منخفض جداً</span>
                    </div>
                    <div class="flex items-center space-x-reverse space-x-2">
                        <div class="w-4 h-4 bg-cyan-500 rounded"></div>
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

        <!-- Demand Analysis -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <div class="bg-white rounded-2xl shadow-xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-800">
                        <i class="fas fa-chart-bar text-blue-500 ml-3"></i>
                        الطلب حسب المدينة
                    </h3>
                    <div class="bg-blue-100 rounded-full p-2">
                        <i class="fas fa-city text-blue-600"></i>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">الرياض</span>
                            <span class="text-sm font-bold text-red-600">طلب مرتفع جداً</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-red-600 h-2 rounded-full" style="width: 92%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">جدة</span>
                            <span class="text-sm font-bold text-orange-600">طلب مرتفع</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-orange-600 h-2 rounded-full" style="width: 78%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">الدمام</span>
                            <span class="text-sm font-bold text-yellow-600">طلب متوسط</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-yellow-600 h-2 rounded-full" style="width: 65%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">مكة المكرمة</span>
                            <span class="text-sm font-bold text-cyan-600">طلب منخفض</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-cyan-600 h-2 rounded-full" style="width: 45%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-800">
                        <i class="fas fa-chart-pie text-green-500 ml-3"></i>
                        الطلب حسب النوع
                    </h3>
                    <div class="bg-green-100 rounded-full p-2">
                        <i class="fas fa-building text-green-600"></i>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">شقق</span>
                            <span class="text-sm font-bold text-red-600">طلب مرتفع جداً</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-red-600 h-2 rounded-full" style="width: 88%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">فلل</span>
                            <span class="text-sm font-bold text-orange-600">طلب مرتفع</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-orange-600 h-2 rounded-full" style="width: 72%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">أراضٍ</span>
                            <span class="text-sm font-bold text-yellow-600">طلب متوسط</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-yellow-600 h-2 rounded-full" style="width: 58%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">مكاتب</span>
                            <span class="text-sm font-bold text-cyan-600">طلب منخفض</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-cyan-600 h-2 rounded-full" style="width: 35%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hot Areas -->
        <div class="bg-white rounded-2xl shadow-xl p-6 mb-8">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-800">
                    <i class="fas fa-fire text-red-500 ml-3"></i>
                    المناطق الأكثر طلباً
                </h3>
                <div class="bg-red-100 rounded-full p-2">
                    <i class="fas fa-thermometer-full text-red-600"></i>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-gradient-to-r from-red-50 to-red-100 rounded-lg p-4 border border-red-200">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-bold text-red-800">الرياض الشمالية</h4>
                        <span class="bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">ساخن</span>
                    </div>
                    <p class="text-2xl font-bold text-red-600">98%</p>
                    <p class="text-sm text-red-600 mt-1">طلب مرتفع جداً</p>
                    <div class="mt-2 text-xs text-red-500">
                        <i class="fas fa-users ml-1"></i>
                        342 استفسار هذا الأسبوع
                    </div>
                </div>
                
                <div class="bg-gradient-to-r from-orange-50 to-orange-100 rounded-lg p-4 border border-orange-200">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-bold text-orange-800">جدة الشاطئية</h4>
                        <span class="bg-orange-500 text-white text-xs font-bold px-2 py-1 rounded-full">دافئ</span>
                    </div>
                    <p class="text-2xl font-bold text-orange-600">85%</p>
                    <p class="text-sm text-orange-600 mt-1">طلب مرتفع</p>
                    <div class="mt-2 text-xs text-orange-500">
                        <i class="fas fa-users ml-1"></i>
                        287 استفسار هذا الأسبوع
                    </div>
                </div>
                
                <div class="bg-gradient-to-r from-yellow-50 to-yellow-100 rounded-lg p-4 border border-yellow-200">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-bold text-yellow-800">الدمام التجارية</h4>
                        <span class="bg-yellow-500 text-white text-xs font-bold px-2 py-1 rounded-full">نشط</span>
                    </div>
                    <p class="text-2xl font-bold text-yellow-600">72%</p>
                    <p class="text-sm text-yellow-600 mt-1">طلب متوسط</p>
                    <div class="mt-2 text-xs text-yellow-500">
                        <i class="fas fa-users ml-1"></i>
                        198 استفسار هذا الأسبوع
                    </div>
                </div>
                
                <div class="bg-gradient-to-r from-green-50 to-green-100 rounded-lg p-4 border border-green-200">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-bold text-green-800">مكة المركزية</h4>
                        <span class="bg-green-500 text-white text-xs font-bold px-2 py-1 rounded-full">مستقر</span>
                    </div>
                    <p class="text-2xl font-bold text-green-600">58%</p>
                    <p class="text-sm text-green-600 mt-1">طلب منخفض</p>
                    <div class="mt-2 text-xs text-green-500">
                        <i class="fas fa-users ml-1"></i>
                        124 استفسار هذا الأسبوع
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Inquiries -->
        <div class="bg-white rounded-2xl shadow-xl p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-800">
                    <i class="fas fa-clock text-blue-500 ml-3"></i>
                    الاستفسارات الأخيرة
                </h3>
                <div class="bg-blue-100 rounded-full p-2">
                    <i class="fas fa-phone text-blue-600"></i>
                </div>
            </div>
            
            <div class="space-y-4">
                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                    <div>
                        <h6 class="font-medium text-gray-800">شقة سكنية - الرياض الشمالية</h6>
                        <p class="text-xs text-gray-500">منذ 5 دقائق</p>
                    </div>
                    <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full">عاجل</span>
                </div>
                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                    <div>
                        <h6 class="font-medium text-gray-800">فيلا - جدة الشاطئية</h6>
                        <p class="text-xs text-gray-500">منذ 15 دقيقة</p>
                    </div>
                    <span class="bg-orange-100 text-orange-800 text-xs font-medium px-2.5 py-0.5 rounded-full">مرتفع</span>
                </div>
                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                    <div>
                        <h6 class="font-medium text-gray-800">أرض سكنية - الدمام</h6>
                        <p class="text-xs text-gray-500">منذ 30 دقيقة</p>
                    </div>
                    <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded-full">متوسط</span>
                </div>
                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                    <div>
                        <h6 class="font-medium text-gray-800">مكتب تجاري - الرياض</h6>
                        <p class="text-xs text-gray-500">منذ ساعة</p>
                    </div>
                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">منخفض</span>
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

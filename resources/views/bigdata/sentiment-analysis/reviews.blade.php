@extends('admin.layouts.admin')

@section('title', 'تحليل المراجعات')
@section('page-title', 'تحليل المراجعات')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">
                    <i class="fas fa-star text-yellow-500 ml-3"></i>
                    تحليل المراجعات
                </h1>
                <p class="text-gray-600 mt-2">تحليل مشاعر العملاء في المراجعات والتقييمات</p>
            </div>
            <div class="flex space-x-reverse space-x-3">
                <a href="{{ route('bigdata.sentiment-analysis') }}" class="bg-gradient-to-r from-gray-500 to-gray-600 text-white px-6 py-3 rounded-lg hover:from-gray-600 hover:to-gray-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    <i class="fas fa-arrow-right ml-2"></i>
                    العودة للتحليل
                </a>
                <button onclick="refreshReviews()" class="bg-gradient-to-r from-yellow-500 to-yellow-600 text-white px-6 py-3 rounded-lg hover:from-yellow-600 hover:to-yellow-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
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
                        <p class="text-green-100 text-sm font-medium">المراجعات الإيجابية</p>
                        <p class="text-3xl font-bold mt-2">3,847</p>
                        <p class="text-green-100 text-sm mt-2">
                            <i class="fas fa-smile ml-1"></i>
                            72% من الإجمالي
                        </p>
                    </div>
                    <div class="bg-white/20 rounded-full p-4">
                        <i class="fas fa-smile text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-2xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-yellow-100 text-sm font-medium">المراجعات المحايدة</p>
                        <p class="text-3xl font-bold mt-2">962</p>
                        <p class="text-yellow-100 text-sm mt-2">
                            <i class="fas fa-meh ml-1"></i>
                            18% من الإجمالي
                        </p>
                    </div>
                    <div class="bg-white/20 rounded-full p-4">
                        <i class="fas fa-meh text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-2xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-red-100 text-sm font-medium">المراجعات السلبية</p>
                        <p class="text-3xl font-bold mt-2">534</p>
                        <p class="text-red-100 text-sm mt-2">
                            <i class="fas fa-frown ml-1"></i>
                            10% من الإجمالي
                        </p>
                    </div>
                    <div class="bg-white/20 rounded-full p-4">
                        <i class="fas fa-frown text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-medium">متوسط التقييم</p>
                        <p class="text-3xl font-bold mt-2">4.2</p>
                        <p class="text-blue-100 text-sm mt-2">
                            <i class="fas fa-star ml-1"></i>
                            من 5.0
                        </p>
                    </div>
                    <div class="bg-white/20 rounded-full p-4">
                        <i class="fas fa-star text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reviews Analysis -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <div class="bg-white rounded-2xl shadow-xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-800">
                        <i class="fas fa-chart-bar text-blue-500 ml-3"></i>
                        توزيع التقييمات
                    </h3>
                    <div class="bg-blue-100 rounded-full p-2">
                        <i class="fas fa-chart-bar text-blue-600"></i>
                    </div>
                </div>
                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">5 نجوم</span>
                            <span class="text-sm font-bold text-green-600">2,156 مراجعة</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-green-600 h-2 rounded-full" style="width: 85%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">4 نجوم</span>
                            <span class="text-sm font-bold text-blue-600">1,691 مراجعة</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: 65%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">3 نجوم</span>
                            <span class="text-sm font-bold text-yellow-600">962 مراجعة</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-yellow-600 h-2 rounded-full" style="width: 35%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">نجمتان</span>
                            <span class="text-sm font-bold text-orange-600">324 مراجعة</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-orange-600 h-2 rounded-full" style="width: 15%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">نجمة</span>
                            <span class="text-sm font-bold text-red-600">210 مراجعة</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-red-600 h-2 rounded-full" style="width: 8%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-800">
                        <i class="fas fa-comments text-green-500 ml-3"></i>
                        الكلمات المفتاحية
                    </h3>
                    <div class="bg-green-100 rounded-full p-2">
                        <i class="fas fa-key text-green-600"></i>
                    </div>
                </div>
                <div class="space-y-4">
                    <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                        <span class="text-sm font-medium text-green-800">ممتاز</span>
                        <span class="text-sm font-bold text-green-600">1,234 مرة</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
                        <span class="text-sm font-medium text-blue-800">جيد</span>
                        <span class="text-sm font-bold text-blue-600">987 مرة</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-yellow-50 rounded-lg">
                        <span class="text-sm font-medium text-yellow-800">نظيف</span>
                        <span class="text-sm font-bold text-yellow-600">876 مرة</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-purple-50 rounded-lg">
                        <span class="text-sm font-medium text-purple-800">موقع ممتاز</span>
                        <span class="text-sm font-bold text-purple-600">654 مرة</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-red-50 rounded-lg">
                        <span class="text-sm font-medium text-red-800">سيء</span>
                        <span class="text-sm font-bold text-red-600">123 مرة</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Reviews -->
        <div class="bg-white rounded-2xl shadow-xl p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-800">
                    <i class="fas fa-clock text-orange-500 ml-3"></i>
                    المراجعات الأخيرة
                </h3>
                <div class="bg-orange-100 rounded-full p-2">
                    <i class="fas fa-history text-orange-600"></i>
                </div>
            </div>
            
            <div class="space-y-4">
                <div class="p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <h6 class="font-medium text-gray-800">محمد أحمد - عقار الرياض</h6>
                            <div class="flex items-center mt-1">
                                <div class="flex text-yellow-400">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                                <span class="text-xs text-gray-500 mr-2">منذ ساعة</span>
                            </div>
                        </div>
                        <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">إيجابي</span>
                    </div>
                    <p class="text-sm text-gray-600">تجربة ممتازة جداً. العقار في موقع ممتاز والخدمة كانت رائعة. أنصح بالتعامل معهم بشدة.</p>
                </div>
                
                <div class="p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <h6 class="font-medium text-gray-800">فاطمة محمد - شقة جدة</h6>
                            <div class="flex items-center mt-1">
                                <div class="flex text-yellow-400">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="far fa-star"></i>
                                </div>
                                <span class="text-xs text-gray-500 mr-2">منذ 3 ساعات</span>
                            </div>
                        </div>
                        <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">إيجابي</span>
                    </div>
                    <p class="text-sm text-gray-600">جيد جداً لكن يحتاج بعض التحسينات في الخدمة. الموقع ممتاز والسعر معقول.</p>
                </div>
                
                <div class="p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <h6 class="font-medium text-gray-800">عبدالله سالم - فيلا الدمام</h6>
                            <div class="flex items-center mt-1">
                                <div class="flex text-yellow-400">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="far fa-star"></i>
                                    <i class="far fa-star"></i>
                                </div>
                                <span class="text-xs text-gray-500 mr-2">منذ 5 ساعات</span>
                            </div>
                        </div>
                        <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded-full">محايد</span>
                    </div>
                    <p class="text-sm text-gray-600">متوسط. العقار جيد لكن هناك بعض المشاكل في الصيانة تحتاج حل.</p>
                </div>
                
                <div class="p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <h6 class="font-medium text-gray-800">نورا أحمد - مكتب الرياض</h6>
                            <div class="flex items-center mt-1">
                                <div class="flex text-yellow-400">
                                    <i class="fas fa-star"></i>
                                    <i class="far fa-star"></i>
                                    <i class="far fa-star"></i>
                                    <i class="far fa-star"></i>
                                    <i class="far fa-star"></i>
                                </div>
                                <span class="text-xs text-gray-500 mr-2">منذ يوم</span>
                            </div>
                        </div>
                        <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full">سلبي</span>
                    </div>
                    <p class="text-sm text-gray-600">سيء جداً. الخدمة سيئة والمكتب غير نظيف. لا أنصح بالتعامل معهم.</p>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
function refreshReviews() {
    const button = event.target;
    const originalContent = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin ml-2"></i> جاري التحديث...';
    button.disabled = true;
    
    setTimeout(() => {
        location.reload();
    }, 1000);
}
</script>
@endpush

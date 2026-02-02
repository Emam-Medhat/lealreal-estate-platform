@extends('admin.layouts.admin')

@section('title', 'تحليل وسائل التواصل')
@section('page-title', 'تحليل وسائل التواصل')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">
                    <i class="fas fa-share-alt text-blue-500 ml-3"></i>
                    تحليل وسائل التواصل
                </h1>
                <p class="text-gray-600 mt-2">تحليل المشاعر في منشورات وسائل التواصل الاجتماعي</p>
            </div>
            <div class="flex space-x-reverse space-x-3">
                <a href="{{ route('bigdata.sentiment-analysis') }}" class="bg-gradient-to-r from-gray-500 to-gray-600 text-white px-6 py-3 rounded-lg hover:from-gray-600 hover:to-gray-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    <i class="fas fa-arrow-right ml-2"></i>
                    العودة للتحليل
                </a>
                <button onclick="refreshSocialMedia()" class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-6 py-3 rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
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
                        <p class="text-blue-100 text-sm font-medium">تويتر</p>
                        <p class="text-3xl font-bold mt-2">2,341</p>
                        <p class="text-blue-100 text-sm mt-2">
                            <i class="fab fa-twitter ml-1"></i>
                            منشور محلل
                        </p>
                    </div>
                    <div class="bg-white/20 rounded-full p-4">
                        <i class="fab fa-twitter text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm font-medium">فيسبوك</p>
                        <p class="text-3xl font-bold mt-2">1,876</p>
                        <p class="text-purple-100 text-sm mt-2">
                            <i class="fab fa-facebook ml-1"></i>
                            منشور محلل
                        </p>
                    </div>
                    <div class="bg-white/20 rounded-full p-4">
                        <i class="fab fa-facebook text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-pink-500 to-pink-600 rounded-2xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-pink-100 text-sm font-medium">انستغرام</p>
                        <p class="text-3xl font-bold mt-2">1,234</p>
                        <p class="text-pink-100 text-sm mt-2">
                            <i class="fab fa-instagram ml-1"></i>
                            منشور محلل
                        </p>
                    </div>
                    <div class="bg-white/20 rounded-full p-4">
                        <i class="fab fa-instagram text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-2xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-red-100 text-sm font-medium">يوتيوب</p>
                        <p class="text-3xl font-bold mt-2">783</p>
                        <p class="text-red-100 text-sm mt-2">
                            <i class="fab fa-youtube ml-1"></i>
                            تعليق محلل
                        </p>
                    </div>
                    <div class="bg-white/20 rounded-full p-4">
                        <i class="fab fa-youtube text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Social Media Analysis -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <div class="bg-white rounded-2xl shadow-xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-800">
                        <i class="fab fa-twitter text-blue-500 ml-3"></i>
                        تحليل تويتر
                    </h3>
                    <div class="bg-blue-100 rounded-full p-2">
                        <i class="fab fa-twitter text-blue-600"></i>
                    </div>
                </div>
                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">إيجابي</span>
                            <span class="text-sm font-bold text-green-600">68%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-green-600 h-2 rounded-full" style="width: 68%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">محايد</span>
                            <span class="text-sm font-bold text-yellow-600">22%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-yellow-600 h-2 rounded-full" style="width: 22%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">سلبي</span>
                            <span class="text-sm font-bold text-red-600">10%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-red-600 h-2 rounded-full" style="width: 10%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-800">
                        <i class="fab fa-facebook text-purple-500 ml-3"></i>
                        تحليل فيسبوك
                    </h3>
                    <div class="bg-purple-100 rounded-full p-2">
                        <i class="fab fa-facebook text-purple-600"></i>
                    </div>
                </div>
                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">إيجابي</span>
                            <span class="text-sm font-bold text-green-600">72%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-green-600 h-2 rounded-full" style="width: 72%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">محايد</span>
                            <span class="text-sm font-bold text-yellow-600">18%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-yellow-600 h-2 rounded-full" style="width: 18%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">سلبي</span>
                            <span class="text-sm font-bold text-red-600">10%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-red-600 h-2 rounded-full" style="width: 10%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Trending Topics -->
        <div class="bg-white rounded-2xl shadow-xl p-6 mb-8">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-800">
                    <i class="fas fa-hashtag text-green-500 ml-3"></i>
                    المواضيع الرائجة
                </h3>
                <div class="bg-green-100 rounded-full p-2">
                    <i class="fas fa-fire text-green-600"></i>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-gradient-to-r from-green-50 to-green-100 rounded-lg p-4 border border-green-200">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-bold text-green-800">#عقارات_الرياض</h4>
                        <span class="bg-green-500 text-white text-xs font-bold px-2 py-1 rounded-full">ساخن</span>
                    </div>
                    <p class="text-2xl font-bold text-green-600">8,234</p>
                    <p class="text-sm text-green-600 mt-1">منشور</p>
                    <div class="mt-2 text-xs text-green-500">
                        <i class="fas fa-arrow-up ml-1"></i>
                        +45% اليوم
                    </div>
                </div>
                
                <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg p-4 border border-blue-200">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-bold text-blue-800">#استثمار_عقاري</h4>
                        <span class="bg-blue-500 text-white text-xs font-bold px-2 py-1 rounded-full">رائج</span>
                    </div>
                    <p class="text-2xl font-bold text-blue-600">5,678</p>
                    <p class="text-sm text-blue-600 mt-1">منشور</p>
                    <div class="mt-2 text-xs text-blue-500">
                        <i class="fas fa-arrow-up ml-1"></i>
                        +32% اليوم
                    </div>
                </div>
                
                <div class="bg-gradient-to-r from-purple-50 to-purple-100 rounded-lg p-4 border border-purple-200">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-bold text-purple-800">#فلل_فاخرة</h4>
                        <span class="bg-purple-500 text-white text-xs font-bold px-2 py-1 rounded-full">نشط</span>
                    </div>
                    <p class="text-2xl font-bold text-purple-600">3,456</p>
                    <p class="text-sm text-purple-600 mt-1">منشور</p>
                    <div class="mt-2 text-xs text-purple-500">
                        <i class="fas fa-arrow-up ml-1"></i>
                        +18% اليوم
                    </div>
                </div>
                
                <div class="bg-gradient-to-r from-yellow-50 to-yellow-100 rounded-lg p-4 border border-yellow-200">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-bold text-yellow-800">#تمويل_عقاري</h4>
                        <span class="bg-yellow-500 text-white text-xs font-bold px-2 py-1 rounded-full">صاعد</span>
                    </div>
                    <p class="text-2xl font-bold text-yellow-600">2,123</p>
                    <p class="text-sm text-yellow-600 mt-1">منشور</p>
                    <div class="mt-2 text-xs text-yellow-500">
                        <i class="fas fa-arrow-up ml-1"></i>
                        +12% اليوم
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Posts -->
        <div class="bg-white rounded-2xl shadow-xl p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-800">
                    <i class="fas fa-clock text-orange-500 ml-3"></i>
                    المنشورات الأخيرة
                </h3>
                <div class="bg-orange-100 rounded-full p-2">
                    <i class="fas fa-history text-orange-600"></i>
                </div>
            </div>
            
            <div class="space-y-4">
                <div class="p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <div class="flex items-center mb-2">
                                <i class="fab fa-twitter text-blue-500 ml-2"></i>
                                <h6 class="font-medium text-gray-800">@user123</h6>
                                <span class="text-xs text-gray-500 mr-2">منذ 15 دقيقة</span>
                            </div>
                        </div>
                        <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">إيجابي</span>
                    </div>
                    <p class="text-sm text-gray-600">تجربة ممتازة مع شركة العقارات. الخدمة كانت رائعة والموظفين متعاونين جداً. #عقارات_الرياض</p>
                    <div class="flex items-center mt-2 text-xs text-gray-500">
                        <i class="fas fa-heart ml-3"></i>
                        <span>234</span>
                        <i class="fas fa-retweet ml-3"></i>
                        <span>56</span>
                        <i class="fas fa-comment ml-3"></i>
                        <span>12</span>
                    </div>
                </div>
                
                <div class="p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <div class="flex items-center mb-2">
                                <i class="fab fa-facebook text-purple-500 ml-2"></i>
                                <h6 class="font-medium text-gray-800">محمد أحمد</h6>
                                <span class="text-xs text-gray-500 mr-2">منذ ساعة</span>
                            </div>
                        </div>
                        <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded-full">محايد</span>
                    </div>
                    <p class="text-sm text-gray-600">الأسعار مرتفعة بعض الشيء ولكن الموقع ممتاز. نأمل أن تكون هناك عروض قريباً. #استثمار_عقاري</p>
                    <div class="flex items-center mt-2 text-xs text-gray-500">
                        <i class="fas fa-thumbs-up ml-3"></i>
                        <span>89</span>
                        <i class="fas fa-comment ml-3"></i>
                        <span>23</span>
                        <i class="fas fa-share ml-3"></i>
                        <span>5</span>
                    </div>
                </div>
                
                <div class="p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <div class="flex items-center mb-2">
                                <i class="fab fa-instagram text-pink-500 ml-2"></i>
                                <h6 class="font-medium text-gray-800">@realestate_sa</h6>
                                <span class="text-xs text-gray-500 mr-2">منذ ساعتين</span>
                            </div>
                        </div>
                        <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full">سلبي</span>
                    </div>
                    <p class="text-sm text-gray-600">خدمة سيئة جداً والتعامل غير مهني. لا أنصح بالتعامل معهم أبداً. #فلل_فاخرة</p>
                    <div class="flex items-center mt-2 text-xs text-gray-500">
                        <i class="fas fa-heart ml-3"></i>
                        <span>45</span>
                        <i class="fas fa-comment ml-3"></i>
                        <span>18</span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
function refreshSocialMedia() {
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

@extends('layouts.app')

@section('title', $title ?? 'مركز المساعدة')

@section('content')
<div class="container mx-auto px-4 py-8">
<!-- Page Header -->
<div class="mb-8">
    <div class="bg-gradient-to-r from-slate-700 to-slate-900 rounded-2xl shadow-2xl p-8 text-white relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-0 right-0 w-64 h-64 bg-white rounded-full -mr-32 -mt-32"></div>
            <div class="absolute bottom-0 left-0 w-48 h-48 bg-white rounded-full -ml-24 -mb-24"></div>
        </div>
        
        <div class="relative z-10">
            <div class="flex justify-between items-center">
                <div>
                    <div class="flex items-center mb-3">
                        <div class="bg-white bg-opacity-20 rounded-xl p-3 ml-4 backdrop-blur-sm border border-white border-opacity-30">
                            <i class="fas fa-question-circle text-white text-2xl"></i>
                        </div>
                        <div>
                            <h1 class="text-4xl font-bold text-white mb-1">مركز المساعدة</h1>
                            <p class="text-white text-lg">دليل شامل لاستخدام نظام التقارير</p>
                        </div>
                    </div>
                </div>
                <div class="flex space-x-reverse space-x-3">
                    <a href="{{ $backRoute ?? route('reports.index') }}" class="inline-flex items-center px-6 py-3 bg-white text-black rounded-xl text-sm font-medium hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition-all duration-300 transform hover:scale-105 shadow-lg">
                        <i class="fas fa-arrow-left ml-2"></i>
                        {{ $backLabel ?? 'العودة' }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Search Bar -->
<div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6 mb-8">
    <div class="flex items-center space-x-reverse space-x-4">
        <div class="bg-slate-600 rounded-lg p-3">
            <i class="fas fa-search text-white"></i>
        </div>
        <input type="text" id="helpSearch" placeholder="ابحث في مركز المساعدة..." 
               class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-500 focus:border-transparent bg-white text-black transition-all duration-200">
    </div>
</div>

<!-- Help Categories -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
    <!-- Getting Started -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
        <div class="flex items-center mb-4">
            <div class="bg-blue-100 rounded-lg p-3 ml-3">
                <i class="fas fa-rocket text-blue-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-black">البداية</h3>
        </div>
        <ul class="space-y-3">
            <li>
                <a href="#" class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors group">
                    <i class="fas fa-chevron-left text-gray-400 ml-2 group-hover:text-slate-600"></i>
                    <span class="text-black group-hover:text-slate-700">إنشاء أول تقرير</span>
                </a>
            </li>
            <li>
                <a href="#" class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors group">
                    <i class="fas fa-chevron-left text-gray-400 ml-2 group-hover:text-slate-600"></i>
                    <span class="text-black group-hover:text-slate-700">فهم لوحة التحكم</span>
                </a>
            </li>
            <li>
                <a href="#" class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors group">
                    <i class="fas fa-chevron-left text-gray-400 ml-2 group-hover:text-slate-600"></i>
                    <span class="text-black group-hover:text-slate-700">الإعدادات الأساسية</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- Report Types -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
        <div class="flex items-center mb-4">
            <div class="bg-green-100 rounded-lg p-3 ml-3">
                <i class="fas fa-file-alt text-green-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-black">أنواع التقارير</h3>
        </div>
        <ul class="space-y-3">
            <li>
                <a href="#" class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors group">
                    <i class="fas fa-chevron-left text-gray-400 ml-2 group-hover:text-slate-600"></i>
                    <span class="text-black group-hover:text-slate-700">تقارير المبيعات</span>
                </a>
            </li>
            <li>
                <a href="#" class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors group">
                    <i class="fas fa-chevron-left text-gray-400 ml-2 group-hover:text-slate-600"></i>
                    <span class="text-black group-hover:text-slate-700">تقارير الأداء</span>
                </a>
            </li>
            <li>
                <a href="#" class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors group">
                    <i class="fas fa-chevron-left text-gray-400 ml-2 group-hover:text-slate-600"></i>
                    <span class="text-black group-hover:text-slate-700">تقارير السوق</span>
                </a>
            </li>
            <li>
                <a href="#" class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors group">
                    <i class="fas fa-chevron-left text-gray-400 ml-2 group-hover:text-slate-600"></i>
                    <span class="text-black group-hover:text-slate-700">تقارير مخصصة</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- Advanced Features -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
        <div class="flex items-center mb-4">
            <div class="bg-purple-100 rounded-lg p-3 ml-3">
                <i class="fas fa-cogs text-purple-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-black">مميزات متقدمة</h3>
        </div>
        <ul class="space-y-3">
            <li>
                <a href="#" class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors group">
                    <i class="fas fa-chevron-left text-gray-400 ml-2 group-hover:text-slate-600"></i>
                    <span class="text-black group-hover:text-slate-700">الفلاتر المتقدمة</span>
                </a>
            </li>
            <li>
                <a href="#" class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors group">
                    <i class="fas fa-chevron-left text-gray-400 ml-2 group-hover:text-slate-600"></i>
                    <span class="text-black group-hover:text-slate-700">تصدير البيانات</span>
                </a>
            </li>
            <li>
                <a href="#" class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors group">
                    <i class="fas fa-chevron-left text-gray-400 ml-2 group-hover:text-slate-600"></i>
                    <span class="text-black group-hover:text-slate-700">الجدولة التلقائية</span>
                </a>
            </li>
            <li>
                <a href="#" class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors group">
                    <i class="fas fa-chevron-left text-gray-400 ml-2 group-hover:text-slate-600"></i>
                    <span class="text-black group-hover:text-slate-700">التكامل مع API</span>
                </a>
            </li>
        </ul>
    </div>
</div>

<!-- Popular Articles -->
<div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-8 mb-8">
    <h2 class="text-2xl font-bold text-black mb-6 flex items-center">
        <div class="bg-slate-600 rounded-lg p-2 ml-3 shadow-md">
            <i class="fas fa-star text-white"></i>
        </div>
        المقالات الشائعة
    </h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="border border-gray-200 rounded-xl p-6 hover:shadow-md transition-shadow">
            <div class="bg-blue-100 rounded-lg p-3 mb-4 inline-block">
                <i class="fas fa-chart-bar text-blue-600 text-xl"></i>
            </div>
            <h3 class="text-black font-semibold mb-2">كيفية إنشاء تقرير مبيعات مفصل</h3>
            <p class="text-black text-sm mb-4">دليل خطوة بخطوة لإنشاء تقارير المبيعات مع الفلاتر والتحليلات.</p>
            <a href="#" class="text-slate-600 hover:text-slate-700 text-sm font-medium">اقرأ المزيد →</a>
        </div>
        <div class="border border-gray-200 rounded-xl p-6 hover:shadow-md transition-shadow">
            <div class="bg-green-100 rounded-lg p-3 mb-4 inline-block">
                <i class="fas fa-download text-green-600 text-xl"></i>
            </div>
            <h3 class="text-black font-semibold mb-2">تصدير التقارير بصيغ متعددة</h3>
            <p class="text-black text-sm mb-4">تعلم كيفية تصدير تقاريرك بصيغ PDF, Excel, و CSV.</p>
            <a href="#" class="text-slate-600 hover:text-slate-700 text-sm font-medium">اقرأ المزيد →</a>
        </div>
        <div class="border border-gray-200 rounded-xl p-6 hover:shadow-md transition-shadow">
            <div class="bg-purple-100 rounded-lg p-3 mb-4 inline-block">
                <i class="fas fa-filter text-purple-600 text-xl"></i>
            </div>
            <h3 class="text-black font-semibold mb-2">استخدام الفلاتر المتقدمة</h3>
            <p class="text-black text-sm mb-4">استكشف قوة الفلاتر المتقدمة لتخصيص تقاريرك.</p>
            <a href="#" class="text-slate-600 hover:text-slate-700 text-sm font-medium">اقرأ المزيد →</a>
        </div>
    </div>
</div>

<!-- Video Tutorials -->
<div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-8 mb-8">
    <h2 class="text-2xl font-bold text-black mb-6 flex items-center">
        <div class="bg-slate-600 rounded-lg p-2 ml-3 shadow-md">
            <i class="fas fa-video text-white"></i>
        </div>
        فيديوهات تعليمية
    </h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="border border-gray-200 rounded-xl overflow-hidden hover:shadow-md transition-shadow">
            <div class="bg-gray-200 h-40 flex items-center justify-center">
                <i class="fas fa-play-circle text-4xl text-gray-400"></i>
            </div>
            <div class="p-4">
                <h3 class="text-black font-semibold mb-2">البداية السريعة</h3>
                <p class="text-black text-sm mb-2">تعلم أساسيات النظام في 5 دقائق</p>
                <span class="text-xs text-gray-500">5:00</span>
            </div>
        </div>
        <div class="border border-gray-200 rounded-xl overflow-hidden hover:shadow-md transition-shadow">
            <div class="bg-gray-200 h-40 flex items-center justify-center">
                <i class="fas fa-play-circle text-4xl text-gray-400"></i>
            </div>
            <div class="p-4">
                <h3 class="text-black font-semibold mb-2">إنشاء التقارير</h3>
                <p class="text-black text-sm mb-2">دليل شامل لإنشاء التقارير</p>
                <span class="text-xs text-gray-500">12:30</span>
            </div>
        </div>
        <div class="border border-gray-200 rounded-xl overflow-hidden hover:shadow-md transition-shadow">
            <div class="bg-gray-200 h-40 flex items-center justify-center">
                <i class="fas fa-play-circle text-4xl text-gray-400"></i>
            </div>
            <div class="p-4">
                <h3 class="text-black font-semibold mb-2">التحليلات المتقدمة</h3>
                <p class="text-black text-sm mb-2">استكشف مميزات التحليل المتقدمة</p>
                <span class="text-xs text-gray-500">8:45</span>
            </div>
        </div>
    </div>
</div>

<!-- Contact Support -->
<div class="bg-gradient-to-r from-slate-600 to-slate-700 rounded-2xl p-8 text-white text-center">
    <h2 class="text-2xl font-bold mb-4">لم تجد إجابتك؟</h2>
    <p class="text-white text-lg mb-6">فريق الدعم متاح لمساعدتك على مدار الساعة</p>
    <div class="flex justify-center space-x-reverse space-x-4">
        <a href="{{ route('reports.support.contact') }}" class="inline-flex items-center px-6 py-3 bg-white text-black rounded-xl text-sm font-medium hover:bg-gray-100 transition-all duration-300 transform hover:scale-105 shadow-lg">
            <i class="fas fa-envelope ml-2"></i>
            تواصل معنا
        </a>
        <a href="tel:+966123456789" class="inline-flex items-center px-6 py-3 bg-slate-800 text-white rounded-xl text-sm font-medium hover:bg-slate-900 transition-all duration-300 transform hover:scale-105 shadow-lg">
            <i class="fas fa-phone ml-2"></i>
            اتصل بنا
        </a>
    </div>
</div>

</div>
@endsection

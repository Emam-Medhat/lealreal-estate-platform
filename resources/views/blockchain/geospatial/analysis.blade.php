@extends('admin.layouts.admin')

@section('title', 'التحليلات المكانية')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-emerald-50 to-teal-100">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 p-8 mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-gradient-to-r from-emerald-600 to-teal-600 rounded-xl flex items-center justify-center">
                            <i class="fas fa-chart-area text-white text-xl"></i>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 bg-clip-text text-transparent">
                                التحليلات المكانية
                            </h1>
                            <p class="text-gray-600 text-lg">تحليل البيانات الجغرافية والمكانية المتقدم</p>
                        </div>
                    </div>
                </div>
                
                <div class="flex gap-3">
                    <button class="bg-emerald-600 text-white px-6 py-3 rounded-2xl hover:bg-emerald-700 transition-all duration-300 font-semibold shadow-lg hover:shadow-xl transform hover:scale-105">
                        <i class="fas fa-download ml-2"></i>
                        تصدير التقرير
                    </button>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white/80 backdrop-blur-xl rounded-2xl shadow-xl border border-white/20 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-map-marked-alt text-blue-600 text-xl"></i>
                    </div>
                    <span class="text-sm text-green-600 font-medium">+12.5%</span>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-1">2,847</h3>
                <p class="text-gray-600 text-sm">نقاط بيانات مكانية</p>
            </div>

            <div class="bg-white/80 backdrop-blur-xl rounded-2xl shadow-xl border border-white/20 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-chart-line text-green-600 text-xl"></i>
                    </div>
                    <span class="text-sm text-green-600 font-medium">+8.3%</span>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-1">94.2%</h3>
                <p class="text-gray-600 text-sm">دقة التحليل</p>
            </div>

            <div class="bg-white/80 backdrop-blur-xl rounded-2xl shadow-xl border border-white/20 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-layer-group text-purple-600 text-xl"></i>
                    </div>
                    <span class="text-sm text-green-600 font-medium">+15.7%</span>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-1">156</h3>
                <p class="text-gray-600 text-sm">طبقات بيانات</p>
            </div>

            <div class="bg-white/80 backdrop-blur-xl rounded-2xl shadow-xl border border-white/20 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-clock text-orange-600 text-xl"></i>
                    </div>
                    <span class="text-sm text-red-600 font-medium">-2.1%</span>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-1">1.2s</h3>
                <p class="text-gray-600 text-sm">متوسط وقت المعالجة</p>
            </div>
        </div>

        <!-- Analysis Tools -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <!-- Heat Map Analysis -->
            <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 p-8">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-14 h-14 bg-gradient-to-r from-red-500 to-orange-500 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-fire text-white text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">الخرائط الحرارية</h3>
                        <p class="text-gray-600">تحليل الكثافة والتوزيع</p>
                    </div>
                </div>
                <div class="space-y-4">
                    <div class="bg-gray-50 rounded-2xl p-4">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-gray-700">الكثافة السكانية</span>
                            <span class="text-sm text-gray-500">85%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-gradient-to-r from-red-500 to-orange-500 h-2 rounded-full" style="width: 85%"></div>
                        </div>
                    </div>
                    <div class="bg-gray-50 rounded-2xl p-4">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-gray-700">نشاط تجاري</span>
                            <span class="text-sm text-gray-500">72%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-gradient-to-r from-blue-500 to-cyan-500 h-2 rounded-full" style="width: 72%"></div>
                        </div>
                    </div>
                    <button class="w-full bg-gradient-to-r from-red-600 to-orange-600 text-white py-3 rounded-2xl hover:from-red-700 hover:to-orange-700 transition-all duration-300 font-semibold">
                        بدء التحليل
                    </button>
                </div>
            </div>

            <!-- Spatial Statistics -->
            <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 p-8">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-14 h-14 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-chart-bar text-white text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">الإحصائيات المكانية</h3>
                        <p class="text-gray-600">تحليل الأنماط والاتجاهات</p>
                    </div>
                </div>
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-blue-50 rounded-xl p-3 text-center">
                            <div class="text-2xl font-bold text-blue-600">428</div>
                            <div class="text-xs text-gray-600">منطقة تحليل</div>
                        </div>
                        <div class="bg-indigo-50 rounded-xl p-3 text-center">
                            <div class="text-2xl font-bold text-indigo-600">1.2K</div>
                            <div class="text-xs text-gray-600">نقطة بيانات</div>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">متوسط المسافة</span>
                            <span class="font-medium">2.4 km</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">الكثافة</span>
                            <span class="font-medium">156/km²</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">التغطية</span>
                            <span class="font-medium">89.3%</span>
                        </div>
                    </div>
                    <button class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-3 rounded-2xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 font-semibold">
                        عرض التفاصيل
                    </button>
                </div>
            </div>

            <!-- Pattern Recognition -->
            <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 p-8">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-14 h-14 bg-gradient-to-r from-purple-500 to-pink-500 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-brain text-white text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">التعرف على الأنماط</h3>
                        <p class="text-gray-600">اكتشاف الأنماط المكانية</p>
                    </div>
                </div>
                <div class="space-y-4">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between p-3 bg-purple-50 rounded-xl">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-purple-200 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-home text-purple-600 text-sm"></i>
                                </div>
                                <span class="text-sm font-medium">نمط سكني</span>
                            </div>
                            <span class="text-sm text-purple-600 font-medium">94%</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-pink-50 rounded-xl">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-pink-200 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-shopping-cart text-pink-600 text-sm"></i>
                                </div>
                                <span class="text-sm font-medium">نمط تجاري</span>
                            </div>
                            <span class="text-sm text-pink-600 font-medium">87%</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-indigo-50 rounded-xl">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-indigo-200 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-industry text-indigo-600 text-sm"></i>
                                </div>
                                <span class="text-sm font-medium">نمط صناعي</span>
                            </div>
                            <span class="text-sm text-indigo-600 font-medium">76%</span>
                        </div>
                    </div>
                    <button class="w-full bg-gradient-to-r from-purple-600 to-pink-600 text-white py-3 rounded-2xl hover:from-purple-700 hover:to-pink-700 transition-all duration-300 font-semibold">
                        تحليل متقدم
                    </button>
                </div>
            </div>
        </div>

        <!-- Interactive Map -->
        <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 p-8">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-900">الخريطة التفاعلية</h3>
                <div class="flex gap-2">
                    <button class="px-4 py-2 bg-blue-100 text-blue-700 rounded-xl hover:bg-blue-200 transition-colors text-sm font-medium">
                        <i class="fas fa-layer-group ml-2"></i>
                        الطبقات
                    </button>
                    <button class="px-4 py-2 bg-green-100 text-green-700 rounded-xl hover:bg-green-200 transition-colors text-sm font-medium">
                        <i class="fas fa-filter ml-2"></i>
                        الفلاتر
                    </button>
                </div>
            </div>
            <div class="bg-gray-100 rounded-2xl h-96 flex items-center justify-center">
                <div class="text-center">
                    <div class="w-16 h-16 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-map text-gray-400 text-2xl"></i>
                    </div>
                    <p class="text-gray-600 font-medium">الخريطة التفاعلية</p>
                    <p class="text-gray-500 text-sm">سيتم تحميل الخريطة هنا</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.dashboard')

@section('title', 'سلوك المستخدمين')

@section('content')

<div class="max-w-7xl mx-auto">
    <!-- Behavior Header -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">سلوك المستخدمين</h1>
                <p class="text-gray-600">تحليل وفهم سلوك المستخدمين على المنصة</p>
            </div>
            <div class="flex items-center space-x-2 space-x-reverse">
                <button class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                    <i class="fas fa-brain ml-2"></i>
                    تحليل ذكي
                </button>
                <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-chart-line ml-2"></i>
                    تقارير
                </button>
            </div>
        </div>
    </div>

    <!-- Behavior Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">متوسط الجلسة</p>
                    <h3 class="text-2xl font-bold text-gray-800">8:45</h3>
                    <p class="text-xs text-green-600">+2:30 هذا الأسبوع</p>
                </div>
                <div class="bg-blue-100 text-blue-600 p-3 rounded-full">
                    <i class="fas fa-clock text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">معدل الارتداد</p>
                    <h3 class="text-2xl font-bold text-gray-800">32%</h3>
                    <p class="text-xs text-green-600">-5% هذا الشهر</p>
                </div>
                <div class="bg-green-100 text-green-600 p-3 rounded-full">
                    <i class="fas fa-sign-out-alt text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">الصفحات/جلسة</p>
                    <h3 class="text-2xl font-bold text-gray-800">4.2</h3>
                    <p class="text-xs text-green-600">+0.8 هذا الأسبوع</p>
                </div>
                <div class="bg-purple-100 text-purple-600 p-3 rounded-full">
                    <i class="fas fa-file-alt text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-orange-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">معدل التحويل</p>
                    <h3 class="text-2xl font-bold text-gray-800">3.8%</h3>
                    <p class="text-xs text-red-600">-0.2% هذا الشهر</p>
                </div>
                <div class="bg-orange-100 text-orange-600 p-3 rounded-full">
                    <i class="fas fa-percentage text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Behavior Analysis Sections -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- User Journey -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">مسارات المستخدمين</h2>
            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                    <div class="flex items-center">
                        <div class="bg-blue-100 text-blue-600 p-2 rounded-full ml-3">
                            <i class="fas fa-home text-sm"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">الرئيسية → البحث → تفاصيل</p>
                            <p class="text-sm text-gray-500">المسار الأكثر شيوعاً</p>
                        </div>
                    </div>
                    <div class="text-left">
                        <span class="text-sm font-medium text-blue-600">45%</span>
                    </div>
                </div>

                <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                    <div class="flex items-center">
                        <div class="bg-green-100 text-green-600 p-2 rounded-full ml-3">
                            <i class="fas fa-search text-sm"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">البحث المباشر → التصفية</p>
                            <p class="text-sm text-gray-500">مستخدمون نشطون</p>
                        </div>
                    </div>
                    <div class="text-left">
                        <span class="text-sm font-medium text-green-600">28%</span>
                    </div>
                </div>

                <div class="flex items-center justify-between p-3 bg-purple-50 rounded-lg">
                    <div class="flex items-center">
                        <div class="bg-purple-100 text-purple-600 p-2 rounded-full ml-3">
                            <i class="fas fa-heart text-sm"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">المفضلة → التواصل</p>
                            <p class="text-sm text-gray-500">مستخدمون مهتمون</p>
                        </div>
                    </div>
                    <div class="text-left">
                        <span class="text-sm font-medium text-purple-600">18%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Engagement Patterns -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">أنماط التفاعل</h2>
            <div class="space-y-4">
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm text-gray-600">التفاعل مع الصور</span>
                        <span class="text-sm font-medium">78%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: 78%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm text-gray-600">قراءة الوصف</span>
                        <span class="text-sm font-medium">65%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-600 h-2 rounded-full transition-all duration-300" style="width: 65%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm text-gray-600">مشاهدة الخريطة</span>
                        <span class="text-sm font-medium">52%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-purple-600 h-2 rounded-full transition-all duration-300" style="width: 52%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm text-gray-600">التواصل المباشر</span>
                        <span class="text-sm font-medium">34%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-orange-600 h-2 rounded-full transition-all duration-300" style="width: 34%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">إجراءات سريعة</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <a href="{{ route('analytics.behavior.funnels') }}" class="flex flex-col items-center p-4 border rounded-lg hover:bg-gray-50 transition-colors">
                <i class="fas fa-filter text-blue-600 mb-2 text-2xl"></i>
                <span class="text-sm font-medium">تحليل المسارات</span>
            </a>
            <a href="{{ route('analytics.behavior.retention') }}" class="flex flex-col items-center p-4 border rounded-lg hover:bg-gray-50 transition-colors">
                <i class="fas fa-user-check text-green-600 mb-2 text-2xl"></i>
                <span class="text-sm font-medium">الاحتفاظ</span>
            </a>
            <a href="{{ route('analytics.behavior.engagement') }}" class="flex flex-col items-center p-4 border rounded-lg hover:bg-gray-50 transition-colors">
                <i class="fas fa-heart text-purple-600 mb-2 text-2xl"></i>
                <span class="text-sm font-medium">التفاعل</span>
            </a>
            <a href="{{ route('analytics.behavior.journeys') }}" class="flex flex-col items-center p-4 border rounded-lg hover:bg-gray-50 transition-colors">
                <i class="fas fa-route text-orange-600 mb-2 text-2xl"></i>
                <span class="text-sm font-medium">الرحلات</span>
            </a>
        </div>
    </div>
</div>

@endsection

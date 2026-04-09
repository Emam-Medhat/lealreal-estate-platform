@extends('layouts.dashboard')

@section('title', 'التحليل التنبؤي')

@section('content')

<div class="max-w-7xl mx-auto">
    <!-- Predictive Header -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">التحليل التنبؤي</h1>
                <p class="text-gray-600">نماذج ذكية للتنبؤ بالاتجاهات والسلوك المستقبلي</p>
            </div>
            <div class="flex items-center space-x-2 space-x-reverse">
                <div class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-sm font-medium flex items-center">
                    <i class="fas fa-brain ml-2"></i>
                    AI Powered
                </div>
                <button class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-plus ml-2"></i>
                    نموذج جديد
                </button>
            </div>
        </div>
    </div>

    <!-- Model Performance -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">دقة النماذج</p>
                    <h3 class="text-2xl font-bold text-gray-800">94.2%</h3>
                    <p class="text-xs text-green-600">+2.1% هذا الشهر</p>
                </div>
                <div class="bg-blue-100 text-blue-600 p-3 rounded-full">
                    <i class="fas fa-bullseye text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">التنبؤات اليوم</p>
                    <h3 class="text-2xl font-bold text-gray-800">1,847</h3>
                    <p class="text-xs text-green-600">+324 أمس</p>
                </div>
                <div class="bg-green-100 text-green-600 p-3 rounded-full">
                    <i class="fas fa-chart-line text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">النماذج النشطة</p>
                    <h3 class="text-2xl font-bold text-gray-800">12</h3>
                    <p class="text-xs text-green-600">2 قيد التدريب</p>
                </div>
                <div class="bg-purple-100 text-purple-600 p-3 rounded-full">
                    <i class="fas fa-robot text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-orange-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">وقت الاستجابة</p>
                    <h3 class="text-2xl font-bold text-gray-800">0.8s</h3>
                    <p class="text-xs text-green-600">أسرع بـ 15%</p>
                </div>
                <div class="bg-orange-100 text-orange-600 p-3 rounded-full">
                    <i class="fas fa-tachometer-alt text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Models -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">النماذج النشطة</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="border rounded-lg p-4 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center">
                        <div class="bg-blue-100 text-blue-600 p-2 rounded-full ml-3">
                            <i class="fas fa-home text-sm"></i>
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-900">تنبؤ أسعار العقارات</h3>
                            <p class="text-sm text-gray-500">الأسعار المستقبلية</p>
                        </div>
                    </div>
                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                        نشط
                    </span>
                </div>
                <div class="space-y-2">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">الدقة</span>
                        <span class="text-sm font-medium">96.5%</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">آخر تحديث</span>
                        <span class="text-sm font-medium">منذ ساعتين</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">التنبؤات</span>
                        <span class="text-sm font-medium">284 اليوم</span>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t flex justify-between">
                    <button class="text-blue-600 hover:text-blue-800 text-sm">عرض</button>
                    <button class="text-green-600 hover:text-green-800 text-sm">تنبؤ</button>
                    <button class="text-purple-600 hover:text-purple-800 text-sm">تدريب</button>
                </div>
            </div>

            <div class="border rounded-lg p-4 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center">
                        <div class="bg-green-100 text-green-600 p-2 rounded-full ml-3">
                            <i class="fas fa-users text-sm"></i>
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-900">سلوك المستخدمين</h3>
                            <p class="text-sm text-gray-500">التحويلات المتوقعة</p>
                        </div>
                    </div>
                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                        نشط
                    </span>
                </div>
                <div class="space-y-2">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">الدقة</span>
                        <span class="text-sm font-medium">92.3%</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">آخر تحديث</span>
                        <span class="text-sm font-medium">منذ 5 ساعات</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">التنبؤات</span>
                        <span class="text-sm font-medium">156 اليوم</span>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t flex justify-between">
                    <button class="text-blue-600 hover:text-blue-800 text-sm">عرض</button>
                    <button class="text-green-600 hover:text-green-800 text-sm">تنبؤ</button>
                    <button class="text-purple-600 hover:text-purple-800 text-sm">تدريب</button>
                </div>
            </div>

            <div class="border rounded-lg p-4 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center">
                        <div class="bg-purple-100 text-purple-600 p-2 rounded-full ml-3">
                            <i class="fas fa-chart-line text-sm"></i>
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-900">اتجاهات السوق</h3>
                            <p class="text-sm text-gray-500">توقعات السوق</p>
                        </div>
                    </div>
                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">
                        قيد التدريب
                    </span>
                </div>
                <div class="space-y-2">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">الدقة</span>
                        <span class="text-sm font-medium">88.7%</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">آخر تحديث</span>
                        <span class="text-sm font-medium">منذ يوم</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">التقدم</span>
                        <span class="text-sm font-medium">67%</span>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t flex justify-between">
                    <button class="text-blue-600 hover:text-blue-800 text-sm">عرض</button>
                    <button class="text-gray-400 text-sm" disabled>تنبؤ</button>
                    <button class="text-purple-600 hover:text-purple-800 text-sm">تدريب</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Predictions -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">التنبؤات الحديثة</h2>
        <div class="space-y-4">
            <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg">
                <div class="flex items-center">
                    <div class="bg-blue-100 text-blue-600 p-2 rounded-full ml-3">
                        <i class="fas fa-home text-sm"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">زيادة أسعار الشقق في الرياض</p>
                        <p class="text-sm text-gray-500">متوقع زيادة 8.5% خلال 3 أشهر</p>
                    </div>
                </div>
                <div class="text-left">
                    <span class="text-xs text-gray-400">منذ ساعة</span>
                    <span class="text-xs text-blue-600 block">96.5% ثقة</span>
                </div>
            </div>

            <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg">
                <div class="flex items-center">
                    <div class="bg-green-100 text-green-600 p-2 rounded-full ml-3">
                        <i class="fas fa-users text-sm"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">زيادة الطلب على الفلل</p>
                        <p class="text-sm text-gray-500">متوقع زيادة 15% في الربع القادم</p>
                    </div>
                </div>
                <div class="text-left">
                    <span class="text-xs text-gray-400">منذ 3 ساعات</span>
                    <span class="text-xs text-green-600 block">92.3% ثقة</span>
                </div>
            </div>

            <div class="flex items-center justify-between p-4 bg-purple-50 rounded-lg">
                <div class="flex items-center">
                    <div class="bg-purple-100 text-purple-600 p-2 rounded-full ml-3">
                        <i class="fas fa-chart-line text-sm"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">استقرار سوق العقارات</p>
                        <p class="text-sm text-gray-500">متوقع استقرار خلال 6 أشهر</p>
                    </div>
                </div>
                <div class="text-left">
                    <span class="text-xs text-gray-400">منذ 6 ساعات</span>
                    <span class="text-xs text-purple-600 block">89.1% ثقة</span>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

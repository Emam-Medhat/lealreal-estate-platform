@extends('layouts.dashboard')

@section('title', 'Big Data Dashboard')

@section('content')

<div class="max-w-7xl mx-auto">
    <!-- Big Data Header -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">لوحة البيانات الضخمة</h1>
                <p class="text-gray-600">معالجة وتحليل البيانات الضخمة</p>
            </div>
            <div class="flex items-center space-x-2 space-x-reverse">
                <div class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium flex items-center">
                    <span class="w-2 h-2 bg-green-500 rounded-full ml-2 animate-pulse"></span>
                    نشط
                </div>
            </div>
        </div>
    </div>

    <!-- Data Processing Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">إجمالي البيانات</p>
                    <h3 class="text-2xl font-bold text-gray-800">15.2 TB</h3>
                    <p class="text-xs text-green-600">+2.3 TB هذا الشهر</p>
                </div>
                <div class="bg-blue-100 text-blue-600 p-3 rounded-full">
                    <i class="fas fa-database text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">معالجة اليوم</p>
                    <h3 class="text-2xl font-bold text-gray-800">847 GB</h3>
                    <p class="text-xs text-green-600">95% مكتمل</p>
                </div>
                <div class="bg-green-100 text-green-600 p-3 rounded-full">
                    <i class="fas fa-cogs text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">جودة البيانات</p>
                    <h3 class="text-2xl font-bold text-gray-800">98.5%</h3>
                    <p class="text-xs text-green-600">+0.5% هذا الأسبوع</p>
                </div>
                <div class="bg-purple-100 text-purple-600 p-3 rounded-full">
                    <i class="fas fa-chart-line text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-orange-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">سرعة المعالجة</p>
                    <h3 class="text-2xl font-bold text-gray-800">1.2 GB/s</h3>
                    <p class="text-xs text-green-600">أسرع بـ 15%</p>
                </div>
                <div class="bg-orange-100 text-orange-600 p-3 rounded-full">
                    <i class="fas fa-tachometer-alt text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <a href="{{ route('analytics.bigdata.ingestion') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow text-center">
            <div class="bg-blue-100 text-blue-600 p-4 rounded-full inline-block mb-4">
                <i class="fas fa-download text-2xl"></i>
            </div>
            <h3 class="font-semibold text-gray-800 mb-2">استيراد البيانات</h3>
            <p class="text-sm text-gray-600">استيراد البيانات من مصادر مختلفة</p>
        </a>

        <a href="{{ route('analytics.bigdata.quality') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow text-center">
            <div class="bg-green-100 text-green-600 p-4 rounded-full inline-block mb-4">
                <i class="fas fa-check-circle text-2xl"></i>
            </div>
            <h3 class="font-semibold text-gray-800 mb-2">جودة البيانات</h3>
            <p class="text-sm text-gray-600">فحص وتحسين جودة البيانات</p>
        </a>

        <a href="{{ route('analytics.bigdata.transformation') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow text-center">
            <div class="bg-purple-100 text-purple-600 p-4 rounded-full inline-block mb-4">
                <i class="fas fa-exchange-alt text-2xl"></i>
            </div>
            <h3 class="font-semibold text-gray-800 mb-2">تحويل البيانات</h3>
            <p class="text-sm text-gray-600">تحويل البيانات للصيغة المطلوبة</p>
        </a>

        <a href="{{ route('analytics.bigdata.mining') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow text-center">
            <div class="bg-orange-100 text-orange-600 p-4 rounded-full inline-block mb-4">
                <i class="fas fa-gem text-2xl"></i>
            </div>
            <h3 class="font-semibold text-gray-800 mb-2">تنقيب البيانات</h3>
            <p class="text-sm text-gray-600">استخراج الأنماط والرؤى</p>
        </a>
    </div>

    <!-- Recent Processing Jobs -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">وظائف المعالجة الحديثة</h2>
        <div class="space-y-4">
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                <div class="flex items-center">
                    <div class="bg-green-100 text-green-600 p-2 rounded-full ml-3">
                        <i class="fas fa-check text-sm"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">معالجة بيانات المستخدمين</p>
                        <p class="text-sm text-gray-500">اكتملت بنجاح</p>
                    </div>
                </div>
                <div class="text-left">
                    <span class="text-xs text-gray-400">منذ 5 دقائق</span>
                    <span class="text-xs text-green-600 block">2.3 GB</span>
                </div>
            </div>

            <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg">
                <div class="flex items-center">
                    <div class="bg-blue-100 text-blue-600 p-2 rounded-full ml-3 animate-spin">
                        <i class="fas fa-spinner text-sm"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">تحليل بيانات العقارات</p>
                        <p class="text-sm text-gray-500">جاري المعالجة...</p>
                    </div>
                </div>
                <div class="text-left">
                    <span class="text-xs text-gray-400">بدأ منذ 15 دقيقة</span>
                    <span class="text-xs text-blue-600 block">67% مكتمل</span>
                </div>
            </div>

            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                <div class="flex items-center">
                    <div class="bg-gray-100 text-gray-600 p-2 rounded-full ml-3">
                        <i class="fas fa-clock text-sm"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">تجميع البيانات الشهرية</p>
                        <p class="text-sm text-gray-500">في قائمة الانتظار</p>
                    </div>
                </div>
                <div class="text-left">
                    <span class="text-xs text-gray-400">سيبدأ بعد 30 دقيقة</span>
                    <span class="text-xs text-gray-600 block">5.7 GB</span>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

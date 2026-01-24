@extends('layouts.dashboard')

@section('title', 'التقارير')

@section('content')

<div class="max-w-7xl mx-auto">
    <!-- Reports Header -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">التقارير</h1>
                <p class="text-gray-600">تقارير مفصلة عن أداء المنصة</p>
            </div>
            <div class="flex items-center space-x-2 space-x-reverse">
                <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-plus ml-2"></i>
                    تقرير جديد
                </button>
                <button class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-download ml-2"></i>
                    تصدير
                </button>
            </div>
        </div>
    </div>

    <!-- Report Categories -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow cursor-pointer border-t-4 border-blue-500">
            <div class="text-center">
                <div class="bg-blue-100 text-blue-600 p-4 rounded-full inline-block mb-4">
                    <i class="fas fa-chart-bar text-2xl"></i>
                </div>
                <h3 class="font-semibold text-gray-800 mb-2">تقارير المستخدمين</h3>
                <p class="text-sm text-gray-600 mb-4">تحليلات شاملة عن المستخدمين</p>
                <div class="text-blue-600 font-medium">
                    {{ $userReportsCount ?? 0 }} تقرير
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow cursor-pointer border-t-4 border-green-500">
            <div class="text-center">
                <div class="bg-green-100 text-green-600 p-4 rounded-full inline-block mb-4">
                    <i class="fas fa-building text-2xl"></i>
                </div>
                <h3 class="font-semibold text-gray-800 mb-2">تقارير العقارات</h3>
                <p class="text-sm text-gray-600 mb-4">إحصائيات العقارات والمعروض</p>
                <div class="text-green-600 font-medium">
                    {{ $propertyReportsCount ?? 0 }} تقرير
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow cursor-pointer border-t-4 border-purple-500">
            <div class="text-center">
                <div class="bg-purple-100 text-purple-600 p-4 rounded-full inline-block mb-4">
                    <i class="fas fa-dollar-sign text-2xl"></i>
                </div>
                <h3 class="font-semibold text-gray-800 mb-2">التقارير المالية</h3>
                <p class="text-sm text-gray-600 mb-4">الإيرادات والمصروفات</p>
                <div class="text-purple-600 font-medium">
                    {{ $financialReportsCount ?? 0 }} تقرير
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow cursor-pointer border-t-4 border-orange-500">
            <div class="text-center">
                <div class="bg-orange-100 text-orange-600 p-4 rounded-full inline-block mb-4">
                    <i class="fas fa-chart-line text-2xl"></i>
                </div>
                <h3 class="font-semibold text-gray-800 mb-2">تقارير الأداء</h3>
                <p class="text-sm text-gray-600 mb-4">مؤشرات الأداء الرئيسية</p>
                <div class="text-orange-600 font-medium">
                    {{ $performanceReportsCount ?? 0 }} تقرير
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Reports -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-semibold text-gray-800 border-b pb-2">التقارير الحديثة</h2>
            <div class="flex items-center space-x-2 space-x-reverse">
                <select class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option>الكل</option>
                    <option>المستخدمون</option>
                    <option>العقارات</option>
                    <option>المالية</option>
                    <option>الأداء</option>
                </select>
                <select class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option>آخر 7 أيام</option>
                    <option>آخر 30 يوم</option>
                    <option>آخر 3 أشهر</option>
                    <option>آخر سنة</option>
                </select>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            اسم التقرير
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            النوع
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            التاريخ
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            الحجم
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            الحالة
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            الإجراءات
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @if(isset($recentReports) && $recentReports->count() > 0)
                        @foreach($recentReports as $report)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $report->name }}</div>
                                <div class="text-sm text-gray-500">{{ $report->description }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $report->type }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $report->created_at->format('Y-m-d') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $report->size ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    مكتمل
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button class="text-blue-600 hover:text-blue-900 ml-3">عرض</button>
                                <button class="text-green-600 hover:text-green-900 ml-3">تحميل</button>
                                <button class="text-red-600 hover:text-red-900">حذف</button>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-file-alt text-4xl mb-4"></i>
                                <p>لا توجد تقارير حالياً</p>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

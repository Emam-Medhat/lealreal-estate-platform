@extends('admin.layouts.admin')

@section('title', 'توقعات الأسعار')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">توقعات الأسعار</h1>
            <p class="text-gray-600 mt-2">تحليل وتوقعات أسعار العقارات باستخدام الذكاء الاصطناعي</p>
        </div>
        <div class="flex space-x-3 space-x-reverse">
            <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-plus ml-2"></i>
                توقع جديد
            </button>
            <button class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                <i class="fas fa-download ml-2"></i>
                تصدير البيانات
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">نوع العقار</label>
                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">الكل</option>
                    <option value="apartment">شقة</option>
                    <option value="villa">فيلا</option>
                    <option value="land">أرض</option>
                    <option value="commercial">تجاري</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">المنطقة</label>
                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">الكل</option>
                    <option value="riyadh">الرياض</option>
                    <option value="jeddah">جدة</option>
                    <option value="dammam">الدمام</option>
                    <option value="mecca">مكة</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">نطاق السعر</label>
                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">الكل</option>
                    <option value="0-500000">أقل من 500 ألف</option>
                    <option value="500000-1000000">500 ألف - 1 مليون</option>
                    <option value="1000000-2000000">1 - 2 مليون</option>
                    <option value="2000000+">أكثر من 2 مليون</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">مستوى الثقة</label>
                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">الكل</option>
                    <option value="high">عالي (90%+)</option>
                    <option value="medium">متوسط (70-90%)</option>
                    <option value="low">منخفض (أقل من 70%)</option>
                </select>
            </div>
        </div>
        <div class="mt-4 flex justify-end">
            <button class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors ml-3">
                <i class="fas fa-redo ml-2"></i>
                إعادة تعيين
            </button>
            <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-search ml-2"></i>
                تطبيق الفلاتر
            </button>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">إجمالي التوقعات</p>
                    <p class="text-2xl font-bold text-gray-900">1,234</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-lg">
                    <i class="fas fa-chart-line text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">متوسط الدقة</p>
                    <p class="text-2xl font-bold text-gray-900">94.5%</p>
                </div>
                <div class="bg-green-100 p-3 rounded-lg">
                    <i class="fas fa-bullseye text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">التوقعات اليوم</p>
                    <p class="text-2xl font-bold text-gray-900">45</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-lg">
                    <i class="fas fa-calendar-day text-purple-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">التحديث الأخير</p>
                    <p class="text-2xl font-bold text-gray-900">2 دقيقة</p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-lg">
                    <i class="fas fa-clock text-yellow-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Predictions Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">آخر التوقعات</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">العقار</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">السعر الحالي</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">السعر المتوقع</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">نسبة التغيير</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الثقة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @for($i = 1; $i <= 10; $i++)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="bg-gray-100 p-2 rounded-lg ml-3">
                                    <i class="fas fa-home text-gray-600"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">عقار #{{ 1000 + $i }}</p>
                                    <p class="text-sm text-gray-600">شقة - الرياض</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <p class="font-medium text-gray-900">{{ number_format(rand(800000, 2000000), 0) }} ريال</p>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <p class="font-medium text-blue-600">{{ number_format(rand(850000, 2100000), 0) }} ريال</p>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                <i class="fas fa-arrow-up ml-1"></i>{{ rand(2, 15) }}%
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="bg-gray-200 rounded-full h-2 w-20 ml-2">
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ rand(75, 98) }}%"></div>
                                </div>
                                <span class="text-sm text-gray-600">{{ rand(75, 98) }}%</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                نشط
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-left">
                            <button class="text-blue-600 hover:text-blue-900 ml-3">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="text-green-600 hover:text-green-900 ml-3">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="text-red-600 hover:text-red-900">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @endfor
                </tbody>
            </table>
        </div>
        <div class="p-6 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <p class="text-sm text-gray-700">
                    عرض <span class="font-medium">1</span> إلى <span class="font-medium">10</span> من <span class="font-medium">97</span> نتيجة
                </p>
                <div class="flex space-x-2 space-x-reverse">
                    <button class="px-3 py-1 border border-gray-300 rounded-md hover:bg-gray-50">السابق</button>
                    <button class="px-3 py-1 bg-blue-600 text-white rounded-md">1</button>
                    <button class="px-3 py-1 border border-gray-300 rounded-md hover:bg-gray-50">2</button>
                    <button class="px-3 py-1 border border-gray-300 rounded-md hover:bg-gray-50">3</button>
                    <button class="px-3 py-1 border border-gray-300 rounded-md hover:bg-gray-50">التالي</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

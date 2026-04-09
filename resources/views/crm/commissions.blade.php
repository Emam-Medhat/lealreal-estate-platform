@extends('admin.layouts.admin')

@section('title', 'العمولات')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">العمولات</h1>
            <p class="text-gray-600 mt-2">إدارة عمولات الوكلاء والمستحقات المالية</p>
        </div>
        <div class="flex space-x-3 space-x-reverse">
            <button class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors">
                <i class="fas fa-calculator ml-2"></i>
                حساب عمولة
            </button>
            <button class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                <i class="fas fa-money-check ml-2"></i>
                دفع مستحقات
            </button>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">العمولات المدفوعة</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format(285000, 0) }} ريال</p>
                </div>
                <div class="bg-green-100 p-3 rounded-lg">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-green-600"><i class="fas fa-arrow-up ml-1"></i>18%</span>
                <span class="text-gray-500 mr-2">من الشهر الماضي</span>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">المستحقات</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format(125000, 0) }} ريال</p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-lg">
                    <i class="fas fa-clock text-yellow-600"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-yellow-600"><i class="fas fa-hourglass-half ml-1"></i>قيد الانتظار</span>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">تحت المراجعة</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format(45000, 0) }} ريال</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-lg">
                    <i class="fas fa-eye text-blue-600"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-blue-600"><i class="fas fa-search ml-1"></i>قيد المراجعة</span>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">متوسط العمولة</p>
                    <p class="text-2xl font-bold text-gray-900">2.5%</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-lg">
                    <i class="fas fa-percentage text-purple-600"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-green-600"><i class="fas fa-arrow-up ml-1"></i>0.3%</span>
                <span class="text-gray-500 mr-2">زيادة هذا الشهر</span>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الحالة</label>
                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                    <option value="">الكل</option>
                    <option value="pending">قيد الانتظار</option>
                    <option value="approved">معتمد</option>
                    <option value="paid">مدفوع</option>
                    <option value="rejected">مرفوض</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الوكيل</label>
                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                    <option value="">الكل</option>
                    <option value="1">أحمد محمد</option>
                    <option value="2">فاطمة علي</option>
                    <option value="3">محمد سعيد</option>
                    <option value="4">سارة أحمد</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الشهر</label>
                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                    <option value="">الكل</option>
                    <option value="1">يناير</option>
                    <option value="2">فبراير</option>
                    <option value="3">مارس</option>
                    <option value="4">أبريل</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">نوع العمولة</label>
                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                    <option value="">الكل</option>
                    <option value="sale">بيع</option>
                    <option value="rent">إيجار</option>
                    <option value="referral">إحالة</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Commissions Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">العمولات</h3>
                <div class="flex space-x-2 space-x-reverse">
                    <button class="px-3 py-1 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">
                        <i class="fas fa-filter ml-1"></i>فلترة
                    </button>
                    <button class="px-3 py-1 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">
                        <i class="fas fa-sort ml-1"></i>ترتيب
                    </button>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <input type="checkbox" class="rounded border-gray-300">
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">رقم العمولة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الوكيل</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">العقار</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">قيمة الصفقة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">نسبة العمولة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">مبلغ العمولة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">التاريخ</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @for($i = 1; $i <= 10; $i++)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="checkbox" class="rounded border-gray-300">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-medium text-gray-900">#COM-{{ 1000 + $i }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="bg-gray-200 rounded-full w-8 h-8 ml-2 flex items-center justify-center">
                                    <i class="fas fa-user-tie text-gray-600 text-xs"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ ['أحمد محمد', 'فاطمة علي', 'محمد سعيد', 'سارة أحمد'][$i % 4] }}</p>
                                    <p class="text-sm text-gray-600">الوكيل #{{ $i }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="bg-green-100 p-2 rounded-lg ml-3">
                                    <i class="fas fa-home text-green-600"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">عقار #{{ 3000 + $i }}</p>
                                    <p class="text-sm text-gray-600">{{ ['شقة', 'فيلا', 'دوبلكس'][$i % 3] }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <p class="font-medium text-gray-900">{{ number_format(rand(500000, 5000000), 0) }} ريال</p>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-medium text-gray-900">{{ rand(2, 5) }}.{{ rand(0, 9) }}%</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <p class="font-medium text-yellow-600">{{ number_format(rand(10000, 100000), 0) }} ريال</p>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full 
                                @if($i <= 3) bg-green-100 text-green-800
                                @elseif($i <= 6) bg-yellow-100 text-yellow-800
                                @elseif($i <= 8) bg-blue-100 text-blue-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ['مدفوع', 'قيد الانتظار', 'تحت المراجعة', 'معتمد'][$i <= 3 ? 0 : ($i <= 6 ? 1 : ($i <= 8 ? 2 : 3))] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <p class="text-sm text-gray-900">{{ now()->subDays(rand(1, 30))->format('Y-m-d') }}</p>
                            <p class="text-xs text-gray-600">{{ now()->subDays(rand(1, 30))->format('H:i') }}</p>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-left">
                            <button class="text-green-600 hover:text-green-900 ml-3" title="دفع">
                                <i class="fas fa-money-check"></i>
                            </button>
                            <button class="text-blue-600 hover:text-blue-900 ml-3" title="عرض">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="text-yellow-600 hover:text-yellow-900 ml-3" title="تعديل">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="text-red-600 hover:text-red-900" title="حذف">
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
                    عرض <span class="font-medium">1</span> إلى <span class="font-medium">10</span> من <span class="font-medium">87</span> عمولة
                </p>
                <div class="flex space-x-2 space-x-reverse">
                    <button class="px-3 py-1 border border-gray-300 rounded-md hover:bg-gray-50">السابق</button>
                    <button class="px-3 py-1 bg-yellow-600 text-white rounded-md">1</button>
                    <button class="px-3 py-1 border border-gray-300 rounded-md hover:bg-gray-50">2</button>
                    <button class="px-3 py-1 border border-gray-300 rounded-md hover:bg-gray-50">3</button>
                    <button class="px-3 py-1 border border-gray-300 rounded-md hover:bg-gray-50">التالي</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Commission Analytics -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-8">
        <!-- Top Performers -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">أفضل الوكلاء</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @for($i = 1; $i <= 5; $i++)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="bg-yellow-100 p-2 rounded-lg ml-3">
                                <span class="text-yellow-600 font-bold">{{ $i }}</span>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ ['أحمد محمد', 'فاطمة علي', 'محمد سعيد', 'سارة أحمد', 'خالد العتيبي'][$i-1] }}</p>
                                <p class="text-sm text-gray-600">{{ rand(5, 15) }} صفقات</p>
                            </div>
                        </div>
                        <div class="text-left">
                            <p class="font-semibold text-gray-900">{{ number_format(rand(50000, 150000), 0) }} ريال</p>
                            <p class="text-sm text-green-600">
                                <i class="fas fa-arrow-up ml-1"></i>{{ rand(5, 25) }}%
                            </p>
                        </div>
                    </div>
                    @endfor
                </div>
            </div>
        </div>

        <!-- Monthly Trend -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">الاتجاه الشهري</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach(['يناير', 'فبراير', 'مارس', 'أبريل'] as $month)
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm text-gray-600">{{ $month }}</span>
                            <span class="text-sm font-medium text-gray-900">{{ number_format(rand(60000, 120000), 0) }} ريال</span>
                        </div>
                        <div class="bg-gray-200 rounded-full h-2">
                            <div class="bg-yellow-600 h-2 rounded-full" style="width: {{ rand(60, 100) }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="mt-6 p-4 bg-yellow-50 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-yellow-800">إجمالي هذا الربع</p>
                            <p class="text-lg font-bold text-yellow-900">{{ number_format(850000, 0) }} ريال</p>
                        </div>
                        <div class="text-yellow-600">
                            <i class="fas fa-chart-line text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mt-8">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">إجراءات سريعة</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <button class="p-4 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition-colors text-right">
                    <i class="fas fa-calculator text-yellow-600 text-2xl mb-2"></i>
                    <p class="font-medium text-gray-900">حاسبة العمولات</p>
                    <p class="text-sm text-gray-600">حساب عمولة جديدة</p>
                </button>
                <button class="p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors text-right">
                    <i class="fas fa-money-check text-green-600 text-2xl mb-2"></i>
                    <p class="font-medium text-gray-900">دفع جماعي</p>
                    <p class="text-sm text-gray-600">دفع عمولات متعددة</p>
                </button>
                <button class="p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors text-right">
                    <i class="fas fa-file-export text-blue-600 text-2xl mb-2"></i>
                    <p class="font-medium text-gray-900">تقارير العمولات</p>
                    <p class="text-sm text-gray-600">تصدير تقارير مفصلة</p>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

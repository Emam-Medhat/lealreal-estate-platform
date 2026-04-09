@extends('admin.layouts.admin')

@section('title', 'نظام العروض')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">نظام العروض</h1>
            <p class="text-gray-600 mt-2">إدارة عروض العقارات والمفاوضات</p>
        </div>
        <div class="flex space-x-3 space-x-reverse">
            <button class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                <i class="fas fa-plus ml-2"></i>
                عرض جديد
            </button>
            <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-download ml-2"></i>
                تصدير
            </button>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">إجمالي العروض</p>
                    <p class="text-2xl font-bold text-gray-900">156</p>
                </div>
                <div class="bg-green-100 p-3 rounded-lg">
                    <i class="fas fa-handshake text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">العروض النشطة</p>
                    <p class="text-2xl font-bold text-gray-900">42</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-lg">
                    <i class="fas fa-clock text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">المقبولة</p>
                    <p class="text-2xl font-bold text-gray-900">89</p>
                </div>
                <div class="bg-green-100 p-3 rounded-lg">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">قيمة العروض</p>
                    <p class="text-2xl font-bold text-gray-900">45.2M</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-lg">
                    <i class="fas fa-coins text-purple-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الحالة</label>
                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    <option value="">الكل</option>
                    <option value="pending">قيد الانتظار</option>
                    <option value="accepted">مقبول</option>
                    <option value="rejected">مرفوض</option>
                    <option value="negotiating">قيد التفاوض</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">نوع العرض</label>
                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    <option value="">الكل</option>
                    <option value="purchase">شراء</option>
                    <option value="rent">إيجار</option>
                    <option value="investment">استثمار</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">نطاق السعر</label>
                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    <option value="">الكل</option>
                    <option value="0-500000">أقل من 500 ألف</option>
                    <option value="500000-1000000">500 ألف - 1 مليون</option>
                    <option value="1000000-2000000">1 - 2 مليون</option>
                    <option value="2000000+">أكثر من 2 مليون</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الوكيل</label>
                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    <option value="">الكل</option>
                    <option value="1">أحمد محمد</option>
                    <option value="2">فاطمة علي</option>
                    <option value="3">محمد سعيد</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Offers Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">العروض</h3>
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
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">رقم العرض</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">العقار</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المشتري</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المبلغ</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الوكيل</th>
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
                            <span class="font-medium text-gray-900">#OFF-{{ 1000 + $i }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="bg-green-100 p-2 rounded-lg ml-3">
                                    <i class="fas fa-home text-green-600"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">عقار #{{ 2000 + $i }}</p>
                                    <p class="text-sm text-gray-600">{{ ['شقة', 'فيلا', 'دوبلكس'][$i % 3] }} - {{ ['الرياض', 'جدة', 'الدمام'][$i % 3] }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="bg-gray-200 rounded-full w-8 h-8 ml-2 flex items-center justify-center">
                                    <i class="fas fa-user text-gray-600 text-xs"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ ['أحمد العتيبي', 'فاطمة القحطاني', 'محمد السعيد'][$i % 3] }}</p>
                                    <p class="text-sm text-gray-600">{{ $i }}{{ rand(10, 99) }}{{ rand(100, 999) }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <p class="font-medium text-gray-900">{{ number_format(rand(500000, 5000000), 0) }} ريال</p>
                            <p class="text-sm text-gray-600">{{ rand(5, 30) }}% دفعة أولى</p>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full 
                                @if($i <= 3) bg-yellow-100 text-yellow-800
                                @elseif($i <= 6) bg-green-100 text-green-800
                                @elseif($i <= 8) bg-blue-100 text-blue-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ ['قيد الانتظار', 'مقبول', 'قيد التفاوض', 'مرفوض'][$i <= 3 ? 0 : ($i <= 6 ? 1 : ($i <= 8 ? 2 : 3))] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="bg-gray-200 rounded-full w-6 h-6 ml-2 flex items-center justify-center">
                                    <i class="fas fa-user-tie text-gray-600 text-xs"></i>
                                </div>
                                <span class="text-sm text-gray-900">{{ ['أحمد محمد', 'فاطمة علي', 'محمد سعيد'][$i % 3] }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <p class="text-sm text-gray-900">{{ now()->subDays(rand(1, 30))->format('Y-m-d') }}</p>
                            <p class="text-xs text-gray-600">{{ now()->subDays(rand(1, 30))->format('H:i') }}</p>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-left">
                            <button class="text-green-600 hover:text-green-900 ml-3" title="قبول">
                                <i class="fas fa-check"></i>
                            </button>
                            <button class="text-blue-600 hover:text-blue-900 ml-3" title="تفاوض">
                                <i class="fas fa-comments"></i>
                            </button>
                            <button class="text-yellow-600 hover:text-yellow-900 ml-3" title="عرض">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="text-red-600 hover:text-red-900" title="رفض">
                                <i class="fas fa-times"></i>
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
                    عرض <span class="font-medium">1</span> إلى <span class="font-medium">10</span> من <span class="font-medium">156</span> عرض
                </p>
                <div class="flex space-x-2 space-x-reverse">
                    <button class="px-3 py-1 border border-gray-300 rounded-md hover:bg-gray-50">السابق</button>
                    <button class="px-3 py-1 bg-green-600 text-white rounded-md">1</button>
                    <button class="px-3 py-1 border border-gray-300 rounded-md hover:bg-gray-50">2</button>
                    <button class="px-3 py-1 border border-gray-300 rounded-md hover:bg-gray-50">3</button>
                    <button class="px-3 py-1 border border-gray-300 rounded-md hover:bg-gray-50">التالي</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Offer Analytics -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-8">
        <!-- Success Rate -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">معدل النجاح</h3>
            </div>
            <div class="p-6">
                <div class="text-center mb-6">
                    <div class="relative inline-flex items-center justify-center">
                        <div class="w-32 h-32">
                            <svg class="transform -rotate-90 w-32 h-32">
                                <circle cx="64" cy="64" r="56" stroke="#e5e7eb" stroke-width="8" fill="none"></circle>
                                <circle cx="64" cy="64" r="56" stroke="#10b981" stroke-width="8" fill="none" stroke-dasharray="351.86" stroke-dashoffset="70.37"></circle>
                            </svg>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <span class="text-2xl font-bold text-gray-900">80%</span>
                            </div>
                        </div>
                    </div>
                    <p class="text-sm text-gray-600 mt-2">معدل قبول العروض</p>
                </div>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">مقبول</span>
                        <span class="text-sm font-medium text-green-600">89 عرض</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">مرفوض</span>
                        <span class="text-sm font-medium text-red-600">15 عرض</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">قيد الانتظار</span>
                        <span class="text-sm font-medium text-yellow-600">42 عرض</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Average Offer Value -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">متوسط قيمة العروض</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm text-gray-600">شقق</span>
                            <span class="text-sm font-medium text-gray-900">1.2M ريال</span>
                        </div>
                        <div class="bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: 60%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm text-gray-600">فلل</span>
                            <span class="text-sm font-medium text-gray-900">3.5M ريال</span>
                        </div>
                        <div class="bg-gray-200 rounded-full h-2">
                            <div class="bg-green-600 h-2 rounded-full" style="width: 85%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm text-gray-600">أراضي</span>
                            <span class="text-sm font-medium text-gray-900">2.8M ريال</span>
                        </div>
                        <div class="bg-gray-200 rounded-full h-2">
                            <div class="bg-purple-600 h-2 rounded-full" style="width: 75%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm text-gray-600">تجاري</span>
                            <span class="text-sm font-medium text-gray-900">5.2M ريال</span>
                        </div>
                        <div class="bg-gray-200 rounded-full h-2">
                            <div class="bg-orange-600 h-2 rounded-full" style="width: 95%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('admin.layouts.admin')

@section('title', 'كشف الاحتيال')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">كشف الاحتيال</h1>
            <p class="text-gray-600 mt-2">نظام ذكاء اصطناعي متقدم لكشف الاحتيال في المعاملات العقارية</p>
        </div>
        <div class="flex space-x-3 space-x-reverse">
            <button class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">
                <i class="fas fa-exclamation-triangle ml-2"></i>
                الحالات العاجلة
            </button>
            <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-download ml-2"></i>
                تصدير تقرير
            </button>
        </div>
    </div>

    <!-- Alert Banner -->
    <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-8">
        <div class="flex items-center">
            <div class="bg-red-100 p-2 rounded-lg ml-3">
                <i class="fas fa-exclamation-triangle text-red-600"></i>
            </div>
            <div class="flex-1">
                <h3 class="text-red-800 font-semibold">تنبيه أمني</h3>
                <p class="text-red-600 text-sm">تم اكتشاف 3 حالات احتيال عالية الخطورة تتطلب تدخل فوري</p>
            </div>
            <button class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">
                عرض الحالات
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">الحالات النشطة</p>
                    <p class="text-2xl font-bold text-gray-900">47</p>
                </div>
                <div class="bg-red-100 p-3 rounded-lg">
                    <i class="fas fa-user-secret text-red-600"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-red-600"><i class="fas fa-arrow-up ml-1"></i>12%</span>
                <span class="text-gray-500 mr-2">من الأسبوع الماضي</span>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">عالية الخطورة</p>
                    <p class="text-2xl font-bold text-gray-900">8</p>
                </div>
                <div class="bg-orange-100 p-3 rounded-lg">
                    <i class="fas fa-fire text-orange-600"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-orange-600"><i class="fas fa-exclamation ml-1"></i>تحتاج مراجعة</span>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">تم حلها</p>
                    <p class="text-2xl font-bold text-gray-900">124</p>
                </div>
                <div class="bg-green-100 p-3 rounded-lg">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-green-600"><i class="fas fa-arrow-up ml-1"></i>85%</span>
                <span class="text-gray-500 mr-2">معدل الحل</span>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">دقة الكشف</p>
                    <p class="text-2xl font-bold text-gray-900">96.8%</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-lg">
                    <i class="fas fa-shield-alt text-blue-600"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-green-600"><i class="fas fa-arrow-up ml-1"></i>2.1%</span>
                <span class="text-gray-500 mr-2">تحسن هذا الشهر</span>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">مستوى الخطورة</label>
                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    <option value="">الكل</option>
                    <option value="critical">حرج</option>
                    <option value="high">عالي</option>
                    <option value="medium">متوسط</option>
                    <option value="low">منخفض</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الحالة</label>
                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    <option value="">الكل</option>
                    <option value="pending">قيد الانتظار</option>
                    <option value="investigating">قيد التحقيق</option>
                    <option value="resolved">تم الحل</option>
                    <option value="false_positive">إيجابي كاذب</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">نوع الاحتيال</label>
                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    <option value="">الكل</option>
                    <option value="identity">سرقة الهوية</option>
                    <option value="document">مستندات مزورة</option>
                    <option value="payment">دفع احتيالي</option>
                    <option value="listing">إعلان وهمي</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الفترة</label>
                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    <option value="">الكل</option>
                    <option value="today">اليوم</option>
                    <option value="week">آخر أسبوع</option>
                    <option value="month">آخر شهر</option>
                    <option value="quarter">آخر ربع سنة</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Fraud Cases Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">حالات الاحتيال</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المستخدم</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">نوع الاحتيال</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الخطورة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الثقة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">التاريخ</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @for($i = 1; $i <= 10; $i++)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full 
                                @if($i <= 2) bg-red-100 text-red-800
                                @elseif($i <= 5) bg-orange-100 text-orange-800
                                @else bg-yellow-100 text-yellow-800 @endif">
                                {{ $i <= 2 ? 'حرج' : ($i <= 5 ? 'عالي' : 'متوسط') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="bg-gray-100 p-2 rounded-lg ml-3">
                                    <i class="fas fa-user text-gray-600"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">مستخدم #{{ 1000 + $i }}</p>
                                    <p class="text-sm text-gray-600">user{{ $i }}@example.com</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <p class="text-sm text-gray-900">
                                {{ ['مستندات مزورة', 'سرقة هوية', 'دفع احتيالي', 'إعلان وهمي', 'تلاعب بالأسعار'][rand(0, 4)] }}
                            </p>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-2 h-2 rounded-full 
                                    @if($i <= 2) bg-red-500
                                    @elseif($i <= 5) bg-orange-500
                                    @else bg-yellow-500 @endif ml-2"></div>
                                <span class="text-sm text-gray-900">
                                    {{ $i <= 2 ? 'عالية' : ($i <= 5 ? 'متوسطة' : 'منخفضة') }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="bg-gray-200 rounded-full h-2 w-20 ml-2">
                                    <div class="bg-red-600 h-2 rounded-full" style="width: {{ rand(85, 98) }}%"></div>
                                </div>
                                <span class="text-sm text-gray-600">{{ rand(85, 98) }}%</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <p class="text-sm text-gray-900">{{ now()->subDays(rand(0, 7))->format('Y-m-d H:i') }}</p>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-left">
                            <button class="text-red-600 hover:text-red-900 ml-3" title="عرض التفاصيل">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="text-blue-600 hover:text-blue-900 ml-3" title="بدء التحقيق">
                                <i class="fas fa-search"></i>
                            </button>
                            <button class="text-green-600 hover:text-green-900 ml-3" title="حل الحالة">
                                <i class="fas fa-check"></i>
                            </button>
                            <button class="text-gray-600 hover:text-gray-900" title="تجاهل">
                                <i class="fas fa-times"></i>
                            </button>
                        </td>
                    </tr>
                    @endfor
                </tbody>
            </table>
        </div>
    </div>

    <!-- AI Detection Patterns -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mt-8">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">أنماط الكشف بالذكاء الاصطناعي</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                    <div class="bg-red-100 p-4 rounded-lg mb-3">
                        <i class="fas fa-fingerprint text-red-600 text-3xl"></i>
                    </div>
                    <h4 class="font-semibold text-gray-900">تحليل السلوك</h4>
                    <p class="text-sm text-gray-600 mt-2">مراقبة الأنماط غير الطبيعية في سلوك المستخدمين</p>
                    <div class="mt-3">
                        <div class="bg-gray-200 rounded-full h-2">
                            <div class="bg-red-600 h-2 rounded-full" style="width: 92%"></div>
                        </div>
                        <p class="text-xs text-gray-600 mt-1">دقة 92%</p>
                    </div>
                </div>

                <div class="text-center">
                    <div class="bg-blue-100 p-4 rounded-lg mb-3">
                        <i class="fas fa-file-alt text-blue-600 text-3xl"></i>
                    </div>
                    <h4 class="font-semibold text-gray-900">التحقق من المستندات</h4>
                    <p class="text-sm text-gray-600 mt-2">فحص المستندات للتأكد من صحتها وأصالتها</p>
                    <div class="mt-3">
                        <div class="bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: 88%"></div>
                        </div>
                        <p class="text-xs text-gray-600 mt-1">دقة 88%</p>
                    </div>
                </div>

                <div class="text-center">
                    <div class="bg-green-100 p-4 rounded-lg mb-3">
                        <i class="fas fa-network-wired text-green-600 text-3xl"></i>
                    </div>
                    <h4 class="font-semibold text-gray-900">تحليل الشبكات</h4>
                    <p class="text-sm text-gray-600 mt-2">كشف العلاقات المشبوهة بين المستخدمين</p>
                    <div class="mt-3">
                        <div class="bg-gray-200 rounded-full h-2">
                            <div class="bg-green-600 h-2 rounded-full" style="width: 95%"></div>
                        </div>
                        <p class="text-xs text-gray-600 mt-1">دقة 95%</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('admin.layouts.admin')

@section('title', 'الواقع الافتراضي')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">الواقع الافتراضي</h1>
            <p class="text-gray-600 mt-2">جولات افتراضية ثلاثية الأبعاد للعقارات</p>
        </div>
        <div class="flex space-x-3 space-x-reverse">
            <button class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                <i class="fas fa-plus ml-2"></i>
                جولة جديدة
            </button>
            <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-download ml-2"></i>
                تصدير تقرير
            </button>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">إجمالي الجولات</p>
                    <p class="text-2xl font-bold text-gray-900">234</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-lg">
                    <i class="fas fa-vr-cardboard text-purple-600"></i>
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
                    <p class="text-sm text-gray-600">المشاهدات</p>
                    <p class="text-2xl font-bold text-gray-900">15.2K</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-lg">
                    <i class="fas fa-eye text-blue-600"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-green-600"><i class="fas fa-arrow-up ml-1"></i>25%</span>
                <span class="text-gray-500 mr-2">زيادة هذا الشهر</span>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">متوسط التقييم</p>
                    <p class="text-2xl font-bold text-gray-900">4.7</p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-lg">
                    <i class="fas fa-star text-yellow-600"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-green-600"><i class="fas fa-arrow-up ml-1"></i>0.3</span>
                <span class="text-gray-500 mr-2">تحسن التقييم</span>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">المدة المتوسطة</p>
                    <p class="text-2xl font-bold text-gray-900">8:45</p>
                </div>
                <div class="bg-green-100 p-3 rounded-lg">
                    <i class="fas fa-clock text-green-600"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-green-600"><i class="fas fa-arrow-up ml-1"></i>1:30</span>
                <span class="text-gray-500 mr-2">زيادة المدة</span>
            </div>
        </div>
    </div>

    <!-- Featured Tours -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">الجولات المميزة</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @for($i = 1; $i <= 3; $i++)
                <div class="border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
                    <div class="relative">
                        <img src="https://via.placeholder.com/400x250" alt="Property Tour" class="w-full h-48 object-cover">
                        <div class="absolute top-2 right-2">
                            <span class="bg-purple-600 text-white px-2 py-1 text-xs rounded-full">
                                <i class="fas fa-star ml-1"></i>مميز
                            </span>
                        </div>
                        <div class="absolute bottom-2 left-2 bg-black bg-opacity-50 text-white px-2 py-1 text-xs rounded">
                            <i class="fas fa-play ml-1"></i>3D Tour
                        </div>
                    </div>
                    <div class="p-4">
                        <h4 class="font-semibold text-gray-900">فيلا فاخرة - {{ ['الرياض', 'جدة', 'الدمام'][$i-1] }}</h4>
                        <p class="text-sm text-gray-600 mt-1">{{ 4 + $i }} غرف • {{ 3 + $i }} حمامات • {{ 300 + ($i * 50) }}م²</p>
                        <div class="flex items-center justify-between mt-3">
                            <div class="flex items-center">
                                <div class="flex text-yellow-400">
                                    @for($j = 1; $j <= 5; $j++)
                                        <i class="fas fa-star {{ $j <= (4 + ($i % 2)) ? '' : 'text-gray-300' }} text-sm"></i>
                                    @endfor
                                </div>
                                <span class="text-sm text-gray-600 mr-2">{{ 4 + ($i % 2) }}.{{ rand(0, 9) }}</span>
                            </div>
                            <div class="text-sm text-gray-600">
                                <i class="fas fa-eye ml-1"></i>{{ rand(500, 2000) }}
                            </div>
                        </div>
                        <div class="flex space-x-2 space-x-reverse mt-3">
                            <button class="flex-1 bg-purple-600 text-white px-3 py-2 rounded-lg hover:bg-purple-700 transition-colors text-sm">
                                <i class="fas fa-play ml-1"></i>تشغيل
                            </button>
                            <button class="flex-1 bg-gray-100 text-gray-700 px-3 py-2 rounded-lg hover:bg-gray-200 transition-colors text-sm">
                                <i class="fas fa-edit ml-1"></i>تعديل
                            </button>
                        </div>
                    </div>
                </div>
                @endfor
            </div>
        </div>
    </div>

    <!-- Tours Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">جميع الجولات</h3>
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
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">العقار</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">النوع</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحجم</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المشاهدات</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">التقييم</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @for($i = 1; $i <= 10; $i++)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="bg-purple-100 p-2 rounded-lg ml-3">
                                    <i class="fas fa-home text-purple-600"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">عقار #{{ 2000 + $i }}</p>
                                    <p class="text-sm text-gray-600">{{ ['شقة', 'فيلا', 'دوبلكس', 'بنتهاوس', 'أرض'][$i % 5] }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full 
                                @if($i % 3 == 0) bg-purple-100 text-purple-800
                                @elseif($i % 3 == 1) bg-blue-100 text-blue-800
                                @else bg-green-100 text-green-800 @endif">
                                {{ ['3D Tour', '360°', 'Video Walkthrough'][$i % 3] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <p class="text-sm text-gray-900">{{ rand(50, 500) }}MB</p>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <i class="fas fa-eye text-gray-400 ml-2"></i>
                                <span class="text-sm text-gray-900">{{ rand(100, 5000) }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex text-yellow-400">
                                    @for($j = 1; $j <= 5; $j++)
                                        <i class="fas fa-star {{ $j <= (3 + rand(0, 2)) ? '' : 'text-gray-300' }} text-sm"></i>
                                    @endfor
                                </div>
                                <span class="text-sm text-gray-600 mr-2">{{ 3 + rand(0, 2) }}.{{ rand(0, 9) }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full 
                                @if($i <= 3) bg-green-100 text-green-800
                                @elseif($i <= 7) bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ['منشور', 'قيد المعالجة', 'مسودة'][$i <= 3 ? 0 : ($i <= 7 ? 1 : 2)] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-left">
                            <button class="text-purple-600 hover:text-purple-900 ml-3" title="تشغيل">
                                <i class="fas fa-play"></i>
                            </button>
                            <button class="text-blue-600 hover:text-blue-900 ml-3" title="تحرير">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="text-green-600 hover:text-green-900 ml-3" title="تحميل">
                                <i class="fas fa-download"></i>
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
    </div>

    <!-- VR Analytics -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-8">
        <!-- Engagement Chart -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">معدل المشاركة</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">متوسط مدة المشاهدة</span>
                        <span class="text-sm font-medium text-gray-900">8:45 دقيقة</span>
                    </div>
                    <div class="bg-gray-200 rounded-full h-2">
                        <div class="bg-purple-600 h-2 rounded-full" style="width: 75%"></div>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">نسبة الإكمال</span>
                        <span class="text-sm font-medium text-gray-900">68%</span>
                    </div>
                    <div class="bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: 68%"></div>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">التفاعل مع النقاط الساخنة</span>
                        <span class="text-sm font-medium text-gray-900">92%</span>
                    </div>
                    <div class="bg-gray-200 rounded-full h-2">
                        <div class="bg-green-600 h-2 rounded-full" style="width: 92%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Device Stats -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">إحصائيات الأجهزة</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fas fa-desktop text-gray-600 ml-3"></i>
                            <span class="text-sm text-gray-600">كمبيوتر</span>
                        </div>
                        <span class="text-sm font-medium text-gray-900">45%</span>
                    </div>
                    <div class="bg-gray-200 rounded-full h-2">
                        <div class="bg-gray-600 h-2 rounded-full" style="width: 45%"></div>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fas fa-mobile-alt text-gray-600 ml-3"></i>
                            <span class="text-sm text-gray-600">جوال</span>
                        </div>
                        <span class="text-sm font-medium text-gray-900">35%</span>
                    </div>
                    <div class="bg-gray-200 rounded-full h-2">
                        <div class="bg-green-600 h-2 rounded-full" style="width: 35%"></div>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fas fa-vr-cardboard text-gray-600 ml-3"></i>
                            <span class="text-sm text-gray-600">نظارات VR</span>
                        </div>
                        <span class="text-sm font-medium text-gray-900">20%</span>
                    </div>
                    <div class="bg-gray-200 rounded-full h-2">
                        <div class="bg-purple-600 h-2 rounded-full" style="width: 20%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

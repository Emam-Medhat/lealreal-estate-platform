@extends('admin.layouts.admin')

@section('title', 'شبكة المدونات')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">شبكة المدونات</h1>
            <p class="text-gray-600 mt-2">إدارة جميع المقالات والمحتوى</p>
        </div>
        <div class="flex space-x-3 space-x-reverse">
            <button class="bg-pink-600 text-white px-4 py-2 rounded-lg hover:bg-pink-700 transition-colors">
                <i class="fas fa-plus ml-2"></i>
                مقال جديد
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
                    <p class="text-sm text-gray-600">إجمالي المقالات</p>
                    <p class="text-2xl font-bold text-gray-900">342</p>
                </div>
                <div class="bg-pink-100 p-3 rounded-lg">
                    <i class="fas fa-blog text-pink-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">المنشورة</p>
                    <p class="text-2xl font-bold text-gray-900">287</p>
                </div>
                <div class="bg-green-100 p-3 rounded-lg">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">المسودات</p>
                    <p class="text-2xl font-bold text-gray-900">45</p>
                </div>
                <div class="bg-gray-100 p-3 rounded-lg">
                    <i class="fas fa-file-alt text-gray-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">تحت المراجعة</p>
                    <p class="text-2xl font-bold text-gray-900">10</p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-lg">
                    <i class="fas fa-clock text-yellow-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">البحث</label>
                <div class="relative">
                    <input type="text" placeholder="ابحث في المقالات..." class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الحالة</label>
                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                    <option value="">الكل</option>
                    <option value="published">منشور</option>
                    <option value="draft">مسودة</option>
                    <option value="pending">تحت المراجعة</option>
                    <option value="archived">مؤرشف</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الفئة</label>
                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                    <option value="">الكل</option>
                    <option value="news">أخبار</option>
                    <option value="analysis">تحليلات</option>
                    <option value="guides">دلائل</option>
                    <option value="tutorials">دروس</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الكاتب</label>
                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                    <option value="">الكل</option>
                    <option value="1">أحمد محمد</option>
                    <option value="2">فاطمة علي</option>
                    <option value="3">محمد سعيد</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Blog Posts Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">المقالات</h3>
                <div class="flex space-x-2 space-x-reverse">
                    <button class="px-3 py-1 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">
                        <i class="fas fa-th-large ml-1"></i>شبكة
                    </button>
                    <button class="px-3 py-1 text-sm bg-pink-600 text-white rounded-lg">
                        <i class="fas fa-list ml-1"></i>قائمة
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
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">العنوان</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الفئة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الكاتب</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المشاهدات</th>
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
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="bg-pink-100 p-2 rounded-lg ml-3">
                                    <i class="fas fa-file-alt text-pink-600"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">دليل العقارات الشامل {{ $i }}</p>
                                    <p class="text-sm text-gray-600">نظرة عامة على سوق العقارات...</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                {{ ['أخبار', 'تحليلات', 'دلائل', 'دروس'][$i % 4] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="bg-gray-200 rounded-full w-8 h-8 ml-2 flex items-center justify-center">
                                    <i class="fas fa-user text-gray-600 text-xs"></i>
                                </div>
                                <span class="text-sm text-gray-900">{{ ['أحمد محمد', 'فاطمة علي', 'محمد سعيد'][$i % 3] }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full 
                                @if($i <= 6) bg-green-100 text-green-800
                                @elseif($i <= 8) bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ['منشور', 'مسودة', 'تحت المراجعة'][$i <= 6 ? 0 : ($i <= 8 ? 2 : 1)] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <i class="fas fa-eye text-gray-400 ml-2"></i>
                                <span class="text-sm text-gray-900">{{ rand(100, 5000) }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <p class="text-sm text-gray-900">{{ now()->subDays(rand(1, 30))->format('Y-m-d') }}</p>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-left">
                            <button class="text-pink-600 hover:text-pink-900 ml-3" title="عرض">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="text-blue-600 hover:text-blue-900 ml-3" title="تحرير">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="text-green-600 hover:text-green-900 ml-3" title="نسخ">
                                <i class="fas fa-copy"></i>
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
                    عرض <span class="font-medium">1</span> إلى <span class="font-medium">10</span> من <span class="font-medium">342</span> مقال
                </p>
                <div class="flex space-x-2 space-x-reverse">
                    <button class="px-3 py-1 border border-gray-300 rounded-md hover:bg-gray-50">السابق</button>
                    <button class="px-3 py-1 bg-pink-600 text-white rounded-md">1</button>
                    <button class="px-3 py-1 border border-gray-300 rounded-md hover:bg-gray-50">2</button>
                    <button class="px-3 py-1 border border-gray-300 rounded-md hover:bg-gray-50">3</button>
                    <button class="px-3 py-1 border border-gray-300 rounded-md hover:bg-gray-50">التالي</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Blog Categories -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-8">
        <!-- Categories -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">الفئات</h3>
            </div>
            <div class="p-6">
                <div class="space-y-3">
                    @foreach(['أخبار', 'تحليلات', 'دلائل', 'دروس', 'عقارات', 'استثمار'] as $category)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="bg-pink-100 p-2 rounded-lg ml-3">
                                <i class="fas fa-tag text-pink-600 text-sm"></i>
                            </div>
                            <span class="font-medium text-gray-900">{{ $category }}</span>
                        </div>
                        <div class="flex items-center">
                            <span class="text-sm text-gray-600 ml-3">{{ rand(10, 100) }} مقال</span>
                            <button class="text-blue-600 hover:text-blue-900 mr-2">
                                <i class="fas fa-edit"></i>
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Popular Tags -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">الوسوم الشائعة</h3>
            </div>
            <div class="p-6">
                <div class="flex flex-wrap gap-2">
                    @foreach(['عقارات', 'استثمار', 'تمويل', 'رياض', 'جدة', 'شقق', 'فلل', 'أراضي', 'سكني', 'تجاري'] as $tag)
                    <span class="px-3 py-1 bg-pink-100 text-pink-800 rounded-full text-sm hover:bg-pink-200 cursor-pointer transition-colors">
                        {{ $tag }} ({{ rand(5, 50) }})
                    </span>
                    @endforeach
                </div>
                <div class="mt-4">
                    <button class="text-pink-600 hover:text-pink-900 text-sm font-medium">
                        <i class="fas fa-plus ml-1"></i>إضافة وسم جديد
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

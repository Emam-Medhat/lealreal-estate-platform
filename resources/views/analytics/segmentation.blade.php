@extends('layouts.dashboard')

@section('title', 'تقسيم الجمهور')

@section('content')

<div class="max-w-7xl mx-auto">
    <!-- Segmentation Header -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">تقسيم الجمهور</h1>
                <p class="text-gray-600">تحليل وتقسيم المستخدمين إلى شرائح مختلفة</p>
            </div>
            <div class="flex items-center space-x-2 space-x-reverse">
                <button class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-plus ml-2"></i>
                    شريحة جديدة
                </button>
                <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-filter ml-2"></i>
                    فلترة
                </button>
            </div>
        </div>
    </div>

    <!-- Segments Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">إجمالي الشرائح</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $totalSegments ?? 0 }}</h3>
                    <p class="text-xs text-green-600">+2 هذا الأسبوع</p>
                </div>
                <div class="bg-blue-100 text-blue-600 p-3 rounded-full">
                    <i class="fas fa-layer-group text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">شرائح نشطة</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $activeSegments ?? 0 }}</h3>
                    <p class="text-xs text-green-600">85% من الإجمالي</p>
                </div>
                <div class="bg-green-100 text-green-600 p-3 rounded-full">
                    <i class="fas fa-users text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">متوسط الحجم</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ number_format($avgSegmentSize ?? 0) }}</h3>
                    <p class="text-xs text-gray-600">مستخدمين</p>
                </div>
                <div class="bg-purple-100 text-purple-600 p-3 rounded-full">
                    <i class="fas fa-chart-bar text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-orange-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">معدل التحويل</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ number_format($conversionRate ?? 0, 1) }}%</h3>
                    <p class="text-xs text-green-600">+1.2% هذا الشهر</p>
                </div>
                <div class="bg-orange-100 text-orange-600 p-3 rounded-full">
                    <i class="fas fa-percentage text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Segments List -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-semibold text-gray-800 border-b pb-2">الشرائح الحالية</h2>
            <div class="flex items-center space-x-2 space-x-reverse">
                <select class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option>الكل</option>
                    <option>نشطة</option>
                    <option>غير نشطة</option>
                    <option>مخصصة</option>
                    <option>تلقائية</option>
                </select>
                <input type="text" placeholder="بحث عن شريحة..." class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @if(isset($segments) && $segments->count() > 0)
                @foreach($segments as $segment)
                <div class="border rounded-lg p-4 hover:shadow-lg transition-shadow">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center">
                            <div class="bg-blue-100 text-blue-600 p-2 rounded-full ml-3">
                                <i class="fas fa-tag text-sm"></i>
                            </div>
                            <div>
                                <h3 class="font-medium text-gray-900">{{ $segment->name }}</h3>
                                <p class="text-sm text-gray-500">{{ $segment->type }}</p>
                            </div>
                        </div>
                        <span class="px-2 py-1 text-xs font-medium rounded-full {{ $segment->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $segment->is_active ? 'نشطة' : 'غير نشطة' }}
                        </span>
                    </div>
                    <p class="text-gray-700 text-sm mb-3">{{ $segment->description }}</p>
                    <div class="space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">الحجم</span>
                            <span class="text-sm font-medium">{{ number_format($segment->user_count) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">معدل التحويل</span>
                            <span class="text-sm font-medium">{{ number_format($segment->conversion_rate, 1) }}%</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">آخر تحديث</span>
                            <span class="text-sm font-medium">{{ $segment->updated_at->diffForHumans() }}</span>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t flex justify-between">
                        <button class="text-blue-600 hover:text-blue-800 text-sm">عرض</button>
                        <button class="text-green-600 hover:text-green-800 text-sm">تعديل</button>
                        <button class="text-red-600 hover:text-red-800 text-sm">حذف</button>
                    </div>
                </div>
                @endforeach
            @else
                <div class="col-span-3 text-center py-12 text-gray-500">
                    <i class="fas fa-layer-group text-4xl mb-4"></i>
                    <p>لا توجد شرائح حالياً</p>
                    <p class="text-sm">ابدأ بإنشاء شريحة جديدة لتحليل جمهورك</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Create Segment Form -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">إنشاء شريحة جديدة</h2>
        <form class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">اسم الشريحة</label>
                    <input type="text" class="w-full border border-gray-300 rounded-lg px-3 py-2" placeholder="مثال: المستخدمون النشطون">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">نوع الشريحة</label>
                    <select class="w-full border border-gray-300 rounded-lg px-3 py-2">
                        <option>ديموغرافية</option>
                        <option>سلوكية</option>
                        <option>جغرافية</option>
                        <option>مخصصة</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الوصف</label>
                <textarea class="w-full border border-gray-300 rounded-lg px-3 py-2" rows="3" placeholder="وصف الشريحة والمعايير المستخدمة"></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">معايير التقسيم</label>
                <div class="space-y-3">
                    <div class="flex items-center space-x-2 space-x-reverse">
                        <select class="border border-gray-300 rounded-lg px-3 py-2">
                            <option>العمر</option>
                            <option>الجنس</option>
                            <option>الموقع</option>
                            <option>الاهتمامات</option>
                            <option>سلوك الشراء</option>
                        </select>
                        <select class="border border-gray-300 rounded-lg px-3 py-2">
                            <option>يساوي</option>
                            <option>أكبر من</option>
                            <option>أصغر من</option>
                            <option>يحتوي على</option>
                        </select>
                        <input type="text" class="border border-gray-300 rounded-lg px-3 py-2" placeholder="القيمة">
                        <button type="button" class="bg-red-600 text-white px-3 py-2 rounded-lg hover:bg-red-700">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    <button type="button" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                        <i class="fas fa-plus ml-2"></i>
                        إضافة معيار
                    </button>
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input type="checkbox" id="auto-update" class="ml-2">
                    <label for="auto-update" class="text-sm text-gray-700">تحديث تلقائي</label>
                </div>
                <div class="flex items-center space-x-2 space-x-reverse">
                    <button type="button" class="border border-gray-300 px-4 py-2 rounded-lg hover:bg-gray-50">
                        إلغاء
                    </button>
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                        إنشاء الشريحة
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

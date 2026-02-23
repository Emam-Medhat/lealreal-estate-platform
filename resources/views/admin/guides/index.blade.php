@extends('admin.layouts.admin')

@section('title', 'إدارة الأدلة')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">إدارة الأدلة</h1>
                <p class="text-gray-600">إدارة وتنظيم أدلة المستخدمين</p>
            </div>
            <a href="{{ route('admin.guides.create') }}" 
               class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-6 py-3 rounded-xl font-medium flex items-center space-x-2 space-x-reverse transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-[1.02]">
                <i class="fas fa-plus"></i>
                <span>دليل جديد</span>
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Total Guides -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium mb-1">إجمالي الأدلة</p>
                    <p class="text-3xl font-bold">{{ $guides->total() }}</p>
                    <p class="text-blue-100 text-xs mt-2">
                        <i class="fas fa-book ml-1"></i>
                        جميع الأدلة
                    </p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-book text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Published -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium mb-1">منشور</p>
                    <p class="text-3xl font-bold">{{ App\Models\Guide::where('status', 'published')->count() }}</p>
                    <p class="text-green-100 text-xs mt-2">
                        <i class="fas fa-check-circle ml-1"></i>
                        منشور
                    </p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-check-circle text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Draft -->
        <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-yellow-100 text-sm font-medium mb-1">مسودة</p>
                    <p class="text-3xl font-bold">{{ App\Models\Guide::where('status', 'draft')->count() }}</p>
                    <p class="text-yellow-100 text-xs mt-2">
                        <i class="fas fa-edit ml-1"></i>
                        مسودة
                    </p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-edit text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Views -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium mb-1">المشاهدات</p>
                    <p class="text-3xl font-bold">{{ App\Models\Guide::sum('views') ?? 0 }}</p>
                    <p class="text-purple-100 text-xs mt-2">
                        <i class="fas fa-eye ml-1"></i>
                        إجمالي المشاهدات
                    </p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-eye text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">البحث والتصفية</h3>
        <form method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الحالة</label>
                    <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
                        <option value="">كل الحالات</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>مسودة</option>
                        <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>منشور</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">التصنيف</label>
                    <select name="category" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
                        <option value="">كل التصنيفات</option>
                        <option value="beginner" {{ request('category') == 'beginner' ? 'selected' : '' }}>مبتدئ</option>
                        <option value="intermediate" {{ request('category') == 'intermediate' ? 'selected' : '' }}>متوسط</option>
                        <option value="advanced" {{ request('category') == 'advanced' ? 'selected' : '' }}>متقدم</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">البحث</label>
                    <div class="relative">
                        <input type="text" name="search" 
                               class="w-full px-4 py-2 pr-10 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors" 
                               placeholder="بحث في العنوان أو المحتوى..." 
                               value="{{ request('search') }}">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    </div>
                </div>
                
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-6 py-2 rounded-xl font-medium transition-all duration-300 flex items-center justify-center space-x-2 space-x-reverse shadow-lg hover:shadow-xl transform hover:scale-[1.02]">
                        <i class="fas fa-search"></i>
                        <span>بحث</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Guides Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        @if($guides->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900">العنوان</th>
                            <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900">التصنيف</th>
                            <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900">الصعوبة</th>
                            <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900">الحالة</th>
                            <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900">الكاتب</th>
                            <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900">المشاهدات</th>
                            <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900">وقت القراءة</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-gray-900">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($guides as $guide)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-2 space-x-reverse">
                                        <a href="{{ route('admin.guides.show', $guide) }}" 
                                           class="text-gray-900 hover:text-green-600 font-medium transition-colors">
                                            {{ $guide->title }}
                                        </a>
                                        @if($guide->is_featured)
                                            <i class="fas fa-star text-yellow-500" title="مميز"></i>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-gray-600">{{ $guide->category ?? 'غير محدد' }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    @switch($guide->difficulty)
                                        @case('beginner')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-seedling ml-1"></i>
                                                مبتدئ
                                            </span>
                                            @break
                                        @case('intermediate')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                <i class="fas fa-fire ml-1"></i>
                                                متوسط
                                            </span>
                                            @break
                                        @case('advanced')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <i class="fas fa-rocket ml-1"></i>
                                                متقدم
                                            </span>
                                            @break
                                    @endswitch
                                </td>
                                <td class="px-6 py-4">
                                    @switch($guide->status)
                                        @case('published')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-check-circle ml-1"></i>
                                                منشور
                                            </span>
                                            @break
                                        @case('draft')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                <i class="fas fa-edit ml-1"></i>
                                                مسودة
                                            </span>
                                            @break
                                    @endswitch
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-2 space-x-reverse">
                                        <div class="bg-gray-200 rounded-full p-1">
                                            <i class="fas fa-user text-gray-600 text-xs"></i>
                                        </div>
                                        <span class="text-gray-600">{{ $guide->author->name ?? 'غير محدد' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-1 space-x-reverse text-gray-600">
                                        <i class="fas fa-eye text-sm"></i>
                                        <span>{{ $guide->views ?? 0 }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-1 space-x-reverse text-gray-600">
                                        <i class="fas fa-clock text-sm"></i>
                                        <span>{{ $guide->reading_time }} دقيقة</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center space-x-2 space-x-reverse">
                                        <a href="{{ route('admin.guides.show', $guide) }}" 
                                           class="text-green-600 hover:text-green-800 p-2 rounded-xl hover:bg-green-50 transition-colors"
                                           title="عرض">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.guides.edit', $guide) }}" 
                                           class="text-blue-600 hover:text-blue-800 p-2 rounded-xl hover:bg-blue-50 transition-colors"
                                           title="تعديل">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.guides.destroy', $guide) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="text-red-600 hover:text-red-800 p-2 rounded-xl hover:bg-red-50 transition-colors"
                                                    title="حذف"
                                                    onclick="return confirm('هل أنت متأكد من حذف هذا الدليل؟')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-600">
                        عرض {{ $guides->firstItem() ?? 0 }} - {{ $guides->lastItem() ?? 0 }} من {{ $guides->total() }} دليل
                    </div>
                    <div class="flex items-center space-x-2 space-x-reverse">
                        {{ $guides->links() }}
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-12">
                <div class="bg-gray-100 rounded-full p-4 w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                    <i class="fas fa-book text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">لا توجد أدلة</h3>
                <p class="text-gray-600 mb-6">ابدأ بإنشاء أول دليل للمستخدمين</p>
                <a href="{{ route('admin.guides.create') }}" 
                   class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-6 py-2 rounded-xl font-medium transition-all duration-300 inline-flex items-center space-x-2 space-x-reverse shadow-lg hover:shadow-xl transform hover:scale-[1.02]">
                    <i class="fas fa-plus"></i>
                    <span>إنشاء دليل جديد</span>
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

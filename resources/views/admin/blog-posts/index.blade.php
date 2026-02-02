@extends('admin.layouts.admin')

@section('title', 'إدارة المقالات')

@section('content')
<!-- Page Header -->
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">إدارة المقالات</h1>
            <p class="text-gray-600 mt-1">إدارة وتنظيم مقالات المدونة</p>
        </div>
        <a href="{{ route('admin.blog.posts.create') }}" 
           class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-6 py-3 rounded-lg font-medium flex items-center space-x-2 space-x-reverse transition-all duration-200 shadow-lg hover:shadow-xl">
            <i class="fas fa-plus"></i>
            <span>مقال جديد</span>
        </a>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">إجمالي المقالات</p>
                <p class="text-2xl font-bold text-gray-900">{{ $posts->total() }}</p>
            </div>
            <div class="bg-blue-100 rounded-full p-3">
                <i class="fas fa-file-alt text-blue-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">منشور</p>
                <p class="text-2xl font-bold text-green-600">{{ App\Models\BlogPost::where('status', 'published')->count() }}</p>
            </div>
            <div class="bg-green-100 rounded-full p-3">
                <i class="fas fa-check-circle text-green-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">مسودة</p>
                <p class="text-2xl font-bold text-yellow-600">{{ App\Models\BlogPost::where('status', 'draft')->count() }}</p>
            </div>
            <div class="bg-yellow-100 rounded-full p-3">
                <i class="fas fa-edit text-yellow-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">المشاهدات</p>
                <p class="text-2xl font-bold text-purple-600">{{ App\Models\BlogPost::sum('views') ?? 0 }}</p>
            </div>
            <div class="bg-purple-100 rounded-full p-3">
                <i class="fas fa-eye text-purple-600"></i>
            </div>
        </div>
    </div>
</div>

<!-- Filters Section -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">البحث والتصفية</h3>
    <form method="GET" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الحالة</label>
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                    <option value="">كل الحالات</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>مسودة</option>
                    <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>منشور</option>
                    <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>مؤرشف</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">التصنيف</label>
                <select name="category" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                    <option value="">كل التصنيفات</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">البحث</label>
                <div class="relative">
                    <input type="text" name="search" 
                           class="w-full px-4 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                           placeholder="بحث في العنوان أو المحتوى..." 
                           value="{{ request('search') }}">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                </div>
            </div>
            
            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center space-x-2 space-x-reverse">
                    <i class="fas fa-search"></i>
                    <span>بحث</span>
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Posts Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    @if($posts->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900">العنوان</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900">التصنيف</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900">الحالة</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900">الكاتب</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900">المشاهدات</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900">تاريخ النشر</th>
                        <th class="px-6 py-4 text-center text-sm font-semibold text-gray-900">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($posts as $post)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2 space-x-reverse">
                                    <a href="{{ route('admin.blog.posts.show', $post) }}" 
                                       class="text-gray-900 hover:text-blue-600 font-medium transition-colors">
                                        {{ $post->title }}
                                    </a>
                                    @if($post->is_featured)
                                        <i class="fas fa-star text-yellow-500" title="مميز"></i>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-gray-600">{{ $post->category->name ?? 'غير محدد' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @switch($post->status)
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
                                    @case('archived')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            <i class="fas fa-archive ml-1"></i>
                                            مؤرشف
                                        </span>
                                        @break
                                @endswitch
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2 space-x-reverse">
                                    <div class="bg-gray-200 rounded-full p-1">
                                        <i class="fas fa-user text-gray-600 text-xs"></i>
                                    </div>
                                    <span class="text-gray-600">{{ $post->author->name ?? 'غير محدد' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-1 space-x-reverse text-gray-600">
                                    <i class="fas fa-eye text-sm"></i>
                                    <span>{{ $post->views ?? 0 }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-gray-600">{{ $post->published_at?->format('Y-m-d') ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center space-x-2 space-x-reverse">
                                    <a href="{{ route('admin.blog.posts.show', $post) }}" 
                                       class="text-blue-600 hover:text-blue-800 p-2 rounded-lg hover:bg-blue-50 transition-colors"
                                       title="عرض">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.blog.posts.edit', $post) }}" 
                                       class="text-green-600 hover:text-green-800 p-2 rounded-lg hover:bg-green-50 transition-colors"
                                       title="تعديل">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.blog.posts.destroy', $post) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="text-red-600 hover:text-red-800 p-2 rounded-lg hover:bg-red-50 transition-colors"
                                                title="حذف"
                                                onclick="return confirm('هل أنت متأكد من حذف هذا المقال؟')">
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
                    عرض {{ $posts->firstItem() ?? 0 }} - {{ $posts->lastItem() ?? 0 }} من {{ $posts->total() }} مقال
                </div>
                <div class="flex items-center space-x-2 space-x-reverse">
                    {{ $posts->links() }}
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-12">
            <div class="bg-gray-100 rounded-full p-4 w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                <i class="fas fa-file-alt text-gray-400 text-2xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">لا توجد مقالات</h3>
            <p class="text-gray-600 mb-6">ابدأ بإنشاء أول مقال في المدونة</p>
            <a href="{{ route('admin.blog.posts.create') }}" 
               class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200 inline-flex items-center space-x-2 space-x-reverse">
                <i class="fas fa-plus"></i>
                <span>إنشاء مقال جديد</span>
            </a>
        </div>
    @endif
</div>
@endsection

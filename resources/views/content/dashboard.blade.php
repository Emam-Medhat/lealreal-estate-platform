@extends('admin.layouts.admin')

@section('title', 'لوحة تحكم المحتوى')
@section('page-title', 'لوحة تحكم المحتوى')

@section('content')
<div class="p-6">
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-6">
        <!-- Blog Posts Card -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-blue-500 text-white p-4">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-2xl font-bold">{{ $stats['blog_posts'] }}</h3>
                        <p class="text-blue-100">المقالات</p>
                    </div>
                    <div class="text-3xl">
                        <i class="fas fa-blog"></i>
                    </div>
                </div>
            </div>
            <div class="p-3 bg-gray-50">
                <a href="{{ route('admin.blog.posts.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium flex items-center justify-between">
                    عرض التفاصيل
                    <i class="fas fa-arrow-left"></i>
                </a>
            </div>
        </div>

        <!-- Pages Card -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-green-500 text-white p-4">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-2xl font-bold">{{ $stats['pages'] }}</h3>
                        <p class="text-green-100">الصفحات</p>
                    </div>
                    <div class="text-3xl">
                        <i class="fas fa-file-alt"></i>
                    </div>
                </div>
            </div>
            <div class="p-3 bg-gray-50">
                <a href="{{ route('admin.pages.index') }}" class="text-green-600 hover:text-green-800 text-sm font-medium flex items-center justify-between">
                    عرض التفاصيل
                    <i class="fas fa-arrow-left"></i>
                </a>
            </div>
        </div>

        <!-- News Card -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-cyan-500 text-white p-4">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-2xl font-bold">{{ $stats['news'] }}</h3>
                        <p class="text-cyan-100">الأخبار</p>
                    </div>
                    <div class="text-3xl">
                        <i class="fas fa-newspaper"></i>
                    </div>
                </div>
            </div>
            <div class="p-3 bg-gray-50">
                <a href="{{ route('admin.news.index') }}" class="text-cyan-600 hover:text-cyan-800 text-sm font-medium flex items-center justify-between">
                    عرض التفاصيل
                    <i class="fas fa-arrow-left"></i>
                </a>
            </div>
        </div>

        <!-- Guides Card -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-yellow-500 text-white p-4">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-2xl font-bold">{{ $stats['guides'] }}</h3>
                        <p class="text-yellow-100">الأدلة</p>
                    </div>
                    <div class="text-3xl">
                        <i class="fas fa-book"></i>
                    </div>
                </div>
            </div>
            <div class="p-3 bg-gray-50">
                <a href="{{ route('admin.guides.index') }}" class="text-yellow-600 hover:text-yellow-800 text-sm font-medium flex items-center justify-between">
                    عرض التفاصيل
                    <i class="fas fa-arrow-left"></i>
                </a>
            </div>
        </div>

        <!-- FAQs Card -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-gray-600 text-white p-4">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-2xl font-bold">{{ $stats['faqs'] }}</h3>
                        <p class="text-gray-100">الأسئلة</p>
                    </div>
                    <div class="text-3xl">
                        <i class="fas fa-question-circle"></i>
                    </div>
                </div>
            </div>
            <div class="p-3 bg-gray-50">
                <a href="{{ route('admin.faqs.index') }}" class="text-gray-600 hover:text-gray-800 text-sm font-medium flex items-center justify-between">
                    عرض التفاصيل
                    <i class="fas fa-arrow-left"></i>
                </a>
            </div>
        </div>

        <!-- Published Posts Card -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-gray-800 text-white p-4">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-2xl font-bold">{{ $stats['published_posts'] }}</h3>
                        <p class="text-gray-100">منشورة</p>
                    </div>
                    <div class="text-3xl">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
            <div class="p-3 bg-gray-50">
                <small class="text-gray-600">{{ $stats['draft_posts'] }} مسودة</small>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h5 class="text-lg font-semibold mb-4 text-gray-800">إجراءات سريعة</h5>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="{{ route('admin.blog.posts.create') }}" class="flex items-center justify-center px-4 py-3 border border-blue-500 text-blue-600 rounded-lg hover:bg-blue-50 transition-colors">
                <i class="fas fa-plus ml-2"></i>
                مقال جديد
            </a>
            <a href="{{ route('admin.pages.create') }}" class="flex items-center justify-center px-4 py-3 border border-green-500 text-green-600 rounded-lg hover:bg-green-50 transition-colors">
                <i class="fas fa-plus ml-2"></i>
                صفحة جديدة
            </a>
            <a href="{{ route('admin.news.create') }}" class="flex items-center justify-center px-4 py-3 border border-cyan-500 text-cyan-600 rounded-lg hover:bg-cyan-50 transition-colors">
                <i class="fas fa-plus ml-2"></i>
                خبر جديد
            </a>
            <a href="{{ route('admin.guides.create') }}" class="flex items-center justify-center px-4 py-3 border border-yellow-500 text-yellow-600 rounded-lg hover:bg-yellow-50 transition-colors">
                <i class="fas fa-plus ml-2"></i>
                دليل جديد
            </a>
            <a href="{{ route('admin.media.index') }}" class="flex items-center justify-center px-4 py-3 border border-gray-500 text-gray-600 rounded-lg hover:bg-gray-50 transition-colors">
                <i class="fas fa-images ml-2"></i>
                مكتبة الوسائط
            </a>
            <a href="{{ route('admin.menus.index') }}" class="flex items-center justify-center px-4 py-3 border border-gray-700 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                <i class="fas fa-bars ml-2"></i>
                إدارة القوائم
            </a>
            <a href="{{ route('admin.widgets.index') }}" class="flex items-center justify-center px-4 py-3 border border-blue-500 text-blue-600 rounded-lg hover:bg-blue-50 transition-colors">
                <i class="fas fa-th ml-2"></i>
                إدارة الويدجتات
            </a>
            <a href="{{ route('admin.seo.index') }}" class="flex items-center justify-center px-4 py-3 border border-green-500 text-green-600 rounded-lg hover:bg-green-50 transition-colors">
                <i class="fas fa-search ml-2"></i>
                تحسين محركات البحث
            </a>
        </div>
    </div>

    <!-- Recent Content -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Posts -->
        <div class="bg-white rounded-lg shadow-md">
            <div class="p-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h5 class="text-lg font-semibold text-gray-800">آخر المقالات</h5>
                    <a href="{{ route('admin.blog.posts.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        عرض الكل
                    </a>
                </div>
            </div>
            <div class="p-4">
                @if($recent_posts->count() > 0)
                    <div class="space-y-3">
                        @foreach($recent_posts as $post)
                            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                <div class="flex-1">
                                    <h6 class="font-medium text-gray-800">{{ $post->title }}</h6>
                                    <small class="text-gray-500">
                                        {{ $post->author->name ?? 'غير محدد' }} - {{ $post->created_at->format('Y-m-d') }}
                                    </small>
                                </div>
                                <div>
                                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $post->status == 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ $post->status == 'published' ? 'منشور' : 'مسودة' }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-blog text-4xl mb-3"></i>
                        <p>لا توجد مقالات بعد</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Recent News -->
        <div class="bg-white rounded-lg shadow-md">
            <div class="p-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h5 class="text-lg font-semibold text-gray-800">آخر الأخبار</h5>
                    <a href="{{ route('admin.news.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        عرض الكل
                    </a>
                </div>
            </div>
            <div class="p-4">
                @if($recent_news->count() > 0)
                    <div class="space-y-3">
                        @foreach($recent_news as $news)
                            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                <div class="flex-1">
                                    <h6 class="font-medium text-gray-800">{{ $news->title }}</h6>
                                    <small class="text-gray-500">
                                        {{ $news->author->name ?? 'غير محدد' }} - {{ $news->created_at->format('Y-m-d') }}
                                    </small>
                                </div>
                                <div>
                                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $news->status == 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ $news->status == 'published' ? 'منشور' : 'مسودة' }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-newspaper text-4xl mb-3"></i>
                        <p>لا توجد أخبار بعد</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Content Overview -->
    <div class="bg-white rounded-lg shadow-md p-6 mt-6">
        <h5 class="text-lg font-semibold mb-4 text-gray-800">نظرة عامة على المحتوى</h5>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="text-center">
                <div class="bg-blue-50 rounded-lg p-4">
                    <h3 class="text-2xl font-bold text-blue-600">{{ $stats['blog_posts'] }}</h3>
                    <p class="text-gray-600 mt-1">إجمالي المقالات</p>
                    <div class="w-full bg-blue-200 rounded-full h-2 mt-3">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ ($stats['blog_posts'] / max($stats['blog_posts'], 1)) * 100 }}%"></div>
                    </div>
                </div>
            </div>
            <div class="text-center">
                <div class="bg-green-50 rounded-lg p-4">
                    <h3 class="text-2xl font-bold text-green-600">{{ $stats['pages'] }}</h3>
                    <p class="text-gray-600 mt-1">إجمالي الصفحات</p>
                    <div class="w-full bg-green-200 rounded-full h-2 mt-3">
                        <div class="bg-green-600 h-2 rounded-full" style="width: {{ ($stats['pages'] / max($stats['pages'], 1)) * 100 }}%"></div>
                    </div>
                </div>
            </div>
            <div class="text-center">
                <div class="bg-cyan-50 rounded-lg p-4">
                    <h3 class="text-2xl font-bold text-cyan-600">{{ $stats['news'] }}</h3>
                    <p class="text-gray-600 mt-1">إجمالي الأخبار</p>
                    <div class="w-full bg-cyan-200 rounded-full h-2 mt-3">
                        <div class="bg-cyan-600 h-2 rounded-full" style="width: {{ ($stats['news'] / max($stats['news'], 1)) * 100 }}%"></div>
                    </div>
                </div>
            </div>
            <div class="text-center">
                <div class="bg-yellow-50 rounded-lg p-4">
                    <h3 class="text-2xl font-bold text-yellow-600">{{ $stats['guides'] }}</h3>
                    <p class="text-gray-600 mt-1">إجمالي الأدلة</p>
                    <div class="w-full bg-yellow-200 rounded-full h-2 mt-3">
                        <div class="bg-yellow-600 h-2 rounded-full" style="width: {{ ($stats['guides'] / max($stats['guides'], 1)) * 100 }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

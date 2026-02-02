@extends('admin.layouts.admin')

@section('title', 'لوحة CMS')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">لوحة نظام إدارة المحتوى</h1>
            <p class="text-gray-600 mt-2">إدارة المحتوى والمدونات والوسائط</p>
        </div>
        <div class="flex space-x-3 space-x-reverse">
            <button class="bg-pink-600 text-white px-4 py-2 rounded-lg hover:bg-pink-700 transition-colors">
                <i class="fas fa-plus ml-2"></i>
                محتوى جديد
            </button>
            <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-chart-bar ml-2"></i>
                التحليلات
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">إجمالي المقالات</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_posts'] ?? 0 }}</p>
                </div>
                <div class="bg-pink-100 p-3 rounded-lg">
                    <i class="fas fa-blog text-pink-600"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-green-600"><i class="fas fa-arrow-up ml-1"></i>12%</span>
                <span class="text-gray-500 mr-2">من الشهر الماضي</span>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">الصفحات</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_pages'] ?? 0 }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-lg">
                    <i class="fas fa-file-alt text-blue-600"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-green-600"><i class="fas fa-arrow-up ml-1"></i>8%</span>
                <span class="text-gray-500 mr-2">زيادة هذا الشهر</span>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">الوسائط</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_media'] ?? 0 }}</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-lg">
                    <i class="fas fa-photo-video text-purple-600"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-green-600"><i class="fas fa-arrow-up ml-1"></i>25%</span>
                <span class="text-gray-500 mr-2">ملفات جديدة</span>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">القوائم</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_menus'] ?? 0 }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-lg">
                    <i class="fas fa-bars text-green-600"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-green-600"><i class="fas fa-check ml-1"></i>نشطة</span>
                <span class="text-gray-500 mr-2">جميع القوائم</span>
            </div>
        </div>
    </div>

    <!-- Recent Content & Quick Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Recent Posts -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">آخر المقالات</h3>
            </div>
            <div class="p-6">
                @if($recentPosts->count() > 0)
                    <div class="space-y-4">
                        @foreach($recentPosts->take(5) as $post)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    <div class="bg-pink-100 p-2 rounded-lg ml-3">
                                        <i class="fas fa-file-alt text-pink-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $post->title ?? 'مقال بدون عنوان' }}</p>
                                        <p class="text-sm text-gray-600">{{ $post->created_at->format('Y-m-d H:i') }}</p>
                                    </div>
                                </div>
                                <div class="text-left">
                                    <span class="px-2 py-1 text-xs rounded-full 
                                        @if($post->status == 'published') bg-green-100 text-green-800
                                        @elseif($post->status == 'draft') bg-gray-100 text-gray-800
                                        @else bg-yellow-100 text-yellow-800 @endif">
                                        {{ $post->status ?? 'draft' }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-blog text-gray-300 text-4xl mb-3"></i>
                        <p class="text-gray-500">لا توجد مقالات حالياً</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">إجراءات سريعة</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 gap-4">
                    <button class="p-4 bg-pink-50 rounded-lg hover:bg-pink-100 transition-colors text-right">
                        <i class="fas fa-plus-circle text-pink-600 text-2xl mb-2"></i>
                        <p class="font-medium text-gray-900">مقال جديد</p>
                        <p class="text-sm text-gray-600">إنشاء مقال جديد</p>
                    </button>
                    <button class="p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors text-right">
                        <i class="fas fa-image text-blue-600 text-2xl mb-2"></i>
                        <p class="font-medium text-gray-900">رفع وسائط</p>
                        <p class="text-sm text-gray-600">إضافة صور أو فيديو</p>
                    </button>
                    <button class="p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors text-right">
                        <i class="fas fa-sitemap text-green-600 text-2xl mb-2"></i>
                        <p class="font-medium text-gray-900">قائمة جديدة</p>
                        <p class="text-sm text-gray-600">إنشاء قائمة تنقل</p>
                    </button>
                    <button class="p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors text-right">
                        <i class="fas fa-file text-purple-600 text-2xl mb-2"></i>
                        <p class="font-medium text-gray-900">صفحة جديدة</p>
                        <p class="text-sm text-gray-600">إنشاء صفحة ثابتة</p>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Analytics -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">تحليلات المحتوى</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                    <div class="bg-pink-100 p-4 rounded-lg mb-3">
                        <i class="fas fa-eye text-pink-600 text-3xl"></i>
                    </div>
                    <h4 class="font-semibold text-gray-900">إجمالي المشاهدات</h4>
                    <p class="text-2xl font-bold text-gray-900 mt-2">45.2K</p>
                    <p class="text-sm text-green-600 mt-1">
                        <i class="fas fa-arrow-up ml-1"></i>15% من الشهر الماضي
                    </p>
                </div>

                <div class="text-center">
                    <div class="bg-blue-100 p-4 rounded-lg mb-3">
                        <i class="fas fa-users text-blue-600 text-3xl"></i>
                    </div>
                    <h4 class="font-semibold text-gray-900">الزوار الفريدين</h4>
                    <p class="text-2xl font-bold text-gray-900 mt-2">12.8K</p>
                    <p class="text-sm text-green-600 mt-1">
                        <i class="fas fa-arrow-up ml-1"></i>8% من الشهر الماضي
                    </p>
                </div>

                <div class="text-center">
                    <div class="bg-green-100 p-4 rounded-lg mb-3">
                        <i class="fas fa-clock text-green-600 text-3xl"></i>
                    </div>
                    <h4 class="font-semibold text-gray-900">متوسط وقت القراءة</h4>
                    <p class="text-2xl font-bold text-gray-900 mt-2">3:45</p>
                    <p class="text-sm text-green-600 mt-1">
                        <i class="fas fa-arrow-up ml-1"></i>30 ثانية من الشهر الماضي
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

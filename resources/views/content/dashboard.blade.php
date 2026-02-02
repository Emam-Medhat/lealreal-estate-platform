@extends('admin.layouts.admin')

@section('title', 'لوحة تحكم المحتوى')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h4 class="mb-4">لوحة تحكم المحتوى</h4>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ $stats['blog_posts'] }}</h4>
                            <p class="mb-0">المقالات</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-blog fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('admin.blog.posts.index') }}" class="text-white text-decoration-none">
                        عرض التفاصيل <i class="fas fa-arrow-left"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ $stats['pages'] }}</h4>
                            <p class="mb-0">الصفحات</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-file-alt fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('admin.pages.index') }}" class="text-white text-decoration-none">
                        عرض التفاصيل <i class="fas fa-arrow-left"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ $stats['news'] }}</h4>
                            <p class="mb-0">الأخبار</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-newspaper fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('admin.news.index') }}" class="text-white text-decoration-none">
                        عرض التفاصيل <i class="fas fa-arrow-left"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ $stats['guides'] }}</h4>
                            <p class="mb-0">الأدلة</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-book fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('admin.guides.index') }}" class="text-white text-decoration-none">
                        عرض التفاصيل <i class="fas fa-arrow-left"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card bg-secondary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ $stats['faqs'] }}</h4>
                            <p class="mb-0">الأسئلة</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-question-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('admin.faqs.index') }}" class="text-white text-decoration-none">
                        عرض التفاصيل <i class="fas fa-arrow-left"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card bg-dark text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ $stats['published_posts'] }}</h4>
                            <p class="mb-0">منشورة</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <small>{{ $stats['draft_posts'] }} مسودة</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">إجراءات سريعة</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('admin.blog.posts.create') }}" class="btn btn-outline-primary w-100">
                                <i class="fas fa-plus"></i> مقال جديد
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('admin.pages.create') }}" class="btn btn-outline-success w-100">
                                <i class="fas fa-plus"></i> صفحة جديدة
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('admin.news.create') }}" class="btn btn-outline-info w-100">
                                <i class="fas fa-plus"></i> خبر جديد
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('admin.guides.create') }}" class="btn btn-outline-warning w-100">
                                <i class="fas fa-plus"></i> دليل جديد
                            </a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('admin.media.index') }}" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-images"></i> مكتبة الوسائط
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('admin.menus.index') }}" class="btn btn-outline-dark w-100">
                                <i class="fas fa-bars"></i> إدارة القوائم
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('admin.widgets.index') }}" class="btn btn-outline-primary w-100">
                                <i class="fas fa-th"></i> إدارة الويدجتات
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('admin.seo.index') }}" class="btn btn-outline-success w-100">
                                <i class="fas fa-search"></i> تحسين محركات البحث
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Content -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">آخر المقالات</h6>
                    <a href="{{ route('admin.blog.posts.index') }}" class="btn btn-sm btn-outline-primary">
                        عرض الكل
                    </a>
                </div>
                <div class="card-body">
                    @if($recent_posts->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recent_posts as $post)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">{{ $post->title }}</h6>
                                        <small class="text-muted">
                                            {{ $post->author->name ?? 'غير محدد' }} - {{ $post->created_at->format('Y-m-d') }}
                                        </small>
                                    </div>
                                    <div>
                                        <span class="badge bg-{{ $post->status == 'published' ? 'success' : 'warning' }}">
                                            {{ $post->status == 'published' ? 'منشور' : 'مسودة' }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center">لا توجد مقالات بعد</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">آخر الأخبار</h6>
                    <a href="{{ route('admin.news.index') }}" class="btn btn-sm btn-outline-primary">
                        عرض الكل
                    </a>
                </div>
                <div class="card-body">
                    @if($recent_news->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recent_news as $news)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">{{ $news->title }}</h6>
                                        <small class="text-muted">
                                            {{ $news->author->name ?? 'غير محدد' }} - {{ $news->created_at->format('Y-m-d') }}
                                        </small>
                                    </div>
                                    <div>
                                        <span class="badge bg-{{ $news->status == 'published' ? 'success' : 'warning' }}">
                                            {{ $news->status == 'published' ? 'منشور' : 'مسودة' }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center">لا توجد أخبار بعد</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Content Overview Chart -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">نظرة عامة على المحتوى</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="p-3">
                                <h3 class="text-primary">{{ $stats['blog_posts'] }}</h3>
                                <p class="mb-0">إجمالي المقالات</p>
                                <div class="progress mt-2" style="height: 5px;">
                                    <div class="progress-bar bg-primary" style="width: {{ ($stats['blog_posts'] / max($stats['blog_posts'], 1)) * 100 }}%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3">
                                <h3 class="text-success">{{ $stats['pages'] }}</h3>
                                <p class="mb-0">إجمالي الصفحات</p>
                                <div class="progress mt-2" style="height: 5px;">
                                    <div class="progress-bar bg-success" style="width: {{ ($stats['pages'] / max($stats['pages'], 1)) * 100 }}%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3">
                                <h3 class="text-info">{{ $stats['news'] }}</h3>
                                <p class="mb-0">إجمالي الأخبار</p>
                                <div class="progress mt-2" style="height: 5px;">
                                    <div class="progress-bar bg-info" style="width: {{ ($stats['news'] / max($stats['news'], 1)) * 100 }}%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3">
                                <h3 class="text-warning">{{ $stats['guides'] }}</h3>
                                <p class="mb-0">إجمالي الأدلة</p>
                                <div class="progress mt-2" style="height: 5px;">
                                    <div class="progress-bar bg-warning" style="width: {{ ($stats['guides'] / max($stats['guides'], 1)) * 100 }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

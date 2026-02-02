@extends('admin.layouts.admin')

@section('title', 'عرض المقال: ' . $post->title)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4>{{ $post->title }}</h4>
                <div>
                    <a href="{{ route('admin.blog.posts.edit', $post) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> تعديل
                    </a>
                    <a href="{{ route('admin.blog.posts.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right"></i> العودة
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">محتوى المقال</h6>
                        </div>
                        <div class="card-body">
                            @if($post->featured_image)
                                <img src="{{ asset('storage/' . $post->featured_image) }}" 
                                     class="img-fluid mb-3" alt="{{ $post->title }}">
                            @endif
                            
                            <div class="mb-3">
                                <strong>المقتطف:</strong>
                                <p class="text-muted">{{ $post->excerpt ?: 'لا يوجد مقتطف' }}</p>
                            </div>

                            <div class="content">
                                {!! $post->content !!}
                            </div>
                        </div>
                    </div>

                    @if($post->tags->count() > 0)
                        <div class="card mt-3">
                            <div class="card-header">
                                <h6 class="mb-0">الوسوم</h6>
                            </div>
                            <div class="card-body">
                                @foreach($post->tags as $tag)
                                    <span class="badge bg-primary me-1">{{ $tag->name }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($post->revisions->count() > 0)
                        <div class="card mt-3">
                            <div class="card-header">
                                <h6 class="mb-0">سجل التعديلات</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>المحرر</th>
                                                <th>ملاحظات</th>
                                                <th>التاريخ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($post->revisions as $revision)
                                                <tr>
                                                    <td>{{ $revision->author->name ?? 'غير محدد' }}</td>
                                                    <td>{{ $revision->revision_notes }}</td>
                                                    <td>{{ $revision->created_at->format('Y-m-d H:i') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">معلومات المقال</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>الحالة:</strong></td>
                                    <td>
                                        <span class="badge bg-{{ $post->status == 'published' ? 'success' : ($post->status == 'draft' ? 'warning' : 'secondary') }}">
                                            {{ $post->status == 'published' ? 'منشور' : ($post->status == 'draft' ? 'مسودة' : 'مؤرشف') }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>الكاتب:</strong></td>
                                    <td>{{ $post->author->name ?? 'غير محدد' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>التصنيف:</strong></td>
                                    <td>{{ $post->category->name ?? 'غير محدد' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>المشاهدات:</strong></td>
                                    <td>{{ $post->views ?? 0 }}</td>
                                </tr>
                                <tr>
                                    <td><strong>تاريخ الإنشاء:</strong></td>
                                    <td>{{ $post->created_at->format('Y-m-d H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>تاريخ النشر:</strong></td>
                                    <td>{{ $post->published_at?->format('Y-m-d H:i') ?? 'لم ينشر بعد' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>آخر تحديث:</strong></td>
                                    <td>{{ $post->updated_at->format('Y-m-d H:i') }}</td>
                                </tr>
                            </table>

                            <div class="d-grid gap-2">
                                @if($post->is_featured)
                                    <span class="badge bg-warning text-center">
                                        <i class="fas fa-star"></i> مقال مميز
                                    </span>
                                @endif

                                @if($post->allow_comments)
                                    <span class="badge bg-info text-center">
                                        <i class="fas fa-comments"></i> التعليقات مفتوحة
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="mb-0">تحسين محركات البحث</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>عنوان الصفحة:</strong></td>
                                    <td>{{ $post->meta_title ?: 'غير محدد' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>وصف الصفحة:</strong></td>
                                    <td>{{ $post->meta_description ?: 'غير محدد' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>الرابط الدائم:</strong></td>
                                    <td>
                                        <small>{{ route('blog.show', $post) }}</small>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="mb-0">إجراءات سريعة</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('blog.show', $post) }}" target="_blank" class="btn btn-outline-info">
                                    <i class="fas fa-external-link-alt"></i> عرض في الموقع
                                </a>
                                
                                <form action="{{ route('admin.blog.posts.duplicate', $post) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-warning w-100">
                                        <i class="fas fa-copy"></i> نسخ المقال
                                    </button>
                                </form>

                                @if($post->trashed())
                                    <form action="{{ route('admin.blog.posts.restore', $post) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-success w-100">
                                            <i class="fas fa-undo"></i> استعادة المقال
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

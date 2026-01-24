@extends('layouts.admin')

@section('title', 'إدارة المقالات')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">المقالات</h5>
                    <a href="{{ route('admin.blog.posts.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> مقال جديد
                    </a>
                </div>
                <div class="card-body">
                    <form method="GET" class="mb-3">
                        <div class="row">
                            <div class="col-md-3">
                                <select name="status" class="form-control">
                                    <option value="">كل الحالات</option>
                                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>مسودة</option>
                                    <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>منشور</option>
                                    <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>مؤرشف</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="category" class="form-control">
                                    <option value="">كل التصنيفات</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control" 
                                       placeholder="بحث في العنوان أو المحتوى..." 
                                       value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-outline-primary w-100">
                                    <i class="fas fa-search"></i> بحث
                                </button>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>العنوان</th>
                                    <th>التصنيف</th>
                                    <th>الحالة</th>
                                    <th>الكاتب</th>
                                    <th>المشاهدات</th>
                                    <th>تاريخ النشر</th>
                                    <th>إجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($posts as $post)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.blog.posts.show', $post) }}" class="text-decoration-none">
                                                {{ $post->title }}
                                                @if($post->is_featured)
                                                    <i class="fas fa-star text-warning" title="مميز"></i>
                                                @endif
                                            </a>
                                        </td>
                                        <td>{{ $post->category->name ?? 'غير محدد' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $post->status == 'published' ? 'success' : ($post->status == 'draft' ? 'warning' : 'secondary') }}">
                                                {{ $post->status == 'published' ? 'منشور' : ($post->status == 'draft' ? 'مسودة' : 'مؤرشف') }}
                                            </span>
                                        </td>
                                        <td>{{ $post->author->name ?? 'غير محدد' }}</td>
                                        <td>{{ $post->views ?? 0 }}</td>
                                        <td>{{ $post->published_at?->format('Y-m-d') ?? '-' }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('admin.blog.posts.show', $post) }}" class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.blog.posts.edit', $post) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.blog.posts.destroy', $post) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" 
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

                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            عرض {{ $posts->firstItem() }} - {{ $posts->lastItem() }} من {{ $posts->total() }} مقال
                        </div>
                        {{ $posts->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

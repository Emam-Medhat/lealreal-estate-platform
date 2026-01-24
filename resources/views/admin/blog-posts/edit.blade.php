@extends('layouts.admin')

@section('title', 'تعديل المقال: ' . $post->title)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">تعديل المقال: {{ $post->title }}</h5>
                    <div>
                        <a href="{{ route('admin.blog.posts.show', $post) }}" class="btn btn-outline-info">
                            <i class="fas fa-eye"></i> عرض
                        </a>
                        <a href="{{ route('admin.blog.posts.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-right"></i> العودة
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.blog.posts.update', $post) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="title" class="form-label">العنوان *</label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                           id="title" name="title" value="{{ old('title', $post->title) }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="slug" class="form-label">الرابط الدائم</label>
                                    <input type="text" class="form-control @error('slug') is-invalid @enderror" 
                                           id="slug" name="slug" value="{{ old('slug', $post->slug) }}">
                                    @error('slug')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="excerpt" class="form-label">المقتطف</label>
                                    <textarea class="form-control @error('excerpt') is-invalid @enderror" 
                                              id="excerpt" name="excerpt" rows="3">{{ old('excerpt', $post->excerpt) }}</textarea>
                                    @error('excerpt')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="content" class="form-label">المحتوى *</label>
                                    <textarea class="form-control editor @error('content') is-invalid @enderror" 
                                              id="content" name="content" rows="10" required>{{ old('content', $post->content) }}</textarea>
                                    @error('content')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="featured_image" class="form-label">الصورة الرئيسية</label>
                                    <input type="file" class="form-control @error('featured_image') is-invalid @enderror" 
                                           id="featured_image" name="featured_image" accept="image/*">
                                    @error('featured_image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    
                                    @if($post->featured_image)
                                        <div class="mt-2">
                                            <img src="{{ asset('storage/' . $post->featured_image) }}" 
                                                 class="img-thumbnail" style="max-height: 100px;" alt="الصورة الحالية">
                                            <small class="text-muted d-block">الصورة الحالية</small>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">إعدادات النشر</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="category_id" class="form-label">التصنيف *</label>
                                            <select class="form-select @error('category_id') is-invalid @enderror" 
                                                    id="category_id" name="category_id" required>
                                                <option value="">اختر التصنيف</option>
                                                @foreach($categories as $category)
                                                    <option value="{{ $category->id }}" 
                                                            {{ old('category_id', $post->category_id) == $category->id ? 'selected' : '' }}>
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('category_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="tags" class="form-label">الوسوم</label>
                                            <select class="form-select" id="tags" name="tags[]" multiple>
                                                @foreach($tags as $tag)
                                                    <option value="{{ $tag->id }}" 
                                                            {{ in_array($tag->id, old('tags', $post->tags->pluck('id')->toArray())) ? 'selected' : '' }}>
                                                        {{ $tag->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label for="status" class="form-label">الحالة *</label>
                                            <select class="form-select @error('status') is-invalid @enderror" 
                                                    id="status" name="status" required>
                                                <option value="draft" {{ old('status', $post->status) == 'draft' ? 'selected' : '' }}>مسودة</option>
                                                <option value="published" {{ old('status', $post->status) == 'published' ? 'selected' : '' }}>منشور</option>
                                                <option value="archived" {{ old('status', $post->status) == 'archived' ? 'selected' : '' }}>مؤرشف</option>
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="published_at" class="form-label">تاريخ النشر</label>
                                            <input type="datetime-local" class="form-control @error('published_at') is-invalid @enderror" 
                                                   id="published_at" name="published_at" 
                                                   value="{{ old('published_at', $post->published_at?->format('Y-m-d\TH:i')) }}">
                                            @error('published_at')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" id="is_featured" 
                                                   name="is_featured" value="1" {{ old('is_featured', $post->is_featured) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_featured">
                                                مقال مميز
                                            </label>
                                        </div>

                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" id="allow_comments" 
                                                   name="allow_comments" value="1" {{ old('allow_comments', $post->allow_comments) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="allow_comments">
                                                السماح بالتعليقات
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">تحسين محركات البحث</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="meta_title" class="form-label">عنوان الصفحة</label>
                                            <input type="text" class="form-control @error('meta_title') is-invalid @enderror" 
                                                   id="meta_title" name="meta_title" value="{{ old('meta_title', $post->meta_title) }}">
                                            @error('meta_title')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="meta_description" class="form-label">وصف الصفحة</label>
                                            <textarea class="form-control @error('meta_description') is-invalid @enderror" 
                                                      id="meta_description" name="meta_description" rows="3">{{ old('meta_description', $post->meta_description) }}</textarea>
                                            @error('meta_description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">ملاحظات التعديل</h6>
                                    </div>
                                    <div class="card-body">
                                        <textarea class="form-control" name="revision_notes" rows="3" 
                                                  placeholder="أضف ملاحظات حول هذا التعديل...">{{ old('revision_notes') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> حفظ التعديلات
                                </button>
                                <a href="{{ route('admin.blog.posts.show', $post) }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-right"></i> إلغاء
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto-generate slug from title
    document.getElementById('title').addEventListener('input', function() {
        const slug = this.value.toLowerCase()
            .replace(/[^\w\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .trim('-');
        
        document.getElementById('slug').value = slug;
    });
</script>
@endpush

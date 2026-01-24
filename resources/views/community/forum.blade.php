@extends('layouts.app')

@section('title', 'منتدى السكان')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">منتدى السكان</h1>
            <p class="text-muted mb-0">شارك آراءك وآراء مع جيرانك</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#trendingModal">
                <i class="fas fa-fire me-2"></i>المشاركات الرائجة
            </button>
            <a href="{{ route('resident-forum.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>إنشاء منشور جديد
            </a>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('resident-forum.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">البحث</label>
                        <input type="text" name="search" class="form-control" 
                               value="{{ request('search') }}" placeholder="ابحث في المنشورات...">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">المجتمع</label>
                        <select name="community_id" class="form-select">
                            <option value="">جميع المجتمعات</option>
                            @foreach($communities as $community)
                                <option value="{{ $community->id }}" 
                                        {{ request('community_id') == $community->id ? 'selected' : '' }}>
                                    {{ $community->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">نوع المنشور</label>
                        <select name="post_type" class="form-select">
                            <option value="">جميع الأنواع</option>
                            @foreach($postTypes as $type)
                                <option value="{{ $type }}" 
                                        {{ request('post_type') == $type ? 'selected' : '' }}>
                                    {{ $postTypeLabels[$type] ?? $type }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">الأولوية</label>
                        <select name="priority" class="form-select">
                            <option value="">جميع الأولويات</option>
                            <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>عاجل</option>
                            <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>عالي</option>
                            <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>متوسط</option>
                            <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>منخفض</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Pinned Posts -->
    @if($pinnedPosts->count() > 0)
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-thumbtack me-2"></i>المنشورات المثبتة
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($pinnedPosts as $post)
                        <div class="col-md-6 mb-3">
                            <div class="card border-primary">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="card-title">{{ $post->title }}</h6>
                                        <span class="badge bg-primary">
                                            <i class="fas fa-thumbtack me-1"></i>مثبت
                                        </span>
                                    </div>
                                    <p class="card-text text-muted small">
                                        {{ Str::limit($post->content, 100) }}
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            <i class="fas fa-user me-1"></i>{{ $post->author_name }}
                                        </small>
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-1"></i>{{ $post->published_at_label }}
                                        </small>
                                    </div>
                                    <a href="{{ route('resident-forum.show', $post) }}" 
                                       class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-eye me-1"></i>عرض
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Posts Grid -->
    <div class="row">
        @forelse($posts->chunk(3) as $chunk)
            @foreach($chunk as $post)
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title">{{ $post->title }}</h5>
                                <span class="badge bg-{{ $post->isPinned() ? 'primary' : ($post->isFeatured() ? 'warning' : 'secondary') }}">
                                    @if($post->isPinned())
                                        <i class="fas fa-thumbtack me-1"></i>مثبت
                                    @elseif($post->isFeatured())
                                        <i class="fas fa-star me-1"></i>مميز
                                    @endif
                                </span>
                            </div>
                            
                            <p class="card-text text-muted small mb-3">
                                {{ Str::limit($post->content, 120) }}
                            </p>
                            
                            <div class="mb-2">
                                <small class="text-muted">
                                    <i class="fas fa-user me-1"></i>{{ $post->author_name }}
                                    <span class="ms-2">
                                        <i class="fas fa-tag me-1"></i>{{ $postTypeLabels[$post->post_type] ?? $post->post_type }}
                                    </span>
                                </small>
                            </div>
                            
                            <div class="mb-2">
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i>{{ $post->published_at_label }}
                                </small>
                            </div>
                            
                            <div class="mb-3">
                                <div class="d-flex flex-wrap gap-1">
                                    @if($post->hasImages())
                                        <span class="badge bg-info">
                                            <i class="fas fa-image me-1"></i>صور
                                        </span>
                                    @endif
                                    @if($post->hasVideos())
                                        <span class="badge bg-warning">
                                            <i class="fas fa-video me-1"></i>فيديو
                                        </span>
                                    @endif
                                    @if($post->hasAttachments())
                                        <span class="badge bg-secondary">
                                            <i class="fas fa-paperclip me-1"></i>مرفقات
                                        </span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="d-flex gap-3">
                                    <button class="btn btn-sm btn-outline-primary" 
                                            onclick="likePost({{ $post->id }})">
                                        <i class="fas fa-heart me-1"></i>
                                        <span>{{ $post->like_count }}</span>
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary" 
                                            onclick="commentPost({{ $post->id }})">
                                        <i class="fas fa-comment me-1"></i>
                                        <span>{{ $post->comment_count }}</span>
                                    </button>
                                    <button class="btn btn-sm btn-outline-info" 
                                            onclick="sharePost({{ $post->id }})">
                                        <i class="fas fa-share me-1"></i>
                                        <span>{{ $post->share_count }}</span>
                                    </button>
                                </div>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-eye text-muted me-1"></i>
                                    <small>{{ $post->view_count }}</small>
                                </div>
                            </div>
                            
                            <div class="mt-auto">
                                <div class="d-flex gap-2">
                                    <a href="{{ route('resident-forum.show', $post) }}" 
                                       class="btn btn-outline-primary btn-sm flex-fill">
                                        <i class="fas fa-eye me-1"></i>عرض
                                    </a>
                                    @if(auth()->check())
                                        <a href="{{ route('resident-forum.edit', $post) }}" 
                                           class="btn btn-outline-secondary btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="fas fa-comments fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">لا توجد منشورات متاحة</h4>
                    <p class="text-muted">لم يتم العثور على أي منشورات تطابق معايير البحث الخاصة بك.</p>
                    <a href="{{ route('resident-forum.create') }}" class="btn btn-primary mt-3">
                        <i class="fas fa-plus me-2"></i>إنشاء منشور جديد
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($posts->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $posts->links() }}
        </div>
    @endif
</div>

<!-- Trending Posts Modal -->
<div class="modal fade" id="trendingModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">المشاركات الرائجة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    @foreach($trendingPosts as $post)
                        <div class="col-md-6 mb-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="card-title">{{ $post->title }}</h6>
                                        <div class="d-flex gap-2">
                                            <span class="badge bg-warning">
                                                <i class="fas fa-fire me-1"></i>رائج
                                            </span>
                                            <span class="badge bg-primary">
                                                <i class="fas fa-heart me-1"></i>{{ $post->like_count }}
                                            </span>
                                        </div>
                                    </div>
                                    <p class="card-text text-muted small">
                                        {{ Str::limit($post->content, 80) }}
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            <i class="fas fa-user me-1"></i>{{ $post->author_name }}
                                        </small>
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-1"></i>{{ $post->published_at_label }}
                                        </small>
                                    </div>
                                    <a href="{{ route('resident-forum.show', $post) }}" 
                                       class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-eye me-1"></i>عرض
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Modal -->
<div class="modal fade" id="statisticsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">إحصائيات المنتدى</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <h6 class="card-title">إجمالي المنشورات</h6>
                                <h3 class="text-primary">{{ $stats['total_posts'] }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <h6 class="card-title">المنشورات اليوم</h6>
                                <h3 class="text-success">{{ $stats['today_posts'] }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <h6 class="card-title">إجمالي الإعجابات</h6>
                                <h3 class="text-warning">{{ $stats['total_likes'] }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <h6 class="card-title">إجمالي التعليقات</h6>
                                <h3 class="text-info">{{ $stats['total_comments'] }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title">المنشورات حسب النوع</h6>
                                @foreach($stats['by_type'] as $type => $count)
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span>{{ $postTypeLabels[$type] ?? $type }}</span>
                                        <div class="progress flex-grow-1 mx-3" style="height: 8px;">
                                            <div class="progress-bar bg-primary" 
                                                 style="width: {{ ($count / $stats['total_posts']) * 100 }}%"></div>
                                        </div>
                                        <span class="text-muted">{{ $count }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Like post function
function likePost(postId) {
    fetch(`/resident-forum/${postId}/like`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update like count
            const likeBtn = document.querySelector(`button[onclick="likePost(${postId})"] span`);
            if (likeBtn) {
                likeBtn.textContent = data.like_count;
            }
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('حدث خطأ أثناء الإعجاب');
    });
}

// Comment post function
function commentPost(postId) {
    const comment = prompt('اكتب تعليقك:');
    if (comment) {
        fetch(`/resident-forum/${postId}/comment`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                content: comment
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update comment count
                const commentBtn = document.querySelector(`button[onclick="commentPost(${postId})"] span`);
                if (commentBtn) {
                    commentBtn.textContent = data.comment_count;
                }
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ أثناء التعليق');
        });
    }
}

// Share post function
function sharePost(postId) {
    if (navigator.share) {
        navigator.share({
            title: 'منشور في منتدى السكان',
            text: 'شاهد هذا المنشور المهم في منتدى السكان',
            url: window.location.origin + `/resident-forum/${postId}`
        });
    } else {
        // Fallback for browsers that don't support Web Share API
        const url = window.location.origin + `/resident-forum/${postId}`;
        navigator.clipboard.writeText(url).then(() => {
            alert('تم نسخ الرابط إلى الحافظة!');
        });
    }
}

// Search functionality
document.addEventListener('DOMContentLoaded', function() {
    let searchTimeout;
    const searchInput = document.querySelector('input[name="search"]');
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                this.form.submit();
            }, 500);
        });
    }
    
    // Auto-submit on filter change
    const filters = ['community_id', 'post_type', 'priority'];
    filters.forEach(filterName => {
        const filter = document.querySelector(`select[name="${filterName}"]`);
        if (filter) {
            filter.addEventListener('change', function() {
                this.form.submit();
            });
        }
    });
});
</script>
@endpush

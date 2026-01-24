@extends('layouts.app')

@section('title', 'تقييماتي')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">تقييماتي</h1>
                <div>
                    <a href="{{ route('reviews.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> إضافة تقييم جديد
                    </a>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title">{{ $reviews->count() }}</h4>
                                    <p class="card-text">إجمالي التقييمات</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-star fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title">{{ number_format($averageRating, 1) }}</h4>
                                    <p class="card-text">متوسط التقييم</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-chart-line fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title">{{ $reviews->where('status', 'approved')->count() }}</h4>
                                    <p class="card-text">التقييمات المعتمدة</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-check-circle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title">{{ $reviews->where('status', 'pending')->count() }}</h4>
                                    <p class="card-text">في انتظار الموافقة</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-clock fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('reviews.my-reviews') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="search">بحث</label>
                                <input type="text" class="form-control" id="search" name="search" 
                                       value="{{ request('search') }}" placeholder="بحث في التقييمات...">
                            </div>
                            <div class="col-md-2">
                                <label for="rating">التقييم</label>
                                <select class="form-select" id="rating" name="rating">
                                    <option value="">جميع التقييمات</option>
                                    <option value="5" {{ request('rating') == '5' ? 'selected' : '' }}>5 نجوم</option>
                                    <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>4 نجوم</option>
                                    <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>3 نجوم</option>
                                    <option value="2" {{ request('rating') == '2' ? 'selected' : '' }}>نجمتان</option>
                                    <option value="1" {{ request('rating') == '1' ? 'selected' : '' }}>نجمة واحدة</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="status">الحالة</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">جميع الحالات</option>
                                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>معتمد</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>في انتظار</option>
                                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="type">النوع</label>
                                <select class="form-select" id="type" name="type">
                                    <option value="">جميع الأنواع</option>
                                    <option value="property" {{ request('type') == 'property' ? 'selected' : '' }}>عقار</option>
                                    <option value="agent" {{ request('type') == 'agent' ? 'selected' : '' }}>وكيل</option>
                                    <option value="company" {{ request('type') == 'company' ? 'selected' : '' }}>شركة</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label>&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-outline-primary">
                                        <i class="fas fa-search"></i> بحث
                                    </button>
                                    <a href="{{ route('reviews.my-reviews') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-redo"></i> إعادة تعيين
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Reviews List -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>التقييم</th>
                                    <th>النوع</th>
                                    <th>التقييم</th>
                                    <th>الحالة</th>
                                    <th>التاريخ</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reviews as $review)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($review->reviewable_type === 'App\Models\Property')
                                                    <img src="{{ $review->reviewable->main_image ?? asset('images/default-property.jpg') }}" 
                                                         alt="{{ $review->reviewable->title }}" 
                                                         class="rounded me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                                    <div>
                                                        <div class="fw-bold">{{ $review->reviewable->title }}</div>
                                                        <small class="text-muted">{{ $review->reviewable->location }}</small>
                                                    </div>
                                                @elseif($review->reviewable_type === 'App\Models\Agent')
                                                    <div class="bg-primary rounded me-2 d-flex align-items-center justify-content-center" 
                                                         style="width: 40px; height: 40px;">
                                                        <i class="fas fa-user-tie text-white"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold">{{ $review->reviewable->name }}</div>
                                                        <small class="text-muted">وكيل عقارات</small>
                                                    </div>
                                                @elseif($review->reviewable_type === 'App\Models\Company')
                                                    <div class="bg-secondary rounded me-2 d-flex align-items-center justify-content-center" 
                                                         style="width: 40px; height: 40px;">
                                                        <i class="fas fa-building text-white"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold">{{ $review->reviewable->name }}</div>
                                                        <small class="text-muted">شركة عقارية</small>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            @if($review->reviewable_type === 'App\Models\Property')
                                                <span class="badge bg-info">عقار</span>
                                            @elseif($review->reviewable_type === 'App\Models\Agent')
                                                <span class="badge bg-primary">وكيل</span>
                                            @elseif($review->reviewable_type === 'App\Models\Company')
                                                <span class="badge bg-secondary">شركة</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="me-2">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        @if($i <= $review->rating)
                                                            <i class="fas fa-star text-warning"></i>
                                                        @else
                                                            <i class="far fa-star text-warning"></i>
                                                        @endif
                                                    @endfor
                                                </div>
                                                <span class="badge bg-{{ $review->rating >= 4 ? 'success' : ($review->rating >= 3 ? 'warning' : 'danger') }}">
                                                    {{ $review->rating }}/5
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $review->status == 'approved' ? 'success' : ($review->status == 'rejected' ? 'danger' : 'warning') }}">
                                                {{ $review->status == 'approved' ? 'معتمد' : ($review->status == 'rejected' ? 'مرفوض' : 'في انتظار') }}
                                            </span>
                                        </td>
                                        <td>
                                            <div>
                                                <small>{{ $review->created_at->format('Y-m-d H:i') }}</small>
                                                @if($review->updated_at != $review->created_at)
                                                    <br><small class="text-muted">محدث: {{ $review->updated_at->format('Y-m-d H:i') }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('reviews.show', $review->id) }}" class="btn btn-sm btn-outline-primary" title="عرض">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if($review->canEdit())
                                                    <a href="{{ route('reviews.edit', $review->id) }}" class="btn btn-sm btn-outline-secondary" title="تعديل">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endif
                                                @if($review->responses->count() > 0)
                                                    <button type="button" class="btn btn-sm btn-outline-info" title="الردود" 
                                                            @php $reviewId = $review->id; @endphp
                                                            onclick="showResponses({{ $reviewId }})">
                                                        <i class="fas fa-comments"></i>
                                                    </button>
                                                @endif
                                                @if($review->canDelete())
                                                    <form action="{{ route('reviews.destroy', $review->id) }}" method="POST" class="d-inline" 
                                                          onsubmit="return confirm('هل أنت متأكد من حذف هذا التقييم؟')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="حذف">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">
                                            <div class="py-4">
                                                <i class="fas fa-star fa-3x text-muted mb-3"></i>
                                                <p class="text-muted">لا توجد تقييمات حالياً</p>
                                                <a href="{{ route('reviews.create') }}" class="btn btn-primary">
                                                    <i class="fas fa-plus"></i> إضافة تقييم جديد
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div class="text-muted">
                            عرض {{ $reviews->firstItem() }} - {{ $reviews->lastItem() }} من {{ $reviews->total() }} تقييم
                        </div>
                        {{ $reviews->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Responses Modal -->
<div class="modal fade" id="responsesModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">الردود على التقييم</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="responsesContent">
                <!-- Responses will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
function showResponses(reviewId) {
    fetch(`/reviews/${reviewId}/responses`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('responsesContent').innerHTML = html;
            new bootstrap.Modal(document.getElementById('responsesModal')).show();
        })
        .catch(error => console.error('Error:', error));
}
</script>
@endsection

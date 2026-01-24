@extends('layouts.app')

@section('title', 'دليل الأحياء')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">دليل الأحياء</h1>
            <p class="text-muted mb-0">اكتشف المعلومات الشاملة عن الأحياء والمرافق المحلية</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('neighborhood-guides.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>إضافة دليل جديد
            </a>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('neighborhood-guides.index') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">البحث</label>
                        <input type="text" name="search" class="form-control" 
                               value="{{ request('search') }}" placeholder="ابحث في العناوين والوصف...">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">الحي</label>
                        <select name="neighborhood_id" class="form-select">
                            <option value="">جميع الأحياء</option>
                            @foreach($neighborhoods as $neighborhood)
                                <option value="{{ $neighborhood->id }}" 
                                        {{ request('neighborhood_id') == $neighborhood->id ? 'selected' : '' }}>
                                    {{ $neighborhood->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">نوع الدليل</label>
                        <select name="guide_type" class="form-select">
                            <option value="">جميع الأنواع</option>
                            @foreach($guideTypes as $type)
                                <option value="{{ $type }}" 
                                        {{ request('guide_type') == $type ? 'selected' : '' }}>
                                    {{ $guideTypeLabels[$type] ?? $type }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-outline-primary w-100">
                            <i class="fas fa-search me-2"></i>بحث
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Guides Grid -->
    <div class="row">
        @forelse($guides->chunk(3) as $chunk)
            @foreach($chunk as $guide)
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        @if($guide->hasImages())
                            <img src="{{ $guide->images[0] }}" class="card-img-top" 
                                 style="height: 200px; object-fit: cover;" alt="{{ $guide->title }}">
                        @else
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                                 style="height: 200px;">
                                <i class="fas fa-map-marked-alt fa-3x text-muted"></i>
                            </div>
                        @endif
                        
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title">{{ $guide->title }}</h5>
                                <span class="badge bg-primary">
                                    {{ $guideTypeLabels[$guide->guide_type] ?? $guide->guide_type }}
                                </span>
                            </div>
                            
                            <p class="card-text text-muted small mb-3">
                                {{ Str::limit($guide->description, 100) }}
                            </p>
                            
                            <div class="mb-3">
                                <small class="text-muted">
                                    <i class="fas fa-map-marker-alt me-1"></i>
                                    {{ $guide->neighborhood->name }}
                                </small>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-star text-warning me-1"></i>
                                    <small>{{ $guide->rating }}/5</small>
                                    <span class="text-muted ms-2">({{ $guide->review_count }})</span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-eye text-muted me-1"></i>
                                    <small>{{ $guide->view_count }}</small>
                                </div>
                            </div>
                            
                            <div class="mt-auto">
                                <div class="d-flex gap-2">
                                    <a href="{{ route('neighborhood-guides.show', $guide) }}" 
                                       class="btn btn-outline-primary btn-sm flex-fill">
                                        <i class="fas fa-eye me-1"></i>عرض
                                    </a>
                                    @if(auth()->check())
                                        <a href="{{ route('neighborhood-guides.edit', $guide) }}" 
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
                    <i class="fas fa-map-marked-alt fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">لا توجد أدلة متاحة</h4>
                    <p class="text-muted">لم يتم العثور على أي أدلة تطابق معايير البحث الخاصة بك.</p>
                    <a href="{{ route('neighborhood-guides.create') }}" class="btn btn-primary mt-3">
                        <i class="fas fa-plus me-2"></i>إضافة دليل جديد
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($guides->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $guides->links() }}
        </div>
    @endif
</div>

<!-- Statistics Modal -->
<div class="modal fade" id="statisticsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">إحصائيات الأدلة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title">إجمالي الأدلة</h6>
                                <h3 class="text-primary">{{ $stats['total_guides'] }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title">متوسط التقييم</h6>
                                <h3 class="text-success">{{ number_format($stats['average_rating'], 1) }}/5</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title">الأدلة حسب النوع</h6>
                                @foreach($stats['by_type'] as $type => $count)
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span>{{ $guideTypeLabels[$type] ?? $type }}</span>
                                        <div class="progress flex-grow-1 mx-3" style="height: 8px;">
                                            <div class="progress-bar bg-primary" 
                                                 style="width: {{ ($count / $stats['total_guides']) * 100 }}%"></div>
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
// Search functionality
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit search on input change with debounce
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
    const filters = ['neighborhood_id', 'guide_type'];
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

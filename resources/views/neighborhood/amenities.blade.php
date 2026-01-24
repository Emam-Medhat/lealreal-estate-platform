@extends('layouts.app')

@section('title', 'مرافق المجتمع')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">مرافق المجتمع</h1>
            <p class="text-muted mb-0">استكشف الخدمات والمرافق المتاحة في مجتمعك</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#mapModal">
                <i class="fas fa-map me-2"></i>عرض الخريطة
            </button>
            <a href="{{ route('amenity-maps.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>إضافة مرفق جديد
            </a>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('amenity-maps.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">البحث</label>
                        <input type="text" name="search" class="form-control" 
                               value="{{ request('search') }}" placeholder="ابحث في المرافق...">
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
                        <label class="form-label">نوع المرفق</label>
                        <select name="amenity_type" class="form-select">
                            <option value="">جميع الأنواع</option>
                            @foreach($amenityTypes as $type)
                                <option value="{{ $type }}" 
                                        {{ request('amenity_type') == $type ? 'selected' : '' }}>
                                    {{ $amenityTypeLabels[$type] ?? $type }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-outline-primary w-100">
                            <i class="fas fa-search me-2"></i>بحث
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Amenities Grid -->
    <div class="row">
        @forelse($amenities->chunk(3) as $chunk)
            @foreach($chunk as $amenity)
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        @if($amenity->hasImages())
                            <img src="{{ $amenity->images[0] }}" class="card-img-top" 
                                 style="height: 200px; object-fit: cover;" alt="{{ $amenity->name }}">
                        @else
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                                 style="height: 200px;">
                                <i class="fas fa-building fa-3x text-muted"></i>
                            </div>
                        @endif
                        
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title">{{ $amenity->name }}</h5>
                                <span class="badge bg-{{ $amenity->isActive() ? 'success' : 'secondary' }}">
                                    {{ $amenity->status_label }}
                                </span>
                            </div>
                            
                            <p class="card-text text-muted small mb-2">
                                {{ Str::limit($amenity->description, 80) }}
                            </p>
                            
                            <div class="mb-2">
                                <small class="text-muted">
                                    <i class="fas fa-map-marker-alt me-1"></i>
                                    {{ $amenity->neighborhood->name }}
                                </small>
                            </div>
                            
                            <div class="mb-3">
                                <div class="d-flex flex-wrap gap-1">
                                    @if($amenity->hasAccessibility())
                                        <span class="badge bg-info">
                                            <i class="fas fa-wheelchair me-1"></i>متاح
                                        </span>
                                    @endif
                                    @if($amenity->hasParking())
                                        <span class="badge bg-warning">
                                            <i class="fas fa-parking me-1"></i>موقف سيارات
                                        </span>
                                    @endif
                                    @if($amenity->isFree())
                                        <span class="badge bg-success">
                                            <i class="fas fa-dollar-sign me-1"></i>مجاني
                                        </span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-star text-warning me-1"></i>
                                    <small>{{ $amenity->rating }}/5</small>
                                    <span class="text-muted ms-2">({{ $amenity->review_count }})</span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-eye text-muted me-1"></i>
                                    <small>{{ $amenity->visit_count }}</small>
                                </div>
                            </div>
                            
                            <div class="mt-auto">
                                <div class="d-flex gap-2">
                                    <a href="{{ route('amenity-maps.show', $amenity) }}" 
                                       class="btn btn-outline-primary btn-sm flex-fill">
                                        <i class="fas fa-eye me-1"></i>عرض
                                    </a>
                                    @if($amenity->isOpenNow())
                                        <span class="badge bg-success">
                                            <i class="fas fa-clock me-1"></i>مفتوح
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            <i class="fas fa-clock me-1"></i>مغلق
                                        </span>
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
                    <i class="fas fa-building fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">لا توجد مرافق متاحة</h4>
                    <p class="text-muted">لم يتم العثور على أي مرافق تطابق معايير البحث الخاصة بك.</p>
                    <a href="{{ route('amenity-maps.create') }}" class="btn btn-primary mt-3">
                        <i class="fas fa-plus me-2"></i>إضافة مرفق جديد
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($amenities->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $amenities->links() }}
        </div>
    @endif
</div>

<!-- Map Modal -->
<div class="modal fade" id="mapModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">خريطة المرافق</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="amenityMap" style="height: 500px; width: 100%;"></div>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Modal -->
<div class="modal fade" id="statisticsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">إحصائيات المرافق</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <h6 class="card-title">إجمالي المرافق</h6>
                                <h3 class="text-primary">{{ $stats['total_amenities'] }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <h6 class="card-title">المرفقة النشطة</h6>
                                <h3 class="text-success">{{ $stats['active_amenities'] }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <h6 class="card-title">متوسط التقييم</h6>
                                <h3 class="text-warning">{{ number_format($stats['average_rating'], 1) }}/5</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <h6 class="card-title">إجمالي الزيارات</h6>
                                <h3 class="text-info">{{ $stats['total_visits'] }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title">المرافق حسب النوع</h6>
                                @foreach($stats['by_type'] as $type => $count)
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span>{{ $amenityTypeLabels[$type] ?? $type }}</span>
                                        <div class="progress flex-grow-1 mx-3" style="height: 8px;">
                                            <div class="progress-bar bg-primary" 
                                                 style="width: {{ ($count / $stats['total_amenities']) * 100 }}%"></div>
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
// Initialize map when modal is shown
document.getElementById('mapModal').addEventListener('shown.bs.modal', function () {
    // Initialize map (using Leaflet or similar)
    const map = L.map('amenityMap').setView([24.7136, 46.6753], 13);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);
    
    // Add amenity markers
    @foreach($amenities as $amenity)
        @if($amenity->latitude && $amenity->longitude)
            L.marker([{{ $amenity->latitude }}, {{ $amenity->longitude }}])
                .addTo(map)
                .bindPopup('<strong>{{ $amenity->name }}</strong><br>{{ $amenity->description }}');
        @endif
    @endforeach
});

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
    const filters = ['neighborhood_id', 'amenity_type'];
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

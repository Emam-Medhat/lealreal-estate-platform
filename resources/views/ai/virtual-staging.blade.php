@extends('layouts.app')

@section('title', 'التنسيق الافتراضي بالذكاء الاصطناعي')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-magic me-2"></i>
            التنسيق الافتراضي بالذكاء الاصطناعي
        </h1>
        <div>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newStagingModal">
                <i class="fas fa-plus me-2"></i>تنسيق جديد
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ $stats['total_stagings'] ?? 0 }}</h4>
                            <p class="card-text">إجمالي التنسيقات</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-images fa-2x opacity-75"></i>
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
                            <h4 class="card-title">{{ $stats['published_stagings'] ?? 0 }}</h4>
                            <p class="card-text">التنسيقات المنشورة</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-eye fa-2x opacity-75"></i>
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
                            <h4 class="card-title">{{ $stats['avg_quality_score'] ?? 0 }}</h4>
                            <p class="card-text">متوسط جودة التنسيق</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-star fa-2x opacity-75"></i>
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
                            <h4 class="card-title">{{ $stats['total_views'] ?? 0 }}</h4>
                            <p class="card-text">إجمالي المشاهدات</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-chart-bar fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('ai.virtual-staging.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">بحث</label>
                        <input type="text" name="search" class="form-control" placeholder="ابحث عن تنسيق..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">نوع الغرفة</label>
                        <select name="room_type" class="form-select">
                            <option value="">الكل</option>
                            <option value="living_room" {{ request('room_type') == 'living_room' ? 'selected' : '' }}>غرفة معيشة</option>
                            <option value="bedroom" {{ request('room_type') == 'bedroom' ? 'selected' : '' }}>غرفة نوم</option>
                            <option value="kitchen" {{ request('room_type') == 'kitchen' ? 'selected' : '' }}>مطبخ</option>
                            <option value="bathroom" {{ request('room_type') == 'bathroom' ? 'selected' : '' }}>حمام</option>
                            <option value="office" {{ request('room_type') == 'office' ? 'selected' : '' }}>مكتب</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">نمط التنسيق</label>
                        <select name="staging_style" class="form-select">
                            <option value="">الكل</option>
                            <option value="modern" {{ request('staging_style') == 'modern' ? 'selected' : '' }}>عصري</option>
                            <option value="contemporary" {{ request('staging_style') == 'contemporary' ? 'selected' : '' }}>معاصر</option>
                            <option value="traditional" {{ request('staging_style') == 'traditional' ? 'selected' : '' }}>تقليدي</option>
                            <option value="minimalist" {{ request('staging_style') == 'minimalist' ? 'selected' : '' }}>بسيط</option>
                            <option value="luxury" {{ request('staging_style') == 'luxury' ? 'selected' : '' }}>فاخر</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">الحالة</label>
                        <select name="status" class="form-select">
                            <option value="">الكل</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                            <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>قيد المعالجة</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                            <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>منشور</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="fas fa-search me-1"></i>بحث
                            </button>
                            <a href="{{ route('ai.virtual-staging.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>مسح
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Stagings Grid -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">معرض التنسيقات</h5>
        </div>
        <div class="card-body">
            <div class="row">
                @forelse ($stagings ?? [] as $staging)
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100">
                            <div class="position-relative">
                                @if ($staging->staged_image_path)
                                    <img src="{{ asset($staging->staged_image_path) }}" class="card-img-top" alt="Staged Image" style="height: 200px; object-fit: cover;">
                                @else
                                    <img src="{{ asset($staging->original_image_path) }}" class="card-img-top" alt="Original Image" style="height: 200px; object-fit: cover;">
                                @endif
                                
                                @if ($staging->is_published)
                                    <span class="position-absolute top-0 end-0 m-2 badge bg-success">منشور</span>
                                @endif
                            </div>
                            <div class="card-body">
                                <h6 class="card-title">{{ $staging->room_type_label }}</h6>
                                <p class="card-text text-muted small">{{ $staging->staging_style_label }}</p>
                                
                                <div class="row text-center mb-2">
                                    <div class="col-4">
                                        <small class="d-block text-muted">الجودة</small>
                                        <strong>{{ $staging->quality_score }}</strong>
                                    </div>
                                    <div class="col-4">
                                        <small class="d-block text-muted">الواقعية</small>
                                        <strong>{{ $staging->realism_score }}</strong>
                                    </div>
                                    <div class="col-4">
                                        <small class="d-block text-muted">المشاهدات</small>
                                        <strong>{{ $staging->view_count }}</strong>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="badge bg-{{ $staging->overall_score >= 8 ? 'success' : ($staging->overall_score >= 6 ? 'warning' : 'secondary') }}">
                                            {{ $staging->overall_level }}
                                        </span>
                                    </div>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="showStagingDetails({{ $staging->id }})">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-success" onclick="compareImages({{ $staging->id }})">
                                            <i class="fas fa-columns"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-4">
                        <i class="fas fa-images fa-3x text-muted mb-3"></i>
                        <p class="text-muted">لا توجد تنسيقات حالياً</p>
                    </div>
                @endforelse
            </div>
            
            <!-- Pagination -->
            @if (isset($stagings) && $stagings->hasPages())
                <div class="d-flex justify-content-center mt-3">
                    {{ $stagings->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- New Staging Modal -->
<div class="modal fade" id="newStagingModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تنسيق افتراضي جديد</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="newStagingForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">رقم العقار *</label>
                            <select name="property_id" class="form-select" required>
                                <option value="">اختر العقار</option>
                                @foreach ($properties ?? [] as $property)
                                    <option value="{{ $property->id }}">{{ $property->id }} - {{ $property->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">نوع الغرفة *</label>
                            <select name="room_type" class="form-select" required>
                                <option value="living_room">غرفة معيشة</option>
                                <option value="bedroom">غرفة نوم</option>
                                <option value="kitchen">مطبخ</option>
                                <option value="dining_room">غرفة طعام</option>
                                <option value="bathroom">حمام</option>
                                <option value="office">مكتب</option>
                                <option value="outdoor">فضاء خارجي</option>
                                <option value="entryway">مدخل</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">نمط التنسيق *</label>
                            <select name="staging_style" class="form-select" required>
                                <option value="modern">عصري</option>
                                <option value="contemporary">معاصر</option>
                                <option value="traditional">تقليدي</option>
                                <option value="minimalist">بسيط</option>
                                <option value="luxury">فاخر</option>
                                <option value="scandinavian">إسكندنافي</option>
                                <option value="industrial">صناعي</option>
                                <option value="bohemian">بوهيمي</option>
                                <option value="coastal">ساحلي</option>
                                <option value="farmhouse">ريفي</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">نمط الأثاث *</label>
                            <select name="furniture_style" class="form-select" required>
                                <option value="modern">عصري</option>
                                <option value="classic">كلاسيكي</option>
                                <option value="vintage">عتيق</option>
                                <option value="contemporary">معاصر</option>
                                <option value="industrial">صناعي</option>
                                <option value="scandinavian">إسكندنافي</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">مخطط الألوان *</label>
                            <select name="color_scheme" class="form-select" required>
                                <option value="neutral">محايد</option>
                                <option value="warm">دافئ</option>
                                <option value="cool">بارد</option>
                                <option value="monochromatic">أحادي اللون</option>
                                <option value="complementary">مكمل</option>
                                <option value="analogous">متشابه</option>
                                <option value="triadic">ثلاثي</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">الجمهور المستهدف *</label>
                            <select name="target_audience" class="form-select" required>
                                <option value="families">عائلات</option>
                                <option value="young_professionals">شباب محترفين</option>
                                <option value="retirees">متقاعدين</option>
                                <option value="investors">مستثمرين</option>
                                <option value="students">طلاب</option>
                                <option value="luxury_buyers">مشترين فاخرين</option>
                                <option value="first_time_buyers">مشترين لأول مرة</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">الصورة الأصلية *</label>
                            <input type="file" name="original_image" class="form-control" accept="image/*" required>
                            <small class="text-muted">الصيغ المسموحة: JPG, PNG, GIF (الحد الأقصى: 10MB)</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-magic me-2"></i>بدء التنسيق
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Staging Details Modal -->
<div class="modal fade" id="stagingDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تفاصيل التنسيق</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="stagingDetailsContent">
                    <!-- Content will be loaded via AJAX -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                <button type="button" class="btn btn-primary" onclick="downloadStaging()">
                    <i class="fas fa-download me-2"></i>تحميل الصورة
                </button>
                <button type="button" class="btn btn-success" onclick="publishStaging()">
                    <i class="fas fa-globe me-2"></i>نشر التنسيق
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Image Comparison Modal -->
<div class="modal fade" id="imageComparisonModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">مقارنة الصور</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-center mb-3">الصورة الأصلية</h6>
                        <img id="originalImage" src="" class="img-fluid" alt="Original Image">
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-center mb-3">الصورة المنسقة</h6>
                        <img id="stagedImage" src="" class="img-fluid" alt="Staged Image">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentStagingId = null;

// New Staging Form
document.getElementById('newStagingForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>جاري التنسيق...';
    
    fetch('{{ route("ai.virtual-staging.store") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'نجح!',
                text: data.message,
                confirmButtonText: 'موافق'
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'خطأ!',
                text: data.message,
                confirmButtonText: 'موافق'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'خطأ!',
            text: 'حدث خطأ أثناء التنسيق',
            confirmButtonText: 'موافق'
        });
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
});

// Show Staging Details
function showStagingDetails(stagingId) {
    fetch(`/ai/virtual-staging/${stagingId}`)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            currentStagingId = stagingId;
            displayStagingDetails(data.staging);
            new bootstrap.Modal(document.getElementById('stagingDetailsModal')).show();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'خطأ!',
            text: 'حدث خطأ أثناء تحميل التفاصيل',
            confirmButtonText: 'موافق'
        });
    });
}

// Display Staging Details
function displayStagingDetails(staging) {
    const content = document.getElementById('stagingDetailsContent');
    
    content.innerHTML = `
        <div class="row">
            <div class="col-md-6">
                <h6>معلومات أساسية</h6>
                <table class="table table-sm">
                    <tr>
                        <td><strong>رقم العقار:</strong></td>
                        <td>${staging.property_id}</td>
                    </tr>
                    <tr>
                        <td><strong>نوع الغرفة:</strong></td>
                        <td>${staging.room_type_label}</td>
                    </tr>
                    <tr>
                        <td><strong>نمط التنسيق:</strong></td>
                        <td>${staging.staging_style_label}</td>
                    </tr>
                    <tr>
                        <td><strong>نمط الأثاث:</strong></td>
                        <td>${staging.furniture_style_label}</td>
                    </tr>
                    <tr>
                        <td><strong>مخطط الألوان:</strong></td>
                        <td>${staging.color_scheme_label}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6>تقييم الجودة</h6>
                <table class="table table-sm">
                    <tr>
                        <td><strong>الجودة العامة:</strong></td>
                        <td>${staging.quality_score} (${staging.quality_level})</td>
                    </tr>
                    <tr>
                        <td><strong>الواقعية:</strong></td>
                        <td>${staging.realism_score} (${staging.realism_level})</td>
                    </tr>
                    <tr>
                        <td><strong>الجاذبية:</strong></td>
                        <td>${staging.aesthetic_appeal} (${staging.aesthetic_level})</td>
                    </tr>
                    <tr>
                        <td><strong>التناسق:</strong></td>
                        <td>${staging.style_consistency} (${staging.style_consistency_level})</td>
                    </tr>
                    <tr>
                        <td><strong>الجاذبية السوقية:</strong></td>
                        <td>${staging.market_appeal} (${staging.market_appeal_level})</td>
                    </tr>
                </table>
            </div>
        </div>
        
        <div class="row mt-3">
            <div class="col-12">
                <h6>عناصر التصميم</h6>
                <div class="row">
                    <div class="col-md-6">
                        <strong>عناصر الأثاث (${staging.furniture_count}):</strong>
                        <ul class="list-unstyled">
                            ${(staging.furniture_items || []).map(item => `<li>• ${item.item} (${item.style})</li>`).join('')}
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <strong>عناصر الديكور (${staging.decor_count}):</strong>
                        <ul class="list-unstyled">
                            ${(staging.decor_elements || []).map(item => `<li>• ${item.item} (${item.style})</li>`).join('')}
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    `;
}

// Compare Images
function compareImages(stagingId) {
    fetch(`/ai/virtual-staging/${stagingId}/compare`)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('originalImage').src = data.original_image;
            document.getElementById('stagedImage').src = data.staged_image;
            new bootstrap.Modal(document.getElementById('imageComparisonModal')).show();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'خطأ!',
            text: 'حدث خطأ أثناء تحميل الصور',
            confirmButtonText: 'موافق'
        });
    });
}

// Download Staging
function downloadStaging() {
    if (!currentStagingId) {
        Swal.fire({
            icon: 'warning',
            title: 'تنبيه!',
            text: 'يرجى اختيار تنسيق أولاً',
            confirmButtonText: 'موافق'
        });
        return;
    }
    
    window.open(`/ai/virtual-staging/${currentStagingId}/download`, '_blank');
}

// Publish Staging
function publishStaging() {
    if (!currentStagingId) {
        Swal.fire({
            icon: 'warning',
            title: 'تنبيه!',
            text: 'يرجى اختيار تنسيق أولاً',
            confirmButtonText: 'موافق'
        });
        return;
    }
    
    fetch(`/ai/virtual-staging/${currentStagingId}/publish`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'نجح!',
                text: data.message,
                confirmButtonText: 'موافق'
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'خطأ!',
                text: data.message,
                confirmButtonText: 'موافق'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'خطأ!',
            text: 'حدث خطأ أثناء النشر',
            confirmButtonText: 'موافق'
        });
    });
}
</script>
@endpush

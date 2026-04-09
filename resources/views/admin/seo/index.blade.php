@extends('admin.layouts.admin')

@section('title', 'إدارة تحسين محركات البحث')

@push('styles')
<style>
    .seo-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem;
        border-radius: 15px;
        margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    
    .seo-kpi-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        overflow: hidden;
        background: linear-gradient(180deg, #f8f9ff 0%, #e8eaff 100%);
    }
    
    .seo-kpi-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.15);
    }
    
    .seo-kpi-card.primary {
        border-left: 6px solid #667eea;
    }
    
    .seo-kpi-card.warning {
        border-left: 6px solid #f59e0b;
        background: linear-gradient(180deg, #fff7ed 0%, #fffbeb 100%);
    }
    
    .seo-kpi-card.info {
        border-left: 6px solid #06b6d4;
    }
    
    .seo-kpi-card.success {
        border-left: 6px solid #10b981;
    }
    
    .seo-tool-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        overflow: hidden;
    }
    
    .seo-tool-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.15);
    }
    
    .seo-tool-card .card-header {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        border: none;
        color: white;
        font-weight: 600;
        padding: 1.2rem;
    }
    
    .seo-table-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        overflow: hidden;
    }
    
    .seo-table-card .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
        font-weight: 600;
        padding: 1.2rem;
    }
    
    .seo-table {
        border-radius: 10px;
        overflow: hidden;
    }
    
    .seo-table thead th {
        background: linear-gradient(135deg, #f8f9ff 0%, #e8eaff 100%);
        color: #495057;
        border: none;
        padding: 1rem;
        font-weight: 600;
    }
    
    .seo-table tbody tr {
        transition: background-color 0.3s ease;
    }
    
    .seo-table tbody tr:hover {
        background-color: #f8f9ff;
    }
    
    .btn-primary-custom {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 25px;
        padding: 0.75rem 1.5rem;
        color: white;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-primary-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
    }
    
    .btn-secondary-custom {
        background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
        border: none;
        border-radius: 25px;
        padding: 0.75rem 1.5rem;
        color: white;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-secondary-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(108, 117, 125, 0.3);
    }
    
    .keyword-tag {
        display: inline-block;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 15px;
        font-size: 0.75rem;
        font-weight: 600;
        margin: 0.25rem;
        transition: transform 0.2s ease;
    }
    
    .keyword-tag:hover {
        transform: scale(1.1);
    }
    
    .progress {
        background-color: #e9ecef;
        border-radius: 10px;
        overflow: hidden;
    }
    
    .progress-bar {
        border-radius: 10px;
        transition: width 1s ease;
    }
    
    .progress-bar.success {
        background: linear-gradient(90deg, #10b981 0%, #059669 100%);
    }
    
    .progress-bar.warning {
        background: linear-gradient(90deg, #f59e0b 0%, #d97706 100%);
    }
    
    .progress-bar.danger {
        background: linear-gradient(90deg, #ef4444 0%, #dc2626 100%);
    }
    
    .icon-circle {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
    
    .icon-circle.primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .icon-circle.warning {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
    }
    
    .icon-circle.info {
        background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
        color: white;
    }
    
    .icon-circle.success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
    }
    
    .empty-state {
        padding: 3rem;
        text-align: center;
        background: linear-gradient(135deg, #f8f9ff 0%, #e8eaff 100%);
        border-radius: 15px;
    }
    
    .empty-state i {
        color: #667eea;
        margin-bottom: 1rem;
    }
    
    .fade-in {
        animation: fadeIn 0.5s ease-in;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .badge-custom {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 25px;
        font-weight: 600;
    }
    
    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    
    .stats-number {
        font-size: 2rem;
        font-weight: 700;
        color: #495057;
    }
    
    .stats-label {
        font-size: 0.875rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
</style>
@endpush
    @section('content')
<div class="container-fluid fade-in">
    <!-- Page Header -->
    <div class="seo-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="mb-3">
                    <i class="fas fa-search ml-3"></i>
                    إدارة تحسين محركات البحث
                </h1>
                <p class="mb-0 opacity-90">إدارة إعدادات السيو والوسوم الوصفية لموقعك</p>
            </div>
            <div class="col-md-4 text-md-left">
                <form action="{{ route('admin.seo.generate-sitemap') }}" method="POST" style="display: inline;" onsubmit="return confirm('هل أنت متأكد من توليد خريطة الموقع؟')">
                    @csrf
                    <button type="submit" class="btn btn-secondary-custom btn-lg ml-2">
                        <i class="fas fa-sitemap ml-2"></i> توليد خريطة الموقع
                    </button>
                </form>
                <a href="{{ route('admin.seo.create') }}" class="btn btn-primary-custom btn-lg">
                    <i class="fas fa-plus ml-2"></i> إضافة وسم ميتا
                </a>
            </div>
        </div>
    </div>

    <!-- SEO Overview Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card seo-kpi-card primary h-100 py-3">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="stats-label text-primary mb-1">
                                <i class="fas fa-chart-line ml-2"></i>الصفحات المُحسّنة
                            </div>
                            <div class="stats-number">{{ $stats['optimized_pages'] ?? 0 }}</div>
                            <div class="text-xs text-success mt-2">
                                <i class="fas fa-arrow-up ml-2"></i>{{ $stats['optimization_score'] ?? 0 }}% تقييم
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="icon-circle primary">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card seo-kpi-card warning h-100 py-3">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="stats-label text-warning mb-1">
                                <i class="fas fa-exclamation-triangle ml-2"></i>المشاكل المكتشفة
                            </div>
                            <div class="stats-number">{{ $stats['issues'] ?? 0 }}</div>
                            <div class="text-xs text-danger mt-2">
                                <i class="fas fa-arrow-down ml-2"></i>{{ $stats['critical_issues'] ?? 0 }} حرجة
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="icon-circle warning">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card seo-kpi-card info h-100 py-3">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="stats-label text-info mb-1">
                                <i class="fas fa-key ml-2"></i>الكلمات المفتاحية
                            </div>
                            <div class="stats-number">{{ $stats['keywords'] ?? 0 }}</div>
                            <div class="text-xs text-info mt-2">
                                <i class="fas fa-star ml-2"></i>{{ $stats['top_keyword'] ?? 'غير متوفر' }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="icon-circle info">
                                <i class="fas fa-key"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card seo-kpi-card success h-100 py-3">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="stats-label text-success mb-1">
                                <i class="fas fa-tachometer-alt ml-2"></i>سرعة الصفحة
                            </div>
                            <div class="stats-number">{{ $stats['page_speed'] ?? 0 }}</div>
                            <div class="text-xs text-success mt-2">
                                <i class="fas fa-check ml-2"></i>متوسط التقييم
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="icon-circle success">
                                <i class="fas fa-tachometer-alt"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SEO Tools -->
    <div class="row mb-4">
        <!-- SEO Analysis -->
        <div class="col-lg-6 mb-4">
            <div class="card seo-tool-card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-search ml-2"></i>
                        تحليل السيو
                        <span class="badge-custom float-end">Live</span>
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <label for="analyze-url" class="form-label font-weight-bold">
                            <i class="fas fa-link ml-2"></i>تحليل رابط
                        </label>
                        <div class="input-group input-group-lg">
                            <input type="url" id="analyze-url" placeholder="https://example.com/page" 
                                class="form-control">
                            <button onclick="analyzeUrl()" class="btn btn-primary-custom">
                                <i class="fas fa-search ml-2"></i>تحليل
                            </button>
                        </div>
                        <small class="text-muted mt-2 d-block">أدخل رابطًا لتحليل أداء السيو الخاص به</small>
                    </div>
                    
                    <div id="analysis-results" class="alert alert-info" style="display: none;">
                        <h6 class="alert-heading">
                            <i class="fas fa-chart-bar ml-2"></i>نتائج التحليل
                        </h6>
                        <div class="row mt-3">
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="icon-circle primary p-2 ml-3">
                                        <i class="fas fa-heading text-white"></i>
                                    </div>
                                    <div>
                                        <small class="d-block text-muted">طول العنوان</small>
                                        <strong id="title-length" class="text-primary">-</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="icon-circle success p-2 ml-3">
                                        <i class="fas fa-align-left text-white"></i>
                                    </div>
                                    <div>
                                        <small class="d-block text-muted">الوصف التعريفي</small>
                                        <strong id="meta-description" class="text-success">-</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="icon-circle warning p-2 ml-3">
                                        <i class="fas fa-h1 text-white"></i>
                                    </div>
                                    <div>
                                        <small class="d-block text-muted">وسوم H1</small>
                                        <strong id="h1-tags" class="text-warning">-</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="icon-circle info p-2 ml-3">
                                        <i class="fas fa-image text-white"></i>
                                    </div>
                                    <div>
                                        <small class="d-block text-muted">وسوم الصور</small>
                                        <strong id="image-alt" class="text-info">-</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Keyword Research -->
        <div class="col-lg-6 mb-4">
            <div class="card seo-tool-card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-key ml-2"></i>
                        بحث الكلمات المفتاحية
                        <span class="badge-custom float-end">Beta</span>
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <label for="seed-keyword" class="form-label font-weight-bold">
                            <i class="fas fa-search ml-2"></i>الكلمة الرئيسية
                        </label>
                        <div class="input-group input-group-lg">
                            <input type="text" id="seed-keyword" placeholder="اكتب الكلمة الرئيسية..." 
                                class="form-control">
                            <button onclick="researchKeywords()" class="btn btn-primary-custom">
                                <i class="fas fa-search ml-2"></i>بحث
                            </button>
                        </div>
                        <small class="text-muted mt-2 d-block">اكتشف الكلمات المرتبطة وأحجام البحث</small>
                    </div>
                    
                    <div id="keyword-results" style="display: none;">
                        <h6 class="font-weight-bold mb-3">
                            <i class="fas fa-list ml-2"></i>الكلمات المرتبطة
                        </h6>
                        <div class="list-group" style="max-height: 250px; overflow-y: auto;">
                            <!-- Keywords will be populated here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SEO Meta Data Table -->
    <div class="card seo-table-card mb-4">
        <div class="card-header">
            <h6 class="mb-0">
                <i class="fas fa-table ml-2"></i>
                بيانات SEO Meta
                <span class="badge-custom float-end">{{ $seoMetas->total() ?? 0 }} إجمالي</span>
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table seo-table" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>
                                <i class="fas fa-file-alt ml-2"></i>الصفحة
                            </th>
                            <th>
                                <i class="fas fa-heading ml-2"></i>العنوان
                            </th>
                            <th>
                                <i class="fas fa-align-left ml-2"></i>الوصف
                            </th>
                            <th>
                                <i class="fas fa-key ml-2"></i>الكلمات
                            </th>
                            <th class="text-center">
                                <i class="fas fa-chart-line ml-2"></i>التقييم
                            </th>
                            <th class="text-center">
                                <i class="fas fa-cogs ml-2"></i>الإجراءات
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($seoMetas ?? [] as $meta)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-start">
                                        <div>
                                            <div class="font-weight-bold text-primary">{{ class_basename($meta->model_type) }}</div>
                                            <small class="text-muted d-block">ID: {{ $meta->model_id }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-truncate" style="max-width: 200px;" title="{{ $meta->meta_title }}">
                                        {{ $meta->meta_title ?? '-' }}
                                    </div>
                                </td>
                                <td>
                                    <div class="text-truncate" style="max-width: 250px;" title="{{ $meta->meta_description }}">
                                        {{ $meta->meta_description ?? '-' }}
                                    </div>
                                </td>
                                <td>
                                    @if($meta->meta_keywords)
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach(explode(',', $meta->meta_keywords) as $keyword)
                                                @if(trim($keyword))
                                                    <span class="keyword-tag">{{ trim($keyword) }}</span>
                                                @endif
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-inline-flex align-items-center">
                                        <div class="progress" style="height: 8px; width: 80px;">
                                            <div class="progress-bar 
                                                {{ ($meta->seo_score ?? 0) >= 80 ? 'success' : 
                                                   (($meta->seo_score ?? 0) >= 60 ? 'warning' : 'danger') }}"
                                                role="progressbar" 
                                                style="width: {{ $meta->seo_score ?? 0 }}%">
                                            </div>
                                        </div>
                                        <span class="ml-2 font-weight-bold 
                                            {{ ($meta->seo_score ?? 0) >= 80 ? 'text-success' : 
                                                   (($meta->seo_score ?? 0) >= 60 ? 'text-warning' : 'text-danger') }}">
                                            {{ $meta->seo_score ?? 0 }}%
                                        </span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.seo.show', ['seoMeta' => $meta->id]) }}" 
                                           class="btn btn-info btn-sm" 
                                           title="عرض التفاصيل"
                                           data-toggle="tooltip">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.seo.edit', ['seoMeta' => $meta->id]) }}" 
                                           class="btn btn-warning btn-sm" 
                                           title="تعديل"
                                           data-toggle="tooltip">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.seo.destroy', ['seoMeta' => $meta->id]) }}" 
                                              method="POST" 
                                              style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-danger btn-sm" 
                                                    title="حذف"
                                                    data-toggle="tooltip"
                                                    onclick="return confirm('هل أنت متأكد من حذف بيانات الميتا هذه؟')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-8">
                                    <div class="empty-state">
                                        <i class="fas fa-inbox fa-4x mb-3"></i>
                                        <h4 class="text-gray-700 mb-3">لا توجد بيانات سيو</h4>
                                        <p class="text-gray-600 mb-4">ابدأ بإضافة بيانات الميتا إلى صفحاتك</p>
                                        <a href="{{ route('admin.seo.create') }}" class="btn btn-primary-custom btn-lg">
                                            <i class="fas fa-plus ml-2"></i>إضافة وسم ميتا
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Enhanced Pagination -->
            @if (isset($seoMetas) && $seoMetas->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div>
                        <small class="text-muted">
                            <i class="fas fa-info-circle ml-2"></i>
                            عرض {{ $seoMetas->firstItem() ?? 0 }} إلى {{ $seoMetas->lastItem() ?? 0 }} 
                            من أصل {{ $seoMetas->total() ?? 0 }} سجل
                        </small>
                    </div>
                    <div>
                        {{ $seoMetas->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function analyzeUrl() {
    const url = document.getElementById('analyze-url').value;
    if (!url) {
        alert('يرجى إدخال رابط للتحليل');
        return;
    }

    fetch('{{ route('admin.seo.analyze') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ url: url })
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('analysis-results').style.display = 'block';
        document.getElementById('title-length').textContent = (data.title || '').length + ' حرف';
        document.getElementById('meta-description').textContent = data.description ? 'موجودة' : 'مفقودة';
        document.getElementById('h1-tags').textContent = (data.h1_count || 0) + ' موجود';
        document.getElementById('image-alt').textContent = (data.image_alt_count || 0) + '/' + (data.total_images || 0);
    })
    .catch(error => {
        console.error('Error:', error);
        alert('حدث خطأ أثناء التحليل');
    });
}

function researchKeywords() {
    const keyword = document.getElementById('seed-keyword').value;
    if (!keyword) {
        alert('يرجى إدخال كلمة رئيسية');
        return;
    }

    // Mock keyword research - in real app, this would call an API
    const mockKeywords = [
        { keyword: keyword + ' guide', volume: '1,000', difficulty: 'Low' },
        { keyword: keyword + ' tutorial', volume: '800', difficulty: 'Low' },
        { keyword: keyword + ' tips', volume: '600', difficulty: 'Medium' },
        { keyword: keyword + ' best practices', volume: '400', difficulty: 'Medium' }
    ];

    const resultsDiv = document.getElementById('keyword-results');
    resultsDiv.style.display = 'block';
    
    const keywordsHtml = mockKeywords.map(k => `
        <div class="list-group-item d-flex justify-content-between align-items-center">
            <div>
                <div class="font-weight-bold">${k.keyword}</div>
                <small class="text-muted">Volume: ${k.volume} | Difficulty: ${k.difficulty}</small>
            </div>
            <button class="btn btn-sm btn-outline-primary">استخدام</button>
        </div>
    `).join('');
    
    resultsDiv.querySelector('.list-group').innerHTML = keywordsHtml;
}
</script>
@endsection

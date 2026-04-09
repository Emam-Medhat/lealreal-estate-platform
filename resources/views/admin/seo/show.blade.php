@extends('admin.layouts.admin')

@section('title', 'تفاصيل SEO')

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
    
    .seo-detail-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        overflow: hidden;
    }
    
    .seo-detail-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.15);
    }
    
    .seo-detail-card .card-header {
        border: none;
        color: white;
        font-weight: 600;
        padding: 1.2rem;
    }
    
    .seo-detail-card.primary .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .seo-detail-card.success .card-header {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }
    
    .seo-detail-card.info .card-header {
        background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
    }
    
    .seo-detail-card.warning .card-header {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    }
    
    .seo-detail-card.danger .card-header {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    }
    
    .info-table {
        border-radius: 10px;
        overflow: hidden;
    }
    
    .info-table tbody tr {
        transition: background-color 0.3s ease;
    }
    
    .info-table tbody tr:hover {
        background-color: #f8f9ff;
    }
    
    .info-table td {
        padding: 1rem;
        vertical-align: middle;
    }
    
    .info-table td:first-child {
        font-weight: 600;
        color: #495057;
        width: 30%;
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
    
    .badge-custom {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 25px;
        font-weight: 600;
    }
    
    .badge-custom.primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .badge-custom.info {
        background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
    }
    
    .badge-custom.success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }
    
    .badge-custom.warning {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    }
    
    .badge-custom.dark {
        background: linear-gradient(135deg, #374151 0%, #1f2937 100%);
    }
    
    .content-box {
        background: linear-gradient(180deg, #f8f9ff 0%, #e8eaff 100%);
        padding: 1rem;
        border-radius: 10px;
        border: 1px solid #e9ecef;
    }
    
    .google-preview {
        background: white;
        border: 1px solid #e9ecef;
        border-radius: 10px;
        padding: 1rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
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
    
    .btn-warning-custom {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        border: none;
        border-radius: 25px;
        padding: 0.75rem 1.5rem;
        color: white;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-warning-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(245, 158, 11, 0.3);
    }
    
    .code-block {
        background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
        color: #f9fafb;
        padding: 1.5rem;
        border-radius: 10px;
        font-family: 'Courier New', monospace;
        font-size: 0.875rem;
        max-height: 300px;
        overflow-y: auto;
        direction: ltr;
        text-align: left;
    }
    
    .fade-in {
        animation: fadeIn 0.5s ease-in;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .score-display {
        font-size: 3rem;
        font-weight: 700;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .icon-accent {
        color: #667eea;
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
                    تفاصيل SEO
                </h1>
                <p class="mb-0 opacity-90">عرض وتحليل بيانات تحسين محركات البحث</p>
            </div>
            <div class="col-md-4 text-md-left">
                <a href="{{ route('admin.seo.edit', ['seoMeta' => $seoMeta->id]) }}" class="btn btn-warning-custom btn-lg ml-2">
                    <i class="fas fa-edit ml-2"></i> تعديل
                </a>
                <a href="{{ route('admin.seo.index') }}" class="btn btn-secondary-custom btn-lg">
                    <i class="fas fa-arrow-left ml-2"></i> العودة
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Info -->
        <div class="col-lg-8">
            <div class="card seo-detail-card primary mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle ml-2"></i>
                        المعلومات الأساسية
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table info-table">
                            <tbody>
                                <tr>
                                    <td>
                                        <i class="fas fa-file-alt icon-accent ml-2"></i>نوع الصفحة
                                    </td>
                                    <td>
                                        <span class="badge-custom primary">{{ class_basename($seoMeta->model_type) }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <i class="fas fa-hashtag icon-accent ml-2"></i>المعرف (ID)
                                    </td>
                                    <td>
                                        <span class="badge-custom info">{{ $seoMeta->model_id }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <i class="fas fa-heading icon-accent ml-2"></i>عنوان الصفحة
                                    </td>
                                    <td>
                                        @if($seoMeta->meta_title)
                                            <div class="content-box">
                                                <strong>{{ $seoMeta->meta_title }}</strong>
                                                <small class="text-muted d-block">{{ strlen($seoMeta->meta_title) }}/60 حرف</small>
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <i class="fas fa-align-left icon-accent ml-2"></i>الوصف
                                    </td>
                                    <td>
                                        @if($seoMeta->meta_description)
                                            <div class="content-box">
                                                <p class="mb-1">{{ $seoMeta->meta_description }}</p>
                                                <small class="text-muted">{{ strlen($seoMeta->meta_description) }}/160 حرف</small>
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <i class="fas fa-key icon-accent ml-2"></i>الكلمات المفتاحية
                                    </td>
                                    <td>
                                        @if($seoMeta->meta_keywords)
                                            <div class="keywords-container">
                                                @foreach(explode(',', $seoMeta->meta_keywords) as $keyword)
                                                    @if(trim($keyword))
                                                        <span class="keyword-tag">{{ trim($keyword) }}</span>
                                                    @endif
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <i class="fas fa-link icon-accent ml-2"></i>رابط Canonical
                                    </td>
                                    <td>
                                        @if($seoMeta->canonical_url)
                                            <a href="{{ $seoMeta->canonical_url }}" target="_blank" class="text-primary">
                                                <i class="fas fa-external-link-alt ml-1"></i>{{ $seoMeta->canonical_url }}
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <i class="fas fa-robot icon-accent ml-2"></i>إعدادات Robots
                                    </td>
                                    <td>
                                        <span class="badge-custom dark">{{ $seoMeta->robots ?? 'index, follow' }}</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Structured Data -->
            <div class="card seo-detail-card success mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-code ml-2"></i>
                        البيانات الهيكلية (Structured Data)
                    </h6>
                    @if($seoMeta->structured_data)
                        <button class="btn btn-sm btn-outline-light float-end" onclick="copyStructuredData()">
                            <i class="fas fa-copy ml-1"></i>نسخ
                        </button>
                    @endif
                </div>
                <div class="card-body">
                    @if($seoMeta->structured_data)
                        <div class="code-block">{{ json_encode($seoMeta->structured_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-code text-muted fa-3x mb-3"></i>
                            <p class="text-muted">لا توجد بيانات هيكلية</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Open Graph Info -->
        <div class="col-lg-4">
            <div class="card seo-detail-card info mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-share-alt ml-2"></i>
                        Open Graph (Social Media)
                    </h6>
                </div>
                <div class="card-body">
                    @if($seoMeta->og_image)
                        <div class="mb-4 text-center">
                            <label class="font-weight-bold d-block mb-2">الصورة:</label>
                            <img src="{{ $seoMeta->og_image }}" class="img-fluid rounded border shadow-sm" alt="OG Image" style="max-height: 200px;">
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="font-weight-bold d-block mb-2">
                            <i class="fas fa-heading icon-accent ml-2"></i>العنوان (OG Title):
                        </label>
                        <div class="content-box">
                            <p class="mb-0 text-gray-800">{{ $seoMeta->og_title ?? $seoMeta->meta_title ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="font-weight-bold d-block mb-2">
                            <i class="fas fa-align-left icon-accent ml-2"></i>الوصف (OG Description):
                        </label>
                        <div class="content-box">
                            <p class="mb-0 text-gray-800">{{ $seoMeta->og_description ?? $seoMeta->meta_description ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Preview Card -->
            <div class="card seo-detail-card warning mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fab fa-google ml-2"></i>
                        معاينة محرك البحث (Google)
                    </h6>
                </div>
                <div class="card-body">
                    <div class="google-preview">
                        <div class="title text-primary h5 mb-1 text-truncate" style="color: #1a0dab !important; cursor: pointer; line-height: 1.3;">
                            {{ $seoMeta->meta_title ?? 'عنوان الصفحة' }}
                        </div>
                        <div class="url text-success small mb-2" style="color: #006621 !important;">
                            {{ $seoMeta->canonical_url ?? 'https://example.com/page-url' }}
                        </div>
                        <div class="description text-secondary" style="color: #545454 !important; line-height: 1.4; font-size: 0.875rem;">
                            {{ $seoMeta->meta_description ?? 'هذا هو الوصف الذي سيظهر في نتائج محرك البحث جوجل. يجب أن يكون جذاباً ومختصراً.' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- SEO Score Card -->
            <div class="card seo-detail-card danger mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-line ml-2"></i>
                        تقييم SEO
                    </h6>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <div class="score-display">
                            {{ $seoMeta->meta_title && $seoMeta->meta_description ? '85' : '45' }}%
                        </div>
                        <small class="text-muted">نقاط SEO</small>
                    </div>
                    <div class="progress mb-3" style="height: 10px;">
                        <div class="progress-bar {{ $seoMeta->meta_title && $seoMeta->meta_description ? 'success' : 'warning' }}" 
                             role="progressbar" 
                             style="width: {{ $seoMeta->meta_title && $seoMeta->meta_description ? '85' : '45' }}%">
                        </div>
                    </div>
                    <div class="text-left">
                        <small class="d-block mb-1">
                            <i class="fas fa-{{ $seoMeta->meta_title ? 'check text-success' : 'times text-danger' }} ml-2"></i>
                            عنوان الصفحة
                        </small>
                        <small class="d-block mb-1">
                            <i class="fas fa-{{ $seoMeta->meta_description ? 'check text-success' : 'times text-danger' }} ml-2"></i>
                            وصف الصفحة
                        </small>
                        <small class="d-block">
                            <i class="fas fa-{{ $seoMeta->meta_keywords ? 'check text-success' : 'times text-danger' }} ml-2"></i>
                            كلمات مفتاحية
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyStructuredData() {
    const codeElement = document.querySelector('pre code');
    const textArea = document.createElement('textarea');
    textArea.value = codeElement.textContent;
    document.body.appendChild(textArea);
    textArea.select();
    document.execCommand('copy');
    document.body.removeChild(textArea);
    
    // Show success message
    const button = event.target.closest('button');
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-check mr-1"></i>تم النسخ!';
    button.classList.add('btn-success');
    button.classList.remove('btn-outline-success');
    
    setTimeout(() => {
        button.innerHTML = originalText;
        button.classList.remove('btn-success');
        button.classList.add('btn-outline-success');
    }, 2000);
}
</script>
@endsection
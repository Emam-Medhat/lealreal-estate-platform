@extends('admin.layouts.admin')

@section('title', 'تفاصيل SEO')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-search text-primary mr-2"></i>تفاصيل SEO
            </h1>
            <p class="text-muted mb-0 mt-1">عرض وتحليل بيانات تحسين محركات البحث</p>
        </div>
        <div>
            <a href="{{ route('admin.seo.edit', ['seoMeta' => $seoMeta->id]) }}" class="btn btn-warning shadow-sm">
                <i class="fas fa-edit fa-sm text-white-50"></i> تعديل
            </a>
            <a href="{{ route('admin.seo.meta.tags') }}" class="btn btn-secondary shadow-sm mr-2">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> العودة
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Main Info -->
        <div class="col-lg-8">
            <div class="card shadow mb-4 border-left-primary">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle mr-2"></i>المعلومات الأساسية
                    </h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-borderless">
                            <tbody>
                                <tr class="border-bottom">
                                    <td class="font-weight-bold text-gray-700" style="width: 30%;">
                                        <i class="fas fa-file-alt mr-2 text-primary"></i>نوع الصفحة
                                    </td>
                                    <td>
                                        <span class="badge badge-primary">{{ class_basename($seoMeta->model_type) }}</span>
                                    </td>
                                </tr>
                                <tr class="border-bottom">
                                    <td class="font-weight-bold text-gray-700">
                                        <i class="fas fa-hashtag mr-2 text-info"></i>المعرف (ID)
                                    </td>
                                    <td>
                                        <span class="badge badge-info">{{ $seoMeta->model_id }}</span>
                                    </td>
                                </tr>
                                <tr class="border-bottom">
                                    <td class="font-weight-bold text-gray-700">
                                        <i class="fas fa-heading mr-2 text-success"></i>عنوان الصفحة
                                    </td>
                                    <td>
                                        @if($seoMeta->meta_title)
                                            <div class="bg-light p-2 rounded">
                                                <strong>{{ $seoMeta->meta_title }}</strong>
                                                <small class="text-muted d-block">{{ strlen($seoMeta->meta_title) }}/60 حرف</small>
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr class="border-bottom">
                                    <td class="font-weight-bold text-gray-700">
                                        <i class="fas fa-align-left mr-2 text-warning"></i>الوصف
                                    </td>
                                    <td>
                                        @if($seoMeta->meta_description)
                                            <div class="bg-light p-2 rounded">
                                                <p class="mb-1">{{ $seoMeta->meta_description }}</p>
                                                <small class="text-muted">{{ strlen($seoMeta->meta_description) }}/160 حرف</small>
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr class="border-bottom">
                                    <td class="font-weight-bold text-gray-700">
                                        <i class="fas fa-key mr-2 text-danger"></i>الكلمات المفتاحية
                                    </td>
                                    <td>
                                        @if($seoMeta->meta_keywords)
                                            <div class="keywords-container">
                                                @foreach(explode(',', $seoMeta->meta_keywords) as $keyword)
                                                    @if(trim($keyword))
                                                        <span class="badge badge-info ml-1 mb-1">{{ trim($keyword) }}</span>
                                                    @endif
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr class="border-bottom">
                                    <td class="font-weight-bold text-gray-700">
                                        <i class="fas fa-link mr-2 text-secondary"></i>رابط Canonical
                                    </td>
                                    <td>
                                        @if($seoMeta->canonical_url)
                                            <a href="{{ $seoMeta->canonical_url }}" target="_blank" class="text-primary">
                                                <i class="fas fa-external-link-alt mr-1"></i>{{ $seoMeta->canonical_url }}
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-gray-700">
                                        <i class="fas fa-robot mr-2 text-dark"></i>إعدادات Robots
                                    </td>
                                    <td>
                                        <span class="badge badge-dark">{{ $seoMeta->robots ?? 'index, follow' }}</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Structured Data -->
            <div class="card shadow mb-4 border-left-success">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-code mr-2"></i>البيانات الهيكلية (Structured Data)
                    </h6>
                    @if($seoMeta->structured_data)
                        <button class="btn btn-sm btn-outline-success" onclick="copyStructuredData()">
                            <i class="fas fa-copy mr-1"></i>نسخ
                        </button>
                    @endif
                </div>
                <div class="card-body">
                    @if($seoMeta->structured_data)
                        <pre class="bg-dark text-white p-3 rounded" style="direction: ltr; text-align: left; font-size: 0.875rem; max-height: 300px; overflow-y: auto;"><code>{{ json_encode($seoMeta->structured_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
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
            <div class="card shadow mb-4 border-left-info">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">
                        <i class="fas fa-share-alt mr-2"></i>Open Graph (Social Media)
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
                            <i class="fas fa-heading mr-1 text-info"></i>العنوان (OG Title):
                        </label>
                        <div class="bg-light p-2 rounded">
                            <p class="mb-0 text-gray-800">{{ $seoMeta->og_title ?? $seoMeta->meta_title ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="font-weight-bold d-block mb-2">
                            <i class="fas fa-align-left mr-1 text-info"></i>الوصف (OG Description):
                        </label>
                        <div class="bg-light p-2 rounded">
                            <p class="mb-0 text-gray-800">{{ $seoMeta->og_description ?? $seoMeta->meta_description ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Preview Card -->
            <div class="card shadow mb-4 border-left-warning">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">
                        <i class="fab fa-google mr-2"></i>معاينة محرك البحث (Google)
                    </h6>
                </div>
                <div class="card-body">
                    <div class="google-preview p-3 border rounded bg-white">
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
            <div class="card shadow mb-4 border-left-danger">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-danger">
                        <i class="fas fa-chart-line mr-2"></i>تقييم SEO
                    </h6>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <div class="display-4 font-weight-bold text-{{ $seoMeta->meta_title && $seoMeta->meta_description ? 'success' : 'warning' }}">
                            {{ $seoMeta->meta_title && $seoMeta->meta_description ? '85' : '45' }}%
                        </div>
                        <small class="text-muted">نقاط SEO</small>
                    </div>
                    <div class="progress mb-3" style="height: 10px;">
                        <div class="progress-bar bg-{{ $seoMeta->meta_title && $seoMeta->meta_description ? 'success' : 'warning' }}" 
                             role="progressbar" 
                             style="width: {{ $seoMeta->meta_title && $seoMeta->meta_description ? '85' : '45' }}%">
                        </div>
                    </div>
                    <div class="text-left">
                        <small class="d-block mb-1">
                            <i class="fas fa-{{ $seoMeta->meta_title ? 'check text-success' : 'times text-danger' }} mr-1"></i>
                            عنوان الصفحة
                        </small>
                        <small class="d-block mb-1">
                            <i class="fas fa-{{ $seoMeta->meta_description ? 'check text-success' : 'times text-danger' }} mr-1"></i>
                            وصف الصفحة
                        </small>
                        <small class="d-block">
                            <i class="fas fa-{{ $seoMeta->meta_keywords ? 'check text-success' : 'times text-danger' }} mr-1"></i>
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
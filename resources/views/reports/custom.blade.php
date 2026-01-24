@extends('layouts.app')

@section('title', 'التقارير المخصصة')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-3">التقارير المخصصة</h2>
                    <p class="text-muted">إنشاء تقارير مخصصة بناءً على احتياجاتك</p>
                </div>
                <div>
                    <a href="{{ route('reports.custom.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> تقرير مخصص جديد
                    </a>
                    <a href="{{ route('reports.custom.builder') }}" class="btn btn-info ms-2">
                        <i class="fas fa-tools"></i> منشئ التقارير
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $totalReports }}</h4>
                            <p class="mb-0">إجمالي التقارير</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-file-alt fa-2x"></i>
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
                            <h4 class="mb-0">{{ $publicReports }}</h4>
                            <p class="mb-0">التقارير العامة</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-globe fa-2x"></i>
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
                            <h4 class="mb-0">{{ $templates }}</h4>
                            <p class="mb-0">القوالب المحفوظة</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-layer-group fa-2x"></i>
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
                            <h4 class="mb-0">{{ $totalUsage }}</h4>
                            <p class="mb-0">إجمالي الاستخدام</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-chart-line fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">البحث والتصفية</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('reports.custom.index') }}">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="search">البحث</label>
                                    <input type="text" class="form-control" id="search" name="search" 
                                           value="{{ request('search') }}" placeholder="ابحث عن تقرير...">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="category">الفئة</label>
                                    <select class="form-control" id="category" name="category">
                                        <option value="">جميع الفئات</option>
                                        <option value="sales" {{ request('category') == 'sales' ? 'selected' : '' }}>مبيعات</option>
                                        <option value="marketing" {{ request('category') == 'marketing' ? 'selected' : '' }}>تسويق</option>
                                        <option value="finance" {{ request('category') == 'finance' ? 'selected' : '' }}>مالية</option>
                                        <option value="operations" {{ request('category') == 'operations' ? 'selected' : '' }}>عمليات</option>
                                        <option value="analytics" {{ request('category') == 'analytics' ? 'selected' : '' }}>تحليلات</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="sort">الترتيب</label>
                                    <select class="form-control" id="sort" name="sort">
                                        <option value="recent" {{ request('sort') == 'recent' ? 'selected' : '' }}>الأحدث</option>
                                        <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>الأكثر استخداماً</option>
                                        <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>بالاسم</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="fas fa-search"></i> بحث
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Reports List -->
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">التقارير المخصصة</h5>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-outline-primary" onclick="toggleView('grid')">
                            <i class="fas fa-th"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-primary" onclick="toggleView('list')">
                            <i class="fas fa-list"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @if($reports->count() > 0)
                        <div id="reportsContainer" class="row">
                            @foreach($reports as $report)
                                <div class="col-md-6 mb-4 report-item">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-3">
                                                <div>
                                                    <h6 class="card-title">{{ $report->name }}</h6>
                                                    <p class="card-text text-muted small">{{ $report->description }}</p>
                                                </div>
                                                <div>
                                                    @if($report->is_public)
                                                        <span class="badge bg-success me-1">عام</span>
                                                    @endif
                                                    @if($report->is_template)
                                                        <span class="badge bg-info">قالب</span>
                                                    @endif
                                                </div>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <small class="text-muted">
                                                    <i class="fas fa-database"></i> {{ $report->getDataSourceLabels() }}
                                                </small>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <small class="text-muted">
                                                        <i class="fas fa-chart-bar"></i> {{ $report->getComplexityLevel() }}
                                                    </small>
                                                    <small class="text-muted">
                                                        <i class="fas fa-clock"></i> {{ $report->getEstimatedExecutionTime() }}
                                                    </small>
                                                </div>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <div class="progress" style="height: 4px;">
                                                    <div class="progress-bar bg-{{ $report->getComplexityColor() }}" 
                                                         style="width: {{ $report->getComplexityLevel() === 'بسيط' ? '25' : ($report->getComplexityLevel() === 'متوسط' ? '50' : ($report->getComplexityLevel() === 'معقد' ? '75' : '100')) }}%"></div>
                                                </div>
                                            </div>
                                            
                                            @if($report->tags)
                                                <div class="mb-3">
                                                    @foreach($report->tags as $tag)
                                                        <span class="badge bg-light text-dark me-1">{{ $tag }}</span>
                                                    @endforeach
                                                </div>
                                            @endif
                                            
                                            <div class="d-flex justify-content-between">
                                                <div class="btn-group">
                                                    <a href="{{ $report->getRunUrl() }}" class="btn btn-sm btn-primary" title="تشغيل">
                                                        <i class="fas fa-play"></i>
                                                    </a>
                                                    <a href="{{ $report->getEditUrl() }}" class="btn btn-sm btn-outline-secondary" title="تعديل">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="{{ $report->getDuplicateUrl() }}" class="btn btn-sm btn-outline-info" title="نسخ">
                                                        <i class="fas fa-copy"></i>
                                                    </a>
                                                    @if($report->is_public)
                                                        <a href="{{ $report->getShareUrl() }}" class="btn btn-sm btn-outline-success" title="مشاركة">
                                                            <i class="fas fa-share"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                                <div>
                                                    <small class="text-muted">
                                                        {{ $report->getFormattedUsageCount() }} استخدام
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="d-flex justify-content-between mt-4">
                            <div>
                                <p class="text-muted">
                                    عرض {{ $reports->firstItem() }} - {{ $reports->lastItem() }} من {{ $reports->total() }} تقرير
                                </p>
                            </div>
                            <div>
                                {{ $reports->links() }}
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-cog fa-3x text-muted mb-3"></i>
                            <h5>لا توجد تقارير مخصصة</h5>
                            <p class="text-muted mb-3">ابدأ بإنشاء أول تقرير مخصص لك</p>
                            <a href="{{ route('reports.custom.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> إنشاء تقرير جديد
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Templates -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">القوالب الشائعة</h5>
                </div>
                <div class="card-body">
                    @if($templates->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($templates as $template)
                                <div class="list-group-item list-group-item-action">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">{{ $template->name }}</h6>
                                            <small class="text-muted">{{ $template->description }}</small>
                                        </div>
                                        <div>
                                            <button class="btn btn-sm btn-outline-primary" onclick="loadTemplate({{ $template->id }})">
                                                <i class="fas fa-download"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-layer-group fa-2x text-muted mb-2"></i>
                            <p class="text-muted small">لا توجد قوالب متاحة</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">إجراءات سريعة</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('reports.custom.create') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-plus"></i> تقرير جديد
                        </a>
                        <a href="{{ route('reports.custom.builder') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-tools"></i> منشئ التقارير
                        </a>
                        <a href="{{ route('reports.custom.templates') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-layer-group"></i> إدارة القوالب
                        </a>
                        <a href="{{ route('reports.custom.analytics') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-chart-bar"></i> تحليلات التقارير
                        </a>
                    </div>
                </div>
            </div>

            <!-- Categories -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">الفئات</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('reports.custom.index', ['category' => 'sales']) }}" 
                           class="list-group-item list-group-item-action {{ request('category') == 'sales' ? 'active' : '' }}">
                            <i class="fas fa-chart-line"></i> مبيعات
                            <span class="badge bg-primary float-start">{{ $categoryCounts['sales'] ?? 0 }}</span>
                        </a>
                        <a href="{{ route('reports.custom.index', ['category' => 'marketing']) }}" 
                           class="list-group-item list-group-item-action {{ request('category') == 'marketing' ? 'active' : '' }}">
                            <i class="fas fa-bullhorn"></i> تسويق
                            <span class="badge bg-primary float-start">{{ $categoryCounts['marketing'] ?? 0 }}</span>
                        </a>
                        <a href="{{ route('reports.custom.index', ['category' => 'finance']) }}" 
                           class="list-group-item list-group-item-action {{ request('category') == 'finance' ? 'active' : '' }}">
                            <i class="fas fa-dollar-sign"></i> مالية
                            <span class="badge bg-primary float-start">{{ $categoryCounts['finance'] ?? 0 }}</span>
                        </a>
                        <a href="{{ route('reports.custom.index', ['category' => 'operations']) }}" 
                           class="list-group-item list-group-item-action {{ request('category') == 'operations' ? 'active' : '' }}">
                            <i class="fas fa-cogs"></i> عمليات
                            <span class="badge bg-primary float-start">{{ $categoryCounts['operations'] ?? 0 }}</span>
                        </a>
                        <a href="{{ route('reports.custom.index', ['category' => 'analytics']) }}" 
                           class="list-group-item list-group-item-action {{ request('category') == 'analytics' ? 'active' : '' }}">
                            <i class="fas fa-chart-bar"></i> تحليلات
                            <span class="badge bg-primary float-start">{{ $categoryCounts['analytics'] ?? 0 }}</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        border: 1px solid rgba(0, 0, 0, 0.125);
    }
    
    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid rgba(0, 0, 0, 0.125);
    }
    
    .report-item {
        transition: transform 0.2s ease-in-out;
    }
    
    .report-item:hover {
        transform: translateY(-2px);
    }
    
    .btn-group .btn {
        margin: 0 1px;
    }
    
    .list-group-item {
        border: none;
        border-bottom: 1px solid #dee2e6;
    }
    
    .list-group-item:last-child {
        border-bottom: none;
    }
    
    .badge {
        font-size: 0.75em;
    }
</style>
@endpush

@push('scripts')
<script>
function toggleView(viewType) {
    const container = document.getElementById('reportsContainer');
    
    if (viewType === 'grid') {
        container.className = 'row';
    } else {
        container.className = 'list-group list-group-flush';
        // Convert cards to list items
        const items = container.querySelectorAll('.report-item');
        items.forEach(item => {
            item.className = 'list-group-item';
        });
    }
}

function loadTemplate(templateId) {
    fetch(`/reports/custom/templates/${templateId}/load`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Redirect to create page with template data
                window.location.href = `/reports/custom/create?template=${templateId}`;
            } else {
                alert('فشل تحميل القالب');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ أثناء تحميل القالب');
        });
}

// Auto-refresh for real-time updates
setInterval(() => {
    // Optional: Add real-time updates for usage counts
}, 30000);
</script>
@endpush

@extends('layouts.app')

@section('title', 'خريطة الروتات - Route Map')

@section('content')


<style>
/* Root Variables */
:root {
    --primary: #4361ee;
    --primary-soft: rgba(67, 97, 238, 0.1);
    --success: #4cc9f0;
    --success-soft: rgba(76, 201, 240, 0.1);
    --warning: #f72585;
    --warning-soft: rgba(247, 37, 133, 0.1);
    --info: #4895ef;
    --info-soft: rgba(72, 149, 239, 0.1);
    --danger: #ef233c;
    --danger-soft: rgba(239, 35, 60, 0.1);
    --glass: rgba(255, 255, 255, 0.8);
    --glass-border: rgba(255, 255, 255, 0.3);
}

body {
    background: #f8f9fa;
    background-image: 
        radial-gradient(at 0% 0%, rgba(67, 97, 238, 0.05) 0px, transparent 50%),
        radial-gradient(at 100% 0%, rgba(76, 201, 240, 0.05) 0px, transparent 50%);
    font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif;
    color: #2b2d42;
}

/* Glassmorphism Components */
.glass-card {
    background: var(--glass);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border: 1px solid var(--glass-border);
    border-radius: 24px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.04);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.header-gradient {
    background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
    border-radius: 24px;
}

/* Header Decorations */
.shape-1 {
    position: absolute;
    top: -50px;
    right: -50px;
    width: 200px;
    height: 200px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
}

.shape-2 {
    position: absolute;
    bottom: -30px;
    left: 20%;
    width: 100px;
    height: 100px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 50%;
}

/* Icon Boxes */
.icon-box-white {
    width: 60px;
    height: 60px;
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(4px);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
}

/* Buttons */
.btn-blur-light {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(8px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: white;
    padding: 10px 20px;
    border-radius: 12px;
    font-weight: 500;
    transition: all 0.2s;
}

.btn-blur-light:hover {
    background: rgba(255, 255, 255, 0.2);
    color: white;
    transform: translateY(-2px);
}

.btn-success-modern {
    background: #4cc9f0;
    color: #fff;
    border: none;
    padding: 10px 20px;
    border-radius: 12px;
    font-weight: 600;
    box-shadow: 0 4px 12px rgba(76, 201, 240, 0.3);
    transition: all 0.2s;
}

.btn-success-modern:hover {
    background: #3fb6da;
    color: #fff;
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(76, 201, 240, 0.4);
}

/* Stats Cards */
.stat-card {
    background: white;
    padding: 24px;
    border-radius: 20px;
    display: flex;
    align-items: center;
    gap: 16px;
    border: 1px solid #f1f3f9;
    transition: all 0.3s;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}

.bg-soft-primary { background: var(--primary-soft); }
.bg-soft-success { background: var(--success-soft); }
.bg-soft-warning { background: var(--warning-soft); }
.bg-soft-info { background: var(--info-soft); }
.bg-soft-danger { background: var(--danger-soft); }
.bg-soft-secondary { background: #f1f3f9; }

.stat-label {
    display: block;
    font-size: 0.85rem;
    color: #64748b;
    margin-bottom: 2px;
}

.stat-value {
    margin: 0;
    font-size: 1.5rem;
    font-weight: 700;
}

/* Search Box */
.search-box {
    position: relative;
}

.search-box i {
    position: absolute;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
}

.search-box .form-control {
    padding: 12px 12px 12px 45px;
    border-radius: 14px;
    border: 1px solid #e2e8f0;
    background: #f8fafc;
    font-weight: 500;
}

.search-box .form-control:focus {
    background: white;
    border-color: var(--primary);
    box-shadow: 0 0 0 4px var(--primary-soft);
}

/* Filter Buttons */
.btn-filter {
    background: #f1f3f9;
    border: none;
    padding: 8px 16px;
    border-radius: 10px;
    font-size: 0.85rem;
    font-weight: 600;
    color: #64748b;
    transition: all 0.2s;
}

.btn-filter:hover {
    background: #e2e8f0;
    color: #475569;
}

.btn-filter.active {
    background: var(--primary);
    color: white;
}

.btn-filter-clear {
    background: transparent;
    border: 1px dashed #cbd5e1;
    padding: 8px 16px;
    border-radius: 10px;
    font-size: 0.85rem;
    color: #64748b;
}

/* Method Badges */
.method-badge {
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 0.7rem;
    font-weight: 800;
    letter-spacing: 0.5px;
}

.method-badge.get { background: #dcfce7; color: #166534; }
.method-badge.post { background: #fef9c3; color: #854d0e; }
.method-badge.put { background: #e0f2fe; color: #075985; }
.method-badge.delete { background: #fee2e2; color: #991b1b; }
.method-badge.patch { background: #f1f5f9; color: #475569; }

/* Table Styling */
.table-modern thead th {
    background: #f8fafc;
    border-bottom: 2px solid #f1f3f9;
    padding: 16px 20px;
    font-size: 0.8rem;
    font-weight: 700;
    text-transform: uppercase;
    color: #64748b;
}

.table-modern tbody td {
    padding: 16px 20px;
    vertical-align: middle;
    border-bottom: 1px solid #f1f3f9;
}

.route-row {
    transition: background 0.2s;
}

.route-row:hover {
    background: #f8fafc;
}

.uri-container {
    display: flex;
    align-items: center;
    gap: 8px;
}

.uri-text {
    font-family: 'JetBrains Mono', 'Fira Code', monospace;
    color: var(--primary);
    font-weight: 600;
    font-size: 0.9rem;
    background: var(--primary-soft);
    padding: 4px 8px;
    border-radius: 6px;
}

.api-tag {
    background: #ede9fe;
    color: #5b21b6;
    font-size: 0.65rem;
    font-weight: 700;
    padding: 2px 6px;
    border-radius: 4px;
}

.name-badge {
    background: #f1f5f9;
    color: #334155;
    padding: 4px 10px;
    border-radius: 8px;
    font-size: 0.85rem;
    font-weight: 500;
}

.action-text {
    font-size: 0.85rem;
    color: #64748b;
    max-width: 300px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Action Buttons */
.action-buttons {
    display: flex;
    justify-content: center;
    gap: 8px;
}

.btn-icon {
    width: 34px;
    height: 34px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: none;
    background: #f1f3f9;
    color: #64748b;
    transition: all 0.2s;
    text-decoration: none;
}

.btn-icon:hover {
    transform: translateY(-2px);
}

.btn-view:hover { background: var(--primary); color: white; }
.btn-copy:hover { background: #10b981; color: white; }
.btn-link:hover { background: #3b82f6; color: white; }

/* Toast */
.glass-toast {
    background: rgba(15, 23, 42, 0.9);
    backdrop-filter: blur(8px);
    border-radius: 16px;
    color: white;
    min-width: 250px;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2);
}

.toast-icon {
    color: #10b981;
    font-size: 1.25rem;
}

/* Animations */
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in { animation: fadeIn 0.6s ease-out forwards; }
.animate-fade-in-up { animation: fadeInUp 0.6s ease-out forwards; }

/* Responsive */
@media (max-width: 768px) {
    .container-fluid { padding: 20px !important; }
    .display-5 { font-size: 2rem; }
    .glass-card { border-radius: 16px; }
    .stat-card { padding: 16px; }
}
</style>

<div class="container-fluid py-5 px-md-5">
    <!-- Modern Header Section -->
    <div class="row mb-5 animate-fade-in">
        <div class="col-12">
            <div class="glass-card overflow-hidden">
                <div class="header-gradient p-5 position-relative">
                    <!-- Decorative Shapes -->
                    <div class="shape-1"></div>
                    <div class="shape-2"></div>
                    
                    <div class="position-relative z-1">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-4">
                            <div>
                                <h1 class="display-5 fw-bold text-white mb-2 d-flex align-items-center">
                                    <div class="icon-box-white me-3">
                                        <i class="fas fa-route"></i>
                                    </div>
                                    خريطة رووتات النظام
                                </h1>
                                <p class="text-white-50 fs-5 mb-0">استعراض وإدارة جميع رووتات التطبيق بواجهة عصرية</p>
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                <button onclick="expandAll()" class="btn btn-blur-light">
                                    <i class="fas fa-expand-alt me-2"></i>توسيع
                                </button>
                                <button onclick="collapseAll()" class="btn btn-blur-light">
                                    <i class="fas fa-compress-alt me-2"></i>طي
                                </button>
                                <a href="{{ route('routes.export') }}" class="btn btn-success-modern">
                                    <i class="fas fa-file-export me-2"></i>تصدير CSV
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Section -->
    <div class="row mb-5 animate-fade-in-up">
        <div class="col-lg-2 col-md-4 col-sm-6 mb-4">
            <div class="stat-card">
                <div class="stat-icon bg-soft-primary">
                    <i class="fas fa-globe text-primary"></i>
                </div>
                <div class="stat-content">
                    <span class="stat-label">إجمالي الروتات</span>
                    <h2 class="stat-value">{{ count($routes) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-4">
            <div class="stat-card">
                <div class="stat-icon bg-soft-success">
                    <i class="fas fa-eye text-success"></i>
                </div>
                <div class="stat-content">
                    <span class="stat-label">GET</span>
                    <h2 class="stat-value">{{ $routes->where('is_get', true)->count() }}</h2>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-4">
            <div class="stat-card">
                <div class="stat-icon bg-soft-warning">
                    <i class="fas fa-plus-circle text-warning"></i>
                </div>
                <div class="stat-content">
                    <span class="stat-label">POST</span>
                    <h2 class="stat-value">{{ $routes->where('is_post', true)->count() }}</h2>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-4">
            <div class="stat-card">
                <div class="stat-icon bg-soft-info">
                    <i class="fas fa-edit text-info"></i>
                </div>
                <div class="stat-content">
                    <span class="stat-label">PUT</span>
                    <h2 class="stat-value">{{ $routes->where('is_put', true)->count() }}</h2>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-4">
            <div class="stat-card">
                <div class="stat-icon bg-soft-danger">
                    <i class="fas fa-trash text-danger"></i>
                </div>
                <div class="stat-content">
                    <span class="stat-label">DELETE</span>
                    <h2 class="stat-value">{{ $routes->where('is_delete', true)->count() }}</h2>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-4">
            <div class="stat-card">
                <div class="stat-icon bg-soft-secondary">
                    <i class="fas fa-code text-secondary"></i>
                </div>
                <div class="stat-content">
                    <span class="stat-label">API</span>
                    <h2 class="stat-value">{{ $routes->where('is_api', true)->count() }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filters Section -->
    <div class="row mb-5 animate-fade-in-up" style="animation-delay: 0.1s;">
        <div class="col-12">
            <div class="glass-card p-4">
                <div class="row g-4 align-items-center">
                    <div class="col-xl-4 col-lg-5">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="searchRoutes" class="form-control" placeholder="ابحث عن روت أو مسار...">
                        </div>
                    </div>
                    <div class="col-xl-8 col-lg-7">
                        <div class="filter-group d-flex flex-wrap gap-2 justify-content-lg-end">
                            <button class="btn btn-filter method-get" onclick="filterByMethod('GET')">GET</button>
                            <button class="btn btn-filter method-post" onclick="filterByMethod('POST')">POST</button>
                            <button class="btn btn-filter method-put" onclick="filterByMethod('PUT')">PUT</button>
                            <button class="btn btn-filter method-delete" onclick="filterByMethod('DELETE')">DELETE</button>
                            <button class="btn btn-filter method-patch" onclick="filterByMethod('PATCH')">PATCH</button>
                            <button class="btn btn-filter-clear" onclick="clearFilters()">
                                <i class="fas fa-times me-1"></i>مسح الفلاتر
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Section -->
    <div class="row animate-fade-in-up" style="animation-delay: 0.2s;">
        <div class="col-12">
            <div class="glass-card overflow-hidden border-0">
                <div class="card-header-modern">
                    <div class="d-flex justify-content-between align-items-center px-4 py-3">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-list-ul me-2 text-primary"></i>
                            قائمة الروتات المسجلة
                        </h5>
                        <div id="routeCount" class="badge-count">{{ count($routes) }} روت</div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-modern mb-0" id="routesTable">
                        <thead>
                            <tr>
                                <th style="width: 150px;">الطريقة</th>
                                <th>المسار (URI)</th>
                                <th>الاسم</th>
                                <th>الإجراء (Action)</th>
                                <th style="width: 140px; text-align: center;">العمليات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($routes as $route)
                                <tr class="route-row" data-search="{{ $route['uri'] }} {{ $route['name'] ?? '' }}" data-methods="{{ implode(',', $route['methods']) }}">
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($route['methods'] as $method)
                                                <span class="method-badge {{ strtolower($method) }}">{{ $method }}</span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td>
                                        <div class="uri-container">
                                            <code class="uri-text">{{ $route['uri'] }}</code>
                                            @if($route['is_api'])
                                                <span class="api-tag">API</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($route['name'])
                                            <span class="name-badge">{{ $route['name'] }}</span>
                                        @else
                                            <span class="text-muted small italic opacity-50">بدون اسم</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="action-text" title="{{ $route['action'] }}">
                                            {{ Str::limit($route['action'], 50) }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            @if($route['is_get'])
                                                <a href="{{ url($route['uri']) }}" class="btn-icon btn-view" target="_blank" title="عرض المسار">
                                                    <i class="fas fa-external-link-alt"></i>
                                                </a>
                                            @endif
                                            @if($route['name'])
                                                <button class="btn-icon btn-copy" onclick="copyRouteName('{{ $route['name'] }}')" title="نسخ الاسم">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                                <button class="btn-icon btn-link" onclick="copyRouteUrl('{{ url($route['uri']) }}')" title="نسخ الرابط">
                                                    <i class="fas fa-link"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modern Toast Notification -->
<div id="toast" class="toast-container position-fixed bottom-0 start-0 p-4" style="display: none; z-index: 9999;">
    <div class="glass-toast p-3 d-flex align-items-center gap-3">
        <div class="toast-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="toast-body fw-medium" id="toastMessage">
            تمت العملية بنجاح
        </div>
    </div>
</div>

<script>
// Search functionality
document.getElementById('searchRoutes').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('.route-row');
    let visibleCount = 0;
    
    rows.forEach(row => {
        const searchableText = row.getAttribute('data-search').toLowerCase();
        if (searchableText.includes(searchTerm)) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    updateCountDisplay(visibleCount);
});

// Filter by method
function filterByMethod(method) {
    const rows = document.querySelectorAll('.route-row');
    const buttons = document.querySelectorAll('.btn-filter');
    let visibleCount = 0;
    
    // Toggle active state on buttons
    buttons.forEach(btn => {
        if (btn.classList.contains('method-' + method.toLowerCase())) {
            btn.classList.toggle('active');
        } else {
            btn.classList.remove('active');
        }
    });

    const isActive = document.querySelector('.btn-filter.method-' + method.toLowerCase() + '.active');

    rows.forEach(row => {
        if (!isActive) {
            row.style.display = '';
            visibleCount++;
            return;
        }
        
        const methods = row.getAttribute('data-methods').split(',');
        if (methods.includes(method)) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    updateCountDisplay(visibleCount);
}

// Clear all filters
function clearFilters() {
    const rows = document.querySelectorAll('.route-row');
    const buttons = document.querySelectorAll('.btn-filter');
    
    rows.forEach(row => row.style.display = '');
    buttons.forEach(btn => btn.classList.remove('active'));
    
    document.getElementById('searchRoutes').value = '';
    updateCountDisplay(rows.length);
}

function updateCountDisplay(count) {
    document.getElementById('routeCount').textContent = count + ' روت';
}

// Copy functions
function copyRouteName(name) {
    navigator.clipboard.writeText(name);
    showToast('تم نسخ اسم الروت: ' + name);
}

function copyRouteUrl(url) {
    navigator.clipboard.writeText(url);
    showToast('تم نسخ الرابط الكامل بنجاح');
}

// Toast notification
function showToast(message) {
    const toastElement = document.getElementById('toast');
    const toastMessage = document.getElementById('toastMessage');
    toastMessage.textContent = message;
    
    toastElement.style.display = 'block';
    toastElement.style.opacity = '0';
    toastElement.style.transform = 'translateY(20px)';
    
    // Simple animation
    setTimeout(() => {
        toastElement.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
        toastElement.style.opacity = '1';
        toastElement.style.transform = 'translateY(0)';
    }, 10);
    
    setTimeout(() => {
        toastElement.style.opacity = '0';
        toastElement.style.transform = 'translateY(20px)';
        setTimeout(() => {
            toastElement.style.display = 'none';
        }, 300);
    }, 3000);
}

// Expand/Collapse functions (stubs for the user's existing logic if needed)
function expandAll() {
    // Implement if there's a specific expansion logic
    console.log('Expand All clicked');
}

function collapseAll() {
    // Implement if there's a specific collapse logic
    console.log('Collapse All clicked');
}
</script>
@endsection

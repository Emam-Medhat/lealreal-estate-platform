@extends('layouts.app')

@section('title', 'خريطة الروتات - Route Map')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">
                    <i class="fas fa-route me-2"></i>
                    خريطة رووتات الموقع
                </h1>
                <div class="d-flex gap-2">
                    <input type="text" id="searchRoutes" class="form-control" placeholder="بحث في الروتات..." style="width: 300px;">
                    <button class="btn btn-primary" onclick="expandAll()">
                        <i class="fas fa-expand"></i> توسيع الكل
                    </button>
                    <button class="btn btn-secondary" onclick="collapseAll()">
                        <i class="fas fa-compress"></i> طي الكل
                    </button>
                    <a href="{{ route('routes.export') }}" class="btn btn-success" title="تصدير الروتات">
                        <i class="fas fa-download"></i> تصدير CSV
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- إحصائيات سريعة -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">إجمالي الروتات</h5>
                    <h2 class="mb-0">{{ count($routes) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">روتات GET</h5>
                    <h2 class="mb-0">{{ $routes->where('methods', 'like', '%GET%')->count() }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5 class="card-title">روتات POST</h5>
                    <h2 class="mb-0">{{ $routes->where('methods', 'like', '%POST%')->count() }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">روتات API</h5>
                    <h2 class="mb-0">{{ $routes->where('uri', 'like', 'api%')->count() }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- الروتات مقسمة حسب الملف -->
    @php
        $routesByFile = $routes->groupBy(function($route) {
            if (str_contains($route->uri, 'properties')) return 'properties';
            if (str_contains($route->uri, 'users') || str_contains($route->uri, 'profile')) return 'users';
            if (str_contains($route->uri, 'analytics')) return 'analytics';
            if (str_contains($route->uri, 'ai')) return 'ai';
            if (str_contains($route->uri, 'admin')) return 'admin';
            if (str_contains($route->uri, 'taxes')) return 'taxes';
            if (str_contains($route->uri, 'reports')) return 'reports';
            if (str_contains($route->uri, 'leads')) return 'leads';
            if (str_contains($route->uri, 'documents')) return 'documents';
            if (str_contains($route->uri, 'contracts')) return 'contracts';
            if (str_contains($route->uri, 'auctions')) return 'auctions';
            if (str_contains($route->uri, 'ads')) return 'ads';
            if (str_contains($route->uri, 'financial')) return 'financial';
            if (str_contains($route->uri, 'api')) return 'api';
            return 'general';
        });
    @endphp

    @foreach($routesByFile as $category => $categoryRoutes)
        <div class="card mb-4 route-category" data-category="{{ $category }}">
            <div class="card-header bg-{{ getCardColor($category) }} text-white" onclick="toggleCategory('{{ $category }}')">
                <h5 class="mb-0 d-flex justify-content-between align-items-center">
                    <span>
                        <i class="fas fa-{{ getCategoryIcon($category) }} me-2"></i>
                        {{ getCategoryTitle($category) }}
                        <span class="badge bg-light text-dark ms-2">{{ $categoryRoutes->count() }}</span>
                    </span>
                    <i class="fas fa-chevron-down toggle-icon"></i>
                </h5>
            </div>
            <div class="card-body p-0" id="category-{{ $category }}">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>الطريقة</th>
                                <th>الرابط</th>
                                <th>الاسم</th>
                                <th>الوظيفة</th>
                                <th>الإجراء</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categoryRoutes as $route)
                                <tr class="route-row" data-search="{{ $route->uri }} {{ $route->name ?? '' }}">
                                    <td>
                                        @foreach($route->methods as $method)
                                            <span class="badge bg-{{ getMethodColor($method) }} me-1">{{ $method }}</span>
                                        @endforeach
                                    </td>
                                    <td>
                                        <code class="text-primary">{{ $route->uri }}</code>
                                    </td>
                                    <td>
                                        @if($route->name)
                                            <code class="text-success">{{ $route->name }}</code>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="route-description">{{ getRouteDescription($route) }}</span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            @if(in_array('GET', $route->methods))
                                                <a href="{{ url($route->uri) }}" class="btn btn-outline-primary" target="_blank" title="فتح الرابط">
                                                    <i class="fas fa-external-link-alt"></i>
                                                </a>
                                            @endif
                                            @if($route->name)
                                                <button class="btn btn-outline-info" onclick="copyRouteName('{{ $route->name }}')" title="نسخ اسم الرابط">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                                <button class="btn btn-outline-success" onclick="copyRouteUrl('{{ url($route->uri) }}')" title="نسخ الرابط الكامل">
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
    @endforeach
</div>

<!-- JavaScript -->
<script>
function toggleCategory(category) {
    const element = document.getElementById(`category-${category}`);
    const icon = event.currentTarget.querySelector('.toggle-icon');
    
    if (element.style.display === 'none') {
        element.style.display = 'block';
        icon.classList.remove('fa-chevron-left');
        icon.classList.add('fa-chevron-down');
    } else {
        element.style.display = 'none';
        icon.classList.remove('fa-chevron-down');
        icon.classList.add('fa-chevron-left');
    }
}

function expandAll() {
    document.querySelectorAll('.route-category .card-body').forEach(el => {
        el.style.display = 'block';
    });
    document.querySelectorAll('.toggle-icon').forEach(icon => {
        icon.classList.remove('fa-chevron-left');
        icon.classList.add('fa-chevron-down');
    });
}

function collapseAll() {
    document.querySelectorAll('.route-category .card-body').forEach(el => {
        el.style.display = 'none';
    });
    document.querySelectorAll('.toggle-icon').forEach(icon => {
        icon.classList.remove('fa-chevron-down');
        icon.classList.add('fa-chevron-left');
    });
}

function copyRouteName(name) {
    navigator.clipboard.writeText(name);
    showToast('تم نسخ اسم الرابط: ' + name);
}

function copyRouteUrl(url) {
    navigator.clipboard.writeText(url);
    showToast('تم نسخ الرابط: ' + url);
}

function showToast(message) {
    // Create toast element
    const toast = document.createElement('div');
    toast.className = 'position-fixed top-0 end-0 p-3';
    toast.style.zIndex = '11';
    toast.innerHTML = `
        <div class="toast show" role="alert">
            <div class="toast-body">
                ${message}
            </div>
        </div>
    `;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 3000);
}

// Search functionality
document.getElementById('searchRoutes').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('.route-row');
    
    rows.forEach(row => {
        const searchableText = row.getAttribute('data-search').toLowerCase();
        if (searchableText.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});

// Auto-expand category when searching
document.getElementById('searchRoutes').addEventListener('input', function(e) {
    if (e.target.value.length > 0) {
        expandAll();
    }
});
</script>

<style>
.route-category .card-header {
    cursor: pointer;
    transition: background-color 0.3s;
}

.route-category .card-header:hover {
    opacity: 0.9;
}

.toggle-icon {
    transition: transform 0.3s;
}

.route-description {
    font-size: 0.9em;
    color: #666;
}

.badge {
    font-size: 0.8em;
}

.table th {
    border-top: none;
    font-weight: 600;
    background-color: #f8f9fa;
}

.route-row:hover {
    background-color: #f8f9fa;
}

.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
}

.toast {
    min-width: 250px;
}

.card-body {
    transition: all 0.3s ease;
}
</style>
@endsection

<?php
// Helper functions for the view
function getCardColor($category) {
    $colors = [
        'properties' => 'primary',
        'users' => 'success',
        'analytics' => 'info',
        'ai' => 'warning',
        'admin' => 'danger',
        'taxes' => 'dark',
        'reports' => 'secondary',
        'leads' => 'primary',
        'documents' => 'success',
        'contracts' => 'info',
        'auctions' => 'warning',
        'ads' => 'danger',
        'financial' => 'dark',
        'api' => 'secondary',
        'general' => 'light'
    ];
    return $colors[$category] ?? 'secondary';
}

function getCategoryIcon($category) {
    $icons = [
        'properties' => 'home',
        'users' => 'users',
        'analytics' => 'chart-line',
        'ai' => 'robot',
        'admin' => 'user-shield',
        'taxes' => 'file-invoice-dollar',
        'reports' => 'file-alt',
        'leads' => 'user-tie',
        'documents' => 'file',
        'contracts' => 'handshake',
        'auctions' => 'gavel',
        'ads' => 'bullhorn',
        'financial' => 'calculator',
        'api' => 'cog',
        'general' => 'globe'
    ];
    return $icons[$category] ?? 'circle';
}

function getCategoryTitle($category) {
    $titles = [
        'properties' => 'العقارات',
        'users' => 'المستخدمون والملفات الشخصية',
        'analytics' => 'التحليلات والإحصائيات',
        'ai' => 'الذكاء الاصطناعي',
        'admin' => 'لوحة التحكم',
        'taxes' => 'الضرائب',
        'reports' => 'التقارير',
        'leads' => 'العملاء المحتملون',
        'documents' => 'الوثائق',
        'contracts' => 'العقود',
        'auctions' => 'المزادات',
        'ads' => 'الإعلانات',
        'financial' => 'التحليل المالي',
        'api' => 'واجهة برمجة التطبيقات',
        'general' => 'روتات عامة'
    ];
    return $titles[$category] ?? 'غير مصنف';
}

function getMethodColor($method) {
    $colors = [
        'GET' => 'success',
        'POST' => 'primary',
        'PUT' => 'warning',
        'PATCH' => 'info',
        'DELETE' => 'danger',
        'OPTIONS' => 'secondary',
        'HEAD' => 'light'
    ];
    return $colors[$method] ?? 'secondary';
}

function getRouteDescription($route) {
    $uri = $route->uri;
    $name = $route->name ?? '';
    
    // Properties routes
    if (str_contains($uri, 'properties/create')) return 'إضافة عقار جديد';
    if (str_contains($uri, 'properties') && str_contains($uri, '{property}')) return 'عرض تفاصيل العقار';
    if (str_contains($uri, 'properties') && str_contains($uri, 'edit')) return 'تعديل بيانات العقار';
    
    // User routes
    if (str_contains($uri, 'profile')) return 'الملف الشخصي للمستخدم';
    if (str_contains($uri, 'kyc')) return 'التحقق من الهوية';
    if (str_contains($uri, 'wallet')) return 'المحفظة المالية';
    if (str_contains($uri, 'settings')) return 'إعدادات المستخدم';
    
    // Analytics routes
    if (str_contains($uri, 'analytics/dashboard')) return 'لوحة تحليلات البيانات';
    if (str_contains($uri, 'analytics/overview')) return 'نظرة عامة على التحليلات';
    
    // AI routes
    if (str_contains($uri, 'ai/valuation')) return 'تقييم العقارات بالذكاء الاصطناعي';
    if (str_contains($uri, 'ai/description')) return 'وصف العقارات بالذكاء الاصطناعي';
    
    // Admin routes
    if (str_contains($uri, 'admin')) return 'لوحة تحكم المشرف';
    
    // Tax routes
    if (str_contains($uri, 'taxes')) return 'إدارة الضرائب';
    
    // Reports routes
    if (str_contains($uri, 'reports')) return 'التقارير والإحصائيات';
    
    // Lead routes
    if (str_contains($uri, 'leads')) return 'إدارة العملاء المحتملين';
    
    // Document routes
    if (str_contains($uri, 'documents')) return 'إدارة الوثائق';
    
    // Contract routes
    if (str_contains($uri, 'contracts')) return 'إدارة العقود';
    
    // Auction routes
    if (str_contains($uri, 'auctions')) return 'إدارة المزادات';
    
    // Ad routes
    if (str_contains($uri, 'ads')) return 'إدارة الإعلانات';
    
    // Financial routes
    if (str_contains($uri, 'financial')) return 'التحليل المالي';
    
    // API routes
    if (str_contains($uri, 'api')) return 'واجهة برمجة التطبيقات';
    
    // General routes
    if ($uri === '/') return 'الصفحة الرئيسية';
    if (str_contains($uri, 'login')) return 'تسجيل الدخول';
    if (str_contains($uri, 'register')) return 'إنشاء حساب جديد';
    if (str_contains($uri, 'logout')) return 'تسجيل الخروج';
    
    return 'وظيفة غير محددة';
}
?>
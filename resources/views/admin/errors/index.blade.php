@extends('layouts.app')

@section('title', 'نظام تتبع الأخطاء - Error Tracking')

@section('content')
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
                                        <i class="fas fa-bug"></i>
                                    </div>
                                    نظام تتبع أخطاء النظام
                                </h1>
                                <p class="text-white-50 fs-5 mb-0">مراقبة وتسجيل جميع استثناءات النظام في الوقت الفعلي</p>
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                <button id="scanRoutesBtn" class="btn btn-blur-light">
                                    <i class="fas fa-search me-2"></i>فحص جميع الروتات
                                </button>
                                <form action="{{ route('admin.errors.clear') }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف جميع السجلات؟')">
                                    @csrf
                                    <button type="submit" class="btn btn-blur-light">
                                        <i class="fas fa-trash-alt me-2"></i>مسح السجلات
                                    </button>
                                </form>
                                <button onclick="window.location.reload()" class="btn btn-success-modern">
                                    <i class="fas fa-sync-alt me-2"></i>تحديث البيانات
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Section -->
    <div class="row mb-5 animate-fade-in-up">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stat-card">
                <div class="stat-icon bg-soft-primary">
                    <i class="fas fa-list-ul text-primary"></i>
                </div>
                <div class="stat-content">
                    <span class="stat-label">إجمالي الأخطاء</span>
                    <h2 class="stat-value">{{ $stats['total'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stat-card">
                <div class="stat-icon bg-soft-warning">
                    <i class="fas fa-calendar-day text-warning"></i>
                </div>
                <div class="stat-content">
                    <span class="stat-label">أخطاء اليوم</span>
                    <h2 class="stat-value">{{ $stats['today'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stat-card">
                <div class="stat-icon bg-soft-success">
                    <i class="fas fa-check-circle text-success"></i>
                </div>
                <div class="stat-content">
                    <span class="stat-label">تم حلها</span>
                    <h2 class="stat-value text-success">{{ $stats['resolved'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stat-card">
                <div class="stat-icon bg-soft-danger">
                    <i class="fas fa-clock text-danger"></i>
                </div>
                <div class="stat-content">
                    <span class="stat-label">قيد الانتظار</span>
                    <h2 class="stat-value text-danger">{{ $stats['unresolved'] }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Route Health Scan Results (Hidden by default) -->
    <div id="scanResultsSection" class="row mb-5 animate-fade-in-up" style="display: none;">
        <div class="col-12">
            <div class="glass-card overflow-hidden border-0">
                <div class="card-header-modern bg-soft-primary p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-stethoscope me-2 text-primary"></i>
                            نتائج فحص الروتات
                        </h5>
                        <div id="scanSummary" class="badge-count">جارِ الفحص...</div>
                    </div>
                </div>
                <div class="p-4">
                    <div id="scanLoading" class="text-center py-5">
                        <div class="spinner-border text-primary mb-3" role="status"></div>
                        <p class="text-muted">يتم الآن فحص جميع الروتات المتاحة، قد يستغرق هذا بضع ثوانٍ...</p>
                    </div>
                    <div id="scanContent" style="display: none;">
                        <div class="table-responsive">
                            <table class="table table-modern mb-0">
                                <thead>
                                    <tr>
                                        <th>الروت (URI)</th>
                                        <th>الاسم</th>
                                        <th class="text-center">الحالة</th>
                                        <th>الخطأ المتوقع</th>
                                    </tr>
                                </thead>
                                <tbody id="scanResultsBody">
                                    <!-- Results will be injected here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Section -->
    <div class="row animate-fade-in-up" style="animation-delay: 0.1s;">
        <div class="col-12">
            <div class="glass-card overflow-hidden border-0">
                <div class="card-header-modern">
                    <div class="d-flex justify-content-between align-items-center px-4 py-3">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-history me-2 text-primary"></i>
                            سجل الأخطاء الأخير
                        </h5>
                        <div class="badge-count">{{ $logs->total() }} خطأ مسجل</div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-modern mb-0">
                        <thead>
                            <tr>
                                <th>المسار والخطأ</th>
                                <th>الموقع (File:Line)</th>
                                <th>التاريخ والوقت</th>
                                <th class="text-center">الحالة</th>
                                <th class="text-center">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                            <tr class="route-row">
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="text-danger fw-bold text-truncate" style="max-width: 400px;" title="{{ $log->message }}">
                                            {{ $log->message }}
                                        </span>
                                        <div class="d-flex align-items-center mt-1 gap-2">
                                            <span class="method-badge {{ strtolower($log->method) }}">{{ $log->method }}</span>
                                            <code class="text-muted small text-truncate" style="max-width: 300px;">{{ $log->url }}</code>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="name-badge text-truncate" style="max-width: 200px;">{{ basename($log->file) }}</span>
                                        <span class="text-muted small mt-1">Line: {{ $log->line }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-dark small fw-medium">{{ \Carbon\Carbon::parse($log->created_at)->format('Y-m-d H:i:s') }}</div>
                                    <div class="text-muted" style="font-size: 0.7rem;">{{ \Carbon\Carbon::parse($log->created_at)->diffForHumans() }}</div>
                                </td>
                                <td class="text-center">
                                    @if($log->is_resolved)
                                        <span class="badge bg-soft-success text-success border-0 px-3 py-2 rounded-pill">
                                            <i class="fas fa-check-circle me-1"></i>تم الحل
                                        </span>
                                    @else
                                        <span class="badge bg-soft-danger text-danger border-0 px-3 py-2 rounded-pill">
                                            <i class="fas fa-exclamation-circle me-1"></i>لم يحل
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="action-buttons">
                                        <button onclick="showDetails({{ $log->id }})" class="btn-icon btn-view" title="عرض التفاصيل">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @if(!$log->is_resolved)
                                        <form action="{{ route('admin.errors.resolve', $log->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn-icon btn-copy" style="background: var(--success-soft); color: var(--success);" title="تحديد كتم الحل">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="py-4">
                                        <i class="fas fa-shield-alt text-muted display-1 opacity-25 mb-3"></i>
                                        <p class="text-muted fs-5">لا توجد أخطاء مسجلة حالياً. النظام يعمل بكفاءة عالية!</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($logs->hasPages())
                <div class="p-4 border-top border-light">
                    {{ $logs->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Details Modal -->
<div class="modal fade" id="detailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content glass-card overflow-hidden border-0">
            <div class="header-gradient p-4 position-relative">
                <div class="position-relative z-1 d-flex justify-content-between align-items-center">
                    <h5 class="modal-title text-white fw-bold">
                        <i class="fas fa-bug me-2"></i>تفاصيل الخطأ التقنية
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body p-4 bg-white">
                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <div class="p-3 rounded-4 bg-light">
                            <label class="text-muted small fw-bold text-uppercase mb-2 d-block">الرسالة</label>
                            <p id="modalMessage" class="text-danger fw-bold mb-0"></p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 rounded-4 bg-light text-center">
                            <label class="text-muted small fw-bold text-uppercase mb-2 d-block">الكود</label>
                            <p id="modalCode" class="fw-bold mb-0"></p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 rounded-4 bg-light text-center">
                            <label class="text-muted small fw-bold text-uppercase mb-2 d-block">الوقت</label>
                            <p id="modalTime" class="fw-bold mb-0"></p>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="p-3 rounded-4 bg-light">
                            <label class="text-muted small fw-bold text-uppercase mb-2 d-block">المسار (URL)</label>
                            <code id="modalUrl" class="text-primary fw-bold"></code>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="p-3 rounded-4 bg-light">
                            <label class="text-muted small fw-bold text-uppercase mb-2 d-block">الملف</label>
                            <code id="modalFile" class="text-dark"></code>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 rounded-4 bg-light text-center">
                            <label class="text-muted small fw-bold text-uppercase mb-2 d-block">رقم السطر</label>
                            <span id="modalLine" class="badge bg-dark px-3 py-2 rounded-pill"></span>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="text-muted small fw-bold text-uppercase mb-2 d-block">بيانات الطلب (Request Data)</label>
                    <pre id="modalRequest" class="bg-dark text-success p-3 rounded-4 small overflow-auto" style="max-height: 200px;"></pre>
                </div>

                <div>
                    <label class="text-muted small fw-bold text-uppercase mb-2 d-block">تتبع الخطأ (Stack Trace)</label>
                    <pre id="modalTrace" class="bg-dark text-light p-3 rounded-4 small overflow-auto custom-scrollbar" style="max-height: 400px; font-size: 0.75rem;"></pre>
                </div>
            </div>
            <div class="modal-footer border-0 p-4">
                <button type="button" class="btn btn-secondary px-4 py-2 rounded-3" data-bs-dismiss="modal">إغلاق النافذة</button>
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

<style>
/* Root Variables */
:root {
    --primary: #4361ee;
    --primary-soft: rgba(67, 97, 238, 0.1);
    --success: #10b981;
    --success-soft: rgba(16, 185, 129, 0.1);
    --warning: #f59e0b;
    --warning-soft: rgba(245, 158, 11, 0.1);
    --info: #3b82f6;
    --info-soft: rgba(59, 130, 246, 0.1);
    --danger: #ef4444;
    --danger-soft: rgba(239, 68, 68, 0.1);
    --glass: rgba(255, 255, 255, 0.85);
    --glass-border: rgba(255, 255, 255, 0.3);
}

body {
    background: #f8f9fa;
    background-image: 
        radial-gradient(at 0% 0%, rgba(67, 97, 238, 0.05) 0px, transparent 50%),
        radial-gradient(at 100% 0%, rgba(239, 68, 68, 0.03) 0px, transparent 50%);
    font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif;
}

/* Glassmorphism Components */
.glass-card {
    background: var(--glass);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border: 1px solid var(--glass-border);
    border-radius: 24px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.04);
    transition: all 0.3s ease;
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
.bg-soft-danger { background: var(--danger-soft); }

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

/* Method Badges */
.method-badge {
    padding: 2px 8px;
    border-radius: 6px;
    font-size: 0.65rem;
    font-weight: 800;
    letter-spacing: 0.5px;
}

.method-badge.get { background: #dcfce7; color: #166534; }
.method-badge.post { background: #fef9c3; color: #854d0e; }
.method-badge.put { background: #e0f2fe; color: #075985; }
.method-badge.delete { background: #fee2e2; color: #991b1b; }

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

.name-badge {
    background: #f1f5f9;
    color: #334155;
    padding: 4px 10px;
    border-radius: 8px;
    font-size: 0.75rem;
    font-weight: 600;
}

.badge-count {
    background: var(--primary-soft);
    color: var(--primary);
    padding: 4px 12px;
    border-radius: 10px;
    font-size: 0.8rem;
    font-weight: 600;
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

.custom-scrollbar::-webkit-scrollbar { width: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #334155; border-radius: 10px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #475569; }

/* Modal Styling */
.modal-content.glass-card {
    border-radius: 24px;
    background: white;
}
</style>

<script>
// Route Scanner Logic
document.getElementById('scanRoutesBtn').addEventListener('click', function() {
    const section = document.getElementById('scanResultsSection');
    const loading = document.getElementById('scanLoading');
    const content = document.getElementById('scanContent');
    const summary = document.getElementById('scanSummary');
    const resultsBody = document.getElementById('scanResultsBody');
    
    section.style.display = 'block';
    loading.style.display = 'block';
    content.style.display = 'none';
    summary.textContent = 'جارِ الفحص...';
    resultsBody.innerHTML = '';
    
    // Smooth scroll to section
    section.scrollIntoView({ behavior: 'smooth', block: 'start' });

    fetch("{{ route('admin.errors.scan') }}")
        .then(response => response.json())
        .then(data => {
            loading.style.display = 'none';
            content.style.display = 'block';
            summary.textContent = `تم فحص ${data.total_scanned} روت - وجد ${data.errors_found} أخطاء`;
            
            if (data.results.length === 0) {
                resultsBody.innerHTML = `
                    <tr>
                        <td colspan="4" class="text-center py-4">
                            <div class="text-success fw-bold">
                                <i class="fas fa-check-circle me-2"></i>
                                جميع الروتات التي تم فحصها تعمل بشكل صحيح!
                            </div>
                        </td>
                    </tr>
                `;
            } else {
                data.results.forEach(result => {
                    const statusClass = result.status >= 500 ? 'bg-soft-danger text-danger' : 'bg-soft-warning text-warning';
                    resultsBody.innerHTML += `
                        <tr class="route-row">
                            <td><code class="text-primary fw-bold">${result.uri}</code></td>
                            <td><span class="name-badge">${result.name || 'N/A'}</span></td>
                            <td class="text-center">
                                <span class="badge ${statusClass} px-3 py-2 rounded-pill fw-bold">
                                    ${result.status}
                                </span>
                            </td>
                            <td><span class="text-danger small fw-medium">${result.error_message}</span></td>
                        </tr>
                    `;
                });
            }
        })
        .catch(error => {
            loading.style.display = 'none';
            summary.textContent = 'حدث خطأ أثناء الفحص';
            showToast('فشل الاتصال بالخادم لإجراء الفحص');
        });
});

function showDetails(id) {
    fetch(`/admin/errors/show/${id}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('modalMessage').textContent = data.message;
            document.getElementById('modalCode').textContent = data.code || 'N/A';
            document.getElementById('modalFile').textContent = data.file;
            document.getElementById('modalLine').textContent = data.line;
            document.getElementById('modalUrl').textContent = `${data.method} ${data.url}`;
            document.getElementById('modalTime').textContent = new Date(data.created_at).toLocaleString();
            
            try {
                const requestData = JSON.parse(data.request_data);
                document.getElementById('modalRequest').textContent = JSON.stringify(requestData, null, 4);
            } catch(e) {
                document.getElementById('modalRequest').textContent = data.request_data;
            }
            
            document.getElementById('modalTrace').textContent = data.trace;
            
            // Using Bootstrap modal
            const myModal = new bootstrap.Modal(document.getElementById('detailsModal'));
            myModal.show();
        });
}

// Toast notification
function showToast(message) {
    const toastElement = document.getElementById('toast');
    const toastMessage = document.getElementById('toastMessage');
    toastMessage.textContent = message;
    
    toastElement.style.display = 'block';
    toastElement.style.opacity = '0';
    toastElement.style.transform = 'translateY(20px)';
    
    setTimeout(() => {
        toastElement.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
        toastElement.style.opacity = '1';
        toastElement.style.transform = 'translateY(0)';
    }, 10);
    
    setTimeout(() => {
        toastElement.style.opacity = '0';
        toastElement.style.transform = 'translateY(20px)';
        setTimeout(() => { toastElement.style.display = 'none'; }, 300);
    }, 3000);
}

@if(session('success'))
    document.addEventListener('DOMContentLoaded', function() {
        showToast("{{ session('success') }}");
    });
@endif
</script>
@endsection

@extends('admin.layouts.admin')
@section('title', 'الفواتير')
@section('content')
<style>
/* استايل موحد لواجهة الموقع */
.main-content {
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px;
    background: linear-gradient(135deg, #F8F9FC 0%, #F1F3F8 100%);
    min-height: 100vh;
    position: relative;
}

.main-content::before {
    content: '';
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: 
        radial-gradient(ellipse 60% 40% at 80% 5%, rgba(13,110,253,.04) 0%, transparent 55%),
        radial-gradient(ellipse 50% 50% at 5% 90%, rgba(111,66,193,.03) 0%, transparent 55%);
    pointer-events: none;
    z-index: -1;
}

.sec-head {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 32px;
    padding: 20px 24px;
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(20px);
    border-radius: 20px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.06);
}

.sec-accent {
    width: 6px;
    height: 32px;
    background: linear-gradient(135deg, #0D6EFD, #6F42C1);
    border-radius: 3px;
}

.sec-title {
    font-size: 28px;
    font-weight: 800;
    color: #1A1F36;
    font-family: 'Cairo', 'Plus Jakarta Sans', sans-serif;
}

.sec-pill {
    margin-left: auto;
    padding: 8px 16px;
    background: linear-gradient(135deg, #6F42C1, #59359a);
    color: white;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    letter-spacing: 0.5px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 24px;
    margin-bottom: 32px;
}

.stat-box {
    padding: 28px;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 20px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.06);
    display: flex;
    align-items: center;
    gap: 20px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.stat-box::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #0D6EFD, #6F42C1, #198754, #FD7E14);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.stat-box:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.1);
}

.stat-box:hover::before {
    opacity: 1;
}

.sb-icon {
    width: 64px;
    height: 64px;
    border-radius: 16px;
    background: linear-gradient(135deg, #0D6EFD, #0a58ca);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: white;
    box-shadow: 0 6px 24px rgba(13, 110, 253, 0.3);
}

.sb-icon.c-green {
    background: linear-gradient(135deg, #198754, #146c43);
    box-shadow: 0 6px 24px rgba(25, 135, 84, 0.3);
}

.sb-icon.c-orange {
    background: linear-gradient(135deg, #FD7E14, #dc6a0c);
    box-shadow: 0 6px 24px rgba(253, 126, 20, 0.3);
}

.sb-icon.c-blue {
    background: linear-gradient(135deg, #0DCAF0, #0b8fa0);
    box-shadow: 0 6px 24px rgba(13, 202, 240, 0.3);
}

.sb-data {
    flex: 1;
}

.sb-val {
    font-size: 32px;
    font-weight: 800;
    color: #1A1F36;
    margin-bottom: 4px;
    font-family: 'Cairo', sans-serif;
}

.sb-lbl {
    font-size: 14px;
    color: #6B7280;
    font-weight: 500;
}

.panel {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 20px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.06);
    margin-bottom: 24px;
    overflow: hidden;
}

.panel-head {
    padding: 24px 28px;
    border-bottom: 1px solid rgba(229, 231, 235, 0.5);
    background: rgba(248, 249, 252, 0.5);
}

.panel-title {
    font-size: 20px;
    font-weight: 700;
    color: #1A1F36;
    font-family: 'Cairo', sans-serif;
}

.panel-body {
    padding: 28px;
}

.form-group {
    margin-bottom: 0;
}

.form-label {
    font-size: 13px;
    font-weight: 600;
    color: #374151;
    margin-bottom: 6px;
    display: block;
}

.form-select, .form-control {
    padding: 10px 14px;
    border: 2px solid #E5E7EB;
    border-radius: 12px;
    font-size: 14px;
    transition: all 0.3s ease;
    background: white;
    min-width: 160px;
}

.form-select:focus, .form-control:focus {
    outline: none;
    border-color: #0D6EFD;
    box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1);
}

.tbtn {
    padding: 10px 20px;
    border: none;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    font-family: 'Cairo', sans-serif;
}

.tbtn-blue {
    background: linear-gradient(135deg, #0D6EFD, #0a58ca);
    color: white;
    box-shadow: 0 4px 16px rgba(13, 110, 253, 0.3);
}

.tbtn-blue:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 24px rgba(13, 110, 253, 0.4);
}

.tbtn-green {
    background: linear-gradient(135deg, #198754, #146c43);
    color: white;
    box-shadow: 0 4px 16px rgba(25, 135, 84, 0.3);
}

.tbtn-green:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 24px rgba(25, 135, 84, 0.4);
}

.tbtn-ghost {
    background: rgba(255, 255, 255, 0.8);
    color: #6B7280;
    border: 2px solid #E5E7EB;
}

.tbtn-ghost:hover {
    background: #F9FAFB;
    border-color: #D1D5DB;
    color: #374151;
}

.table-responsive {
    border-radius: 16px;
    overflow: hidden;
}

.table {
    width: 100%;
    border-collapse: collapse;
    background: white;
}

.table th {
    background: linear-gradient(135deg, #F8F9FC, #F1F3F8);
    padding: 16px 20px;
    text-align: right;
    font-weight: 700;
    font-size: 13px;
    color: #374151;
    border-bottom: 2px solid #E5E7EB;
    font-family: 'Cairo', sans-serif;
}

.table td {
    padding: 16px 20px;
    border-bottom: 1px solid #F3F4F6;
    font-size: 14px;
    color: #1A1F36;
}

.table tbody tr:hover {
    background: rgba(13, 110, 253, 0.02);
}

.badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    display: inline-block;
}

.badge-paid {
    background: linear-gradient(135deg, #198754, #146c43);
    color: white;
}

.badge-pending {
    background: linear-gradient(135deg, #FD7E14, #dc6a0c);
    color: white;
}

.badge-overdue {
    background: linear-gradient(135deg, #DC3545, #b02a37);
    color: white;
}

.badge-void {
    background: linear-gradient(135deg, #6B7280, #4B5563);
    color: white;
}

.btn-action {
    padding: 6px 10px;
    border: none;
    border-radius: 8px;
    font-size: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
    margin: 0 2px;
}

.btn-view {
    background: linear-gradient(135deg, #0D6EFD, #0a58ca);
    color: white;
}

.btn-view:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 16px rgba(13, 110, 253, 0.3);
}

.btn-download {
    background: linear-gradient(135deg, #6F42C1, #59359a);
    color: white;
}

.btn-download:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 16px rgba(111, 66, 193, 0.3);
}

/* Animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.anim { animation: fadeInUp 0.6s ease-out; }
.anim.a1 { animation-delay: 0.1s; }
.anim.a2 { animation-delay: 0.2s; }
.anim.a3 { animation-delay: 0.3s; }
.anim.a4 { animation-delay: 0.4s; }
.anim.a5 { animation-delay: 0.5s; }
.anim.a6 { animation-delay: 0.6s; }
.anim.a7 { animation-delay: 0.7s; }
</style>

        <div class="main-content">
    <div class="sec-head anim a1">
        <div class="sec-accent"></div>
        <div class="sec-title">الفواتير</div>
        <div class="sec-pill">Invoices Management</div>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid mb-4">
        <div class="stat-box anim a2">
            <div class="sb-icon"><i class="fas fa-file-invoice"></i></div>
            <div class="sb-data">
                <div class="sb-val" id="totalInvoices">-</div>
                <div class="sb-lbl">إجمالي الفواتير</div>
            </div>
        </div>
        <div class="stat-box anim a3">
            <div class="sb-icon c-green"><i class="fas fa-check-circle"></i></div>
            <div class="sb-data">
                <div class="sb-val" id="paidInvoices">-</div>
                <div class="sb-lbl">مدفوعة</div>
            </div>
        </div>
        <div class="stat-box anim a4">
            <div class="sb-icon c-orange"><i class="fas fa-clock"></i></div>
            <div class="sb-data">
                <div class="sb-val" id="pendingInvoices">-</div>
                <div class="sb-lbl">معلقة</div>
            </div>
        </div>
        <div class="stat-box anim a5">
            <div class="sb-icon c-blue"><i class="fas fa-dollar-sign"></i></div>
            <div class="sb-data">
                <div class="sb-val" id="totalAmount">-</div>
                <div class="sb-lbl">إجمالي المبلغ</div>
            </div>
        </div>
    </div>

    <!-- Actions & Filters -->
    <div class="panel anim a6 mb-4">
        <div class="panel-body">
            <form method="GET" class="d-flex flex-wrap gap-3 align-items-end justify-content-between w-100">
                <div class="d-flex flex-wrap gap-3 align-items-end">
                    <div class="form-group mb-0">
                        <label class="form-label fw-bold mb-1">الحالة</label>
                        <select name="status" class="form-select">
                            <option value="">الكل</option>
                            <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>مدفوعة</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>معلقة</option>
                            <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>متأخرة</option>
                            <option value="void" {{ request('status') == 'void' ? 'selected' : '' }}>ملغاة</option>
                        </select>
                    </div>
                    <div class="form-group mb-0">
                        <label class="form-label fw-bold mb-1">من تاريخ</label>
                        <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                    </div>
                    <div class="form-group mb-0">
                        <label class="form-label fw-bold mb-1">إلى تاريخ</label>
                        <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                    </div>
                    <button type="submit" class="tbtn tbtn-blue"><i class="fas fa-search"></i> بحث</button>
                </div>
                <div class="d-flex gap-2">
                     <button type="button" onclick="exportInvoices()" class="tbtn tbtn-ghost"><i class="fas fa-download"></i> تصدير</button>
                     <a href="{{ route('subscriptions.plans.index') }}" class="tbtn tbtn-green"><i class="fas fa-plus"></i> اشتراك جديد</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="panel anim a7">
        <div class="panel-head">
            <div class="panel-title">قائمة الفواتير</div>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>رقم الفاتورة</th>
                        <th>التاريخ</th>
                        <th>الخطة</th>
                        <th>المبلغ</th>
                        <th>الحالة</th>
                        <th>تاريخ الاستحقاق</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $invoice)
                    <tr>
                        <td class="fw-bold text-primary">#{{ $invoice->id }}</td>
                        <td>{{ $invoice->created_at->format('Y-m-d') }}</td>
                        <td>{{ $invoice->subscription->plan->name ?? '-' }}</td>
                        <td class="fw-bold">{{ number_format($invoice->amount, 2) }} {{ $invoice->currency }}</td>
                        <td><span class="badge badge-{{ $invoice->status }}">{{ $invoice->status == 'paid' ? 'مدفوعة' : ($invoice->status == 'pending' ? 'معلقة' : ($invoice->status == 'overdue' ? 'متأخرة' : 'ملغاة')) }}</span></td>
                        <td>{{ $invoice->due_date ? $invoice->due_date->format('Y-m-d') : '-' }}</td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('subscriptions.invoices.show', $invoice) }}" class="btn-action btn-view" title="عرض"><i class="fas fa-eye"></i></a>
                                @if($invoice->status === 'paid')
                                <button onclick="downloadInvoice({{ $invoice->id }})" class="btn-action btn-download" title="تحميل"><i class="fas fa-download"></i></button>
                                @endif
                                @if($invoice->status === 'pending' && !$invoice->isOverdue())
                                    <a href="{{ route('subscriptions.invoices.pay', $invoice) }}" class="btn-action btn-view" title="دفع"><i class="fas fa-credit-card"></i></a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="empty-state" style="padding: 40px;">
                                <div style="width: 80px; height: 80px; margin: 0 auto 20px; background: linear-gradient(135deg, #F8F9FC, #F1F3F8); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-file-invoice" style="font-size: 32px; color: #6B7280;"></i>
                                </div>
                                <p style="font-size: 18px; font-weight: 700; color: #374151; margin-bottom: 8px;">لا توجد فواتير</p>
                                <p style="font-size: 14px; color: #9CA3AF;">لم يتم إنشاء أي فواتير بعد</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($invoices, 'links') && $invoices->hasPages())
        <div class="panel-footer" style="padding: 20px 28px; border-top: 1px solid rgba(229, 231, 235, 0.5); background: rgba(248, 249, 252, 0.5);">
            {{ $invoices->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    loadStats();
});

function loadStats() {
    fetch('/subscriptions/invoices/stats')
        .then(response => response.json())
        .then(data => {
            animateValue('totalInvoices', data.total_invoices || 0, 1000);
            animateValue('paidInvoices', data.paid_invoices || 0, 1000);
            animateValue('pendingInvoices', data.pending_invoices || 0, 1000);
            animateValue('totalAmount', data.total_amount || 0, 2000, true);
        })
        .catch(error => console.error('Error loading stats:', error));
}

function animateValue(elementId, endValue, duration, isCurrency = false) {
    const element = document.getElementById(elementId);
    if(!element) return;
    const startValue = 0;
    const startTime = performance.now();
    
    function updateValue(currentTime) {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        const easeOutQuart = 1 - Math.pow(1 - progress, 4);
        const currentValue = startValue + (endValue - startValue) * easeOutQuart;
        
        if (isCurrency) {
            element.textContent = new Intl.NumberFormat('ar-SA', { style: 'currency', currency: 'SAR' }).format(currentValue);
        } else {
            element.textContent = Math.floor(currentValue).toLocaleString('ar-SA');
        }
        
        if (progress < 1) requestAnimationFrame(updateValue);
    }
    requestAnimationFrame(updateValue);
}

function downloadInvoice(invoiceId) {
    window.open(`/subscriptions/invoices/${invoiceId}/download`, '_blank');
}

function exportInvoices() {
    const url = new URL(window.location.origin + '/subscriptions/invoices/export');
    url.searchParams.set('format', 'csv');
    const params = new URLSearchParams(window.location.search);
    for (const [key, value] of params) {
        if (key !== 'page') url.searchParams.set(key, value);
    }
    window.location.href = url.toString();
}
</script>
@endpush
@extends('admin.layouts.admin')

@section('title', 'تفاصيل الفاتورة')
@section('page-title', 'تفاصيل الفاتورة')

@push('styles')
<style>
.invoice-container {
    max-width: 900px;
    margin: 0 auto;
    padding: 2rem;
}

.invoice-card {
    background: var(--bg-glass);
    backdrop-filter: blur(25px);
    border: 1px solid rgba(255, 255, 255, 0.4);
    border-radius: var(--radius-lg);
    padding: 2rem;
    box-shadow: var(--shadow-soft);
}

.invoice-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 2px solid rgba(102, 126, 234, 0.2);
}

.invoice-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0 0 0.5rem 0;
}

.invoice-meta {
    color: var(--text-secondary);
    font-size: 0.9rem;
}

.invoice-number {
    font-size: 2rem;
    font-weight: 700;
    color: var(--primary-blue);
    font-family: var(--ff-d);
}

.invoice-status {
    display: inline-block;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 600;
    margin-top: 0.5rem;
}

.status-paid {
    background: rgba(67, 233, 123, 0.2);
    color: var(--accent-emerald);
}

.status-pending {
    background: rgba(255, 193, 7, 0.2);
    color: var(--accent-yellow);
}

.status-overdue {
    background: rgba(240, 147, 251, 0.2);
    color: var(--accent-pink);
}

.status-void {
    background: rgba(160, 174, 192, 0.2);
    color: var(--text-ghost);
}

.invoice-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
    margin-bottom: 2rem;
}

.invoice-section {
    background: rgba(255, 255, 255, 0.5);
    border-radius: var(--radius-md);
    padding: 1.5rem;
}

.section-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.info-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
    border-bottom: 1px solid rgba(102, 126, 234, 0.1);
}

.info-row:last-child {
    border-bottom: none;
}

.info-label {
    color: var(--text-secondary);
    font-size: 0.9rem;
}

.info-value {
    color: var(--text-primary);
    font-weight: 600;
    font-size: 0.9rem;
}

.amount-highlight {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--primary-blue);
    font-family: var(--ff-d);
}

.items-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 2rem;
}

.items-table th {
    background: rgba(102, 126, 234, 0.1);
    padding: 1rem;
    text-align: right;
    font-weight: 600;
    color: var(--text-primary);
    font-size: 0.9rem;
    border-bottom: 2px solid rgba(102, 126, 234, 0.2);
}

.items-table td {
    padding: 1rem;
    border-bottom: 1px solid rgba(102, 126, 234, 0.1);
    color: var(--text-primary);
}

.items-table .amount {
    font-weight: 600;
    text-align: left;
}

.action-buttons {
    display: flex;
    gap: 1rem;
    justify-content: center;
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 2px solid rgba(102, 126, 234, 0.2);
}

.btn {
    padding: 0.75rem 1.5rem;
    border-radius: var(--radius-md);
    border: none;
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition-smooth);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-primary {
    background: var(--primary-gradient);
    color: white;
    box-shadow: var(--shadow-blue);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-large);
}

.btn-secondary {
    background: transparent;
    color: var(--text-secondary);
    border: 2px solid rgba(102, 126, 234, 0.3);
}

.btn-secondary:hover {
    background: rgba(102, 126, 234, 0.1);
    border-color: var(--primary-blue);
    color: var(--primary-blue);
}

.btn-success {
    background: var(--gradient-forest);
    color: white;
}

.btn-success:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-emerald);
}

.payment-info {
    background: rgba(79, 172, 254, 0.1);
    border: 1px solid rgba(79, 172, 254, 0.3);
    border-radius: var(--radius-md);
    padding: 1rem;
    margin-top: 1rem;
}

.payment-info h4 {
    color: var(--accent-cyan);
    margin-bottom: 0.5rem;
}

@media (max-width: 768px) {
    .invoice-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .invoice-header {
        flex-direction: column;
        gap: 1rem;
    }
    
    .action-buttons {
        flex-direction: column;
    }
}
</style>
@endpush

@section('content')
<div class="invoice-container">
    <div class="invoice-card">
        <!-- Invoice Header -->
        <div class="invoice-header">
            <div>
                <h1 class="invoice-title">فاتورة اشتراك</h1>
                <div class="invoice-meta">
                    صدرت في {{ $invoice->created_at->format('Y-m-d H:i') }}
                </div>
            </div>
            <div style="text-align: left;">
                <div class="invoice-number">#{{ $invoice->id }}</div>
                <div class="invoice-status status-{{ $invoice->status }}">
                    @switch($invoice->status)
                        @case('paid')
                            مدفوعة
                            @break
                        @case('pending')
                            {{ $invoice->isOverdue() ? 'متأخرة' : 'معلقة' }}
                            @break
                        @case('void')
                            ملغاة
                            @break
                        @default
                            {{ $invoice->status }}
                    @endswitch
                </div>
            </div>
        </div>

        <!-- Invoice Grid -->
        <div class="invoice-grid">
            <!-- Billing Information -->
            <div class="invoice-section">
                <h3 class="section-title">
                    📍 معلومات الفوترة
                </h3>
                <div class="info-row">
                    <span class="info-label">العميل</span>
                    <span class="info-value">
                        @if($invoice->subscription && $invoice->subscription->user)
                            {{ $invoice->subscription->user->name }}
                        @else
                            -
                        @endif
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">البريد الإلكتروني</span>
                    <span class="info-value">
                        @if($invoice->subscription && $invoice->subscription->user)
                            {{ $invoice->subscription->user->email }}
                        @else
                            -
                        @endif
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">تاريخ الفوترة</span>
                    <span class="info-value">{{ $invoice->billing_date->format('Y-m-d') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">تاريخ الاستحقاق</span>
                    <span class="info-value">{{ $invoice->due_date->format('Y-m-d') }}</span>
                </div>
            </div>

            <!-- Subscription Information -->
            <div class="invoice-section">
                <h3 class="section-title">
                    📋 معلومات الاشتراك
                </h3>
                <div class="info-row">
                    <span class="info-label">الخطة</span>
                    <span class="info-value">
                        @if($invoice->subscription && $invoice->subscription->plan)
                            {{ $invoice->subscription->plan->name }}
                        @else
                            -
                        @endif
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">رقم الاشتراك</span>
                    <span class="info-value">
                        @if($invoice->subscription)
                            #{{ $invoice->subscription->id }}
                        @else
                            -
                        @endif
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">العملة</span>
                    <span class="info-value">{{ $invoice->currency }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">المبلغ الإجمالي</span>
                    <span class="info-value amount-highlight">
                        {{ number_format($invoice->amount, 2) }} {{ $invoice->currency }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Invoice Items -->
        @if($invoice->items && count($invoice->items) > 0)
            <div class="invoice-section">
                <h3 class="section-title">
                    📦 تفاصيل المنتجات
                </h3>
                <table class="items-table">
                    <thead>
                        <tr>
                            <th>الوصف</th>
                            <th>الكمية</th>
                            <th>السعر</th>
                            <th>الإجمالي</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->items as $item)
                            <tr>
                                <td>{{ $item['description'] ?? '-' }}</td>
                                <td>{{ $item['quantity'] ?? 1 }}</td>
                                <td>{{ number_format($item['amount'] ?? 0, 2) }}</td>
                                <td class="amount">
                                    {{ number_format(($item['amount'] ?? 0) * ($item['quantity'] ?? 1), 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <!-- Payment Information -->
        @if($invoice->status === 'paid')
            <div class="invoice-section">
                <h3 class="section-title">
                    💳 معلومات الدفع
                </h3>
                <div class="info-row">
                    <span class="info-label">تاريخ الدفع</span>
                    <span class="info-value">{{ $invoice->paid_at->format('Y-m-d H:i') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">طريقة الدفع</span>
                    <span class="info-value">{{ $invoice->payment_method ?? '-' }}</span>
                </div>
                @if($invoice->transaction_id)
                    <div class="info-row">
                        <span class="info-label">رقم المعاملة</span>
                        <span class="info-value">{{ $invoice->transaction_id }}</span>
                    </div>
                @endif
                @if($invoice->payment_notes)
                    <div class="info-row">
                        <span class="info-label">ملاحظات الدفع</span>
                        <span class="info-value">{{ $invoice->payment_notes }}</span>
                    </div>
                @endif
            </div>
        @endif

        <!-- Void Information -->
        @if($invoice->status === 'void')
            <div class="invoice-section">
                <h3 class="section-title">
                    ❌ معلومات الإلغاء
                </h3>
                <div class="info-row">
                    <span class="info-label">تاريخ الإلغاء</span>
                    <span class="info-value">{{ $invoice->voided_at->format('Y-m-d H:i') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">سبب الإلغاء</span>
                    <span class="info-value">{{ $invoice->void_reason ?? '-' }}</span>
                </div>
            </div>
        @endif

        <!-- Action Buttons -->
        <div class="action-buttons">
            <a href="{{ route('subscriptions.invoices.index') }}" class="btn btn-secondary">
                ← العودة للفواتير
            </a>
            
            @if($invoice->status === 'paid')
                <button onclick="downloadInvoice()" class="btn btn-primary">
                    📥 تحميل PDF
                </button>
            @endif
            
            @if($invoice->status === 'pending' && !$invoice->isOverdue())
                <a href="{{ route('subscriptions.invoices.pay', $invoice) }}" class="btn btn-success">
                    💳 دفع الفاتورة
                </a>
            @endif
            
            @if($invoice->status === 'pending')
                <button onclick="sendInvoiceEmail()" class="btn btn-secondary">
                    📧 إرسال بالبريد
                </button>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function downloadInvoice() {
    window.open(`/subscriptions/invoices/{{ $invoice->id }}/download`, '_blank');
}

function sendInvoiceEmail() {
    if (confirm('هل تريد إرسال هذه الفاتورة بالبريد الإلكتروني؟')) {
        fetch(`/subscriptions/invoices/{{ $invoice->id }}/send-email`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({})
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('تم إرسال الفاتورة بنجاح!');
            } else {
                alert('فشل في إرسال الفاتورة: ' + (data.message || 'خطأ غير معروف'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ أثناء إرسال الفاتورة');
        });
    }
}

// Auto-refresh if status is pending
@if($invoice->status === 'pending')
setTimeout(() => {
    window.location.reload();
}, 30000); // Refresh every 30 seconds
@endif
</script>
@endpush

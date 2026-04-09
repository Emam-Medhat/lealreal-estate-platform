@extends('admin.layouts.admin')

@section('title', 'تعديل الفاتورة')
@section('page-title', 'تعديل الفاتورة')

@push('styles')
<style>
.edit-invoice-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 2rem;
}

.form-card {
    background: var(--bg-glass);
    backdrop-filter: blur(25px);
    border: 1px solid rgba(255, 255, 255, 0.4);
    border-radius: var(--radius-lg);
    padding: 2rem;
    box-shadow: var(--shadow-soft);
}

.form-header {
    text-align: center;
    margin-bottom: 2rem;
}

.form-title {
    font-size: 1.8rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.form-subtitle {
    color: var(--text-secondary);
    font-size: 0.95rem;
}

.invoice-info {
    background: rgba(79, 172, 254, 0.1);
    border: 1px solid rgba(79, 172, 254, 0.3);
    border-radius: var(--radius-md);
    padding: 1rem;
    margin-bottom: 2rem;
}

.invoice-info h3 {
    color: var(--accent-cyan);
    margin-bottom: 0.5rem;
}

.invoice-info p {
    margin: 0.25rem 0;
    color: var(--text-primary);
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-label {
    display: block;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
}

.form-control {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 2px solid rgba(102, 126, 234, 0.2);
    border-radius: var(--radius-md);
    background: rgba(255, 255, 255, 0.9);
    color: var(--text-primary);
    font-size: 0.95rem;
    transition: var(--transition-smooth);
}

.form-control:focus {
    outline: none;
    border-color: var(--primary-blue);
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-control:disabled {
    background: rgba(160, 174, 192, 0.2);
    cursor: not-allowed;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }
}

.btn-primary {
    background: var(--primary-gradient);
    color: white;
    border: none;
    padding: 0.75rem 2rem;
    border-radius: var(--radius-md);
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    transition: var(--transition-smooth);
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
    padding: 0.75rem 2rem;
    border-radius: var(--radius-md);
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    transition: var(--transition-smooth);
    margin-right: 1rem;
}

.btn-secondary:hover {
    background: rgba(102, 126, 234, 0.1);
    border-color: var(--primary-blue);
    color: var(--primary-blue);
}

.form-actions {
    display: flex;
    justify-content: center;
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid rgba(102, 126, 234, 0.1);
}

.items-section {
    background: rgba(255, 255, 255, 0.5);
    border-radius: var(--radius-md);
    padding: 1.5rem;
    margin-bottom: 1.5rem;
}

.items-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.items-title {
    font-weight: 600;
    color: var(--text-primary);
}

.add-item-btn {
    background: var(--gradient-forest);
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: var(--radius-sm);
    font-size: 0.8rem;
    cursor: pointer;
    transition: var(--transition-smooth);
}

.add-item-btn:hover {
    transform: translateY(-1px);
}

.item-row {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr auto;
    gap: 0.5rem;
    align-items: center;
    margin-bottom: 0.5rem;
}

.remove-item-btn {
    background: var(--gradient-sunset);
    color: white;
    border: none;
    padding: 0.5rem;
    border-radius: var(--radius-sm);
    cursor: pointer;
    transition: var(--transition-smooth);
}

.remove-item-btn:hover {
    transform: scale(1.1);
}

.alert {
    padding: 1rem;
    border-radius: var(--radius-md);
    margin-bottom: 1.5rem;
    border-left: 4px solid;
}

.alert-warning {
    background: rgba(255, 193, 7, 0.1);
    border-color: var(--accent-yellow);
    color: var(--text-primary);
}

.alert-danger {
    background: rgba(240, 147, 251, 0.1);
    border-color: var(--accent-pink);
    color: var(--text-primary);
}

.required {
    color: var(--accent-coral);
}
</style>
@endpush

@section('content')
<div class="edit-invoice-container">
    <div class="form-card">
        <div class="form-header">
            <h1 class="form-title">✏️ تعديل الفاتورة</h1>
            <p class="form-subtitle">فاتورة رقم #{{ $invoice->id }}</p>
        </div>

        @if($invoice->status === 'paid')
            <div class="alert alert-danger">
                <strong>⚠️ تنبيه:</strong> لا يمكن تعديل الفواتير المدفوعة
            </div>
        @endif

        <!-- Invoice Information -->
        <div class="invoice-info">
            <h3>📄 معلومات الفاتورة</h3>
            <p><strong>رقم الفاتورة:</strong> #{{ $invoice->id }}</p>
            <p><strong>الحالة:</strong> 
                @switch($invoice->status)
                    @case('paid')
                        <span style="color: var(--accent-emerald);">مدفوعة</span>
                        @break
                    @case('pending')
                        <span style="color: var(--accent-yellow);">
                            {{ $invoice->isOverdue() ? 'متأخرة' : 'معلقة' }}
                        </span>
                        @break
                    @case('void')
                        <span style="color: var(--text-ghost);">ملغاة</span>
                        @break
                    @default
                        {{ $invoice->status }}
                @endswitch
            </p>
            <p><strong>تاريخ الإنشاء:</strong> {{ $invoice->created_at->format('Y-m-d H:i') }}</p>
            <p><strong>المبلغ الحالي:</strong> {{ number_format($invoice->amount, 2) }} {{ $invoice->currency }}</p>
        </div>

        @if($invoice->status !== 'paid')
            <form action="{{ route('subscriptions.invoices.update', $invoice) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Invoice Details -->
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">المبلغ <span class="required">*</span></label>
                        <input type="number" name="amount" class="form-control" 
                               value="{{ number_format($invoice->amount, 2, '.', '') }}" 
                               placeholder="0.00" step="0.01" min="0" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">العملة</label>
                        <input type="text" name="currency" class="form-control" 
                               value="{{ $invoice->currency }}" disabled>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">الوصف <span class="required">*</span></label>
                    <textarea name="description" class="form-control" rows="3" required>{{ $invoice->description }}</textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">تاريخ الاستحقاق <span class="required">*</span></label>
                    <input type="date" name="due_date" class="form-control" 
                           value="{{ $invoice->due_date->format('Y-m-d') }}" required>
                </div>

                <!-- Invoice Items -->
                <div class="items-section">
                    <div class="items-header">
                        <h3 class="items-title">📦 تفاصيل المنتجات</h3>
                        <button type="button" class="add-item-btn" onclick="addInvoiceItem()">
                            + إضافة بند
                        </button>
                    </div>
                    <div id="itemsContainer">
                        @if($invoice->items && count($invoice->items) > 0)
                            @foreach($invoice->items as $index => $item)
                                <div class="item-row">
                                    <input type="text" name="items[{{ $index }}][description]" class="form-control" 
                                           value="{{ $item['description'] ?? '' }}" placeholder="وصف البند" required>
                                    <input type="number" name="items[{{ $index }}][amount]" class="form-control" 
                                           value="{{ number_format($item['amount'] ?? 0, 2, '.', '') }}" 
                                           placeholder="السعر" step="0.01" min="0" required>
                                    <input type="number" name="items[{{ $index }}][quantity]" class="form-control" 
                                           value="{{ $item['quantity'] ?? 1 }}" 
                                           placeholder="الكمية" min="1" required>
                                    <button type="button" class="remove-item-btn" onclick="removeInvoiceItem(this)">×</button>
                                </div>
                            @endforeach
                        @else
                            <div class="item-row">
                                <input type="text" name="items[0][description]" class="form-control" 
                                       placeholder="وصف البند" required>
                                <input type="number" name="items[0][amount]" class="form-control" 
                                       placeholder="السعر" step="0.01" min="0" required>
                                <input type="number" name="items[0][quantity]" class="form-control" 
                                       placeholder="الكمية" min="1" value="1" required>
                                <button type="button" class="remove-item-btn" onclick="removeInvoiceItem(this)">×</button>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <a href="{{ route('subscriptions.invoices.show', $invoice) }}" class="btn btn-secondary">
                        إلغاء
                    </a>
                    <button type="submit" class="btn btn-primary">
                        💾 حفظ التغييرات
                    </button>
                </div>
            </form>
        @else
            <div class="form-actions">
                <a href="{{ route('subscriptions.invoices.show', $invoice) }}" class="btn btn-primary">
                    العودة للتفاصيل
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
let itemCount = {{ $invoice->items ? count($invoice->items) : 1 }};

function addInvoiceItem() {
    const container = document.getElementById('itemsContainer');
    const itemRow = document.createElement('div');
    itemRow.className = 'item-row';
    itemRow.innerHTML = `
        <input type="text" name="items[${itemCount}][description]" class="form-control" 
               placeholder="وصف البند" required>
        <input type="number" name="items[${itemCount}][amount]" class="form-control" 
               placeholder="السعر" step="0.01" min="0" required>
        <input type="number" name="items[${itemCount}][quantity]" class="form-control" 
               placeholder="الكمية" min="1" value="1" required>
        <button type="button" class="remove-item-btn" onclick="removeInvoiceItem(this)">×</button>
    `;
    container.appendChild(itemRow);
    itemCount++;
}

function removeInvoiceItem(button) {
    const items = document.querySelectorAll('.item-row');
    if (items.length > 1) {
        button.parentElement.remove();
    } else {
        alert('يجب وجود بند واحد على الأقل');
    }
}

// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const amount = document.querySelector('input[name="amount"]').value;
    const description = document.querySelector('textarea[name="description"]').value;
    
    if (!amount || amount <= 0) {
        e.preventDefault();
        alert('الرجاء إدخال مبلغ صحيح');
        return;
    }
    
    if (!description || description.trim().length < 3) {
        e.preventDefault();
        alert('الرجاء إدخال وصف صحيح');
        return;
    }
    
    // Validate at least one item
    const items = document.querySelectorAll('.item-row');
    let validItems = 0;
    items.forEach(item => {
        const desc = item.querySelector('input[name*="description"]').value;
        const amount = item.querySelector('input[name*="amount"]').value;
        if (desc && amount && amount > 0) {
            validItems++;
        }
    });
    
    if (validItems === 0) {
        e.preventDefault();
        alert('الرجاء إضافة بند واحد على الأقل');
        return;
    }
});

// Set minimum date for due date
document.addEventListener('DOMContentLoaded', function() {
    const today = new Date().toISOString().split('T')[0];
    document.querySelector('input[name="due_date"]').min = today;
});
</script>
@endpush

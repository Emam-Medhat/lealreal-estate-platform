@extends('layouts.app')

@section('title', 'إنشاء فاتورة جديدة - Real Estate Pro')

@section('content')
<div style="background: #f8f9fa; min-height: 100vh; padding: 20px 0;">
    <div class="container">
        <!-- Header -->
        <div class="text-center mb-5">
            <h1 style="color: #2c3e50; font-size: 2.5rem; font-weight: 300; margin-bottom: 10px;">إنشاء فاتورة جديدة</h1>
            <p style="color: #7f8c8d; font-size: 1.1rem;">إنشاء فاتورة جديدة للنظام</p>
            <div class="mt-3">
                <a href="{{ route('payments.invoices.index') }}" class="btn btn-outline-secondary" style="border-radius: 10px;">
                    <i class="fas fa-arrow-right me-2"></i>
                    العودة للفواتير
                </a>
            </div>
        </div>

        <!-- Form -->
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card" style="border: none; border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
                    <div class="card-body p-5">
                        <form method="POST" action="{{ route('payments.invoices.store') }}">
                            @csrf
                            
                            <!-- Basic Information -->
                            <div class="mb-4">
                                <h5 style="color: #2c3e50; font-weight: 500; margin-bottom: 20px;">
                                    <i class="fas fa-info-circle me-2"></i>
                                    المعلومات الأساسية
                                </h5>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="user_id" class="form-label">العميل</label>
                                            <select class="form-select" id="user_id" name="user_id" required style="border-radius: 10px;">
                                                <option value="">اختر العميل</option>
                                                @if(isset($users))
                                                    @foreach($users as $user)
                                                        <option value="{{ $user->id }}">{{ $user->full_name }} ({{ $user->email }})</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            @error('user_id')
                                                <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="type" class="form-label">نوع الفاتورة</label>
                                            <select class="form-select" id="type" name="type" required style="border-radius: 10px;">
                                                <option value="">اختر النوع</option>
                                                <option value="subscription">اشتراك</option>
                                                <option value="property">عقاري</option>
                                                <option value="service">خدمة</option>
                                                <option value="penalty">غرامة</option>
                                                <option value="other">أخرى</option>
                                            </select>
                                            @error('type')
                                                <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="title" class="form-label">عنوان الفاتورة</label>
                                            <input type="text" class="form-control" id="title" name="title" required style="border-radius: 10px;">
                                            @error('title')
                                                <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="issue_date" class="form-label">تاريخ الإصدار</label>
                                            <input type="date" class="form-control" id="issue_date" name="issue_date" value="{{ now()->format('Y-m-d') }}" required style="border-radius: 10px;">
                                            @error('issue_date')
                                                <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="due_date" class="form-label">تاريخ الاستحقاق</label>
                                            <input type="date" class="form-control" id="due_date" name="due_date" required style="border-radius: 10px;">
                                            @error('due_date')
                                                <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="payment_method" class="form-label">طريقة الدفع</label>
                                            <select class="form-select" id="payment_method" name="payment_method" style="border-radius: 10px;">
                                                <option value="">اختر طريقة الدفع</option>
                                                <option value="cash">نقدي</option>
                                                <option value="card">بطاقة ائتمان</option>
                                                <option value="bank_transfer">تحويل بنكي</option>
                                                <option value="check">شيك</option>
                                                <option value="online">دفع إلكتروني</option>
                                            </select>
                                            @error('payment_method')
                                                <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">الوصف</label>
                                    <textarea class="form-control" id="description" name="description" rows="3" style="border-radius: 10px;"></textarea>
                                    @error('description')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Invoice Items -->
                            <div class="mb-4">
                                <h5 style="color: #2c3e50; font-weight: 500; margin-bottom: 20px;">
                                    <i class="fas fa-list me-2"></i>
                                    بنود الفاتورة
                                </h5>
                                
                                <div id="items-container">
                                    <div class="item-row mb-3">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <input type="text" class="form-control mb-2" name="items[0][description]" placeholder="وصف البند" required style="border-radius: 10px;">
                                            </div>
                                            <div class="col-md-2">
                                                <input type="number" class="form-control mb-2" name="items[0][quantity]" placeholder="الكمية" value="1" min="1" required style="border-radius: 10px;" oninput="calculateTotals()">
                                            </div>
                                            <div class="col-md-2">
                                                <input type="number" class="form-control mb-2" name="items[0][unit_price]" placeholder="السعر" step="0.01" min="0" required style="border-radius: 10px;" oninput="calculateTotals()">
                                            </div>
                                            <div class="col-md-2">
                                                <input type="number" class="form-control mb-2" name="items[0][total]" placeholder="الإجمالي" step="0.01" min="0" readonly style="border-radius: 10px; background: #f8f9fa;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="addItem()" style="border-radius: 8px;">
                                    <i class="fas fa-plus me-2"></i>
                                    إضافة بند
                                </button>
                            </div>

                            <!-- Financial Summary -->
                            <div class="mb-4">
                                <h5 style="color: #2c3e50; font-weight: 500; margin-bottom: 20px;">
                                    <i class="fas fa-calculator me-2"></i>
                                    الملخص المالي
                                </h5>
                                
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="subtotal" class="form-label">المجموع الفرعي</label>
                                            <input type="number" class="form-control" id="subtotal" name="subtotal" step="0.01" min="0" readonly style="border-radius: 10px; background: #f8f9fa;">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="tax_amount" class="form-label">ضريبة القيمة المضافة</label>
                                            <input type="number" class="form-control" id="tax_amount" name="tax_amount" step="0.01" min="0" readonly style="border-radius: 10px; background: #f8f9fa;">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="discount_amount" class="form-label">الخصم</label>
                                            <input type="number" class="form-control" id="discount_amount" name="discount_amount" step="0.01" min="0" value="0" style="border-radius: 10px;" oninput="calculateTotals()">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="total" class="form-label">الإجمالي</label>
                                            <input type="number" class="form-control" id="total" name="total" step="0.01" min="0" readonly style="border-radius: 10px; background: #e8f5e8; font-weight: bold; font-size: 1.2rem;">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Notes -->
                            <div class="mb-4">
                                <h5 style="color: #2c3e50; font-weight: 500; margin-bottom: 20px;">
                                    <i class="fas fa-sticky-note me-2"></i>
                                    ملاحظات
                                </h5>
                                
                                <div class="mb-3">
                                    <label for="notes" class="form-label">ملاحظات إضافية</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3" style="border-radius: 10px;"></textarea>
                                    @error('notes')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary btn-lg" style="border-radius: 10px; padding: 12px 30px;">
                                    <i class="fas fa-save me-2"></i>
                                    حفظ الفاتورة
                                </button>
                                <a href="{{ route('payments.invoices.index') }}" class="btn btn-outline-secondary btn-lg ms-2" style="border-radius: 10px; padding: 12px 30px;">
                                    <i class="fas fa-times me-2"></i>
                                    إلغاء
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let itemCount = 1;

function addItem() {
    const container = document.getElementById('items-container');
    const newItem = document.createElement('div');
    newItem.className = 'item-row mb-3';
    newItem.innerHTML = `
        <div class="row">
            <div class="col-md-6">
                <input type="text" class="form-control mb-2" name="items[${itemCount}][description]" placeholder="وصف البند" required style="border-radius: 10px;">
            </div>
            <div class="col-md-2">
                <input type="number" class="form-control mb-2" name="items[${itemCount}][quantity]" placeholder="الكمية" value="1" min="1" required style="border-radius: 10px;" onchange="calculateTotals()">
            </div>
            <div class="col-md-2">
                <input type="number" class="form-control mb-2" name="items[${itemCount}][unit_price]" placeholder="السعر" step="0.01" min="0" required style="border-radius: 10px;" onchange="calculateTotals()">
            </div>
            <div class="col-md-2">
                <input type="number" class="form-control mb-2" name="items[${itemCount}][total]" placeholder="الإجمالي" step="0.01" min="0" readonly style="border-radius: 10px; background: #f8f9fa;">
            </div>
        </div>
    `;
    container.appendChild(newItem);
    
    // Add event listeners to new inputs
    const quantityInput = newItem.querySelector(`input[name="items[${itemCount}][quantity]"]`);
    const unitPriceInput = newItem.querySelector(`input[name="items[${itemCount}][unit_price]"]`);
    
    quantityInput.addEventListener('input', calculateTotals);
    unitPriceInput.addEventListener('input', calculateTotals);
    
    itemCount++;
}

function calculateTotals() {
    let subtotal = 0;
    const items = document.querySelectorAll('.item-row');
    
    items.forEach((item, index) => {
        const quantity = parseFloat(item.querySelector(`input[name="items[${index}][quantity]"]`).value) || 0;
        const unitPrice = parseFloat(item.querySelector(`input[name="items[${index}][unit_price]"]`).value) || 0;
        const total = quantity * unitPrice;
        
        item.querySelector(`input[name="items[${index}][total]"]`).value = total.toFixed(2);
        subtotal += total;
    });
    
    const taxRate = 0.15; // 15% VAT
    const taxAmount = subtotal * taxRate;
    const discountAmount = parseFloat(document.getElementById('discount_amount').value) || 0;
    const total = subtotal + taxAmount - discountAmount;
    
    document.getElementById('subtotal').value = subtotal.toFixed(2);
    document.getElementById('tax_amount').value = taxAmount.toFixed(2);
    document.getElementById('total').value = total.toFixed(2);
}

// Add event listeners for initial item
document.addEventListener('DOMContentLoaded', function() {
    const firstItem = document.querySelector('.item-row');
    if (firstItem) {
        const quantityInput = firstItem.querySelector('input[name="items[0][quantity]"]');
        const unitPriceInput = firstItem.querySelector('input[name="items[0][unit_price]"]');
        const discountInput = document.getElementById('discount_amount');
        
        quantityInput.addEventListener('input', calculateTotals);
        unitPriceInput.addEventListener('input', calculateTotals);
        discountInput.addEventListener('input', calculateTotals);
    }
});
</script>
@endsection

@extends('layouts.app')

@section('title', 'إنشاء دفعة ضريبية - Real Estate Pro')

@section('content')
<div style="background: #f8f9fa; min-height: 100vh; padding: 20px 0;">
    <div class="container">
        <!-- Header -->
        <div class="text-center mb-5">
            <h1 style="color: #2c3e50; font-size: 2.5rem; font-weight: 300; margin-bottom: 10px;">إنشاء دفعة ضريبية جديدة</h1>
            <p style="color: #7f8c8d; font-size: 1.1rem;">إضافة دفعة ضريبية جديدة للنظام</p>
            <div class="mt-3">
                <a href="{{ route('taxes.payments.index') }}" class="btn btn-outline-secondary" style="border-radius: 10px;">
                    <i class="fas fa-arrow-right"></i> العودة للمدفوعات
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <!-- Main Form Card -->
                <div class="card mb-4" style="border: none; border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
                    <div class="card-body p-4">
                        <h5 style="color: #2c3e50; font-size: 1.3rem; font-weight: 500; margin-bottom: 20px;">
                            <i class="fas fa-dollar-sign me-2" style="color: #3498db;"></i>
                            معلومات الدفعة
                        </h5>
                        
                        <form method="POST" action="{{ route('taxes.payments.store') }}">
                            @csrf
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="amount" style="color: #2c3e50; font-weight: 500; margin-bottom: 8px; display: block;">
                                        <i class="fas fa-money-bill-wave me-1" style="color: #3498db;"></i>
                                        المبلغ *
                                    </label>
                                    <input type="number" class="form-control" id="amount" name="amount" 
                                           value="{{ old('amount') }}" placeholder="أدخل المبلغ" step="0.01" required
                                           style="border-radius: 10px; border: 1px solid #e0e0e0; padding: 12px;">
                                    @error('amount')
                                        <div style="color: #e74c3c; font-size: 0.875rem; margin-top: 5px;">
                                            <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="payment_method" style="color: #2c3e50; font-weight: 500; margin-bottom: 8px; display: block;">
                                        <i class="fas fa-credit-card me-1" style="color: #3498db;"></i>
                                        طريقة الدفع *
                                    </label>
                                    <select class="form-select" id="payment_method" name="payment_method" required
                                            style="border-radius: 10px; border: 1px solid #e0e0e0; padding: 12px;">
                                        <option value="">اختر طريقة الدفع</option>
                                        <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>نقدي</option>
                                        <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>تحويل بنكي</option>
                                        <option value="credit_card" {{ old('payment_method') == 'credit_card' ? 'selected' : '' }}>بطاقة ائتمان</option>
                                        <option value="online" {{ old('payment_method') == 'online' ? 'selected' : '' }}>دفع إلكتروني</option>
                                    </select>
                                    @error('payment_method')
                                        <div style="color: #e74c3c; font-size: 0.875rem; margin-top: 5px;">
                                            <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row g-3 mt-2">
                                <div class="col-md-6">
                                    <label for="payment_date" style="color: #2c3e50; font-weight: 500; margin-bottom: 8px; display: block;">
                                        <i class="fas fa-calendar-alt me-1" style="color: #3498db;"></i>
                                        تاريخ الدفع *
                                    </label>
                                    <input type="date" class="form-control" id="payment_date" name="payment_date" 
                                           value="{{ old('payment_date') ?? date('Y-m-d') }}" required
                                           style="border-radius: 10px; border: 1px solid #e0e0e0; padding: 12px;">
                                    @error('payment_date')
                                        <div style="color: #e74c3c; font-size: 0.875rem; margin-top: 5px;">
                                            <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="reference_number" style="color: #2c3e50; font-weight: 500; margin-bottom: 8px; display: block;">
                                        <i class="fas fa-receipt me-1" style="color: #3498db;"></i>
                                        الرقم المرجعي (اختياري)
                                    </label>
                                    <input type="text" class="form-control" id="reference_number" name="reference_number" 
                                           value="{{ old('reference_number') }}" placeholder="أدخل الرقم المرجعي"
                                           style="border-radius: 10px; border: 1px solid #e0e0e0; padding: 12px;">
                                    @error('reference_number')
                                        <div style="color: #e74c3c; font-size: 0.875rem; margin-top: 5px;">
                                            <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mt-4">
                                <label for="notes" style="color: #2c3e50; font-weight: 500; margin-bottom: 8px; display: block;">
                                    <i class="fas fa-align-left me-1" style="color: #3498db;"></i>
                                    ملاحظات (اختياري)
                                </label>
                                <textarea class="form-control" id="notes" name="notes" 
                                          rows="4" placeholder="أدخل ملاحظات إضافية" style="border-radius: 10px; border: 1px solid #e0e0e0; padding: 12px;">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div style="color: #e74c3c; font-size: 0.875rem; margin-top: 5px;">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                    @enderror
                            </div>

                            <!-- Tax Selection -->
                            <div class="row g-3 mt-3">
                                <div class="col-md-6">
                                    <label for="property_tax_id" style="color: #2c3e50; font-weight: 500; margin-bottom: 8px; display: block;">
                                        <i class="fas fa-home me-1" style="color: #3498db;"></i>
                                        الضريبة العقارية (اختياري)
                                    </label>
                                    <select class="form-select" id="property_tax_id" name="property_tax_id"
                                            style="border-radius: 10px; border: 1px solid #e0e0e0; padding: 12px;">
                                        <option value="">اختر الضريبة العقارية</option>
                                        @if(isset($propertyTaxes) && $propertyTaxes->count() > 0)
                                            @foreach($propertyTaxes as $tax)
                                                <option value="{{ $tax->id }}" {{ old('property_tax_id') == $tax->id ? 'selected' : '' }}>
                                                    {{ $tax->property->title ?? 'N/A' }} - {{ number_format($tax->amount, 2) }}
                                                </option>
                                            @endforeach
                                        @else
                                            <option value="" disabled>لا توجد ضرائب عقارية معلقة حالياً</option>
                                        @endif
                                    </select>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="tax_filing_id" style="color: #2c3e50; font-weight: 500; margin-bottom: 8px; display: block;">
                                        <i class="fas fa-file-invoice me-1" style="color: #3498db;"></i>
                                        إقرار ضريبي (اختياري)
                                    </label>
                                    <select class="form-select" id="tax_filing_id" name="tax_filing_id"
                                            style="border-radius: 10px; border: 1px solid #e0e0e0; padding: 12px;">
                                        <option value="">اختر الإقرار الضريبي</option>
                                        @if(isset($taxFilings) && $taxFilings->count() > 0)
                                            @foreach($taxFilings as $filing)
                                                <option value="{{ $filing->id }}" {{ old('tax_filing_id') == $filing->id ? 'selected' : '' }}>
                                                    {{ $filing->tax_year }} - {{ $filing->tax_type }}
                                                </option>
                                            @endforeach
                                        @else
                                            <option value="" disabled>لا توجد إقرارات ضريبية معتمدة حالياً</option>
                                        @endif
                                    </select>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-outline-secondary" onclick="resetForm()" style="border-radius: 10px;">
                                    <i class="fas fa-redo me-2"></i>
                                    إعادة تعيين
                                </button>
                                <button type="submit" class="btn" style="background: #3498db; color: white; border: none; border-radius: 10px; padding: 12px 30px; font-weight: 500;">
                                    <i class="fas fa-save me-2"></i>
                                    حفظ الدفعة
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- Info Card -->
                <div class="card mb-4" style="border: none; border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
                    <div class="card-body p-4">
                        <h5 style="color: #2c3e50; font-size: 1.2rem; font-weight: 500; margin-bottom: 20px;">
                            <i class="fas fa-info-circle me-2" style="color: #3498db;"></i>
                            معلومات هامة
                        </h5>
                        
                        <div class="alert alert-info" style="border: none; border-radius: 10px; background: #e3f2fd; color: #1976d2;">
                            <h6 style="font-weight: 500; margin-bottom: 15px;">
                                <i class="fas fa-lightbulb me-2"></i>
                                نصائح هامة
                            </h6>
                            <ul style="font-size: 0.9rem; margin-bottom: 0; padding-right: 20px;">
                                <li style="margin-bottom: 8px;">تأكد من صحة الرقم الضريبي</li>
                                <li style="margin-bottom: 8px;">حدد المبلغ بدقة</li>
                                <li style="margin-bottom: 8px;">اختر تاريخ الدفع الصحيح</li>
                                <li>أضف وصفاً واضحاً للدفعة</li>
                            </ul>
                        </div>

                        <div class="text-center">
                            <button type="button" class="btn btn-outline-secondary w-100" onclick="resetForm()" style="border-radius: 10px;">
                                <i class="fas fa-redo me-2"></i>
                                إعادة تعيين النموذج
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats Card -->
                <div class="card" style="border: none; border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
                    <div class="card-body p-4">
                        <h5 style="color: #2c3e50; font-size: 1.2rem; font-weight: 500; margin-bottom: 20px;">
                            <i class="fas fa-chart-pie me-2" style="color: #3498db;"></i>
                            إحصائيات سريعة
                        </h5>
                        
                        <div class="list-group" style="border-radius: 10px; overflow: hidden;">
                            <div class="list-group-item d-flex justify-content-between align-items-center" style="border: none; border-bottom: 1px solid #f0f0f0; padding: 12px 15px;">
                                <div>
                                    <i class="fas fa-home me-2" style="color: #27ae60;"></i>
                                    <span style="color: #2c3e50;">الضرائب العقارية المعلقة</span>
                                </div>
                                <span style="color: #7f8c8d;">{{ isset($propertyTaxes) ? $propertyTaxes->count() : 0 }}</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center" style="border: none; border-bottom: 1px solid #f0f0f0; padding: 12px 15px;">
                                <div>
                                    <i class="fas fa-file-invoice me-2" style="color: #3498db;"></i>
                                    <span style="color: #2c3e50;">الإقرارات الضريبية المعتمدة</span>
                                </div>
                                <span style="color: #7f8c8d;">{{ isset($taxFilings) ? $taxFilings->count() : 0 }}</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center" style="border: none; padding: 12px 15px;">
                                <div>
                                    <i class="fas fa-calculator me-2" style="color: #f39c12;"></i>
                                    <span style="color: #2c3e50;">إجمالي المستحق</span>
                                </div>
                                <span style="color: #7f8c8d;">
                                    {{ isset($propertyTaxes) && isset($taxFilings) ? number_format($propertyTaxes->sum('amount') + $taxFilings->sum('amount'), 2) : '0.00' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function resetForm() {
    document.querySelector('form').reset();
    // Set default date
    document.getElementById('payment_date').value = new Date().toISOString().split('T')[0];
}
</script>
@endsection

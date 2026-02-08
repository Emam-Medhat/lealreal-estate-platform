@extends('layouts.app')

@section('title', 'تفاصيل الدفعة الضريبية - Real Estate Pro')

@section('content')
<div style="background: #f8f9fa; min-height: 100vh; padding: 20px 0;">
    <div class="container">
        <!-- Header -->
        <div class="text-center mb-5">
            <h1 style="color: #2c3e50; font-size: 2.5rem; font-weight: 300; margin-bottom: 10px;">تفاصيل الدفعة الضريبية</h1>
            <p style="color: #7f8c8d; font-size: 1.1rem;">عرض معلومات الدفعة الضريبية</p>
            <div class="mt-3">
                <a href="{{ route('taxes.payments.index') }}" class="btn btn-outline-secondary" style="border-radius: 10px;">
                    <i class="fas fa-arrow-right"></i> العودة للمدفوعات
                </a>
            </div>
        </div>

        <!-- Payment Details -->
        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4" style="border: none; border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
                    <div class="card-body p-4">
                        <h5 style="color: #2c3e50; font-size: 1.3rem; font-weight: 500; margin-bottom: 20px;">
                            <i class="fas fa-info-circle me-2" style="color: #3498db;"></i>
                            معلومات الدفعة
                        </h5>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <p style="color: #7f8c8d; margin-bottom: 10px;">
                                    <strong style="color: #2c3e50;">رقم الدفعة:</strong> {{ $taxPayment->payment_number }}
                                </p>
                                <p style="color: #7f8c8d; margin-bottom: 10px;">
                                    <strong style="color: #2c3e50;">المبلغ:</strong> 
                                    <span style="color: #27ae60; font-weight: 500;">{{ number_format($taxPayment->amount, 2) }}</span>
                                </p>
                                <p style="color: #7f8c8d; margin-bottom: 10px;">
                                    <strong style="color: #2c3e50;">طريقة الدفع:</strong> 
                                    @switch($taxPayment->payment_method)
                                        @case('cash')
                                            نقدي
                                            @break
                                        @case('bank_transfer')
                                            تحويل بنكي
                                            @break
                                        @case('credit_card')
                                            بطاقة ائتمان
                                            @break
                                        @case('online')
                                            دفع إلكتروني
                                            @break
                                        @default
                                            غير محدد
                                    @endswitch
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p style="color: #7f8c8d; margin-bottom: 10px;">
                                    <strong style="color: #2c3e50;">تاريخ الدفع:</strong> 
                                    {{ $taxPayment->payment_date ? $taxPayment->payment_date->format('Y-m-d') : 'N/A' }}
                                </p>
                                <p style="color: #7f8c8d; margin-bottom: 10px;">
                                    <strong style="color: #2c3e50;">الحالة:</strong> 
                                    <span class="badge" style="background: {{ $taxPayment->status === 'paid' ? '#27ae60' : ($taxPayment->status === 'pending' ? '#f39c12' : '#e74c3c') }}; color: white;">
                                        @switch($taxPayment->status)
                                            @case('paid')
                                                مدفوع
                                                @break
                                            @case('pending')
                                                معلق
                                                @break
                                            @case('overdue')
                                                متأخر
                                                @break
                                            @default
                                                غير محدد
                                        @endswitch
                                    </span>
                                </p>
                                <p style="color: #7f8c8d; margin-bottom: 10px;">
                                    <strong style="color: #2c3e50;">الرقم المرجعي:</strong> 
                                    {{ $taxPayment->reference_number ?? 'غير متوفر' }}
                                </p>
                            </div>
                        </div>
                        
                        @if($taxPayment->notes)
                        <div class="mt-3">
                            <p style="color: #7f8c8d; margin-bottom: 10px;">
                                <strong style="color: #2c3e50;">ملاحظات:</strong>
                            </p>
                            <p style="color: #2c3e50; background: #f8f9fa; padding: 10px; border-radius: 8px;">
                                {{ is_array($taxPayment->notes) ? ($taxPayment->notes['text'] ?? 'غير متوفر') : $taxPayment->notes }}
                            </p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Linked Taxes -->
                @if($taxPayment->propertyTax || $taxPayment->taxFiling)
                <div class="card" style="border: none; border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
                    <div class="card-body p-4">
                        <h5 style="color: #2c3e50; font-size: 1.3rem; font-weight: 500; margin-bottom: 20px;">
                            <i class="fas fa-link me-2" style="color: #3498db;"></i>
                            الضرائب المرتبطة
                        </h5>
                        
                        @if($taxPayment->propertyTax)
                        <div class="mb-3">
                            <h6 style="color: #2c3e50; font-weight: 500;">الضريبة العقارية</h6>
                            <p style="color: #7f8c8d; margin-bottom: 5px;">
                                <strong>العقار:</strong> {{ $taxPayment->propertyTax->property->title ?? 'N/A' }}
                            </p>
                            <p style="color: #7f8c8d; margin-bottom: 5px;">
                                <strong>المبلغ:</strong> {{ number_format($taxPayment->propertyTax->amount, 2) }}
                            </p>
                            <p style="color: #7f8c8d; margin-bottom: 0;">
                                <strong>الحالة:</strong> {{ $taxPayment->propertyTax->status }}
                            </p>
                        </div>
                        @endif
                        
                        @if($taxPayment->taxFiling)
                        <div class="mb-3">
                            <h6 style="color: #2c3e50; font-weight: 500;">الإقرار الضريبي</h6>
                            <p style="color: #7f8c8d; margin-bottom: 5px;">
                                <strong>السنة الضريبية:</strong> {{ $taxPayment->taxFiling->tax_year }}
                            </p>
                            <p style="color: #7f8c8d; margin-bottom: 5px;">
                                <strong>نوع الضريبة:</strong> {{ $taxPayment->taxFiling->tax_type }}
                            </p>
                            <p style="color: #7f8c8d; margin-bottom: 0;">
                                <strong>الحالة:</strong> {{ $taxPayment->taxFiling->status }}
                            </p>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>

            <div class="col-md-4">
                <!-- Actions Card -->
                <div class="card mb-4" style="border: none; border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
                    <div class="card-body p-4">
                        <h5 style="color: #2c3e50; font-size: 1.2rem; font-weight: 500; margin-bottom: 20px;">
                            <i class="fas fa-cogs me-2" style="color: #3498db;"></i>
                            الإجراءات
                        </h5>
                        
                        <div class="d-grid gap-2">
                            <a href="{{ route('taxes.payments.edit', $taxPayment->id) }}" 
                               class="btn btn-outline-primary" style="border-radius: 10px;">
                                <i class="fas fa-edit me-2"></i>
                                تعديل الدفعة
                            </a>
                            
                            @if($taxPayment->status === 'pending')
                            <form method="POST" action="{{ route('taxes.payments.confirm', $taxPayment->id) }}" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-success w-100" style="border-radius: 10px;">
                                    <i class="fas fa-check me-2"></i>
                                    تأكيد الدفعة
                                </button>
                            </form>
                            @endif
                            
                            <form method="POST" action="{{ route('taxes.payments.destroy', $taxPayment->id) }}" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger w-100" style="border-radius: 10px;"
                                        onclick="return confirm('هل أنت متأكد من حذف هذه الدفعة؟')">
                                    <i class="fas fa-trash me-2"></i>
                                    حذف الدفعة
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Info Card -->
                <div class="card" style="border: none; border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
                    <div class="card-body p-4">
                        <h5 style="color: #2c3e50; font-size: 1.2rem; font-weight: 500; margin-bottom: 20px;">
                            <i class="fas fa-info-circle me-2" style="color: #3498db;"></i>
                            معلومات إضافية
                        </h5>
                        
                        <div class="list-group" style="border-radius: 10px; overflow: hidden;">
                            <div class="list-group-item d-flex justify-content-between align-items-center" style="border: none; border-bottom: 1px solid #f0f0f0; padding: 12px 15px;">
                                <div>
                                    <i class="fas fa-user me-2" style="color: #3498db;"></i>
                                    <span style="color: #2c3e50;">المستخدم</span>
                                </div>
                                <span style="color: #7f8c8d;">{{ $taxPayment->user->name ?? 'N/A' }}</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center" style="border: none; border-bottom: 1px solid #f0f0f0; padding: 12px 15px;">
                                <div>
                                    <i class="fas fa-calendar me-2" style="color: #3498db;"></i>
                                    <span style="color: #2c3e50;">إنشاء في</span>
                                </div>
                                <span style="color: #7f8c8d;">{{ $taxPayment->created_at->format('Y-m-d H:i') }}</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center" style="border: none; padding: 12px 15px;">
                                <div>
                                    <i class="fas fa-clock me-2" style="color: #3498db;"></i>
                                    <span style="color: #2c3e50;">آخر تحديث</span>
                                </div>
                                <span style="color: #7f8c8d;">{{ $taxPayment->updated_at->format('Y-m-d H:i') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

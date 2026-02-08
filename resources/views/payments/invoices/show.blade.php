@extends('layouts.app')

@section('title', 'عرض الفاتورة - Real Estate Pro')

@section('content')
<div style="background: #f8f9fa; min-height: 100vh; padding: 20px 0;">
    <div class="container">
        <!-- Header -->
        <div class="text-center mb-5">
            <h1 style="color: #2c3e50; font-size: 2.5rem; font-weight: 300; margin-bottom: 10px;">عرض الفاتورة</h1>
            <p style="color: #7f8c8d; font-size: 1.1rem;">تفاصيل الفاتورة #{{ $invoice->invoice_number }}</p>
            <div class="mt-3">
                <a href="{{ route('payments.invoices.index') }}" class="btn btn-outline-secondary me-2" style="border-radius: 10px;">
                    <i class="fas fa-arrow-right me-2"></i>
                    العودة للفواتير
                </a>
                <a href="{{ route('payments.invoices.edit', $invoice) }}" class="btn btn-outline-primary me-2" style="border-radius: 10px;">
                    <i class="fas fa-edit me-2"></i>
                    تعديل
                </a>
                <a href="{{ route('payments.invoices.download', $invoice) }}" class="btn btn-outline-success me-2" style="border-radius: 10px;">
                    <i class="fas fa-download me-2"></i>
                    تحميل PDF
                </a>
                @if($invoice->status === 'pending')
                    <form method="POST" action="{{ route('payments.invoices.mark.paid', $invoice) }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-outline-warning" style="border-radius: 10px;" onclick="return confirm('هل أنت متأكد من تحديد هذه الفاتورة كمدفوعة؟')">
                            <i class="fas fa-check me-2"></i>
                            تحديد كمدفوعة
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Invoice Details -->
        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-4" style="border: none; border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
                    <div class="card-body p-5">
                        <!-- Invoice Header -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h4 style="color: #2c3e50; font-weight: 600;">فاتورة</h4>
                                <p style="color: #7f8c8d; margin: 0;">رقم الفاتورة: {{ $invoice->invoice_number }}</p>
                                <p style="color: #7f8c8d; margin: 0;">تاريخ الإصدار: {{ $invoice->issue_date ? $invoice->issue_date->format('Y-m-d') : 'N/A' }}</p>
                                <p style="color: #7f8c8d; margin: 0;">تاريخ الاستحقاق: {{ $invoice->due_date ? $invoice->due_date->format('Y-m-d') : 'N/A' }}</p>
                            </div>
                            <div class="col-md-6 text-end">
                                <h4 style="color: #2c3e50; font-weight: 600;">العميل</h4>
                                <p style="color: #7f8c8d; margin: 0;">{{ $invoice->user->full_name ?? 'N/A' }}</p>
                                <p style="color: #7f8c8d; margin: 0;">{{ $invoice->user->email ?? 'N/A' }}</p>
                                <p style="color: #7f8c8d; margin: 0;">{{ $invoice->user->phone ?? 'N/A' }}</p>
                            </div>
                        </div>

                        <!-- Invoice Title and Description -->
                        <div class="mb-4">
                            <h4 style="color: #2c3e50; font-weight: 600; margin-bottom: 10px;">{{ $invoice->title }}</h4>
                            @if($invoice->description)
                                <p style="color: #7f8c8d;">{{ $invoice->description }}</p>
                            @endif
                        </div>

                        <!-- Invoice Items -->
                        <div class="mb-4">
                            <h5 style="color: #2c3e50; font-weight: 500; margin-bottom: 20px;">بنود الفاتورة</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th style="background: #f8f9fa; color: #2c3e50;">الوصف</th>
                                            <th style="background: #f8f9fa; color: #2c3e50; text-align: center;">الكمية</th>
                                            <th style="background: #f8f9fa; color: #2c3e50; text-align: center;">السعر</th>
                                            <th style="background: #f8f9fa; color: #2c3e50; text-align: center;">الإجمالي</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($invoice->items && is_array($invoice->items))
                                            @foreach($invoice->items as $item)
                                                <tr>
                                                    <td>{{ $item['description'] ?? 'N/A' }}</td>
                                                    <td style="text-align: center;">{{ $item['quantity'] ?? 0 }}</td>
                                                    <td style="text-align: center;">{{ number_format($item['unit_price'] ?? 0, 2) }}</td>
                                                    <td style="text-align: center;">{{ number_format($item['total'] ?? 0, 2) }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="4" class="text-center">لا توجد بنود</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Financial Summary -->
                        <div class="row">
                            <div class="col-md-6">
                                @if($invoice->notes)
                                    <div class="mb-3">
                                        <h6 style="color: #2c3e50; font-weight: 500;">ملاحظات:</h6>
                                        <p style="color: #7f8c8d;">{{ $invoice->notes }}</p>
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <div class="card" style="background: #f8f9fa; border: none; border-radius: 10px;">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span style="color: #7f8c8d;">المجموع الفرعي:</span>
                                            <span style="color: #2c3e50; font-weight: 500;">{{ number_format($invoice->subtotal, 2) }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span style="color: #7f8c8d;">ضريبة القيمة المضافة:</span>
                                            <span style="color: #2c3e50; font-weight: 500;">{{ number_format($invoice->tax_amount, 2) }}</span>
                                        </div>
                                        @if($invoice->discount_amount > 0)
                                            <div class="d-flex justify-content-between mb-2">
                                                <span style="color: #7f8c8d;">الخصم:</span>
                                                <span style="color: #e74c3c; font-weight: 500;">-{{ number_format($invoice->discount_amount, 2) }}</span>
                                            </div>
                                        @endif
                                        <hr>
                                        <div class="d-flex justify-content-between">
                                            <h6 style="color: #2c3e50; font-weight: 600;">الإجمالي:</h6>
                                            <h6 style="color: #27ae60; font-weight: 600;">{{ number_format($invoice->total ?? $invoice->total_amount, 2) }}</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Status Card -->
                <div class="card mb-4" style="border: none; border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
                    <div class="card-body p-4">
                        <h5 style="color: #2c3e50; font-weight: 500; margin-bottom: 20px;">حالة الفاتورة</h5>
                        <div class="text-center">
                            @switch($invoice->status)
                                @case('pending')
                                    <span class="badge" style="background: #f39c12; color: white; font-size: 1.2rem; padding: 10px 20px; border-radius: 10px;">معلق</span>
                                    @break
                                @case('paid')
                                    <span class="badge" style="background: #27ae60; color: white; font-size: 1.2rem; padding: 10px 20px; border-radius: 10px;">مدفوع</span>
                                    @break
                                @case('cancelled')
                                    <span class="badge" style="background: #e74c3c; color: white; font-size: 1.2rem; padding: 10px 20px; border-radius: 10px;">ملغي</span>
                                    @break
                                @case('overdue')
                                    <span class="badge" style="background: #e67e22; color: white; font-size: 1.2rem; padding: 10px 20px; border-radius: 10px;">متأخر</span>
                                    @break
                                @default
                                    <span class="badge" style="background: #95a5a6; color: white; font-size: 1.2rem; padding: 10px 20px; border-radius: 10px;">غير محدد</span>
                            @endswitch
                        </div>
                        
                        @if($invoice->paid_date)
                            <div class="text-center mt-3">
                                <p style="color: #27ae60; margin: 0;">تاريخ الدفع: {{ $invoice->paid_date->format('Y-m-d') }}</p>
                            </div>
                        @endif
                        
                        @if($invoice->payment_method)
                            <div class="text-center mt-2">
                                <p style="color: #7f8c8d; margin: 0;">طريقة الدفع: {{ $invoice->payment_method }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Actions Card -->
                <div class="card" style="border: none; border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
                    <div class="card-body p-4">
                        <h5 style="color: #2c3e50; font-weight: 500; margin-bottom: 20px;">الإجراءات</h5>
                        <div class="d-grid gap-2">
                            <a href="{{ route('payments.invoices.edit', $invoice) }}" class="btn btn-outline-primary" style="border-radius: 10px;">
                                <i class="fas fa-edit me-2"></i>
                                تعديل الفاتورة
                            </a>
                            <a href="{{ route('payments.invoices.download', $invoice) }}" class="btn btn-outline-success" style="border-radius: 10px;">
                                <i class="fas fa-download me-2"></i>
                                تحميل PDF
                            </a>
                            @if($invoice->status === 'pending')
                                <form method="POST" action="{{ route('payments.invoices.mark.paid', $invoice) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-warning w-100" style="border-radius: 10px;" onclick="return confirm('هل أنت متأكد من تحديد هذه الفاتورة كمدفوعة؟')">
                                        <i class="fas fa-check me-2"></i>
                                        تحديد كمدفوعة
                                    </button>
                                </form>
                            @endif
                            @if($invoice->status === 'pending')
                                <form method="POST" action="{{ route('payments.invoices.cancel', $invoice) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger w-100" style="border-radius: 10px;" onclick="return confirm('هل أنت متأكد من إلغاء هذه الفاتورة؟')">
                                        <i class="fas fa-times me-2"></i>
                                        إلغاء الفاتورة
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'المدفوعات الضريبية - Real Estate Pro')

@section('content')
<div style="background: #f8f9fa; min-height: 100vh; padding: 20px 0;">
    <div class="container">
        <!-- Header -->
        <div class="text-center mb-5">
            <h1 style="color: #2c3e50; font-size: 2.5rem; font-weight: 300; margin-bottom: 10px;">المدفوعات الضريبية</h1>
            <p style="color: #7f8c8d; font-size: 1.1rem;">إدارة وتتبع جميع المدفوعات الضريبية</p>
        </div>

        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card" style="border: none; border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
                    <div class="card-body p-4 text-center">
                        <i class="fas fa-dollar-sign" style="font-size: 2rem; color: #27ae60; margin-bottom: 10px;"></i>
                        <h4 style="color: #2c3e50; font-size: 1.5rem; font-weight: 500;">{{ number_format($payments->sum('amount'), 2) }}</h4>
                        <p style="color: #7f8c8d; margin: 0;">إجمالي المدفوعات</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card" style="border: none; border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
                    <div class="card-body p-4 text-center">
                        <i class="fas fa-check-circle" style="font-size: 2rem; color: #3498db; margin-bottom: 10px;"></i>
                        <h4 style="color: #2c3e50; font-size: 1.5rem; font-weight: 500;">{{ $payments->where('status', 'paid')->count() }}</h4>
                        <p style="color: #7f8c8d; margin: 0;">مدفوعات مكتملة</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card" style="border: none; border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
                    <div class="card-body p-4 text-center">
                        <i class="fas fa-clock" style="font-size: 2rem; color: #f39c12; margin-bottom: 10px;"></i>
                        <h4 style="color: #2c3e50; font-size: 1.5rem; font-weight: 500;">{{ $payments->where('status', 'pending')->count() }}</h4>
                        <p style="color: #7f8c8d; margin: 0;">مدفوعات معلقة</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card" style="border: none; border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
                    <div class="card-body p-4 text-center">
                        <i class="fas fa-calendar" style="font-size: 2rem; color: #e74c3c; margin-bottom: 10px;"></i>
                        <h4 style="color: #2c3e50; font-size: 1.5rem; font-weight: 500;">{{ $payments->where('status', 'overdue')->count() }}</h4>
                        <p style="color: #7f8c8d; margin: 0;">مدفوعات متأخرة</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and Search -->
        <div class="card mb-4" style="border: none; border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
            <div class="card-body p-4">
                <form method="GET" action="{{ route('taxes.payments.index') }}">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   class="form-control" style="border-radius: 10px; border: 1px solid #e0e0e0; padding: 12px;"
                                   placeholder="البحث عن دفعة...">
                        </div>
                        <div class="col-md-2">
                            <select name="status" class="form-select" style="border-radius: 10px; border: 1px solid #e0e0e0; padding: 12px;">
                                <option value="">كل الحالات</option>
                                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>مدفوع</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>معلق</option>
                                <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>متأخر</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="date_from" value="{{ request('date_from') }}" 
                                   class="form-control" style="border-radius: 10px; border: 1px solid #e0e0e0; padding: 12px;"
                                   placeholder="من تاريخ">
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="date_to" value="{{ request('date_to') }}" 
                                   class="form-control" style="border-radius: 10px; border: 1px solid #e0e0e0; padding: 12px;"
                                   placeholder="إلى تاريخ">
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn w-100" 
                                    style="background: #3498db; color: white; border: none; border-radius: 10px; padding: 12px; font-weight: 500;">
                                <i class="fas fa-search"></i> بحث
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Payments Table -->
        <div class="card" style="border: none; border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 style="color: #2c3e50; font-size: 1.3rem; font-weight: 500;">
                        <i class="fas fa-list me-2" style="color: #3498db;"></i>
                        قائمة المدفوعات
                    </h5>
                    <a href="{{ route('taxes.payments.create') }}" class="btn" 
                       style="background: #27ae60; color: white; border: none; border-radius: 10px; padding: 12px 20px;">
                        <i class="fas fa-plus me-2"></i>
                        إضافة دفعة جديدة
                    </a>
                </div>

                @if($payments->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th style="color: #2c3e50; font-weight: 500;">رقم الدفعة</th>
                                    <th style="color: #2c3e50; font-weight: 500;">الرقم الضريبي</th>
                                    <th style="color: #2c3e50; font-weight: 500;">المبلغ</th>
                                    <th style="color: #2c3e50; font-weight: 500;">تاريخ الدفع</th>
                                    <th style="color: #2c3e50; font-weight: 500;">الحالة</th>
                                    <th style="color: #2c3e50; font-weight: 500;">ملاحظات</th>
                                    <th style="color: #2c3e50; font-weight: 500;">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($payments as $payment)
                                    <tr>
                                        <td>{{ $payment->payment_number }}</td>
                                        <td>{{ $payment->tax_number ?? 'N/A' }}</td>
                                        <td style="color: #27ae60; font-weight: 500;">{{ number_format($payment->amount, 2) }}</td>
                                        <td>{{ $payment->payment_date ? $payment->payment_date->format('Y-m-d') : 'N/A' }}</td>
                                        <td>
                                            @switch($payment->status)
                                                @case('paid')
                                                    <span class="badge" style="background: #27ae60; color: white;">مدفوع</span>
                                                    @break
                                                @case('pending')
                                                    <span class="badge" style="background: #f39c12; color: white;">معلق</span>
                                                    @break
                                                @case('overdue')
                                                    <span class="badge" style="background: #e74c3c; color: white;">متأخر</span>
                                                    @break
                                                @default
                                                    <span class="badge" style="background: #95a5a6; color: white;">غير محدد</span>
                                            @endswitch
                                        </td>
                                        <td>
                                            <span style="color: #7f8c8d; font-size: 0.9rem;">
                                                {{ $payment->notes ? (is_array($payment->notes) ? Str::limit($payment->notes['text'] ?? '', 30) : Str::limit($payment->notes, 30)) : 'لا توجد ملاحظات' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('taxes.payments.show', $payment->id) }}" 
                                                   class="btn btn-sm btn-outline-primary" style="border-radius: 5px;">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('taxes.payments.edit', $payment->id) }}" 
                                                   class="btn btn-sm btn-outline-secondary" style="border-radius: 5px;">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form method="POST" action="{{ route('taxes.payments.destroy', $payment->id) }}" 
                                                      style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                            style="border-radius: 5px;" 
                                                            onclick="return confirm('هل أنت متأكد من حذف هذه الدفعة؟')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $payments->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-inbox" style="font-size: 4rem; color: #bdc3c7; margin-bottom: 20px;"></i>
                        <h4 style="color: #7f8c8d;">لا توجد مدفوعات حالياً</h4>
                        <p style="color: #95a5a6;">ابدأ بإضافة أول دفعة ضريبية</p>
                        <a href="{{ route('taxes.payments.create') }}" class="btn mt-3" 
                           style="background: #3498db; color: white; border: none; border-radius: 10px; padding: 12px 20px;">
                            <i class="fas fa-plus me-2"></i>
                            إضافة دفعة جديدة
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

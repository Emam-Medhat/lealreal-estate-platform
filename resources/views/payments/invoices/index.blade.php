@extends('layouts.app')

@section('title', 'الفواتير - Real Estate Pro')

@section('content')
<div style="background: #f8f9fa; min-height: 100vh; padding: 20px 0;">
    <div class="container">
        <!-- Header -->
        <div class="text-center mb-5">
            <h1 style="color: #2c3e50; font-size: 2.5rem; font-weight: 300; margin-bottom: 10px;">الفواتير</h1>
            <p style="color: #7f8c8d; font-size: 1.1rem;">إدارة فواتير النظام</p>
            <div class="mt-3">
                <a href="{{ route('payments.invoices.create') }}" class="btn btn-primary" style="border-radius: 10px;">
                    <i class="fas fa-plus me-2"></i>
                    إنشاء فاتورة جديدة
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-center" style="border: none; border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
                    <div class="card-body p-4">
                        <h3 style="color: #3498db; font-size: 2rem; font-weight: 600;">{{ $invoices->count() }}</h3>
                        <p style="color: #7f8c8d; margin: 0;">إجمالي الفواتير</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center" style="border: none; border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
                    <div class="card-body p-4">
                        <h3 style="color: #27ae60; font-size: 2rem; font-weight: 600;">{{ $invoices->where('status', 'paid')->count() }}</h3>
                        <p style="color: #7f8c8d; margin: 0;">الفواتير المدفوعة</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center" style="border: none; border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
                    <div class="card-body p-4">
                        <h3 style="color: #f39c12; font-size: 2rem; font-weight: 600;">{{ $invoices->where('status', 'pending')->count() }}</h3>
                        <p style="color: #7f8c8d; margin: 0;">الفواتير المعلقة</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center" style="border: none; border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
                    <div class="card-body p-4">
                        <h3 style="color: #e74c3c; font-size: 2rem; font-weight: 600;">{{ number_format($invoices->sum(function($inv) { return $inv->total ?? $inv->total_amount; }), 2) }}</h3>
                        <p style="color: #7f8c8d; margin: 0;">إجمالي المبالغ</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-4" style="border: none; border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
            <div class="card-body p-4">
                <form method="GET" action="{{ route('payments.invoices.index') }}">
                    <div class="row">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="البحث برقم الفاتورة أو اسم العميل" value="{{ request('search') }}" style="border-radius: 10px;">
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-select" style="border-radius: 10px;">
                                <option value="">جميع الحالات</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>معلق</option>
                                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>مدفوع</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="type" class="form-select" style="border-radius: 10px;">
                                <option value="">جميع الأنواع</option>
                                <option value="property" {{ request('type') == 'property' ? 'selected' : '' }}>عقاري</option>
                                <option value="service" {{ request('type') == 'service' ? 'selected' : '' }}>خدمة</option>
                                <option value="subscription" {{ request('type') == 'subscription' ? 'selected' : '' }}>اشتراك</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-outline-primary w-100" style="border-radius: 10px;">
                                <i class="fas fa-search me-2"></i>
                                بحث
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Invoices Table -->
        @if($invoices->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th style="color: #2c3e50; font-weight: 500;">رقم الفاتورة</th>
                            <th style="color: #2c3e50; font-weight: 500;">العميل</th>
                            <th style="color: #2c3e50; font-weight: 500;">المبلغ</th>
                            <th style="color: #2c3e50; font-weight: 500;">تاريخ الإصدار</th>
                            <th style="color: #2c3e50; font-weight: 500;">تاريخ الاستحقاق</th>
                            <th style="color: #2c3e50; font-weight: 500;">الحالة</th>
                            <th style="color: #2c3e50; font-weight: 500;">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoices as $invoice)
                        <tr>
                            <td>{{ $invoice->invoice_number }}</td>
                            <td>{{ $invoice->user->full_name ?? 'N/A' }}</td>
                            <td style="color: #27ae60; font-weight: 500;">{{ number_format($invoice->total ?? $invoice->total_amount, 2) }}</td>
                            <td>{{ $invoice->issue_date ? $invoice->issue_date->format('Y-m-d') : 'N/A' }}</td>
                            <td>{{ $invoice->due_date ? $invoice->due_date->format('Y-m-d') : 'N/A' }}</td>
                            <td>
                                @switch($invoice->status)
                                    @case('paid')
                                        <span class="badge" style="background: #27ae60; color: white;">مدفوع</span>
                                        @break
                                    @case('pending')
                                        <span class="badge" style="background: #f39c12; color: white;">معلق</span>
                                        @break
                                    @case('cancelled')
                                        <span class="badge" style="background: #e74c3c; color: white;">ملغي</span>
                                        @break
                                    @default
                                        <span class="badge" style="background: #95a5a6; color: white;">غير محدد</span>
                                @endswitch
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('payments.invoices.show', $invoice) }}" class="btn btn-sm btn-outline-primary" style="border-radius: 8px;">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('payments.invoices.edit', $invoice) }}" class="btn btn-sm btn-outline-secondary" style="border-radius: 8px;">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('payments.invoices.download', $invoice) }}" class="btn btn-sm btn-outline-success" style="border-radius: 8px;">
                                        <i class="fas fa-download"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $invoices->links() }}
            </div>
        @else
            <div class="card text-center" style="border: none; border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
                <div class="card-body p-5">
                    <i class="fas fa-file-invoice" style="font-size: 4rem; color: #bdc3c7; margin-bottom: 20px;"></i>
                    <h4 style="color: #7f8c8d;">لا توجد فواتير</h4>
                    <p style="color: #95a5a6;">لم يتم العثور على أي فواتير. قم بإنشاء فاتورة جديدة للبدء.</p>
                    <a href="{{ route('payments.invoices.create') }}" class="btn btn-primary" style="border-radius: 10px;">
                        <i class="fas fa-plus me-2"></i>
                        إنشاء فاتورة جديدة
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

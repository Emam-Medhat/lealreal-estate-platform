@extends('layouts.app')

@section('title', 'المدفوعات')

@push('styles')
<style>
    .payments-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem;
        border-radius: 15px;
        margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }

    .card-shell {
        border: none;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        overflow: hidden;
        background: #fff;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card-shell:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.15);
    }

    .card-shell .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: #fff;
        font-weight: 600;
        padding: 1.2rem;
    }

    .metric {
        border-radius: 15px;
        color: #fff;
        padding: 1.5rem;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .metric:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.15);
    }

    .metric.blue { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); }
    .metric.green { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
    .metric.yellow { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
    .metric.red { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }

    .btn-primary-custom {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 25px;
        padding: 0.75rem 1.5rem;
        color: #fff;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-primary-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
    }

    .table-modern thead th {
        background: linear-gradient(135deg, #f8f9ff 0%, #e8eaff 100%);
        color: #495057;
        border: none;
        padding: 1rem;
        font-weight: 700;
        text-align: right;
        white-space: nowrap;
    }

    .table-modern tbody tr { transition: background-color 0.3s ease; }
    .table-modern tbody tr:hover { background-color: #f8f9ff; }

    .empty-state {
        padding: 3rem;
        text-align: center;
        background: linear-gradient(135deg, #f8f9ff 0%, #e8eaff 100%);
        border-radius: 15px;
    }

    .empty-state i { color: #667eea; }

    .fade-in { animation: fadeIn 0.5s ease-in; }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endpush

@section('content')
<div class="container-fluid fade-in">
    <div class="payments-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="mb-3">
                    <i class="fas fa-credit-card ml-3"></i>
                    المدفوعات
                </h1>
                <p class="mb-0 opacity-90">عرض وتحليل المدفوعات داخل النظام</p>
            </div>
            <div class="col-md-4 text-md-left">
                <a href="{{ route('admin.financial.dashboard') }}" class="btn btn-primary-custom">
                    <i class="fas fa-arrow-left ml-2"></i>
                    لوحة المالية
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="metric blue">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-blue-100 text-sm font-medium mb-1">إجمالي المدفوعات</div>
                    <div class="text-3xl font-bold">{{ $stats['total_payments'] ?? 0 }}</div>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-receipt text-2xl"></i>
                </div>
            </div>
        </div>
        <div class="metric green">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-green-100 text-sm font-medium mb-1">مكتملة</div>
                    <div class="text-3xl font-bold">{{ $stats['completed_payments'] ?? 0 }}</div>
                    <div class="text-green-100 text-xs mt-2">{{ number_format($stats['completed_amount'] ?? 0, 2) }} ريال</div>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-check-circle text-2xl"></i>
                </div>
            </div>
        </div>
        <div class="metric yellow">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-yellow-100 text-sm font-medium mb-1">قيد الانتظار</div>
                    <div class="text-3xl font-bold">{{ $stats['pending_payments'] ?? 0 }}</div>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-clock text-2xl"></i>
                </div>
            </div>
        </div>
        <div class="metric red">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-red-100 text-sm font-medium mb-1">فاشلة</div>
                    <div class="text-3xl font-bold">{{ $stats['failed_payments'] ?? 0 }}</div>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-times-circle text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="card-shell mb-6">
        <div class="card-header">
            <h6 class="mb-0">
                <i class="fas fa-filter ml-2"></i>
                البحث والتصفية
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.financial.payments') }}">
                <div class="flex flex-col lg:flex-row lg:items-end gap-4 flex-wrap">
                    <div class="flex-1 min-w-[240px]">
                        <label class="block text-sm font-medium text-gray-700 mb-2">بحث</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="بحث في المدفوعات..."
                               class="w-full h-11 px-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" />
                    </div>
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-sm font-medium text-gray-700 mb-2">الحالة</label>
                        <select name="status" class="w-full h-11 px-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                            <option value="">كل الحالات</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>معلق</option>
                            <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>فشل</option>
                        </select>
                    </div>
                    <div class="flex items-end gap-2 min-w-[260px]">
                        <button type="submit" class="btn btn-primary-custom w-full h-11">
                            <i class="fas fa-search ml-2"></i>
                            بحث
                        </button>
                        <a href="{{ route('admin.financial.payments') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 h-11 rounded-xl font-medium transition-all duration-300 text-center flex items-center justify-center w-full">
                            <i class="fas fa-redo ml-2"></i>
                            إعادة تعيين
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card-shell">
        <div class="card-header">
            <h6 class="mb-0">
                <i class="fas fa-list ml-2"></i>
                قائمة المدفوعات
            </h6>
        </div>
        <div class="card-body">
            <div class="overflow-x-auto">
                <table class="table table-modern" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>العملية</th>
                            <th>المستخدم</th>
                            <th>العقار</th>
                            <th>المبلغ</th>
                            <th>الحالة</th>
                            <th>التاريخ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($payments as $payment)
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-gray-900">#{{ $payment->id }}</div>
                                    <div class="text-xs text-gray-500">{{ $payment->payment_method ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-gray-900">{{ $payment->user_name ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-700">{{ $payment->property_title ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-gray-900">{{ number_format($payment->amount ?? 0, 2) }} ريال</div>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $status = $payment->status ?? 'unknown';
                                        $statusClass = 'bg-gray-100 text-gray-800';
                                        if ($status === 'completed') { $statusClass = 'bg-green-100 text-green-800'; }
                                        elseif ($status === 'pending') { $statusClass = 'bg-yellow-100 text-yellow-800'; }
                                        elseif ($status === 'failed') { $statusClass = 'bg-red-100 text-red-800'; }
                                    @endphp
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $statusClass }}">
                                        {{ $status === 'completed' ? 'مكتمل' : ($status === 'pending' ? 'معلق' : ($status === 'failed' ? 'فشل' : $status)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm">{{ \Carbon\Carbon::parse($payment->created_at)->format('Y-m-d') }}</div>
                                    <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($payment->created_at)->format('H:i') }}</div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="empty-state">
                                        <i class="fas fa-credit-card fa-4x mb-3"></i>
                                        <h4 class="text-gray-700 mb-2">لا توجد مدفوعات حالياً</h4>
                                        <p class="text-gray-600 mb-0">سيتم عرض المدفوعات هنا عند توفرها</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="flex justify-center sm:justify-start mt-4">
                @if(method_exists($payments, 'links'))
                    {{ $payments->links() }}
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

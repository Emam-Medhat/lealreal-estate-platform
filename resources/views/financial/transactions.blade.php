@extends('layouts.app')

@section('title', 'المعاملات المالية')

@push('styles')
<style>
    .transactions-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem;
        border-radius: 15px;
        margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    
    .stats-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        overflow: hidden;
        background: #fff;
    }
    
    .stats-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.15);
    }

    .stats-card .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
        font-weight: 600;
        padding: 1.2rem;
    }

    .stats-card--blue {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: #fff;
    }

    .stats-card--green {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: #fff;
    }

    .stats-card--red {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: #fff;
    }

    .stats-card--purple {
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
        color: #fff;
    }

    .stats-card--metric {
        padding: 1.5rem;
    }

    .stats-card--neutral {
        background: #fff;
        color: inherit;
    }
    
    .filter-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        overflow: hidden;
    }
    
    .filter-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.15);
    }
    
    .filter-card .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
        font-weight: 600;
        padding: 1.2rem;
    }
    
    .transactions-table {
        border-radius: 10px;
        overflow: hidden;
    }
    
    .transactions-table thead th {
        background: linear-gradient(135deg, #f8f9ff 0%, #e8eaff 100%);
        color: #495057;
        border: none;
        padding: 1rem;
        font-weight: 600;
        text-align: right;
    }
    
    .transactions-table tbody tr {
        transition: background-color 0.3s ease;
    }
    
    .transactions-table tbody tr:hover {
        background-color: #f8f9ff;
    }
    
    .btn-primary-custom {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 25px;
        padding: 0.75rem 1.5rem;
        color: white;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-primary-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
    }
    
    .btn-secondary-custom {
        background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
        border: none;
        border-radius: 25px;
        padding: 0.75rem 1.5rem;
        color: white;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-secondary-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(108, 117, 125, 0.3);
    }
    
    .badge-custom {
        padding: 0.5rem 1rem;
        border-radius: 25px;
        font-weight: 600;
        font-size: 0.875rem;
    }
    
    .badge-custom.success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
    }
    
    .badge-custom.warning {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
    }
    
    .badge-custom.danger {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
    }
    
    .badge-custom.info {
        background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
        color: white;
    }
    
    .badge-custom.secondary {
        background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
        color: white;
    }
    
    .empty-state {
        padding: 3rem;
        text-align: center;
        background: linear-gradient(135deg, #f8f9ff 0%, #e8eaff 100%);
        border-radius: 15px;
    }
    
    .empty-state i {
        color: #667eea;
        margin-bottom: 1rem;
    }
    
    .fade-in {
        animation: fadeIn 0.5s ease-in;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endpush

@section('content')
<div class="container-fluid fade-in">
    <!-- Header Section -->
    <div class="transactions-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="mb-3">
                    <i class="fas fa-exchange-alt ml-3"></i>
                    المعاملات المالية
                </h1>
                <p class="mb-0 opacity-90">إدارة وتتبع جميع المعاملات المالية في النظام</p>
            </div>
            <div class="col-md-4 text-md-left">
                <div class="flex items-center gap-3">
                    @if(\Illuminate\Support\Facades\Route::has('admin.financial.transactions.export'))
                        <a href="{{ route('admin.financial.transactions.export') }}" class="btn btn-secondary-custom">
                            <i class="fas fa-download ml-2"></i>
                            تصدير التقرير
                        </a>
                    @endif
                    @if(\Illuminate\Support\Facades\Route::has('admin.financial.transactions.create'))
                        <a href="{{ route('admin.financial.transactions.create') }}" class="btn btn-primary-custom">
                            <i class="fas fa-plus ml-2"></i>
                            معاملة جديدة
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Total Transactions -->
        <div class="stats-card stats-card--metric stats-card--blue">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium mb-1">إجمالي المعاملات</p>
                    <p class="text-3xl font-bold">{{ $stats['total_transactions'] }}</p>
                    <p class="text-blue-100 text-xs mt-2">
                        <i class="fas fa-exchange-alt ml-1"></i>
                        جميع المعاملات
                    </p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-exchange-alt text-2xl"></i>
                </div>
            </div>
        </div>
        
        <!-- Credit Transactions -->
        <div class="stats-card stats-card--metric stats-card--green">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium mb-1">معاملات دائنة</p>
                    <p class="text-3xl font-bold">{{ $stats['credit_transactions'] }}</p>
                    <p class="text-green-100 text-xs mt-2">
                        <i class="fas fa-arrow-down ml-1"></i>
                        {{ number_format($stats['total_credit_amount'], 2) }} ريال
                    </p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-arrow-down text-2xl"></i>
                </div>
            </div>
        </div>
        
        <!-- Debit Transactions -->
        <div class="stats-card stats-card--metric stats-card--red">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-100 text-sm font-medium mb-1">معاملات مدينة</p>
                    <p class="text-3xl font-bold">{{ $stats['debit_transactions'] }}</p>
                    <p class="text-red-100 text-xs mt-2">
                        <i class="fas fa-arrow-up ml-1"></i>
                        {{ number_format($stats['total_debit_amount'], 2) }} ريال
                    </p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-arrow-up text-2xl"></i>
                </div>
            </div>
        </div>
        
        <!-- Net Amount -->
        <div class="stats-card stats-card--metric stats-card--purple">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium mb-1">الصافي</p>
                    <p class="text-3xl font-bold">{{ number_format($stats['net_amount'], 2) }}</p>
                    <p class="text-purple-100 text-xs mt-2">
                        <i class="fas fa-balance-scale ml-1"></i>
                        ريال سعودي
                    </p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-balance-scale text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="filter-card">
        <div class="card-header">
            <h6 class="mb-0">
                <i class="fas fa-filter ml-2"></i>
                البحث والتصفية
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.financial.transactions') }}">
                <div class="flex flex-col lg:flex-row lg:items-end gap-4 flex-wrap">
                    <div class="flex-1 min-w-[240px]">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-2">بحث</label>
                        <div class="relative">
                            <input type="text" 
                                   class="w-full h-11 pl-10 pr-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" 
                                   id="search" 
                                   name="search" 
                                   value="{{ request('search') }}" 
                                   placeholder="البحث في المعاملات...">
                            <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                        </div>
                    </div>
                    <div class="flex-1 min-w-[180px]">
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-2">النوع</label>
                        <select class="w-full h-11 px-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" 
                                id="type" 
                                name="type">
                            <option value="">جميع الأنواع</option>
                            <option value="credit" {{ request('type') == 'credit' ? 'selected' : '' }}>دائن</option>
                            <option value="debit" {{ request('type') == 'debit' ? 'selected' : '' }}>مدين</option>
                        </select>
                    </div>
                    <div class="flex-1 min-w-[180px]">
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">الحالة</label>
                        <select class="w-full h-11 px-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" 
                                id="status" 
                                name="status">
                            <option value="">جميع الحالات</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>معلق</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                            <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>فشل</option>
                        </select>
                    </div>
                    <div class="flex-1 min-w-[220px]">
                        <label for="transaction_type" class="block text-sm font-medium text-gray-700 mb-2">نوع المعاملة</label>
                        <select class="w-full h-11 px-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" 
                                id="transaction_type" 
                                name="transaction_type">
                            <option value="">جميع المعاملات</option>
                            @foreach($transactionTypes as $type)
                                <option value="{{ $type->transaction_type }}" {{ request('transaction_type') == $type->transaction_type ? 'selected' : '' }}>
                                    {{ $type->transaction_type }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end gap-2 min-w-[260px]">
                        <button type="submit" class="btn btn-primary-custom w-full h-11">
                            <i class="fas fa-search ml-2"></i>
                            بحث
                        </button>
                        <a href="{{ route('admin.financial.transactions') }}" 
                           class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 h-11 rounded-xl font-medium transition-all duration-300 text-center flex items-center justify-center w-full">
                            <i class="fas fa-redo ml-2"></i>
                            إعادة تعيين
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="stats-card stats-card--neutral">
        <div class="card-header">
            <h6 class="mb-0">
                <i class="fas fa-list ml-2"></i>
                قائمة المعاملات
            </h6>
        </div>
        <div class="card-body">
            <div class="overflow-x-auto">
                <table class="table transactions-table" width="100%" cellspacing="0">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900">المعاملة</th>
                            <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900">المستخدم</th>
                            <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900">النوع</th>
                            <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900">المبلغ</th>
                            <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900">الحالة</th>
                            <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900">التاريخ</th>
                            <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($transactions as $transaction)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4">
                                    <div>
                                        <div class="font-semibold text-gray-900">#{{ $transaction->id }}</div>
                                        <div class="text-sm text-gray-500">{{ $transaction->transaction_type ?? 'N/A' }}</div>
                                        @if($transaction->description)
                                            <div class="text-xs text-gray-400 mt-1">{{ Str::limit($transaction->description, 50) }}</div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div>
                                        <div class="font-semibold text-gray-900">{{ $transaction->user_name ?? 'N/A' }}</div>
                                        @if($transaction->email)
                                            <div class="text-sm text-gray-500">{{ $transaction->email }}</div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                        {{ $transaction->type == 'credit' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $transaction->type == 'credit' ? 'دائن' : 'مدين' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-semibold {{ $transaction->type == 'credit' ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $transaction->type == 'credit' ? '+' : '-' }}{{ number_format($transaction->amount, 2) }} ريال
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                        {{ $transaction->status == 'completed' ? 'bg-green-100 text-green-800' : 
                                           ($transaction->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                           ($transaction->status == 'failed' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')) }}">
                                        {{ $transaction->status == 'completed' ? 'مكتمل' : 
                                           ($transaction->status == 'pending' ? 'معلق' : 
                                           ($transaction->status == 'failed' ? 'فشل' : $transaction->status)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm">
                                        <div>{{ \Carbon\Carbon::parse($transaction->created_at)->format('Y-m-d') }}</div>
                                        <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($transaction->created_at)->format('H:i') }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-1">
                                        <button class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-150" title="عرض">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="p-2 text-gray-600 hover:bg-gray-50 rounded-lg transition-colors duration-150" title="تعديل">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors duration-150" title="مطابقة">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-150" title="حذف">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="empty-state">
                                        <i class="fas fa-exchange-alt fa-4x mb-3"></i>
                                        <h4 class="text-gray-700 mb-3">لا توجد معاملات حالياً</h4>
                                        <p class="text-gray-600">قم بإنشاء معاملة جديدة لتبدأ باستخدام المعاملات المالية</p>
                                        @if(\Illuminate\Support\Facades\Route::has('admin.financial.transactions.create'))
                                            <a href="{{ route('admin.financial.transactions.create') }}" class="btn btn-primary-custom">
                                                <i class="fas fa-plus ml-2"></i>
                                                معاملة جديدة
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="flex justify-center sm:justify-start">
                @if(method_exists($transactions, 'links'))
                    {{ $transactions->links() }}
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

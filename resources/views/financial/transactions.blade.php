@extends('layouts.app')

@section('title', 'المعاملات المالية')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">المعاملات المالية</h1>
                <p class="text-gray-600">إدارة وتتبع جميع المعاملات المالية في النظام</p>
            </div>
            <div class="flex items-center gap-3">
                <button class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-6 py-3 rounded-xl font-medium transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-[1.02]">
                    <i class="fas fa-download ml-2"></i>
                    تصدير التقرير
                </button>
                <button class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-6 py-3 rounded-xl font-medium transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-[1.02]">
                    <i class="fas fa-plus ml-2"></i>
                    معاملة جديدة
                </button>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Total Transactions -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02]">
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
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02]">
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
        <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02]">
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
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02]">
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
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <form method="GET" action="{{ route('admin.financial.transactions') }}">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                <div class="md:col-span-3">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">بحث</label>
                    <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" 
                           id="search" name="search" value="{{ request('search') }}" placeholder="بحث في المعاملات...">
                </div>
                <div class="md:col-span-2">
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">النوع</label>
                    <select class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" 
                            id="type" name="type">
                        <option value="">جميع الأنواع</option>
                        <option value="credit" {{ request('type') == 'credit' ? 'selected' : '' }}>دائن</option>
                        <option value="debit" {{ request('type') == 'debit' ? 'selected' : '' }}>مدين</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">الحالة</label>
                    <select class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" 
                            id="status" name="status">
                        <option value="">جميع الحالات</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>معلق</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>فشل</option>
                    </select>
                </div>
                <div class="md:col-span-3">
                    <label for="transaction_type" class="block text-sm font-medium text-gray-700 mb-2">نوع المعاملة</label>
                    <select class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" 
                            id="transaction_type" name="transaction_type">
                        <option value="">جميع المعاملات</option>
                        @foreach($transactionTypes as $type)
                            <option value="{{ $type->transaction_type }}" {{ request('transaction_type') == $type->transaction_type ? 'selected' : '' }}>
                                {{ $type->transaction_type }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2 flex items-end gap-2">
                    <button type="submit" class="flex-1 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-4 py-2 rounded-xl font-medium transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-[1.02]">
                        <i class="fas fa-search ml-2"></i>
                        بحث
                    </button>
                    <a href="{{ route('admin.financial.transactions') }}" 
                       class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-xl font-medium transition-all duration-300 text-center">
                        <i class="fas fa-redo ml-2"></i>
                        إعادة تعيين
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Transactions Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
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
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-exchange-alt text-4xl text-gray-300 mb-4"></i>
                                    <p class="text-gray-500 text-lg mb-4">لا توجد معاملات حالياً</p>
                                    <button class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-6 py-3 rounded-xl font-medium transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-[1.02]">
                                        <i class="fas fa-plus ml-2"></i>
                                        معاملة جديدة
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="text-sm text-gray-600">
                @if(method_exists($transactions, 'firstItem'))
                    عرض {{ $transactions->firstItem() }} - {{ $transactions->lastItem() }} من {{ $transactions->total() }} معاملة
                @else
                    لا توجد معاملات للعرض
                @endif
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

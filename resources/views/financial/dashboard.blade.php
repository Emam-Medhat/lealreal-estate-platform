@extends('admin.layouts.admin')

@section('title', 'Financial Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">لوحة التحكم المالية</h1>
                <p class="text-gray-600">نظرة عامة على الأداء المالي والمعاملات</p>
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
        <!-- Total Revenue -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium mb-1">إجمالي الإيرادات</p>
                    <p class="text-3xl font-bold">{{ number_format($stats['total_revenue'], 2) }}</p>
                    <p class="text-blue-100 text-xs mt-2">
                        <i class="fas fa-chart-line ml-1"></i>
                        ريال سعودي
                    </p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-coins text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Payments -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium mb-1">إجمالي المدفوعات</p>
                    <p class="text-3xl font-bold">{{ $stats['total_payments'] }}</p>
                    <p class="text-green-100 text-xs mt-2">
                        <i class="fas fa-credit-card ml-1"></i>
                        جميع المدفوعات
                    </p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-money-bill-wave text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Active Wallets -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium mb-1">المحافظات النشطة</p>
                    <p class="text-3xl font-bold">{{ $stats['active_wallets'] }}</p>
                    <p class="text-purple-100 text-xs mt-2">
                        <i class="fas fa-wallet ml-1"></i>
                        محافظات نشطة
                    </p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-piggy-bank text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Balance -->
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm font-medium mb-1">إجمالي الرصيد</p>
                    <p class="text-3xl font-bold">{{ number_format($stats['total_wallet_balance'], 2) }}</p>
                    <p class="text-orange-100 text-xs mt-2">
                        <i class="fas fa-balance-scale ml-1"></i>
                        ريال سعودي
                    </p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-chart-pie text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Payments -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-gray-900">المدفوعات الأخيرة</h2>
                <a href="{{ route('admin.financial.payments') }}" class="text-blue-600 hover:text-blue-700 font-medium text-sm">عرض الكل</a>
            </div>
            <div class="space-y-3">
                @forelse($recentPayments as $payment)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <div class="font-semibold text-gray-900">{{ $payment->user_name }}</div>
                            <div class="text-sm text-gray-600">{{ $payment->amount }} ريال</div>
                        </div>
                        <div class="text-sm text-gray-500">
                            {{ \Carbon\Carbon::parse($payment->created_at)->format('Y-m-d H:i') }}
                        </div>
                    </div>
                @empty
                    <div class="text-center text-gray-500 py-8">
                        لا توجد مدفوعات حديثة
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Invoices -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-gray-900">الفواتير الأخيرة</h2>
                <a href="{{ route('admin.financial.invoices') }}" class="text-blue-600 hover:text-blue-700 font-medium text-sm">عرض الكل</a>
            </div>
            <div class="space-y-3">
                @forelse($recentInvoices as $invoice)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <div class="font-semibold text-gray-900">{{ $invoice->user_name }}</div>
                            <div class="text-sm text-gray-600">{{ $invoice->total_amount }} ريال</div>
                        </div>
                        <div class="text-sm text-gray-500">
                            {{ \Carbon\Carbon::parse($invoice->created_at)->format('Y-m-d H:i') }}
                        </div>
                    </div>
                @empty
                    <div class="text-center text-gray-500 py-8">
                        لا توجد فواتير حديثة
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">إجراءات سريعة</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('admin.financial.payments') }}" class="flex items-center p-4 bg-blue-50 rounded-xl hover:bg-blue-100 transition-colors group">
                <div class="bg-blue-600 text-white rounded-full p-3 mb-3">
                    <i class="fas fa-credit-card text-xl"></i>
                </div>
                <div class="text-center">
                    <div class="font-semibold text-gray-900">إدارة المدفوعات</div>
                    <div class="text-sm text-gray-600">عرض وتحليل جميع المدفوعات</div>
                </div>
            </a>
            
            <a href="{{ route('admin.financial.invoices') }}" class="flex items-center p-4 bg-green-50 rounded-xl hover:bg-green-100 transition-colors group">
                <div class="bg-green-600 text-white rounded-full p-3 mb-3">
                    <i class="fas fa-file-invoice text-xl"></i>
                </div>
                <div class="text-center">
                    <div class="font-semibold text-gray-900">إدارة الفواتير</div>
                    <div class="text-sm text-gray-600">عرض وإدارة جميع الفواتير</div>
                </div>
            </a>
            
            <a href="{{ route('admin.financial.wallets') }}" class="flex items-center p-4 bg-purple-50 rounded-xl hover:bg-purple-100 transition-colors group">
                <div class="bg-purple-600 text-white rounded-full p-3 mb-3">
                    <i class="fas fa-wallet text-xl"></i>
                </div>
                <div class="text-center">
                    <div class="font-semibold text-gray-900">إدارة المحافظات</div>
                    <div class="text-sm text-gray-600">عرض وإدارة جميع المحافظات</div>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection

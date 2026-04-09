@extends('admin.layouts.admin')

@section('title', 'المحافظ الرقمية')
@section('page-title', 'المحافظ الرقمية')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">المحافظ الرقمية</h1>
                <p class="text-gray-600">إدارة وتتبع المحافظ الرقمية</p>
            </div>
            <a href="{{ route('blockchain.wallets.create') }}" 
               class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-6 py-3 rounded-xl font-medium flex items-center space-x-2 space-x-reverse transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-[1.02]">
                <i class="fas fa-plus"></i>
                <span>إنشاء محفظة جديدة</span>
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Total Wallets -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium mb-1">إجمالي المحافظ</p>
                    <p class="text-3xl font-bold">{{ $wallets->count() }}</p>
                    <p class="text-blue-100 text-xs mt-2">
                        <i class="fas fa-wallet ml-1"></i>
                        جميع المحافظ
                    </p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-wallet text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Active Wallets -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium mb-1">محافظ نشطة</p>
                    <p class="text-3xl font-bold">{{ $wallets->where('status', 'active')->count() }}</p>
                    <p class="text-green-100 text-xs mt-2">
                        <i class="fas fa-check-circle ml-1"></i>
                        المحافظ النشطة
                    </p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-check-circle text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Balance -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium mb-1">إجمالي الرصيد</p>
                    <p class="text-3xl font-bold">{{ number_format($wallets->sum('balance'), 2) }}</p>
                    <p class="text-purple-100 text-xs mt-2">
                        <i class="fas fa-coins ml-1"></i>
                        ريال سعودي
                    </p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-coins text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm font-medium mb-1">المعاملات الأخيرة</p>
                    <p class="text-3xl font-bold">24</p>
                    <p class="text-orange-100 text-xs mt-2">
                        <i class="fas fa-exchange-alt ml-1"></i>
                        آخر 24 ساعة
                    </p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-exchange-alt text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Wallets Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900">العنوان</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900">النوع</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900">الرصيد</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900">الحالة</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900">المعاملات</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($wallets as $wallet)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4">
                                <div class="font-mono text-sm text-gray-900 bg-gray-100 px-2 py-1 rounded">
                                    {{ Str::limit($wallet->address, 20) }}...
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                    {{ $wallet->type == 'ethereum' ? 'bg-blue-100 text-blue-800' : 
                                       ($wallet->type == 'bitcoin' ? 'bg-orange-100 text-orange-800' : 'bg-gray-100 text-gray-800') }}">
                                    {{ $wallet->type_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm">
                                    <div class="font-semibold text-gray-900">{{ number_format($wallet->balance, 4) }}</div>
                                    <div class="text-xs text-gray-500">{{ $wallet->currency }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                    {{ $wallet->status == 'active' ? 'bg-green-100 text-green-800' : 
                                       ($wallet->status == 'inactive' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800') }}">
                                    {{ $wallet->status_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-center">
                                    <div class="font-semibold text-gray-900">{{ $wallet->transactions_count ?? 0 }}</div>
                                    <div class="text-xs text-gray-500">معاملة</div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-1">
                                    <a href="{{ route('blockchain.wallets.show', $wallet->id) }}" 
                                       class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-150" title="عرض">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('blockchain.wallets.edit', $wallet->id) }}" 
                                       class="p-2 text-gray-600 hover:bg-gray-50 rounded-lg transition-colors duration-150" title="تعديل">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('blockchain.wallets.destroy', $wallet->id) }}" method="POST" class="inline" 
                                          onsubmit="return confirm('هل أنت متأكد من حذف هذه المحفظة؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-150" title="حذف">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-wallet text-4xl text-gray-300 mb-4"></i>
                                    <p class="text-gray-500 text-lg mb-4">لا توجد محافظ حالياً</p>
                                    <a href="{{ route('blockchain.wallets.create') }}" 
                                       class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-6 py-3 rounded-xl font-medium transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-[1.02]">
                                        <i class="fas fa-plus ml-2"></i>
                                        إنشاء محفظة جديدة
                                    </a>
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
                عرض {{ $wallets->firstItem() }} - {{ $wallets->lastItem() }} من {{ $wallets->total() }} محفظة
            </div>
            <div class="flex justify-center sm:justify-start">
                {{ $wallets->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

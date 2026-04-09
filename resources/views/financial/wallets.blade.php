@extends('admin.layouts.admin')

@section('title', 'Wallets Management')

@section('content')
<div class="space-y-6 p-4">
    <!-- Header Section -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">إدارة المحافظات</h1>
                <p class="text-gray-600">إدارة وتتبع جميع محافظات المستخدمين</p>
            </div>
            <div class="flex items-center gap-3">
                <button class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-6 py-3 rounded-xl font-medium transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-[1.02]">
                    <i class="fas fa-download ml-2"></i>
                    تصدير التقرير
                </button>
                <button class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-6 py-3 rounded-xl font-medium transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-[1.02]">
                    <i class="fas fa-plus ml-2"></i>
                    محفظة جديدة
                </button>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Total Wallets -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-4 text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium mb-1">إجمالي المحافظات</p>
                    <p class="text-3xl font-bold">{{ $stats['total_wallets'] }}</p>
                    <p class="text-blue-100 text-xs mt-2">
                        <i class="fas fa-wallet ml-1"></i>
                        جميع المحافظات
                    </p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-wallet text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Active Wallets -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-4 text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium mb-1">محافظات نشطة</p>
                    <p class="text-3xl font-bold">{{ $stats['active_wallets'] }}</p>
                    <p class="text-green-100 text-xs mt-2">
                        <i class="fas fa-check-circle ml-1"></i>
                        محافظات نشطة
                    </p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-check-circle text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Frozen Wallets -->
        <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-2xl p-4 text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-100 text-sm font-medium mb-1">محافظات مجمدة</p>
                    <p class="text-3xl font-bold">{{ $stats['frozen_wallets'] }}</p>
                    <p class="text-red-100 text-xs mt-2">
                        <i class="fas fa-snowflake ml-1"></i>
                        محافظات مجمدة
                    </p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-snowflake text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Balance -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-4 text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium mb-1">إجمالي الرصيد</p>
                    <p class="text-3xl font-bold">{{ number_format($stats['total_balance'], 2) }}</p>
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
    </div>

    <!-- Top Wallets -->
    @if($topWallets->count() > 0)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
            <h2 class="text-xl font-bold text-gray-900 mb-4">أعلى المحافظات</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($topWallets as $wallet)
                    <div class="bg-gray-50 rounded-xl p-3">
                        <div class="flex items-center justify-between mb-2">
                            <div>
                                <div class="font-semibold text-gray-900">{{ $wallet->user_name }}</div>
                                <div class="text-sm text-gray-600">الرصيد: {{ number_format($wallet->balance, 2) }} ريال</div>
                            </div>
                            <div class="bg-blue-100 text-blue-600 rounded-full p-2">
                                <i class="fas fa-trophy"></i>
                            </div>
                        </div>
                        <div class="text-sm text-gray-500">
                            الإجمالي المكتسب: {{ number_format($wallet->total_earned, 2) }} ريال
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Wallets Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-right text-sm font-semibold text-gray-900">المستخدم</th>
                        <th class="px-4 py-3 text-right text-sm font-semibold text-gray-900">البريد الإلكتروني</th>
                        <th class="px-4 py-3 text-right text-sm font-semibold text-gray-900">الرصيد</th>
                        <th class="px-4 py-3 text-right text-sm font-semibold text-gray-900">الرصيد المجمد</th>
                        <th class="px-4 py-3 text-right text-sm font-semibold text-gray-900">إجمالي المكتسب</th>
                        <th class="px-4 py-3 text-right text-sm font-semibold text-gray-900">إجمالي المنفق</th>
                        <th class="px-4 py-3 text-right text-sm font-semibold text-gray-900">الحالة</th>
                        <th class="px-4 py-3 text-right text-sm font-semibold text-gray-900">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($wallets as $wallet)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-4 py-3">
                                <div class="font-semibold text-gray-900">{{ $wallet->user_name }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-sm text-gray-600">{{ $wallet->email }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-semibold text-blue-600">{{ number_format($wallet->balance, 2) }} ريال</div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-sm text-gray-600">{{ number_format($wallet->frozen_balance ?? 0, 2) }} ريال</div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-sm font-medium text-green-600">{{ number_format($wallet->total_earned ?? 0, 2) }} ريال</div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-sm font-medium text-red-600">{{ number_format($wallet->total_spent ?? 0, 2) }} ريال</div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                    {{ $wallet->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $wallet->status == 'active' ? 'نشط' : 'مجمد' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-1">
                                    <button class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-150" title="عرض">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="p-2 text-gray-600 hover:bg-gray-50 rounded-lg transition-colors duration-150" title="تعديل">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    @if($wallet->status == 'active')
                                        <form action="#" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="p-2 text-yellow-600 hover:bg-yellow-50 rounded-lg transition-colors duration-150" title="تجميد">
                                                <i class="fas fa-snowflake"></i>
                                            </button>
                                        </form>
                                    @else
                                        <form action="#" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors duration-150" title="تفعيل">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-wallet text-4xl text-gray-300 mb-4"></i>
                                    <p class="text-gray-500 text-lg mb-4">لا توجد محافظات حالياً</p>
                                    <button class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-6 py-3 rounded-xl font-medium transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-[1.02]">
                                        <i class="fas fa-plus ml-2"></i>
                                        محفظة جديدة
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-4 py-3 border-t border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
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

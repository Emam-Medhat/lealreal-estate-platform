@extends('layouts.app')

@section('title', 'المطالبات')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">المطالبات</h1>
        <a href="{{ route('insurance.claims.create') }}" class="bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600">
            <i class="fas fa-plus ml-2"></i>مطالبة جديدة
        </a>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث عن مطالبة..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <select name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">جميع الحالات</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>معلقة</option>
                    <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>مقدمة</option>
                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>قيد المعالجة</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>موافقة</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوضة</option>
                    <option value="settled" {{ request('status') == 'settled' ? 'selected' : '' }}>مسواة</option>
                    <option value="denied" {{ request('status') == 'denied' ? 'selected' : '' }}>مرفوضة نهائياً</option>
                </select>
            </div>
            <div>
                <select name="policy" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">جميع البوالص</option>
                    @foreach($policies as $id => $title)
                        <option value="{{ $id }}" {{ request('policy') == $id ? 'selected' : '' }}>{{ $title }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">جميع الأنواع</option>
                    <option value="property_damage" {{ request('type') == 'property_damage' ? 'selected' : '' }}>أضرار عقارية</option>
                    <option value="liability" {{ request('type') == 'liability' ? 'selected' : '' }}>مسؤولية</option>
                    <option value="theft" {{ request('type') == 'theft' ? 'selected' : '' }}>سرقة</option>
                    <option value="fire" {{ request('type') == 'fire' ? 'selected' : '' }}>حريق</option>
                    <option value="flood" {{ request('type') == 'flood' ? 'selected' : '' }}>فيضان</option>
                </select>
            </div>
            <div>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 w-full">
                    <i class="fas fa-search ml-2"></i>بحث
                </button>
            </div>
        </form>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="bg-orange-500 text-white rounded-full p-3 ml-3">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">مطالبات معلقة</p>
                    <p class="text-2xl font-bold text-orange-600">{{ $stats['pending_claims'] }}</p>
                </div>
            </div>
        </div>
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="bg-blue-500 text-white rounded-full p-3 ml-3">
                    <i class="fas fa-cog"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">قيد المعالجة</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $stats['processing_claims'] }}</p>
                </div>
            </div>
        </div>
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="bg-green-500 text-white rounded-full p-3 ml-3">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">موافقة</p>
                    <p class="text-2xl font-bold text-green-600">{{ $stats['approved_claims'] }}</p>
                </div>
            </div>
        </div>
        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="bg-purple-500 text-white rounded-full p-3 ml-3">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">إجمالي المدفوعات</p>
                    <p class="text-2xl font-bold text-purple-600">{{ number_format($stats['total_paid'], 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Claims Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">رقم المطالبة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">العنوان</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">البوليصة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">النوع</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المبلغ</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">تاريخ الحادث</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($claims as $claim)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $claim->claim_number }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $claim->title }}</div>
                                <div class="text-sm text-gray-500">{{ Str::limit($claim->description, 50) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $claim->policy->title ?? 'N/A' }}</div>
                                <div class="text-sm text-gray-500">{{ $claim->policy->policy_number ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $claim->claim_type_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ number_format($claim->claimed_amount, 2) }}
                                @if($claim->approved_amount)
                                    <div class="text-xs text-green-600">موافق: {{ number_format($claim->approved_amount, 2) }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @switch($claim->status)
                                    @case('pending')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            معلقة
                                        </span>
                                        @break
                                    @case('submitted')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            مقدمة
                                        </span>
                                        @break
                                    @case('processing')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                            قيد المعالجة
                                        </span>
                                        @break
                                    @case('approved')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            موافقة
                                        </span>
                                        @break
                                    @case('rejected')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            مرفوضة
                                        </span>
                                        @break
                                    @case('settled')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            مسواة
                                        </span>
                                        @break
                                    @case('denied')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            مرفوضة نهائياً
                                        </span>
                                        @break
                                @endswitch
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $claim->incident_date->format('Y-m-d') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-left text-sm font-medium">
                                <a href="{{ route('insurance.claims.show', $claim) }}" class="text-blue-600 hover:text-blue-900 ml-2">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('insurance.claims.edit', $claim) }}" class="text-yellow-600 hover:text-yellow-900 ml-2">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($claim->status === 'pending')
                                    <a href="{{ route('insurance.claims.submit', $claim) }}" class="text-green-600 hover:text-green-900 ml-2">
                                        <i class="fas fa-paper-plane"></i>
                                    </a>
                                @endif
                                @if($claim->status === 'submitted' || $claim->status === 'processing')
                                    <a href="{{ route('insurance.claims.process', $claim) }}" class="text-purple-600 hover:text-purple-900 ml-2">
                                        <i class="fas fa-cog"></i>
                                    </a>
                                @endif
                                @if($claim->status === 'processing')
                                    <a href="{{ route('insurance.claims.approve', $claim) }}" class="text-green-600 hover:text-green-900 ml-2">
                                        <i class="fas fa-check"></i>
                                    </a>
                                    <a href="{{ route('insurance.claims.reject', $claim) }}" class="text-red-600 hover:text-red-900 ml-2">
                                        <i class="fas fa-times"></i>
                                    </a>
                                @endif
                                @if($claim->status === 'approved')
                                    <a href="{{ route('insurance.claims.settle', $claim) }}" class="text-blue-600 hover:text-blue-900 ml-2">
                                        <i class="fas fa-hand-holding-usd"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                لا توجد مطالبات تأمين مسجلة
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="bg-gray-50 px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
            {{ $claims->links() }}
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow-md p-6 mt-6">
        <h2 class="text-lg font-semibold mb-4">إجراءات سريعة</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <a href="{{ route('insurance.claims.create') }}" class="flex items-center p-4 bg-orange-50 rounded-lg hover:bg-orange-100 transition">
                <div class="bg-orange-500 text-white rounded-full p-2 ml-3">
                    <i class="fas fa-plus"></i>
                </div>
                <div>
                    <p class="font-medium text-orange-900">مطالبة جديدة</p>
                    <p class="text-sm text-orange-600">تقديم مطالبة تأمين</p>
                </div>
            </a>
            <a href="{{ route('insurance.claims.bulk') }}" class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition">
                <div class="bg-blue-500 text-white rounded-full p-2 ml-3">
                    <i class="fas fa-tasks"></i>
                </div>
                <div>
                    <p class="font-medium text-blue-900">معالجة جماعية</p>
                    <p class="text-sm text-blue-600">معالجة مطالبات متعددة</p>
                </div>
            </a>
            <a href="{{ route('insurance.claims.reports') }}" class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition">
                <div class="bg-green-500 text-white rounded-full p-2 ml-3">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <div>
                    <p class="font-medium text-green-900">تقارير المطالبات</p>
                    <p class="text-sm text-green-600">تحليل وتقارير</p>
                </div>
            </a>
            <a href="{{ route('insurance.claims.export') }}" class="flex items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition">
                <div class="bg-purple-500 text-white rounded-full p-2 ml-3">
                    <i class="fas fa-download"></i>
                </div>
                <div>
                    <p class="font-medium text-purple-900">تصدير البيانات</p>
                    <p class="text-sm text-purple-600">تصدير إلى Excel/PDF</p>
                </div>
            </a>
        </div>
    </div>

    <!-- Recent Claims Timeline -->
    <div class="bg-white rounded-lg shadow-md p-6 mt-6">
        <h2 class="text-lg font-semibold mb-4">آخر التحديثات</h2>
        <div class="space-y-4">
            @foreach($recentUpdates as $update)
                <div class="flex items-start space-x-3 space-x-reverse">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 rounded-full bg-{{ $update['color'] }}-100 flex items-center justify-center">
                            <i class="fas fa-{{ $update['icon'] }} text-{{ $update['color'] }}-600"></i>
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-medium text-gray-900">{{ $update['claim_title'] }}</p>
                            <p class="text-xs text-gray-500">{{ $update['time'] }}</p>
                        </div>
                        <p class="text-sm text-gray-600">{{ $update['description'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

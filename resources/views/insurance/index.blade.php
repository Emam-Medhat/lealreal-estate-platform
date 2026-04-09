@extends('layouts.app')

@section('title', 'نظام التأمين')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">نظام التأمين</h1>
        <div class="flex space-x-2 space-x-reverse">
            <a href="{{ route('insurance.policies.create') }}" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600">
                <i class="fas fa-plus ml-2"></i>بوليصة جديدة
            </a>
            <a href="{{ route('insurance.providers.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                <i class="fas fa-building ml-2"></i>شركة تأمين
            </a>
            <a href="{{ route('insurance.claims.create') }}" class="bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600">
                <i class="fas fa-exclamation-triangle ml-2"></i>مطالبة جديدة
            </a>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث عن بوليصة..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <select name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">جميع الحالات</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>مسودة</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشطة</option>
                    <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>معلقة</option>
                    <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>منتهية</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغاة</option>
                </select>
            </div>
            <div>
                <select name="provider" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">جميع الشركات</option>
                    @foreach($providers as $id => $name)
                        <option value="{{ $id }}" {{ request('provider') == $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">جميع الأنواع</option>
                    <option value="property" {{ request('type') == 'property' ? 'selected' : '' }}>عقاري</option>
                    <option value="liability" {{ request('type') == 'liability' ? 'selected' : '' }}>مسؤولية</option>
                    <option value="comprehensive" {{ request('type') == 'comprehensive' ? 'selected' : '' }}>شامل</option>
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
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="bg-blue-500 text-white rounded-full p-3 ml-3">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">بوالص نشطة</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $stats['active_policies'] }}</p>
                </div>
            </div>
        </div>
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
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="bg-yellow-500 text-white rounded-full p-3 ml-3">
                    <i class="fas fa-clock"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">تنتهي قريباً</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $stats['expiring_soon'] }}</p>
                </div>
            </div>
        </div>
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="bg-green-500 text-white rounded-full p-3 ml-3">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">إجمالي التغطية</p>
                    <p class="text-2xl font-bold text-green-600">{{ number_format($stats['total_coverage'], 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4">إجراءات سريعة</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <a href="{{ route('insurance.policies.create') }}" class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition">
                <div class="bg-blue-500 text-white rounded-full p-2 ml-3">
                    <i class="fas fa-plus"></i>
                </div>
                <div>
                    <p class="font-medium text-blue-900">إنشاء بوليصة</p>
                    <p class="text-sm text-blue-600">بوليصة تأمين جديدة</p>
                </div>
            </a>
            <a href="{{ route('insurance.claims.create') }}" class="flex items-center p-4 bg-orange-50 rounded-lg hover:bg-orange-100 transition">
                <div class="bg-orange-500 text-white rounded-full p-2 ml-3">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div>
                    <p class="font-medium text-orange-900">تقديم مطالبة</p>
                    <p class="text-sm text-orange-600">مطالبة تأمين جديدة</p>
                </div>
            </a>
            <a href="{{ route('insurance.quotes.create') }}" class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition">
                <div class="bg-green-500 text-white rounded-full p-2 ml-3">
                    <i class="fas fa-calculator"></i>
                </div>
                <div>
                    <p class="font-medium text-green-900">طلب سعر</p>
                    <p class="text-sm text-green-600">عرض سعر تأمين</p>
                </div>
            </a>
            <a href="{{ route('insurance.reports') }}" class="flex items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition">
                <div class="bg-purple-500 text-white rounded-full p-2 ml-3">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <div>
                    <p class="font-medium text-purple-900">التقارير</p>
                    <p class="text-sm text-purple-600">تقارير وإحصائيات</p>
                </div>
            </a>
        </div>
    </div>

    <!-- Recent Policies -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold">البوالص الحديثة</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">رقم البوليصة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">العنوان</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">شركة التأمين</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">القسط</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">التغطية</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">تاريخ الانتهاء</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($recentPolicies as $policy)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $policy->policy_number }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $policy->title }}</div>
                                <div class="text-sm text-gray-500">{{ $policy->policy_type }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $policy->provider->name ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @switch($policy->status)
                                    @case('active')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            نشطة
                                        </span>
                                        @break
                                    @case('draft')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            مسودة
                                        </span>
                                        @break
                                    @case('suspended')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            معلقة
                                        </span>
                                        @break
                                    @case('expired')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            منتهية
                                        </span>
                                        @break
                                    @case('cancelled')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            ملغاة
                                        </span>
                                        @break
                                @endswitch
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ number_format($policy->premium_amount, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ number_format($policy->coverage_amount, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $policy->end_date->format('Y-m-d') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-left text-sm font-medium">
                                <a href="{{ route('insurance.policies.show', $policy) }}" class="text-blue-600 hover:text-blue-900 ml-2">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('insurance.policies.edit', $policy) }}" class="text-yellow-600 hover:text-yellow-900 ml-2">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('insurance.policies.download', $policy) }}" class="text-green-600 hover:text-green-900 ml-2">
                                    <i class="fas fa-download"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                لا توجد بوالص تأمين مسجلة
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Upcoming Renewals -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4">التجديدات القادمة</h2>
        <div class="space-y-4">
            @foreach($upcomingRenewals as $renewal)
                <div class="flex items-center justify-between bg-gray-50 rounded-lg p-4">
                    <div class="flex-1">
                        <div class="flex items-center">
                            <div class="text-sm font-medium text-gray-900">{{ $renewal->policy->title }}</div>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                تنتهي في {{ $renewal->days_until_expiry }} يوم
                            </span>
                        </div>
                        <div class="text-sm text-gray-500">
                            {{ $renewal->policy->provider->name }} - {{ $renewal->policy->end_date->format('Y-m-d') }}
                        </div>
                    </div>
                    <div class="flex items-center space-x-2 space-x-reverse">
                        <a href="{{ route('insurance.policies.renew', $renewal->policy) }}" class="bg-blue-500 text-white px-3 py-1 rounded text-sm hover:bg-blue-600">
                            <i class="fas fa-sync ml-1"></i>تجديد
                        </a>
                        <a href="{{ route('insurance.policies.show', $renewal->policy) }}" class="bg-gray-500 text-white px-3 py-1 rounded text-sm hover:bg-gray-600">
                            <i class="fas fa-eye ml-1"></i>عرض
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Recent Claims -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold">المطالبات الحديثة</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">رقم المطالبة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">البوليصة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">النوع</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المبلغ</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">التاريخ</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($recentClaims as $claim)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $claim->claim_number }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $claim->policy->title }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $claim->claim_type }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ number_format($claim->claimed_amount, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @switch($claim->status)
                                    @case('pending')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            معلقة
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
                                    @case('processing')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            قيد المعالجة
                                        </span>
                                        @break
                                @endswitch
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $claim->created_at->format('Y-m-d') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-left text-sm font-medium">
                                <a href="{{ route('insurance.claims.show', $claim) }}" class="text-blue-600 hover:text-blue-900 ml-2">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('insurance.claims.edit', $claim) }}" class="text-yellow-600 hover:text-yellow-900 ml-2">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                لا توجد مطالبات تأمين مسجلة
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

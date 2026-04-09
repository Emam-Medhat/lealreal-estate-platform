@extends('layouts.app')

@section('title', 'بوالص التأمين')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">بوالص التأمين</h1>
        <a href="{{ route('insurance.policies.create') }}" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600">
            <i class="fas fa-plus ml-2"></i>بوليصة جديدة
        </a>
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
                    <p class="text-sm text-gray-600">إجمالي الأقساط</p>
                    <p class="text-2xl font-bold text-green-600">{{ number_format($stats['total_premiums'], 2) }}</p>
                </div>
            </div>
        </div>
        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="bg-purple-500 text-white rounded-full p-3 ml-3">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">معدل التجديد</p>
                    <p class="text-2xl font-bold text-purple-600">{{ $stats['renewal_rate'] }}%</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Policies Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">رقم البوليصة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">العنوان</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">شركة التأمين</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">العقار</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">القسط</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">التغطية</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">تاريخ الانتهاء</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($policies as $policy)
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
                                <div class="text-sm text-gray-900">{{ $policy->property->title ?? 'N/A' }}</div>
                                <div class="text-sm text-gray-500">{{ $policy->property->property_number ?? 'N/A' }}</div>
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
                                @if($policy->is_expiring_soon)
                                    <span class="text-yellow-600 text-xs">({{ $policy->days_until_expiry }} يوم)</span>
                                @endif
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
                                @if($policy->status === 'active')
                                    <a href="{{ route('insurance.policies.claims', $policy) }}" class="text-orange-600 hover:text-orange-900 ml-2">
                                        <i class="fas fa-exclamation-triangle"></i>
                                    </a>
                                @endif
                                @if($policy->status === 'active' && $policy->is_expiring_soon)
                                    <a href="{{ route('insurance.policies.renew', $policy) }}" class="text-purple-600 hover:text-purple-900 ml-2">
                                        <i class="fas fa-sync"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-4 text-center text-gray-500">
                                لا توجد بوالص تأمين مسجلة
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="bg-gray-50 px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
            {{ $policies->links() }}
        </div>
    </div>

    <!-- Bulk Actions -->
    <div class="bg-white rounded-lg shadow-md p-6 mt-6">
        <h3 class="text-lg font-semibold mb-4">إجراءات جماعية</h3>
        <div class="flex space-x-4 space-x-reverse">
            <button onclick="bulkRenew()" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                <i class="fas fa-sync ml-2"></i>تجديد مختار
            </button>
            <button onclick="bulkCancel()" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600">
                <i class="fas fa-times ml-2"></i>إلغاء مختار
            </button>
            <button onclick="bulkExport()" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600">
                <i class="fas fa-download ml-2"></i>تصدير مختار
            </button>
        </div>
    </div>

    <!-- Upcoming Renewals -->
    <div class="bg-white rounded-lg shadow-md p-6 mt-6">
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
</div>

@section('scripts')
<script>
function bulkRenew() {
    // Implement bulk renewal logic
    alert('سيتم تحديد البوالص للتجديد');
}

function bulkCancel() {
    // Implement bulk cancellation logic
    alert('سيتم تحديد البوالص للإلغاء');
}

function bulkExport() {
    // Implement bulk export logic
    alert('سيتم تصدير البوالص المحددة');
}
</script>
@endsection

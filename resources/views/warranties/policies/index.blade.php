@extends('admin.layouts.admin')

@section('title', 'جميع الضمانات')

@section('page-title', 'جميع الضمانات')

@section('content')
<!-- Page Header -->
<div class="mb-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">جميع الضمانات</h1>
            <p class="text-gray-600 mt-1">إدارة وعرض جميع سياسات الضمان</p>
        </div>
        <div class="flex space-x-reverse space-x-3">
            <a href="{{ route('warranties.policies.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <i class="fas fa-plus ml-2"></i>
                إضافة ضمان جديد
            </a>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">البحث والتصفية</h3>
    </div>
    <div class="p-6">
        <form method="GET" action="{{ route('warranties.policies.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الحالة</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">الكل</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                        <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>منتهي</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">العقار</label>
                    <select name="property_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">الكل</option>
                        @foreach($properties as $property)
                            <option value="{{ $property->id }}" {{ request('property_id') == $property->id ? 'selected' : '' }}>
                                {{ $property->title }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">مقدم الخدمة</label>
                    <select name="service_provider_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">الكل</option>
                        @foreach($serviceProviders as $provider)
                            <option value="{{ $provider->id }}" {{ request('service_provider_id') == $provider->id ? 'selected' : '' }}>
                                {{ $provider->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">حالة انتهاء الصلاحية</label>
                    <select name="expiry_status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">الكل</option>
                        <option value="expired" {{ request('expiry_status') == 'expired' ? 'selected' : '' }}>منتهي</option>
                        <option value="expiring_soon" {{ request('expiry_status') == 'expiring_soon' ? 'selected' : '' }}>سينتهي قريباً</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-search ml-2"></i>
                    بحث
                </button>
                <a href="{{ route('warranties.policies.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 mr-3">
                    <i class="fas fa-redo ml-2"></i>
                    إعادة تعيين
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Warranties Table -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">قائمة الضمانات</h3>
    </div>
    <div class="overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">رقم الضمان</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">العنوان</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">العقار</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">مقدم الخدمة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">نوع الضمان</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">تاريخ الانتهاء</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @if($warranties->count() > 0)
                        @foreach($warranties as $warranty)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $warranty->warranty_code ?? $warranty->warranty_number }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $warranty->title }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $warranty->property->title ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $warranty->serviceProvider->name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ __('warranties.types.' . $warranty->warranty_type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($warranty->status == 'active') bg-green-100 text-green-800
                                        @elseif($warranty->status == 'expired') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ __('warranties.status.' . $warranty->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $warranty->end_date->format('Y-m-d') }}
                                    @if($warranty->end_date->isPast())
                                        <span class="text-red-600 text-xs">(منتهي)</span>
                                    @elseif($warranty->end_date->diffInDays(now()) <= 30)
                                        <span class="text-amber-600 text-xs">(سينتهي قريباً)</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-reverse space-x-2">
                                        <a href="{{ route('warranties.policies.show', $warranty) }}" class="text-blue-600 hover:text-blue-900">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('warranties.policies.edit', $warranty) }}" class="text-amber-600 hover:text-amber-900">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="{{ route('warranties.policies.destroy', $warranty) }}" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا الضمان؟')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <i class="fas fa-shield-alt text-gray-400 text-3xl mb-3"></i>
                                <p class="text-gray-500">لا توجد ضمانات حالياً</p>
                                <a href="{{ route('warranties.policies.create') }}" class="mt-3 inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-blue-700">
                                    <i class="fas fa-plus ml-2"></i>
                                    إضافة ضمان جديد
                                </a>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
    
    @if($warranties->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $warranties->links() }}
        </div>
    @endif
</div>
@endsection
